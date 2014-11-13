/**
 * # Analytics Events
 *
 * Events are triggered throughout the visitors journey through the site. See more on [Inbound Now][in]
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 *
 * [in]: http://www.inboundnow.com/
 */

// https://github.com/carldanley/WP-JS-Hooks/blob/master/src/event-manager.js
var _inboundEvents = (function (_inbound) {


    _inbound.trigger = function(trigger, data){
        _inbound.Events[trigger](data);
    };
    /*!
    function log_event(category, action, label) {
      _gaq.push(['_trackEvent', category, action, label]);
    }

    function log_click(category, link) {
      log_event(category, 'Click', $(link).text());
    }
    */

    /*!
     *
     * Private Function that Fires & Emits Events
     *
     * There are three options for firing events and they trigger in this order:
     *
     * 1. Vanilla JS dispatch event
     * 2. `_inbound.add_action('namespace', callback, priority)`
     * 3. jQuery Trigger `jQuery.trigger('namespace', callback);`
     *
     * The Event `data` can be filtered before events are triggered
     * with filters. Example: filter_ + "namespace"
     *
     * ```js
     * // Filter Form Data before submissionsz
     * _inbound.add_filter( 'filter_before_form_submission', event_filter_data_example, 10);
     *
     * function event_filter_data_example(data) {
     *     var data = data || {};
     *     // Do something with data
     *     return data;
     * }
     * ```
     *
     * @param  {string} eventName Name of the event
     * @param  {object} data      Data passed to external functions/triggers
     * @param  {object} options   Options for configuring events
     * @return {null}           Nothing returned
     */
     function fireEvent(eventName, data, options){
        var data = data || {};
        options = options || {};
        //console.log(eventName);
        //console.log(data);
        /*! defaults for JS dispatch event */
        options.bubbles = options.bubbles || true,
        options.cancelable = options.cancelable || true;

        /*! Customize Data via filter_ + "namespace" */
        data = _inbound.apply_filters( 'filter_'+ eventName, data);

        var TriggerEvent = new CustomEvent(eventName, {
            detail: data,
            bubbles: options.bubbles,
            cancelable: options.cancelable
          }
        );

      // console.log('Action:' + eventName + " ran on ->", data);

       /*! 1. Trigger Pure Javascript Event See: https://developer.mozilla.org/en-US/docs/Web/Guide/Events/Creating_and_triggering_events for example on creating events */
       window.dispatchEvent(TriggerEvent);
       /*!  2. Trigger _inbound action  */
       _inbound.do_action(eventName, data);
       /*!  3. jQuery trigger   */
       triggerJQueryEvent(eventName, data);

    }

    function triggerJQueryEvent(eventName, data){
      if (window.jQuery) {
          var data = data || {};
          jQuery(document).trigger(eventName, data);
      }
    };

    var universalGA,
        classicGA,
        googleTagManager;

    _inbound.Events =  {
      // Create cookie
      loadEvents: function() {
         // this.analyticsLoaded();

      },
      loadOnReady: function(){
            _inbound.Events.analyticsLoaded();
      },
      /* # Event Usage */

      /**
       * Adding Custom Actions
       * ------------------
       * You can hook into custom events throughout analytics. See the full list of available [events below](#all-events)
       *
       * `
       * _inbound.add_action( 'action_name', callback, priority );
       * `
       *
       * ```js
       * // example:
       *
       * _inbound.add_action( 'page_visit', callback, 10 );
       *
       * // add custom callback
       * function callback(data){
       *   // run callback on 'page_visit' trigger
       * }
       * ```
       *
       * @param  {string} action_name Name of the event trigger
       * @param  {function} callback  function to trigger when event happens
       * @param  {int} priority   Order to trigger the event in
       *
       */

      /**
       * Removing Custom Actions
       * ------------------
       * You can hook into custom events throughout analytics. See the full list of available [events below](#all-events)
       *
       * `
       * _inbound.remove_action( 'action_name');
       * `
       *
       * ```js
       * // example:
       *
       * _inbound.remove_action( 'page_visit');
       * // all 'page_visit' actions have been deregistered
       * ```
       *
       * @param  {string} action_name Name of the event trigger
       *
       */

      /**
       * # Event List
       *
       * Events are triggered throughout the visitors journey through the site
       */

      /**
       * Triggers when the browser url params are parsed. You can perform custom actions
       * if specific url params exist.
       */
      analytics_loaded: function() {
          var ops = { 'opt1': true };
          var data = {'data': 'xyxy'};
          fireEvent('analytics_loaded', data, ops);
      },
      /**
       *  Triggers when the browser url params are parsed. You can perform custom actions
       *  if specific url params exist.
       *
       * ```js
       * // Usage:
       *
       * // Add function to 'url_parameters' event
       * _inbound.add_action( 'url_parameters', url_parameters_func_example, 10);
       *
       * function url_parameters_func_example(urlParams) {
       *     var urlParams = urlParams || {};
       *      for( var param in urlParams ) {
       *      var key = param;
       *      var value = urlParams[param];
       *      }
       *      // All URL Params
       *      alert(JSON.stringify(urlParams));
       *
       *      // Check if URL parameter `utm_source` exists and matches value
       *      if(urlParams.utm_source === "twitter") {
       *        alert('This person is from twitter!');
       *      }
       * }
       * ```
       */
      url_parameters: function(data){
          fireEvent('url_parameters', data);
      },
      /**
       *  Triggers when session starts
       *
       * ```js
       * // Usage:
       *
       * // Add session_start_func_example function to 'session_start' event
       * _inbound.add_action( 'session_start', session_start_func_example, 10);
       *
       * function session_start_func_example(data) {
       *     var data = data || {};
       *     // session active
       * }
       * ```
       */
      session_start: function() {
          console.log('Session Start');
          fireEvent('session_start');
      },
      /**
       *  Triggers when session is already active
       *
       * ```js
       * // Usage:
       *
       * // Add session_heartbeat_func_example function to 'session_heartbeat' event
       * _inbound.add_action( 'session_heartbeat', session_heartbeat_func_example, 10);
       *
       * function session_heartbeat_func_example(data) {
       *     var data = data || {};
       *     // Do something with every 10 seconds
       * }
       * ```
       */
      session_active: function() {
          fireEvent('session_active');
          console.log('Session Active');
      },
      /**
       *  Session emitter. Runs every 10 seconds. This is a useful function for
       *  pinging third party services
       *
       * ```js
       * // Usage:
       *
       * // Add session_heartbeat_func_example function to 'session_heartbeat' event
       * _inbound.add_action( 'session_heartbeat', session_heartbeat_func_example, 10);
       *
       * function session_heartbeat_func_example(data) {
       *     var data = data || {};
       *     // Do something with every 10 seconds
       * }
       * ```
       */
      session_heartbeat: function() {
          console.log(InboundLeadData);
      },
      /**
       * Triggers when visitor session goes idle. Idling occurs after 60 seconds of
       * inactivity or when the visitor switches browser tabs
       *
       * ```js
       * // Usage:
       *
       * // Add function to 'session_idle' event
       * _inbound.add_action( 'session_idle', session_idle_func_example, 10);
       *
       * function session_idle_func_example(data) {
       *     var data = data || {};
       *     // Do something when session idles
       *     alert('Here is a special offer for you!');
       * }
       * ```
       */
      session_idle: function(){
          fireEvent('session_idle');
          console.log('Session IDLE');
      },

      session_end: function() {
          fireEvent('session_end');
          console.log('Session End');
      },
      /* Page Visit Events */
      /**
       * Triggers Every Page View
       *
       * ```js
       * // Usage:
       *
       * // Add function to 'page_visit' event
       * _inbound.add_action( 'page_visit', page_visit_func_example, 10);
       *
       * function session_idle_func_example(pageData) {
       *     var pageData = pageData || {};
       *     if( pageData.view_count > 8 ){
       *       alert('Wow you have been to this page more than 8 times.');
       *     }
       * }
       * ```
       */
      page_visit: function(pageData) {
          fireEvent('page_view', pageData);
      },
      /**
       * Triggers If the visitor has never seen the page before
       *
       * ```js
       * // Usage:
       *
       * // Add function to 'page_first_visit' event
       * _inbound.add_action( 'page_first_visit', page_first_visit_func_example, 10);
       *
       * function page_first_visit_func_example(pageData) {
       *     var pageData = pageData || {};
       *     alert('Welcome to this page! Its the first time you have seen it')
       * }
       * ```
       */
      page_first_visit: function(pageData) {
          fireEvent('page_first_visit');
          console.log('First Ever Page View of this Page');
          console.log(pageData);
      },
      /**
       * Triggers If the visitor has seen the page before
       *
       * ```js
       * // Usage:
       *
       * // Add function to 'page_revisit' event
       * _inbound.add_action( 'page_revisit', page_revisit_func_example, 10);
       *
       * function page_revisit_func_example(pageData) {
       *     var pageData = pageData || {};
       *     alert('Welcome back to this page!');
       *     // Show visitor special content/offer
       * }
       * ```
       */
      page_revisit: function(pageData) {
          console.log('Page Revisit viewed ' + pageData + " times");
          fireEvent('page_revisit', pageData);
          console.log(pageData);
      },

      /**
       *  `tab_hidden` is triggered when the visitor switches browser tabs
       *
       * ```js
       * // Usage:
       *
       * // Adding the callback
       * function tab_hidden_function( data ) {
       *      alert('The Tab is Hidden');
       * };
       *
       *  // Hook the function up the the `tab_hidden` event
       *  _inbound.add_action( 'tab_hidden', tab_hidden_function, 10 );
       * ```
       */
      tab_hidden: function(data) {
          console.log('Tab Hidden');
          fireEvent('tab_hidden');
      },
      /**
       *  `tab_visible` is triggered when the visitor switches back to the sites tab
       *
       * ```js
       * // Usage:
       *
       * // Adding the callback
       * function tab_visible_function( data ) {
       *      alert('Welcome back to this tab!');
       *      // trigger popup or offer special discount etc.
       * };
       *
       *  // Hook the function up the the `tab_visible` event
       *  _inbound.add_action( 'tab_visible', tab_visible_function, 10 );
       * ```
       */
      tab_visible: function(data) {
          console.log('Tab Visible');
          fireEvent('tab_visible');
      },
      /**
       *  `tab_mouseout` is triggered when the visitor mouses out of the browser window.
       *  This is especially useful for exit popups
       *
       * ```js
       * // Usage:
       *
       * // Adding the callback
       * function tab_mouseout_function( data ) {
       *      alert("Wait don't Go");
       *      // trigger popup or offer special discount etc.
       * };
       *
       *  // Hook the function up the the `tab_mouseout` event
       *  _inbound.add_action( 'tab_mouseout', tab_mouseout_function, 10 );
       * ```
       */
      tab_mouseout: function(data){
          fireEvent('tab_mouseout');
      },
      /**
       *  `before_form_submission` is triggered before the form is submitted to the server.
       *  You can filter the data here or send it to third party services
       *
       * ```js
       * // Usage:
       *
       * // Adding the callback
       * function before_form_submission_function( data ) {
       *      var data = data || {};
       *      // filter form data
       * };
       *
       *  // Hook the function up the the `before_form_submission` event
       *  _inbound.add_action( 'before_form_submission', before_form_submission_function, 10 );
       * ```
       */
      before_form_submission: function(formData) {
          fireEvent('before_form_submission', formData);
      },
      after_form_submission: function(formData){
          fireEvent('after_form_submission', formData);
      },
      /*! Scrol depth https://github.com/robflaherty/jquery-scrolldepth/blob/master/jquery.scrolldepth.js */

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

  };

  return _inbound;

})(_inbound || {});