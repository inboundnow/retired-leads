jQuery(document).ready(function($) { 
    var content_placement = cta_display.wp_cta_obj;
    if (typeof (content_placement) != "undefined" && content_placement != null && content_placement != "") {  
    var cta = cta_display.wp_cta_obj[Math.floor(Math.random()*cta_display.wp_cta_obj.length)];
    var url = cta.url;
    var num = cta.count;
    var behave = cta.behavorial;
    jQuery("#cta-popup-id").text(cta.id);
    //console.log(behave);
    var rand = Math.floor(Math.random()*num);
}
    
    /* Notes 
    get_option of global behavorial ctas with lists. array ctaid[ctaid]['listsarray']
    check cookie for value inarray
    rand through list and replace iframe link

    */
    //console.log( url + rand);
    //var cta_width = cta['variation'][rand].cta_width; // might be caching
    //var cta_height = cta['variation'][rand].cta_height; // might be caching
    //console.log( cta_width + cta_height);
    //var full_link = url + "?wp-cta-variation-id=" + rand;
    //var extra_params = "&cta";
    //jQuery("#wp-cta").attr("src", full_link + extra_params);
    jQuery("#wp-cta-per-page").attr("src", url );
    var widget_defaults = jQuery('iframe#wp-cta').hasClass("widget-default-cta-size");
    console.log(widget_defaults);
    $( "iframe.wp-cta-display" ).each(function(index, value) { 
        var the_frame = jQuery(this);
        //console.log(the_frame);
        
        the_frame.load(function() {
            var popon = false;
            // if admin set height. do this
            var frame_dimensions = the_frame.get(0).contentWindow.cta_options;
            var cta_width = frame_dimensions.cta_width; // might be caching
            var cta_height = frame_dimensions.cta_height; // might be caching
            var popup_check = jQuery("#wp-cta-popup");
            if (typeof (popup_check) != "undefined" && popup_check != null && popup_check != "") {
            var popon = true;
            } 
            var width_backup = the_frame.contents().find("#cpt_cta_width").text();
            var height_backup = the_frame.contents().find("#cpt_cta_height").text();
            if (typeof (cta_height) != "undefined" && cta_height != null && cta_height != "") {
                 console.log("height set from iframe");
             
                the_frame.height(cta_height);
                if(popon){
                    popup_check.height(cta_height);
                 
                }
            }
            // if admin set width do this
             if (typeof (cta_width) != "undefined" && cta_width != null && cta_width != "") {
                console.log("width set from iframe");
              
                the_frame.width(cta_width);
                 if(popon){
                    popup_check.width(cta_width);
                }
            }
            // if dimensions not defined
            if (typeof (cta_height) === "undefined" || cta_height == null || cta_height == "") {
                console.log("no height set from iframe");
                var setheight = the_frame.contents().find("body").height() + 20;
                the_frame.height(setheight);
                 if(popon){
                    popup_check.height(setheight);
                }
            }
            // if dimensions not defined
            if (typeof (cta_width) === "undefined" || cta_width == null || cta_width == "") {
                console.log("no width set from iframe");
                //var setwidth = jQuery("#wp-cta").contents().find("body").width();
                var setwidth = the_frame.parent().width();
                console.log(setwidth);
                the_frame.width(setwidth);
                if(popon){
                    var setpopwidth = the_frame.attr("data-parent"); // use content width;
                    popup_check.width(setpopwidth);
                    console.log(setpopwidth);
                    the_frame.width(setpopwidth);
                }
            }    
                jQuery(this).contents().find("#wpadminbar").hide();
                jQuery(this).contents().find("html").addClass('fix-admin-view');
                check_hidden = jQuery(this).css('display');
                // if frame hidden do this
                if (check_hidden === "none") {
                   jQuery(this).fadeIn(700); 
                }
                if(popon){
                    popup_check.removeClass("cta_wait_hide");
                    popup_check.show();
                }
                // IE feature detection
                var isIE9 = document.addEventListener,
                isIE8 = document.querySelector,
                isIE7 = window.XMLHttpRequest;
                // need better window opening
                if(isIE9){
                // is IE9
                 jQuery(this).show();
                } else if(isIE8) {
                // is IE8
                 jQuery(this).show();
                } else if(isIE7) {
                // is IE7
                  jQuery(this).show();
                }

 
        });
    });   
});