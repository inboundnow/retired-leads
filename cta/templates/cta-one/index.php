<?php 
/*****************************************/
// Template Title:  CTA one
// Plugin: CTA - Inboundnow.com
/*****************************************/

/* Declare Template Key */
$key = wp_cta_get_parent_directory(dirname(__FILE__)); 
$path = WP_CTA_URLPATH.'templates/'.$key.'/';
$url = plugins_url();
/* Define Landing Pages's custom pre-load hook for 3rd party plugin integration */
do_action('wp_cta_init');

// Convert Hex to RGB Value for submit button
function Hex_2_RGB($hex) {
        $hex = preg_replace("/#/", "", $hex);
        $color = array();
 
        if(strlen($hex) == 3) {
            $color['r'] = hexdec(substr($hex, 0, 1) . $r);
            $color['g'] = hexdec(substr($hex, 1, 1) . $g);
            $color['b'] = hexdec(substr($hex, 2, 1) . $b);
        }
        else if(strlen($hex) == 6) {
            $color['r'] = hexdec(substr($hex, 0, 2));
            $color['g'] = hexdec(substr($hex, 2, 2));
            $color['b'] = hexdec(substr($hex, 4, 2));
        }
 
        return $color;
        
}
if ( isset($_GET['wp-cta-variation-id']) ){
  $var_id = $_GET['wp-cta-variation-id'];
} else {
   $var_id = 0;
}

/* Load $post data */
if (have_posts()) : while (have_posts()) : the_post();
    
    /* Pre-load meta data into variables */
    $width = get_post_meta( get_the_ID(), 'wp_cta_width-'.$var_id, true ) . "px";
    $height = get_post_meta( get_the_ID(), 'wp_cta_height-'.$var_id, true ) . "px";
    $content_color = wp_cta_get_value($post, $key, 'content-color'); 
    $body_color = wp_cta_get_value($post, $key, 'cta-background-color');
    $text_color = wp_cta_get_value($post, $key, 'content-text-color');

    $headline_color = wp_cta_get_value($post, $key, 'headline-color');
    $headline_text = wp_cta_get_value($post, $key, 'header-text');
    
    $button_text = wp_cta_get_value($post, $key, 'button-text');
     $button_link = wp_cta_get_value($post, $key, 'button-link');
    $submit_button_color = wp_cta_get_value($post, $key, 'button-background-color'); 
    $ribbon_status = wp_cta_get_value($post, $key, 'link_status' ); 

$RBG_array = Hex_2_RGB($submit_button_color);
$red = $RBG_array['r'];
$green = $RBG_array["g"];
$blue = $RBG_array["b"]; 
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"><head profile="http://gmpg.org/xfn/11"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title><?php wp_title(); ?></title>
<link rel="stylesheet" href="<?php echo $path; ?>assets/css/style.css" type="text/css" media="screen">

<style media="screen" type="text/css">

<?php if ($body_color !="") {
            echo ".box {background-color: #$body_color;}"; // change header color
        }
        ?> <?php if ($text_color !="") {
            echo ".box {color: #$text_color;}";
        }
        ?> <?php if ($width !="") {
            echo ".box {width: ".$width." !important;}";
        }
        ?>
        <?php if ($height !="") {
            echo ".box {height: ".$height." !important;}";
        }
        ?>
      
         <?php if ($submit_button_color != "") {
          echo".cta:hover { background: #$submit_button_color;}
                 a.cta {  border: 1px solid #$submit_button_color;
                        background-image: linear-gradient(bottom, #$submit_button_color 0%, #$submit_button_color 100%);
                        background-image: -o-linear-gradient(bottom, #$submit_button_color 0%, #$submit_button_color 100%);
                        background-image: -moz-linear-gradient(bottom, #$submit_button_color 0%, #$submit_button_color 100%);
                        background-image: -webkit-linear-gradient(bottom, #$submit_button_color 0%, #$submit_button_color 100%);
                        background-image: -ms-linear-gradient(bottom, #$submit_button_color 0%, #$submit_button_color 100%);
                        background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #$submit_button_color),color-stop(1, #$submit_button_color));
                        }";

           }

        ?> 
        #content-wrapper {
            padding:20px;
            padding-top: 0px;
        }
</style>
<?php /* Load all functions hooked to wp_cta_head including global js and global css */
			wp_head(); // Load Regular WP Head
			do_action('wp_cta_head'); // Load Custom Landing Page Specific Header Items
		?>

</head>
<body>

<div class="box">
<?php if ($ribbon_status === "option_on"){ ?> 
  <img class="ribbon" src="http://tempsitebeta.com/img/corner-ribbon.png" />
  <?php } ?>  
  <h2><?php echo $headline_text; ?></h2>
    <div class='center'><?php the_content();?></div>
  <p class="buy-now"><a href="<?php echo $button_link;?>" class="cta"><?php echo $button_text; ?></a></p>
</div>     

 <?php break; endwhile; endif; // end wordpress loop
    do_action('wp_cta_footer'); // load landing pages footer hook
    wp_footer(); // load normal wordpress footer ?>

</body>
</html>