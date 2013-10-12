<?php
if (is_admin())
{
	include_once(WP_CTA_PATH.'modules/module.metaboxes-ab-testing.php');
	
	add_action('init','wp_cta_ab_testing_admin_init');

	function wp_cta_ab_testing_admin_init($hook)
	{	
		
		if (!is_admin()||!isset($_GET['post']))
			return;
			
		$post = get_post($_GET['post']);
		
		if (isset($post)&&($post->post_type=='wp-call-to-action'&&(isset($_GET['action'])&&$_GET['action']=='edit')))
		{
			
			$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
			//echo $current_variation_id;
			$variations = get_post_meta($post->ID,'wp-cta-ab-variations', true);

			//echo $variations;exit;
			//remove landing page's main save_post action
			if ($current_variation_id>0)
			{		
				remove_action('save_post','wp_cta_save_meta',10);					
			}

			//check for delete command
			if (isset($_GET['ab-action'])&&$_GET['ab-action']=='delete-variation')
			{
				$array_variations = explode(',',$variations);
				$array_variations = wp_cta_ab_unset_variation($array_variations,$_GET['wp-cta-variation-id']);
				$variations = implode(',' , $array_variations);
				update_post_meta($post->ID,'wp-cta-ab-variations', $variations);
				
				$suffix = '-'.$_GET['wp-cta-variation-id'];
				$len = strlen($suffix);
				
				//delete each meta value associated with variation
				global $wpdb;
				$data   =   array();
				$wpdb->query("
					SELECT `meta_key`, `meta_value`
					FROM $wpdb->postmeta
					WHERE `post_id` = ".$_GET['post']."
				");
				
				foreach($wpdb->last_result as $k => $v){
					$data[$v->meta_key] =   $v->meta_value;
				};
				//echo $len;exit;
				foreach ($data as $key=>$value)
				{
					if (substr($key,-$len)==$suffix)
					{
						delete_post_meta($_GET['post'], $key, $value);
					}
				}								
				
				$current_variation_id = 0;	
				$_SESSION['wp_cta_ab_test_open_variation'] = 0;						
			}

			//check for pause command
			if (isset($_GET['ab-action'])&&$_GET['ab-action']=='pause-variation')
			{						
				if ($_GET['wp-cta-variation-id']==0)
				{
					update_post_meta( $post->ID , 'wp_cta_ab_variation_status' , '0' );
				}
				else
				{
					update_post_meta( $post->ID , 'wp_cta_ab_variation_status-'.$_GET['wp-cta-variation-id'] , '0');
				}		
			}
			
			//check for pause command
			if (isset($_GET['ab-action'])&&$_GET['ab-action']=='play-variation')
			{						
				if ($_GET['wp-cta-variation-id']==0)
				{
					update_post_meta( $post->ID , 'wp_cta_ab_variation_status' , '1' );
				}
				else
				{
					update_post_meta( $post->ID , 'wp_cta_ab_variation_status-'.$_GET['wp-cta-variation-id'] , '1');
				}		
			}
			
			//return;
			//echo $current_variation_id;;
			
			(isset($_GET['new-variation'])&&$_GET['new-variation']==1) ? $new_variation = 1 : $new_variation = 0;				
	
			//set wysiwyg boxes to correct variation data
			
			$content_area = wp_cta_content_area(null,null,true);
			$conversion_area = wp_cta_conversion_area(null,null,true,false);
			
			//prepare for new variation creation - use A as default content if not being cloned
			if (($new_variation==1&&!isset($_GET['clone']))||isset($_GET['clone'])&&$_GET['clone']==0)
			{
				//echo 1; exit;
				$content_area = get_post_field('post_content', $_GET['post']);
				$conversion_area = get_post_meta($_GET['post'],'wp-cta-conversion-area', true);
			}
			else if ($new_variation==1&&isset($_GET['clone']))
			{

				$content_area = get_post_field('content-'.$_GET['clone'], $_GET['post']);
				$conversion_area = get_post_meta($_GET['post'],'wp-call-to-action-myeditor-'.$_GET['clone'], true);
			}

			//if new variation and cloning then programatically prepare the next variation id
			if($new_variation==1&&isset($_GET['clone']))
			{
				$array_variations = explode(',',$variations);
				sort($array_variations,SORT_NUMERIC);

				$lid = end($array_variations);
				$current_variation_id = $lid+1;
				
				$_SESSION['wp_cta_ab_test_open_variation'] = $current_variation_id;
			}
			//echo $current_variation_id;exit;
			//enqueue and localize scripts
			wp_enqueue_style('wp-cta-ab-testing-admin-css', WP_CTA_URLPATH . 'css/admin-ab-testing.css');
			wp_enqueue_script('wp-cta-ab-testing-admin-js', WP_CTA_URLPATH . 'js/admin/admin.post-edit-ab-testing.js', array( 'jquery' ));
			wp_localize_script( 'wp-cta-ab-testing-admin-js', 'variation', array( 'pid' => $_GET['post'], 'vid' => $current_variation_id  , 'new_variation' => $new_variation  , 'variations'=> $variations  , 'conversion_area' => $conversion_area  , 'content_area' => $content_area  ));
		
			
		}
	}
	
	
	function wp_cta_ab_testing_force_default_editor() {
		//allowed: tinymce, html, test
		return 'html';
	}
	
	
	add_filter('wp_cta_edit_main_headline','wp_cta_ab_testing_admin_prepare_headline');
	function wp_cta_ab_testing_admin_prepare_headline($main_headline)
	{			
		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
		
		if (isset($_REQUEST['post']))
		{
			$post_id = $_REQUEST['post'];
		}
		else if (isset($_REQUEST['wp_cta_id']))
		{
			$post_id = $_REQUEST['wp_cta_id'];
		}
		
		//return "hello";
		
		if ($current_variation_id>0&&!isset($_REQUEST['new-variation'])&&!isset($_REQUEST['clone']))
		{
			$main_headline = get_post_meta($post_id,'wp-cta-main-headline-'.$current_variation_id, true);
		}
		else if (isset($_GET['clone'])&&$_GET['clone']>0)
		{
			$main_headline = get_post_meta($post_id,'wp-cta-main-headline-'.$_GET['clone'], true);
		}
		
		if (!$main_headline)
		{
			//echo 1;exit;
			get_post_meta($post_id,'wp-cta-main-headline', true);
		}
		
		return $main_headline;
	}

	add_filter('wp_cta_edit_varaition_notes','wp_cta_ab_testing_admin_prepare_notes');
	function wp_cta_ab_testing_admin_prepare_notes($varaition_notes)
	{			
		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
		
		if (isset($_REQUEST['post']))
		{
			$post_id = $_REQUEST['post'];
		}
		else if (isset($_REQUEST['wp_cta_id']))
		{
			$post_id = $_REQUEST['wp_cta_id'];
		}
		
		//return "hello";
		
		if ($current_variation_id>0&&!isset($_REQUEST['new-variation'])&&!isset($_REQUEST['clone']))
		{
			$varaition_notes = get_post_meta($post_id,'wp-cta-variation-notes-'.$current_variation_id, true);
		}
		else if (isset($_GET['clone'])&&$_GET['clone']>0)
		{
			$varaition_notes = get_post_meta($post_id,'wp-cta-variation-notes-'.$_GET['clone'], true);
		}
		
		if (!$varaition_notes)
		{
			$post_id = (isset($_GET['post'])) ? $_GET['post'] : '0';
			get_post_meta($post_id ,'wp-cta-variation-notes', true);
		}
		
		return $varaition_notes;
	}	
	
	add_filter('wp_cta_selected_template_id','wp_cta_ab_testing_prepare_id');//prepare name id for hidden selected template input
	add_filter('wp_cta_display_headline_input_id','wp_cta_ab_testing_prepare_id');//prepare id for main headline in template customizer mode
	add_filter('wp_cta_display_notes_input_id','wp_cta_ab_testing_prepare_id');//prepare id for main headline in template customizer mode
	function wp_cta_ab_testing_prepare_id($id)
	{	
		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
		
		//check if variation clone is initiated
		if (isset($_GET['new_meta_key']))
			$current_variation_id = $_GET['new_meta_key'];
		
		if ($current_variation_id>0)
		{
			$id = $id.'-'.$current_variation_id;
		}
		
		return $id;
	}
		
	//prepare id for wp_editor in template customizer
	add_filter('wp_cta_wp_editor_id','wp_cta_ab_testing_prepare_wysiwyg_editor_id');
	function wp_cta_ab_testing_prepare_wysiwyg_editor_id($id)
	{		
		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
		//echo $current_variation_id;exit;
		if ($current_variation_id>0)
		{
			switch ($id) {
				case "wp_content":
					$id = 'content-'.$current_variation_id;
					break;
				case "wp-cta-conversion-area":
					$id = 'wp-call-to-action-myeditor-'.$current_variation_id;
					break;
				default:
					$id = $id.'-'.$current_variation_id;
			}
			
		}		
		
		return $id;
	}
	
	
	add_filter('wp_cta_show_metabox','wp_cta_ab_testing_admin_prepare_meta_ids', 5, 2);
	function wp_cta_ab_testing_admin_prepare_meta_ids($wp_cta_custom_fields, $main_key)
	{	
		if (isset($_REQUEST['new-variation'])&&!isset($_REQUEST['clone']))
		{
			return $wp_cta_custom_fields;
		}

		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

		if (isset($_GET['clone']))
			$current_variation_id = $_GET['clone'];
		
		if ($current_variation_id>0)
		{
			$post_id = $_GET['post'];
			foreach ($wp_cta_custom_fields as $key=>$field)
			{
				$default = get_post_meta($post_id, $field['id'], true);
				//echo $post_id.'-'.$field['id'].":".$default;
				//echo "<br>";
				$id = $field['id'];
				$field['id'] = $id.'-'.$current_variation_id ;
				$field['default'] = $default;						
				
				$wp_cta_custom_fields[$key] = $field;
			}
			return $wp_cta_custom_fields;
		}				
		
		//print_r($wp_cta_custom_fields);exit;
		return $wp_cta_custom_fields;
	}
	
	add_filter('wp_cta_variation_selected_template','wp_cta_ab_testing_variation_selected_template', 10, 2);
	function wp_cta_ab_testing_variation_selected_template($selected_template, $post)
	{	
		if (isset($_GET['new-variation']))
			return $selected_template;

		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

		if ($current_variation_id>0)
		{
			$selected_template = get_post_meta( $post->ID , 'wp-cta-selected-template-'.$current_variation_id , true);
		}				
		
		//print_r($wp_cta_custom_fields);exit;
		return $selected_template;
	}
	
	//add filter to modify thumbnail preview
	add_filter('wp_cta_live_screenshot_url', 'wp_cta_ab_testing_prepare_screenshot');
	function wp_cta_ab_testing_prepare_screenshot($link)
	{		
		$variation_id = wp_cta_ab_testing_get_current_variation_id();	
		$link = $link."?wp-cta-variation-id=".$variation_id;
		return $link;
	}
	
	
	
	add_filter("post_type_link", "wp_cta_ab_append_variation_id_to_adminbar_link", 10,2);			
	function wp_cta_ab_append_variation_id_to_adminbar_link($link, $post)
	{		
		if( $post->post_type == 'wp-call-to-action' ) 
		{ 
			$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

			if ($current_variation_id>0)
				$link = $link."?wp-cta-variation-id=".$current_variation_id;
		}

		return $link;
	}

	if(!defined('AUTOSAVE_INTERVAL')) {
    define('AUTOSAVE_INTERVAL', 86400);
	}

    add_filter('wp_insert_post_data','wp_cta_ab_testing_wp_insert_post_data',10,2);			
	function wp_cta_ab_testing_wp_insert_post_data($data,$postarr)
	{	
		
		//exit;
		//$variation_id = wp_cta_ab_testing_get_current_variation_id();		
		//echo $variation_id;exit;
		if (isset($postarr['wp-cta-variation-id'])&&$postarr['wp-cta-variation-id']>0)
		{		
			$postarr = array();
			$data = array();

			remove_action('save_post','wp_cta_save_meta',10);
			remove_action('save_post','wp_cta_ab_testing_save_post',10);

			$postID = $_POST['post_ID'];
			if($parent_id = wp_is_post_revision($_POST['post_ID']))
			{
				$postID = $parent_id;
			}
		
			wp_cta_ab_testing_save_post($postID);
			
		}
		else
		{			
		}
		
		if (count($data)>1)
			return $data;
	} 
	
	add_action('save_post','wp_cta_ab_testing_save_post');
	function wp_cta_ab_testing_save_post($postID)
	{
		global $post;
		unset($_POST['post_content']);
		$var_final = (isset($_POST['wp-cta-variation-id'])) ? $_POST['wp-cta-variation-id'] : '0';
		//echo $var_final;exit;
		if (  isset($_POST['post_type']) && $_POST['post_type']=='wp-call-to-action')
		{
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||$_POST['post_type']=='revision')
			{
				return;
			}

			if($parent_id = wp_is_post_revision($postID))
			{
				$postID = $parent_id;
			}
		

			$this_variation = $var_final;					
			//echo $this_variation;exit;
			
			//first add to varation list if not present.
			$variations = get_post_meta($postID,'wp-cta-ab-variations', true);
			if ($variations)
			{
				$array_variations = explode(',',$variations);
				if (!in_array($this_variation,$array_variations))
				{
					$array_variations[] = $this_variation;
				}
			}
			else
			{
				if  ($this_variation>0)
				{
					$array_variations[] = 0;
					$array_variations[] = $this_variation;
				}
				else
				{
					$array_variations[] = $this_variation;
				}
			}
		
			//print_r($array_variations);exit;
			//update_post_meta($postID,'wp-cta-ab-variations', "");
			update_post_meta($postID,'wp-cta-ab-variations', implode(',',$array_variations));					
			//add_post_meta($postID, 'wp_cta_ab_variation_status-'.$this_variation , 1);
			
			//echo $this_variation;exit;
			if ($this_variation==0)
			{
				return;
			}
			//echo $this_variation;exit;
			//print_r($_POST);

			//next alter all custom fields to store correct varation and create custom fields for special inputs
			$ignore_list = array('post_status','post_type','tax_input','post_author','user_ID','post_ID','catslist','post_title','samplepermalinknonce',
			'autosavenonce','action','autosave','mm','jj','aa','hh','mn','ss','_wp_http_referer','wp-cta-variation-id','_wpnonce','originalaction','original_post_status',
			'referredby','_wp_original_http_referer','meta-box-order-nonce','closedpostboxesnonce','hidden_post_status','hidden_post_password','hidden_post_visibility','visibility',
			'post_password','hidden_mm','cur_mm','hidden_jj','cur_jj','hidden_aa','cur_aa','hidden_hh','cur_hh','hidden_mn','cur_mn','original_publish','save','newwp_call_to_action_category','newwp_call_to_action_category_parent',
			'_ajax_nonce-add-wp_call_to_action_category','wp_cta_custom_fields_nonce','wp-cta-selected-template','post_mime_type','ID','comment_status','ping_status');
			
			// Disable variation meta rewrite with this array
			$universal_settings = array('wp_cta_behavorial_targeting', 'wp_cta_bt_value');
			
			//$special_list = array('content','post-content');
			//print_r($_POST);exit;
			//echo $this_variation;exit;
			foreach ($_POST as $key=>$value)
			{
				if (!in_array($key,$ignore_list)&&!strstr($key,'nonce'))
				{
					if (!strstr($key,"-{$this_variation}") && !strstr($key,"wp_cta_global") && !in_array($key, $universal_settings))
					{
						$new_array[$key.'-'.$this_variation] = $value;
					}
					else
					{
						//echo $key." : -{$this_variation}<br>";
						$new_array[$key] = $value;
					}
					
				}
			}
			
			//echo $postID;
			//print_r($new_array);exit;
			
			foreach($new_array as $key => $val)
			{						
				$old = get_post_meta($postID, $key, true);				
				$new = $val;	
				//echo "$key  : $old v. $new <br>";
				//if (isset($new) && $new != $old ) {
					update_post_meta($postID, $key, $new);
				//} elseif ('' == $new && $old) {
					//delete_post_meta($postID, $key, $old);
				//}							
			}								
			
		}
	}

}

