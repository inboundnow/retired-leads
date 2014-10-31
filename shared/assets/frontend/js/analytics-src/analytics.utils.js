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