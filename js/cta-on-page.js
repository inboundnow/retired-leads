jQuery(document).ready(function($) {
  	var iframe_size = jQuery(parent.document).find('#wp-cta').width(),
  	link_open = jQuery("#cpt_cta_link_opens").text();
  	
  	//console.log(iframe_size);
  	if (link_open === "new_tab"){
  		//console.log('new tab');
  		jQuery('a').each(function(){
  			jQuery(this).attr('target', '_blank');
  			jQuery(this).addClass('external-new-tab');
		});
		// Close Popup if it exists
		jQuery("body").on('click', '.external-new-tab', function () {
			var if_pop = jQuery(parent.document).find('.mfp-close');
			if ($(if_pop).length > 0){
			setTimeout(function() {
             jQuery(parent.document).find('.mfp-close').click();  
        	}, 600);
			}
    	});
  		/* works but browsers might block. Expand for popup windows
  		function OpenInNewTab(url){
			var win=window.open(url, '_blank');
			win.focus();
		}
  		jQuery("body").on('click', 'a', function (event) {
		event.preventDefault();
		open_link = jQuery(this).attr("href");
		OpenInNewTab(open_link); // open link in new tab
	    }); */
  	} else {
	    //console.log('this window');
  		jQuery("body").on('click', 'a', function (event) {
		event.preventDefault();
		open_link = jQuery(this).attr("href");
		parent.window.location.href = open_link;
	    });
  	}

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