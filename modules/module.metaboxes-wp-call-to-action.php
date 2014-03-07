<?php

//hook add_meta_box action into custom call fuction
//wp_cta_generate_meta is contained in functions.php
add_action('add_meta_boxes', 'wp_cta_generate_meta');
function wp_cta_generate_meta()
{
	global $post;
	$CTAExtensions = CTALoadExtensions();

	if ($post->post_type!='wp-call-to-action') {
		return;
	}

	/* Template Select Metabox */
	add_meta_box(
		'wp_cta_metabox_select_template', // $id
		__( 'Template Options', 'wpcta_custom_meta' ),
		'wp_cta_display_meta_box_select_template', // $callback
		'wp-call-to-action', // $page
		'normal', // $context
		'high'); // $priority


	/* render templates and extension metaboxes */
	$extension_data = $CTAExtensions->definitions;
	$current_template = get_post_meta( $post->ID , 'wp-cta-selected-template' , true);
	$current_template = apply_filters('wp_cta_variation_selected_template',$current_template, $post);

	($extension_data) ? $extension_data : $extension_data = array();

	foreach ($extension_data as $key=>$data)
	{

		if ( ( isset($data['info']['data_type'] ) &&  $data['info']['data_type'] =='template' && $key==$current_template )  )
		{

			$template_name = ucwords(str_replace('-',' ',$key));
			$id = strtolower(str_replace(' ','-',$key));
			add_meta_box(
				"wp_cta_{$id}_custom_meta_box", // $id
				__( "<small>$template_name Options:</small>", "wp_cta_{$key}_custom_meta" ),
				'wp_cta_show_template_options_metabox', // $callback
				'wp-call-to-action', // post-type
				'normal', // $context
				'default',// $priority
				array('key'=>$key)
				); //callback args
		}
	}

	/* extension only */
	foreach ($extension_data as $key=>$data)
	{
		if (substr($key,0,4)=='ext-' || isset($data['info']['data_type']) && $data['info']['data_type'] =='metabox')
		{

			$id = "metabox-".$key;

			(isset($data['info']['label'])) ? $name = $data['info']['label'] : $name = ucwords(str_replace(array('-','ext '),' ',$key). " Extension Options");
			(isset($data['info']['position'])) ? $position = $data['info']['position'] : $position = "normal";
			(isset($data['info']['priority'])) ? $priority = $data['info']['priority'] : $priority = "default";

			//echo $key."<br>";
			add_meta_box(
				"wp_cta_{$id}_custom_meta_box", // $id
				__( "$name", "wp_cta" ),
				'wp_cta_show_metabox', // $callback
				'wp-call-to-action', // post-type
				$position , // $context
				$priority ,// $priority
				array('key'=>$key)
				); //callback args

		}
	}

	/* Advanced Call to Action Options */
	 add_meta_box(
        'wp_cta_tracking_metabox', // $id
        'Advanced Call to Action Options', // $title
        'wp_cta_show_advanced_settings_metabox', // $callback
        'wp-call-to-action', // $page
        'normal', // $context
        'low'); // $priority

	/* Custom CSS */
	add_meta_box(
		'wp_cta_3_custom_css',
		'Custom CSS',
		'wp_cta_custom_css_input',
		'wp-call-to-action',
		'normal',
		'low');

	/* Custom JS */
	add_meta_box(
		'wp_cta_3_custom_js',
		'Custom JS',
		'wp_cta_custom_js_input',
		'wp-call-to-action',
		'normal',
		'low');
}


function wp_cta_show_metabox($post,$key)
{
	$CTAExtensions = CTALoadExtensions();
	$extension_data = $CTAExtensions->definitions;
	
	$key = $key['args']['key'];

	$wp_cta_custom_fields = $extension_data[$key]['settings'];

	$wp_cta_custom_fields = apply_filters('wp_cta_show_metabox',$wp_cta_custom_fields, $key);

	inbound_template_metabox_render('cta' , $key, $wp_cta_custom_fields, $post);
}

/* template select metabox */
function wp_cta_display_meta_box_select_template() {
	global $post;

	$template =  get_post_meta($post->ID, 'wp-cta-selected-template', true);
	$template = apply_filters('wp_cta_selected_template',$template);

	if (!isset($template)||isset($template)&&!$template){ $template = 'default';}

	$name = apply_filters('wp_cta_selected_template_id','wp-cta-selected-template');

	// Use nonce for verification
	echo "<input type='hidden' name='wp_cta_wp-cta_custom_fields_nonce' value='".wp_create_nonce('wp-cta-nonce')."' />";
	?>

	<div id="wp_cta_template_change"><h2><a class="button" id="wp-cta-change-template-button">Choose Another Template</a></div>
	<input type='hidden' id='wp_cta_select_template' name='<?php echo $name; ?>' value='<?php echo $template; ?>'>
	<div id="template-display-options"></div>

	<?php
}


