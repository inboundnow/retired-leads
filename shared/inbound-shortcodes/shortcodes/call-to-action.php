<?php
/**
*   Inbound Forms Shortcode Options
*   Forms code found in /shared/classes/form.class.php
*/

	$shortcodes_config['call-to-action'] = array(
		'no_preview' => false,
		'options' => array(
			'insert_default' => array(
						'name' => __('Insert cta', INBOUND_LABEL),
						'desc' => __('Choose CTA', INBOUND_LABEL),
						'type' => 'cta',
						'std' => '',
						'class' => 'main-form-settings',
			),

		),
		'shortcode' => '[inbound_forms id="{{insert_default}}" name="{{form_name}}"]',
		'popup_title' => __('Quick Insert Inbound Form Shortcode',  INBOUND_LABEL)
	);
