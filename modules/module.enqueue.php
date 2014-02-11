<?php

/* enqueue frontend scripts */

add_action('wp_enqueue_scripts', 'wpleads_enqueuescripts_header');
function wpleads_enqueuescripts_header()
{
	global $post;
	
	$post_type = isset($post) ? get_post_type( $post ) : null;
	
	$current_page = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$post_id = wpl_url_to_postid($current_page);

	(isset($_SERVER['HTTP_REFERER'])) ? $referrer = $_SERVER['HTTP_REFERER'] : $referrer ='direct access';
	(isset($_SERVER['REMOTE_ADDR'])) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = '0.0.0.0.0';
    $lead_cpt_id = (isset($_COOKIE['wp_lead_id'])) ? $_COOKIE['wp_lead_id'] : false;
    $lead_email = (isset($_COOKIE['wp_lead_email'])) ? $_COOKIE['wp_lead_email'] : false;
    $lead_unique_key = (isset($_COOKIE['wp_lead_uid'])) ? $_COOKIE['wp_lead_uid'] : false;

	// Localize lead data
	$lead_data_array = array();
	if ($lead_cpt_id){
		$lead_data_array['lead_id'] = $lead_cpt_id;
		$type = 'wplid';
	}
	if ($lead_email) {
		$lead_data_array['lead_email'] = $lead_email;
		$type = 'wplemail';
	}
	if ($lead_unique_key) {
		$lead_data_array['lead_uid'] = $lead_unique_key;
		$type = 'wpluid';
	}

	// Load Tracking Scripts
	if($post_type != "wp-call-to-action") 
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-cookie', WPL_URL . '/js/jquery.cookie.js', array( 'jquery' ));
		wp_register_script('jquery-total-storage',WPL_URL . '/js/jquery.total-storage.min.js', array( 'jquery' ));
		wp_enqueue_script('jquery-total-storage');

		if($post_id === 0){
			$final_page_id = wp_leads_get_page_final_id();
		} else {
			$final_page_id = $post_id;
		}

		wp_enqueue_script( 'funnel-tracking' , WPL_URL . '/shared/tracking/page-tracking.js', array( 'jquery','jquery-cookie'));
		//wp_enqueue_script( 'selectron-js' , WPL_URL . '/shared/js/selectron.js', array( 'jquery','jquery-cookie')); // coming soon for field mapping
		wp_enqueue_script( 'store-lead-ajax' , WPL_URL . '/shared/tracking/js/store.lead.ajax.js', array( 'jquery','jquery-cookie'));
		wp_localize_script( 'store-lead-ajax' , 'inbound_ajax', array( 'admin_url' => admin_url( 'admin-ajax.php' ), 'post_id' => $final_page_id, 'post_type' => $post_type));
		$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
		$wordpress_date_time = date("Y-m-d G:i:s T", $time);
		wp_localize_script( 'funnel-tracking' , 'wplft', array( 'post_id' => $final_page_id, 'ip_address' => $ip_address, 'wp_lead_data' => $lead_data_array, 'admin_url' => admin_url( 'admin-ajax.php' ), 'track_time' => $wordpress_date_time));

		// Load Lead Page View Tracking
		$lead_page_view_tracking = get_option( 'page-view-tracking' , 1);
		if ($lead_page_view_tracking)
		{
			if( $post_id === 0 ){
				$final_page_id = wp_leads_get_page_final_id();
			} else {
				$final_page_id = $post_id;
			}
			wp_enqueue_script( 'wpl-behavorial-tracking' , WPL_URL . '/js/wpl.behavorial-tracking.js', array( 'jquery','jquery-cookie','funnel-tracking'));
			wp_localize_script( 'wpl-behavorial-tracking' , 'wplnct', array( 'admin_url' => admin_url( 'admin-ajax.php' ), 'final_page_id' => $final_page_id  ));
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

/* enqueue admin scripts */
add_action('admin_enqueue_scripts', 'wpleads_admin_enqueuescripts');
function wpleads_admin_enqueuescripts($hook)
{
	global $post;
	
	$post_type = isset($post) ? get_post_type( $post ) : null;
	
	if (isset($_GET['taxonomy'])){
		return;
	}
	
	wp_enqueue_style('wpleads-global-backend-css', WPL_URL.'/css/wpl.global-backend.css');
	
	if ((isset($_GET['post_type'])&&$_GET['post_type']=='wp-lead')||(isset($post->post_type)&&$post->post_type=='wp-lead'))
	{
		//echo $_GET['post_type'];exit;
		if ( $hook == 'post.php' )
		{
			wp_enqueue_script('wpleads-edit', WPL_URL.'/js/wpl.admin.edit.js', array('jquery'));
			wp_enqueue_script('tinysort', WPL_URL.'/js/jquery.tinysort.js', array('jquery'));
			wp_enqueue_script('tag-cloud', WPL_URL.'/js/jquery.tagcloud.js', array('jquery'));
			wp_localize_script( 'wpleads-edit', 'wp_lead_map', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_lead_map_nonce' => wp_create_nonce('wp-lead-map-nonce') ) );
			wp_enqueue_script('jquery-cookie', WPL_URL . '/shared/js/jquery.cookie.js', array( 'jquery' ));
			
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


		if ( $hook == 'post-new.php' )
		{
			wp_enqueue_script('wpleads-create-new-lead', WPL_URL . '/js/wpl.add-new.js');
		}

		if ( $hook == 'post.php' )
		{
			if (isset($_GET['small_lead_preview'])) {
				wp_enqueue_style('wpleads-popup-css', WPL_URL.'/css/wpl.popup.css');
			}
			wp_enqueue_style('wpleads-admin-edit-css', WPL_URL.'/css/wpl.edit-lead.css');
		}


	}

	if ((isset($_GET['post_type'])&&$_GET['post_type']=='list')||(isset($post->post_type)&&$post->post_type=='list'))
	{
		wp_enqueue_style('wpleads-list-css', WPL_URL.'/css/wpl.leads-list.css');
		wp_enqueue_script('lls-edit-list-cpt', WPL_URL . '/js/wpl.admin.cpt.list.js');
	}
	
	/* do enqueues for global settings */
	if (isset($_GET['page'])&&($_GET['page']=='lp_global_settings'&&$_GET['page']=='lp_global_settings'))
	{
		wp_enqueue_style('wpl_manage_lead_css', WPL_URL . '/css/wpl.admin-global-settings.css');
	}
	
	/* do enqueue for post type rule */
	if ((isset($post)&&$post->post_type=='rule')||(isset($_REQUEST['post_type'])&&$_REQUEST['post_type']=='rule'))
	{
		wp_enqueue_script('jquery-qtip', WPL_URL . '/js/jquery-qtip/jquery.qtip.min.js');
		wp_enqueue_script('rules-load-qtip', WPL_URL . '/js/jquery-qtip/load.qtip.js');
		
		if (isset($post))
		{
			wp_enqueue_script('rules-rules-js', WPL_URL . '/js/admin.rules-management.js');
			wp_localize_script( 'rules-rules-js' , 'rules_rule', array( 'rule_id' => $post->ID , 'admin_url' => admin_url('admin-ajax.php')));
		
			wp_enqueue_style('rules-rules-management-css', WPL_URL.'/css/admin.rules-management.css');
		}
	}

}