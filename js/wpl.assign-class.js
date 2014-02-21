function inbound_track_forms(forms_array, functionName) {
	jQuery.each(forms_array, function(index, id) {
		var clean_id = jQuery.trim(id);
		if (clean_id.indexOf('#')>-1) {
			jQuery(clean_id)[functionName]('wpl-track-me');
		} else if (clean_id.indexOf('.')>-1) {
			jQuery(clean_id)[functionName]('wpl-track-me');
		} else {
			jQuery("#" + clean_id)[functionName]('wpl-track-me');
		}
	});
}

jQuery(document).ready(function($) {

	var form_ids = wpleads.form_ids;
	var form_exclude_ids = wpleads.form_exclude_ids;
	if (typeof (form_ids) != "undefined" && form_ids != null && form_ids != "") {
		var forms = form_ids.split(',');
		inbound_track_forms(forms, 'addClass');
	}
	if (typeof (form_exclude_ids) != "undefined" && form_exclude_ids != null && form_exclude_ids != "") {
		var exclude_forms = form_exclude_ids.split(',');
		inbound_track_forms(exclude_forms, 'removeClass');
	}


	jQuery('form').each(function(){
	var match = 'comment', attributes = {}, form = $(this), form_id = form.attr('id'), form_class = form.attr('class'), form_name = form.attr('name'), form_action = form.attr('action'), form_target = form.attr('target');
	// map attrs
	attributes = {
		"form_id": form_id,
		"form_class": form_class,
		"form_name": form_name,
		"form_action": form_action,
		"form_target": form_target
	};
	// loop through attrs for match
	for (var atr in attributes) {
	   console.log(atr + ": " + attributes[atr]);
	   if (typeof (attributes[atr]) != "undefined" && attributes[atr] != null && attributes[atr] != "") {
			if (attributes[atr].toLowerCase().indexOf(match)>-1) {
				form.addClass('wpl-track-me').addClass('wpl-comment-form');
			}
			// add fallback to ajaxed forms
			if (attributes[atr].toLowerCase().indexOf('ajax')>-1) {
				form.addClass('wpl-ajax-fallback');
			}

			if (attributes[atr].toLowerCase().indexOf('search')>-1) {
				form.addClass('wpl-search-box').addClass('wpl-track-me');

			}
		}
	}

	});


});