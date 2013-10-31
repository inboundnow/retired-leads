<?php
/**
*   List Icon Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['list_icons'] = array(
		'no_preview' => true,
		'options' => array(),
		'child' => array(
			'options' => array(
				'icon' => array(
					'name' => __('Icon', INBOUND_LABEL),
					'desc' => __('Select the icon.', INBOUND_LABEL),
					'type' => 'select',
					'options' => $fontawesome,
					'std' => 'none'
				),
				'content' => array(
					'name' => __('List Content',  INBOUND_LABEL),
					'desc' => __('Put the content here.',  INBOUND_LABEL),
					'type' => 'textarea',
					'std' => ''
				)
			),
			'shortcode' => '[list icon="{{icon}}"]{{content}}[/list]',
			'clone' => __('Add More List',  INBOUND_LABEL )
		),
		'shortcode' => '[list_icons]{{child}}[/list_icons]',
		'popup_title' => __('Insert List Icons Shortcode', INBOUND_LABEL)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('list_icons', 'inbound_shortcode_list_icons');

	function inbound_shortcode_list_icons( $atts, $content = null ) {
		extract(shortcode_atts(array(), $atts));

		$out = '';

		if (!preg_match_all("/(.?)\[(list)\b(.*?)(?:(\/))?\](?:(.+?)\[\/list\])?(.?)/s", $content, $matches)) {

			return do_shortcode($content);

		} else {

			for($i = 0; $i < count($matches[0]); $i++) {
				$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
			}

			$out .= '<ul class="icons">';

			for($i = 0; $i < count($matches[0]); $i++) {
				$icon = ( $matches[3][$i]['icon'] ) ? '<i class="icon-'. $matches[3][$i]['icon'] .'"></i>' : '';

	            $out .= '<li>'. $icon . do_shortcode(trim($matches[5][$i])) .'</li>';
            }

			$out .= '</ul>';
		}

		return $out;
	}