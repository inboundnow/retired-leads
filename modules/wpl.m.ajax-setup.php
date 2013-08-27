<?php

add_action('wp_ajax_wpl_track_user', 'wpl_track_user_callback');
add_action('wp_ajax_nopriv_wpl_track_user', 'wpl_track_user_callback');
// Tracks known leads
function wpl_track_user_callback() 
{
	global $wpdb;
	//echo "here";exit;
	(isset(	$_POST['wp_lead_id'] )) ? $lead_id = $_POST['wp_lead_id'] : $lead_id = '';
	(isset(	$_POST['nature'] )) ? $nature = $_POST['nature'] : $nature = 'non-conversion'; // what is nature?
	(isset(	$_POST['json'] )) ? $json = addslashes($_POST['json']) : $json = 0;
	(isset(	$_POST['wp_lead_uid'] )) ? $wp_lead_uid = $_POST['wp_lead_uid'] : $wp_lead_uid = 0;
	(isset(	$_POST['page_id'] )) ? $page_id = $_POST['page_id'] : $page_id = 0;
	(isset(	$_POST['current_url'] )) ? $current_url = $_POST['current_url'] : $current_url = 'notfound';
	
	if(isset($_POST['wp_lead_id'])) {
		wp_leads_update_page_view_obj($lead_id, $page_id, $current_url);
	}
 
	/* Old non logged in tracking. This updates tracking table. We might need the table in future. */

	$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
	$wordpress_date_time = date("Y-m-d G:i:s", $time);
	
	$query = "SELECT * FROM ".$wpdb->prefix."lead_tracking WHERE DATE(date) = DATE('{$wordpress_date_time}') AND tracking_id='{$wp_lead_uid}' AND nature='non-conversion'";
	$result = mysql_query($query);				
	if (!$result){ echo $query; echo mysql_error(); exit; }
	
	if (mysql_num_rows($result)>0)
	{
		//echo "here";
		$row = mysql_fetch_array($result);
		$row_id = $row['id'];
		
		$query = "UPDATE ".$wpdb->prefix."lead_tracking SET date='{$wordpress_date_time}' , data = '{$json}' , nature = 'non-conversion' WHERE id='{$row_id}' AND DATE(date) = DATE('{$wordpress_date_time}') ORDER BY `id` DESC LIMIT 1";
		
		$result = mysql_query($query);				
		if (!$result){ echo $query; echo mysql_error(); exit; }
	}
	else
	{
		//echo "there";
		$query = 'INSERT INTO '.$wpdb->prefix.'lead_tracking
				(lead_id,tracking_id,date,data,nature) VALUES
				("'.$lead_id.'" , "'.$wp_lead_uid.'" , "'.$wordpress_date_time.'" , "'.$json.'" , "non-conversion")';
		
		$result = mysql_query($query);				
		if (!$result){ echo $query; echo mysql_error(); exit; }
		
		$row_id = mysql_insert_id();
	}
	
	echo $row_id;
	die();

}
/**
 * wp_leads_update_page_view_obj updates page_views meta for known leads
 * @param  string $lead_id     [description]
 * @param  string $page_id     [description]
 */
