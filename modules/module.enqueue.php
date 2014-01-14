<?php


add_action('admin_enqueue_scripts','wp_cta_admin_enqueue');

function wp_cta_admin_enqueue($hook)
{
	global $post;
	$screen = get_current_screen();

	wp_enqueue_style('wp-cta-admin-css', CTA_URLPATH . 'css/admin-style.css');

	//jquery cookie
	wp_dequeue_script('jquery-cookie');
	wp_enqueue_script('jquery-cookie', CTA_URLPATH . 'js/jquery.cta.cookie.js');

		// Frontend Editor
	if ((isset($_GET['page']) == 'wp-cta-frontend-editor')) {

	}

	// load global metabox scripts on all post type edit screens
	if ( $hook == 'post-new.php' || $hook == 'post.php') {
	wp_enqueue_script('selectjs', CTA_URLPATH . '/shared/js/select2.min.js');
	wp_enqueue_style('selectjs', CTA_URLPATH . '/shared/css/select2.css');
	}

	//easyXDM - for store rendering
	if (isset($_GET['page']) && (($_GET['page'] == 'wp_cta_store') || ($_GET['page'] == 'wp_cta_addons'))) {
		wp_dequeue_script('easyXDM');
		wp_enqueue_script('easyXDM', CTA_URLPATH . 'js/libraries/easyXDM.debug.js');
		//wp_enqueue_script('wp-cta-js-store', CTA_URLPATH . 'js/admin/admin.store.js');
	}

	// Admin enqueue - Landing Page CPT only
	if ( isset($post) && 'wp-call-to-action' == $post->post_type || ( isset($_GET['post_type']) && $_GET['post_type']=='wp-call-to-action' ) )
		{
			wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
			wp_enqueue_script('jpicker', CTA_URLPATH . 'js/libraries/jpicker/jpicker-1.1.6.min.js');
			wp_localize_script( 'jpicker', 'jpicker', array( 'thispath' => CTA_URLPATH.'js/libraries/jpicker/images/' ));
			wp_enqueue_style('jpicker-css', CTA_URLPATH . 'js/libraries/jpicker/css/jPicker-1.1.6.min.css');
			wp_dequeue_script('jquery-qtip');
			wp_enqueue_script('jquery-qtip', CTA_URLPATH . 'js/libraries/jquery-qtip/jquery.qtip.min.js');
			wp_enqueue_script('load-qtip', CTA_URLPATH . 'js/libraries/jquery-qtip/load.qtip.js', array('jquery-qtip'));
			wp_enqueue_style('qtip-css', CTA_URLPATH . 'css/jquery.qtip.min.css');
			wp_enqueue_style('wp-cta-only-cpt-admin-css', CTA_URLPATH . 'css/admin-wp-cta-cpt-only-style.css');
			wp_enqueue_script( 'wp-cta-admin-clear-stats-ajax-request', CTA_URLPATH . 'js/ajax.clearstats.js', array( 'jquery' ) );
			wp_localize_script( 'wp-cta-admin-clear-stats-ajax-request', 'ajaxadmin', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'wp_call_to_action_clear_nonce' => wp_create_nonce('wp-call-to-action-clear-nonce') ) );

		// Add New and Edit Screens
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			//echo wp_create_nonce('wp-cta-nonce');exit;

			add_filter( 'wp_default_editor', 'wp_cta_ab_testing_force_default_editor' );/* force visual editor to open in text mode */
			wp_enqueue_script('wp-cta-post-edit-ui', CTA_URLPATH . 'js/admin/admin.post-edit.js');
			wp_localize_script( 'wp-cta-post-edit-ui', 'wp_cta_post_edit_ui', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_call_to_action_meta_nonce' => wp_create_nonce('wp-call-to-action-meta-nonce'), 'wp_call_to_action_template_nonce' => wp_create_nonce('wp-cta-nonce') ) );

			//admin.metaboxes.js - Template Selector - Media Uploader
			wp_enqueue_script('wp-cta-js-metaboxes', CTA_URLPATH . 'js/admin/admin.metaboxes.js');
			$template_data = wp_cta_get_extension_data();
			$template_data = json_encode($template_data);
			$template = get_post_meta($post->ID, 'wp-cta-selected-template', true);
			$template = apply_filters('wp_cta_selected_template',$template);
			$template = strtolower($template);
			$params = array('selected_template'=>$template, 'templates'=>$template_data);
			wp_localize_script('wp-cta-js-metaboxes', 'data', $params);

			// Isotope sorting
			wp_enqueue_script('wp-cta-js-isotope', CTA_URLPATH . 'js/libraries/isotope/jquery.isotope.js', array('jquery'), '1.0', true );
			wp_enqueue_style('wp-cta-css-isotope', CTA_URLPATH . 'js/libraries/isotope/css/style.css');

			// Conditional TINYMCE for landing pages
			wp_dequeue_script('jquery-tinymce');
			wp_enqueue_script('jquery-tinymce', CTA_URLPATH . 'js/libraries/tiny_mce/jquery.tinymce.js');

		}

		// Edit Screen
		if ( $hook == 'post.php' ) {
			wp_enqueue_style('admin-post-edit-css', CTA_URLPATH . 'css/admin-post-edit.css');
			if (isset($_GET['frontend']) && $_GET['frontend'] === 'true') {
				//show_admin_bar( false ); // doesnt work
				wp_enqueue_style('new-customizer-admin', CTA_URLPATH . 'css/new-customizer-admin.css');
				wp_enqueue_script('new-customizer-admin', CTA_URLPATH . 'js/admin/new-customizer-admin.js');
			}

			wp_enqueue_script('jquery-datepicker', CTA_URLPATH . 'js/libraries/jquery-datepicker/jquery.timepicker.min.js');
			wp_enqueue_script('jquery-datepicker-functions', CTA_URLPATH . 'js/libraries/jquery-datepicker/picker_functions.js');
			wp_enqueue_script('jquery-datepicker-base', CTA_URLPATH . 'js/libraries/jquery-datepicker/lib/base.js');
			wp_enqueue_script('jquery-datepicker-datepair', CTA_URLPATH . 'js/libraries/jquery-datepicker/lib/datepair.js');
			wp_localize_script( 'jquery-datepicker', 'jquery_datepicker', array( 'thispath' => CTA_URLPATH.'js/libraries/jquery-datepicker/' ));
			wp_enqueue_style('jquery-timepicker-css', CTA_URLPATH . 'js/libraries/jquery-datepicker/jquery.timepicker.css');
			wp_enqueue_style('jquery-datepicker-base.css', CTA_URLPATH . 'js/libraries/jquery-datepicker/lib/base.css');
			wp_enqueue_style('inbound-metaboxes', CTA_URLPATH . 'shared/metaboxes/inbound-metaboxes.css');
			/*
			wp_enqueue_script('jquery-intro', CTA_URLPATH . 'js/admin/intro.js', array( 'jquery' ));
			wp_enqueue_style('intro-css', CTA_URLPATH . 'css/admin-tour.css'); */
		}

		if (isset($_GET['page']) && $_GET['page'] === 'wp_cta_global_settings') {
			wp_enqueue_script('cta-settings-js', CTA_URLPATH . 'js/admin/admin.global-settings.js');
		}

		// Add New Screen
		if ( $hook == 'post-new.php'  )
		{
			wp_enqueue_script('wp-cta-js-create-new', CTA_URLPATH . 'js/admin/admin.post-new.js', array('jquery'), '1.0', true );
			wp_enqueue_style('wp-cta-css-post-new', CTA_URLPATH . 'css/admin-post-new.css');
		}

		// List Screen
		if ( $screen->id == 'edit-wp-call-to-action' )
		{
			wp_enqueue_script('wp-call-to-action-list', CTA_URLPATH . 'js/admin/admin.wp-call-to-action-list.js');
			wp_enqueue_style('wp-call-to-action-list-css', CTA_URLPATH.'css/admin-wp-call-to-action-list.css');
			wp_enqueue_script('jqueryui');
			wp_admin_css('thickbox');
			add_thickbox();
		}

	}
}