function wp_cta_show_template_options_metabox($post,$key)
{

	$CTAExtensions = CTALoadExtensions();
	$extension_data = $CTAExtensions->definitions;

	$key = $key['args']['key'];

	$wp_cta_custom_fields = $extension_data[$key]['settings'];

	$wp_cta_custom_fields = apply_filters('wp_cta_template_options', $wp_cta_custom_fields );

	inbound_template_metabox_render( 'cta' , $key , $wp_cta_custom_fields , $post);
}


function wp_cta_show_advanced_settings_metabox() {
    global $post;

	$CTAExtensions = CTALoadExtensions();
	$extension_data = $CTAExtensions->definitions;

    // Use nonce for verification
    //echo '<input type="hidden" name="custom_wp_cta_metaboxes_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    wp_nonce_field('save-custom-wp-cta-boxes','custom_wp_cta_metaboxes_nonce');
    // Begin the field table and loop
    echo '<div class="form-table">';
    echo '<div class="cta-description-box"><span class="calc button-secondary">Calculate height/width</span></div>';


	foreach ($extension_data['wp-cta']['settings'] as $key=>$field)
	{
		if ( isset($field['region']) && $field['region'] =='advanced')
		{
			( isset($field['global']) && $field['global'] ) ? $field['id'] : $field['id'] = "wp-cta-".$field['id'];

			$field['id'] = apply_filters( 'wp_cta_ab_field_id' , $field['id'] );

			wp_cta_render_setting($field);
		}
	}
	
	do_action( "wordpress_cta_add_meta" ); // Action for adding extra meta boxes/options

   echo '</div>'; // end table
}


