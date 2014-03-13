<?php
/*
Plugin Name: Leads
Plugin URI: http://www.inboundnow.com/leads/
Description: Track website visitor activity, manage incoming leads, and send collected emails to your email service provider.
Author: Inbound Now
Version: 1.3.5
Author URI: http://www.inboundnow.com/
Text Domain: landing-pages
Domain Path: shared/languages/leads/
*/

define('WPL_CURRENT_VERSION', '1.3.5' );
define('WPL_URL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('WPL_PATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );
define('WPL_CORE', plugin_basename( __FILE__ ) );
define('WPL_SLUG', plugin_basename( __FILE__ ) );
define('WPL_FILE',  __FILE__ );
define('WPL_STORE_URL', 'http://www.inboundnow.com' );
$uploads = wp_upload_dir();
define('WPL_UPLOADS_PATH', $uploads['basedir'].'/leads/' );
define('WPL_UPLOADS_URLPATH', $uploads['baseurl'].'/leads/' );
if(!defined('INBOUND_NOW_LEADS_PATH')) { define('INBOUND_NOW_LEADS_PATH', WP_PLUGIN_DIR . '/leads'); }

if (is_admin()){
	if(!isset($_SESSION)){@session_start();}
}

/* load core files */
switch (is_admin()) :
	case true :

		/* load admin */
		include_once('modules/module.activate.php');
		include_once('modules/module.ajax-setup.php');
		include_once('modules/module.nav-menus.php');
		include_once('modules/module.wp_list_table-leads.php');
		include_once('modules/module.metaboxes.wp-lead.php');
		include_once('modules/module.metaboxes.list.php');
		include_once('modules/module.metaboxes.automation.php');
		include_once('modules/module.post-type.wp-lead.php');
		include_once('modules/module.post-type.list.php');
		include_once('modules/module.post-type.landing-pages.php');
		include_once('modules/module.post-type.automation.php');
		include_once('modules/module.lead-management.php');
		include_once('modules/module.form-integrations.php');
		include_once('modules/module.global-settings.php');
		include_once('modules/module.dashboard.php');
		include_once('modules/module.tracking.php');
		include_once('modules/module.enqueue-admin.php');
		include_once('modules/module.form-integrations.php');
		BREAK;

	case false :
		/* load global */
		include_once('modules/module.ajax-setup.php');
		include_once('modules/module.post-type.wp-lead.php');
		include_once('modules/module.post-type.list.php');
		include_once('modules/module.form-integrations.php');

		/* load frontend */
		include_once('modules/module.enqueue-frontend.php');
		include_once('modules/module.tracking.php');


		BREAK;
endswitch;

/* load cron definitions - must be loaded outside of is_admin() conditional */
//include_once('modules/module.cron.lead-rules.php');

/* Inbound Core Shared Files. Lead files take presidence */
add_action( 'plugins_loaded', 'inbound_load_shared_leads' );
function inbound_load_shared_leads()
{
	/* Check if Shared Files Already Loaded */
	if (defined('INBOUDNOW_SHARED'))
		return;

	/* Define Shared Constant for Load Prevention*/
	define('INBOUDNOW_SHARED','loaded');

	include_once('shared/tracking/store.lead.php'); // Lead Storage from landing pages
	include_once('shared/classes/form.class.php');  // Mirrored forms
	include_once('shared/classes/menu.class.php');  // Inbound Marketing Menu
	include_once('shared/classes/feedback.class.php');  // Inbound Feedback Form
	include_once('shared/classes/debug.class.php');  // Inbound Debug & Scripts Class
	include_once('shared/classes/compatibility.class.php');  // Inbound Compatibility Class

	include_once('shared/inbound-shortcodes/inbound-shortcodes.php');  // Shared Shortcodes
	include_once('shared/extend/inboundnow.extend.php');
	include_once('shared/extend/inboundnow.extension-licensing.php'); // Legacy - Inboundnow Package Licensing
	include_once('shared/extend/inboundnow.extension-updating.php'); // Legacy -Inboundnow Package Updating
	include_once('shared/extend/inboundnow.global-settings.php'); // Inboundnow Global Settings
	include_once('shared/metaboxes/template.metaboxes.php');  // Shared Shortcodes
	include_once('shared/functions/global.shared.functions.php'); // Global Shared Utility functions
	include_once('shared/assets/assets.loader.class.php');  // Load Shared CSS and JS Assets
	include_once('shared/functions/global.leads.cpt.php'); // Shared Lead functionality
}

function wpleads_check_active() {
}
