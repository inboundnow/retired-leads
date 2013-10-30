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

 /* Initialize ajax.social-gate.js */
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
// $height = get_post_meta( $post_id, 'wp_cta_height-'.$var_id, true );
$height = $width * 0.315; // height controlled by width
$facebook_like_url = wp_cta_get_value($post, $key, 'facebook-like-url' );
$download_url = wp_cta_get_value($post, $key, 'download-url' );
$content_color = wp_cta_get_value($post, $key, 'content-color' );
$turn_off_editor = wp_cta_get_value($post, $key, 'turn-off-editor' );
$fb_app_id = wp_cta_get_value($post, $key, 'fb-app-id' );
$color = wp_cta_get_value($post, $key, 'color_scheme' );
$download_text = wp_cta_get_value($post, $key, 'download-url-text' );
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
  <link rel="stylesheet" href="<?php echo $path; ?>like-download-style.css">
  <style type="text/css">
body {margin: 0px; padding:0px;}
<?php
/* Height & Width CSS helper - Add to inline style tag */
if ( $width != "" ) {
echo ".css_element { width: ".$width."px;}";
}
if ( $height != "" ) {
echo ".css_element { height: ".$height."px;}";
}
if ($border_radius != "0"){
  echo "#content { border-radius: ".$border_radius."px;}";
}
/* Color Options CSS helper - Add to inline style tag */
if ( $content_color != "" ) {
echo "#content { background-color: #$content_color;}";
}
?>
</style>

<!-- Load Normal WordPress wp_head() function -->
<?php wp_head(); ?>
<!-- Load Landing Pages's custom pre-load hook for 3rd party plugin integration -->
<?php do_action('wp_cta_head'); ?>

</head>

<body class="pop-up-container lightbox-pop">

<div id="content" style="width:<?php echo $width;?>px; height:<?php echo $height;?>px; ">
<div id="Step1">

<div style="clear:both">
<div style="float:left;">
<?php
if ($color == "light") { ?>
<img src="<?php echo $path; ?>like-to-download-700.png" width='<?php echo $width;?>' height='<?php echo $height;?>' >

<? } else { ?>
<img src="<?php echo $path; ?>like-to-download-700-light.png" width='<?php echo $width;?>' height='<?php echo $height;?>' >
<?php }
 ?>
</div>
<?php
$margintop = $height * .35;
$marginwidth = $width * .33;
?>
<div style="margin-top:-<?php echo "$margintop"; ?>px; margin-left: <?php echo "$marginwidth"; ?>px; float: left;">

<?php $fbwidth = $width * .52;?>
<div id="fb-root"></div><script src="//connect.facebook.net/en_US/all.js#appId=<?php echo $the_fb_id;?>&amp;xfbml=1"></script><fb:like href="<?php echo $facebook_like_url; ?>" send="false" width="<?php echo $fbwidth;?>" height="30" show_faces="false" font=""></fb:like>

</div>
</div>
</div>
<div id="Step2" style="display: none">


</div>
<div id="Step3" style="display: none">
<?php
$downloadbuttonmargintop = $height * .33;
$downloadbuttonmarginleft = $width * .33;
?>
<center><a class="btn large primary" href="<?php echo $download_url; ?>" style="margin-top:<?php echo "$downloadbuttonmargintop"; ?>px" target="_blank"><?php echo $download_text;?></a></center>
</div>
<div id="fb-root"> </div>

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
      document.getElementById('Step1').style.display = 'none';
      document.getElementById('Step2').style.display = 'block';
      document.getElementById('Step3').style.display = 'none';
      fb_like_to_download_event();
      setTimeout('document.getElementById(\'Step1\').style.display = \'none\';document.getElementById(\'Step2\').style.display = \'none\';document.getElementById(\'Step3\').style.display = \'block\';', 500);
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