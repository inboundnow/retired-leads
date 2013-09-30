<?php


/**
 * LOAD NATIVE TEMPLATES FROM WP-CONTENT/PLUGINS LANDING-PAGES/TEMPLATES/
 */		
 
	$template_paths = wp_cta_get_core_template_paths();	

	if (count($template_paths)>0)
	{
		foreach ($template_paths as $name)
		{	
			if ($name != ".svn"){	
			include_once(WP_CTA_PATH."templates/$name/config.php");
			}	
		}		
	}

/**
 * LOAD NON-NATIVE TEMPLATES FROM WP-CONTENT/UPLOADS/LANDING-PAGES/TEMPLATES/
 */
 
	$template_paths = wp_cta_get_extension_template_paths();	
	$uploads = wp_upload_dir();
	$uploads_path = $uploads['basedir'];
	//print_r($template_paths);exit;
	$extended_templates_path = $uploads_path.'/wp-calls-to-action/templates/';

	if (count($template_paths)>0)
	{
		foreach ($template_paths as $name)
		{	
			include_once($extended_templates_path."$name/config.php");	
		}		
	}

	$extension_data = wp_cta_get_extension_data();
	if (isset($extension_data))
	{
		$extension_data_cats = wp_cta_get_extension_data_cats($extension_data);
	}


	$template_paths = wp_cta_get_core_template_paths();	
	//print_r($template_paths);

	//Now load all config.php files with their custom meta data
	if (count($template_paths)>0)
	{
		foreach ($template_paths as $name)
		{	
			if ($name != ".svn"){	
			include_once(WP_CTA_PATH."templates/$name/config.php"); 	
			}
		}
		
		$extension_data = wp_cta_get_extension_data();
		if (isset($extension_data))
		{
			$extension_data_cats = wp_cta_get_extension_data_cats($extension_data);
		}
	}

 /**
 * DECLARE HELPER FUNCTIONS
 */


function wp_cta_get_extension_data()
{
	global $wp_cta_data;
	//print_r($wp_cta_data);exit;
	
	
	$parent_key = 'wp-cta';
	
	$wp_cta_data[$parent_key]['settings'] = 
		array(	
			//ADD METABOX - SELECTED TEMPLATE	
			array(
				'id'  => 'selected-template',
				'label' => 'Select Template',
				'description' => "This option provides a placeholder for the selected template data.",
				'type'  => 'radio', // this is not honored. Template selection setting is handled uniquely by core.
				'default'  => 'blank-template',
				'options' => null // this is not honored. Template selection setting is handled uniquely by core.
			)
		);
	
	//IMPORT ALL EXTERNAL DATA
	$wp_cta_data = apply_filters( 'wp_cta_extension_data' , $wp_cta_data);
	
	return $wp_cta_data;
}


function wp_cta_get_core_template_paths()
{
		
	$template_path = WP_CTA_PATH."templates/" ; 
	$results = scandir($template_path);
	
	//scan through templates directory and pull in name paths
	foreach ($results as $name) {
		if ($name === '.' or $name === '..' or $name === '__MACOSX') continue;

		if (is_dir($template_path . '/' . $name)) {
			$template_paths[] = $name;
		}
	}
	
	return $template_paths;
}


function wp_cta_get_extension_template_paths()
{
	//scan through templates directory and pull in name paths
	$uploads = wp_upload_dir();
	$uploads_path = $uploads['basedir'];
	$extended_path = $uploads_path.'/wp-calls-to-action/templates/';
	$template_paths = array();
	
	if (!is_dir($extended_path))
	{
		wp_mkdir_p( $extended_path );
	}
	
	$results = scandir($extended_path);
	
		
	//scan through templates directory and pull in name paths
	foreach ($results as $name) {
		if ($name === '.' or $name === '..' or $name === '__MACOSX') continue;

		if (is_dir($extended_path . '/' . $name)) {
			$template_paths[] = $name;
		}
	}

	return $template_paths;
}


function wp_cta_get_extension_data_cats($extension_data)
{

	//print_r($extension_data);
	foreach ($extension_data as $key=>$val)
	{
		
		if ($key=='wp-cta'||!isset($val['info']['category']))
			continue;

		/* allot for older lp_data model */		
		if (isset($val['category']))
		{
			$cat_value = $val['category'];
		}
		else
		{
			if (isset($val['info']['category']))
			{
				$cat_value = $val['info']['category'];
			}
		}

		$name = str_replace(array('-','_'),' ',$cat_value);
		$name = ucwords($name);
		if (!isset($template_cats[$cat_value]))
		{						
			$template_cats[$cat_value]['count'] = 1;
			$template_cats[$cat_value]['value'] = $cat_value;
			$template_cats[$cat_value]['label'] = "$name";
		}
		else
		{			
			$template_cats[$cat_value]['count']++;
			$template_cats[$cat_value]['label'] = "$name";
			$template_cats[$cat_value]['value'] = $cat_value;
		}
	}
	//print_r($template_cats);exit;
	
	return $template_cats;
}
