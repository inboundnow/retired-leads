jQuery(document).ready(function($) {
  	var iframe_size = jQuery(parent.document).find('#wp-cta').width();
  	//console.log(iframe_size);
	jQuery("body").on('click', 'a', function (event) {
	event.preventDefault();
	open_link = jQuery(this).attr("href");
	parent.window.location.href = open_link;
    });
   
 });