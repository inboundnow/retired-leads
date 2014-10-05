/**
 * Form functions
 * @param  Object InboundAnalytics - Form tracking functionality
 * @return Object - form functions
 */
var InboundForms = (function (InboundAnalytics) {

    InboundAnalytics.Forms =  {
      // Init Form functions
      init: function() {
          this.attachFormSubmitEvent();
      },
      formLoop: function(){
          for(var i=0; i<window.document.forms.length; i++){
            var form = window.document.forms[i];
            var trackForm = InboundAnalytics.Utils.hasClass("wpl-track-me", form);
            if (trackForm) {
              this.attachFormSubmitEvent(form); /* attach form listener */

            }
          }
      },
      formSubmit: function(form) {

      },
      mapFormValues: function(form) {
              var inputByName = {};
              var params = [];
              /* test for [] array syntax */
              var fieldNameExp = /\[([^\[]*)\]/g;
              for (var i=0; i < form.elements.length; i++) {

                formField = form.elements[i];
                multiple = false;

                if (formField.name) {
                    /* test for [] array syntax */
                    cleanName = formField.name.replace(fieldNameExp, "_$1");
                    if (!inputByName[cleanName]) { inputByName[cleanName] = []; }

                    switch (formField.nodeName) {

                        case 'INPUT':
                            value = this.getInputValue(formField);
                            console.log(value);
                            if (value === false) { continue; }
                            break;

                        case 'SELECT':
                            if (formField.multiple) {
                                values = [];
                                multiple = true;

                                for (var j = 0; j < formField.length; j++) {
                                    if (formField[j].selected) {
                                        values.push(encodeURIComponent(formField[j].value));
                                    }
                                }

                            } else {
                                value = (formField.value);
                            }
                            break;

                        case 'TEXTAREA':
                            value = formField.value;
                            break;

                    }

                    if (value) {
                        inputByName[cleanName].push(multiple ? values.join(',') : encodeURIComponent(value));
                    }

                }

            }
            var matchCommon = /name|first name|last name|email|e-mail|phone|website|job title|company|tele|address|comment/;
            for (var inputName in inputByName) {
                 if (matchCommon.test(inputName) !== false) {
                    console.log(inputName + " Matches Regex");
                    /* run mapping loop only for the matches here */
                 }
                 params.push( inputName + '=' + inputByName[inputName].join(',') );
            }
            var final_params = params.join('&');
            console.log(final_params);
      },
      getInputValue = function(input) {
             var value = false;

             switch (input.type) {
                 case 'radio':
                 case 'checkbox':
                     if (input.checked) {
                         value = input.value;
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
      /*
      inbound_form_classes: function(forms, functionName, classes) {
        jQuery.each(forms, function(index, id) {
          var selector = jQuery.trim(id);
          for (var this_class in classes) {
            if (selector.indexOf('#')>-1) {
              jQuery(selector)[functionName](classes[this_class]);
              //console.log(selector);
            } else if (selector.indexOf('.')>-1) {
              jQuery(selector)[functionName](classes[this_class]);
            } else {
              jQuery("#" + selector)[functionName](classes[this_class]);
            }
          }

        });
      }*/
      /* Add tracking class to forms */
      attachFormSubmitEvent: function (form) {

            console.log("The Form has the class wpl-track-me", hasClass);
            InboundAnalytics.Utils.addListener(form, 'submit', InboundAnalytics.LeadsAPI.formSubmit );

      },


  };

  return InboundAnalytics;

})(InboundAnalytics || {});