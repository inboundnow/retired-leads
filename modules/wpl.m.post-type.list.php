<?php


/*----------------------------------------------------------------------------------------------------------------------------*/
/*********************** PREPARE CPT FOR LISTS*********************************************************************************/
/*----------------------------------------------------------------------------------------------------------------------------*/

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
			
		if ($post->post_type=='list')
		{		

			$list_title = $_POST['post_title'];	
			$list_slug = $_POST['post_name'];
			
			$wplead_cat_id = get_post_meta( $post_id, 'wplead_list_category_id', true);
			if ($wplead_cat_id)
			{
				wp_update_term( $wplead_cat_id, 'wplead_list_category', array('name'=>$list_title,'slug'=>$list_slug) );
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
			
			do_action('wpleads_save_list_post',$post_id);
		}
		else
		{
			
			if (isset($_POST['wpleads_list_sorting']))
			{
				$list_categories = $_POST['wpleads_list_sorting'];
				
				//delete all custom post meta related to lists
				global $wpdb;
				
				$data   =   array();
			
				$wpdb->query("
				
					SELECT `meta_key`, `meta_value`
					FROM $wpdb->postmeta
					WHERE `post_id` = ".$post->ID."
					
				");
				
				foreach($wpdb->last_result as $k => $v){
					$data[$v->meta_key] =   $v->meta_value;
				};
				
				foreach ($data as $key=>$value)
				{
					if (strstr($key,'wpleads_list_sorting'))
					{
						delete_post_meta($post->ID,$key);
					}
				}

				//rebuild the lists post meta
				foreach ($list_categories as $key=>$list_id)
				{
					update_post_meta( $post->ID , 'wpleads_list_sorting-'.$list_id , 1);					
				}
				
				//hello!
			}
		}
	}

	//add action for cpt deleting
	add_action('before_delete_post', 'wpleads_permanently_delete_post');
	function wpleads_permanently_delete_post($post_id){
		global $post;
		$list_slug = $post->post_name;

		$result = remove_role($list_slug);
		
		$wplead_cat_id = get_post_meta( $post_id, 'wplead_list_category_id', true);
		wp_delete_term($wplead_cat_id,'wplead_list_category');
	}
	
}



?>