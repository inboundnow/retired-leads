<?php
/**
* Inbound Lead Storage
*
* - Handles lead creation and data storage
*/

if (!class_exists('LeadStorage')) {
	class LeadStorage {
		static $mapped_fields;

		static function init() {
			add_action('wp_ajax_inbound_lead_store', array(__CLASS__, 'inbound_lead_store'), 10, 1);
			add_action('wp_ajax_nopriv_inbound_lead_store', array(__CLASS__, 'inbound_lead_store'), 10, 1);
		}

		static function inbound_lead_store($args = array()) {
			global $user_ID, $wpdb;
			if (!is_array($args)) { $args = array(); }
			/* Mergs $args with POST request for support of ajax and direct calls */
			if(isset($_POST)){
				$args = array_merge( $args, $_POST );
			}

			if(isset($user_ID)){
				$lead['user_ID'] = $user_ID;
			}
			/* Current wordpress time from settings */
			$time = current_time( 'timestamp', 0 );
			$lead['wordpress_date_time'] = date("Y-m-d G:i:s T", $time);

			$lead['email'] = self::checkVal('email', $args);
			$lead['page_views'] = self::checkVal('page_views', $args);
			$lead['raw_params'] = self::checkVal('raw_params', $args);
			$lead['mapped_params'] = self::checkVal('mapped_params', $args);
			$lead['ip_address'] = self::inbound_get_ip_address();


			/* check for set email */
			if ( (isset($lead['email']) && !empty($lead['email']) && strstr($lead['email'] ,'@'))) {
				//print_r($_POST); wp_die();

				$leadExists = self::lookupLeadByEmail($lead['email']);
				//print_r($leadExists); wp_die();
				/* Update Lead if Exists else Create New Lead */
				if ( $leadExists ) {
					$lead['id'] = $leadExists;
					/* action hook on existing leads only */
					do_action('wpleads_existing_lead_update', $lead);
				} else {
				/* Create new lead if one doesnt exist */
					$lead['id'] = self::createNewLead($lead);
				}
				/* do everything else for lead storage */
				self::inbound_update_common_meta($lead);

				do_action('wpleads_after_conversion_lead_insert', $lead['id']); // action hook on all lead inserts

				/* Add Leads to List on creation */
				if(!empty($lead['lead_lists']) && is_array($lead['lead_lists'])){
					global $Inbound_Leads;
					$Inbound_Leads->add_lead_to_list($lead['id'], $lead['lead_lists'], 'wplead_list_category');
				}

				/* Store past search history */
				if($lead['search_data']){
					self::storeSearchHistory($lead);
				}

				if ( isset($lead['page_id']) ) {
					self::storeConversionData($lead);
				}

				if ( isset($lead['source']) ) {
					self::storeReferralData($lead);
				}

				/* Store Conversion Data to LANDING PAGE/CTA DATA	*/
				if (isset($lead['post_type']) && $lead['post_type'] == 'landing-page' || $lead['post_type'] == 'wp-call-to-action') {
					self::storeConversionStats($lead);
				}

				/* Store IP addresss & Store GEO Data */
				if ($lead['ip_address']) {
					self::storeGeolocationData($lead);
				}

				setcookie('wp_lead_id' , $lead['id'], time() + (20 * 365 * 24 * 60 * 60),'/');

				do_action('inbound_store_lead_post', $lead );
				do_action('wp_cta_store_lead_post', $lead );
				do_action('wpl_store_lead_post', $lead );
				do_action('lp_store_lead_post', $lead );

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					echo $lead['id'];
					die();
				} else {
					return $lead['id'];
				}
			}
		}
		static function createNewLead($lead){
			/* Create New Lead */
			$post = array(
				'post_title'		=> $lead['email'],
				//'post_content'		=> $json,
				'post_status'		=> 'publish',
				'post_type'		=> 'wp-lead',
				'post_author'		=> 1
			);

			//$post = add_filter('lp_leads_post_vars',$post);
			$id = wp_insert_post($post);
			/* specific updates for new leads */
			update_post_meta( $id, 'wpleads_email_address', $lead['email'] );
			update_post_meta( $id, 'page_views', $lead['page_views'] ); /* Store Page Views Object */
			/* dont need update_post_meta( $id, 'wpleads_page_view_count', $lead['page_view_count']); */

			do_action('wpleads_new_lead_insert', $lead ); // action hook on new leads only
			return $id;
		}
		static function storeSearchHistory($lead){
				$search = $lead['search_data'];
				$search_data = get_post_meta( $lead['id'], 'wpleads_search_data', TRUE );
				$search_data = json_decode($search_data,true);
				if (is_array($search_data)){
					$s_count = count($search_data) + 1;
					$loop_count = 1;
					foreach ($search as $key => $value) {
					$search_data[$s_count]['date'] = $search[$loop_count]['date'];
					$search_data[$s_count]['value'] = $search[$loop_count]['value'];
					$s_count++; $loop_count++;
					}
				} else {
					// Create search obj
					$s_count = 1;
					$loop_count = 1;
					foreach ($search as $key => $value) {
					$search_data[$s_count]['date'] = $search[$loop_count]['date'];
					$search_data[$s_count]['value'] = $search[$loop_count]['value'];
					$s_count++; $loop_count++;
					}
				}
				$search_data = json_encode($search_data);
				update_post_meta($lead['id'], 'wpleads_search_data', $search_data); // Store search object
		}
		/* update conversion object */
		static function storeConversionData( $lead ) {

				$conversion_data = get_post_meta( $lead['id'], 'wpleads_conversion_data', TRUE );
				$conversion_data = json_decode($conversion_data,true);
				$variation = $lead['variation'];

				if ( is_array($conversion_data)) {
					$c_count = count($conversion_data) + 1;
					$conversion_data[$c_count]['id'] = $lead['page_id'];
					$conversion_data[$c_count]['variation'] = $variation;
					$conversion_data[$c_count]['datetime'] = $lead['wordpress_date_time'];
				} else {
					$conversion_data[1]['id'] = $lead['page_id'];
					$conversion_data[1]['variation'] = $variation;
					$conversion_data[1]['datetime'] = $lead['wordpress_date_time'];
					$conversion_data[1]['first_time'] = 1;
				}

				$lead['conversion_data'] = json_encode($conversion_data);
				update_post_meta($lead['id'],'wpleads_conversion_count', $c_count); // Store conversions count
				update_post_meta($lead['id'], 'wpleads_conversion_data', $lead['conversion_data']);// Store conversion obj

		}
		/* Store Conversion Data to LANDING PAGE/CTA DATA	*/
		static function storeConversionStats($lead){
			$page_conversion_data = get_post_meta( $lead['page_id'], 'inbound_conversion_data', TRUE );
			$page_conversion_data = json_decode($page_conversion_data,true);
			$version = ($lead['variation'] != 'default') ? $lead['variation'] : '0';
			if (is_array($page_conversion_data)) {
				$convert_count = count($page_conversion_data) + 1;
				$page_conversion_data[$convert_count]['lead_id'] = $lead['id'];
				$page_conversion_data[$convert_count]['variation'] = $version;
				$page_conversion_data[$convert_count]['datetime'] = $lead['wordpress_date_time'];
			} else {
				$page_conversion_data[1]['lead_id'] = $lead['id'];
				$page_conversion_data[1]['variation'] = $version;
				$page_conversion_data[1]['datetime'] = $lead['wordpress_date_time'];
			}
			$page_conversion_data = json_encode($page_conversion_data);
			update_post_meta($lead['page_id'], 'inbound_conversion_data', $page_conversion_data);
		}
		/* Store Lead Referral Source Data */
		static function storeReferralData($lead) {
			$referral_data = get_post_meta( $lead['id'], 'wpleads_referral_data', TRUE );
			$referral_data = json_decode($referral_data,true);
			if (is_array($referral_data)){
				$r_count = count($referral_data) + 1;
				$referral_data[$r_count]['source'] = $lead['source'];
				$referral_data[$r_count]['datetime'] = $lead['wordpress_date_time'];
			} else {
				$referral_data[1]['source'] = $lead['source'];
				$referral_data[1]['datetime'] = $lead['wordpress_date_time'];
				$referral_data[1]['original_source'] = 1;
			}
			$lead['referral_data'] = json_encode($referral_data);
			update_post_meta($lead['id'], 'wpleads_referral_data', $lead['referral_data']); // Store referral object
		}
		/*	Loop trough lead_data array and update post meta */
		static function inbound_update_common_meta($lead) {

			if (!empty($lead['user_ID'])) {
				/* Update user_ID if exists */
				update_post_meta( $lead['id'], 'wpleads_wordpress_user_id', $lead['user_ID'] );
			}

			/* Update wp_lead_uid if exist */
			if (!empty($lead['wp_lead_uid'])) {
				update_post_meta( $lead['id'], 'wp_leads_uid', $lead['wp_lead_uid'] );
			}

			/* Update mappable fields */
			$lead_fields = Leads_Field_Map::build_map_array();
			foreach ( $lead_fields as $key => $value ) {
				if (isset($lead_data[$key])) {
					update_post_meta( $lead['id'], $key, $lead_data[$key] );
				}
			}
		}
		/**
		 *	Connects to geoplugin.net and gets data on IP address and sets it into historical log
		 *	@param ARRAY $lead_data
		 */
		static function storeGeolocationData( $lead ) {

			$ip_addresses = get_post_meta( $lead['id'], 'wpleads_ip_address', true );
			$ip_addresses = json_decode( stripslashes($ip_addresses) , true);

			if (!$ip_addresses) {
				$ip_addresses = array();
			}

			$new_record[ $lead['ip_address'] ]['ip_address'] = $lead['ip_address'];


			/* ignore for local environments */
			if ($lead['ip_address']!= "127.0.0.1"){ // exclude localhost
				$response = wp_remote_get('http://www.geoplugin.net/php.gp?ip='.$lead['ip_address']);
				if ( isset($response['body']) ) {
					$geo_array = @unserialize($response['body']);
					$new_record[ $lead['ip_address'] ]['geodata'] = $geo_array;
				}

			}

			$ip_addresses = array_merge( $new_record, $ip_addresses );
			$ip_addresses = json_encode( $ip_addresses );

			update_post_meta( $lead['id'], 'wpleads_ip_address', $ip_addresses );
		}

		static function lookupLeadByEmail($email){
			global $wpdb;
			$query = $wpdb->prepare(
				'SELECT ID FROM ' . $wpdb->posts . '
				WHERE post_title = %s
				AND post_type = \'wp-lead\'',
				$email
			);
			$wpdb->query( $query );
			if ( $wpdb->num_rows ) {
				$lead_id = $wpdb->get_var( $query );
				return $lead_id;
			} else {
				return false;
			}

		}

		static function checkVal($key, $args) {
			$val = (isset($args[$key])) ? $args[$key] : false;
			return $val;
		}
		static function inbound_get_ip_address() {
			if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				if(isset($_SERVER["HTTP_CLIENT_IP"])) {
					$proxy = $_SERVER["HTTP_CLIENT_IP"];
				} else {
					$proxy = $_SERVER["REMOTE_ADDR"];
				}
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} else {
				if(isset($_SERVER["HTTP_CLIENT_IP"])) {
					$ip = $_SERVER["HTTP_CLIENT_IP"];
				} else {
					$ip = $_SERVER["REMOTE_ADDR"];
				}
			}
			return $ip;
		}

	}

	LeadStorage::init();
}