<?php

define("QUICK_CACHE_ALLOWED", false); 
define("DONOTCACHEPAGE", true); 
define('DONOTCACHCEOBJECT', true); 
define('DONOTCDN', true); 

if ( file_exists ( './../../../../wp-config.php' ) )
{
	include_once ( './../../../../wp-config.php' );
	include_once ( './../../../../wp-includes/admin-bar.php' );
}
else if ( file_exists ( './../../../../../wp-config.php' ) )
{
	include_once ( './../../../../../wp-config.php' );
}
	
$debug_session = 1; 
$debug = 0; 
$debug = apply_filters('wp_cta_ab_testing_session_check',$debug);

//echo 1; exit;

if(session_id()=="") {
    // session isn't started
	//session_start();
}

//echo $_GET['permalink_name'];exit;
//echo $_SESSION[$_GET['permalink_name']];
if (isset($_SESSION[$_GET['permalink_name']]) && !$debug_session)
{
	$url = $_SESSION[$_GET['permalink_name']];
}
else
{
	if ($debug)
	{
		echo "Get Permalink Name: ".$_GET['permalink_name'] . "<br>";
	}
	
	$query = "SELECT * FROM {$table_prefix}posts WHERE post_name='".mysql_real_escape_string($_GET['permalink_name'])."' AND post_type='wp-call-to-action' LIMIT 1";
	$result = mysql_query($query);
	if (!$result){ echo $query; echo mysql_error(); exit;}
	//echo mysql_num_rows($result);

	$arr = mysql_fetch_array($result);
	$pid = $arr['ID'];
	
	$variations = get_post_meta($pid,'cta_ab_variations', true);
	$marker = get_post_meta($pid,'cta_ab_variations-marker', true);
	if (!is_numeric($marker))
		$marker = 0;
	
	
	//echo "marker$marker";
	//echo "<br>";
	//echo $variations;
	
	$variations = explode(',',$variations);
	$variations = array_filter($variations,'is_numeric');
	
	//echo "<br>";
	//echo count($variations);
	//echo "<br>";
	
	if ($variations)
	{		
		foreach ($variations as $key=>$vid)
		{

			if ($vid==0)
			{
				$variation_status = get_post_meta( $pid , 'wp_cta_ab_variation_status' , true );
			}
			else
			{
				$variation_status = get_post_meta( $pid , 'cta_ab_variation_status_'.$vid , true );
			}
			//echo "Status:".$variation_status."ID".$vid;
			//echo "<br>";
			if (!is_numeric($variation_status)||$variation_status==1)
				$live_variations[] = $vid;
		}
		
		$keys_as_values = array_flip($live_variations);
		
		//set pointer to beginning of array;
		reset($keys_as_values);
		
		//print_r($live_variations);
		
		if (!isset($live_variations[$marker]))
		{
			//echo "reset pointer!"; exit;
			//echo "<br>";
			$marker= reset($keys_as_values);
			//echo  next($live_variations);
		}
		
		//if ($marker == end($live_variations))
		//{
			//echo "reset pointer!"; 
			//echo "<br>";
			//$marker= reset($live_variations);
			//echo  next($live_variations);
		//}
		
		
		//echo key($live_variations);exit;
		$i = 0;
		if (key($keys_as_values)!=$marker)
		{
			while ((next($keys_as_values) != $marker ))
			{		
				if ($i>100)
					break;
				
				//echo "here";	
			
				//next($live_variations);
				//echo "<br>";
				//echo "key:".key($live_variations);
				//echo "<br>";
				//echo "marker:$marker <br>";
				$i++;
			}
		}

		//echo "<br>";
		//echo "Marker:".$marker;
		//echo "<br>";
		
		$variation_id = $live_variations[$marker];
		if ($debug)
		{	
			echo "first vid:$variation_id";
			echo "<br>";
		}
		//echo $variation_status;
		//echo "<br>";
		
		//echo "fire:";
		//set next marker
		//print_r($live_variations);
		//echo "<br>";
		if ($debug)
		{
			echo "premarker:".$marker;
			echo "<br>";
			//echo current($live_variations);
			//echo each($live_variations);
			//next($live_variations);
		}
		
		$marker = next($keys_as_values);
		
		if ($debug)
		{
			echo "current_marker: ".$marker;
			//echo "<br>";
			//echo next($live_variations);
			//echo "<br>";
			//echo next($live_variations);
			//echo "<br>";
			//echo "final marker: $marker";
			//echo "<br>";
		}
		//exit;
		if (!$marker)
		{
			//echo "here";exit;
			$marker = reset($keys_as_values);
		}
			
		//echo "final marker:$marker";
		//echo "<br>";
		
		update_post_meta($pid, 'cta_ab_variations-marker', $marker);
		
	}
	else
	{
		$variation_id = 0;
	}
		
	//echo "<br>";
	//echo "final vid:".$variation_id;exit;
	$url = get_permalink($pid);
	$old_params = "";
	
	foreach ($_GET as $key=>$value) 
	{	
		if ($key != "permalink_name"){
			$old_params .= "&$key=" . $value;
  		}
  	}
	
	$url = $url."?wp-cta-variation-id=".$variation_id.$old_params;
	$_SESSION[$_GET['permalink_name']] = $url;
}

if ($debug)
{
	exit;
}
//echo "<br>";
//echo $url;
setcookie('wp-cta-variation-id', $variation_id,time()+3600,"/");
//$page = wp_cta_remote_connect($url);

@header("HTTP/1.1 307 Temporary Redirect");
@header("Location: $url");

function wp_cta_get_next($array, $key) {
   $currentKey = key($array);
   while ($currentKey !== null && $currentKey != $key) {
       next($array);
       $currentKey = key($array);
   }
   return next($array);
}