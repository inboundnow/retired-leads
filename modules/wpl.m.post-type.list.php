<?php
/* CPT Lead Lists */
add_action('init', 'wpleads_register_list',11);
function wpleads_register_list() {
	//echo $slug;exit;
    $labels = array(
        'name' => _x('Lead Lists', 'post type general name'),
        'singular_name' => _x('List', 'post type singular name'),
        'add_new' => _x('Add New', 'List'),
        'add_new_item' => __('Create New List'),
        'edit_item' => __('Edit List'),
        'new_item' => __('New Lists'),
        'view_item' => __('View Lists'),
        'search_items' => __('Search Lists'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'query_var' => true,
       	'show_in_menu'  => 'edit.php?post_type=wp-lead',
        'menu_icon' => WPL_URL . '/images/lists.png',
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','custom-fields')
      );

    register_post_type( 'list' , $args );
	//flush_rewrite_rules( false );

	//add categories to wp-lead posttype
	//flush_rewrite_rules( false );
	register_taxonomy('wplead_list_category','wp-lead', array(
            'hierarchical' => true,
            'label' => "Lists",
            'singular_label' => "List Management",
            'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
            'query_var' => true,
			"rewrite" => false

    ));

	add_action('admin_menu', 'remove_list_cat_menu');
	function remove_list_cat_menu() {
		global $submenu;
		unset($submenu['edit.php?post_type=wp-lead'][15]);
		//print_r($submenu); exit;
	}
}

/*----------------------------------------------------------------------------------------------------------------------------*/
/*********************** PREPARE COLLUMNS FOR LISTS****************************************************************************/
/*----------------------------------------------------------------------------------------------------------------------------*/


if (is_admin())
{
	// Change the columns for the edit CPT screen
	add_filter( "manage_list_posts_columns", "wpleads_list_change_columns" );
	function wpleads_list_change_columns( $cols ) {
		$cols = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "List",
			"wpleads-leads" => "Leads"
		);

		$cols = apply_filters('wpleads_list_change_columns',$cols);

		return $cols;
	}


	add_action( "manage_posts_custom_column", "wpleads_list_custom_columns", 10, 2 );
	function wpleads_list_custom_columns( $column, $post_id )
	{
		switch ( $column ) {
			case "title":
				$list_name = get_the_title( $post_id );

				$list_name = apply_filters('wpleads_list_name',$list_name);

				echo $list_name;
			  break;

			case "wpleads-leads":
			  $lead_items = wpleads_count_associated_lead_items($post_id);
			  echo $lead_items;
			  break;
		}

		do_action('wpleads_list_custom_columns',$column, $post_id);

	}


	// Make these columns sortable
	add_filter( "manage_edit-list_sortable_columns", "wpleads_list_sortable_columns" );
	function wpleads_list_sortable_columns($columns) {

		$columns = apply_filters('',$columns);

		return $columns;
	}

}

//************************************************************************************//
//*************** POST SAVING & POST DELETING ****************************************//
//************************************************************************************//
if (is_admin())
{
	//add action for cpt saving
	add_action('save_post', 'wpleads_list_save_post');
	function wpleads_list_save_post($post_id) {
		global $post;

		if (!isset($post))
			$post = get_post($post_id);

		if ($post->post_type=='revision' ||  'trash' == get_post_status( $post_id ))
		{
			return;
		}
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) ||( isset($_POST['post_type']) && $_POST['post_type']=='revision'))
		{
			return;
		}

		if ($post->post_type=='list' && isset($_POST) && count($_POST) > 0 )
		{

			$list_title = (isset($_POST['post_title'])) ? $_POST['post_title'] : '';
			$list_slug = (isset($_POST['post_name'])) ? $_POST['post_name'] : '';

			//add list as category to lead cpt and store the category taxonomy as meta pair in list cpt
			wpleads_add_list_to_wplead_list_category_taxonomy($post_id, $list_title, $list_slug);

			//if role creation is turned on
			$role_creation = get_option('wpl-main-role-creation', 1);

			if ($role_creation)
			{
				//now create role
				$result = add_role($list_slug, $list_title, array(
					'read' => true, // True allows that capability
					'edit_posts' => false,
					'delete_posts' => false, // Use false to explicitly deny
				));

				if (null !== $result) {
					//echo 'Yay!  New role created!';
				} else {
					//echo 'Oh... the basic_contributor role already exists.';
				}
			}

			do_action('wpleads_save_list_post',$post_id);
		}

		//add in meta pair markers for lists the lead belong to
		if ($post->post_type=='wp-lead' && isset($_POST) && count($_POST) > 0 )
		{

			if (isset($_POST['tax_input']))
			{
				//delete_post_meta($post->ID, 'wpleads_list_ids');

				$tax_input = $_POST['tax_input'];
				foreach ($tax_input['wplead_list_category'] as $key=>$value)
				{
					if ($value)
					{
						$store_post = $post;
						$list = wpleads_get_list_by_taxonomy_id($value);

						$post = $store_post;
						$list_name = $list['post_name'];
						$list_id = $list['ID'];
						$wpleads_list_ids[$list_name]['list_id'] = $list_id;
						$wpleads_list_ids[$list_name]['wplead_list_category_id'] = $value;
					}
				}
			}

			if (isset($wpleads_list_ids) && count($wpleads_list_ids) > 0)
			{
				$wpleads_list_ids = json_encode($wpleads_list_ids);
				$wpleads_list_ids = update_post_meta($post->ID, 'wpleads_list_ids', $wpleads_list_ids);
			}
			else
			{
				update_post_meta($post->ID, 'wpleads_list_ids', "");
			}
		}
	}

	//add action for cpt deleting
	add_action('before_delete_post', 'wpleads_permanently_delete_list');
	function wpleads_permanently_delete_list($post_id){
		global $post;
		//if (!isset($post))
		//	return;
		$list_slug = $post->post_name;

		//if role creation is turned on
		$role_creation = get_option('wpl-main-role-creation', 1);

		if ($role_creation)
		{
			$result = remove_role($list_slug);
		}

		$wplead_cat_id = get_post_meta( $post_id, 'wplead_list_category_id', true);
		wp_delete_term($wplead_cat_id,'wplead_list_category');
	}
}


