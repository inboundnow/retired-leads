<?php
/**
* Template Name:  follow to download
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

/* Load Regular WordPress $post data and start the loop */
if (have_posts()) : while (have_posts()) : the_post();
$post_id = get_the_ID();
$var_id=(isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';
$width = get_post_meta( $post_id, 'wp_cta_width-'.$var_id, true );
$height = get_post_meta( $post_id, 'wp_cta_height-'.$var_id, true );
$header_text = wp_cta_get_value($post, $key, 'header-text' );
$share_url = wp_cta_get_value($post, $key, 'share-url' );
$share_text = wp_cta_get_value($post, $key, 'share-text' );
$twittername = wp_cta_get_value($post, $key, 'twittername' );
$download_url = wp_cta_get_value($post, $key, 'download-url' );
$content_color = wp_cta_get_value($post, $key, 'content-color' );
$text_color = wp_cta_get_value($post, $key, 'text-color' );

$download_text = wp_cta_get_value($post, $key, 'download-url-text' );
$border_radius = wp_cta_get_value($post, $key, 'border-radius' );

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
  padding-right: 5%;
  padding-left: 5%;
  padding-bottom: 2%;
  padding-top: 10px;
}
#inbound-share-model {
  text-align: center;
}
</style>

<!-- Load Normal WordPress wp_head() function -->
<?php wp_head(); ?>
<!-- Load Landing Pages's custom pre-load hook for 3rd party plugin integration -->
<?php do_action('wp_cta_head'); ?>
<script src="<?php echo $path;?>js/jquery.tweetAction.js"></script>
</head>

<body class="pop-up-container lightbox-pop">

<div id="content" style="width:<?php echo $width;?>px;height:<?php echo $height;?>px; margin: auto;">
  <div id="extra-text-area"><?php echo do_shortcode( $header_text );?></div>
  <div id="inbound-share-model">
                    <p> <span id="tweetLink"><img src="//embeds.inboundnow.com/twitter/follow-to-download/assets/img/follow-image.png" title="Click and Follow to activate the download" ></span></p>
          <div id="arrow-down"></div>


  <span id="placeholder-span" class="downloadButton" title="click the above share button to activate the download">Download</span>

  <a href="<?php echo $download_url; ?>" style="display:none;" class="downloadButton active" title="Thanks! Click to Download">Download</a>

          <script>
        jQuery(document).ready(function($) {

                jQuery("#feedburnerform").removeClass('wpl-track-me');
                jQuery(".downloadButton").removeAttr('href');
                jQuery(".downloadButton").addClass('prevent-default');
                setTimeout(function() {
                   jQuery("#feedburnerform").removeClass('wpl-track-me');
                   jQuery(".downloadButton").removeAttr('href');
                   jQuery(".downloadButton").addClass('prevent-default');
                    }, 1000);

          jQuery("body").on('click', '.prevent-default', function (event) {
              event.preventDefault();
              console.log('clicked');
              });
      // Using our tweetAction plugin. For a complete list with supported
      // parameters, refer to http://dev.twitter.com/pages/intents#tweet-intent

      $('#tweetLink').tweetAction({
          screen_name: '<?php echo $twittername; ?>'
      },function(){
        $("#placeholder-span").hide();
                  // When the user closes the pop-up window:
                  var the_link = jQuery("#the_link").attr('href');
                  var link_target = jQuery("#the_link").hasClass('external-new-tab');
                  if (link_target === true){
                    $('a.downloadButton').addClass('external-new-tab');
                  }
                  $('a.downloadButton')
                          .show()
                          .attr('href', the_link)
                          .attr('title', 'Thanks! Click to Download');

      });

  });
          </script>

          <a id="the_link" style="display:none;" href="<?php echo $download_url; ?>"></a>
     </div>
<?php
break;
endwhile; endif;
do_action('wp_cta_footer');
wp_footer();
?>
</body>