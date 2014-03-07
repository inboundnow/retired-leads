<?php

/* add meta boxes to posts, pages, and non excluded cpts */
add_action('add_meta_boxes', 'cta_placements_content_add_meta_box');
function cta_placements_content_add_meta_box()
{

	$post_types= get_post_types('','names');

	$exclude[] = 'attachment';
	$exclude[] = 'revisions';
	$exclude[] = 'nav_menu_item';
	$exclude[] = 'wp-lead';
	$exclude[] = 'automation';
	$exclude[] = 'rule';
	$exclude[] = 'list';
	$exclude[] = 'wp-call-to-action';
	$exclude[] = 'tracking-event';
	$exclude[] = 'inbound-forms';
	// add filter

	foreach ($post_types as $value ) {
		$priority = ($value === 'landing-page') ? 'core' : 'high';
		if (!in_array($value,$exclude))
		{
			add_meta_box( 'wp-cta-inert-to-post', 'Insert Call to Action Template into Content', 'cta_placements_content_meta_box' , $value, 'normal', $priority );
		}
	}
}


function cta_placements_content_meta_box()
{
	global $post;
	global $table_prefix;
	

	//print_r($wp_cta_post_template_ids);

   // $content_placements_post_status = get_post_meta($post->ID, 'id here');
	wp_cta_display_metabox(); // renders checkboxes
	wp_cta_display_controller();
}



function wp_cta_display_metabox() {
	global $post;
	
	$args = array(
	'posts_per_page'  => -1,
	'post_type'=> 'wp-call-to-action');
	
	$cta_list = get_posts($args);
	
	$cta_display_list = get_post_meta($post->ID ,'cta_display_list', true);
	$cta_display_list = ($cta_display_list != '') ? $cta_display_list : array();
	//print_r($cta_display_list);
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		function format(state) {
			if (!state.id) return state.text; // optgroup
			var href = jQuery("#cta-" + state.id).attr("href");
			return state.text + "<a class='thickbox cta-select-preview-link' href='" + href + "'>(view)</a>";
		}
		jQuery("#cta_template_selection").select2({
			placeholder: "Select one or more calls to action to rotate through",
			allowClear: true,
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});
		// show conditional fields
		jQuery('select#cta_content_placement').on('change', function () {
			var this_val = jQuery(this).val();
			jQuery(".dynamic-visable-on").hide();
			console.log(this_val);
			jQuery('.reveal-' + this_val).removeClass('inbound-hidden-row').show().addClass('dynamic-visable-on');
		});
		var onload = jQuery('select#cta_content_placement').val();
		jQuery('.reveal-' + onload).removeClass('inbound-hidden-row').show().addClass('dynamic-visable-on');
	});
	</script>
	<style type="text/css">
		.select2-container {
			width: 100%;
			padding-top: 15px;
		}
		.inbound-hidden-row {
			display: none;
		}
		.wp-cta-option-row {
			padding-top: 5px;
			padding-bottom: 5px;
		}
		.wp_cta_label.cta-per-page-option, .wp-cta-option-area.cta-per-page-option {
		display: inline-block;
		}
		label.cta-per-page-option {
			width: 190px;
			padding-left: 12px;
			display: inline-block;
		}
		.cta-options-label {
			width: 190px;

			display: inline-block;
			vertical-align: top;
			padding-top: 20px;
		}
		.cta-options-row {
		width: 65%;
		display: inline-block;
		}
		.cta-select-preview-link {
			font-size: 10px;
			 padding-left: 5px;
			vertical-align: middle;
		}
		.select2-highlighted a.cta-select-preview-link {
			color: #fff !important;
		}
		.cta-links-hidden {
			display: none;
		}
	</style>
	<div class='wp_cta_select_display'>
		<div class="inside">
			<div class="wp-cta-option-row">
				<div class='cta-options-label'>
					<label for=keyword>
					Call to Action Template
					</label>
				</div>
				<div class='cta-options-row'>
				<?php
				 foreach ( $cta_list as $cta ) {
					$this_id = $cta->ID;
					$this_link = get_permalink( $this_id );
					$this_link = preg_replace('/\?.*/', '', $this_link); ?>

					<a class='thickbox cta-links-hidden' id="cta-<?php echo $this_id;?>" href='<?php echo $this_link;?>?wp-cta-variation-id=0&wp_cta_iframe_window=on&post_id=<?php echo $cta->ID; ?>&TB_iframe=true&width=640&height=703'>Preview</a>

				<?php } ?>
				<select multiple name='cta_display_list[]' id="cta_template_selection" style='display:none;'>
				<?php
				foreach ( $cta_list as $cta  ) {
					$this_id = $cta->ID;
					$this_link = get_permalink( $this_id );
					$title = $cta->post_title;
					$selected = (in_array($this_id, $cta_display_list)) ? " selected='selected'" : "";

					echo '<option', $selected, ' value="'.$this_id.'" rel="work?" >'.$title.'</option>';

				} ?>
				</select><br /><span class="description">Click the above select box to select call to action templates to insert</span>
				</div>
			</div>
		</div>
	</div>

	<?php
}

function wp_cta_display_controller()
{
	$CTAExtensions = CTALoadExtensions();
	$extension_data = $CTAExtensions->definitions;
	
	foreach ($extension_data['wp-cta-controller']['settings'] as $key=>$field)
	{
		if ( isset($field['region']) && $field['region'] =='cta-placement-controls')
		{
			wp_cta_render_setting($field);
		}
	}
}

//save the meta box action
add_action( 'save_post', 'wp_cta_display_meta_save', 10);
function wp_cta_display_meta_save($post_id)
{

	global $post;

	if (!isset($post)){
		return;
	}
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return;
	}

	$exclude[] = 'attachment';
	$exclude[] = 'revisions';
	$exclude[] = 'nav_menu_item';
	$exclude[] = 'wp-lead';
	$exclude[] = 'automation';
	$exclude[] = 'rule';
	$exclude[] = 'list';
	$exclude[] = 'wp-call-to-action';
	$exclude[] = 'tracking-event';
	$exclude[] = 'inbound-forms';

	if (in_array($post->post_type , $exclude)) {
		return;
	}

	// add filter
		
	$CTAExtensions = CTALoadExtensions();
	$extension_data = $CTAExtensions->definitions;
	
	foreach ($extension_data['wp-cta-controller']['settings'] as $key=>$field)
	{
		( isset($field['global']) && $field['global'] ) ? $field['id'] : $field['id'] = $field['id'];	
				
		if($field['type'] == 'tax_select'){
			continue;
		}		
		
		$old = get_post_meta($post_id, $field['id'], true);
		(isset($_POST[$field['id']])) ? $new = $_POST[$field['id']] : $new = null;
		
		/*
		echo $field['id'].' old:'.$old.'<br>';
		echo $field['id'].' new:'.$new.'<br>';
		*/
		
		if (isset($new) && $new != $old ) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
		
	}

	if ( isset($_POST['cta_display_list']) ) {
		update_post_meta($post_id, "cta_display_list", $_POST['cta_display_list'] );
	} else {
		delete_post_meta($post_id, "cta_display_list" ); // remove empty checkboxes
	}

	if ( isset($_POST['cta_alignment']) ) { // if we get new data
		update_post_meta($post_id, "cta_alignment", $_POST['cta_alignment'] );
	} else {
		delete_post_meta($post_id, "cta_alignment" );
	}

}