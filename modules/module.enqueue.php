<?php


add_action('wp_footer', 'wp_cta_footer_scripts');
function wp_cta_footer_scripts(){
global $post;
    if (isset($post)&&$post->post_type=='wp-call-to-action') {
	if (isset($_GET['wp-cta-variation-id'])) {
		$version = $_GET['wp-cta-variation-id'];
		$width = get_post_meta($post->ID, 'wp_cta_width-'.$version, true);
		$height = get_post_meta($post->ID, 'wp_cta_height-'.$version, true);
		//$replace = get_post_meta( 2112, 'wp_cta_global_bt_lists', true); // move to ext
	} else {
		global $wp_query;
		$current_page_id = $wp_query->get_queried_object_id();
		$width = get_post_meta($current_page_id, 'wp_cta_width-0', true);
		$height = get_post_meta($current_page_id, 'wp_cta_height-0', true);
		//$replace = null; // more to ext
	}
	wp_enqueue_script('wp_cta_js', WP_CTA_URLPATH . 'js/cta-on-page.js', array( 'jquery' ), true);
	wp_localize_script( 'wp_cta_js' , 'cta_options' , array( 'cta_width' => $width, 'cta_height' => $height ));
    }
}

add_action('wp_enqueue_scripts','wp_cta_fontend_enqueue_scripts');
function wp_cta_fontend_enqueue_scripts($hook)
{
	global $post;
	global $wp_query;


	if (!isset($post))
		return;

	$post_type = $post->post_type;
	$post_id = $post->ID;

	$current_page_id = $wp_query->get_queried_object_id();

	(isset($_SERVER['REMOTE_ADDR'])) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = '0.0.0.0.0';

	// Load Script on All Frontend Pages
	wp_enqueue_script('jquery');
	wp_dequeue_script('jquery-cookie');
	wp_enqueue_script('jquery-cookie', WP_CTA_URLPATH . 'js/jquery.cta.cookie.js', array( 'jquery' ));

	wp_register_script('jquery-total-storage',WP_CTA_URLPATH . 'js/jquery.total-storage.min.js', array( 'jquery', 'json2' ));
	wp_enqueue_script('jquery-total-storage');

	wp_register_script('funnel-tracking', WP_CTA_URLPATH . 'shared/tracking/page-tracking.js', array( 'jquery', 'jquery-cookie', 'jquery-total-storage'));
	wp_register_script('form-population', WP_CTA_URLPATH . 'shared/tracking/form-population.js', array( 'jquery', 'jquery-cookie', 'jquery-total-storage'));
	wp_enqueue_script('form-population');

	/* Global Lead Data */
	$lead_cpt_id = (isset($_COOKIE['wp_lead_id'])) ? $_COOKIE['wp_lead_id'] : false;
    $lead_email = (isset($_COOKIE['wp_lead_email'])) ? $_COOKIE['wp_lead_email'] : false;
    $lead_unique_key = (isset($_COOKIE['wp_lead_uid'])) ? $_COOKIE['wp_lead_uid'] : false;

	$lead_data_array = array();

	if ($lead_cpt_id) {
		$lead_data_array['lead_id'] = $lead_cpt_id;
		$type = 'wplid';}
	if ($lead_email) {
		$lead_data_array['lead_email'] = $lead_email;
		$type = 'wplemail';}
	if ($lead_unique_key) {
		$lead_data_array['lead_uid'] = $lead_unique_key;
		$type = 'wpluid';
	}

	$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
	$wordpress_date_time = date("Y-m-d G:i:s T", $time);

	wp_enqueue_script('funnel-tracking');
	wp_localize_script( 'funnel-tracking' , 'wplft', array( 'post_id' => $post_id, 'ip_address' => $ip_address, 'wp_lead_data' => $lead_data_array, 'admin_url' => admin_url( 'admin-ajax.php' ), 'track_time' => $wordpress_date_time));

	// Load on Non CTA Pages
	if (isset($post)&&$post->post_type !=='wp-call-to-action')
	{

		wp_enqueue_script('cta-render-js', WP_CTA_URLPATH.'js/cta-render.js', array('jquery'), true);

		$cta_obj = wp_cta_localize_script();
		$params = array( 'wp_cta_obj' => $cta_obj );

		wp_localize_script( 'cta-render-js', 'cta_display', $params );

		// load common cta styles
		wp_enqueue_style('cta-css', WP_CTA_URLPATH . 'css/cta-load.css');

		// If CTA Popup Placement is Set for Post Load these
		$wp_cta_placement = get_post_meta($current_page_id, 'wp_cta_content_placement');
		if (isset($wp_cta_placement[0]) && $wp_cta_placement[0] == 'popup') {
		$popup_timeout = get_post_meta($current_page_id, 'wp_cta_popup_timeout', TRUE);
		$pop_time_final = (!empty($popup_timeout)) ? $popup_timeout * 1000 : 3000;
		$popup_cookie = get_post_meta($current_page_id, 'wp_cta_popup_cookie', TRUE);
		$popup_cookie_length = get_post_meta($current_page_id, 'wp_cta_popup_cookie_length', TRUE);
		$popup_pageviews = get_post_meta($current_page_id, 'wp_cta_popup_pageviews', TRUE);
		$global_cookie = get_option( 'wp-cta-main-global-cookie', 0 );
		$global_cookie_length = get_option( 'wp-cta-main-global-cookie-length', 30 );

		wp_enqueue_script('magnific-popup', WP_CTA_URLPATH . 'js/libraries/popup/jquery.magnific-popup.min.js', array( 'jquery' ));
	    wp_enqueue_style('magnific-popup-css', WP_CTA_URLPATH . 'js/libraries/popup/magnific-popup.css');

		$popup_params = array(  'timeout' => $pop_time_final,
	            				'c_status' => $popup_cookie,
	            				'c_length' => $popup_cookie_length,
	            				'page_views'=> $popup_pageviews,
	            				'global_c_status' => $global_cookie,
	            				'global_c_length' => $global_cookie_length
	            					);

	    wp_localize_script( 'magnific-popup', 'wp_cta_popup', $popup_params );
	    }
	    // Slideout CTA
    	if (isset($wp_cta_placement[0]) && $wp_cta_placement[0] == 'slideout')
		{
			wp_register_script('scroll-js',WP_CTA_URLPATH . 'js/libraries/scroll.js', array( 'jquery', 'jquery-cookie', 'jquery-total-storage'));
			wp_enqueue_script('scroll-js');
			// load common cta styles
			wp_enqueue_style('scroll-cta-css', WP_CTA_URLPATH . 'css/scroll-cta.css');
			$slide_out_placement = get_post_meta($current_page_id, 'wp_cta_slide_out_alignment', TRUE);
			$reveal_on = get_post_meta($current_page_id, 'wp_cta_slide_out_reveal', TRUE);
			$reveal_element = get_post_meta($current_page_id, 'wp_cta_slide_out_element', TRUE);
			$slide_speed = get_post_meta($current_page_id, 'wp_cta_slide_out_speed', TRUE);
			$keep_open = get_post_meta($current_page_id, 'wp_cta_slide_out_keep_open', TRUE);
			$slide_speed_final = (isset($slide_speed) && $slide_speed != "") ? $slide_speed * 1000 : 1000;
			$scroll_offset = (isset($reveal_on) && $reveal_on != "") ? $reveal_on : 50;
			$scroll_params = array( 'animation' => 'flyout',
									'speed' => $slide_speed_final,
									'keep_open' => $keep_open,
									'compare' => 'simple',
									'css_side' => 5,
									'css_width' => 360,
									'ga_opt_noninteraction' => 1,
									'ga_track_clicks'=> 1,
									'offset_element'=> $reveal_element,
									'ga_track_views'=> 1,
									'offset_percent'=> $scroll_offset,
									'position'=> $slide_out_placement,
									'title'=> "New Post",
									'url_new_window'=> 0);

			wp_localize_script( 'scroll-js', 'wp_cta_slideout', $scroll_params );
        }

	}

	if (isset($post)&&$post->post_type=='wp-call-to-action')
	{
		// not in use
		if (isset($_GET['cta']))
		{
			show_admin_bar( false );
			add_action('wp_head', 'cta_kill_admin_css');
		}

		wp_enqueue_script('jquery');
		wp_register_script('wp_cta_js',WP_CTA_URLPATH . 'js/cta-on-page.js', array( 'jquery'), true);

		// Shared Core Inbound Scripts
		if (@function_exists('wpleads_check_active')) {
		wp_enqueue_script( 'store-lead-ajax', WPL_URL . '/shared/tracking/js/store.lead.ajax.js', array( 'jquery','jquery-cookie'), '1', true);
		} else {
		wp_enqueue_script( 'store-lead-ajax', WP_CTA_URLPATH .'shared/tracking/js/store.lead.ajax.js', array( 'jquery','jquery-cookie'), '1', true);
		}
		wp_localize_script( 'store-lead-ajax' , 'inbound_ajax', array( 'admin_url' => admin_url( 'admin-ajax.php' ), 'post_id' => $post_id, 'post_type' => $post_type));

			$variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';
			wp_enqueue_script( 'cta-view-track' , WP_CTA_URLPATH . 'js/page_view_track.js', array( 'jquery','jquery-cookie'));
			wp_localize_script( 'cta-view-track' , 'cta_path_info', array( 'variation' => $variation, 'admin_url' => admin_url( 'admin-ajax.php' )));

			// load form pre-population script
			wp_register_script('form-population',WP_CTA_URLPATH . 'js/jquery.form-population.js', array( 'jquery', 'jquery-cookie'	));
			wp_enqueue_script('form-population');


			if ( is_admin_bar_showing() ) {
			wp_register_script('cta-admin-bar',WP_CTA_URLPATH . 'js/admin/cta-admin-bar.js', array( 'jquery'	));
			wp_enqueue_script('cta-admin-bar');
			}
		if (isset($_GET['template-customize']) &&$_GET['template-customize']=='on') {
			// wp_register_script('lp-customizer-load-js', WP_CTA_URLPATH . 'js/customizer.load.js', array('jquery'));
			// wp_enqueue_script('lp-customizer-load-js');
			echo "<style type='text/css'>#variation-list{background:#eaeaea !important; top: 26px !important; height: 35px !important;padding-top: 10px !important;}#wpadminbar {height: 29px !important;}</style>"; // enqueue styles not firing
		}

		if (isset($_GET['live-preview-area'])) {
			show_admin_bar( false );
			wp_register_script('lp-customizer-load-js', WP_CTA_URLPATH . 'js/customizer.load.js', array('jquery'));
			wp_enqueue_script('lp-customizer-load-js');

			}
	}

}

