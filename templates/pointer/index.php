<?php
/**
* Template Name:  Flat CTA Template
*
* @package  WordPress Landing Pages
* @author   David Wells
* @link(homepage, http://www.inboundnow.com)
* @version  1.0
* @example link to example page
*/

/** 
* Run the cta template option array through this to generate usable variable
* 
* $wp_cta_data[$key]['settings'] is in the config.php file of cta template.
* 
* In this example $options_array is the $wp_cta_data[$key]['settings'] array.
*/



/* Declare Template Key */
$key = wp_cta_get_parent_directory(dirname(__FILE__)); 
$path = WP_CTA_URLPATH.'templates/'.$key.'/';
$url = plugins_url();
/* Define Landing Pages's custom pre-load hook for 3rd party plugin integration */
do_action('wp_cta_init');

if ( isset($_GET['wp-cta-variation-id']) ){
  $var_id = $_GET['wp-cta-variation-id'];
} else {
   $var_id = 0;
}

/* Load Regular WordPress $post data and start the loop */
if (have_posts()) : while (have_posts()) : the_post();
$post_id = get_the_ID();
$width = get_post_meta( $post_id, 'wp_cta_width-'.$var_id, true ) . "px";
$height = get_post_meta( $post_id, 'wp_cta_height-'.$var_id, true ) . "px";


$header_text = wp_cta_get_value($post, $key, 'header-text' ); 
$sub_header_text = wp_cta_get_value($post, $key, 'sub-header-text' ); 
$text_color = wp_cta_get_value($post, $key, 'text-color' ); 
$content_color = wp_cta_get_value($post, $key, 'content-color' ); 
$submit_button_color = wp_cta_get_value($post, $key, 'submit-button-color' ); 
$submit_button_text = wp_cta_get_value($post, $key, 'submit-button-text' ); 
$content_text_color = wp_cta_get_value($post, $key, 'content-text-color' );
$link_url = wp_cta_get_value($post, $key, 'link_url' );


$content = get_the_content();
$new_content = wpautop($content);
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
  <style type="text/css">
@import url(http://fonts.googleapis.com/css?family=Nunito:300);

body { font-family: "Nunito", sans-serif; font-size: 24px; }
a    { text-decoration: none; }
p    { text-align: center; }
sup  { font-size: 36px; font-weight: 100; line-height: 55px; }

.button
{
  text-transform: uppercase;
  letter-spacing: 2px;
  text-align: center;
  color: #0C5;

  font-size: 24px;
  font-family: "Nunito", sans-serif;
  font-weight: 300;
  
display: block;
  

  
  padding: 20px 0;
  width: 220px;
  height:30px;

  background: #0D6;
  border: 1px solid #0D6;
  color: #FFF;
  overflow: hidden;
  
  transition: all 0.5s;
}

.button:hover, .button:active 
{
  text-decoration: none;
  color: #0C5;
  border-color: #0C5;
  background: #FFF;
}

.button span 
{
  display: inline-block;
  position: relative;
  padding-right: 0;
  
  transition: padding-right 0.5s;
}

.button span:after 
{
  content: ' ';  
  position: absolute;
  top: 0;
  right: -18px;
  opacity: 0;
  width: 10px;
  height: 10px;
  margin-top: -10px;

  background: rgba(0, 0, 0, 0);
  border: 3px solid #FFF;
  border-top: none;
  border-right: none;

  transition: opacity 0.5s, top 0.5s, right 0.5s;
  transform: rotate(-45deg);
}

.button:hover span, .button:active span 
{
  padding-right: 30px;
}

.button:hover span:after, .button:active span:after 
{
  transition: opacity 0.5s, top 0.5s, right 0.5s;
  opacity: 1;
  border-color: #0C5;
  right: 0;
  top: 50%;
}
<?php 
if ($width !="") {
            echo "#inbound-wrapper {width: $width;}"; // change header color
}
if ($height !="") {
            echo "#inbound-wrapper {height: $height;}"; // change header color
}
if ($content_color !="") {
            echo "#inbound-wrapper, body {background-color: #$content_color;}"; // change header color
}
if ($text_color != ""){
   echo "#header h1 {color:#$text_color;}";
}
if ($submit_button_color != "") {
          echo ".btn { border: 3px solid #$submit_button_color; color: #$submit_button_color;}";
          echo ".btn:hover, .btn:active { color: #$content_color; background: #$submit_button_color;}";
          //echo".button { background: #$submit_button_color;}";
          //echo ".button:hover { background: $darker; border-bottom: 3px solid #DB3D3D;}";
          //regulr background: #DB3D3D; border-bottom: 3px solid #C12424;
          // hover .button:hover {background: #C12424;border-bottom: 3px solid #DB3D3D;}
}
if ($content_text_color != ""){
   echo "#inbound-content p {color:#$content_text_color;}";
}
?> 
</style>

<!-- Load Normal WordPress wp_head() function -->
<?php wp_head(); ?> 
<!-- Load Landing Pages's custom pre-load hook for 3rd party plugin integration -->
<?php do_action('wp_cta_head'); ?>

</head>

<body class="pop-up-container lightbox-pop">



<div id="inbound-wrapper">

<p><?php echo $header_text; ?><br>
<?php echo do_shortcode( $new_content  ); ?>
<sup>&darr;</sup></p>
<a href="<?php echo $link_url;?>" class="button">
  <span><?php echo $submit_button_text; ?></span>
</a>

</div>



<?php 
break;
endwhile; endif; 
do_action('wp_cta_footer'); 
wp_footer();
?>  
</body>