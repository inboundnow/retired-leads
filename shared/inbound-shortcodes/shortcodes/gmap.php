<?php
/**
*   Google Map Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['gmap'] = array(
		'no_preview' => true,
		'options' => array(
			'type' => array(
				'name' => __('Map Type', INBOUND_LABEL),
				'desc' => __('Select a map type', INBOUND_LABEL),
				'type' => 'select',
				'options' => array('HYBRID' => 'Hybrid', 'ROADMAP' => 'Road Map', 'SATELLITE' => 'Satellite', 'TERRAIN' => 'Terrain'),
				'std' => ''
			),
			'width' => array(
				'name' => __('Map Width', INBOUND_LABEL),
				'desc' => '',
				'type' => 'text',
				'std' => ''
			),
			'height' => array(
				'name' => __('Map Height', INBOUND_LABEL),
				'desc' => '',
				'type' => 'text',
				'std' => ''
			),
			'address' => array(
				'name' => __('Address', INBOUND_LABEL),
				'desc' => __('Enter your address here. ex: "New York" or "New York, USA" If you want a accurate position please fill the Latitude and Longitude fields', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'latitude' => array(
				'name' => __('Latitude', INBOUND_LABEL),
				'desc' => __('Latitude is specified in degrees within the range [-90, 90]', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'longitude' => array(
				'name' => __('Longitude', INBOUND_LABEL),
				'desc' => __('Longitude is specified in degrees within the range [-180, 180]', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'zoom' => array(
				'name' => __('Zoom', INBOUND_LABEL),
				'desc' => __('Set the zoom level, numeric value from 1 to 19', INBOUND_LABEL),
				'type' => 'text',
				'std' => '8'
			),
			'pancontrol' => array(
				'name' => __('Pan Control', INBOUND_LABEL),
				'desc' => __('Enable pan control?', INBOUND_LABEL),
				'type' => 'select',
				'options' => array('true' => 'Enable', 'false' => 'Disable'),
				'std' => ''
			),
			'zoomcontrol' => array(
				'name' => __('Zoom Control', INBOUND_LABEL),
				'desc' => __('Enable zoom control?', INBOUND_LABEL),
				'type' => 'select',
				'options' => array('true' => 'Enable', 'false' => 'Disable'),
				'std' => ''
			),
			'maptypecontrol' => array(
				'name' => __('Map Type Control', INBOUND_LABEL),
				'desc' => __('Enable map type control?', INBOUND_LABEL),
				'type' => 'select',
				'options' => array('true' => 'Enable', 'false' => 'Disable'),
				'std' => ''
			),
			'content' => array(
				'name' => __('Popup Content', INBOUND_LABEL),
				'desc' => __('A descriptive text for the Google Map marker popup, ex: "My Office"', INBOUND_LABEL),
				'type' => 'textarea',
				'std' => ''
			)
		),
		'shortcode' => '[gmap type="{{type}}" address="{{address}}" lat="{{latitude}}" lng="{{longitude}}" zoom="{{zoom}}" pancontrol="{{pancontrol}}" zoomcontrol="{{zoomcontrol}}" maptypecontrol="{{maptypecontrol}}" width="{{width}}" height="{{height}}"]{{content}}[/gmap]',
		'popup_title' => __('Insert Google Map Shortcode', INBOUND_LABEL)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('gmap', 'inbound_shortcode_gmap');

	function inbound_shortcode_gmap( $atts, $content = null ) {

		extract(shortcode_atts(array(
			'type' => 'ROADMAP',
			'address' => '',
			'lat' => '',
			'lng' => '',
			'width' => '',
			'height' => 400,
			'zoom' => 8,
			'scroll' => 'true',
			'pancontrol' => 'true',
			'zoomcontrol' => 'true',
			'maptypecontrol' => 'true'
		), $atts));
		$out = '';
		if ( !$lat == '' && !$lng == '') :
		$out .= '<div id="google_map_'.preg_replace('![^a-z0-9]+!i', '', $lat) . preg_replace('![^a-z0-9]+!i', '', $lng) . $type .'" class="google_map"  style="width:'.$width.'px;height:'.$height.'px"></div>' . "\n";
		$out .= '
		<script type="text/javascript">
		(function($) {
			$("#google_map_'.preg_replace('![^a-z0-9]+!i', '', $lat) . preg_replace('![^a-z0-9]+!i', '', $lng) . $type .'").gMap({
				latitude:'.$lat.',
				longitude:'.$lng.',
				maptype:"'.$type.'",
				scrollwheel: '.$scroll.',
				zoom:'.$zoom.',
				controls: {
					panControl:'.$pancontrol.',
					zoomControl:'.$zoomcontrol.',
					mapTypeControl:'.$maptypecontrol.'
				},
				markers: [{
					latitude:'.$lat.',
					longitude:'.$lng.',
					html: "'.$content.'"
				}]
			});
		})(jQuery);
		</script>';
		else :
		$out .= '<div id="google_map_'.preg_replace('![^a-z0-9]+!i', '',$address) . $type .'" class="google_map" style="width:'.$width.'px;height:'.$height.'px"></div>' . "\n";
		$out .= '
		<script type="text/javascript">
		(function($) {
			$("#google_map_'.preg_replace('![^a-z0-9]+!i', '',$address) . $type .'").gMap({
				address:"'.$address.'",
				maptype:"'.$type.'",
				scrollwheel: '.$scroll.',
				zoom:'.$zoom.',
				controls: {
					panControl:'.$pancontrol.',
					zoomControl:'.$zoomcontrol.',
					mapTypeControl:'.$maptypecontrol.'
				},
				markers: [{
					address:"'.$address.'",
					html: "'.$content.'"
				}]
			});
		})(jQuery);
		</script>';
		endif;

		return $out;
	}