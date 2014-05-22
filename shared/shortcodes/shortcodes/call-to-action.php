<?php
/**
*   Inbound Forms Shortcode Options
*   Forms code found in /shared/classes/form.class.php
*/

	$shortcodes_config['call-to-action'] = array(
		'no_preview' => true,
		'options' => array(
			'insert_default' => array(
						'name' => __('Insert cta', 'leads'),
						'desc' => __('Choose CTA', 'leads'),
						'type' => 'cta',
						'std' => '',
						'class' => 'main-form-settings',
			),
			'align' => array(
						'name' => __('CTA Alignment', 'leads'),
						'desc' => __('Choose Your Form Layout', 'leads'),
						'type' => 'select',
						'options' => array(
							"none" => "None (Centered)",
							"right" => "Float Right",
							"left" => "Float Left",
							),
						'std' => 'none',
			),

		),
		'shortcode' => '[inbound_forms id="{{insert_default}}" name="{{form_name}}" align="{{align}}"]',
		'popup_title' => __('Insert Call to Action',  'leads')
	);
