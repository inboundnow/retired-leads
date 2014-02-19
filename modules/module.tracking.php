<?php

/* needs documentation  - looks like a listener to set the lead id manually */
add_action( 'wp_head', 'wpleads_set_lead' );
function wpleads_set_lead() {
	if (isset($_GET['wpl_email'])) {
		$lead_id = $_GET['wpl_email'];
		wpleads_set_lead_id($lead_id);
	}
}

/* cookies lead id */
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

/**
 * Sets cookie with current lists the lead is a part of
 * @param  string $lead_id - lead CPT id
 * @return sets cookie of lists lead belongs to
 */
function wp_leads_set_current_lists($lead_id){
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

// DOESNT RUN UNLESS USER LOGGED IN =/
//add_action( 'wp_head', 'lead_revisit_notifications' );
function lead_revisit_notifications() {
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

/**
 * wp_leads_update_page_view_obj updates page_views meta for known leads
 * @param  int $lead_id     [ID of lead]
 * @param  int $page_id     [ID of page]
 */

function wp_leads_update_page_view_obj( $lead_id, $page_id ) {

	if($page_id === "" || empty($page_id)){
		return;
	}
	$current_page_view_count = get_post_meta($lead_id,'wpleads_page_view_count', true);

	$increment_page_views = $current_page_view_count + 1;

	update_post_meta($lead_id,'wpleads_page_view_count', $increment_page_views); // update count

	$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
	$wordpress_date_time = date("Y-m-d G:i:s T", $time);

	$page_view_data = get_post_meta( $lead_id, 'page_views', TRUE );
	//echo $page_id; // for debug

	// If page_view meta exists do this
	if ($page_view_data)
	{
		$current_count = 0; // default
		$timeout = 30;  // 30 Timeout analytics tracking for same page timestamps
		$page_view_data = json_decode($page_view_data,true);

		// increment view count on page
		if(isset($page_view_data[$page_id]))
		{
			$current_count = count($page_view_data[$page_id]);
			$last_view = $page_view_data[$page_id][$current_count];
			$timeout = abs(strtotime($last_view) - strtotime($wordpress_date_time));
		}

		// If page hasn't been viewed in past 30 seconds. Log it
		if ($timeout >= 30)
		{
			$page_view_data[$page_id][$current_count + 1] = $wordpress_date_time;
			$page_view_data = json_encode($page_view_data);
			update_post_meta( $lead_id, 'page_views', $page_view_data );
		}

	}
	else
	{
		// Create page_view meta if it doesn't exist
		$page_view_data = array();
		$page_view_data[$page_id][0] = $wordpress_date_time;
		$page_view_data = json_encode($page_view_data);
		update_post_meta( $lead_id, 'page_views', $page_view_data );
	}
}

/**
 * wpleads_check_url_for_queries disects keyword params from referring url
 * @param  string $referrer
 */
function wpleads_check_url_for_queries($referrer)
{
	//now check if google
	if (strstr($referrer,'q='))
	{
		//get keywords
		preg_match('/q=(.*?)(&|\z)/', $referrer,$matches);
		$keywords = $matches[1];
		$keywords = urldecode($keywords);
		$keywords = str_replace('+',' ',$keywords);

		//get search engine domain
		$parsed = parse_url($referrer);
		$domain = $parsed['host'];

		return array($keywords,$domain);

	}

	return false;
}