//PERFORM FRONT-END ONLY ACTIONS
else
{

	//prepare customizer meta data for ab varations
	add_filter('wp_cta_get_value','wp_cta_ab_testing_prepare_variation_meta', 1 , 4);			
	function wp_cta_ab_testing_prepare_variation_meta($return, $post, $key, $id)
	{
		if (isset($_REQUEST['wp-cta-variation-id'])||isset($_COOKIE['wp-cta-variation-id']))
		{						
			(isset($_REQUEST['wp-cta-variation-id'])) ? $variation_id = $_REQUEST['wp-cta-variation-id'] : $variation_id = $_COOKIE['wp-cta-variation-id'];
			if ($variation_id>0)
					return get_post_meta($post->ID, $key.'-'.$id. '-' .$variation_id , true);
			else
				return $return;
		}
		else
		{
			return $return;
		}
	}

	//prepare customizer, admin, and preview links for variations
	add_filter('wp_cta_customizer_customizer_link', 'wp_cta_ab_append_variation_id_to_link');		 	
	add_filter('wp_cta_customizer_admin_bar_link', 'wp_cta_ab_append_variation_id_to_link');	
	add_filter('wp_cta_customizer_preview_link','wp_cta_ab_append_variation_id_to_link');
	
	function wp_cta_ab_append_variation_id_to_link($link)
	{		

		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

		if ($current_variation_id>0)
			$link = $link."&wp-cta-variation-id=".$current_variation_id;

		return $link;
	}
	
}

