<?php

add_filter('wp_cta_js_hook_submit_form_success','wp_cta_lead_collection_js');

function wp_cta_lead_collection_js()
{	
	$current_page = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$post_id = wp_cta_url_to_postid($current_page);
	(isset($_SERVER['HTTP_REFERER'])) ? $referrer = $_SERVER['HTTP_REFERER'] : $referrer ='direct access';	
	(isset($_SERVER['REMOTE_ADDR'])) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = '0.0.0.0.0';

	do_action('wp-cta-lead-collection-add-js-pre'); 
	
	?>	
	var email = jQuery(".wp-cta-email-value input").val();
	var firstname = jQuery(".wp-cta-first-name-value input").val();
	var lastname = jQuery(".wp-cta-last-name-value input").val();
	submit_halt = 1;
	
	//alert('1');
	if (!email)
	{
		 jQuery("#wp_cta_container_form input[type=text]").each(function() {
			if (this.value)
			{
				if (jQuery(this).attr("name").toLowerCase().indexOf('email')>-1) {
					email = this.value;
				}
				else if(jQuery(this).attr("name").toLowerCase().indexOf('name')>-1&&!firstname) {
					 firstname = this.value;
				}
				else if (jQuery(this).attr("name").toLowerCase().indexOf('name')>-1) {
					 lastname = this.value;
				}
			}
		});
	}
	else
	{		
		if (!lastname&&jQuery("input").eq(1).val().indexOf("@") === -1)
		{
			lastname = jQuery("input").eq(1).val();
		}
	}
	
	if (!email)
	{
		jQuery("#wp_cta_container_form input[type=text]").each(function() {
			if (jQuery(this).closest('li').children('label').length>0)
			{
				if (jQuery(this).closest('li').children('label').html().toLowerCase().indexOf('email')>-1) 
				{
					email = this.value;
				}
				else if (jQuery(this).closest('li').children('label').html().toLowerCase().indexOf('name')>-1&&!firstname) {
					firstname = this.value;
				}
				else if (jQuery(this).closest('li').children('label').html().toLowerCase().indexOf('name')>-1) {
					lastname = this.value;
				}
			}
		});
	}
	
	if (!email)
	{
		jQuery("#wp_cta_container_form input[type=text]").each(function() {
			if (jQuery(this).closest('div').children('label').length>0)
			{
				if (jQuery(this).closest('div').children('label').html().toLowerCase().indexOf('email')>-1) 
				{
					email = this.value;
				}
				else if (jQuery(this).closest('div').children('label').html().toLowerCase().indexOf('name')>-1&&!firstname) {
					firstname = this.value;
				}
				else if (jQuery(this).closest('div').children('label').html().toLowerCase().indexOf('name')>-1) {
					lastname = this.value;
				}
			}
		});
	}
	
	
	if (!lastname&&firstname)
	{
		var parts = firstname.split(" ");
		firstname = parts[0];
		lastname = parts[1];
	}
	
	var form_inputs = jQuery('#wp_cta_container_form form').find('input[type=text],textarea,select');

    var post_values = {};
    form_inputs.each(function() {
        post_values[this.name] = jQuery(this).val();
    });	
    var post_values_json = JSON.stringify(post_values);
	var wp_lead_uid = jQuery.cookie("wp_lead_uid");
	jQuery.cookie("wp_lead_email", email, { path: '/', expires: 365 });
	var current_variation = <?php $variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0'; echo  $variation ;?>;	
	jQuery.ajax({
		type: 'POST',
		url: '<?php echo admin_url('admin-ajax.php') ?>',
		data: {
			action: 'wp_cta_store_lead',
			emailTo: email, 
			first_name: firstname, 
			last_name: lastname,
			wp_lead_uid: wp_lead_uid,
			raw_post_values_json : post_values_json,
			wp_cta_v: current_variation,
			wp_cta_id: '<?php echo $post_id; ?>'<?php 
				do_action('wp-cta-lead-collection-add-ajax-data'); 
			?>
		},
		success: function(user_id){
				if (form_id)
				{
					jQuery('form').unbind('submit');
					jQuery('#wp_cta_container_form form').submit();
					//jQuery('#'+form_id+':input[type=submit]').click();
				}
				else
				{
					this_form.unbind('submit');
					this_form.submit();
				}
			   },
		error: function(MLHttpRequest, textStatus, errorThrown){
				//alert(MLHttpRequest+' '+errorThrown+' '+textStatus);
				//die();
				submit_halt =0;
			}

	});
	<?php
}

if (!post_type_exists('wp-lead'))
{
	add_action('init', 'wp_cta_wpleads_register');
	function wp_cta_wpleads_register() {
		//echo $slug;exit;
		$labels = array(
			'name' => _x('Leads', 'post type general name'),
			'singular_name' => _x('Lead', 'post type singular name'),
			'add_new' => _x('Add New', 'Lead'),
			'add_new_item' => __('Add New Lead'),
			'edit_item' => __('Edit Lead'),
			'new_item' => __('New Leads'),
			'view_item' => __('View Leads'),
			'search_items' => __('Search Leads'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			//'show_ui' => true,
			'show_ui' => false,
			'query_var' => true,
			//'menu_icon' => WPL_URL . '/images/leads.png',
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('custom-fields','thumbnail')
		  );

		register_post_type( 'wp-lead' , $args );
		//flush_rewrite_rules( false );

	}
}