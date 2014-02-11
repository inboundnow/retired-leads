<?php

/**********************************************************/
/******************CREATE SETTINGS SUBMENU*****************/
add_action('admin_menu', 'wpleads_add_menu');
function wpleads_add_menu()
{
	if (current_user_can('manage_options'))
	{
		add_submenu_page('edit.php?post_type=wp-lead', 'Lead Management', 'Lead Management', 'manage_options', 'lead_management','lead_management_admin_screen');

		add_submenu_page('edit.php?post_type=wp-lead', 'Lead Rules', 'Lead Rules', 'manage_options', 'lead-rules-redirect',100);
		
		add_submenu_page('edit.php?post_type=wp-lead', 'Forms', 'Create Forms', 'manage_options', 'inbound-forms-redirect',100);

		add_submenu_page('edit.php?post_type=wp-lead', 'Settings', 'Global Settings', 'manage_options', 'wpleads_global_settings','wpleads_display_global_settings');
	}
	
	/* remove option to add new lead */
    global $submenu;
    unset($submenu['post-new.php?post_type=wp-lead'][15]);
	remove_submenu_page('edit.php?post_type=wp-lead', 'post-new.php?post_type=wp-lead');
    
}

add_action('admin_init', 'wpleads_lead_lists_nav_redirect');
function wpleads_lead_lists_nav_redirect($value){
	global $pagenow;
	$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : false);
	if($pagenow=='edit.php' && $page=='lead-rules-redirect'){
		wp_redirect(get_admin_url().'edit.php?post_type=rule');
		exit;
	}
}