//PERFORM ACTIONS REQUIRED ON BOTH FRONT AND BACKEND

//ready content area for displaying ab variations
add_filter('wp_cta_content_area','wp_cta_ab_testing_prepare_content_area' , 10 , 2 );
function wp_cta_ab_testing_prepare_content_area($content, $post=null)
{				
	$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
	if (isset($post))
	{
		$post_id = $post->ID;
	}
	else if (isset($_REQUEST['post']))
	{
		$post_id = $_REQUEST['post'];
	}
	else if (isset($_REQUEST['wp_cta_id']))
	{
		$post_id = $_REQUEST['wp_cta_id'];
	}
	
	if ($current_variation_id>0)
		$content = get_post_meta($post_id,'content-'.$current_variation_id, true);				
	
	//echo "$current_variation_id : $content";exit;
	return $content;
}

//ready conversion area for displaying ab variations
add_filter('wp_cta_conversion_area','wp_cta_ab_testing_prepare_conversion_area' , 10 , 2 );
function wp_cta_ab_testing_prepare_conversion_area($content,$post=null)
{				
	$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
	
	if (isset($post))
	{
		$post_id = $post->ID;
	}
	else if (isset($_REQUEST['post']))
	{
		$post_id = $_REQUEST['post'];
	}
	else if (isset($_REQUEST['wp_cta_id']))
	{
		$post_id = $_REQUEST['wp_cta_id'];
	}
	
	if ($current_variation_id>0)
		$content = get_post_meta($post_id,'wp-call-to-action-myeditor-'.$current_variation_id, true);				
	//echo $content;exit;
	return $content;
}

