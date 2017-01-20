<?php

if(!class_exists('Inbound_List_Double_Optin')){
    
    class Inbound_List_Double_Optin{
        
        function __construct(){
            self::add_hooks();
        }
        
        public static function add_hooks(){
            /* Add the waiting for double optin list */
            add_action('wplead_list_category_add_form', array(__CLASS__, 'create_double_optin_waiting_list'));
            
            /* Modify the Edit lead list page */
            add_action('wplead_list_category_edit_form_fields', array(__CLASS__, 'lead_list_edit_form_fields'));
            
            /* Filter hook for processing the link token */
            add_filter('process_inbound_list_double_optin_confirm_link', array(__CLASS__, 'add_confirm_link_shortcode_params'), 10, 2);		

            add_action('add_lead_to_lead_list', array(__CLASS__, 'remove_from_double_optin_list'), 10, 2);
        }
        
        /** 
         * Create Double Optin List
         */
        public static function create_double_optin_waiting_list(){

            /*get the double optin waiting list id*/
            if(!defined('INBOUND_PRO_CURRENT_VERSION')){
                $double_optin_list_id = get_option('list-double-optin-list-id', '');
            }else{
                $settings = Inbound_Options_API::get_option('inbound-pro', 'settings', array());
                $double_optin_list_id = $settings['leads']['list-double-optin-list-id'];
            }

            // If the list doesn't already exist, create it
            if( false == get_term_by('id', $double_optin_list_id, 'wplead_list_category')) {
                $name = __( 'Waiting for Double Opt-in Confirmation' , 'inbound-pro' );
                // Set the list ID so that we know the post was created successfully
                $term_id = wp_insert_term($name, 'wplead_list_category');
               
                update_option('list-double-optin-list-id', $term_id['term_id']);
                $settings = Inbound_Options_API::get_option('inbound-pro', 'settings');
                $settings['leads']['list-double-optin-list-id'] = $term_id['term_id'];
                Inbound_Options_API::update_option('inbound-pro', 'settings', $settings);
            
            }
        }        
        
        public static function lead_list_edit_form_fields($list){
            /*get the double optin waiting list id*/
            if(!defined('INBOUND_PRO_CURRENT_VERSION')){
                $double_optin_list_id = get_option('list-double-optin-list-id', '');
            }else{
                $settings = Inbound_Options_API::get_option('inbound-pro', 'settings', array());
                $double_optin_list_id = $settings['leads']['list-double-optin-list-id'];
            }
            
            /*exit if this is the double optin list*/
            if($list->term_id == $double_optin_list_id){
                return;
            }
           
            $settings = get_term_meta($list->term_id, 'wplead_lead_list_meta_settings', true);
            if(empty($settings)){
                /*if there are no settings in the meta, push the default double_optin value*/
                update_term_meta($list->term_id, 'wplead_lead_list_meta_settings', array('double_optin' => 0));
            }else if(!array_key_exists('double_optin', $settings)){
                /*if there are settings, but double_optin isn't there, push the default value*/
                $settings['double_optin'] = 0;
                update_term_meta($list->term_id, 'wplead_lead_list_meta_settings', $settings);
            }
            ?>
            <tr class="form-field inboundnow-list-options">
                <th valign="top" scope="row">
                    <label>Double Opt-in</label>
                </th>
                <td>
                    <select id="inbound-list-double-optin-select" class="inboundnow-lead-list-option" name="double_optin" title="<?php _e('Enable list Double Opt In to send leads an email requesting consent to being added to a list.','inbound-pro');?>"><option value="0">Double Opt In Disabled</option><option value="1">Double Opt In Enabled</option></select>
                </td>
            </tr>
            </table>
            <!--email response input-->
            <div class="inbound-list-double-optin-enabled">
                <h2><?php _e( 'Set the double opt in response' , 'inbound-pro' ); ?></h2>
                <div style='display:block; overflow: auto;'>
                    <div id='email-confirm-settings'>
                        <label for="inbound_email_send"><?php _e('Send double opt in confirmation email' , 'inbound-pro'); ?> </label><br />
                        <select class="inboundnow-lead-list-option" name="inbound_list_double_optin_send_notification" id="inbound_list_double_optin_send_notification" title="<?php _e('Select whether this list should send a double opt in request email to leads. If you use multiple double opt in enabled lists on the same Inbound form, you may want to only have one of those lists send the request email.','inbound-pro'); ?>">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            if(defined('INBOUND_PRO_CURRENT_VERSION')){
                $emails = Inbound_Mailer_Post_Type::get_automation_emails_as( 'ARRAY' );
                if (!$emails) {
                    $emails[] = __( 'No Automation emails detected. Please create an automated email first.' , 'inbound-pro' );
                }
            }
            ?>
            <div style="overflow: auto;" id="inbound-list-double-optin-email-response-template">
                <div id=''>
                    <label for="inbound_list_double_optin_email_template"><?php _e( 'Select Response Email Template' , 'inbound-pro' ); ?></label><br />
                    <select class="inboundnow-lead-list-option" name="inbound_list_double_optin_email_template" id="inbound_list_double_optin_email_template">
                        <option value='custom'><?php _e( 'Use a custom email' , 'inbound-pro' ); ?></option>
                        <?php
                        foreach ($emails as $id => $label) {
                            echo '<option value="'.$id.'">'.$label.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <br />
            <div class="inbound-list-use-custom-email-template">
                <table class='widefat tokens' class="">
                    <tr><td>
                    <h2>Available Dynamic Email Tokens</h2>
                    <ul id="email-token-list">
                        <li class='core_token list_email_token' title="<?php _e('Email address of sender', 'inbound-pro');?>" >{{admin-email-address}}</li>
                        <li class='core_token list_email_token' title="<?php _e('Name of this website', 'inbound-pro');?>" >{{site-name}}</li>
                        <li class='core_token list_email_token' title="<?php _e('URL of this website', 'inbound-pro');?>" >{{site-url}}</li>
                        <li class='core_token list_email_token' title="<?php _e('Datetime of Sent Email.', 'inbound-pro');?>" >{{date-time}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('First & Last name of recipient', 'inbound-pro');?>" >{{lead-full-name}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('First name of recipient', 'inbound-pro');?>" >{{lead-first-name}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('Last name of recipient', 'inbound-pro');?>" >{{lead-last-name}}</li>

                        <li class='lead_token list_email_token' title="<?php _e('Email address of recipient', 'inbound-pro');?>" >{{lead-email-address}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('Company Name of recipient', 'inbound-pro');?>" >{{lead-company-name}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('Address Line 1 of recipient', 'inbound-pro');?>" >{{lead-address-line-1}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('Address Line 2 of recipient', 'inbound-pro');?>" >{{lead-address-line-2}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('City of recipient', 'inbound-pro');?>" >{{lead-city}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('Name of Inbound Now form user converted on', 'inbound-pro');?>" >{{form-name}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('Page the visitor singed-up on.', 'inbound-pro');?>" >{{source}}</li>
                        <li class='lead_token list_email_token' title="<?php _e('This is the link to the double opt in confirmation page.', 'inbound-pro');?>" >[inbound-list-double-optin-link link_text="<?php _e('Please confirm being put on our mailing list', 'inbound-pro'); ?>"]</li>
                    </ul>
                    </td>
                    </tr>
                </table><br />
                <?php $email_subject = (isset($settings['inbound_confirmation_subject'])) ? $settings['inbound_confirmation_subject'] : '';?>
                <input type="text" class="inboundnow-lead-list-option inbound-list-use-custom-email-template" name="inbound_confirmation_subject" placeholder="Email Subject Line" size="30" value="<?php echo $email_subject;?>" id="inbound_confirmation_subject" autocomplete="off" style="font-size: 22px;">
                <br />
                </div>	
                <div id="inbound-email-response-text-editor" class="">
                    <?php 
                    $editor_settings = array('tinymce' => array('content_style' => '#tinymce.mce-content-body.inbound_email_response_editor.locale-en-us.mceContentBody.webkit.wp-editor.html5-captions{max-width:100%!important;}'),);
                        $default_content = (isset($settings['is_html_inbound-email-response-text-data-input'])) ? $settings['is_html_inbound-email-response-text-data-input'] : __('Use the to create a double optin response for this lead list', 'inbound-pro');
                        $editor_id = 'inbound_email_response_editor';
                        wp_editor($default_content, $editor_id, $editor_settings); 

                    ?>
                </div>
                <input type="text" class="hidden inboundnow-lead-list-option" id="is_html_inbound-email-response-text-data-input" name="is_html_inbound-email-response-text-data-input" value=""/>
            </div>
            <style type="text/css">
                .inbound-list-double-optin-enabled,.inbound-list-use-custom-email-template,
                #inbound-list-double-optin-email-response-template{
                    display: none;
                }
                
                /*use visibilty to hide the editor. display:none; squishes the window*/
                #inbound-email-response-text-editor{
                    visibility: hidden;
                }
                
                #inbound_email_response_editor_ifr #tinymce.mce-content-body.inbound_email_response_editor.locale-en-us.mceContentBody.webkit.wp-editor.html5-captions{
                    max-width: 10000px !important;
                }
                
                inbound_email_response_editor{
                    display: inline;
                    max-width: 10000px;
                }
                
                .list_email_token{
                    display: inline-block;
                    width: 30%;
                }
                
                #wpfooter{
                    position: initial;
                }
            </style>
                
            <script>
                jQuery(document).ready(function(){
                    
                    function getTinymceContent(){
                        if (jQuery("#wp-inbound_email_response_editor-wrap").hasClass("tmce-active")){
                            return tinyMCE.activeEditor.getContent();
                        }else{
                            return jQuery('textarea#inbound_email_response_editor').val();
                        }
                    }
                    
                    function setEditorInputValue(){
                        jQuery('#is_html_inbound-email-response-text-data-input').val(getTinymceContent());
                        console.log(jQuery('#is_html_inbound-email-response-text-data-input').val());
                    }
                    
                    /*update the hidden editor data input with the editor text on submit*/
                    jQuery('input#submit').on('click', function(){
                        jQuery('#is_html_inbound-email-response-text-data-input').val(getTinymceContent());
                    });
                    
                    /**change the css visibilites of elements**/
                    /*if the double optin status has changed*/
                    jQuery('#inbound-list-double-optin-select').on('change', function(){
                        /*if double optin has been enabled*/
                        if(jQuery('#inbound-list-double-optin-select').val() == '1'){
                            jQuery('.inbound-list-double-optin-enabled').css({'display' : 'initial'});
                            
                            /*if to send an email notification has been sent*/
                            if(jQuery('#inbound_list_double_optin_send_notification').val() == '1'){
                                jQuery('#inbound-list-double-optin-email-response-template').css({'display' : 'initial'});
                                
                                if(jQuery('#inbound_list_double_optin_email_template').val() == 'custom'){
                                    jQuery('.inbound-list-use-custom-email-template,#inbound-email-response-text-editor').css({'display' : 'initial'});
                                }else{
                                    jQuery('.inbound-list-use-custom-email-template,#inbound-email-response-text-editor').css({'display' : 'none'});
                                }
                                
                            }else{
                            /*if no notification is to be sent, hide the editor*/
                                jQuery('#inbound-email-response-text-editor').css({'display': 'none'});
                            }
                        }else{
                        /*if double optin has been disabled*/
                            jQuery('.inbound-list-double-optin-enabled,#inbound-list-double-optin-email-response-template,\
                                    .inbound-list-use-custom-email-template,#inbound-email-response-text-editor').css({'display' : 'none'});
                        }
                    });
                    
                    /*if "send double optin confirmation email" has changed*/
                    jQuery('#inbound_list_double_optin_send_notification').on('change', function(){
                        /*if to notify has been selected*/
                        if(jQuery('#inbound_list_double_optin_send_notification').val() == '1'){
                          
                            /*display the email template selector*/
                            jQuery('#inbound-list-double-optin-email-response-template').css({'display' : 'initial'});
                          
                            /*if the previously selected email template is a custom one*/
                            if(jQuery('#inbound_list_double_optin_email_template').val() == 'custom'){
                                /*display all the inputs required for making a custom email*/
                                jQuery('.inbound-list-use-custom-email-template,#inbound-email-response-text-editor').css({'display' : 'initial'});
                            }

                        }else{
                        /*if no notification is to be sent, hide the inputs*/
                            jQuery('#inbound-list-double-optin-email-response-template,.inbound-list-use-custom-email-template,\
                                    #inbound-email-response-text-editor').css({'display' : 'none'});
                        }
                    });
                    
                    /*if the email template to send has changed*/
                    jQuery('#inbound_list_double_optin_email_template').on('change', function(){
                        
                        /*if the email to send is a custom one, show the custom inputs*/
                        if(jQuery('#inbound_list_double_optin_email_template').val() == 'custom'){
                            jQuery('.inbound-list-use-custom-email-template,#inbound-email-response-text-editor').css({'display' : 'initial'});
                        }else{
                        /*if the email is an automated one, hide the custom inputs*/
                            jQuery('.inbound-list-use-custom-email-template,#inbound-email-response-text-editor').css({'display' : 'none'});
                        }  
                    });
                    
                    /*trigger a refresh of the email inputs just after the page is loaded*/
                    setTimeout(function(){
                        jQuery('#inbound-list-double-optin-select').trigger('change');
                        /*change the visibility of the editor.*/
                        jQuery('#inbound-email-response-text-editor').css({'visibility': 'visible'});
                    }, 250);
                    
                });
            </script>
            
            <?php
        }

        /**
         *  Sends A Double Optin Confirmation to Lead After Conversion
         */
        public static function send_double_optin_confirmation($lead) {

            /*get the lists*/
            $lists = get_post_meta($lead['id'], 'double_optin_lists', true);
            
            /*exit if there aren't any lists*/
            if(!isset($lists) || empty($lists)){
                return;
            }

            /**setup the lead data for the templating engine**/
            $lead_data;
            $lead_data['wpleads_email_address'] = $lead['email'];
            /*process the mapped fields*/
            parse_str($lead['mapped_params'], $mapped);
            foreach($mapped as $key=>$value){
                if(!isset($lead_data[$key])){
                    $lead_data[$key] = $value;
                }
            }			
            /*process the raw params*/
            parse_str($lead['raw_params'], $raw_params);
            foreach($raw_params as $key=>$value){
                if(!isset($lead_data[$key])){
                    $lead_data[$key] = $value;
                }
            }
            /*add remaining fields to lead_data*/
            foreach($lead as $key=>$value){
                if(!isset($lead_data[$key])){
                    $lead_data[$key] = $value;
                }
            }

            /*get the form meta*/
            $form_meta_data = get_post_meta($lead_data['inbound_form_id']);
            
            /*set the post id*/
            $form_meta_data['post_id'] = $lead_data['inbound_form_id'];

            /* Get Lead Email Address */
            $lead_email = Inbound_Forms::get_email_from_post_data($lead_data);

            /*exit if there's no email*/
            if (!$lead_email) {
                return;
            }


            $Inbound_Templating_Engine = Inbound_Templating_Engine();
            $form_id = $form_meta_data['post_id'];

            /* Rebuild Form Meta Data to Load Single Values	*/
            foreach ($form_meta_data as $key => $value) {
                $form_meta_data[$key] = $value[0];
            }

            /**Loop through the double optin lists the lead has waiting for a response. 
             * 
             * If the response email is an automated one, shoot it off here.
             * If it's a custom template, add it to the email_contents array to be processed further down the page**/
            $email_contents = array();
            foreach($lists as $list_id){
                $list_settings = get_term_meta((int)$list_id, 'wplead_lead_list_meta_settings', true);

                /*skip the current list if it isn't supposed to send notifications*/
                if($list_settings['inbound_list_double_optin_send_notification'] != '1'){
                    continue;
                }
             
                /* if there is a double optin email template and its not a custom one */
                if ( !empty($list_settings['inbound_list_double_optin_email_template']) && $list_settings['inbound_list_double_optin_email_template'] != 'custom' ) {
                    $vid = Inbound_Mailer_Variations::get_next_variant_marker( $list_settings['inbound_list_double_optin_email_template'] );

                    $args = array(
                        'email_id' => $list_settings['inbound_list_double_optin_email_template'],
                        'vid' => $vid,
                        'email_address' => $lead_email,
                        'lead_id' => $lead_data['id'],
                        'tags' => array('inbound-forms'),
                        'lead_lists' => $lists,
                    );
                    
                    $response = Inbound_Mail_Daemon::send_solo_email( $args ); 
                
                }else if($list_settings['inbound_list_double_optin_email_template'] == 'custom' && !empty($list_settings['is_html_inbound-email-response-text-data-input'])){
                /* if there is an email template and it's a custom one*/
                    
                    /*add the email to the queue of emails to send*/
                    $email_contents[$list_id]['email_subject'] = $list_settings['inbound_confirmation_subject'];
                    $email_contents[$list_id]['email_content'] = $list_settings['is_html_inbound-email-response-text-data-input'];
                }
            }

            /*exit if there are no response emails*/
            if(empty(array_filter($email_contents))){
                return;
            }

            foreach($email_contents as $list_id => $email_content){  

                $content = $email_content['email_content'];
                $confirm_subject = $email_content['email_subject'];
             
                $args  =  array('lead_id' => $lead['id'],
                                'list_ids' => $lists,
                                'email_id' => $lead_email);

                $content = self::add_confirm_link_shortcode_params($content, $args);
                
                $content = apply_filters('the_content', $content);
                $content = str_replace(']]>', ']]&gt;', $content);

                $confirm_email_message = $content;

                $confirm_subject = apply_filters('inbound_lead_conversion/subject', $confirm_subject, $form_meta_data, $lead_data);
                $confirm_email_message = apply_filters('inbound_lead_conversion/body', $confirm_email_message, $form_meta_data, $lead_data);
                $confirm_subject = $Inbound_Templating_Engine->replace_tokens($confirm_subject, array($lead_data, $form_meta_data));

                /* add default subject if empty */
                if (!$confirm_subject) {
                    $confirm_subject = __('Thank you!', 'inbound-pro');
                }

                $confirm_email_message = $Inbound_Templating_Engine->replace_tokens($confirm_email_message, array($lead_data, $form_meta_data));


                $from_name = get_option('blogname', '');
                $from_email = get_option('admin_email');

                $headers = "From: " . $from_name . " <" . $from_email . ">\n";
                $headers .= 'Content-type: text/html';
                $headers = apply_filters('list_double_optin_lead_conversion/headers', $headers);

			$log_file = fopen('C:\\xampp\\htdocs\\inbound-site-3\\wordpress\\text.txt', 'a');
			fwrite($log_file, print_r($confirm_email_message, true));
			fwrite($log_file, print_r('-----------------------------------$confirm_email_message---------------------------', true) );
		//	fwrite($log_file, print_r($post_id, true));
			fwrite($log_file, print_r('*************************************************************', true) );
			fclose($log_file);

                wp_mail($lead_email, $confirm_subject, $confirm_email_message, $headers);
           }

        }
        
        /**
         * Adds the lead id, the ids of the lists for the lead to confirm, and the lead's email to the shortcode
         * Also removes all text except the link text from the shortcode
         * params: $content: HTML string; the email content, $args : array(lead_id, email_id, list_ids); args to add to the shortcode. 
         * 
         * This could be reformatted into a general shortcode search and replace function
         */
        public static function add_confirm_link_shortcode_params($content, $args){
            //regex for finding shortcodes, from https://core.trac.wordpress.org/browser/tags/3.6.1/wp-includes/shortcodes.php#L211
            $shortcode_regex = '/\['                             // Opening bracket
                            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                            . "(inbound-list-double-optin-link)"         // 2: Shortcode name
                            . '(?![\\w-])'                       // Not followed by word character or hyphen
                            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
                            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
                            .     '(?:'
                            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
                            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
                            .     ')*?'
                            . ')'
                            . '(?:'
                            .     '(\\/)'                        // 4: Self closing tag ...
                            .     '\\]'                          // ... and closing bracket
                            . '|'
                            .     '\\]'                          // Closing bracket
                            .     '(?:'
                            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                            .             '[^\\[]*+'             // Not an opening bracket
                            .             '(?:'
                            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                            .                 '[^\\[]*+'         // Not an opening bracket
                            .             ')*+'
                            .         ')'
                            .         '\\[\\/\\2\\]'             // Closing shortcode tag
                            .     ')?'
                            . ')'
                            . '(\\]?)/';
          
            preg_match_all($shortcode_regex, $content, $matches);

            /**This adds the lead id, list ids, and lead email to each shortcode.  //There shouldn't be more than one shortcode though...
             * It also removes any atts other than the link text**/
            for($i = 0; $i < count($matches[0]); $i++){
                /*if the current shortcode is inbound-inbound-list-double-optin-link*/
                if($matches[2][$i] == 'inbound-list-double-optin-link'){
                    /*if link text has been specified*/
                    if(false !== strpos($matches[3][$i], 'link_text="')){
                        $start = strpos($matches[3][$i], 'link_text="') + strlen('link_text="');
                        $end = strpos($matches[3][$i], '"', $start);
                        $link_text = substr($matches[3][$i], $start, $end - $start);
                        
                        /*create the replacement shortcode*/
                        $replacement_shortcode = '[list-double-optin-link lead_id=' . (int)$args['lead_id'] . ' list_ids=' . implode(',', $args['list_ids']) . ' email_id=' . sanitize_email($args['email_id']) . ' link_text="' . sanitize_text_field($link_text) . '" ]';
                        
                        /*replace the old shortcode with the new one*/
                        $content = str_replace($matches[0][$i], $replacement_shortcode, $content);
                    }else{
                    /*if no link text has been specified*/
                        $replacement_shortcode = '[list-double-optin-link lead_id=' . (int)$args['lead_id'] . ' list_ids=' . implode(',', $args['list_ids']) . ' email_id=' . sanitize_email($args['email_id']) . ' link_text="' . __('Please confirm being added to our mailing list', 'inbound-pro') . '" ]';

                        $content = str_replace($matches[0][$i], $replacement_shortcode, $content);
                    }
                }
            }
            return $content;
        }
        
        /**
         * Removes leads from the waiting for double optin confirmation list if they've been added to lists directly
         */
        public static function remove_from_double_optin_list($lead_id, $list_ids){
            $double_optin_lists = get_post_meta($lead_id, 'double_optin_lists', true);

            /* exit if there's no double optin lists */
            if(empty($double_optin_lists)){
                return;
            }
            
            /* exit if the lead hasn't been added to a double optin list */
            if(!in_array($list_ids, $double_optin_lists)){
                return;
            }
            
            if(!is_array($list_ids)){
                $list_ids = array($list_ids);
            }
            
            
            foreach($list_ids as $list_id){
                if(in_array($list_id, $double_optin_lists)){
                    $index = array_search($list_id, $double_optin_lists);
                    unset($double_optin_lists[$index]);
                }
            } 

            /**if there are still lists awaiting double optin confirmation after list values have been removed**/
            if(!empty($double_optin_lists)){
                /*update the meta listing with the remaining lists*/
                update_post_meta($lead_id, 'double_optin_lists', array_values($double_optin_lists));
            }else{
            /**if there are no lists awaiting double optin confirmation**/
                
                /*get the double optin waiting list id*/
                if(!defined('INBOUND_PRO_CURRENT_VERSION')){
                    $double_optin_list_id = get_option('list-double-optin-list-id', '');
                }else{
                    $settings = Inbound_Options_API::get_option('inbound-pro', 'settings', array());
                    $double_optin_list_id = $settings['leads']['list-double-optin-list-id'];
                }
 
                /*remove the meta listing for double optin*/
                delete_post_meta($lead_id, 'double_optin_lists');
                /*remove this lead from the double optin list*/
                wp_remove_object_terms($lead_id, (int)$double_optin_list_id, 'wplead_list_category');
                /*update the lead status*/
                update_post_meta( $lead_id , 'wp_lead_status' , 'read');
            }
        
        }
        
        
        
        
        
    }

    new Inbound_List_Double_Optin;
}





?>
