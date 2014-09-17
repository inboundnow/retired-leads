<?php
/**
 * Inbound Leads API
 *
 * This class provides a front-facing JSON API that makes it possible to
 * query data within the Leads database
 *
 *
 * @package     Leads
 * @subpackage  Classes/Leads API
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Inbound_API')) {

	/**
	 * Inbound_API Class
	 *
	 * Renders API returns as a JSON array
	 *
	 */
	class Inbound_API {

		/**
		 * API Version
		 */
		const VERSION = '1';

		/**
		 * Pretty Print?
		 *
		 * @var bool
		 * @since 1.5
		 */
		static $pretty_print = false;

		/**
		 * Log API requests?
		 *
		 * @var bool
		 * @access static
		 * @since 1.5
		 */
		static $log_requests = true;

		/**
		 * Is this a valid request?
		 *
		 * @var bool
		 * @access static
		 * @since 1.5
		 */
		static $is_valid_request = false;

		/**
		 * User ID Performing the API Request
		 *
		 * @var int
		 * @access static
		 * @since 1.5.1
		 */
		static $user_id = 0;

		/**
		 * Instance of EDD Stats class
		 *
		 * @var object
		 * @access static
		 */
		static $stats;

		/**
		 * Response data to return
		 *
		 * @var array
		 * @access static
		 */
		static $data = array();

		/**
		 *
		 * @var bool
		 * @access static
		 */
		static $override = true;

		/**
		 * Initialize the Inbound Leads API
		 *
		 */
		public function __construct() {
			/* Create endpoint listeners */
			add_action( 'init',                     array( __CLASS__ , 'add_endpoint'     ) );
			
			/* Build Query Router */
			add_action( 'template_redirect',        array( __CLASS__ , 'process_query'    ), -1 );
			
			/* Listen for & execute api key commands */
			add_action( 'inbound_process_api_key',  array( __CLASS__ , 'process_api_key'  ) );

			/* Determine if JSON_PRETTY_PRINT is available */
			self::$pretty_print = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;

			/* Allow API request logging to be turned off */
			self::$log_requests = apply_filters( 'inbound_api_log_requests', self::$log_requests );

		}

		/**
		 * Registers a new rewrite endpoint for accessing the API
		 *
		 * @access public
		 * @param array $rewrite_rules WordPress Rewrite Rules
		 *
		 */
		public static function add_endpoint( $rewrite_rules ) {
			add_rewrite_endpoint( 'inbound-api', EP_ALL );
			add_rewrite_endpoint( 'v1', EP_ALL );
			add_rewrite_endpoint( 'leads', EP_ALL );
			add_rewrite_endpoint( 'tags', EP_ALL );
			add_rewrite_endpoint( 'lists', EP_ALL );
			add_rewrite_endpoint( 'field-map', EP_ALL );
			add_rewrite_endpoint( 'analytics', EP_ALL );
		}

		/**
		 * Registers query vars for API access
		 *
		 * @access public
		 * @param array $vars Query vars
		 * @return array $vars New query vars
		 */
		public static function query_vars( $vars ) {
			$vars[] = 'inbound-api';
			$vars[] = 'token';
			$vars[] = 'key';
			$vars[] = 'number';
			$vars[] = 'date';
			$vars[] = 'page';
			$vars[] = 'startdate';
			$vars[] = 'enddate';
			$vars[] = 'has_tags';
			$vars[] = 'does_not_have_tags';
			$vars[] = 'in_lists';
			$vars[] = 'not_in_lists';
			$vars[] = 'has_uid';
			$vars[] = 'has_emails';
			$vars[] = 'meta_query';

			return $vars;
		}

		/**
		 * Validate the API request
		 *
		 * Checks for the user's public key and token against the secret key
		 *
		 * @access private
		 * @global object $wp_query WordPress Query
		 * @uses Inbound_API::get_user()
		 * @uses Inbound_API::invalid_key()
		 * @uses Inbound_API::invalid_auth()
		 * @return void
		 */
		private static function validate_request() {
			global $wp_query;

			self::$override = false;

			/* Check for presence of keys and tokens */
			if ( empty( $wp_query->query_vars['token'] ) || empty( $wp_query->query_vars['key'] ) ) {
				self::missing_auth();
			}

			// Retrieve the user by public API key and ensure they exist
			if ( ! ( $user = self::get_user( $wp_query->query_vars['key'] ) ) ) {
				self::invalid_key();
			} else {
				$token  = urldecode( $wp_query->query_vars['token'] );
				$secret = get_user_meta( $user, 'inbound_user_secret_key', true );
				$public = urldecode( $wp_query->query_vars['key'] );

				if ( hash( 'md5', $secret . $public ) === $token ) {
					self::$is_valid_request = true;
				} else {
					self::invalid_auth();
				}
			}
			 
		}

		/**
		 * Retrieve the user ID based on the public key provided
		 *
		 * @access public
		 * @global object $wpdb Used to query the database using the WordPress Database API
		 *
		 * @param string $key Public Key
		 *
		 * @return bool if user ID is found, false otherwise
		 */
		public static function get_user( $key = '' ) {
			global $wpdb, $wp_query;

			if( empty( $key ) )
				$key = urldecode( $wp_query->query_vars['key'] );

			if ( empty( $key ) ) {
				return false;
			}

			$user = get_transient( md5( 'inbound_api_user_' . $key ) );

			if ( false === $user ) {
				$user = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'inbound_user_public_key' AND meta_value = %s LIMIT 1", $key ) );
				set_transient( md5( 'inbound_api_user_' . $key ) , $user, DAY_IN_SECONDS );
			}

			if ( $user != NULL ) {
				self::$user_id = $user;
				return $user;
			}

			return false;
		}

		/**
		 * Displays a missing authentication error if all the parameters aren't
		 * provided
		 *
		 * @access private
		 * @uses Inbound_API::output()
		 */
		private static function missing_auth() {
			$error['error'] = __( 'You must specify both a token and API key!', 'leads' );

			self::$data = $error;
			self::output( 401 );
		}

		/**
		 * Displays an authentication failed error if the user failed to provide valid
		 * credentials
		 *
		 * @access private
		 * @uses Inbound_API::output()
		 * @return void
		 */
		private static function invalid_auth() {
			$error['error'] = __( 'Your request could not be authenticated! (check your token)', 'leads' );

			self::$data = $error;
			self::output( 401 );
		}

		/**
		 * Displays an invalid API key error if the API key provided couldn't be
		 * validated
		 *
		 * @access private
		 * @uses Inbound_API::output()
		 * @return void
		 */
		private static function invalid_key() {
			$error['error'] = __( 'Invalid API key!', 'leads' );

			self::$data = $error;
			self::output( 401 );
		}
		
		/**
		 * Validates parameter type
		 *
		 * @access private
		 * @uses Inbound_API::output()
		 * @param MIXED $value value to measure
		 * @param $accepted value type desired
		 * @return $value or die();
		 */
		private static function validate_parameter( $value , $accepted ) {
			
			if (gettype($value == $accepted )) {
				return $value;
			}
			
			$error['error'] = sprintf( __( 'Invalid parameter provided. Expecting %1$s for %2$s the %3$s was provided', 'leads' ) , $accepted , $key , $provided) ;

			self::$data = $error;
			self::output( 401 );
		}
		
		/**
		 * Displays an invalid parameter error
		 *
		 * @access private
		 * @uses Inbound_API::output()
		 * @return void
		 */
		private static function invalid_parameter( $key , $accepted, $provided ) {
			$error['error'] = sprintf( __( 'Invalid parameter provided. Expecting %1$s for %2$s the %3$s was provided', 'leads' ) , $accepted , $key , $provided) ;

			self::$data = $error;
			self::output( 401 );
		}


		/**
		 * Listens for the API and then processes the API requests
		 *
		 * @access public
		 * @global $wp_query
		 * @return void
		 */
		public static function process_query() {
			global $wp_query;
			
			/* Check for inbound-api var. Get out if not present */
			if ( ! isset( $wp_query->query_vars['inbound-api'] ) ) {
				return;
			}
			
			//print_r($wp_query->query_vars);

			/* Check for a valid user and set errors if necessary */
			self::validate_request();

			/* Only proceed if no errors have been noted */
			if( ! self::$is_valid_request ) {
				return;
			}

			if( ! defined( 'INBOUND_DOING_API' ) ) {
				define( 'INBOUND_DOING_API', true );
			}

			/* Determine the kind of query */
			$query_type = self::get_query_type();

			$data = array();

			switch( $query_type ) :
				
				case 'v1/leads' :
					/* get leads */
					$data = self::leads_get();
					BREAK;
					
				case 'v1/leads/add' :
					/* Add leads */
					$data = self::leads_add();
					BREAK;
					
				case 'v1/leads/modify' :
					/* Update lead records */
					$data = self::leads_update();
					BREAK;
					
				case 'v1/leads/delete' :
					/* delete leads */
					$data = self::leads_delete();
					BREAK;
					
				case 'v1/lists' :
					/* get lead lists */
					$data = self::lists_get();
					BREAK;
					
				case 'v1/lists/add' :
					/* add lead lists */
					$data = self::lists_add();
					BREAK;
					
				case 'v1/lists/update' :
					/* add lead lists */
					$data = self::lists_update();
					BREAK;
					
				case 'v1/field-map' :
					/* add lead lists */
					$data = self::fieldmap_get();
					BREAK;
					
				case 'v1/lists/delete' :
					/* delete lead lists */
					$data = self::lists_delete();
					BREAK;
					
				case 'v1/analytics/track_links' :
					/* delete lead lists */
					$data = self::analytics_track_links();
					BREAK;					

			endswitch;

			/* Allow extensions to setup their own return data */
			self::$data = apply_filters( 'inbound_api_output_data', $data, $query_type );


			/* Send out data to the output function */
			self::output();
		}

		/**
		 * Determines the kind of query requested and also ensures it is a valid query
		 *
		 * @global $wp_query
		 * @return string $query type of query to run
		 */
		public static function get_query_type() {
			global $wp_query;

			// Whitelist our query options
			$accepted = apply_filters( 'inbound_api_valid_query_types', array(
				'v1/leads',
				'v1/leads/add',
				'v1/leads/modify',
				'v1/leads/delete',
				'v1/lists',
				'v1/lists/add',
				'v1/lists/update',
				'v1/lists/delete',
				'v1/field-map',
				'v1/analytics/track_links',
			) );

			$query = isset( $wp_query->query_vars['inbound-api'] ) ? $wp_query->query_vars['inbound-api'] : null;

			// Make sure our query is valid
			if ( ! in_array( $query, $accepted ) ) {
				$error['error'] = __( 'Invalid endpoint: ' . $query , 'leads' );

				self::$data = $error;
				self::output();
			}

			return $query;
		}

		/**
		 * Get page number
		 *
		 * @access private
		 * @global $wp_query
		 * @return int $wp_query->query_vars['page'] if page number returned (default: 1)
		 */
		public static function get_paged() {
			global $wp_query;

			return isset( $wp_query->query_vars['page'] ) ? $wp_query->query_vars['page'] : 1;
		}


		/**
		 * Number of results to display per page
		 *
		 * @access private
		 * @global $wp_query
		 * @return int $per_page Results to display per page (default: 10)
		 */
		public static function per_page() {
			global $wp_query;

			$per_page = isset( $wp_query->query_vars['number'] ) ? $wp_query->query_vars['number'] : 10;

			if( $per_page < 0 && self::get_query_mode() == 'customers' )
				$per_page = 99999999; // Customers query doesn't support -1

			return apply_filters( 'inbound_api_results_per_page', $per_page );
		}


		/**
		 * Sets up the dates used to retrieve leads
		 *
		 * @access public
		 * @since 1.5.1
		 * @param array $args Arguments to override defaults
		 * @return array $dates
		*/
		public static function get_dates( $args = array() ) {
			$dates = array();

			$defaults = array(
				'type'      => '',
				'product'   => null,
				'date'      => null,
				'startdate' => null,
				'enddate'   => null
			);

			$args = wp_parse_args( $args, $defaults );

			$current_time = current_time( 'timestamp' );

			if ( 'range' === $args['date'] ) {
				$startdate          = strtotime( $args['startdate'] );
				$enddate            = strtotime( $args['enddate'] );
				$dates['day_start'] = date( 'd', $startdate );
				$dates['day_end'] 	= date( 'd', $enddate );
				$dates['m_start'] 	= date( 'n', $startdate );
				$dates['m_end'] 	= date( 'n', $enddate );
				$dates['year'] 		= date( 'Y', $startdate );
				$dates['year_end'] 	= date( 'Y', $enddate );
			} else {
				// Modify dates based on predefined ranges
				switch ( $args['date'] ) :

					case 'this_month' :
						$dates['day'] 	    = null;
						$dates['m_start'] 	= date( 'n', $current_time );
						$dates['m_end']		= date( 'n', $current_time );
						$dates['year']		= date( 'Y', $current_time );
					break;

					case 'last_month' :
						$dates['day'] 	  = null;
						$dates['m_start'] = date( 'n', $current_time ) == 1 ? 12 : date( 'n', $current_time ) - 1;
						$dates['m_end']	  = $dates['m_start'];
						$dates['year']    = date( 'n', $current_time ) == 1 ? date( 'Y', $current_time ) - 1 : date( 'Y', $current_time );
					break;

					case 'today' :
						$dates['day']		= date( 'd', $current_time );
						$dates['m_start'] 	= date( 'n', $current_time );
						$dates['m_end']		= date( 'n', $current_time );
						$dates['year']		= date( 'Y', $current_time );
					break;

					case 'yesterday' :
						$month              = date( 'n', $current_time ) == 1 && date( 'd', $current_time ) == 1 ? 12 : date( 'n', $current_time );
						$days_in_month      = cal_days_in_month( CAL_GREGORIAN, $month, date( 'Y', $current_time ) );
						$yesterday          = date( 'd', $current_time ) == 1 ? $days_in_month : date( 'd', $current_time ) - 1;
						$dates['day']		= $yesterday;
						$dates['m_start'] 	= $month;
						$dates['m_end'] 	= $month;
						$dates['year']		= $month == 1 && date( 'd', $current_time ) == 1 ? date( 'Y', $current_time ) - 1 : date( 'Y', $current_time );
					break;

					case 'this_quarter' :
						$month_now = date( 'n', $current_time );

						$dates['day'] 	        = null;

						if ( $month_now <= 3 ) {

							$dates['m_start'] 	= 1;
							$dates['m_end']		= 3;
							$dates['year']		= date( 'Y', $current_time );

						} else if ( $month_now <= 6 ) {

							$dates['m_start'] 	= 4;
							$dates['m_end']		= 6;
							$dates['year']		= date( 'Y', $current_time );

						} else if ( $month_now <= 9 ) {

							$dates['m_start'] 	= 7;
							$dates['m_end']		= 9;
							$dates['year']		= date( 'Y', $current_time );

						} else {

							$dates['m_start'] 	= 10;
							$dates['m_end']		= 12;
							$dates['year']		= date( 'Y', $current_time );

						}
					break;

					case 'last_quarter' :
						$month_now = date( 'n', $current_time );

						$dates['day'] 	        = null;

						if ( $month_now <= 3 ) {

							$dates['m_start'] 	= 10;
							$dates['m_end']		= 12;
							$dates['year']		= date( 'Y', $current_time ) - 1; // Previous year

						} else if ( $month_now <= 6 ) {

							$dates['m_start'] 	= 1;
							$dates['m_end']		= 3;
							$dates['year']		= date( 'Y', $current_time );

						} else if ( $month_now <= 9 ) {

							$dates['m_start'] 	= 4;
							$dates['m_end']		= 6;
							$dates['year']		= date( 'Y', $current_time );

						} else {

							$dates['m_start'] 	= 7;
							$dates['m_end']		= 9;
							$dates['year']		= date( 'Y', $current_time );

						}
					break;

					case 'this_year' :
						$dates['day'] 	    = null;
						$dates['m_start'] 	= null;
						$dates['m_end']		= null;
						$dates['year']		= date( 'Y', $current_time );
					break;

					case 'last_year' :
						$dates['day'] 	    = null;
						$dates['m_start'] 	= null;
						$dates['m_end']		= null;
						$dates['year']		= date( 'Y', $current_time ) - 1;
					break;

				endswitch;
			}

			/**
			 * Returns the filters for the dates used to retrieve earnings/sales
			 *
			 * @param object $dates The dates used for retrieving earnings/sales
			 */

			return apply_filters( 'inbound_api_stat_dates', $dates );
		}


		/**
		 * Retrieve the output data
		 *
		 * @access public
		 * @return array
		 */
		public static function get_output() {
			return self::$data;
		}

		/**
		 * Output Query in either JSON/XML. The query data is outputted as JSON
		 * by default
		 *
		 * @global $wp_query
		 *
		 * @param int $status_code
		 */
		public static function output( $status_code = 200 ) {
			global $wp_query;

			$format = apply_filters('inbound_api_output_format' , 'json');

			status_header( $status_code );

			do_action( 'inbound_api_output_before', self::$data );

			switch ( $format ) :

				case 'json' :

					header( 'Content-Type: application/json' );
					if ( ! empty( self::$pretty_print ) ) {
						echo json_encode( self::$data, self::$pretty_print );
					} else {
						echo json_encode( self::$data );
					}

					break;


				default :

					// Allow other formats to be added via extensions
					do_action( 'inbound_api_output_' . $format, self::$data, $this );

					break;

			endswitch;

			do_action( 'inbound_api_output_after', self::$data );

			die();
		}

		/**
		 * Retrieve the user's token
		 *
		 * @access private
		 * @param int $user_id
		 * @return string
		 */
		private static function get_token( $user_id = 0 ) {
			$user = get_userdata( $user_id );
			return hash( 'md5', $user->inbound_user_secret_key . $user->inbound_user_public_key );
		}

		/**
		 *  Query designed to return leads based on conditions defined by user.
		 *  
		 *  @access public
		 *  @param ARRAY $params key/value pairs that will direct the building of WP_Query
		 */
		public static function leads_get( $params = array() ) {
			
			/* Merge POST & GET & @param vars into array variable */
			$params = array_merge( $params , $_REQUEST ); 
			
			/* prepare WP_Query defaults */
			$args = self::leads_prepare_defaults( $params );
			
			/* Prepare WP_Query arguments with tax_query rules */
			$args = self::leads_prepare_tax_query( $args , $params );
			
			/* Prepare WP_Query arguments with meta_query rules */
			if (isset($params['meta_query'])) {
				$args['meta_query'] = self::validate_parameter( $params['meta_query'] , 'ARRAY'  );
			} 
			
			/* Run Query */
			$results = new WP_Query( $args );

			/* Get meta data for each result */
			$results = self::prepare_lead_results( $results );
			
			return $results;
		}
		
		/**
		 *  Sets the API defaults for the /leads/(get) endpoint 
		 *  
		 *  @access public
		 *  @param ARRAY $params
		 *  @returns ARRAY $params 
		 */
		public static function leads_prepare_defaults( $params ) {
			
			 $args['s'] = (isset($params['email'])) ? self::validate_parameter( $params['email'] , 'STRING'  ) : '';
			 $args['p'] = (isset($params['ID'])) ? self::validate_parameter( $params['ID'] , 'INT'  ) : '';
			 $args['posts_per_page'] = (isset($params['number'])) ? self::validate_parameter( $params['number'] , 'INT' ) : 50;
			 $args['paged'] = (isset($params['page'])) ? self::validate_parameter( $params['page'] , 'INT' ) : 1 ;
			 $args['post_type'] = 'wp-lead';
			 return $args;
		}
		
		/**
		 *  Builds a tax_query ARRAY from included parameters if applicable. 
		 *  Used for tag searches and lead list searches. 
		 *  
		 *  @param ARRAY $args arguments for WP_Query
		 *  @param ARRAY $params includes param key/value pairs submitted to the api
		 *  @returns ARRAY $args
		 */
		public static function leads_prepare_tax_query( $args , $params ) {
			
			if ( isset($params['include_lists']) || isset($params['exclude_lists']) || isset($params['include_tags']) || isset($params['exclude_tags'])) {
				$args['tax_query']['relation'] = 'AND';
			}
			
			if ( isset($params['include_lists']) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wplead_list_category',
					'field'    => 'ID',
					'terms'    => self::validate_parameter( $params['include_lists'] , 'ARRAY'  ),
					'operator' => 'IN',
				);
			}
			
			if ( isset($params['exclude_lists']) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wplead_list_category',
					'field'    => 'ID',
					'terms'    => self::validate_parameter( $params['exclude_lists'] , 'ARRAY'  ),
					'operator' => 'NOT IN',
				);
			}
			
			if ( isset($params['include_tags']) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'lead-tags',
					'field'    => 'ID',
					'terms'    => self::validate_parameter( $params['include_tags'] , 'ARRAY'  ),
					'operator' => 'IN'
				);
			}
			
			if ( isset($params['exclude_tags']) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'lead-tags',
					'field'    => 'ID',
					'terms'    => self::validate_parameter( $params['exclude_tags'] , 'ARRAY'  ),
					'operator' => 'NOT IN'
				);
			}
			
			return $args;
		}
		
		/**
		 *  Converts WP_Query object into array and imports additional lead data
		 *  
		 *  @param OBJECT $results WP_Query results
		 *  @return ARRAY $leads updated array of results
		 */
		public static function prepare_lead_results( $results ) {
			
			if ( !$results->have_posts() ) {
				 return null;
			}
			
			$leads = array();
			$leads['results_count'] = $results->found_posts;
			$leads['max_pages'] = $results->max_num_pages;
			while ( $results->have_posts() ) : $results->the_post(); 
				
				$ID = $results->post->ID;
				
				/* set ID */
				$leads[ $ID ]['ID'] = $ID;
				
				/* set lead lists */
				$lists = get_the_terms( $ID , 'wplead_list_category' );
				$leads[ $ID ]['lists'] = $lists;
				
				/* set lead tags */
				$tags = get_the_terms( $ID , 'lead-tags' );
				$leads[ $ID ]['tags'] = $tags;
				
				/* set lead meta data */ 
				$meta_data = get_post_custom($ID);
				$leads[ $ID ]['meta_data'] = $meta_data;
			
			endwhile;
		
			return $leads;
		}
		
		public static function leads_add() {
		
		}
		
		public static function leads_update() {
		
		}
		
		public static function leads_delete() {
		
		}
		
		public static function field_map_get() {
		
		}
		
		public static function lists_get() {
		
		}
		
		public static function lists_add() {
		
		}
		
		public static function lists_update() {
		
		}
		
		public static function lists_delete() {
		
		}
		
		public static function analytics_track_links() {
		
		}
	
	}

	$GLOBALS['Inbound_API'] = new Inbound_API();
	
}
