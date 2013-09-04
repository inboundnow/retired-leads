jQuery(document).ready(function($) {
/* core tracking script */
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
			
			/* Setup hook replacement
			// do_action('wpl_js_hook_submit_form_pre',null);
			*/
			
			var email = "";
			var firstname = "";
			var lastname = "";			
			var tracking_obj = JSON.stringify(trackObj);
			var page_view_count = countProperties(pageviewObj);
			//console.log("view count" + page_view_count);
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
			var page_id = inbound_ajax.post_id;
			if (typeof (landing_path_info) != "undefined" && landing_path_info != null && landing_path_info != "") {	
				var lp_v = landing_path_info.variation;
			} else {
				var lp_v = null;
			}
		
			jQuery.cookie("wp_lead_email", email, { path: '/', expires: 365 });

			jQuery.ajax({
				type: 'POST',
				url: inbound_ajax.admin_url,
				data: {
					action: 'inbound_store_lead',
					emailTo: email, 
					first_name: firstname, 
					last_name: lastname,
					wp_lead_uid: wp_lead_uid,
					page_view_count: page_view_count,
					page_views: page_views,
					lp_v: lp_v,
					json: tracking_obj, // replace with page_view_obj
					// type: 'form-completion',
					raw_post_values_json : post_values_json,
					lp_id: page_id
					/* Replace with jquery hook
						do_action('wpl-lead-collection-add-ajax-data'); 
					*/
				},
				success: function(user_id){			
						//jQuery.cookie("wp_lead_id", user_id, { path: '/', expires: 365 });
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
						
						jQuery('button, input[type="button"]').css('cursor', 'default');
						jQuery('input').css('cursor', 'default');
						jQuery('body').css('cursor', 'default');
						
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
 });