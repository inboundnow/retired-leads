<?php
/**
* WordPress: WP Calls To Action Template Config File
* Template Name:  Demo Template
* @package  WordPress Calls to Action
* @author 	InboundNow
*/

do_action('wp_cta_global_config'); // The wp_cta_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = wp_cta_get_parent_directory(dirname(__FILE__)); 

/// Information START - define template information
/**
 * $wp_cta_data[$key]['info']
 * type - multidemensional array
 *
 * This array houses the template metadata
 */

/* $wp_cta_data[$key]['settings'] Parameters

    'version' - (string) (optional)
    Version Number. default = "1.0"

    'label' - (string) (optional)
    Custom Nice Name for templates. default = template file folder name

    'description' - (string) (optional)
    Landing page description.

    'category' - (string) (optional)
    Category for template. default = "all"

    'demo' - (string) (optional)
    Link to demo url.
*/

$wp_cta_data[$key]['info'] = 
array(
	'version' => "1.0", // Version Number
	'label' => "Demo Template", // Nice Name
	'category' => 'Box', // Template Category
	'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
	'description'  => 'The Demo theme is here to help developers and designs implment thier own designs into the landing page plugin. Study this template to learn about Landing Page Plugin\'s templating system and to assist in building new templates.' // template description
);

/**
 * $wp_cta_data[$key]['settings']
 * type - multidemensional array
 *
 * This array houses the metabox options for the template
 */

/* $wp_cta_data[$key]['settings'] Parameters

    'label' - (string) (required)
    Label for Meta Fields.

    'description' - (string) (optional)
    Description for meta Field 

    'id' - (string) (required)
    unprefixed-meta-key. The $key (template file path name) is appended in the loop this array is used in.

    'type' - (string) (required)
    Meta box type. default = 'text'

    'default' - (string) (optional)
    Default Field Value.  default = ''

    'context' - (string) (optional)
    where this box will go, will be used for advanced placement/styling.  default = normal
 
 */

// Define Meta Options for template
$wp_cta_data[$key]['settings'] = 
array(
    array(
        'label' => 'Text Field Label Here', // Name of field
        'description' => "Text Field Description Here", // what field does
        'id' => 'text-box-id', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'text', // metafield type
        'default'  => 'Default', // default content
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
        'id'  => 'background-color',
        'type'  => 'colorpicker',
        'default'  => '222222',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Content Text Color',
        'description' => "Use this setting to change the content text color",
        'id'  => 'content-text-color',
        'type'  => 'colorpicker',
        'default'  => '222222',
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
        'id'  => 'submit-button-color',
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
        )
    );