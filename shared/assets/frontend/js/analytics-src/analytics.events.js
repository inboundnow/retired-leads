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

    /**
     *
     * Actions and filters List
     * addAction( 'namespace.identifier', callback, priority )
     * addFilter( 'namespace.identifier', callback, priority )
     * removeAction( 'namespace.identifier' )
     * removeFilter( 'namespace.identifier' )
     * doAction( 'namespace.identifier', arg1, arg2, moreArgs, finalArg )
     * applyFilters( 'namespace.identifier', content )
     * @return {[type]} [description]
     */
    _inbound.trigger = function(){

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

    var universalGA,
        classicGA,
        googleTagManager;

    _inbound.Events =  {
      // Create cookie
      loadEvents: function() {
         // this.analyticsLoaded();

      },
      loadOnReady: function(){

        //_inbound.Events.fireEvent('inbound_analytics_loaded', data, ops);
        _inbound.Events.analyticsLoaded();
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
       * The Event `data` can be filtered before events are triggered
       *
       * @param  {string} eventName Name of the event
       * @param  {object} data      Data passed to external functions/triggers
       * @param  {object} options   Options for configuring events
       * @return {null}           Nothing returned
       */
      fireEvent: function(eventName, data, options){
          var data = data || {};
          data.options = options || {};

          /* defaults for JS dispatch event */
          data.options.bubbles = data.options.bubbles || true,
          data.options.cancelable = data.options.cancelable || true;

          /* Customize Data via filter_ + "namespace" */
          data = _inbound.apply_filters( 'filter_'+ eventName, data);

          var TriggerEvent = new CustomEvent(eventName, {
              detail: data,
              bubbles: data.options.bubbles,
              cancelable: data.options.cancelable
            }
          );

        // console.log('Action:' + eventName + " ran on ->", data);
         /**
          *  1. Trigger Pure Javascript Event
          *
          *  See: https://developer.mozilla.org/en-US/docs/Web/Guide/Events/Creating_and_triggering_events
          *  for example on creating events
          */
         window.dispatchEvent(TriggerEvent);
         /**
          *   2. Trigger _inbound action
          */
         _inbound.do_action(eventName, data);
         /**
          *   3. jQuery trigger
          */
         this.triggerJQueryEvent(eventName, data);

         if(_inbound.Settings.track){
            //analytics.track('Registered', data); segment example
            /* Sending events to GA
                sendEvent = function (time) {

                  if (googleTagManager) {

                    dataLayer.push({'event':'Riveted', 'eventCategory':'Riveted', 'eventAction': 'Time Spent', 'eventLabel': time, 'eventValue': reportInterval, 'eventNonInteraction': nonInteraction});

                  } else {

                    if (universalGA) {
                      ga('send', 'event', 'Riveted', 'Time Spent', time.toString(), reportInterval, {'nonInteraction': nonInteraction});
                    }

                    if (classicGA) {
                      _gaq.push(['_trackEvent', 'Riveted', 'Time Spent', time.toString(), reportInterval, nonInteraction]);
                    }

                  }

                };
             */
         }
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
          var ops = { 'opt1': true };
          var data = {'data': 'xyxy'};
          this.fireEvent('inbound_analytics_loaded', data, ops);
      },
      analyticsTriggered: function() {
          var triggered = new CustomEvent("inbound_analytics_triggered");
          window.dispatchEvent(triggered);
      },
      analyticsSaved: function() {
          var page_view_saved = new CustomEvent("inbound_analytics_saved");
          window.dispatchEvent(page_view_saved);
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
          //console.log('Tab Hidden');
          this.fireEvent('tab_hidden');
      },
      browserTabVisible: function() {
          //console.log('Tab Visible');
          this.fireEvent('tab_visible');
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