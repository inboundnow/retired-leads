<?php
/* Start Global Options
function wp_cta_display_options(){
    return array(
        '_is_all_wp_cta'  => 'Every Page',
        '_is_front_wp_cta' => 'Static Front Page',
        '_is_page_wp_cta' => 'Single Page',
        '_is_home_wp_cta' => 'Blog Page',
        '_is_single_wp_cta' => 'Single Post',
        '_is_archive_wp_cta' => 'Archive',
        '_is_author_wp_cta' => 'Author Archive',
        '_is_404_wp_cta' => '404 Page',
        '_is_search_wp_cta' => 'Search Page'
    );
}
// Meta box
add_action('add_meta_boxes', 'wp_cta_add_meta_box');
function wp_cta_add_meta_box() {
        add_meta_box('wp-cta-buttons-meta', __('Call To Action Display', 'wp-call-to-action'),  'wp_cta_metabox_admin', 'wp-call-to-action', 'side');
}

function wp_cta_metabox_admin() {
    global $post;
    $display_options = wp_cta_display_options();

    $default_content = "";
    $default_content .= '<input type="hidden" name="ctaw_settings_noncename" id="ctaw_settings_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
    $default_content .= '<p>Global Settings</p>';
    $default_content .= '<ul id="inline-sortable">';
    foreach ($display_options as $ctaw_display=>$ctaw_name) {
        $default_content .= '<li class="ui-state-default"><label class="selectit"><input value="1" type="checkbox" name="'.$ctaw_display.'" id="post-share-' . $ctaw_display . '"' . checked(get_post_meta($post->ID, $ctaw_display, true), 1, false) . '/> <span>' . __($ctaw_name) . '</span></label></li>';
    }
    $default_content .= '</ul>';
    echo $default_content;
}

add_action('save_post', 'wp_cta_admin_process');
function wp_cta_admin_process($post_ID) {
    if (!isset($_POST['ctaw_settings_noncename']) || !wp_verify_nonce($_POST['ctaw_settings_noncename'], plugin_basename(__FILE__))) {
        return $post_ID;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_ID;

    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_ID))
            return $post_ID;
    } else {
        if (!current_user_can('edit_post', $post_ID))
            return $post_ID;
    }

    if (!wp_is_post_revision($post_ID) && !wp_is_post_autosave($post_ID)) {
        $display_options = wp_cta_display_options();
        foreach ($display_options as $ctaw_display=>$ctaw_name) {
            if (isset($_POST[$ctaw_display]) && $_POST[$ctaw_display] != ''){
                update_post_meta($post_ID, $ctaw_display, 1);
            } else {
                update_post_meta($post_ID, $ctaw_display, 0);
            }
        }
    }
}
*/

add_filter('wp_cta_show_metabox','wp_cta_add_global_demensions' , 10, 2);
function wp_cta_add_global_demensions($field_settings, $key){

	$extension_data = wp_cta_get_extension_data();
	if (isset($extension_data[$key]['info']['data_type']) && $extension_data[$key]['info']['data_type']=='metabox')
		return $field_settings;

	//prepend width and height as setting.
    $var_id = wp_cta_ab_testing_get_current_variation_id();

    if (isset($_GET['clone'])) {
		$var_id = $_GET['clone'];
    }

	$width =  array(
        'label' => 'CTA Width',
        'description' => "Enter the Width of the CTA in pixels. Example: 300 or 300px",
        'id'  => 'wp_cta_width-'.$var_id,
        'type'  => 'number',
        'default'  => '300',
        'class' => 'cta-width',
        'context'  => 'priority',
        'global' => true // disables template name prefix
        );

	$height = array(
        'label' => 'CTA Height',
        'description' => "Enter the Height of the CTA in pixels. Example: 300 or 300px",
        'id'  => 'wp_cta_height-'.$var_id,
        'type'  => 'number',
        'default'  => '300',
        'class' => 'cta-height',
        'context'  => 'priority',
        'global' => true // disables template name prefix
        );

 array_unshift($field_settings, $width, $height);

 return $field_settings;
}
/*
$height_key = 'wp_cta_height-1';
$var_id = 1;
$key_fix = 2;
$height_key = str_replace($var_id, $key_fix, $height_key);
echo $height_key;
*/
add_action( 'save_post', 'wp_cta_save_custom_height_width' );
function wp_cta_save_custom_height_width( $post_id )
{
    global $post;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;
    if (isset($post) && $post->post_type=='wp-call-to-action') {

        $var_id = wp_cta_ab_testing_get_current_variation_id();
        $height_key = 'wp_cta_height-'.$var_id;
        $width_key = 'wp_cta_width-'.$var_id;

        if ( isset ( $_POST[ $height_key ] ) ) {
            update_post_meta( $post_id, $height_key, $_POST[ $height_key ] );
        } else {
            delete_post_meta( $post_id, $height_key );
        }

        if ( isset ( $_POST[ $width_key ] ) ) {
            update_post_meta( $post_id, $width_key, $_POST[ $width_key ] );
        } else {
            delete_post_meta( $post_id, $width_key );
        }
    }
}



