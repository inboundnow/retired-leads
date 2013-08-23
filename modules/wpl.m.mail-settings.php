<?php
	$tab_slug = 'mail';
	$wpleads_global_settings[$tab_slug]['label'] = 'Global Settings';	
	
	$wpleads_global_settings[$tab_slug]['options'][] = wpleads_add_option($tab_slug,"text","landing-page-permalink-prefix","go","Default landing page permalink prefix","Enter in the 'prefix' for landing page permalinks. eg: /prefix/pemalink-name", $options=null);
	$wpleads_global_settings[$tab_slug]['options'][] = wpleads_add_option($tab_slug,"text","landing-page-group-permalink-prefix","group","Default split testing group permalink prefix","Enter in the 'prefix' for split testing group permalinks. eg: /prefix/pemalink-name", $options=null);
	$wpleads_global_settings[$tab_slug]['options'][] = wpleads_add_option($tab_slug,"radio","landing-page-auto-format-forms","1","Enable Form Standardization","With this setting enabled landing pages plugin will clean and standardize all input ids and classnames. Uncheck this setting to disable standardization.", $options= array('1'=>'on','0'=>'off'));
?>