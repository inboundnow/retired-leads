<?php
/**
 * License handler for InboundNow Packaged Extensions
 *
 * This class should simplify the process of adding license information
 * to inboundnow multi-purposed extensions.
 *
 * @author  Hudson Atwell
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined( 'INBOUNDNOW_STORE_URL' ) )
	define('INBOUNDNOW_STORE_URL','http://www.inboundnow.com/');

if ( ! class_exists( 'INBOUNDNOW_EXTENSION_LICENSE' ) ) :

	/**
	 * LP_EXTENSION_LICENSE Class
	 */
	class INBOUNDNOW_EXTENSION_LICENSE {
		private $item_slug;
		private $item_shortname;
		private $version;		
		private $master_license_key;
		
		/**
		 * Class constructor
		 *
		 * @param string  $_file
		 * @param string  $_item_slug
		 * @param string  $_version
		 * @param string  $_author
		 * @param string  $_optname
		 * @param string  $_api_url
		 */
		function __construct( $_item_label, $_item_slug ) {

			$this->item_label      = $_item_label;
			$this->item_slug      = $_item_slug;
			$this->master_license_key = get_option('inboundnow_master_license_key' , '');

			// Setup hooks		
			$this->hooks();
		}

		/**
		 * Setup hooks
		 *
		 * @access  private
		 * @return  void
		 */
		 
		private function hooks() {
			// Register settings
			add_filter( 'lp_define_global_settings', array( $this, 'lp_settings' ), 2 );
			add_filter( 'wp_cta_define_global_settings', array( $this, 'wp_cta_settings' ), 2 );
			add_filter( 'wpleads_define_global_settings', array( $this, 'wpleads_settings' ), 2 );
			
			/**
			 * HOOKS : display license field in global settings area
			 */
			 
			add_action('lp_render_global_settings', array( $this, 'display_license_field'));
			add_action('wpleads_render_global_settings', array( $this, 'display_license_field'));
			add_action('wp_cta_render_global_settings', array( $this, 'display_license_field'));
			
			
			/**
			 * HOOKS : display license field in global settings area
			 */
			 
			add_action('lp_save_global_settings', array( $this, 'save_license_field'));
			add_action('wpleads_save_global_settings', array( $this, 'save_license_field'));
			add_action('wp_cta_save_global_settings', array( $this, 'save_license_field'));

		}

		/**
		 * Add license field to settings
		 *
		 * @access  public
		 * @param array   $settings
		 * @return  array
		 */
		public function lp_settings( $lp_global_settings ) {
			$lp_global_settings['lp-license-keys']['settings'][] = array(

					'id'      => $this->item_slug,
					'slug'      => $this->item_slug,
					'label'    => sprintf( '%1$s' , $this->item_label ),
					'description'    => 'Head to http://www.inboundnow.com/ to retrieve your license key for '.$this->item_label,
					'type'    => 'inboundnow-license-key',
					'default'    => '',
				);
			
			return $lp_global_settings;
		}
		
		/**
		 * Add license field to settings
		 *
		 * @access  public
		 * @param array   $settings
		 * @return  array
		 */
		public function wp_cta_settings( $wp_cta_global_settings ) {
		
			$wp_cta_global_settings['wp-cta-license-keys']['settings'][] = array(

					'id'      => $this->item_slug,					
					'slug'      => $this->item_slug,
					'label'    => sprintf( '%1$s' , $this->item_label ),
					'description'    => 'Head to http://www.inboundnow.com/ to retrieve your license key for '. $this->item_label ,
					'type'    => 'inboundnow-license-key',
					'default'    => '',
				);
			
			return $wp_cta_global_settings;
		}
		
		/**
		 * Add license field to settings
		 *
		 * @access  public
		 * @param array   $settings
		 * @return  array
		 */
		public function wpleads_settings( $wpleads_global_settings ) {
			$wpleads_global_settings['wpleads-license-keys']['label'] = 'License Keys';
			$wpleads_global_settings['wpleads-license-keys']['settings'][] = array(

					'id'      => $this->item_slug,					
					'slug'      => $this->item_slug,
					'label'    => sprintf( '%1$s' , $this->item_label ),
					'description'    => 'Head to http://www.inboundnow.com/ to retrieve your license key for'. $this->item_label,
					'type'    => 'inboundnow-license-key',
					'default'    => '',
				);
			
			//print_r($lp_global_settings);exit;
			return $wpleads_global_settings;
		}


		
		public function display_license_field($field)
		{
			if ( $field['type']=='inboundnow-license-key' &&  ($field['slug']==$this->item_slug) )
			{
				
				$field['id']  = "inboundnow-license-keys-".$field['slug'];		
		
				$field['value'] = get_option($field['id']);
				
				switch ($_GET['post_type']){
				
					case "landing-page":
						$prefix = "lp_";
						break;
					case "wp-lead":
						$prefix = "wpleads_";
						break;
					case "wp-call-to-action":
						$prefix = "wp_cta_";
						break;
					
				}
				//echo here;exit;
				$license_status = $this->check_license_status($field);
				//echo $license_status;exit;
				echo '<input type="hidden" name="inboundnow_license_status-'.$field['slug'].'" id="'.$field['id'].'" value="'.$license_status.'" size="30" />
				<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$field['value'].'" size="30" />
						<div class="'.$prefix.'tooltip tool_text" title="'.$field['description'].'"></div>'; 
				
				if ($license_status=='valid')
				{
					echo '<div class="'.$prefix.'license_status_valid">Valid</div>';
				}
				else
				{
					echo '<div class="'.$prefix.'license_status_invalid">Invalid</div>';
				}				
			}
		}


		
		function save_license_field($field)
		{
			if ($field['type']=='inboundnow-license-key')
			{
				//echo $field['id'].":".$_POST['main-landing-page-auto-format-forms']."<br>";
				$field['id']  = "inboundnow-license-keys-".$field['slug'];
				$field['old_value'] = get_option($field['id'] );	
				
				(isset($_POST[$field['id'] ]))? $field['new_value'] = $_POST[$field['id'] ] : $field['new_value'] = null;
				
				if ((isset($field['new_value']) && $field['new_value'] !== $field['old_value'] ) || !isset($field['old_value']) ) 
				{
					//echo $field['id'];exit;
					$bool = update_option($field['id'],$field['new_value']);	
					

					// data to send in our API request
					$api_params = array( 
						'edd_action'=> 'activate_license', 
						'license' 	=> $field['new_value'], 
						'item_name' =>  $field['slug'] ,
						'cache_bust'=> substr(md5(rand()),0,7)
					);						
					//print_r($api_params);
					
					// Call the custom API.
					$response = wp_remote_get( add_query_arg( $api_params, INBOUNDNOW_STORE_URL ), array( 'timeout' => 30, 'sslverify' => false ) );
					//var_dump($response);
					
					// make sure the response came back okay
					if ( is_wp_error( $response ) )
						$_SESSION['license_error_'. $field['slug']] = $response['body'];

					// decode the license data
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					

					// $license_data->license will be either "active" or "inactive"						
					$license_status = update_option('inboundnow-license_status-'.$field['slug'], $license_data->license);
					
				} 
				elseif ('' == $field['new_value'] ) 
				{
					if ($this->master_license_key) 
					{
						$bool = update_option($field['id'], $this->master_license_key );
					}
					else
					{
						update_option($field['id'], '' );
					}
				}
			}
		}


		/**
		 * FUNCTIONS : checks the status of the license key
		 */

		function check_license_status($field)
		{
			//print_r($field);exit;
			$date = date("Y-m-d");
			$cache_date = get_option($field['id']."-expire");
			$license_status = get_option($field['id']);
			
			if (isset($cache_date)&&($date<$cache_date)&&$license_status=='valid')
			{
				return "valid";
			}
				
			$license_key = get_option($field['id']);
			
			$api_params = array( 
				'edd_action' => 'check_license', 
				'license' => $license_key, 
				'item_name' => urlencode( $field['slug'] ) ,
				'cache_bust'=> substr(md5(rand()),0,7)
			);
			//print_r($api_params);
			
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, INBOUNDNOW_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
			//print_r($response);

			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			//echo $license_data;exit;
			
			if( $license_data->license == 'valid' ) {
				$newDate = date('Y-m-d', strtotime("+15 days"));
				update_option($field['id']."-expire", $newDate);
				return 'valid';
				// this license is still valid
			} else {
				return 'invalid';
			}
		}
	}

endif; // end class_exists check
