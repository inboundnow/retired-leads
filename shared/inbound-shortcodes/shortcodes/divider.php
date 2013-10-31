<?php
/**
*   Divider Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['divider'] = array(
		'no_preview' => true,
		'options' => array(
			'style' => array(
				'name' => __('Border Style', INBOUND_LABEL),
				'desc' => __('Select the style.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'none' => __('No Border', INBOUND_LABEL),
					'dashed' => __('Dashed', INBOUND_LABEL),
					'dotted' => __('Dotted', INBOUND_LABEL),
					'double' => __('Double', INBOUND_LABEL),
					'solid' => __('Solid', INBOUND_LABEL)
				),
				'std' => 'none'
			),
			'color' => array(
				'name' => __('Border Color', INBOUND_LABEL),
				'desc' => __('Enter a hex color code.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '#ebebea'
			),
			'margin_top' => array(
				'name' => __('Top Margin', INBOUND_LABEL),
				'desc' => __('Enter the top margin value.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '0px'
			),
			'margin_bottom' => array(
				'name' => __('Bottom Margin', INBOUND_LABEL),
				'desc' => __('Enter the bottom margin value.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '0px'
			)
		),
		'shortcode' => '[divider style="{{style}}" color="{{color}}" margin_top="{{margin_top}}" margin_bottom="{{margin_bottom}}"]',
		'popup_title' => __('Insert Divider Shortcode', INBOUND_LABEL)
	);

/* 	Page builder module config
 * 	----------------------------------------------------- */
	$freshbuilder_modules['divider'] = array(
		'name' => __('Divider', INBOUND_LABEL),
		'size' => 'one_full',
		'options' => array(
			'style' => array(
				'name' => __('Border Style', INBOUND_LABEL),
				'desc' => __('Select the style.', INBOUND_LABEL),'type' => 'select',
				'options' => array(
					'none' => __('No Border', INBOUND_LABEL),
					'dashed' => __('Dashed', INBOUND_LABEL),
					'dotted' => __('Dotted', INBOUND_LABEL),
					'double' => __('Double', INBOUND_LABEL),
					'solid' => __('Solid', INBOUND_LABEL)
				),
				'std' => 'none',
				'class' => '',
				'is_content' => '0'
			),
			'color' => array(
				'name' => __('Border Color', INBOUND_LABEL),
				'desc' => __('Enter a hex color code.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '#ebebea',
				'class' => '',
				'is_content' => '0'
			),
			'margin_top' => array(
				'name' => __('Margin Top', INBOUND_LABEL),
				'desc' => __('Enter the top margin value.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '0px',
				'class' => '',
				'is_content' => '0'
			),
			'margin_bottom' => array(
				'name' => __('Margin Bottom', INBOUND_LABEL),
				'desc' => __('Enter the bottom margin value.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '0px',
				'class' => '',
				'is_content' => '0'
			)
		)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('divider', 'inbound_shortcode_divider');
	if (!function_exists('inbound_shortcode_divider')) {
		function inbound_shortcode_divider( $atts, $content = null ) {
			extract(shortcode_atts(array(
				'style' => '',
				'margin_top' => '',
				'margin_bottom' => '',
				'color' => '',
				'class' => ''
			), $atts));

			$margin_top = ($margin_top) ? $margin_top : 0;
			$margin_bottom = ($margin_bottom) ? $margin_bottom : 0;
			$color = ($color) ? $color : '#eaeaea';
			$class = ($class) ? " $class" : '';

			return '<div class="divider '. $style . $class .'" style="margin-top:'. $margin_top .';margin-bottom:'. $margin_bottom .';border-color:'. $color .'"></div>';
		}
	}