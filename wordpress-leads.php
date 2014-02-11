<?php
/*
Plugin Name: Leads
Plugin URI: http://www.inboundnow.com/landing-pages/downloads/lead-management/
Description: Wordpress Lead Manager provides CRM (Customer Relationship Management) applications for WordPress Landing Page plugin. Lead Manager Plugin provides a record management interface for viewing, editing, and exporting lead data collected by Landing Page Plugin.
Author: Inbound Now
Version: 1.3.1
Author URI: http://www.inboundnow.com/landing-pages/
*/

define('WPL_CURRENT_VERSION', '1.3.1' );
define('WPL_URL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('WPL_PATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );
define('WPL_CORE', plugin_basename( __FILE__ ) );
define('WPL_SLUG', plugin_basename( __FILE__ ) );
define('WPL_FILE',  __FILE__ );
define('WPL_STORE_URL', 'http://www.inboundnow.com' );
define('WPL_TEXT_DOMAIN', 'leads' );
$uploads = wp_upload_dir();
define('WPL_UPLOADS_PATH', $uploads['basedir'].'/leads/' );
define('WPL_UPLOADS_URLPATH', $uploads['baseurl'].'/leads/' );

if (is_admin())
	if(!isset($_SESSION)){@session_start();}


/* load core files */
switch (is_admin()) :
	case true :

		/* loads global */
		include_once('modules/module.enqueue.php');
		include_once('modules/module.post-type.wp-lead.php');
		include_once('modules/module.post-type.list.php');
		include_once('modules/module.ajax-setup.php');
		include_once('modules/module.form-integrations.php');

		/* load admin */
		include_once('modules/module.activate.php');
		include_once('modules/module.nav-menus.php');
		include_once('modules/module.lead-management.php');
		include_once('modules/module.metaboxes.wp-lead.php');
		include_once('modules/module.metaboxes.rule.php');
		include_once('modules/module.wp_list_table-leads.php');
		include_once('modules/module.metaboxes.list.php');
		include_once('modules/module.post-type.landing-pages.php');
		include_once('modules/module.post-type.rule.php');
		include_once('modules/module.global-settings.php');
		include_once('modules/module.dashboard.php');
		include_once('modules/module.tracking.php');

		BREAK;

	case false :
		/* load global */
		include_once('modules/module.enqueue.php');
		include_once('modules/module.post-type.wp-lead.php');
		include_once('modules/module.post-type.list.php');
		include_once('modules/module.post-type.rule.php');
		include_once('modules/module.ajax-setup.php');
		include_once('modules/module.form-integrations.php');
		include_once('modules/module.tracking.php');

		/* load frontend */

		BREAK;
endswitch;

/* load cron definitions - must be loaded outside of is_admin() conditional */
include_once('modules/module.cron.lead-rules.php');

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
	include_once('shared/inboundnow/inboundnow.extend.php');
	include_once('shared/inboundnow/inboundnow.extension-licensing.php'); // Legacy - Inboundnow Package Licensing
	include_once('shared/inboundnow/inboundnow.extension-updating.php'); // Legacy -Inboundnow Package Updating
	include_once('shared/inboundnow/inboundnow.global-settings.php'); // Inboundnow Global Settings
	include_once('shared/metaboxes/template.metaboxes.php');  // Shared Shortcodes
	include_once('shared/functions/global.shared.functions.php'); // Global Shared Utility functions
	include_once('shared/assets/assets.loader.class.php');  // Load Shared CSS and JS Assets
}

function wpleads_check_active() {
}
