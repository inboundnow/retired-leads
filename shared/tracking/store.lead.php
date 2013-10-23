<?php
/**
 * Inbound Lead Storage
 * 
 * - Handles lead creation and storage
 */

if (!function_exists('inbound_store_lead')) {

add_action('wp_ajax_inbound_store_lead', 'inbound_store_lead');
add_action('wp_ajax_nopriv_inbound_store_lead', 'inbound_store_lead');

function inbound_store_lead() 
{
	global $user_ID, $wpdb;
	
	// Grab form values
	$data['element_type'] =	$_POST['element_type'];
	$data['wp_lead_uid'] = $_POST['wp_lead_uid'];
	$data['raw_post_values_json'] = $_POST['raw_post_values_json'];

	$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
	$data['user_ID'] = $user_ID; 
	$data['wordpress_date_time'] = date("Y-m-d G:i:s T", $time); 
	
	
	$data['email'] = $_POST['emailTo'];
	(isset(	$_POST['first_name'] )) ? $data['first_name'] = $_POST['first_name'] : $data['first_name'] = "";
	(isset(	$_POST['last_name'] )) ? $data['last_name'] = $_POST['last_name'] : $data['last_name'] = "";		
	(isset(	$_POST['company_name'] )) ? $data['company_name'] = $_POST['company_name'] : $data['company_name'] = "";
	(isset(	$_POST['phone'] )) ? $data['phone'] = $_POST['phone'] : $data['phone'] = "";
	(isset(	$_POST['address'] )) ? $data['address'] = $_POST['address'] : $data['address'] = "";
	
	(isset(	$_SERVER['REMOTE_ADDR'] )) ? $data['ip_address'] = $_SERVER['REMOTE_ADDR'] :$data['ip_address'] = "undefined";
	(isset(	$_POST['wp_lead_uid'] )) ? $data['wp_lead_uid'] = $_POST['wp_lead_uid'] : $data['wp_lead_uid'] = "null";
	(isset(	$_POST['lp_id'] )) ? $data['lp_id'] = $_POST['lp_id'] : $data['lp_id'] = 0;
	(isset(	$_POST['post_type'] )) ? $data['post_type'] = $_POST['post_type'] : $data['post_type'] = 'na';
	(isset(	$_POST['lp_variation'] )) ? $data['lp_variation'] = $_POST['lp_variation'] : $data['lp_variation'] = 0;
	(isset(	$_POST['page_views'] )) ? $data['page_views'] = $_POST['page_views'] : $data['page_views'] = false;
	(isset(	$_POST['page_view_count'] )) ? $data['page_view_count'] = $_POST['page_view_count'] : $data['page_view_count'] = 0;

	do_action('inbound_store_lead_pre' , $data); // Global lead storage action hook
	
	// header('HTTP/1.0 404 Not found'); exit; // simulate fail
	// check for set email
	if ( ( isset( $_POST['emailTo']) && !empty( $_POST['emailTo']) && strstr($_POST['emailTo'],'@') ))
	{
		$query = $wpdb->prepare(
			'SELECT ID FROM ' . $wpdb->posts . '
			WHERE post_title = %s
			AND post_type = \'wp-lead\'',
			$_POST['emailTo']
		);
		$wpdb->query( $query );

		// Add lookup fallbacks

		if ( $wpdb->num_rows ) {
		/* Update Existing Lead */
			$data['lead_id'] = $wpdb->get_var( $query );
			$meta = get_post_meta( $data['lead_id'], 'times', TRUE ); // replace times			
			$meta++;
			
			if ($data['lp_id'])
			{
				$conversion_data = get_post_meta( $data['lead_id'], 'wpleads_conversion_data', TRUE );
				$conversion_data = json_decode($conversion_data,true);
				$conversion_data[$meta]['id'] = $data['lp_id'];
				$conversion_data[$meta]['variation'] = $data['lp_variation'];
				$conversion_data[$meta]['datetime'] = $data['wordpress_date_time'];
				$data['conversion_data'] = json_encode($conversion_data);
			}
			
			update_post_meta( $data['lead_id'], 'times', $meta ); // replace times
			update_post_meta( $data['lead_id'], 'wpleads_email_address', $data['email'] );
			
			if (!empty($data['user_ID']))
				update_post_meta( $data['lead_id'], 'wpleads_wordpress_user_id', $data['user_ID'] );				
			if (!empty($data['first_name']))
				update_post_meta( $data['lead_id'], 'wpleads_first_name', $data['first_name'] );
			if (!empty($data['last_name']))
				update_post_meta( $data['lead_id'], 'wpleads_last_name', $data['last_name'] );
			if (!empty($data['phone']))
				update_post_meta( $data['lead_id'], 'wpleads_work_phone', $data['phone'] );
			if (!empty($data['company_name']))
				update_post_meta( $data['lead_id'], 'wpleads_company_name', $data['company'] );
			if (!empty($data['address']))
				update_post_meta( $data['lead_id'], 'wpleads_address_line_1', $data['address'] );
			if (!empty($data['wp_lead_uid']))
				update_post_meta( $data['lead_id'], 'wp_leads_uid', $data['wp_lead_uid'] );
				
			update_post_meta( $data['lead_id'], 'wpleads_ip_address', $data['ip_address'] );
			update_post_meta( $data['lead_id'], 'wpleads_conversion_data', $data['conversion_data'] );
			update_post_meta( $data['lead_id'], 'wpleads_landing_page_'.$data['lp_id'], 1 );
			
			do_action('wpleads_after_conversion_lead_update',$data['lead_id']);
		
		} else { 
		/* Create New Lead */
			$post = array(
				'post_title'		=> $data['email'], 
				 //'post_content'		=> $json,
				'post_status'		=> 'publish',
				'post_type'		=> 'wp-lead',
				'post_author'		=> 1
			);
			
			//$post = add_filter('lp_leads_post_vars',$post);
			
			if ($data['lp_id'])
			{			
				$conversion_data[1]['id'] = $data['lp_id'];
				$conversion_data[1]['variation'] = $data['lp_variation'];
				$conversion_data[1]['datetime'] = $data['wordpress_date_time'];
				$conversion_data[1]['first_time'] = 1;					
				$data['conversion_data'] = json_encode($conversion_data);
			}
			

			$data['lead_id'] = wp_insert_post($post);
			update_post_meta( $data['lead_id'], 'times', 1 );
			update_post_meta( $data['lead_id'], 'wpleads_wordpress_user_id', $user_ID );
			update_post_meta( $data['lead_id'], 'wpleads_email_address', $data['email'] );
			
			if (!empty($data['first_name']))
				update_post_meta( $data['lead_id'], 'wpleads_first_name', $data['first_name'] );
			if (!empty($data['last_name']))
				update_post_meta( $data['lead_id'], 'wpleads_last_name', $data['last_name'] );
			if (!empty($data['phone']))
				update_post_meta( $data['lead_id'], 'wpleads_work_phone', $data['phone'] );
			if (!empty($data['company_name']))
				update_post_meta( $data['lead_id'], 'wpleads_company_name', $data['company_name'] );
			if (!empty($data['address']))
				update_post_meta( $data['lead_id'], 'wpleads_address_line_1', $data['address'] );
				
			update_post_meta( $data['lead_id'], 'wpleads_ip_address', $data['ip_address'] );
			update_post_meta( $data['lead_id'], 'wp_leads_uid', $data['wp_lead_uid'] );
			update_post_meta( $data['lead_id'], 'page_views', $data['page_views'] );
			//update_post_meta( $data['lead_id'], 'wpl-lead-page-view-count', $page_view_count ); // enable
			//update_post_meta( $data['lead_id'], 'wpl-lead-conversion-count', 1 ); // enable
			update_post_meta( $data['lead_id'], 'wpleads_conversion_data', $data['conversion_data'] );
			
			update_post_meta( $data['lead_id'], 'wpleads_landing_page_'.$data['lp_id'], 1 );
			
			$geo_array = unserialize(lp_remote_connect('http://www.geoplugin.net/php.gp?ip='.$data['ip_address']));
			
			
			(isset($geo_array['geoplugin_areaCode'])) ? update_post_meta( $data['lead_id'], 'wpleads_areaCode', $geo_array['geoplugin_areaCode'] ) : null;
			(isset($geo_array['geoplugin_city'])) ? update_post_meta( $data['lead_id'], 'wpleads_city', $geo_array['geoplugin_city'] ) : null;
			(isset($geo_array['geoplugin_regionName'])) ? update_post_meta( $data['lead_id'], 'wpleads_region_name', $geo_array['geoplugin_regionName'] ) : null;
			(isset($geo_array['geoplugin_regionCode'])) ? update_post_meta( $data['lead_id'], 'wpleads_region_code', $geo_array['geoplugin_regionCode'] ) : null;
			(isset($geo_array['geoplugin_countryName'])) ? update_post_meta( $data['lead_id'], 'wpleads_country_name', $geo_array['geoplugin_countryName'] ) : null;
			(isset($geo_array['geoplugin_countryCode'])) ? update_post_meta( $data['lead_id'], 'wpleads_country_code', $geo_array['geoplugin_countryCode'] ) : null;
			(isset($geo_array['geoplugin_latitude'])) ? update_post_meta( $data['lead_id'], 'wpleads_latitude', $geo_array['geoplugin_latitude'] ) : null;
			(isset($geo_array['geoplugin_longitude'])) ? update_post_meta( $data['lead_id'], 'wpleads_longitude', $geo_array['geoplugin_longitude'] ) : null;
			(isset($geo_array['geoplugin_currencyCode'])) ? update_post_meta( $data['lead_id'], 'wpleads_currency_code', $geo_array['geoplugin_currencyCode'] ) : null;
			(isset($geo_array['geoplugin_currencySymbol_UTF8'])) ? update_post_meta( $data['lead_id'], 'wpleads_currency_symbol', $geo_array['geoplugin_currencySymbol_UTF8'] ) : null;
			
			do_action('wpleads_after_conversion_lead_insert',$data['lead_id']);
		
		}
		
		setcookie('wp_lead_id' , $data['lead_id'], time() + (20 * 365 * 24 * 60 * 60),'/');
		
		do_action('inbound_store_lead_post', $data );
		do_action('wp_cta_store_lead_post', $data );
		do_action('wpl_store_lead_post', $data );
		do_action('lp_store_lead_post', $data ); 
		
		echo $data['lead_id'];
		die();
	}
	else
	{
		
	}
}
}