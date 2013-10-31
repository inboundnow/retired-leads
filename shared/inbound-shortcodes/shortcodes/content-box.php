<?php
/**
*   Content Box Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['content_box'] = array(
		'no_preview' => true,
		'options' => array(
			'color' => array(
				'name' => __('Box Color', INBOUND_LABEL),
				'desc' => __('Select the color.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'default' => __('Default', INBOUND_LABEL),
					'blue' => __('Blue', INBOUND_LABEL),
					'green' => __('Green', INBOUND_LABEL),
					'red' => __('Red', INBOUND_LABEL),
					'yellow' => __('Yellow', INBOUND_LABEL)
				),
				'std' => ''
			),
			'content' => array(
				'name' => __('Content', INBOUND_LABEL),
				'desc' => __('Enter the content.', INBOUND_LABEL),
				'type' => 'textarea',
				'std' => ''
			)
		),
		'shortcode' => '[content_box color="{{color}}"]{{content}}[/content_box]',
		'popup_title' => __('Insert Content Box Shortcode', INBOUND_LABEL)
	);

/* 	Page builder module config
 * 	----------------------------------------------------- */
	$freshbuilder_modules['content_box'] = array(
		'name' => __('Content Box', INBOUND_LABEL),
		'size' => 'one_third',
		'options' => array(
			'color' => array(
				'name' => __('Box Color', INBOUND_LABEL),
				'desc' => __('Select the color.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'default' => __('Default', INBOUND_LABEL),
					'blue' => __('Blue', INBOUND_LABEL),
					'green' => __('Green', INBOUND_LABEL),
					'red' => __('Red', INBOUND_LABEL),
					'yellow' => __('Yellow', INBOUND_LABEL)
				),
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'content' => array(
				'name' => __('Content', INBOUND_LABEL),
				'desc' => __('Enter the content', INBOUND_LABEL),
				'type' => 'textarea',
				'std' => '',
				'class' => '',
				'is_content' => 1
			)
		)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('content_box', 'inbound_shortcode_content_box');
	if (!function_exists('inbound_shortcode_content_box')) {
		function inbound_shortcode_content_box( $atts, $content = null ) {
			extract(shortcode_atts(array(
				'color' => 'default'
			), $atts));

			return '<div class="content-box '.$color.'">'.do_shortcode($content).'</div>';
		}
	}