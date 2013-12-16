<?php
/**
*   Inbound Forms Shortcode Options
*   Forms code found in /shared/classes/form.class.php
*/

	if (empty($lead_mapping_fields)){
		// if lead transient doesn't exist use defaults
		$lead_mapping_fields = array(
		'null' => 'No Mapping',
		"wpleads_first_name" => "First Name",
		"wpleads_last_name" => "Last Name",
		"wpleads_email_address" => "Email",
		"wpleads_website" => "Website",
		"wpleads_company_name" => "Company Name",
		"wpleads_mobile_phone" => "Mobile Phone",
		"wpleads_work_phone" => "Work Phone",
		"wpleads_address_line_1" => "Address",
		"wpleads_address_line_2" => "Address Continued",
		"wpleads_city" => "City",
		"wpleads_region_name" => 'State/Region',
		"wpleads_zip" => "Zip-code",
		"wpleads_country_code" => "Country",
		"wpleads_websites" => "Related Websites",
		"wpleads_notes" => "Notes"
		);
	}
	//print_r($lead_mapping_fields);

	$shortcodes_config['forms'] = array(
		'no_preview' => false,
		'options' => array(
			'insert_default' => array(
						'name' => __('Choose Starting Template', INBOUND_LABEL),
						'desc' => __('Start Building Your Form from premade templates', INBOUND_LABEL),
						'type' => 'select',
						'options' => $form_names,
						'std' => 'none',
						'class' => 'main-form-settings',
			),
			'form_name' => array(
				'name' => __('Form Name<span class="small-required-text">*</span>', INBOUND_LABEL),
				'desc' => __('This is not shown to visitors', INBOUND_LABEL),
				'type' => 'text',
				'placeholder' => "Example: XYZ Whitepaper Download",
				'std' => '',
				'class' => 'main-form-settings',
			),
			/*'confirmation' => array(
						'name' => __('Form Layout', INBOUND_LABEL),
						'desc' => __('Choose Your Form Layout', INBOUND_LABEL),
						'type' => 'select',
						'options' => array(
							"redirect" => "Redirect After Form Completion",
							"text" => "Display Text on Same Page",
							),
						'std' => 'redirect'
			),*/
			'redirect' => array(
				'name' => __('Redirect URL<span class="small-required-text">*</span>', INBOUND_LABEL),
				'desc' => __('Where do you want to send people after they fill out the form?', INBOUND_LABEL),
				'type' => 'text',
				'placeholder' => "http://www.yoursite.com/thank-you",
				'std' => '',
				'reveal_on' => 'redirect',
				'class' => 'main-form-settings',
			),
			/*'thank_you_text' => array(
					'name' => __('Field Description <span class="small-optional-text">(optional)</span>',  INBOUND_LABEL),
					'desc' => __('Put field description here.',  INBOUND_LABEL),
					'type' => 'textarea',
					'std' => '',
					'class' => 'advanced',
					'reveal_on' => 'text'
			), */
			'notify' => array(
				'name' => __('Notify on Form Completions<span class="small-required-text">*</span>', INBOUND_LABEL),
				'desc' => __('Who should get admin notifications on this form?', INBOUND_LABEL),
				'type' => 'text',
				'placeholder' => "youremail@email.com",
				'std' => '',
				'class' => 'main-form-settings',
			),
			'helper-block-one' => array(
					'name' => __('Name Name Name',  INBOUND_LABEL),
					'desc' => __('<span class="switch-to-form-insert button">Cancel Form Creation & Insert Existing Form</span>',  INBOUND_LABEL),
					'type' => 'helper-block',
					'std' => '',
					'class' => 'main-form-settings',
			),
			'heading_design' => array(
					'name' => __('Name Name Name',  INBOUND_LABEL),
					'desc' => __('Layout Options',  INBOUND_LABEL),
					'type' => 'helper-block',
					'std' => '',
					'class' => 'main-design-settings',
			),
			'layout' => array(
						'name' => __('Form Layout', INBOUND_LABEL),
						'desc' => __('Choose Your Form Layout', INBOUND_LABEL),
						'type' => 'select',
						'options' => array(
							"vertical" => "Vertical",
							"horizontal" => "Horizontal",
							),
						'std' => 'inline',
						'class' => 'main-design-settings',
			),
			'labels' => array(
						'name' => __('Label Alignment', INBOUND_LABEL),
						'desc' => __('Choose Label Layout', INBOUND_LABEL),
						'type' => 'select',
						'options' => array(
							"top" => "Labels on Top",
							"bottom" => "Labels on Bottom",
							"inline" => "Inline",
							"placeholder" => "Use HTML5 Placeholder text only"
							),
						'std' => 'top',
						'class' => 'main-design-settings',
					),
			'submit' => array(
				'name' => __('Submit Button Text', INBOUND_LABEL),
				'desc' => __('Enter the text you want to show on the submit button. (or a link to a custom submit button image)', INBOUND_LABEL),
				'type' => 'text',
				'std' => 'Submit',
				'class' => 'main-design-settings',
			),
			'width' => array(
				'name' => __('Custom Width', INBOUND_LABEL),
				'desc' => __('Enter in pixel width or % width. Example: 400 <u>or</u> 100%', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => 'main-design-settings',
			),
		),
		'child' => array(
			'options' => array(
				'label' => array(
					'name' => __('Field Label',  INBOUND_LABEL),
					'desc' => '',
					'type' => 'text',
					'std' => '',
					'placeholder' => "Enter the Form Field Label. Example: First Name"
				),
				'field_type' => array(
					'name' => __('Field Type', INBOUND_LABEL),
					'desc' => __('Select an form field type', INBOUND_LABEL),
					'type' => 'select',
					'options' => array(
						"text" => "Single Line Text",
						"textarea" => "Paragraph Text",
						'dropdown' => "Dropdown Options",
						"radio" => "Radio Select",
						"number" => "Number",
						"checkbox" => "Checkbox",
						//"html-block" => "HTML Block",
						"date" => "Date Field",
						"time" => "Time Field",
						'hidden' => "Hidden Field",
						//'file_upload' => "File Upload",
						//'editor' => "HTML Editor"
						//"multi-select" => "multi-select"
						),
					'std' => ''
				),

				'dropdown_options' => array(
					'name' => __('Dropdown choices',  INBOUND_LABEL),
					'desc' => __('Enter Your Dropdown Options. Separate by commas.',  INBOUND_LABEL),
					'type' => 'text',
					'std' => '',
					'placeholder' => 'Choice 1, Choice 2, Choice 3',
					'reveal_on' => 'dropdown' // on select choice show this
				),
				'radio_options' => array(
					'name' => __('Radio Choices',  INBOUND_LABEL),
					'desc' => __('Enter Your Radio Options. Separate by commas.',  INBOUND_LABEL),
					'type' => 'text',
					'std' => '',
					'placeholder' => 'Choice 1, Choice 2',
					'reveal_on' => 'radio' // on select choice show this
				),
				'checkbox_options' => array(
					'name' => __('Checkbox choices',  INBOUND_LABEL),
					'desc' => __('Enter Your Checkbox Options. Separate by commas.',  INBOUND_LABEL),
					'type' => 'text',
					'std' => '',
					'placeholder' => 'Choice 1, Choice 2, Choice 3',
					'reveal_on' => 'checkbox' // on select choice show this
				),
				'html_block_options' => array(
					'name' => __('HTML Block',  INBOUND_LABEL),
					'desc' => __('This is a raw HTML block in the form. Insert text/HTML',  INBOUND_LABEL),
					'type' => 'textarea',
					'std' => '',
					'reveal_on' => 'html-block' // on select choice show this
				),
				'helper' => array(
					'name' => __('Field Description <span class="small-optional-text">(optional)</span>',  INBOUND_LABEL),
					'desc' => __('<span class="show-advanced-fields">Show advanced fields</span>',  INBOUND_LABEL),
					'type' => 'helper-block',
					'std' => '',
					'class' => '',
				),
				'required' => array(
					'name' => __('Required Field? <span class="small-optional-text">(optional)</span>', INBOUND_LABEL),
					'checkbox_text' => __('Check to make field required', INBOUND_LABEL),
					'desc' => '',
					'type' => 'checkbox',
					'std' => '0',
					'class' => 'advanced',
				),
				'placeholder' => array(
					'name' => __('Field Placeholder <span class="small-optional-text">(optional)</span>',  INBOUND_LABEL),
					'desc' => __('Put field placeholder text here. Only works for normal text inputs',  INBOUND_LABEL),
					'type' => 'text',
					'std' => '',
					'class' => 'advanced',
				),
				'description' => array(
					'name' => __('Field Description <span class="small-optional-text">(optional)</span>',  INBOUND_LABEL),
					'desc' => __('Put field description here.',  INBOUND_LABEL),
					'type' => 'textarea',
					'std' => '',
					'class' => 'advanced',
				),

				'hidden_input_options' => array(
					'name' => __('Dynamic Field Filling',  INBOUND_LABEL),
					'desc' => __('Enter Your Dynamic URL parameter',  INBOUND_LABEL),
					'type' => 'text',
					'std' => '',
					'placeholder' => 'enter dynamic url parameter example: utm_campaign ',
					'class' => 'advanced',
					//'reveal_on' => 'hidden' // on select choice show this
				),
				'map_to' => array(
							'name' => __('Map Field To', INBOUND_LABEL),
							'desc' => __('Map this field to Leads Value', INBOUND_LABEL),
							'type' => 'select',
							'options' => $lead_mapping_fields,
							'std' => 'none',
							'class' => 'advanced exclude',
				),
			),
			'shortcode' => '[inbound_field label="{{label}}" type="{{field_type}}" description="{{description}}" required="{{required}}" dropdown="{{dropdown_options}}" radio="{{radio_options}}"  checkbox="{{checkbox_options}}" placeholder="{{placeholder}}" html="{{html_block_options}}" dynamic="{{hidden_input_options}}" map_to="{{map_to}}"]',
			'clone' => __('Add Another Field',  INBOUND_LABEL )
		),
		'shortcode' => '[inbound_form name="{{form_name}}" redirect="{{redirect}}" notify="{{notify}}" layout="{{layout}}" labels="{{labels}}" submit="{{submit}}" width="{{width}}"]{{child}}[/inbound_form]',
		'popup_title' => __('Insert Inbound Form Shortcode',  INBOUND_LABEL)
	);

/* CPT Lead Lists */
add_action('init', 'inbound_forms_cpt',11);
if (!function_exists('inbound_forms_cpt')) {
	function inbound_forms_cpt() {
		//echo $slug;exit;
	    $labels = array(
	        'name' => _x('Inbound Forms', 'post type general name'),
	        'singular_name' => _x('Form', 'post type singular name'),
	        'add_new' => _x('Add New', 'Form'),
	        'add_new_item' => __('Create New Form'),
	        'edit_item' => __('Edit Form'),
	        'new_item' => __('New Form'),
	        'view_item' => __('View Lists'),
	        'search_items' => __('Search Lists'),
	        'not_found' =>  __('Nothing found'),
	        'not_found_in_trash' => __('Nothing found in Trash'),
	        'parent_item_colon' => ''
	    );

	    $args = array(
	        'labels' => $labels,
	        'public' => false,
	        'publicly_queryable' => false,
	        'show_ui' => true,
	        'query_var' => true,
	       	'show_in_menu'  => false,
	        'capability_type' => 'post',
	        'hierarchical' => false,
	        'menu_position' => null,
	        'supports' => array('title','custom-fields', 'editor')
	      );

	    register_post_type( 'inbound-forms' , $args );
		//flush_rewrite_rules( false );

		/*
		add_action('admin_menu', 'remove_list_cat_menu');
		function remove_list_cat_menu() {
			global $submenu;
			unset($submenu['edit.php?post_type=wp-lead'][15]);
			//print_r($submenu); exit;
		} */
	}
}


if (is_admin())
{
	// Change the columns for the edit CPT screen
	add_filter( "manage_inbound-forms_posts_columns", "inbound_forms_change_columns" );
	if (!function_exists('inbound_forms_change_columns')) {
		function inbound_forms_change_columns( $cols ) {
			$cols = array(
				"cb" => "<input type=\"checkbox\" />",
				'title' => "Form Name",
				"inbound-form-shortcode" => "Shortcode",
				"inbound-form-converions" => "Conversion Count",
				"date" => "Date"
			);
			return $cols;
		}
	}

	add_action( "manage_posts_custom_column", "inbound_forms_custom_columns", 10, 2 );
	if (!function_exists('inbound_forms_custom_columns')) {
		function inbound_forms_custom_columns( $column, $post_id )
		{
			switch ( $column ) {

				case "inbound-form-shortcode":
					$shortcode = get_post_meta( $post_id , 'inbound_shortcode', true );
					$form_name = get_the_title( $post_id );
				  if ($shortcode == "") {
				  	$shortcode = 'N/A';
				  }

				  echo '<input type="text" class="regular-text code short-shortcode-input" readonly="readonly" id="shortcode" name="shortcode" value=\'[inbound_forms id="'.$post_id.'" name="'.$form_name.'"]\'>';
				  break;
				case "inbound-form-converions":
				  $count = get_post_meta( $post_id, 'inbound_form_conversion_count', true);
				   if (get_post_meta( $post_id, 'inbound_form_conversion_count', true) == "") {
				  	$count = 'N/A';
				  }
				  echo $count;
				  break;
			}
		}
	}
}


if (!function_exists('inbound_forms_redirect')) {
function inbound_forms_redirect($value){
	    global $pagenow;
	    $page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : false);
	    if($pagenow=='edit.php' && $page=='inbound-forms-redirect'){
	        wp_redirect(get_admin_url().'edit.php?post_type=inbound-forms');
	        exit;
	    }
	}
}
add_action('admin_init', 'inbound_forms_redirect');

