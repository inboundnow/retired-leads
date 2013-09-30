<?php

add_action( 'widgets_init', 'wp_cta_load_widgets' );

function wp_cta_load_widgets() {

	register_widget( 'wp_cta_dynamic_widget' );
	register_widget( 'wp_cta_placement_widget' );
}
	
class wp_cta_dynamic_widget extends WP_Widget 
{
	
	function wp_cta_dynamic_widget() {
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'class_wp_cta_dynamic_widget', 'description' => __('Use this widget to display Calls to Action in sidebars', 'wp_cta_sidebar_widget') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'id_wp_cta_dynamic_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'id_wp_cta_dynamic_widget', __('Dynamic Call to Action Widget', 'wp_cta_sidebar_widget'), $widget_ops, $control_ops );
	}
	
	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		global $wp_query; global $post;
		$this_id = $wp_query->post->ID;
		$this_type = $wp_query->post->post_type;
		
		
			
			$wp_cta_post_template_ids = get_post_meta($post->ID, 'cta_display_list');
			$wp_cta_placement = get_post_meta($post->ID, 'wp_cta_content_placement');
				
				if (!empty($wp_cta_placement)){ 
				$placement = $wp_cta_placement[0];
				} else {
					$placement = 'off';
				}

			if ($placement=='widget_1')
			{
				
				$conversion_area = do_shortcode(get_post_meta($this_id, 'wp-cta-conversion-area', true));
				$standardize_form = get_option( 'main-wp-call-to-action-auto-format-forms' , 1); // conditional to check for options
			
				$count = count($wp_cta_post_template_ids[0]);
		        $rand_key = array_rand($wp_cta_post_template_ids[0], 1);
		        $ctaw_id = $wp_cta_post_template_ids[0][$rand_key];
		        $the_link = get_permalink( $ctaw_id );
		    	

		    	$ad_content = '<iframe id="wp-cta-per-page" class="wp-cta-display" src="" scrolling="no" frameBorder="0" style="border:none; overflow:hidden; " allowtransparency="true"></iframe>';
				/* Before widget (defined by themes). */
				//echo $before_widget;

				/* Display the widget title if one was input (before and after defined by themes). 
				if ($title)
				{
					echo $before_title . $title . $after_title;
				} */
				
				echo $ad_content;
				
				/* After widget (defined by themes). */
				//echo $after_widget;
			}
		
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			This call to action area is dynamic. It will be completely empty unless you have toggled on a call to action on the individual pages settings and selected the "sidebar" option.
		</p>

	<?php
	}
}

class wp_cta_placement_widget extends WP_Widget 
{
	
	function wp_cta_placement_widget() {
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'class_wp_cta_placement_widget', 'description' => __('Use this widget to display Calls to Action in sidebars', 'wp_cta_sidebar_widget') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'id_wp_cta_placement_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'id_wp_cta_placement_widget', __('Call to Action Widget', 'wp_cta_sidebar_widget'), $widget_ops, $control_ops );
	}
	
	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		global $wp_query; global $post;
		$this_id = $wp_query->post->ID;
		$this_type = $wp_query->post->post_type;
		
		
			
			$wp_cta_post_template_ids = get_post_meta($post->ID, 'cta_display_list');
			$wp_cta_placement = get_post_meta($post->ID, 'wp_cta_content_placement');
			
				if (!empty($wp_cta_placement)){ 
				$placement = $wp_cta_placement[0];
				} else {
					$placement = 'off';
				}

			
		    	
		    	$selected_ctas = array();
               	$args = array('post_type' => 'wp-call-to-action', 'numberposts' => -1);
            	$cta_post_type = get_posts($args);
            	
