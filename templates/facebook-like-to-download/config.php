<?php
/**
* WordPress: WP Calls To Action Template Config File
* Template Name:  Flat
* @package  WordPress Calls to Action
* @author 	InboundNow
*/
do_action('wp_cta_global_config'); // The wp_cta_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = wp_cta_get_parent_directory(dirname(__FILE__));


$wp_cta_data[$key]['info'] =
array(
	'version' => "1.0", // Version Number
	'label' => "Facebook Like to Download", // Nice Name
	'category' => 'social', // Template Category
	'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
	'description'  => 'Get more facebook likes' // template description
);



// Define Meta Options for template
$wp_cta_data[$key]['settings'] =
array(
    array(
        'label' => 'Instructions', // Name of field
        'description' => "This Call to action is used for like gating downloadable content. Basically you can get more facebook likes on any URL (fanpage or otherwise) in return for a peice of downloadable content", // what field does
        'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'description-block', // metafield type
        'default'  => '', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
    array(
        'label' => 'URL to Like on Facebook',
        'description' => "Header Text",
        'id'  => 'facebook-like-url',
        'type'  => 'text',
        'default'  => 'http://www.facebook.com/inboundnow',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Link to Download',
        'description' => "This will be the download for people to get once they like the above URL",
        'id'  => 'download-url',
        'type'  => 'text',
        'default'  => 'http://www.link-to-download.com',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Text on Download Button',
        'description' => "",
        'id'  => 'download-url-text',
        'type'  => 'text',
        'default'  => 'Click to Download',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Background Color',
        'description' => "Changes background color",
        'id'  => 'content-color',
        'type'  => 'colorpicker',
        'default'  => '60BCF0',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Color Scheme',
        'description' => "Light or Dark?",
        'id'  => 'color_scheme', // called in template's index.php file with lp_get_value($post, $key, 'checkbox-id-here');
        'type'  => 'dropdown',
        'default'  => 'option_on',
        'options' => array('light' => 'Light','dark'=>'Dark'),
        'context'  => 'normal'
        ),
      array(
        'label' => 'turn-off-editor',
        'description' => "Turn off editor",
        'id'  => 'turn-off-editor',
        'type'  => 'custom-css',
        'default'  => '#postdivrich, .wp_cta_height-0, .wp_cta_height-1, .wp_cta_height-3, .wp_cta_height-2 {display:none !important;}'
        ),
       array(
        'label' => 'Instructions', // Name of field
        'description' => "<strong>Advanced Options:</strong> Sometimes facebook requires unique app IDs on sites to run a like to download tool. If the like button doesn't work. Enter your app id below", // what field does
        'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'description-block', // metafield type
        'default'  => '', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
       array(
       'label' => 'Border Radius (rounded corners)',
       'description' => "Set to 0 for no rounded corners, set to 5+ to round the CTA edges",
       'id'  => 'border-radius',
       'type'  => 'number',
       'default'  => '5',
       'context'  => 'normal'
       ),
        array(
        'label' => 'Facebook App ID',
        'description' => "Optional",
        'id'  => 'fb-app-id',
        'type'  => 'text',
        'default'  => '',
        'context'  => 'normal'
        ),
    );