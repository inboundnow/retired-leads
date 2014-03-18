<?php
//define('DISABLE_WP_CRON', true);

$wpleads_lead_rules = new InboundAutomationCron;

class InboundAutomationCron {
	private $queue;
	private $queue_encoded;

	function __construct() {
		$this->daily_cron_init();
		$this->manual_cron_init();

	}

	function daily_cron_init() {
		/* hook processing actions to daily cron action */
		add_action('wpleads_lead_automation_daily' , array( 'InboundAutomationCron', 'process_automation_daily' ) );

		/* force rule processing if GET command detected */
		if (isset($_GET['wpleads_lead_automation_run_daily_cron'])){
			add_action ('admin_init', array( $this , 'process_automation_daily' ) );
		}
	}

	function manual_cron_init() {
		/* check manual processing queue for jobs */
		$this->queue = get_option('automation_queue' , "");
		$this->queue = json_decode( $this->queue , true );

		if (!is_array($this->queue) && count($this->queue) < 1){
				return null;
		}

		/* if rule queue populated schedule processing event */
		if ( ! wp_next_scheduled( 'wpleads_lead_automation_manual' ) )
		{
			wp_schedule_event( time(), '2min', 'wpleads_lead_automation_manual' );
		}

		/* add 5 minute interval to wordpress cron api */
		add_filter( 'cron_schedules', array( $this , 'define_period' ) );

		/* force rule processing if GET command detected */
		if (isset($_GET['wpleads_lead_automation_run_manual_cron'])){
			add_action ('admin_init', array( $this , 'process_automation_manually' ) );
		}

		/* hook callback for lead rule processing cron hook */
		add_action ('wpleads_lead_automation_manual', array( $this , 'process_automation_manually' ) );

	}

	function define_period( $schedules ) {
		$schedules['2min'] = array(
			'interval' => 2 * 60,
			'display' => __( 'Once every two minutes' )
		);

		return $schedules;
	}

	function process_automation_manually() {
		set_time_limit ( 0 );
		ignore_user_abort ( true );

		if ( !is_array($this->queue) && isset($_GET['wpleads_lead_automation_run_manual_cron']) ) {
			echo 'All done!';exit;
		}

		if ( !is_array($this->queue) )
			return;

		$count = count($this->queue);

		_e("Rules in processing queue: {$count} <br>" , 'leads' );

		foreach ($this->queue as $automation_id => $automation_data) {

			_e("Processing next rule in line: {$automation_id} <br>" , 'leads' );


			$tmp = $automation_data;

			end($tmp);
			$last_key = key($tmp);
			foreach ($automation_data as $batch_id=>$lead_ids) {
				_e("Processing batch number: {$batch_id} of {$last_key} <br>" , 'leads' );
				echo "<hr>";
				$i=0;
				foreach ($lead_ids as $lead_id) {
					$this->execute_rule( $automation_id , $lead_id );
					$i++;
				}

				/* remove batch from rule queue  */
				unset( $this->queue[$automation_id][$batch_id] );

				/* check if rule id has any more batches and delete rule id from queue if batches are exausted - delete rule id if empty*/
				if ( count($this->queue[$automation_id]) == 0 ) {
					unset($this->queue[$automation_id]);
				}

				/* break */
				break;
			}

			/* check if rules queue has any more rule ids to process - delete rule queue if empty  */
			if (count($this->queue) == 0 ) {
					$this->queue = null;
			}

			/* break */
			break;
		}

		/* update rule queue */
		if (is_array($this->queue)) {
			$this->queue_encoded = json_encode( $this->queue );
		}

		update_option( 'automation_queue' , $this->queue_encoded );
		exit;
	}