/* End global Options */
// Add in Custom Options
function add_wp_cta_post_metaboxes() {
    add_meta_box(
        'wp_cta_tracking_metabox', // $id
        'Advanced Call to Action Options', // $title
        'wp_cta_show_advanced_settings_metabox', // $callback
        'wp-call-to-action', // $page
        'normal', // $context
        'low'); // $priority
}

add_action('add_meta_boxes', 'add_wp_cta_post_metaboxes');

$custom_wp_cta_metaboxes = array(
    array(
        'label' => 'Open Links',
        'description' => "How do you want links on the call to action to work?",
        'id'  => 'link_open_option', // called in template's index.php file with lp_get_value($post, $key, 'checkbox-id-here');
        'type'  => 'dropdown',
        'default'  => 'this_window',
        'options' => array('this_window' => 'Open Links in Same Window (default)','new_tab'=>'Open Links in New Tab'),
        'context'  => 'normal'
        )
);
function wp_cta_show_advanced_settings_metabox() {
    global $custom_wp_cta_metaboxes, $custom_wp_cta_metaboxes_two, $post;
    // Use nonce for verification
    //echo '<input type="hidden" name="custom_wp_cta_metaboxes_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    wp_nonce_field('save-custom-wp-cta-boxes','custom_wp_cta_metaboxes_nonce');
    // Begin the field table and loop
    echo '<div class="form-table">';
    echo '<div class="cta-description-box"><span class="calc button-secondary">Calculate height/width</span></div>';
   	wp_cta_render_metaboxes($custom_wp_cta_metaboxes);
    do_action( "wordpress_cta_add_meta" ); // Action for adding extra meta boxes/options
    echo '</div>'; // end table
}
// This function is going to be replace by Global
function wp_cta_render_metaboxes($meta_boxes) {
	global $post, $wpdb;
	 foreach ($meta_boxes as $field) {
        // get value of this field if it exists for this post
        //print_r($meta_boxes);
        $meta = get_post_meta($post->ID, $field['id'], true);
        $final_meta = (!empty($meta)) ? $meta : $field['default'];
        $field_options_class = (isset($field['options_area'])) ? " " . $field['options_area'] : '';
        $meta_class = (isset($field['class'])) ? " " . $field['class'] : '';
        $dynamic_hide = (isset($field['reveal_on'])) ? ' inbound-hidden-row' : '';
        $reveal_on = (isset($field['reveal_on'])) ? ' reveal-' . $field['reveal_on'] : '';
        // begin a table row with
       	$no_label = array('html-block');
        echo '<div id='.$field['id'].' class="wp-cta-option-row '.$field_options_class .$meta_class. $dynamic_hide.  $reveal_on.'">';
       if (!in_array($field['type'],$no_label)) {
        	echo'<div class="wp_cta_label'.$field_options_class .$meta_class. $dynamic_hide.  $reveal_on.'"><label class="'.$meta_class.'" for="'.$field['id'].'">'.$field['label'].'</label></div>';
            }
          echo '<div class="wp-cta-option-area '.$meta_class.' field-'.$field['type'].'">';
                switch($field['type']) {
                    // text
                    case 'text':
                        echo '<input type="text" class="'.$meta_class.'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$final_meta.'" size="30" />
                                <div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
                    break;
                    case 'html-block':
                        echo '<div class="'.$meta_class.'">'.$field['description'].'</div>';
                    break;
                    case 'dropdown':
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$meta_class.'">';
                        foreach ($field['options'] as $value=>$label) {
                            echo '<option', $meta == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';
                        }
                        echo '</select><div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
                    break;
                    // textarea
                    case 'textarea':
                        echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="250" rows="6">'.$meta.'</textarea>
                                <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // checkbox
                    case 'checkbox':
                        echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','/>
                                <label for="'.$field['id'].'">'.$field['desc'].'</label>';
                    break;
                    // select
                    case 'select':
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                        foreach ($field['options'] as $option) {
                            echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                        }
                        echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // radio
                    case 'radio':
                        foreach ( $field['options'] as $option ) {
                            echo '<input type="radio" name="'.$field['id'].'" id="'.$option['value'].'" value="'.$option['value'].'" ',$meta == $option['value'] ? ' checked="checked"' : '',' />
                                    <label for="'.$option['value'].'">'.$option['label'].'</label><br />';
                        }
                        echo '<span class="description">'.$field['desc'].'</span>';
                    break;
                    // checkbox_group
                    case 'checkbox_group':
                        foreach ($field['options'] as $option) {
                            echo '<input type="checkbox" value="'.$option['value'].'" name="'.$field['id'].'[]" id="'.$option['value'].'"',$meta && in_array($option['value'], $meta) ? ' checked="checked"' : '',' />
                                    <label for="'.$option['value'].'">'.$option['label'].'</label><br />';
                        }
                        echo '<span class="description">'.$field['desc'].'</span>';
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

				    foreach ($meta_keys as $meta_key) {

			    	if (array_key_exists($meta_key, $nice_names)) {
							$label = $nice_names[$meta_key];


							(in_array($meta_key, $list)) ? $selected = " selected='selected'" : $selected ="";

				            echo '<option', $selected, ' value="'.$meta_key.'" rel="" >'.$label.'</option>';

				        }
				    }
		 			echo "</select><br><span class='description'>'".$field['desc']."'</span>";
		 			break;

		 			case 'list_type':
                    $categories = get_terms( 'wplead_list_category', array(
					 	'orderby'    => 'count',
					 	'hide_empty' => 0
					 ) );
					   // print_r($categories);
					    $selected_lists = array();
						$selected_lists = get_post_meta( $post->ID, 'wp_cta_global_bt_lists', true);

                   echo '<select multiple name="'.$field['id'].'[]" class="inbound-multi-select" id="'.$field['id'].'">';


				    foreach ($categories as $cat) {
			    		$term_id = $cat->term_id;
			    		$cat_name = $cat->name;
						//echo $cat_name;

							(in_array($term_id, $selected_lists)) ? $selected = " selected='selected'" : $selected ="";

				            echo '<option', $selected, ' value="'.$term_id.'" rel="" >'.$cat_name.'</option>';

				    }
		 			echo "</select><br><span class='description'>'".$field['desc']."'</span>";
		 			break;

                } //end switch
        echo '</div></div>';
    } // end foreach
}

