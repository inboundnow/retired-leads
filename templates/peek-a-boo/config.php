<?php
/**
* WordPress: WP Calls To Action Template Config File
* Template Name:  Peek a Boo
* @package  WordPress Calls to Action
* @author 	InboundNow
*/
do_action('wp_cta_global_config'); // The wp_cta_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = wp_cta_get_parent_directory(dirname(__FILE__)); 


$wp_cta_data[$key]['info'] = 
array(
	'version' => "1.0", // Version Number
	'label' => "Peek a Boo", // Nice Name
	'category' => 'wide', // Template Category
	'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
	'description'  => 'This is the blank template for any image/html/shortcode CTA' // template description
);



// Define Meta Options for template
$wp_cta_data[$key]['settings'] = 
array(
    array(
        'label' => 'Instructions', // Name of field
        'description' => "<strong>Template Instructions:</strong> This call to action has two modes. By default the entire CTA is hyperlinked with the \"Link URL\" option below but you can also insert a form in either editor box and toggle the link off.", // what field does
        'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
        'type'  => 'description-block', // metafield type
        'default'  => '<p>This entire call to action is linked with your destination URL. Clicking anywhere will send people to your landing page</p>', // default content
        'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
    array(
        'label' => 'CTA Link URL',
        'description' => "Where do you want to send people to when they click the CTA?",
        'id'  => 'link_url',
        'type'  => 'text',
        'default'  => 'http://www.inboundnow.com'
        ),
     array(
        'label' => 'Disable Link and Use form in CTA',
        'description' => "This will disable the link and let you use a small form in the CTA template in one of the editor areas below",
        'id'  => 'link_status', // called in template's index.php file with lp_get_value($post, $key, 'checkbox-id-here');
        'type'  => 'dropdown',
        'default'  => 'option_on',
        'options' => array('option_on' => 'Link is On','option_off'=>'Link is Off'),    
        'context'  => 'normal'
        ),
    array(
        'label' => 'Header Text (optional)',
        'description' => "Header Text. This is optional. Remove the text to make it disappear",
        'id'  => 'header-text',
        'type'  => 'text',
        'default'  => 'This Main Headline Will Rock Your Socks',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Headline Text Color',
        'description' => "Use this setting to change headline color",
        'id'  => 'headline-text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Main Image',
        'description' => "This is the main graphic with the popup",
        'id'  => 'hero', // called in template's index.php file with lp_get_value($post, $key, 'media-id');
        'type'  => 'media',
        'default'  => 'http://www.fillmurray.com/250/250',
        'context'  => 'normal'
        ),
    array(
        'label' => 'Static Background Color',
        'description' => "Changes background color",
        'id'  => 'static-bg-color',
        'type'  => 'colorpicker',
        'default'  => 'EEEEEE',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Static Area Text Color',
        'description' => "Use this setting to change the content text color",
        'id'  => 'static-text-color',
        'type'  => 'colorpicker',
        'default'  => '000000',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Static Teaser Text',
        'description' => "Text on the button.",
        'id'  => 'teaser-text',
        'type'  => 'wysiwyg',
        'default'  => 'Do You Know How to Avoid these common XYZ Mistakes?'
        ),
      array(
        'label' => 'Slideout Background Color',
        'description' => "Changes background color",
        'id'  => 'slideout-bg-color',
        'type'  => 'colorpicker',
        'default'  => 'D90E0E',
        'context'  => 'normal'
        ),
      array(
        'label' => 'Slideout Text Color',
        'description' => "Changes background color",
        'id'  => 'slideout-text-color',
        'type'  => 'colorpicker',
        'default'  => 'ffffff',
        'context'  => 'normal'
        ),
     array(
        'label' => 'Slideout Area Text',
        'description' => "This is the slide out area. You can insert copy or a form here. If you use a form. Toggle the link off below",
        'id'  => 'submit-button-text',
        'type'  => 'wysiwyg',
        'default'  => '<h4>Header Area Lorem ipsum dolor sit</h4>
        <p>Nulla rhoncus orci sed odio euismod vestibulum. Praesent porta aliquet nulla, ut mattis velit rhoncus eu duspendisse nibh orci laoreet. </p>'
        ),
     array(
        'label' => 'turn-off-editor',
        'description' => "Turn off editor",
        'id'  => 'turn-off-editor',
        'type'  => 'custom-css',
        'default'  => '#postdivrich {display:none !important;}'
        )
    );