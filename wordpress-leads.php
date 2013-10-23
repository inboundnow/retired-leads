<?php
/* 
Plugin Name: Leads
Plugin URI: http://www.inboundnow.com/landing-pages/downloads/lead-management/
Description: Wordpress Lead Manager provides CRM (Customer Relationship Management) applications for WordPress Landing Page plugin. Lead Manager Plugin provides a record management interface for viewing, editing, and exporting lead data collected by Landing Page Plugin. 
Author: Hudson Atwell(@atwellpub), David Wells (@inboundnow)
Version: 1.0.7
Author URI: http://www.inboundnow.com/landing-pages/
*/

define('WPL_URL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('WPL_PATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );
define('WPL_CORE', plugin_basename( __FILE__ ) );
define('WPL_STORE_URL', 'http://www.inboundnow.com' );

include_once('modules/wpl.m.post-type.wp-lead.php'); 
include_once('modules/wpl.m.post-type.list.php'); 
include_once('modules/wpl.m.ajax-setup.php'); 
include_once('modules/wpl.m.form-integrations.php'); 
include_once('functions/wpl.f.global.php'); 

/* Inbound Core Shared Files. Lead files take presidence */
include_once('shared/tracking/store.lead.php'); // Lead Storage from landing pages
include_once('shared/classes/form.class.php');  // Mirrored forms			
include_once('shared/inboundnow/inboundnow.extension-licensing.php'); // Inboundnow Package Licensing
include_once('shared/inboundnow/inboundnow.extension-updating.php'); // Inboundnow Package Updating		

add_action( 'wpl_store_lead_post', 'wpleads_hook_store_lead_post' );