// Save the Data
add_action('save_post', 'save_wp_cta_post_metaboxes', 15);
function save_wp_cta_post_metaboxes($post_id) {
    global $custom_wp_cta_metaboxes, $custom_wp_cta_metaboxes_two, $post;

	if ( isset($post) && 'wp-call-to-action' == $post->post_type )
    {

		// verify nonce
		if (isset($_POST['custom_wp_cta_metaboxes_nonce'])&&!wp_verify_nonce($_POST['custom_wp_cta_metaboxes_nonce'], 'save-custom-wp-cta-boxes'))
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;

		// check permissions
		if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id))
				return $post_id;
			} elseif (!current_user_can('edit_post', $post_id)) {
				return $post_id;
		}

		wp_cta_meta_save_loop($custom_wp_cta_metaboxes);
		//wp_cta_meta_save_loop($custom_wp_cta_metaboxes_two);
		//exit;
		// save taxonomies
		$post = get_post($post_id);
		if (isset($_POST['category']))
		{
			$category = $_POST['category'];
			wp_set_object_terms( $post_id, $category, 'category' );
		}

    }
}

function wp_cta_meta_save_loop($save_values){

	global $post;

	if (!$save_values)
		return;

    // loop through fields and save the data
    foreach ($save_values as $field) {
        if($field['type'] == 'tax_select') continue;

		//print_r($field);
        $old = get_post_meta($post->ID, $field['id'], true);
        $new = (isset($_POST[$field['id']])) ? $_POST[$field['id']] : '' ;
        if ($new != $old) {
        update_post_meta($post->ID, $field['id'], $new);
        }


    } // end foreach
	//exit;
}

