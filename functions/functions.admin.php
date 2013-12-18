<?php

add_action('admin_enqueue_scripts','wp_cta_admin_enqueue');

function wp_cta_admin_enqueue($hook)
{
	global $post;
	$screen = get_current_screen();

	wp_enqueue_style('wp-cta-admin-css', WP_CTA_URLPATH . 'css/admin-style.css');

	//jquery cookie
	wp_dequeue_script('jquery-cookie');
	wp_enqueue_script('jquery-cookie', WP_CTA_URLPATH . 'js/jquery.cta.cookie.js');

		// Frontend Editor
	if ((isset($_GET['page']) == 'wp-cta-frontend-editor')) {

	}

	// load global metabox scripts on all post type edit screens
	if ( $hook == 'post-new.php' || $hook == 'post.php') {
	wp_enqueue_script('selectjs', WP_CTA_URLPATH . '/shared/js/select2.min.js');
	wp_enqueue_style('selectjs', WP_CTA_URLPATH . '/shared/css/select2.css');
	}

	//easyXDM - for store rendering
	if (isset($_GET['page']) && (($_GET['page'] == 'wp_cta_store') || ($_GET['page'] == 'wp_cta_addons'))) {
		wp_dequeue_script('easyXDM');
		wp_enqueue_script('easyXDM', WP_CTA_URLPATH . 'js/libraries/easyXDM.debug.js');
		//wp_enqueue_script('wp-cta-js-store', WP_CTA_URLPATH . 'js/admin/admin.store.js');
	}

	// Admin enqueue - Landing Page CPT only
	if ( isset($post) && 'wp-call-to-action' == $post->post_type || ( isset($_GET['post_type']) && $_GET['post_type']=='wp-call-to-action' ) )
		{
			wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
			wp_enqueue_script('jpicker', WP_CTA_URLPATH . 'js/libraries/jpicker/jpicker-1.1.6.min.js');
			wp_localize_script( 'jpicker', 'jpicker', array( 'thispath' => WP_CTA_URLPATH.'js/libraries/jpicker/images/' ));
			wp_enqueue_style('jpicker-css', WP_CTA_URLPATH . 'js/libraries/jpicker/css/jPicker-1.1.6.min.css');
			wp_dequeue_script('jquery-qtip');
			wp_enqueue_script('jquery-qtip', WP_CTA_URLPATH . 'js/libraries/jquery-qtip/jquery.qtip.min.js');
			wp_enqueue_script('load-qtip', WP_CTA_URLPATH . 'js/libraries/jquery-qtip/load.qtip.js', array('jquery-qtip'));
			wp_enqueue_style('qtip-css', WP_CTA_URLPATH . 'css/jquery.qtip.min.css');
			wp_enqueue_style('wp-cta-only-cpt-admin-css', WP_CTA_URLPATH . 'css/admin-wp-cta-cpt-only-style.css');
			wp_enqueue_script( 'wp-cta-admin-clear-stats-ajax-request', WP_CTA_URLPATH . 'js/ajax.clearstats.js', array( 'jquery' ) );
			wp_localize_script( 'wp-cta-admin-clear-stats-ajax-request', 'ajaxadmin', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'wp_call_to_action_clear_nonce' => wp_create_nonce('wp-call-to-action-clear-nonce') ) );

		// Add New and Edit Screens
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			//echo wp_create_nonce('wp-cta-nonce');exit;

			add_filter( 'wp_default_editor', 'wp_cta_ab_testing_force_default_editor' );/* force visual editor to open in text mode */
			wp_enqueue_script('wp-cta-post-edit-ui', WP_CTA_URLPATH . 'js/admin/admin.post-edit.js');
			wp_localize_script( 'wp-cta-post-edit-ui', 'wp_cta_post_edit_ui', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_call_to_action_meta_nonce' => wp_create_nonce('wp-call-to-action-meta-nonce'), 'wp_call_to_action_template_nonce' => wp_create_nonce('wp-cta-nonce') ) );

			//admin.metaboxes.js - Template Selector - Media Uploader
			wp_enqueue_script('wp-cta-js-metaboxes', WP_CTA_URLPATH . 'js/admin/admin.metaboxes.js');
			$template_data = wp_cta_get_extension_data();
			$template_data = json_encode($template_data);
			$template = get_post_meta($post->ID, 'wp-cta-selected-template', true);
			$template = apply_filters('wp_cta_selected_template',$template);
			$template = strtolower($template);
			$params = array('selected_template'=>$template, 'templates'=>$template_data);
			wp_localize_script('wp-cta-js-metaboxes', 'data', $params);

			// Isotope sorting
			wp_enqueue_script('wp-cta-js-isotope', WP_CTA_URLPATH . 'js/libraries/isotope/jquery.isotope.js', array('jquery'), '1.0', true );
			wp_enqueue_style('wp-cta-css-isotope', WP_CTA_URLPATH . 'js/libraries/isotope/css/style.css');

			// Conditional TINYMCE for landing pages
			wp_dequeue_script('jquery-tinymce');
			wp_enqueue_script('jquery-tinymce', WP_CTA_URLPATH . 'js/libraries/tiny_mce/jquery.tinymce.js');

		}

		// Edit Screen
		if ( $hook == 'post.php' ) {
			wp_enqueue_style('admin-post-edit-css', WP_CTA_URLPATH . 'css/admin-post-edit.css');
			if (isset($_GET['frontend']) && $_GET['frontend'] === 'true') {
				//show_admin_bar( false ); // doesnt work
				wp_enqueue_style('new-customizer-admin', WP_CTA_URLPATH . 'css/new-customizer-admin.css');
				wp_enqueue_script('new-customizer-admin', WP_CTA_URLPATH . 'js/admin/new-customizer-admin.js');
			}

			wp_enqueue_script('jquery-datepicker', WP_CTA_URLPATH . 'js/libraries/jquery-datepicker/jquery.timepicker.min.js');
			wp_enqueue_script('jquery-datepicker-functions', WP_CTA_URLPATH . 'js/libraries/jquery-datepicker/picker_functions.js');
			wp_enqueue_script('jquery-datepicker-base', WP_CTA_URLPATH . 'js/libraries/jquery-datepicker/lib/base.js');
			wp_enqueue_script('jquery-datepicker-datepair', WP_CTA_URLPATH . 'js/libraries/jquery-datepicker/lib/datepair.js');
			wp_localize_script( 'jquery-datepicker', 'jquery_datepicker', array( 'thispath' => WP_CTA_URLPATH.'js/libraries/jquery-datepicker/' ));
			wp_enqueue_style('jquery-timepicker-css', WP_CTA_URLPATH . 'js/libraries/jquery-datepicker/jquery.timepicker.css');
			wp_enqueue_style('jquery-datepicker-base.css', WP_CTA_URLPATH . 'js/libraries/jquery-datepicker/lib/base.css');
			wp_enqueue_style('inbound-metaboxes', WP_CTA_URLPATH . 'shared/metaboxes/inbound-metaboxes.css');
			/*
			wp_enqueue_script('jquery-intro', WP_CTA_URLPATH . 'js/admin/intro.js', array( 'jquery' ));
			wp_enqueue_style('intro-css', WP_CTA_URLPATH . 'css/admin-tour.css'); */
		}

		// Add New Screen
		if ( $hook == 'post-new.php'  )
		{
			wp_enqueue_script('wp-cta-js-create-new', WP_CTA_URLPATH . 'js/admin/admin.post-new.js', array('jquery'), '1.0', true );
			wp_enqueue_style('wp-cta-css-post-new', WP_CTA_URLPATH . 'css/admin-post-new.css');
		}

		// List Screen
		if ( $screen->id == 'edit-wp-call-to-action' )
		{
			wp_enqueue_script('wp-call-to-action-list', WP_CTA_URLPATH . 'js/admin/admin.wp-call-to-action-list.js');
			wp_enqueue_style('wp-call-to-action-list-css', WP_CTA_URLPATH.'css/admin-wp-call-to-action-list.css');
			wp_enqueue_script('jqueryui');
			wp_admin_css('thickbox');
			add_thickbox();
		}

	}
}

