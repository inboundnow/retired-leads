<?php

/**
 * Creates Form Shortcode
 */

/* 
Usage
[inbound_form fields="First Name, Last Name, Email, Company, Phone" required="Company" textareas="Company"]
*/  

//=============================================
// Define constants
//=============================================
if (!defined('INBOUND_FORMS')) {
    define('INBOUND_FORMS', plugin_dir_url(__FILE__));
}
if (!defined('INBOUND_FORMS_PATH')) {
    define('INBOUND_FORMS_PATH', plugin_dir_path(__FILE__));
}
if (!defined('INBOUND_FORMS_BASENAME')) {
    define('INBOUND_FORMS_BASENAME', plugin_basename(__FILE__));
}
if (!defined('INBOUND_FORMS_ADMIN')) {
    define('INBOUND_FORMS_ADMIN', get_bloginfo('url') . "/wp-admin");
}
if (!class_exists('InboundForms')) {
class InboundForms {
    static $add_script;
    //=============================================
    // Hooks and Filters
    //=============================================
    static function init()  {
        add_shortcode('inbound_form', array(__CLASS__, 'inbound_forms_create'));
        add_action('init', array(__CLASS__, 'register_script'));
        add_action('wp_footer', array(__CLASS__, 'print_script'));
        add_action('wp_footer', array(__CLASS__, 'inline_my_script'));
    }

    // Shortcode params
    static function inbound_forms_create($atts) {
        self::$add_script = true;
        $email = get_option('admin_email');
        $args = shortcode_atts(array(
            'fields'     => 'email',
            'labels'     => 'Email',
            'required'     => 'email',
            'textareas' => '',
            'redirect_url'   => '',
            'notify' => $email,
            'button_text'     => 'Submit',
            'button_color'   => 'green',
        ), $atts);
        
        $content = self::embed_code($args);
        return $content;
    }

    // setup enqueue scripts
    static function register_script() {
    //wp_register_script('preloadify-js', plugins_url('/js/preloadify/jquery.preloadify.js', __FILE__), array('jquery'), '1.0', true);
    //wp_register_style( 'preloadify-css', plugins_url( '/inbound-forms/js/preloadify/plugin/css/style.css' ) );
    }

    // only call enqueue once
    static function print_script() {
    if ( ! self::$add_script )
      return;
    wp_print_scripts('preloadify-js');
    wp_enqueue_style( 'preloadify-css' );
     }

    // move to file 
    static function inline_my_script() {
      if ( ! self::$add_script )
      return;

    echo '<script>
          jQuery(document).ready(function($){ 
          
          function validateEmail(email) { 
            // http://stackoverflow.com/a/46181/11236
            
              var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(email);
          }

      

          // Turn off submit if no email present
          jQuery(".inbound-now-form").each(function(){
              var emailfield = $(this).find("input.inbound-email").val();
              if(emailfield === ""){
                $(this).find("#inbound_form_submit").attr("disabled","disabled");
              }
          });  
          
         // validate email
           $("input.inbound-email").keyup(function() {
               var email = $(this).val();
                if (validateEmail(email)) {
                  $(this).css("color", "green");
                  $(this).addClass("valid-email");
                  $(this).removeClass("invalid-email");
                } else {
                  $(this).css("color", "red");
                  $(this).addClass("invalid-email");
                  $(this).removeClass("valid-email");
                }
            if($(this).hasClass("valid-email")) {
                 $(this).parent().parent().find("#inbound_form_submit").removeAttr("disabled");
              }
           });

          });
          </script>';

    echo "<style type='text/css'>
          .inbound-field { margin-bottom:5px; }
          .inbound-label {min-width:90px; display:inline-block;}
          .inbound-label.text-area-on {vertical-align:top;}
          input.invalid-email {-webkit-box-shadow: 0 0 6px #F8B9B7;
                          -moz-box-shadow: 0 0 6px #f8b9b7;
                          box-shadow: 0 0 6px #F8B9B7;
                          color: #B94A48;
                          border-color: #E9322D;}
        input.valid-email {-webkit-box-shadow: 0 0 6px #B7F8BA;
                    -moz-box-shadow: 0 0 6px #f8b9b7;
                    box-shadow: 0 0 6px #98D398;
                    color: #008000;
                    border-color: #008000;}                
            </style>";
    }
    
    static function send_email(){
      // need action on post
      /* Not tested from: http://wp.tutsplus.com/tutorials/creative-coding/creating-a-simple-contact-form-for-simple-needs/
      // if the <form> element is POSTed, run the following code
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $error = false;
            // set the "required fields" to check
            $required_fields = array( "your_name", "email", "message", "subject" );
         
            // this part fetches everything that has been POSTed, sanitizes them and lets us use them as $form_data['subject']
            foreach ( $_POST as $field => $value ) {
                if ( get_magic_quotes_gpc() ) {
                    $value = stripslashes( $value );
                }
                $form_data[$field] = strip_tags( $value );
            }
         
            // if the required fields are empty, switch $error to TRUE and set the result text to the shortcode attribute named 'error_empty'
            foreach ( $required_fields as $required_field ) {
                $value = trim( $form_data[$required_field] );
                if ( empty( $value ) ) {
                    $error = true;
                    $result = $error_empty;
                }
            }
         
            // and if the e-mail is not valid, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'
            if ( ! is_email( $form_data['email'] ) ) {
                $error = true;
                $result = $error_noemail;
            }
         
            if ( $error == false ) {
                $email_subject = "[" . get_bloginfo( 'name' ) . "] " . $form_data['subject'];
                $email_message = $form_data['message'] . "\n\nIP: " . wptuts_get_the_ip();
                $headers  = "From: " . $form_data['name'] . " <" . $form_data['email'] . ">\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\n";
                $headers .= "Content-Transfer-Encoding: 8bit\n";
                wp_mail( $email, $email_subject, $email_message, $headers );
                $result = $success;
                $sent = true;
            }
            // but if $error is still FALSE, put together the POSTed variables and send the e-mail!
            if ( $error == false ) {
                // get the website's name and puts it in front of the subject
                $email_subject = "[" . get_bloginfo( 'name' ) . "] " . $form_data['subject'];
                // get the message from the form and add the IP address of the user below it
                $email_message = $form_data['message'] . "\n\nIP: " . wptuts_get_the_ip();
                // set the e-mail headers with the user's name, e-mail address and character encoding
                $headers  = "From: " . $form_data['your_name'] . " <" . $form_data['email'] . ">\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\n";
                $headers .= "Content-Transfer-Encoding: 8bit\n";
                // send the e-mail with the shortcode attribute named 'email' and the POSTed data
                wp_mail( $email, $email_subject, $email_message, $headers );
                // and set the result text to the shortcode attribute named 'success'
                $result = $success;
                // ...and switch the $sent variable to TRUE
                $sent = true;
            }
        }
        */
    }


    static function embed_code($args){
        extract($args);
        
        $form = '<!-- This Inbound Form is Automatically Tracked -->';
        $form .= '<form class="inbound-now-form wpl-track-me" method="post" action="' . get_permalink() . '">';
        // add nonce
        $fields_fields = explode(",", $fields);
        
        foreach ($fields_fields as $key => $value) {
          $req = '';
          $textarea_on = '';
          $type = 'text';
          (preg_match( '/Email|e-mail|email/i', $value, $email_input)) ? $email_input = " inbound-email" : $email_input = "";

          // Match Phone
          (preg_match( '/Phone|phone number|telephone/i', $value, $phone_input)) ? $phone_input = " inbound-phone" : $phone_input = "";
          
          // match name or first name. (minus: name=, last name, last_name,) 
          (preg_match( '/(?<!((last |last_)))name(?!\=)/im', $value, $first_name_input)) ? $first_name_input = " inbound-first-name" : $first_name_input =  "";

          // Match Last Name
          (preg_match( '/(?<!((first)))(last name|last_name|last)(?!\=)/im', $value, $last_name_input)) ? $last_name_input = " inbound-last-name" : $last_name_input =  "";
          

          if(stristr($value, $required) === FALSE) {
            $req = '';
          } else {
            $req = 'required';
          }
          
          if(stristr($value, $textareas) === FALSE) {
            $textarea_on = '';
          } else {
            $textarea_on = ' text-area-on';
          }

          
          if($email_input === " inbound-email"){
            $req = 'required';
            $type = 'email';
          }

           if($phone_input === " inbound-phone"){
            $type = 'tel';
          }
          
          $value = trim($value);
          $name = str_replace(array(' ','_'),'-',$value);
          $name = ucwords($name);
          $input_classes = $name . $email_input . $first_name_input . $last_name_input . $phone_input;
          $form .= '<div class="inbound-field '.$name.'">
                    <label class="inbound-label '.$name. $textarea_on.'" for="'.$name.'">' . $value . ':</label>';
          if($textarea_on === ' text-area-on') {
           // Textarea input 
           $form .=  '<textarea class="inbound-input inbound-input-textarea '.$input_classes.'" name="'.$name.'" id="in_'.$name.'" '.$req.'/></textarea>;';      
            } else {
           // Text input   
           $form .=  '<input class="inbound-input inbound-input-text '.$input_classes.'" type="'.$type.'" name="'.$name.'" id="in_'.$name.'" value="" '.$req.'/>';
          }           
          $form .= "</div>";
        }
        $form .= '<div>
                      <input type="submit" value="' . $button_text . '" name="send" id="inbound_form_submit" />
                  </div>
                  </form>';
        return $form;
    }

}
}

InboundForms::init();

?>