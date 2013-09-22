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
$classes = wp_cta_get_value($post, $key, 'classes');
$content = get_the_content();

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
 
  <!-- Included CSS Files -->
  <link rel="stylesheet" href="<?php echo $path; ?>assets/css/style.css">
 <style type="text/css">
  body{ width:<?php echo $width; ?>; height: <?php echo $height; ?>;}
  #content-wrapper img.alignleft, #content-wrapper .wp-caption.alignleft {
    margin: 0px;
    }
  </style>
  <!-- Included JS Files -->
  <script src="<?php echo $path; ?>assets/js/modernizr.js"></script>

<!-- Load Normal WordPress wp_head() function -->
<?php wp_head(); ?> 
<!-- Load Landing Pages's custom pre-load hook for 3rd party plugin integration -->
<?php do_action('wp_cta_head'); ?>

</head>

<body <?php // body_class($class); ?>>

<div id="wrapper" >

<div id="content-wrapper">
  <div id="content">
  <!-- Use the_title(); to print out the main headline -->
         <?php 
          // Conditional check for main content placeholder
          if ($content != "") {
            the_content(); // show the content!
          } else {
          // Fill empty the_content(); area with placeholder html.
          echo "<p>This is the default content from the main wordpress editor screen. If it's empty, this content will show (a.k.a. fill in some content!)</p>"; 
          } ?>
      
    </div><!-- end #content -->


</div> <!-- end #content-wrapper -->


<?php 
break;
endwhile; endif; 
do_action('wp_cta_footer'); 
wp_footer();
?>  
</body>
</html>