add_filter('admin_url','wp_cta_add_fullscreen_param');
function wp_cta_add_fullscreen_param( $link )
{
	if (isset($_GET['page']))
		return $link;

	if (  ( isset($post) && 'wp-call-to-action' == $post->post_type ) || ( isset($_REQUEST['post_type']) && $_REQUEST['post_type']=='wp-call-to-action' ) )
	{
		$params['frontend'] = 'false';
		if(isset($_GET['frontend']) && $_GET['frontend'] == 'true') {
	        $params['frontend'] = 'true';
	    }
	    if(isset($_REQUEST['frontend']) && $_REQUEST['frontend'] == 'true') {
	        $params['frontend'] = 'true';
	    }
	    $link = add_query_arg( $params, $link );

	}

	return $link;
}

function wp_cta_list_feature($label,$url=null)
{
	return	array(
		"label" => $label,
		"url" => $url
		);
}

add_action('wp_trash_post', 'wp_cta_trash_lander');
function wp_cta_trash_lander($post_id) {
	$extension_data = wp_cta_get_extension_data();
	global $post;

	if (!isset($post)||isset($_POST['split_test']))
		return;

	if ($post->post_type=='revision')
	{
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||(isset($_POST['post_type'])&&$_POST['post_type']=='revision'))
	{
		return;
	}

	if ($post->post_type=='wp-call-to-action')
	{

		$wp_cta_id = $post->ID;

		$args=array(
		  'post_type' => 'wp-call-to-action-group',
		  'post_satus'=>'publish'
		);

		$my_query = null;
		$my_query = new WP_Query($args);

		if( $my_query->have_posts() )
		{
			$i=1;
			while ($my_query->have_posts()) : $my_query->the_post();
				$group_id = get_the_ID();
				$group_data = get_the_content();
				$group_data = json_decode($group_data,true);

				$wp_cta_ids = array();
				foreach ($group_data as $key=>$value)
				{
					$wp_cta_ids[] = $key;
				}

				if (in_array($wp_cta_id,$wp_cta_ids))
				{
					unset($group_data[$wp_cta_id]);
					//echo 1; exit;
					$this_data = json_encode($group_data);
					//print_r($this_data);
					$new_post = array(
						'ID' => $group_id,
						'post_title' => get_the_title(),
						'post_content' => $this_data,
						'post_status' => 'publish',
						'post_date' => date('Y-m-d H:i:s'),
						'post_author' => 1,
						'post_type' => 'wp-call-to-action-group'
					);
					//print_r($new_post);
					$post_id = wp_update_post($new_post);
				}
			endwhile;
		}
	}
}