// Add in Main Headline
add_action( 'edit_form_after_title', 'wp_cta_wp_call_to_action_header_area' );
add_action( 'save_post', 'wp_cta_save_notes_area' );

function wp_cta_wp_call_to_action_header_area()
{
   global $post;
	$wp_cta_variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';

	$varaition_notes = get_post_meta( $post->ID , 'wp-cta-variation-notes', true );
    if ( empty ( $post ) || 'wp-call-to-action' !== get_post_type( $GLOBALS['post'] ) )
        return;

    echo '<span id="cta_shortcode_form" style="display:none; font-size: 13px;margin-left: 15px;">
         Shortcode: <input type="text" style="width: 130px;" class="regular-text code short-shortcode-input" readonly="readonly" id="shortcode" name="shortcode" value=\'[cta id="'.$post->ID.'"]\'>
        <div class="wp_cta_tooltip" style="margin-left: 0px;" title="You can copy and paste this shortcode into any page or post to render this call to action. You can also insert CTAs from the wordpress editor on any given page"></div></span>';

    if ( ! $varaition_notes = get_post_meta( $post->ID , 'wp-cta-variation-notes',true ) )
        $varaition_notes = '';

		$varaition_notes = apply_filters('wp_cta_edit_varaition_notes', $varaition_notes, 1);
		echo "<div id='wp-cta-notes-area' data-field-type='text'>";
   		wp_cta_display_notes_input('wp-cta-variation-notes',$varaition_notes);
    	echo '</div><div id="wp-cta-current-view">'.$wp_cta_variation.'</div><div id="switch-wp-cta">0</div>';

   	// Set frontend editor params
    if(isset($_REQUEST['frontend']) && $_REQUEST['frontend'] == 'true') {
    echo('<input type="hidden" name="frontend" id="frontend-on" value="true" />');
	}

}

function wp_cta_save_notes_area( $post_id )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;

    $key = 'wp-cta-variation-notes';

    if ( isset ( $_POST[ $key ] ) )
        return update_post_meta( $post_id, $key, $_POST[ $key ] );

	//echo 1; exit;
    delete_post_meta( $post_id, $key );
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

// Add template select metabox
add_action('add_meta_boxes', 'wp_cta_add_custom_meta_box_select_templates');
function wp_cta_add_custom_meta_box_select_templates() {

	add_meta_box(
		'wp_cta_metabox_select_template', // $id
		__( 'Template Options', 'wpcta_custom_meta' ),
		'wp_cta_display_meta_box_select_template', // $callback
		'wp-call-to-action', // $page
		'normal', // $context
		'high'); // $priority
}

// Render select template box
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

add_action('admin_notices', 'wp_cta_display_meta_box_select_template_container');

