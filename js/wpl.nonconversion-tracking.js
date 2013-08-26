jQuery(document).ready(function($) {
	//alert(wplnct.admin_url);
	
	//record non conversion status
	var wp_lead_uid = jQuery.cookie("wp_lead_uid");	
	var wp_lead_id = jQuery.cookie("wp_lead_id");	
	//var data_block = jQuery.parseJSON(trackObj);
	var json = JSON.stringify(trackObj);
	var page_id = wplnct.final_page_id;
	//console.log(page_id);

if (typeof (wp_lead_id) != "undefined" && wp_lead_id != null && wp_lead_id != "") {	
	jQuery.ajax({
		type: 'POST',
		url: wplnct.admin_url,
		data: {
			action: 'wpl_track_user',
			wp_lead_uid: wp_lead_uid,
			wp_lead_id: wp_lead_id,
			page_id: page_id,
			current_url: window.location.href,
			json: json		
		},
		success: function(user_id){
			console.log('Page View Fired');	
			   },
		error: function(MLHttpRequest, textStatus, errorThrown){
				//alert(MLHttpRequest+' '+errorThrown+' '+textStatus);
				//die();
			}

	});
}
// Check for Lead lists
var expired = jQuery.cookie("lead_session_expire"); // check for session
if (expired != "false") {
	//var data_to_lookup = global-localized-vars;
	if (typeof (wp_lead_id) != "undefined" && wp_lead_id != null && wp_lead_id != "") {
		jQuery.ajax({
					type: 'POST',
					url: wplnct.admin_url,
					data: {
						action: 'wpl_check_lists',
						wp_lead_id: wp_lead_id, 
						
					},
					success: function(user_id){
							//jQuery.cookie("wp_lead_id", user_id, { path: '/', expires: 365 });
							console.log("checked");
						   },
					error: function(MLHttpRequest, textStatus, errorThrown){
							
						}

				});
		} 
	}
/* end list check */

/* Set Expiration Date of Session Logging */
var e_date = new Date(); // Current date/time
var e_minutes = 30; // 30 minute timeout to reset sessions
e_date.setTime(e_date.getTime() + (e_minutes * 60 * 1000)); // Calc 30 minutes from now
jQuery.cookie("lead_session_expire", false, {expires: e_date, path: '/' }); // Set cookie on page loads
var expire_time = jQuery.cookie("lead_session_expire"); // 
//console.log(expire_time);
});