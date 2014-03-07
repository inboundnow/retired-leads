function wp_cta_load_variation(cta_id)
{
	/* load & reveal variation */
	//console.log('Variation Ajax URL:' + cta_variation.ajax_url + '?cta_id='+cta_id);

	jQuery.ajax({
		 type: "GET",
		 url: cta_variation.ajax_url,
		 dataType: "script",
		 async:false,
		 data : {
		  'cta_id' : cta_id
		 },
		 success: function(vid) {
		 	var loaded_ctas = {};
		 	var loaded_local_cta = jQuery.totalStorage('wp_cta_loaded');
		 	if (loaded_local_cta != null) {
		 		var loaded_ctas = JSON.parse(localStorage.getItem('wp_cta_loaded'));
		 	}

			loaded_ctas[cta_id] = vid;
			// localStorage.setItem('wp_cta_loaded', JSON.stringify(loaded_ctas)); // old syntax

			jQuery.totalStorage('wp_cta_loaded', loaded_ctas); // store cta data
			console.log('WP CTA Load Object Updated:' + JSON.stringify(loaded_ctas));
		}
	});
}

jQuery(document).ready(function($) {

	/* reset local storage variable every page load */
	//localStorage.removeItem('wp_cta_loaded');
	jQuery.totalStorage.deleteItem('wp_cta_loaded'); // remove pageviews

	if (cta_variation.cta_id) {
		wp_cta_load_variation(cta_variation.cta_id);
	}
});
