<?php
if(!class_exists('Inbound_Confirm_Double_Optin')){
    
    class Inbound_Confirm_Double_Optin{

        /**
         * Initialize class
         */
        function __construct(){
            self::add_hooks();
        }
        
        /**
         * Load Hooks and Filters
         */
        public static function add_hooks(){

        /* Add processing listener */
        add_action( 'init' , array( __class__, 'process_double_optin' ), 20 );        
        
        /* Shortcode for displaying list double opt in confirmation form */
        add_shortcode( 'inbound-list-confirm-double-optin' , array( __CLASS__, 'display_double_optin_form' ), 1 );
        
		/* Process shortcode to produce the confirmation link,
         * the name is different from the one the user uses to prevent early rendering */
		add_shortcode( 'list-double-optin-link', array( __CLASS__, 'process_list_confirm_link' ) );        
            
        }

        /**
         *  Listens for the user to confirm being opted into a list
         */
        public static function process_double_optin(){
 
            if (!isset($_POST['action']) || $_POST['action'] != 'inbound_confirm_event' ) {
                return;
            }
            /* determine if anything is selected */
            if (!isset($_POST['list_ids']) && !isset($_POST['lists_all'])) {
                return;
            }
 
            /* decode token */
            $params = self::decode_confirm_token( $_POST['token'] );
            /* prepare all token */
            $all = (isset($_POST['lists_all']) && $_POST['lists_all']  ) ? true : false;
            
            /*if all isn't selected*/
            if($all === false){
                /*map the list ids to the selected inputs*/
                $params['list_ids'] = array_map(function($id){ $id = (int)$id;  if($id && $id > 0){ return $id; }}, $_POST['list_ids']);
            }

            if(isset($_POST['confirm'])){
                self::confirm_being_added_to_lists($params, $all);
            }
        }

        public static function display_double_optin_form($atts){
            global $inbound_settings;

            if(!defined('INBOUND_PRO_CURRENT_VERSION')){
                $confirm_button_text = get_option('list-double-optin-button-text', __( 'Confirm List Opt In', 'inbound-pro'));
                $confirm_show_lists = get_option('list-double-optin-show-lists', 'on');
                $lead_confirmed_confirmation_message = get_option('list-double-optin-confirmed-message', __( 'Thank You!', 'inbound-pro'));
                $all_lists = get_option('list-double-optin-all-list-text', __( 'All Lists' , 'inbound-pro' ));
            }else{
                $confirm_button_text = (isset($inbound_settings['leads']['list-double-optin-button-text'])) ? $inbound_settings['leads']['list-double-optin-button-text'] : __( 'Confirm List Opt In', 'inbound-pro');
                $confirm_show_lists = (isset($inbound_settings['leads']['list-double-optin-show-lists'])) ? $inbound_settings['leads']['list-double-optin-show-lists'] : 'on';
                $lead_confirmed_confirmation_message = (isset($inbound_settings['leads']['list-double-optin-confirmed-message'])) ? $inbound_settings['leads']['list-double-optin-confirmed-message'] : __( 'Thank You!', 'inbound-pro');
                $all_lists = (isset($inbound_settings['leads']['list-double-optin-all-list-text'])) ? $inbound_settings['leads']['list-double-optin-all-list-text'] : __( 'All Lists' , 'inbound-pro' );
               
            }
            
            
            if ( isset( $_GET['confirmed'] ) ) {
                $confirm =  "<span class='confirmed-message'>". $lead_confirmed_confirmation_message ."</span>";
                $confirm = apply_filters( 'wpleads/list-double-optin/confirmation-html' , $confirm );
                return $confirm;
            }
            
            if ( !isset( $_GET['token'] ) ) {
                return __( 'Invalid token' , 'inbound-pro' );
            }
            /* get all lead lists */
            $lead_lists = Inbound_Leads::get_lead_lists_as_array();
            /* decode token */
            $params = self::decode_confirm_token( sanitize_text_field($_GET['token']) );
            if ( !isset( $params['lead_id'] ) ) {
                return __( 'Oops. Something is wrong with the confirmation link. Are you logged in?' , 'inbound-pro' );
            }
            /* Begin confirm html inputs */
            $html = "<form action='?confirmed=true' name='confirm' method='post'>";
            $html .= "<input type='hidden' name='token' value='".strip_tags($_GET['token'])."' >";
            $html .= "<input type='hidden' name='action' value='inbound_confirm_event' >";

            /* loop through lists and show confirm inputs */ // uncommenting this shows only the lists that are encoded in the token
//            if ( isset($params['list_ids']) && $confirm_show_lists == 'on' ) {
//                foreach ($params['list_ids'] as $list_id ) {
            /*get all lists that the lead is waiting to opt into*/
            $lists_to_opt_into = get_post_meta($params['lead_id'], 'double_optin_lists', true);
            if ( !empty($lists_to_opt_into) && $confirm_show_lists == 'on' ) {
                foreach($lists_to_opt_into as $list_id){
                    if ($list_id == '-1' || !$list_id ) {
                        continue;
                    }
                    /* make sure not to reveal unrelated lists */
                    if (!has_term($list_id, 'wplead_list_category' , $params['lead_id'] )) {
                        $html .= "<span class='confirm-span'><label class='lead-list-label'><input type='checkbox' name='list_ids[]' value='" . $list_id . "' class='lead-list-class'> " . $lead_lists[$list_id] . '</label></span><br />';
                    }
                }
            }
            $html .= "<span class='confirm-span' style=" . ( $confirm_show_lists == 'off' ? 'display:none;' : '' ) . "><label class='lead-list-label'><input name='lists_all' type='checkbox' value='all' ". ( $confirm_show_lists == 'off' ? 'checked="true"' : '' ) ."> " . $all_lists . "</label></span>";
            $html .= "<div class='confirm-div confirm-options'>";
            $html .= "	<span class='confirm-action-label'>".$confirm_button_text .":</span>";
            $html .= "	<div class='confirm-button'>";
            $html .= "		<span class='unsub-span'>
                                <label class='confirm-label'>
                                    <input name='confirm' type='submit' value='". $confirm_button_text ."' class='inbound-button-submit inbound-submit-action'>
                                </label>
                            </span>";
            $html .= "	</div>";
            $html .= "</div>";
            $html .= "</form>";
            return $html;
                
                
        }


        /**
        * Creates the double optin confirmation link
        * The shorcode used by the user is: inbound-list-double-optin-link.
        * But Inbound_List_Double_Optin::add_confirm_link_shortcode_params trims the name to: list-double-optin-link.
        * Then it gets rendered.
        * The reason for this is so the shorcode isn't rendered until the atts have been added to it.
        */
        public static function process_list_confirm_link( $params ) {
            $params = shortcode_atts( array(
                'lead_id' => '',
                'list_ids' => '-1',
                'email_id' => '-1',
                'link_text' => '',
            ), $params, 'list-double-optin-link');
            /* check to see if lead id is set as a REQUEST */
            if ( isset($params['lead_id']) ) {
                $params['lead_id'] = intval($params['lead_id']);
            } else if ( isset($_REQUEST['lead_id']) ) {
                $params['lead_id'] = intval($_REQUEST['lead_id']);
            } else if ( isset($_COOKIE['wp_lead_id']) ) {
                $params['lead_id'] = intval($_COOKIE['wp_lead_id']);
            }
            /* Add variation id to confirm link */
            $params['variation_id'] = ( isset($_REQUEST['inbvid']) )  ? intval($_REQUEST['inbvid']) : intval(0);
            /* generate confirm link */
            $confirm_link =  self::generate_confirm_link( $params );
            return '<a href="'. $confirm_link . '">'. sanitize_text_field($params['link_text']) .'</a>';
        }



        /**
         *  Generates confirm url given lead id and lists
         *  @param ARRAY $params contains: lead_id (INT ), list_ids (MIXED), email_id (INT)
         *  @return STRING $confirm_url
         */
        public static function generate_confirm_link( $params ) {
            if (!isset($params['lead_id']) || !$params['lead_id']) {
                return __( '#confirm-not-available-in-online-mode' , 'inbound-pro' );
            }
            if (isset($_GET['lead_lists']) && !is_array($_GET['lead_lists'])){
                $params['list_ids'] = explode( ',' , $_GET['lead_lists']);
            } else if (isset($params['list_ids']) && !is_array($params['list_ids'])) {
                $params['list_ids'] = explode( ',' , $params['list_ids']);
            }
            $args = array_merge( $params , $_GET );
            $token = self::encode_confirm_token( $args );
 
            if(!defined('INBOUND_PRO_CURRENT_VERSION')){
                $double_optin_page_id = get_option('list-double-optin-page-id', '');
            }else{
                $settings = Inbound_Options_API::get_option('inbound-pro', 'settings', array());
                $double_optin_page_id = $settings['leads']['list-double-optin-page-id'];
            } 
    
            if ( empty($double_optin_page_id) )  {
                $post = get_page_by_title( __( 'Confirm Double Opt In' , 'inbound-pro' ) );
                $double_optin_page_id =  $post->ID;
            }
            $base_url = get_permalink( $double_optin_page_id  );
            return add_query_arg( array( 'token'=>$token ) , $base_url );
        }
        
        
        /**
         *  Encodes data into a confirm token
         *  @param ARRAY $params contains: lead_id (INT ), list_ids (MIXED), email_id (INT)
         *  @return INT $token
         */
        public static function encode_confirm_token( $params ) {
            unset($params['doing_wp_cron']);
            $json = json_encode($params);
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $encrypted_string =
                base64_encode(
                    trim(
                        mcrypt_encrypt(
                            MCRYPT_RIJNDAEL_256, substr( SECURE_AUTH_KEY , 0 , 16 )  , $json, MCRYPT_MODE_ECB, $iv
                        )
                    )
                );
            $decode_test = self::decode_confirm_token($encrypted_string);
            return  str_replace(array('+', '/', '='), array('-', '_', '^'), $encrypted_string);
        }


        /**
         *  Decodes confirm token into an array of parameters
         *  @param STRING $reader_id Encoded lead id.
         *  @return ARRAY $confirm array of confirmation data
         */
        public static function decode_confirm_token( $token ) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $decrypted_string =
                trim(
                    mcrypt_decrypt(
                        MCRYPT_RIJNDAEL_256 ,  substr( SECURE_AUTH_KEY , 0 , 16 )   ,  base64_decode( str_replace(array('-', '_', '^'), array('+', '/', '='), $token ) ) , MCRYPT_MODE_ECB, $iv
                    )
                );
            return json_decode($decrypted_string , true);
        }

        /**
         * Adds the lead to lists he selected when filling out the confirmation form.
         * If all_lists was selected, all lists currently waiting for confirmation will be selected and the lead will be added to those.
         */
        public static function confirm_being_added_to_lists($params, $all = false){
            
            /*get the double optin waiting list id*/
            if(!defined('INBOUND_PRO_CURRENT_VERSION')){
                $double_optin_list_id = get_option('list-double-optin-list-id', '');
            }else{
                $settings = Inbound_Options_API::get_option('inbound-pro', 'settings', array());
                $double_optin_list_id = $settings['leads']['list-double-optin-list-id'];
            }


            /*get the lists waiting to be opted into*/
            $stored_double_optin_lists = get_post_meta($params['lead_id'], 'double_optin_lists', true);
            
            /*if there aren't any lists, exit*/
            if(empty($stored_double_optin_lists)){
                return;
            }
            
            /*if opt into all lists has been selected, set list ids to all stored list ids*/
            if($all){
                $params['list_ids'] = $stored_double_optin_lists;
            }
            
            /**for each supplied list, add the lead to the list. 
             * And remove the list id from the array of lists needing to be opted into**/
            foreach($params['list_ids'] as $list_id){
                Inbound_Leads::add_lead_to_list($params['lead_id'], $list_id);
                
                if(in_array($list_id, $stored_double_optin_lists)){
                    $index = array_search($list_id, $stored_double_optin_lists);
                    unset($stored_double_optin_lists[$index]);
                }
            }

            /**if there are still lists awaiting double optin confirmation after the "waiting" meta listing has been updated**/
            if(!empty($stored_double_optin_lists)){
                /*update the "waiting" meta listing with the remaining lists*/
                update_post_meta($params['lead_id'], 'double_optin_lists', array_values($stored_double_optin_lists));
            }else{
            /**if there are no lists awaiting double optin confirmation**/
                /*remove the meta listing for double optin*/
                delete_post_meta($params['lead_id'], 'double_optin_lists');
                /*remove this lead from the double optin list*/
                wp_remove_object_terms($params['lead_id'], $double_optin_list_id, 'wplead_list_category');
                /*update the lead status*/
                update_post_meta( $params['lead_id'], 'wp_lead_status', 'active');            
            }
        }
    }
    new Inbound_Confirm_Double_Optin;

}

?>