function wp_cta_render_setting($field) {
	global $post, $wpdb;


	$meta = get_post_meta($post->ID, $field['id'], true);

	if ( !isset( $field['default'] ) ) {
		$field['default'] = '';
	}
	//echo $field['id'].':'.var_dump($meta);
	//echo '<br>';

	$final['value'] = (!empty($meta)) ? $meta : $field['default'];
	$meta_class = (isset($field['class'])) ? " " . $field['class'] : '';
	$dynamic_hide = (isset($field['reveal_on'])) ? ' inbound-hidden-row' : '';
	$reveal_on = (isset($field['reveal_on'])) ? ' reveal-' . $field['reveal_on'] : '';

	// begin a table row with
	$no_label = array('html-block');

	echo '<div id='.$field['id'].' class="wp-cta-option-row '.$meta_class. $dynamic_hide.  $reveal_on.'">';
	if (!in_array($field['type'],$no_label)) {
		echo'<div class="wp_cta_label'.$meta_class. $dynamic_hide.  $reveal_on.'"><label class="'.$meta_class.'" for="'.$field['id'].'">'.$field['label'].'</label></div>';
	}
	echo '<div class="wp-cta-option-area '.$meta_class.' field-'.$field['type'].'">';
			switch($field['type']) {
				// text
				case 'text':
					echo '<input type="text" class="'.$meta_class.'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$final['value'].'" size="30" />
							<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
					break;
				case 'html-block':
					echo '<div class="'.$meta_class.'">'.$field['description'].'</div>';
					break;
				case 'dropdown':
					echo '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$meta_class.'">';
					foreach ($field['options'] as $value=>$label) {
						echo '<option', $final['value'] == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';
					}
					echo '</select><div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
					break;
				// select
				case 'image-select':
					echo '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$meta_class.'">';
					foreach ($field['options'] as $value=>$label) {
						echo '<option', $final['value'] == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';
					}
					echo '</select><br /><div class="wp-cta-image-container"></div></br><span class="description">'.$field['description'].'</span>';

					break;
				// textarea
				case 'textarea':
					echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="250" rows="6">'.$final['value'].'</textarea>
							<br /><span class="description">'.$field['description'].'</span>';
					break;
				// checkbox
				case 'checkbox':
					echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$final['value'] ? ' checked="checked"' : '','/>
							<label for="'.$field['id'].'">'.$field['description'].'</label>';
					break;
				// radio
				case 'radio':
					foreach ( $field['options'] as $option ) {
						echo '<input type="radio" name="'.$field['id'].'" id="'.$option['value'].'" value="'.$option['value'].'" ',$final['value'] == $option['value'] ? ' checked="checked"' : '',' />
								<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
					}
					echo '<span class="description">'.$field['description'].'</span>';
					break;
				// checkbox_group
				case 'checkbox_group':
					foreach ($field['options'] as $option) {
						echo '<input type="checkbox" value="'.$option['value'].'" name="'.$field['id'].'[]" id="'.$option['value'].'"',$final['value'] && in_array($option['value'], $final['value']) ? ' checked="checked"' : '',' />
								<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
					}
					echo '<span class="description">'.$field['description'].'</span>';
					break;
				case 'meta_vals':
					$post_type = 'wp-lead';
					$query = "
						SELECT DISTINCT($wpdb->postmeta.meta_key)
						FROM $wpdb->posts
						LEFT JOIN $wpdb->postmeta
						ON $wpdb->posts.ID = $wpdb->postmeta.post_id
						WHERE $wpdb->posts.post_type = 'wp-lead'
						AND $wpdb->postmeta.meta_key != ''
						AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
						AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
					";
					$sql = 'SELECT DISTINCT meta_key FROM '.$wpdb->postmeta;
					$meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));
				   // print_r($fields);
					$list = get_post_meta( $post->ID, 'wp_cta_global_bt_values', true);
					//print_r($list);
					echo '<select multiple name="'.$field['id'].'[]" class="inbound-multi-select" id="'.$field['id'].'">';
						$nice_names = array(
						"wpleads_first_name" => "First Name",
						"wpleads_last_name" => "Last Name",
						"wpleads_email_address" => "Email Address",
						"wpleads_city" => "City",
						"wpleads_areaCode" => "Area Code",
						"wpleads_country_name" => "Country Name",
						"wpleads_region_code" => "State Abbreviation",
						"wpleads_region_name" => "State Name",
						"wp_lead_status" => "Lead Status",
						"events_triggered" => "Number of Events Triggered",
						"lp_page_views_count" => "Page View Count",
						"wpl-lead-conversion-count" => "Number of Conversions"
					);

					foreach ($meta_keys as $meta_key)
					{
						if (array_key_exists($meta_key, $nice_names)) {
							$label = $nice_names[$meta_key];


							(in_array($meta_key, $list)) ? $selected = " selected='selected'" : $selected ="";

							echo '<option', $selected, ' value="'.$meta_key.'" rel="" >'.$label.'</option>';

						}
					}
					echo "</select><br><span class='description'>'".$field['description']."'</span>";
				break;

				case 'multiselect':

					$selected_lists = $final['value'];

					echo '<select multiple name="'.$field['id'].'[]" class="inbound-multi-select" id="'.$field['id'].'">';


					foreach ( $field['options'] as $id => $value )
					{
						(in_array($id, $selected_lists)) ? $selected = " selected='selected'" : $selected ="";
						echo '<option', $selected, ' value="'.$id.'" rel="" >'.$value.'</option>';

					}
					echo "</select><br><span class='description'>'".$field['description']."'</span>";
					break;

			} //end switch
	echo '</div></div>';
}

// Add in Main Headline
add_action( 'edit_form_after_title', 'wp_cta_wp_call_to_action_header_area' );
function wp_cta_wp_call_to_action_header_area()
{
   global $post;

	$wp_cta_variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';

	$variation_notes = apply_filters('wp_cta_edit_variation_notes', ''  );


    if ( empty ( $post ) || 'wp-call-to-action' !== get_post_type( $GLOBALS['post'] ) ) {
        return;
	}

    echo '<span id="cta_shortcode_form" style="display:none; font-size: 13px;margin-left: 15px;">
         Shortcode: <input type="text" style="width: 130px;" class="regular-text code short-shortcode-input" readonly="readonly" id="shortcode" name="shortcode" value=\'[cta id="'.$post->ID.'"]\'>
        <div class="wp_cta_tooltip" style="margin-left: 0px;" title="You can copy and paste this shortcode into any page or post to render this call to action. You can also insert CTAs from the wordpress editor on any given page"></div></span>';



		echo "<div id='wp-cta-notes-area' data-field-type='text'>";
   		wp_cta_display_notes_input('wp-cta-variation-notes',$variation_notes);
    	echo '</div><div id="wp-cta-current-view">'.$wp_cta_variation.'</div><div id="switch-wp-cta">0</div>';

   	// Set frontend editor params
    if(isset($_REQUEST['frontend']) && $_REQUEST['frontend'] == 'true') {
    echo('<input type="hidden" name="frontend" id="frontend-on" value="true" />');
	}

}