//ready conversion area for displaying ab variations
add_filter('wp_cta_conversion_area_position','wp_cta_ab_testing_wp_cta_conversion_area_position' , 10 , 2 );
function wp_cta_ab_testing_wp_cta_conversion_area_position($position, $post = null, $key = 'default')
{				

	$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
	
	if (isset($post))
	{
		$post_id = $post->ID;
	}
	else if (isset($_REQUEST['post']))
	{
		$post_id = $_REQUEST['post'];
	}
	else if (isset($_REQUEST['wp_cta_id']))
	{
		$post_id = $_REQUEST['wp_cta_id'];
	}
	
	if ($current_variation_id>0)
		$position = get_post_meta($post->ID, "{$key}-conversion-area-placement-".$current_variation_id, true);

	return $position;
}


add_filter('wp_cta_main_headline','wp_cta_ab_testing_prepare_headline');
function wp_cta_ab_testing_prepare_headline($main_headline)
{	
	
	$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

	if (isset($_REQUEST['post']))
	{
		$post_id = $_REQUEST['post'];
	}
	else if (isset($_REQUEST['wp_cta_id']))
	{
		$post_id = $_REQUEST['wp_cta_id'];
	}
	
	if ($current_variation_id>0)
		$main_headline = get_post_meta($post_id,'wp-cta-main-headline-'.$current_variation_id, true);

	if (!$main_headline)
	{
		get_post_meta($post_id,'wp-cta-main-headline', true);
	}
	

	return $main_headline;
}