function wp_cta_add_option($key,$type,$id,$default=null,$label=null,$description=null, $options=null)
{
	switch ($type)
	{
		case "colorpicker":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'colorpicker',
			'default'  => $default
			);
			break;
		case "text":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'text',
			'default'  => $default
			);
			break;
		case "license-key":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'license-key',
			'default'  => $default,
			'slug' => $id
			);
			break;
		case "textarea":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'textarea',
			'default'  => $default
			);
			break;
		case "wysiwyg":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'wysiwyg',
			'default'  => $default
			);
			break;
		case "media":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'media',
			'default'  => $default
			);
			break;
		case "checkbox":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'checkbox',
			'default'  => $default,
			'options' => $options
			);
			break;
		case "radio":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'radio',
			'default'  => $default,
			'options' => $options
			);
			break;
		case "dropdown":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'dropdown',
			'default'  => $default,
			'options' => $options
			);
			break;
		case "datepicker":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'datepicker',
			'default'  => $default
			);
			break;
		case "default-content":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'default-content',
			'default'  => $default
			);
			break;
		case "description-block":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'description-block',
			'default'  => $default
			);
			break;
		case "dimension":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $id,
			'type'  => 'dimension',
			'default'  => $default
			);
			break;
		case "custom-css":
			return array(
			'label' => $label,
			'description'  => $description,
			'id'    => $id,
			'type'  => 'turn-off-editor',
			'default'  => $default // inline css
			);
			break;
	}
}


