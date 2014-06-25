<?php
/*
Plugin Name: Calls to Action
Plugin URI: http://www.inboundnow.com/cta/
Description: Display Targeted Calls to Action on your WordPress site.
Version: 2.0.9
Author: InboundNow
Author URI: http://www.inboundnow.com/
Text Domain: cta
Domain Path: shared/languages/cta/
*/

// DEFINE CONSTANTS AND GLOBAL VARIABLES
define('WP_CTA_CURRENT_VERSION', '2.0.9' );
define('WP_CTA_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('WP_CTA_PATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('WP_CTA_SLUG', plugin_basename( dirname(__FILE__) ) );
define('WP_CTA_FILE', __FILE__ );

$uploads = wp_upload_dir();
define('WP_CTA_UPLOADS_PATH', $uploads['basedir'].'/calls-to-action/templates/' );
define('WP_CTA_UPLOADS_URLPATH', $uploads['baseurl'].'/calls-to-action/templates/' );
define('WP_CTA_STORE_URL', 'http://www.inboundnow.com/cta/' );


/* load core files */
switch (is_admin()) :
	case true :
		/* loads admin files */
		//include_once('functions/functions.global.php'); // old
		include_once('modules/module.activate.php');
		include_once('classes/class.post-type.wp-call-to-action.php');
		include_once('classes/class.extension.wp-lead.php');
		include_once('classes/class.extension.wordpress-seo.php');
		include_once('modules/module.admin-menus.php');
		include_once('modules/module.ajax-setup.php');
		include_once('modules/module.enqueue.php');
		include_once('modules/module.global-settings.php');
		include_once('modules/module.clone.php');
		include_once('modules/module.install.php');
		include_once('modules/module.extension-updater.php');
		include_once('modules/module.ab-testing.php');
		include_once('modules/module.widgets.php');
		include_once('modules/module.calls-to-action.php');
		include_once('modules/module.load-extensions.php');
		include_once('modules/module.metaboxes-global.php');
		include_once('modules/module.metaboxes-wp-call-to-action.php');
		include_once('modules/module.templates.php');
		include_once('modules/module.store.php');
		include_once('modules/module.utils.php');
		include_once('modules/module.customizer.php');
		include_once('modules/module.track.php');

		/* Temp update notice */
		function wp_major_cta_update_notice(){
		    global $pagenow;
		    global $current_user ;
		    $user_id = $current_user->ID;
		    if ( ! get_user_meta($user_id, 'wp_cta_major_update_ignore') ) {
		             echo '<div class="updated" style="position:relative;">
		             <a style="position: absolute; font-size: 20px; top: 10px; right: 30px;" href="?wp_cta_major_update_ignore=0">
		             Sounds good! Hide This Message
		             </a>
		             	<h1>Welcome to WordPress Calls to Action Version 2.0</h1>
		             	<p>Please visit and re-save your current call to action templates. There were a number of necessary changes to the plugin to make it much much better!<br><br> New to the plugin? <a href="http://docs.inboundnow.com/guide/how-to-place-calls-to-action/">Learn how to use it here.</a></p>
		                 <p><b style="font-size:18px; font-weight:bold;margin-bottom:5px;">Whats new?</b><br>

		                    <ul style="list-style: square; padding-left: 20px;margin-top: -10px;">
			                    <li>Faster Call to Action load times</li>
			                    <li>Better A/B Testing functionality</li>
			                    <li>Mobile responsive Call to action templates</li>
			                    <li>A new & improved call to action templating engine</li>
			                    <li>All around code improvements</li>
		                    </ul>
						<b style="font-size:18px; font-weight:bold;margin-bottom:5px;margin-top:20px; display:block;">Whats different?</b>
						We had to back out popups and slidein functionality for the initial release. Also, the like to download CTA is no longer in the plugin.<br><br>
		                <span style="color:red;">Important:</span> We are no longer supporting versions lower than 2.0.0. You can revert to the older version of the CTA plugin by downloading the files here: http://wordpress.org/plugins/cta/developers/ under "Other Versions"
		                 </p>
		             </div>';
		    }
		}
		add_action('admin_notices', 'wp_major_cta_update_notice');
		add_action('admin_init', 'wp_cta_major_notice_ignore');
		function wp_cta_major_notice_ignore() {
		    global $current_user;
		        $user_id = $current_user->ID;
		        if ( isset($_GET['wp_cta_major_update_ignore']) && '0' == $_GET['wp_cta_major_update_ignore'] ) {
		             add_user_meta($user_id, 'wp_cta_major_update_ignore', 'true', true);
		    }
		}

		BREAK;

	case false :
		/* load front-end files */
		// include_once('functions/functions.global.php'); // old
		include_once('modules/module.load-extensions.php');
		include_once('classes/class.post-type.wp-call-to-action.php');
		include_once('classes/class.extension.wp-lead.php');
		include_once('classes/class.extension.wordpress-seo.php');
		include_once('modules/module.enqueue.php');
		include_once('modules/module.track.php');
		include_once('modules/module.click-tracking.php');
		include_once('modules/module.ajax-setup.php');
		include_once('modules/module.widgets.php');
		include_once('modules/module.cookies.php');
		include_once('modules/module.ab-testing.php');
		include_once('modules/module.calls-to-action.php');
		include_once('modules/module.utils.php');
		include_once('modules/module.customizer.php');

		BREAK;
endswitch;

/* Inbound Core Shared Files. */
add_action( 'plugins_loaded', 'inbound_load_shared' , 12);

function inbound_load_shared(){
	/* Check if Shared Files Already Loaded */
	if (defined('INBOUDNOW_SHARED'))
		return;

	/* Define Shared Constant for Load Prevention*/
	define('INBOUDNOW_SHARED','loaded');

	include_once('shared/tracking/store.lead.php'); // Lead Storage from landing pages
	include_once('shared/classes/class.form.php');  // Mirrored forms
	include_once('shared/classes/class.menu.php');  // Inbound Marketing Menu
	include_once('shared/classes/class.feedback.php');  // Inbound Feedback Form
	include_once('shared/classes/class.debug.php');  // Inbound Debug & Scripts Class
	include_once('shared/classes/class.compatibility.php');  // Inbound Compatibility Class
	include_once('shared/shortcodes/inbound-shortcodes.php');  // Shared Shortcodes
	include_once('shared/extend/inboundnow.extend.php');
	include_once('shared/extend/inboundnow.extension-licensing.php'); // Legacy - Inboundnow Package Licensing
	include_once('shared/extend/inboundnow.extension-updating.php'); // Legacy -Inboundnow Package Updating
	include_once('shared/extend/inboundnow.global-settings.php'); // Inboundnow Global Settings
	include_once('shared/metaboxes/template.metaboxes.php');  // Shared Shortcodes
	include_once('shared/functions/global.shared.functions.php'); // Global Shared Utility functions
	include_once('shared/assets/assets.loader.class.php');  // Load Shared CSS and JS Assets
	include_once('shared/functions/global.leads.cpt.php'); // Shared Lead functionality

}



?>