add_action('admin_head', 'inbound_get_form_names',16);
if (!function_exists('inbound_get_form_names')) {
	function inbound_get_form_names() {
		global $post;

		$loop = get_transient( 'inbound-form-names-off' );
	    if ( false === $loop ) {
		$args = array(
		'posts_per_page'  => -1,
		'post_type'=> 'inbound-forms');
		$form_list = get_posts($args);
		//print_r($cta_list);
		$form_array = array();
		$default_array = array(
								"none" => "None (Build Your Own)",
								"default_form_3" => "Simple Email Form",
								"default_form_1" => "First, Last, Email Form",
								"default_form_2" => "Standard Company Form",
								// Add in other forms made here
							);
		foreach ( $form_list as $form  )
					{
						$this_id = $form->ID;
						$this_link = get_permalink( $this_id );
						$title = $form->post_title;


					    $form_array['form_' . $this_id] = $title;


					 }
		$result = array_merge( $default_array, $form_array);

		set_transient('inbound-form-names', $result, 24 * HOUR_IN_SECONDS);
		}

	}
}
if (!function_exists('inbound_form_save'))
{
	/* 	Shortcode moved to shared form class */
	add_action('wp_ajax_inbound_form_save', 'inbound_form_save');
	add_action('wp_ajax_nopriv_inbound_form_save', 'inbound_form_save');

	function inbound_form_save()
	{
		global $user_ID, $wpdb;
	    // Post Values
	    $form_name = (isset( $_POST['name'] )) ? $_POST['name'] : "";
	    $shortcode = (isset( $_POST['shortcode'] )) ? $_POST['shortcode'] : "";
	    $form_settings =  (isset( $_POST['form_settings'] )) ? $_POST['form_settings'] : "";
	    $form_values =  (isset( $_POST['form_values'] )) ? $_POST['form_values'] : "";
	    $field_count =  (isset( $_POST['field_count'] )) ? $_POST['field_count'] : "";
	    $page_id = (isset( $_POST['post_id'] )) ? $_POST['post_id'] : "";
	    $post_type = (isset( $_POST['post_type'] )) ? $_POST['post_type'] : "";
	    $redirect_value = (isset( $_POST['redirect_value'] )) ? $_POST['redirect_value'] : "";
	    $notify_email = (isset( $_POST['notify_email'] )) ? $_POST['notify_email'] : "";
	    $email_contents = (isset( $_POST['email_contents'] )) ? $_POST['email_contents'] : "";
	    $send_email = (isset( $_POST['send_email'] )) ? $_POST['send_email'] : "off";
	    $send_subject = (isset( $_POST['send_subject'] )) ? $_POST['send_subject'] : "off";


	    if ($post_type === 'inbound-forms'){
	    	$post_ID = $page_id;
	    	  $update_post = array(
	    	      'ID'           => $post_ID,
	    	      'post_title'   => $form_name,
	    	      'post_status'       => 'publish',
	    	      'post_content' => $email_contents
	    	  );
	    	  wp_update_post( $update_post );
	    	  $form_settings_data = get_post_meta( $post_ID, 'form_settings', TRUE );
	    	  update_post_meta( $post_ID, 'inbound_form_settings', $form_settings );
	    	  update_post_meta( $post_ID, 'inbound_form_created_on', $page_id );
	    	  $shortcode = str_replace("[inbound_form", "[inbound_form id=\"" . $post_ID . "\"", $shortcode);
	    	  update_post_meta( $post_ID, 'inbound_shortcode', $shortcode );
	    	  update_post_meta( $post_ID, 'inbound_form_values', $form_values );
	    	  update_post_meta( $post_ID, 'inbound_form_field_count', $field_count );
	    	  update_post_meta( $post_ID, 'inbound_redirect_value', $redirect_value );
	    	  update_post_meta( $post_ID, 'inbound_notify_email', $notify_email );
	    	  update_post_meta( $post_ID, 'inbound_email_send_notification', $send_email );
	    	  update_post_meta( $post_ID, 'inbound_confirmation_subject', $send_subject );

	    	  $output =  array('post_id'=> $post_ID,
	    	                   'form_name'=>$form_name,
	    	                   'redirect' => $redirect_value);

	    	  		echo json_encode($output,JSON_FORCE_OBJECT);
	    	  		wp_die();
	    } else {
	    // If from popup run this
	        $query = $wpdb->prepare(
	            'SELECT ID FROM ' . $wpdb->posts . '
	            WHERE post_title = %s
	            AND post_type = \'inbound-forms\'',
	            $form_name
	        );
	        $wpdb->query( $query );
	        // If form exists
	        if ( $wpdb->num_rows ) {
	            $post_ID = $wpdb->get_var( $query );

	            if ($post_ID != $page_id) {
	            	// if form name exists already in popup mode
	            	echo json_encode("Found");
	            	exit;
	            } else {
	            	update_post_meta( $post_ID, 'inbound_form_settings', $form_settings );
	            	update_post_meta( $post_ID, 'inbound_form_created_on', $page_id );
	            	update_post_meta( $post_ID, 'inbound_shortcode', $shortcode );
	            	update_post_meta( $post_ID, 'inbound_form_values', $form_values );
	            	update_post_meta( $post_ID, 'inbound_form_field_count', $field_count );
	            	update_post_meta( $post_ID, 'inbound_redirect_value', $redirect_value );
	            	update_post_meta( $post_ID, 'inbound_notify_email', $notify_email );
	            	update_post_meta( $post_ID, 'inbound_email_send_notification', $send_email );
	            	update_post_meta( $post_ID, 'inbound_confirmation_subject', $send_subject );
	            }

	        } else {
	            // If form doesn't exist create it
	            $post = array(
	                'post_title'        => $form_name,
	                'post_content' => $email_contents,
	                'post_status'       => 'publish',
	                'post_type'     => 'inbound-forms',
	                'post_author'       => 1
	            );

	            $post_ID = wp_insert_post($post);
	            update_post_meta( $post_ID, 'inbound_form_settings', $form_settings );
	            update_post_meta( $post_ID, 'inbound_form_created_on', $page_id );
	            update_post_meta( $post_ID, 'inbound_shortcode', $shortcode );
	            update_post_meta( $post_ID, 'inbound_form_values', $form_values );
	            update_post_meta( $post_ID, 'inbound_form_field_count', $field_count );
	            update_post_meta( $post_ID, 'inbound_redirect_value', $redirect_value );
	            update_post_meta( $post_ID, 'inbound_notify_email', $notify_email );
	            update_post_meta( $post_ID, 'inbound_email_send_notification', $send_email );
	            update_post_meta( $post_ID, 'inbound_confirmation_subject', $send_subject );
	        }
	        $shortcode = str_replace("[inbound_form", "[inbound_form id=\"" . $post_ID . "\"", $shortcode);
	        update_post_meta( $post_ID, 'inbound_shortcode', $shortcode );

	           	$output =  array('post_id'=> $post_ID,
	                     'form_name'=>$form_name,
	                     'redirect' => $redirect_value);

	    		echo json_encode($output,JSON_FORCE_OBJECT);
	    		wp_die();
	    }
	}
}
/* 	Shortcode moved to shared form class */
if (!function_exists('inbound_form_get_data')) {
	add_action('wp_ajax_inbound_form_get_data', 'inbound_form_get_data');
	add_action('wp_ajax_nopriv_inbound_form_get_data', 'inbound_form_get_data');

	function inbound_form_get_data()
	{
	    // Post Values
	    $post_ID = (isset( $_POST['form_id'] )) ? $_POST['form_id'] : "";

	    if (isset( $_POST['form_id'])&&!empty( $_POST['form_id']))
	    {

	        $form_settings_data = get_post_meta( $post_ID, 'inbound_form_settings', TRUE );
	        $field_count = get_post_meta( $post_ID, 'inbound_form_field_count', TRUE );
	        $shortcode = get_post_meta( $post_ID, 'inbound_shortcode', TRUE );
	       	$inbound_form_values = get_post_meta( $post_ID, 'inbound_form_values', TRUE );
	        /*   update_post_meta( $post_ID, 'inbound_form_created_on', $page_id );
	            update_post_meta( $post_ID, 'inbound_shortcode', $shortcode );
	            update_post_meta( $post_ID, 'inbound_form_values', $form_values );
	            update_post_meta( $post_ID, 'inbound_form_field_count', $field_count );
	        */
	       	$output =  array('inbound_shortcode'=> $shortcode,
	                 'field_count'=>$field_count,
	                 'form_settings_data' => $form_settings_data,
	                 'field_values'=>$inbound_form_values);

			echo json_encode($output,JSON_FORCE_OBJECT);

	    }
	    wp_die();
	}
}

if (!function_exists('inbound_form_auto_publish'))
{
	/* 	Shortcode moved to shared form class */
	add_action('wp_ajax_inbound_form_auto_publish', 'inbound_form_auto_publish');
	add_action('wp_ajax_nopriv_inbound_form_auto_publish', 'inbound_form_auto_publish');

	function inbound_form_auto_publish()
	{
	    // Post Values
	    $post_ID = (isset( $_POST['post_id'] )) ? $_POST['post_id'] : "";
	    $post_title = (isset( $_POST['post_title'] )) ? $_POST['post_title'] : "";

	    if (isset( $_POST['post_id'])&&!empty( $_POST['post_id']))
	    {
	    	// Update Post status to published immediately
	    	// Update post 37
	    	  $my_post = array(
	    	      'ID'           => $post_ID,
	    	      'post_title'   => $post_title,
	    	      'post_status'  => 'publish'
	    	  );

	    	// Update the post into the database
	    	  wp_update_post( $my_post );
	    }
	    wp_die();
	}
}