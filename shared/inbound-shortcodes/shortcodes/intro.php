<?php
/**
*   Intro Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['intro'] = array(
		'no_preview' => true,
		'options' => array(
			'title' => array(
				'name' => __('Title', INBOUND_LABEL),
				'desc' => __('Enter the heading text.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'alignment' => array(
				'name' => __('Text Alignment', INBOUND_LABEL),
				'desc' => __('Enter text alignment.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'align-center' => __('Align Center', INBOUND_LABEL),
					'align-left' => __('Align Left', INBOUND_LABEL),
					'align-right' => __('Align Right', INBOUND_LABEL)
				),
				'std' => 'align-left',
			),
			'content' => array(
				'name' => __('Content', INBOUND_LABEL),
				'desc' => __('Enter the content', INBOUND_LABEL),
				'type' => 'textarea',
				'std' => ''
			)
		),
		'shortcode' => '[intro title="{{title}}" alignment="{{alignment}}"]{{content}}[/intro]',
		'popup_title' => __('Insert Intro Shortcode',  INBOUND_LABEL)
	);

/* 	Page builder module config
 * 	----------------------------------------------------- */
	$freshbuilder_modules['intro'] = array(
		'name' => __('Intro', INBOUND_LABEL),
		'size' => 'one_full',
		'options' => array(
			'title' => array(
				'name' => __('Title', INBOUND_LABEL),
				'desc' => __('Enter the heading text.', INBOUND_LABEL),
				'type' => 'text',
				'class' => '',
				'is_content' => 0
			),
			'alignment' => array(
				'name' => __('Text Alignment', INBOUND_LABEL),
				'desc' => __('The text alignment', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'align-center' => __('Align Center', INBOUND_LABEL),
					'align-left' => __('Align Left', INBOUND_LABEL),
					'align-right' => __('Align Right', INBOUND_LABEL)
				),
				'std' => 'align-left',
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
	add_shortcode('intro', 'inbound_shortcode_intro');

	function inbound_shortcode_intro( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'title' => '',
			'alignment' => ''
		), $atts));

		$out = '';
		$out .= '<div class="intro clearfix '. $alignment .'">';
		$out .= '<h1>'. $title .'</h1>';
		$out .= '<div class="intro-content">'. do_shortcode($content) .'</div>';
		$out .= '</div>';

		return $out;
	}