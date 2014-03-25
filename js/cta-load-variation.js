function wp_cta_load_variation( cta_id , disable_ajax )
{
	/* load & reveal variation */
	//console.log('Variation Ajax URL:' + cta_variation.ajax_url + '?cta_id='+cta_id);
	if ( parseInt(disable_ajax) != 1 ) {
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

				jQuery.totalStorage('wp_cta_loaded', loaded_ctas); // store cta data
				console.log('WP CTA Load Object Updated:' + JSON.stringify(loaded_ctas));
			}
		});
	} else {
		var loaded_ctas = {};
		var loaded_local_cta = jQuery.totalStorage('wp_cta_loaded');
		if (loaded_local_cta != null) {
			var loaded_ctas = JSON.parse(localStorage.getItem('wp_cta_loaded'));
		}

		loaded_ctas[cta_id] = 0;

		jQuery.totalStorage('wp_cta_loaded', loaded_ctas); // store cta data
		console.log('WP CTA Load Object Updated:' + JSON.stringify(loaded_ctas));
	}
}

jQuery(document).ready(function($) {

	/* reset local storage variable every page load */
	jQuery.totalStorage.deleteItem('wp_cta_loaded'); // remove pageviews

	if (cta_variation.cta_id) {
		wp_cta_load_variation( cta_variation.cta_id , cta_variation.disable_ajax );
	}
});