// Render select template box
function wp_cta_display_meta_box_select_template_container() {
	global $wp_cta_data, $post,  $extension_data_cats, $current_url;

	if (isset($post)&&$post->post_type!='wp-call-to-action'||!isset($post)){ return false; }

	( !strstr( $current_url, 'post-new.php')) ?  $toggle = "display:none" : $toggle = "";

	$extension_data = wp_cta_get_extension_data();
	unset($extension_data['wp-cta']);

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
			echo '<li><a href="#" data-filter="*">All</a></li>';
			$categories = array();
			foreach ($extension_data_cats as $cat)
			{

				$slug = str_replace(' ','-',$cat['value']);
				$slug = strtolower($slug);
				$cat['value'] = ucwords($cat['value']);
				if (!in_array($cat['value'],$categories))
				{
					echo '<li><a href="#" data-filter=".'.$slug.'">'.$cat['value'].'</a></li>';
					$categories[] = $cat['value'];
				}

			}
		echo "</ul>";
		echo '<div id="templates-container" >';

		foreach ($extension_data as $this_template=>$data)
		{

			if (substr($this_template,0,4)=='ext-')
				continue;


			if (isset($data['info']['data_type'])&&$data['info']['data_type']=='metabox')
				continue;


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
			<div id='template-item' class="<?php echo $cat_slug; ?>">
				<div id="template-box">
					<div class="wp_cta_tooltip_templates" title="<?php echo $data['info']['description']; ?>"></div>
				<a class='wp_cta_select_template' href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>'>
					<img src="<?php echo $thumbnail; ?>" class='template-thumbnail' alt="<?php echo $data['info']['label']; ?>" id='wp_cta_<?php echo $this_template; ?>'>
				</a>
				<p>
					<div id="template-title"><?php echo $data['info']['label']; ?></div>
					<a href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>' class='wp_cta_select_template'>Select</a> |
					<a class='thickbox <?php echo $cat_slug;?>' href='<?php echo $data['info']['demo'];?>' id='wp_cta_preview_this_template'>Preview</a>
				</p>
				</div>
			</div>
			<?php
		}
	echo '</div>';
	echo "<div class='clear'></div>";
	echo "</div>";
	echo "<div style='display:none;' class='currently_selected'>This is Currently Selected</a></div>";
}

// Custom CSS Widget
add_action('add_meta_boxes', 'add_custom_meta_box_wp_cta_custom_css');
add_action('save_post', 'wp_call_to_actions_save_custom_css');

function add_custom_meta_box_wp_cta_custom_css() {
   add_meta_box('wp_cta_3_custom_css', 'Custom CSS', 'wp_cta_custom_css_input', 'wp-call-to-action', 'normal', 'low');
}

function wp_cta_custom_css_input() {
	global $post;

	echo "<em>Custom CSS may be required to customize this call to action. Insert Your CSS Below. Format: #element-id { display:none !important; }</em>";
	echo '<input type="hidden" name="wp-cta-custom-css-noncename" id="wp_cta_custom_css_noncename" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	$custom_css_name = apply_filters('wp-cta-custom-css-name','wp-cta-custom-css');
	echo '<textarea name="'.$custom_css_name.'" id="wp-cta-custom-css" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,$custom_css_name,true).'</textarea>';
}

function wp_call_to_actions_save_custom_css($post_id) {
	global $post;
	if (!isset($post)||!isset($_POST['wp-cta-custom-css']))
		return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;


	$custom_css_name = apply_filters('wp-cta-custom-css-name','wp-cta-custom-css');

	$wp_cta_custom_css = $_POST[$custom_css_name];
	update_post_meta($post_id, 'wp-cta-custom-css', $wp_cta_custom_css);
}

//Insert custom JS box to landing page
add_action('add_meta_boxes', 'add_custom_meta_box_wp_cta_custom_js');
add_action('save_post', 'wp_call_to_actions_save_custom_js');

function add_custom_meta_box_wp_cta_custom_js() {
   add_meta_box('wp_cta_3_custom_js', 'Custom JS', 'wp_cta_custom_js_input', 'wp-call-to-action', 'normal', 'low');
}

function wp_cta_custom_js_input() {
	global $post;
	echo "<em></em>";
	//echo wp_create_nonce('wp-cta-custom-js');exit;
	$custom_js_name = apply_filters('wp-cta-custom-js-name','wp-cta-custom-js');

	echo '<input type="hidden" name="wp_cta_custom_js_noncename" id="wp_cta_custom_js_noncename" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	echo '<textarea name="'.$custom_js_name.'" id="wp_cta_custom_js" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,$custom_js_name,true).'</textarea>';
}

