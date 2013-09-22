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
  html.small-html {
    margin-top: 0px !important;
  }
  body{ padding: 0px; margin: 0px; margin: auto; font-family: Arial;}
#inbound-wrapper {
  width: 100%;
height: 100%;
min-height: 300px;
margin: 0;
position: absolute;
overflow: hidden;
font-family: Helvetica, Arial;
font-size: small;
background-color: #60BCF0;
color: #FFF;
}  
#inbound-hero {
  width: 35%;
float: left;
margin-right: 4%;
margin-left: 4%;
position: relative;
}
#inbound-content {
font-size: 97%;
opacity: 1;
padding: 0 0 0.6em 0.1em;
line-height: 1.6;
width: 82%;
margin: auto;
text-align: left;
}
.inbound-hero-img {
  width: 100%;
}

body {
width:100%;
height:100%;
min-height:500px;
margin:0;
position:absolute;
overflow:hidden;
font-family:Helvetica, Arial;
font-size:small;
background: #60BCF0;
color: #fff;
}

#header {
margin: 0 auto;
padding: 2em;
text-align: center;
padding-top: 25px;
}

#header h1 {
font-size: 2.625em;
line-height: 1.3;
margin: 0;
font-weight: 300;
}

#header span {
display: block;
font-size: 60%;
opacity: 0.7;
padding: 0 0 0.6em 0.1em;
}

.demos {
padding-top: 25px;
padding-bottom: 13px;
}

.btn:hover, .btn:active {
color: #60BCF0;
background: #fff;
}

.btn {
border: none;
font-family: inherit;
font-size: inherit;
color: inherit;
background: none;
cursor: pointer;
padding: 25px 40px;
display: inline-block;

text-transform: uppercase;
letter-spacing: 1px;
font-weight: 700;
outline: none;
position: relative;
-webkit-transition: all 0.3s;
-moz-transition: all 0.3s;
transition: all 0.3s;
}

.btn {
border: 3px solid #fff;
color: #fff;
}

body > div.container {
  padding: 50px 0;
  position: relative;
}
div.container iframe {
  position: absolute;
  top: 75px;
  right: 0;
}
h2 {
  margin-top: 40px;
  font-size: 14px;
  text-transform: uppercase;
}
p.spacer {
  height: 5px;
  overflow: hidden;
}
footer {
  padding: 20px 0;
    position: relative;
}
footer iframe {
  position: absolute;
  top: 20px;
  right: 0;
}
table td:first-child {
  width: 125px;
}
li strong {
  color: #111;
}
.btn.btn-large {
  float: right;
  margin-top: 40px;
}

.ui-ios-overlay {
  z-index: 99999;
  position: fixed;
  top: 50%;
  left: 50%;
  width: 200px;
  height: 200px;
  margin-left: -100px;
  margin-top: -100px;
  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#cc000000,endColorstr=#cc000000);
  background: rgba(0,0,0,0.8);
    -webkit-border-radius: 20px;
    -moz-border-radius: 20px;
    border-radius: 20px;
}
.ui-ios-overlay .title {
  color: #FFF;
  font-weight: bold;
  text-align: center;
  display: block;
  font-size: 26px;
  position: absolute;
  bottom: 30px;
  left: 0;
  width: 100%;
}
.ui-ios-overlay img {
  display: block;
  margin: 20% auto 0 auto;
}
.ui-ios-overlay .spinner {
  left: 50% !important;
  top: 40% !important;
}

.ios-overlay-show {
  -webkit-animation-name: ios-overlay-show;
  -webkit-animation-duration: 750ms;
  -moz-animation-name: ios-overlay-show;
  -moz-animation-duration: 750ms;
  -ms-animation-name: ios-overlay-show;
  -ms-animation-duration: 750ms;
  -o-animation-name: ios-overlay-show;
  -o-animation-duration: 750ms;
  animation-name: ios-overlay-show;
  animation-duration: 750ms;
}

