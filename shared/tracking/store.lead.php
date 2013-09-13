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
	// header('HTTP/1.0 404 Not found'); exit; // simulate fail
	// check for set email
	if (isset( $_POST['emailTo'])&&!empty( $_POST['emailTo'])&&strstr($_POST['emailTo'],'@'))
	{
		// Grab form values
		$title = $_POST['emailTo'];
		$content =	$_POST['first_name'];
		$wp_lead_uid = $_POST['wp_lead_uid'];
		$raw_post_values_json = $_POST['raw_post_values_json'];
	
		$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
		$wordpress_date_time = date("Y-m-d G:i:s T", $time); 
		
		(isset(	$_POST['first_name'] )) ? $first_name = $_POST['first_name'] : $first_name = "";
		(isset(	$_POST['last_name'] )) ? $last_name = $_POST['last_name'] : $last_name = "";
		(isset(	$_SERVER['REMOTE_ADDR'] )) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = "undefined";
		(isset(	$_POST['wp_lead_uid'] )) ? $wp_lead_uid = $_POST['wp_lead_uid'] : $wp_lead_uid = "null";
		(isset(	$_POST['lp_id'] )) ? $lp_id = $_POST['lp_id'] : $lp_id = 0;
		(isset(	$_POST['post_type'] )) ? $post_type = $_POST['post_type'] : $post_type = 'na';
		(isset(	$_POST['lp_v'] )) ? $lp_variation = $_POST['lp_v'] : $lp_variation = 0;
		(isset(	$_POST['page_views'] )) ? $page_views = $_POST['page_views'] : $page_views = false;
		(isset(	$_POST['page_view_count'] )) ? $page_view_count = $_POST['page_view_count'] : $page_view_count = 0;
		
		// Update Landing Page Conversions
		if($post_type === 'landing-page'){
			
			$disable_admin_tracking = get_option( 'main-landing-page-disable-admin-tracking', '0' );
			
			if ( !$disable_admin_tracking || !current_user_can( 'manage_options' ) )
			{				
				$lp_conversions = get_post_meta( $lp_id, 'lp-ab-variation-conversions-'.$lp_variation, true );
				$lp_conversions++;
				update_post_meta( $lp_id, 'lp-ab-variation-conversions-'.$lp_variation, $lp_conversions );
			}
		}

		// Update Call to Action Conversions
		if($post_type === 'wp-call-to-action'){
			
			//$disable_admin_tracking = get_option( 'main-landing-page-disable-admin-tracking', '0' );
			
			//if ( !$disable_admin_tracking || !current_user_can( 'manage_options' ) )
			//{				
				$cta_conversions = get_post_meta( $lp_id, 'wp-cta-ab-variation-conversions-'.$lp_variation, true );
				$cta_conversions++;
				update_post_meta( $lp_id, 'wp-cta-ab-variation-conversions-'.$lp_variation, $cta_conversions );
			//}
		}
		
		//do_action('inbound_store_lead_pre'); // Global lead storage action hook
		//do_action('lp_store_lead_pre'); // Landing Page specific storage hook (remove)
		do_action('wpl_store_lead_pre'); // Leads specific storage hook (remove)
		
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
			$post_ID = $wpdb->get_var( $query );
			$meta = get_post_meta( $post_ID, 'times', TRUE ); // replace times			
			$meta++;
			
			if ($lp_id)
			{
				$conversion_data = get_post_meta( $post_ID, 'wpleads_conversion_data', TRUE );
				$conversion_data = json_decode($conversion_data,true);
				$conversion_data[$meta]['id'] = $lp_id;
				$conversion_data[$meta]['variation'] = $lp_variation;
				$conversion_data[$meta]['datetime'] = $wordpress_date_time;
				$conversion_data = json_encode($conversion_data);
			}
			
			update_post_meta( $post_ID, 'times', $meta ); // replace times
			update_post_meta( $post_ID, 'wpleads_email_address', $title );
			
			if (!empty($user_ID))
				update_post_meta( $post_ID, 'wpleads_wordpress_user_id', $user_ID );				
			if (!empty($first_name))
				update_post_meta( $post_ID, 'wpleads_first_name', $first_name );
			if (!empty($last_name))
				update_post_meta( $post_ID, 'wpleads_last_name', $last_name );
			if (!empty($wp_lead_uid))
				update_post_meta( $post_ID, 'wp_leads_uid', $wp_lead_uid );
				
			update_post_meta( $post_ID, 'wpleads_ip_address', $ip_address );
			update_post_meta( $post_ID, 'wpleads_conversion_data', $conversion_data );
			update_post_meta( $post_ID, 'wpleads_landing_page_'.$lp_id, 1 );
			
			do_action('wpleads_after_conversion_lead_update',$post_ID);
		
		} else { 
		/* Create New Lead */
			$post = array(
				'post_title'		=> $title, 
				 //'post_content'		=> $json,
				'post_status'		=> 'publish',
				'post_type'		=> 'wp-lead',
				'post_author'		=> 1
			);
			
			//$post = add_filter('lp_leads_post_vars',$post);
			
			if ($lp_id)
			{			
				$conversion_data[1]['id'] = $lp_id;
				$conversion_data[1]['variation'] = $lp_variation;
				$conversion_data[1]['datetime'] = $wordpress_date_time;
				$conversion_data[1]['first_time'] = 1;					
				$conversion_data = json_encode($conversion_data);
			}
			

			$post_ID = wp_insert_post($post);
			update_post_meta( $post_ID, 'times', 1 );
			update_post_meta( $post_ID, 'wpleads_wordpress_user_id', $user_ID );
			update_post_meta( $post_ID, 'wpleads_email_address', $title );
			update_post_meta( $post_ID, 'wpleads_first_name', $first_name);
			update_post_meta( $post_ID, 'wpleads_last_name', $last_name);
			update_post_meta( $post_ID, 'wpleads_ip_address', $ip_address );
			update_post_meta( $post_ID, 'wp_leads_uid', $wp_lead_uid );
			update_post_meta( $post_ID, 'page_views', $page_views );
			//update_post_meta( $post_ID, 'wpl-lead-page-view-count', $page_view_count ); // enable
			//update_post_meta( $post_ID, 'wpl-lead-conversion-count', 1 ); // enable
			update_post_meta( $post_ID, 'wpleads_conversion_data', $conversion_data );
			
			update_post_meta( $post_ID, 'wpleads_landing_page_'.$lp_id, 1 );
			
			$geo_array = unserialize(lp_remote_connect('http://www.geoplugin.net/php.gp?ip='.$ip_address));
			
			
			(isset($geo_array['geoplugin_areaCode'])) ? update_post_meta( $post_ID, 'wpleads_areaCode', $geo_array['geoplugin_areaCode'] ) : null;
			(isset($geo_array['geoplugin_city'])) ? update_post_meta( $post_ID, 'wpleads_city', $geo_array['geoplugin_city'] ) : null;
			(isset($geo_array['geoplugin_regionName'])) ? update_post_meta( $post_ID, 'wpleads_region_name', $geo_array['geoplugin_regionName'] ) : null;
			(isset($geo_array['geoplugin_regionCode'])) ? update_post_meta( $post_ID, 'wpleads_region_code', $geo_array['geoplugin_regionCode'] ) : null;
			(isset($geo_array['geoplugin_countryName'])) ? update_post_meta( $post_ID, 'wpleads_country_name', $geo_array['geoplugin_countryName'] ) : null;
			(isset($geo_array['geoplugin_countryCode'])) ? update_post_meta( $post_ID, 'wpleads_country_code', $geo_array['geoplugin_countryCode'] ) : null;
			(isset($geo_array['geoplugin_latitude'])) ? update_post_meta( $post_ID, 'wpleads_latitude', $geo_array['geoplugin_latitude'] ) : null;
			(isset($geo_array['geoplugin_longitude'])) ? update_post_meta( $post_ID, 'wpleads_longitude', $geo_array['geoplugin_longitude'] ) : null;
			(isset($geo_array['geoplugin_currencyCode'])) ? update_post_meta( $post_ID, 'wpleads_currency_code', $geo_array['geoplugin_currencyCode'] ) : null;
			(isset($geo_array['geoplugin_currencySymbol_UTF8'])) ? update_post_meta( $post_ID, 'wpleads_currency_symbol', $geo_array['geoplugin_currencySymbol_UTF8'] ) : null;
			
			do_action('wpleads_after_conversion_lead_insert',$post_ID);
		
		}
		setcookie('wp_lead_id' , $post_ID, time() + (20 * 365 * 24 * 60 * 60),'/');
	
		$data['lp_id'] = $lp_id;
		$data['lead_id'] = $post_ID;
		$data['first_name'] = $first_name;
		$data['last_name'] = $last_name;
		$data['email'] = $title;
		$data['wp_lead_uid'] = $wp_lead_uid;
		$data['raw_post_values_json'] = $raw_post_values_json;
		
		do_action('wpl_store_lead_post', $data );
		
		echo $post_ID;
		die();
	}
}
}