                foreach ($cta_post_type as $cta) {
                    if(isset($instance['cta_ids_' . $cta->ID]) && $instance['cta_ids_' . $cta->ID] == '1'){
                        array_push($selected_ctas, $cta->ID);
                    }
                }
        		$cta_ids =  implode(",", $selected_ctas);
        		$count = count($selected_ctas);
        		$rand_key = array_rand($selected_ctas, 1);
		        $ctaw_id = $selected_ctas[$rand_key];
		        $the_link = get_permalink( $ctaw_id );
        		$width = $instance['cta_default_width'];
        		$height = $instance['cta_default_height'];
        		$margin_top = $instance['cta_margin_top'];
        		$margin_bottom = $instance['cta_margin_bottom'];

        		// Behavorial function
        		$behavorial = get_post_meta( $ctaw_id, 'wp_cta_global_bt_status', true ); // move to ext
				$behavorial_class = "";
				if(!empty($behavorial) && $behavorial != "") {
					$behavorial_class = ' behavorial';
				}


        		$turn_iframes_off = $instance['no_a_b'];
				
				if($turn_iframes_off === 1){
					$cta_content = wp_cta_no_frame_display( $cta_ids );
					echo $cta_content;
					return;
				}
				$width_output = "";
		    	$class = "";
		    	$height_output = "";
		    	$display_output = "display:none;";
        		//print_r($cta_ids);
        		if(!empty($width) && $width != "") {
	    		str_replace("px", "", $width);
	    			$width_output = "width:" . $width . "px;";
	    			$class = " widget-default-cta-size";
		    	} 
		    	//$height = get_post_meta( $ctaw_id, 'wp_cta_height', true );
		    	if(!empty($height) && $height != "") {
		    	str_replace("px", "", $height);
		    		$height_output = "height:" . $height . "px;";
		    		$display_output = "";
		    		$class = " widget-default-cta-size";
		    	}
				$margin_top_output = "";
		    	if(!empty($margin_top) && $margin_top != "") {
		    	str_replace("px", "", $margin_top);
		    		$margin_top_output = "margin-top:" . $margin_top . "px;";
		    	}

		    	$margin_bottom_output = "";
		    	if(!empty($margin_bottom) && $margin_bottom != "") {
		    	str_replace("px", "", $margin_bottom);
		    		$margin_bottom_output = "margin-bottom:" . $margin_bottom . "px;";
		    	}
        		
		    	$cta_content = '<iframe id="wp-cta" class="wp-cta-display'.$class.''.$behavorial_class.'" src="'.$the_link.'" scrolling="no" frameBorder="0" style="border:none; overflow:hidden; '.$width_output.' '.$height_output.'  '.$margin_top_output.' '.$margin_bottom_output.'" allowtransparency="true"></iframe>';
				/* Before widget (defined by themes). */
				//echo $before_widget;

				/* Display the widget title if one was input (before and after defined by themes). 
				if ($title)
				{
					echo $before_title . $title . $after_title;
				} */
				
				echo $cta_content;
				
