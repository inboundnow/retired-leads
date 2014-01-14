<?php

/**
 * SETUP DEBUG TOOLS
 */

add_action( 'init', 'inbound_meta_debug' );
if (!function_exists('inbound_meta_debug')) {
	function inbound_meta_debug(){
	//print all global fields for post
	if (isset($_GET['debug'])) {
			global $wpdb;
			$data   =   array();
			$wpdb->query("
			  SELECT `meta_key`, `meta_value`
				FROM $wpdb->postmeta
				WHERE `post_id` = ".$_GET['post']."
			");
			foreach($wpdb->last_result as $k => $v){
				$data[$v->meta_key] =   $v->meta_value;
			};
			if (isset($_GET['post']))
			{
				echo "<pre>";
				print_r( $data);
				echo "</pre>";
			}
		}
	}
}


add_action( 'wp_head', 'wp_cta_kill_ie8' );
function wp_cta_kill_ie8() {
    global $is_IE;
    if ( $is_IE ) {
        echo '<!--[if lt IE 9]>';
        echo '<link rel="stylesheet" type="text/css" href="'.WP_CTA_URLPATH.'/css/ie8-and-down.css" />';
        echo '<![endif]-->';
    }
}


// Fix SEO Title Tags to not use the_title
//add_action('wp','wpcta_seo_title_filters');
function wpcta_seo_title_filters() {

    global $wp_filter;
    global $wp;
	print_r($wp);exit;
    if (strstr())
	{
       add_filter('wp_title', 'wp_cta_fix_seo_title', 100);
    }
}   

function wp_cta_fix_seo_title() 
{
	if ('wp-call-to-action' == get_post_type()) 
	{
		global $post;
	if (get_post_meta($post->ID, '_yoast_wpseo_title', true)) {
		$seotitle = get_post_meta($post->ID, '_yoast_wpseo_title', true) . " ";
	// All in one seo get_post_meta($post->ID, '_aioseop_title', true) for future use	
	} else {
		$seotitle = $seotitle = get_post_meta($post->ID, 'wp-cta-main-headline', true) . " "; }
	}
	return $seotitle;
}

// Add Custom Class to Landing Page Nav Menu to hide/remove
add_filter( 'wp_nav_menu_args', 'wp_cta_wp_nav_menu_args' );
function wp_cta_wp_nav_menu_args( $args = '' )
{
	global $post;
	if ( 'wp-call-to-action' == get_post_type() ) {
		$nav_status = get_post_meta($post->ID, 'default-wp_cta_hide_nav', true);
		if ($nav_status === 'off' || empty($nav_status)) {
			if (isset($args['container_class']))
			{
				$current_class = " ".$args['container_class'];
			}

			$args['container_class'] = "custom_wp_call_to_action_nav{$current_class}";
			
			$args['echo'] = false; // works!
		}
	}
	
	
	return $args;
}

// Remove Base Theme Styles from templates
add_action('wp_print_styles', 'wp_cta_remove_all_styles', 100);
function wp_cta_remove_all_styles() 
{
	if (!is_admin())
	{
		if ( 'wp-call-to-action' == get_post_type() ) 
		{
			global $post;
			$template = get_post_meta($post->ID, 'wp-cta-selected-template', true);

			if (strstr($template,'-slash-'))
			{
				$template = str_replace('-slash-','/',$template);
			}
					
			$my_theme =  wp_get_theme($template);
			
			if ($my_theme->exists()||$template=='blank-template')
			{
				return;
			}
			else
			{
				global $wp_styles;
				$wp_styles->queue = array();
				//wp_register_style( 'admin-bar' );
				wp_enqueue_style( 'admin-bar' );
			}	
		}	
	}

}
// Remove all body_classes from custom landing page templates - disabled but you can use the function above to model native v non-native template conditionals.
/*
add_action('wp','wpcta_remove_plugin_filters');

function wpcta_remove_plugin_filters() {

    global $wp_filter;
    global $wp;
    if ($wp->query_vars["post_type"] == 'wp-call-to-action') {
       add_filter('body_class','wp_cta_body_class_names');
    }
}   

function wp_cta_body_class_names($classes) {
	 global $post;
	if('wp-call-to-action' == get_post_type() ) {
 	$arr = array();
    $template_id = get_post_meta($post->ID, 'wp-cta-selected-template', true);
    $arr[] = 'template-' . $template_id;
 }
    return $arr;
}*/