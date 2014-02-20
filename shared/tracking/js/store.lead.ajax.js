var inbound_form_data = inbound_form_data || {};
// Ensure global _gaq Google Analytics queue has been initialized.
var _gaq = _gaq || [];

function add_inbound_form_class(el, value) {
  //var value = value.replace(" ", "_");
  //var value = value.replace("-", "_");
  //el.addClass('inbound_map_value');
  //el.attr('data-inbound-form-map', 'inbound_map_' + value);
}

function get_inbound_form_value(el) {
  var value = el.value;
  return value;
}

// Build Form Object
function inbound_map_fields(el, value, Obj) {
  var formObj = [];
  var $this = el;
  var clean_output = value;
  var label = $this.closest('label').text();
  var exclude = ['credit-card']; // exlcude values from formObj
  var inarray = jQuery.inArray(clean_output, exclude);
  if(inarray == 0){
  	return null;
  }
  // Add items to formObj
  formObj.push({
  				field_label: label,
                field_name: $this.attr("name"),
                field_value: $this.attr("value"),
                field_id: $this.attr("id"),
                field_class: $this.attr("class"),
                field_type: $this.attr("type"),
                match: clean_output,
                js_selector: $this.attr("data-js-selector")
              });
  return formObj;
}

// Trim Whitespace
function trim(s) {
    s = s.replace(/(^\s*)|(\s*$)/gi,"");
    s = s.replace(/[ ]{2,}/gi," ");
    s = s.replace(/\n /,"\n"); return s;
}

function inbound_ga_log_event(category, action, label) {
  _gaq.push(['_trackEvent', category, action, label]);
}

// Run Form Mapper
// TODO check for already processesed fields via in_object_already
// check on the dupe value
function run_field_map_function(el, lookingfor) {
  var return_form;
  var formObj = new Array();
  var $this = el;
  var body = jQuery("body");
  var this_val = $this.attr("value");
      var array = lookingfor.split(",");
      var array_length = array.length - 1;

      // Main Loop
      for (var i = 0; i < array.length; i++) {
          var clean_output = trim(array[i]);
          var nice_name = clean_output.replace(/^\s+|\s+$/g,'');
          var nice_name = nice_name.replace(" ",'_');
          var in_object_already = nice_name in inbound_form_data;
          //console.log(clean_output);

          // Look for attr name match
          if ($this.attr("name").toLowerCase().indexOf(clean_output)>-1) {
            var the_map = inbound_map_fields($this, clean_output, formObj);
            add_inbound_form_class($this, clean_output);
            console.log('match name: ' + clean_output);
            console.log(nice_name in inbound_form_data);
            if (!in_object_already) {
            inbound_form_data[nice_name] = this_val;
        	}
          }
          // look for id match
          else if ($this.attr("id").toLowerCase().indexOf(clean_output)>-1) {
            var the_map = inbound_map_fields($this, clean_output, formObj);
            add_inbound_form_class($this, clean_output);
            console.log('match id: ' + clean_output);
            if (!in_object_already) {
            inbound_form_data[nice_name] = this_val;
        	}
          }
          // Look for label name match
          else if ($this.closest('li').children('label').length>0)
          {
            if ($this.closest('li').children('label').html().toLowerCase().indexOf(clean_output)>-1)
            {
              var the_map = inbound_map_fields($this, clean_output, formObj);
              add_inbound_form_class($this, clean_output);
              console.log($this.context);

              var exists_in_dom = body.find("[data-inbound-form-map='inbound_map_" + nice_name + "']").length;
              console.log(exists_in_dom);
              console.log('match li: ' + clean_output);
              if (!in_object_already) {
              	inbound_form_data[nice_name] = this_val;
              }

            }
          }
          // Look for closest div label name match
          else if ($this.closest('div').children('label').length>0)
          {
            if ($this.closest('div').children('label').html().toLowerCase().indexOf(clean_output)>-1)
            {
              var the_map = inbound_map_fields($this, clean_output, formObj);
              add_inbound_form_class($this, clean_output);
              console.log('match div: ' + clean_output);
              if (!in_object_already) {
              inbound_form_data[nice_name] = this_val;
          	  }
            }
          } else {
          	// regex check?
          	return false;
          }
      }
      return_form = the_map;

  return inbound_form_data;
}

