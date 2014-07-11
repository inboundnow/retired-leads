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
		if (!preg_match_all('/(.?)\[(inbound_field)(.*?)\]/s',$content, $matches))
		{
			return '';

		}
		else
		{
			for($i = 0; $i < count($matches[0]); $i++)
			{
				$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
			}
			//print_r($matches[3]);
			// matches are $matches[3][$i]['label']
			$clean_form_id = preg_replace("/[^A-Za-z0-9 ]/", '', trim($name));
			$form_id = strtolower(str_replace(array(' ','_'),'-',$clean_form_id));


			$form = '<div id="inbound-form-wrapper" class="">';
			$form .= '<form class="inbound-now-form wpl-track-me" method="post" id="'.$form_id.'" action="" style="'.$form_width.'">';
			$main_layout = ($form_layout != "") ? 'inbound-'.$form_layout : 'inbound-normal';
			for($i = 0; $i < count($matches[0]); $i++)
			{

				$label = (isset($matches[3][$i]['label'])) ? $matches[3][$i]['label'] : '';


				$clean_label = preg_replace("/[^A-Za-z0-9 ]/", '', trim($label));
				$formatted_label = strtolower(str_replace(array(' ','_'),'-',$clean_label));
				$field_placeholder = (isset($matches[3][$i]['placeholder'])) ? $matches[3][$i]['placeholder'] : '';

				$placeholder_use = ($field_placeholder != "") ? $field_placeholder : $label;

				if ($field_placeholder != "")
				{
					$form_placeholder = "placeholder='".$placeholder_use."'";
				}
				else if (isset($form_labels) && $form_labels === "placeholder")
				{
					$form_placeholder = "placeholder='".$placeholder_use."'";
				}
				else
				{
					$form_placeholder = "";
				}

				$description_block = (isset($matches[3][$i]['description'])) ? $matches[3][$i]['description'] : '';
				$field_container_class = (isset($matches[3][$i]['field_container_class'])) ? $matches[3][$i]['field_container_class'] : '';
				$field_input_class = (isset($matches[3][$i]['field_input_class'])) ? $matches[3][$i]['field_input_class'] : '';
				$required = (isset($matches[3][$i]['required'])) ? $matches[3][$i]['required'] : '0';
				$req = ($required === '1') ? 'required' : '';
				$req_label = ($required === '1') ? '<span class="inbound-required">*</span>' : '';
				$map_field = (isset($matches[3][$i]['map_to'])) ? $matches[3][$i]['map_to'] : '';
				if ($map_field != "") {
					$field_name = $map_field;
				} else {
					$field_name = strtolower(str_replace(array(' ','_'),'-',$label));
				}


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

				$form .= '<div class="inbound-field '.$main_layout.' label-'.$form_labels_class.' '.$field_container_class.'">';

				if ($type != 'hidden' && $form_labels != "bottom" && $type != "html-block" && $type != "divider" || $type === "radio")
				{
					$form .= '<label for="'. $field_name .'" class="inbound-label '.$formatted_label.' '.$form_labels_class.' inbound-input-'.$type.'" style="'.$font_size.'">' . $matches[3][$i]['label'] . $req_label . '</label>';
				}

				if ($type === 'textarea') {
					$form .=	'<textarea class="inbound-input inbound-input-textarea '.$field_input_class.'" name="'.$field_name.'" id="in_'.$field_name.'" '.$req.'/>'.$placeholder_use.'</textarea>';
				}
				else if ($type === 'dropdown')
				{
					$dropdown_fields = array();
					$dropdown = $matches[3][$i]['dropdown'];
					$dropdown_fields = explode(",", $dropdown);

					$form .= '<select name="'. $field_name .'" class="'.$field_input_class.'"  '.$req.'>';

					if ($placeholder_use) {
						$form .= '<option value="" disabled selected>'.str_replace( '%3F' , '?' , $placeholder_use).'</option>';
					}

					foreach ($dropdown_fields as $key => $value) {
						//$drop_val_trimmed =	trim($value);
						//$dropdown_val = strtolower(str_replace(array(' ','_'),'-',$drop_val_trimmed));
						$form .= '<option value="'. trim(str_replace('"', '\"' , $value)) .'">'. $value .'</option>';
					}
					$form .= '</select>';
				}
				else if ($type === 'dropdown_countries')
				{

					$dropdown_fields = self::get_countries_array();

					$form .= '<select name="'. $field_name .'" class="'.$field_input_class.'" '.$req.'>';

					if ($field_placeholder) {
						$form .= '<option value="" disabled selected>'.$field_placeholder.'</option>';
					}

					foreach ($dropdown_fields as $key => $value) {
						$form .= '<option value="'.$key.'">'. utf8_encode($value) .'</option>';
					}
					$form .= '</select>';
				}
				else if ($type === 'radio')
				{
					$radio_fields = array();
					$radio = $matches[3][$i]['radio'];
					$radio_fields = explode(",", $radio);
					// $clean_radio = str_replace(array(' ','_'),'-',$value) // clean leading spaces. finish

					foreach ($radio_fields as $key => $value)
					{
						$radio_val_trimmed =	trim($value);
						$radio_val =	strtolower(str_replace(array(' ','_'),'-',$radio_val_trimmed));
						$form .= '<span class="radio-'.$main_layout.' radio-'.$form_labels_class.' '.$field_input_class.'"><input type="radio" name="'. $field_name .'" value="'. $radio_val .'">'. $radio_val_trimmed .'</span>';
					}
				}
				else if ($type === 'checkbox')
				{
					$checkbox_fields = array();

					$checkbox = $matches[3][$i]['checkbox'];
					$checkbox_fields = explode(",", $checkbox);
					// $clean_radio = str_replace(array(' ','_'),'-',$value) // clean leading spaces. finish
					foreach ($checkbox_fields as $key => $value) {
						$value = html_entity_decode($value);
						$checkbox_val_trimmed =	strip_tags(trim($value));
						$checkbox_val =	strtolower(str_replace(array(' ','_'),'-',$checkbox_val_trimmed));


						$required_id = ( $key == 0 ) ? $req : '';

						$form .= '<input class="checkbox-'.$main_layout.' checkbox-'.$form_labels_class.' '.$field_input_class.'" type="checkbox" name="'. $field_name .'" value="'. $checkbox_val .'" '.$required_id.'>'.$value.'<br>';
					}
				}
				else if ($type === 'html-block')
				{
					$html = $matches[3][$i]['html'];
					//echo $html;
					$form .= "<div class={$field_input_class}>";
					$form .= do_shortcode(html_entity_decode($html));
					$form .= "</div>";
				}
				else if ($type === 'divider')
				{
					$divider = $matches[3][$i]['divider_options'];
					//echo $html;
					$form .= "<div class='inbound-form-divider {$field_input_class}'>" . $divider . "<hr></div>";
				}
				else if ($type === 'editor')
				{
					//wp_editor(); // call wp editor
				}
				else
				{
					$hidden_param = (isset($matches[3][$i]['dynamic'])) ? $matches[3][$i]['dynamic'] : '';
					$fill_value = (isset($matches[3][$i]['default'])) ? $matches[3][$i]['default'] : '';
					$dynamic_value = (isset($_GET[$hidden_param])) ? $_GET[$hidden_param] : '';
					if ($type === 'hidden' && $dynamic_value != "") {
						$fill_value = $dynamic_value;
					}
					$form .=	'<input class="inbound-input inbound-input-text '.$formatted_label . $input_classes.' '.$field_input_class.'" name="'.$field_name.'" '.$form_placeholder.' id="'.$formatted_label.'" value="'.$fill_value.'" type="'.$type.'" '.$req.'/>';
				}
				if ($type != 'hidden' && $form_labels === "bottom" && $type != "radio" && $type != "html-block" && $type != "divider")
				{
					$form .= '<label for="'. $field_name .'" class="inbound-label '.$formatted_label.' '.$form_labels_class.' inbound-input-'.$type.'" style="'.$font_size.'">' . $matches[3][$i]['label'] . $req_label . '</label>';
				}

				if ($description_block != "" && $type != 'hidden'){
					$form .= "<div class='inbound-description'>".$description_block."</div>";
				}

				$form .= '</div>';
			}
			// End Loop

			$current_page = get_permalink();
			$form .= '<div class="inbound-field '.$main_layout.' inbound-submit-area"><button type="submit" class="inbound-button-submit inbound-submit-action" value="'.$submit_button.'" name="send" id="inbound_form_submit" style="'.$submit_bg.$submit_color.$image_button.'">
						'.$icon_insert.''.$submit_button.$inner_button.'</button></div><input type="hidden" name="inbound_submitted" value="1">';
					// <!--<input type="submit" '.$submit_button_type.' class="button" value="'.$submit_button.'" name="send" id="inbound_form_submit" />-->

			$form .= '<input type="hidden" name="inbound_form_name" class="inbound_form_name" value="'.$form_name.'"><input type="hidden" name="inbound_form_lists" id="inbound_form_lists" value="'.$lists.'"><input type="hidden" name="inbound_form_id" class="inbound_form_id" value="'.$id.'"><input type="hidden" name="inbound_current_page_url" value="'.$current_page.'"><input type="hidden" name="inbound_furl" value="'. base64_encode($redirect) .'"><input type="hidden" name="inbound_notify" value="'. base64_encode($notify) .'"></form></div>';
			$form .= "<style type='text/css'>.inbound-button-submit{ {$font_size} }</style>";
			$form = preg_replace('/<br class="inbr".\/>/', '', $form); // remove editor br tags

			return $form;
		}
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
	static function register_script()
	{
		wp_enqueue_style( 'inbound-shortcodes' );
	}

	// only call enqueue once
	static function print_script()
	{
		if ( ! self::$add_script )
		return;
		wp_enqueue_style( 'inbound-shortcodes' );
	}

	// move to file
	static function inline_my_script()
	{
		if ( ! self::$add_script )
			return;

		echo '<script type="text/javascript">
			jQuery(document).ready(function($){

	
			jQuery("form").submit(function(e) {
				jQuery(this).find("input").each(function(){
				    if(!jQuery(this).prop("required")){
				    } else if (!jQuery(this).val()) {
					alert("Oops! Looks like you have not filled out all of the required fields!");
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
		//$content = str_replace('{{form-name}}', $form_data['inbound_form_name']		, $content);

		foreach ($form_data as $key => $value) {
			$token_key = str_replace('_','-', $key);
			$token_key = str_replace('inbound-','', $token_key);

			$content = str_replace( '{{'.trim($token_key).'}}' , $value , $content );
		}

		return $content;
	}

	/* Legacy Lead Notification Email Method */
	static function send_mail($form_data, $form_meta_data)
	{
		add_filter( 'wp_mail_content_type', 'set_html_content_type' );
		function set_html_content_type() {
			return 'text/html';
		}

		$notification_status = "off";
		$email_to = false;
		$multi_send = false;

		if (isset($form_meta_data['inbound_email_send_notification'][0])){
			$notification_status = $form_meta_data['inbound_email_send_notification'][0];
		}

		if (isset($form_meta_data['inbound_notify_email'])){
			$email_to = $form_meta_data['inbound_notify_email'][0];
			$email_addresses = explode(",", $email_to[0]);
			if(is_array($email_addresses) && count($email_addresses) > 1) {
				$multi_send = true;
			}

			$email_subject = (isset($form_meta_data['inbound_notify_email_subject'])) ? $form_meta_data['inbound_notify_email_subject'] : 'Thank You';
		}

		/* print_r($form_meta_data); exit; */
		/* print_r($form_data); exit; */

		$form_email = false;
		foreach ($form_data as $key => $value) {
			if (preg_match('/email|e-mail/i', $key)) {
				$form_email = $form_data[$key];
			}

		}

		if (!$form_email) {
			if (isset($form_data['email'])) {
				$form_email = $form_data['email'];
			} else if (isset($form_data['e-mail'])) {
				$form_email = $form_data['e-mail'];
			} else if (isset($form_data['wpleads_email_address'])) {
				$form_email = $form_data['wpleads_email_address'];
			} else {
				$form_email = 'null map email field';
			}
		}

		/* Might be better email send need to test and look at html edd emails */
		if ( $form_email && $email_to )
		{

			// DO PHP LEAD SAVE HERE
			//
			$to = $email_to; // admin email or email from shortcode

			$admin_url = get_bloginfo( 'url' ) . "/wp-admin";
			$redirect_message = (isset($form_data['inbound_redirect']) && $form_data['inbound_redirect'] != "") ? "They were redirected to " . $form_data['inbound_redirect'] : '';
			$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
			$data_time = date('F jS, Y \a\t g:ia', $time);


			// get the website's name and puts it in front of the subject
			$email_subject = (isset($form_meta_data['inbound_notify_email_subject'][0])) ? $form_meta_data['inbound_notify_email_subject'][0] : '{{site-name}} - {{form-name}} - New Lead Conversion';

			$email_subject = apply_filters( 'inbound_replace_email_tokens' , $email_subject , $form_data , $form_meta_data);

			// get the message from the form and add the IP address of the user below it
		$email_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;" charset="UTF-8" />
<style type="text/css">
	html {
	background: #EEEDED;
	}
</style>
</head>
<body style="margin: 0px; background-color: #FFFFFF; font-family: Helvetica, Arial, sans-serif; font-size:12px;" text="#444444" bgcolor="#FFFFFF" link="#21759B" alink="#21759B" vlink="#21759B" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">

<table cellpadding="0" width="600" bgcolor="#FFFFFF" cellspacing="0" border="0" align="center" style="width:100%!important;line-height:100%!important;border-collapse:collapse;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
	<tbody><tr>
	<td valign="top" height="20">&nbsp;</td>
	</tr>
	<tr>
	<td valign="top">
		<table cellpadding="0" bgcolor="#ffffff" cellspacing="0" border="0" align="center" style="border-collapse:collapse;width:600px;font-size:13px;line-height:20px;color:#545454;font-family:Arial,sans-serif;border-radius:3px;margin-top:0;margin-right:auto;margin-bottom:0;margin-left:auto">
	<tbody><tr>
	<td valign="top">
		<table cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;width:100%;border-radius:3px 3px 0 0;font-size:1px;line-height:3px;height:3px;border-top-color:#0298e3;border-right-color:#0298e3;border-bottom-color:#0298e3;border-left-color:#0298e3;border-top-style:solid;border-right-style:solid;border-bottom-style:solid;border-left-style:solid;border-top-width:1px;border-right-width:1px;border-bottom-width:1px;border-left-width:1px">
			<tbody><tr>
			<td valign="top" style="font-family:Arial,sans-serif;background-color:#5ab8e7;border-top-width:1px;border-top-color:#8ccae9;border-top-style:solid" bgcolor="#5ab8e7">&nbsp;</td>
			</tr>
		</tbody></table>
		<table cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;width:600px;border-radius:0 0 3px 3px;border-top-color:#8c8c8c;border-right-color:#8c8c8c;border-bottom-color:#8c8c8c;border-left-color:#8c8c8c;border-top-style:solid;border-right-style:solid;border-bottom-style:solid;border-left-style:solid;border-top-width:0;border-right-width:1px;border-bottom-width:1px;border-left-width:1px">
		<tbody><tr>
			<td valign="top" style="font-size:13px;line-height:20px;color:#545454;font-family:Arial,sans-serif;border-radius:0 0 3px 3px;padding-top:3px;padding-right:30px;padding-bottom:15px;padding-left:30px">

	<h1 style="margin-top:20px;margin-right:0;margin-bottom:20px;margin-left:0; font-size:28px; line-height: 28px; color:#000;">New Lead on '.$form_data['inbound_form_name'].'</h1>
	<p style="margin-top:20px;margin-right:0;margin-bottom:20px;margin-left:0">There is a new lead that just converted on <strong>'.$data_time.'</strong> from page: '.$form_data['inbound_current_page_url'].'. '.$redirect_message.' </p>

<!-- NEW TABLE -->
<table class="heavyTable" style="width: 100%;
	max-width: 600px;
	border-collapse: collapse;
	border: 1px solid #cccccc;
	background: white;
	margin-bottom: 20px;">
	<tbody>
	<tr style="background: #3A9FD1; height: 54px; font-weight: lighter; color: #fff;border: 1px solid #3A9FD1;text-align: left; padding-left: 10px;">
			<td	align="left" width="600" style="-webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; color: #fff; font-weight: bold; text-decoration: none; font-family: Helvetica, Arial, sans-serif; display: block;">
				<h1 style="font-size: 30px; display: inline-block;margin-top: 15px;margin-left: 10px; margin-bottom: 0px; letter-spacing: 0px; word-spacing: 0px; font-weight: 300;">Lead Information</h1>
				<div style="float:right; margin-top: 5px; margin-right: 15px;"><!--[if mso]>
				<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.admin_url( 'edit.php?post_type=wp-lead&lead-email-redirect=' . $form_email ).'" style="height:40px;v-text-anchor:middle;width:130px;font-size:18px;" arcsize="10%" stroke="f" fillcolor="#ffffff">
					<w:anchorlock/>
					<center>
				<![endif]-->
					<a href="'.admin_url( 'edit.php?post_type=wp-lead&lead-email-redirect=' . $form_email ).'"
				style="background-color:#ffffff;border-radius:4px;color:#3A9FD1;display:inline-block;font-family:sans-serif;font-size:18px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:130px;-webkit-text-size-adjust:none;">View Lead</a>
				<!--[if mso]>
					</center>
				</v:roundrect>
				<![endif]-->
				</div>
			</td>
	</tr>';
// working!
	$exclude_array = array('Inbound Redirect', 'Inbound Submitted', 'Inbound Notify', 'Inbound Parent Page', 'Send', 'Inbound Furl' );

	$main_count = 0;
	$url_request = "";

	foreach ($form_data as $key => $value)
	{
		//array_push($action_categories, $ctaw_cat->category_nicename);
		$urlparam = ($main_count < 1 ) ?	"?" : "&";
		$url_request .= $urlparam . $key . "=" . urlencode($value);
		$name = str_replace(array('-','_'),' ', $key);
		$name = ucwords($name);


		$field_data = ($form_data[$key] != "") ? $form_data[$key] : "<span style='color:#949494; font-size: 10px;'>(Field left blank)</span>";

		if ($name === "Inbound Form Id" ) {
			$field_data = "<a title='View/Edit this form' href='" . admin_url( 'post.php?post=' . $field_data . '&action=edit' ). "'>".$field_data."</a>";
		}

		if ( $name === "Inbound Current Page Url" ) {
			$name = "Converted on Page";
			$page_converted_on = $field_data;
		}

		if(!in_array($name, $exclude_array)) {
		$email_message .= '
		<tr style="border-bottom: 1px solid #cccccc;">
		<td width="600" style="border-right: 1px solid #cccccc; padding: 10px; padding-bottom: 5px;">
		<div style="padding-left:5px; display:inline-block; padding-bottom: 5px; font-size: 16px; color:#555;">
		<strong>'.$name.':</strong></div>
		<div style="padding-left:5px; display:inline-block; font-size: 14px; color:#000;">'.$field_data.'</div>
		</td>
		</tr>';
		}
		$main_count++;
	}

	$email_message .= '<!-- IF CHAR COUNT OVER 50 make label display block -->

	</tbody>
 </table>
 <!-- END NEW TABLE -->
<!-- Start 3 col -->
<table style="margin-bottom: 20px; border: 1px solid #cccccc; border-collapse: collapse;" width="100%" border="1" BORDERWIDTH="1" BORDERCOLOR="CCCCCC" cellspacing="0" cellpadding="5" align="left" valign="top" borderspacing="0" >

<tbody valign="top">
 <tr valign="top" border="0">
	<td width="160" height="50" align="center" valign="top" border="0">
	<h3 style="color:#2e2e2e;font-size:15px;"><a style="text-decoration: none;" href="'.admin_url( 'edit.php?post_type=wp-lead&lead-email-redirect=' . $form_email . '&tab=tabs-wpleads_lead_tab_conversions' ).'">View Lead Activity</a></h3>
	</td>

	<td width="160" height="50" align="center" valign="top" border="0">
	<h3 style="color:#2e2e2e;font-size:15px;"><a style="text-decoration: none;" href="'.admin_url( 'edit.php?post_type=wp-lead&lead-email-redirect=' . $form_email . '&scroll-to=wplead_metabox_conversion' ).'">Pages Viewed</a></h3>
	</td>

 <td width="160" height="50" align="center" valign="top" border="0">
	<h3 style="color:#2e2e2e;font-size:15px;"><a style="text-decoration: none;" href="'.admin_url( 'edit.php?post_type=wp-lead&lead-email-redirect=' . $form_email . '&tab=tabs-wpleads_lead_tab_raw_form_data' ).'">View Form Data</a></h3>
 </td>
 </tr>
</tbody></table>
<!-- end 3 col -->
 <!-- Start half/half -->
 <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:10px;">
	<tbody><tr>
		<td align="center" width="250" height="30" cellpadding="5">
		<div><!--[if mso]>
			<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.admin_url( 'edit.php?post_type=wp-lead&lead-email-redirect=' . $form_email ).'" style="height:40px;v-text-anchor:middle;width:250px;" arcsize="10%" strokecolor="#7490af" fillcolor="#3A9FD1">
			<w:anchorlock/>
			<center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;">View Lead</center>
			</v:roundrect>
		<![endif]--><a href="'.admin_url( 'edit.php?post_type=wp-lead&lead-email-redirect=' . $form_email ).'"
		style="background-color:#3A9FD1;border:1px solid #7490af;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:18px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:250px;-webkit-text-size-adjust:none;mso-hide:all;" title="View the full Lead details in WordPress">View Full Lead Details</a>
		</div>
		</td>

		<td align="center" width="250" height="30" cellpadding="5">
		<div><!--[if mso]>
			<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="mailto:'.$form_email.'?subject=RE: '.$form_data['inbound_form_name'].'&body=Thanks for filling out our form." style="height:40px;v-text-anchor:middle;width:250px;" arcsize="10%" strokecolor="#558939" fillcolor="#59b329">
			<w:anchorlock/>
			<center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;">Reply to Lead Now</center>
			</v:roundrect>
		<![endif]--><a href="mailto:'.$form_email.'?subject=RE: '.$form_data['inbound_form_name'].'&body=Thanks for filling out our form on '.$page_converted_on.'"
		style="background-color:#59b329;border:1px solid #558939;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:18px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:250px;-webkit-text-size-adjust:none;mso-hide:all;" title="Email This Lead now">Reply to Lead Now</a></div>

		</td>
	</tr>
	</tbody>
 </table>
<!-- End half/half -->

			</td>
		</tr>
		</tbody></table>
	</td>
	</tr>
</tbody></table>
<table cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse;width:600px;font-size:13px;line-height:20px;color:#545454;font-family:Arial,sans-serif;margin-top:0;margin-right:auto;margin-bottom:0;margin-left:auto">
	<tbody><tr>
	<td valign="top" width="30" style="color:#272727">&nbsp;</td>
	<td valign="top" height="18" style="height:18px;color:#272727"></td>
		<td style="color:#272727">&nbsp;</td>
	<td style="color:#545454;text-align:right" align="right">&nbsp;</td>
	<td valign="middle" width="30" style="color:#272727">&nbsp;</td>
	</tr>
	<tr>
	<td valign="middle" width="30" style="color:#272727">&nbsp;</td>
		<td width="50" height="40" valign="middle" align="left" style="color:#272727">
		<img src="'. INBOUND_FORMS . 'images/inbound-email.png" height="40" width="40" alt=" " style="outline:none;text-decoration:none;max-width:100%;display:block;width:40px;min-height:40px;border-radius:20px">
		</td>
	<td style="color:#272727">
		<b>Leads</b>
		from Inbound Now
	</td>
	<td valign="middle" align="left" style="color:#545454;text-align:right">'.$data_time.'</td>
	<td valign="middle" width="30" style="color:#272727">&nbsp;</td>
	</tr>
	<tr>
	<td valign="top" height="6" style="color:#272727;line-height:1px">&nbsp;</td>
	<td style="color:#272727;line-height:1px">&nbsp;</td>
		<td style="color:#272727;line-height:1px">&nbsp;</td>
	<td style="color:#545454;text-align:right;line-height:1px" align="right">&nbsp;</td>
	<td valign="middle" width="30" style="color:#272727;line-height:1px">&nbsp;</td>
	</tr>
</tbody></table>

		<table cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse;width:600px">
		<tbody><tr>
			<td valign="top" style="color:#b1b1b1;font-size:11px;line-height:16px;font-family:Arial,sans-serif;text-align:center" align="center">
			<p style="margin-top:1em;margin-right:0;margin-bottom:1em;margin-left:0"></p>
			</td>
		</tr>
		</tbody></table>
	</td>
	</tr>
	<tr>
	<td valign="top" height="20">&nbsp;</td>
	</tr>
</tbody></table>
</body>';

			if (isset($form_data['first-name']) && isset($form_data['last-name']))
			{
				$from_name = $form_data['first-name'] . " ". $form_data['last-name'];
			}
			else if (isset($form_data['first-name']))
			{
				$from_name = $form_data['first-name'];
			}
			else
			{
				$from_name = get_bloginfo( 'name' );
			}
			// set the e-mail headers with the user's name, e-mail address and character encoding
			$headers	= "From: " . $from_name . " <" . $form_email . ">\n";
			$headers .= 'Content-type: text/html';
			// send the e-mail with the shortcode attribute named 'email' and the POSTed data
			if($multi_send) {
				foreach ($email_addresses as $key => $recipient) {
				wp_mail( $recipient, stripslashes($email_subject), $email_message, $headers );
				}
			} else {

				wp_mail( $to, stripslashes($email_subject), $email_message, $headers );
			}

			// and set the result text to the shortcode attribute named 'success'
			//$result = $success;
			// ...and switch the $sent variable to TRUE
			$sent = true;
			//print_r($email_message); // preview email


		}

	}

	/* Perform Actions After a Form Submit */
	static function do_actions(){

		if(isset($_POST['inbound_submitted']) && $_POST['inbound_submitted'] === '1')
		{
			/* get form submitted form's meta data */
			$form_meta_data = get_post_meta( $_POST['inbound_form_id'] );

			if(isset($_POST['inbound_furl']) && $_POST['inbound_furl'] != "") {
				$redirect = base64_decode($_POST['inbound_furl']);
			} else if (isset($_POST['inbound_current_page_url'])) {
				$redirect = $_POST['inbound_current_page_url'];
			}

			// Save Form Conversion to Form CPT
			if(isset($_POST['inbound_form_id']) && $_POST['inbound_form_id'] != "") {
				$form_id = $_POST['inbound_form_id'];
				// Increment Form Conversion Count
				//$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
				// $wordpress_date_time = date("Y-m-d G:i:s", $time);
				$form_conversion_num = get_post_meta($form_id, 'inbound_form_conversion_count', true);
				$form_conversion_num++;
				update_post_meta( $form_id, 'inbound_form_conversion_count', $form_conversion_num );
				// Add Lead Email to Conversions List

				if ( isset($_POST['email'])) {
					$lead_conversion_list = get_post_meta( $form_id, 'lead_conversion_list', TRUE );
					$lead_conversion_list = json_decode($lead_conversion_list,true);
					if (is_array($lead_conversion_list)) {
						$lead_count = count($lead_conversion_list);
						$lead_conversion_list[$lead_count]['email'] = $_POST['email'];
						// $lead_conversion_list[$lead_count]['date'] = $wordpress_date_time;
						$lead_conversion_list = json_encode($lead_conversion_list);
						update_post_meta( $form_id, 'lead_conversion_list', $lead_conversion_list );
					} else {
						$lead_conversion_list = array();
						$lead_conversion_list[0]['email'] = $_POST['email'];
						//	$lead_conversion_list[0]['date'] = $wordpress_date_time;
						$lead_conversion_list = json_encode($lead_conversion_list);
						update_post_meta( $form_id, 'lead_conversion_list', $lead_conversion_list );
					}
				}
			}


			//print_r($_POST);
			foreach ( $_POST as $field => $value ) {

				if ( get_magic_quotes_gpc() ) {
					$value = stripslashes( $value );
				}

				$field = strtolower($field);

				if (preg_match( '/Email|e-mail|email/i', $field)) {
					$field = "wpleads_email_address";
				}

				if (preg_match( '/(?<!((last |last_)))name(?!\=)/im', $field) && !isset($form_data['first-name'])) {
					$field = "wpleads_first_name";
				}

				if (preg_match( '/(?<!((first)))(last name|last_name|last)(?!\=)/im', $field) && !isset($form_data['last-name'])) {
					$field = "wpleads_last_name";
				}

				if (preg_match( '/Phone|phone number|telephone/i', $field)) {
					$field = "wpleads_work_phone";
				}

				$form_post_data[$field] = strip_tags( $value );
			}
			
			
			$form_meta_data['post_id'] = $_POST['inbound_form_id']; // pass in form id
			
			/* Send emails if passes spam checks - spam checks happen on lead store ajax script and here on the email actions script - redundantly */
			if (!apply_filters( 'form_submission_spam_check' , $form_post_data ) ) {
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

			add_filter( 'wp_mail_content_type', 'set_html_content_type' );
			function set_html_content_type() {
				return 'text/html';
			}

			/* Rebuild Form Meta Data to Load Single Values	*/
			foreach( $form_meta_data as $key => $value ) {
				$form_meta_data[$key] = $value[0];
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
					$from_email = $form_post_data[$key];
				}
			}

			/* Prepare Additional Data For Token Engine */
			$form_post_data['redirect_message'] = (isset($form_post_data['inbound_redirect']) && $form_post_data['inbound_redirect'] != "") ? "They were redirected to " . $form_post_data['inbound_redirect'] : '';

			/* Discover From Name */
			$from_name = get_option( 'blogname' , '' );
			$Inbound_Templating_Engine = Inbound_Templating_Engine();
			$subject = $Inbound_Templating_Engine->replace_tokens( $subject , array( $form_post_data , $form_meta_data )	);
			$body = $Inbound_Templating_Engine->replace_tokens( $template['body'] , array( $form_post_data , $form_meta_data )	);


			$headers = 'From: '. $from_name .' <'. $from_email .'>' . "\r\n";
			$headers = apply_filters( 'inbound_lead_notification_email_headers' , $headers );

			foreach ($to_address as $key => $recipient) {
				$result = wp_mail( $recipient , $subject , $body , $headers );
			}

		} else {

			/* Run Legacy Code */
			self::send_mail($form_post_data , $form_meta_data);

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

		}
		/* Else Use Custom Template */
		else {

			$template = get_post($form_id);
			$content = $template->post_content;
			$confirm_subject = get_post_meta( $form_id, 'inbound_confirmation_subject', TRUE );
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);

			$confirm_email_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html;' . get_option('blog_charset') . '" />
				</head>
				<body style="margin: 0px; background-color: #F4F3F4; font-family: Helvetica, Arial, sans-serif; font-size:12px;" text="#444444" bgcolor="#F4F3F4" link="#21759B" alink="#21759B" vlink="#21759B" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" border="0">
					<tr>';
			$confirm_email_message .= $content;
			$confirm_email_message .= '</tr>
							</table>
						</body>
						</html>';
		}



		$confirm_subject = $Inbound_Templating_Engine->replace_tokens( $confirm_subject , array( $form_post_data , $form_meta_data )	);
		$confirm_email_message = $Inbound_Templating_Engine->replace_tokens( $confirm_email_message , array( $form_post_data , $form_meta_data )	);


		$from_name = get_option( 'blogname' , '' );
		$from_email = get_option( 'admin_email' );

		$headers	= "From: " . $from_name . " <" . $from_email . ">\n";
		$headers .= 'Content-type: text/html';

		wp_mail( $lead_email, $confirm_subject , $confirm_email_message, $headers );

	}

	/* Get Email Template for New Lead Notification */
	public static function get_new_lead_email_template( ) {

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

	public static function get_countries_array() {
		return array (
			 __( 'AF' , 'leads') => __( 'Afghanistan' , 'leads' ) ,
			 __( 'AX' , 'leads') => __( 'Aland Islands' , 'leads' ) ,
			 __( 'AL' , 'leads') => __( 'Albania' , 'leads' ) ,
			 __( 'DZ' , 'leads') => __( 'Algeria' , 'leads' ) ,
			 __( 'AS' , 'leads') => __( 'American Samoa' , 'leads' ) ,
			 __( 'AD' , 'leads') => __( 'Andorra' , 'leads' ) ,
			 __( 'AO' , 'leads') => __( 'Angola' , 'leads' ) ,
			 __( 'AI' , 'leads') => __( 'Anguilla' , 'leads' ) ,
			 __( 'AQ' , 'leads') => __( 'Antarctica' , 'leads' ) ,
			 __( 'AG' , 'leads') => __( 'Antigua and Barbuda' , 'leads' ) ,
			 __( 'AR' , 'leads') => __( 'Argentina' , 'leads' ) ,
			 __( 'AM' , 'leads') => __( 'Armenia' , 'leads' ) ,
			 __( 'AW' , 'leads') => __( 'Aruba' , 'leads' ) ,
			 __( 'AU' , 'leads') => __( 'Australia' , 'leads' ) ,
			 __( 'AT' , 'leads') => __( 'Austria' , 'leads' ) ,
			 __( 'AZ' , 'leads') => __( 'Azerbaijan' , 'leads' ) ,
			 __( 'BS' , 'leads') => __( 'Bahamas' , 'leads' ) ,
			 __( 'BH' , 'leads') => __( 'Bahrain' , 'leads' ) ,
			 __( 'BD' , 'leads') => __( 'Bangladesh' , 'leads' ) ,
			 __( 'BB' , 'leads') => __( 'Barbados' , 'leads' ) ,
			 __( 'BY' , 'leads') => __( 'Belarus' , 'leads' ) ,
			 __( 'BE' , 'leads') => __( 'Belgium' , 'leads' ) ,
			 __( 'BZ' , 'leads') => __( 'Belize' , 'leads' ) ,
			 __( 'BJ' , 'leads') => __( 'Benin' , 'leads' ) ,
			 __( 'BM' , 'leads') => __( 'Bermuda' , 'leads' ) ,
			 __( 'BT' , 'leads') => __( 'Bhutan' , 'leads' ) ,
			 __( 'BO' , 'leads') => __( 'Bolivia' , 'leads' ) ,
			 __( 'BA' , 'leads') => __( 'Bosnia and Herzegovina' , 'leads' ) ,
			 __( 'BW' , 'leads') => __( 'Botswana' , 'leads' ) ,
			 __( 'BV' , 'leads') => __( 'Bouvet Island' , 'leads' ) ,
			 __( 'BR' , 'leads') => __( 'Brazil' , 'leads' ) ,
			 __( 'IO' , 'leads') => __( 'British Indian Ocean Territory' , 'leads' ) ,
			 __( 'BN' , 'leads') => __( 'Brunei Darussalam' , 'leads' ) ,
			 __( 'BG' , 'leads') => __( 'Bulgaria' , 'leads' ) ,
			 __( 'BF' , 'leads') => __( 'Burkina Faso' , 'leads' ) ,
			 __( 'BI' , 'leads') => __( 'Burundi' , 'leads' ) ,
			 __( 'KH' , 'leads') => __( 'Cambodia' , 'leads' ) ,
			 __( 'CM' , 'leads') => __( 'Cameroon' , 'leads' ) ,
			 __( 'CA' , 'leads') => __( 'Canada' , 'leads' ) ,
			 __( 'CV' , 'leads') => __( 'Cape Verde' , 'leads' ) ,
			 __( 'BQ' , 'leads') => __( 'Caribbean Netherlands ' , 'leads' ) ,
			 __( 'KY' , 'leads') => __( 'Cayman Islands' , 'leads' ) ,
			 __( 'CF' , 'leads') => __( 'Central African Republic' , 'leads' ) ,
			 __( 'TD' , 'leads') => __( 'Chad' , 'leads' ) ,
			 __( 'CL' , 'leads') => __( 'Chile' , 'leads' ) ,
			 __( 'CN' , 'leads') => __( 'China' , 'leads' ) ,
			 __( 'CX' , 'leads') => __( 'Christmas Island' , 'leads' ) ,
			 __( 'CC' , 'leads') => __( 'Cocos (Keeling) Islands' , 'leads' ) ,
			 __( 'CO' , 'leads') => __( 'Colombia' , 'leads' ) ,
			 __( 'KM' , 'leads') => __( 'Comoros' , 'leads' ) ,
			 __( 'CG' , 'leads') => __( 'Congo' , 'leads' ) ,
			 __( 'CD' , 'leads') => __( 'Congo, Democratic Republic of' , 'leads' ) ,
			 __( 'CK' , 'leads') => __( 'Cook Islands' , 'leads' ) ,
			 __( 'CR' , 'leads') => __( 'Costa Rica' , 'leads' ) ,
			 __( 'CI' , 'leads') => __( 'Cote d\'Ivoire' , 'leads' ) ,
			 __( 'HR' , 'leads') => __( 'Croatia' , 'leads' ) ,
			 __( 'CU' , 'leads') => __( 'Cuba' , 'leads' ) ,
			 __( 'CW' , 'leads') => __( 'Curacao' , 'leads' ) ,
			 __( 'CY' , 'leads') => __( 'Cyprus' , 'leads' ) ,
			 __( 'CZ' , 'leads') => __( 'Czech Republic' , 'leads' ) ,
			 __( 'DK' , 'leads') => __( 'Denmark' , 'leads' ) ,
			 __( 'DJ' , 'leads') => __( 'Djibouti' , 'leads' ) ,
			 __( 'DM' , 'leads') => __( 'Dominica' , 'leads' ) ,
			 __( 'DO' , 'leads') => __( 'Dominican Republic' , 'leads' ) ,
			 __( 'EC' , 'leads') => __( 'Ecuador' , 'leads' ) ,
			 __( 'EG' , 'leads') => __( 'Egypt' , 'leads' ) ,
			 __( 'SV' , 'leads') => __( 'El Salvador' , 'leads' ) ,
			 __( 'GQ' , 'leads') => __( 'Equatorial Guinea' , 'leads' ) ,
			 __( 'ER' , 'leads') => __( 'Eritrea' , 'leads' ) ,
			 __( 'EE' , 'leads') => __( 'Estonia' , 'leads' ) ,
			 __( 'ET' , 'leads') => __( 'Ethiopia' , 'leads' ) ,
			 __( 'FK' , 'leads') => __( 'Falkland Islands' , 'leads' ) ,
			 __( 'FO' , 'leads') => __( 'Faroe Islands' , 'leads' ) ,
			 __( 'FJ' , 'leads') => __( 'Fiji' , 'leads' ) ,
			 __( 'FI' , 'leads') => __( 'Finland' , 'leads' ) ,
			 __( 'FR' , 'leads') => __( 'France' , 'leads' ) ,
			 __( 'GF' , 'leads') => __( 'French Guiana' , 'leads' ) ,
			 __( 'PF' , 'leads') => __( 'French Polynesia' , 'leads' ) ,
			 __( 'TF' , 'leads') => __( 'French Southern Territories' , 'leads' ) ,
			 __( 'GA' , 'leads') => __( 'Gabon' , 'leads' ) ,
			 __( 'GM' , 'leads') => __( 'Gambia' , 'leads' ) ,
			 __( 'GE' , 'leads') => __( 'Georgia' , 'leads' ) ,
			 __( 'DE' , 'leads') => __( 'Germany' , 'leads' ) ,
			 __( 'GH' , 'leads') => __( 'Ghana' , 'leads' ) ,
			 __( 'GI' , 'leads') => __( 'Gibraltar' , 'leads' ) ,
			 __( 'GR' , 'leads') => __( 'Greece' , 'leads' ) ,
			 __( 'GL' , 'leads') => __( 'Greenland' , 'leads' ) ,
			 __( 'GD' , 'leads') => __( 'Grenada' , 'leads' ) ,
			 __( 'GP' , 'leads') => __( 'Guadeloupe' , 'leads' ) ,
			 __( 'GU' , 'leads') => __( 'Guam' , 'leads' ) ,
			 __( 'GT' , 'leads') => __( 'Guatemala' , 'leads' ) ,
			 __( 'GG' , 'leads') => __( 'Guernsey' , 'leads' ) ,
			 __( 'GN' , 'leads') => __( 'Guinea' , 'leads' ) ,
			 __( 'GW' , 'leads') => __( 'Guinea-Bissau' , 'leads' ) ,
			 __( 'GY' , 'leads') => __( 'Guyana' , 'leads' ) ,
			 __( 'HT' , 'leads') => __( 'Haiti' , 'leads' ) ,
			 __( 'HM' , 'leads') => __( 'Heard and McDonald Islands' , 'leads' ) ,
			 __( 'HN' , 'leads') => __( 'Honduras' , 'leads' ) ,
			 __( 'HK' , 'leads') => __( 'Hong Kong' , 'leads' ) ,
			 __( 'HU' , 'leads') => __( 'Hungary' , 'leads' ) ,
			 __( 'IS' , 'leads') => __( 'Iceland' , 'leads' ) ,
			 __( 'IN' , 'leads') => __( 'India' , 'leads' ) ,
			 __( 'ID' , 'leads') => __( 'Indonesia' , 'leads' ) ,
			 __( 'IR' , 'leads') => __( 'Iran' , 'leads' ) ,
			 __( 'IQ' , 'leads') => __( 'Iraq' , 'leads' ) ,
			 __( 'IE' , 'leads') => __( 'Ireland' , 'leads' ) ,
			 __( 'IM' , 'leads') => __( 'Isle of Man' , 'leads' ) ,
			 __( 'IL' , 'leads') => __( 'Israel' , 'leads' ) ,
			 __( 'IT' , 'leads') => __( 'Italy' , 'leads' ) ,
			 __( 'JM' , 'leads') => __( 'Jamaica' , 'leads' ) ,
			 __( 'JP' , 'leads') => __( 'Japan' , 'leads' ) ,
			 __( 'JE' , 'leads') => __( 'Jersey' , 'leads' ) ,
			 __( 'JO' , 'leads') => __( 'Jordan' , 'leads' ) ,
			 __( 'KZ' , 'leads') => __( 'Kazakhstan' , 'leads' ) ,
			 __( 'KE' , 'leads') => __( 'Kenya' , 'leads' ) ,
			 __( 'KI' , 'leads') => __( 'Kiribati' , 'leads' ) ,
			 __( 'KW' , 'leads') => __( 'Kuwait' , 'leads' ) ,
			 __( 'KG' , 'leads') => __( 'Kyrgyzstan' , 'leads' ) ,
			 __( 'LA' , 'leads') => __( 'Lao People\'s Democratic Republic' , 'leads' ) ,
			 __( 'LV' , 'leads') => __( 'Latvia' , 'leads' ) ,
			 __( 'LB' , 'leads') => __( 'Lebanon' , 'leads' ) ,
			 __( 'LS' , 'leads') => __( 'Lesotho' , 'leads' ) ,
			 __( 'LR' , 'leads') => __( 'Liberia' , 'leads' ) ,
			 __( 'LY' , 'leads') => __( 'Libya' , 'leads' ) ,
			 __( 'LI' , 'leads') => __( 'Liechtenstein' , 'leads' ) ,
			 __( 'LT' , 'leads') => __( 'Lithuania' , 'leads' ) ,
			 __( 'LU' , 'leads') => __( 'Luxembourg' , 'leads' ) ,
			 __( 'MO' , 'leads') => __( 'Macau' , 'leads' ) ,
			 __( 'MK' , 'leads') => __( 'Macedonia' , 'leads' ) ,
			 __( 'MG' , 'leads') => __( 'Madagascar' , 'leads' ) ,
			 __( 'MW' , 'leads') => __( 'Malawi' , 'leads' ) ,
			 __( 'MY' , 'leads') => __( 'Malaysia' , 'leads' ) ,
			 __( 'MV' , 'leads') => __( 'Maldives' , 'leads' ) ,
			 __( 'ML' , 'leads') => __( 'Mali' , 'leads' ) ,
			 __( 'MT' , 'leads') => __( 'Malta' , 'leads' ) ,
			 __( 'MH' , 'leads') => __( 'Marshall Islands' , 'leads' ) ,
			 __( 'MQ' , 'leads') => __( 'Martinique' , 'leads' ) ,
			 __( 'MR' , 'leads') => __( 'Mauritania' , 'leads' ) ,
			 __( 'MU' , 'leads') => __( 'Mauritius' , 'leads' ) ,
			 __( 'YT' , 'leads') => __( 'Mayotte' , 'leads' ) ,
			 __( 'MX' , 'leads') => __( 'Mexico' , 'leads' ) ,
			 __( 'FM' , 'leads') => __( 'Micronesia, Federated States of' , 'leads' ) ,
			 __( 'MD' , 'leads') => __( 'Moldova' , 'leads' ) ,
			 __( 'MC' , 'leads') => __( 'Monaco' , 'leads' ) ,
			 __( 'MN' , 'leads') => __( 'Mongolia' , 'leads' ) ,
			 __( 'ME' , 'leads') => __( 'Montenegro' , 'leads' ) ,
			 __( 'MS' , 'leads') => __( 'Montserrat' , 'leads' ) ,
			 __( 'MA' , 'leads') => __( 'Morocco' , 'leads' ) ,
			 __( 'MZ' , 'leads') => __( 'Mozambique' , 'leads' ) ,
			 __( 'MM' , 'leads') => __( 'Myanmar' , 'leads' ) ,
			 __( 'NA' , 'leads') => __( 'Namibia' , 'leads' ) ,
			 __( 'NR' , 'leads') => __( 'Nauru' , 'leads' ) ,
			 __( 'NP' , 'leads') => __( 'Nepal' , 'leads' ) ,
			 __( 'NC' , 'leads') => __( 'New Caledonia' , 'leads' ) ,
			 __( 'NZ' , 'leads') => __( 'New Zealand' , 'leads' ) ,
			 __( 'NI' , 'leads') => __( 'Nicaragua' , 'leads' ) ,
			 __( 'NE' , 'leads') => __( 'Niger' , 'leads' ) ,
			 __( 'NG' , 'leads') => __( 'Nigeria' , 'leads' ) ,
			 __( 'NU' , 'leads') => __( 'Niue' , 'leads' ) ,
			 __( 'NF' , 'leads') => __( 'Norfolk Island' , 'leads' ) ,
			 __( 'KP' , 'leads') => __( 'North Korea' , 'leads' ) ,
			 __( 'MP' , 'leads') => __( 'Northern Mariana Islands' , 'leads' ) ,
			 __( 'NO' , 'leads') => __( 'Norway' , 'leads' ) ,
			 __( 'OM' , 'leads') => __( 'Oman' , 'leads' ) ,
			 __( 'PK' , 'leads') => __( 'Pakistan' , 'leads' ) ,
			 __( 'PW' , 'leads') => __( 'Palau' , 'leads' ) ,
			 __( 'PS' , 'leads') => __( 'Palestinian Territory, Occupied' , 'leads' ) ,
			 __( 'PA' , 'leads') => __( 'Panama' , 'leads' ) ,
			 __( 'PG' , 'leads') => __( 'Papua New Guinea' , 'leads' ) ,
			 __( 'PY' , 'leads') => __( 'Paraguay' , 'leads' ) ,
			 __( 'PE' , 'leads') => __( 'Peru' , 'leads' ) ,
			 __( 'PH' , 'leads') => __( 'Philippines' , 'leads' ) ,
			 __( 'PN' , 'leads') => __( 'Pitcairn' , 'leads' ) ,
			 __( 'PL' , 'leads') => __( 'Poland' , 'leads' ) ,
			 __( 'PT' , 'leads') => __( 'Portugal' , 'leads' ) ,
			 __( 'PR' , 'leads') => __( 'Puerto Rico' , 'leads' ) ,
			 __( 'QA' , 'leads') => __( 'Qatar' , 'leads' ) ,
			 __( 'RE' , 'leads') => __( 'Reunion' , 'leads' ) ,
			 __( 'RO' , 'leads') => __( 'Romania' , 'leads' ) ,
			 __( 'RU' , 'leads') => __( 'Russian Federation' , 'leads' ) ,
			 __( 'RW' , 'leads') => __( 'Rwanda' , 'leads' ) ,
			 __( 'BL' , 'leads') => __( 'Saint Barthelemy' , 'leads' ) ,
			 __( 'SH' , 'leads') => __( 'Saint Helena' , 'leads' ) ,
			 __( 'KN' , 'leads') => __( 'Saint Kitts and Nevis' , 'leads' ) ,
			 __( 'LC' , 'leads') => __( 'Saint Lucia' , 'leads' ) ,
			 __( 'VC' , 'leads') => __( 'Saint Vincent and the Grenadines' , 'leads' ) ,
			 __( 'MF' , 'leads') => __( 'Saint-Martin (France)' , 'leads' ) ,
			 __( 'SX' , 'leads') => __( 'Saint-Martin (Pays-Bas)' , 'leads' ) ,
			 __( 'WS' , 'leads') => __( 'Samoa' , 'leads' ) ,
			 __( 'SM' , 'leads') => __( 'San Marino' , 'leads' ) ,
			 __( 'ST' , 'leads') => __( 'Sao Tome and Principe' , 'leads' ) ,
			 __( 'SA' , 'leads') => __( 'Saudi Arabia' , 'leads' ) ,
			 __( 'SN' , 'leads') => __( 'Senegal' , 'leads' ) ,
			 __( 'RS' , 'leads') => __( 'Serbia' , 'leads' ) ,
			 __( 'SC' , 'leads') => __( 'Seychelles' , 'leads' ) ,
			 __( 'SL' , 'leads') => __( 'Sierra Leone' , 'leads' ) ,
			 __( 'SG' , 'leads') => __( 'Singapore' , 'leads' ) ,
			 __( 'SK' , 'leads') => __( 'Slovakia (Slovak Republic)' , 'leads' ) ,
			 __( 'SI' , 'leads') => __( 'Slovenia' , 'leads' ) ,
			 __( 'SB' , 'leads') => __( 'Solomon Islands' , 'leads' ) ,
			 __( 'SO' , 'leads') => __( 'Somalia' , 'leads' ) ,
			 __( 'ZA' , 'leads') => __( 'South Africa' , 'leads' ) ,
			 __( 'GS' , 'leads') => __( 'South Georgia and the South Sandwich Islands' , 'leads' ) ,
			 __( 'KR' , 'leads') => __( 'South Korea' , 'leads' ) ,
			 __( 'SS' , 'leads') => __( 'South Sudan' , 'leads' ) ,
			 __( 'ES' , 'leads') => __( 'Spain' , 'leads' ) ,
			 __( 'LK' , 'leads') => __( 'Sri Lanka' , 'leads' ) ,
			 __( 'PM' , 'leads') => __( 'St. Pierre and Miquelon' , 'leads' ) ,
			 __( 'SD' , 'leads') => __( 'Sudan' , 'leads' ) ,
			 __( 'SR' , 'leads') => __( 'Suriname' , 'leads' ) ,
			 __( 'SJ' , 'leads') => __( 'Svalbard and Jan Mayen Islands' , 'leads' ) ,
			 __( 'SZ' , 'leads') => __( 'Swaziland' , 'leads' ) ,
			 __( 'SE' , 'leads') => __( 'Sweden' , 'leads' ) ,
			 __( 'CH' , 'leads') => __( 'Switzerland' , 'leads' ) ,
			 __( 'SY' , 'leads') => __( 'Syria' , 'leads' ) ,
			 __( 'TW' , 'leads') => __( 'Taiwan' , 'leads' ) ,
			 __( 'TJ' , 'leads') => __( 'Tajikistan' , 'leads' ) ,
			 __( 'TZ' , 'leads') => __( 'Tanzania' , 'leads' ) ,
			 __( 'TH' , 'leads') => __( 'Thailand' , 'leads' ) ,
			 __( 'NL' , 'leads') => __( 'The Netherlands' , 'leads' ) ,
			 __( 'TL' , 'leads') => __( 'Timor-Leste' , 'leads' ) ,
			 __( 'TG' , 'leads') => __( 'Togo' , 'leads' ) ,
			 __( 'TK' , 'leads') => __( 'Tokelau' , 'leads' ) ,
			 __( 'TO' , 'leads') => __( 'Tonga' , 'leads' ) ,
			 __( 'TT' , 'leads') => __( 'Trinidad and Tobago' , 'leads' ) ,
			 __( 'TN' , 'leads') => __( 'Tunisia' , 'leads' ) ,
			 __( 'TR' , 'leads') => __( 'Turkey' , 'leads' ) ,
			 __( 'TM' , 'leads') => __( 'Turkmenistan' , 'leads' ) ,
			 __( 'TC' , 'leads') => __( 'Turks and Caicos Islands' , 'leads' ) ,
			 __( 'TV' , 'leads') => __( 'Tuvalu' , 'leads' ) ,
			 __( 'UG' , 'leads') => __( 'Uganda' , 'leads' ) ,
			 __( 'UA' , 'leads') => __( 'Ukraine' , 'leads' ) ,
			 __( 'AE' , 'leads') => __( 'United Arab Emirates' , 'leads' ) ,
			 __( 'GB' , 'leads') => __( 'United Kingdom' , 'leads' ) ,
			 __( 'US' , 'leads') => __( 'United States' , 'leads' ) ,
			 __( 'UM' , 'leads') => __( 'United States Minor Outlying Islands' , 'leads' ) ,
			 __( 'UY' , 'leads') => __( 'Uruguay' , 'leads' ) ,
			 __( 'UZ' , 'leads') => __( 'Uzbekistan' , 'leads' ) ,
			 __( 'VU' , 'leads') => __( 'Vanuatu' , 'leads' ) ,
			 __( 'VA' , 'leads') => __( 'Vatican' , 'leads' ) ,
			 __( 'VE' , 'leads') => __( 'Venezuela' , 'leads' ) ,
			 __( 'VN' , 'leads') => __( 'Vietnam' , 'leads' ) ,
			 __( 'VG' , 'leads') => __( 'Virgin Islands (British)' , 'leads' ) ,
			 __( 'VI' , 'leads') => __( 'Virgin Islands (U.S.)' , 'leads' ) ,
			 __( 'WF' , 'leads') => __( 'Wallis and Futuna Islands' , 'leads' ) ,
			 __( 'EH' , 'leads') => __( 'Western Sahara' , 'leads' ) ,
			 __( 'YE' , 'leads') => __( 'Yemen' , 'leads' ) ,
			 __( 'ZM' , 'leads') => __( 'Zambia' , 'leads' ) ,
			 __( 'ZW' , 'leads') => __( 'Zimbabwe' , 'leads' )
		);
	}

	}
}

Inbound_Forms::init();
?>