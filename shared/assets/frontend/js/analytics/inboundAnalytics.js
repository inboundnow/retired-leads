/*! Inbound Analyticsv1.0.0 | (c) 2014 Inbound Now | https://github.com/inboundnow/cta */
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
         _inbound.PageTracking.StorePageView();
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
/**
 * # Hooks & Filters
 *
 * This file contains all of the form functions of the main _inbound object.
 * Filters and actions are described below
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */

var _inboundHooks = (function (_inbound) {

	/**
	 * Handles managing all events for whatever you plug it into. Priorities for hooks are based on lowest to highest in
	 * that, lowest priority hooks are fired first.
	 */
	var EventManager = function() {
		/**
		 * Maintain a reference to the object scope so our public methods never get confusing.
		 */
		var MethodsAvailable = {
			removeFilter : removeFilter,
			applyFilters : applyFilters,
			addFilter : addFilter,
			removeAction : removeAction,
			doAction : doAction,
			addAction : addAction
		};

		/**
		 * Contains the hooks that get registered with this EventManager. The array for storage utilizes a "flat"
		 * object literal such that looking up the hook utilizes the native object literal hash.
		 */
		var STORAGE = {
			actions : {},
			filters : {}
		};

		/**
		 * Adds an action to the event manager.
		 *
		 * @param action Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addAction( action, callback, priority, context ) {
			if( typeof action === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'actions', action, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs an action if it exists. You can pass as many arguments as you want to this function; the only rule is
		 * that the first argument must always be the action.
		 */
		function doAction( /* action, arg1, arg2, ... */ ) {
			var args = Array.prototype.slice.call( arguments );
			var action = args.shift();

			if( typeof action === 'string' ) {
				_runHook( 'actions', action, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified action if it contains a namespace.identifier & exists.
		 *
		 * @param action The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeAction( action, callback ) {
			if( typeof action === 'string' ) {
				_removeHook( 'actions', action, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Adds a filter to the event manager.
		 *
		 * @param filter Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addFilter( filter, callback, priority, context ) {
			if( typeof filter === 'string' && typeof callback === 'function' ) {
				console.log('add filter', filter);
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'filters', filter, callback, priority );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs a filter if it exists. You should only ever pass 1 argument to be filtered. The only rule is that
		 * the first argument must always be the filter.
		 */
		function applyFilters( /* filter, filtered arg, arg2, ... */ ) {
			var args = Array.prototype.slice.call( arguments );
			var filter = args.shift();

			if( typeof filter === 'string' ) {
				return _runHook( 'filters', filter, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified filter if it contains a namespace.identifier & exists.
		 *
		 * @param filter The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeFilter( filter, callback ) {
			if( typeof filter === 'string') {
				_removeHook( 'filters', filter, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified hook by resetting the value of it.
		 *
		 * @param type Type of hook, either 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to remove
		 * @private
		 */
		function _removeHook( type, hook, callback, context ) {
			if ( !STORAGE[ type ][ hook ] ) {
				return;
			}
			if ( !callback ) {
				STORAGE[ type ][ hook ] = [];
			} else {
				var handlers = STORAGE[ type ][ hook ];
				var i;
				if ( !context ) {
					for ( i = handlers.length; i--; ) {
						if ( handlers[i].callback === callback ) {
							handlers.splice( i, 1 );
						}
					}
				}
				else {
					for ( i = handlers.length; i--; ) {
						var handler = handlers[i];
						if ( handler.callback === callback && handler.context === context) {
							handlers.splice( i, 1 );
						}
					}
				}
			}
		}

		/**
		 * Adds the hook to the appropriate storage container
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to add to our event manager
		 * @param callback The function that will be called when the hook is executed.
		 * @param priority The priority of this hook. Must be an integer.
		 * @param [context] A value to be used for this
		 * @private
		 */
		function _addHook( type, hook, callback, priority, context ) {
			var hookObject = {
				callback : callback,
				priority : priority,
				context : context
			};

			// Utilize 'prop itself' : http://jsperf.com/hasownproperty-vs-in-vs-undefined/19
			var hooks = STORAGE[ type ][ hook ];
			if( hooks ) {
				hooks.push( hookObject );
				hooks = _hookInsertSort( hooks );
			}
			else {
				hooks = [ hookObject ];
			}

			STORAGE[ type ][ hook ] = hooks;
		}

		/**
		 * Use an insert sort for keeping our hooks organized based on priority. This function is ridiculously faster
		 * than bubble sort, etc: http://jsperf.com/javascript-sort
		 *
		 * @param hooks The custom array containing all of the appropriate hooks to perform an insert sort on.
		 * @private
		 */
		function _hookInsertSort( hooks ) {
			var tmpHook, j, prevHook;
			for( var i = 1, len = hooks.length; i < len; i++ ) {
				tmpHook = hooks[ i ];
				j = i;
				while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
					hooks[ j ] = hooks[ j - 1 ];
					--j;
				}
				hooks[ j ] = tmpHook;
			}

			return hooks;
		}

		/**
		 * Runs the specified hook. If it is an action, the value is not modified but if it is a filter, it is.
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook ( namespace.identifier ) to be ran.
		 * @param args Arguments to pass to the action/filter. If it's a filter, args is actually a single parameter.
		 * @private
		 */
		function _runHook( type, hook, args ) {
			var handlers = STORAGE[ type ][ hook ];

			if ( !handlers ) {
				return (type === 'filters') ? args[0] : false;
			}

			var i = 0, len = handlers.length;
			if ( type === 'filters' ) {
				for ( ; i < len; i++ ) {
					args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			} else {
				for ( ; i < len; i++ ) {
					handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			}

			return ( type === 'filters' ) ? args[ 0 ] : true;
		}

		// return all of the publicly available methods
		return MethodsAvailable;

	};

	_inbound.hooks = new EventManager();


	/**
	 * Event Hooks and Filters public methods
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
	 *  ```js
	 *   // Add remove action 'name_of_action'
	 *   _inbound.remove_action( 'name_of_action');
	 *  ```
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
	 *  This is used if you want to allow for third party JS plugins to act on your functions
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
	 *  ```js
	 *   _inbound.add_filter( 'urlParamFilter', URL_Param_Filter, 10 );
	 *   function URL_Param_Filter(urlParams) {
	 *
	 *   var params = urlParams || {};
	 *   // check for item in object
	 *   if(params.utm_source !== "undefined"){
	 *     //alert('url param "utm_source" is here');
	 *   }
	 *
	 *   // delete item from object
	 *   delete params.utm_source;
	 *
	 *   return params;
	 *
	 *   }
	 *   ```
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
	 *   ```js
	 *   // Add remove filter 'urlParamFilter'
	 *   _inbound.remove_action( 'urlParamFilter');
	 *   ```
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
	  //console.log('Filter:' + arguments[0] + " ran on ->", arguments[1]);
	  // prefix action
	  arguments[0] = 'inbound.' + arguments[0];

	  return _inbound.hooks.applyFilters.apply(this, arguments);

	 };


    return _inbound;

})(_inbound || {});
/**
 * # _inbound UTILS
 *
 * This file contains all of the utility functions used by analytics
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */
var _inboundUtils = (function(_inbound) {

    var storageSupported;

    _inbound.Utils = {
        init: function() {
            this.polyFills();
            this.checkLocalStorage();
            this.SetUID();
            this.storeReferralData();
        },
        /*! http://stackoverflow.com/questions/951791/javascript-global-error-handling */
        /* Polyfills for missing browser functionality */
        polyFills: function() {
            /* Console.log fix for old browsers */
            if (!window.console) {
                window.console = {};
            }
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
            /* Event trigger polyfill for IE9 and 10
            (function() {
                function CustomEvent(event, params) {
                    params = params || {
                        bubbles: false,
                        cancelable: false,
                        detail: undefined
                    };
                    var evt = document.createEvent('CustomEvent');
                    evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
                    return evt;
                }

                CustomEvent.prototype = window.Event.prototype;

                window.CustomEvent = CustomEvent;
            })();*/
            /* custom event for ie8+ https://gist.github.com/WebReflection/6693661 */
            try{new CustomEvent('?');}catch(o_O){
              /*!(C) Andrea Giammarchi -- WTFPL License*/
              this.CustomEvent = function(
                eventName,
                defaultInitDict
              ){

                // the infamous substitute
                function CustomEvent(type, eventInitDict) {
                  var event = document.createEvent(eventName);
                  if (type !== null) {
                    initCustomEvent.call(
                      event,
                      type,
                      (eventInitDict || (
                        // if falsy we can just use defaults
                        eventInitDict = defaultInitDict
                      )).bubbles,
                      eventInitDict.cancelable,
                      eventInitDict.detail
                    );
                  } else {
                    // no need to put the expando property otherwise
                    // since an event cannot be initialized twice
                    // previous case is the most common one anyway
                    // but if we end up here ... there it goes
                    event.initCustomEvent = initCustomEvent;
                  }
                  return event;
                }

                // borrowed or attached at runtime
                function initCustomEvent(
                  type, bubbles, cancelable, detail
                ) {
                  this['init' + eventName](type, bubbles, cancelable, detail);
                  'detail' in this || (this.detail = detail);
                }

                // that's it
                return CustomEvent;
              }(
                // is this IE9 or IE10 ?
                // where CustomEvent is there
                // but not usable as construtor ?
                this.CustomEvent ?
                  // use the CustomEvent interface in such case
                  'CustomEvent' : 'Event',
                  // otherwise the common compatible one
                {
                  bubbles: false,
                  cancelable: false,
                  detail: null
                }
              );
            }
            /* querySelectorAll polyfill for ie7+ */
            if (!document.querySelectorAll) {
              document.querySelectorAll = function (selectors) {
                var style = document.createElement('style'), elements = [], element;
                document.documentElement.firstChild.appendChild(style);
                document._qsa = [];

                style.styleSheet.cssText = selectors + '{x-qsa:expression(document._qsa && document._qsa.push(this))}';
                window.scrollBy(0, 0);
                style.parentNode.removeChild(style);

                while (document._qsa.length) {
                  element = document._qsa.shift();
                  element.style.removeAttribute('x-qsa');
                  elements.push(element);
                }
                document._qsa = null;
                return elements;
              };
            }

            if (!document.querySelector) {
              document.querySelector = function (selectors) {
                var elements = document.querySelectorAll(selectors);
                return (elements.length) ? elements[0] : null;
              };
            }
            /* Innertext shim for firefox https://github.com/duckinator/innerText-polyfill/blob/master/innertext.js */
            if ( (!('innerText' in document.createElement('a'))) && ('getSelection' in window) ) {
                HTMLElement.prototype.__defineGetter__("innerText", function() {
                    var selection = window.getSelection(),
                        ranges    = [],
                        str;

                    // Save existing selections.
                    for (var i = 0; i < selection.rangeCount; i++) {
                        ranges[i] = selection.getRangeAt(i);
                    }

                    // Deselect everything.
                    selection.removeAllRanges();

                    // Select `el` and all child nodes.
                    // 'this' is the element .innerText got called on
                    selection.selectAllChildren(this);

                    // Get the string representation of the selected nodes.
                    str = selection.toString();

                    // Deselect everything. Again.
                    selection.removeAllRanges();

                    // Restore all formerly existing selections.
                    for (var i = 0; i < ranges.length; i++) {
                        selection.addRange(ranges[i]);
                    }

                    // Oh look, this is what we wanted.
                    // String representation of the element, close to as rendered.
                    return str;
                })
            }
        },
        /**
         * Create cookie
         *
         * ```js
         * // Creates cookie for 10 days
         * _inbound.utils.createCookie( 'cookie_name', 'value', 10 );
         * ```
         *
         * @param  {string} name        Name of cookie
         * @param  {string} value       Value of cookie
         * @param  {string} days        Length of storage
         */
        createCookie: function(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        },
        /**
         * Read cookie value
         *
         * ```js
         * var cookie = _inbound.utils.readCookie( 'cookie_name' );
         * console.log(cookie); // cookie value
         * ```
         * @param  {string} name name of cookie
         * @return {string}      value of cookie
         */
        readCookie: function(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1, c.length);
                }
                if (c.indexOf(nameEQ) === 0) {
                    return c.substring(nameEQ.length, c.length);
                }
            }
            return null;
        },
        /**
         * Erase cookie
         *
         * ```js
         * // usage:
         * _inbound.utils.eraseCookie( 'cookie_name' );
         * // deletes 'cookie_name' value
         * ```
         * @param  {string} name name of cookie
         * @return {string}      value of cookie
         */
        eraseCookie: function(name) {
            createCookie(name, "", -1);
        },
        /* Get All Cookies */
        getAllCookies: function() {
            var cookies = {};
            if (document.cookie && document.cookie !== '') {
                var split = document.cookie.split(';');
                for (var i = 0; i < split.length; i++) {
                    var name_value = split[i].split("=");
                    name_value[0] = name_value[0].replace(/^ /, '');
                    cookies[decodeURIComponent(name_value[0])] = decodeURIComponent(name_value[1]);
                }
            }
            _inbound.totalStorage('inbound_cookies', cookies); // store cookie data
            return cookies;
        },
        /* Grab URL params and save */
        setUrlParams: function() {
            var urlParams = {};

            (function() {
                var e,
                    d = function(s) {
                        return decodeURIComponent(s).replace(/\+/g, " ");
                    },
                    q = window.location.search.substring(1),
                    r = /([^&=]+)=?([^&]*)/g;

                while (e = r.exec(q)) {
                    if (e[1].indexOf("[") == "-1")
                        urlParams[d(e[1])] = d(e[2]);
                    else {
                        var b1 = e[1].indexOf("["),
                            aN = e[1].slice(b1 + 1, e[1].indexOf("]", b1)),
                            pN = d(e[1].slice(0, b1));

                        if (typeof urlParams[pN] != "object")
                            urlParams[d(pN)] = {},
                            urlParams[d(pN)].length = 0;

                        if (aN)
                            urlParams[d(pN)][d(aN)] = d(e[2]);
                        else
                            Array.prototype.push.call(urlParams[d(pN)], d(e[2]));

                    }
                }
            })();

            /* Set Param Cookies */
            for (var k in urlParams) {
                if (typeof urlParams[k] == "object") {
                    for (var k2 in urlParams[k])
                        this.createCookie(k2, urlParams[k][k2], 30);
                } else {
                    this.createCookie(k, urlParams[k], 30);
                }
            }
            /* Set Param LocalStorage */
            if (storageSupported) {
                var pastParams = _inbound.totalStorage('inbound_url_params') || {};
                var params = this.mergeObjs(pastParams, urlParams);
                _inbound.totalStorage('inbound_url_params', params); // store cookie data
            }

            var options = {'option1': 'yo', 'option2': 'woooo'};

            _inbound.Events.fireEvent('url_parameters', urlParams, options);

        },
        getAllUrlParams: function() {
            var get_params = {};
            if (storageSupported) {
                var get_params = _inbound.totalStorage('inbound_url_params');
            }
            return get_params;
        },
        /* Get url param */
        getParameterVal: function(name, string) {
            return (RegExp(name + '=' + '(.+?)(&|$)').exec(string)||[,false])[1];
        },
        // Check local storage
        // provate browsing safari fix https://github.com/marcuswestin/store.js/issues/42#issuecomment-25274685
        checkLocalStorage: function() {
            if ('localStorage' in window) {
                try {
                    ls = (typeof window.localStorage === 'undefined') ? undefined : window.localStorage;
                    if (typeof ls == 'undefined' || typeof window.JSON == 'undefined') {
                        storageSupported = false;
                    } else {
                        storageSupported = true;
                    }

                } catch (err) {
                    storageSupported = false;
                }
            }
            return storageSupported;
            /* http://spin.atomicobject.com/2013/01/23/ios-private-browsing-localstorage/
            var hasStorage;
            hasStorage = function() {
              var mod, result;
              try {
                mod = new Date;
                localStorage.setItem(mod, mod.toString());
                result = localStorage.getItem(mod) === mod.toString();
                localStorage.removeItem(mod);
                return result;
              } catch (_error) {}
            };
             */
        },
        /* Add days to datetime */
        addDays: function(myDate, days) {
            return new Date(myDate.getTime() + days * 24 * 60 * 60 * 1000);
        },
        GetDate: function() {
            var time_now = new Date(),
                day = time_now.getDate() + 1;
            year = time_now.getFullYear(),
                hour = time_now.getHours(),
                minutes = time_now.getMinutes(),
                seconds = time_now.getSeconds(),
                month = time_now.getMonth() + 1;
            if (month < 10) {
                month = '0' + month;
            }
            _inbound.debug('Current Date:', function() {
                console.log(year + '/' + month + "/" + day + " " + hour + ":" + minutes + ":" + seconds);
            });
            var datetime = year + '/' + month + "/" + day + " " + hour + ":" + minutes + ":" + seconds;
            return datetime;
        },
        /* Set Expiration Date of Session Logging */
        SetSessionTimeout: function() {
            var session_check = this.readCookie("lead_session_expire");
            //console.log(session_check);
            if (session_check === null) {
                _inbound.Events.sessionStart(); // trigger 'inbound_analytics_session_start'
            } else {
                _inbound.Events.sessionActive(); // trigger 'inbound_analytics_session_active'
            }
            var d = new Date();
            d.setTime(d.getTime() + 30 * 60 * 1000);

            this.createCookie("lead_session_expire", true, d, true); // Set cookie on page loads
            var lead_data_expiration = this.readCookie("lead_data_expiration");
            if (lead_data_expiration === null) {
                /* Set 3 day timeout for checking DB for new lead data for Lead_Global var */
                var ex = this.addDays(d, 3);
                this.createCookie("lead_data_expiration", ex, ex, true);
            }

        },
        storeReferralData: function() {
            //console.log(expire_time);
            var d = new Date(),
            referrer = document.referrer || "Direct Traffic",
            referrer_cookie = _inbound.Utils.readCookie("inbound_referral_site"),
            original_src = _inbound.totalStorage('inbound_original_referral');

            d.setTime(d.getTime() + 30 * 60 * 1000);

            if (typeof(referrer_cookie) === "undefined" || referrer_cookie === null || referrer_cookie === "") {
                this.createCookie("inbound_referral_site", referrer, d, true);
            }
            if (typeof(original_src) === "undefined" || original_src === null || original_src === "") {
                _inbound.totalStorage('inbound_original_referral', original_src);
            }
        },
        CreateUID: function(length) {
            var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split(''),
                str = '';
            if (!length) {
                length = Math.floor(Math.random() * chars.length);
            }
            for (var i = 0; i < length; i++) {
                str += chars[Math.floor(Math.random() * chars.length)];
            }
            return str;
        },
        SetUID: function(leadUID) {
            /* Set Lead UID */
            if (this.readCookie("wp_lead_uid") === null) {
                var wp_lead_uid = leadUID || this.CreateUID(35);
                this.createCookie("wp_lead_uid", wp_lead_uid);
            }
        },
        /* Count number of session visits */
        countProperties: function(obj) {
            var count = 0;
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop))
                    ++count;
            }
            return count;
        },
        mergeObjs: function(obj1, obj2) {
            var obj3 = {};
            for (var attrname in obj1) {
                obj3[attrname] = obj1[attrname];
            }
            for (var attrname in obj2) {
                obj3[attrname] = obj2[attrname];
            }
            return obj3;
        },
        hasClass: function(className, el) {
            var hasClass = false;
            if ('classList' in document.documentElement) {
                var hasClass = el.classList.contains(className);
            } else {
                var hasClass = new RegExp('(^|\\s)' + className + '(\\s|$)').test(el.className); /* IE Polyfill */
            }
            return hasClass;
        },
        addClass: function(className, elem) {
            if ('classList' in document.documentElement) {
                elem.classList.add(className);
            } else {
                if (!this.hasClass(elem, className)) {
                    elem.className += (elem.className ? ' ' : '') + className;
                }
            }
        },
        removeClass: function(className, elem) {
            if ('classList' in document.documentElement) {

                elem.classList.remove(className);
            } else {
                if (this.hasClass(elem, className)) {
                    elem.className = elem.className.replace(new RegExp('(^|\\s)*' + className + '(\\s|$)*', 'g'), '');
                }
            }
        },
        trim: function(s) {
            s = s.replace(/(^\s*)|(\s*$)/gi, "");
            s = s.replace(/[ ]{2,}/gi, " ");
            s = s.replace(/\n /, "\n");
            return s;
        },
        doAjax: function(data, responseHandler, method, async) {
            // Set the variables
            var url = wplft.admin_url || "",
                method = method || "POST",
                async = async || true,
                data = data || null,
                action = data.action;

            _inbound.debug('Ajax Processed:', function() {
                console.log('ran ajax action: ' + action);
            });
            if (window.jQuery) {
                jQuery.ajax({
                    type: method,
                    url: wplft.admin_url,
                    data: data,
                    success: responseHandler,
                    error: function(MLHttpRequest, textStatus, errorThrown) {
                        console.log(MLHttpRequest + ' ' + errorThrown + ' ' + textStatus);
                        _inbound.Events.analyticsError(MLHttpRequest, textStatus, errorThrown);
                    }

                });
            }
        },
        ajaxPolyFill: function() {
            if (typeof XMLHttpRequest !== 'undefined') {
                return new XMLHttpRequest();
            }
            var versions = [
                "MSXML2.XmlHttp.5.0",
                "MSXML2.XmlHttp.4.0",
                "MSXML2.XmlHttp.3.0",
                "MSXML2.XmlHttp.2.0",
                "Microsoft.XmlHttp"
            ];

            var xhr;
            for (var i = 0; i < versions.length; i++) {
                try {
                    xhr = new ActiveXObject(versions[i]);
                    break;
                } catch (e) {}
            }
            return xhr;
        },
        ajaxSendData: function(url, callback, method, data, sync) {
            var x = this.ajaxPolyFill();
            x.open(method, url, sync);
            x.onreadystatechange = function() {
                if (x.readyState == 4) {
                    callback(x.responseText)
                }
            };
            if (method == 'POST') {
                x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            }
            x.send(data);
        },
        ajaxGet: function(url, data, callback, sync) {
            var query = [];
            for (var key in data) {
                query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
            this.ajaxSendData(url + '?' + query.join('&'), callback, 'GET', null, sync)
        },
        ajaxPost: function(url, data, callback, sync) {
            var query = [];
            for (var key in data) {
                query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
            this.ajaxSendData(url, callback, 'POST', query.join('&'), sync)
        },
        makeRequest: function(url, data) {
            if (window.XMLHttpRequest) { // Mozilla, Safari, ...
                httpRequest = new XMLHttpRequest();
            } else if (window.ActiveXObject) { // IE
                try {
                    httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try {
                        httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e) {}
                }
            }

            if (!httpRequest) {
                alert('Giving up :( Cannot create an XMLHTTP instance');
                return false;
            }
            httpRequest.onreadystatechange = _inbound.LeadsAPI.alertContents;
            httpRequest.open('GET', url);
            httpRequest.send(data);
        },
        domReady: function(win, fn) {

            var done = false,
                top = true,

                doc = win.document,
                root = doc.documentElement,

                add = doc.addEventListener ? 'addEventListener' : 'attachEvent',
                rem = doc.addEventListener ? 'removeEventListener' : 'detachEvent',
                pre = doc.addEventListener ? '' : 'on',

                init = function(e) {
                    if (e.type == 'readystatechange' && doc.readyState != 'complete') return;
                    (e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
                    if (!done && (done = true)) fn.call(win, e.type || e);
                },

                poll = function() {
                    try {
                        root.doScroll('left');
                    } catch (e) {
                        setTimeout(poll, 50);
                        return;
                    }
                    init('poll');
                };

            if (doc.readyState == 'complete') fn.call(win, 'lazy');
            else {
                if (doc.createEventObject && root.doScroll) {
                    try {
                        top = !win.frameElement;
                    } catch (e) {}
                    if (top) poll();
                }
                doc[add](pre + 'DOMContentLoaded', init, false);
                doc[add](pre + 'readystatechange', init, false);
                win[add](pre + 'load', init, false);
            }

        },
        /* Cross-browser event listening  */
        addListener: function(element, eventName, listener) {
            //console.log(eventName);
            //console.log(listener);
            if (element.addEventListener) {
                element.addEventListener(eventName, listener, false);
            } else if (element.attachEvent) {
                element.attachEvent("on" + eventName, listener);
            } else {
                element['on' + eventName] = listener;
            }
        },
        removeListener: function(element, eventName, listener) {

            if (element.removeEventListener) {
                element.removeEventListener(eventName, listener, false);
            } else if (element.detachEvent) {
                element.detachEvent("on" + eventName, listener);
            } else {
                element["on" + eventName] = null;
            }
        },
        /*
         * Throttle function borrowed from:
         * Underscore.js 1.5.2
         * http://underscorejs.org
         * (c) 2009-2013 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
         * Underscore may be freely distributed under the MIT license.
         */
        throttle: function (func, wait) {
          var context, args, result;
          var timeout = null;
          var previous = 0;
          var later = function() {
            previous = new Date;
            timeout = null;
            result = func.apply(context, args);
          };
          return function() {
            var now = new Date;
            if (!previous) previous = now;
            var remaining = wait - (now - previous);
            context = this;
            args = arguments;
            if (remaining <= 0) {
              clearTimeout(timeout);
              timeout = null;
              previous = now;
              result = func.apply(context, args);
            } else if (!timeout) {
              timeout = setTimeout(later, remaining);
            }
            return result;
          };
        },
        checkVisibility: function() {
              /* test out simplier script
              function onBlur() {
                document.body.className = 'blurred';
              };
              function onFocus(){
                document.body.className = 'focused';
              };

              if (false) { // check for Internet Explorer
                document.onfocusin = onFocus;
                document.onfocusout = onBlur;
              } else {
                window.onfocus = onFocus;
                window.onblur = onBlur;
              }
              */

             var hidden, visibilityState, visibilityChange;

              if (typeof document.hidden !== "undefined") {
                hidden = "hidden", visibilityChange = "visibilitychange", visibilityState = "visibilityState";
              } else if (typeof document.mozHidden !== "undefined") {
                hidden = "mozHidden", visibilityChange = "mozvisibilitychange", visibilityState = "mozVisibilityState";
              } else if (typeof document.msHidden !== "undefined") {
                hidden = "msHidden", visibilityChange = "msvisibilitychange", visibilityState = "msVisibilityState";
              } else if (typeof document.webkitHidden !== "undefined") {
                hidden = "webkitHidden", visibilityChange = "webkitvisibilitychange", visibilityState = "webkitVisibilityState";
              } // if

              var document_hidden = document[hidden];

              _inbound.Utils.addListener(document, visibilityChange, function(e) {
              //document.addEventListener(visibilityChange, function() {
                if(document_hidden != document[hidden]) {
                  if(document[hidden]) {
                    // Document hidden
                    _inbound.Events.browserTabHidden();
                  } else {
                    // Document shown
                    _inbound.Events.browserTabVisible();
                  } // if

                  document_hidden = document[hidden];
                } // if
              });
        }
    };

    return _inbound;

})(_inbound || {});
/**
 * # Inbound Forms
 *
 * This file contains all of the form functions of the main _inbound object.
 * Filters and actions are described below
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */

/* Launches form class */
var InboundForms = (function (_inbound) {

    var debugMode = false,
    utils = _inbound.Utils,
    no_match = [],
    rawParams = [],
    mappedParams = [],
    settings = _inbound.Settings;

    var FieldMapArray = [
                        "first name",
                        "last name",
                        "name",
                        "email",
                        "e-mail",
                        "phone",
                        "website",
                        "job title",
                        "your favorite food",
                        "company",
                        "tele",
                        "address",
                        "comment"
                        /* Adding values here maps them */
    ];

    _inbound.Forms =  {

      // Init Form functions
      init: function() {
          _inbound.Forms.runFieldMappingFilters();
          _inbound.Forms.assignTrackClass();
          _inbound.Forms.formTrackInit();
      },
      /**
       * This triggers the forms.field_map filter on the mapping array.
       * This will allow you to add or remore Items from the mapping lookup
       *
       * ### Example inbound.form_map_before filter
       *
       * This is an example of how form mapping can be filtered and
       * additional fields can be mapped via javascript
       *
       * ```js
       *  // Adding the filter function
       *  function Inbound_Add_Filter_Example( FieldMapArray ) {
       *    var map = FieldMapArray || [];
       *    map.push('new lookup value');
       *
       *    return map;
       *  };
       *
       *  // Adding the filter on dom ready
       *  _inbound.hooks.addFilter( 'inbound.form_map_before', Inbound_Add_Filter_Example, 10 );
       * ```
       *
       * @return {[type]} [description]
       */
      runFieldMappingFilters: function(){
          FieldMapArray = _inbound.hooks.applyFilters( 'forms.field_map', FieldMapArray);
          //alert(FieldMapArray);
      },
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
      },
      formTrackInit: function(){

          for(var i=0; i<window.document.forms.length; i++){
            var trackForm = false;
            var form = window.document.forms[i];

            trackForm = this.checkTrackStatus(form);
            // var trackForm = _inbound.Utils.hasClass("wpl-track-me", form);
            if (trackForm) {
              this.attachFormSubmitEvent(form); /* attach form listener */
              this.initFormMapping(form);
            }
          }
      },
      checkTrackStatus: function(form){
          var ClassIs = form.getAttribute('class');
          if( ClassIs !== "" && ClassIs !== null) {
              if(ClassIs.toLowerCase().indexOf("wpl-track-me")>-1) {
                return true;
              } else if (ClassIs.toLowerCase().indexOf("inbound-track")>-1) {
                return true;
              } else {
                console.log("No form to track on this page. Please assign on in settings");
                return false;
              }
          }
      },
      assignTrackClass: function() {
          if(window.inbound_track_include){
              var selectors = inbound_track_include.include.split(',');
              this.loopClassSelectors(selectors, 'add');
          }
          if(window.inbound_track_exclude){
              var selectors = inbound_track_exclude.exclude.split(',');
              this.loopClassSelectors(selectors, 'remove');
          }
      },
      /* Loop through include/exclude items for tracking */
      loopClassSelectors: function(selectors, action){
          for (var i = selectors.length - 1; i >= 0; i--) {
            selector = document.querySelector(utils.trim(selectors[i]));
            //console.log("SELECTOR", selector);
            if(selector) {
                if( action === 'add'){
                  _inbound.Utils.addClass('wpl-track-me', selector);
                  _inbound.Utils.addClass('inbound-track', selector);
                } else {
                  _inbound.Utils.removeClass('wpl-track-me', selector);
                  _inbound.Utils.removeClass('inbound-track', selector);
                }
            }
          };
      },
      /* Map field fields on load */
      initFormMapping: function(form) {
                        var hiddenInputs = [];

                        for (var i=0; i < form.elements.length; i++) {
                            formInput = form.elements[i];

                            if (formInput.type === 'hidden') {
                                hiddenInputs.push(formInput);
                                continue;
                            }
                            /* Map form fields */
                            this.mapField(formInput);
                            /* Remember visible inputs */
                            this.rememberInputValues(formInput);
                            /* Fill visible inputs */
                            if(settings.formAutoPopulation){
                              this.fillInputValues(formInput);
                            }

                        }
                        for (var i = hiddenInputs.length - 1; i >= 0; i--) {
                            formInput = hiddenInputs[i];
                            this.mapField(formInput);
                        };

                    //console.log('mapping on load completed');
      },
      /* prevent default submission temporarily */
      formListener: function(event) {
          console.log(event);
          event.preventDefault();
          _inbound.Forms.saveFormData(event.target);
      },
      /* attach form listeners */
      attachFormSubmitEvent: function (form) {
        utils.addListener(form, 'submit', this.formListener);
      },
      releaseFormSubmit: function(form){
        //console.log('remove form listener event');
        utils.removeClass('wpl-track-me', form);
        utils.removeListener(form, 'submit', this.formListener);
        form.submit();
        /* fallback if submit name="submit" */
        setTimeout(function() {
            for (var i=0; i < form.elements.length; i++) {
              formInput = form.elements[i];
              type = formInput.type || false;
              if (type === "submit") {
                form.elements[i].click();
              }
            }
        }, 1300);

      },
      saveFormData: function(form) {
          var inputsObject = inputsObject || {};
          for (var i=0; i < form.elements.length; i++) {
              this.debug('inputs obj',function(){
                  console.log(inputsObject);
              });

              formInput = form.elements[i];
              multiple = false;

              if (formInput.name) {

                  inputName = formInput.name.replace(/\[([^\[]*)\]/g, "%5B%5D$1");
                  //inputName = inputName.replace(/-/g, "_");
                  if (!inputsObject[inputName]) { inputsObject[inputName] = {}; }
                  if (formInput.type) { inputsObject[inputName]['type'] = formInput.type; }
                  if (!inputsObject[inputName]['name']) { inputsObject[inputName]['name'] = formInput.name; }
                  if (formInput.dataset.mapFormField) {
                    inputsObject[inputName]['map'] = formInput.dataset.mapFormField;
                  }
                  /*if (formInput.id) { inputsObject[inputName]['id'] = formInput.id; }
                  if ('classList' in document.documentElement)  {
                      if (formInput.classList) { inputsObject[inputName]['class'] = formInput.classList; }
                  }*/

                  switch (formInput.nodeName) {

                      case 'INPUT':
                          value = this.getInputValue(formInput);

                          console.log(value);
                          if (value === false) { continue; }
                          break;

                      case 'TEXTAREA':
                          value = formInput.value;
                          break;

                      case 'SELECT':
                          if (formInput.multiple) {
                              values = [];
                              multiple = true;

                              for (var j = 0; j < formInput.length; j++) {
                                  if (formInput[j].selected) {
                                      values.push(encodeURIComponent(formInput[j].value));
                                  }
                              }

                          } else {
                              value = (formInput.value);
                          }

                          console.log('select val', value);
                          break;
                  }

                  if (value) {
                      /* inputsObject[inputName].push(multiple ? values.join(',') : encodeURIComponent(value)); */
                      if (!inputsObject[inputName]['value']) { inputsObject[inputName]['value'] = []; }
                      inputsObject[inputName]['value'].push(multiple ? values.join(',') : encodeURIComponent(value));
                      var value = multiple ? values.join(',') : encodeURIComponent(value);

                  }

              }
          }

          //console.log('These are the raw values', inputsObject);
          //_inbound.totalStorage('the_key', inputsObject);
          //var inputsObject = sortInputs(inputsObject);

          var matchCommon = /name|first name|last name|email|e-mail|phone|website|job title|company|tele|address|comment/;

          for (var input in inputsObject) {
              //console.log(input);

              var inputValue = inputsObject[input]['value'];
              var inputMappedField = inputsObject[input]['map'];
              //if (matchCommon.test(input) !== false) {
                  //console.log(input + " Matches Regex run mapping test");
                  //var map = inputsObject[input];
                  //console.log("MAPP", map);
                  //mappedParams.push( input + '=' + inputsObject[input]['value'].join(',') );
              //}

              /* Add custom hook here to look for additional values */
              if (typeof (inputValue) != "undefined" && inputValue != null && inputValue != "") {
                  rawParams.push( input + '=' + inputsObject[input]['value'].join(',') );
              }

              if (typeof (inputMappedField) != "undefined" && inputMappedField != null && inputsObject[input]['value']) {
                //console.log('Data ATTR', formInput.dataset.mapFormField);
                mappedParams.push( inputMappedField + "=" + inputsObject[input]['value'].join(',') );
                if(input === 'email'){
                  var email = inputsObject[input]['value'].join(',');
                  //alert(email);

                }
              }
          }

          var raw_params = rawParams.join('&');
          console.log("Stringified Raw Form PARAMS", raw_params);


          var mapped_params = mappedParams.join('&');
          console.log("Stringified Mapped PARAMS", mapped_params);

          /* Check Use form Email or Cookie */
          var email = utils.getParameterVal('email', mapped_params) || utils.readCookie('wp_lead_email');
          var fullName = utils.getParameterVal('name', mapped_params);
          var fName = utils.getParameterVal('first_name', mapped_params);
          var lName = utils.getParameterVal('last_name', mapped_params);

          // Fallbacks for empty values
          if (!lName && fName) {
            var parts = decodeURI(fName).split(" ");
            if(parts.length > 0){
                fName = parts[0];
                lName = parts[1];
            }
          }

          if(fullName && !lName && !fName){
            var parts = decodeURI(fullName).split(" ");
            if(parts.length > 0){
                fName = parts[0];
                lName = parts[1];
            }
          }

          fullName = (fName && lName) ? fName + " " + lName : fullName;

          console.log(fName); // outputs email address or false
          console.log(lName); // outputs email address or false
          console.log(fullName); // outputs email address or false
          //return false;
          var page_views = _inbound.totalStorage('page_views') || {};
          var urlParams = _inbound.totalStorage('inbound_url_params') || {};

          var inboundDATA = {
            'email': email
          };
          /* Get Variation ID */
          if (typeof (landing_path_info) != "undefined") {
            var variation = landing_path_info.variation;
          } else if (typeof (cta_path_info) != "undefined") {
            var variation = cta_path_info.variation;
          } else {
            var variation = 0;
          }
          var post_type = inbound_settings.post_type || 'page';
          var page_id = inbound_settings.post_id || 0;
          // data['wp_lead_uid'] = jQuery.cookie("wp_lead_uid") || null;
          // data['search_data'] = JSON.stringify(jQuery.totalStorage('inbound_search')) || {};
          search_data = {};
          /* Filter here for raw */
          //alert(mapped_params);
          /**
           * Old data model
              var return_data = {
                        "action": 'inbound_store_lead',
                        "emailTo": data['email'],
                        "first_name": data['first_name'],
                        "last_name": data['last_name'],
                        "phone": data['phone'],
                        "address": data['address'],
                        "company_name": data['company'],
                        "page_views": data['page_views'],
                        "form_input_values": all_form_fields,
                        "Mapped_Data": mapped_form_data,
                        "Search_Data": data['search_data']
              };
           */
          formData = {
            'action': 'inbound_lead_store',
            'email': email,
            "full_name": fullName,
            "first_name": fName,
            "last_name": lName,
            'raw_params' : raw_params,
            'mapped_params' : mapped_params,
            'url_params': JSON.stringify(urlParams),
            'search_data': 'test',
            'page_views': JSON.stringify(page_views),
            'post_type': post_type,
            'page_id': page_id,
            'variation': variation,
            'source': utils.readCookie("inbound_referral_site")
          };
          callback = function(string){
            /* Action Example */
            _inbound.Events.fireEvent('after_form_submission', formData);
            alert('callback fired' + string);
            /* Set Lead cookie ID */
            utils.createCookie("wp_lead_id", string);
            _inbound.totalStorage.deleteItem('page_views'); // remove pageviews
            _inbound.totalStorage.deleteItem('tracking_events'); // remove events
            _inbound.Forms.releaseFormSubmit(form);

          }
          //_inbound.LeadsAPI.makeRequest(landing_path_info.admin_url);
          _inbound.Events.fireEvent('before_form_submission', formData);
          //_inbound.trigger('inbound_form_before_submission', formData, true);

          utils.ajaxPost(inbound_settings.admin_url, formData, callback);
      },
      rememberInputValues: function(input) {
          var name = ( input.name ) ? "inbound_" + input.name : '';
          var type = ( input.type ) ? input.type : 'text';
          if(type === 'submit' || type === 'hidden' || type === 'file' || type === "password") {
              return false;
          }

          utils.addListener(input, 'change', function(e) {

            if(e.target.name) {
                /* Check for input type */
                if(type !== "checkbox") {
                    var value = e.target.value;
                } else {
                  var values = [];
                  var checkboxes = document.querySelectorAll('input[name="'+e.target.name+'"]');
                    for (var i = 0; i < checkboxes.length; i++) {
                      var checked = checkboxes[i].checked;
                      if(checked){
                        values.push(checkboxes[i].value);
                      }
                      value = values.join(',');
                    };
                }
            console.log('change ' + e.target.name  + " " + encodeURIComponent(value));
            /* Set Field Input Cookies */
            utils.createCookie("inbound_" + e.target.name, encodeURIComponent(value));
            // _inbound.totalStorage('the_key', FormStore);
            /* Push to 'unsubmitted form object' */
            }

          });
      },
      fillInputValues: function(input){
          var name = ( input.name ) ? "inbound_" + input.name : '';
          var type = ( input.type ) ? input.type : 'text';
          if(type === 'submit' || type === 'hidden' || type === 'file' || type === "password") {
              return false;
          }
          if(utils.readCookie(name) && name != 'comment' ){

             value = decodeURIComponent(utils.readCookie(name));
             if(type === 'checkbox' || type === 'radio'){
                 var checkbox_vals = value.split(',');
                 for (var i = 0; i < checkbox_vals.length; i++) {
                      if (input.value.indexOf(checkbox_vals[i])>-1) {
                        input.checked = true;
                      }
                 }
             } else {
                if(value !== "undefined"){
                  input.value = value;
                }
             }
          }
      },
      /* Maps data attributes to fields on page load */
      mapField: function(input) {

            var input_id = input.id || false;
            var input_name = input.name || false;

            /* Loop through all match possiblities */
            for (i = 0; i < FieldMapArray.length; i++) {
              //for (var i = FieldMapArray.length - 1; i >= 0; i--) {
               var found = false;
               var match = FieldMapArray[i];
               var lookingFor = utils.trim(match);
               var nice_name = lookingFor.replace(/ /g,'_');

               this.debug('Names',function(){
                   console.log("NICE NAME", nice_name);
                   console.log('looking for match on ' + lookingFor);
               });

               /* look for name attribute match */
               if (input_name && input_name.toLowerCase().indexOf(lookingFor)>-1) {
                  var found = true;
                  this.debug('FOUND name attribute',function(){
                      console.warn('FOUND name: ' + lookingFor);
                  });

               /* look for id match */
               } else if (input_id && input_id.toLowerCase().indexOf(lookingFor)>-1) {
                  var found = true;

                  this.debug('FOUND id:',function(){
                      console.log('FOUND id: ' + lookingFor);
                  });

               /* Check siblings for label */
               } else if (label = this.siblingsIsLabel(input)) {

                  //var label = (label.length > 1 ? label[0] : label);
                  //console.log('label', label);
                  if (label[0].innerText.toLowerCase().indexOf(lookingFor)>-1) {
                      var found = true;

                      this.debug('Sibling matches single label',function(){
                          console.log('FOUND label text: ' + lookingFor);
                      });

                  }
                  /* Check closest li for label */
               } else if (labelText = this.CheckParentForLabel(input)) {

                  this.debug('li labels found in form',function(){
                    console.log(labelText)
                  });

                  if (labelText.toLowerCase().indexOf(lookingFor)>-1) {
                      var found = true;
                  }

               } else {

                  this.debug('NO MATCH',function(){
                      console.log('NO Match on ' + lookingFor + " in " + input_name);
                  });

                  no_match.push(lookingFor);

               }

              /* Map the field */
              if (found) {
                this.addDataAttr(input, nice_name);
                this.removeArrayItem(FieldMapArray, lookingFor);
                i--; //decrement count
              }

            }

            return inbound_data;

      },
      /* Get correct input values */
      getInputValue: function(input) {
                   var value = false;

                   switch (input.type) {
                       case 'radio':
                       case 'checkbox':
                           if (input.checked) {
                               value = input.value;
                               console.log("CHECKBOX VAL", value)
                           }
                           break;

                       case 'text':
                       case 'hidden':
                       default:
                           value = input.value;
                           break;

                   }

                   return value;
      },
      /* Add data-map-form-field attr to input */
      addDataAttr: function(formInput, match){

                      var getAllInputs = document.getElementsByName(formInput.name);
                      for (var i = getAllInputs.length - 1; i >= 0; i--) {
                          if(!formInput.dataset.mapFormField) {
                              getAllInputs[i].dataset.mapFormField = match;
                          }
                      };
      },
      /* Optimize FieldMapArray array for fewer lookups */
      removeArrayItem: function(array, item){
          if (array.indexOf) {
            index = array.indexOf(item);
          } else {
            for (index = array.length - 1; index >= 0; --index) {
              if (array[index] === item) {
                break;
              }
            }
          }
          if (index >= 0) {
            array.splice(index, 1);
          }
          console.log('removed ' + item + " from array");
          return;
      },
      /* Look for siblings that are form labels */
      siblingsIsLabel: function(input){
          var siblings = this.getSiblings(input);
          var labels = [];
          for (var i = siblings.length - 1; i >= 0; i--) {
              if(siblings[i].nodeName.toLowerCase() === 'label'){
                 labels.push(siblings[i]);
              }
          };
          /* if only 1 label */
          if (labels.length > 0 && labels.length < 2){
              return labels;
          }

         return false;
      },
      getChildren: function(n, skipMe){
          var r = [];
          var elem = null;
          for ( ; n; n = n.nextSibling )
             if ( n.nodeType == 1 && n != skipMe)
                r.push( n );
          return r;
      },
      getSiblings: function (n) {
          return this.getChildren(n.parentNode.firstChild, n);
      },
      /* Check parent elements inside form for labels */
      CheckParentForLabel: function(element) {
          if(element.nodeName === 'FORM') { return null; }
            do {
                  var labels = element.getElementsByTagName("label");
                  if (labels.length > 0 && labels.length < 2) {
                      return element.getElementsByTagName("label")[0].innerText;
                  }

            } while(element = element.parentNode);

            return null;
      }

  };

  return _inbound;

})(_inbound || {});
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
/* LocalStorage Component */
var InboundTotalStorage = (function (_inbound){

  var supported, ls, mod = '_inbound';
  if ('localStorage' in window){
    try {
      ls = (typeof window.localStorage === 'undefined') ? undefined : window.localStorage;
      if (typeof ls == 'undefined' || typeof window.JSON == 'undefined'){
        supported = false;
      } else {
        supported = true;
      }
      window.localStorage.setItem(mod, '1');
      window.localStorage.removeItem(mod);
    }
    catch (err){
      supported = false;
    }
  }

  /* Make the methods public */
  _inbound.totalStorage = function(key, value, options){
    return _inbound.totalStorage.impl.init(key, value);
  };

  _inbound.totalStorage.setItem = function(key, value){
    return _inbound.totalStorage.impl.setItem(key, value);
  };

  _inbound.totalStorage.getItem = function(key){
    return _inbound.totalStorage.impl.getItem(key);
  };

  _inbound.totalStorage.getAll = function(){
    return _inbound.totalStorage.impl.getAll();
  };

  _inbound.totalStorage.deleteItem = function(key){
    return _inbound.totalStorage.impl.deleteItem(key);
  };


  _inbound.totalStorage.impl = {

    init: function(key, value){
      if (typeof value != 'undefined') {
        return this.setItem(key, value);
      } else {
        return this.getItem(key);
      }
    },

    setItem: function(key, value){
      if (!supported){
        try {
          _inbound.Utils.createCookie(key, value);
          return value;
        } catch(e){
          console.log('Local Storage not supported by this browser. Install the cookie plugin on your site to take advantage of the same functionality. You can get it at https://github.com/carhartl/jquery-cookie');
        }
      }
      var saver = JSON.stringify(value);
      ls.setItem(key, saver);
      return this.parseResult(saver);
    },
    getItem: function(key){
      if (!supported){
        try {
          return this.parseResult(_inbound.Utils.readCookie(key));
        } catch(e){
          return null;
        }
      }
      var item = ls.getItem(key);
      return this.parseResult(item);
    },
    deleteItem: function(key){
      if (!supported){
        try {
          _inbound.Utils.eraseCookie(key, null);
          return true;
        } catch(e){
          return false;
        }
      }
      ls.removeItem(key);
      return true;
    },
    getAll: function(){
      var items = [];
      if (!supported){
        try {
          var pairs = document.cookie.split(";");
          for (var i = 0; i<pairs.length; i++){
            var pair = pairs[i].split('=');
            var key = pair[0];
            items.push({key:key, value:this.parseResult(_inbound.Utils.readCookie(key))});
          }
        } catch(e){
          return null;
        }
      } else {
        for (var j in ls){
          if (j.length){
            items.push({key:j, value:this.parseResult(ls.getItem(j))});
          }
        }
      }
      return items;
    },
    parseResult: function(res){
      var ret;
      try {
        ret = JSON.parse(res);
        if (typeof ret == 'undefined'){
          ret = res;
        }
        if (ret == 'true'){
          ret = true;
        }
        if (ret == 'false'){
          ret = false;
        }
        if (parseFloat(ret) == ret && typeof ret != "object"){
          ret = parseFloat(ret);
        }
      } catch(e){
        ret = res;
      }
      return ret;
    }
  };
})(_inbound || {});
/**
 * Leads API functions
 * @param  Object _inbound - Main JS object
 * @return Object - include event triggers
 */
var _inboundLeadsAPI = (function (_inbound) {
    var httpRequest;
    _inbound.LeadsAPI =  {
      init: function() {

      },
      getAllLeadData: function(expire_check) {
          var wp_lead_id = _inbound.Utils.readCookie("wp_lead_id"),
          old_data = _inbound.totalStorage('inbound_lead_data'),
          data = {
            action: 'inbound_get_all_lead_data',
            wp_lead_id: wp_lead_id,
          },
          success = function(returnData){
                    var obj = JSON.parse(returnData);
                    console.log('Got all the lead data check ');
                    setGlobalLeadVar(obj);
                    _inbound.totalStorage('inbound_lead_data', obj); // store lead data
          };

          if(!old_data) {
            console.log("No old data");
          }

          if (expire_check === 'true'){
            console.log("Session has not expired");
          }

          if(!old_data && expire_check === null) {
              _inbound.debug('Go to Database',function(){
                   console.log(expire_check);
                   console.log(old_data);
              });
              _inbound.Utils.ajaxPost(inbound_settings.admin_url, data, success);
              //_inbound.Utils.doAjax(data, success);
          } else {
              setGlobalLeadVar(old_data); // set global lead var with localstorage data
              var lead_data_expiration = _inbound.Utils.readCookie("lead_data_expiration");
              if (lead_data_expiration === null) {
                //_inbound.Utils.doAjax(data, success);
                _inbound.Utils.ajaxPost(inbound_settings.admin_url, data, success);
                console.log('localized data old. Pull new from DB');
              }
          }

      },
      getLeadLists: function() {
          var wp_lead_id = _inbound.Utils.readCookie("wp_lead_id");
          var data = {
                  action: 'wpl_check_lists',
                  wp_lead_id: wp_lead_id,
          };
          var success = function(user_id){
                    _inbound.Utils.createCookie("lead_session_list_check", true, { path: '/', expires: 1 });
                    console.log("Lists checked");
          };
          //_inbound.Utils.doAjax(data, success);
          _inbound.Utils.ajaxPost(inbound_settings.admin_url, data, success);
      }
    };

  return _inbound;

})(_inbound || {});
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
/**
 * # Start
 *
 * Runs init functions and runs the domReady functions
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */



if (window.jQuery) {
 jQuery(document).on('inbound_analytics_loaded', function (event, data) {
   console.log("inbound_analytics_loaded");
 });
}
 var Inbound_Add_Filter_Example = function( array ) {
  console.log('filter ran');
  var map = array || [];
  map.push('tehdhshs');
  return map;
 };
 var Inbound_Add_Action_Example = function() {
          console.log('callback triggered');
          //jQuery('form').css('color', 'red');
  };
 _inbound.add_action( 'namespace.identifier', Inbound_Add_Action_Example, 10 );


 _inbound.add_action( 'before_form_submission', alert_form_data, 10 );
 //_inbound.remove_action( 'inbound_form_before_form_submission');
/* raw_js_trigger event trigger */
 window.addEventListener("before_form_submission", raw_js_trigger, false);
 function raw_js_trigger(e){
     var data = e.detail;

     alert('Pure Javascript before_form_submission action fire');
     //alert(JSON.stringify(data));
 }

if (window.jQuery) {
  jQuery(document).on('before_form_submission', function (event, data) {
    console.log("before_form_submission action triggered");
    alert('Run jQuery before_form_submission trigger');
    //alert(JSON.stringify(data));
  });
}

function alert_form_data(data){
  alert('inbound before_form_submission action fire');
  //alert(JSON.stringify(data));
}

function DOIT(){
  alert('DO IT');
}


_inbound.hooks.addAction( 'inbound_form_submission', DOIT, 10 );


 _inbound.init(); // analytics init

 var InboundLeadData = _inbound.totalStorage('inbound_lead_data') || null;
 function setGlobalLeadVar(retString){
     InboundLeadData = retString;
 }

 _inbound.Utils.domReady(window, function(){
    /* Filter Example */
    _inbound.hooks.addFilter( 'inbound.form_map_before', Inbound_Add_Filter_Example, 10 );
    /* On Load Analytics Events */
    _inbound.DomLoaded();
    /* Action Example */
    _inbound.do_action( 'namespace.identifier');


    var utils = _inbound.Utils,
    wp_lead_uid = utils.readCookie("wp_lead_uid"),
    wp_lead_id = utils.readCookie("wp_lead_id"),
    expire_check = utils.readCookie("lead_session_expire"); // check for session

    if (!expire_check) {
       console.log('expired vistor. Run Processes');
      //var data_to_lookup = global-localized-vars;
      if (typeof (wp_lead_id) !== "undefined" && wp_lead_id !== null && wp_lead_id !== "") {
          /* Get InboundLeadData */
          _inbound.LeadsAPI.getAllLeadData(expire_check);
          /* Lead list check */
          _inbound.LeadsAPI.getLeadLists();
        }
    }

  /* Set Session Timeout */
  utils.SetSessionTimeout();

});



 function action_a( value ) {
  window.actionValue += 'a';
  console.log('page view action');
 }
 function action_b( value ) {
  window.actionValue += 'b';
  //alert('priority 2')
 }
 function action_c( value ) {
  window.actionValue += 'c';
  //alert('priority 8')
 }
 window.actionValue = '';

_inbound.hooks.addAction( 'inbound.page_view', action_a );
//_inbound.hooks.addAction( 'test.action', action_c, 8 );
//_inbound.hooks.addAction( 'test.action', action_b, 2 );



  //_inbound.hooks.removeAction( 'test.action' );
if (window.jQuery) {
  jQuery(document).ready(function($) {
       console.log('doing action');
       _inbound.hooks.doAction( 'test.action' );
       console.log(window.actionValue);

   });
 }

/*
URL param action
 */
// Add to page
_inbound.add_action( 'url_parameters', URL_Param_Function, 10 );
// callback function
function URL_Param_Function(urlParams){

	//urlParams = _inbound.apply_filters( 'urlParamFilter', urlParams);

	for( var param in urlParams ) {
		var key = param;
		var value = urlParams[param];
	}

	//alert(JSON.stringify(urlParams));

	/* Check if URL parameter exists and matches value */
	if(urlParams.test === "true") {
		alert('url param true is true');
	}
}

/* Applying filters to your actions */
_inbound.add_filter( 'filter_url_parameters', URL_Param_Filter, 10 );
function URL_Param_Filter(urlParams) {

	var params = urlParams || {};
	/* check for item in object */
	if(params.utm_source !== "undefined"){
		//alert('its here');
	}
	/* delete item from object */
	delete params.utm_source;

	return params;

}

/* Applying filters to your actions */
_inbound.add_filter( 'filter_inbound_analytics_loaded', event_filter_data_example, 10);
function event_filter_data_example(data) {

	var data = data || {};

	/* Add property to data */
	data.add_this = 'additional data';

	/* check for item in object */
	if(data.opt1 === true){
		alert('options.opt1 = true');
	}

	/* Add or modifiy option to event */
	data.options.new_options = 'new option';

	/* delete item from data */
	delete data.utm_source;

	return data;

}

_inbound.add_action( 'tab_hidden', Tab_Hidden_Function, 10 );
function Tab_Hidden_Function(data){
	//alert('NOPE! LOOK AT ME!!!!');
}

_inbound.add_action( 'tab_visible', Tab_vis1_Function, 9 );
function Tab_vis1_Function(data){
	//alert('Welcome back bro 1');
}

_inbound.add_action( 'tab_visible', Tab_vis_Function, 10 );
function Tab_vis_Function(data){
	//alert('Welcome back bro 2');
}