function wp_call_to_actions_save_custom_js($post_id) {
	global $post;
	if (!isset($post)||!isset($_POST['wp-cta-custom-js']))
		return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

	$custom_js_name = apply_filters('wp-cta-custom-js-name','wp-cta-custom-js');

	$wp_cta_custom_js = $_POST[$custom_js_name];

	update_post_meta($post_id, 'wp-cta-custom-js', $wp_cta_custom_js);
}

//hook add_meta_box action into custom call fuction
//wp_cta_generate_meta is contained in functions.php
add_action('add_meta_boxes', 'wp_cta_generate_meta');
function wp_cta_generate_meta()
{
	global $post;
	if ($post->post_type!='wp-call-to-action')
		return;

	$extension_data = wp_cta_get_extension_data();
	$current_template = get_post_meta( $post->ID , 'wp-cta-selected-template' , true);
	$current_template = apply_filters('wp_cta_variation_selected_template',$current_template, $post);

	foreach ($extension_data as $key=>$data)
	{
		if ($key!='wp-cta'&&substr($key,0,4)!='ext-' && $key==$current_template)
		{
			$template_name = ucwords(str_replace('-',' ',$key));
			$id = strtolower(str_replace(' ','-',$key));
			//echo $key."<br>";
			add_meta_box(
				"wp_cta_{$id}_custom_meta_box", // $id
				__( "<small>$template_name Options:</small>", "wp_cta_{$key}_custom_meta" ),
				'wp_cta_show_metabox', // $callback
				'wp-call-to-action', // post-type
				'normal', // $context
				'default',// $priority
				array('key'=>$key)
				); //callback args
		}
	}
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

}

function wp_cta_show_metabox($post,$key)
{
	$extension_data = wp_cta_get_extension_data();
	$key = $key['args']['key'];

	$wp_cta_custom_fields = $extension_data[$key]['settings'];

	$wp_cta_custom_fields = apply_filters('wp_cta_show_metabox',$wp_cta_custom_fields, $key);

	inbound_template_metabox_render($key,$wp_cta_custom_fields,$post);
}


