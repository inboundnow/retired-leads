<?php
/**
 * Creates Inbound Form Shortcode
 */

if (!class_exists('Inbound_Forms')) {
	class Inbound_Forms {
		static $add_script;
		//=============================================
		// Hooks and Filters
		//=============================================
		static function init()	{

			add_shortcode('inbound_form', array(__CLASS__, 'inbound_forms_create'));
			add_shortcode('inbound_forms', array(__CLASS__, 'inbound_short_form_create'));
			add_action('init', array(__CLASS__, 'register_script'));
			add_action('wp_footer', array(__CLASS__, 'print_script'));
			add_action('wp_footer', array(__CLASS__, 'inline_my_script'));
			add_action( 'init',	array(__CLASS__, 'do_actions'));
			add_filter( 'inbound_replace_email_tokens' , array( __CLASS__ , 'replace_tokens' ) , 10 , 3 );

		}

		/* Create Longer shortcode for [inbound_form] */
		static function inbound_forms_create( $atts, $content = null )
		{

			global $post;

			self::$add_script = true;

			$email = get_option('admin_email');

			extract(shortcode_atts(array(
				'id' => '',
				'name' => '',
				'layout' => '',
				'notify' => $email,
				'notify_subject' => '{{site-name}} {{form-name}} - New Lead Conversion',
				'labels' => '',
				'font_size' => '', // set default from CSS
				'width' => '',
				'redirect' => '',
				'icon' => '',
				'lists' => '',
				'submit' => 'Submit',
				'submit_colors' => '',
				'submit_text_color' => '',
				'submit_bg_color' => ''
			), $atts));


			if ( !$id && isset($_GET['post']) ) {
				$id = $_GET['post'];
			}


			$form_name = $name;
			//$form_name = strtolower(str_replace(array(' ','_', '"', "'"),'-',$form_name));
			$form_layout = $layout;
			$form_labels = $labels;
			$form_labels_class = (isset($form_labels)) ? "inbound-label-".$form_labels : 'inbound-label-inline';
			$submit_button = ($submit != "") ? $submit : 'Submit';
			$icon_insert = ($icon != "" && $icon != 'none') ? '<i class="fa-'. $icon . '" font-awesome fa"></i>' : '';

			// Set submit button colors
			if(isset($submit_colors) && $submit_colors === 'on'){
				$submit_bg = " background:" . $submit_bg_color . "; border: 5px solid ".$submit_bg_color."; border-radius: 3px;";
				$submit_color = " color:" . $submit_text_color . ";";

			} else {
				$submit_bg = "";
				$submit_color = "";
			}

			if (preg_match("/px/", $font_size)){
				$font_size = (isset($font_size)) ? " font-size: $font_size;" : '';
			} else if (preg_match("/%/", $font_size)) {
				$font_size = (isset($font_size)) ? " font-size: $font_size;" : '';
			} else if (preg_match("/em/", $font_size)) {
				$font_size = (isset($font_size)) ? " font-size: $font_size;" : '';
			} else if ($font_size == "") {
				$font_size = '';
			} else {
				$font_size = (isset($font_size)) ? " font-size:" . $font_size . "px;" : '';
			}

			// Check for image in submit button option
			if (preg_match('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i',$submit_button)) {
				$image_button = ' color: rgba(0, 0, 0, 0);border: none;box-shadow: none;background: transparent; border-radius:0px;padding: 0px;';
				$inner_button = "<img src='$submit_button' width='100%'>";
				$icon_insert = '';
				$submit_button = '';
			} else {
				$image_button = '';
				$inner_button = '';

			}

			/* Sanitize width input */
			if (preg_match('/px/i',$width)) {
				$fixed_width = str_replace("px", "", $width);
				$width_output = "width:" . $fixed_width . "px;";
			} elseif (preg_match('/%/i',$width)) {
				$fixed_width_perc = str_replace("%", "", $width);
				$width_output = "width:" . $fixed_width_perc . "%;";
			} else {
				$width_output = "width:" . $width . "px;";
			}

			$form_width = ($width != "") ? $width_output : '';

			//if (!preg_match_all("/(.?)\[(inbound_field)\b(.*?)(?:(\/))?\](?:(.+?)\[\/inbound_field\])?(.?)/s", $content, $matches)) {
			if (!preg_match_all('/(.?)\[(inbound_field)(.*?)\]/s',$content, $matches)) {

				return '';

			} else {

				for($i = 0; $i < count($matches[0]); $i++) {
					$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
				}
				//print_r($matches[3]);
				// matches are $matches[3][$i]['label']
				$clean_form_id = preg_replace("/[^A-Za-z0-9 ]/", '', trim($name));
				$form_id = strtolower(str_replace(array(' ','_'),'-',$clean_form_id));


				$form = '<div id="inbound-form-wrapper" class="">';
				$form .= '<form class="inbound-now-form wpl-track-me inbound-track" method="post" id="'.$form_id.'" action="" style="'.$form_width.'">';
				$main_layout = ($form_layout != "") ? 'inbound-'.$form_layout : 'inbound-normal';
				for($i = 0; $i < count($matches[0]); $i++)
				{

					$label = (isset($matches[3][$i]['label'])) ? $matches[3][$i]['label'] : '';


					$clean_label = preg_replace("/[^A-Za-z0-9 ]/", '', trim($label));
					$formatted_label = strtolower(str_replace(array(' ','_'),'-',$clean_label));
					$field_placeholder = (isset($matches[3][$i]['placeholder'])) ? $matches[3][$i]['placeholder'] : '';

					$placeholder_use = ($field_placeholder != "") ? $field_placeholder : $label;

					if ($field_placeholder != "") {
						$form_placeholder = "placeholder='".$placeholder_use."'";
					} else if (isset($form_labels) && $form_labels === "placeholder") {
						$form_placeholder = "placeholder='".$placeholder_use."'";
					} else {
						$form_placeholder = "";
					}

					$description_block = (isset($matches[3][$i]['description'])) ? $matches[3][$i]['description'] : '';
					$field_container_class = (isset($matches[3][$i]['field_container_class'])) ? $matches[3][$i]['field_container_class'] : '';
					$field_input_class = (isset($matches[3][$i]['field_input_class'])) ? $matches[3][$i]['field_input_class'] : '';
					$required = (isset($matches[3][$i]['required'])) ? $matches[3][$i]['required'] : '0';
					$req = ($required === '1') ? 'required' : '';
					$exclude_tracking = (isset($matches[3][$i]['exclude_tracking'])) ? $matches[3][$i]['exclude_tracking'] : '0';
					$et_output = ($exclude_tracking === '1') ? ' data-ignore-form-field="true"' : '';
					$req_label = ($required === '1') ? '<span class="inbound-required">*</span>' : '';
					$map_field = (isset($matches[3][$i]['map_to'])) ? $matches[3][$i]['map_to'] : '';
					if ($map_field != "") {
						$field_name = $map_field;
					} else {
						//$label = self::santize_inputs($label);
						$field_name = strtolower(str_replace(array(' ','_'),'-',$label));
					}

					$data_mapping_attr = ($map_field != "") ? ' data-map-form-field="'.$map_field.'" ' : '';

					/* Map Common Fields */
					(preg_match( '/Email|e-mail|email/i', $label, $email_input)) ? $email_input = " inbound-email" : $email_input = "";

					// Match Phone
					(preg_match( '/Phone|phone number|telephone/i', $label, $phone_input)) ? $phone_input = " inbound-phone" : $phone_input = "";

					// match name or first name. (minus: name=, last name, last_name,)
					(preg_match( '/(?<!((last |last_)))name(?!\=)/im', $label, $first_name_input)) ? $first_name_input = " inbound-first-name" : $first_name_input =	"";

					// Match Last Name
					(preg_match( '/(?<!((first)))(last name|last_name|last)(?!\=)/im', $label, $last_name_input)) ? $last_name_input = " inbound-last-name" : $last_name_input =	"";

					$input_classes = $email_input . $first_name_input . $last_name_input . $phone_input;

					$type = (isset($matches[3][$i]['type'])) ? $matches[3][$i]['type'] : '';
					$show_labels = true;

					if ($type === "hidden" || $type === "honeypot" || $type === "html-block" || $type === "divider") {
						$show_labels = false;
					}
			                
					// added by kirit dholakiya for validation of multiple checkbox
					$div_chk_req = '';
					if($type=='checkbox' && $required=='1') {
							$div_chk_req =' checkbox-required ';
					}
			                
					$form .= '<div class="inbound-field '.$div_chk_req.$main_layout.' label-'.$form_labels_class.' '.$form_labels_class.' '.$field_container_class.'">';

					if ($show_labels && $form_labels != "bottom" || $type === "radio") {
						$form .= '<label for="'. $field_name .'" class="inbound-label '.$formatted_label.' '.$form_labels_class.' inbound-input-'.$type.'" style="'.$font_size.'">' . html_entity_decode($matches[3][$i]['label']) . $req_label . '</label>';
					}

					if ($type === 'textarea') {
						$form .=	'<textarea placeholder="'.$placeholder_use.'" class="inbound-input inbound-input-textarea '.$field_input_class.'" name="'.$field_name.'" id="'.$field_name.'" '.$data_mapping_attr.$et_output.' '.$req.'/></textarea>';

					} else if ($type === 'dropdown') {

						$dropdown_fields = array();
						$dropdown = $matches[3][$i]['dropdown'];
						$dropdown_fields = explode(",", $dropdown);

						$form .= '<select name="'. $field_name .'" class="'.$field_input_class.'"'.$data_mapping_attr.$et_output.' '.$req.'>';

						if ($placeholder_use) {
							$form .= '<option value="" disabled selected>'.str_replace( '%3F' , '?' , $placeholder_use).'</option>';
						}

						foreach ($dropdown_fields as $key => $value) {
							//$drop_val_trimmed =	trim($value);
							//$dropdown_val = strtolower(str_replace(array(' ','_'),'-',$drop_val_trimmed));
							$form .= '<option value="'. trim(str_replace('"', '\"' , $value)) .'">'. $value .'</option>';
						}
						$form .= '</select>';

					} else if ($type === 'dropdown_countries') {

						$dropdown_fields = self::get_countries_array();

						$form .= '<select name="'. $field_name .'" class="'.$field_input_class.'" '.$req.'>';

						if ($field_placeholder) {
							$form .= '<option value="" disabled selected>'.$field_placeholder.'</option>';
						}

						foreach ($dropdown_fields as $key => $value) {
							$form .= '<option value="'.$key.'">'. utf8_encode($value) .'</option>';
						}
						$form .= '</select>';

					} else if ($type === 'date-selector') {

						$m = date('m');
						$d = date('d');
						$y = date('Y');

						$months = self::get_date_selectons('months');
						$days = self::get_date_selectons('days');
						$years = self::get_date_selectons('years');

						$form .= '<div class="dateSelector">';
						$form .= '	<select id="formletMonth" name="'. $field_name .'[month]" >';
						foreach ($months as $key => $value) {
							( $m == $key ) ? $sel = 'selected="selected"' : $sel = '';
							$form .= '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
						}
						$form .= '	</select>';
						$form .= '	<select id="formletDays" name="'. $field_name .'[day]" >';
						foreach ($days as $key => $value) {
							( $d == $key ) ? $sel = 'selected="selected"' : $sel = '';
							$form .= '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
						}
						$form .= '	</select>';
						$form .= '	<select id="formletYears" name="'. $field_name .'[year]" >';
						foreach ($years as $key => $value) {
							( $y == $key ) ? $sel = 'selected="selected"' : $sel = '';
							$form .= '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
						}
						$form .= '	</select>';
						$form .= '</div>';

					} else if ($type === 'radio') {

						$radio_fields = array();
						$radio = $matches[3][$i]['radio'];
						$radio_fields = explode(",", $radio);
						// $clean_radio = str_replace(array(' ','_'),'-',$value) // clean leading spaces. finish

						foreach ($radio_fields as $key => $value) {
							$radio_val_trimmed =	trim($value);
							$radio_val =	strtolower(str_replace(array(' ','_'),'-',$radio_val_trimmed));
							$form .= '<span class="radio-'.$main_layout.' radio-'.$form_labels_class.' '.$field_input_class.'"><input type="radio" name="'. $field_name .'" value="'. $radio_val .'">'. $radio_val_trimmed .'</span>';
						}

					} else if ($type === 'checkbox') {

						$checkbox_fields = array();

						$checkbox = $matches[3][$i]['checkbox'];
						$checkbox_fields = explode(",", $checkbox);
						foreach ($checkbox_fields as $key => $value) {
							$value = html_entity_decode($value);
							$checkbox_val_trimmed =	strip_tags(trim($value));
							$checkbox_val =	strtolower(str_replace(array(' ','_'),'-',$checkbox_val_trimmed));

							$form .= '<input class="checkbox-'.$main_layout.' checkbox-'.$form_labels_class.' '.$field_input_class.'" type="checkbox" name="'. $field_name .'[]" value="'. $checkbox_val .'" >'.$checkbox_val_trimmed.'<br>';
						}
					} else if ($type === 'html-block') {

						$html = $matches[3][$i]['html'];
						//echo $html;
						$form .= "<div class={$field_input_class}>";
						$form .= do_shortcode(html_entity_decode($html));
						$form .= "</div>";

					} else if ($type === 'divider') {

						$divider = $matches[3][$i]['divider_options'];
						//echo $html;
						$form .= "<div class='inbound-form-divider {$field_input_class}'>" . $divider . "<hr></div>";

					} else if ($type === 'editor') {
						//wp_editor(); // call wp editor
					} else if ($type === 'honeypot') {

						$form .= '<input type="hidden" name="stop_dirty_subs" class="stop_dirty_subs" value="">';

					} else {
						$hidden_param = (isset($matches[3][$i]['dynamic'])) ? $matches[3][$i]['dynamic'] : '';
						$fill_value = (isset($matches[3][$i]['default'])) ? $matches[3][$i]['default'] : '';
						$dynamic_value = (isset($_GET[$hidden_param])) ? $_GET[$hidden_param] : '';
						if ($type === 'hidden' && $dynamic_value != "") {
							$fill_value = $dynamic_value;
						}
						$form .=	'<input class="inbound-input inbound-input-text '.$formatted_label . $input_classes.' '.$field_input_class.'" name="'.$field_name.'" '.$form_placeholder.' id="'.$field_name.'" value="'.$fill_value.'" type="'.$type.'"'.$data_mapping_attr.$et_output.' '.$req.'/>';
					}

					if ($show_labels && $form_labels === "bottom" && $type != "radio") {
						$form .= '<label for="'. $field_name .'" class="inbound-label '.$formatted_label.' '.$form_labels_class.' inbound-input-'.$type.'" style="'.$font_size.'">' . $matches[3][$i]['label'] . $req_label . '</label>';
					}

					if ($description_block != "" && $type != 'hidden'){
						$form .= "<div class='inbound-description'>".$description_block."</div>";
					}

					$form .= '</div>';
				}
				// End Loop

				$current_page =  "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$form .= '<div class="inbound-field '.$main_layout.' inbound-submit-area"><button type="submit" class="inbound-button-submit inbound-submit-action" value="'.$submit_button.'" name="send" id="inbound_form_submit" data-ignore-form-field="true" style="'.$submit_bg.$submit_color.$image_button.'">
							'.$icon_insert.''.$submit_button.$inner_button.'</button></div><input data-ignore-form-field="true" type="hidden" name="inbound_submitted" value="1">';
						// <!--<input type="submit" '.$submit_button_type.' class="button" value="'.$submit_button.'" name="send" id="inbound_form_submit" />-->

				$form .= '<input type="hidden" name="inbound_form_n" class="inbound_form_n" value="'.$form_name.'"><input type="hidden" name="inbound_form_lists" id="inbound_form_lists" value="'.$lists.'" data-map-form-field="inbound_form_lists"><input type="hidden" name="inbound_form_id" class="inbound_form_id" value="'.$id.'"><input type="hidden" name="inbound_current_page_url" value="'.$current_page.'"><input type="hidden" name="inbound_furl" value="'. base64_encode($redirect) .'"><input type="hidden" name="inbound_notify" value="'. base64_encode($notify) .'"><input type="hidden" class="inbound_params" name="inbound_params" value=""></form></div>';
				$form .= "<style type='text/css'>.inbound-button-submit{ {$font_size} }</style>";
				$form = preg_replace('/<br class="inbr".\/>/', '', $form); // remove editor br tags

				return $form;
			}
		}
		static function santize_inputs($content){
			// Strip HTML Tags
			$clear = strip_tags($content);
			// Clean up things like &amp;
			$clear = html_entity_decode($clear);
			// Strip out any url-encoded stuff
			$clear = urldecode($clear);
			// Replace non-AlNum characters with space
			$clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
			// Replace Multiple spaces with single space
			$clear = preg_replace('/ +/', ' ', $clear);
			// Trim the string of leading/trailing space
			$clear = trim($clear);
			return $clear;
		}
		/* Create shorter shortcode for [inbound_forms] */
		static function inbound_short_form_create( $atts, $content = null )
		{
			extract(shortcode_atts(array(
				'id' => '',
			), $atts));

			$shortcode = get_post_meta( $id, 'inbound_shortcode', TRUE );

			// If form id missing add it
			if (!preg_match('/id="/', $shortcode)) {
			$shortcode = str_replace("[inbound_form", "[inbound_form id=\"" . $id . "\"", $shortcode);
			}
			if ($id === 'default_3'){
				$shortcode = '[inbound_form name="Form Name" layout="vertical" labels="top" submit="Submit" ][inbound_field label="Email" type="text" required="1" ][/inbound_form]';
			}
			if ($id === 'default_1'){
				$shortcode = '[inbound_form name="3 Field Form" layout="vertical" labels="top" submit="Submit" ][inbound_field label="First Name" type="text" required="0" ][inbound_field label="Last Name" type="text" required="0" ][inbound_field label="Email" type="text" required="1" placeholder="Enter Your Email Address" ][/inbound_form]';
			}
			if ($id === 'default_2'){
				$shortcode = '[inbound_form name="Standard Company Form" layout="vertical" labels="top" submit="Submit" ]

							[inbound_field label="First Name" type="text" required="0" placeholder="Enter Your First Name" ]

							[inbound_field label="Last Name" type="text" required="0" placeholder="Enter Your Last Name" ]

							[inbound_field label="Email" type="text" required="1" placeholder="Enter Your Email Address" ]

							[inbound_field label="Company Name" type="text" required="0" placeholder="Enter Your Company Name" ]

							[inbound_field label="Job Title" type="text" required="0" placeholder="Enter Your Job Title" ]

							[/inbound_form]';
			}
			if (empty($shortcode)) {
				$shortcode = "Form ID: " . $id . " Not Found";
			}
			if ($id === 'none'){
				$shortcode = "";
			}

			return do_shortcode( $shortcode );
		}

		/* Enqueue JS & CSS */
		static function register_script() {
			wp_enqueue_style( 'inbound-shortcodes' );
		}

		// only call enqueue once
		static function print_script() {
			if ( ! self::$add_script )
			return;
			wp_enqueue_style( 'inbound-shortcodes' );
		}

		// move to file
		static function inline_my_script() {
			if ( ! self::$add_script )
				return;

			echo '<script type="text/javascript">

				jQuery(document).ready(function($){

				jQuery("form").submit(function(e) {
				    // added below condition for check any of checkbox checked or not by kirit dholakiya
                    if( jQuery(\'.checkbox-required\')[0] && jQuery(\'.checkbox-required input[type=checkbox]:checked\').length==0)
                    {
                        jQuery(\'.checkbox-required input[type=checkbox]:first\').focus();
						alert("' . __( 'Oops! Looks like you have not filled out all of the required fields!' , 'cta' ) .'");
                        e.preventDefault();
						e.stopImmediatePropagation();
                    }
					jQuery(this).find("input").each(function(){
						if(!jQuery(this).prop("required")){
						} else if (!jQuery(this).val()) {
						alert("' . __( 'Oops! Looks like you have not filled out all of the required fields!' , 'cta' ) .'");

						e.preventDefault();
						e.stopImmediatePropagation();
						return false;
						}
					});
				});

				jQuery("#inbound_form_submit br").remove(); // remove br tags
				function validateEmail(email) {

					var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					return re.test(email);
				}
				var parent_redirect = parent.window.location.href;
				jQuery("#inbound_parent_page").val(parent_redirect);


			// validate email
				$("input.inbound-email").on("change keyup", function (e) {
					var email = $(this).val();
					$(".email_suggestion").remove();
					if (validateEmail(email)) {
						$(this).css("color", "green");
						$(this).addClass("valid-email");
						$(this).removeClass("invalid-email");
					} else {
						$(this).css("color", "red");
						$(this).addClass("invalid-email");
						$(this).removeClass("valid-email");
					}
					if($(this).hasClass("valid-email")) {
						$(this).parent().parent().find("#inbound_form_submit").removeAttr("disabled");
					}
				});

				});
				</script>';

			echo "<style type='text/css'>
			/* Add button style options http://medleyweb.com/freebies/50-super-sleek-css-button-style-snippets/ */
			.email_suggestion {
				font-size: 13px;
				padding-top: 0px;
				margin-top: 0px;
				display: block;
				font-style: italic;
			}
			input.invalid-email {-webkit-box-shadow: 0 0 6px #F8B9B7;
								-moz-box-shadow: 0 0 6px #f8b9b7;
								box-shadow: 0 0 6px #F8B9B7;
								color: #B94A48;
								border-color: #E9322D;}
			input.valid-email {-webkit-box-shadow: 0 0 6px #B7F8BA;
						-moz-box-shadow: 0 0 6px #f8b9b7;
						box-shadow: 0 0 6px #98D398;
						color: #008000;
						border-color: #008000;}
				</style>";
		}

		public static function replace_tokens( $content , $form_data = null , $form_meta_data = null ) {

			/* replace core tokens */
			$content = str_replace('{{site-name}}', get_bloginfo( 'name' ) , $content);
			//$content = str_replace('{{form-name}}', $form_data['inbound_form_n']		, $content);

			foreach ($form_data as $key => $value) {
				$token_key = str_replace('_','-', $key);
				$token_key = str_replace('inbound-','', $token_key);

				$content = str_replace( '{{'.trim($token_key).'}}' , $value , $content );
			}

			return $content;
		}
		// Save Form Conversion to Form CPT
		static function store_form_stats($form_id, $email) {

				//$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
				// $wordpress_date_time = date("Y-m-d G:i:s", $time);
				$form_conversion_num = get_post_meta($form_id, 'inbound_form_conversion_count', true);
				$form_conversion_num++;
				update_post_meta( $form_id, 'inbound_form_conversion_count', $form_conversion_num );

				// Add Lead Email to Conversions List
				$lead_conversion_list = get_post_meta( $form_id, 'lead_conversion_list', TRUE );
				$lead_conversion_list = json_decode($lead_conversion_list,true);
				if (is_array($lead_conversion_list)) {
					$lead_count = count($lead_conversion_list);
					$lead_conversion_list[$lead_count]['email'] = $email;
					// $lead_conversion_list[$lead_count]['date'] = $wordpress_date_time;
					$lead_conversion_list = json_encode($lead_conversion_list);
					update_post_meta( $form_id, 'lead_conversion_list', $lead_conversion_list );
				} else {
					$lead_conversion_list = array();
					$lead_conversion_list[0]['email'] = $email;
					//	$lead_conversion_list[0]['date'] = $wordpress_date_time;
					$lead_conversion_list = json_encode($lead_conversion_list);
					update_post_meta( $form_id, 'lead_conversion_list', $lead_conversion_list );
				}

		}
		/* Perform Actions After a Form Submit */
		static function do_actions(){

			if(isset($_POST['inbound_submitted']) && $_POST['inbound_submitted'] === '1') {
				$form_post_data = array();
				if(isset($_POST['stop_dirty_subs']) && $_POST['stop_dirty_subs'] != "") {
					wp_die( $message = 'Die You spam bastard' );
					return false;
				}
				/* get form submitted form's meta data */
				$form_meta_data = get_post_meta( $_POST['inbound_form_id'] );

				if(isset($_POST['inbound_furl']) && $_POST['inbound_furl'] != "") {
					$redirect = base64_decode($_POST['inbound_furl']);
				} else if (isset($_POST['inbound_current_page_url'])) {
					$redirect = $_POST['inbound_current_page_url'];
				}



				//print_r($_POST);
				foreach ( $_POST as $field => $value ) {

					if ( get_magic_quotes_gpc() && is_string($value) ) {
						$value = stripslashes( $value );
					}

					$field = strtolower($field);

					if (preg_match( '/Email|e-mail|email/i', $field)) {
						$field = "wpleads_email_address";
						if(isset($_POST['inbound_form_id']) && $_POST['inbound_form_id'] != "") {
							self::store_form_stats($_POST['inbound_form_id'], $value);
						}
					}


					$form_post_data[$field] = (!is_array($value)) ?  strip_tags( $value ) : $value;

				}

				$form_meta_data['post_id'] = $_POST['inbound_form_id']; // pass in form id

				/* Send emails if passes spam check returns false */
				if ( !apply_filters( 'inbound_check_if_spam' , false ,  $form_post_data ) ) {
					self::send_conversion_admin_notification($form_post_data , $form_meta_data);
					self::send_conversion_lead_notification($form_post_data , $form_meta_data);
				}

				/* hook runs after form actions are completed and before page redirect */
				do_action('inboundnow_form_submit_actions', $form_post_data, $form_meta_data);

				/* redirect now */
				if ($redirect != "") {
					wp_redirect( $redirect );
					exit();
				}

			}

		}

		/* Sends Notification of New Lead Conversion to Admin & Others Listed on the Form Notification List */
		public static function send_conversion_admin_notification( $form_post_data , $form_meta_data ) {

			if ( $template = self::get_new_lead_email_template()) {

				add_filter( 'wp_mail_content_type', 'inbound_set_html_content_type' );
				function inbound_set_html_content_type() {
					return 'text/html';
				}

				/* Rebuild Form Meta Data to Load Single Values	*/
				foreach( $form_meta_data as $key => $value ) {
					if ( isset($value[0]) ) {
						$form_meta_data[$key] = $value[0];
					}
				}

				/* If there's no notification email in place then bail */
				if ( !isset($form_meta_data['inbound_notify_email']) ) {
					return;
				}

				/* Get Email We Should Send Notifications To */
				$email_to = $form_meta_data['inbound_notify_email'];

				/* Check for Multiple Email Addresses */
				$addresses = explode(",", $email_to);
				if(is_array($addresses) && count($addresses) > 1) {
					$to_address = $addresses;
				} else {
					$to_address[] = $email_to;
				}

				/* Look for Custom Subject Line ,	Fall Back on Default */
				$subject = (isset($form_meta_data['inbound_notify_email_subject'])) ? $form_meta_data['inbound_notify_email_subject'] :	$template['subject'];

				/* Discover From Email Address */
				foreach ($form_post_data as $key => $value) {
					if (preg_match('/email|e-mail/i', $key)) {
						$reply_to_email = $form_post_data[$key];
					}
				}
				$domain = get_option( 'siteurl');
				$domain = str_replace('http://', '', $domain);
				$domain = str_replace('https://', '', $domain);
				$domain = str_replace('www', '', $domain);
				$email_default = 'wordpress@' . $domain;
				$from_email = get_option( 'admin_email' , $email_default );
				$from_email = apply_filters( 'inbound_admin_notification_from_email' , $from_email );
				$reply_to_email = (isset($reply_to_email)) ? $reply_to_email : $from_email;
				/* Prepare Additional Data For Token Engine */
				$form_post_data['redirect_message'] = (isset($form_post_data['inbound_redirect']) && $form_post_data['inbound_redirect'] != "") ? "They were redirected to " . $form_post_data['inbound_redirect'] : '';

				/* Discover From Name */
				$from_name = get_option( 'blogname' , '' );
				$from_name = apply_filters( 'inbound_admin_notification_from_name', $from_name  );

				$Inbound_Templating_Engine = Inbound_Templating_Engine();
				$subject = $Inbound_Templating_Engine->replace_tokens( $subject, array($form_post_data, $form_meta_data));
				$body = $Inbound_Templating_Engine->replace_tokens( $template['body'] , array($form_post_data, $form_meta_data )	);


				$headers = 'From: '. $from_name .' <'. $from_email .'>' . "\r\n";
				$headers = "Reply-To: ".$reply_to_email . "\r\n";
				$headers = apply_filters( 'inbound_lead_notification_email_headers' , $headers );

				foreach ($to_address as $key => $recipient) {
					$result = wp_mail( $recipient , $subject , $body , $headers );
				}

			}

		}

		/* Sends An Email to Lead After Conversion */
		public static function send_conversion_lead_notification( $form_post_data , $form_meta_data ) {


			/* If Notifications Are Off Then Exit */
			if ( !isset($form_meta_data['inbound_email_send_notification'][0]) || $form_meta_data['inbound_email_send_notification'][0] != 'on' ){
				return;
			}

			/* Get Lead Email Address */
			$lead_email = false;
			foreach ($form_post_data as $key => $value) {
				if (preg_match('/email|e-mail/i', $key)) {
					$lead_email = $form_post_data[$key];
				}
			}

			/* Redundancy */
			if (!$lead_email) {
				if (isset($form_post_data['email'])) {
					$lead_email = $form_post_data['email'];
				} else if (isset($form_post_data['e-mail'])) {
					$lead_email = $form_post_data['e-mail'];
				} else if (isset($form_post_data['wpleads_email_address'])) {
					$lead_email = $form_post_data['wpleads_email_address'];
				} else {
					$lead_email = 'null map email field';
				}
			}

			if ( !$lead_email ) {
				return;
			}


			$Inbound_Templating_Engine = Inbound_Templating_Engine();
			$form_id = $form_meta_data['post_id']; //This is page id or post id
			$template_id = $form_meta_data['inbound_email_send_notification_template'][0];

			/* Rebuild Form Meta Data to Load Single Values	*/
			foreach( $form_meta_data as $key => $value ) {
				$form_meta_data[$key] = $value[0];
			}

			/* If Email Template Selected Use That */
			if ( $template_id && $template_id != 'custom' ) {

				$template_array = self::get_email_template( $template_id );
				$confirm_subject = $template_array['subject'];
				$confirm_email_message = $template_array['body'];

			/* Else Use Custom Template */
			} else {

				$template = get_post($form_id);
				$content = $template->post_content;
				$confirm_subject = get_post_meta( $form_id, 'inbound_confirmation_subject', TRUE );
				$content = apply_filters('the_content', $content);
				$content = str_replace(']]>', ']]&gt;', $content);

				$confirm_email_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html;' . get_option('blog_charset') . '" /></head><body style="margin: 0px; background-color: #F4F3F4; font-family: Helvetica, Arial, sans-serif; font-size:12px;" text="#444444" bgcolor="#F4F3F4" link="#21759B" alink="#21759B" vlink="#21759B" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0"><table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" border="0"><tr>';
				$confirm_email_message .= $content;
				$confirm_email_message .= '</tr></table></body></html>';
			}



			$confirm_subject = $Inbound_Templating_Engine->replace_tokens( $confirm_subject, array($form_post_data, $form_meta_data ));

			/* add default subject if empty */
			if (!$confirm_subject) {
				$confirm_subject = __( 'Thank you!' , 'cta' );
			}

			$confirm_email_message = $Inbound_Templating_Engine->replace_tokens( $confirm_email_message , array( $form_post_data, $form_meta_data )	);


			$from_name = get_option( 'blogname' , '' );
			$from_email = get_option( 'admin_email' );

			$headers	= "From: " . $from_name . " <" . $from_email . ">\n";
			$headers .= 'Content-type: text/html';

			wp_mail( $lead_email, $confirm_subject , $confirm_email_message, $headers );

		}

		/* Get Email Template for New Lead Notification */
		static function get_new_lead_email_template( ) {

			$email_template = array();

			$templates = get_posts(array(
				'post_type' => 'email-template',
				'posts_per_page' => 1,
				'meta_key' => '_inbound_template_id',
				'meta_value' => 'inbound-new-lead-notification'
			));

			foreach ( $templates as $template ) {
				$email_template['ID'] = $template->ID;
				$email_template['subject'] = get_post_meta( $template->ID , 'inbound_email_subject_template' , true );
				$email_template['body'] = get_post_meta( $template->ID , 'inbound_email_body_template' , true );
			}

			return $email_template;
		}

		/* Get Email Template by ID */
		public static function get_email_template( $ID ) {

			$email_template = array();

			$template = get_post($ID);

			$email_template['ID'] = $template->ID;
			$email_template['subject'] = get_post_meta( $template->ID , 'inbound_email_subject_template' , true );
			$email_template['body'] = get_post_meta( $template->ID , 'inbound_email_body_template' , true );

			return $email_template;
		}

		/**
		*  Prepare an array of days, months, years. Make i18n ready
		*  @param STRING $case lets us know which array to return
		*
		*  @returns ARRAY of data
		*/
		public static function get_date_selectons( $case ) {

			switch( $case ) {

				case 'months':
					return array(
						'01' => __( 'Jan' , 'cta' ),
						'02' => __( 'Feb' , 'cta' ),
						'03' => __( 'Mar' , 'cta' ),
						'04' => __( 'Apr' , 'cta' ),
						'05' => __( 'May' , 'cta' ),
						'06' => __( 'Jun' , 'cta' ),
						'07' => __( 'Jul' , 'cta' ),
						'08' => __( 'Aug' , 'cta' ),
						'09' => __( 'Sep' , 'cta' ),
						'10' => __( 'Oct' , 'cta' ),
						'11' => __( 'Nov' , 'cta' ),
						'12' => __( 'Dec' , 'cta' )
					);
					break;
				case 'days' :
					return array (
						'01' => '01',	'02' => '02',	'03' => '03',	'04' => '04',	'05' => '05',
						'06' => '06',	'07' => '07',	'08' => '08',	'09' => '09',	'10' => '10',
						'11' => '11',	'12' => '12',	'13' => '13',	'14' => '14',	'15' => '15',
						'16' => '16',	'17' => '17',	'18' => '18',	'19' => '19',	'20' => '20',
						'21' => '21',	'22' => '22',	'23' => '23',	'24' => '24',	'25' => '25',
						'26' => '26',	'27' => '27',	'28' => '28',	'29' => '29',	'30' => '30',
						'31' => '31'
					);
					break;
				case 'years' :

					for ($i=1920;$i<2101;$i++) {
						$years[$i] = $i;
					}

					return $years;
					break;
			}
		}

		/**
		*  Prepare an array of country codes and country names. Make i18n ready
		*/
		public static function get_countries_array() {
			return array (
				 __( 'AF' , 'leads') => __( 'Afghanistan' , 'cta' ) ,
				 __( 'AX' , 'leads') => __( 'Aland Islands' , 'cta' ) ,
				 __( 'AL' , 'leads') => __( 'Albania' , 'cta' ) ,
				 __( 'DZ' , 'leads') => __( 'Algeria' , 'cta' ) ,
				 __( 'AS' , 'leads') => __( 'American Samoa' , 'cta' ) ,
				 __( 'AD' , 'leads') => __( 'Andorra' , 'cta' ) ,
				 __( 'AO' , 'leads') => __( 'Angola' , 'cta' ) ,
				 __( 'AI' , 'leads') => __( 'Anguilla' , 'cta' ) ,
				 __( 'AQ' , 'leads') => __( 'Antarctica' , 'cta' ) ,
				 __( 'AG' , 'leads') => __( 'Antigua and Barbuda' , 'cta' ) ,
				 __( 'AR' , 'leads') => __( 'Argentina' , 'cta' ) ,
				 __( 'AM' , 'leads') => __( 'Armenia' , 'cta' ) ,
				 __( 'AW' , 'leads') => __( 'Aruba' , 'cta' ) ,
				 __( 'AU' , 'leads') => __( 'Australia' , 'cta' ) ,
				 __( 'AT' , 'leads') => __( 'Austria' , 'cta' ) ,
				 __( 'AZ' , 'leads') => __( 'Azerbaijan' , 'cta' ) ,
				 __( 'BS' , 'leads') => __( 'Bahamas' , 'cta' ) ,
				 __( 'BH' , 'leads') => __( 'Bahrain' , 'cta' ) ,
				 __( 'BD' , 'leads') => __( 'Bangladesh' , 'cta' ) ,
				 __( 'BB' , 'leads') => __( 'Barbados' , 'cta' ) ,
				 __( 'BY' , 'leads') => __( 'Belarus' , 'cta' ) ,
				 __( 'BE' , 'leads') => __( 'Belgium' , 'cta' ) ,
				 __( 'BZ' , 'leads') => __( 'Belize' , 'cta' ) ,
				 __( 'BJ' , 'leads') => __( 'Benin' , 'cta' ) ,
				 __( 'BM' , 'leads') => __( 'Bermuda' , 'cta' ) ,
				 __( 'BT' , 'leads') => __( 'Bhutan' , 'cta' ) ,
				 __( 'BO' , 'leads') => __( 'Bolivia' , 'cta' ) ,
				 __( 'BA' , 'leads') => __( 'Bosnia and Herzegovina' , 'cta' ) ,
				 __( 'BW' , 'leads') => __( 'Botswana' , 'cta' ) ,
				 __( 'BV' , 'leads') => __( 'Bouvet Island' , 'cta' ) ,
				 __( 'BR' , 'leads') => __( 'Brazil' , 'cta' ) ,
				 __( 'IO' , 'leads') => __( 'British Indian Ocean Territory' , 'cta' ) ,
				 __( 'BN' , 'leads') => __( 'Brunei Darussalam' , 'cta' ) ,
				 __( 'BG' , 'leads') => __( 'Bulgaria' , 'cta' ) ,
				 __( 'BF' , 'leads') => __( 'Burkina Faso' , 'cta' ) ,
				 __( 'BI' , 'leads') => __( 'Burundi' , 'cta' ) ,
				 __( 'KH' , 'leads') => __( 'Cambodia' , 'cta' ) ,
				 __( 'CM' , 'leads') => __( 'Cameroon' , 'cta' ) ,
				 __( 'CA' , 'leads') => __( 'Canada' , 'cta' ) ,
				 __( 'CV' , 'leads') => __( 'Cape Verde' , 'cta' ) ,
				 __( 'BQ' , 'leads') => __( 'Caribbean Netherlands ' , 'cta' ) ,
				 __( 'KY' , 'leads') => __( 'Cayman Islands' , 'cta' ) ,
				 __( 'CF' , 'leads') => __( 'Central African Republic' , 'cta' ) ,
				 __( 'TD' , 'leads') => __( 'Chad' , 'cta' ) ,
				 __( 'CL' , 'leads') => __( 'Chile' , 'cta' ) ,
				 __( 'CN' , 'leads') => __( 'China' , 'cta' ) ,
				 __( 'CX' , 'leads') => __( 'Christmas Island' , 'cta' ) ,
				 __( 'CC' , 'leads') => __( 'Cocos (Keeling) Islands' , 'cta' ) ,
				 __( 'CO' , 'leads') => __( 'Colombia' , 'cta' ) ,
				 __( 'KM' , 'leads') => __( 'Comoros' , 'cta' ) ,
				 __( 'CG' , 'leads') => __( 'Congo' , 'cta' ) ,
				 __( 'CD' , 'leads') => __( 'Congo, Democratic Republic of' , 'cta' ) ,
				 __( 'CK' , 'leads') => __( 'Cook Islands' , 'cta' ) ,
				 __( 'CR' , 'leads') => __( 'Costa Rica' , 'cta' ) ,
				 __( 'CI' , 'leads') => __( 'Cote d\'Ivoire' , 'cta' ) ,
				 __( 'HR' , 'leads') => __( 'Croatia' , 'cta' ) ,
				 __( 'CU' , 'leads') => __( 'Cuba' , 'cta' ) ,
				 __( 'CW' , 'leads') => __( 'Curacao' , 'cta' ) ,
				 __( 'CY' , 'leads') => __( 'Cyprus' , 'cta' ) ,
				 __( 'CZ' , 'leads') => __( 'Czech Republic' , 'cta' ) ,
				 __( 'DK' , 'leads') => __( 'Denmark' , 'cta' ) ,
				 __( 'DJ' , 'leads') => __( 'Djibouti' , 'cta' ) ,
				 __( 'DM' , 'leads') => __( 'Dominica' , 'cta' ) ,
				 __( 'DO' , 'leads') => __( 'Dominican Republic' , 'cta' ) ,
				 __( 'EC' , 'leads') => __( 'Ecuador' , 'cta' ) ,
				 __( 'EG' , 'leads') => __( 'Egypt' , 'cta' ) ,
				 __( 'SV' , 'leads') => __( 'El Salvador' , 'cta' ) ,
				 __( 'GQ' , 'leads') => __( 'Equatorial Guinea' , 'cta' ) ,
				 __( 'ER' , 'leads') => __( 'Eritrea' , 'cta' ) ,
				 __( 'EE' , 'leads') => __( 'Estonia' , 'cta' ) ,
				 __( 'ET' , 'leads') => __( 'Ethiopia' , 'cta' ) ,
				 __( 'FK' , 'leads') => __( 'Falkland Islands' , 'cta' ) ,
				 __( 'FO' , 'leads') => __( 'Faroe Islands' , 'cta' ) ,
				 __( 'FJ' , 'leads') => __( 'Fiji' , 'cta' ) ,
				 __( 'FI' , 'leads') => __( 'Finland' , 'cta' ) ,
				 __( 'FR' , 'leads') => __( 'France' , 'cta' ) ,
				 __( 'GF' , 'leads') => __( 'French Guiana' , 'cta' ) ,
				 __( 'PF' , 'leads') => __( 'French Polynesia' , 'cta' ) ,
				 __( 'TF' , 'leads') => __( 'French Southern Territories' , 'cta' ) ,
				 __( 'GA' , 'leads') => __( 'Gabon' , 'cta' ) ,
				 __( 'GM' , 'leads') => __( 'Gambia' , 'cta' ) ,
				 __( 'GE' , 'leads') => __( 'Georgia' , 'cta' ) ,
				 __( 'DE' , 'leads') => __( 'Germany' , 'cta' ) ,
				 __( 'GH' , 'leads') => __( 'Ghana' , 'cta' ) ,
				 __( 'GI' , 'leads') => __( 'Gibraltar' , 'cta' ) ,
				 __( 'GR' , 'leads') => __( 'Greece' , 'cta' ) ,
				 __( 'GL' , 'leads') => __( 'Greenland' , 'cta' ) ,
				 __( 'GD' , 'leads') => __( 'Grenada' , 'cta' ) ,
				 __( 'GP' , 'leads') => __( 'Guadeloupe' , 'cta' ) ,
				 __( 'GU' , 'leads') => __( 'Guam' , 'cta' ) ,
				 __( 'GT' , 'leads') => __( 'Guatemala' , 'cta' ) ,
				 __( 'GG' , 'leads') => __( 'Guernsey' , 'cta' ) ,
				 __( 'GN' , 'leads') => __( 'Guinea' , 'cta' ) ,
				 __( 'GW' , 'leads') => __( 'Guinea-Bissau' , 'cta' ) ,
				 __( 'GY' , 'leads') => __( 'Guyana' , 'cta' ) ,
				 __( 'HT' , 'leads') => __( 'Haiti' , 'cta' ) ,
				 __( 'HM' , 'leads') => __( 'Heard and McDonald Islands' , 'cta' ) ,
				 __( 'HN' , 'leads') => __( 'Honduras' , 'cta' ) ,
				 __( 'HK' , 'leads') => __( 'Hong Kong' , 'cta' ) ,
				 __( 'HU' , 'leads') => __( 'Hungary' , 'cta' ) ,
				 __( 'IS' , 'leads') => __( 'Iceland' , 'cta' ) ,
				 __( 'IN' , 'leads') => __( 'India' , 'cta' ) ,
				 __( 'ID' , 'leads') => __( 'Indonesia' , 'cta' ) ,
				 __( 'IR' , 'leads') => __( 'Iran' , 'cta' ) ,
				 __( 'IQ' , 'leads') => __( 'Iraq' , 'cta' ) ,
				 __( 'IE' , 'leads') => __( 'Ireland' , 'cta' ) ,
				 __( 'IM' , 'leads') => __( 'Isle of Man' , 'cta' ) ,
				 __( 'IL' , 'leads') => __( 'Israel' , 'cta' ) ,
				 __( 'IT' , 'leads') => __( 'Italy' , 'cta' ) ,
				 __( 'JM' , 'leads') => __( 'Jamaica' , 'cta' ) ,
				 __( 'JP' , 'leads') => __( 'Japan' , 'cta' ) ,
				 __( 'JE' , 'leads') => __( 'Jersey' , 'cta' ) ,
				 __( 'JO' , 'leads') => __( 'Jordan' , 'cta' ) ,
				 __( 'KZ' , 'leads') => __( 'Kazakhstan' , 'cta' ) ,
				 __( 'KE' , 'leads') => __( 'Kenya' , 'cta' ) ,
				 __( 'KI' , 'leads') => __( 'Kiribati' , 'cta' ) ,
				 __( 'KW' , 'leads') => __( 'Kuwait' , 'cta' ) ,
				 __( 'KG' , 'leads') => __( 'Kyrgyzstan' , 'cta' ) ,
				 __( 'LA' , 'leads') => __( 'Lao People\'s Democratic Republic' , 'cta' ) ,
				 __( 'LV' , 'leads') => __( 'Latvia' , 'cta' ) ,
				 __( 'LB' , 'leads') => __( 'Lebanon' , 'cta' ) ,
				 __( 'LS' , 'leads') => __( 'Lesotho' , 'cta' ) ,
				 __( 'LR' , 'leads') => __( 'Liberia' , 'cta' ) ,
				 __( 'LY' , 'leads') => __( 'Libya' , 'cta' ) ,
				 __( 'LI' , 'leads') => __( 'Liechtenstein' , 'cta' ) ,
				 __( 'LT' , 'leads') => __( 'Lithuania' , 'cta' ) ,
				 __( 'LU' , 'leads') => __( 'Luxembourg' , 'cta' ) ,
				 __( 'MO' , 'leads') => __( 'Macau' , 'cta' ) ,
				 __( 'MK' , 'leads') => __( 'Macedonia' , 'cta' ) ,
				 __( 'MG' , 'leads') => __( 'Madagascar' , 'cta' ) ,
				 __( 'MW' , 'leads') => __( 'Malawi' , 'cta' ) ,
				 __( 'MY' , 'leads') => __( 'Malaysia' , 'cta' ) ,
				 __( 'MV' , 'leads') => __( 'Maldives' , 'cta' ) ,
				 __( 'ML' , 'leads') => __( 'Mali' , 'cta' ) ,
				 __( 'MT' , 'leads') => __( 'Malta' , 'cta' ) ,
				 __( 'MH' , 'leads') => __( 'Marshall Islands' , 'cta' ) ,
				 __( 'MQ' , 'leads') => __( 'Martinique' , 'cta' ) ,
				 __( 'MR' , 'leads') => __( 'Mauritania' , 'cta' ) ,
				 __( 'MU' , 'leads') => __( 'Mauritius' , 'cta' ) ,
				 __( 'YT' , 'leads') => __( 'Mayotte' , 'cta' ) ,
				 __( 'MX' , 'leads') => __( 'Mexico' , 'cta' ) ,
				 __( 'FM' , 'leads') => __( 'Micronesia, Federated States of' , 'cta' ) ,
				 __( 'MD' , 'leads') => __( 'Moldova' , 'cta' ) ,
				 __( 'MC' , 'leads') => __( 'Monaco' , 'cta' ) ,
				 __( 'MN' , 'leads') => __( 'Mongolia' , 'cta' ) ,
				 __( 'ME' , 'leads') => __( 'Montenegro' , 'cta' ) ,
				 __( 'MS' , 'leads') => __( 'Montserrat' , 'cta' ) ,
				 __( 'MA' , 'leads') => __( 'Morocco' , 'cta' ) ,
				 __( 'MZ' , 'leads') => __( 'Mozambique' , 'cta' ) ,
				 __( 'MM' , 'leads') => __( 'Myanmar' , 'cta' ) ,
				 __( 'NA' , 'leads') => __( 'Namibia' , 'cta' ) ,
				 __( 'NR' , 'leads') => __( 'Nauru' , 'cta' ) ,
				 __( 'NP' , 'leads') => __( 'Nepal' , 'cta' ) ,
				 __( 'NC' , 'leads') => __( 'New Caledonia' , 'cta' ) ,
				 __( 'NZ' , 'leads') => __( 'New Zealand' , 'cta' ) ,
				 __( 'NI' , 'leads') => __( 'Nicaragua' , 'cta' ) ,
				 __( 'NE' , 'leads') => __( 'Niger' , 'cta' ) ,
				 __( 'NG' , 'leads') => __( 'Nigeria' , 'cta' ) ,
				 __( 'NU' , 'leads') => __( 'Niue' , 'cta' ) ,
				 __( 'NF' , 'leads') => __( 'Norfolk Island' , 'cta' ) ,
				 __( 'KP' , 'leads') => __( 'North Korea' , 'cta' ) ,
				 __( 'MP' , 'leads') => __( 'Northern Mariana Islands' , 'cta' ) ,
				 __( 'NO' , 'leads') => __( 'Norway' , 'cta' ) ,
				 __( 'OM' , 'leads') => __( 'Oman' , 'cta' ) ,
				 __( 'PK' , 'leads') => __( 'Pakistan' , 'cta' ) ,
				 __( 'PW' , 'leads') => __( 'Palau' , 'cta' ) ,
				 __( 'PS' , 'leads') => __( 'Palestinian Territory, Occupied' , 'cta' ) ,
				 __( 'PA' , 'leads') => __( 'Panama' , 'cta' ) ,
				 __( 'PG' , 'leads') => __( 'Papua New Guinea' , 'cta' ) ,
				 __( 'PY' , 'leads') => __( 'Paraguay' , 'cta' ) ,
				 __( 'PE' , 'leads') => __( 'Peru' , 'cta' ) ,
				 __( 'PH' , 'leads') => __( 'Philippines' , 'cta' ) ,
				 __( 'PN' , 'leads') => __( 'Pitcairn' , 'cta' ) ,
				 __( 'PL' , 'leads') => __( 'Poland' , 'cta' ) ,
				 __( 'PT' , 'leads') => __( 'Portugal' , 'cta' ) ,
				 __( 'PR' , 'leads') => __( 'Puerto Rico' , 'cta' ) ,
				 __( 'QA' , 'leads') => __( 'Qatar' , 'cta' ) ,
				 __( 'RE' , 'leads') => __( 'Reunion' , 'cta' ) ,
				 __( 'RO' , 'leads') => __( 'Romania' , 'cta' ) ,
				 __( 'RU' , 'leads') => __( 'Russian Federation' , 'cta' ) ,
				 __( 'RW' , 'leads') => __( 'Rwanda' , 'cta' ) ,
				 __( 'BL' , 'leads') => __( 'Saint Barthelemy' , 'cta' ) ,
				 __( 'SH' , 'leads') => __( 'Saint Helena' , 'cta' ) ,
				 __( 'KN' , 'leads') => __( 'Saint Kitts and Nevis' , 'cta' ) ,
				 __( 'LC' , 'leads') => __( 'Saint Lucia' , 'cta' ) ,
				 __( 'VC' , 'leads') => __( 'Saint Vincent and the Grenadines' , 'cta' ) ,
				 __( 'MF' , 'leads') => __( 'Saint-Martin (France)' , 'cta' ) ,
				 __( 'SX' , 'leads') => __( 'Saint-Martin (Pays-Bas)' , 'cta' ) ,
				 __( 'WS' , 'leads') => __( 'Samoa' , 'cta' ) ,
				 __( 'SM' , 'leads') => __( 'San Marino' , 'cta' ) ,
				 __( 'ST' , 'leads') => __( 'Sao Tome and Principe' , 'cta' ) ,
				 __( 'SA' , 'leads') => __( 'Saudi Arabia' , 'cta' ) ,
				 __( 'SN' , 'leads') => __( 'Senegal' , 'cta' ) ,
				 __( 'RS' , 'leads') => __( 'Serbia' , 'cta' ) ,
				 __( 'SC' , 'leads') => __( 'Seychelles' , 'cta' ) ,
				 __( 'SL' , 'leads') => __( 'Sierra Leone' , 'cta' ) ,
				 __( 'SG' , 'leads') => __( 'Singapore' , 'cta' ) ,
				 __( 'SK' , 'leads') => __( 'Slovakia (Slovak Republic)' , 'cta' ) ,
				 __( 'SI' , 'leads') => __( 'Slovenia' , 'cta' ) ,
				 __( 'SB' , 'leads') => __( 'Solomon Islands' , 'cta' ) ,
				 __( 'SO' , 'leads') => __( 'Somalia' , 'cta' ) ,
				 __( 'ZA' , 'leads') => __( 'South Africa' , 'cta' ) ,
				 __( 'GS' , 'leads') => __( 'South Georgia and the South Sandwich Islands' , 'cta' ) ,
				 __( 'KR' , 'leads') => __( 'South Korea' , 'cta' ) ,
				 __( 'SS' , 'leads') => __( 'South Sudan' , 'cta' ) ,
				 __( 'ES' , 'leads') => __( 'Spain' , 'cta' ) ,
				 __( 'LK' , 'leads') => __( 'Sri Lanka' , 'cta' ) ,
				 __( 'PM' , 'leads') => __( 'St. Pierre and Miquelon' , 'cta' ) ,
				 __( 'SD' , 'leads') => __( 'Sudan' , 'cta' ) ,
				 __( 'SR' , 'leads') => __( 'Suriname' , 'cta' ) ,
				 __( 'SJ' , 'leads') => __( 'Svalbard and Jan Mayen Islands' , 'cta' ) ,
				 __( 'SZ' , 'leads') => __( 'Swaziland' , 'cta' ) ,
				 __( 'SE' , 'leads') => __( 'Sweden' , 'cta' ) ,
				 __( 'CH' , 'leads') => __( 'Switzerland' , 'cta' ) ,
				 __( 'SY' , 'leads') => __( 'Syria' , 'cta' ) ,
				 __( 'TW' , 'leads') => __( 'Taiwan' , 'cta' ) ,
				 __( 'TJ' , 'leads') => __( 'Tajikistan' , 'cta' ) ,
				 __( 'TZ' , 'leads') => __( 'Tanzania' , 'cta' ) ,
				 __( 'TH' , 'leads') => __( 'Thailand' , 'cta' ) ,
				 __( 'NL' , 'leads') => __( 'The Netherlands' , 'cta' ) ,
				 __( 'TL' , 'leads') => __( 'Timor-Leste' , 'cta' ) ,
				 __( 'TG' , 'leads') => __( 'Togo' , 'cta' ) ,
				 __( 'TK' , 'leads') => __( 'Tokelau' , 'cta' ) ,
				 __( 'TO' , 'leads') => __( 'Tonga' , 'cta' ) ,
				 __( 'TT' , 'leads') => __( 'Trinidad and Tobago' , 'cta' ) ,
				 __( 'TN' , 'leads') => __( 'Tunisia' , 'cta' ) ,
				 __( 'TR' , 'leads') => __( 'Turkey' , 'cta' ) ,
				 __( 'TM' , 'leads') => __( 'Turkmenistan' , 'cta' ) ,
				 __( 'TC' , 'leads') => __( 'Turks and Caicos Islands' , 'cta' ) ,
				 __( 'TV' , 'leads') => __( 'Tuvalu' , 'cta' ) ,
				 __( 'UG' , 'leads') => __( 'Uganda' , 'cta' ) ,
				 __( 'UA' , 'leads') => __( 'Ukraine' , 'cta' ) ,
				 __( 'AE' , 'leads') => __( 'United Arab Emirates' , 'cta' ) ,
				 __( 'GB' , 'leads') => __( 'United Kingdom' , 'cta' ) ,
				 __( 'US' , 'leads') => __( 'United States' , 'cta' ) ,
				 __( 'UM' , 'leads') => __( 'United States Minor Outlying Islands' , 'cta' ) ,
				 __( 'UY' , 'leads') => __( 'Uruguay' , 'cta' ) ,
				 __( 'UZ' , 'leads') => __( 'Uzbekistan' , 'cta' ) ,
				 __( 'VU' , 'leads') => __( 'Vanuatu' , 'cta' ) ,
				 __( 'VA' , 'leads') => __( 'Vatican' , 'cta' ) ,
				 __( 'VE' , 'leads') => __( 'Venezuela' , 'cta' ) ,
				 __( 'VN' , 'leads') => __( 'Vietnam' , 'cta' ) ,
				 __( 'VG' , 'leads') => __( 'Virgin Islands (British)' , 'cta' ) ,
				 __( 'VI' , 'leads') => __( 'Virgin Islands (U.S.)' , 'cta' ) ,
				 __( 'WF' , 'leads') => __( 'Wallis and Futuna Islands' , 'cta' ) ,
				 __( 'EH' , 'leads') => __( 'Western Sahara' , 'cta' ) ,
				 __( 'YE' , 'leads') => __( 'Yemen' , 'cta' ) ,
				 __( 'ZM' , 'leads') => __( 'Zambia' , 'cta' ) ,
				 __( 'ZW' , 'leads') => __( 'Zimbabwe' , 'cta' )
			);
		}

	}

	Inbound_Forms::init();
}
