/**
 * # Page View Tracking
 *
 * Page view tracking
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */
/* Launches view tracking */
var _inboundPageTracking = (function(_inbound) {

    var started = false,
      stopped = false,
      turnedOff = false,
      clockTime = 0,
      startTime = new Date(),
      clockTimer = null,
      idleTimer = null,
      reportInterval,
      idleTimeout,
      utils = _inbound.Utils;

    _inbound.PageTracking = {

        init: function(options) {

          // Set up options and defaults
          options = options || {};
          reportInterval = parseInt(options.reportInterval, 10) || 10;
          idleTimeout = parseInt(options.idleTimeout, 10) || 10;

          // Basic activity event listeners
          utils.addListener(document, 'keydown', utils.throttle(_inbound.PageTracking.trigger, 1000));
          utils.addListener(document, 'click', utils.throttle(_inbound.PageTracking.trigger, 1000));
          utils.addListener(window, 'mousemove', utils.throttle(_inbound.PageTracking.trigger, 1000));
          //utils.addListener(window, 'scroll',  utils.throttle(_inbound.PageTracking.trigger, 1000));

          // Page visibility listeners
          _inbound.PageTracking.checkVisibility();

          /* Start timer on page load */
          this.trigger();

        },

        setIdle: function (reason) {
          var reason = reason || "No Movement";
          console.log('Activity Timeout due to ' + reason);
          clearTimeout(_inbound.PageTracking.idleTimer);
          _inbound.PageTracking.stopClock();
        },

        checkVisibility: function() {
             var hidden, visibilityState, visibilityChange;

              if (typeof document.hidden !== "undefined") {
                hidden = "hidden", visibilityChange = "visibilitychange", visibilityState = "visibilityState";
              } else if (typeof document.mozHidden !== "undefined") {
                hidden = "mozHidden", visibilityChange = "mozvisibilitychange", visibilityState = "mozVisibilityState";
              } else if (typeof document.msHidden !== "undefined") {
                hidden = "msHidden", visibilityChange = "msvisibilitychange", visibilityState = "msVisibilityState";
              } else if (typeof document.webkitHidden !== "undefined") {
                hidden = "webkitHidden", visibilityChange = "webkitvisibilitychange", visibilityState = "webkitVisibilityState";
              }

              var document_hidden = document[hidden];

              _inbound.Utils.addListener(document, visibilityChange, function(e) {
                  /*! Listen for visibility changes */
                  if(document_hidden != document[hidden]) {
                    if(document[hidden]) {
                      // Document hidden
                      _inbound.Events.browserTabHidden();
                      _inbound.PageTracking.setIdle('browser tab switch');
                    } else {
                      // Document shown
                      _inbound.Events.browserTabVisible();
                      _inbound.PageTracking.trigger();
                    } // if

                    document_hidden = document[hidden];
                  }
              });
        },
        clock: function() {
          clockTime += 1;
          //console.log(clockTime);
          if (clockTime > 0 && (clockTime % reportInterval === 0)) {
            // sendEvent(clockTime);
            /*! every 10 seconds run this */
            console.log('poll Server');

          }

        },
        stopClock: function() {
          stopped = true;
          clearTimeout(clockTimer);
        },

        restartClock: function() {
          stopped = false;
          console.log('Activity resumed');
          clearTimeout(clockTimer);
          clockTimer = setInterval(_inbound.PageTracking.clock, 1000);
        },

        turnOff: function() {
          _inbound.PageTracking.setIdle();
          turnedOff = true;
        },

        turnOn: function () {
          turnedOff = false;
        },
        /* This start only runs once */
        startActivityMonitor: function() {

          // Calculate seconds from start to first interaction
          var currentTime = new Date();
          var diff = currentTime - startTime;
          console.log('time diff', diff);
          // Set global
          started = true;

          // Send User Timing Event
          //sendUserTiming(diff);

          // Start clock
          clockTimer = setInterval(_inbound.PageTracking.clock, 1000);

        },

        trigger: function (e) {
          //console.log(e.type);
          if (turnedOff) {
            return;
          }

          if (!started) {
            _inbound.PageTracking.startActivityMonitor();
          }

          if (stopped) {
            _inbound.PageTracking.restartClock();
          }

          clearTimeout(idleTimer);
          idleTimer = setTimeout(_inbound.PageTracking.setIdle, idleTimeout * 1000 + 100);
        },

        /**
         * Returns the pages viewed by the site visitor
         *
         * ```js
         *  var pageViews = _inbound.PageTracking.getPageViews();
         *  // returns page view object
         * ```
         *
         * @return {object} page view object with page ID as key and timestamp
         */
        getPageViews: function() {
            var local_store = _inbound.Utils.checkLocalStorage();
            if (local_store) {
                var page_views = localStorage.getItem("page_views"),
                    local_object = JSON.parse(page_views);
                if (typeof local_object == 'object' && local_object) {
                    this.StorePageView();
                }
                return local_object;
            }
        },
        StorePageView: function() {
            var timeout = this.CheckTimeOut(),
                page_seen_count;
            var pageviewObj = _inbound.totalStorage('page_views');
            if (pageviewObj === null) {
                pageviewObj = {};
            }
            var current_page_id = inbound_settings.post_id;
            var datetime = _inbound.Utils.GetDate();

            if (timeout) {
                // If pageviewObj exists, do this
                var page_seen = pageviewObj[current_page_id];

                if (typeof(page_seen) != "undefined" && page_seen !== null) {
                    pageviewObj[current_page_id].push(datetime);
                    /* Page Revisit Trigger */
                    page_seen_count = pageviewObj[current_page_id].length;
                    _inbound.Events.pageRevisit(page_seen_count);

                } else {
                    pageviewObj[current_page_id] = [];
                    pageviewObj[current_page_id].push(datetime);
                    /* Page First Seen Trigger */
                    page_seen_count = 1;
                    _inbound.Events.pageFirstView(page_seen_count);
                }

                _inbound.totalStorage('page_views', pageviewObj);

            }
        },
        CheckTimeOut: function() {
            var PageViews = _inbound.totalStorage('page_views') || {},
                page_id = inbound_settings.post_id,
                pageviewTimeout = true,
                /* Default */
                page_seen = PageViews[page_id];
            if (typeof(page_seen) !== "undefined" && page_seen !== null) {

                var time_now = _inbound.Utils.GetDate(),
                    vc = PageViews[page_id].length - 1,
                    last_view = PageViews[page_id][vc],
                    last_view_ms = new Date(last_view).getTime(),
                    time_now_ms = new Date(time_now).getTime(),
                    timeout_ms = last_view_ms + 30 * 1000,
                    time_check = Math.abs(last_view_ms - time_now_ms),
                    wait_time = _inbound.Settings.timeout || 30000;

                _inbound.debug('Timeout Checks =', function() {
                    console.log('Current Time is: ' + time_now);
                    console.log('Last view is: ' + last_view);
                    console.log("Last view milliseconds " + last_view_ms);
                    console.log("time now milliseconds " + time_now_ms);
                    console.log("Wait Check: " + wait_time);
                    console.log("TIME CHECK: " + time_check);
                });

                //var wait_time = Math.abs(last_view_ms - timeout_ms) // output timeout time 30sec;

                if (time_check < wait_time) {
                    time_left = Math.abs((wait_time - time_check)) * 0.001;
                    pageviewTimeout = false;
                    var status = wait_time / 1000 + ' sec timeout not done: ' + time_left + " seconds left";
                    console.log(status);
                } else {
                    var status = 'Timeout Happened. Page view fired';
                    this.firePageView();
                    pageviewTimeout = true;
                    _inbound.Events.analyticsTriggered();
                }

                //console.log(status);

            } else {
                /*! Page never seen before */
                this.firePageView();
            }

            return pageviewTimeout;

        },
        firePageView: function() {
            var lead_id = _inbound.Utils.readCookie('wp_lead_id'),
                lead_uid = _inbound.Utils.readCookie('wp_lead_uid');

            if (lead_id) {

                _inbound.debug('Run page view ajax');

                var data = {
                    action: 'wpl_track_user',
                    wp_lead_uid: lead_uid,
                    wp_lead_id: lead_id,
                    page_id: inbound_settings.post_id,
                    current_url: window.location.href,
                    json: '0'
                };
                var firePageCallback = function(user_id) {
                    _inbound.Events.analyticsSaved();
                };
                //_inbound.Utils.doAjax(data, firePageCallback);
                _inbound.Utils.ajaxPost(inbound_settings.admin_url, data, firePageCallback);
            }
        }
    };

    return _inbound;

})(_inbound || {});