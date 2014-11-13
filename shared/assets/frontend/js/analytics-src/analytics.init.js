/**
 * # _inbound
 *
 * This main the _inbound class
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */

var inbound_data = inbound_data || {};
var _inboundOptions = _inboundOptions || {};
/* Ensure global _gaq Google Analytics queue has been initialized. */
var _gaq = _gaq || [];

var _inboundOptions = {
  test: true,
  //timeout: 10000
};

if(typeof wplft === "undefined"){
  /* load dummy data for testing */
  var url = JSON.stringify(window.location.origin);
  var wplft = {"post_id":"4","ip_address":"67.169.95.68","wp_lead_data":{"lead_id":null,"lead_email":"sondersbob@yahoo.com","lead_uid":"8SpCl1HIZihblvoJSsXrXKmTKTOLr3CI8cu"},"admin_url":url,"track_time":"2014\/11\/05 3:40:56","post_type":"page","page_tracking":"off","search_tracking":"on","comment_tracking":"on","custom_mapping":[]};
}

var _inbound = (function (options) {
   /* Constants */
   var defaults = {
        debugMode : false,
        timeout: 30000,
        formAutoTracking: true,
        formAutoPopulation: true
   };

   var Analytics = {
     /* Initialize individual modules */
     init: function () {
         _inbound.Utils.init();
         _inbound.PageTracking.init();
         _inbound.Events.loadEvents(settings);
     },
     DomLoaded: function(){
        /* run form mapping */
        _inbound.Forms.init();
        /* set URL params */
        _inbound.Utils.setUrlParams();

        _inbound.Events.loadOnReady();
        /* run form mapping for dynamically generated forms */
        setTimeout(function() {
             _inbound.Forms.init();
         }, 2000);
     },

     /**
      * Merge script defaults with user options
      * @private
      * @param {Object} defaults Default settings
      * @param {Object} options User options
      * @returns {Object} Merged values of defaults and options
      */
     extend: function (defaults, options) {
         var extended = {};
         var prop;
         for (prop in defaults) {
             if (Object.prototype.hasOwnProperty.call(defaults, prop)) {
                 extended[prop] = defaults[prop];
             }
         }
         for (prop in options) {
             if (Object.prototype.hasOwnProperty.call(options, prop)) {
                 extended[prop] = options[prop];
             }
         }
         return extended;
     },
     /* Debugger Function toggled by var debugMode */
     debug: function(msg, callback){
        //if app not in debug mode, exit immediately
        if(!settings.debugMode || !console){return};
        var msg = msg || false;
        //console.log the message
        if(msg && (typeof msg === 'string')){console.log(msg)};

        //execute the callback if one was passed-in
        if(callback && (callback instanceof Function)){
             callback();
        };
     }
   };

   var settings = Analytics.extend(defaults, options);
   /* Set globals */
   Analytics.Settings = settings || {};

  return Analytics;

})(_inboundOptions);