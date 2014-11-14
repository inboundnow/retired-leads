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

	}
}
