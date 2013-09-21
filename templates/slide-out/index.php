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
        'label' => 'Instructions', // Name of field
        'description' => "Instructions: This entire call to action is linked with your destination URL. Clicking anywhere will send people to your landing page", // what field does
        'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'description-block', // metafield type
        'default'  => '<p>This entire call to action is linked with your destination URL. Clicking anywhere will send people to your landing page</p>', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
    array(
        'label' => 'Header Text (optional)',
        'description' => "Header Text",
        'id'  => 'header-text',
        'type'  => 'text',
        'default'  => 'This Main Headline Will Rock Your Socks',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Headline Text Color',
        'description' => "Use this setting to change headline color",
        'id'  => 'headline-text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Main Image',
        'description' => "This is the main graphic with the popup",
        'id'  => 'hero', // called in template's index.php file with lp_get_value($post, $key, 'media-id');
        'type'  => 'media',
        'default'  => 'http://www.fillmurray.com/250/250',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Static Background Color',
        'description' => "Changes background color",
        'id'  => 'static-bg-color',
        'type'  => 'colorpicker',
        'default'  => 'EEEEEE',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Static Area Text Color',
        'description' => "Use this setting to change the content text color",
        'id'  => 'static-text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Static Teaser Text',
        'description' => "Text on the button.",
        'id'  => 'teaser-text',
        'type'  => 'text',
        'default'  => 'Do You Know How to Avoid these common XYZ Mistakes?'
        ),
      array(
        'label' => 'Slideout Background Color',
        'description' => "Changes background color",
        'id'  => 'slideout-bg-color',
        'type'  => 'colorpicker',
        'default'  => 'D90E0E',
        'context'  => 'normal'
        ),
      array(
        'label' => 'Slideout Text Color',
        'description' => "Changes background color",
        'id'  => 'slideout-text-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Slideout Button Text',
        'description' => "Text on the button.",
        'id'  => 'submit-button-text',
        'type'  => 'text',
        'default'  => 'Download Now'
        ),
     array(
        'label' => 'Link URL',
        'description' => "Where do you want to send people to?",
        'id'  => 'link_url',
        'type'  => 'text',
        'default'  => 'http://www.inboundnow.com'
        )
      );
/* 
foreach ($options_array as $key => $value) {
  $name = str_replace(array('-'),'_', $value['id']);
 echo "$" . $name  . " = " .  'wp_cta_get_value(' . '$'. 'post, ' . '$'. 'key, '. " '" . $value['id'] . "' " . ');' . "\n";
 echo "<br>";  
}
/**/ 
/* Output=
 
$headline_text_color = wp_cta_get_value($post, $key, 'headline-text-color' ); 
$header_text = wp_cta_get_value($post, $key, 'header-text' ); 
 
*/

/* 
echo "<pre>";
foreach ($options_array as $key => $value) {
  $name = str_replace(array('-'),'_', $value['id']);
  if($value['type'] === 'colorpicker') {
// echo "$" . $name  . " = " .  'wp_cta_get_value(' . '$'. 'post, ' . '$'. 'key, '. " '" . $value['id'] . "' " . ');' . "\n";
 //echo "<br>";  
  echo "\n";
  echo "if (" . " $" . "$name " . "!= \"\" ) {";
  echo "\n";
  echo "echo \".css_element { color: #$" . "".$name."" . ";}\"; \n"; // change sidebar color
  echo "}";
  echo "\n";
  }
}
echo "</pre>";
/**/ 

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
$headline_text_color = wp_cta_get_value($post, $key, 'headline-text-color' ); 
$hero = wp_cta_get_value($post, $key, 'hero' ); 
$static_bg_color = wp_cta_get_value($post, $key, 'static-bg-color' ); 
$static_text_color = wp_cta_get_value($post, $key, 'static-text-color' ); 
$teaser_text = wp_cta_get_value($post, $key, 'teaser-text' ); 
$slideout_bg_color = wp_cta_get_value($post, $key, 'slideout-bg-color' ); 
$slideout_text_color = wp_cta_get_value($post, $key, 'slideout-text-color' ); 
$submit_button_text = wp_cta_get_value($post, $key, 'submit-button-text' ); 
$link_status = wp_cta_get_value($post, $key, 'link_status' ); 
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
   <link rel="stylesheet" href="<?php echo $path; ?>style.css">
  <style type="text/css">
  a { text-decoration: none; }
  .inboundheader-box {text-align: center;}
  <?php
  if ( $headline_text_color != "" ) {
echo "h1 { color: #$headline_text_color;}"; 
}

if ( $static_bg_color != "" ) {
echo ".service-white { background: #$static_bg_color !important;}"; 
}

if ( $static_text_color != "" ) {
echo ".service-white p { color: #$static_text_color !important;}"; 
}

if ( $slideout_bg_color != "" ) {
 $new_color = inbound_Hex_2_RGB($slideout_bg_color);
 $red = $new_color['r'];
 $green = $new_color['g'];
 $blue = $new_color['b'];
echo ".service-details .service-hover-text, .service-details:hover .service-hover-text { background: rgba($red, $green, $blue, 0.85);}"; 
}

if ( $slideout_text_color != "" ) {
echo ".service-details .service-hover-text, .service-details:hover .service-hover-text { color: #$slideout_text_color;}"; 
}
  if ( $width != "" ) {
echo "#inbound-wrapper, .service-details, .inboundheader-box { width: $width;}"; 
}
  if ( $height != "" ) {
echo "#inbound-wrapper,.service-details { height: $height;}"; 
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

<?php if ($link_status === "option_on"){ ?> 
<a href="<?php echo $link_url;?>">
<?php } ?>  
<div class="container">
  <?php if ($header_text != "") { ?>
  <div class="inboundheader-box">
  <h1><?php echo $header_text;?></h1>
  </div>
  <?php } ?>
  <div class="service-details">
    <img src="<?php echo $hero;?>" alt="realm">
    <div class="service-hover-text">
      <?php echo do_shortcode( $submit_button_text ); ?>
    </div>
    <div class="service-white service-text">
      <?php $new_teaser = wpautop($teaser_text); echo do_shortcode( $new_teaser ); ?>
      
    </div>
  </div>
</div>
<?php if ($link_status === "option_on"){ ?> 
</a>
<?php } ?>
</div>

	


<?php 
break;
endwhile; endif; 
do_action('wp_cta_footer'); 
wp_footer();
?>  
</body>