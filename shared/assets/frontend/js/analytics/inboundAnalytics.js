/*! Inbound Analyticsv1.0.0 | (c) 2014 Inbound Now | https://github.com/inboundnow/cta */
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
  //timeout: 10000
};

var _inbound = (function (options) {
   /* Constants */
   var defaults = {
        debugMode : false,
        timeout: 30000
   };

   var Analytics = {
     /* Initialize individual modules */
     init: function () {
         _inbound.Utils.init();
         _inbound.PageTracking.StorePageView();
         _inbound.Events.loadEvents(settings);
     },
     DomLoaded: function(){
        _inbound.Forms.init();
        setTimeout(function() {
             _inbound.Forms.init();
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

   var settings = Analytics.getSettings(defaults, options);
   /* Set globals */
   Analytics.Settings = settings || {};

  return Analytics;

})(_inboundOptions);
/**
 * Utility functions
 * @param  Object _inbound - Main JS object
 * include util functions
 */
var _inboundUtils = (function (_inbound) {

    /* Private methods here */

    _inbound.Utils =  {
      init: function() {
          this.polyFills();
          this.setUrlParams();
          this.SetUID();
          this.getReferer();
      },
      /* http://stackoverflow.com/questions/951791/javascript-global-error-handling */
      /* Polyfills for missing browser functionality */
      polyFills: function() {
           /* Console.log fix for old browsers */
           if (!window.console) { window.console = {}; }
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
           /* Event trigger polyfill for IE9 and 10 */
           (function () {
             function CustomEvent ( event, params ) {
               params = params || { bubbles: false, cancelable: false, detail: undefined };
               var evt = document.createEvent( 'CustomEvent' );
               evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
               return evt;
              }

             CustomEvent.prototype = window.Event.prototype;

             window.CustomEvent = CustomEvent;
           })();
      },
      // Create cookie
      createCookie: function(name, value, days, custom_time) {
          var expires = "";
          if (days) {
              var date = new Date();
              date.setTime(date.getTime()+(days*24*60*60*1000));
              expires = "; expires="+date.toGMTString();
          }
          if(custom_time){
             expires = "; expires="+days.toGMTString();
          }
          document.cookie = name+"="+value+expires+"; path=/";
      },
      /* Read cookie */
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
      /* Erase cookies */
      eraseCookie: function(name) {
          createCookie(name,"",-1);
      },
      /* Get All Cookies */
      getAllCookies: function(){
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
          var urlParams = {},
          local_store = this.checkLocalStorage();

            (function () {
              var e,
                d = function (s) { return decodeURIComponent(s).replace(/\+/g, " "); },
                q = window.location.search.substring(1),
                r = /([^&=]+)=?([^&]*)/g;

              while (e = r.exec(q)) {
                if (e[1].indexOf("[") == "-1")
                  urlParams[d(e[1])] = d(e[2]);
                else {
                  var b1 = e[1].indexOf("["),
                    aN = e[1].slice(b1+1, e[1].indexOf("]", b1)),
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

            if (JSON) {
                for (var k in urlParams) {
                  if (typeof urlParams[k] == "object") {
                    for (var k2 in urlParams[k])
                    this.createCookie(k2, urlParams[k][k2], 30);
                  } else {
                    this.createCookie(k, urlParams[k], 30);
                  }
                 }
            }

            if(local_store){
              var pastParams =  _inbound.totalStorage('inbound_url_params');
              var params = this.mergeObjs(pastParams, urlParams);
              _inbound.totalStorage('inbound_url_params', params); // store cookie data
            }
      },
      getUrlParams: function(){
          var local_store = this.checkLocalStorage(),
          get_params = {};
          if(local_store){
            var get_params = _inbound.totalStorage('inbound_url_params');
          }
          return get_params;
      },
      // Check local storage
      // provate browsing safari fix https://github.com/marcuswestin/store.js/issues/42#issuecomment-25274685
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
      addDays: function(myDate,days) {
        return new Date(myDate.getTime() + days*24*60*60*1000);
      },
      GetDate: function(){
        var time_now = new Date(),
        day = time_now.getDate() + 1;
        year = time_now.getFullYear(),
        hour = time_now.getHours(),
        minutes = time_now.getMinutes(),
        seconds = time_now.getSeconds(),
        month = time_now.getMonth() + 1;
        if (month < 10) { month = '0' + month; }
        _inbound.debug('Current Date:',function(){
            console.log(year + '/' + month + "/" + day + " " + hour + ":" + minutes + ":" + seconds);
        });
        var datetime = year + '/' + month + "/" + day + " " + hour + ":" + minutes + ":" + seconds;
        return datetime;
      },
      /* Set Expiration Date of Session Logging */
      SetSessionTimeout: function(){
          var session_check = this.readCookie("lead_session_expire");
          //console.log(session_check);
          if(session_check === null){
            _inbound.Events.sessionStart(); // trigger 'inbound_analytics_session_start'
          } else {
            _inbound.Events.sessionActive(); // trigger 'inbound_analytics_session_active'
          }
          var d = new Date();
          d.setTime(d.getTime() + 30*60*1000);

          this.createCookie("lead_session_expire", true, d, true); // Set cookie on page loads
          var lead_data_expiration = this.readCookie("lead_data_expiration");
          if (lead_data_expiration === null){
            /* Set 3 day timeout for checking DB for new lead data for Lead_Global var */
            var ex = this.addDays(d, 3);
            this.createCookie("lead_data_expiration", ex, ex, true);
          }

      },
      getReferer: function(){
        //console.log(expire_time);
        var d = new Date();
        d.setTime(d.getTime() + 30*60*1000);
        var referrer_cookie = _inbound.Utils.readCookie("wp_lead_referral_site");
        if (typeof (referrer_cookie) === "undefined" || referrer_cookie === null || referrer_cookie === "") {
          var referrer = document.referrer || "NA";
          this.createCookie("wp_lead_referral_site", referrer, d, true); // Set cookie on page loads
        }
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
      },
      SetUID:  function () {
       /* Set Lead UID */
       if(this.readCookie("wp_lead_uid") === null) {
          var wp_lead_uid =  this.CreateUID(35);
          this.createCookie("wp_lead_uid", wp_lead_uid );
          _inbound.debug('Set UID');
       }
      },
      /* Count number of session visits */
      countProperties: function (obj) {
          var count = 0;
          for(var prop in obj) {
              if(obj.hasOwnProperty(prop))
                  ++count;
          }
          return count;
      },
      mergeObjs:  function(obj1,obj2){
            var obj3 = {};
            for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
            for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
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
      addClass: function(className, elem){
        if ('classList' in document.documentElement) {
              elem.classList.add(className);
        } else {
           if (!this.hasClass(elem, className)) {
             elem.className += (elem.className ? ' ' : '') + className;
           }
        }
      },
      removeClass: function(className, elem){
        if ('classList' in document.documentElement) {

            elem.classList.remove(className);
        } else {
          if (this.hasClass(elem, className)) {
            elem.className = elem.className.replace(new RegExp('(^|\\s)*' + className + '(\\s|$)*', 'g'), '');
          }
        }
      },
      trim: function(s) {
          s = s.replace(/(^\s*)|(\s*$)/gi,"");
          s = s.replace(/[ ]{2,}/gi," ");
          s = s.replace(/\n /,"\n"); return s;
      },
      doAjax: function(data, responseHandler, method, async){
      // Set the variables
      var url = wplft.admin_url || "",
      method = method || "POST",
      async = async || true,
      data = data || null,
      action = data.action;

      _inbound.debug('Ajax Processed:',function(){
           console.log('ran ajax action: ' + action);
      });

      jQuery.ajax({
          type: method,
          url: wplft.admin_url,
          data: data,
          success: responseHandler,
          error: function(MLHttpRequest, textStatus, errorThrown){
            console.log(MLHttpRequest+' '+errorThrown+' '+textStatus);
            _inbound.Events.analyticsError(MLHttpRequest, textStatus, errorThrown);
          }

        });
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
        for(var i = 0; i < versions.length; i++) {
            try {
                xhr = new ActiveXObject(versions[i]);
                break;
            } catch (e) {
            }
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
          }
          catch (e) {
            try {
              httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e) {}
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

      var done = false, top = true,

      doc = win.document, root = doc.documentElement,

      add = doc.addEventListener ? 'addEventListener' : 'attachEvent',
      rem = doc.addEventListener ? 'removeEventListener' : 'detachEvent',
      pre = doc.addEventListener ? '' : 'on',

      init = function(e) {
        if (e.type == 'readystatechange' && doc.readyState != 'complete') return;
        (e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
        if (!done && (done = true)) fn.call(win, e.type || e);
      },

      poll = function() {
        try { root.doScroll('left'); } catch(e) { setTimeout(poll, 50); return; }
        init('poll');
      };

      if (doc.readyState == 'complete') fn.call(win, 'lazy');
      else {
        if (doc.createEventObject && root.doScroll) {
          try { top = !win.frameElement; } catch(e) { }
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
      if(element.addEventListener) {
        element.addEventListener(eventName, listener, false);
      } else if (element.attachEvent) {
        element.attachEvent("on" + eventName, listener);
      } else {
        element['on' + eventName] = listener;
      }
    },
    removeListener: function(element, eventName, listener) {
      console.log('test');
      console.log(listener);
      if (element.removeEventListener) {
         element.removeEventListener(eventName, listener, false);
      } else if (element.detachEvent) {
         element.detachEvent("on" + eventName, listener);
      } else {
         element["on" + eventName] = null;
      }
    }
  };

  return _inbound;

})(_inbound || {});
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

    return _inbound;

})(_inbound || {});
/**
 * # Inbound Analytics Form Functions
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
    mappedParams = [];

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
          console.log(_inbound.hooks);
          _inbound.Forms.runFieldMappingFilters();
          _inbound.Forms.assignTrackClass();
          _inbound.Forms.formTrackInit();
      },
      /**
       * This triggers the forms.field_map filter on the mapping array.
       * This will allow you to add or remore Items from the mapping lookup
       *
       * ### Example
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
      assignTrackClass: function(form) {
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
                            this.mapField(formInput);
                            /* Remember visible inputs */
                            this.rememberInputValues(formInput);

                        }
                        for (var i = hiddenInputs.length - 1; i >= 0; i--) {
                            formInput = hiddenInputs[i];
                            this.mapField(formInput);
                        };

                    //console.log('mapping on load completed');
      },
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
        }, 1000);

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
                }
              }
          }

          var raw_params = rawParams.join('&');
          console.log("Raw PARAMS", raw_params);
          var mapped_params = mappedParams.join('&');
          console.log("Mapped PARAMS", mapped_params);
          var page_views = _inbound.totalStorage('page_views') || {};

          var inboundDATA = {
            'email': email
          };
          search_data = {};
          /* Filter here for raw */
          //alert(mapped_params);
          formData = {
            'raw_params' : raw_params,
            'mapped_params' : mapped_params,
            'action': 'inbound_lead_store',
            'email': 'jimbo@test.com',
            'search_data': 'test',
            'page_views': page_views,
            'post_type': 'landing-page'
          };
          callback = function(string){
            /* Action Example */
            _inbound.hooks.doAction( 'inbound_form_after_submission');
            alert('callback fired' + string);

            _inbound.Forms.releaseFormSubmit(form);
            //form.submit();
            setTimeout(function() {
              for (var i=0; i < form.elements.length; i++) {
                  if (form.elements[i] === "submit") {
                    form.elements[i].click();
                  }
              }
            }, 1000);

          }
          //_inbound.LeadsAPI.makeRequest(landing_path_info.admin_url);
          utils.ajaxPost(landing_path_info.admin_url, formData, callback);
      },

      rememberInputValues: function(input) {

          var name = ( input.name ) ? "inbound_" + input.name : '';
          var type = ( input.type ) ? input.type : 'text';
          if(type === 'submit' || type === 'hidden' || type === 'checkbox' || type === 'file' || type === "password") {
              return false;
          }

            if(utils.readCookie(name) && name != 'comment' ){
                //jQuery(this).val( jQuery.cookie(name) );
               value = decodeURIComponent(utils.readCookie(name));
               input.value = value;
            }

            utils.addListener(input, 'change', function(e) {
              /* TODO Fix the correct Value */
              console.log('change ' + e.target.name  + " " + encodeURIComponent(e.target.value));
              var fieldname = e.target.name.replace(/-/g, "_");

              utils.createCookie("inbound_" + e.target.name, encodeURIComponent(e.target.value));
              // _inbound.totalStorage('the_key', FormStore);
              /* Push to 'unsubmitted form object' */
            });
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
 * Event functions
 * @param  Object _inbound - Main JS object
 * @return Object - include event triggers
 */
// https://github.com/carldanley/WP-JS-Hooks/blob/master/src/event-manager.js
var _inboundEvents = (function (_inbound) {
    console.log(_inbound.Settings);
    _inbound.Events =  {
      // Create cookie
      loadEvents: function(test) {
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

  /* Object to hold all methods: public and private */

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
      storeLeadData: function(){
        if(element.addEventListener) {
            element.addEventListener("submit", function(evt){
                evt.preventDefault();
                window.history.back();
            }, true);
        } else {
            element.attachEvent('onsubmit', function(evt){
                evt.preventDefault();
                window.history.back();
            });
        }
      },
      inbound_map_fields: function (el, value, Obj) {
          var formObj = [];
          var $this = el;
          var clean_output = value;
          var label = $this.closest('label').text();
          var exclude = ['credit-card']; // exlcude values from formObj
          var inarray = jQuery.inArray(clean_output, exclude);
          if(inarray == 0){
            return null;
          }
          // Add items to formObj
          formObj.push({
              field_label: label,
              field_name: $this.attr("name"),
              field_value: $this.attr("value"),
              field_id: $this.attr("id"),
              field_class: $this.attr("class"),
              field_type: $this.attr("type"),
              match: clean_output,
              js_selector: $this.attr("data-js-selector")
          });
          return formObj;
        },
       run_field_map_function: function (el, lookingfor) {
         var return_form;
         var formObj = new Array();
         var $this = el;
         var body = jQuery("body");
         var input_id = $this.attr("id") || "NULL";
         var input_name = $this.attr("name") || "NULL";
         var this_val = $this.attr("value");
         var array = lookingfor.split(",");
         var array_length = array.length - 1;

             // Main Loop
             for (var i = 0; i < array.length; i++) {
                 var clean_output = _inbound.Utils.trim(array[i]);
                 var nice_name = clean_output.replace(/^\s+|\s+$/g,'');
                 var nice_name = nice_name.replace(" ",'_');
                 var in_object_already = nice_name in inbound_data;
                 //console.log(clean_output);

                 if (input_name.toLowerCase().indexOf(clean_output)>-1) {
                   /*  Look for attr name match */
                   var the_map = _inbound.LeadsAPI.inbound_map_fields($this, clean_output, formObj);
                   _inbound.LeadsAPI.add_inbound_form_class($this, clean_output);
                   console.log('match name: ' + clean_output);
                   console.log(nice_name in inbound_data);
                    if (!in_object_already) {
                     inbound_data[nice_name] = this_val;
                    }
                 } else if (input_id.toLowerCase().indexOf(clean_output)>-1) {
                  /* look for id match */
                   var the_map = _inbound.LeadsAPI.inbound_map_fields($this, clean_output, formObj);
                   _inbound.LeadsAPI.add_inbound_form_class($this, clean_output);
                   console.log('match id: ' + clean_output);

                    if (!in_object_already) {
                      inbound_data[nice_name] = this_val;
                    }

                 } else if ($this.closest('li').children('label').length>0) {
                  /* Look for label name match */
                  var closest_label = $this.closest('li').children('label').html() || "NULL";
                   if (closest_label.toLowerCase().indexOf(clean_output)>-1) {

                     var the_map = _inbound.LeadsAPI.inbound_map_fields($this, clean_output, formObj);
                     _inbound.LeadsAPI.add_inbound_form_class($this, clean_output);
                     console.log($this.context);

                     var exists_in_dom = body.find("[data-inbound-form-map='inbound_map_" + nice_name + "']").length;
                     console.log(exists_in_dom);
                     console.log('match li: ' + clean_output);

                     if (!in_object_already) {
                      inbound_data[nice_name] = this_val;
                     }

                   }
                 } else if ($this.closest('div').children('label').length>0) {
                  /* Look for closest div label name match */
                  var closest_div = $this.closest('div').children('label').html() || "NULL";
                   if (closest_div.toLowerCase().indexOf(clean_output)>-1)
                   {
                     var the_map = _inbound.LeadsAPI.inbound_map_fields($this, clean_output, formObj);
                     _inbound.LeadsAPI.add_inbound_form_class($this, clean_output);
                     console.log('match div: ' + clean_output);
                     if (!in_object_already) {
                     inbound_data[nice_name] = this_val;
                    }
                   }
                 } else if ($this.closest('p').children('label').length>0) {
                  /* Look for closest p label name match */
                  var closest_p = $this.closest('p').children('label').html() || "NULL";
                   if (closest_p.toLowerCase().indexOf(clean_output)>-1)
                   {
                     var the_map = _inbound.LeadsAPI.inbound_map_fields($this, clean_output, formObj);
                     _inbound.LeadsAPI.add_inbound_form_class($this, clean_output);
                     console.log('match p: ' + clean_output);
                     if (!in_object_already) {
                     inbound_data[nice_name] = this_val;
                    }
                   }
                 } else {
                  console.log('Need additional mapping data');
                 }
             }
             return_form = the_map;

         return inbound_data;
       },
       add_inbound_form_class: function(el, value) {
         var value = value.replace(" ", "_");
         var value = value.replace("-", "_");
         el.addClass('inbound_map_value');
         el.attr('data-inbound-form-map', 'inbound_map_' + value);
       },
       inbound_form_type: function(this_form) {
        var inbound_data = inbound_data || {},
        form_type = 'normal';
        if ( this_form.is( ".wpl-comment-form" ) ) {
          inbound_data['form_type'] = 'comment';
          form_type = 'comment';
        } else if ( this_form.is( ".wpl-search-box" ) ) {
          var is_search = true;
          form_type = 'search';
          inbound_data['form_type'] = 'search';
        } else if ( this_form.is( '.wpl-track-me-link' ) ){
          var have_email = readCookie('wp_lead_email');
          console.log(have_email);
          inbound_data['form_type'] = 'link';
          form_type = 'search';
        }
        return form_type;
       },
       grab_all_form_input_vals: function(this_form){
        var post_values = post_values || {},
        inbound_exclude = inbound_exclude || [],
        form_inputs = this_form.find('input,textarea,select');
        inbound_exclude.push('inbound_furl', 'inbound_current_page_url', 'inbound_notify', 'inbound_submitted', 'post_type', 'post_status', 's', 'inbound_form_name', 'inbound_form_id', 'inbound_form_lists');
        var form_type = _inbound.LeadsAPI.inbound_form_type(this_form),
        inbound_data = inbound_data || {},
        email = inbound_data['email'] || false;

        form_inputs.each(function() {
          var $input = jQuery(this),
          input_type = $input.attr('type'),
          input_val = $input.val();
          if (input_type === 'checkbox') {
            input_checked = $input.attr("checked");
            console.log(input_val);
            console.log(input_checked);
            console.log(post_values[this.name]);
            if (input_checked === "checked"){
            if (typeof (post_values[this.name]) != "undefined") {
              post_values[this.name] = post_values[this.name] + "," + input_val;
              console.log(post_values[this.name]);
            } else {
              post_values[this.name] = input_val;
            }

            }
          }
          if (jQuery.inArray(this.name, inbound_exclude) === -1 && input_type != 'checkbox'){
             post_values[this.name] = input_val;
          }
          if (this.value.indexOf('@')>-1&&!email){
            email = input_val;
            inbound_data['email'] = email;
          }
          if (form_type === 'search') {
            inbound_data['search_keyword'] = input_val.replace('"', "'");
          }
        });
        var all_form_fields = JSON.stringify(post_values);
        return all_form_fields;
       },
       return_mapped_values: function (this_form) {
        // Map form fields
        jQuery(this_form).find('input[type!="hidden"],textarea,select').each(function() {
          console.log('run');
          var this_input = jQuery(this);
          var this_input_val = this_input.val();
          if (typeof (this_input_val) != "undefined" && this_input_val != null && this_input_val != "") {
          var inbound_data = _inbound.LeadsAPI.run_field_map_function( this_input, "name, first name, last name, email, e-mail, phone, website, job title, company, tele, address, comment");
          }
          return inbound_data;
        });
        return inbound_data;
       },
       inbound_form_submit: function(this_form, e) {
        /* Define Variables */
        var data = inbound_data || {};
        // Dynamic JS object for passing custom values. This can be hooked into by third parties by using the below syntax.
        var pageviewObj = jQuery.totalStorage('page_views');
        data['page_view_count'] = _inbound.Utils.countProperties(pageviewObj);
        data['leads_list'] = jQuery(this_form).find('#inbound_form_lists').val();
        data['source'] = jQuery.cookie("wp_lead_referral_site") || "NA";
        data['page_id'] = inbound_ajax.post_id;
        data['page_views'] = JSON.stringify(pageviewObj);

        // Map form fields
        var returned_form_data = _inbound.LeadsAPI.return_mapped_values(this_form); //console.log(returned_form_data);
        var data = _inbound.Utils.mergeObjs(data,returned_form_data); //console.log(data);
        var this_form = jQuery(this_form);
        // Set variables after mapping
        data['email'] = (!data['email']) ? this_form.find('.inbound-email').val() : data['email'];
        data['form_name'] = this_form.find('.inbound_form_name').val() || "Not Found";
        data['form_id'] = this_form.find('.inbound_form_id').val() || "Not Found";
        data['first_name'] = (!data['first_name']) ? data['name'] : data['first_name'];
        data['last_name'] = data['last_name'] || '';
        data['phone'] = data['phone'] || '';
        data['company'] = data['company'] || '';
        data['address'] = data['address'] || '';

        // Fallbacks for values
        data['name'] = (data['first_name'] && data['last_name']) ? data['first_name'] + " " + data['last_name'] : data['name'];

        if (!data['last_name'] && data['first_name']) {
          var parts = data['first_name'].split(" ");
          data['first_name'] = parts[0];
          data['last_name'] = parts[1];
        }

        /* Store form fields & exclude field values */
        var all_form_fields = _inbound.LeadsAPI.grab_all_form_input_vals(this_form);
        /* end Store form fields & exclude field values */

        if(data['email']){
           _inbound.Utils.createCookie("wp_lead_email", data['email'], 365); /* set email cookie */
        }

        //var variation = (typeof (landing_path_info) != "undefined") ? landing_path_info.variation : false;

        if (typeof (landing_path_info) != "undefined") {
          var variation = landing_path_info.variation;
        } else if (typeof (cta_path_info) != "undefined") {
          var variation = cta_path_info.variation;
        } else {
          var variation = 0;
        }

        data['variation'] = variation;
        data['post_type'] = inbound_ajax.post_type;
        data['wp_lead_uid'] = jQuery.cookie("wp_lead_uid") || null;
        data['ip_address'] = inbound_ajax.ip_address;
        data['search_data'] = JSON.stringify(jQuery.totalStorage('inbound_search')) || {};

        var lp_check = (inbound_ajax.post_type === 'landing-page') ? 'Landing Page' : "";
        var cta_check = (inbound_ajax.post_type === 'wp-call-to-action') ? 'Call to Action' : "";
        var page_type = (!cta_check && !lp_check) ? inbound_ajax.post_type : lp_check + cta_check;

        // jsonify data
        var mapped_form_data = JSON.stringify(data);

        var return_data = {};
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
        }
        return return_data;
      },
      formSubmit: function (e){
        /*if(!confirm('Are you sure?')) {
          e.returnValue = false;
          if(e.preventDefault) e.preventDefault();
          return false;
        }
        return true;*/
        /*var inbound_data = inbound_data || {},
        this_form = e.target,
        event_type = e.type,
        is_search = false,
        form_type = 'normal';*/

        e.preventDefault(); /* Halt form processing */
        console.log("This works");
        var data = _inbound.LeadsAPI.inbound_form_submit(e.target, e); // big function for processing
        console.log(data);
        alert('Working');
        //document.getElementById("ajaxButton").onclick = function() { makeRequest('test.html'); };

        /* Final Ajax Call on Submit */
        _inbound.LeadsAPI.makeRequest('test.html');
      },
       alertContents: function() {
         if (httpRequest.readyState === 4) {
           if (httpRequest.status === 200) {
             alert(httpRequest.responseText);
           } else if(xmlhttp.status == 400) {
             alert('There was an error 400');
           } else {
             alert('There was a problem with the request.');
           }
         }
       },
      getAllLeadData: function(expire_check) {
          var wp_lead_id = _inbound.Utils.readCookie("wp_lead_id"),
          old_data = jQuery.totalStorage('inbound_lead_data'),
          data = {
            action: 'inbound_get_all_lead_data',
            wp_lead_id: wp_lead_id,
          },
          success = function(returnData){
                    var obj = JSON.parse(returnData);
                    console.log('Got all the lead data check ');
                    setGlobalLeadVar(obj);
                    jQuery.totalStorage('inbound_lead_data', obj); // store lead data
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
              _inbound.Utils.doAjax(data, success);
          } else {
              setGlobalLeadVar(old_data); // set global lead var with localstorage data
              var lead_data_expiration = _inbound.Utils.readCookie("lead_data_expiration");
              if (lead_data_expiration === null) {
                _inbound.Utils.doAjax(data, success);
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
                    jQuery.cookie("lead_session_list_check", true, { path: '/', expires: 1 });
                    console.log("Lists checked");
          };
          _inbound.Utils.doAjax(data, success);
      }
    };

  return _inbound;

})(_inbound || {});
var _inboundPageTracking = (function (_inbound) {

    _inbound.PageTracking = {

    getPageViews: function () {
        var local_store = _inbound.Utils.checkLocalStorage();
        if(local_store){
          var page_views = localStorage.getItem("page_views"),
          local_object = JSON.parse(page_views);
          if (typeof local_object =='object' && local_object) {
            this.StorePageView();
          }
          return local_object;
        }
    },
    StorePageView: function() {
          var timeout = this.CheckTimeOut();
          var pageviewObj = _inbound.totalStorage('page_views');
          if(pageviewObj === null) {
            pageviewObj = {};
          }
          var current_page_id = wplft.post_id;
          var datetime = _inbound.Utils.GetDate();

          if (timeout) {
              // If pageviewObj exists, do this
              var page_seen = pageviewObj[current_page_id];

              if(typeof(page_seen) != "undefined" && page_seen !== null) {
                  pageviewObj[current_page_id].push(datetime);
                  /* Page Revisit Trigger */
                  var page_seen_count = pageviewObj[current_page_id].length;
                  _inbound.Events.pageRevisit(page_seen_count);

              } else {
                  pageviewObj[current_page_id] = [];
                  pageviewObj[current_page_id].push(datetime);
                  /* Page First Seen Trigger */
                  var page_seen_count = 1;
                  _inbound.Events.pageFirstView(page_seen_count);
              }

              _inbound.totalStorage('page_views', pageviewObj);

          }
    },
    CheckTimeOut: function() {
        var PageViews = _inbound.totalStorage('page_views') || {};
        var page_id = wplft.post_id,
        pageviewTimeout = true, /* Default */
        page_seen = PageViews[page_id];
        if(typeof(page_seen) !== "undefined" && page_seen !== null) {

            var time_now = _inbound.Utils.GetDate(),
            vc = PageViews[page_id].length - 1,
            last_view = PageViews[page_id][vc],
            last_view_ms = new Date(last_view).getTime(),
            time_now_ms = new Date(time_now).getTime(),
            timeout_ms = last_view_ms + 30*1000,
            time_check = Math.abs(last_view_ms - time_now_ms),
            wait_time = _inbound.Settings.timeout || 30000;

            _inbound.debug('Timeout Checks =',function(){
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
              var status = wait_time / 1000 + ' sec timeout not done: ' + time_left + " seconds left";
            } else {
              var status = 'Timeout Happened. Page view fired';
              this.firePageView();
              pageviewTimeout = true;
              _inbound.Events.analyticsTriggered();
            }

            //_inbound.debug('',function(){
                 console.log(status);
            //});
       } else {
          /* Page never seen before */
          this.firePageView();
       }

       return pageviewTimeout;

    },
    firePageView: function() {
      var lead_id = _inbound.Utils.readCookie('wp_lead_id'),
      lead_uid = _inbound.Utils.readCookie('wp_lead_uid');

      if (typeof (lead_id) !== "undefined" && lead_id !== null && lead_id !== "") {

        _inbound.debug('Run page view ajax');

        var data = {
                action: 'wpl_track_user',
                wp_lead_uid: lead_uid,
                wp_lead_id: lead_id,
                page_id: wplft.post_id,
                current_url: window.location.href,
                json: '0'
              };
        var firePageCallback = function(user_id){
                _inbound.Events.analyticsSaved();
        };
        _inbound.Utils.doAjax(data, firePageCallback);
      }
    },
    tabSwitch: function() {
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

        document.addEventListener(visibilityChange, function() {
          if(document_hidden != document[hidden]) {
            if(document[hidden]) {
              // Document hidden
              console.log('hidden');
              _inbound.Events.browserTabHidden();
            } else {
              // Document shown
              console.log('shown');
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
 * Init Inbound Analytics
 * - initializes analytics
 */

 var Inbound_Add_Filter_Example = function( array ) {
  console.log('filter ran');
  var map = array || [];
  map.push('tehdhshs');
  return map;
 };
 var Inbound_Add_Action_Example = function(){ console.log('callback triggered')};
 _inbound.hooks.addAction( 'namespace.identifier', Inbound_Add_Action_Example, 10 );

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
    _inbound.hooks.doAction( 'namespace.identifier');


    var utils = _inbound.Utils,
    wp_lead_uid = utils.readCookie("wp_lead_uid"),
    wp_lead_id = utils.readCookie("wp_lead_id"),
    expire_check = utils.readCookie("lead_session_expire"); // check for session

    if (expire_check === null) {
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
  console.log('page view action')
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

jQuery(document).ready(function($) {
     console.log('doing action');
     _inbound.hooks.doAction( 'test.action' );
     console.log(window.actionValue);

 });