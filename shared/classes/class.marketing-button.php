<?php
/**
 * Inbound Marketing Button in editor
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Inbound_Marketing_Button {

    public function __construct() {
        self::init();
    }

    public function init() {
        add_action('admin_enqueue_scripts', array(__CLASS__, 'load_marketing_button_js'), 101);
        add_action( 'media_buttons', array(__CLASS__, 'inbound_marketing_button'), 11);
        add_action( 'admin_footer', array(__CLASS__, 'for_popup'));
    }
    static function load_marketing_button_js() {
        wp_enqueue_script('inbound-marketing-button', INBOUNDNOW_SHARED_URLPATH . 'assets/js/admin/marketing-button.js');
        wp_enqueue_script('maginificient-popup', INBOUNDNOW_SHARED_URLPATH . 'assets/js/global/jquery.magnific-popup.min.js');
        wp_enqueue_style('maginificient-popup-css', INBOUNDNOW_SHARED_URLPATH . 'assets/css/magnific-popup.css');
    }
    /*
     There are two places the marketing button renders:
     in normal WP editors and via JS for ACF normal
     */
    static function inbound_marketing_button() {
        global $pagenow, $typenow, $wp_version;
        $output = '';
        /** Only run in post/page creation and edit screens */
        if (in_array($pagenow, array('post.php','page.php','post-new.php','post-edit.php' ))) {
            /* check current WP version */
            if ( version_compare( $wp_version, '3.5', '<' ) ) {
                $img = '<img width="20" height="20" src="'.INBOUNDNOW_SHARED_URLPATH.'assets/images/global/inbound-icon.png" />';
            } else {
                $img = '<span class="wp-media-buttons-icon" id="inboundnow-media-button"></span>';
            }
            $output = '<a style="padding-left: 3px;" href="#inbound-marketing-popup" class="open-marketing-button-popup button" class="button">'.$img.'Button</a>';
        }
        echo $output;
    }

    static function for_popup() {
        global $pagenow, $typenow;
        // Only run in post/page creation and edit screens
        if (in_array($pagenow, array('post.php','page.php','post-new.php','post-edit.php'))) { ?>
            <div id="inbound-marketing-popup" class="shortcode-popup-block mfp-hide">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vitae mauris arcu, eu pretium nisi. Praesent fringilla ornare ullamcorper. Pellentesque diam orci, sodales in blandit ut, placerat quis felis. Vestibulum at sem massa, in tempus nisi. Vivamus ut fermentum odio. Etiam porttitor faucibus volutpat. Vivamus vitae mi ligula, non hendrerit urna. Suspendisse potenti. Quisque eget massa a massa semper mollis.
            </div>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
               $('.open-marketing-button-popup').magnificPopup({
                 type:'inline',
                 midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
               });
             });
            </script>
        <?php
        }
    }
}
$Inbound_Marketing_Button = new Inbound_Marketing_Button();