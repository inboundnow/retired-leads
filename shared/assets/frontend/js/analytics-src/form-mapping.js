var form = window.document.forms[0];
var inputsObject = {};
var rawParams = [];
var mappedParams = [];
var getInputValue = function(input) {
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

};
var trim = function(s) {
          s = s.replace(/(^\s*)|(\s*$)/gi,"");
          s = s.replace(/[ ]{2,}/gi," ");
          s = s.replace(/\n /,"\n"); return s;
};

var serialize = function(obj, prefix) {
  var str = [];
  for(var p in obj) {
    if (obj.hasOwnProperty(p)) {
      var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
      str.push(typeof v == "object" ?
        serialize(v, k) :
        encodeURIComponent(k) + "=" + encodeURIComponent(v));
    }
  }
  return str.join("&");
}

var ClosestLabel = function(element, tagname) {
    if(element.nodeName === 'FORM') { return null; }

      tagname = tagname.toLowerCase();
      do {
         if(element.nodeName.toLowerCase() === tagname){
            var LabelExists = element.getElementsByTagName("label").length>0;
            var labelText = (LabelExists ? element.getElementsByTagName("label")[0].innerText : false);
            return labelText;
         }

      } while(element = element.parentNode);

      return null;
};
var Closest = function(element, tagname) {
    if(element.nodeName === 'FORM') { return null; }

      tagname = tagname.toLowerCase();
      do {
         if(element.nodeName.toLowerCase() === tagname){
            return element;
         }

      } while(element = element.parentNode);

      return null;
};
/* make visible inputs first in loop */
var sortInputs = function(obj) {
  var visibleInputs = [],
  hiddenInputs = [],
  temp_obj = {};

  for (var key in obj) {
    if (obj.hasOwnProperty(key)) {
        if(obj[key].type !== "hidden"){
            visibleInputs.push(key);
        } else {
            hiddenInputs.push(key);
        }
    }
  }

  var merged = hiddenInputs.concat(visibleInputs.reverse()); // Merges both arrays
  for (var i = merged.length - 1; i >= 0; i--) {
     temp_obj[merged[i]] = obj[merged[i]];
  };

  return temp_obj;
};
//var inbound_data = MapInput( this_input );
inbound_data = {};
no_match = [];
var MapInput = function (inputObject) {

        var matchArray = [  "name",
                            "first name",
                            "last name",
                            "email",
                            "e-mail",
                            "phone",
                            "website",
                            "job title",
                            "your_favorite_food_",
                            "company",
                            "tele",
                            "address",
                            "comment"];
         //var body = jQuery("body");
         var input_id = inputObject.id || false;
         var input_name = inputObject.name || false;
         var this_val = inputObject['value'];
         var input = inputObject['input'];
             // Main Loop
             for (var i = matchArray.length - 1; i >= 0; i--) {

                var match = matchArray[i];
                //console.log("Match name " + match);
                //console.log("Input name " + input_name);
                 var lookingFor = trim(match);
                 var nice_name = lookingFor.replace(" ",'_');
                 var in_object_already = nice_name in inbound_data;
                 console.log('looking for: ' + lookingFor);

                 if (input_name && input_name.toLowerCase().indexOf(lookingFor)>-1) {
                   //  Look for attr name match

                   console.warn('match name: ' + lookingFor);
                    if (!in_object_already) {
                     inbound_data[nice_name] = this_val;
                    }
                    //inbound_data.push('match name: ' + lookingFor + ":" + this_val);

                 } else if (input_id && input_id.toLowerCase().indexOf(lookingFor)>-1) {
                  // look for id match
                    console.warn("input labels found in form");
                   console.log('match id: ' + lookingFor);
                   if (!in_object_already) {
                    inbound_data[nice_name] = this_val;
                   }
                    //inbound_data.push('match id: ' + lookingFor + ":" + this_val);

                 } else if (labelText = ClosestLabel(input, "li")) {
                    console.warn("li labels found in form");
                    if (labelText.toLowerCase().indexOf(lookingFor)>-1) {
                        console.log('match label text: ' + lookingFor);
                        if (!in_object_already) {
                         inbound_data[nice_name] = this_val;
                        }
                    }
                 } else if (labelText = ClosestLabel(input, "div")) {
                    console.warn("div labels found in form");
                   if (labelText.toLowerCase().indexOf(lookingFor)>-1) {
                       console.log('match label text: ' + lookingFor);
                       if (!in_object_already) {
                        inbound_data[nice_name] = this_val;
                       }
                   }

                 } else if (labelText = ClosestLabel(input, "p")) {
                    console.warn("P labels found in form");
                    if (labelText.toLowerCase().indexOf(lookingFor)>-1) {
                         console.log('match label text: ' + lookingFor);
                         if (!in_object_already) {
                          inbound_data[nice_name] = this_val;
                         }
                     }

                 } else {
                    console.warn("No matches");

                  console.log('Need additional mapping data');
                  no_match.push(lookingFor + ":" + this_val);

                 }
             }

         console.log(inbound_data);
         console.log(serialize(inbound_data));
         console.log('no match here', no_match);
         return inbound_data;

};

for (var i=0; i < form.elements.length; i++) {

                formInput = form.elements[i];
                multiple = false;
                var parent = formInput.parentNode;
                var parent_parent = parent.parentNode;
                console.log("PARENT", parent);
                console.log("PARENT PARENT", parent_parent);

                if (formInput.name) {

                    inputName = formInput.name.replace(/\[([^\[]*)\]/g, "_$1");
                    inputName = inputName.replace(/-/g, "_");
                    if (!inputsObject[inputName]) { inputsObject[inputName] = {}; }
                    if (!inputsObject[inputName]['input']) { inputsObject[inputName]['input'] = formInput; };

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

                        if (formInput.type) { inputsObject[inputName]['type'] = formInput.type; }
                        if (formInput.id) { inputsObject[inputName]['id'] = formInput.id; }
                        if ('classList' in document.documentElement)  {
                            if (formInput.classList) { inputsObject[inputName]['class'] = formInput.classList; }
                        }
                        // inputsObject[inputName].push(multiple ? values.join(',') : encodeURIComponent(value));

                        if (!inputsObject[inputName]['value']) { inputsObject[inputName]['value'] = []; }
                        if (!inputsObject[inputName]['name']) { inputsObject[inputName]['name'] = inputName; }

                        inputsObject[inputName]['value'].push(multiple ? values.join(',') : encodeURIComponent(value));

                    }

                }

            }
            console.log(inputsObject);


            var inputsObject = sortInputs(inputsObject);

            var matchCommon = /name|first name|last name|email|e-mail|phone|website|job title|company|tele|address|comment/;
            for (var input in inputsObject) {
                console.log(input);

                var inputValue = inputsObject[input]['value'];

                //if (matchCommon.test(input) !== false) {
                    console.log(input + " Matches Regex run mapping test");
                    var map = MapInput(inputsObject[input]);
                    console.log("MAPP", map);
                    //mappedParams.push( input + '=' + inputsObject[input]['value'].join(',') );
                //}

                /* Add custom hook here to look for additional values */
                if (typeof (inputValue) != "undefined" && inputValue != null && inputValue != "") {
                    rawParams.push( input + '=' + inputsObject[input]['value'].join(',') );
                }
            }
            var raw_params = rawParams.join('&');
            console.log("Raw PARAMS", raw_params);
            /* Filter here for raw */
            var mapped_params =  JSON.stringify(map);
            console.log("Mapped PARAMS", mapped_params);
            /* Filter here for mapped */