//generates drop down select of landing pages
function wp_cta_generate_drowndown($select_id, $post_type, $selected = 0, $width = 400, $height = 230,$font_size = 13,$multiple=true)
{
	$post_type_object = get_post_type_object($post_type);
	$label = $post_type_object->label;

	if ($multiple==true)
	{
		$multiple = "multiple='multiple'";
	}
	else
	{
		$multiple = "";
	}

	$posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
	echo '<select name="'. $select_id .'" id="'.$select_id.'" class="wp-cta-multiple-select" style="width:'.$width.'px;height:'.$height.'px;font-size:'.$font_size.'px;"  '.$multiple.'>';
	foreach ($posts as $post) {
		echo '<option value="', $post->ID, '"', $selected == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
	}
	echo '</select>';
}


function wp_cta_wp_editor( $content, $id, $settings = array() )
{
	//echo $id;
	$content = apply_filters('wp_cta_wp_editor_content',$content);
	$id = apply_filters('wp_cta_wp_editor_id',$id);
	$settings = apply_filters('wp_cta_wp_editor_settings',$settings);
	//echo "hello";
	//echo $id;exit;
	wp_editor( $content, $id, $settings);
}


function wp_cta_display_headline_input($id,$main_headline)
{
	//echo $id;
	$id = apply_filters('wp_cta_display_headline_input_id',$id);

	echo "<input type='text' name='{$id}' id='{$id}' value='{$main_headline}' size='30'>";
}
function wp_cta_display_notes_input($id,$variation_notes)
{
	//echo $id;
	$id = apply_filters('wp_cta_display_notes_input_id',$id);

	echo "<span id='add-wp-cta-notes'>Notes:</span><input placeholder='Add Notes to your variation. Example: This version is testing a green submit button' type='text' class='wp-cta-notes' name='{$id}' id='{$id}' value='{$variation_notes}' size='30'>";
}

function wp_cta_ready_screenshot_url($link,$datetime)
{
	return $link.'?dt='.$datetime;
}


function wp_cta_display_success($message)
{
	echo "<br><br><center>";
	echo "<font color='green'><i>".$message."</i></font>";
	echo "</center>";
}


function wp_cta_make_percent($rate, $return = false)
{
	//echo "1{$rate}2";exit;
	//yes, we know this is not a true filter
	if (is_numeric($rate))
	{
		$percent = $rate * (100);
		$percent = number_format($percent,1);
		if($return){ return $percent."%"; } else { echo $percent."%"; }
	}
	else
	{
		if($return){ return $rate; } else { echo $rate; }
	}
}

function wp_cta_check_license_status($field)
{
	//print_r($field);exit;
	$date = date("Y-m-d");
	$cache_date = get_option($field['id']."-expire");
	$license_status = get_option('wp_cta_license_status-'.$field['slug']);

	if (isset($cache_date)&&($date<$cache_date)&&$license_status=='valid')
	{
		return "valid";
	}

	$license_key = get_option($field['id']);

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license_key,
		'item_name' => urlencode( $field['slug'] )
	);
	//print_r($api_params);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, WP_CTA_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
	//print_r($response);

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	//echo $license_data;exit;

	if( $license_data->license == 'valid' ) {
		$newDate = date('Y-m-d', strtotime("+15 days"));
		update_option($field['id']."-expire", $newDate);
		return 'valid';
		// this license is still valid
	} else {
		return 'invalid';
	}
}


function wp_call_to_action_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

function wp_cta_wpseo_priority(){return 'low';}
add_filter( 'wpseo_metabox_prio', 'wp_cta_wpseo_priority');
add_action( 'in_admin_header', 'wp_cta_in_admin_header');
function wp_cta_in_admin_header()
{
	global $post;
	global $wp_meta_boxes;

	if (isset($post)&&$post->post_type=='wp-call-to-action')
	{
		unset( $wp_meta_boxes[get_current_screen()->id]['normal']['core']['postcustom'] );
	}
}


/**** AB TESTING FUNCTIONS ****/

function wp_cta_ab_unset_variation($variations,$vid)
{
	if(($key = array_search($vid, $variations)) !== false) {
		unset($variations[$key]);
	}

	return $variations;
}


