jQuery(document).ready(function () {

	var form_ids = wpleads.form_ids;
	var forms = form_ids.split(',');
	
	jQuery.each(forms, function(index, id) {
		jQuery('#'+ jQuery.trim(id)).addClass('wpl-track-me');
	}); 

});