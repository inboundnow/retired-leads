
/*
URL param action
 */
// Add to page
_inbound.add_action( 'url_params', URL_Param_Function, 10 );
// callback function
function URL_Param_Function(urlParams){

	urlParams = _inbound.apply_filters( 'urlParamFilter', urlParams);

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
_inbound.add_filter( 'urlParamFilter', URL_Param_Filter, 10 );
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