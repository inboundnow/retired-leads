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
define('WPL_STORE_URL', 'http://www.inboundnow.com' );
define('WPL_TEXT_DOMAIN', 'leads' );

include_once('modules/wpl.m.post-type.wp-lead.php');
include_once('modules/wpl.m.post-type.list.php');
include_once('modules/wpl.m.ajax-setup.php');
include_once('modules/wpl.m.form-integrations.php');
include_once('functions/wpl.f.global.php');
include_once('modules/wpl.m.management.php');

/* Inbound Core Shared Files. Lead files take presidence */
add_action( 'plugins_loaded', 'inbound_load_shared_leads' );
function inbound_load_shared_leads() {
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


add_action( 'wpl_store_lead_post', 'wpleads_hook_store_lead_post' );

if (is_admin()) {

	/*SETUP END*/
	register_activation_hook(__FILE__, 'wpleads_activate');

	include_once('modules/wpl.m.activate.php');
	include_once('modules/wpl.m.metaboxes.wp-lead.php');
	include_once('modules/wpl.m.wp_list_table-leads.php');
	include_once('modules/wpl.m.metaboxes.list.php');
	include_once('functions/wpl.f.admin.php');
	include_once('modules/wpl.m.global-settings.php');
	include_once('modules/wpl.m.dashboard.php');

}

if (!function_exists('lp_remote_connect')) {
	function lp_remote_connect($url) {
		$method1 = ini_get('allow_url_fopen') ? "Enabled" : "Disabled";
		if ($method1 == 'Disabled') {
			//do curl
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$url");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
			curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
			curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
			$string = curl_exec($ch);
		} else {
			$string = file_get_contents($url);
		}

		return $string;
	}
}

add_action('wp_enqueue_scripts', 'wpleads_enqueuescripts_header');
function wpleads_enqueuescripts_header() {
	global $post;
	$post_type = isset($post) ? get_post_type( $post ) : null;
	// Load Tracking Scripts
	if($post_type != "wp-call-to-action") {
		wp_enqueue_script('jquery');
		// Load form pre-population
		$form_prepopulation = get_option( 'wpl-main-form-prepopulation' , 1); // Check lead settings
		$lp_form_prepopulation = get_option( 'lp-main-landing-page-prepopulate-forms' , 1);
		if ($lp_form_prepopulation === "1") {
			$form_prepopulation = "1";
		}

		if ($form_prepopulation === "1") {
			wp_enqueue_script('form-population', WPL_URL . '/js/wpl.form-population.js', array( 'jquery','jquery-cookie'));
		} else {
			wp_dequeue_script('form-population');
		}

		// Load form tracking class
		$form_ids = get_option( 'wpl-main-tracking-ids' , 1);
		if ($form_ids) {
			wp_enqueue_script('wpl-assign-class', WPL_URL . '/js/wpl.assign-class.js', array( 'jquery'));
			wp_localize_script( 'wpl-assign-class', 'wpleads', array( 'form_ids' => $form_ids ) );
		}

	}
}

add_action('admin_enqueue_scripts', 'wpleads_admin_enqueuescripts');
function wpleads_admin_enqueuescripts($hook) {
	global $post;
	$post_type = isset($post) ? get_post_type( $post ) : null;
	if (isset($_GET['taxonomy']))
		return;

	wp_enqueue_style('wpleads-global-backend-css', WPL_URL.'/css/wpl.global-backend.css');
	if ((isset($_GET['post_type'])&&$_GET['post_type']=='wp-lead')||(isset($post->post_type)&&$post->post_type=='wp-lead'))
	{
		//echo $_GET['post_type'];exit;
		if ( $hook == 'post.php' ) {
			wp_enqueue_script('wpleads-edit', WPL_URL.'/js/wpl.admin.edit.js', array('jquery'));
			wp_enqueue_script('tinysort', WPL_URL.'/js/jquery.tinysort.js', array('jquery'));
			wp_enqueue_script('tag-cloud', WPL_URL.'/js/jquery.tagcloud.js', array('jquery'));
			wp_localize_script( 'wpleads-edit', 'wp_lead_map', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_lead_map_nonce' => wp_create_nonce('wp-lead-map-nonce') ) );
			/* Moved to shared asset loader
			//wp_enqueue_script('jquery-cookie', WPL_URL . 'shared/js/jquery.cookie.js', array( 'jquery' ));
			*/
			if (isset($_GET['small_lead_preview'])) {
			wp_enqueue_style('wpleads-popup-css', WPL_URL.'/css/wpl.popup.css');
			}
			wp_enqueue_style('wpleads-admin-edit-css', WPL_URL.'/css/wpl.edit-lead.css');
		}

		//Tool tip js
		wp_enqueue_script('jquery-qtip', WPL_URL . '/js/jquery-qtip/jquery.qtip.min.js');
		wp_enqueue_script('wpl-load-qtip', WPL_URL . '/js/jquery-qtip/load.qtip.js');
		wp_enqueue_style('qtip-css', WPL_URL . '/css/jquery.qtip.min.css'); //Tool tip css
		wp_enqueue_style('wpleads-admin-css', WPL_URL.'/css/wpl.admin.css');

		// Leads list management js
		wp_enqueue_script('wpleads-list', WPL_URL . '/js/wpl.leads-list.js');
		wp_enqueue_style('wpleads-list-css', WPL_URL.'/css/wpl.leads-list.css');



		if ( $hook == 'post-new.php' ) {
			wp_enqueue_script('wpleads-create-new-lead', WPL_URL . '/js/wpl.add-new.js');
		}


	}

	if ((isset($_GET['post_type'])&&$_GET['post_type']=='list')||(isset($post->post_type)&&$post->post_type=='list')) {
		wp_enqueue_style('wpleads-list-css', WPL_URL.'/css/wpl.leads-list.css');
		wp_enqueue_script('lls-edit-list-cpt', WPL_URL . '/js/wpl.admin.cpt.list.js');
	}
}


function wpleads_set_lead_id($lead_id){
	global $wpdb;
		$query = $wpdb->prepare(
				'SELECT ID FROM ' . $wpdb->posts . '
				WHERE post_title = %s
				AND post_type = \'wp-lead\'',
				$lead_id
				);
				$wpdb->query( $query );
				if ( $wpdb->num_rows ) {
					$lead_ID = $wpdb->get_var( $query );
					setcookie('wp_lead_id' , $lead_ID, time() + (20 * 365 * 24 * 60 * 60),'/');
				}
}

add_action( 'wp_head', 'wpleads_set_lead' );
function wpleads_set_lead() {
	if (isset($_GET['wpl_email'])) {
		$lead_id = $_GET['wpl_email'];
		wpleads_set_lead_id($lead_id);
	}
}

// DOESNT RUN UNLESS USER LOGGED IN =/
//add_action( 'wp_head', 'wp_leads_update_lead_page_views' );
function wp_leads_update_lead_page_views() {
global $wp; global $post;

$post_type = get_post_type( $post );

// Only proceed if lead exists
	if ( isset($_COOKIE['wp_lead_id']) && !is_admin() && !is_404() && $post_type != "wp-call-to-action") {

		/*
		//Revisit notication base
		// revisit cookie 2 hour timeout
		// add action and rename this parent function
		//http://www.flippercode.com/send-html-emails-using-wp-mail-wordpress
		add_filter( 'wp_mail_from', 'wp_leads_mail_from' );
		function wp_leads_mail_from( $email )
		{
		    return 'david@inboundnow.com';
		}
		add_filter( 'wp_mail_from_name', 'wp_leads_mail_from_name' );
		function wp_leads_mail_from_name( $name )
		{
		    return 'David';
		}
		if (!isset($_GET['cta'])) {
		$to = 'david@inboundnow.com';
		$subject = 'Hello from my blog!';
		$message = 'Check it out -- my blog is emailing you!';

		$mail = wp_mail($to, $subject, $message);

		if($mail) echo 'Your message has been sent!';
		else echo 'There was a problem sending your message. Please try again.';
		}
		*/

	}

}

if (is_admin()) {

	/**********************************************************/
	/******************CREATE SETTINGS SUBMENU*****************/
	add_action('admin_menu', 'wpleads_add_menu');
	function wpleads_add_menu() {
		//echo 1; exit;
		if (current_user_can('manage_options'))
		{
			add_submenu_page('edit.php?post_type=wp-lead', 'Lead Management', 'Lead Management', 'manage_options', 'lead_management','lead_management_admin_screen');

			add_submenu_page('edit.php?post_type=wp-lead', 'Forms', 'Create Forms', 'manage_options', 'inbound-forms-redirect',100);

			add_submenu_page('edit.php?post_type=wp-lead', 'Settings', 'Global Settings', 'manage_options', 'wpleads_global_settings','wpleads_display_global_settings');



		}
	}

	add_action('lp_lead_table_data_is_details_column','wpleads_add_user_edit_button');
	function wpleads_add_user_edit_button($item){
		$image = WPL_URL.'/images/icons/edit_user.png';
		echo '&nbsp;&nbsp;<a href="'.get_admin_url().'post.php?post='.$item['ID'].'&action=edit" target="_blank"><img src="'.$image.'" title="Edit Lead"></a>';
	}

	add_action('lp_module_lead_splash_post','wpleads_add_user_conversion_data_to_splash');
	function wpleads_add_user_conversion_data_to_splash($data) {
		$conversion_data = $data['lead_custom_fields']['wpleads_conversion_data'];
		//$test = get_post_meta($data['lead_id'],'wpl-lead-conversions', true);
		//print_r($test);
		echo "<h3  class='lp-lead-splash-h3'>Recent Conversions:</h3>";
		echo "<table>";
		echo "<tr>";
					echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-0'>#</td>";
					echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-1'>Location</td>";
					echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-2'>Datetime</td>";
					echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-3'>First-time?</td>";
		echo "<tr>";
		foreach ($conversion_data as $key=>$value)
		{
			$i = $key+1;
			//print_r($conversion_data);
			$value = json_decode($value, true);
			//print_r($value);
			foreach ($value as $k=>$row)
			{


				echo "<tr>";
					echo "<td>";
						echo "[$i]";
						//echo $row['id'];
						//print_r($row);exit;
					echo "</td>";
					echo "<td>";
						echo "<a href='".get_permalink($row['id'])."' target='_blank'>".get_the_title(intval($row['id']))."</a>";
					echo "</td>";
					echo "<td>";
						echo $row['datetime'];
					echo "</td>";
					echo "<td>";
						if ($row['first_time']==1)
						{
							echo "yes";
						}
					echo "</td>";
				echo "<tr>";
				$i++;
			}
		}

		echo "</table>";
	}
}