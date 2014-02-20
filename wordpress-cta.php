<?php
/*
Plugin Name: Calls to Action
Plugin URI: http://www.inboundnow.com/cta/
Description: Display Targeted Calls to Action on your WordPress site.
Version: 1.3.3
Author: Inbound Now
Author URI: http://www.inboundnow.com/
Text Domain: cta
Domain Path: shared/languages/cta/
*/

// DEFINE CONSTANTS AND GLOBAL VARIABLES
define('WP_CTA_CURRENT_VERSION', '1.3.3' );
define('WP_CTA_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('WP_CTA_PATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('WP_CTA_SLUG', plugin_basename( dirname(__FILE__) ) );
define('WP_CTA_FILE', __FILE__ );
define('WP_CTA_TEXT_DOMAIN', 'cta' );

$uploads = wp_upload_dir();
define('WP_CTA_UPLOADS_PATH', $uploads['basedir'].'/wp-calls-to-action/templates/' );
define('WP_CTA_UPLOADS_URLPATH', $uploads['baseurl'].'/wp-calls-to-action/templates/' );
define('WP_CTA_STORE_URL', 'http://www.inboundnow.com/cta/' );



if (is_admin())
	if(!isset($_SESSION)){@session_start();}

/* load core files */
switch (is_admin()) :
	case true :
		/* loads admin files */
		//include_once('functions/functions.global.php'); // old
		include_once('modules/module.activate.php');
		include_once('modules/module.post-type.php');
		include_once('modules/module.admin-menus.php');
		include_once('modules/module.ajax-setup.php');
		include_once('modules/module.enqueue.php');
		include_once('modules/module.global-settings.php');
		include_once('modules/module.clone.php');
		include_once('modules/module.install.php');
		include_once('modules/module.extension-updater.php');
		include_once('modules/module.ab-testing.php');
		include_once('modules/module.calls-to-action.php');
		include_once('modules/module.load-extensions.php');
		include_once('modules/module.metaboxes-global.php');
		include_once('modules/module.metaboxes-wp-call-to-action.php');
		include_once('modules/module.templates.php');
		include_once('modules/module.store.php');
		include_once('modules/module.utils.php');
		include_once('modules/module.customizer.php');
		include_once('modules/module.track.php');
		include_once('modules/module.alert.php');

		BREAK;

	case false :
		/* load front-end files */
		// include_once('functions/functions.global.php'); // old
		include_once('modules/module.post-type.php');
		include_once('modules/module.enqueue.php');
		include_once('modules/module.track.php');
		include_once('modules/module.click-tracking.php');
		include_once('modules/module.ajax-setup.php');

		include_once('modules/module.cookies.php');
		include_once('modules/module.ab-testing.php');
		include_once('modules/module.calls-to-action.php');
		include_once('modules/module.utils.php');
		include_once('modules/module.customizer.php');

		BREAK;
endswitch;

include_once('modules/module.widgets.php'); // Loads in both

/* TEMP function to make ajax call function */
// Set Leads to list from form tool. Need to consolidate into add_lead_to_list_tax
if (!function_exists('add_lead_lists_ajax')) {
function add_lead_lists_ajax($lead_id, $list_id, $tax = 'wplead_list_category') {

    $current_lists = wp_get_post_terms( $lead_id, $tax, 'id' );
    $all_term_ids = array();
    $all_term_slugs = array();
    foreach ($current_lists as $term ) {
        $add = $term->term_id;
        $slug = $term->slug;
        $all_term_ids[] = $add;
        $all_term_slugs[] = $slug;
    }
    // Set terms for lead tags taxomony
    $list_array = $list_id;
    if(is_array($list_array)) {
        foreach ($list_array as $key => $value) {
            $num = intval($value);
            if ( !in_array($num, $all_term_ids) ) {
                $all_term_ids[] = $num;
                wp_set_object_terms( $lead_id, $all_term_ids, $tax);
            }
        }
    }
}
}

/* Inbound Core Shared Files. */
add_action( 'plugins_loaded', 'inbound_load_shared' , 12);

function inbound_load_shared(){
	/* Check if Shared Files Already Loaded */
	if (defined('INBOUDNOW_SHARED'))
		return;

	/* Define Shared Constant for Load Prevention*/
	define('INBOUDNOW_SHARED','loaded');

	include_once('shared/tracking/store.lead.php'); // Lead Storage from cta
	include_once('shared/classes/form.class.php');  // Mirrored forms
	include_once('shared/classes/debug.class.php');  // Inbound Debug & Scripts Class
	include_once('shared/classes/compatibility.class.php');  // Inbound Compatibility Class
	include_once('shared/inboundnow/inboundnow.extend.php'); // Legacy
	include_once('shared/inboundnow/inboundnow.extension-licensing.php'); // Inboundnow Package Licensing
	include_once('shared/inboundnow/inboundnow.extension-updating.php'); // Inboundnow Package Updating
	include_once('shared/inboundnow/inboundnow.global-settings.php'); // Inboundnow Global Settings
	include_once('shared/metaboxes/template.metaboxes.php');  // Shared Shortcodes
	include_once('shared/inbound-shortcodes/inbound-shortcodes.php');  // Shared Shortcodes
	include_once('shared/classes/menu.class.php');  // Inbound Marketing Menu
	include_once('shared/classes/feedback.class.php');  // Inbound Feedback Form
}

/* Display Where CTA shortcodes are being used
$test = show_pages_containing_ctas('cta id=\"1802\"');
print_r($test);

function show_pages_containing_ctas($tag,$limit=1){
	global $wpdb;
	$sql = "SELECT ID FROM `{$wpdb->posts}` WHERE post_content LIKE \"%[$tag%\" LIMIT $limit";
	$sql = "SELECT DISTINCT(ID) FROM `{$wpdb->posts}` WHERE post_content LIKE \"%[$tag%\" AND post_status IN ('publish') LIMIT $limit";
	$ids = $wpdb->get_col($sql,0);
	return (is_array($ids)&&count($ids)>0)?$ids:array();
}
*/
