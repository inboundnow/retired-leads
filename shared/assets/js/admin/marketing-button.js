/**
 * Marketing Button JS
 */
var MarketingButton = (function () {

  var _privateMethod = function () {};

  var _once = function(fn, context){
    var result;

    return function() {
        if(fn) {
            result = fn.apply(context || this, arguments);
            fn = null;
        }

        return result;
    };
  };
  var inbound_buttons_loaded = false;
  var Public = {
    init: function () {
        // add listeners to iframes
        this.waitForEditorLoad();
        this.attachClickHandler();
    },
    waitForEditorLoad: function() {
        var that = this;
        jQuery(".acf_postbox .field_type-wysiwyg iframe")
            .waitUntilExists(function(){
                if(!inbound_buttons_loaded) {
                    // do stuff with editor
                    that.addButtonsToACFNormal();

                    inbound_buttons_loaded = true;
                }
            });
    },
    /* Add buttons to normal ACF */
    addButtonsToACFNormal: function(){
        console.log('add buttons');
        jQuery('.acf_postbox .field_type-wysiwyg').each(function(){
            var $this = jQuery(this);
            var label = $this.find('label');
            var iframeID = $this.find('iframe').attr('id');
            console.log('iframe', iframeID);
            var marButton = '<a data-editor="'+iframeID+'" href="#inbound-marketing-popup" class="button inbound-marketing-button open-marketing-button-popup" title="Marketing" style="padding-left: .4em; margin-left:10px;"><span style="width: 20px;height: 25px;display: inline-block;vertical-align: bottom;" class="wp-media-buttons-icon" id="inboundnow-media-button"></span>New Button</a>';
            jQuery(marButton).appendTo(label);
        });

    },
    attachClickHandler: function() {
        var that = this;
        jQuery("body").on('click', '.inbound-marketing-button', function (e) {
            e.preventDefault();
            var id = jQuery(this).attr('data-editor');
            var iframeTarget = document.getElementById(id).contentWindow.document.body;
            /*var pos = that.getCursorPosition(iframeTarget);*/

            /* Run popup here */
            jQuery.magnificPopup.open({
              items: {
                src: '#inbound-marketing-popup', // can be a HTML string, jQuery object, or CSS selector
                type: 'inline'
              }
            });

            /* Mount react app here */

            setTimeout(function() {
                 that.insertContent('WOWOOOOOO', iframeTarget);
            }, 2000);
        });
        // on marketing button click, grab ID to insert to
    },
    onChangeHandler: function(){

    },
    insertContent: function(text, iframe) {
                    var sel, range, html;
                    var doc = iframe.ownerDocument || iframe.document;
                    var win = doc.defaultView || doc.parentWindow;
                    sel = win.getSelection();
                    if (sel && sel.rangeCount > 0) {
                        console.log('Content inserted!');
                        range = sel.getRangeAt(0);
                        range.deleteContents();
                        var textNode = document.createTextNode(text);
                        range.insertNode(textNode);
                        range.setStartAfter(textNode);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    } else {
                        console.log('havent clicked in box yet');
                        /* focus for the user to insert the content */
                        iframe.focus();
                        console.log('run this again');
                        this.insertContent(text, iframe);
                    }
     },
    getCursorPosition: function (iframe) {
        var caretOffset = 0,
        doc = iframe.ownerDocument || iframe.document,
        win = doc.defaultView || doc.parentWindow,
        sel;

        if (typeof win.getSelection != "undefined") {
            sel = win.getSelection();
            if (sel.rangeCount > 0) {
                var range = win.getSelection().getRangeAt(0);
                var preCaretRange = range.cloneRange();
                preCaretRange.selectNodeContents(iframe);
                preCaretRange.setEnd(range.endContainer, range.endOffset);
                caretOffset = preCaretRange.toString().length;
            }
        } else if ( (sel = doc.selection) && sel.type != "Control") {
            var textRange = sel.createRange();
            var preCaretTextRange = doc.body.createTextRange();
            preCaretTextRange.moveToElementText(iframe);
            preCaretTextRange.setEndPoint("EndToEnd", textRange);
            caretOffset = preCaretTextRange.text.length;
        }

        return caretOffset;
    }
  };

  return Public;

})();

jQuery(document).ready(function($) {
    console.log('Markeint buttons gooooo')
    MarketingButton.init();

});

(function ($) {

/**
* @function
* @property {object} jQuery plugin which runs handler function once specified element is inserted into the DOM
* @param {function} handler A function to execute at the time when the element is inserted
* @param {bool} shouldRunHandlerOnce Optional: if true, handler is unbound after its first invocation
* @example $(selector).waitUntilExists(function);
*/

$.fn.waitUntilExists = function (handler, shouldRunHandlerOnce, isChild) {
    var found       = 'found';
    var $this       = $(this.selector);
    var $elements   = $this.not(function () { return $(this).data(found); }).each(handler).data(found, true);

    if (!isChild) {
        (window.wait_until_exists = window.wait_until_exists || {})[this.selector] =  window.setInterval(function () {
                $this.waitUntilExists(handler, shouldRunHandlerOnce, true);
            }, 500)
        ;
    } else if (shouldRunHandlerOnce && $elements.length) {
        window.clearInterval(window.wait_until_exists[this.selector]);
    }

    return $this;
}

}(jQuery));
