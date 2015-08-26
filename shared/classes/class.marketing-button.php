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
    }
    static function inbound_marketing_button() {
        global $pagenow, $typenow, $wp_version;
        $output = '';
        /** Only run in post/page creation and edit screens */
        if (in_array($pagenow, array('post.php','page.php','post-new.php','post-edit.php' ))) {
            /* check current WP version */
            if ( version_compare( $wp_version, '3.5', '<' ) ) {
                $img = '<img src="assets/images/edd-media.png" />';
                $output = '<a href="" class="">' . $img . '</a>';
            } else {
                $img = '<span class="wp-media-buttons-icon" id="edd-media-button"></span>';
                $output = '<a href="" class="button">Button</a>';
            }
        }
        echo $output;
    }

    static function for_popup() {
        global $pagenow, $typenow;
        // Only run in post/page creation and edit screens
        if (in_array($pagenow, array('post.php','page.php','post-new.php','post-edit.php'))) { ?>

            <div id="inbound-marketing-popup" style="display: none;">
                <div class="wrap" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                    MARKETING BUTTON SHIT HERE
                </div>
            </div>
        <?php
        }
    }
}
$Inbound_Marketing_Button = new Inbound_Marketing_Button();