/**
 * Lead Tracking JS
 * http://www.inboundnow.com
 * This is the main analytics entry point
 */

var inbound_data = inbound_data || {};
var _inboundOptions = _inboundOptions || {};
/* Ensure global _gaq Google Analytics queue has been initialized. */
var _gaq = _gaq || [];

var InboundAnalytics = (function (Options) {

   /* Constants */
   var debugMode = false;

   var App = {
     /* Initialize individual modules */
     init: function () {
         InboundAnalytics.Utils.init();
         InboundAnalytics.PageTracking.StorePageView();
         InboundAnalytics.Events.loadEvents();
         InboundAnalytics.Forms.init();
     },
     /* Debugger Function toggled by var debugMode */
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
     }
   };

  return App;

})(_inboundOptions);