function wp_leads_update_page_view_obj($lead_id, $page_id, $current_url) {

		if($page_id === "" || empty($page_id)){
			return;
		}
		$current_page_view_count = get_post_meta($lead_id,'wpl-lead-page-view-count', true);
		
		$increment_page_views = $current_page_view_count + 1; 
		
		update_post_meta($lead_id,'wpl-lead-page-view-count', $increment_page_views); // update count
		
		$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
		$wordpress_date_time = date("Y-m-d G:i:s T", $time); 

		$page_view_data = get_post_meta( $lead_id, 'page_views', TRUE );		
		
		//echo $page_id; // for debug
		
		// If page_view meta exists do this	
		if ($page_view_data) {
			$current_count = 0; // default
			$timeout = 30;  // 30 Timeout analytics tracking for same page timestamps
			$page_view_data = json_decode($page_view_data,true);
				
				// increment view count on page
				if(isset($page_view_data[$page_id])){
				$current_count = count($page_view_data[$page_id]);
				$last_view = $page_view_data[$page_id][$current_count];
				$timeout = abs(strtotime($last_view) - strtotime($wordpress_date_time));
				}

			// If page hasn't been viewed in past 30 seconds. Log it
			if ($timeout >= 30) {	
			$page_view_data[$page_id][$current_count + 1] = $wordpress_date_time;
			$page_view_data = json_encode($page_view_data);
			update_post_meta( $lead_id, 'page_views', $page_view_data );
			}

		} else {
		// Create page_view meta if it doesn't exist
			$page_view_data = array();	
			$page_view_data[$page_id][0] = $wordpress_date_time;			
			$page_view_data = json_encode($page_view_data);
			update_post_meta( $lead_id, 'page_views', $page_view_data );
		}
}

add_action('wp_ajax_wpl_check_lists', 'wpl_check_lists_callback');
add_action('wp_ajax_nopriv_wpl_check_lists', 'wpl_check_lists_callback');

function wpl_check_lists_callback() 
{
$wp_lead_id = $_POST['wp_lead_id'];
if (isset( $_POST['wp_lead_id'])&&!empty( $_POST['wp_lead_id']))
	{
		wp_leads_get_current_lists($wp_lead_id);
	}
}
/**
 * Sets cookie with current lists the lead is a part of
 */
function wp_leads_get_current_lists($lead_id){
		// Set List Cookies if lead is in lists.
		$terms = get_the_terms( $lead_id, 'wplead_list_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {

			$lead_list = array();
			$count = 0;
			foreach ( $terms as $term ) {
				$lead_list[] = $term->term_id;
				$count++;
			}

			//$test = serialize($lead_list);
			$list_array = json_encode(array( 'ids' => $lead_list )); ;

			setcookie('wp_lead_list' , $list_array, time() + (20 * 365 * 24 * 60 * 60),'/');
			//setcookie('check_lead_list' , true, time() + ( 24 * 60 * 60),'/');
		}
}
function wp_leads_get_meta_data($lead_id){
	// function for grabbing any metadata from the backend and displaying to visitor.
	// meta values etc.
}