@-webkit-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@-moz-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@-ms-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@-o-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}

.ios-overlay-hide {
  -webkit-animation-name: ios-overlay-hide;
  -webkit-animation-duration: 750ms;
  -webkit-animation-fill-mode: forwards;
  -moz-animation-name: ios-overlay-hide;
  -moz-animation-duration: 750ms;
  -moz-animation-fill-mode: forwards;
  -ms-animation-name: ios-overlay-hide;
  -ms-animation-duration: 750ms;
  -ms-animation-fill-mode: forwards;
  -o-animation-name: ios-overlay-hide;
  -o-animation-duration: 750ms;
  -o-animation-fill-mode: forwards;
  animation-name: ios-overlay-hide;
  animation-duration: 750ms;
  animation-fill-mode: forwards;
}

@-webkit-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@-moz-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@-ms-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@-o-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
.ui-ios-overlay {
  z-index: 99999;
  position: fixed;
  top: 50%;
  left: 50%;
  width: 200px;
  height: 200px;
  margin-left: -100px;
  margin-top: -100px;
  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#cc000000,endColorstr=#cc000000);
  background: rgba(0,0,0,0.8);
    -webkit-border-radius: 20px;
    -moz-border-radius: 20px;
    border-radius: 20px;
}
.ui-ios-overlay .title {
  color: #FFF;
  font-weight: bold;
  text-align: center;
  display: block;
  font-size: 26px;
  position: absolute;
  bottom: 30px;
  left: 0;
  width: 100%;
}
.ui-ios-overlay img {
  display: block;
  margin: 20% auto 0 auto;
}
.ui-ios-overlay .spinner {
  left: 50% !important;
  top: 40% !important;
}

.ios-overlay-show {
  -webkit-animation-name: ios-overlay-show;
  -webkit-animation-duration: 750ms;
  -moz-animation-name: ios-overlay-show;
  -moz-animation-duration: 750ms;
  -ms-animation-name: ios-overlay-show;
  -ms-animation-duration: 750ms;
  -o-animation-name: ios-overlay-show;
  -o-animation-duration: 750ms;
  animation-name: ios-overlay-show;
  animation-duration: 750ms;
}

@-webkit-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@-moz-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@-ms-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@-o-keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@keyframes ios-overlay-show {
  0% { opacity: 0; }
  100% { opacity: 1; }
}

.ios-overlay-hide {
  -webkit-animation-name: ios-overlay-hide;
  -webkit-animation-duration: 750ms;
  -webkit-animation-fill-mode: forwards;
  -moz-animation-name: ios-overlay-hide;
  -moz-animation-duration: 750ms;
  -moz-animation-fill-mode: forwards;
  -ms-animation-name: ios-overlay-hide;
  -ms-animation-duration: 750ms;
  -ms-animation-fill-mode: forwards;
  -o-animation-name: ios-overlay-hide;
  -o-animation-duration: 750ms;
  -o-animation-fill-mode: forwards;
  animation-name: ios-overlay-hide;
  animation-duration: 750ms;
  animation-fill-mode: forwards;
}

@-webkit-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@-moz-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@-ms-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@-o-keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
}
@keyframes ios-overlay-hide {
  0% { opacity: 1; }
  100% { opacity: 0; }
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
#checkMark {
  text-decoration: none;
}
 input[type="submit"]:hover {
  background: #f15958;
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



<div id="header">
<h1><?php echo $header_text; ?><span><?php echo $sub_header_text; ?></span></h1>
<div id='inbound-content'>
<?php echo do_shortcode( $new_content  ); ?>
</div>
<nav class="demos">
<a href="<?php echo $link_url;?>" id="checkMark" class="btn"><?php echo $submit_button_text; ?></a>
</nav>
</div>
</div>

	


<?php 
break;
endwhile; endif; 
do_action('wp_cta_footer'); 
wp_footer();
?>  
</body>