<?php


//Add rule creation/management metabox
add_action('add_meta_boxes', 'wpleads_add_metabox_leads_list');
function wpleads_add_metabox_leads_list() {
	global $post;
	
	if ( !isset($post) || $post->post_status!='publish') {
		return;
	}
	
	$id = $post->ID;
	$title = get_the_title($id);	
	add_meta_box(
		'wpleads_metabox_leads_list', // $id
		sprintf( __( 'Leads in %s list', 'lead' ) , $title ),
		'wpleads_display_metabox_leads_list', // $callback
		'list', // $cpt
		'normal', // $context
		'high'); // $priority 
}

// Render select template box
function wpleads_display_metabox_leads_list() {
	global $post; 
	 
		
	?>
	<div id="lls-leads-table-container">
		<div id="lls-leads-table-container-inside">
		<!--<div class='wpleads_toolbar_container' style='float:right;padding:20px;'>[hide leads not related to this list]</div>-->
		<?php
			$myListTable = new LLS_WPL_LISTING();		
			$myListTable->prepare_items();
			$myListTable->display();
		?>
		</div>
	</div>
	
	<?php
	
}

