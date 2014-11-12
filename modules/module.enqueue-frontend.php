<?php

add_action('wp_enqueue_scripts', 'wpleads_enqueuescripts_header');

function wpleads_enqueuescripts_header() {
	global $post;

	$post_type = isset($post) ? get_post_type( $post ) : null;

	// Load Tracking Scripts
	if($post_type != "wp-call-to-action") {

		/* load jquery */
		wp_enqueue_script('jquery');

		// Load form pre-population
		$form_prepopulation = get_option( 'wpl-main-form-prepopulation' , 1); // Check lead settings
		$lp_form_prepopulation = get_option( 'lp-main-landing-page-prepopulate-forms' , 1);
		if ($lp_form_prepopulation === "1") {
			$form_prepopulation = "1";
		}

		if ($form_prepopulation === "1") {

		}

		// Load form tracking class
		$inbound_track_include = get_option( 'wpl-main-tracking-ids', 1);
		if (!empty($inbound_track_include) && $inbound_track_include != "") {
			wp_enqueue_script('wpl-assign-class', WPL_URLPATH . 'js/wpl.assign-class.js', array( 'jquery'));
			wp_localize_script( 'wpl-assign-class', 'inbound_track_include', array( 'include' =>  $inbound_track_include ) );
		}
		$inbound_track_exclude = get_option( 'wpl-main-exclude-tracking-ids');
		if (!empty($inbound_track_exclude) && $inbound_track_exclude != "") {
			wp_enqueue_script('wpl-assign-class', WPL_URLPATH . 'js/wpl.assign-class.js', array( 'jquery'));
			wp_localize_script( 'wpl-assign-class', 'inbound_track_exclude', array( 'exclude' =>  $inbound_track_exclude ) );
		}

	}
}