if (is_admin())
{
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

			if (isset($_GET['page']) && $_GET['page'] === 'wp_cta_global_settings') {
				wp_enqueue_script('cta-settings-js', WP_CTA_URLPATH . 'js/admin/admin.global-settings.js');
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

	// The loadtiny is specifically to load thing in the module.customizer-display.php iframe (not really working for whatever reason)
	if (isset($_GET['page'])&&$_GET['page']=='wp-cta-frontend-editor')
	{
		add_action('init','wp_cta_customizer_enqueue');
		add_action('wp_enqueue_scripts', 'wp_cta_customizer_enqueue');
		function wp_cta_customizer_enqueue($hook)
		{

			wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
			wp_dequeue_script('jquery-cookie');
			wp_enqueue_script('jquery-cookie', WP_CTA_URLPATH . 'js/jquery.cookie.js');
			wp_enqueue_style( 'wp-admin' );
			wp_admin_css('thickbox');
			add_thickbox();
			wp_enqueue_style('wp-cta-admin-css', WP_CTA_URLPATH . 'css/admin-style.css');
			wp_enqueue_script('wp-cta-post-edit-ui', WP_CTA_URLPATH . 'js/admin/admin.post-edit.js');
			wp_enqueue_script('wp-cta-frontend-editor-js', WP_CTA_URLPATH . 'js/customizer.save.js');
			// Ajax Localize
			wp_localize_script( 'wp-cta-post-edit-ui', 'wp_cta_post_edit_ui', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_call_to_action_meta_nonce' => wp_create_nonce('wp-call-to-action-meta-nonce') ) );

			wp_enqueue_script('wp-cta-js-isotope', WP_CTA_URLPATH . 'js/libraries/isotope/jquery.isotope.js', array('jquery'), '1.0' );
			wp_enqueue_style('wp-cta-css-isotope', WP_CTA_URLPATH . 'js/libraries/isotope/css/style.css');
			//jpicker - color picker
			wp_enqueue_script('jpicker', WP_CTA_URLPATH . 'js/libraries/jpicker/jpicker-1.1.6.min.js');
			wp_localize_script( 'jpicker', 'jpicker', array( 'thispath' => WP_CTA_URLPATH.'js/libraries/jpicker/images/' ));
			wp_enqueue_style('jpicker-css', WP_CTA_URLPATH . 'js/libraries/jpicker/css/jPicker-1.1.6.min.css');
			wp_enqueue_style('jpicker-css', WP_CTA_URLPATH . 'js/libraries/jpicker/css/jPicker.css');
			wp_enqueue_style('wp-cta-customizer-frontend', WP_CTA_URLPATH . 'css/customizer.frontend.css');
			wp_dequeue_script('form-population');
			wp_dequeue_script('funnel-tracking');
			wp_enqueue_script('jquery-easing', WP_CTA_URLPATH . 'js/jquery.easing.min.js');

		}
	}
}