function wpleads_add_list_to_wplead_list_category_taxonomy($post_id, $list_title, $list_slug = null)
{

	$wplead_cat_id = get_post_meta( $post_id, 'wplead_list_category_id', true);
	if ($wplead_cat_id)
	{
		wp_update_term( $wplead_cat_id, 'wplead_list_category', array('name'=>$list_title) );
	}
	else
	{
		//add category taxonomy in wpleads
		$term = wp_insert_term( $list_title, 'wplead_list_category', $args = array('slug'=>$list_slug) );

		if (is_object($term))
		{
			$term_error_array = $term->error_data;
			$term_id = $term_error_array['term_exists'];
		}
		else
		{
			$term_id = $term['term_id'];
		}

		update_post_meta( $post_id, 'wplead_list_category_id', $term_id);
	}
}

function wpleads_get_list_by_taxonomy_id($term_id)
{

	$args = array(
		'post_type' => 'list',
		'post_status' => 'published',
		'meta_key'=> 'wplead_list_category_id',
		'meta_value'=> $term_id,
		'posts_per_page' => 1
	);

	$wp_query = new WP_Query( $args );
	while ($wp_query->have_posts()) : $wp_query->the_post();
		return array('ID'=>$wp_query->post->ID,'post_name'=>$wp_query->post->post_name , 'post_title'=>$wp_query->post->post_title);
	endwhile;

}

function wpleads_add_lead_to_list($list_id, $lead_id, $add = true)
{

	$wplead_cat_id = get_post_meta($list_id,'wplead_list_category_id', true);

	wp_set_post_terms( $lead_id, intval($wplead_cat_id), 'wplead_list_category', true);
	
	//build meta pair for list ids lead belongs to
	$wpleads_list_ids = get_post_meta($lead_id, 'wpleads_list_ids', true);

	if ($wpleads_list_ids)
	{
		$wpleads_list_ids_new = array();
		
		//get array
		$wpleads_list_ids = json_decode($wpleads_list_ids, true);
		if ( !is_array($wpleads_list_ids) )
			$wpleads_list_ids = array();
			
		//clean
		delete_post_meta($lead_id, 'wpleads_list_ids');

		//restore
		foreach ($wpleads_list_ids as $key=>$value)
		{
			if ($value)
			{
				//echo $value;
				$list = get_post($value['list_id']);
				$list_name = $list->post_name;
				$wplead_cat_id = get_post_meta($value,'wplead_list_category_id', true);

				$wpleads_list_ids_new[$list_name]['list_id'] = $value['list_id'];
				$wpleads_list_ids_new[$list_name]['wplead_list_category_id'] = $wplead_cat_id;
			}
		}				
			
		//push newest if not exists
		if (!in_array($list_id, $wpleads_list_ids_new))
		{
			$list = get_post($list_id);
			$list_name = $list->post_name;
			
			$wplead_cat_id = get_post_meta($list_id,'wplead_list_category_id', true);

			$wpleads_list_ids_new[$list_name]['list_id'] = $list_id;
			$wpleads_list_ids_new[$list_name]['wplead_list_category_id'] = $wplead_cat_id;
		}


		//print_r($wpleads_list_ids_new);exit;

		$wpleads_list_ids_new = json_encode($wpleads_list_ids_new);
		$wpleads_list_ids_new = update_post_meta($lead_id, 'wpleads_list_ids', $wpleads_list_ids_new);
	}
	else
	{
		$list = get_post($list_id);
		$list_name = $list->post_name;
		$wpleads_list_ids[$list_name]['list_id'] = $list_id;
		$wpleads_list_ids[$list_name]['wplead_list_category_id'] = $wplead_cat_id;

		$wpleads_list_ids = json_encode($wpleads_list_ids);
		$wpleads_list_ids = update_post_meta($lead_id , 'wpleads_list_ids', $wpleads_list_ids);
	}

}

function wpleads_remove_lead_from_list( $list_id, $lead_id )
{
	$categories = wp_get_post_terms( $lead_id, 'wplead_list_category', array( 'fields'=>'ids' ) );
	
	$list_category_id = get_post_meta($list_id,'wplead_list_category_id', true);

	$pos = array_search( $list_category_id, $categories );		
	
	if( false !== $pos ) {			
		unset( $categories[$pos] );					
	}
	
	wp_set_post_terms ($lead_id, $categories, 'wplead_list_category');
	
	//build meta pair for list ids lead belongs to
	$wpleads_list_ids = get_post_meta($lead_id, 'wpleads_list_ids' , true);
	
	if ($wpleads_list_ids)
	{
		//get array
		$wpleads_list_ids = json_decode($wpleads_list_ids, true);
		
		if ( !is_array(wpleads_list_ids) )
			$wpleads_list_ids = array();
			
		//clean
		delete_post_meta($lead_id, 'wpleads_list_ids');
		
		//rebuild
		foreach ($wpleads_list_ids as $key=>$value)
		{
			if ($value['ID']==$list_id)
				unset($wpleads_list_ids[$key]);
		}
		
		//store
		$wpleads_list_ids = json_encode($wpleads_list_ids);
		$wpleads_list_ids = update_post_meta($lead_id, 'wpleads_list_ids', $wpleads_list_ids);
	}	
}




?>