<?php
/**
*   Profile Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['profile'] = array(
		'no_preview' => true,
		'options' => array(
			'name' => array(
				'name' => __('Profile Name', INBOUND_LABEL),
				'desc' => __('Enter the name.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'meta' => array(
				'name' => __('Profile Meta', INBOUND_LABEL),
				'desc' => __('Enter the profile meta. e.g job position etc.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'image' => array(
				'name' => __('Profile Image', INBOUND_LABEL),
				'desc' => __('Paste your profile image URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'link' => array(
				'name' => __('Profile Link', INBOUND_LABEL),
				'desc' => __('Paste your profile link URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'facebook' => array(
				'name' => __('Profile Facebook', INBOUND_LABEL),
				'desc' => __('Paste your facebook URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'twitter' => array(
				'name' => __('Profile Twitter', INBOUND_LABEL),
				'desc' => __('Paste your twitter URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'email' => array(
				'name' => __('Profile Email Address', INBOUND_LABEL),
				'desc' => __('Paste your email address here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => ''
			),
			'content' => array(
				'name' => __('Profile Description',  INBOUND_LABEL),
				'desc' => __('Enter the profile description text.',  INBOUND_LABEL),
				'type' => 'textarea',
				'std' => ''
			)
		),
		'shortcode' => '[profile name="{{name}}" meta="{{meta}}" image="{{image}}"]{{content}}[/profile]',
		'popup_title' => __('Insert Profile Shortcode', INBOUND_LABEL)
	);

/* 	Page builder module config
 * 	----------------------------------------------------- */
	$freshbuilder_modules['profile'] = array(
		'name' => __('Profile', INBOUND_LABEL),
		'size' => 'one_fourth',
		'options' => array(
			'name' => array(
				'name' => __('Profile Name', INBOUND_LABEL),
				'desc' => __('Enter the name.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'meta' => array(
				'name' => __('Profile Meta', INBOUND_LABEL),
				'desc' => __('Enter the profile meta. e.g job position etc.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'image' => array(
				'name' => __('Profile Image', INBOUND_LABEL),
				'desc' => __('Paste your profile image URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'link' => array(
				'name' => __('Profile Link', INBOUND_LABEL),
				'desc' => __('Paste your profile URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'facebook' => array(
				'name' => __('Profile Facebook', INBOUND_LABEL),
				'desc' => __('Paste your facebook URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'twitter' => array(
				'name' => __('Profile Twitter', INBOUND_LABEL),
				'desc' => __('Paste your twitter URL here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'email' => array(
				'name' => __('Profile Email Address', INBOUND_LABEL),
				'desc' => __('Paste your email address here.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'content' => array(
				'name' => __('Profile Description', INBOUND_LABEL),
				'desc' => __('Enter the profile description text.',  INBOUND_LABEL),
				'type' => 'textarea',
				'std' => '',
				'class' => '',
				'is_content' => 1
			)
		)
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('profile', 'inbound_shortcode_profile');

	function inbound_shortcode_profile( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'name' => '',
			'meta' => '',
			'image' => '',
			'link' => '',
			'facebook' => '',
			'twitter' => '',
			'email' => ''
		), $atts));

		$out = '';
		$out .= '<div class="profile-box clearfix">';

			if($link != '') :
				$out .= '<figure class="profile-img"><a href="'. $link .'"><img src="'. $image .'" alt="'. $name .'"/></a></figure>';
			else :
				$out .= '<figure class="profile-img"><img src="'. $image .'" alt="'. $name .'"/></figure>';
			endif;

			if($name != '')
			$out .= '<h3 class="profile-name">'. $name .'</h3>';

			if($meta != '')
			$out .= '<div class="profile-meta">'. $meta .'</div>';

			$out .= '<div class="profile-desc">'. do_shortcode($content) .'</div>';

			if($facebook || $twitter || $email ) {
				$out .= '<div class="profile-footer">';
					if($facebook != '')
					$out .= '<a href="'. $facebook .'"><i class="icon-facebook-sign"></i> Facebook</a>';

					if($twitter != '')
					$out .= '<a href="'. $twitter .'"><i class="icon-twitter"></i> Twitter</a>';

					if($email != '' && is_email($email) )
					$out .= '<a href="mailto:'. $email .'"><i class="icon-envelope-alt"></i> Email</a>';
				$out .= '</div>';
			}
		$out .= '</div>';

		return $out;
	}