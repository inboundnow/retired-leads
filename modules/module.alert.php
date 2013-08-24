<?php

/* Temporarily off** 
/* Template page notices 
function wp_cta_template_page_notice(){
    global $pagenow;
    global $current_user ;
    $page_string = isset($_GET["page"]) ? $_GET["page"] : "null";
    $user_id = $current_user->ID;
    if ( ! get_user_meta($user_id, 'wp_cta_template_page_notice') ) {    
        if ( ($pagenow == 'edit.php') && ($page_string == "wp_cta_manage_templates") ) {
             echo '<div class="updated">
                 <p>To add a new template to the landing page plugin. <strong>Click on "Add New Template" above</strong> (Video popout Link)  <a style="float:right;" href="?wp_cta_template_page_ignore=0">Hide This</a></p>
             </div>';
        }
    }
} 
add_action('admin_notices', 'wp_cta_template_page_notice'); 
add_action('admin_init', 'wp_cta_template_page_ignore');
function wp_cta_template_page_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['wp_cta_template_page_ignore']) && '0' == $_GET['wp_cta_template_page_ignore'] ) {
             add_user_meta($user_id, 'wp_cta_template_page_ignore', 'true', true);
    }
}
// Start Landing Page Welcome
add_action('admin_notices', 'wp_cta_activation_notice');
function wp_cta_activation_notice() {
    global $current_user ;
        $user_id = $current_user->ID;
    if ( ! get_user_meta($user_id, 'wp_cta_activation_ignore_notice') ) {
        echo '<div class="updated"><p>';
        echo "<a style='float:right;' href='?wp_cta_activation_message_ignore=0'>Dismiss This</a>Welcome to the WordPress Landing Page Plugin! Need help getting started? View the <strong>Quickstart Guide</strong><br>
        Want to get notified about WordPress Landing Page Plugin updates, new features, new landing page design templates, and add-ons? <br>
        Form here | ";
        echo "</p></div>";
    }
}
add_action('admin_init', 'wp_cta_activation_message_ignore');
function wp_cta_activation_message_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['wp_cta_activation_message_ignore']) && '0' == $_GET['wp_cta_activation_message_ignore'] ) {
             add_user_meta($user_id, 'wp_cta_activation_ignore_notice', 'true', true);
    }
} */
// End Landing Page Welcome

function wp_cta_template_page_get_more(){
    global $pagenow;  
    $page_string = isset($_GET["page"]) ? $_GET["page"] : "null";
        if ( (($pagenow == 'edit.php') && ($page_string == "wp_cta_manage_templates")) || (($pagenow == "post-new.php") &&  ($_GET['post_type'] == "wp-call-to-action")) ) {
             echo '<div id="more-templates" style="display:none;">
                 <a target="_blank" href="/wp-admin/edit.php?post_type=wp-call-to-action&page=wp_cta_store" class="button new-wp-cta-button button-primary button-large">Download Additional Call to Action Templates</a>
             </div><script type="text/javascript">jQuery(document).ready(function($) { var moretemp = jQuery("#more-templates");
jQuery("#bulk_actions").prepend(moretemp); jQuery(".wp-cta-selection-heading").append(moretemp); jQuery(".wp-cta-selection-heading #more-templates").css("float","right"); jQuery(moretemp).show(); });</script>';
        }
}
add_action('admin_notices', 'wp_cta_template_page_get_more');
/* End Template Notices */

function wp_cta_ab_notice(){
    global $pagenow;  
    $page_string = isset($_GET["page"]) ? $_GET["page"] : "null";
        if ( (($pagenow == 'edit.php') && ($page_string == "wp_cta_split_testing")) ) {
               echo '<div class="error"><p>';
        echo "<h3 style='font-weight:normal;'><strong>Please Note</strong> that this version 1 way of running Landing Page split tests will be phases out of the plugin soon.<br><br> Please use the <strong>new and improved A/B testing functionality</strong> directly in the landing page edit screen.";
        echo "</h3><h1><a href=\"#\" onClick=\"window.open('http://www.youtube.com/embed/KJ_EDJAvv9Y?autoplay=1','wp-call-to-action','width=640,height=480,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no')\">Watch Video Explanation</a></h1></p></div>";
        }
}
add_action('admin_notices', 'wp_cta_ab_notice');