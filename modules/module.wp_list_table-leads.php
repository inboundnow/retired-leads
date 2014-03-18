<?php


if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LLS_WPL_LISTING extends WP_List_Table
{
	private $leads_data;
	private $singular;
	private $plural;


	function __construct() {
		global $post;

		$final_data = array();

		$wplead_cat_id = get_post_meta($post->ID,'wplead_list_category_id', true);

		$args = array(
			'post_type' => 'wp-lead',
			'post_status' => 'published',
			'tax_query'=> array(
					array(
					'taxonomy'=>'wplead_list_category',
					'field'=>'term_id',
					'terms'=> $wplead_cat_id
					)
				),
			'posts_per_page' => -1
		);

		$wp_query = new WP_Query( $args );
		$i= 0;
		while ($wp_query->have_posts()) : $wp_query->the_post();


			$lead_id = $wp_query->post->ID;
			$final_data[$lead_id]['ID'] = $lead_id;

			$first_name = get_post_meta($lead_id, 'wpleads_first_name', true);
			$last_name = get_post_meta($lead_id, 'wpleads_last_name', true);
			$full_name = $first_name." ".$last_name;
			$final_data[$lead_id]['lls_lead_name'] = $full_name;

			$email = get_post_meta($lead_id, 'wpleads_email_address', true);
			$final_data[$lead_id]['lls_lead_email'] = $email;

			$points = get_post_meta($lead_id, 'lls_points', true);
			$final_data[$lead_id]['lls_lead_points'] = $points;
			$i++;
		endwhile;


		//print_r($final_data);exit;
		$this->leads_data = $final_data;
		//$this->_args = array();

		$this->singular = 'ID';
		$this->plural = 'ID';

		$args = $this->_args;
		//print_r($args);exit;
		$args['plural'] = sanitize_key( '' );
		$args['singular'] = sanitize_key( '' );

		$this->_args = $args;
		//
	}

	function get_columns() {
		$columns = array(
			//'cb'        => '<input type="checkbox" />',
			'lls_lead_name' => __( 'Lead Name' , 'leads' ),
			'lls_lead_email' => __( 'Lead Email' , 'leads' ),
			'lls_lead_points' => __( 'Lead Points' , 'leads' ),
			'lls_lead_actions' => __( 'Actions' , 'leads' )
		);
		return $columns;
	}

	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="rule[]" value="%s" />', $item['ID']
		);
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'lls_lead_name'  => array('lls_lead_name',false),
			'lls_lead_email' => array('lls_lead_email',false),
			'lls_lead_actions' => array('lls_lead_actions',false),
			'lls_lead_points' => array('lls_lead_points',false)
		);

		return $sortable_columns;
	}

	function usort_reorder( $a, $b ) {
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'lls_lead_name';
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		//print_r($b);exit;
		//echo $order;exit;
		return ( $order === 'asc' ) ? $result : -$result;
	}

	function prepare_items() {

		$columns  = $this->get_columns();

		$hidden = array('ID');
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		if(isset($this->leads_data)&&is_array($this->leads_data)) {
			usort( $this->leads_data, array( &$this, 'usort_reorder' ) );
		}

		$per_page = 500;
		$current_page = $this->get_pagenum();

		$total_items = count( $this->leads_data );

		if (isset($this->leads_data)&&is_array($this->leads_data)) {
			$this->found_data = array_slice( $this->leads_data,( ( $current_page-1 )* $per_page ), $per_page );
		}

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		) );

		(isset($this->found_data)) ? $this->items = $this->found_data : $this->items = null;
	}

	function column_default( $item, $column_name ) {
		//echo $item;exit;
		switch( $column_name ) {
			case 'lls_lead_name':
				echo '<span class="lls_col_lead_name">'.$item[ $column_name ].'</span>';
				return;
			case 'lls_lead_email':
				return '<span class="lls_col_lead_name">'.$item[ $column_name ].'</span>';
			case 'lls_lead_points':
				if ($item[ $column_name ])
					return '<span class="lls_col_lead_name">'.$item[ $column_name ].'</span>';
				else
					return '<span class="lls_col_lead_name">0</span>';
			case 'lls_lead_actions':
				echo '<div class="row-actions" style="visibility:visible;margin:0px;">';
					echo '<span class="edit">';
						echo '<a title="View this item" href="post.php?post='.$item[ 'ID' ].'&amp;action=edit">View/Manage lead</a>';
					echo '</span>';
				do_action('lls_wp_list_table_row_lead_actions', $item[ 'ID' ]);
				echo '</div>';
				return;
			default:
				return '<span class="lls_col_lead_name">'.$item[ $column_name ].'</span>'; //Show the whole array for troubleshooting purposes
		}
	}

	function admin_header() {

	}

	function no_items() {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		_e( 'no leads in list' );
		echo "<br>";
	}

}

add_action('lls_lead_actions', 'lls_add_lead_actions');
function lls_add_lead_actions($id) {
	global $post;
	?>
	<div class='lead_row_actions' id='row-actions-<?php echo $id; ?>'>
		<span id='row-action-delete-<?php echo $id; ?>' class='row-action-item'><a href='post.php?post=<?php echo $post->ID; ?>&action=edit&&delete_rule=<?php echo $id; ?>'>[delete]</a></span>
		<span id='row-action-edit-<?php echo $id; ?>' class='row-action-item'><a href='post.php?post=1768&action=edit&edit_rule=<?php echo $id; ?>'>[edit rule]</a></span>

	</div>

	<?php
}

?>