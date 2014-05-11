<?php
/**
 * Lead CPT functionality used across plugins
 */

add_action( 'init', 'inbound_leads_register' , 10 );
if (!function_exists('inbound_leads_register')) {
function inbound_leads_register() {

	$lead_active = get_option( 'Leads_Activated' ); // Check if leads is activated
	//delete_option( 'Leads_Activated');
	//add_option( 'Leads_Activated', true );

	$labels = array(
		'name' => _x('Leads', 'post type general name'),
		'singular_name' => _x('Lead', 'post type singular name'),
		'add_new' => _x('Add New', 'Lead'),
		'add_new_item' => __('Add New Lead'),
		'edit_item' => __('Edit Lead'),
		'new_item' => __('New Leads'),
		'view_item' => __('View Leads'),
		'search_items' => __('Search Leads'),
		'not_found' =>	__('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => INBOUND_SHARED_ASSETS . '/global/images/leads.png',
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('custom-fields','thumbnail')
	);

	$args['show_in_menu'] = ($lead_active) ? true : false;

	register_post_type( 'wp-lead' , $args );

	// Lead Lists
	$list_labels = array(
		'name'						=> _x( 'Lead Lists', 'taxonomy general name' ),
		'singular_name'				=> _x( 'Lead List', 'taxonomy singular name' ),
		'search_items'				=> __( 'Search Lead Lists' ),
		'popular_items'				=> __( 'Popular Lead Lists' ),
		'all_items'					=> __( 'All Lead Lists' ),
		'parent_item'				=> null,
		'parent_item_colon'			=> null,
		'edit_item'					=> __( 'Edit Lead List' ),
		'update_item'				=> __( 'Update Lead List' ),
		'add_new_item'				=> __( 'Add New Lead List' ),
		'new_item_name'				=> __( 'New Lead List' ),
		'separate_items_with_commas' => __( 'Separate Lead Lists with commas' ),
		'add_or_remove_items'		=> __( 'Add or remove Lead Lists' ),
		'choose_from_most_used'		=> __( 'Choose from the most used lead List' ),
		'not_found'					=> __( 'No Lead Lists found.' ),
		'menu_name'					=> __( 'Lead Lists' ),
	);

	$list_args = array(
		'hierarchical'			=> true,
		'labels'				=> $list_labels,
		'singular_label'		=> "List Management",
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'show_in_nav_menus'		=> false,
		'show_admin_column'	 => true,
		'query_var'			 => true,
		'rewrite'				=> false,
	);

	register_taxonomy('wplead_list_category','wp-lead', $list_args );

	// Lead Tags
	$labels = array(
		'name'						=> _x( 'Lead Tags', 'taxonomy general name' ),
		'singular_name'				=> _x( 'Lead Tag', 'taxonomy singular name' ),
		'search_items'				=> __( 'Search Lead Tags' ),
		'popular_items'				=> __( 'Popular Lead Tags' ),
		'all_items'					=> __( 'All Lead Tags' ),
		'parent_item'				=> null,
		'parent_item_colon'			=> null,
		'edit_item'					=> __( 'Edit Lead Tag' ),
		'update_item'				=> __( 'Update Lead Tag' ),
		'add_new_item'				=> __( 'Add New Lead Tag' ),
		'new_item_name'				=> __( 'New Lead Tag' ),
		'separate_items_with_commas'=> __( 'Separate Lead Tags with commas' ),
		'add_or_remove_items'		=> __( 'Add or remove Lead Tags' ),
		'choose_from_most_used'		=> __( 'Choose from the most used lead tags' ),
		'not_found'					=> __( 'No lead tags found.' ),
		'menu_name'					=> __( 'Lead Tags' ),
	);

	$args = array(
		'hierarchical'			=> false,
		'labels'				=> $labels,
		'show_ui'				=> true,
		'show_admin_column'	 	=> true,
		'show_in_nav_menus'		=> false,
		'update_count_callback' => '_update_post_term_count',
		'query_var'				=> true,
		'rewrite'				=> array( 'slug' => 'lead-tag' ),
	);

	register_taxonomy( 'lead-tags', 'wp-lead', $args );

	add_action('admin_menu', 'remove_lead_tag_menu');
	function remove_lead_tag_menu() {
		global $submenu;
		unset($submenu['edit.php?post_type=wp-lead'][16]);
		//print_r($submenu); exit;
	}
}
}

/* Top Metabox */
add_action( 'edit_form_after_title', 'inbound_leads_install_notice' );
if (!function_exists('inbound_leads_install_notice')) {
	function inbound_leads_install_notice() {
		global $post;

		$first_name = get_post_meta( $post->ID , 'wpleads_first_name', true );
		$last_name = get_post_meta( $post->ID , 'wpleads_last_name', true );

		if ( empty ( $post ) || 'wp-lead' !== get_post_type( $GLOBALS['post'] ) )
			return;

		// Lead Screen if leads not installed
		if (!is_plugin_active('leads/wordpress-leads.php')) {
			echo "WordPress leads is not currently installed/activated to view and manage leads please turn it on.";
		}

	}
}

// Set Leads to list from form tool. Need to consolidate into add_lead_to_list_tax
if (!function_exists('add_lead_lists_ajax')) {
function add_lead_lists_ajax($lead_id, $list_id, $tax = 'wplead_list_category') {

	$current_lists = wp_get_post_terms( $lead_id, $tax, 'id' );
	$all_term_ids = array();
	$all_term_slugs = array();
	foreach ($current_lists as $term ) {
		$add = $term->term_id;
		$slug = $term->slug;
		$all_term_ids[] = $add;
		$all_term_slugs[] = $slug;
	}
	// Set terms for lead tags taxomony
	$list_array = $list_id;
	if(is_array($list_array)) {
		foreach ($list_array as $key => $value) {
			$num = intval($value);
			if ( !in_array($num, $all_term_ids) ) {
				$all_term_ids[] = $num;
				wp_set_object_terms( $lead_id, $all_term_ids, $tax);
			}
		}
	}
}
}
/* merge add functions as switch case
function add_lead_to_list_tax($lead_id, $list_id, $tax = 'wplead_list_category') {

	$current_lists = wp_get_post_terms( $lead_id, $tax, 'id' );

	$all_term_ids = array();
	$all_term_slugs = array();
	foreach ($current_lists as $term ) {
		$add = $term->term_id;
		$slug = $term->slug;
		$all_term_ids[] = $add;
		$all_term_slugs[] = $slug;
	}

	$tag_check = strpos($list_id, ",");
	if ($tag_check !== false) {
		// Set terms for lead tags taxomony
		$list_array = explode(",", $list_id);
		if(is_array($list_array)) {
			foreach ($list_array as $key => $value) {
				$trim = trim(strtolower($value));
				$add_slug = preg_replace('/\s+/', '-', $trim);
				if ( !in_array($add_slug, $all_term_slugs) ) {
					$all_term_slugs[] = $add_slug;
					wp_set_object_terms( $lead_id, $all_term_slugs, $tax);
				}
			}
		}
	} else {
		// Set terms for list taxomony
		if ( !in_array($list_id, $all_term_ids) ) {
			$all_term_ids[] = $list_id;
			wp_set_object_terms( $lead_id, $all_term_ids, $tax);
		}
	}
}

function remove_lead_from_list_tax($lead_id, $list_id,	$tax = 'wplead_list_category') {
	$current_terms = wp_get_post_terms( $lead_id, $tax, 'id' );

	$all_remove_terms = '';
	foreach ($current_terms as $term ) {
		$add = $term->term_id;
		$all_remove_terms .= $add . ' ,';
	}
	$final = explode(' ,', $all_remove_terms);
	$final = array_filter($final, 'strlen');

	if (in_array($list_id, $final) ) {
		$new = array_flip ( $final );
		unset($new[$list_id]);
		$save = array_flip ( $new );
		wp_set_object_terms( $lead_id, $save, $tax);
	}
}
*/
