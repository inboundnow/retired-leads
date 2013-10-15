<?php
/**
* WordPress: WP Calls To Action Template Config File
* Template Name:  Call Out Box
* @package  WordPress Calls to Action
* @author 	InboundNow
*/
do_action('wp_cta_global_config'); // The wp_cta_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = wp_cta_get_parent_directory(dirname(__FILE__)); 


$wp_cta_data[$key]['info'] = 
array(	
	'data_type' => 'template', // Template Data Type
    'version' => "1.0", // Version Number
    'label' => "Ebook Call out", // Nice Name
    'category' => 'Sidebar', // Template Category
    'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
    'description'  => 'This is the blank template for any image/html/shortcode CTA' // template description
);


// Define Meta Options for template
$wp_cta_data[$key]['settings'] = 
array(
    array(
        'label' => 'Instructions', // Name of field
        'description' => "The dimensions of this Calls to action template should be a very similiar height and width. For example 300 x 300px. You can insert your Logo and some text on top of the book. Max recommended width:400px<br><br>Additionally you can use the content area below to insert additional text below the book and button", // what field does
        'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'description-block', // metafield type
        'default'  => '<p>This is a popup call to action used to promote something. Use the main hero image and the main content area to create your popup</p>', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
     array(
        'label' => 'Logo Image',
        'description' => "This is the main graphic with the popup",
        'id'  => 'hero', // called in template's index.php file with lp_get_value($post, $key, 'media-id');
        'type'  => 'media',
        'default'  => '/wp-content/plugins/cta/templates/blank-ebook/logo.png',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Book Color',
        'description' => "Dropdown option description",
        'id'  => 'book-color',
        'type'  => 'dropdown',
        'default'  => 'white',        
        'options' => array('white'=>'White Book', 'gray'=>'Gray Book', 'green'=>"Green Book", 'light-blue'=>"Light Blue Book", 'black'=>"Black Book"),
        'context'  => 'normal'
        ),
    array(
        'label' => 'Text Over Book',
        'description' => "Header Text",
        'id'  => 'header-text',
        'type'  => 'text',
        'default'  => 'Download our Awesome Ebook',
        'context'  => 'normal'
        ),

    array(
        'label' => 'Text Over Book Color',
        'description' => "Use this setting to change headline color",
        'id'  => 'headline-text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Background Color',
        'description' => "Changes background color",
        'id'  => 'content-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Content Text Color',
        'description' => "Use this setting to change the content text color",
        'id'  => 'content-text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Button Color',
        'description' => "Use this setting to change the template's submit button color.",
        'id'  => 'submit-button-color',
        'type'  => 'colorpicker',
        'default'  => 'E14D4D'
        ),
     array(
        'label' => 'Button Text Color',
        'description' => "Use this setting to change the template's submit button text color.",
        'id'  => 'submit-button-text-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff'
        ),
     array(
        'label' => 'Button Text',
        'description' => "Text on the button.",
        'id'  => 'submit-button-text',
        'type'  => 'text',
        'default'  => 'Download Now'
        ),
      array(
        'label' => 'Redirect URL',
        'description' => "Where to redirect people",
        'id'  => 'redirect',
        'type'  => 'text',
        'default'  => 'http://www.inboundnow.com'
        )
    );