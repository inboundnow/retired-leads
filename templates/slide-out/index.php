<?php
/**
* Template Name:  Slideout Template
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
 
$options_array = 
array(
    array(
        'label' => 'Headline Text Color', // Name of field
        'description' => "Use this setting to change headline color", // what field does
        'id'  => 'headline-text-color', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'colorpicker', // metafield type
        'default'  => 'FFFFFF', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
    array(
        'label' => 'Header Text',
        'description' => "Header Text",
        'id'  => 'header-text',
        'type'  => 'text',
        'default'  => 'Awesome Text that makes you want to buy',
        'context'  => 'normal'
        )
      );
 
foreach ($options_array as $key => $value) {
  $name = str_replace(array('-'),'_', $value['id']);
//  echo "$" . $name  . " = " .  'wp_cta_get_value(' . '$'. 'post, ' . '$'. 'key, '. " '" . $value['id'] . "' " . ');' . "\n";
//  echo "<br>";  
}
 
/* Output=
 
$headline_text_color = wp_cta_get_value($post, $key, 'headline-text-color' ); 
$header_text = wp_cta_get_value($post, $key, 'header-text' ); 
 
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
  width: 615px;
  padding: 10px;
  padding-bottom: 0px;
  padding-top: 0px;
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
@import url(http://fonts.googleapis.com/css?family=Open+Sans:400,300,700);
.container {
  width: 500px;
  margin-left: auto;
  margin-right: auto;
}

h1 {
  font-family: "Open Sans", sans;
  font-weight: 300;
  text-align: center;
  margin-bottom: 0px;
margin-top: 0px;
font-size: 25px;
}

.service-details {
  /*width: 460px;*/
  height: 250px;
  /*height: 230px;*/
  overflow: hidden;
  position: relative;
}

.service-details img {
  position: absolute;
  top: 0;
  left: 0;
  height: inherit;
  width: 50%;
  height: 100%;
  float: left;
  transition: all 0.8s;
  -moz-transition: all 0.8s;
}

.service-details:hover img {
  /*opacity: 0.4 !important;*/
}

.service-details .service-hover-text h3 {
  padding: 0px;
  margin: 0px;
  font-size: 25px;
  font-weight: 300;
  font-family: "Open Sans";
}

.service-details .service-hover-text h4 {
  padding: 0px;
  padding-bottom: 13px;
  margin: 0px;
  font-size: 14px;
  letter-spacing: 3px;
  width: 90%;
  font-family: "Open Sans";

  border-bottom: 2px solid #000;
}

.service-details .service-hover-text p {
  padding-top: 13px;
  font-size: 14px;
  line-height: 20px;
  font-family: "Open Sans";
}

.service-details .service-hover-text {
  width: 44%;
  height: 89%;
  position: absolute;
  top: 0%;
  left: 50%;
  padding: 3% 4%;
  background: #D90E0E;
  color: white;
  /*  display: none;*/
  transition: all 0.5s ease-in-out;
  -moz-transition: all 0.4s;
}

.service-details:hover .service-hover-text {
  display: block !important;
  color: white;
  background: rgba(217, 14, 14, 0.85);
  left: 0px;
  top: 0px;
}

.service-details .service-text {
  width: 50%;
  height: inherit;
  background: #000;
  float: left;
  position: absolute;
  left: 50%;
}

.service-details .service-text p {
  padding: 0px 0px 0px 20px;
font-size: 24px;
font-family: "Open Sans";
font-weight: 700;
  color: #fff;
}

.service-details .service-text p span {
  font-family: "Open Sans" !important;
}

.service-details .service-text a, .service-white .service-text {
  padding: 0px 0px 0px 20px;
  font-size: 14px !important;
  color: #FF5A22 !important;
  font-family: "Open Sans" !important;
  text-decoration: none !important;
}

.service-details .service-text {
  float: left;
}

.service-white {
  background: #eee !important;
  width: 50% !important;
  height: inherit !important;
}

.service-white p {
  color: #000 !important;
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



<div class="container">
  <h1>Profile CSS Hover Effect</h1>
  <div class="service-details">
    <img src="http://i.imgur.com/SkFZNy4.jpg" alt="realm">
    <div class="service-hover-text">
      <h4>Header Area Lorem ipsum dolor sit</h4>
      
      <p>Nulla rhoncus orci sed odio euismod vestibulum. Praesent porta aliquet nulla, ut mattis velit rhoncus eu duspendisse nibh orci laoreet. </p>
    </div>
    <div class="service-white service-text">
      <p>Jane Doe</p>
      
    </div>
  </div>
</div>
</div>

	


<?php 
break;
endwhile; endif; 
do_action('wp_cta_footer'); 
wp_footer();
?>  
</body>