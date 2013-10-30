<?php
/**
* Template Name:  Facebook like to download
*
* @package  WordPress Landing Pages
* @author   David Wells
* @link(homepage, http://www.inboundnow.com)
* @version  1.0
*/


/* Declare Template Key */
$key = wp_cta_get_parent_directory(dirname(__FILE__));
$path = WP_CTA_URLPATH.'templates/'.$key.'/';
$url = plugins_url();
/* Define Landing Pages's custom pre-load hook for 3rd party plugin integration */
do_action('wp_cta_init');

// Enable tracking on Social Media Shares
add_action('wp_enqueue_scripts','wp_cta_like_to_download_register_ajax');
function wp_cta_like_to_download_register_ajax() {
  global $path;

  $current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
  // embed the javascript file that makes the AJAX request
  wp_enqueue_script( 'fb-like-ajax-request', $path . 'js/ajax.social-gate.js', array( 'jquery' ) );
  wp_localize_script( 'fb-like-ajax-request', 'wp_cta_settings', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'current_url' =>  $current_url,  'cta_link_rewrites' =>  'off') );
}

/* Load Regular WordPress $post data and start the loop */
if (have_posts()) : while (have_posts()) : the_post();
$post_id = get_the_ID();
$var_id=(isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';
$width = get_post_meta( $post_id, 'wp_cta_width-'.$var_id, true );
$height = get_post_meta( $post_id, 'wp_cta_height-'.$var_id, true );
$header_text = wp_cta_get_value($post, $key, 'header-text' );
$share_url = wp_cta_get_value($post, $key, 'share-url' );
$style = wp_cta_get_value($post, $key, 'style' );
$content_color = wp_cta_get_value($post, $key, 'content-color' );
$text_color = wp_cta_get_value($post, $key, 'text-color' );
$fb_app_id = wp_cta_get_value($post, $key, 'fb-app-id' );
$border_radius = wp_cta_get_value($post, $key, 'border-radius' );

if(!empty($fb_app_id) && $fb_app_id != ""){
  $the_fb_id = $fb_app_id;
} else{
  $the_fb_id = "209205675779605";
}


?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <!--  Define page title -->
  <title><?php wp_title(); ?></title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <link rel="stylesheet" href="<?php echo $path; ?>css/style.css" />
  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <style type="text/css">
body {margin: 0px; padding:0px;}
<?php
if ($border_radius != "0"){
  echo "#content { border-radius: ".$border_radius."px;}";
}
/* Color Options CSS helper - Add to inline style tag */
if ( $content_color != "" ) {
echo "#content { background-color: #$content_color;}";
}
if ( $text_color != "" ) {
echo "#extra-text-area { color: #$text_color;}";
}
?>
#extra-text-area {
  text-align: center;
  text-shadow: none;
  font-size: 1.5em;
  line-height: 1.3em;

}
#inbound-share-model {
  text-align: center;
}
</style>

<!-- Load Normal WordPress wp_head() function -->
<?php wp_head(); ?>
<!-- Load Landing Pages's custom pre-load hook for 3rd party plugin integration -->
<?php do_action('wp_cta_head'); ?>

</head>

<body class="pop-up-container lightbox-pop">

<div id="content" style="width:<?php echo $width;?>px;height:<?php echo $height;?>px; margin: auto;">
  <div id="extra-text-area"><?php echo do_shortcode( $header_text );?></div>
  <div id="inbound-share-model">
   <img src="<?php echo $path; ?>img/<?php echo $style;?>.png" width="<?php echo $width;?>">
   <div id="fb-root"></div><script src="//connect.facebook.net/en_US/all.js#appId=<?php echo $the_fb_id;?>&amp;xfbml=1"></script><fb:like href="<?php echo $share_url; ?>" send="false" width="<?php echo $width * .90;?>" height="30" show_faces="false" font=""></fb:like>

   <script type="text/javascript">

   window.fbAsyncInit = function() {
       // init the FB JS SDK
       FB.init({
         appId      : '<?php echo $the_fb_id; ?>', // App ID from the App Dashboard
         status     : true, // check the login status upon init?
         cookie     : true, // set sessions cookies to allow your server to access the session?
         xfbml      : true  // parse XFBML tags on this page?
       });

       FB.Event.subscribe('edge.create', function(href, widget) {

         fb_like_to_download_event();

      });

     };

    (function(d, debug){
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
        ref.parentNode.insertBefore(js, ref);
      }(document, /*debug*/ false));
   </script>
     </div>
<?php
break;
endwhile; endif;
do_action('wp_cta_footer');
wp_footer();
?>
</body>