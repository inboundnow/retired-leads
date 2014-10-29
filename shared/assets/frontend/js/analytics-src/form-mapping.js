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

/* dupe function */
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
function getChildren(n, skipMe){
    var r = [];
    var elem = null;
    for ( ; n; n = n.nextSibling )
       if ( n.nodeType == 1 && n != skipMe)
          r.push( n );
    return r;
};

var getSiblings = function (n) {
    return getChildren(n.parentNode.firstChild, n);
}
var siblingsIsLabel = function(input){
    var siblings = getSiblings(input);
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
}
var CheckParentForLabel = function(element) {
    if(element.nodeName === 'FORM') { return null; }

      do {
            var labels = element.getElementsByTagName("label");
            if (labels.length > 0 && labels.length < 2){
                return element.getElementsByTagName("label")[0].innerText;
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
                    "comment"
];
var removeArrayItem = function(array, item){
    if (array.indexOf) {
      index = array.indexOf(item);
    }
    else {
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
}
var map_field = function(inputObject) {


                //var body = jQuery("body");
                var input_id = inputObject.id || false;
                var input_name = inputObject.name || false;
                var input_val = inputObject['value'];

                // Main Loop
                for (i = 0; i < matchArray.length; i++) {
                //for (var i = matchArray.length - 1; i >= 0; i--) {
                 var match = matchArray[i];
                 var lookingFor = trim(match);
                 var nice_name = lookingFor.replace(" ",'_');
                 var in_object_already = nice_name in inbound_data;
                 //console.log('looking for match on ' + lookingFor);

                 if (input_name && input_name.toLowerCase().indexOf(lookingFor)>-1) {
                   //  Look for attr name match
                   console.warn('FOUND name: ' + lookingFor);
                    if (!in_object_already) {
                     inbound_data[nice_name] = input_val;
                    }
                    removeArrayItem(matchArray, lookingFor);
                    i--; //decrement
                    //inbound_data.push('match name: ' + lookingFor + ":" + input_val);

                 } else if (input_id && input_id.toLowerCase().indexOf(lookingFor)>-1) {
                  // look for id match
                    console.warn("input labels found in form");
                   console.log('FOUND id: ' + lookingFor);
                   if (!in_object_already) {
                    inbound_data[nice_name] = input_val;
                   }
                   removeArrayItem(matchArray, lookingFor);
                   i--; //decrement
                 /* Check siblings for label */
                 } else if (label = siblingsIsLabel(input)) {
                    console.warn('Sibling matches single label');

                    if (label[0].innerText.toLowerCase().indexOf(lookingFor)>-1) {
                        console.log('FOUND label text: ' + lookingFor);
                        if (!in_object_already) {
                         inbound_data[nice_name] = input_val;
                        }
                        removeArrayItem(matchArray, lookingFor);
                        i--; //decrement
                    }
                    /* Check closest li for label */
                 } else if (labelText = CheckParentForLabel(input)) {
                    console.warn("li labels found in form");
                    console.log(labelText)
                    if (labelText.toLowerCase().indexOf(lookingFor)>-1) {
                        console.log('FOUND label text: ' + lookingFor);
                        if (!in_object_already) {
                         inbound_data[nice_name] = input_val;
                        }
                        removeArrayItem(matchArray, lookingFor);
                        i--; //decrement
                    }

                 } else {

                  console.log('NO Match on ' + lookingFor + " in " + input_name+" : " + input_val);
                  no_match.push(lookingFor + ":" + input_val);

                 }
                }

                return inbound_data;

};
var addDataAttr = function(formInput){
                    formInput.dataset.mapFormField = 'x';
};
/* Map field fields on load */
var MapOnLoad = function() {
                var form = window.document.forms[0];
                var visibleInputs = [],
                hiddenInputs = [];

                for (var i=0; i < form.elements.length; i++) {

                    formInput = form.elements[i];
                    if (formInput.type === 'hidden') {
                        hiddenInputs.push(formInput);
                        continue;
                    }

                    console.log('not hidden', formInput)
                    map_field(formInput);
                }

                for (var i = hiddenInputs.length - 1; i >= 0; i--) {
                    formInput = hiddenInputs[i];
                    console.log('hidden', formInput)
                };
};
//MapOnLoad();
var MapInput = function (inputObject) {


                //var body = jQuery("body");
                var input_id = inputObject.id || false;
                var input_name = inputObject.name || false;
                var this_val = inputObject['value'];
                var input = inputObject['input'];


                // Main Loop
                for (i = 0; i < matchArray.length; i++) {
                //for (var i = matchArray.length - 1; i >= 0; i--) {
                 console.log(matchArray);
                 var match = matchArray[i];
                 var lookingFor = trim(match);
                 var nice_name = lookingFor.replace(" ",'_');
                 var in_object_already = nice_name in inbound_data;
                 //console.log('looking for match on ' + lookingFor);

                 if (input_name && input_name.toLowerCase().indexOf(lookingFor)>-1) {
                   //  Look for attr name match

                   console.warn('FOUND name: ' + lookingFor);
                    if (!in_object_already) {
                     inbound_data[nice_name] = this_val;
                    }
                    removeArrayItem(matchArray, lookingFor);
                    i--; //decrement
                    //inbound_data.push('match name: ' + lookingFor + ":" + this_val);

                 } else if (input_id && input_id.toLowerCase().indexOf(lookingFor)>-1) {
                  // look for id match
                    console.warn("input labels found in form");
                   console.log('FOUND id: ' + lookingFor);
                   if (!in_object_already) {
                    inbound_data[nice_name] = this_val;
                   }
                   removeArrayItem(matchArray, lookingFor);
                   i--; //decrement
                 /* Check siblings for label */
                 } else if (label = siblingsIsLabel(input)) {
                    console.warn('Sibling matches single label');

                    if (label[0].innerText.toLowerCase().indexOf(lookingFor)>-1) {
                        console.log('FOUND label text: ' + lookingFor);
                        if (!in_object_already) {
                         inbound_data[nice_name] = this_val;
                        }
                        removeArrayItem(matchArray, lookingFor);
                        i--; //decrement
                    }
                    /* Check closest li for label */
                 } else if (labelText = CheckParentForLabel(input)) {
                    console.warn("li labels found in form");
                    console.log(labelText)
                    if (labelText.toLowerCase().indexOf(lookingFor)>-1) {
                        console.log('FOUND label text: ' + lookingFor);
                        if (!in_object_already) {
                         inbound_data[nice_name] = this_val;
                        }
                        removeArrayItem(matchArray, lookingFor);
                        i--; //decrement
                    }

                 } else {

                  console.log('NO Match on ' + lookingFor + " in " + input_name+" : " + this_val);
                  no_match.push(lookingFor + ":" + this_val);

                 }
                }

                //console.log(inbound_data);
                //console.log(serialize(inbound_data));
                //console.log('no match here', no_match);
                return inbound_data;

};

        for (var i=0; i < form.elements.length; i++) {

                formInput = form.elements[i];
                multiple = false;

                if (formInput.name) {

                    inputName = formInput.name.replace(/\[([^\[]*)\]/g, "_$1");
                    inputName = inputName.replace(/-/g, "_");
                    if (!inputsObject[inputName]) { inputsObject[inputName] = {}; }
                    if (!inputsObject[inputName]['input']) { inputsObject[inputName]['input'] = formInput; };
                    if (!inputsObject[inputName]['name']) { inputsObject[inputName]['name'] = inputName; }

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

                        inputsObject[inputName]['value'].push(multiple ? values.join(',') : encodeURIComponent(value));

                    }

                }

            }
            console.log(inputsObject);


            var inputsObject = sortInputs(inputsObject);

            var matchCommon = /name|first name|last name|email|e-mail|phone|website|job title|company|tele|address|comment/;
            for (var input in inputsObject) {
                //console.log(input);

                var inputValue = inputsObject[input]['value'];

                //if (matchCommon.test(input) !== false) {
                    //console.log(input + " Matches Regex run mapping test");
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