<?php
/* Inbound Now Debug Class
*
*  This class enabled users to dequeue third party javascript from pages to stop JS errors
*/

if (!defined('INBOUND_CLASS_URL'))
    define('INBOUND_CLASS_URL', plugin_dir_url(__FILE__));

if (!class_exists('InboundDebugScripts')) {
  class InboundDebugScripts {
    static $add_debug;

  /*  Contruct
   *  --------------------------------------------------------- */
    static function init() {
      self::$add_debug = true;

      add_action('wp_enqueue_scripts', array(__CLASS__, 'inbound_kill_bogus_scripts'), 100);
      add_action('wp_enqueue_scripts', array(__CLASS__, 'inbound_compatibilities'), 101);
      add_action('wp_ajax_inbound_dequeue_js', array(__CLASS__, 'inbound_dequeue_js'));
      add_action('wp_ajax_nopriv_inbound_dequeue_js', array(__CLASS__, 'inbound_dequeue_js'));
    }

    static function inbound_dequeue_js() {
      if ( ! self::$add_debug )
      return;

          // Post Values
          $post_id = (isset( $_POST['post_id'] )) ? $_POST['post_id'] : "";
          $the_script = (isset( $_POST['the_script'] )) ? $_POST['the_script'] : "";
          $status = (isset( $_POST['status'] )) ? $_POST['status'] : "";

          /* Store Script Data to Post */
        $script_data = get_post_meta( $post_id, 'inbound_dequeue_js', TRUE );
          $script_data = json_decode($script_data,true);
          if(is_array($script_data)) {

            if($status === 'off') {
              // add or remove from list
              $script_data[$the_script] = $status;
            } else {
              unset($script_data[$the_script]);
            }

        } else {
          // Create the first item in array
          if($status === 'off') {
          $script_data[$the_script] = $status;
          }
        }
          $script_save = json_encode($script_data);

          update_post_meta( $post_id, 'inbound_dequeue_js', $script_save );

        $output =  array('encode'=> $script_save );

        echo json_encode($output,JSON_FORCE_OBJECT);
        wp_die();
     }

    static function script_whitelist() {
        $white_list_scripts = array( "admin-bar",
                 "jquery",
                 "jquery-cookie",
                 "form-population",
                 "jquery-total-storage",
                 "inbound-shortcodes-plugins",
                 "inbound-shortcodes",
                 "store-lead-ajax",
                 "cta-view-track",
                 "funnel-tracking",
                 'cta-admin-bar',
                 "inbound-dequeue-scripts");

        return $white_list_scripts;
    }

    static function inbound_kill_bogus_scripts() {
        if (!isset($_GET['inbound-dequeue-scripts'])) {
          global $wp_scripts;
          $script_list = $wp_scripts->queue; // All enqueued scripts
          global $wp_query;
          $current_page_id = $wp_query->get_queried_object_id();
          $script_data = get_post_meta( $current_page_id , 'inbound_dequeue_js', TRUE );
          $script_data = json_decode($script_data,true);

          $white_list_scripts = self::script_whitelist();

          foreach ($script_list as $key => $value) {
           if (!in_array($value, $white_list_scripts)) {
             // Kill bad scripts
             if (isset($script_data[$value]) && in_array($script_data[$value], $script_data)) {
               wp_dequeue_script( $value ); // Kill bad script
             }
           }
          }
        }
    }

    static function inbound_compatibilities() {

      if (isset($_GET['inbound-dequeue-scripts'])) {
        if ( 'wp-call-to-action' == get_post_type() ) {
          //show_admin_bar( false );
          wp_enqueue_script('inbound-dequeue-scripts', INBOUND_CLASS_URL . 'js/inbound-dequeue-scripts.js', array( 'jquery' ));
          wp_localize_script( 'inbound-dequeue-scripts' , 'inbound_debug' , array( 'admin_url' => admin_url( 'admin-ajax.php' )));

            global $wp_scripts;
            $script_list = $wp_scripts->queue; // All enqueued scripts
            $white_list_scripts = self::script_whitelist();
            // TURN OFF ALL OTHER SCRIPTS FOR DISABLING
            foreach ($script_list as $key => $value) {
             //echo $key . $value;
             if (!in_array($value, $white_list_scripts)){
               wp_dequeue_script( $value );
             }

            }
            /*echo "<pre>";
             print_r($wp_scripts->queue);
             echo "</pre>"; */

             echo '<style type="text/css" media="screen">
             #group{position:relative;margin:0 auto;padding:6px 10px 10px;background-image:linear-gradient(top,rgba(255,255,255,.1),rgba(0,0,0,.1));background-color:#555;width:300px}#group:after{content:" ";position:absolute;z-index:1;top:0;left:0;right:0;bottom:0;border-radius:5px}.switch{position:relative;border:0;padding:0;width:245px;font-family:helvetica;font-weight:700;font-size:22px;color:#222;text-shadow:0 1px 0 rgba(255,255,255,.3)}.switch legend{float:left;width:40%;padding:7px 10% 3px 0;text-align:right}.switch input{position:absolute;opacity:0}.switch legend:after{content:"";position:absolute;top:0;left:50%;z-index:0;width:50%;height:100%;padding:2px;background-color:#222;border-radius:3px;box-shadow:inset -1px 2px 5px rgba(0,0,0,.8),0 1px 0 rgba(255,255,255,.2)}.switch label{position:relative;z-index:2;float:left;width:25%;margin-top:2px;padding:5px 0 3px;text-align:center;color:#64676b;text-shadow:0 1px 0 #000;cursor:pointer;transition:color 0s ease .1s}.switch input:checked+label{color:#fff}.switch input:focus+label{outline:0}.switch .switch-button{clear:both;position:absolute;top:-1px;left:50%;z-index:1;width:25%;height:100%;margin:2px;background-color:#70c66b;background-image:linear-gradient(top,rgba(255,255,255,.2),rgba(0,0,0,0));border-radius:3px;border:1px dashed rgba(0,0,0,.3);box-shadow:0 0 0 3px #70c66b,-2px 3px 5px #000;transition:all .3s ease-out}.switch .switch-button:after{content:" ";position:absolute;z-index:1;top:0;left:0;right:0;bottom:0;border-radius:3px;border:1px dashed #fff}#inbound-dequeue-id{display:none}.switch input:last-of-type:checked~.switch-button{left:75%}.switch .switch-button.status-off{background-color:red;box-shadow:0 0 0 3px red,-2px 3px 5px #000}.switch label.turn-on{color:#fff}
            </style>';
    global $wp_query;
    $current_page_id = $wp_query->get_queried_object_id();
      $script_data = get_post_meta( $current_page_id , 'inbound_dequeue_js', TRUE );
        $script_data = json_decode($script_data,true);


             echo '<div id="inbound-fix-page" class="'.$current_page_id.'" style="position:fixed; right:0px; overflow:auto; height: 100%; top: 32px; background:#fff; border: 1px solid;">';
             echo "<span id='inbound-dequeue-id'>".$current_page_id."</span>";

             foreach ($script_list as $key => $value) {
              if (!in_array($value, $white_list_scripts)){
              $checked =  "";
              $status_class = "";
                // Kill bad script
                if (isset($script_data[$value]) && in_array($script_data[$value], $script_data)){
                  $checked =  "checked";
                  $status_class =  "status-off";
                  wp_dequeue_script( $value ); // Kill bad script
                }
              echo "Script: ". $value ."<br>";
              echo '<div id="group">
                  <fieldset class="switch" id="'.$value.'">
                    <legend>Status: </legend>

                    <input id="'.$value.'-on" name="'.$value.'-status" type="radio" '.$checked.'>
                    <label for="'.$value.'-on" class="turn-on">On</label>

                    <input id="'.$value.'-off" name="'.$value.'-status" type="radio" '.$checked.'>
                    <label for="'.$value.'-off" class="turn-off">Off</label>

                    <span class="switch-button '.$status_class.'"></span>
                  </fieldset>
                  </div>';
              }
             }
             echo "</div>";

             // This will control the dequing
             /*
             foreach ($scripts_queued as $key => $value) {
              //echo $key . $value;
              if (!in_array($value, $white_list_scripts)){
                wp_dequeue_script( $value );
              }

             } */
        }

    }
}
}
}
/*  Initialize InboundNow Debug
 *  --------------------------------------------------------- */

InboundDebugScripts::init();

?>