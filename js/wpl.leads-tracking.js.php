<?php
	$current_page = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$post_id = wpl_url_to_postid($current_page);
	(isset($_SERVER['HTTP_REFERER'])) ? $referrer = $_SERVER['HTTP_REFERER'] : $referrer ='direct access';	
	(isset($_SERVER['REMOTE_ADDR'])) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = '0.0.0.0.0';

	//echo $post_id;exit;

	
	?>	
	<script type='text/javascript'>
	/* WP-leads */
	var form = jQuery('.wpl-track-me');

	if (form.length>0)
	{

		form.submit(function(e) { 
			form_id = jQuery(this).attr('id');
			this_form = jQuery(this);
			jQuery('button, input[type="button"]').css('cursor', 'wait');
			jQuery('input').css('cursor', 'wait');
			jQuery('body').css('cursor', 'wait');
			
			e.preventDefault();
			var email_check = 1;
			var submit_halt = 0;
			
			<?php
			do_action('wpl_js_hook_submit_form_pre',null);
			?>
			
			var email = "";
			var firstname = "";
			var lastname = "";			
			var json = JSON.stringify(trackObj);
			var page_view_count = jQuery(trackObj.items).length;
			submit_halt = 1;

			if (!email)
			{

				 jQuery(".wpl-track-me").find('input[type=text],input[type=email]').each(function() {
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
				jQuery(".wpl-track-me").find('input[type=text],input[type=email]').each(function() {
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
				jQuery(".wpl-track-me").find('input[type=text],input[type=email]').each(function() {
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

			var form_inputs = jQuery('.wpl-track-me').find('input[type=text],textarea,select');

			var post_values = {};
			form_inputs.each(function() {
				post_values[this.name] = jQuery(this).val();
			});	
			var post_values_json = JSON.stringify(post_values);
			var wp_lead_uid = jQuery.cookie("wp_lead_uid");	
			var page_views = JSON.stringify(pageviewObj);
			
			jQuery.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php') ?>',
				data: {
					action: 'inbound_store_lead',
					emailTo: email, 
					first_name: firstname, 
					last_name: lastname,
					wp_lead_uid: wp_lead_uid,
					page_view_count: page_view_count,
					page_views: page_views,
					json: json,
					nature: 'conversion',
					raw_post_values_json : post_values_json,
					lp_id: '<?php echo $post_id; ?>'<?php 
						do_action('wpl-lead-collection-add-ajax-data'); 
					?>
				},
				success: function(user_id){
						jQuery.cookie("wp_lead_id", user_id, { path: '/', expires: 365 });
						jQuery.totalStorage('wp_lead_id', user_id); 
						if (form_id)
						{
							jQuery('form').unbind('submit');
							//jQuery('.wpl-track-me form').submit();
							jQuery('#'+form_id).submit();
						}
						else
						{
							this_form.unbind('submit');
							this_form.submit();
						}
						jQuery.totalStorage.deleteItem('page_views'); // remove pageviews
						jQuery.totalStorage.deleteItem('tracking_events'); // remove events
						//jQuery.totalStorage.deleteItem('cta_clicks'); // remove cta
					   },
				error: function(MLHttpRequest, textStatus, errorThrown){
						//alert(MLHttpRequest+' '+errorThrown+' '+textStatus);
						//die();
						submit_halt =0;
					}

			});
			
		});
		
	}

</script>