<?php
if (is_admin())
{
	include_once(WP_CTA_PATH.'modules/module.metaboxes-ab-testing.php');

	add_action('init','wp_cta_ab_testing_admin_init');

	function wp_cta_ab_testing_admin_init($hook)
	{

		if (!is_admin()||!isset($_GET['post'])){
			return;
		}

		$post = get_post($_GET['post']);

		if (isset($post)&&($post->post_type=='wp-call-to-action'&&(isset($_GET['action'])&&$_GET['action']=='edit')))
		{

			$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

			$variations = get_post_meta($post->ID,'cta_ab_variations', true);

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
				update_post_meta($post->ID,'cta_ab_variations', $variations);

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
					update_post_meta( $post->ID , 'cta_ab_variation_status_'.$_GET['wp-cta-variation-id'] , '0');
				}

				do_action('wp_cta_pause_variation', $post, $_GET['wp-cta-variation-id']);
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
					update_post_meta( $post->ID , 'cta_ab_variation_status_'.$_GET['wp-cta-variation-id'] , '1');
				}

				do_action('wp_cta_play_variation', $post, $_GET['wp-cta-variation-id']);
			}

			//return;
			//echo $current_variation_id;;

			(isset($_GET['new-variation'])&&$_GET['new-variation']==1) ? $new_variation = 1 : $new_variation = 0;

			//set wysiwyg boxes to correct variation data


			//prepare for new variation creation - use A as default content if not being cloned
			if (($new_variation==1&&!isset($_GET['clone']))||isset($_GET['clone'])&&$_GET['clone']==0)
			{
				$content_area = get_post_field('post_content', $_GET['post']);
			}
			else if ($new_variation==1&&isset($_GET['clone']))
			{
				$content_area = get_post_field('content-'.$_GET['clone'], $_GET['post']);
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

			/* enqueue and localize scripts */
			wp_enqueue_style('wp-cta-ab-testing-admin-css', WP_CTA_URLPATH . 'css/admin-ab-testing.css');
			wp_enqueue_script('wp-cta-ab-testing-admin-js', WP_CTA_URLPATH . 'js/admin/admin.post-edit-ab-testing.js', array( 'jquery' ));
			wp_localize_script( 'wp-cta-ab-testing-admin-js', 'variation', array( 'pid' => $_GET['post'], 'vid' => $current_variation_id  , 'new_variation' => $new_variation  , 'variations'=> $variations  ));


		}
	}

	function wp_cta_ab_unset_variation($variations,$vid)
	{
		if(($key = array_search($vid, $variations)) !== false) {
			unset($variations[$key]);
		}

		return $variations;
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
			get_post_meta($post_id,'wp-cta-main-headline', true);
		}

		return $main_headline;
	}

	add_filter('wp_cta_edit_variation_notes','wp_cta_ab_testing_admin_prepare_notes' , 10 , 2);
	function wp_cta_ab_testing_admin_prepare_notes($variation_notes , $current_variation_id = null)
	{

		( $current_variation_id > 0 || $current_variation_id != null  ) ?  $current_variation_id :  $current_variation_id = wp_cta_ab_testing_get_current_variation_id();

		if (isset($_REQUEST['post']))
		{
			$post_id = $_REQUEST['post'];
		}
		else if (isset($_REQUEST['wp_cta_id']))
		{
			$post_id = $_REQUEST['wp_cta_id'];
		}

		if ($current_variation_id>0&&!isset($_REQUEST['new-variation'])&&!isset($_REQUEST['clone']))
		{
			$variation_notes = get_post_meta($post_id,'wp-cta-variation-notes-'.$current_variation_id, true);

		}
		else if (isset($_GET['clone'])&&$_GET['clone']>0)
		{
			$variation_notes = get_post_meta($post_id,'wp-cta-variation-notes-'.$_GET['clone'], true);
		}

		if (!$variation_notes)
		{
			$post_id = (isset($_GET['post'])) ? $_GET['post'] : '0';
			$variation_notes = get_post_meta($post_id ,'wp-cta-variation-notes', true);
		}

		return $variation_notes;
	}

	add_filter('wp_cta_selected_template_id','wp_cta_ab_testing_prepare_id');//prepare name id for hidden selected template input
	add_filter('wp_cta_display_headline_input_id','wp_cta_ab_testing_prepare_id');//prepare id for main headline in template customizer mode
	add_filter('wp_cta_display_notes_input_id','wp_cta_ab_testing_prepare_id');//prepare id for main headline in template customizer mode
	add_filter('wp_cta_custom_js_meta_key','wp_cta_ab_testing_prepare_id');
	add_filter('wp_cta_custom_css_meta_key','wp_cta_ab_testing_prepare_id');
	add_filter('wp_cta_ab_field_id','wp_cta_ab_testing_prepare_id');
	function wp_cta_ab_testing_prepare_id($id)
	{
		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

		//check if variation clone is initiated
		if (isset($_GET['new_meta_key'])){
			$current_variation_id = $_GET['new_meta_key'];
		}

		if (isset($_REQUEST['wp-cta-variation-id'])){
			$current_variation_id = $_REQUEST['wp-cta-variation-id'];
		}

		if ($current_variation_id>0)
		{
			$id = $id.'-'.$current_variation_id;
		}

		return $id;
	}

	/* prepare id for wp_editor in template customizer */
	add_filter('wp_cta_wp_editor_id','wp_cta_ab_testing_prepare_wysiwyg_editor_id');
	function wp_cta_ab_testing_prepare_wysiwyg_editor_id($id)
	{
		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

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


	add_filter('wp_cta_template_options','wp_cta_ab_testing_admin_prepare_meta_ids', 5 );
	add_filter('wp_cta_advanced_settings','wp_cta_ab_testing_admin_prepare_meta_ids', 15 );
	function wp_cta_ab_testing_admin_prepare_meta_ids( $settings )
	{
		if (isset($_REQUEST['new-variation'])&&!isset($_REQUEST['clone']))
		{
			//return $settings;
		}

		$current_variation_id = wp_cta_ab_testing_get_current_variation_id();

		if (isset($_GET['clone'])){
			$current_variation_id = $_GET['clone'];
		}

		if ($current_variation_id>0)
		{
			$post_id = $_GET['post'];

			foreach ($settings as $key=>$field)
			{
				$default = get_post_meta($post_id, $field['id'], true);

				$id = $field['id'];
				$field['id'] = $id.'-'.$current_variation_id ;

				if ($default) {
					$field['default'] = $default;
				}

				$settings[$key] = $field;
			}

			return $settings;
		}

		return $settings;
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

		return $selected_template;
	}

	/* add filter to modify thumbnail preview */
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
		if (isset($postarr['wp-cta-variation-id'])&&$postarr['wp-cta-variation-id']>0)
		{
			global $post;

			$postarr = array();
			$data = array();

			/*
			remove_action('save_post','wp_cta_save_meta',10);
			remove_action( 'save_post', 'wp_cta_save_notes_area' );
			remove_action('save_post','wp_cta_ab_testing_save_post',10);
			*/

			$postID = $_POST['post_ID'];
			if($parent_id = wp_is_post_revision($_POST['post_ID']))
			{
				$postID = $parent_id;
			}

			do_action('save_post' , $postID , $post );
		}

		if (count($data)>1){
			return $data;
		}
	}

	add_action('save_post','wp_cta_ab_testing_save_post');
	function wp_cta_ab_testing_save_post($postID)
	{
		global $post;
		unset($_POST['post_content']);

		if (  !isset($_POST['post_type']) || $_POST['post_type']!='wp-call-to-action') {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||$_POST['post_type']=='revision')
		{
			return;
		}

		if($parent_id = wp_is_post_revision($postID))
		{
			$postID = $parent_id;
		}

		$this_variation = (isset($_POST['wp-cta-variation-id'])) ? $_POST['wp-cta-variation-id'] : '0';

		$variations = get_post_meta($postID,'cta_ab_variations', true);
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


		//update_post_meta($postID,'cta_ab_variations', "");
		update_post_meta($postID,'cta_ab_variations', implode(',',$array_variations));
		//add_post_meta($postID, 'wp_cta_ab_variation_status-'.$this_variation , 1);

		/**
		print_r($array_variations);
		echo $this_variation;exit;
		/**/
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


function wp_cta_ab_key_to_letter($key) {
    $alphabet = array(  __( 'A' , 'cta' ),
						__( 'B' , 'cta' ),
						__( 'C' , 'cta' ),
						__( 'D' , 'cta' ),
						__( 'E' , 'cta' ),
						__( 'F' , 'cta' ),
						__( 'G' , 'cta' ),
						__( 'H' , 'cta' ),
						__( 'I' , 'cta' ),
						__( 'J' , 'cta' ),
						__( 'K' , 'cta' ),
						__( 'L' , 'cta' ),
						__( 'M' , 'cta' ),
						__( 'N' , 'cta' ),
						__( 'O' , 'cta' ),
						__( 'P' , 'cta' ),
						__( 'Q' , 'cta' ),
						__( 'R' , 'cta' ),
						__( 'S' , 'cta' ),
						__( 'T' , 'cta' ),
						__( 'U' , 'cta' ),
						__( 'V' , 'cta' ),
						__( 'W' , 'cta' ),
						__( 'X' , 'cta' ),
						__( 'Y' , 'cta' ),
						__( 'Z' , 'cta' )
                       );

	if (isset($alphabet[$key])){
		return $alphabet[$key];
	}
}


function wp_cta_ab_testing_get_current_variation_id()
{


		
	if (!isset($_SESSION['wp_cta_ab_test_open_variation'])&&!isset($_GET['wp-cta-variation-id']))
	{
		$current_variation_id = 0;
	}


	/* check if variation clone is initiated */
	if (isset($_GET['new_meta_key'])){
		$current_variation_id = $_GET['new_meta_key'];
	}
	
	if (isset($_GET['wp-cta-variation-id']))
	{
		$_SESSION['wp_cta_ab_test_open_variation'] = $_GET['wp-cta-variation-id'];
		$current_variation_id = $_GET['wp-cta-variation-id'];
	}

	if (isset($_GET['message'])&&$_GET['message']==1&&isset( $_SESSION['wp_cta_ab_test_open_variation'] ))
	{
		$current_variation_id = $_SESSION['wp_cta_ab_test_open_variation'];

		//echo "here:".$_SESSION['wp_cta_ab_test_open_variation'];
	}

	if (isset($_GET['ab-action'])&&$_GET['ab-action']=='delete-variation')
	{
		$current_variation_id = 0;
		$_SESSION['wp_cta_ab_test_open_variation'] = 0;
	}

	if (!isset($current_variation_id)){
		$current_variation_id = 0 ;
	}

	return $current_variation_id;
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



add_filter('wp_cta_conversion_area_pre_standardize','wp_cta_ab_testing_alter_conversion_area', 10, 2);
function wp_cta_ab_testing_alter_conversion_area($content, $post_id)
{
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
	if (!wp_cta_determine_spider())
	{
		$impressions = get_post_meta($page_id,'wp-cta-ab-variation-impressions-'.$variation_id, true);
		if (!is_numeric($impressions)) {
			$impressions = 1;
		} else {
			$impressions++;
		}

		update_post_meta($page_id,'wp-cta-ab-variation-impressions-'.$variation_id, $impressions);
	}
}

add_action('wp_cta_record_conversion','wp_cta_ab_testing_record_conversion', 10, 2);
function wp_cta_ab_testing_record_conversion($cta_id, $variation_id)
{

	if (!wp_cta_determine_spider())
	{
		$conversions = get_post_meta( $cta_id , 'wp-cta-ab-variation-conversions-'.$variation_id , true);

		if (!is_numeric($conversions)) {
			$conversions = 1;
		} else {
			$conversions++;
		}        

		update_post_meta($cta_id , 'wp-cta-ab-variation-conversions-'.$variation_id , $conversions);
	}

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
	<?PHP
}

