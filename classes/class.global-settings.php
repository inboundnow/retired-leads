<?php

/**
 * Creates Global Settings 
 *
 * @package     Calls To Action
 * @subpackage  Global Settings
*/

if ( !class_exists('CTA_Global_Settings') ) {

	class CTA_Global_Settings {
	
		/**
		*  Initializes class
		*/
		public function __construct() {
			self::add_hooks();
		}
		
		/**
		*  Loads hooks and filters
		*/
		public static function add_hooks() {
			add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'enqueue_scripts' ) );
		}
		
		public static function enqueue_scripts() {
			$screen = get_current_screen();
			
			if ( ( isset($screen) && $screen->base != 'wp-call-to-action_page_wp_cta_global_settings' ) ){
				return;
			}
			
			if (isset($_GET['page'])&&($_GET['page']=='wp_cta_global_settings'&&$_GET['page']=='wp_cta_global_settings'))
			{}
			wp_enqueue_style('wp-cta-css-global-settings-here', WP_CTA_URLPATH . 'css/admin-global-settings.css');
		}
	
	}

	
	/**
	*  Loads CTA_Global_Settings on admin_init
	*/
	function load_CTA_Global_Settings() {
		$CTA_Global_Settings = new CTA_Global_Settings;
	}
	add_action( 'admin_init' , 'load_CTA_Global_Settings' );

}

