<?php

/**
 * LOAD COMMONLY USED TEMPLATE TOOLS
 */
add_action('wp_cta_init', 'inbound_include_template_functions');
if (!function_exists('inbound_include_template_functions'))
{
	function inbound_include_template_functions()
	{
		include_once(CTA_PATH.'shared/functions.templates.php');
	}
}


/**
 * LOAD CORRECT CTA TEMPLATE ON FRONTEND
 */

add_filter('single_template', 'wp_cta_custom_template');

function wp_cta_custom_template($single) {
    global $wp_query, $post, $query_string;

	$template = get_post_meta($post->ID, 'wp-cta-selected-template', true);
	$template = apply_filters('wp_cta_selected_template',$template);

	if (isset($template))
	{
		//echo 2;exit;
		if ($post->post_type == "wp-call-to-action")
		{
			if (strstr($template,'-slash-'))
			{
				$template = str_replace('-slash-','/',$template);
			}

			$my_theme =  wp_get_theme($template);

			if ($my_theme->exists())
			{
				return "";
			}
			else if ($template!='default')
			{
				$template = str_replace('_','-',$template);
				//echo CTA_URLPATH.'templates/'.$template.'/index.php'; exit;
				if (file_exists(CTA_PATH.'templates/'.$template.'/index.php'))
				{
					//query_posts ($query_string . '&showposts=1');
					return CTA_PATH.'templates/'.$template.'/index.php';
				}
				else
				{
					//query_posts ($query_string . '&showposts=1');
					return CTA_UPLOADS_PATH.$template.'/index.php';
				}
			}
		}
	}
    return $single;
}

/**
 * APPLY CTA CUSTOM JS AND CUSTOM CSS TO FRONT END
 */
add_action('wp_head','wp_call_to_actions_insert_custom_head');
function wp_call_to_actions_insert_custom_head() 
{
	global $post;

   if (isset($post)&&'wp-call-to-action'==$post->post_type)
   {
		//$global_js =  htmlspecialchars_decode(get_option( 'wp_cta_global_js', '' ));
		$global_record_admin_actions = get_option( 'wp_cta_global_record_admin_actions', '0' );

		$custom_css_name = apply_filters('wp-cta-custom-css-name','wp-cta-custom-css');
		$custom_js_name = apply_filters('wp-cta-custom-js-name','wp-cta-custom-js');
		//echo $custom_css_name;
		$custom_css = get_post_meta($post->ID, $custom_css_name, true);
		$custom_js = get_post_meta($post->ID, $custom_js_name, true);
		//echo $this_id;exit;

		//Print Cusom CSS
		if (!stristr($custom_css,'<style'))
		{
			echo '<style type="text/css" id="wp_cta_css_custom">'.$custom_css.'</style>';
		}
		else
		{
			echo $custom_css;
		}
		if (!stristr($custom_css,'<script'))
		{
			echo '<script type="text/javascript" id="wp_cta_js_custom">jQuery(document).ready(function($) {
			'.$custom_js.' });</script>';
		}
		else
		{
			echo $custom_js;
		}

		if ($global_record_admin_actions==0&&current_user_can( 'manage_options' ))
		{
		}
		else
		{

			if (!wp_cta_determine_spider())
			{
				//wp_cta_set_page_views(get_the_ID($this_id));
			}
		}

		//rewind_posts();
		//wp_reset_query();
   }
}

/* add liostener to check to render cta in full screen mode */
add_filter('admin_url','wp_cta_add_fullscreen_param');
function wp_cta_add_fullscreen_param( $link )
{
	if (isset($_GET['page']))
		return $link;

	if (  ( isset($post) && 'wp-call-to-action' == $post->post_type ) || ( isset($_REQUEST['post_type']) && $_REQUEST['post_type']=='wp-call-to-action' ) )
	{
		$params['frontend'] = 'false';
		if(isset($_GET['frontend']) && $_GET['frontend'] == 'true') {
	        $params['frontend'] = 'true';
	    }
	    if(isset($_REQUEST['frontend']) && $_REQUEST['frontend'] == 'true') {
	        $params['frontend'] = 'true';
	    }
	    $link = add_query_arg( $params, $link );

	}

	return $link;
}
