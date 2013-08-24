<?php
/* 
function add_first_and_last($output) {
  $output = preg_replace('/class="menu-item/', 'class="first-menu-item menu-item', $output, 1);
  $output = substr_replace($output, 'class="last-menu-item menu-item', strripos($output, 'class="menu-item'), strlen('class="menu-item'));
  return $output;
}
add_filter('wp_nav_menu', 'add_first_and_last');
//Filtering a Class in Navigation Menu Item
add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
function special_nav_class($classes, $item){
     if ( 'wp-call-to-action' == get_post_type() ) {
             $classes[] = 'wp_cta_explode_menu';
     }
     return $classes;
}*/

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
//add_action('wp','wpcta_remove_plugin_filters');
/**
 * Debug Activation errors */
/*
 add_action('activated_plugin','save_error');
function save_error(){
    update_option('plugin_error',  ob_get_contents());
}
echo "here" . get_option('plugin_error') . "hi";
 */
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
}