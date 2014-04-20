function wp_cta_load_variation( cta_id , vid , disable_ajax )
{
	/* Preload wp_cta_loaded storage object into variable */
	var loaded_ctas = {};
	var loaded_local_cta = jQuery.totalStorage('wp_cta_loaded');
	if (loaded_local_cta != null) {
		var loaded_ctas = JSON.parse(localStorage.getItem('wp_cta_loaded'));
	}

	/* if variation is pre-defined then immediately load variation*/
	if ( typeof vid != 'undefined' ) {

		console.log('CTA '+cta_id+' loads variation:' + vid);
		jQuery('.wp_cta_'+cta_id+'_variation_'+vid).show();

		/* record impression */
		loaded_ctas[cta_id] = vid;
		jQuery.ajax({
			type: 'POST',
			url: cta_variation.admin_url,
			data: {
				action: 'wp_cta_record_impressions',
				ctas: JSON.stringify(loaded_ctas)
			},
			success: function(user_id){
					console.log('CTA Impressions Recorded');
				   },
			error: function(MLHttpRequest, textStatus, errorThrown){

				}

		});
	
		/* add tracking classes to links and forms */
		var wp_cta_id = '<input type="hidden" name="wp_cta_id" value="' + cta_id + '">';
		var wp_cta_vid = '<input type="hidden" name="wp_cta_vid" value="'+ vid +'">';
		jQuery('#wp_cta_'+cta_id+'_variation_'+vid+' form').each(function(){
			jQuery(this).addClass('wpl-track-me');
			jQuery(this).append(wp_cta_id);
			jQuery(this).append(wp_cta_vid);
		});


		/* add click tracking - get lead cookies */
		var lead_cpt_id = jQuery.cookie("wp_lead_id");
		var lead_email = jQuery.cookie("wp_lead_email");
		var lead_unique_key = jQuery.cookie("wp_lead_uid");


		/* add click tracking  - setup lead data for click event tracking */
		if (typeof (lead_cpt_id) != "undefined" && lead_cpt_id !== null) {
			string = "&wpl_id=" + lead_cpt_id + "&l_type=wplid";
		} else if (typeof (lead_email) != "undefined" && lead_email !== null && lead_email !== "") {
			string = "&wpl_id=" + lead_email + "&l_type=wplemail";;
		} else if (typeof (lead_unique_key) != "undefined" && lead_unique_key !== null && lead_unique_key !== "") {
			string = "&wpl_id=" + lead_unique_key + "&l_type=wpluid";
		} else {
			string = "";
		}

		var external = RegExp('^((f|ht)tps?:)?//(?!' + location.host + ')');
		jQuery('#wp_cta_'+cta_id+'_variation_'+vid+' a').each(function ()
		{
			jQuery(this).attr("data-event-id",  cta_id ).attr("data-cta-variation", vid );

			var originalurl = jQuery(this).attr("href");
			if (originalurl  && originalurl.substr(0,1)!='#')
			{

				var cta_variation_string = "&wp-cta-v=" + vid;

				var newurl =  cta_reveal.home_url + "?wp_cta_redirect_" + cta_id + "=" + originalurl + cta_variation_string + string;
				jQuery(this).attr("href", newurl);
			}
		});

	} 
	/* if split testing is disabled then update wp_cta_loaded storage object with variation 0 */
	else if ( parseInt(disable_ajax) == 1 ) {
		/* update local storage variable */
		loaded_ctas[cta_id] = 0;

		/* update local storage object */
		jQuery.totalStorage('wp_cta_loaded', loaded_ctas); // store cta data
		console.log('WP CTA Load Object Updated:' + JSON.stringify(loaded_ctas));
		
	} 
	/* Poll the ajax server for the correct variation to display */
	else {		
		jQuery.ajax({
			 type: "GET",
			 url: cta_variation.ajax_url,
			 dataType: "script",
			 async:false,
			 data : {
			  'cta_id' : cta_id
			 },
			 success: function(vid) {
				/* update local storage variable */
				loaded_ctas[cta_id] = vid;
				
				/* update local storage object */
				jQuery.totalStorage('wp_cta_loaded', loaded_ctas); // store cta data
				console.log('WP CTA Load Object Updated:' + JSON.stringify(loaded_ctas));
			}
		});
	}
}

jQuery(document).ready(function($) {

	/* reset local storage variable every page load */
	jQuery.totalStorage.deleteItem('wp_cta_loaded'); // remove pageviews

	if (cta_variation.cta_id) {
		wp_cta_load_variation( cta_variation.cta_id , null , cta_variation.disable_ajax );
	}
});