function wp_cta_ab_get_wp_cta_active_status($post,$vid=null)
{
	if ($vid==0)
	{
		$variation_status = get_post_meta( $post->ID , 'wp_cta_ab_variation_status' , true);
	}
	else
	{
		$variation_status = get_post_meta( $post->ID , 'wp_cta_ab_variation_status-'.$vid , true);
	}

	if (!is_numeric($variation_status))
	{
		return 1;
	}
	else
	{
		return $variation_status;
	}
}

 add_action('admin_menu', 'cta_placements_content_add_meta_box');
function cta_placements_content_add_meta_box()
{
	$post_types= get_post_types('','names');

	$exclude[] = 'attachment';
	$exclude[] = 'revisions';
	$exclude[] = 'nav_menu_item';
	$exclude[] = 'wp-lead';
	$exclude[] = 'rule';
	$exclude[] = 'list';
	$exclude[] = 'wp-call-to-action';
	$exclude[] = 'tracking-event';
	$exclude[] = 'inbound-forms';
	// add filter

	foreach ($post_types as $value ) {
		$priority = ($value === 'landing-page') ? 'core' : 'high';
		if (!in_array($value,$exclude))
		{
			add_meta_box( 'wp-cta-inert-to-post', 'Insert Call to Action Template into Content', 'cta_placements_content_meta_box' , $value, 'normal', $priority );
		}
	}
}

