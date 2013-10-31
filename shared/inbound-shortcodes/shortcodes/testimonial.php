<?php
/**
*   Testimonial Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['testimonial'] = array(
		'no_preview' => true,
		'options' => array(
			'heading' => array(
				'name' => __('Heading Text', INBOUND_LABEL),
				'desc' => __('Enter the heading text.', INBOUND_LABEL),
				'type' => 'text',
				'std' => 'Testimonial'
			),
			'column' => array(
				'name' => __('Column', INBOUND_LABEL),
				'desc' => __('Select the number of column.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'1' => __('1 Column', INBOUND_LABEL),
					'2' => __('2 Columns', INBOUND_LABEL),
					'3' => __('3 Columns', INBOUND_LABEL),
					'4' => __('4 Columns', INBOUND_LABEL),
					'5' => __('5 Columns', INBOUND_LABEL)
				),
				'std' => '1'
			)
		),
		'child' => array(
			'options' => array(
				'author' => array(
					'name' => __('Testimony Author',  INBOUND_LABEL),
					'desc' => __('Enter the testimony author name.',  INBOUND_LABEL),
					'type' => 'text',
					'std' => ''
				),
				'meta' => array(
					'name' => __('Testimony Author Meta', INBOUND_LABEL),
					'desc' => __('The author job, company or website name.', INBOUND_LABEL),
					'type' => 'text',
					'std' => ''
				),
				'content' => array(
					'name' => __('Testimony Content',  INBOUND_LABEL),
					'desc' => __('Put the content here.',  INBOUND_LABEL),
					'type' => 'textarea',
					'std' => ''
				)
			),
			'shortcode' => '[testimony author="{{author}}" meta="{{meta}}"]{{content}}[/testimony]',
			'clone' => __('Add More Testimony',  INBOUND_LABEL )
		),
		'shortcode' => '[testimonial heading="{{heading}}"  column="{{column}}"]{{child}}[/testimonial]',
		'popup_title' => __('Insert Testimonial Shortcode',  INBOUND_LABEL)
	);

/* 	Page builder module config
 * 	----------------------------------------------------- */
	$freshbuilder_modules['testimonial'] = array(
		'name' => __('Testimonial', INBOUND_LABEL),
		'size' => 'one_half',
		'options' => array(
			'heading' => array(
				'name' => __('Heading', INBOUND_LABEL),
				'desc' => __('Enter the heading text.', INBOUND_LABEL),
				'type' => 'text',
				'std' => 'Testimonial',
				'class' => '',
				'is_content' => 0
			),
			'column' => array(
				'name' => __('Column', INBOUND_LABEL),
				'desc' => __('Select the number of column.', INBOUND_LABEL),
				'type' => 'select',
				'options' => array(
					'1' => __('1 Column', INBOUND_LABEL),
					'2' => __('2 Columns', INBOUND_LABEL),
					'3' => __('3 Columns', INBOUND_LABEL),
					'4' => __('4 Columns', INBOUND_LABEL),
					'5' => __('5 Columns', INBOUND_LABEL)
				),
				'std' => '3',
				'class' => '',
				'is_content' => 0
			)
		),
		'child' => array(
			'author' => array(
				'name' => __('Testimony Author', INBOUND_LABEL),
				'desc' => __('Enter the testimony author name.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'meta' => array(
				'name' => __('Testimony Author Meta', INBOUND_LABEL),
				'desc' => __('The author job, company or website name.', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'content' => array(
				'name' => __('Testimony Text', INBOUND_LABEL),
				'desc' => __('Put the content here.', INBOUND_LABEL),
				'type' => 'textarea',
				'std' => '',
				'class' => '',
				'is_content' => 1
			)
		),
		'child_code' => 'testimony'
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('testimonial', 'inbound_shortcode_testimonial');

	function inbound_shortcode_testimonial( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'heading' => __('Testimonial', INBOUND_LABEL),
			'column' => 1,
		), $atts));

		$grid = ' grid full';
		if ($column == '2') $grid = ' grid one-half';
		if ($column == '3') $grid = ' grid one-third';
		if ($column == '4') $grid = ' grid one-fourth';
		if ($column == '5') $grid = ' grid one-fifth';
		$out = '';


		$out .= '<div class="testimonial row">';
		if ($heading != '') {
			$out .= '<div class="grid full"><div class="heading"><h3>'.$heading.'</h3><div class="sep"></div></div></div>';
		}

		if (!preg_match_all("/(.?)\[(testimony)\b(.*?)(?:(\/))?\](?:(.+?)\[\/testimony\])?(.?)/s", $content, $matches)) {
			return do_shortcode($content);
		}
		else {

			for($i = 0; $i < count($matches[0]); $i++) {
				$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
			}

			for($i = 0; $i < count($matches[0]); $i++) {
	            $out .= '<div class="'.$grid.'">';
                    $out .= '<div class="fancy-quote">';
                        $out .= '<div class="quote-text">';
                            $out .= '<div class="triangle"></div>';
                            $out .= '<p>'.do_shortcode(trim($matches[5][$i])).'</p>';
                        $out .= '</div>';

                        $out .= '<div class="quote-author">';
                            if( $matches[3][$i]['author'] ) {
	                            $out .= '<span class="quote-author-name">'.$matches[3][$i]['author'].'</span>';
	                        }

                            if( $matches[3][$i]['meta'] ){
		                        $out .= ' - <span class="quote-author-meta">'.$matches[3][$i]['meta'].'</span>';
		                    }
                        $out .= '</div>';
                    $out .= '</div>';
                $out .= '</div>';

                if( $i == $column - 1 ) {
                	$out .= '<div class="clear"></div>';
                }
            }
		}

		$out .= '</div>';

		return $out;
	}