				/* After widget (defined by themes). */
				//echo $after_widget;
				//echo do_shortcode($myhubspotwp_action->hs_display_action($before_widget, $after_widget, $before_title, $after_title, $hide_title, $cta_ids));
		
		
	}

	/**
	 * Update the widget settings.
	 */
	 /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {       
        $instance = $old_instance;
        $instance['no_a_b'] = $new_instance['no_a_b'] ? 1 : 0;
        $instance['cta_default_width'] = $new_instance['cta_default_width'] ? $new_instance['cta_default_width'] : "";
        $instance['cta_default_height'] = $new_instance['cta_default_height'] ? $new_instance['cta_default_height'] : "";
        $instance['cta_margin_top'] = $new_instance['cta_margin_top'] ? $new_instance['cta_margin_top'] : "";
        $instance['cta_margin_bottom'] = $new_instance['cta_margin_bottom'] ? $new_instance['cta_margin_bottom'] : "";

                $args = array('post_type' => 'wp-call-to-action', 'numberposts' => -1);
                $cta_post_type = get_posts($args);
                foreach ($cta_post_type as $cta) {
                    $instance['cta_ids_' . $cta->ID] = $new_instance['cta_ids_' . $cta->ID] ? 1 : 0;
                }
        return $instance;
    }

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
    function form($instance) {
            $default_instance = array('no_a_b' => '');
            $args = array('post_type' => 'wp-call-to-action', 'numberposts' => -1);
            $cta_post_type = get_posts($args);
            foreach ($cta_post_type as $cta) {
                $cta_id = 'cta_ids_' . $cta->ID;
                $default_instance[$cta_id] = 0;
            }
            $instance = wp_parse_args($instance, $default_instance);
           //print_r($instance);
           	$width = "";
            if ( isset( $instance[ 'cta_default_width' ] ) ) {
			$width = $instance[ 'cta_default_width' ];
			}
			$height = "";
			 if ( isset( $instance[ 'cta_default_height' ] ) ) {
			$height = $instance[ 'cta_default_height' ];
			}
			$margin_top = "";
			 if ( isset( $instance[ 'cta_margin_top' ] ) ) {
			$margin_top = $instance[ 'cta_margin_top' ];
			}
			$margin_bottom = "";
			 if ( isset( $instance[ 'cta_margin_bottom' ] ) ) {
			$margin_bottom = $instance[ 'cta_margin_bottom' ];
			}
            ?>
            
            <div class='cta-widget-p'><strong>Select Calls to Action(s):</strong><br />
            	<small>If multiple calls to action are checked, they will randomly rotate. Only 1 CTA is displayed per widget</small>
            <div class='cta-widget-select-options'>	
            <?php
            foreach ($cta_post_type as $cta) {
                setup_postdata($cta); 
                $this_id = $cta->ID;
				$this_link = get_permalink( $this_id );
				$this_link = preg_replace('/\?.*/', '', $this_link); ?>
                <input class="checkbox" type="checkbox" <?php checked($instance['cta_ids_' . $cta->ID], '1'); ?> value="<?php _e($cta->ID); ?>" name="<?php echo $this->get_field_name('cta_ids_' . $cta->ID); ?>" id="<?php echo $this->get_field_id('cta_ids_' . $cta->ID); ?>" /> <label for="<?php echo $this->get_field_id('cta_ids_' . $cta->ID); ?>"><?php _e($cta->post_title); ?><a class='thickbox cta-links-hidden cta-widget-preview-links' id="cta-<?php echo $this_id;?>" href='<?php echo $this_link;?>?wp-cta-variation-id=0&wp_cta_iframe_window=on&post_id=<?php echo $cta->ID; ?>&TB_iframe=true&width=640&height=703'>Preview</a></label>
                <br />
                <?php
            }
        ?>
    	</div>
        </div>

       	<hr>
       	<h4 class='cta-advanced-section'>Advanced Options</h4>
        <div class="advanced-cta-widget-options">
        	<div class='cta-widget'><label for="<?php echo $this->get_field_id('cta_margin_top'); ?>">Margin Top</label>
        	<input class="cta-text" type="text" value="<?php echo $margin_top; ?>" id="<?php echo $this->get_field_id('cta_margin_top'); ?>" name="<?php echo $this->get_field_name('cta_margin_top'); ?>" />px</div>
        	<div class='cta-widget'><label for="<?php echo $this->get_field_id('cta_margin_bottom'); ?>">Margin Bottom</label>
        	<input class="cta-text" type="text" value="<?php echo $margin_bottom; ?>" id="<?php echo $this->get_field_id('cta_margin_bottom'); ?>" name="<?php echo $this->get_field_name('cta_margin_bottom'); ?>" />px</div>
        	<div class='cta-widget'><label for="<?php echo $this->get_field_id('cta_default_width'); ?>">Set Default Width</label>
        	<input class="cta-text" type="text" value="<?php echo $width; ?>" id="<?php echo $this->get_field_id('cta_default_width'); ?>" name="<?php echo $this->get_field_name('cta_default_width'); ?>" />px</div>
        	<div class='cta-widget'><label for="<?php echo $this->get_field_id('cta_default_height'); ?>">Set Default height</label>
        	<input class="cta-text" type="text" value="<?php echo $height; ?>" id="<?php echo $this->get_field_id('cta_default_height'); ?>" name="<?php echo $this->get_field_name('cta_default_height'); ?>" />px</div>
        	<input class="checkbox" type="checkbox" <?php checked($instance['no_a_b'], '1'); ?> id="<?php echo $this->get_field_id('no_a_b'); ?>" name="<?php echo $this->get_field_name('no_a_b'); ?>" /> <label for="<?php echo $this->get_field_id('no_a_b'); ?>"><strong>Turn off iframes</strong> <small>This is disable A/B testing (only works with images). Not recommended</small></label>
        </div>

        <?php
    }
}
//=============================================
// Display call to action via WP_Query
//=============================================
    function wp_cta_no_frame_display($cta_ids = null){
  
        $cta_array = array();
        $args = array('post_type' => 'wp-call-to-action');

                if($cta_ids != null && trim($cta_ids) != ""){
                    $args = wp_parse_args( array('post__in' => explode(',', $cta_ids)), $args );
                }

                $queryObject = new WP_Query($args);
                // The Loop...
                if ($queryObject->have_posts()) {
                        while ($queryObject->have_posts()) {
                                $queryObject->the_post();
                                array_push($cta_array,
                                        array(
                                            get_the_ID(),
                                            get_the_title(),
                                            wpautop(get_the_content())
                                        ));
                        }

                    //display results
                    $rand_key = array_rand($cta_array,1);
                    $cta_id = $cta_array[$rand_key][0];
                    $hs_title = $cta_array[$rand_key][1];
                    $cta_content = $cta_array[$rand_key][2];

                
                    $lead_cpt_id = (isset($_COOKIE['wp_lead_id'])) ? $_COOKIE['wp_lead_id'] : false;
    				$lead_email = (isset($_COOKIE['wp_lead_email'])) ? $_COOKIE['wp_lead_email'] : false;
    				$lead_unique_key = (isset($_COOKIE['wp_lead_uid'])) ? $_COOKIE['wp_lead_uid'] : false;

		    		if ($lead_cpt_id) {
		                $lead_id = $lead_cpt_id;
		                $type = 'wplid';
		            }
		            elseif ($lead_email) {
		                $lead_id = $lead_email;
		              	$type = 'wplemail';
		            }
		            elseif ($lead_unique_key) {
		                $lead_id = $lead_unique_key;
		                $type = 'wpluid';
		            } else {
		            	$lead_id = null;
		            	$type = null;
		            }
		
                    $siteurl = get_page_link();
                    
                    $symbol = (preg_match('/\?/', $siteurl)) ? '&' : '?';
                    $cta_content = str_replace('"', '\'', $cta_content);
                    $cta_content = str_replace('href=\'http', 'href=\'' . $siteurl . $symbol . 'wp_cta_redirect_' . $cta_id . '=http', $cta_content);

                    /* $str = '<a title="Hudson Test" href="href=" http:="" inboundsoon.wpengine.com?wp_cta_redirect_2112="http://inboundsoon.wpengine.com/go/hudson-test-2/"><img class="alignright size-full wp-image-1711" alt="7-strat" src="http://inboundsoon.wpengine.com/wp-content/uploads/2013/06/7-strat.png" width="270" height="318"></a>';
                   	$cta_content= preg_replace('/(http[s]?:[^\s]*)/i','$0&wp-cta-v=0&wpl_id='.$lead_id.'&l_type='.$type.'',$cta_content); */
					//$cta_content= preg_replace('/(http[s]?:[^\s]*)/i','$0&wp-cta-v=0&wpl_id='.$lead_id.'&l_type='.$type.'',$cta_content);
					
                    $content = "";

                   //http://inboundsoon.wpengine.com/go/hudson-test-2/?wp_cta_redirect_2112=http://inboundsoon.wpengine.com/go/hudson-test-2/&wp-cta-v=0&wpl_id=2078&l_type=wplid
                   $content = "<div class='wp-cta-container'>";
                    $content .= $cta_content;
                	$content .= "</div>";
                    
        } else {
            $content = "";
        }
                wp_reset_postdata();

        return $content;
    }