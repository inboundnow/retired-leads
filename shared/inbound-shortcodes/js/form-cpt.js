jQuery(document).ready(function($) {
	//var button = '<a href="#" id="inbound_save_this_form" style="" class="button-primary">Save This Form</a>';
	//jQuery("#inbound_save_form").before(button);
    var form_move = jQuery("#entire-form-area");
    jQuery("#titlediv").after(form_move);
    jQuery("#entire-form-area").fadeIn(1000);
	jQuery("#inbound_save_form").removeClass('button').addClass('button-primary').text('Save Form');
    jQuery("#inbound-shortcodes-preview").hide().fadeIn(5000);
   	jQuery("body").on('change keyup', '#title', function () {
        jQuery("#title-prompt-text").hide();
   		var this_val = jQuery(this).val();
   		jQuery("#inbound_shortcode_form_name").val(this_val);
    });
    jQuery("body").on('change keyup', '#inbound_shortcode_form_name', function () {
            jQuery("#title-prompt-text").hide();
    		var this_val = jQuery(this).val();
    		jQuery("#title").val(this_val);
    });
    jQuery("body").on('click', '#inbound_save_this_form', function () {
    	var post_id = jQuery("#post_ID").val();
    });
    var post_status = jQuery("#hidden_post_status").val();
    if (post_status === 'draft') {
        jQuery("#inbound_save_form").text("Publish Form");
    }
    var post_id = jQuery("#post_ID").val();
    var post_title = jQuery("#title").val();
    //jQuery("#inbound_shortcode_form_name").val(post_title);
    var form_toggle = 'form_' + post_id;
    setTimeout(function() {
            jQuery("#inbound_shortcode_insert_default").val(form_toggle);
            InboundShortcodes.update_fields();
                var SelectionData = jQuery("#cpt-form-serialize").text();
                if (SelectionData != "") {

                	jQuery.each(SelectionData.split('&'), function (index, elem) {
                	   	var vals = elem.split('=');
                	   	var $select_val = jQuery('select[name="'+vals[0]+'"]').attr('name');
                	   	var $select = jQuery('select[name="'+vals[0]+'"]');
                	   	var $input = jQuery('input[name="'+vals[0]+'"]'); // input vals
                	   	var $textarea = jQuery('textarea[name="'+vals[0]+'"]'); // input vals
                	   	var separator = '';
                	   	/*if ($div.html().length > 0) {
                	   		separator = ', ';
                	   	}*/
                	   	$input.val(decodeURIComponent(vals[1].replace(/\+/g, ' ')));
                	   	if ($select_val != 'inbound_shortcode_insert_default'){
                	   	$select.val(decodeURIComponent(vals[1].replace(/\+/g, ' ')));
                	   	}
                	   	$textarea.val(decodeURIComponent(vals[1].replace(/\+/g, ' ')));
                	   });

            	}
     }, 1000);


    if (post_status === 'draft' && post_title != "" || post_status ==='pending' && post_title != "" ) {
    	// run auto publish ajax
    	        jQuery.ajax({
    	            type: 'POST',
    	            url: ajaxurl,
    	            context: this,
    	            data: {
    	                action: 'inbound_form_auto_publish',
    	                post_id: post_id,
    	                post_title: post_title
    	            },

    	            success: function (data) {
    	               console.log("This Form has been auto published");
    	            },

    	            error: function (MLHttpRequest, textStatus, errorThrown) {
    	                alert("Ajax not enabled");
    	            }
    	        });
    }
 });
