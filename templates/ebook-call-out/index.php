<?php
/**
* Template Name:  Blank Ebook
*
* @package  WordPress Landing Pages
* @author   David Wells
* @link(homepage, http://www.inboundnow.com)
* @version  1.0
* @example link to example page
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
$width = get_post_meta( $post_id, 'wp_cta_width-'.$var_id, true );
$height = get_post_meta( $post_id, 'wp_cta_height-'.$var_id, true );

$headline_text_color = wp_cta_get_value($post, $key, 'headline-text-color' ); 
$header_text = wp_cta_get_value($post, $key, 'header-text' );
$hero = wp_cta_get_value($post, $key, 'hero' );
$content_color = wp_cta_get_value($post, $key, 'content-color' ); 
$content_text_color = wp_cta_get_value($post, $key, 'content-text-color' ); 
$submit_button_color = wp_cta_get_value($post, $key, 'submit-button-color' ); 
$submit_button_text_color = wp_cta_get_value($post, $key, 'submit-button-text-color' ); 
$submit_button_text = wp_cta_get_value($post, $key, 'submit-button-text' );
$redirect = wp_cta_get_value($post, $key, 'redirect' );
$content = get_the_content();
$book_color = wp_cta_get_value($post, $key, 'book-color' );
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
  body{ padding: 0px; margin: 0px; margin: auto; font-family: Arial;}
#inbound-wrapper {
  width: 400px;
  height: 400px;
  padding: 0px;
  margin: 0px;
  padding-bottom: 0px;
}  
#inbound-hero {
  width: 100%;
float: left;
height: 400px;
margin-right: 0px;
margin-left: 0px;
position: relative;
}

.inbound-hero-img {
  position: absolute;
  z-index: 1;
  overflow: hidden;
width: 100%;
}
.inbound-logo {
position: absolute;
z-index: 10;
top: 18%;
width: 40%;
margin-left: 29%;
}
h1 {

}
p, li {
font-size: 14px;
line-height: 1.4;
margin: 0 0 1.4em;
}
li {
  margin-bottom: 0px;
}
.divider_line {
  clear: both;
}
.inbound-horizontal input {
margin-right: 10px;
padding: 3px;
padding-top: 2px;

}
.inbound-horizontal {
display: inline-block;
vertical-align: middle;
}
.inbound-horizontal label {
margin-right: 5px;
font-size: 20px;
vertical-align: middle;
}
input[type="submit"] {
background: #E14D4D;
border: none;
border-radius: 5px;
color: #FFF;
font-size: 23px;
font-weight: bold;
padding: 0px;
padding-left: 10px;
text-align: center;
vertical-align: top;
padding-right: 10px;
margin-bottom: 4px;
}

 input[type="submit"]:hover {
  background: #f15958;
}
.inbound-rotate {
    /* Safari */
-webkit-transform: rotate(14deg);

/* Firefox */
-moz-transform: rotate(14deg);

/* IE */
-ms-transform: rotate(14deg);

/* Opera */
-o-transform: rotate(14deg);

/* Internet Explorer */
filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}
h1 {
font-size: 1.2em;
position: absolute;
top: 39%;
width: 47%;
margin-left: 19%;
text-align: center;
z-index: 11;
}
#inbound-button {
background-color: #C8232B;
border: 1px solid rgba(0, 0, 0, 0.15);
-webkit-border-radius: 2px;
-moz-border-radius: 2px;
border-radius: 2px;
-webkit-box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.15), inset 1px 1px 1px rgba(255, 255, 255, 0.2);
-moz-box-shadow: 0px 2px 3px rgba(0,0,0,0.15), inset 1px 1px 1px rgba(255,255,255,0.2);
box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.15), inset 1px 1px 1px rgba(255, 255, 255, 0.2);
color: #FFF;
cursor: pointer;
display: inline-block;
font-family: inherit;
font-size: 14px;
font-weight: bold;
padding: 8px 15px;
text-shadow: -1px -1px 1px rgba(0, 0, 0, 0.15);
text-decoration: none;

z-index: 99;
}
#inbound-button .inbound-button-style {
  color:#fff;
  text-decoration: none;

}
#inbound-content {
  padding-left: 4%;
padding-right: 4%;
}
#inbound-content p:last-child{
  margin-bottom: 0px;
  padding-bottom: 0px;
}
<?php if ($width > 282 && $width < 310) { ?>
h1 {top: 32%;}
.inbound-logo { 
top: 14%;
  }
<?php } ?>
<?php if ($width > 310 && $width < 355) { ?>
h1 {top: 34%;}
<?php } ?>
<?php if ($width < 282 ) { ?>
h1 {top: 26%;}
.inbound-logo { 
top: 14%;
  }
<?php } ?>
<?php 
if ($width !="") {
          $new_width = $width . "px";
            echo "#inbound-wrapper {width: $new_width;}"; // change header color
}
if ($height !="") {
   $new_height = $height . "px";
            echo "#inbound-wrapper, #inbound-hero {height: $new_height;}"; // change header color
}
if ($content_color !="") {
            echo "#inbound-wrapper, #inbound-content {background-color: #$content_color;}"; // change header color
}
if ($content_text_color !="") {
            echo "#inbound-content {color: #$content_text_color;}";
            echo "#content, #content-wrapper p {color: #$content_text_color; }";
            echo ".inbound-horizontal label {color: #$content_text_color; }";
}
if ($headline_text_color != ""){
   echo "h1 {color:#$headline_text_color;}";
}
if ($submit_button_color != "") {
         echo "#inbound-button {background-color: #$submit_button_color !important;}";
          //echo".button { background: #$submit_button_color;}";
          //echo ".button:hover { background: $darker; border-bottom: 3px solid #DB3D3D;}";
          //regulr background: #DB3D3D; border-bottom: 3px solid #C12424;
          // hover .button:hover {background: #C12424;border-bottom: 3px solid #DB3D3D;}
}
if ($submit_button_text_color != "") {
          echo"#inbound-button .inbound-button-style { color: #$submit_button_text_color;}";
}
if ($headline_color != "") {
          echo"h1.inbound-rotate { color: #$headline_color;}";
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


<div id="inbound-hero">
<h1 class="inbound-rotate"><?php echo $header_text;?></h1>
<img class='inbound-logo inbound-rotate' src="<?php echo $hero;?>">
<img class='inbound-hero-img' src="<?php echo $path . "notepad-" . $book_color . ".png";?>">
<div id="inbound-button">
<a class="inbound-button-style" href="<?php echo $redirect;?>" target="_blank"><?php echo $submit_button_text;?></a>
</div>
</div> 
<div id="inbound-content">
<?php echo do_shortcode( $new_content  ); ?>
</div>
</div>


<?php 
break;
endwhile; endif; 
do_action('wp_cta_footer'); 
wp_footer();
?>  
<script type="text/javascript">
jQuery(document).ready(function($) {
   // put all your jQuery goodness in here.


  jQuery.fn.center = function ()
{
    this.css("position","fixed");
    this.css("top", ($(".inbound-hero-img").height()) + 5);
    this.css("left", ($("#inbound-wrapper").width() / 2) - (this.outerWidth() / 2));
    return this;
}

$('#inbound-button').center();
$(window).resize(function(){
   $('#inbound-button').center();
});
 });
</script>
</body>