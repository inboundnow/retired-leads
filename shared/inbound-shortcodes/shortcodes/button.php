<?php
/**
*   Button Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['button'] = array(
		'no_preview' => false,

		'options' => array(
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
			),
			'content' => array(
				'name' => __('Label', INBOUND_LABEL),
				'desc' => __('Enter the button text label.', INBOUND_LABEL),
				'type' => 'text',
				'std' => 'Button Text'
			),
			'size' => array(
				'name' => __('Button Size', INBOUND_LABEL),
				'desc' => __('Select the button size.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'small' => 'Small',
					'normal' => 'Normal',
					'large' => 'Large'
				),
				'std' => 'normal'
			),
			'width' => array(
				'name' => __('Custom Width', INBOUND_LABEL),
				'desc' => __('Enter in pixel width or % width. Example: 200 <u>or</u> 100%', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => 'main-design-settings',
			),
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
			),
			'icon' => array(
				'name' => __('Icon', INBOUND_LABEL),
				'desc' => __('Select an icon.', INBOUND_LABEL),
				'type' => 'select',
				'options' => $fontawesome,
				'std' => ''
			),
			'url' => array(
				'name' => __('Link Destination', INBOUND_LABEL),
				'desc' => __('Enter the destination URL.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'blank' => array(
				'name' => __('Open Link in New Tab?', INBOUND_LABEL),
				'checkbox_text' => __('Check to open the link in the new tab.', INBOUND_LABEL),
				'desc' => '',
				'type' => 'checkbox',
				'std' => '1'
			),
		),
		'shortcode' => '[button style="{{style}}" size="{{size}}" color="{{color}}" icon="{{icon}}" url="{{url}}" blank="{{blank}}"]{{content}}[/button]',
		'popup_title' => __('Insert Button Shortcode', INBOUND_LABEL)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('button', 'inbound_shortcode_button');
	if (!function_exists('inbound_shortcode_button')) {
		function inbound_shortcode_button( $atts, $content = null ) {
			extract(shortcode_atts(array(
				'style'=> '',
				'size' => '',
				'color' => '',
				'icon' => '',
				'url' => '',
				'blank' => ''
			), $atts));

			$class = "button $color $size";
			$icon_raw = 'icon-'. $icon;
			$target = ($blank) ? ' target="_blank"' : '';
			$button_start = "";

				switch( $style ) {

						case 'default':
							$button  = $button_start;
							$button .= '<a class="'. $class .'" href="'. $url .'"'. $target .'><i class="'.$icon_raw.'"></i>&nbsp;' . $content .'</a>';
							$button .= $button_start;
							break;

						case 'flat' :
							$button  = $button_start;
							$button .= '<a href="'. $url .'"'. $target .' class="inbound-flat-btn facebook"><span class="'.$icon_raw.' icon"></span><span>'.$content.'</span></a>';

							$button .= $button_start;
							break;
						case 'sunk' :
							$button  = $button_start;
							$button .= '<div class="inbound-sunk-button-wrapper">
										<a href="'. $url .'"'. $target .' class="inbound-sunk-button inbound-sunk-light"><span class="'.$icon_raw.' icon"></span>'.$content.'</a>
										</div>';

							$button .= $button_start;
							break;
					}


			return $button;
		}
	}