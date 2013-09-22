<?php
/* Add Stats metabox on right */
add_action('add_meta_boxes', 'wp_cta_ab_display_stats_metabox');
function wp_cta_ab_display_stats_metabox() {

		add_meta_box( 
		'wp_cta_ab_display_stats_metabox', 
		__( 'A/B Testing', 'bab' ),
		'wp_cta_ab_stats_metabox',
		'wp-call-to-action' , 
		'side', 
		'high' );
}

function wp_cta_ab_stats_metabox() {
	global $post;
	$variations = get_post_meta($post->ID,'wp-cta-ab-variations', true);
	$variations = explode(',',$variations);
	$variations = array_filter($variations,'is_numeric');
	?>
	<div>
		<style type="text/css">
		
		</style>
		<div class="inside" style='margin-left:-8px;'> 
			<div id="bab-stat-box">
			<?php if (isset($_GET['new_meta_key'])) { ?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
			   // This fixes meta data saves for cloned pages
			   function isNumber (o) {
				  return ! isNaN (o-0) && o !== null && o !== "" && o !== false;
				}
			   var new_meta_key = "<?php echo $_GET['new_meta_key'];?>";
			     jQuery('#template-display-options input[type=text], #template-display-options select, #template-display-options input[type=radio], #template-display-options textarea').each(function(){
			        var this_id = jQuery(this).attr("id");
			        var final_number = this_id.match(/[^-]+$/g);
			        var new_id = this_id.replace(/[^-]+$/g, new_meta_key);
			        var is_number = isNumber(final_number);
			        console.log(final_number);
			        console.log(is_number);
			        if (is_number === false) {
			        	jQuery(this).attr("id", this_id + "-" + new_meta_key);
			        	jQuery(this).attr("name", this_id + "-" + new_meta_key);
			        } else {
				        jQuery(this).attr("id", new_id);
				        jQuery(this).attr("name", new_id);
			    	}
			    });
			 });
			</script>
			<?php }	?>
				<?php
				$howmany = count($variations);
				foreach ($variations as $key=>$vid)
				{
					if (!is_numeric($vid)&&$key==0)
						$vid = 0;
						
					$variation_status = wp_cta_ab_get_wp_cta_active_status($post,$vid);
					$variation_status_class = ($variation_status ==1) ? "variation-on" : 'variation-off';
					
					$permalink = get_permalink($post->ID);
					if (strstr($permalink,'?wp-cta-variation-id'))
					{
						$permalink = explode('?',$permalink);
						$permalink = $permalink[0];
					}
					$permalink = $permalink."?wp-cta-variation-id=".$vid;
					
					$impressions = get_post_meta($post->ID,'wp-cta-ab-variation-impressions-'.$vid, true);
					$conversions = get_post_meta($post->ID,'wp-cta-ab-variation-conversions-'.$vid, true);
					
					
					(is_numeric($impressions)) ? $impressions = $impressions : $impressions = 0; 
					(is_numeric($conversions)) ? $conversions = $conversions : $conversions = 0; 
					
					if ($impressions>0)
					{
						$conversion_rate = $conversions / $impressions;
						(($conversions===0)) ? $sign = "" : $sign = "%"; 
						$conversion_rate = round($conversion_rate,2) * 100 . $sign; 
					}
					else
					{
						$conversion_rate = 0;
					}
					
					if ($key==0)
					{
						$title = get_post_meta($post->ID,'wp-cta-main-headline', true);
					}
					else
					{
						$title = get_post_meta($post->ID,'wp-cta-main-headline-'.$vid, true);
					}
					
					//determine letter from key
					?>

					<div id="wp-cta-variation-<?php echo wp_cta_ab_key_to_letter($key); ?>" class="bab-variation-row <?php echo $variation_status_class;?>" >
						<div class='bab-varation-header'>
								<span class='bab-variation-name'>Variation <span class='bab-stat-letter'><?php echo wp_cta_ab_key_to_letter($vid); ?></span>
								<?php 
								if($variation_status!=1)
								{
								?>
									<span class='is-paused'>(Paused)</span>
								<?php
								}
								?>
								</span> 
									
									
								<span class="wp-cta-delete-var-stats" data-letter='<?php echo wp_cta_ab_key_to_letter($vid); ?>' data-vid='<?php echo $vid; ?>' rel='<?php echo $post->ID;?>' title="Delete this variations stats">Clear Stats</span>
							</div>
						<div class="bab-stat-row">	
							<div class='bab-stat-stats' colspan='2'>
								<div class='bab-stat-container-impressions bab-number-box'>
									<span class='bab-stat-span-impressions'><?php echo $impressions; ?></span>
									<span class="bab-stat-id">Views</span>
								</div>
								<div class='bab-stat-container-conversions bab-number-box'>
									<span class='bab-stat-span-conversions'><?php echo $conversions; ?></span>
									<span class="bab-stat-id">Conversions</span></span>
								</div>
								<div class='bab-stat-container-conversion_rate bab-number-box'>
									<span class='bab-stat-span-conversion_rate'><?php echo $conversion_rate; ?></span>
									<span class="bab-stat-id bab-rate">Conversion Rate</span>
								</div>
								<div class='bab-stat-control-container'>
									<span class='bab-stat-control-pause'><a title="Pause this variation" href='?post=<?php echo $post->ID; ?>&action=edit&wp-cta-variation-id=<?php echo $vid; ?>&ab-action=pause-variation'>Pause</a></span> <span class='bab-stat-seperator pause-sep'>|</span>
									<span class='bab-stat-control-play'><a title="Turn this variation on" href='?post=<?php echo $post->ID; ?>&action=edit&wp-cta-variation-id=<?php echo $vid; ?>&ab-action=play-variation'>Play</a></span> <span class='bab-stat-seperator play-sep'>|</span>
									<span class='bab-stat-menu-edit'><a title="Edit this variation" href='?post=<?php echo $post->ID; ?>&action=edit&wp-cta-variation-id=<?php echo $vid; ?>'>Edit</a></span> <span class='bab-stat-seperator'>|</span>
									<span class='bab-stat-menu-preview'><a title="Preview this variation" class='thickbox' href='<?php echo $permalink; ?>&wp_cta_iframe_window=on&post_id=<?php echo $post->ID;?>&TB_iframe=true&width=1503&height=467' target='_blank'>Preview</a></span> <span class='bab-stat-seperator'>|</span>
									<span class='bab-stat-menu-clone'><a title="Clone this variation" href='?post=<?php echo $post->ID; ?>&action=edit&new-variation=1&clone=<?php echo $vid; ?>&new_meta_key=<?php echo $howmany; ?>'>Clone</a></span> <span class='bab-stat-seperator'>|</span>
									<span class='bab-stat-control-delete'><a title="Delete this variation" href='?post=<?php echo $post->ID; ?>&action=edit&wp-cta-variation-id=<?php echo $vid; ?>&ab-action=delete-variation'>Delete</a></span> 
								</div>
							</div>
						</div>
						<div class="bab-stat-row">	
					
								<div class='bab-stat-menu-container'>
									
									<?php do_action('wp_cta_ab_testing_stats_menu_post'); ?>						
						
							</div>
						</div>
					</div>
						<?php
						
				}
				?>
			</div>
				
		</div>	
	</div>
	<?php
}		

