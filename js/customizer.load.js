jQuery(document).ready(function($) {
	var viewchoice = $.cookie("wp-cta-view-choice");
	jQuery("body").css('background', 'transparent');
	var current_page = jQuery("#current_variation_id").text();
var original_url = jQuery(parent.document).find("#TB_iframeContent").attr("src");
console.log(original_url);
	// reload the iframe preview page (for option toggles)
/*	jQuery('.variation-wp-cta').on('click', function (event) {
		varaition_is = jQuery(this).attr("id");
		var original_url = jQuery(parent.document).find("#TB_iframeContent").attr("src");
		var current_id = jQuery("#current-post-id").text();
		someURL = original_url;
		return;
		console.log(someURL);
		splitURL = someURL.split('?');
		someURL = splitURL[0];
		new_url = someURL + "?wp-cta-variation-id=" + varaition_is + "&wp_cta_iframe_window=on&post_id=" + current_id;
		jQuery(parent.document).find("#TB_iframeContent").attr("src", new_url);

	});
*/
	jQuery("body").on('click', '.dislink', function (e) {
 		e.preventDefault();
 		console.log('disabled clicked');
    });





	jQuery('head').append('<link rel="stylesheet" href="/wp-content/plugins/cta/css/customizer-load.css" type="text/css" />');


	if (viewchoice === "full-size") {
		jQuery('html').removeClass('small-html');
		jQuery('html').width('100%');
		jQuery('body').height('100%');
	 	setTimeout(function() {
	 	jQuery(parent.document).find('#wp_cta_customizer_options').contents().find(".full-size-view").hide();
		jQuery(parent.document).find('#wp_cta_customizer_options').contents().find(".shrink-view").show();
	}, 1000);
	}

	setTimeout(function () {

			var sidebarwidth = jQuery('#wp_cta_customizer_options').width();
			var widthfix = jQuery(parent.document).width() - sidebarwidth;

			//console.log('ran');
		}, 2000);
	/* Almost working
	jQuery('.live-preview-area-box, #wp_cta_container_form').on('mouseover', function (event)
	{
		var $tgt = jQuery(event.target);
		var domElement = jQuery(event.target);
		var current_el_id = jQuery(this).attr('id');
		var match_color = jQuery(this).css("color");

		jQuery(this).addClass("live-preview-active");

		if ( match_color === "rgb(255, 255, 255)") {
			jQuery(this).addClass('wp-cta-see-this');
		}

		var current_el_parent_id = jQuery(this).parent().attr('id');

		if (typeof (current_el_id) != "undefined" && current_el_id !== null)
		{
						actual_el = "#" + current_el_id;
		}
		else if (typeof (current_el_parent_id) != "undefined" && current_el_parent_id !== null && current_el_parent_id !== "")
		{
						var actual_el = "#" + current_el_parent_id;
		}
		else
		{
			console.log("empty");
		}
		//console.log(actual_el);
		var finding_the_match = jQuery(parent.document).find('#wp_cta_customizer_options').contents().find(actual_el);
		jQuery(finding_the_match).parent().parent().css("background", "#EBEBEB");


	});

	jQuery('.live-preview-area-box, #wp_cta_container_form').on('mouseout', function (event) {
		var $tgt = jQuery(event.target);
		var domElement = jQuery(event.target);
		var match_color = jQuery(this).css("color");

		jQuery(this).removeClass("live-preview-active");

		if ( match_color === "rgb(0, 0, 0)") {
			jQuery(this).removeClass('wp-cta-see-this');
		}

		var current_el_id = jQuery(this).attr('id');
		var current_el_parent_id = jQuery(this).parent().attr('id');

		if (typeof (current_el_id) != "undefined" && current_el_id !== null)
		{
			actual_el = "#" + current_el_id;
		}
		else if (typeof (current_el_parent_id) != "undefined" && current_el_parent_id !== null && current_el_parent_id !== "")
		{
			var actual_el = "#" + current_el_parent_id;
		}
		else
		{
			console.log("empty");
		}
		// console.log(actual_el);
		var finding_the_match = jQuery(parent.document).find('#wp_cta_customizer_options').contents().find(actual_el);
		jQuery(finding_the_match).parent().parent().css("background", "white");

	});



	jQuery('.live-preview-area-box').on('click', function (event)
	{
		var $tgt = jQuery(event.target);
		var domElement = jQuery(event.target);
		var current_el_id = jQuery(this).attr('id');
		var click_id = "click-" + current_el_id;
		var frame_body = jQuery(parent.document).find('#wp_cta_customizer_options').contents().find(click_id);

		console.log(frame_body);
		//jQuery(parent.document).find('#wp_cta_customizer_options').contents().find(click_id).click();
		//parent.document.getElementById('wp_cta_customizer_options').contentWindow.MyFunction(click_id, event)


	});
	*/
});