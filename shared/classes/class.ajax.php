<?php

/**
 *	This class loads miscellaneous WordPress AJAX listeners
 */
if (!class_exists('Inbound_Ajax')) {

	class Inbound_Ajax {

		/**
		 *	Initializes classs
		 */
		public function __construct() {
			self::load_hooks();
		}

		/**
		 *	Loads hooks and filters
		 */
		public static function load_hooks() {

			/* Ajax that runs on pageload */
			add_action( 'wp_ajax_nopriv_inbound_ajax', array(__CLASS__, 'run_ajax_actions') );
			add_action( 'wp_ajax_inbound_ajax', array(__CLASS__, 'run_ajax_actions') );


			/* Increases the page view statistics of lead on page load */
			add_action('wp_ajax_inbound_track_lead', array(__CLASS__, 'track_lead' ) );
			add_action('wp_ajax_nopriv_inbound_track_lead', array(__CLASS__, 'track_lead' ) );

		}

		/**
		 * Executes hook that runs all ajax actions
		 */
		public static function run_ajax_actions() {

		}

		/**
		 *
		 */
		public static function track_lead() {

			global $wpdb;

			$lead_data['lead_id'] = (isset(	$_POST['wp_lead_id'] )) ?  $_POST['wp_lead_id'] : '';
			$lead_data['nature'] = (isset(	$_POST['nature'] )) ? $_POST['nature'] : 'non-conversion'; /* what is nature? */
			$lead_data['json'] = (isset( $_POST['json'] )) ? addslashes($_POST['json']) : 0;
			$lead_data['wp_lead_uid'] =  (isset( $_POST['wp_lead_uid'] )) ? $_POST['wp_lead_uid'] : 0;
			$lead_data['page_id'] = (isset(	$_POST['page_id'] )) ? $_POST['page_id'] :  0;
			$lead_data['current_url'] = (isset(	$_POST['current_url'] )) ? $_POST['current_url'] : 'notfound';


			$page_views = stripslashes($_POST['page_views']);
			$page_views = ($page_views) ? $page_views : '';

			/* update funnel cookie */
			if (isset($_COOKIE['inbound_page_views']) && !$page_views ) {
				$_SESSION['inbound_page_views'] = stripslashes($_COOKIE['inbound_page_views']);
			} else {
				$_SESSION['inbound_page_views'] = $page_views;
			}

			/* update lead data */
			if(isset($_POST['wp_lead_id']) && function_exists('wp_leads_update_page_view_obj') ) {
				wp_leads_update_page_view_obj($lead_data);
			}

			/* update content data */

			do_action( 'lp_record_impression', $lead_data['page_id'], $_POST['post_type'],  $_POST['variation_id'] );

			/* set lead list cookies */
			if ( function_exists('wp_leads_set_current_lists') && isset( $_POST['wp_lead_id']) && !empty( $_POST['wp_lead_id']) ) {
				wp_leads_set_current_lists( $_POST['wp_lead_id'] );
			}

			die();
		}

	}

	/* Loads Inbound_Ajax pre init */
	$Inbound_Ajax = new Inbound_Ajax();
}