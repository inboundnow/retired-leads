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

	$wp_cta_per_post_options = wp_cta_per_page_settings();

	//$content_placements_profile_id = get_post_meta($post->ID, 'id here');
	$wp_cta_post_template_ids = get_post_meta($post->ID, 'cta_display_list');
	$wp_cta_placement = get_post_meta($post->ID, 'wp_cta_content_placement');

	if (!empty($wp_cta_placement))
	{
		$placement = $wp_cta_placement[0];
	}
	else
	{
		$placement = 'off';
	}

	$wp_cta_alignment = get_post_meta($post->ID, 'wp_cta_alignment');
	if (!empty($wp_cta_alignment)){
		$alignment = $wp_cta_alignment[0];
	} else {
		$alignment = 'center';
	}

	//print_r($wp_cta_post_template_ids);

   // $content_placements_post_status = get_post_meta($post->ID, 'id here');
	wp_cta_display_metabox(); // renders checkboxes
	wp_cta_render_metaboxes($wp_cta_per_post_options);?>

	<?php
}



function wp_cta_display_metabox() {
	global $post;

	$args = array(
	'posts_per_page'  => -1,
	'post_type'=> 'wp-call-to-action');

	$cta_list = get_posts($args);

	$cta_display_list = get_post_meta($post->ID ,'cta_display_list', true);
	$cta_display_list = ($cta_display_list != '') ? $cta_display_list : array();
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
		jQuery('select#wp_cta_content_placement').on('change', function () {
			var this_val = jQuery(this).val();
			jQuery(".dynamic-visable-on").hide();
			console.log(this_val);
			jQuery('.reveal-' + this_val).removeClass('inbound-hidden-row').show().addClass('dynamic-visable-on');
		});
		var onload = jQuery('select#wp_cta_content_placement').val();
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



//save the meta box action
add_action( 'save_post', 'wp_cta_display_meta_save', 10, 2 );
function wp_cta_display_meta_save($post_id, $post)
{
	global $post;
	if (!isset($post))
		return;
	if ($post->post_type=='wp-call-to-action')
		return;

	$wp_cta_per_post_options = wp_cta_per_page_settings();

	wp_cta_meta_save_loop($wp_cta_per_post_options);

    if ( isset($_POST['cta_display_list']) ) { // if we get new data
        update_post_meta($post_id, "cta_display_list", $_POST['cta_display_list'] );
    } else {
    	delete_post_meta($post_id, "cta_display_list" ); // remove empty checkboxes
    }

    if ( isset($_POST['wp_cta_alignment']) ) { // if we get new data
        update_post_meta($post_id, "wp_cta_alignment", $_POST['wp_cta_alignment'] );
    } else {
    	delete_post_meta($post_id, "wp_cta_alignment" );
    }

}

function wp_cta_per_page_settings()
{

	global $post;

	if (!isset($post))
		return;

	$post_type = $post->post_type;
	$var_id = ''; // default

	if (isset($post)&&$post->post_type==='landing-page'){
		$var_id = '-' . lp_ab_testing_get_current_variation_id();
	}

	if (isset($_GET['clone'])) {
		$var_id = '-' . $_GET['clone'];
	}

	if (isset($_GET['lp-variation-id'])) {
		$var_id = '-' . $_GET['lp-variation-id'];
	}

	$wp_cta_per_post_options = array(
		array(
			'label' => 'Placement on Page',
			'description' => "Where would you like to insert the CTA on this page?",
			'id'  => 'wp_cta_content_placement' . $var_id,
			'type'  => 'dropdown',
			'default'  => 'off',
			'options' => array( 'off' => 'Call to Action Off',
								'above'=>'Above Content',
								'middle' => 'Middle of Content',
								'below' => 'Below Content',
								'widget_1' => 'Use Dynamic Sidebar Widget',
								'popup' => 'Use Popup',
								'slideout' => 'Use Slide Out',
								'shortcode' => "Use Shortcode (insert from editor button)"),
			'context'  => 'normal',
			'class' => 'cta-per-page-option'
			),
		array(
			'label' => 'Slide Out Alignment',
			'description' => "Slide out from the right or left?",
			'id'  => 'wp_cta_slide_out_alignment' . $var_id,
			'type'  => 'dropdown',
			'default'  => 'right',
			'options' => array( 'right' => 'Slide out from Right',
								'left'=> 'Slide out from Left',
								),
			'context'  => 'normal',
			'class' => 'cta-per-page-option cta-slide-out-option',
			'reveal_on' => 'slideout'
			),
		array(
			'label' => 'Scroll length to show',
			'description' => "This will determine how far down the page the slideout should show on page scroll",
			'id'  => 'wp_cta_slide_out_reveal' . $var_id,
			'type'  => 'dropdown',
			'default'  => '100',
			'options' => array('100'=>'Show at very bottom of this page', '90'=>'When a visitor has scrolled 90% Down the Page', '80'=>'When a visitor has scrolled 80% Down the Page', '70'=>' When a visitor has scrolled 70% Down the Page', '60'=>'When a visitor has scrolled 60% Down the Page', '50'=>'Half Way Down the Page', '40'=>'When a visitor has scrolled 40% Down the Page', '30'=>'When a visitor has scrolled 30% Down the Page', '20'=>'When a visitor has scrolled 20% Down the Page', '10'=>'When a visitor has scrolled 10% Down the Page', '1'=>'Show immediately at top of this Page'),
			'context'  => 'normal',
			'class' => 'cta-per-page-option cta-slide-out-option',
			'reveal_on' => 'slideout'
			),
		array(
			'label' => 'slideout Advanced Header',
			'description' => "<div style='margin-top:10px; margin-left:10px;'><h4 style='margin-bottom:0px;'>Advanced Slideout Settings</h4></div>",
			'id'  => 'slideout_advanced_message' . $var_id,
			'type'  => 'html-block',
			'default'  => '',
			'context'  => 'normal',
			'class' => '',
			'reveal_on' => 'slideout'
			),
		array(
			'label' => 'Slideout Speed',
			'description' => "(Advanced Option) How fast do you want the slideout to enter? Time in seconds. For milliseconds use decimal points. example: 500ms is .5",
			'id'  => 'wp_cta_slide_out_speed' . $var_id,
			'type'  => 'text',
			'default'  => '1',
			'context'  => 'normal',
			'class' => 'cta-per-page-option cta-slideout-option',
			'reveal_on' => 'slideout'
			),
		array(
			'label' => 'Keep in view once fired?',
			'description' => "Do you want to keep the slide out on the page if the user scrolls away? If this is toggled no, once the user scrolls to another part of the page, this slideout is hidden.",
			'id'  => 'wp_cta_slide_out_keep_open' . $var_id,
			'type'  => 'dropdown',
			'options' => array('no'=>'no', 'yes' => 'yes'),
			'default'  => 'no',
			'context'  => 'normal',
			'class' => 'cta-per-page-option cta-slideout-option',
			'reveal_on' => 'slideout'
			),
		array(
			'label' => 'Attach Slideout to Page Element',
			'description' => "(Advanced Option) You can attach the slide out event to a CSS selector here.",
			'id'  => 'wp_cta_slide_out_element' . $var_id,
			'type'  => 'text',
			'default'  => '',
			'context'  => 'normal',
			'class' => 'cta-per-page-option cta-slideout-option',
			'reveal_on' => 'slideout'
			),
		array(
			'label' => 'Popup Delay Time',
			'description' => "How long do you want to delay the popup before showing on the page? Time in seconds",
			'id'  => 'wp_cta_popup_timeout' . $var_id,
			'type'  => 'text',
			'default'  => '7',
			'context'  => 'normal',
			'class' => 'cta-per-page-option cta-slideout-option',
			'reveal_on' => 'popup'
			),
		array(
			'label' => 'Popup Advanced Header',
			'description' => "<div style='margin-top:10px; margin-left:10px;'><h4 style='margin-bottom:0px;'>Advanced Popup Settings</h4></div>",
			'id'  => 'popup_advanced_message' . $var_id,
			'type'  => 'html-block',
			'default'  => '',
			'context'  => 'normal',
			'class' => '',
			'reveal_on' => 'popup'
			),
		array(
			'label' => 'Show Popup Only Once Per Visitor?',
			'description' => "How long do you want to delay the popup before showing on the page? Time in seconds",
			'id'  => 'wp_cta_popup_cookie' . $var_id,
			'type'  => 'dropdown',
			'options' => array('yes' => 'yes','no'=>'no'),
			'default'  => 'no',
			'context'  => 'normal',
			'class' => 'cta-per-page-option cta-slideout-option',
			'reveal_on' => 'popup'
			),
	   array(
		   'label' => 'Don\'t Reshow Popup for X days',
		   'description' => "How long do you want to wait to show the same visitor this popup again?",
		   'id'  => 'wp_cta_popup_cookie_length' . $var_id,
		   'type'  => 'text',
		   'default'  => '7',
		   'context'  => 'normal',
		   'class' => 'cta-per-page-option cta-slideout-option',
		   'reveal_on' => 'popup'
		   ),
	   array(
		   'label' => 'Pageviews Before Popup Appears',
		   'description' => "How many page views does the visitor need to see the popup?",
		   'id'  => 'wp_cta_popup_pageviews' . $var_id,
		   'type'  => 'text',
		   'default'  => 0,
		   'context'  => 'normal',
		   'class' => 'cta-per-page-option cta-slideout-option',
		   'reveal_on' => 'popup'
		   ),
		array(
			'label' => 'shortcode Message',
			'description' => "<div style='margin-top:10px; margin-left:10px;'><p>To use a shortcode to display your Call to Action. Insert the <input type='text' style='width:112px;' class='regular-text code' readonly='readonly' value='[insert_cta]'> shortcode in the content above.</p><p><b>OR</b> Click on the power button icon <span class='inbound-power'></span> in the editor above and select 'Insert Call to Action', this option will use different CTAs ids than the ones selected in this metabox</p></div>",
			'id'  => 'shortcode_message' . $var_id,
			'type'  => 'html-block',
			'default'  => '',
			'context'  => 'normal',
			'class' => '',
			'reveal_on' => 'shortcode'
			),
		array(
			'label' => 'Sidebar Message',
			'description' => "<div style='margin-top:10px; margin-left:10px;'><p>This option will place the selected CTA templates into the dynamic sidebar widget on this page. Make sure you have added the dynamic Call to Action widget to the sidebar of this page for this option to work.</p><p>To add the dynamic sidebar widget to this page, go into appearance > widgets and add the widget to the sidebar of your choice</p></div>",
			'id'  => 'sidebar_message' . $var_id,
			'type'  => 'html-block',
			'default'  => '',
			'context'  => 'normal',
			'class' => '',
			'reveal_on' => 'widget_1'
			),
		array(
			'label' => 'Below Message',
			'description' => "<div style='margin-top:10px; margin-left:10px;'><p>Your Call to Action will be inserted into the bottom of the page/post.</p></div>",
			'id'  => 'below_message' . $var_id,
			'type'  => 'html-block',
			'default'  => '',
			'context'  => 'normal',
			'class' => '',
			'reveal_on' => 'below'
			),
		array(
			'label' => 'Above Message',
			'description' => "<div style='margin-top:10px; margin-left:10px;'><p>Your Call to Action will be inserted into the top of the page/post.</p></div>",
			'id'  => 'above_message' . $var_id,
			'type'  => 'html-block',
			'default'  => '',
			'context'  => 'normal',
			'class' => '',
			'reveal_on' => 'above'
			),
		array(
			'label' => 'Middle Message',
			'description' => "<div style='margin-top:10px; margin-left:10px;'><p>Your Call to Action will be inserted into the middle of the page/post's content.</p></div>",
			'id'  => 'above_message' . $var_id,
			'type'  => 'html-block',
			'default'  => '',
			'context'  => 'normal',
			'class' => '',
			'reveal_on' => 'middle'
			),

	);
	return $wp_cta_per_post_options;
}