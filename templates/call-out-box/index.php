<?php
/**
* Template Name:  Flat UI CTA
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
$width = get_post_meta( get_the_ID(), 'wp_cta_width-'.$var_id, true ) . "px";
$height = get_post_meta( get_the_ID(), 'wp_cta_height-'.$var_id, true ) . "px";
$content_color = wp_cta_get_value($post, $key, 'content-color');
$text_color = wp_cta_get_value($post, $key, 'content-text-color');
$header_text = wp_cta_get_value($post, $key, 'header-text');
$headline_color = wp_cta_get_value($post, $key, 'headline-text-color');
$button_text = wp_cta_get_value($post, $key, 'submit-button-text');
$button_link = wp_cta_get_value($post, $key, 'submit-button-link');
$submit_button_color = wp_cta_get_value($post, $key, 'submit-button-color');
$submit_button_text_color = wp_cta_get_value($post, $key, 'submit-button-text-color');

$content = get_the_content();

$input = "#" . $submit_button_color;

$submit_color_scheme = inbound_color_scheme($submit_button_color);

//echo $submit_color_scheme[50];

$darker = inbound_color($submit_color_scheme, 55);

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

  <!-- Included JS Files -->
  <script src="<?php echo $path; ?>assets/js/modernizr.js"></script>

<!-- Load Normal WordPress wp_head() function -->
<?php wp_head(); ?>

<style type="text/css">
<?php if ($width !="") {
            echo "#content {width: $width;}"; // change header color
        }
        ?>
<?php if ($height !="") {
            echo "#content {height: $height;}"; // change header color
        }
        ?>
  <?php if ($content_color !="") {
            echo "#content {background-color: #$content_color;}"; // change header color
        }
        ?>
        ?> <?php if ($text_color !="") {
            echo "#content-wrapper {color: #$text_color;}";
            echo "#content, #content-wrapper p {color: #$text_color; }";
        }
        ?>
         <?php if ($submit_button_color != "") {
         echo ".button {background: #$submit_button_color; border-bottom: 3px solid $darker;}";
         echo ".button:hover { background: $darker; border-bottom: 3px solid #$submit_button_color;}";
          //echo".button { background: #$submit_button_color;}";
          //echo ".button:hover { background: $darker; border-bottom: 3px solid #DB3D3D;}";
          //regulr background: #DB3D3D; border-bottom: 3px solid #C12424;
          // hover .button:hover {background: #C12424;border-bottom: 3px solid #DB3D3D;}
        }
        ?>
          <?php if ($submit_button_text_color != "") {
          echo".button { color: #$submit_button_text_color;}";
        }
        ?>
       <?php if ($headline_color != "") {
          echo"h1#main-headline { color: #$headline_color; margin-top: 0px; padding-top: 10px; line-height: 36px; margin-bottom: 10px;}";
        }
        ?>

</style>

<!-- Load Landing Pages's custom pre-load hook for 3rd party plugin integration -->
<?php do_action('wp_cta_head'); ?>

</head>

<body>

<div id="wrapper">

<div id="content-wrapper">
  <div id="content">
  <h1 id="main-headline"><?php echo $header_text;?></h1>
  <!-- Use the_title(); to print out the main headline -->
    <div class="the_content">

         <?php
          // Conditional check for main content placeholder
          if ($content != "") {
            the_content(); // show the content!
          } else {

          } ?>
      </div>
      <a id="cta-link" href="<?php echo $button_link;?>"><span class="button"><?php echo $button_text;?></span></a>
    </div><!-- end #content -->


</div> <!-- end #content-wrapper -->


<?php
break;
endwhile; endif;
do_action('wp_cta_footer');
wp_footer();
?>
<script type="text/javascript">

</script>
</body>
</html>