function wp_cta_per_page_settings(){

global $post;
if (!isset($post))
	return;

$post_type = $post->post_type;
$var_id = ''; // default
if (isset($post)&&$post->post_type==='landing-page'){
	$var_id = '-' . lp_ab_testing_get_current_variation_id();
}
if (isset($_GET['clone'])) {
	$var_id = '-' . $_GET['clone'];
}
if (isset($_GET['lp-variation-id'])) {
	$var_id = '-' . $_GET['lp-variation-id'];
}
$wp_cta_per_post_options = array(
    array(
        'label' => 'Placement on Page',
        'description' => "Where would you like to insert the CTA on this page?",
        'id'  => 'wp_cta_content_placement' . $var_id,
        'type'  => 'dropdown',
        'default'  => 'off',
        'options' => array( 'off' => 'Call to Action Off',
        					'above'=>'Above Content',
        					'middle' => 'Middle of Content',
        					'below' => 'Below Content',
        					'widget_1' => 'Use Dynamic Sidebar Widget',
        					'popup' => 'Use Popup',
        					'slideout' => 'Use Slide Out',
        					'shortcode' => "Use Shortcode (insert from editor button)"),
        'context'  => 'normal',
        'class' => 'cta-per-page-option'
        ),
    array(
        'label' => 'Slide Out Alignment',
        'description' => "Slide out from the right or left?",
        'id'  => 'wp_cta_slide_out_alignment' . $var_id,
        'type'  => 'dropdown',
        'default'  => 'right',
        'options' => array( 'right' => 'Slide out from Right',
        					'left'=> 'Slide out from Left',
        					),
        'context'  => 'normal',
        'class' => 'cta-per-page-option cta-slide-out-option',
        'reveal_on' => 'slideout'
        ),
    array(
        'label' => 'Scroll length to show',
        'description' => "This will determine how far down the page the slideout should show on page scroll",
        'id'  => 'wp_cta_slide_out_reveal' . $var_id,
        'type'  => 'dropdown',
        'default'  => '100',
        'options' => array('100'=>'Show at very bottom of this page', '90'=>'When a visitor has scrolled 90% Down the Page', '80'=>'When a visitor has scrolled 80% Down the Page', '70'=>' When a visitor has scrolled 70% Down the Page', '60'=>'When a visitor has scrolled 60% Down the Page', '50'=>'Half Way Down the Page', '40'=>'When a visitor has scrolled 40% Down the Page', '30'=>'When a visitor has scrolled 30% Down the Page', '20'=>'When a visitor has scrolled 20% Down the Page', '10'=>'When a visitor has scrolled 10% Down the Page', '1'=>'Show immediately at top of this Page'),
        'context'  => 'normal',
        'class' => 'cta-per-page-option cta-slide-out-option',
        'reveal_on' => 'slideout'
        ),
    array(
        'label' => 'slideout Advanced Header',
        'description' => "<div style='margin-top:10px; margin-left:10px;'><h4 style='margin-bottom:0px;'>Advanced Slideout Settings</h4></div>",
        'id'  => 'slideout_advanced_message' . $var_id,
        'type'  => 'html-block',
        'default'  => '',
        'context'  => 'normal',
        'class' => '',
        'reveal_on' => 'slideout'
        ),
    array(
        'label' => 'Slideout Speed',
        'description' => "(Advanced Option) How fast do you want the slideout to enter? Time in seconds. For milliseconds use decimal points. example: 500ms is .5",
        'id'  => 'wp_cta_slide_out_speed' . $var_id,
        'type'  => 'text',
        'default'  => '1',
        'context'  => 'normal',
        'class' => 'cta-per-page-option cta-slideout-option',
        'reveal_on' => 'slideout'
        ),
    array(
        'label' => 'Keep in view once fired?',
        'description' => "Do you want to keep the slide out on the page if the user scrolls away? If this is toggled no, once the user scrolls to another part of the page, this slideout is hidden.",
        'id'  => 'wp_cta_slide_out_keep_open' . $var_id,
        'type'  => 'dropdown',
        'options' => array('no'=>'no', 'yes' => 'yes'),
        'default'  => 'no',
        'context'  => 'normal',
        'class' => 'cta-per-page-option cta-slideout-option',
        'reveal_on' => 'slideout'
        ),
    array(
        'label' => 'Attach Slideout to Page Element',
        'description' => "(Advanced Option) You can attach the slide out event to a CSS selector here.",
        'id'  => 'wp_cta_slide_out_element' . $var_id,
        'type'  => 'text',
        'default'  => '',
        'context'  => 'normal',
        'class' => 'cta-per-page-option cta-slideout-option',
        'reveal_on' => 'slideout'
        ),
    array(
        'label' => 'Popup Delay Time',
        'description' => "How long do you want to delay the popup before showing on the page? Time in seconds",
        'id'  => 'wp_cta_popup_timeout' . $var_id,
        'type'  => 'text',
        'default'  => '7',
        'context'  => 'normal',
        'class' => 'cta-per-page-option cta-slideout-option',
        'reveal_on' => 'popup'
        ),
    array(
        'label' => 'Popup Advanced Header',
        'description' => "<div style='margin-top:10px; margin-left:10px;'><h4 style='margin-bottom:0px;'>Advanced Popup Settings</h4></div>",
        'id'  => 'popup_advanced_message' . $var_id,
        'type'  => 'html-block',
        'default'  => '',
        'context'  => 'normal',
        'class' => '',
        'reveal_on' => 'popup'
        ),
    array(
        'label' => 'Show Popup Only Once Per Visitor?',
        'description' => "How long do you want to delay the popup before showing on the page? Time in seconds",
        'id'  => 'wp_cta_popup_cookie' . $var_id,
        'type'  => 'dropdown',
        'options' => array('yes' => 'yes','no'=>'no'),
        'default'  => 'no',
        'context'  => 'normal',
        'class' => 'cta-per-page-option cta-slideout-option',
        'reveal_on' => 'popup'
        ),
   array(
       'label' => 'Don\'t Reshow Popup for X days',
       'description' => "How long do you want to wait to show the same visitor this popup again?",
       'id'  => 'wp_cta_popup_cookie_length' . $var_id,
       'type'  => 'text',
       'default'  => '7',
       'context'  => 'normal',
       'class' => 'cta-per-page-option cta-slideout-option',
       'reveal_on' => 'popup'
       ),
   array(
       'label' => 'Pageviews Before Popup Appears',
       'description' => "How many page views does the visitor need to see the popup?",
       'id'  => 'wp_cta_popup_pageviews' . $var_id,
       'type'  => 'text',
       'default'  => 0,
       'context'  => 'normal',
       'class' => 'cta-per-page-option cta-slideout-option',
       'reveal_on' => 'popup'
       ),
    array(
        'label' => 'shortcode Message',
        'description' => "<div style='margin-top:10px; margin-left:10px;'><p>To use a shortcode to display your Call to Action. Insert the <input type='text' style='width:112px;' class='regular-text code' readonly='readonly' value='[insert_cta]'> shortcode in the content above.</p><p><b>OR</b> Click on the power button icon <span class='inbound-power'></span> in the editor above and select 'Insert Call to Action', this option will use different CTAs ids than the ones selected in this metabox</p></div>",
        'id'  => 'shortcode_message' . $var_id,
        'type'  => 'html-block',
        'default'  => '',
        'context'  => 'normal',
        'class' => '',
        'reveal_on' => 'shortcode'
        ),
    array(
        'label' => 'Sidebar Message',
        'description' => "<div style='margin-top:10px; margin-left:10px;'><p>This option will place the selected CTA templates into the dynamic sidebar widget on this page. Make sure you have added the dynamic Call to Action widget to the sidebar of this page for this option to work.</p><p>To add the dynamic sidebar widget to this page, go into appearance > widgets and add the widget to the sidebar of your choice</p></div>",
        'id'  => 'sidebar_message' . $var_id,
        'type'  => 'html-block',
        'default'  => '',
        'context'  => 'normal',
        'class' => '',
        'reveal_on' => 'widget_1'
        ),
    array(
        'label' => 'Below Message',
        'description' => "<div style='margin-top:10px; margin-left:10px;'><p>Your Call to Action will be inserted into the bottom of the page/post.</p></div>",
        'id'  => 'below_message' . $var_id,
        'type'  => 'html-block',
        'default'  => '',
        'context'  => 'normal',
        'class' => '',
        'reveal_on' => 'below'
        ),
    array(
        'label' => 'Above Message',
        'description' => "<div style='margin-top:10px; margin-left:10px;'><p>Your Call to Action will be inserted into the top of the page/post.</p></div>",
        'id'  => 'above_message' . $var_id,
        'type'  => 'html-block',
        'default'  => '',
        'context'  => 'normal',
        'class' => '',
        'reveal_on' => 'above'
        ),
    array(
        'label' => 'Middle Message',
        'description' => "<div style='margin-top:10px; margin-left:10px;'><p>Your Call to Action will be inserted into the middle of the page/post's content.</p></div>",
        'id'  => 'above_message' . $var_id,
        'type'  => 'html-block',
        'default'  => '',
        'context'  => 'normal',
        'class' => '',
        'reveal_on' => 'middle'
        ),

);
return $wp_cta_per_post_options;
}