add_action('init','wp_cta_ab_testing_add_rewrite_rules');
function wp_cta_ab_testing_add_rewrite_rules()
{
	$this_path = WP_CTA_PATH;
	$this_path = explode('wp-content',$this_path);
	$this_path = "wp-content".$this_path[1];

	$slug = get_option( 'wp-cta-main-wp-call-to-action-permalink-prefix', 'cta' );
	//echo $slug;exit;
	add_rewrite_rule("$slug/([^/]*)?", $this_path."modules/module.redirect-ab-testing.php?permalink_name=$1 ",'top');
	add_rewrite_rule("wp-call-to-action=([^/]*)?", $this_path.'modules/module.redirect-ab-testing.php?permalink_name=$1','top');
	
	add_filter('mod_rewrite_rules', 'wp_cta_ab_testing_modify_rules',1);
	function wp_cta_ab_testing_modify_rules($rules)
	{
		if (!stristr($rules,'RewriteCond %{QUERY_STRING} !wp-cta-variation-id'))
		{
			$rules_array = preg_split ('/$\R?^/m', $rules);
			if (count($rules_array)<3)
			{
				$rules_array = explode("\n", $rules);
				$rules_array = array_filter($rules_array);
			}
			
			//print_r($rules_array);exit;
			
			$this_path = WP_CTA_PATH;
			$this_path = explode('wp-content',$this_path);
			$this_path = "wp-content".$this_path[1];				
			$slug = get_option( 'wp-cta-main-wp-call-to-action-permalink-prefix', 'cta' );
			
			$i = 0;
			foreach ($rules_array as $key=>$val)
			{
				
				if (stristr($val,"RewriteRule ^{$slug}/([^/]*)? "))
				{
					$new_val = "RewriteCond %{QUERY_STRING} !wp-cta-variation-id";
					$rules_array[$i] = $new_val;
					$i++;
					$rules_array[$i] = $val;
					$i++;
				}
				else
				{
					$rules_array[$i] = $val;
					$i++;
				}
			}
		
			$rules = implode("\r\n", $rules_array);
		}
		
		return $rules;
	}
	
}