function return_mapped_values(this_form) {
	// Map form fields
	jQuery(this_form).find('input[type=text],input[type=email],textarea,select').each(function() {
		var this_input = jQuery(this);
		if (this.value) {
		var inbound_form_data = run_field_map_function( this_input, "name, first name, last name, email, e-mail, phone, website, job title, company, tele, address");
		}
		return inbound_form_data;
	});
	return inbound_form_data;
}

function merge_form_options(obj1,obj2){
    var obj3 = {};
    for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
    for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
    return obj3;
}

function release_form_sub(this_form, element_type){
	jQuery('body, button, input, input[type="button"]').css('cursor', 'default');

	if (element_type=='FORM') {
		this_form.unbind('submit');
		this_form.submit();
	}

	if (element_type=='A') {
		this_form.unbind('wpl-track-me');

		if (a_href) {
			window.location = a_href;
		} else {
			location.reload();
		}
	}
}

jQuery(document).ready(function($) {
/* Core Inbound Form Tracking Script */
	jQuery("body").on('submit', '.wpl-track-me', function (e) {

		this_form = jQuery(this);
		element_type = 'FORM';

		// process form only once
		processed = this_form.hasClass('lead_processed');
		if (processed === true) {
			return;
		}
		// halt normal form submission
		$('body, button, input[type="button"], input').css('cursor', 'wait');
		e.preventDefault();

		// Email Validation Check
		var inbound_form_exists = $("#inbound-form-wrapper").length;
		var email_validation = $(".inbound-email.invalid-email").length;
		if (email_validation > 0 && inbound_form_exists > 0) {
			jQuery(".inbound-email.invalid-email").focus();
			alert("Please enter a valid email address");
			return false;
		}

		/* Define Variables */
		// Dynamic JS object for passing custom values. This can be hooked into by third parties by using the below syntax.
		var inbound_form_data = inbound_form_data || {};
		inbound_form_data['page_view_count'] = countProperties(pageviewObj);
		inbound_form_data['leads_list'] = jQuery(this_form).find('#inbound_form_lists').val();
		inbound_form_data['source'] = jQuery.cookie("wp_lead_referral_site") || "NA";
		inbound_form_data['page_id'] = inbound_ajax.post_id;

		// Map form fields
		var returned_form_data = return_mapped_values(this_form);
		var inbound_form_data = merge_form_options(inbound_form_data,returned_form_data);
		//console.log(inbound_form_data);

		// Set variables
		var email = inbound_form_data['email'] || false; // back fallback
		if (!email) {
		var email = this_form.find('.inbound-email').val() || false; // email fallback
		inbound_form_data['email'] = email;
		}


		var form_name = this_form.find('.inbound_form_name').val() || "Not Found";
		inbound_form_data['form_name'] = form_name;
		var firstname = inbound_form_data['first_name'] || false;
		var lastname = inbound_form_data['last_name'] || false;
		var phone = inbound_form_data['phone'] || "";
		var company = inbound_form_data['company'] || "";
		var address = inbound_form_data['address'] || "";

		if (firstname && lastname) {
			inbound_form_data['name'] = firstname + " " + lastname;
		}

		if(!firstname){
		  var firstname = inbound_form_data['name'] || false;
		}

		if (!lastname && firstname) {
			var parts = firstname.split(" ");
			firstname = parts[0];
			lastname = parts[1];
		}

		var form_inputs = jQuery('.wpl-track-me').find('input[type=text],input[type=hidden],textarea,select');
		var post_values = {};

		// unset values with exclude array
		var inbound_exclude = inbound_exclude || [];
		inbound_exclude.push('inbound_furl', 'inbound_current_page_url', 'inbound_notify', 'inbound_submitted', 'post_type', 'post_status', 's', 'inbound_form_name', 'inbound_form_id', 'inbound_form_lists');

		form_inputs.each(function() {
			if (jQuery.inArray(this.name, inbound_exclude) === -1){
			   post_values[this.name] = jQuery(this).val();
			}
			if (this.value.indexOf('@')>-1&&!email){
				email = jQuery(this).val();
				inbound_form_data['email'] = email;
			}
		});

		var all_form_fields = JSON.stringify(post_values);

		var page_views = JSON.stringify(pageviewObj);

		if (typeof (landing_path_info) != "undefined" && landing_path_info != null && landing_path_info != "") {
			var lp_variation = landing_path_info.variation;
		} else if (typeof (cta_path_info) != "undefined" && cta_path_info != null && cta_path_info != "") {
			var lp_variation = cta_path_info.variation;
		} else {
			var lp_variation = 0;
		}

		inbound_form_data['variation'] = lp_variation;
		inbound_form_data['post_type'] = inbound_ajax.post_type;
		inbound_form_data['wp_lead_uid'] = jQuery.cookie("wp_lead_uid") || null;
		inbound_form_data['ip_address'] = inbound_ajax.ip_address;

		var lp_check = (inbound_ajax.post_type === 'landing-page') ? 'Landing Page' : "";
		var cta_check = (inbound_ajax.post_type === 'wp-call-to-action') ? 'Call to Action' : "";
		var page_type = (!cta_check && !lp_check) ? inbound_ajax.post_type : lp_check + cta_check;

		// jsonify inbound_form_data
		var post_form_data = JSON.stringify(inbound_form_data);
		console.log(post_form_data);

		jQuery.cookie("wp_lead_email", email, { path: '/', expires: 365 }); // save email

		jQuery.ajax({
			type: 'POST',
			url: inbound_ajax.admin_url,
			timeout: 10000,
			data: {
				action: 'inbound_store_lead',
				emailTo: email,
				first_name: firstname,
				last_name: lastname,
				phone: phone,
				address: address,
				company_name: company,
				page_views: page_views,
				form_input_values: all_form_fields,
				Mapped_Data: post_form_data
				/* Replace with jquery hook
					do_action('wpl-lead-collection-add-ajax-data');
				*/
			},
			success: function(user_id){
					jQuery(this_form).trigger("inbound_form_complete"); // Trigger custom hook
					jQuery.cookie("wp_lead_id", user_id, { path: '/', expires: 365 });
					jQuery.totalStorage('wp_lead_id', user_id);
					this_form.addClass('lead_processed');
					inbound_ga_log_event('Inbound Form Conversions', 'Conversion', "Conversion on '"+ form_name + "' form on page '" + document.title + "' on url '" + window.location.href + "'"); // GA push
					//console.log(_gaq);

					// Unbind form
					release_form_sub(this_form, 'FORM');

					$('body, button, input[type="button"], input').css('cursor', 'default');

					jQuery.totalStorage.deleteItem('page_views'); // remove pageviews
					jQuery.totalStorage.deleteItem('tracking_events'); // remove events
					//jQuery.totalStorage.deleteItem('cta_clicks'); // remove cta


				   },
			error: function(MLHttpRequest, textStatus, errorThrown){
					//console.log(MLHttpRequest+' '+errorThrown+' '+textStatus); // debug

					// Create fallback localstorage object
					var conversionObj = new Array();
					conversionObj.push({
										action: 'inbound_store_lead',
										emailTo: email,
										first_name: firstname,
										last_name: lastname,
										wp_lead_uid: wp_lead_uid,
										page_view_count: page_view_count,
										page_views: page_views,
										post_type: inbound_ajax.post_type,
										lp_variation: lp_variation,
										// type: 'form-completion',
										form_input_values : all_form_fields,
										lp_id: page_id
										});

					jQuery.totalStorage('failed_conversion', conversionObj); // store failed data
					jQuery.cookie("failed_conversion", true, { path: '/', expires: 365 });

					// If fail, cookie form data and ajax submit on next page load
					console.log('ajax fail');
					release_form_sub( this_form , element_type );

				}
		});

	});

	jQuery("body").on('click', '.wpl-track-me-link', function (e) {

		this_form = jQuery(this);

		var element_type='A';
		var a_href = jQuery(this).attr("href");

		// process form only once
		processed = this_form.hasClass('lead_processed');
		if (processed === true) {
			return;
		}

		form_id = jQuery(this).attr('id');
		form_class = jQuery(this).attr('class');

		jQuery(this).css('cursor', 'wait');
		jQuery('body').css('cursor', 'wait');


		e.preventDefault(); // halt normal form

		var tracking_obj = "";
		var page_view_count = countProperties(pageviewObj);
		//console.log("view count" + page_view_count);

		var wp_lead_uid = jQuery.cookie("wp_lead_uid");
		var page_views = JSON.stringify(pageviewObj);

		var page_id = inbound_ajax.post_id;
		if (typeof (landing_path_info) != "undefined" && landing_path_info != null && landing_path_info != "") {
			var lp_variation = landing_path_info.variation;
		} else if (typeof (cta_path_info) != "undefined" && cta_path_info != null && cta_path_info != "") {
			var lp_variation = cta_path_info.variation;
		} else {
			var lp_variation = null;
		}

		jQuery.ajax({
			type: 'POST',
			url: inbound_ajax.admin_url,
			timeout: 10000,
			data: {
				action: 'inbound_store_lead',
				wp_lead_uid: wp_lead_uid,
				page_views: page_views,
				post_type: inbound_ajax.post_type,
				lp_variation: lp_variation,
				lp_id: page_id
				/* Replace with jquery hook
					do_action('wpl-lead-collection-add-ajax-data');
				*/
			},
			success: function(user_id){
					jQuery(this_form).trigger("inbound_form_complete"); // Trigger custom hook
					jQuery.cookie("wp_lead_id", user_id, { path: '/', expires: 365 });
					jQuery.totalStorage('wp_lead_id', user_id);
					this_form.addClass('lead_processed');

					// Unbind form
					release_form_sub(this_form, 'A');

					jQuery.totalStorage.deleteItem('page_views'); // remove pageviews
					jQuery.totalStorage.deleteItem('tracking_events'); // remove events
					//jQuery.totalStorage.deleteItem('cta_clicks'); // remove cta
				   },
			error: function(MLHttpRequest, textStatus, errorThrown){
					//console.log(MLHttpRequest+' '+errorThrown+' '+textStatus); // debug

					// Create fallback localstorage object
					var conversionObj = new Array();
					conversionObj.push({
										action: 'inbound_store_lead',
										emailTo: email,
										first_name: firstname,
										last_name: lastname,
										wp_lead_uid: wp_lead_uid,

										page_views: page_views,
										post_type: inbound_ajax.post_type,
										lp_variation: lp_variation,
										// type: 'form-completion',
										form_input_values : all_form_fields,
										lp_id: page_id
										});

					jQuery.totalStorage('failed_conversion', conversionObj); // store failed data
					jQuery.cookie("failed_conversion", true, { path: '/', expires: 365 });

					// If fail, cookie form data and ajax submit on next page load
					console.log('ajax fail');
					release_form_sub( this_form , element_type );

				}
		});



});


/*  Fallback for lead storage if ajax fails */
var failed_conversion = jQuery.cookie("failed_conversion");
var fallback_obj = jQuery.totalStorage('failed_conversion');

if (typeof (failed_conversion) != "undefined" && failed_conversion == 'true' ) {
	if (typeof fallback_obj == 'object' && fallback_obj) {
		//console.log('fallback ran');
			jQuery.ajax({
				type: 'POST',
				url: inbound_ajax.admin_url,
				data: {
						action: fallback_obj[0].action,
						emailTo: fallback_obj[0].emailTo,
						first_name: fallback_obj[0].first_name,
						last_name: fallback_obj[0].last_name,
						wp_lead_uid: fallback_obj[0].wp_lead_uid,

						page_views: fallback_obj[0].page_views,
						post_type: fallback_obj[0].post_type,
						lp_variation: fallback_obj[0].lp_variation,
						json: fallback_obj[0].json, // replace with page_view_obj
						// type: 'form-completion',
						form_input_values: fallback_obj[0].raw_post_values_json,
						lp_id: fallback_obj[0].lp_id
						/* Replace with jquery hook
							do_action('wpl-lead-collection-add-ajax-data');
						*/
					},
				success: function(user_id){
					//console.log('Fallback fired');
					jQuery.removeCookie("failed_conversion"); // remove failed cookie
					jQuery.totalStorage.deleteItem('failed_conversion'); // remove failed data
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
						//alert(MLHttpRequest+' '+errorThrown+' '+textStatus);
						//die();
				}

			});
	}
}

});