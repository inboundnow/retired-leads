<?php

/* Load Shared Files */
if (!class_exists('Inbound_Load_Shared')) {

	class Inbound_Load_Shared {
		
		/**
		 * Initialize shared component loading only once.
		 *  
		 */
		public static function init() {
			/* Bail if shared files already loaded */
			if (defined('INBOUDNOW_SHARED')) {
				return;
			}

			self::load_constants();
			self::load_files();
			self::load_legacy_elements();
		}
		
		/**
		 *  Define constants used by shared files here
		 *  
		 */
		public static function load_constants() {
			define( 'INBOUDNOW_SHARED' , 'loaded' );
			define( 'INBOUDNOW_SHARED_PATH' ,	self::get_shared_path() );
			define( 'INBOUDNOW_SHARED_URLPATH' ,	self::get_shared_urlpath() );
		}
		
		/**
		 *  Include shared php files here
		 *  
		 */
		public static function load_files() {			

			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.post-type.wp-lead.php'); 	
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.form.php');	// Mirrored forms		
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.menu.php');	// Inbound Marketing Menu
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.feedback.php');	// Inbound Feedback Form
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.debug.php');	// Inbound Debug & Scripts Class
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.compatibility.php');	// Inbound Compatibility Class
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.templating-engine.php');	// {{token}} Replacement Engine
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.shortcodes.email-template.php'); 
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.lead-fields.php');	
			include_once( INBOUDNOW_SHARED_PATH . 'classes/class.inbound-forms.akismet.php');	
			include_once( INBOUDNOW_SHARED_PATH . 'tracking/store.lead.php'); // Lead Storage from landing pages
			
			include_once( INBOUDNOW_SHARED_PATH . 'shortcodes/inbound-shortcodes.php');	// Shared Shortcodes
			include_once( INBOUDNOW_SHARED_PATH . 'extend/inboundnow.extend.php');
			include_once( INBOUDNOW_SHARED_PATH . 'extend/inboundnow.global-settings.php'); // Inboundnow Global Settings
			include_once( INBOUDNOW_SHARED_PATH . 'metaboxes/template.metaboxes.php');	// Shared Shortcodes
			include_once( INBOUDNOW_SHARED_PATH . 'functions/global.shared.functions.php'); // Global Shared Utility functions
			include_once( INBOUDNOW_SHARED_PATH . 'assets/assets.loader.class.php');	// Load Shared CSS and JS Assets

			self::load_legacy_elements();
		}
		
		/**
		 *  Legacy constants go here
		 *  
		 *  
		 */
		public static function load_legacy_elements() {
			
			if ( !defined( 'LANDINGPAGES_TEXT_DOMAIN' ) ) {
				define('LANDINGPAGES_TEXT_DOMAIN', 'landing-pages' );
			}
			
			if (!defined('INBOUNDNOW_LABEL')) {
				define('INBOUNDNOW_LABEL', 'inboundnow-legacy' );
			}
			
		}
		
		/**
		 *  Returns the correct absolute path to the Inbound Now shared directory
		 *  
		 *  @return Path to shared folder
		 *  
		 */
		public static function get_shared_path() {
			if ( defined('WP_CTA_PATH') ) {
				return WP_CTA_PATH . 'shared/';
			} else if (	defined('LANDINGPAGES_PATH') ) {
				return LANDINGPAGES_PATH . '/shared/';
			} else if (	defined('WPL_PATH') ) {
				return WPL_PATH . '/shared/';
			}
		}
		
		/**
		 *  Returns the correct URL path to the Inbound Now Shared directory
		 *  
		 *  @return URL path to shared directory
		 *  
		 */
		public static function get_shared_urlpath() {
			if ( defined('WP_CTA_PATH') ) {
				return WP_CTA_URLPATH . 'shared/';
			} else if (	defined('LANDINGPAGES_PATH') ) {
				return LANDINGPAGES_URLPATH . '/shared/';
			} else if (	defined('WPL_PATH') ) {
				return WPL_URLPATH . '/shared/';
			}
		}
	}	
}