add_action('wp_ajax_wpl_store_lead', 'wpl_store_lead_callback');
add_action('wp_ajax_nopriv_wpl_store_lead', 'wpl_store_lead_callback');
/* Not in use. Shared Lead storage in use. inbound_store_lead function */
function wpl_store_lead_callback() 
{
	// Grab form values
	$title = $_POST['emailTo'];
	$content =	$_POST['first_name'];
	$wp_lead_uid = $_POST['wp_lead_uid'];
	$raw_post_values_json = $_POST['raw_post_values_json'];
	
	if (isset( $_POST['emailTo'])&&!empty( $_POST['emailTo'])&&strstr($_POST['emailTo'],'@'))
	{
		//echo 'here';
		global $user_ID, $wpdb;
		$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
		$wordpress_date_time = date("Y-m-d G:i:s", $time); 
		
		(isset(	$_POST['first_name'] )) ? $first_name = $_POST['first_name'] : $first_name = "";
		(isset(	$_POST['last_name'] )) ? $last_name = $_POST['last_name'] : $last_name = "";
		(isset(	$_SERVER['REMOTE_ADDR'] )) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = "undefined";
		(isset(	$_POST['wp_lead_uid'] )) ? $wp_lead_uid = $_POST['wp_lead_uid'] : $wp_lead_uid = "null";
		(isset(	$_POST['lp_id'] )) ? $lp_id = $_POST['lp_id'] : $lp_id = 0;
		(isset(	$_POST['page_views'] )) ? $page_views = $_POST['page_views'] : $page_views = false;
		
		do_action('wpl_store_lead_pre');
		
		$query = $wpdb->prepare(
			'SELECT ID FROM ' . $wpdb->posts . '
			WHERE post_title = %s
			AND post_type = \'wp-lead\'',
			$_POST['emailTo']
		);
		$wpdb->query( $query );

		if ( $wpdb->num_rows ) {
			// If lead exists add data/append data to it
			$post_ID = $wpdb->get_var( $query );
			//echo "here";
			//echo $post_ID;
			$meta = get_post_meta( $post_ID, 'times', TRUE );			
			$meta++;
			
			update_post_meta( $post_ID, 'times', $meta );
			update_post_meta( $post_ID, 'wpleads_email_address', $title );
			
			if (!empty($user_ID))
				update_post_meta( $post_ID, 'wpleads_wordpress_user_id', $user_ID );				
			if (!empty($first_name))
				update_post_meta( $post_ID, 'wpleads_first_name', $first_name );
			if (!empty($last_name))
				update_post_meta( $post_ID, 'wpleads_last_name', $last_name );
			if (!empty($wp_lead_id))
				update_post_meta( $post_ID, 'wpleads_uid', $wp_lead_uid );
				
			update_post_meta( $post_ID, 'wpleads_ip_address', $ip_address );
			//update_post_meta( $post_ID, 'page_view_temp', $page_views );
			update_post_meta( $post_ID, 'wpleads_landing_page_'.$lp_id, 1 );
			
			do_action('wpleads_after_conversion_lead_update',$post_ID);
		
		} else { 
			// If lead doesn't exist create it
			$post = array(
				'post_title'		=> $title, 
				 //'post_content'		=> $json,
				'post_status'		=> 'publish',
				'post_type'		=> 'wp-lead',
				'post_author'		=> 1
			);
			
			//$post = add_filter('wpl_leads_post_vars',$post);
			
			$post_ID = wp_insert_post($post);
			update_post_meta( $post_ID, 'times', 1 );
			update_post_meta( $post_ID, 'wpleads_wordpress_user_id', $user_ID );
			update_post_meta( $post_ID, 'wpleads_email_address', $title );
			update_post_meta( $post_ID, 'wpleads_first_name', $first_name);
			update_post_meta( $post_ID, 'wpleads_last_name', $last_name);
			update_post_meta( $post_ID, 'wpleads_ip_address', $ip_address );
			update_post_meta( $post_ID, 'wpleads_uid', $wp_lead_uid );
			update_post_meta( $post_ID, 'wpleads_landing_page_'.$lp_id, 1 );
			update_post_meta( $post_ID, 'page_views', $page_views );
			$geo_array = unserialize(wpl_remote_connect('http://www.geoplugin.net/php.gp?ip='.$ip_address));
			
			
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
			
			// hook
			do_action('wpleads_after_conversion_lead_insert',$post_ID);
		
		}


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


//function to store additonal lead conversion data - plugins into Wordpress Leads standalone and Landing Pages plugin.
function wpleads_hook_store_lead_post($data)
{
	//print_r($data);
	if ($data['lead_id'])
	{
		global $wpdb;
		
		$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
		$wordpress_date_time = date("Y-m-d G:i:s", $time); 

		 //(isset(	$_POST['nature'] )) ? $nature = $_POST['nature'] : $nature = 1;
		(isset(	$_POST['json'] )) ? $json = $_POST['json'] : $json = 0;
		(isset(	$_POST['page_view_count'] )) ? $view_count = $_POST['page_view_count'] : $view_count = 0;
		
		$json = stripslashes($json);
		$json = json_decode($json, true);
		
		$json[0]['converted_page'] = array( 'page_id'=> $_POST['lp_id'] , 'datetime' => $wordpress_date_time );
		//print_r($json);
		//print_r($data);
		
		$json = json_encode($json);
		$json = addslashes($json);
		
		//$query = 'INSERT INTO '.$wpdb->prefix.'lead_tracking
		//		(lead_id,tracking_id,date,data,nature) VALUES
		//		("'.$data['lead_id'].'" , "'.$data['wp_lead_uid'].'" , "'.$wordpress_date_time.'" , "'.$json.'" , "conversion")';
		
		$query = "SELECT * FROM ".$wpdb->prefix."lead_tracking WHERE tracking_id='".$data['wp_lead_uid']."' ORDER BY id DESC LIMIT 1";
		$result = mysql_query($query);				
		if (!$result){ echo $query; echo mysql_error(); exit; }
		
		if (mysql_num_rows($result)>0)
		{
			//echo "here";
			$row = mysql_fetch_array($result);
			$row_id = $row['id'];
			
			$query = "UPDATE ".$wpdb->prefix."lead_tracking SET date='{$wordpress_date_time}' , data = '{$json}' , lead_id= '{$data['lead_id']}' ,  nature = 'conversion' WHERE id='{$row_id}' ";
			
			$result = mysql_query($query);				
			if (!$result){ echo $query; echo mysql_error(); exit; }
		}
		else
		{
			//echo "there";
			$query = "INSERT INTO ".$wpdb->prefix."lead_tracking
					(lead_id,tracking_id,date,data,nature) VALUES
					('".$data['lead_id']."' , '".$data['wp_lead_uid']."' , '".$wordpress_date_time."' , '".$json."' , 'conversion')";
			
			$result = mysql_query($query);				
			if (!$result){ echo $query; echo mysql_error(); exit; }
			
			$row_id = mysql_insert_id();
		}
		
		setcookie('user_data_json', "",time()+3600,"/"); // clear page view data

		/* Store number of page views as meta */
		$current_page_view_count = get_post_meta($data['lead_id'],'wpl-lead-page-view-count', true);
		if ($current_page_view_count)
		{
			$add_count_views = $view_count;
		}
		else
		{					
			$current_page_view_count = 0;
			$add_count_views = $view_count;			
		}
		$increment_page_views = $current_page_view_count + $add_count_views;
		update_post_meta($data['lead_id'],'wpl-lead-page-view-count', $increment_page_views);
		/* End Store number of page views as meta */

		/* Store conversions as meta */
		$conversions = get_post_meta($data['lead_id'],'wpl-lead-conversions', true);

		if ($conversions)
		{
			$array_conversions = explode(',',$conversions);
			$count_of_conversions = count($array_conversions);
			// if (!in_array($data['lp_id'],$array_conversions)) {
				$array_conversions[] = $data['lp_id'];
				//$array_conversions[] = $data['lp_id'];
			//}
		}
		else
		{					
			$array_conversions[] = $data['lp_id'];
			$count_of_conversions = 0;			
		}
		
		update_post_meta($data['lead_id'],'wpl-lead-conversions', implode(',',$array_conversions));
		/* Store conversions count as meta */
		$increment_conversions = $count_of_conversions + 1;
		update_post_meta($data['lead_id'],'wpl-lead-conversion-count', $increment_conversions);
		
		//update raw post data json 
		$raw_post_data = get_post_meta($data['lead_id'],'wpl-lead-raw-post-data', true);
					
		$a1 = json_decode( $raw_post_data, true );
		$a2 = json_decode( stripslashes($data['raw_post_values_json']), true );
		
		foreach ($a2 as $key=>$value)
		{
			if (stristr($key,'company'))
			{
				update_post_meta( $post_ID, 'wpleads_company_name', $value );
			}					
			else if (stristr($key,'website'))
			{
				$websites = get_post_meta( $post_ID, 'wpleads_websites', $value );
				
				if(is_array($websites))
				{
					$array_websites = explode(';',$websites);
				}
				
				$array_websites[] = $value;
				$websites = implode(';',$array_websites);
				update_post_meta( $post_ID, 'wpleads_websites', $websites );
			}
		}
		
		if (is_array($a1))
		{
			$new_raw_post_data = array_merge_recursive( $a1, $a2 );
		}
		else
		{
			$new_raw_post_data = $a2;
		}
		//print_r($new_raw_post_data);exit;
		
		$new_raw_post_data = json_encode( $new_raw_post_data );
		update_post_meta( $data['lead_id'],'wpl-lead-raw-post-data', $new_raw_post_data );
		
	}
}