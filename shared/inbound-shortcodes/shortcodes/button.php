<?php
/**
*   Button Shortcode
*/

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['button'] = array(
		'no_preview' => false,

		'options' => array(
			/*
			'style' => array(
				'name' => __('Button Style', INBOUND_LABEL),
				'desc' => __('Select the button style.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'default' => 'Default',
					'flat' => 'flat',
					'sunk' => 'sunk'
				),
				'std' => 'default'
			),*/
			'content' => array(
				'name' => __('Button Text', INBOUND_LABEL),
				'desc' => __('Enter the button text label.', INBOUND_LABEL),
				'type' => 'text',
				'std' => 'Button Text'
			),
			'url' => array(
				'name' => __('Button Link', INBOUND_LABEL),
				'desc' => __('Enter the destination URL.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'font-size' => array(
							'name' => __('Font Size', INBOUND_LABEL),
							'desc' => __('Size of Button Font. This also determines default button size', INBOUND_LABEL),
							'type' => 'text',
							'std' => '20'
			),
			/*
			'color' => array(
				'name' => __('Button Color', INBOUND_LABEL),
				'desc' => __('Select the button color.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'default' => 'Default',
					'black' => 'Black',
					'blue' => 'Blue',
					'brown' => 'Brown',
					'green' => 'Green',
					'orange' => 'Orange',
					'pink' => 'Pink',
					'purple' => 'Purple',
					'red' => 'Red',
					'silver' => 'Silver',
					'yellow' => 'Yellow',
					'white' => 'White'
				),
				'std' => 'default'
			), */
			'color' => array(
							'name' => __('Button Color', INBOUND_LABEL),
							'desc' => __('Color of button', INBOUND_LABEL),
							'type' => 'colorpicker',
							'std' => '#c8232b'
						),
			'text-color' => array(
							'name' => __('Button Text Color', INBOUND_LABEL),
							'desc' => __('Color of text', INBOUND_LABEL),
							'type' => 'colorpicker',
							'std' => '#ffffff'
						),
			'icon' => array(
				'name' => __('Icon', INBOUND_LABEL),
				'desc' => __('Select an icon.', INBOUND_LABEL),
				'type' => 'select',
				'options' => $fontawesome,
				'std' => ''
			),

			'width' => array(
				'name' => __('Custom Width', INBOUND_LABEL),
				'desc' => __('Enter in pixel width or % width. Example: 200 <u>or</u> 100%', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => 'main-design-settings',
			),
			'target' => array(
				'name' => __('Open Link in New Tab?', INBOUND_LABEL),
				'checkbox_text' => __('Do you want to open links in this window or a new one?', INBOUND_LABEL),
				'desc' => '',
				'type' => 'select',
				'options' => array(
					'_self' => 'Open Link in Same Window',
					'_blank' => 'Open Link in New Tab',

				),
				'std' => '_self'
			),
		),
		// style="{{style}}"
		'shortcode' => '[button font_size="{{font-size}}" color="{{color}}" text_color="{{text-color}}" icon="{{icon}}" url="{{url}}" width="{{width}}" target="{{target}}"]{{content}}[/button]',
		'popup_title' => __('Insert Button Shortcode', INBOUND_LABEL)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */