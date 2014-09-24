/* Fork of jquery.total-storage.js */
var InboundTotalStorage = (function (InboundAnalytics){

  /* Variables I'll need throghout */

  var supported, ls, mod = 'inboundAnalytics';
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
  InboundAnalytics.totalStorage = function(key, value, options){
    return InboundAnalytics.totalStorage.impl.init(key, value);
  };

  InboundAnalytics.totalStorage.setItem = function(key, value){
    return InboundAnalytics.totalStorage.impl.setItem(key, value);
  };

  InboundAnalytics.totalStorage.getItem = function(key){
    return InboundAnalytics.totalStorage.impl.getItem(key);
  };

  InboundAnalytics.totalStorage.getAll = function(){
    return InboundAnalytics.totalStorage.impl.getAll();
  };

  InboundAnalytics.totalStorage.deleteItem = function(key){
    return InboundAnalytics.totalStorage.impl.deleteItem(key);
  };

  /* Object to hold all methods: public and private */

  InboundAnalytics.totalStorage.impl = {

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
          InboundAnalytics.Utils.createCookie(key, value);
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
          return this.parseResult(InboundAnalytics.Utils.readCookie(key));
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
          InboundAnalytics.Utils.eraseCookie(key, null);
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
            items.push({key:key, value:this.parseResult(InboundAnalytics.Utils.readCookie(key))});
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
})(InboundAnalytics || {});