/* Console.log fix for old browsers */
(function() {
  if (!window.console) {
    window.console = {};
  }
  // union of Chrome, FF, IE, and Safari console methods
  var m = [
    "log", "info", "warn", "error", "debug", "trace", "dir", "group",
    "groupCollapsed", "groupEnd", "time", "timeEnd", "profile", "profileEnd",
    "dirxml", "assert", "count", "markTimeline", "timeStamp", "clear"
  ];
  // define undefined methods as noops to prevent errors
  for (var i = 0; i < m.length; i++) {
    if (!window.console[m[i]]) {
      window.console[m[i]] = function() {};
    }
  }
})();

/**
 * Lead Tracking JS
 * http://www.inboundnow.com
 */

var InboundAnalytics = (function () {

   var debugMode = false;

   var _privateMethod = function () {
      console.log('Run private');
   };


   var App = {
     SetUID:  function () {
      /* Set Lead UID */
      if(InboundAnalytics.Utils.readCookie("wp_lead_uid") === null) {
         var wp_lead_uid =  InboundAnalytics.Utils.CreateUID(35);
         InboundAnalytics.Utils.createCookie("wp_lead_uid", wp_lead_uid );
      }
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


var IA_PageViews = (function (InboundAnalytics) {

    InboundAnalytics.PageTracking = {
    init: function () {
          InboundAnalytics.PageTracking.StorePageView();
    },
    getPageViews: function () {
      var local_store = InboundAnalytics.Utils.checkLocalStorage();
        if(local_store){
          var page_views = localStorage.getItem("page_views"),
          local_object = JSON.parse(page_views);
          if (typeof local_object =='object' && local_object) {
            InboundAnalytics.PageTracking.StorePageView();

          }
          return local_object;
        }
    },
    StorePageView: function() {
          var timeout = InboundAnalytics.PageTracking.CheckTimeOut();
          var pageviewObj = jQuery.totalStorage('page_views');
          if(pageviewObj === null) {
            pageviewObj = {};
          }
          var current_page_id = wplft.post_id;
          var datetime = wplft.track_time;

          if (timeout) {

              // If pageviewObj exists, do this
              var page_seen = pageviewObj[current_page_id];

              if(typeof(page_seen) != "undefined" && page_seen !== null) {
                  pageviewObj[current_page_id].push(datetime);
              } else {
                  pageviewObj[current_page_id] = [];
                  pageviewObj[current_page_id].push(datetime);
              }

              jQuery.totalStorage('page_views', pageviewObj);
          }
    },
    CheckTimeOut: function() {
        var PageViews = jQuery.totalStorage('page_views');
        if(PageViews === null) {
          PageViews = {};
        }
        var page_id = wplft.post_id,
        pageviewTimeout = true, /* Default */
        page_seen = PageViews[page_id];
        if(typeof(page_seen) != "undefined" && page_seen !== null) {

            var time_now = wplft.track_time,
            vc = PageViews[page_id].length - 1,
            last_view = PageViews[page_id][vc],
            last_view_ms = new Date(last_view).getTime(),
            time_now_ms = new Date(time_now).getTime(),
            timeout_ms = last_view_ms + 30*1000,
            time_check = Math.abs(last_view_ms - time_now_ms),
            wait_time = 30000;

            InboundAnalytics.debug('Timeout Checks =',function(){
                 console.log('Current Time is: ' + time_now);
                 console.log('Last view is: ' + last_view);
                 console.log("Last view milliseconds " + last_view_ms);
                 console.log("time now milliseconds " + time_now_ms);
                 console.log("Wait Check: " + wait_time);
                 console.log("TIME CHECK: " + time_check);
            });

            //var wait_time = Math.abs(last_view_ms - timeout_ms) // output timeout time 30sec;

            if (time_check < wait_time){
              time_left =  Math.abs((wait_time - time_check)) * .001;
              pageviewTimeout = false;
              var status = '30 sec timeout not done: ' + time_left + " seconds left";
            } else {
              var status = 'Timeout Happened. Page view fired';
              InboundAnalytics.PageTracking.firePageView();
              pageviewTimeout = true;
            }

            InboundAnalytics.debug('',function(){
                 console.log(status);
            });
       }

       return pageviewTimeout;

    },
    firePageView: function() {
      var lead_id = InboundAnalytics.Utils.readCookie('wp_lead_id'),
      lead_uid = InboundAnalytics.Utils.readCookie('wp_lead_uid')
      if (typeof (lead_id) != "undefined" && lead_id != null && lead_id != "") {

        InboundAnalytics.debug('',function(){
             console.log('Run page view ajax');
        });
        jQuery.ajax({
              type: 'POST',
              url: wplft.admin_url,
              data: {
                action: 'wpl_track_user',
                wp_lead_uid: lead_uid,
                wp_lead_id: lead_id,
                page_id: wplft.post_id,
                current_url: window.location.href,
                json: '0'
              },
              success: function(user_id){
                console.log('Page View Saved');
              },
              error: function(MLHttpRequest, textStatus, errorThrown){
                  //alert(MLHttpRequest+' '+errorThrown+' '+textStatus);
                  //die();
              }
          });
      }
    }
  }

    return InboundAnalytics;

})(InboundAnalytics || {});


/**
 * Utility functions
 * @param  Object InboundAnalytics - Main JS object
 * @return Object - include util functions
 */
var IA_Utils = (function (InboundAnalytics) {

    InboundAnalytics.Utils =  {
      // Create cookie
      createCookie: function(name, value, days) {
          var expires = "";
          if (days) {
              var date = new Date();
              date.setTime(date.getTime()+(days*24*60*60*1000));
              expires = "; expires="+date.toGMTString();
          }
          document.cookie = name+"="+value+expires+"; path=/";
      },
      // Read cookie
      readCookie: function(name) {
          var nameEQ = name + "=";
          var ca = document.cookie.split(';');
          for(var i=0;i < ca.length;i++) {
              var c = ca[i];
              while (c.charAt(0) === ' ') {
                  c = c.substring(1,c.length);
              }
              if (c.indexOf(nameEQ) === 0) {
                  return c.substring(nameEQ.length,c.length);
              }
          }
          return null;
      },
      // Erase cookie
      eraseCookie: function(name) {
          createCookie(name,"",-1);
      },
      // Check local storage
      checkLocalStorage: function() {
        if ('localStorage' in window) {
            try {
              ls = (typeof window.localStorage === 'undefined') ? undefined : window.localStorage;
              if (typeof ls == 'undefined' || typeof window.JSON == 'undefined'){
                supported = false;
              } else {
                supported = true;
              }

            }
            catch (err){
              supported = false;
            }
        }
        return supported;
      },
      CreateUID: function(length) {
          var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split(''),
          str = '';
          if (! length) {
              length = Math.floor(Math.random() * chars.length);
          }
          for (var i = 0; i < length; i++) {
              str += chars[Math.floor(Math.random() * chars.length)];
          }
          return str;
      }

  };

  return InboundAnalytics;

})(InboundAnalytics || {});



InboundAnalytics.PageTracking.init();


/* run on ready */
jQuery(document).ready(function($) {

  //record non conversion status
  var wp_lead_uid = jQuery.cookie("wp_lead_uid");
  var wp_lead_id = jQuery.cookie("wp_lead_id");
  //var data_block = jQuery.parseJSON(trackObj);
  var json = 0;
  var page_id = inbound_ajax.page_id;
  //console.log(page_id);

// Page view trigging moved to /shared/tracking/page-tracking.js

// Check for Lead lists
var expired = jQuery.cookie("lead_session_list_check"); // check for session
if (expired != "true") {
  //var data_to_lookup = global-localized-vars;
  if (typeof (wp_lead_id) != "undefined" && wp_lead_id != null && wp_lead_id != "") {
    jQuery.ajax({
          type: 'POST',
          url: inbound_ajax.admin_url,
          data: {
            action: 'wpl_check_lists',
            wp_lead_id: wp_lead_id,

          },
          success: function(user_id){
              jQuery.cookie("lead_session_list_check", true, { path: '/', expires: 1 });
              console.log("Lists checked");
               },
          error: function(MLHttpRequest, textStatus, errorThrown){

            }

        });
    }
  }
/* end list check */

/* Set Expiration Date of Session Logging */
var e_date = new Date(); // Current date/time
var e_minutes = 30; // 30 minute timeout to reset sessions
e_date.setTime(e_date.getTime() + (e_minutes * 60 * 1000)); // Calc 30 minutes from now
jQuery.cookie("lead_session_expire", false, {expires: e_date, path: '/' }); // Set cookie on page loads
var expire_time = jQuery.cookie("lead_session_expire"); //
//console.log(expire_time);
var referrer_cookie = jQuery.cookie("wp_lead_referral_site");
if (typeof (referrer_cookie) === "undefined" || referrer_cookie === null || referrer_cookie === "") {
  var referrer = document.referrer || "NA";
  jQuery.cookie("wp_lead_referral_site", referrer, {expires: e_date, path: '/' }); // Set referral cookie
}

});