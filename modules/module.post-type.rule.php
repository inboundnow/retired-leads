<?php


/* Build Rule CPT */

add_action('init', 'rules_cpt_rule_register', 11);
function rules_cpt_rule_register() {
	//echo $slug;exit;
    $labels = array(
        'name' => _x('Lead Rules', 'post type general name'),
        'singular_name' => _x('Rule', 'post type singular name'),
        'add_new' => _x('Add New Rule', 'Rule'),
        'add_new_item' => __('Create New Rule'),
        'edit_item' => __('Edit Rule'),
        'new_item' => __('New Rules'),
        'view_item' => __('View Rules'),
        'search_items' => __('Search Rules'),
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
        'menu_icon' => WPL_URL . '/images/automation.png',		
       	'show_in_menu'  => 'edit.php?post_type=wp-lead',
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title' , 'custom-fields' )
      );

    register_post_type( 'rule' , $args );


}

/*----------------------------------------------------------------------------------------------------------------------------*/
/*********************** PREPARE COLLUMNS FOR LISTS****************************************************************************/
/*----------------------------------------------------------------------------------------------------------------------------*/


if (is_admin())
{
	// Change the columns for the edit CPT screen
	add_filter( "manage_rule_posts_columns", "rule_change_columns" );
	function rule_change_columns( $cols ) {
		$cols = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Rule",
			"ma-rule-status" => "Rule Status"
		);

		$cols = apply_filters('rule_change_columns',$cols);

		return $cols;
	}


	add_action( "manage_posts_custom_column", "rule_custom_columns", 10, 2 );
	function rule_custom_columns( $column, $post_id )
	{
		switch ( $column ) {
			case "title":
				$rule_name = get_the_title( $post_id );

				$rule_name = apply_filters('rule_rule_name',$rule_name);

				echo $rule_name;
			  break;

			case "ma-rule-status":
				$status = get_post_meta($post_id,'rule_active',true);
				echo $status;
			  break;

		}

		do_action('rule_custom_columns',$column, $post_id);

	}


	// Make these columns sortable
	//add_filter( "manage_edit-rule_sortable_columns", "rule_sortable_columns" );
	function rule_sortable_columns($columns) {

		$columns = apply_filters('',$columns);

		return $columns;
	}



}

