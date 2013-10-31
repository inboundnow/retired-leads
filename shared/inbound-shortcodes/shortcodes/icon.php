<?php
/**
*   Icon Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['icon'] = array(
		'options' => array(
			'icon' => array(
				'name' => __('Icon', INBOUND_LABEL),
				'desc' => __('Select the icon.', INBOUND_LABEL),
				'type' => 'select',
				'options' => $fontawesome,
				'std' => 'none'
			),
			'size' => array(
				'name' => __('Size', INBOUND_LABEL),
				'desc' => __('Select the icon size.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'normal' => __('Normal Size', INBOUND_LABEL),
					'large' => __('Large Size', INBOUND_LABEL),
					'2x' => __('2x Size', INBOUND_LABEL),
					'3x' => __('3x Size', INBOUND_LABEL),
					'4x' => __('4x Size', INBOUND_LABEL)
				),
				'std' => 'normal'
			),
			'style' => array(
				'name' => __('Style', INBOUND_LABEL),
				'desc' => __('Select the icon style.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'normal' => __('Normal', INBOUND_LABEL),
					'muted' => __('Muted', INBOUND_LABEL),
					'border' => __('Border', INBOUND_LABEL),
					'spin' => __('Spin', INBOUND_LABEL)
				),
				'std' => 'normal'
			),
		),
		'shortcode' => '[icon icon="{{icon}}" size="{{size}}" style="{{style}}"]',
		'popup_title' => __('Insert Icon Shortcode', INBOUND_LABEL)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('icon', 'inbound_shortcode_icon');

	function inbound_shortcode_icon( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'icon' => '',
			'size' => '',
			'style' => ''
		), $atts));

		return '<i class="icon-'. $icon .' icon-'. $size .' icon-'. $style .'"></i>';
	}