add_filter('wp_cta_selected_template','wp_cta_ab_testing_get_selected_template');//get correct selected template for each variation
function wp_cta_ab_testing_get_selected_template($template)
{			
	global $post;
	
	$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

	if ($current_variation_id>0)
	{
		
		$new_template =  get_post_meta($post->ID, 'wp-cta-selected-template-'.$current_variation_id, true);
		if ($new_template)
			$template = $new_template;	
	}
	
	return $template;
}

//prepare custom js and css for 
add_filter('wp-cta-custom-js-name','wp_cta_ab_testing_prepare_name');
add_filter('wp-cta-custom-css-name','wp_cta_ab_testing_prepare_name');
function wp_cta_ab_testing_prepare_name($id)
{	
	$current_variation_id = wp_cta_ab_testing_get_current_variation_id();
	
	if ($current_variation_id>0)
	{
		$id = $id.'-'.$current_variation_id;
	}
	
	return $id;
}

add_action('wp_ajax_wp_cta_ab_testing_prepare_variation', 'wp_cta_ab_testing_prepare_variation_callback');
add_action('wp_ajax_nopriv_wp_cta_ab_testing_prepare_variation', 'wp_cta_ab_testing_prepare_variation_callback');

function wp_cta_ab_testing_prepare_variation_callback()
{	
	if (!wp_cta_determine_spider())
	{	
		//echo "hello";
		//PRINT trim($_POST['current_url']);
		$page_id = wp_cta_url_to_postid( trim($_POST['current_url']) );	
		//echo $page_id;
		$variations = get_post_meta($page_id,'wp-cta-ab-variations', true);
		$marker = get_post_meta($page_id,'wp-cta-ab-variations-marker', true);
		if (!is_numeric($marker))
			$marker = 0;
		
		//echo "marker$marker";
		
		if ($variations)
		{
			//echo $variations;
			$variations = explode(',',$variations);
			//print_r($variations);
			
			$variation_id = $variations[$marker];

			$marker++;
			
			if ($marker>=count($variations))
			{
				//echo "here";
				$marker = 0;
			}		
			
			update_post_meta($page_id, 'wp-cta-ab-variations-marker', $marker);
			
			echo $variation_id;					
			die();
		}
		
	}			
}
		
add_filter('wp_cta_conversion_area_pre_standardize','wp_cta_ab_testing_alter_conversion_area', 10, 2);
function wp_cta_ab_testing_alter_conversion_area($content, $post_id)
{
	//echo "here;";exit;

	$variation_id = wp_cta_ab_testing_get_current_variation_id();

	if ($variation_id>0)
	{
		$content = do_shortcode(get_post_meta($post_id,'wp-call-to-action-myeditor-'.$variation_id, true));
	}
	
	return $content;
}
//echo 1; exit;
add_filter('the_content','wp_cta_ab_testing_alter_content_area', 10, 2);
add_filter('get_the_content','wp_cta_ab_testing_alter_content_area', 10, 2);
function wp_cta_ab_testing_alter_content_area($content)
{
	global $post;

	$variation_id = wp_cta_ab_testing_get_current_variation_id();

	if ($variation_id>0) 
	{
		//echo get_post_meta($post->ID,'content-'.$variation_id, true);exit;
		$content = do_shortcode(get_post_meta($post->ID,'content-'.$variation_id, true));
	}
	
	return $content;
}

