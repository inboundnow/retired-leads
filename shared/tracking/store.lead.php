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
	// header('HTTP/1.0 404 Not found'); exit; // simulate ajax fail

	// Grab form values
	$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
	$data['user_ID'] = $user_ID;
	$data['wordpress_date_time'] = date("Y-m-d G:i:s T", $time);
	$data['email'] = $_POST['emailTo'];
	$data['element_type'] =	(isset($_POST['element_type'])) ? $_POST['element_type'] : false;
	$data['wp_lead_uid'] = (isset($_POST['wp_lead_uid'])) ? $_POST['wp_lead_uid'] : false;
	$data['raw_post_values_json'] = (isset($_POST['raw_post_values_json'])) ? $_POST['raw_post_values_json'] : false;
	$data['first_name'] = (isset($_POST['first_name'])) ?  $_POST['first_name'] : false;
	$data['last_name'] = (isset($_POST['last_name'])) ? $_POST['last_name'] : false;
	$data['company_name'] = (isset($_POST['company_name'] )) ? $_POST['company_name'] : false;
	$data['phone'] = (isset($_POST['phone'])) ? $_POST['phone'] : false;
	$data['address'] = (isset($_POST['address'])) ? $_POST['address'] : false;
	$data['ip_address'] = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : false;
	$data['wp_lead_uid'] = (isset($_POST['wp_lead_uid'])) ? $_POST['wp_lead_uid'] : false;
	$data['lp_id'] = (isset($_POST['lp_id'])) ? $_POST['lp_id'] : '0';
	$data['post_type'] = (isset($_POST['post_type'])) ? $_POST['post_type'] : 'na';
	$data['lp_variation'] = (isset($_POST['lp_variation'])) ? $_POST['lp_variation'] : 'default';
	$data['page_views'] = (isset($_POST['page_views'])) ?  $_POST['page_views'] : false;
	$data['page_view_count'] = (isset($_POST['page_view_count'] )) ? $_POST['page_view_count'] : false;

	do_action('inbound_store_lead_pre' , $data); // Global lead storage action hook

	// check for set email
	if ( ( isset( $_POST['emailTo']) && !empty( $_POST['emailTo']) && strstr($_POST['emailTo'],'@') )) {
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
			update_post_meta( $data['lead_id'], 'times', $meta ); // replace times

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

			update_post_meta( $data['lead_id'], 'wp_leads_uid', $data['wp_lead_uid'] );
			update_post_meta( $data['lead_id'], 'page_views', $data['page_views'] ); /* Store Page Views Object */
			update_post_meta( $data['lead_id'], 'wpl-lead-page-view-count', $data['page_view_count']);
			update_post_meta( $data['lead_id'], 'wpleads_landing_page_'.$data['lp_id'], 1 );
			do_action('wpleads_after_conversion_lead_insert',$data['lead_id']);

		}

	/*
	* Run for all leads
	*/

	/* Store IP addresss & Store GEO Data */
	if ($data['ip_address']){
		update_post_meta( $data['lead_id'], 'wpleads_ip_address', $data['ip_address'] );
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
	}

	/* Store Conversion Data */
	$conversion_data = get_post_meta( $data['lead_id'], 'wpleads_conversion_data', TRUE );
	$conversion_data = json_decode($conversion_data,true);
	$variation = ($data['lp_variation'] != 'default') ? $data['lp_variation'] : '0';
	if (is_array($conversion_data)){
		$c_count = count($conversion_data) + 1;
		$conversion_data[$c_count]['id'] = $data['lp_id'];
		$conversion_data[$c_count]['variation'] = $variation;
		$conversion_data[$c_count]['datetime'] = $data['wordpress_date_time'];
	} else {
		$c_count = 1;
		$conversion_data[$c_count]['id'] = $data['lp_id'];
		$conversion_data[$c_count]['variation'] = $variation;
		$conversion_data[$c_count]['datetime'] = $data['wordpress_date_time'];
		$conversion_data[$c_count]['first_time'] = 1;
	}
	$data['conversion_data'] = json_encode($conversion_data);
	update_post_meta($data['lead_id'],'wpl-lead-conversion-count', $c_count); // Store conversions count
	update_post_meta($data['lead_id'], 'wpleads_conversion_data', $data['conversion_data']); // Store conversion object

	/* Store page views for page tracking off */
	$page_tracking_status = get_option('wpl-main-page-view-tracking', 1);
	if($data['page_views'] && $page_tracking_status == 0){

		$page_view_data = get_post_meta( $lead_id, 'page_views', TRUE );
		$page_view_data = json_decode($page_view_data,true);

		// If page_view meta exists do this
		if (is_array($page_view_data)) {
			$new_page_views = inbound_json_array_merge( $page_view_data, $data['page_views']);
			$page_views = json_encode($new_page_views);

		} else {
		// Create page_view meta if it doesn't exist
			$page_views = $data['page_views'];
			$page_views = json_encode($page_views);
		}
		// View count
		$view_count = get_post_meta( $data['lead_id'], 'wpl-lead-page-view-count', TRUE );
		if ($view_count){
			$page_view_count = $data['page_view_count'] + $view_count;
		} else {
			$page_view_count = $data['page_view_count'];
		}
		// update meta
		if ($data['page_view_count']){
		update_post_meta($data['lead_id'],'wpl-lead-page-view-count', $page_view_count);
		}
		update_post_meta($data['lead_id'], 'page_views', $page_views );
	}


	/* Raw Form Values Store */
	if ($data['raw_post_values_json']) {
		$raw_post_data = get_post_meta($data['lead_id'],'wpl-lead-raw-post-data', true);
		$a1 = json_decode( $raw_post_data, true );
		$a2 = json_decode( stripslashes($data['raw_post_values_json']), true );
		$exclude_array = array('card_number','card_cvc','card_exp_month','card_exp_year'); // add filter
		$lead_mapping_fields = get_transient( 'wp-lead-fields' );

		foreach ($a2 as $key=>$value){
			if (array_key_exists( $key , $exclude_array )) {
				unset($a2[$key]);
				continue;
			}
			if (array_key_exists($key, $lead_mapping_fields)) {
				update_post_meta( $data['lead_id'], $key, $value );
			}
			if (stristr($key,'company')) {
				update_post_meta( $data['lead_id'], 'wpleads_company_name', $value );
			} else if (stristr($key,'website')) {
				$websites = get_post_meta( $data['lead_id'], 'wpleads_websites', $value );
				if(is_array($websites)) {
					$array_websites = explode(';',$websites);
				}
				$array_websites[] = $value;
				$websites = implode(';',$array_websites);
				update_post_meta( $data['lead_id'], 'wpleads_websites', $websites );
			}
		}
		// Merge form fields if exist
		if (is_array($a1)) {
			$new_raw_post_data = array_merge_recursive( $a1, $a2 );
		} else {
			$new_raw_post_data = $a2;
		}
		$new_raw_post_data = json_encode( $new_raw_post_data );
		update_post_meta( $data['lead_id'],'wpl-lead-raw-post-data', $new_raw_post_data );
	}

	setcookie('wp_lead_id' , $data['lead_id'], time() + (20 * 365 * 24 * 60 * 60),'/');

	do_action('inbound_store_lead_post', $data );
	do_action('wp_cta_store_lead_post', $data );
	do_action('wpl_store_lead_post', $data );
	do_action('lp_store_lead_post', $data );

	echo $data['lead_id'];
	die();
	}
}
}
if (!function_exists('inbound_json_array_merge')) {
	function inbound_json_array_merge( $arr1, $arr2 ) {
	    $keys = array_keys( $arr2 );
	    foreach( $keys as $key ) {
	        if( isset( $arr1[$key] )
	            && is_array( $arr1[$key] )
	            && is_array( $arr2[$key] )
	        ) {
	            $arr1[$key] = my_merge( $arr1[$key], $arr2[$key] );
	        } else {
	            $arr1[$key] = $arr2[$key];
	        }
	    }
	    return $arr1;
	}
}