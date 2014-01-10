<?php

if (!function_exists('inboundnow_add_master_license'))
{
	/* Add Master License Key Setting*/
	add_filter('lp_define_global_settings', 'inboundnow_add_master_license', 1, 1);
	add_filter('wpleads_define_global_settings', 'inboundnow_add_master_license', 1, 1);
	add_filter('wpcta_define_global_settings', 'inboundnow_add_master_license', 1, 1);
	function inboundnow_add_master_license($lp_global_settings)
	{
		if (array_key_exists('lp-license-keys',$lp_global_settings))
		{
			$lp_global_settings['lp-license-keys']['settings'][] = 	array(
					'id'  => 'extensions-license-keys-master-key-header',
					'description' => __( "Head to http://www.inboundnow.com/ to retrieve your extension-ready license key." , LANDINGPAGES_TEXT_DOMAIN),
					'type'  => 'header',
					'default' => '<h3 class="lp_global_settings_header">'. __( 'InboundNow Master Key' , LANDINGPAGES_TEXT_DOMAIN) .'</h3>'
			);
			
			$lp_global_settings['lp-license-keys']['settings'][] = 	array(
					'id'  => 'inboundnow_master_license_key',
					'option_name'  => 'inboundnow_master_license_key',
					'label' => __('InboundNow Master License Key' , LANDINGPAGES_TEXT_DOMAIN),
					'description' => __( "Head to http://www.inboundnow.com/ to retrieve your extension-ready license key." , LANDINGPAGES_TEXT_DOMAIN),
					'type'  => 'text',
					'default' => ''
			);
		}

		return $lp_global_settings;
	}
}
