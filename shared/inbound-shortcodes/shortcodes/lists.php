<?php
/**
*   Content Box Shortcode
*/

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['lists'] = array(
		'no_preview' => false,
		'options' => array(
			'icon' => array(
							'name' => __('List Icon', INBOUND_LABEL),
							'desc' => __('Select an icon for the List', INBOUND_LABEL),
							'type' => 'select',
							'options' => $fontawesome,
							'std' => 'ok-sign'
						),
			'font-size' => array(
							'name' => __('Font Size', INBOUND_LABEL),
							'desc' => __('Size of List Font', INBOUND_LABEL),
							'type' => 'text',
							'std' => '20'
						),
			'bottom-margin' => array(
							'name' => __('Bottom Margin', INBOUND_LABEL),
							'desc' => __('space between list items', INBOUND_LABEL),
							'type' => 'text',
							'std' => '10'
						),
			'icon-color' => array(
							'name' => __('Icon Color', INBOUND_LABEL),
							'desc' => __('Color of Icon', INBOUND_LABEL),
							'type' => 'colorpicker',
							'std' => '000000'
						),
			'text-color' => array(
							'name' => __('Text Color', INBOUND_LABEL),
							'desc' => __('Color of Text in List', INBOUND_LABEL),
							'type' => 'colorpicker',
							'std' => ''
						),


		),
		'shortcode' => '[list icon="{{icon}}" font_size="{{font-size}}" icon_color="{{icon-color}}" text_color="{{text-color}}" bottom_margin="{{bottom-margin}}"](Insert Your Unordered List Here. Use the List insert button in the editor. Delete this text)[/list]',
		'popup_title' => __('Insert Styled List Shortcode', INBOUND_LABEL)
	);