function wp_cta_render_metabox($key,$custom_fields,$post)
{
	//print_r($custom_fields);exit;
	// Use nonce for verification
	echo "<input type='hidden' name='wp_cta_{$key}_custom_fields_nonce' value='".wp_create_nonce('wp-cta-nonce')."' />";

	// Begin the field table and loop
	echo '<div class="form-table" id="inbound-meta">';

	//print_r($custom_fields);exit;
	$current_var = wp_cta_ab_testing_get_current_variation_id();
	foreach ($custom_fields as $field) {
		$field_id = $key . "-" .$field['id'];
		$field_name = $field['id'];
		$label_class = $field['id'] . "-label";
		$type_class = " inbound-" . $field['type'];
		$type_class_row = " inbound-" . $field['type'] . "-row";
		$type_class_option = " inbound-" . $field['type'] . "-option";
		$option_class = (isset($field['class'])) ? $field['class'] : '';
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field_id, true);
		$global_meta = get_post_meta($post->ID, $field_name, true);
		if(empty($global_meta)) {
			$global_meta = $field['default'];
		}

		//print_r($field);
		if ((!isset($meta)&&isset($field['default'])&&!is_numeric($meta))||isset($meta)&&empty($meta)&&isset($field['default'])&&!is_numeric($meta))
		{
			//echo $field['id'].":".$meta;
			//echo "<br>";
			$meta = $field['default'];
		}

        // Remove prefixes on global => true template options
        if (isset($field['global']) && $field['global'] === true) {
        $field_id = $field_name;
        $meta = get_post_meta($post->ID, $field_name, true);
        }

		// begin a table row with
		echo '<div class="'.$field['id'].$type_class_row.' div-'.$option_class.' wp-call-to-action-option-row inbound-meta-box-row">';
				if ($field['type'] != "description-block" && $field['type'] != "custom-css" ) {
				echo '<div id="inbound-'.$field_id.'" data-actual="'.$field_id.'" class="inbound-meta-box-label wp-call-to-action-table-header '.$label_class.$type_class.'"><label for="'.$field_id.'">'.$field['label'].'</label></div>';
				}

				echo '<div class="wp-call-to-action-option-td inbound-meta-box-option '.$type_class_option.'" data-field-type="'.$field['type'].'">';
				switch($field['type']) {
					// default content for the_content
					case 'default-content':
						echo '<span id="overwrite-content" class="button-secondary">Insert Default Content into main Content area</span><div style="display:none;"><textarea name="'.$field_id.'" id="'.$field_id.'" class="default-content" cols="106" rows="6" style="width: 75%; display:hidden;">'.$meta.'</textarea></div>';
						break;
					case 'description-block':
						echo '<div id="'.$field_id.'" class="description-block">'.$field['description'].'</div>';
						break;
					case 'custom-css':
						echo '<style type="text/css">'.$field['default'].'</style>';
						break;
					// text
					case 'colorpicker':
						if (!$meta)
						{
							$meta = $field['default'];
						}
						$var_id = (isset($_GET['new_meta_key'])) ? "-" . $_GET['new_meta_key'] : '';
						echo '<input type="text" class="jpicker" style="background-color:#'.$meta.'" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" size="5" /><span class="button-primary new-save-wp-cta" data-field-type="text" id="'.$field_id.$var_id.'" style="margin-left:10px; display:none;">Update</span>
								<div class="wp_cta_tooltip tool_color" title="'.$field['description'].'"></div>';
						break;
					case 'datepicker':
						echo '<div class="jquery-date-picker inbound-datepicker" id="date-picking" data-field-type="text">
						<span class="datepair" data-language="javascript">
									Date: <input type="text" id="date-picker-'.$key.'" class="date start" /></span>
									Time: <input id="time-picker-'.$key.'" type="text" class="time time-picker" />
									<input type="hidden" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" class="new-date" value="" >
									<p class="description">'.$field['description'].'</p>
							</div>';
						break;
					case 'text':
						echo '<input type="text" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" size="30" />
								<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
						break;
					case 'number':

						echo '<input type="number" class="'.$option_class.'" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" size="30" />
								<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';

						break;
					// textarea
					case 'textarea':
						echo '<textarea name="'.$field_id.'" id="'.$field_id.'" cols="106" rows="6" style="width: 75%;">'.$meta.'</textarea>
								<div class="wp_cta_tooltip tool_textarea" title="'.$field['description'].'"></div>';
						break;
					// wysiwyg
					case 'wysiwyg':
						echo "<div class='iframe-options iframe-options-".$field_id."' id='".$field['id']."'>";
						wp_editor( $meta, $field_id, $settings = array( 'editor_class' => $field_name ) );
						echo	'<p class="description">'.$field['description'].'</p></div>';
						break;
					// media
					case 'media':
						//echo 1; exit;
						echo '<label for="upload_image" data-field-type="text">';
						echo '<input name="'.$field_id.'"  id="'.$field_id.'" type="text" size="36" name="upload_image" value="'.$meta.'" />';
						echo '<input class="upload_image_button" id="uploader_'.$field_id.'" type="button" value="Upload Image" />';
						echo '<p class="description">'.$field['description'].'</p>';
						break;
					// checkbox
					case 'checkbox':
						$i = 1;
						echo "<table class='wp_cta_check_box_table'>";
						if (!isset($meta)){$meta=array();}
						elseif (!is_array($meta)){
							$meta = array($meta);
						}
						foreach ($field['options'] as $value=>$label) {
							if ($i==5||$i==1)
							{
								echo "<tr>";
								$i=1;
							}
								echo '<td data-field-type="checkbox"><input type="checkbox" name="'.$field_id.'[]" id="'.$field_id.'" value="'.$value.'" ',in_array($value,$meta) ? ' checked="checked"' : '','/>';
								echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label></td>';
							if ($i==4)
							{
								echo "</tr>";
							}
							$i++;
						}
						echo "</table>";
						echo '<div class="wp_cta_tooltip tool_checkbox" title="'.$field['description'].'"></div>';
					break;
					// radio
					case 'radio':
						foreach ($field['options'] as $value=>$label) {
							//echo $meta.":".$field_id;
							//echo "<br>";
							echo '<input type="radio" name="'.$field_id.'" id="'.$field_id.'" value="'.$value.'" ',$meta==$value ? ' checked="checked"' : '','/>';
							echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label> &nbsp;&nbsp;&nbsp;&nbsp;';
						}
						echo '<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
					break;
					// select
					case 'dropdown':
						echo '<select name="'.$field_id.'" id="'.$field_id.'" class="'.$field['id'].'">';
						foreach ($field['options'] as $value=>$label) {
							echo '<option', $meta == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';
						}
						echo '</select><div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
					break;



				} //end switch
		echo '</div></div>';
	} // end foreach
	echo '</div>'; // end table
	//exit;
}

