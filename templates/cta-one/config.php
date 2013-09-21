<?php
/**
* WordPress: WP Calls To Action Template Config File
* Template Name:  CTA One
* @package  WordPress Calls to Action
* @author 	InboundNow
*/

do_action('wp_cta_global_config'); // The wp_cta_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = wp_cta_get_parent_directory(dirname(__FILE__)); 


$wp_cta_data[$key]['info'] = 
array(
	'version' => "1.0", // Version Number
	'label' => "CTA One", // Nice Name
	'category' => 'Box', // Template Category
	'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
	'description'  => 'CTA 1' // template description
);

// Define Meta Options for template
$wp_cta_data[$key]['settings'] = 
array(
    array(
        'label' => 'CTA Background Color', // Name of field
        'description' => "Changes background color", // what field does
        'id' => 'cta-background-color', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'colorpicker', // metafield type
        'default'  => 'f3f3f3', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
    array(
        'label' => 'Content Text Color',
        'description' => "Use this setting to change headline color",
        'id'  => 'content-text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Header Text',
        'description' => "Header Text",
        'id'  => 'header-text',
        'type'  => 'text',
        'default'  => 'Awesome Headline Text',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Button Background Color',
        'description' => "Use this setting to change the template's submit button color.",
        'id'  => 'button-background-color',
        'type'  => 'colorpicker',
        'default'  => '0084E3',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Button Text',
        'description' => "Text on the button.",
        'id'  => 'button-text',
        'type'  => 'text',
        'default'  => 'Buy Now',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Disable Ribbon',
        'description' => "This will disable the top right ribbon",
        'id'  => 'link_status', // called in template's index.php file with lp_get_value($post, $key, 'checkbox-id-here');
        'type'  => 'dropdown',
        'default'  => 'option_on',
        'options' => array('option_on' => 'Ribbon is On','option_off'=>'Ribbon is Off'),    
        'context'  => 'normal'
        )
    );