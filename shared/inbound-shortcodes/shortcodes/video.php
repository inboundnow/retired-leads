<?php
/**
*   Video Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['video'] = array(
		'no_preview' => true,
		'options' => array(
			'url' => array(
				'name' => __('Video URL', INBOUND_LABEL),
				'desc' => __('Paste the video URL here, click <a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">here</a> to see all available video hosts.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			)
		),
		'shortcode' => '[video url="{{url}}" /]',
		'popup_title' => __('Insert Video Shortcode', INBOUND_LABEL)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('video', 'inbound_shortcode_video');

	function inbound_shortcode_video( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'url' => ''
		), $atts));

		return '<div class="video-container">'. wp_oembed_get( $url ) .'</div>';
	}