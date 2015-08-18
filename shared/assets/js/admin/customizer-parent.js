var InboundCustomizerParent = (function () {

  var _privateMethod = function () {};

  var myObject = {
    init:  function () {
        console.log('parent init');
        jQuery("#wp-admin-bar-edit a").text("Main Edit Screen");
    },
    anotherMethod:  function () {

    }
  };

  return myObject;

})();

jQuery(document).ready(function($) {
   InboundCustomizerParent.init();
});



var ModuleTwo = (function (Module) {

    Module.extension = function () {
        // another method!
    };

    return Module;

})(InboundCustomizerParent || {});