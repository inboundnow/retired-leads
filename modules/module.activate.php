<?php

/**
 * REGISTER ACTIVATION HOOK
 */

register_activation_hook( WP_CTA_FILE , 'wp_call_to_action_activate');
function wp_call_to_action_activate($wp = '3.6', $php = '5.3', $lp = '1.3.6', $leads = '1.2.1')
{
	global $wp_version;
	if ( version_compare( phpversion(), $php, '<' ) )
	{
	    $flag = 'PHP';
	    $php_version = phpversion();
	    $version = 'PHP' == $flag ? $php : $wp;
		wp_die(__( '<p>The <strong>WordPress Calls to Action</strong> plugin requires'.$flag.'  version '.$php.' or greater.</p>Your server is running version '. $php_version . '. Please Contact your hosting provider to update your PHP version. PHP 5.3 came out in December of 2010.' , 'cta' ) , 'Plugin Activation Error' ,  array( 'response'=>200, 'back_link'=>TRUE ) );
		deactivate_plugins( basename( WP_CTA_FILE ) );
	}
	elseif ( version_compare( $wp_version, $wp, '<' ) )
	{
	    $flag = 'WordPress';
	    wp_die( sprintf( __( '<p>The <strong>WordPress Calls to Action</strong> plugin requires %s  version %s or greater.</p>' , 'cta' ) , $flag , $php) , 'Plugin Activation Error' ,  array( 'response'=>200, 'back_link'=>TRUE ) );
		deactivate_plugins( basename( WP_CTA_FILE ) );
	}
	elseif (defined('LANDINGPAGES_CURRENT_VERSION') && version_compare( LANDINGPAGES_CURRENT_VERSION, $lp, '<' ))
	{
		$flag = 'Landing Pages';
		wp_die( sprintf( __('<p>The <strong>WordPress Calls to Action</strong> plugin requires %s  version %s or greater. <br><br>Please Update WordPress Landing Page Plugin to update Calls to action</p>' , 'cta' ) , $flag , $lp )  ,'Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	}
	elseif (defined('LEADS_CURRENT_VERSION') && version_compare( LEADS_CURRENT_VERSION, $leads, '<' ))
	{
		$flag = 'Leads';
		wp_die( sprintf( __('<p>The <strong>WordPress Calls to Action</strong> plugin requires %s  version %s or greater. <br><br>Please Update WordPress Leads Plugin to update Calls to action</p>' , 'cta' ) , $flag , $leads ) ,'Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	}
	else
	{
		// Activate Plugin
		add_option( 'wp_cta_global_css', '', '', 'no' );
		add_option( 'wp_cta_global_js', '', '', 'no' );
		add_option( 'wp_cta_global_record_admin_actions', '1', '', 'no' );
		add_option( 'wp_cta_global_wp_cta_slug', 'cta', '', 'no' );
		update_option( 'wp_cta_activate_rewrite_check', '1');

		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	// Add default CTA setup and setup 3 categores: sidebar, blog post, popup

}