	/* RULE AUTOMATION ENGINE FOR PROCESSING RECENTLY CONVERTED LEADS */
	function process_automation_daily() {
		global $wpdb;

		set_time_limit ( 0 );
		ignore_user_abort ( true );
		/*clear duplicate scheduled events*/
		//wp_clear_scheduled_hook( 'wpleads_lead_automation_daily' );

		$timezone_format = _x('Y-m-d', 'timezone date format');
		$wordpress_date_time =  date_i18n($timezone_format);

		$args = array(
			'posts_per_page' => -1,
			'post_status' => 'published',
			'post_type' => 'wp-lead',
			'meta_query' => array(
					array(
						'key' => 'wpleads_last_updated',
						'value' => $wordpress_date_time,
						'compare' => 'LIKE'
					),
					array(
						'key' => 'wpleads_needs_processing',
						'value' => 1,
						'compare' => '='
					)
			)
		);

		$leads = get_posts( $args );
		//print_r($leads);

		_e("Processing Begin: ". count($leads) ." Leads to process <br><br>" , 'leads' );

		foreach  ($leads as $lead) {

			$lead_id = $lead->ID;

			/********* LOOP THROUGH RULES - PERFORM ACTIONS ******************/
			$automation_q = "SELECT ID FROM  {$wpdb->prefix}posts WHERE post_type = 'automation' AND post_status = 'publish' " ;


			$automation_r = mysql_query($automation_q);
			if (!$automation_r){ echo $automation_q; echo mysql_error(); exit; }


			while  ($array = mysql_fetch_array($automation_r))
			{
				$automation_id = $array['ID'];
				$this->execute_rule( $automation_id , $lead_id );
			}


			do_action('automation_cron_after_lead_processed', $lead_id);

			/* set lead processed */
			update_post_meta( $lead_id , 'wpleads_needs_processing' , 0 );

		}
	}

