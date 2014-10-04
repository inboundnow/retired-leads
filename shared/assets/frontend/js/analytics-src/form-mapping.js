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

             // Main Loop
             for (var i = matchArray.length - 1; i >= 0; i--) {

                var match = matchArray[i].replace(" ",'_');
                console.log("Match name " + match);
                console.log("Input name " + input_name);
                 var clean_output = trim(match);
                 var nice_name = clean_output.replace(" ",'_');
                 var in_object_already = nice_name in inbound_data;
                 console.log('clean' + clean_output);

                 if (input_name && input_name.toLowerCase().indexOf(clean_output)>-1) {
                   //  Look for attr name match

                    console.log('match name: ' + clean_output);
                    if (!in_object_already) {
                     inbound_data[nice_name] = this_val;
                    }
                    //inbound_data.push('match name: ' + clean_output + ":" + this_val);

                 } else if (input_id && input_id.toLowerCase().indexOf(clean_output)>-1) {
                  // look for id match

                   console.log('match id: ' + clean_output);
                   if (!in_object_already) {
                    inbound_data[nice_name] = this_val;
                   }
                    //inbound_data.push('match id: ' + clean_output + ":" + this_val);

                 } else {

                  console.log('Need additional mapping data');
                  no_match.push(clean_output + ":" + this_val);

                 }
             }



         console.log(inbound_data);
         console.log(serialize(inbound_data));
         console.log(no_match);
         return inbound_data;

};
if ('classList' in document.documentElement) { var findClasses = true; } else { var findClasses = false; }

for (var i=0; i < form.elements.length; i++) {

                formInput = form.elements[i];
                multiple = false;

                if (formInput.name) {

                    inputName = formInput.name.replace(/\[([^\[]*)\]/g, "_$1");
                    inputName = inputName.replace(/-/g, "_");
                    if (!inputsObject[inputName]) { inputsObject[inputName] = {}; }

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
                        if (findClasses) {
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
            var matchCommon = /name|first name|last name|email|e-mail|phone|website|job title|company|tele|address|comment/;
            for (var input in inputsObject) {

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