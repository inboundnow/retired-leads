<?php
/**
*   Social Share Shortcode
*   Built from http://www.mojotech.com/social-builder
*/

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['social-share'] = array(
		'no_preview' => false,
		'options' => array(
			'style' => array(
				'name' => 'Style of Icons',
				'desc' => __('Style of Icons', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					"bar" => "Bar",
					"circle" => "Circle",
					'square' => "Square",
					'black' => "Black",

					),
				'std' => 'bar'
			),
			'align' => array(
				'name' => __('Align Icons', INBOUND_LABEL),
				'desc' => __('Alignment Settings', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					"horizontal" => "Horizontal",
					"vertical" => "Vertical",
					),
				'std' => 'inline-block'
			),

			'facebook' => array(
				'name' => __('Facebook', INBOUND_LABEL),
				'desc' => __('Show facebook share icon', INBOUND_LABEL),
				'type' => 'checkbox',
				'std' => '1'
			),
			'twitter' => array(
				'name' => __('Twitter', INBOUND_LABEL),
				'desc' => __('Show twitter share icon', INBOUND_LABEL),
				'type' => 'checkbox',
				'std' => '1'
			),
			'google_plus' => array(
				'name' => __('Google+', INBOUND_LABEL),
				'desc' => __('Show google plus share icon', INBOUND_LABEL),
				'type' => 'checkbox',
				'std' => '1'
			),
			'linkedin' => array(
				'name' => __('Linkedin', INBOUND_LABEL),
				'desc' => __('Show linkedin share icon', INBOUND_LABEL),
				'type' => 'checkbox',
				'std' => '1'
			),
			'pinterest' => array(
				'name' => __('Pinterest', INBOUND_LABEL),
				'desc' => __('Show pinterest share icon', INBOUND_LABEL),
				'type' => 'checkbox',
				'std' => '1',
			),

			'text' => array(
				'name' => __('Custom Share Text', INBOUND_LABEL),
				'desc' => __('Optional setting. Enter your custom share text', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'placeholder' => 'Custom Share Text. Title of page used by default',
			),
			'link' => array(
				'name' => __('Custom Share URL', INBOUND_LABEL),
				'desc' => __('Optional setting. Enter your custom share link URL', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'placeholder' => 'Custom URL. Page permalink used by default',
			),
			'heading' => array(
				'name' => __('Heading', INBOUND_LABEL),
				'desc' => __('Optional setting.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'placeholder' => 'Optional Header Text',
			),
			'header-align' => array(
				'name' => __('Heading Align', INBOUND_LABEL),
				'desc' => __('Heading Alignment Settings', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					"inline" => "Inline",
					"above" => "Above",

					),
				'std' => 'inline'
			),
		),
		'shortcode' => '[social_share style="{{style}}" align="{{align}}" heading_align="{{header-align}}" text="{{text}}" heading="{{heading}}" facebook="{{facebook}}" twitter="{{twitter}}" google_plus="{{google_plus}}" linkedin="{{linkedin}}" pinterest="{{pinterest}}" link="{{link}}" /]',
		'popup_title' => __('Insert Social Share Shortcode', INBOUND_LABEL)
	);