	function execute_rule( $automation_id , $lead_id ) {
		$conditions_met = array();

		echo "<br><br>";
		_e("Running Rule ID $automation_id on Lead ID: {$lead_id} <br>" , 'leads' );

		/* get session count */
		//$session_count_total = automation_cron_get_session_count($lead_id);

		/* get page views total from lead meta data */
		$pages_viewed_count = get_post_meta($lead_id,'wpleads_page_view_count', true);
		$pages_viewed_array = $this->get_pages_viewed( $lead_id );

		/* get conversion data */
		$converted_count = get_post_meta($lead_id,'wpleads_conversion_count', true);
		$conversion_pages_viewed =  $this->get_conversion_pages_viewed($lead_id);

		/* get rule data */
		$automation_meta_data= get_post_meta($automation_id);
		//print_r($automation_meta_data);exit;

		$automation_meta_data['automation_id'] = array($automation_id);
		$automation_meta_data['automation_name'] = array(get_the_title($automation_id));

		_e("Rule Name: ".$automation_meta_data['automation_name'][0]."<br>" , 'leads' );

		/* get rule conditions */
		$automation_block_ids = $automation_meta_data['automation_condition_blocks'][0];
		$automation_block_ids = json_decode($automation_block_ids,true);

		if (!$automation_block_ids)
			$automation_block_ids = array('0'=>'0');


		if ($automation_meta_data['automation_active'][0]!='active') {
			sprintf( _e("Skipping Rule: Rule %d ( %s ) is set to inactive! <br>" , 'leads' ) , $automation_meta_data['automation_id'][0] , $automation_meta_data['automation_name'][0]);
			return;
		}

		if ($automation_meta_data['automation_condition_automation_check_0'][0]=='on') {
			$automation_accomplished = get_post_meta($lead_id, 'automation_accomplished', true);
			$automation_accomplished = json_decode( $automation_accomplished , true );

			if ( !is_array($automation_accomplised) ) {
				$automation_accomplished = array();
			}

			if ( array_key_exists ( $automation_meta_data['automation_id'][0] , $automation_accomplished ) ) {
				sprintf(  _e("Skipping Rule: Rule %d ( %s )  already completed for lead %d! <br>" , 'leads' ) , $automation_meta_data['automation_id'][0] , $automation_meta_data['automation_name'][0] , $lead_id);
				return;
			}
		}


		foreach ($automation_block_ids as $condtion_key => $cid) {

			$conditions_met[$cid] = false;

			switch ($automation_meta_data['automation_if_'.$cid][0]) {

				case "page_views_general":
					if ($pages_viewed_count>=$automation_meta_data['automation_condition_number_'.$cid][0])
					{
						//_e("here" , 'leads' );exit;
						$conditions_met[$cid] = true;
					}
					break;
				case "page_views_category_specific":

					//get page views belonging to certain category
					$page_views_count = 0;

					$category = explode(':',$automation_meta_data['automation_condition_category_'.$cid][0]);
					$category_id = $category[0];
					$category_taxonomy = $category[1];

					foreach ($pages_viewed_array as $key=>$page_id)
					{

						$return = is_object_in_term( $page_id, $category_taxonomy, $category_id );

						if ($return){
							$page_views_count++;
						}
					}


					if ($page_views_count>=$automation_meta_data['automation_condition_number_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					else
					{
						_e("Message: $page_views_count out of ".$automation_meta_data['automation_condition_number_'.$cid][0]." exist in target rule category.<br>" , 'leads' );
					}
					break;
				case "page_conversions_general":

					if ($converted_count>=$automation_meta_data['automation_condition_number_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
				case "page_conversions_category_specific":
					//get page views belonging to certain category
					$converted_page_views_count = 0;

					$category = explode(':',$automation_meta_data['automation_condition_category_'.$cid][0]);
					$category_id = $category[0];
					$category_taxonomy = $category[1];
					foreach ($conversion_pages_viewed as $key=>$page_id)
					{

						$return = is_object_in_term( $page_id, $category_taxonomy, $category_id );
						if ($return)
							$converted_page_views_count++;

					}

					if ($converted_page_views_count>=$automation_meta_data['automation_condition_number_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}

					break;
				case "automation_executed":
					$automation_accomplished = get_post_meta($lead_id, 'automation_accomplished', true);
					$automation_accomplished = json_decode( $automation_accomplished , true );
					$automation_executed = count($automation_accomplished);

					if ($automation_executed>=$automation_meta_data['automation_condition_number_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
			}
		}

		$conditions_met = apply_filters('wpleads_lead_automation_extend_check', $conditions_met, $automation_block_ids, $lead_id, $automation_meta_data);

		/* check condition nature and devide if we should run the rule */
		if ($automation_meta_data['automation_conditions_nature'][0]=='match_all')
		{
			$run_rule = true;
			foreach ($automation_block_ids as $condtion_key => $cid)
			{
				if (!$conditions_met[$cid])
					$run_rule = false;
			}

			if (!$run_rule)
			{
				//$conditions_met = implode(':',$conditions_met);

				_e("Failed to meet required conditions: Rule ".$automation_meta_data['automation_id'][0]." (".$automation_meta_data['automation_name'][0].") ---- Conditions Met:" , 'leads' );
				print_r($conditions_met);
			}
		}
		else
		{
			$run_rule = false;
			foreach ($automation_block_ids as $condtion_key => $cid)
			{
				if ($conditions_met[$cid]){
					$run_rule = true;
				}
			}

			if (!$run_rule)
			{
				//$conditions_met = implode(':',$conditions_met);
				_e("Failed to meet required conditions: Rule ".$automation_meta_data['automation_id'][0]." (".$automation_meta_data['automation_name'][0].") ---- Conditions Met: " , 'leads' );
				print_r($conditions_met);
			}
		}

		if ($run_rule){
			$this->perform_automation_action($lead_id,$automation_meta_data);
		}

	}


	function get_conversion_pages_viewed($lead_id) {
		global $wpdb;

		$conversion_data = get_post_meta( $lead_id , 'wpleads_conversion_data' ,  true);
		$conversion_data = json_decode( $conversion_data , true );
		//prepare list of all converted pages viewed

		if (!$conversion_data)
			return array();

		foreach ($conversion_data as $key => $data) {
			if (!isset($data['id'])||!is_numeric($data['id']))
				continue;

			$conversion_pages_viewed[] = $data['id'] ;
		}

		return $conversion_pages_viewed;
	}

	function get_pages_viewed($lead_id) {
		global $wpdb;

		$pages_viewed_data = get_post_meta( $lead_id , 'page_views' ,  true);

		$pages_viewed_data = json_decode( $pages_viewed_data , true );

		if (!$pages_viewed_data) {
			return array();
		}

		foreach ($pages_viewed_data as $key => $data) {
			if (is_numeric($key)){
				$pages_viewed[] = $key;
			}
		}

		if (!$pages_viewed) {
			$pages_viewed = array();
		}

		$pages_viewed = array_unique($pages_viewed);

		return $pages_viewed;
	}

	function perform_automation_action( $lead_id , $automation_meta_data ) {

		$gateway_open = apply_filters('wpleads_lead_automation_action_gateway', true , $lead_id, $automation_meta_data);

		if ($gateway_open) {
			/* SORT INTO WORDPRESS LISTS */
			$lists_wp = $automation_meta_data['automation_condition_list_add_0'][0];

			$lists_wp = explode(';',$lists_wp);
			//print_r($lists_wp); echo $lead_id; exit;
			$lists_wp = array_filter($lists_wp);

			if (is_array($lists_wp)&&count($lists_wp)>0) {
				foreach ($lists_wp as $k=>$list_id) {
					_e("Action: Synching Lead $lead_id with List $list_id <br>" , 'leads' );
					// wpleads_add_lead_to_list($list_id, $lead_id, $add = true); // old list cpt function
					add_lead_to_list_tax($lead_id, intval($list_id));
				}
			}

			/* REMOVE FROM WORDPRESS LISTS */
			$lists_wp = $automation_meta_data['automation_condition_list_remove_0'][0];
			$lists_wp = explode(';',$lists_wp);
			$lists_wp = array_filter($lists_wp);
			if (is_array($lists_wp)&&count($lists_wp)>0) {
				$categories = wp_get_post_terms( $lead_id, 'wplead_list_category', array( 'fields'=>'ids' ) );

				foreach ($lists_wp as $k=>$list_id)
				{

					_e("Action: Removing Lead $lead_id from List $list_id <br>" , 'leads' );
					// wpleads_remove_lead_from_list($list_id , $lead_id); // old for list CPT
					remove_lead_from_list_tax($lead_id, intval($list_id));
				}

			}

			/* AWARD POINTS */
			$points = $automation_meta_data['automation_condition_points_0'][0];
			if ($points&&$points[0]=='-') {
				$points = str_replace('-','', $points);
				$points = trim($points);
				//subtract points
				$current_points = get_post_meta($lead_id, 'automation_points', true);
				($current_points) ? $current_points = $current_points - $points : $current_points = 0 - $points;
				update_post_meta($lead_id, 'automation_points' , $current_points);
			} else if ($points) {
				//add points
				$current_points = get_post_meta($lead_id, 'automation_points', true);
				($current_points) ? $current_points = $current_points + $points : $current_points = 0 + $points;
				update_post_meta($lead_id, 'automation_points' , $current_points);
			}


			/* UPDATE NUMBER OF RULES ACCOMPLISHED FOR LEAD */
			$automation_accomplished = get_post_meta($lead_id, 'automation_accomplished', true);
			$automation_accomplished = json_decode( $automation_accomplished , true );

			if ( !is_array($automation_accomplished) ){
				$automation_accomplished = array();
			}

			if ( !array_key_exists ( $automation_meta_data['automation_id'][0] , $automation_accomplished ) ){
				$automation_accomplished[$automation_meta_data['automation_id'][0]] = $automation_meta_data['automation_name'][0];
			}

			$automation_accomplsihed_count = count( $automation_accomplished );
			$automation_accomplished = json_encode( $automation_accomplished );
			update_post_meta( $lead_id , 'automation_accomplished' , $automation_accomplished);


			do_action('automation_cron_perform_action_post', $lead_id, $automation_meta_data);

			_e("Message: Lead matched this rule & all actions completed!<br>" , 'leads' );
			_e("Message: Total rules accomplished for this lead {$automation_accomplsihed_count}<br>" , 'leads' );
		}
	}
}