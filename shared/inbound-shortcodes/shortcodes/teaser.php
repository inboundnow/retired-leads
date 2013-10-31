<?php
/**
*   Teaser Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['teaser'] = array(
		'no_preview' => true,
		'options' => array(
			'heading' => array(
				'name' => __('Heading', INBOUND_LABEL),
				'desc' => __('Enter the heading text', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'style' => array(
				'name' => __('Style', INBOUND_LABEL),
				'desc' => __('Select the style.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'' => __('Default', INBOUND_LABEL),
					'nested' => __('Nested', INBOUND_LABEL),
					'centered' => __('Centered', INBOUND_LABEL)
				),
				'std' => ''
			),
			'column' => array(
				'name' => __('Column', INBOUND_LABEL),
				'desc' => __('Select the number of column.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'1' => __('1 Column', INBOUND_LABEL),
					'2' => __('2 Columns', INBOUND_LABEL),
					'3' => __('3 Columns', INBOUND_LABEL),
					'4' => __('4 Columns', INBOUND_LABEL),
					'5' => __('5 Columns', INBOUND_LABEL)
				),
				'std' => '3'
			)
		),
		'child' => array(
			'options' => array(
				'title' => array(
					'name' => __('Title', INBOUND_LABEL),
					'desc' => __('Enter the title.', INBOUND_LABEL),
					'type' => 'text',
					'std' => ''
				),
				'subtitle' => array(
					'name' => __('Sub Title', INBOUND_LABEL),
					'desc' => __('Enter the sub title.', INBOUND_LABEL),
					'type' => 'text',
					'std' => ''
				),
				'icon' => array(
					'name' => __('Icon', INBOUND_LABEL),
					'desc' => __('Select an icon.', INBOUND_LABEL),
					'type' => 'select',
					'options' => $fontawesome,
					'std' => ''
				),
				'image' => array(
					'name' => __('Image URL', INBOUND_LABEL),
					'desc' => __('Enter your image url, it will override the icon above', INBOUND_LABEL),
					'type' => 'text',
					'std' => '',
					'class' => ''
				),
				'link' => array(
					'name' => __('Link', INBOUND_LABEL),
					'desc' => __('The title link destination URL.', INBOUND_LABEL),
					'type' => 'text',
					'std' => ''
				),
				'content' => array(
					'name' => __('Teaser Content', INBOUND_LABEL),
					'desc' => __('Enter the content.', INBOUND_LABEL),
					'type' => 'textarea',
					'std' => ''
				)
			),
			'shortcode' => '[block title="{{title}}" subtitle="{{subtitle}}" icon="{{icon}}" link="{{link}}" ]{{content}}[/block]',
			'clone' => __('Add More Block',  INBOUND_LABEL )
		),
		'shortcode' => '[teaser heading="{{heading}}" style="{{style}}" column="{{column}}"]{{child}}[/teaser]',
		'popup_title' => __('Insert Teaser Shortcode', INBOUND_LABEL)
	);

/* 	Page builder module config
 * 	----------------------------------------------------- */
	$freshbuilder_modules['teaser'] = array(
		'name' => __('Teaser', INBOUND_LABEL),
		'size' => 'one_full',
		'options' => array(
			'heading' => array(
				'name' => __('Heading', INBOUND_LABEL),
				'desc' => __('Enter the heading text.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'style' => array(
				'name' => __('Style', INBOUND_LABEL),
				'desc' => __('Select the style.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'' => __('Default', INBOUND_LABEL),
					'nested' => __('Nested', INBOUND_LABEL),
					'centered' => __('Centered', INBOUND_LABEL)
				),
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'column' => array(
				'name' => __('Column', INBOUND_LABEL),
				'desc' => __('Select the column.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'1' => __('1 Column', INBOUND_LABEL),
					'2' => __('2 Columns', INBOUND_LABEL),
					'3' => __('3 Columns', INBOUND_LABEL),
					'4' => __('4 Columns', INBOUND_LABEL),
					'5' => __('5 Columns', INBOUND_LABEL)
				),
				'std' => '3',
				'class' => '',
				'is_content' => 0
			)
		),
		'child' => array(
			'icon' => array(
				'name' => __('Icon', INBOUND_LABEL),
				'desc' => __('Select an icon.', INBOUND_LABEL),
				'type' => 'select',
				'options' => $fontawesome,
				'std' => 'none',
				'class' => '',
				'is_content' => 0
			),
			'image' => array(
				'name' => __('Image URL', INBOUND_LABEL),
				'desc' => __('Enter your image url, it will override the icon above', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'title' => array(
				'name' => __('Title', INBOUND_LABEL),
				'desc' => __('Enter the heading text.', INBOUND_LABEL),
				'type' => 'text',
				'class' => '',
				'is_content' => 0
			),
			'subtitle' => array(
				'name' => __('Sub Title', INBOUND_LABEL),
				'desc' => __('Enter the sub title.', INBOUND_LABEL),
				'type' => 'text',
				'class' => '',
				'is_content' => 0
			),
			'link' => array(
				'name' => __('Link', INBOUND_LABEL),
				'desc' => __('The title link destination URL.', INBOUND_LABEL),
				'type' => 'text',
				'class' => '',
				'is_content' => 0
			),
			'content' => array(
				'name' => __('Content', INBOUND_LABEL),
				'desc' => __('Enter the content.', INBOUND_LABEL),
				'type' => 'textarea',
				'std' => '',
				'class' => '',
				'is_content' => 1
			)
		),
		'child_code' => 'block'
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('teaser', 'inbound_shortcode_teaser');

	function inbound_shortcode_teaser( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'heading' => '',
			'style' => '',
			'column' => '4'
		), $atts));

		$out = '';

		$grid = ' grid full';
		if ($column == '2') $grid = ' grid one-half';
		if ($column == '3') $grid = ' grid one-third';
		if ($column == '4') $grid = ' grid one-fourth';
		if ($column == '5') $grid = ' grid one-fifth';

		$style = ($style != '') ? ' '. $style : '';

		if (!preg_match_all("/(.?)\[(block)\b(.*?)(?:(\/))?\](?:(.+?)\[\/block\])?(.?)/s", $content, $matches)) {
			return do_shortcode($content);
		}
		else {

			for($i = 0; $i < count($matches[0]); $i++) {
				$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
			}

			$out .= '<div class="row">';

				if ($heading != '') {
					$out .= '<div class="grid full"><div class="heading"><h3>'.$heading.'</h3><div class="sep"></div></div></div>';
				}

				for($i = 0; $i < count($matches[0]); $i++) {
					$title = ( $matches[3][$i]['link'] ) ? '<a class="reserve" href="'. $matches[3][$i]['link'] .'">'. $matches[3][$i]['title'] .'</a>' : $matches[3][$i]['title'];

					$out .= '<aside class="teaser'. $grid . $style .'">';

						if( $matches[3][$i]['image'] ) {
							$out .= '<div class="teaser-image"><img src="'. $matches[3][$i]['image'] .'" alt="" /></div>';
						}
						elseif ( $matches[3][$i]['icon'] ) {
							$out .= '<div class="teaser-icon"><i class="icon-'. $matches[3][$i]['icon'] .'"></i></div>';
						}

						$out .= '<header class="teaser-header">';

							$out .= '<h3 class="teaser-title">'.$title.'</h3>';

							if( $matches[3][$i]['subtitle'] ) {
								$out .= '<div class="teaser-subtitle">'. $matches[3][$i]['subtitle'] .'</div>';
							}
						$out .= '</header>';

						if( $matches[5][$i] ) {
							$out .= '<div class="teaser-content">'.do_shortcode( trim($matches[5][$i]) ).'</div>';
						}
					$out .= '</aside>';
				}

				if( $i == $column - 1 ) {
					$out .= '<div class="clear"></div>';
				}

			$out .= '</div>';
		}

		return $out;
	}