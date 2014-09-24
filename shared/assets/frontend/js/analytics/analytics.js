/**
 * Lead Tracking JS
 * http://www.inboundnow.com
 */
var inbound_data = inbound_data || {};
// Ensure global _gaq Google Analytics queue has been initialized.
var _gaq = _gaq || [];
var InboundAnalytics = (function () {

   var debugMode = false;

   var _privateMethod = function () {
      console.log('Run private');
   };

   var App = {
     init: function () {
          InboundAnalytics.Utils.init();
          InboundAnalytics.PageTracking.StorePageView();
          InboundAnalytics.Events.loadEvents();
     },
     /* Debugger Function toggled by var debugMode */
     debug: function(msg,callback){
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

 })();