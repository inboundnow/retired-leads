<?php
/*
Plugin Name: Leads
Plugin URI: http://www.inboundnow.com/leads/
Description: Track website visitor activity, manage incoming leads, and send collected emails to your email service provider.
Author: Inbound Now
Version: 1.3.7
Author URI: http://www.inboundnow.com/
Text Domain: leads
Domain Path: shared/languages/leads/
*/

/* WordPress Leads Class */
if ( ! class_exists( 'WordPress_Leads' ) ) {
final class WordPress_Leads {

	private static $instance;


	/**
	 * Main WordPress_Leads Instance
	 *
	 * Insures that only one instance of WordPress_Leads exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	*/
	public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WordPress_Leads ) ) {
				self::$instance = new WordPress_Leads;
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
			}
			return self::$instance;
		}
	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'leads' ), '1.7' );
	}

	/* Disable unserializing of the class */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'leads' ), '1.7' );
	}

	/* Setup plugin constants */
	private function setup_constants() {
		define('WPL_CURRENT_VERSION', '1.3.7' );
		define('WPL_URL',  plugins_url( '/', __FILE__ ) );
		define('WPL_PATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );
		define('WPL_CORE', plugin_basename( __FILE__ ) );
		define('WPL_SLUG', plugin_basename( __FILE__ ) );
		define('WPL_FILE',  __FILE__ );
		define('WPL_STORE_URL', 'http://www.inboundnow.com' );
		$uploads = wp_upload_dir();
		define('WPL_UPLOADS_PATH', $uploads['basedir'].'/leads/' );
		define('WPL_UPLOADS_URLPATH', $uploads['baseurl'].'/leads/' );
	}

	/* Include required files */
	private function includes() {
		global $inboundnow_options;

		if ( is_admin() ) {
			/* Admin Includes */
			require_once('modules/module.activate.php');
			require_once('modules/module.ajax-setup.php');
			require_once('modules/module.nav-menus.php');
			require_once('modules/module.wp_list_table-leads.php');
			require_once('modules/module.metaboxes.wp-lead.php');
			require_once('modules/module.metaboxes.list.php');
			require_once('modules/module.post-type.wp-lead.php');
			require_once('modules/module.post-type.list.php');
			require_once('modules/module.post-type.landing-pages.php');
			require_once('modules/module.lead-management.php');
			require_once('modules/module.form-integrations.php');
			require_once('modules/module.global-settings.php');
			require_once('modules/module.dashboard.php');
			require_once('modules/module.tracking.php');
			require_once('modules/module.enqueue-admin.php');
			require_once('modules/module.form-integrations.php');
			
			require_once('classes/class.post-type.email-template.php');
			require_once('classes/class.metaboxes.email-template.php');
			require_once('classes/class.shortcodes.email-template.php');
			require_once('classes/class.wordpress-core.email.php');

		} else {
			/* Frontend Includes */
			//require_once INBOUND_NOW_PATH . 'includes/process-download.php';
			/* load global */
			require_once('modules/module.ajax-setup.php');
			require_once('modules/module.post-type.wp-lead.php');
			require_once('modules/module.post-type.list.php');
			require_once('modules/module.form-integrations.php');
			require_once('classes/class.post-type.email-template.php');
			require_once('classes/class.metaboxes.email-template.php');
			require_once('classes/class.shortcodes.email-template.php');
			require_once('classes/class.wordpress-core.email.php');
			/* load frontend */
			require_once('modules/module.enqueue-frontend.php');
			require_once('modules/module.tracking.php');

		}

		//require_once INBOUND_NOW_PATH . 'includes/install.php';
	}

	/* Loads the plugin language files */
	public function load_textdomain() {
			// Set filter for plugin's languages directory
			$inbound_now_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$inbound_now_lang_dir = apply_filters( 'inbound_now_languages_directory', $inbound_now_lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'inbound-now' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'inbound-now', $locale );

			// Setup paths to current locale file
			$mofile_local  = $inbound_now_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/inbound-now/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/inbound-now folder
				load_textdomain( 'inbound-now', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/inbound-now-pro/languages/ folder
				load_textdomain( 'inbound-now', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'inbound-now', false, $inbound_now_lang_dir );
			}
		}
	}

}

/* Load Leads  */
function Run_WordPress_Leads() {
	return WordPress_Leads::instance();
}
Run_WordPress_Leads();

/* Load Shared Files */
require_once('load-shared.php'); // Load shared files




// Legacy function
function wpleads_check_active() {
}
