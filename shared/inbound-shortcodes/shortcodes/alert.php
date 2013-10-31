<?php
/**
*   Alert Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['alert'] = array(
		'no_preview' => true,
		'options' => array(
			'color' => array(
				'name' => __('Color Style', INBOUND_LABEL),
				'desc' => __('Select the style.', INBOUND_LABEL),
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
				'name' => __('Message', INBOUND_LABEL),
				'desc' => __('Your message here.', INBOUND_LABEL),
				'type' => 'textarea',
				'std' => ''
			)
		),
		'shortcode' => '[alert color="{{color}}"]{{content}}[/alert]',
		'popup_title' => __('Insert Alert Message Shortcode', INBOUND_LABEL)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('alert', 'inbound_shortcode_alert');
	if (!function_exists('inbound_shortcode_alert')) {
		function inbound_shortcode_alert( $atts, $content = null ) {
			extract(shortcode_atts(array(
				'color' => ''
			), $atts));

			return '<div class="alert-message '.$color.'">'.do_shortcode($content).'</div>';
		}
	}