<?php

/*  Generate Lead Rule Processing Batch */
add_action('wp_ajax_automation_run_automation_on_all_leads', 'wpleads_lead_automation_build_queue');
add_action('wp_ajax_nopriv_automation_run_automation_on_all_leads', 'wpleads_lead_automation_build_queue');

function wpleads_lead_automation_build_queue()
{
	global $wpdb;

	$automation_id = $_POST['automation_id'];
	$automation_queue = get_option( 'automation_queue');
	$automation_queue = json_decode( $automation_queue , true);

	if ( !is_array($automation_queue) )
		$automation_queue = array();

	if ( !in_array( $automation_id , $automation_queue ) )
	{
		/* get all lead ids */
		$sql = "SELECT distinct(ID) FROM {$wpdb->prefix}posts WHERE post_status='publish'  AND post_type = 'wp-lead' ";
		$result = mysql_query($sql);

		$batch = 1;
		$row = 0;

		while ($lead = mysql_fetch_array($result))
		{
			if ($row>1000)
			{
				$batch++;
				$row=0;
			}

			$automation_queue[$automation_id][$batch][] = $lead['ID'];

			$row++;
		}
	}

	$automation_queue = json_encode( $automation_queue);
	update_option( 'automation_queue' , $automation_queue);

	var_dump($automation_queue);
	die();
}

/* Increases the page view statistics of lead on page load */
add_action('wp_ajax_wpl_track_user', 'wpl_track_user_callback');
add_action('wp_ajax_nopriv_wpl_track_user', 'wpl_track_user_callback');

function wpl_track_user_callback()
{
	global $wpdb;

	(isset(	$_POST['wp_lead_id'] )) ? $lead_id = $_POST['wp_lead_id'] : $lead_id = '';
	(isset(	$_POST['nature'] )) ? $nature = $_POST['nature'] : $nature = 'non-conversion'; // what is nature?
	(isset(	$_POST['json'] )) ? $json = addslashes($_POST['json']) : $json = 0;
	(isset(	$_POST['wp_lead_uid'] )) ? $wp_lead_uid = $_POST['wp_lead_uid'] : $wp_lead_uid = 0;
	(isset(	$_POST['page_id'] )) ? $page_id = $_POST['page_id'] : $page_id = 0;
	(isset(	$_POST['current_url'] )) ? $current_url = $_POST['current_url'] : $current_url = 'notfound';

	// NEW Tracking
	if(isset($_POST['wp_lead_id'])) {
		wp_leads_update_page_view_obj($lead_id, $page_id, $current_url);
	}

	die();
}

/* sets cookie of lists that lead belongs to */
add_action('wp_ajax_wpl_check_lists', 'wpl_check_lists_callback');
add_action('wp_ajax_nopriv_wpl_check_lists', 'wpl_check_lists_callback');
function wpl_check_lists_callback() {
	$wp_lead_id = $_POST['wp_lead_id'];
	if (isset( $_POST['wp_lead_id'])&&!empty( $_POST['wp_lead_id']))
	{
		wp_leads_set_current_lists($wp_lead_id);
	}
}



