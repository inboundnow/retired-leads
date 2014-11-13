/**
 * # Inbound Forms
 *
 * This file contains all of the form functions of the main _inbound object.
 * Filters and actions are described below
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */

/* Launches form class */
var InboundForms = (function (_inbound) {

    var debugMode = false,
    utils = _inbound.Utils,
    no_match = [],
    rawParams = [],
    mappedParams = [],
    settings = _inbound.Settings;

    var FieldMapArray = [
                        "first name",
                        "last name",
                        "name",
                        "email",
                        "e-mail",
                        "phone",
                        "website",
                        "job title",
                        "your favorite food",
                        "company",
                        "tele",
                        "address",
                        "comment"
                        /* Adding values here maps them */
    ];

    _inbound.Forms =  {

      // Init Form functions
      init: function() {
          _inbound.Forms.runFieldMappingFilters();
          _inbound.Forms.assignTrackClass();
          _inbound.Forms.formTrackInit();
      },
      /**
       * This triggers the forms.field_map filter on the mapping array.
       * This will allow you to add or remore Items from the mapping lookup
       *
       * ### Example inbound.form_map_before filter
       *
       * This is an example of how form mapping can be filtered and
       * additional fields can be mapped via javascript
       *
       * ```js
       *  // Adding the filter function
       *  function Inbound_Add_Filter_Example( FieldMapArray ) {
       *    var map = FieldMapArray || [];
       *    map.push('new lookup value');
       *
       *    return map;
       *  };
       *
       *  // Adding the filter on dom ready
       *  _inbound.hooks.addFilter( 'inbound.form_map_before', Inbound_Add_Filter_Example, 10 );
       * ```
       *
       * @return {[type]} [description]
       */
      runFieldMappingFilters: function(){
          FieldMapArray = _inbound.hooks.applyFilters( 'forms.field_map', FieldMapArray);
          //alert(FieldMapArray);
      },
      debug: function(msg, callback){
         //if app not in debug mode, exit immediately
         if(!debugMode || !console){return};
         var msg = msg || false;
         //console.log the message
         if(msg && (typeof msg === 'string')){console.log(msg)};

         //execute the callback if one was passed-in
         if(callback && (callback instanceof Function)){
              callback();
         };
      },
      formTrackInit: function(){

          for(var i=0; i<window.document.forms.length; i++){
            var trackForm = false;
            var form = window.document.forms[i];

            trackForm = this.checkTrackStatus(form);
            // var trackForm = _inbound.Utils.hasClass("wpl-track-me", form);
            if (trackForm) {
              this.attachFormSubmitEvent(form); /* attach form listener */
              this.initFormMapping(form);
            }
          }
      },
      checkTrackStatus: function(form){
          var ClassIs = form.getAttribute('class');
          if( ClassIs !== "" && ClassIs !== null) {
              if(ClassIs.toLowerCase().indexOf("wpl-track-me")>-1) {
                return true;
              } else if (ClassIs.toLowerCase().indexOf("inbound-track")>-1) {
                return true;
              } else {
                console.log("No form to track on this page. Please assign on in settings");
                return false;
              }
          }
      },
      assignTrackClass: function() {
          if(window.inbound_track_include){
              var selectors = inbound_track_include.include.split(',');
              this.loopClassSelectors(selectors, 'add');
          }
          if(window.inbound_track_exclude){
              var selectors = inbound_track_exclude.exclude.split(',');
              this.loopClassSelectors(selectors, 'remove');
          }
      },
      /* Loop through include/exclude items for tracking */
      loopClassSelectors: function(selectors, action){
          for (var i = selectors.length - 1; i >= 0; i--) {
            selector = document.querySelector(utils.trim(selectors[i]));
            //console.log("SELECTOR", selector);
            if(selector) {
                if( action === 'add'){
                  _inbound.Utils.addClass('wpl-track-me', selector);
                  _inbound.Utils.addClass('inbound-track', selector);
                } else {
                  _inbound.Utils.removeClass('wpl-track-me', selector);
                  _inbound.Utils.removeClass('inbound-track', selector);
                }
            }
          };
      },
      /* Map field fields on load */
      initFormMapping: function(form) {
                        var hiddenInputs = [];

                        for (var i=0; i < form.elements.length; i++) {
                            formInput = form.elements[i];

                            if (formInput.type === 'hidden') {
                                hiddenInputs.push(formInput);
                                continue;
                            }
                            /* Map form fields */
                            this.mapField(formInput);
                            /* Remember visible inputs */
                            this.rememberInputValues(formInput);
                            /* Fill visible inputs */
                            if(settings.formAutoPopulation){
                              this.fillInputValues(formInput);
                            }

                        }
                        for (var i = hiddenInputs.length - 1; i >= 0; i--) {
                            formInput = hiddenInputs[i];
                            this.mapField(formInput);
                        };

                    //console.log('mapping on load completed');
      },
      /* prevent default submission temporarily */
      formListener: function(event) {
          console.log(event);
          event.preventDefault();
          _inbound.Forms.saveFormData(event.target);
      },
      /* attach form listeners */
      attachFormSubmitEvent: function (form) {
        utils.addListener(form, 'submit', this.formListener);
      },
      releaseFormSubmit: function(form){
        //console.log('remove form listener event');
        utils.removeClass('wpl-track-me', form);
        utils.removeListener(form, 'submit', this.formListener);
        form.submit();
        /* fallback if submit name="submit" */
        setTimeout(function() {
            for (var i=0; i < form.elements.length; i++) {
              formInput = form.elements[i];
              type = formInput.type || false;
              if (type === "submit") {
                form.elements[i].click();
              }
            }
        }, 1300);

      },
      saveFormData: function(form) {
          var inputsObject = inputsObject || {};
          for (var i=0; i < form.elements.length; i++) {
              this.debug('inputs obj',function(){
                  console.log(inputsObject);
              });

              formInput = form.elements[i];
              multiple = false;

              if (formInput.name) {

                  inputName = formInput.name.replace(/\[([^\[]*)\]/g, "%5B%5D$1");
                  //inputName = inputName.replace(/-/g, "_");
                  if (!inputsObject[inputName]) { inputsObject[inputName] = {}; }
                  if (formInput.type) { inputsObject[inputName]['type'] = formInput.type; }
                  if (!inputsObject[inputName]['name']) { inputsObject[inputName]['name'] = formInput.name; }
                  if (formInput.dataset.mapFormField) {
                    inputsObject[inputName]['map'] = formInput.dataset.mapFormField;
                  }
                  /*if (formInput.id) { inputsObject[inputName]['id'] = formInput.id; }
                  if ('classList' in document.documentElement)  {
                      if (formInput.classList) { inputsObject[inputName]['class'] = formInput.classList; }
                  }*/

                  switch (formInput.nodeName) {

                      case 'INPUT':
                          value = this.getInputValue(formInput);

                          console.log(value);
                          if (value === false) { continue; }
                          break;

                      case 'TEXTAREA':
                          value = formInput.value;
                          break;

                      case 'SELECT':
                          if (formInput.multiple) {
                              values = [];
                              multiple = true;

                              for (var j = 0; j < formInput.length; j++) {
                                  if (formInput[j].selected) {
                                      values.push(encodeURIComponent(formInput[j].value));
                                  }
                              }

                          } else {
                              value = (formInput.value);
                          }

                          console.log('select val', value);
                          break;
                  }

                  if (value) {
                      /* inputsObject[inputName].push(multiple ? values.join(',') : encodeURIComponent(value)); */
                      if (!inputsObject[inputName]['value']) { inputsObject[inputName]['value'] = []; }
                      inputsObject[inputName]['value'].push(multiple ? values.join(',') : encodeURIComponent(value));
                      var value = multiple ? values.join(',') : encodeURIComponent(value);

                  }

              }
          }

          //console.log('These are the raw values', inputsObject);
          //_inbound.totalStorage('the_key', inputsObject);
          //var inputsObject = sortInputs(inputsObject);

          var matchCommon = /name|first name|last name|email|e-mail|phone|website|job title|company|tele|address|comment/;

          for (var input in inputsObject) {
              //console.log(input);

              var inputValue = inputsObject[input]['value'];
              var inputMappedField = inputsObject[input]['map'];
              //if (matchCommon.test(input) !== false) {
                  //console.log(input + " Matches Regex run mapping test");
                  //var map = inputsObject[input];
                  //console.log("MAPP", map);
                  //mappedParams.push( input + '=' + inputsObject[input]['value'].join(',') );
              //}

              /* Add custom hook here to look for additional values */
              if (typeof (inputValue) != "undefined" && inputValue != null && inputValue != "") {
                  rawParams.push( input + '=' + inputsObject[input]['value'].join(',') );
              }

              if (typeof (inputMappedField) != "undefined" && inputMappedField != null && inputsObject[input]['value']) {
                //console.log('Data ATTR', formInput.dataset.mapFormField);
                mappedParams.push( inputMappedField + "=" + inputsObject[input]['value'].join(',') );
                if(input === 'email'){
                  var email = inputsObject[input]['value'].join(',');
                  //alert(email);

                }
              }
          }

          var raw_params = rawParams.join('&');
          console.log("Stringified Raw Form PARAMS", raw_params);


          var mapped_params = mappedParams.join('&');
          console.log("Stringified Mapped PARAMS", mapped_params);

          /* Check Use form Email or Cookie */
          var email = utils.getParameterVal('email', mapped_params) || utils.readCookie('wp_lead_email');
          var fullName = utils.getParameterVal('name', mapped_params);
          var fName = utils.getParameterVal('first_name', mapped_params);
          var lName = utils.getParameterVal('last_name', mapped_params);

          // Fallbacks for empty values
          if (!lName && fName) {
            var parts = decodeURI(fName).split(" ");
            if(parts.length > 0){
                fName = parts[0];
                lName = parts[1];
            }
          }

          if(fullName && !lName && !fName){
            var parts = decodeURI(fullName).split(" ");
            if(parts.length > 0){
                fName = parts[0];
                lName = parts[1];
            }
          }

          fullName = (fName && lName) ? fName + " " + lName : fullName;

          console.log(fName); // outputs email address or false
          console.log(lName); // outputs email address or false
          console.log(fullName); // outputs email address or false
          //return false;
          var page_views = _inbound.totalStorage('page_views') || {};
          var urlParams = _inbound.totalStorage('inbound_url_params') || {};

          var inboundDATA = {
            'email': email
          };
          /* Get Variation ID */
          if (typeof (landing_path_info) != "undefined") {
            var variation = landing_path_info.variation;
          } else if (typeof (cta_path_info) != "undefined") {
            var variation = cta_path_info.variation;
          } else {
            var variation = 0;
          }
          var post_type = inbound_settings.post_type || 'page';
          var page_id = inbound_settings.post_id || 0;
          // data['wp_lead_uid'] = jQuery.cookie("wp_lead_uid") || null;
          // data['search_data'] = JSON.stringify(jQuery.totalStorage('inbound_search')) || {};
          search_data = {};
          /* Filter here for raw */
          //alert(mapped_params);
          /**
           * Old data model
              var return_data = {
                        "action": 'inbound_store_lead',
                        "emailTo": data['email'],
                        "first_name": data['first_name'],
                        "last_name": data['last_name'],
                        "phone": data['phone'],
                        "address": data['address'],
                        "company_name": data['company'],
                        "page_views": data['page_views'],
                        "form_input_values": all_form_fields,
                        "Mapped_Data": mapped_form_data,
                        "Search_Data": data['search_data']
              };
           */
          formData = {
            'action': 'inbound_lead_store',
            'email': email,
            "full_name": fullName,
            "first_name": fName,
            "last_name": lName,
            'raw_params' : raw_params,
            'mapped_params' : mapped_params,
            'url_params': JSON.stringify(urlParams),
            'search_data': 'test',
            'page_views': JSON.stringify(page_views),
            'post_type': post_type,
            'page_id': page_id,
            'variation': variation,
            'source': utils.readCookie("inbound_referral_site")
          };
          callback = function(leadID){
            /* Action Example */

            _inbound.Events.after_form_submission(formData);
            alert('callback fired' + leadID);
            /* Set Lead cookie ID */
            utils.createCookie("wp_lead_id", leadID);
            _inbound.totalStorage.deleteItem('page_views'); // remove pageviews
            _inbound.totalStorage.deleteItem('tracking_events'); // remove events
            /* Resume normal form functionality */
            _inbound.Forms.releaseFormSubmit(form);

          }
          //_inbound.LeadsAPI.makeRequest(landing_path_info.admin_url);
          _inbound.Events.before_form_submission(formData);
          //_inbound.trigger('inbound_form_before_submission', formData, true);

          utils.ajaxPost(inbound_settings.admin_url, formData, callback);
      },
      rememberInputValues: function(input) {
          var name = ( input.name ) ? "inbound_" + input.name : '';
          var type = ( input.type ) ? input.type : 'text';
          if(type === 'submit' || type === 'hidden' || type === 'file' || type === "password") {
              return false;
          }

          utils.addListener(input, 'change', function(e) {

            if(e.target.name) {
                /* Check for input type */
                if(type !== "checkbox") {
                    var value = e.target.value;
                } else {
                  var values = [];
                  var checkboxes = document.querySelectorAll('input[name="'+e.target.name+'"]');
                    for (var i = 0; i < checkboxes.length; i++) {
                      var checked = checkboxes[i].checked;
                      if(checked){
                        values.push(checkboxes[i].value);
                      }
                      value = values.join(',');
                    };
                }
            console.log('change ' + e.target.name  + " " + encodeURIComponent(value));
            /* Set Field Input Cookies */
            utils.createCookie("inbound_" + e.target.name, encodeURIComponent(value));
            // _inbound.totalStorage('the_key', FormStore);
            /* Push to 'unsubmitted form object' */
            }

          });
      },
      fillInputValues: function(input){
          var name = ( input.name ) ? "inbound_" + input.name : '';
          var type = ( input.type ) ? input.type : 'text';
          if(type === 'submit' || type === 'hidden' || type === 'file' || type === "password") {
              return false;
          }
          if(utils.readCookie(name) && name != 'comment' ){

             value = decodeURIComponent(utils.readCookie(name));
             if(type === 'checkbox' || type === 'radio'){
                 var checkbox_vals = value.split(',');
                 for (var i = 0; i < checkbox_vals.length; i++) {
                      if (input.value.indexOf(checkbox_vals[i])>-1) {
                        input.checked = true;
                      }
                 }
             } else {
                if(value !== "undefined"){
                  input.value = value;
                }
             }
          }
      },
      /* Maps data attributes to fields on page load */
      mapField: function(input) {

            var input_id = input.id || false;
            var input_name = input.name || false;

            /* Loop through all match possiblities */
            for (i = 0; i < FieldMapArray.length; i++) {
              //for (var i = FieldMapArray.length - 1; i >= 0; i--) {
               var found = false;
               var match = FieldMapArray[i];
               var lookingFor = utils.trim(match);
               var nice_name = lookingFor.replace(/ /g,'_');

               this.debug('Names',function(){
                   console.log("NICE NAME", nice_name);
                   console.log('looking for match on ' + lookingFor);
               });

               /* look for name attribute match */
               if (input_name && input_name.toLowerCase().indexOf(lookingFor)>-1) {
                  var found = true;
                  this.debug('FOUND name attribute',function(){
                      console.warn('FOUND name: ' + lookingFor);
                  });

               /* look for id match */
               } else if (input_id && input_id.toLowerCase().indexOf(lookingFor)>-1) {
                  var found = true;

                  this.debug('FOUND id:',function(){
                      console.log('FOUND id: ' + lookingFor);
                  });

               /* Check siblings for label */
               } else if (label = this.siblingsIsLabel(input)) {

                  //var label = (label.length > 1 ? label[0] : label);
                  //console.log('label', label);
                  if (label[0].innerText.toLowerCase().indexOf(lookingFor)>-1) {
                      var found = true;

                      this.debug('Sibling matches single label',function(){
                          console.log('FOUND label text: ' + lookingFor);
                      });

                  }
                  /* Check closest li for label */
               } else if (labelText = this.CheckParentForLabel(input)) {

                  this.debug('li labels found in form',function(){
                    console.log(labelText)
                  });

                  if (labelText.toLowerCase().indexOf(lookingFor)>-1) {
                      var found = true;
                  }

               } else {

                  this.debug('NO MATCH',function(){
                      console.log('NO Match on ' + lookingFor + " in " + input_name);
                  });

                  no_match.push(lookingFor);

               }

              /* Map the field */
              if (found) {
                this.addDataAttr(input, nice_name);
                this.removeArrayItem(FieldMapArray, lookingFor);
                i--; //decrement count
              }

            }

            return inbound_data;

      },
      /* Get correct input values */
      getInputValue: function(input) {
                   var value = false;

                   switch (input.type) {
                       case 'radio':
                       case 'checkbox':
                           if (input.checked) {
                               value = input.value;
                               console.log("CHECKBOX VAL", value)
                           }
                           break;

                       case 'text':
                       case 'hidden':
                       default:
                           value = input.value;
                           break;

                   }

                   return value;
      },
      /* Add data-map-form-field attr to input */
      addDataAttr: function(formInput, match){

                      var getAllInputs = document.getElementsByName(formInput.name);
                      for (var i = getAllInputs.length - 1; i >= 0; i--) {
                          if(!formInput.dataset.mapFormField) {
                              getAllInputs[i].dataset.mapFormField = match;
                          }
                      };
      },
      /* Optimize FieldMapArray array for fewer lookups */
      removeArrayItem: function(array, item){
          if (array.indexOf) {
            index = array.indexOf(item);
          } else {
            for (index = array.length - 1; index >= 0; --index) {
              if (array[index] === item) {
                break;
              }
            }
          }
          if (index >= 0) {
            array.splice(index, 1);
          }
          console.log('removed ' + item + " from array");
          return;
      },
      /* Look for siblings that are form labels */
      siblingsIsLabel: function(input){
          var siblings = this.getSiblings(input);
          var labels = [];
          for (var i = siblings.length - 1; i >= 0; i--) {
              if(siblings[i].nodeName.toLowerCase() === 'label'){
                 labels.push(siblings[i]);
              }
          };
          /* if only 1 label */
          if (labels.length > 0 && labels.length < 2){
              return labels;
          }

         return false;
      },
      getChildren: function(n, skipMe){
          var r = [];
          var elem = null;
          for ( ; n; n = n.nextSibling )
             if ( n.nodeType == 1 && n != skipMe)
                r.push( n );
          return r;
      },
      getSiblings: function (n) {
          return this.getChildren(n.parentNode.firstChild, n);
      },
      /* Check parent elements inside form for labels */
      CheckParentForLabel: function(element) {
          if(element.nodeName === 'FORM') { return null; }
            do {
                  var labels = element.getElementsByTagName("label");
                  if (labels.length > 0 && labels.length < 2) {
                      return element.getElementsByTagName("label")[0].innerText;
                  }

            } while(element = element.parentNode);

            return null;
      }

  };

  return _inbound;

})(_inbound || {});