add_action('save_post', 'wp_cta_save_meta');
function wp_cta_save_meta($post_id) {
	global $post;

	$extension_data = wp_cta_get_extension_data();

	if (!isset($post))
		return;

	if ($post->post_type=='revision')
	{
		return;
	}

	if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||(isset($_POST['post_type'])&&$_POST['post_type']=='revision'))
	{
		return;
	}

	if ($post->post_type=='wp-call-to-action')
	{
		//print_r($extension_data);exit;
		//print_r($_POST);exit;
		//echo $_POST['wp-cta-selected-template'];exit;
		foreach ($extension_data as $key=>$data)
		{
			if ($key=='wp-cta')
			{
				// verify nonce
				if (!isset($_POST["wp_cta_{$key}_custom_fields_nonce"])||!wp_verify_nonce($_POST["wp_cta_{$key}_custom_fields_nonce"], 'wp-cta-nonce'))
				{
					return $post_id;
				}

				$wp_cta_custom_fields = $extension_data[$key]['settings'];

				foreach ($wp_cta_custom_fields as $field)
				{
					$id = $key."-".$field['id'];
					$old = get_post_meta($post_id, $id, true);
					$new = $_POST[$id];

					if (isset($new) && $new != $old ) {
						update_post_meta($post_id, $id, $new);
					} elseif ('' == $new && $old) {
						delete_post_meta($post_id, $id, $old);
					}
				}
			}
			else if ((isset($_POST['wp-cta-selected-template'])&&$_POST['wp-cta-selected-template']==$key)||substr($key,0,4)=='ext-' || (isset($extension_data[$key]['info']['data_type']) && $extension_data[$key]['info']['data_type']=='metabox'))
			{
				$wp_cta_custom_fields = $extension_data[$key]['settings'];

				// verify nonce
				if (!wp_verify_nonce($_POST["wp_cta_{$key}_custom_fields_nonce"], 'wp-cta-nonce'))
				{
					return $post_id;
				}

				// loop through fields and save the data
				foreach ($wp_cta_custom_fields as $field) {
					$id = $key."-".$field['id'];


					if($field['type'] == 'tax_select')
						continue;

					if (!isset($_POST[$id]))
					{
						continue;
					}
					$old = get_post_meta($post_id, $id, true);
					$new = $_POST[$id];
					//echo "id:$id";
					//echo "<br>old:$old:<br>new:".$new."<br>";
					//echo "<br>";

					if (isset($new) && $new != $old ) {
						update_post_meta($post_id, $id, $new);
					} elseif ('' == $new && $old) {
						delete_post_meta($post_id, $id, $old);
					}
				} // end foreach
				//exit;
			}
		}

		//make sure template is saved
		//$selected_template = $_POST['wp-cta-selected-template'];
		//update_post_meta($post_id, $field['id'], $new);

		// save taxonomies
		$post = get_post($post_id);
		//$category = $_POST['wp_call_to_action_category'];
		//wp_set_object_terms( $post_id, $category, 'wp_call_to_action_category' );
	}
}

