/**
 * Event functions
 * @param  Object InboundAnalytics - Main JS object
 * @return Object - include event triggers
 */
// https://github.com/carldanley/WP-JS-Hooks/blob/master/src/event-manager.js
var InboundAnalyticsEvents = (function (InboundAnalytics) {

    InboundAnalytics.Events =  {
      // Create cookie
      loadEvents: function() {
          this.analyticsLoaded();
      },
      triggerJQueryEvent: function(eventName, data){
        if (window.jQuery) {
            var data = data || {};
            jQuery(document).trigger(eventName, data);
           /* var something = (function() {
                var executed = false;
                return function () {
                    if (!executed) {
                        executed = true;
                        console.log(eventName + " RAN");

                    }
                };
            })();*/
        }
      },
      analyticsLoaded: function() {
          var eventName = "inbound_analytics_loaded";
          var loaded = new CustomEvent(eventName);
          window.dispatchEvent(loaded);
          this.triggerJQueryEvent(eventName);
      },
      analyticsTriggered: function() {
          var triggered = new CustomEvent("inbound_analytics_triggered");
          window.dispatchEvent(triggered);
      },
      analyticsSaved: function() {
          var page_view_saved = new CustomEvent("inbound_analytics_saved");
          window.dispatchEvent(page_view_saved);
          console.log('Page View Saved');
      },
      analyticsError: function(MLHttpRequest, textStatus, errorThrown) {
          var error = new CustomEvent("inbound_analytics_error", {
            detail: {
              MLHttpRequest: MLHttpRequest,
              textStatus: textStatus,
              errorThrown: errorThrown
            }
          });
          window.dispatchEvent(error);
          console.log('Page Save Error');
      },
      pageFirstView: function(page_seen_count) {
          var page_first_view = new CustomEvent("inbound_analytics_page_first_view", {
              detail: {
                count: 1,
                time: new Date(),
              },
              bubbles: true,
              cancelable: true
            }
          );
          window.dispatchEvent(page_first_view);

          console.log('First Ever Page View of this Page');
      },
      pageRevisit: function(page_seen_count) {
          var eventName = "inbound_analytics_page_revisit";
          var data = { count: page_seen_count,
                       time: new Date()
                     };
          var page_revisit = new CustomEvent(eventName, {
              detail: data,
              bubbles: true,
              cancelable: true
            }
          );
          window.dispatchEvent(page_revisit);
          this.triggerJQueryEvent(eventName, data);
          console.log('Page Revisit');
      },
      /* get idle times https://github.com/robflaherty/riveted/blob/master/riveted.js */
      browserTabHidden: function() {
        /* http://www.thefutureoftheweb.com/demo/2007-05-16-detect-browser-window-focus/ */
          var eventName = "inbound_analytics_tab_hidden";
          var tab_hidden = new CustomEvent(eventName);
          window.dispatchEvent(tab_hidden);
          console.log('Tab Hidden');
          this.triggerJQueryEvent(eventName);
      },
      browserTabVisible: function() {
        var eventName = "inbound_analytics_tab_visible";
        var tab_visible = new CustomEvent(eventName);
        window.dispatchEvent(tab_visible);
        console.log('Tab Visible');
        this.triggerJQueryEvent(eventName);
      },
      /* Scrol depth https://github.com/robflaherty/jquery-scrolldepth/blob/master/jquery.scrolldepth.js */
      sessionStart: function() {
          var session_start = new CustomEvent("inbound_analytics_session_start");
          window.dispatchEvent(session_start);
          console.log('Session Start');
      },
      sessionActive: function() {
          var session_active = new CustomEvent("inbound_analytics_session_active");
          window.dispatchEvent(session_active);
          console.log('Session Active');
      },

  };

  return InboundAnalytics;

})(InboundAnalytics || {});