function wp_cta_display_notes_input($id , $variation_notes)
{
	//echo $id;
	$id = apply_filters('wp_cta_display_notes_input_id',$id);

	echo "<span id='add-wp-cta-notes'>Notes:</span><input placeholder='Add Notes to your variation. Example: This version is testing a green submit button' type='text' class='wp-cta-notes' name='{$id}' id='{$id}' value='{$variation_notes}' size='30'>";
}


add_action( 'save_post', 'wp_cta_save_notes_area' );
function wp_cta_save_notes_area( $post_id )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
	}

    if ( ! current_user_can( 'edit_post', $post_id ) ){
        return;
	}

    $key = 'wp-cta-variation-notes';
	$key = apply_filters( 'wp_cta_display_notes_input_id' , $key );

    if ( isset ( $_POST[ $key ] ) ){
        return update_post_meta( $post_id, $key, $_POST[ $key ] );
	}

}


add_filter( 'enter_title_here', 'wp_cta_change_enter_title_text', 10, 2 );
function wp_cta_change_enter_title_text( $text, $post ) {
	if ($post->post_type=='wp-call-to-action')
	{
        return 'Enter Call to Action Description';
	}
	else
	{
		return $text;
	}
}



add_action('admin_notices', 'wp_cta_display_meta_box_select_template_container');

// Render select template box
function wp_cta_display_meta_box_select_template_container() {
	global $wp_cta_data, $post, $current_url;

	$CTAExtensions = CTALoadExtensions();

	if (isset($post)&&$post->post_type!='wp-call-to-action'||!isset($post)){ return false; }

	( !strstr( $current_url, 'post-new.php')) ?  $toggle = "display:none" : $toggle = "";

	$extension_data = $CTAExtensions->definitions;
	unset($extension_data['wp-cta']);
	unset($extension_data['wp-cta-controller']);

	$uploads = wp_upload_dir();
	$uploads_path = $uploads['basedir'];
	$extended_path = $uploads_path.'/wp-call-to-actions/templates/';

	$template =  get_post_meta($post->ID, 'wp-cta-selected-template', true);
	$template = apply_filters('wp_cta_selected_template',$template);

	echo "<div class='wp-cta-template-selector-container' style='{$toggle}'>";
	echo "<div class='wp-cta-selection-heading'>";
	echo "<h1>Select Your Call to Action Template!</h1>";
	echo '<a class="button-secondary" style="display:none;" id="wp-cta-cancel-selection">Cancel Template Change</a>';
	echo "</div>";
		echo '<ul id="template-filter" >';
			echo '<li><a href="#" data-filter=".template-item-boxes">All</a></li>';
			$categories = array();
			foreach ( $CTAExtensions->template_categories as $cat)
			{

				$category_slug = str_replace(' ','-',$cat['value']);
				$category_slug = strtolower($category_slug);
				$cat['value'] = ucwords($cat['value']);
				if (!in_array($cat['value'],$categories))
				{
					echo '<li><a href="#" data-filter=".'.$category_slug.'">'.$cat['value'].'</a></li>';
					$categories[] = $cat['value'];
				}

			}
		echo "</ul>";
		echo '<div id="templates-container" >';

		foreach ($extension_data as $this_template=>$data)
		{

			if (isset($data['info']['data_type'])&&$data['info']['data_type']!='template'){
				continue;
			}

			$cats = explode( ',' , $data['info']['category'] );
			foreach ($cats as $key => $cat)
			{
                $cat = trim($cat);
				$cat = str_replace(' ', '-', $cat);
				$cats[$key] = trim(strtolower($cat));
			}

			$cat_slug = implode(' ', $cats);

			// Get Thumbnail
			if (file_exists(WP_CTA_PATH.'templates/'.$this_template."/thumbnail.png"))
			{
				$thumbnail = WP_CTA_URLPATH.'templates/'.$this_template."/thumbnail.png";
			}
			else
			{
				$thumbnail = WP_CTA_UPLOADS_URLPATH.$this_template."/thumbnail.png";
			}
			?>
			<div id='template-item' class="<?php echo $cat_slug; ?> template-item-boxes">
				<div id="template-box">
					<div class="wp_cta_tooltip_templates" title="<?php echo $data['info']['description']; ?>"></div>
				<a class='wp_cta_select_template' href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>'>
					<img src="<?php echo $thumbnail; ?>" class='template-thumbnail' alt="<?php echo $data['info']['label']; ?>" id='wp_cta_<?php echo $this_template; ?>'>
				</a>

					<div id="template-title" style="text-align: center;
font-size: 14px; padding-top: 10px;"><?php echo $data['info']['label']; ?></div>
					<!-- |<a href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>' class='wp_cta_select_template'>Select</a>
					<a class='thickbox <?php echo $cat_slug;?>' href='<?php echo $data['info']['demo'];?>' id='wp_cta_preview_this_template'>Preview</a> -->
				</div>
			</div>
			<?php
		}
	echo '</div>';
	echo "<div class='clear'></div>";
	echo "</div>";
	echo "<div style='display:none;' class='currently_selected'>This is Currently Selected</a></div>";
}


