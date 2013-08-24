<?php

if (is_admin())
{	
	//define main tabs and bind display functions
	if (isset($_GET['page'])&&($_GET['page']=='wp_cta_global_settings'&&$_GET['page']=='wp_cta_global_settings'))
	{
		add_action('admin_init','wp_cta_global_settings_enqueue');
		function wp_cta_global_settings_enqueue()
		{		
			wp_enqueue_style('wp-cta-css-global-settings-here', WP_CTA_URLPATH . 'css/admin-global-settings.css');			
		}
	}
	
	// Setup navigation and display elements
	$tab_slug = 'main';
	$wp_cta_global_settings[$tab_slug]['label'] = 'Global Settings';	
	
	$wp_cta_global_settings[$tab_slug]['options'][] = wp_cta_add_option($tab_slug,"text","wp-call-to-action-permalink-prefix","cta","Default landing page permalink prefix","Enter in the 'prefix' for landing page permalinks. eg: /prefix/pemalink-name", $options=null);
	$wp_cta_global_settings[$tab_slug]['options'][] = wp_cta_add_option($tab_slug,"radio","wp-call-to-action-auto-format-forms","1","Enable Form Standardization","With this setting enabled landing pages plugin will clean and standardize all input ids and classnames. Uncheck this setting to disable standardization.", $options= array('1'=>'on','0'=>'off'));
	$wp_cta_global_settings[$tab_slug]['options'][] = wp_cta_add_option($tab_slug,"textarea","wp-call-to-action-auto-format-forms-retain-elements","<button><script><textarea><style><input><form><select><label><a><p><b><u><strong><i><img><strong><span><font><h1><h2><h3><center><blockquote><embed><object><small>","Form Standardization Element Whitelist","Form standardization strips the conversion area content of html elements. Add the elements you do not want to be stripped to this list.", $options= array('1'=>'on','0'=>'off'));


	function wp_cta_get_global_settings_elements()
	{
		global $wp_cta_global_settings;
		return $wp_cta_global_settings;
	}	
	
	function wp_cta_display_global_settings_js()
	{	
		if (isset($_GET['tab']))
		{
			$default_id = $_GET['tab'];
		}
		else
		{
			$default_id ='main';
		}
		?>
		<script type='text/javascript'>
			jQuery(document).ready(function() 
			{
				//jQuery('#<? echo $default_id; ?>').css('display','block');
				//jQuery('#<? echo $default_id; ?>').css('display','block');
				 setTimeout(function() {
	     			var getoption = document.URL.split('&option=')[1];
					var showoption = "#" + getoption;
					jQuery(showoption).click();
    			}, 100);

				jQuery('.wp-cta-nav-tab').live('click', function() {
					var this_id = this.id.replace('tabs-','');
					//alert(this_id);
					jQuery('.wp-cta-tab-display').css('display','none');
					jQuery('#'+this_id).css('display','block');
					jQuery('.wp-cta-nav-tab').removeClass('nav-tab-special-active');
					jQuery('.wp-cta-nav-tab').addClass('nav-tab-special-inactive');
					jQuery('#tabs-'+this_id).addClass('nav-tab-special-active');						
					jQuery('#id-open-tab').val(this_id);
				});
	
			});			
		</script>
		<?php
	}
	
	function wp_cta_display_global_settings()
	{	
		global $wpdb;
		global $wp_cta_global_settings;
		$wp_cta_global_settings = wp_cta_get_global_settings_elements();
		$active_tab = 'main'; 
		if (isset($_REQUEST['open-tab']))
		{
			$active_tab = $_REQUEST['open-tab'];
		}

		//echo $active_tab;exit;
		
		wp_cta_display_global_settings_js();
		wp_cta_save_global_settings();

		echo '<h2 class="nav-tab-wrapper">';		
	
		foreach ($wp_cta_global_settings as $key => $data)
		{
			?>
			<a  id='tabs-<?php echo $key; ?>' class="wp-cta-nav-tab nav-tab nav-tab-special<?php echo $active_tab == $key ? '-active' : '-inactive'; ?>"><?php echo $data['label']; ?></a> 
			<?php
		}
		echo "</h2><div class='wp-cta-settings-tab-sidebar'><div class='wp-cta-sidebar-settings'><h2 style='font-size:17px;'>Like the Plugin? Leave us a review</h2><center><a class='review-button' href='http://wordpress.org/support/view/plugin-reviews/cta?rate=5#postform' target='_blank'>Leave a Review</a></center><small>Reviews help constantly improve the plugin & keep us motivated! <strong>Thank you for your support!</strong></small></div><div class='wp-cta-sidebar-settings'><h2>Help keep the plugin up to date, awesome & free!</h2><form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_top'>
			<input type='hidden' name='cmd' value='_s-xclick'>
			<input type='hidden' name='hosted_button_id' value='GKQ2BR3RKB3YQ'>
			<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
			<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'></form>
			<small>Spare some change? Buy us a coffee/beer.<strong> We appreciate your continued support.</strong></small></div><div class='wp-cta-sidebar-settings'><h2 style='font-size:18px;'>Follow Updates on Facebook</h2><iframe src='//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Finboundnow&amp;width=234&amp;height=65&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=false&amp;appId=364256913591848' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:234px; height:65px;' allowTransparency='true'></iframe></div></div>";
		echo "<form action='edit.php?post_type=wp-call-to-action&page=wp_cta_global_settings' method='POST'>
		<input type='hidden' name='nature' value='wp-cta-global-settings-save'>
		<input type='hidden' name='open-tab' id='id-open-tab' value='{$active_tab}'>";
		foreach ($wp_cta_global_settings as $key => $array)
		{
			$these_settings = $wp_cta_global_settings[$key]['options'];	
			wp_cta_render_global_settings($key,$these_settings, $active_tab);
		}
		echo '<div style="float:left;padding-left:9px;padding-top:20px;">
				<input type="submit" value="Save Settings" tabindex="5" id="wp-cta-button-create-new-group-open" class="button-primary" >
			</div>';
		echo "</form>";
		?>
		<div id="wp-cta-additional-resources" class="clear">
			<hr>
		<div id="more-templates">
			<center>
			<a href="http://www.inboundnow.com/wp-call-to-actions/downloads/category/templates/" target="_blank"><img src="<?php echo WP_CTA_URLPATH;?>/images/templates-image.png"></a>
			
			</center>
		</div>
		<div id="more-addons">
			<center>
			<a href="http://www.inboundnow.com/wp-call-to-actions/downloads/category/add-ons/" target="_blank"><img src="<?php echo WP_CTA_URLPATH;?>/images/add-on-image.png"></a>
		</center>
		</div>
		<div id="custom-templates">
			<center><a href="http://www.inboundnow.com/wp-call-to-actions/custom-wordpress-wp-call-to-action-setup/" target=="_blank"><img src="<?php echo WP_CTA_URLPATH;?>/images/custom-setup-image.png"></a>
			</center>
		</div>
		</div>
		<div class="clear" id="php-sql-wp-cta-version">
		 <h3>Installation Status</h3>
              <table class="form-table" id="wp-cta-wordpress-site-status">

                <tr valign="top">
                   <th scope="row"><label>PHP Version</label></th>
                    <td class="installation_item_cell">
                        <strong><?php echo phpversion(); ?></strong>
                    </td>
                    <td>
                        <?php
                            if(version_compare(phpversion(), '5.0.0', '>')){
                                ?>
                                <img src="<?php echo WP_CTA_URLPATH;?>/images/tick.png"/>
                                <?php
                            }
                            else{
                                ?>
                                <img src="<?php echo WP_CTA_URLPATH;?>/images/cross.png"/>
                                <span class="installation_item_message"><?php _e("Gravity Forms requires PHP 5 or above.", "gravityforms"); ?></span>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                   <th scope="row"><label>MySQL Version</label></th>
                    <td class="installation_item_cell">
                        <strong><?php echo $wpdb->db_version();?></strong>
                    </td>
                    <td>
                        <?php
                            if(version_compare($wpdb->db_version(), '5.0.0', '>')){
                                ?>
                                <img src="<?php echo WP_CTA_URLPATH;?>/images/tick.png"/>
                                <?php
                            }
                            else{
                                ?>
                                <img src="<?php echo WP_CTA_URLPATH;?>/images/cross.png"/>
                                <span class="installation_item_message"><?php _e("Gravity Forms requires MySQL 5 or above.", "gravityforms"); ?></span>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                   <th scope="row"><label>WordPress Version</label></th>
                    <td class="installation_item_cell">
                        <strong><?php echo get_bloginfo("version"); ?></strong>
                    </td>
                    <td>
                        <?php
                            if(version_compare(get_bloginfo("version"), '3.3', '>')){
                                ?>
                                <img src="<?php echo WP_CTA_URLPATH;?>/images/tick.png"/>
                                <?php
                            }
                            else{
                                ?>
                                <img src="<?php echo WP_CTA_URLPATH;?>/images/cross.png"/>
                                <span class="installation_item_message">landing pages requires version X or higher</span>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                 <tr valign="top">
                   <th scope="row"><label>Landing Page Version</label></th>
                    <td class="installation_item_cell">
                        <strong>Version <?php echo wp_call_to_action_get_version();?></strong>
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
        </div>
	<?php	
	}
	
	function wp_cta_save_global_settings() 
	{
		
		$wp_cta_global_settings = wp_cta_get_global_settings_elements();
		
		if (!isset($_POST['nature']))
			return;
	
		
		foreach ($wp_cta_global_settings as $key=>$array)
		{	
			$wp_cta_options = $wp_cta_global_settings[$key]['options'];		
			//echo 1; 

			// loop through fields and save the data
			foreach ($wp_cta_options as $option) 
			{
				$old = get_option($option['id']);				
				$new = $_POST[$option['id']];	
			
				if ((isset($new) && $new !== $old )|| !isset($old) ) 
				{
					//echo $option['id'];exit;
					$bool = update_option($option['id'],$new);				
					if ($option['id']=='main-wp-call-to-action-permalink-prefix'||$option['id']=='main-wp-call-to-action-group-permalink-prefix')
					{
						global $wp_rewrite;
						$wp_rewrite->flush_rules();
					}
					if ($option['type']=='license-key')
					{						
						// retrieve the license from the database
						$license = trim( get_option( 'edd_sample_license_key' ) );
						
						// data to send in our API request
						$api_params = array( 
							'edd_action'=> 'activate_license', 
							'license' 	=> $new, 
							'item_name' =>  $option['slug'] // the name of our product in EDD
						);
						
						// Call the custom API.
						$response = wp_remote_get( add_query_arg( $api_params, WP_CTA_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

						// make sure the response came back okay
						if ( is_wp_error( $response ) )
							break;

						// decode the license data
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						
						//echo $license_data->license;
						//echo $option['slug'];exit;
						
						// $license_data->license will be either "active" or "inactive"						
						$license_status = update_option('wp_cta_license_status-'.$option['slug'], $license_data->license);
					}
				} 
				elseif ('' == $new && $old) 
				{
					$bool = update_option($option['id'],$option['default']);
				}
				else
				{
					//print_r($option);
					if ($option['type']=='license-key'&& $new )
					{
					
						$license_status = get_option('wp_cta_license_status-'.$option['slug']);
						
						if ($license_status=='valid' && $new == $old)
						{
							continue;
						}

						// retrieve the license from the database
						$license = trim( get_option( 'edd_sample_license_key' ) );
						
						// data to send in our API request
						$api_params = array( 
							'edd_action'=> 'activate_license', 
							'license' 	=> $new, 
							'item_name' =>  $option['slug'] // the name of our product in EDD
						);
						
						// Call the custom API.
						$response = wp_remote_get( add_query_arg( $api_params, WP_CTA_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
						//print_r($response);
						//echo "<br>";
						
						// make sure the response came back okay
						if ( is_wp_error( $response ) )
							break;

						// decode the license data
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						
						// $license_data->license will be either "active" or "inactive"						
						$license_status = update_option('wp_cta_license_status-'.$option['slug'], $license_data->license);
					}
				}
				
				do_action('wp_cta_save_global_settings',$option);
			} // end foreach		
			
		}
		//exit;
	}
	//exit;
}