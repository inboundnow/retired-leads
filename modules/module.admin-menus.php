<?php

/**
 * LOAD SUB MENU SECTIONS
 */
 
add_action('admin_menu', 'wp_cta_add_menu');
function wp_cta_add_menu()
{
	if (current_user_can('manage_options'))
	{

		add_submenu_page('edit.php?post_type=wp-call-to-action', 'Forms', 'Create Forms', 'manage_options', 'inbound-forms-redirect',100);

		// coming soon
		add_submenu_page('edit.php?post_type=wp-call-to-action', 'Templates', 'Manage Templates', 'manage_options', 'wp_cta_manage_templates','wp_cta_manage_templates',100);

		// comming soon add_submenu_page('edit.php?post_type=wp-call-to-action', 'Get Addons', 'Add-on Extensions', 'manage_options', 'wp_cta_store','wp_cta_store_display',100);

		 add_submenu_page('edit.php?post_type=wp-call-to-action', 'Settings', 'Global Settings', 'manage_options', 'wp_cta_global_settings','wp_cta_display_global_settings');

		// Add settings page for frontend editor
		add_submenu_page('edit.php?post_type=wp-call-to-action', __('Editor','Editor'), __('Editor','Editor'), 'manage_options', 'wp-cta-frontend-editor', 'wp_cta_frontend_editor_screen');

	}
}