var errors = [];
window.onerror = function(msg, url, linenumber) {
  //alert('Error message: '+msg+' URL: '+url+' Line Number: '+linenumber);
  errors.push(msg + ' from ' + url + ' on line ' +linenumber);
}
jQuery(document).ready(function($) {
  var url = window.location.href;
  var match = url.match(/\?/);
  var param = "?";
  if(match) {
  	var param = "&";
  }
  var url = url.replace('&inbound_js', '') + param + "inbound-dequeue-scripts";
  setTimeout(function() {
    document.write("<strong>Below are the javascript errors on this page</strong> " + "<br>");
    /*if (errors.length === 0 ) {
        document.write("<strong style="color:green;">None Detected</strong> " + "<br>");
    }*/
     for (var i=0,len=errors.length; i<len; i++){
        document.write(i + 1 + ". " + errors[i] + "<br>");
     }
    document.write("<div style=\'margin-top:20px;\'><strong>You need to fix these errors for things to work. There are 3 options:</strong> " + "<br>");
    document.write("<strong>1. <a href=\'" +url+ "\'>Click here and dequeue (turn off) the broken javascript files</a> from this page.</strong> " + "<br>");
    document.write("<strong>2. Contact the original developer of the plugin/theme causing the error.</strong> " + "<br>");
    document.write("<strong>3. Disable the plugin or theme causing the conflict.</strong> " + "<br></div>");
   }, 500);
 });