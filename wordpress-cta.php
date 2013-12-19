<?php
/*
Plugin Name: Calls to Action
Plugin URI: http://www.inboundnow.com/cta/
Description: Display Targeted Calls to Action on your WordPress site.
Version: 1.2.4
Author: David Wells, Hudson Atwell
Author URI: http://www.inboundnow.com/
*/

// DEFINE CONSTANTS AND GLOBAL VARIABLES
define('WP_CTA_CURRENT_VERSION', '1.2.4' );
define('WP_CTA_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('WP_CTA_PATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('WP_CTA_PLUGIN_SLUG', 'cta' );
define('WP_CTA_STORE_URL', 'http://www.inboundnow.com/cta/' );
$uploads = wp_upload_dir();
define('WP_CTA_UPLOADS_PATH', $uploads['basedir'].'/wp-calls-to-action/templates/' );
define('WP_CTA_UPLOADS_URLPATH', $uploads['baseurl'].'/wp-calls-to-action/templates/' );



/* Inbound Core Shared Files. */
add_action( 'plugins_loaded', 'inbound_load_shared' );

function inbound_load_shared(){
	include_once('shared/tracking/store.lead.php'); // Lead Storage from cta
	include_once('shared/classes/form.class.php');  // Mirrored forms

}

include_once('shared/inbound-shortcodes/inbound-shortcodes.php');  // Shared Shortcodes
include_once('shared/classes/menu.class.php');  // Inbound Marketing Menu
include_once('shared/classes/feedback.class.php');  // Inbound Feedback Form

/**
 * LOAD BACKEND ONLY FILES
 */
if (is_admin())
{
	if(!isset($_SESSION)){@session_start();}

	include_once('functions/functions.admin.php');
	include_once('modules/module.global-settings.php');
	include_once('modules/module.clone.php');
	include_once('modules/module.install.php');
	include_once('modules/module.extension-updater.php');
}

/**
 * LOAD FILES THAT WILL BE USED ON THE FRONT AND BACKEND
 */
include_once('functions/functions.global.php');
include_once('modules/module.post-type.php');
include_once('modules/module.track.php');
add_action('init', 'wp_cta_click_track_redirect', 11); // Click Tracking init
include_once('modules/module.click-tracking.php');


include_once('modules/module.ajax-setup.php');
include_once('modules/module.utils.php');
include_once('modules/module.widgets.php');
include_once('modules/module.cookies.php');
include_once('modules/module.ab-testing.php');




/**
 * LOAD COMMONLY USED TEMPLATE TOOLS
 */

add_action('wp_cta_init', 'inbound_include_template_functions');
if (!function_exists('inbound_include_template_functions'))
{
	function inbound_include_template_functions()
	{
		include_once('shared/functions.templates.php');
	}
}


/**
 * LOAD CTA TEMPLATES AND EXTENSIONS
 */

if (is_admin())
{
	//include additional metaboxes
	include_once('shared/metaboxes/template.metaboxes.php');  // Shared Shortcodes
	include_once('load.extensions.php');
	include_once('modules/module.metaboxes.php');
}

/**
 * REGISTER ACTIVATION HOOK
 */



register_activation_hook(__FILE__, 'wp_call_to_action_activate');
function wp_call_to_action_activate($wp = '3.6', $php = '5.2.4', $lp = '1.3.6', $leads = '1.2.1')
{
	global $wp_version;
	if ( version_compare( PHP_VERSION, $php, '<' ) ) {
	    $flag = 'PHP';
	    $version = 'PHP' == $flag ? $php : $wp;
		wp_die('<p>The <strong>WordPress Calls to Action</strong> plugin requires'.$flag.'  version '.$php.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
		deactivate_plugins( basename( __FILE__ ) );
	} elseif ( version_compare( $wp_version, $wp, '<' ) ) {
	    $flag = 'WordPress';
	    wp_die('<p>The <strong>WordPress Calls to Action</strong> plugin requires'.$flag.'  version '.$wp.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	    deactivate_plugins( basename( __FILE__ ) );
	} elseif (defined('LANDINGPAGES_CURRENT_VERSION') && version_compare( LANDINGPAGES_CURRENT_VERSION, $lp, '<' )){
		$flag = 'Landing Pages';
		wp_die('<p>The <strong>WordPress Calls to Action</strong> plugin requires '.$flag.'  version '.$lp.' or greater. <br><br>Please Update WordPress Landing Page Plugin to update Calls to action</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	} elseif (defined('LEADS_CURRENT_VERSION') && version_compare( LEADS_CURRENT_VERSION, $leads, '<' )){
		$flag = 'Leads';
		wp_die('<p>The <strong>WordPress Calls to Action</strong> plugin requires '.$flag.'  version '.$leads.' or greater. <br><br>Please Update WordPress Leads Plugin to update Calls to action</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	} else {
	// Activate Plugin
	add_option( 'wp_cta_global_css', '', '', 'no' );
	add_option( 'wp_cta_global_js', '', '', 'no' );
	add_option( 'wp_cta_global_record_admin_actions', '1', '', 'no' );
	add_option( 'wp_cta_global_wp_cta_slug', 'cta', '', 'no' );
	update_option( 'wp_cta_activate_rewrite_check', '1');

	global $wp_rewrite;
	$wp_rewrite->flush_rules();
	}
	// Add default CTA setup and setup 3 categores: sidebar, blog post, popup

}

/**
 * APPLY CTA CUSTOM JS AND CUSTOM CSS TO FRONT END
 */

add_action('wp_head','wp_call_to_actions_insert_custom_head');
function wp_call_to_actions_insert_custom_head() {
	global $post;

   if (isset($post)&&'wp-call-to-action'==$post->post_type)
   {
		//$global_js =  htmlspecialchars_decode(get_option( 'wp_cta_global_js', '' ));
		$global_record_admin_actions = get_option( 'wp_cta_global_record_admin_actions', '0' );

		$custom_css_name = apply_filters('wp-cta-custom-css-name','wp-cta-custom-css');
		$custom_js_name = apply_filters('wp-cta-custom-js-name','wp-cta-custom-js');
		//echo $custom_css_name;
		$custom_css = get_post_meta($post->ID, $custom_css_name, true);
		$custom_js = get_post_meta($post->ID, $custom_js_name, true);
		//echo $this_id;exit;

		//Print Cusom CSS
		if (!stristr($custom_css,'<style'))
		{
			echo '<style type="text/css" id="wp_cta_css_custom">'.$custom_css.'</style>';
		}
		else
		{
			echo $custom_css;
		}
		if (!stristr($custom_css,'<script'))
		{
			echo '<script type="text/javascript" id="wp_cta_js_custom">jQuery(document).ready(function($) {
			'.$custom_js.' });</script>';
		}
		else
		{
			echo $custom_js;
		}

		if ($global_record_admin_actions==0&&current_user_can( 'manage_options' ))
		{
		}
		else
		{

			if (!wp_cta_determine_spider())
			{
				//wp_cta_set_page_views(get_the_ID($this_id));
			}
		}

		//rewind_posts();
		//wp_reset_query();
   }
}


/**
 * LOAD SUB MENU SECTIONS
 */

if (is_admin())
{
	include_once('modules/module.templates.php');
	include_once('modules/module.store.php');

	// Create Sub-menu

	add_action('admin_menu', 'wp_cta_add_menu');

	function wp_cta_add_menu()
	{
		if (current_user_can('manage_options'))
		{

			add_submenu_page('edit.php?post_type=wp-call-to-action', 'Forms', 'Create Forms', 'manage_options', 'inbound-forms-redirect',100);

			// coming soon
			add_submenu_page('edit.php?post_type=wp-call-to-action', 'Templates', 'Manage Templates', 'manage_options', 'wp_cta_manage_templates','wp_cta_manage_templates',100);

			// comming soon add_submenu_page('edit.php?post_type=wp-call-to-action', 'Get Addons', 'Add-on Extensions', 'manage_options', 'wp_cta_store','wp_cta_store_display',100);

			 add_submenu_page('edit.php?post_type=wp-call-to-action', 'Settings', 'Global Settings', 'manage_options', 'wp_cta_global_settings','wp_cta_display_global_settings');

			// Add settings page for frontend editor
    		add_submenu_page('edit.php?post_type=wp-call-to-action', __('Editor','Editor'), __('Editor','Editor'), 'manage_options', 'wp-cta-frontend-editor', 'wp_cta_frontend_editor_screen');

		}
	}

}

/**
 * LOAD CORRECT CTA TEMPLATE ON FRONTEND
 */

add_filter('single_template', 'wp_cta_custom_template');

function wp_cta_custom_template($single) {
    global $wp_query, $post, $query_string;

	$template = get_post_meta($post->ID, 'wp-cta-selected-template', true);
	$template = apply_filters('wp_cta_selected_template',$template);

	if (isset($template))
	{
		//echo 2;exit;
		if ($post->post_type == "wp-call-to-action")
		{
			if (strstr($template,'-slash-'))
			{
				$template = str_replace('-slash-','/',$template);
			}

			$my_theme =  wp_get_theme($template);

			if ($my_theme->exists())
			{
				return "";
			}
			else if ($template!='default')
			{
				$template = str_replace('_','-',$template);
				//echo WP_CTA_URLPATH.'templates/'.$template.'/index.php'; exit;
				if (file_exists(WP_CTA_PATH.'templates/'.$template.'/index.php'))
				{
					//query_posts ($query_string . '&showposts=1');
					return WP_CTA_PATH.'templates/'.$template.'/index.php';
				}
				else
				{
					//query_posts ($query_string . '&showposts=1');
					return WP_CTA_UPLOADS_PATH.$template.'/index.php';
				}
			}
		}
	}
    return $single;
}

/**
 * SETUP DEBUG TOOLS
 */

add_action( 'init', 'inbound_meta_debug' );
if (!function_exists('inbound_meta_debug')) {
	function inbound_meta_debug(){
	//print all global fields for post
	if (isset($_GET['debug'])) {
			global $wpdb;
			$data   =   array();
			$wpdb->query("
			  SELECT `meta_key`, `meta_value`
				FROM $wpdb->postmeta
				WHERE `post_id` = ".$_GET['post']."
			");
			foreach($wpdb->last_result as $k => $v){
				$data[$v->meta_key] =   $v->meta_value;
			};
			if (isset($_GET['post']))
			{
				echo "<pre>";
				print_r( $data);
				echo "</pre>";
			}
		}
	}
}
// Load visual editor scripts
include_once('modules/module.customizer.php');
?>