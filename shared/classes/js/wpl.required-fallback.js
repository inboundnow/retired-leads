jQuery(document).ready(function() {
	jQuery('form').submit(function(e) {
		jQuery('form').find('input').each(function(){
		    if(!jQuery(this).prop('required')){
		    } else if (!jQuery(this).val()) {
			alert('Oops! Looks like you have not filled out all of the required fields!');
			e.preventDefault();
			e.stopImmediatePropagation();
			return false;
		    }
		});
	});
});