function cta_placements_content_meta_box()
{
	global $post;
	global $table_prefix;
	$wp_cta_per_post_options = wp_cta_per_page_settings();



	//echo $post_id;exit;

		//$content_placements_profile_id = get_post_meta($post->ID, 'id here');
		$wp_cta_post_template_ids = get_post_meta($post->ID, 'cta_display_list');
		$wp_cta_placement = get_post_meta($post->ID, 'wp_cta_content_placement');
		if (!empty($wp_cta_placement)){
			$placement = $wp_cta_placement[0];
		} else {
			$placement = 'off';
		}

		$wp_cta_alignment = get_post_meta($post->ID, 'wp_cta_alignment');
		if (!empty($wp_cta_alignment)){
			$alignment = $wp_cta_alignment[0];
		} else {
			$alignment = 'center';
		}

		//print_r($wp_cta_post_template_ids);

	   // $content_placements_post_status = get_post_meta($post->ID, 'id here');
	   	wp_cta_display_metabox(); // renders checkboxes
		wp_cta_render_metaboxes($wp_cta_per_post_options);?>

	<?php
}


function wp_cta_display_metabox() {
	global $post;
	$args = array(
	'posts_per_page'  => -1,
	'post_type'=> 'wp-call-to-action');
	$cta_list = get_posts($args);
	$cta_display_list = get_post_meta($post->ID ,'cta_display_list', true);
	$cta_display_list = ($cta_display_list != '') ? $cta_display_list : array();
?>
<script type="text/javascript">
jQuery(document).ready(function($)
{
	function format(state) {
		if (!state.id) return state.text; // optgroup
		var href = jQuery("#cta-" + state.id).attr("href");
		return state.text + "<a class='thickbox cta-select-preview-link' href='" + href + "'>(view)</a>";
	}
	jQuery("#cta_template_selection").select2({
		placeholder: "Select one or more calls to action to rotate through",
		allowClear: true,
		formatResult: format,
		formatSelection: format,
		escapeMarkup: function(m) { return m; }
	});
	// show conditional fields
	jQuery('select#wp_cta_content_placement').on('change', function () {
		var this_val = jQuery(this).val();
		jQuery(".dynamic-visable-on").hide();
		console.log(this_val);
		jQuery('.reveal-' + this_val).removeClass('inbound-hidden-row').show().addClass('dynamic-visable-on');
	});
	var onload = jQuery('select#wp_cta_content_placement').val();
	jQuery('.reveal-' + onload).removeClass('inbound-hidden-row').show().addClass('dynamic-visable-on');
});
</script>
<style type="text/css">
	.select2-container {
		width: 100%;
		padding-top: 15px;
	}
	.inbound-hidden-row {
		display: none;
	}
	.wp-cta-option-row {
		padding-top: 5px;
		padding-bottom: 5px;
	}
	.wp_cta_label.cta-per-page-option, .wp-cta-option-area.cta-per-page-option {
	display: inline-block;
	}
	label.cta-per-page-option {
		width: 190px;
		padding-left: 12px;
		display: inline-block;
	}
	.cta-options-label {
		width: 190px;

		display: inline-block;
		vertical-align: top;
		padding-top: 20px;
	}
	.cta-options-row {
	width: 65%;
	display: inline-block;
	}
	.cta-select-preview-link {
		font-size: 10px;
         padding-left: 5px;
        vertical-align: middle;
	}
	.select2-highlighted a.cta-select-preview-link {
		color: #fff !important;
	}
	.cta-links-hidden {
		display: none;
	}
</style>
<div class='wp_cta_select_display'>
	<div class="inside">
		<div class="wp-cta-option-row">
			<div class='cta-options-label'>
				<label for=keyword>
				Call to Action Template
				</label>
			</div>
			<div class='cta-options-row'>
			<?php
			 foreach ( $cta_list as $cta ) {
				$this_id = $cta->ID;
				$this_link = get_permalink( $this_id );
				$this_link = preg_replace('/\?.*/', '', $this_link); ?>

				<a class='thickbox cta-links-hidden' id="cta-<?php echo $this_id;?>" href='<?php echo $this_link;?>?wp-cta-variation-id=0&wp_cta_iframe_window=on&post_id=<?php echo $cta->ID; ?>&TB_iframe=true&width=640&height=703'>Preview</a>

			<?php } ?>
			<select multiple name='cta_display_list[]' id="cta_template_selection" style='display:none;'>
			<?php
			foreach ( $cta_list as $cta  ) {
				$this_id = $cta->ID;
				$this_link = get_permalink( $this_id );
				$title = $cta->post_title;
				$selected = (in_array($this_id, $cta_display_list)) ? " selected='selected'" : "";

				echo '<option', $selected, ' value="'.$this_id.'" rel="work?" >'.$title.'</option>';

			} ?>
			</select><br /><span class="description">Click the above select box to select call to action templates to insert</span>
			</div>
		</div>
	</div>
</div>

	<?php
}

//save the meta box action
add_action( 'save_post', 'wp_cta_display_meta_save', 10, 2 );
function wp_cta_display_meta_save($post_id, $post)
{
	global $post;
	$wp_cta_per_post_options = wp_cta_per_page_settings();

	wp_cta_meta_save_loop($wp_cta_per_post_options);
    if ( isset($_POST['cta_display_list']) ) { // if we get new data
        update_post_meta($post_id, "cta_display_list", $_POST['cta_display_list'] );
    } else {
    	delete_post_meta($post_id, "cta_display_list" ); // remove empty checkboxes
    }

    if ( isset($_POST['wp_cta_alignment']) ) { // if we get new data
        update_post_meta($post_id, "wp_cta_alignment", $_POST['wp_cta_alignment'] );
    } else {
    	delete_post_meta($post_id, "wp_cta_alignment" );
    }

}