add_filter('the_title','wp_cta_ab_testing_alter_title_area', 10, 2);
add_filter('get_the_title','wp_cta_ab_testing_alter_title_area', 10, 2);
function wp_cta_ab_testing_alter_title_area($content)
{
	global $post;

	$variation_id = wp_cta_ab_testing_get_current_variation_id();
		
	if ($variation_id>0)
	{						
		if (isset($post))
			$post_id = $post->ID;
		else if (isset($_REQUEST['post_id']))
			$post_id =$_REQUEST['post_id'];
		
		//echo $post_id;exit;
		$content = do_shortcode(get_post_meta($post_id,'wp-cta-main-headline-'.$variation_id, true));
	}
	
	return $content;
}
	

add_action('wp_cta_record_impression','wp_cta_ab_testing_record_impression', 10, 2);
function wp_cta_ab_testing_record_impression($page_id, $variation_id=0)
{
	$impressions = get_post_meta($page_id,'wp-cta-ab-variation-impressions-'.$variation_id, true);
	if (!is_numeric($impressions))
		$impressions = 1;
	else
		$impressions++;
		
	update_post_meta($page_id,'wp-cta-ab-variation-impressions-'.$variation_id, $impressions);
}

add_action('wp_cta_record_conversion','wp_cta_ab_testing_record_conversion', 10, 2);
function wp_cta_ab_testing_record_conversion($page_id, $variation_id)
{
	$conversions = get_post_meta($page_id,'wp-cta-ab-variation-conversions-'.$variation_id, true);

	if (!is_numeric($conversions))
		$conversions = 1;
	else
		$conversions++;
		
	update_post_meta($page_id,'wp-cta-ab-variation-conversions-'.$variation_id, $conversions);
}

	

add_action('wp_cta_launch_customizer_pre','wp_cta_ab_testing_customizer_enqueue');
function wp_cta_ab_testing_customizer_enqueue($post)
{
	//echo 1; exit;
	$permalink = get_permalink( $post->ID );					
	$randomstring = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

	wp_enqueue_script( 'wp_cta_ab_testing_customizer_js', WP_CTA_URLPATH . 'js/customizer.ab-testing.js', array( 'jquery' ) );
	wp_localize_script( 'wp_cta_ab_testing_customizer_js', 'cta_ab_customizer', array( 'wp_cta_id' => $post->ID ,'permalink' => $permalink , 'randomstring' => $randomstring));
	wp_enqueue_style('wp_cta_ab_testing_customizer_css', WP_CTA_URLPATH . 'css/customizer-ab-testing.css');	
}

add_action('wp_cta_frontend_editor_screen_pre','wp_cta_ab_testing_frontend_editor_screen_pre');		
function wp_cta_ab_testing_frontend_editor_screen_pre($post)
{
	$wp_cta_variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';
	$letter = wp_cta_ab_key_to_letter($wp_cta_variation);
	echo '<div id="current_variation_id">'.$wp_cta_variation.'</div>';
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function ($) {	
		//append letter
		var letterexists = jQuery(".variation-letter-top").length;
		console.log(letterexists);
		if (letterexists === 0){
		jQuery('#wp-cta-frontend-options-container h1:first').prepend('<span class="variation-letter-top"><?php echo $letter; ?></span>');
		}
	});
	</script>
<?php } 

/*-------------------------------------------------------WORKSPACE-------------------------------------------------------*/
//print all global fields for post
/*
global $wpdb;
$data   =   array();
$wpdb->query("
	SELECT `meta_key`, `meta_value`
	FROM $wpdb->postmeta
	WHERE `post_id` = ".$_GET['post']."
");
foreach($wpdb->last_result as $k => $v){
	$data[$v->meta_key] =   $v->meta_value;
};
if (isset($_GET['post']))
{
	print_r( $data);
} 
*/

?>
