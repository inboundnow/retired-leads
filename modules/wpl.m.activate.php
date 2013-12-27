<?php
 
function wpleads_activate()
{
	global $wpdb;
	$blogids = ""; // define to kill error
	$multisite = 0;
	// Makes sure the plugin is defined before trying to use it
	if ( ! function_exists( 'is_plugin_active_for_network' ) )
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
 
	if ( is_plugin_active_for_network( WPL_CORE ) ) {
		if (function_exists('is_multisite') && is_multisite()) {       
				$old_blog = $wpdb->blogid;
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				$multisite = 1;        
		}
	}

		
	if (count($blogids)>1)
	{
		$count = count($blogids);
	}
	else
	{
		$count=1;
	}
	
	for ($i=0;$i<$count;$i++)
	{
		if ($multisite==1)
		{
			 switch_to_blog($blogids[$i]);
		}

		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}lead_tracking (
				id INT(40) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				lead_id INT(40) NOT NULL,
				tracking_id VARCHAR(40) NOT NULL,
				date DATETIME NOT NULL,
				data TEXT NULL,
				nature VARCHAR(25) NOT NULL,
				processed INT(40) NOT NULL
				) ";
				
		$result = mysql_query($sql);
		
		if (!$result){ echo $sql; echo mysql_error(); exit; }

		$sql = "ALTER TABLE `{$wpdb->prefix}lead_tracking`	CHANGE `tracking_id` `tracking_id` varchar(40)";
		$result = mysql_query($sql);		
		//if (!$result){ echo $sql; echo mysql_error(); exit; }
		
		$sql = "update {$wpdb->prefix}postmeta set meta_key = 'wpleads_conversion_count' where meta_key = 'wpl-lead-conversion-count'";
		$result = mysql_query($sql);
		
		$sql = "update {$wpdb->prefix}postmeta set meta_key = 'wpleads_page_view_count' where meta_key = 'wpl-lead-page-view-count'";
		$result = mysql_query($sql);
		
		$sql = "update {$wpdb->prefix}postmeta set meta_key = 'wpleads_raw_post_data' where meta_key = 'wpl-lead-raw-post-data'";
		$result = mysql_query($sql);
	}		

}

?>