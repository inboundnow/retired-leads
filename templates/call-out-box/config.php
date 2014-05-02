<?php
/**
* WordPress: WP Calls To Action Template Config File
* Template Name:  Call Out Box
* @package  WordPress Calls to Action
* @author 	InboundNow
*/

do_action('wp_cta_global_config'); // The wp_cta_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = basename(dirname(__FILE__));
$this_path = WP_CTA_PATH.'templates/'.$key.'/';
$url_path = WP_CTA_URLPATH.'templates/'.$key.'/';

$wp_cta_data[$key]['info'] =
array(
	'data_type' => 'template', // Template Data Type
	'version' => "1.0", // Version Number
	'label' => "Call Out Box", // Nice Name
	'category' => 'Box', // Template Category
	'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
	'description'  => 'This is a simple box template', // template description
	'path' => $this_path //path to template folder
);


/* Define Meta Options for template */
$wp_cta_data[$key]['settings'] =
array(
    array(
        'label' => 'Instructions', // Name of field
        'description' => "Instructions for this call to action template go here", // what field does
        'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'description-block', // metafield type
        'default'  => '<p>Insert your call to action graphic into the content area below. Don\'t forget to hyperlink it to your final destination</p>', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
    array(
        'label' => 'Headline Text Color',
        'description' => "Use this setting to change headline color",
        'id'  => 'headline-text-color',
        'type'  => 'colorpicker',
        'default'  => 'FFFFFF',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Header Text',
        'description' => "Header Text",
        'id'  => 'header-text',
        'type'  => 'text',
        'default'  => 'Awesome Text that makes you want to buy',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Background Color',
        'description' => "Changes background color",
        'id'  => 'content-background-color',
        'type'  => 'colorpicker',
        'default'  => '222222',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Message Text',
        'description' => "Message Text",
        'id'  => 'content-text',
        'type'  => 'wysiwyg',
        'default'  => 'Insert Content Here.',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Content Text Color',
        'description' => "Use this setting to change the content text color",
        'id'  => 'content-text-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Button Background Color',
        'description' => "Use this setting to change the template's submit button color.",
        'id'  => 'submit-button-color',
        'type'  => 'colorpicker',
        'default'  => 'db3d3d'
        ),
     array(
        'label' => 'Button Text Color',
        'description' => "Use this setting to change the template's submit button text color.",
        'id'  => 'submit-button-text-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff'
        ),
     array(
        'label' => 'Button Link',
        'description' => "Link on the button.",
        'id'  => 'submit-button-link',
        'type'  => 'text',
        'default'  => 'http://www.inboundnow.com'
        ),
     array(
        'label' => 'Button Text',
        'description' => "Text on the button.",
        'id'  => 'submit-button-text',
        'type'  => 'text',
        'default'  => 'Click here'
        ),
     array(
         'label' => 'Show Button?',
         'description' => "You can toggle off the main CTA button if you are using a form in this CTA",
         'id'  => 'show-button',
         'type'  => 'dropdown',
         'default'  => 'true',
         'options' => array('true'=>'Show Button', 'false'=>'Hide Button', ),
         'context'  => 'normal'
         ),
    );


/* define dynamic template markup */
$wp_cta_data[$key]['markup'] = file_get_contents($this_path . 'index.php');
