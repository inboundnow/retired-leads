/**
 * Lead Tracking JS
 * http://www.inboundnow.com
 * This is the main analytics entry point
 */

var inbound_data = inbound_data || {};
var _inboundOptions = _inboundOptions || {};
/* Ensure global _gaq Google Analytics queue has been initialized. */
var _gaq = _gaq || [];

var _inboundOptions = {
  test: true,
}

var InboundAnalytics = (function (options) {
   /* Constants */
   var defaults = {
        debugMode : false
   };

   var App = {
     /* Initialize individual modules */
     init: function () {
         InboundAnalytics.Utils.init();
         InboundAnalytics.PageTracking.StorePageView();
         InboundAnalytics.Events.loadEvents(settings);
     },
     DomLoaded: function(){
        InboundAnalytics.Forms.init();
        setTimeout(function() {
             InboundAnalytics.Forms.init();
         }, 2000);
     },
     getSettings: function (defaults, options) {
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
     /**
      * Merge defaults with user options
      * @private
      * @param {Object} defaults Default settings
      * @param {Object} options User options
      * @returns {Object} Merged values of defaults and options
      */
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

   var settings = App.getSettings(defaults, options);
   /* Set globals */
   App.Settings = settings || {};

  return App;

})(_inboundOptions);