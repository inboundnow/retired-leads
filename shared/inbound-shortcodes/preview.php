<?php

/*	Include Wordpress
 *	--------------------------------------------------------------------------- */
	$absolute_path = __FILE__;
	$path_to_file = explode( 'wp-content', $absolute_path );
	$path_to_wp = $path_to_file[0];

	require_once( $path_to_wp . '/wp-load.php' );

/*	Get Shortcodes
 *	--------------------------------------------------------------------------- */

$test = "http://local.dev/wp-content/plugins/inbound-forms/preview.php?sc=[inbound_form_test%20name=%22test%22][inbound_field%20label=%22%22%20type=%22html-block%22%20description=%22%22%20required=%220%22%20dropdown=%22%22%20radio=%22%22%20html=%22test%20&lt;span%20class=%22foot%22&gt;%20%22][/inbound_form_test]&lt;/span&gt;";


	$shortcode = html_entity_decode( trim( $_GET['sc'] ) ); 

/* HTML MATCHES */
	//	$test = 'html="&lt;span%20class="test"&gt;tes&lt;/span&gt;"';
  // preg_match_all('%\[inbound_form_test\s*(?:(layout)\s*=\s*(.*?))?\](.*?)\[/inbound_form_test\]%', $shortcode, $matches);

	//preg_match_all('/'.$varname.'\s*?=\s*?(.*)\s*?(;|$)/msU',$shortcode,$matches);
  

$horiz = "";
if (preg_match("/horizontal/i", $shortcode)) {
$horiz = "<h2 title='Open preview in new tab' class='open_new_tab'>Click to Preview Horizontal Form in new tab</h2>";
}

	$shortcode = str_replace('\"', '"', $shortcode);
	$shortcode = str_replace('&lt;', '<', $shortcode);
	$shortcode = str_replace('&gt;', '>', $shortcode);
	$shortcode = str_replace('{{child}}', '', $shortcode);
	$shortcode = str_replace('label=""', 'label="Default"', $shortcode);  
	//$field_name_fallback = ($field_name === "") ? 'fallback_name' : '0';
	?>
	<!DOCTYPE HTML>
	<html lang="en">
	<head>
	<link rel="stylesheet" type="text/css" href="<?php echo INBOUND_FORMS; ?>/css/frontend-render.css" media="all" />
			
<?php wp_head(); ?>
<style type="text/css">
html {margin: 0 !important;}
body {padding: 30px 15px;
background:#fff;
padding-top: 5px;}
.bottom-insert-button {
position: fixed;
bottom: 5px;
left: 10%;
text-align: center;
margin: auto;
width: 80%;
display: inline-block;
text-decoration: none;
font-size: 17px;
line-height: 23px;
height: 24px;
margin: 0;
padding: 0 10px 1px;
cursor: pointer;
border-width: 1px;
border-style: solid;
-webkit-border-radius: 3px;
-webkit-appearance: none;
border-radius: 3px;
white-space: nowrap;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;

background-color: #21759B;
background-image: -webkit-gradient(linear,left top,left bottom,from(#2A95C5),to(#21759B));
background-image: -webkit-linear-gradient(top,#2A95C5,#21759B);
background-image: -moz-linear-gradient(top,#2a95c5,#21759b);
background-image: -ms-linear-gradient(top,#2a95c5,#21759b);
background-image: -o-linear-gradient(top,#2a95c5,#21759b);
background-image: linear-gradient(to bottom,#2A95C5,#21759B);
border-color: #21759B;
border-bottom-color: #1E6A8D;
-webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.5);
box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.5);
color: #FFF;
text-decoration: none;
text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);

}
.disclaimer {
top: 0px;
display: inline-block;
margin-bottom: 20px;
font-size: 11px;
}
.open_new_tab {
color: #2465D8;
margin-bottom: 15px;
cursor: pointer;
font-size: 12px;
text-align: center;
}
			</style>
		</head>
		<body>
			<span class="disclaimer"><strong>Note:</strong> Previews aren't always exactly what they will look like on your page. Sometimes other styles can interfere</span>
			<?php echo $horiz;
				if ($horiz != ""){ ?>
					<script type="text/javascript">
					function OpenInNewTab(url) {
					  var win=window.open(url, '_blank');
					  win.focus();
					}

					jQuery(document).ready(function($) {
					   var this_link = window.location.href;
					   jQuery("body").on('click', '.open_new_tab', function () {
					   		OpenInNewTab(this_link);
    					});
					   	if ( window.self === window.top ) { 
							jQuery(".open_new_tab").hide();
							jQuery(".disclaimer").hide();
						} else {
							
						}
					 });
					</script>
		
				<?php }
			?>
			
			
			<?php echo do_shortcode( $shortcode ); ?>
			
			<?php // echo "<br>". $shortcode; ?>
			
		<?php wp_footer();?>
		</body>
	</html>