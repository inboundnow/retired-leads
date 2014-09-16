<?php


if (is_admin()) {


	// The loadtiny is specifically to load thing in the module.customizer-display.php iframe (not really working for whatever reason)
	if (isset($_GET['page'])&&$_GET['page']=='wp-cta-frontend-editor') {
		add_action('init','wp_cta_customizer_enqueue');
		add_action('wp_enqueue_scripts', 'wp_cta_customizer_enqueue');
		function wp_cta_customizer_enqueue($hook) {
			wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
			wp_dequeue_script('jquery-cookie');
			wp_enqueue_script('jquery-cookie', WP_CTA_URLPATH . 'js/jquery.cookie.js');
			wp_enqueue_style( 'wp-admin' );
			wp_admin_css('thickbox');
			add_thickbox();

			wp_enqueue_style('wp-cta-admin-css', WP_CTA_URLPATH . 'css/admin-style.css');

			wp_enqueue_script('wp-cta-post-edit-ui', WP_CTA_URLPATH . 'js/admin/admin.post-edit.js');
			wp_localize_script( 'wp-cta-post-edit-ui', 'wp_cta_post_edit_ui', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_call_to_action_meta_nonce' => wp_create_nonce('wp-call-to-action-meta-nonce') ) );
			wp_enqueue_script('wp-cta-frontend-editor-js', WP_CTA_URLPATH . 'js/customizer.save.js');

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