//print out tabs
add_action('edit_form_after_title','wp_cta_ab_testing_add_tabs', 5);
function wp_cta_ab_testing_add_tabs()
{
	global $post;
	$post_type_is = get_post_type($post->ID);
	$permalink = get_permalink($post->ID);
	
	// Only show wp-cta tabs on landing pages post types (for now)
	if ($post_type_is === "wp-call-to-action") 
	{
		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
		echo "<input type='hidden' id='open_variation' value='{$current_variation_id}'>";
		if (isset($_GET['new_meta_key'])) {
		echo "<input type='hidden' id='variation_new_meta_key' value='".$_GET['new_meta_key']."'>";
		}
		if (isset($_GET['clone'])) {
		echo "<input type='hidden' id='clone_variation_id' value='".$_GET['clone']."'>";
		}
		
		$variations = get_post_meta($post->ID,'wp-cta-ab-variations', true);
		$array_variations = explode(',',$variations);
		$variations = array_filter($array_variations,'is_numeric');
		sort($array_variations,SORT_NUMERIC);
		
		$lid = end($array_variations);
		$new_variation_id = $lid+1;
		
		if ($current_variation_id>0||isset($_GET['new-variation']))
		{
			$first_class = 'inactive';
		}
		else
		{
			$first_class = 'active';
		}
		
		echo '<h2 class="nav-tab-wrapper a_b_tabs">';
		echo '<a href="?post='.$post->ID.'&wp-cta-variation-id=0&action=edit" class="wp-cta-ab-tab nav-tab nav-tab-special-'.$first_class.'" id="tabs-0">A</a>';					

		$var_id_marker = 1;

		
		foreach ($array_variations as $i => $vid)
		{
			
			if ($vid!=0)
			{
				$letter = wp_cta_ab_key_to_letter($vid);
				
				//alert (variation.new_variation); 
				if ($current_variation_id==$vid&&!isset($_GET['new-variation']))
				{
					$cur_class = 'active';
				}
				else
				{
					$cur_class = 'inactive';
				}
				echo '<a href="?post='.$post->ID.'&wp-cta-variation-id='.$vid.'&action=edit" class="wp-cta-nav-tab nav-tab nav-tab-special-'.$cur_class.'" id="tabs-add-variation">'.$letter.'</a>';
				
			}					
		} 
		
		if (!isset($_GET['new-variation']))
		{
			echo '<a href="?post='.$post->ID.'&wp-cta-variation-id='.$new_variation_id.'&action=edit&new-variation=1" class="wp-cta-nav-tab nav-tab nav-tab-special-inactive nav-tab-add-new-variation" id="tabs-add-variation">Add New Variation</a>';
		}
		else
		{
			$variation_count = count($array_variations);
			$letter = wp_cta_ab_key_to_letter($variation_count);
			echo '<a href="?post='.$post->ID.'&wp-cta-variation-id='.$new_variation_id.'&action=edit" class="wp-cta-nav-tab nav-tab nav-tab-special-active" id="tabs-add-variation">'.$letter.'</a>';
		}
		$edit_link = (isset($_GET['wp-cta-variation-id'])) ? '?wp-cta-variation-id='.$_GET['wp-cta-variation-id'].'' : '?wp-cta-variation-id=0';
		$post_link = get_permalink($post->ID);
		$post_link = preg_replace('/\?.*/', '', $post_link);
		echo "<a rel='".$post_link."' id='cta-launch-front' class='button-primary new-save-wp-cta-frontend' href='$post_link$edit_link&cta-template-customize=on'>Launch Visual Editor</a>";
		echo '</h2>';
	} 
}