/* Custom CSS */
function wp_cta_custom_css_input() {
	global $post;

	echo "<em>Custom CSS may be required to customize this call to action. Insert Your CSS Below. Format: #element-id { display:none !important; }</em>";
	echo '<input type="hidden" name="wp-cta-custom-css-noncename" id="wp_cta_custom_css_noncename" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	$custom_css_meta_key = apply_filters('wp_cta_custom_css_meta_key','wp-cta-custom-css');
	$custom_css = get_post_meta($post->ID,$custom_css_meta_key,true);

	$line_count = substr_count( $custom_css , "\n" );
	($line_count) ? $line_count : $line_count = 5;

	echo '<textarea name="'.$custom_css_meta_key.'" id="wp-cta-custom-css" rows="'. $line_count .'" cols="30" style="width:100%;">'.$custom_css .'</textarea>';
}

add_action('save_post', 'wp_call_to_actions_save_custom_css');
function wp_call_to_actions_save_custom_css($post_id) {
	global $post;
	if (!isset($post)||!isset($_POST['wp-cta-custom-css']))
		return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;


	$custom_css_meta_key = apply_filters('wp_cta_custom_css_meta_key','wp-cta-custom-css');

	$wp_cta_custom_css = $_POST[$custom_css_meta_key];
	update_post_meta($post_id, 'wp-cta-custom-css', $wp_cta_custom_css);
}

/* Custom JS */

function wp_cta_custom_js_input() {
	global $post;
	echo "<em></em>";
	//echo wp_create_nonce('wp-cta-custom-js');exit;
	$custom_js_meta_key = apply_filters('wp_cta_custom_js_meta_key','wp-cta-custom-js');
	$custom_js = get_post_meta($post->ID,$custom_js_meta_key,true);
	$line_count = substr_count( $custom_js , "\n" );

	($line_count) ? $line_count : $line_count = 5;

	echo '<input type="hidden" name="wp_cta_custom_js_noncename" id="wp_cta_custom_js_noncename" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	echo '<textarea name="'.$custom_js_meta_key.'" id="wp_cta_custom_js" rows="'.$line_count.'" cols="30" style="width:100%;">'.$custom_js.'</textarea>';
}

add_action('save_post', 'wp_call_to_actions_save_custom_js');
function wp_call_to_actions_save_custom_js($post_id) {
	global $post;

	if (!isset($post)||!isset($_POST['wp-cta-custom-js']))
		return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	$custom_js_meta_key = apply_filters('wp_cta_custom_js_meta_key','wp-cta-custom-js');

	$wp_cta_custom_js = $_POST[$custom_js_meta_key];

	update_post_meta($post_id, 'wp-cta-custom-js', $wp_cta_custom_js);
}



add_action('save_post', 'wp_cta_save_meta');
function wp_cta_save_meta($post_id) {
	global $post;
	$CTAExtensions = CTALoadExtensions();

	$extension_data =  $CTAExtensions->definitions;

	if (!isset($post)) {
		return;
	}

	if ($post->post_type=='revision') {
		return;
	}

	if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||(isset($_POST['post_type'])&&$_POST['post_type']=='revision')) {
		return;
	}

	if ($post->post_type=='wp-call-to-action')
	{
		unset($extension_data['wp-cta-controller']);
		foreach ($extension_data as $key=>$data)
		{

			foreach ($extension_data[$key]['settings'] as $field)
			{
				( isset($field['global']) && $field['global'] ) ? $field['id'] : $field['id'] = $key."-".$field['id'];

				if($field['type'] == 'tax_select'){
					continue;
				}

				$field['id'] = apply_filters( 'wp_cta_ab_field_id' , $field['id'] );

				$old = get_post_meta($post_id, $field['id'], true);

				( isset($_POST[$field['id']]) ) ? $new = $_POST[$field['id']] : $new = null;

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

		}
	}
}

/* remove custom fields from wp-call-to-action cpt */
add_action( 'in_admin_header', 'wp_cta_in_admin_header');
function wp_cta_in_admin_header()
{
	global $post;
	global $wp_meta_boxes;

	if (isset($post)&&$post->post_type=='wp-call-to-action')
	{
		unset( $wp_meta_boxes[get_current_screen()->id]['normal']['core']['postcustom'] );
	}
}
