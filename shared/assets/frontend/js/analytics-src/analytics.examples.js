
/*
URL param action
 */
// Add to page
_inbound.add_action( 'url_parameters', URL_Param_Function, 10 );
// callback function
function URL_Param_Function(urlParams){

	//urlParams = _inbound.apply_filters( 'urlParamFilter', urlParams);

	for( var param in urlParams ) {
		var key = param;
		var value = urlParams[param];
	}

	//alert(JSON.stringify(urlParams));

	/* Check if URL parameter exists and matches value */
	if(urlParams.test === "true") {
		alert('url param true is true');
	}
}

/* Applying filters to your actions */
_inbound.add_filter( 'filter_url_parameters', URL_Param_Filter, 10 );
function URL_Param_Filter(urlParams) {

	var params = urlParams || {};
	/* check for item in object */
	if(params.utm_source !== "undefined"){
		//alert('its here');
	}
	/* delete item from object */
	delete params.utm_source;

	return params;

}

/* Applying filters to your actions */
_inbound.add_filter( 'filter_inbound_analytics_loaded', event_filter_data_example, 10);
function event_filter_data_example(data) {

	var data = data || {};

	/* Add property to data */
	data.add_this = 'additional data';

	/* check for item in object */
	if(data.opt1 === true){
		alert('options.opt1 = true');
	}

	/* Add or modifiy option to event */
	data.options.new_options = 'new option';

	/* delete item from data */
	delete data.utm_source;

	return data;

}

_inbound.add_action( 'tab_hidden', Tab_Hidden_Function, 10 );
function Tab_Hidden_Function(data){
	//alert('NOPE! LOOK AT ME!!!!');
}

_inbound.add_action( 'tab_visible', Tab_vis1_Function, 9 );
function Tab_vis1_Function(data){
	//alert('Welcome back bro 1');
}

_inbound.add_action( 'tab_visible', Tab_vis_Function, 10 );
function Tab_vis_Function(data){
	//alert('Welcome back bro 2');
}

