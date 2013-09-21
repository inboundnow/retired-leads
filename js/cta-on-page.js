jQuery(document).ready(function($) {
  	var iframe_size = jQuery(parent.document).find('#wp-cta').width();
  	//console.log(iframe_size);
	jQuery("body").on('click', 'a', function (event) {
	event.preventDefault();
	open_link = jQuery(this).attr("href");
	parent.window.location.href = open_link;
    });

    jQuery('form').each(function(){
    	jQuery(this).addClass('wpl-track-me');
	});

	
	
	$(".wpl-track-me").on("inbound_form_complete", function() {
		var redirect_val = jQuery("#inbound_redirect").val();
		var parent_page = parent.window.location;
		if (typeof (redirect_val) != "undefined" && redirect_val != null && redirect_val != "") {
	    parent.window.location.href = redirect_val; // redirect to thank you
		} else {
		var if_pop = jQuery(parent.document).find('.mfp-close');
		console.log("pop" + if_pop);
			if ($(if_pop).length > 0){
	
			jQuery(parent.document).find('.mfp-close').click();
			} else {
			parent.window.location.href = parent_page;
			}
		// jQuery.magnificPopup.close();	
		} 
    });
	
   
 });