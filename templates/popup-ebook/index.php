<?php
/**
* Template Name:  Blank CTA Template
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
$width = get_post_meta( $post_id, 'wp_cta_width-'.$var_id, true ) . "px";
$height = get_post_meta( $post_id, 'wp_cta_height-'.$var_id, true ) . "px";

$headline_text_color = wp_cta_get_value($post, $key, 'headline-text-color' );
$header_text = wp_cta_get_value($post, $key, 'header-text' );
$hero = wp_cta_get_value($post, $key, 'hero' );
$content_color = wp_cta_get_value($post, $key, 'content-color' );
$content_text_color = wp_cta_get_value($post, $key, 'content-text-color' );
$submit_button_color = wp_cta_get_value($post, $key, 'submit-button-color' );
$submit_button_text_color = wp_cta_get_value($post, $key, 'submit-button-text-color' );
$submit_button_text = wp_cta_get_value($post, $key, 'submit-button-text' );
$redirect = wp_cta_get_value($post, $key, 'redirect' );
$email = wp_cta_get_value($post, $key, 'email' );
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
  body{ padding: 0px; margin: 0px; margin: auto; font-family: Arial;}
#inbound-wrapper {
  width: 700px;
  padding: 10px;
  padding-bottom: 0px;
}
#inbound-hero {
  width: 35%;
float: left;
margin-right: 4%;
margin-left: 4%;
position: relative;
}
#inbound-content {
width: 50%;
float: left;
padding-left: 20px;
position: relative;
}
.inbound-hero-img {
  width: 100%;
}
h1 {
font-size: 23px;
padding-top: 0px;
padding-bottom: 0px;
margin-top: 5px;
margin-bottom: 15px;
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
<?php
if ($width !="") {
            echo "#content {width: $width;}"; // change header color
}
if ($height !="") {
            echo "#content {height: $height;}"; // change header color
}
if ($content_color !="") {
            echo "#inbound-wrapper {background-color: #$content_color;}"; // change header color
}
if ($content_text_color !="") {
            echo "#inbound-content {color: #$content_text_color;}";
            echo "#content, #content-wrapper p {color: #$content_text_color; }";
            echo ".inbound-horizontal label {color: #$content_text_color; }";
}
if ($headline_text_color != ""){
   echo "h1 {color:#$headline_text_color;";
}
if ($submit_button_color != "") {
         echo ".button {background: #$submit_button_color; border-bottom: 3px solid $darker;}";
         echo ".button:hover { background: $darker; border-bottom: 3px solid #$submit_button_color;}";
          //echo".button { background: #$submit_button_color;}";
          //echo ".button:hover { background: $darker; border-bottom: 3px solid #DB3D3D;}";
          //regulr background: #DB3D3D; border-bottom: 3px solid #C12424;
          // hover .button:hover {background: #C12424;border-bottom: 3px solid #DB3D3D;}
}
if ($submit_button_text_color != "") {
          echo".button { color: #$submit_button_text_color;}";
}
if ($headline_color != "") {
          echo"h1#main-headline { color: #$headline_color; margin-top: 0px; padding-top: 10px; line-height: 36px; margin-bottom: 10px;}";
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


<h1><?php echo $header_text;?></h1>
<div id="inbound-content">

<?php echo do_shortcode( $new_content  ); ?>
</div>
<div id="inbound-hero">
<img class='inbound-hero-img' src="<?php echo $hero;?>">
</div>

  <div class="divider_line"></div>


  <div id="inbound-form-wrapper">

<?php
$title = get_the_title( $post_id );
echo do_shortcode( '[inbound_form name="'.$title.'" redirect="'.$redirect.'" notify="'.$email.'" layout="horizontal" labels="top" submit="'.$submit_button_text.'" ]

[inbound_field label="Name" type="text" required="0" ]

[inbound_field label="Email" type="text" required="1" ]

[/inbound_form]' );?>
<?php echo do_shortcode( '[inbound_form fields="Name, Email" required="" layout="horizontal" redirect="'.$redirect.'" button_text="'.$submit_button_text.'"]' );?>

  </div>

</div>




<?php
break;
endwhile; endif;
do_action('wp_cta_footer');
wp_footer();
?>
</body>