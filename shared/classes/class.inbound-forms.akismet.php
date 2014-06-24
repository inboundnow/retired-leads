<?php


if ( !class_exists('Inbound_Akismet') ) {

	class Inbound_Akismet {

		function __construct() {
			self::load_hooks();
		}

		private function load_hooks() {
			
		}
		
		public static function check_is_spam( $lead_data ) {
			if (!get_api_key()) {
				return;
			}
			
			$params = Inbound_Akismet::prepare_params( $lead_data );
			
		}
		
		/* Get Akismet API key */
		public static function get_api_key() {

			if ( is_callable( array( 'Akismet', 'get_api_key' ) ) ) { // Akismet v3.0+
				return (bool) Akismet::get_api_key();
			}

			if ( function_exists( 'akismet_get_key' ) ) {
				return (bool) akismet_get_key();
			}

			return false;
		}

		/* Extract lead data and prepare params for akismet filtering */
		public static function prepare_params( $lead_data ) {			
			global $akismet_api_host, $akismet_api_port;
			

			$first_name = (isset($lead_data['wpleads_first_name'])) ? $lead_data['wpleads_first_name'] : '';
			$last_name = (isset($lead_data['wpleads_last_name'])) ? $lead_data['wpleads_last_name'] : '';
			$email_address = (isset($lead_data['wpleads_email_address'])) ? $lead_data['wpleads_email_address'] : '';
			$notes = (isset($lead_data['wpleads_notes'])) ? $lead_data['wpleads_notes'] : '';
		
			$params = array(
				'comment_author' => $first_name . ' ' . $last_name,
				'comment_author_email' => $email_address,
				'comment_content' => $notes
			);
			
			$params['blog'] = get_option( 'home' );
			$params['blog_lang'] = get_locale();
			$params['blog_charset'] = get_option( 'blog_charset' );
			$params['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
			$params['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$params['referrer'] = $_SERVER['HTTP_REFERER'];
		
		}
		
	}

	/* Load Email Templates Post Type Pre Init */
	add_action('init' , function() {
		$GLOBALS['Inbound_Akismet'] = new Inbound_Akismet();
	} );
}