if (is_admin()) 
{
	load_plugin_textdomain('wpleads',false,dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	/*SETUP NAVIGATION AND DISPLAY ELEMENTS
	$tab_slug = 'lp-license-keys';
	$lp_global_settings[$tab_slug]['label'] = 'License Keys';	
	
	$lp_global_settings[$tab_slug]['options'][] = lp_add_option($tab_slug,"license-key","lead-manager","","Lead Manager","Head to http://www.inboundnow.com/landing-pages/account/ to retrieve your license key for Lead Manager for Landing Pages", $options=null);

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
// Needs optimization
add_action('wp_head', 'wp_leads_get_page_final_id');
function wp_leads_get_page_final_id(){
		global $post;
		$current_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = preg_replace('/\?.*/', '', $current_url);
		
		$page_id = wpl_url_to_postid($current_url);

		$site_url = get_option('siteurl');
		$clean_current_url = rtrim($current_url,"/");

		// If homepage
		if($clean_current_url === $site_url){
			$page_id = get_option('page_on_front'); // 
		}

		// If category page
		if (is_category() || is_archive()) {
		$cat = get_category_by_path(get_query_var('category_name'),false);
			$page_id = "cat_" . $cat->cat_ID;
			//$current_name = $cat->cat_name;
			$post_type = "category";
			}
		if (is_tag()){
			$page_id = "tag_" . get_query_var('tag_id');
		}
			
		if(is_home()) { $page_id = get_option( 'page_for_posts' ); }

		elseif(is_front_page()){ $page_id = get_option('page_on_front'); }

		if ($page_id === 0) {
			$page_id = $post->ID;
		}

		return $page_id;
}

if (!function_exists('lp_remote_connect')) {
function lp_remote_connect($url)
{
	$method1 = ini_get('allow_url_fopen') ? "Enabled" : "Disabled";
	if ($method1 == 'Disabled')
	{
		//do curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		$string = curl_exec($ch);
	}
	else
	{
		$string = file_get_contents($url);
	}
	
	return $string;
}
}

add_action('wp_enqueue_scripts', 'wpleads_enqueuescripts_header');
function wpleads_enqueuescripts_header()
{
	global $post;
	$post_type = get_post_type( $post );
	$current_page = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$post_id = wpl_url_to_postid($current_page);
	
	(isset($_SERVER['HTTP_REFERER'])) ? $referrer = $_SERVER['HTTP_REFERER'] : $referrer ='direct access';	
	(isset($_SERVER['REMOTE_ADDR'])) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = '0.0.0.0.0';
    $lead_cpt_id = (isset($_COOKIE['wp_lead_id'])) ? $_COOKIE['wp_lead_id'] : false;
    $lead_email = (isset($_COOKIE['wp_lead_email'])) ? $_COOKIE['wp_lead_email'] : false;
    $lead_unique_key = (isset($_COOKIE['wp_lead_uid'])) ? $_COOKIE['wp_lead_uid'] : false;
	
	// Localize lead data    
	$lead_data_array = array();
		if ($lead_cpt_id) {
			$lead_data_array['lead_id'] = $lead_cpt_id;
			$type = 'wplid';}
		if ($lead_email) {
			$lead_data_array['lead_email'] = $lead_email;
			$type = 'wplemail';}
		if ($lead_unique_key) {
	    	$lead_data_array['lead_uid'] = $lead_unique_key;
			$type = 'wpluid'; 
		}
	
	// Load Tracking Scripts
	if($post_type != "wp-call-to-action") {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-cookie', WPL_URL . '/js/jquery.cookie.js', array( 'jquery' ));
		wp_register_script('jquery-total-storage',WPL_URL . '/js/jquery.total-storage.min.js', array( 'jquery' ));
		wp_enqueue_script('jquery-total-storage');

		if($post_id === 0){
			$final_page_id = wp_leads_get_page_final_id();
			} else {
			$final_page_id = $post_id;
			}

		wp_enqueue_script( 'funnel-tracking' , WPL_URL . '/js/wpl.funnel-tracking.js', array( 'jquery','jquery-cookie'));
		wp_enqueue_script( 'store-lead-ajax' , WPL_URL . '/shared/tracking/js/store.lead.ajax.js', array( 'jquery','jquery-cookie'));
		wp_localize_script( 'store-lead-ajax' , 'inbound_ajax', array( 'admin_url' => admin_url( 'admin-ajax.php' ), 'post_id' => $final_page_id, 'post_type' => $post_type));
		wp_localize_script( 'funnel-tracking' , 'wplft', array( 'post_id' => $final_page_id, 'ip_address' => $ip_address, 'wp_lead_data' => $lead_data_array, 'admin_url' => admin_url( 'admin-ajax.php' )));

		// Load Lead Page View Tracking
		$lead_page_view_tracking = get_option( 'page-view-tracking' , 1);
		if ($lead_page_view_tracking)
		{	
			if($post_id === 0){
				$final_page_id = wp_leads_get_page_final_id();
			} else {
				$final_page_id = $post_id;
			}
			wp_enqueue_script( 'wpl-nonconversion-tracking' , WPL_URL . '/js/wpl.nonconversion-tracking.js', array( 'jquery','jquery-cookie','funnel-tracking'));
			wp_localize_script( 'wpl-nonconversion-tracking' , 'wplnct', array( 'admin_url' => admin_url( 'admin-ajax.php' ), 'final_page_id' => $final_page_id  ));
		}

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
		if ($form_ids)
		{
			wp_enqueue_script('wpl-assign-class', WPL_URL . '/js/wpl.assign-class.js', array( 'jquery'));	
			wp_localize_script( 'wpl-assign-class', 'wpleads', array( 'form_ids' => $form_ids ) );
		}

	}
}

add_action('admin_enqueue_scripts', 'wpleads_admin_enqueuescripts');
function wpleads_admin_enqueuescripts($hook)
{
	global $post;
	
	if (isset($_GET['taxonomy']))
		return;

	if ((isset($_GET['post_type'])&&$_GET['post_type']=='wp-lead')||(isset($post->post_type)&&$post->post_type=='wp-lead'))
	{
		//echo $_GET['post_type'];exit; 
		if ( $hook == 'post.php' ) 
		{
			wp_enqueue_script('wpleads-edit', WPL_URL.'/js/wpl.admin.edit.js', array('jquery'));
			wp_enqueue_script('tinysort', WPL_URL.'/js/jquery.tinysort.js', array('jquery'));
			wp_enqueue_script('tag-cloud', WPL_URL.'/js/jquery.tagcloud.js', array('jquery'));
			wp_localize_script( 'wpleads-edit', 'wp_lead_map', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_lead_map_nonce' => wp_create_nonce('wp-lead-map-nonce') ) );
		}
		
		
		//Tool tip js
		wp_enqueue_script('jquery-qtip', WPL_URL . '/js/jquery-qtip/jquery.qtip.min.js');
		wp_enqueue_script('wpl-load-qtip', WPL_URL . '/js/jquery-qtip/load.qtip.js');
		wp_enqueue_style('qtip-css', WPL_URL . '/css/jquery.qtip.min.css'); //Tool tip css
		wp_enqueue_style('wpleads-admin-css', WPL_URL.'/css/wpl.admin.css');
		
				
		// Leads list management js
		wp_enqueue_script('wpleads-list', WPL_URL . '/js/wpl.leads-list.js');
		wp_enqueue_style('wpleads-list-css', WPL_URL.'/css/wpl.leads-list.css');

	
		
		if ( $hook == 'post-new.php' ) 
		{
			wp_enqueue_script('wpleads-create-new-lead', WPL_URL . '/js/wpl.add-new.js');
		}		
		
	
	}
	
	if ((isset($_GET['post_type'])&&$_GET['post_type']=='list')||(isset($post->post_type)&&$post->post_type=='list'))
	{	
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
	if ( isset($_COOKIE['wp_lead_id']) && !is_admin() && !is_404() && $post_type != "wp-call-to-action") 
	{

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

if (is_admin())
{
	
	/**********************************************************/
	/******************CREATE SETTINGS SUBMENU*****************/
	add_action('admin_menu', 'wpleads_add_menu');	
	function wpleads_add_menu()
	{
		//echo 1; exit;
		if (current_user_can('manage_options'))
		{	
		
			add_submenu_page('edit.php?post_type=wp-lead', 'Settings', 'Settings', 'manage_options', 'wpleads_global_settings','wpleads_display_global_settings');
			
		}
	}
	
	add_action('lp_lead_table_data_is_details_column','wpleads_add_user_edit_button');
	function wpleads_add_user_edit_button($item)
	{
		$image = WPL_URL.'/images/icons/edit_user.png';
		echo '&nbsp;&nbsp;<a href="'.get_admin_url().'post.php?post='.$item['ID'].'&action=edit" target="_blank"><img src="'.$image.'" title="Edit Lead"></a>';
	}
	
	add_action('lp_module_lead_splash_post','wpleads_add_user_conversion_data_to_splash');
	function wpleads_add_user_conversion_data_to_splash($data)
	{
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