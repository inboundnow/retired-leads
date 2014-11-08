/**
 * # Events
 *
 * These events are triggered as the visitor travels through the site
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */
/* todo all events live here

Simplify API for triggering

*/
// https://github.com/carldanley/WP-JS-Hooks/blob/master/src/event-manager.js
var _inboundEvents = (function (_inbound) {

    _inbound.trigger = function(){
        alert('triggr');
    };
    /*
    function log_event(category, action, label) {
      _gaq.push(['_trackEvent', category, action, label]);
    }

    function log_click(category, link) {
      log_event(category, 'Click', $(link).text());
    }

    $(document).ready(function() {
      $('nav a').click(function() {
        log_click('Navigation', this);
      });
    })

    window.addEventListener("inbound_analytics_page_revisit", page_seen_function, false);
    function page_seen_function(e){
        var view_count = e.detail.count;
        console.log("This page has been seen " + e.detail.count + " times");
        if(view_count > 10){
          console.log("Page has been viewed more than 10 times");
        }
    }
     */

    /*
    *  add_action
    *
    *  This function uses _inbound.hooks to mimics WP add_action
    *
    *  ```js
    *   function Inbound_Add_Action_Example(data) {
    *       // Do stuff here.
    *   };
    *   // Add action to the hook
    *   _inbound.add_action( 'name_of_action', Inbound_Add_Action_Example, 10 );
    *   ```
    */
    _inbound.add_action = function() {
     // allow multiple action parameters such as 'ready append'
     var actions = arguments[0].split(' ');

     for( k in actions ) {

       // prefix action
       arguments[0] = 'inbound.' + actions[ k ];

       _inbound.hooks.addAction.apply(this, arguments);
     }

     return this;

    };
    /*
    *  remove_action
    *
    *  This function uses _inbound.hooks to mimics WP remove_action
    *
    */
    _inbound.remove_action = function() {
     // prefix action
     arguments[0] = 'inbound.' + arguments[0];
     _inbound.hooks.removeAction.apply(this, arguments);

     return this;

    };
    /*
    *  do_action
    *
    *  This function uses _inbound.hooks to mimics WP do_action
    *
    */
    _inbound.do_action = function() {
     // prefix action
     arguments[0] = 'inbound.' + arguments[0];
     _inbound.hooks.doAction.apply(this, arguments);

     return this;

    };
    /*
    *  add_filter
    *
    *  This function uses _inbound.hooks to mimics WP add_filter
    *
    */
    _inbound.add_filter = function() {
     // prefix action
     arguments[0] = 'inbound.' + arguments[0];
     _inbound.hooks.addFilter.apply(this, arguments);

     return this;

    };
    /*
    *  remove_filter
    *
    *  This function uses _inbound.hooks to mimics WP remove_filter
    *
    */
    _inbound.remove_filter = function() {
     // prefix action
     arguments[0] = 'inbound.' + arguments[0];

     _inbound.hooks.removeFilter.apply(this, arguments);

     return this;

    };
    /*
    *  apply_filters
    *
    *  This function uses _inbound.hooks to mimics WP apply_filters
    *
    */
    _inbound.apply_filters = function() {

     // prefix action
     arguments[0] = 'inbound.' + arguments[0];

     return _inbound.hooks.applyFilters.apply(this, arguments);

    };

    var universalGA,
        classicGA,
        googleTagManager;

    _inbound.Events =  {
      // Create cookie
      loadEvents: function() {
         // this.analyticsLoaded();
         _inbound.Events.fireEvent('inbound_analytics_loaded', 'test', true);
      },
      /**
       * Fires Analytics Events
       *
       * There are three options for firing events and they trigger in this order:
       * 1. Pure JS
       * 2. _inbound.add_action
       * 3. jQuery Trigger
       *
       * They trigger in that order.
       *
       * @param  {string} eventName Name of the event
       * @param  {object} data      Data passed to external functions/triggers
       * @param  {object} options   Options for configuring events
       * @return {null}           Nothing returned
       */
      fireEvent: function(eventName, data, options){
          // Raw Javascript Version - trigger custom function on page already seen
          var options = options || {};
          var bubbles = options.bubbles || true,
          cancelable = options.cancelable || true,
          data = data || {};

          var TriggerEvent = new CustomEvent(eventName, {
              detail: data,
              bubbles: bubbles,
              cancelable: cancelable
            }
          );

         /* 1. Raw JS window trigger */
         window.dispatchEvent(TriggerEvent);
         /* 2. Trigger _inbound action */
         _inbound.do_action(eventName, data);
         /* 3. jQuery trigger */
         this.triggerJQueryEvent(eventName, data);
      },
      /*
       * Determine which version of GA is being used
       * "ga", "_gaq", and "dataLayer" are the possible globals
       */
      checkTypeofGA: function() {
        if (typeof ga === "function") {
          universalGA = true;
        }

        if (typeof _gaq !== "undefined" && typeof _gaq.push === "function") {
          classicGA = true;
        }

        if (typeof dataLayer !== "undefined" && typeof dataLayer.push === "function") {
          googleTagManager = true;
        }

      },
      triggerJQueryEvent: function(eventName, data, fireOnce){
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
          _inbound.hooks.doAction( 'inbound.page_view');
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

  return _inbound;

})(_inbound || {});