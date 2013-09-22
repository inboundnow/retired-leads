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
	'label' => "Flat CTA", // Nice Name
	'category' => 'wide', // Template Category
	'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
	'description'  => 'This is the blank template for any image/html/shortcode CTA' // template description
);



// Define Meta Options for template
$wp_cta_data[$key]['settings'] = 
array(
    array(
        'label' => 'Instructions', // Name of field
        'description' => "Fill in the below fields to configure the flat CTA. Make sure to insert the height and width of your call to action. Use the visual editor to see live changes and for faster editing", // what field does
        'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'description-block', // metafield type
        'default'  => '', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
    array(
        'label' => 'Header Text',
        'description' => "Header Text",
        'id'  => 'header-text',
        'type'  => 'text',
        'default'  => 'Snappy Headline',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Header Sub Text',
        'description' => "Sub Header Text",
        'id'  => 'sub-header-text',
        'type'  => 'text',
        'default'  => 'Awesome Subheadline Text Goes here',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Header Text Color',
        'description' => "Use this setting to change headline color",
        'id'  => 'text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
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
        'label' => 'Content Text Color (optional)',
        'description' => "Use this setting to change headline color",
        'id'  => 'content-text-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Submit Button Color',
        'description' => "Use this setting to change the template's submit button color.",
        'id'  => 'submit-button-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff'
        ),
     array(
        'label' => 'Submit Button Text',
        'description' => "Text on the button.",
        'id'  => 'submit-button-text',
        'type'  => 'text',
        'default'  => 'Download Now'
        ),
      array(
        'label' => 'Destination Link',
        'description' => "Where do you want to link people to?",
        'id'  => 'link_url',
        'type'  => 'text',
        'default'  => 'http://www.inboundnow.com'
        ),
      array(
        'label' => 'turn-off-editor',
        'description' => "Turn off editor",
        'id'  => 'turn-off-editor',
        'type'  => 'custom-css',
        'default'  => '#postdivrich {display:none !important;}'
        )
    );