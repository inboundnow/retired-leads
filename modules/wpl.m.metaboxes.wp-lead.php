<?php
/* INCLUDE FILE WHERE MAIN USERFIELDS ARE DEFINED */
include_once('wpl.m.userfields.php');

if (isset($_GET['page'])&&($_GET['page']=='lp_global_settings'&&$_GET['page']=='lp_global_settings'))
{
	add_action('admin_init','wpl_manage_lead_enqueue');
	function wpl_manage_lead_enqueue()
	{		
		wp_enqueue_style('wpl_manage_lead_css', WPL_URL . 'css/admin-global-settings.css');	
	}
}

/* REMOVE DEFAULT METABOXES */
add_filter('default_hidden_meta_boxes', 'wplead_hide_metaboxes', 10, 2);
function wplead_hide_metaboxes($hidden, $screen) 
{

	global $post;
	if ( isset($post) && $post->post_type == 'wp-lead' )
	{
		//print_r($hidden);exit;
		$hidden = array(
			'postexcerpt',
			'slugdiv',
			'postcustom',
			'trackbacksdiv', 
			'lead-timelinestatusdiv', 
			'lead-timelinesdiv', 
			'authordiv', 
			'revisionsdiv',
			'wpseo_meta',
			'wp-advertisement-dropper-post',
			'postdivrich'
		);
		
	}
	return $hidden;
}

/* REMOVE WYSIWYG */
add_filter( 'user_can_richedit', 'wplead_disable_for_cpt' );
function wplead_disable_for_cpt( $default ) {
    global $post;
    if ( $post->post_type == 'wp-lead' )
	{
      // echo 1; exit;
	   return false;
	}
    return $default;
}



function wp_leads_get_search_keywords($url = '')
{
	// Get the referrer
	//$referrer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

	// Parse the referrer URL
   
  	$parsed_url = parse_url($url);
  	$host = $parsed_url['host']; // base url
    $se_match = array("google", "yahoo", "bing");

		foreach($se_match as $val) {
		  if (preg_match("/" . $val . "/", $url)){
		  	$is_search_engine = stripslashes($bl);
		  }
		}

	$query_str = (!empty($parsed_url['query'])) ? $parsed_url['query'] : '';
	$query_str = (empty($query_str) && !empty($parsed_url['fragment'])) ? $parsed_url['fragment'] : $query_str;

	// Parse the query string into a query array
	parse_str($query_str, $query);
	$empty_keywords = "Empty Keywords, User is probably logged into " . $is_search_engine;
	// Check some major search engines to get the correct query var
	$search_engines = array(
		'q' => 'alltheweb|aol|ask|ask|bing|google',
		'p' => 'yahoo',
		'wd' => 'baidu'
	);
	foreach ($search_engines as $query_var => $se)
	{
		$se = trim($se);
		preg_match('/(' . $se . ')\./', $host, $matches);
		if (!empty($matches[1]) && !empty($query[$query_var])) {
			return "From". $is_search_engine ." ". $query[$query_var];
		} else {
			return "From". $is_search_engine ." ". $empty_keywords;
		}
	}
	// return false;
}
//echo wp_leads_get_search_keywords('http://www.google.co.th/url?sa=t&rct=j&q=keywordsssss&esrc=s&source=web&cd=4&ved=0CE8QFjAD&url=http%3A%2F%2Fwww.inboundnow.com%2Fhow-to-properly-set-up-wordpress-301-redirects%2F&ei=FMHDUZPqBMztiAfi_YCoBA&usg=AFQjCNFuh3aH04u2Z4xXl2XNb3emE95p5Q&sig2=yrdyyZz83KfGte6SNZL7gA&bvm=bv.48293060,d.aGc');

/* Add quick stats box */
add_action('add_meta_boxes', 'wplead_display_quick_stat_metabox');
function wplead_display_quick_stat_metabox() {
	global $post;
	$first_name = get_post_meta( $post->ID , 'wpleads_first_name',true );
	$last_name = get_post_meta( $post->ID , 'wpleads_last_name', true );
	add_meta_box( 
	'wplead-quick-stats-metabox', 
	__( "Quick Stats", 'wplead_metabox_gravatar_preview' ),
	'wplead_quick_stats_metabox',
	'wp-lead' , 
	'side', 
	'high' );
}

function leads_time_diff($date1, $date2) {
	$time_diff = array();
	$diff = abs(strtotime($date2) - strtotime($date1));
	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
	$minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60) / 60);
	//$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

	$time_diff['years'] = $years;
	$time_diff['y-text'] = ($years > 1) ? "Years" : "Year";
	$time_diff['months'] = $months;
	$time_diff['m-text'] = ($months > 1) ? "Months" : "Month";
	$time_diff['days'] = $days;
	$time_diff['d-text'] = ($days > 1) ? "Days" : "Day";
	$time_diff['hours'] = $hours;
	$time_diff['h-text'] = ($hours > 1) ? "Hours" : "Hour";
	$time_diff['minutes'] = $minutes;
	$time_diff['mm-text'] = ($minutes > 1) ? "Minutes" : "Minute"; 

	return $time_diff; 
}

function wplead_quick_stats_metabox() {
	global $post;
	global $wpdb;

	//define last touch point

		$last_conversion = get_post_meta($post->ID,'wpleads_conversion_data', true);
		$last_conversion = json_decode($last_conversion, true);
		$count_conversions = count($last_conversion);
		$the_date = $last_conversion[$count_conversions]['datetime']; // actual

		$email = get_post_meta( $post->ID , 'wpleads_email_address', true );
		$first_name = get_post_meta( $post->ID , 'wpleads_first_name',true );
		$last_name = get_post_meta( $post->ID , 'wpleads_last_name', true );
		$conversions_count = get_post_meta($post->ID,'wpl-lead-conversion-count', true);
		$page_view_count = get_post_meta($post->ID,'wpl-lead-page-view-count', true);
	?>
	<div>
		<div class="inside" style='margin-left:-8px;text-align:center;'> 

			<div id="quick-stats-box">
				<?php do_action('wpleads_before_quickstats'); // Custom Action for additional data ?>
			<div id="page_view_total">Total Page Views <span id="p-view-total"><?php echo $page_view_count; ?></span></div>
			<div id="conversion_count_total"># of Conversions <span id="conversion-total"><?php echo $conversions_count; ?></span></div>
			
		<?php if (!empty($the_date)) {
		$today = new DateTime(date('Y-m-d G:i:s'));
		$today = $today->format('Y-m-d G:i:s');
		$date_obj = leads_time_diff($the_date, $today);
		$wordpress_timezone = get_option('gmt_offset');
		$years = $date_obj['years'];
		$months = $date_obj['months'];
		$days = $date_obj['days'];
		$hours = $date_obj['hours'] + $wordpress_timezone;
		$minutes = $date_obj['minutes'];
		$year_text = $date_obj['y-text'];
		$month_text = $date_obj['m-text'];
		$day_text = $date_obj['d-text'];
		$hours_text = $date_obj['h-text'];
		$minute_text = $date_obj['mm-text']; ?>
		
			<div id="last_touch_point">Time Since Last Conversion 
				<span id="touch-point">
					
					<?php

					echo "<span class='touchpoint-year'><span class='touchpoint-value'>" . $years . "</span> ".$year_text." </span><span class='touchpoint-month'><span class='touchpoint-value'>" . $months."</span> ".$month_text." </span><span class='touchpoint-day'><span class='touchpoint-value'>".$days."</span> ".$day_text." </span><span class='touchpoint-hour'><span class='touchpoint-value'>".$hours."</span> ".$hours_text." </span><span class='touchpoint-minute'><span class='touchpoint-value'>".$minutes."</span> ".$minute_text."</span> Ago"; 
					?>
				</span>
			</div>
		<?php } ?>
			<div id="time-since-last-visit"></div>
			<div id="lead-score"></div><!-- Custom Before Quick stats and After Hook here for custom fields shown -->
			</div>
				<?php do_action('wpleads_after_quickstats'); // Custom Action for additional data after quick stats ?>
		</div>	
	</div>
	<?php
}


/* ADD IP ADDRESS METABOX TO SIDEBAR */
add_action('add_meta_boxes', 'wplead_display_ip_address_metabox');
function wplead_display_ip_address_metabox() {
	global $post;
	add_meta_box( 
	'lp-ip-address-sidebar-preview', 
	__( 'Last Conversion Activity Location', 'wplead_metabox_ip_address_preview' ),
	'wplead_ip_address_metabox',
	'wp-lead' , 
	'side', 
	'low' );
}

function wplead_ip_address_metabox() {
	global $post;

	$ip_address = get_post_meta( $post->ID , 'wpleads_ip_address', true );
	$geo_array = unserialize(wpleads_remote_connect('http://www.geoplugin.net/php.gp?ip='.$ip_address));
	$city = get_post_meta($post->ID, 'wpleads_city', true);		
	$state = get_post_meta($post->ID, 'wpleads_region_name', true);	
	//print_r($geo_array);
	$latitude = $geo_array['geoplugin_latitude'];
	$longitude = $geo_array['geoplugin_longitude'];
	
	?>
	<div >
		<div class="inside" style='margin-left:-8px;text-align:left;'> 
			<div id='last-conversion-box'>
	
						<div id='lead-geo-data-area'>
							
						<?php
						if (is_array($geo_array))
						{
							unset($geo_array['geoplugin_status']);
							unset($geo_array['geoplugin_credit']);
							unset($geo_array['geoplugin_request']);
							unset($geo_array['geoplugin_currencyConverter']);
							unset($geo_array['geoplugin_currencySymbol_UTF8']);
							unset($geo_array['geoplugin_currencySymbol']);
							unset($geo_array['geoplugin_dmaCode']);
							if (isset($geo_array['geoplugin_city']) && $geo_array['geoplugin_city'] != ""){
							echo "<div class='lead-geo-field'><span class='geo-label'>City:</span>" . $geo_array['geoplugin_city'] . "</div>"; }
							if (isset($geo_array['geoplugin_regionName']) && $geo_array['geoplugin_regionName'] != ""){
							echo "<div class='lead-geo-field'><span class='geo-label'>State:</span>" . $geo_array['geoplugin_regionName'] . "</div>";
							}
							if (isset($geo_array['geoplugin_areaCode']) && $geo_array['geoplugin_areaCode'] != ""){
							echo "<div class='lead-geo-field'><span class='geo-label'>Area Code:</span>" . $geo_array['geoplugin_areaCode'] . "</div>";
							}
							if (isset($geo_array['geoplugin_countryName']) && $geo_array['geoplugin_countryName'] != ""){
							echo "<div class='lead-geo-field'><span class='geo-label'>Country:</span>" . $geo_array['geoplugin_countryName'] . "</div>";
							}
							if (isset($geo_array['geoplugin_regionName']) && $geo_array['geoplugin_regionName'] != ""){
							echo "<div class='lead-geo-field'><span class='geo-label'>IP Address:</span>" . $ip_address . "</div>";
							}
							/*
							foreach ($geo_array as $key=>$val)
							{
								$key = str_replace('geoplugin_','',$key);
								echo "<tr class='lp-geo-data'>";
								echo "<td class='lp-geo-key'><em><small>$key</small></em></td>";
								echo "<td class='lp-geo-val'><em><small>$val</small></em></td>";
								echo "</tr>";
							} */
						}
						if (($latitude != 0) && ($longitude != 0)){ 
						echo '<a class="maps-link" href="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q='.$latitude.','.$longitude.'&z=12" target="_blank">View Map</a>';	
						echo '<div id="lead-google-map">
								<iframe width="278" height="276" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;q='.$latitude.','.$longitude.'&amp;aq=&amp;output=embed&amp;z=11"></iframe>
								</div>'; } else {
									echo "<h2>No Geo data collected</h2>";
								}
						echo '</div></div></div></div>';			
		
	}

 
/* Top Metabox */
add_action( 'edit_form_after_title', 'wp_leads_header_area' );
add_action( 'save_post', 'wp_leads_save_header_area' );

function wp_leads_header_area()
{
   global $post;
	
	$first_name = get_post_meta( $post->ID , 'wpleads_first_name', true );
	$last_name = get_post_meta( $post->ID , 'wpleads_last_name', true );
	$lead_status = 'wp_lead_status';
	
    if ( empty ( $post ) || 'wp-lead' !== get_post_type( $GLOBALS['post'] ) )
        return;

    if ( ! $content = get_post_meta( $post->ID , 'wpleads_first_name',true ) )
        $content = '';

    if ( ! $status_content = get_post_meta( $post->ID, $lead_status, TRUE ) )
        $status_content = '';
		
    echo "<div id='lead-top-area'>";
		echo "<div id='lead-header'><h1>".$first_name.' '.$last_name. "</h1></div>";

		$values = get_post_custom( $post->ID );  
		$selected = isset( $values['wp_lead_status'] ) ? esc_attr( $values['wp_lead_status'][0] ) : "";  
		?> 
		
		<div id='lead-status'>
			<label for="wp_lead_status">Lead Status:</label>  
			<select name="wp_lead_status" id="wp_lead_status">
				<option value="Read" <?php selected( $selected, 'Read' ); ?>>Read/Viewed</option>
				<option value="New Lead" <?php selected( $selected, 'New Lead' ); ?>>New Lead</option>
				<option value="Contacted" <?php selected( $selected, 'Contacted' ); ?>>Contacted</option>
				<option value="Active" <?php selected( $selected, 'Active' ); ?>>Active</option>   
				<option value="Lost" <?php selected( $selected, 'Lost' ); ?>>Disqualified/Lost</option> 
				<option value="Customer" <?php selected( $selected, 'Customer' ); ?>>Customer</option>
				<option value="Archive" <?php selected( $selected, 'Archive' ); ?>>Archive</option>    
				<!-- Action hook here for custom lead status addon -->
			</select>  
		</div>
		<span id="current-lead-status" style="display:none;"><?php echo get_post_meta( $post->ID, $lead_status, TRUE );?></span>
	</div>
    <?php 
}

function wp_leads_save_header_area( $post_id )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;

    $key = 'wp_lead_status';

    if ( isset ( $_POST[ $key ] ) )
        return update_post_meta( $post_id, $key, $_POST[ $key ] );

    delete_post_meta( $post_id, $key );
}

function wp_leads_grab_extra_data()
{
    
    // do not load on admin
    if (!is_admin() ) {
        return;
    }
    global $post;
    $email = get_post_meta($post->ID , 'wpleads_email_address', true );
    $api_key = get_option( 'wpl-main-extra-lead-data' , "");
   
    if($api_key === "" || empty($api_key)) {
    	$site_admin_url = get_option( 'site_url');
    	echo "<div class='lead-notice'>Please <a href='".$site_admin_url."/wp-admin/edit.php?post_type=wp-lead&page=wpleads_global_settings'>enter your full contact API key</a> for additional lead data</div>" ;
    	return;
    }

    if ((isset($post->post_type)&&$post->post_type=='wp-lead') && !empty($email)) {
        
        $social_data = get_post_meta($post->ID , 'social_data', true );
        $person_obj = $social_data;
        // check for social data
        if (empty($social_data)) {
            
            $args = array('sslverify' => false
            );
            
            $api_call = "https://api.fullcontact.com/v2/person.json?email=".urlencode($email)."&apiKey=$api_key";
            
            $response = wp_remote_get($api_call, $args );
            
            // error. bail.
            if (is_wp_error($response ) ) {
                return;
            }
            
            $status_code = $response['response']['code']; // Check for API limit

            if ($status_code === 200) {
                // if api still good. parse return values
                $person_obj = json_decode($response['body'], true);
                $image = (isset($person_obj['photos'][0]['url'])) ?$person_obj['photos'][0]['url'] : "";
                update_post_meta($post->ID, 'lead_main_image', $image );
                update_post_meta($post->ID, 'social_data', $person_obj );
              
            } elseif ($status_code === 404) {
            	echo "<div class='lead-notice'>No additional data found for this email address. It could be malformed (code: " . $status_code . ")</div>";
                $person_obj = array(); // return empty on failure

            } else {
                echo "<div class='lead-notice'>Error with Email Parse. Not found in social database (code: " . $status_code . ")</div>";
                $person_obj = array(); // return empty on failure
            }
            
        } 

        return $person_obj;
    }
}


			
function wp_lead_display_extra_data($values, $type) {				

		$person_obj = $values;
		//print_r($person_obj);
		$confidence_level = (isset($person_obj['likelihood'])) ? $person_obj['likelihood'] : "";
		
		$photos = (isset($person_obj['photos'])) ? $person_obj['photos'] : "No Photos";
		$fullname = (isset($person_obj['contactInfo']['fullName'])) ? $person_obj['contactInfo']['fullName'] : ""; 
		$websites = (isset($person_obj['contactInfo']['websites'])) ? $person_obj['contactInfo']['websites'] : "N/A";
		$chats = (isset($person_obj['contactInfo']['chats'])) ? $person_obj['contactInfo']['chats'] : "No"; 
		$social_profiles = (isset($person_obj['socialProfiles'])) ? $person_obj['socialProfiles'] : "No Profiles Found"; 
		$organizations = (isset($person_obj['organizations'])) ? $person_obj['organizations'] : "No Organizations Found";
		$demographics = (isset($person_obj['demographics'])) ? $person_obj['demographics'] : "N/A";
		$interested_in = (isset($person_obj['digitalFootprint']['topics'])) ? $person_obj['digitalFootprint']['topics'] : "N/A";

		$image = (isset($person_obj['photos'][0]['url'])) ?$person_obj['photos'][0]['url'] : "/wp-content/plugins/leads/images/gravatar_default_150.jpg";
		$klout_score = (isset($person_obj['digitalFootprint']['scores'][0]['value'])) ? $person_obj['digitalFootprint']['scores'][0]['value'] : "N/A"; 
		
		//echo "<img src='" . $image . "'><br>"; 
		//echo "<h2>Extra social Data <span class='confidence-level'>".$confidence_level."</span></h2>";
		//echo $fullname;

		// Get All Photos associated with the person
		if($type === 'photo' && isset($photos) && is_array($photos)) {
		
			foreach($photos as $photo)
		    {
		    	//print_r($photo);
		    	echo $photo['url'] . " from " . $photo['typeName'] . "<br>";
		    }  
			
		}
		// Get All Websites associated with the person
		elseif ($type === 'website' && isset($websites) && is_array($websites)) {
				echo "<div id='lead-websites'><h4>Websites</h4>";
				//print_r($websites);
				foreach($websites as $site)
			    {
			    	echo "<a href='". $site['url'] . "' target='_blank'>".$site['url']."</a><br>";
			    } 
			    echo "</div>";
		}
	    // Get All Social Media Account associated with the person
	    elseif ($type === 'social' && isset($social_profiles) && is_array($social_profiles)) {
		    	echo "<div id='lead-social-profiles'><h4>Social Media Profiles</h4>";
		    	//print_r($social_profiles);
				foreach($social_profiles as $profiles)
			    {
			    	$network = (isset($profiles['typeName'])) ? $profiles['typeName'] : "";
			    	$username = (isset($profiles['username'])) ? $profiles['username'] : "";
			    	($network == 'Twitter' ) ? $echo_val = "@" . $username : $echo_val = "";
			    	echo "<a href='". $profiles['url'] . "' target='_blank'>".$profiles['typeName']."</a> ". $echo_val ."<br>";
			    }
			    echo "</div>";
		}	
		// Get All Work Organizations associated with the person
	    elseif ($type === 'work' && isset($organizations) && is_array($organizations)) {
	    	echo "<div id='lead-work-history'>";

			foreach($organizations as $org)
		    {
		    	$title = (isset($org['title'])) ? $org['title'] : "";
		    	$org_name = (isset($org['name'])) ? $org['name'] : "";
		    	(isset($org['name'])) ? $at_org = "<span class='primary-work-org'>" . $org['name'] . "</span>" : $at_org = ""; // get primary org
		    	($org['isPrimary'] === true) ? $print = "<span id='primary-title'>" . $title ."</span> at " . $at_org : $print = "";
		    	($org['isPrimary'] === true) ? $hideclass = "work-primary" : $hideclass = "work-secondary";
		    	echo $print;
		    	echo "<span class='lead-work-label ".$hideclass."'>" . $title . " at ". $org_name ."</span>";
		    }
		    echo "<span id='show-work-history'>View past work</span></div>";
		}
		// Get All demo graphic info associated with the person
	    elseif ($type === 'demographics' && isset($demographics) && is_array($demographics)) {
	    	echo "<div id='lead-demographics'><h4>Demographics</h4>";
	    	$location = (isset($demographics['locationGeneral'])) ? $demographics['locationGeneral'] : "";
	    	$age = (isset($demographics['age'])) ? $demographics['age'] : "";
	    	$ageRange = (isset($demographics['ageRange'])) ? $demographics['ageRange'] : "";
	    	$gender = (isset($demographics['gender'])) ? $demographics['gender'] : "";
	    	echo $gender . " in " . $location;
	    	echo "</div>";
		}
		// Get All Topics associated with the person
		elseif ($type === 'topics' && isset($interested_in) && is_array($interested_in)) {
			echo "<div id='lead-topics'><h4>Interests</h4>";
			foreach($interested_in as $topic)
		    {
		    	echo "<span class='lead-topic-tag'>". $topic['value'] . "</span>";
		    }
		    echo "</div>";
		} 
				
}
/* ADD MAIN METABOX */
//Add select template meta box
add_action('add_meta_boxes', 'wplead_add_metabox_main');
function wplead_add_metabox_main() {
	global $post;
	
	$first_name = get_post_meta( $post->ID , 'wpleads_first_name',true );
	$last_name = get_post_meta( $post->ID , 'wpleads_last_name', true );
	add_meta_box(
		'wplead_metabox_main', // $id
		__( 'Lead Overview', 'wpleads' ),
		'wpleads_display_metabox_main', // $callback
		'wp-lead', // $page
		'normal', // $context
		'high'); // $priority 
}

// Render select template box
function wpleads_display_metabox_main() {
	//echo 1; exit;
	global $post; 
	
	global $wpdb;

	//define tabs
	$tabs[] = array('id'=>'wpleads_lead_tab_main','label'=>'Lead Information');
	$tabs[] = array('id'=>'wpleads_lead_tab_conversions','label'=>'Activity');
	$tabs[] = array('id'=>'wpleads_lead_tab_raw_form_data','label'=>'Logs');	

	$tabs = apply_filters('wpl_lead_tabs',$tabs);
	 
	//define open tab
	$active_tab = 'wpleads_lead_tab_main'; 
	if (isset($_REQUEST['open-tab']))
	{
		$active_tab = $_REQUEST['open-tab'];
	}

		
	//print jquery for tab switching
	wpl_manage_lead_js($tabs);

	$wpleads_user_fields = wp_leads_get_lead_fields();
	foreach ($wpleads_user_fields as $key=>$field)
	{
			$wpleads_user_fields[$key]['value'] = get_post_meta( $post->ID , $wpleads_user_fields[$key]['key'] ,true );		
	}

	// Use nonce for verification
	echo "<input type='hidden' name='wplead_custom_fields_nonce' value='".wp_create_nonce('lp-nonce')."' />";
	echo "<input type='hidden' name='open-tab' id='id-open-tab' value='{$active_tab}'>";
	?>
	<div class="metabox-holder split-test-ui">
		<div class="meta-box-sortables ui-sortable">
		<h2 id="lp-st-tabs" class="nav-tab-wrapper">	
			<?php
			foreach ($tabs as $key=>$array)
			{
				?>
				<a  id='tabs-<?php echo $array['id']; ?>' class="wpl-nav-tab nav-tab nav-tab-special<?php echo $active_tab == $array['id'] ? '-active' : '-inactive'; ?>"><?php echo $array['label']; ?></a> 
				<?php
			}
			?>
		</h2>		
		<div class="wpl-tab-display" id='wpleads_lead_tab_main'>
			<div id="wpleads_lead_tab_main_inner">
			<div id='toggle-lead-fields'><a class="preview button" href="#" id="show-hidden-fields">Show Hidden/Empty Fields</a></div>
			<?php

			$social_values = wp_leads_grab_extra_data(); // Get extra data on lead
			$email = get_post_meta( $post->ID , 'wpleads_email_address', true );
			$first_name = get_post_meta( $post->ID , 'wpleads_first_name',true );
			$last_name = get_post_meta( $post->ID , 'wpleads_last_name', true );
			$extra_image = get_post_meta( $post->ID , 'lead_main_image', true );
			$size = 150;
			$size_small = 36;
			$url = site_url();
			$default = WPL_URL . '/images/gravatar_default_150.jpg';

			$gravatar = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
			$gravatar2 = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size_small;
		
			$response = get_headers($gravatar);
			if ($response[0] === "HTTP/1.0 302 Found"){
    			$gravatar = $url . '/wp-content/plugins/leads/images/gravatar_default_150.jpg';
    			$gravatar2 = $url . '/wp-content/plugins/leads/images/gravatar_default_50.jpg';		
			} else {
				$gravatar = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
				$gravatar2 = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size_small;
			}

			?>
			<div id="lead_image">
				<div id="lead_image_container">
				<div id="lead_name_overlay"><?php echo $first_name . " " . $last_name;?></div>
					<?php						
						echo'<img src="'.$gravatar.'"  title="'.$first_name.' '.$last_name.'"></a>';
						wp_lead_display_extra_data($social_values, 'work'); // Display extra data work history
						wp_lead_display_extra_data($social_values, 'social'); // Display extra social	
												
					?>
				</div>	
			</div>
			<style type="text/css">.icon32-posts-wp-lead {background-image: url("<?php echo $gravatar2;?>") !important;}</style>
			<div id="leads-right-col">	
			<?php
			//print_r($wpleads_user_fields);exit;
			do_action('wpleads_before_main_fields'); // Custom Action for additional info above Lead list

			wpleads_render_setting($wpleads_user_fields);
			
			wp_lead_display_extra_data($social_values, 'website'); // Display websites

			wp_lead_display_extra_data($social_values, 'demographics'); // Display demographics
			
			wp_lead_display_extra_data($social_values, 'topics'); // Display extra topics

			$tags = wpl_tag_cloud(); // get content tags
				if (!empty($tags)){
				echo '<div id="lead-tag-cloud"><h4>Tag cloud of content consumed</h4>';
				foreach ($tags as $key => $value) {
					echo "<a href='#' rel='$value'>$key</a>";
				}
				echo "</div>";
			}

			//wp_lead_display_extra_data($values, 'photo'); // Display extra photos
			echo "<div id='wpl-after-main-fields'>";
			do_action('wpleads_after_main_fields'); // Custom Action for additional info above Lead list
			echo "</div>";
			
			?>
			</div><!-- end #leads-right-col div-->

		</div><!-- end wpleads_metabox_main_inner -->
		</div><!-- end wpleads_metabox_main AKA Tab 1-->
		<div class="wpl-tab-display" id="wpleads_lead_tab_conversions" style="display: <?php if ($active_tab == 'wpleads_lead_tab_conversions') { echo 'block;'; } else { echo 'none;'; } ?>">	
			<div id="conversions-data-display">
				<?php //define activity toggles. Filterable
					$nav_items[] = array('id'=>'lead-conversions','label'=>'Conversions');
					$nav_items[] = array('id'=>'lead-page-views','label'=>'Page Views');
					$nav_items = apply_filters('wpl_lead_activity_tabs',$nav_items); ?>

			<div class="nav-container">
				<nav>
			      <ul>
			        <li class="active"><a href="#all" class="lead-activity-show-all">All</a></li>
			        <?php
			        	// Print toggles
						foreach ($nav_items as $key=>$array)
						{
							?>
							<li><a href='#<?php echo $array['id']; ?>' class="lead-activity-toggle"><?php echo $array['label']; ?></a></li>
							<?php
						}
					?>
			      </ul>
			    </nav>
			</div>
			<ul class="event-order-list" data-change-sort='#all-lead-history'>
			Sort by:		
		    <li id="newest-event" class='lead-sort-active'>Most Recent</li> |
		    <li id="oldest-event">Oldest</li>
			   <!-- <li id="highest">Highest Rated</li>
			    <li id="lowest">Lowest Rated</li> -->
			</ul>
			<div id="all-lead-history"><ol></ol></div>
			<div id="lead-conversions" class='lead-activity'>
				<h2>Landing Page Conversions</h2>
			
			<?php $conversions = get_post_meta($post->ID,'wpleads_conversion_data', true);
				  $conversions_array = json_decode($conversions, true);
           	//print_r($conversions);
            // Sort Array by date
			 function leads_sort_array_datetime($a,$b){
			        return strtotime($a['datetime'])<strtotime($b['datetime'])?1:-1;
			};
       		 uasort($conversions_array,'leads_sort_array_datetime'); // Date sort  
       		$conversion_count = count($conversions_array);
          	if ($conversions) 
          	{
 				$i = $conversion_count;
				foreach ($conversions_array as $key => $value)
				{	
					//print_r($value);
				
						$converted_page_id  = $value['id'];
						$converted_page_permalink   = get_permalink($converted_page_id);
						$converted_page_title = get_the_title($converted_page_id);
						
						if (array_key_exists('datetime', $value))
						{
							$converted_page_time = $value['datetime'];
						}
						else
						{
							$converted_page_time = $wordpress_date_time;
						}
							
						$conversion_date_raw = new DateTime($converted_page_time);
						$date_of_conv = $conversion_date_raw->format('F jS, Y \a\t g:ia (l)');
						$conversion_clean_date = $conversion_date_raw->format('Y-m-d H:i:s');
						
						// Display Data
						echo '<div class="lead-timeline recent-conversion-item landing-page-conversion" data-date="'.$conversion_clean_date.'">
								<a class="lead-timeline-img" href="#non">
									<img src="/wp-content/plugins/leads/images/page-view.png" alt="" width="50" height="50" />
								</a>
									
								<div class="lead-timeline-body">
									<div class="lead-event-text">
									  <p><span class="lead-item-num">'.$i.'.</span><span class="lead-helper-text">Converted on landing page/form: </span><a href="'.$converted_page_permalink.'" id="lead-session-'.$i.'" rel="'.$i.'" target="_blank">'.$converted_page_title.'</a><span class="conversion-date">'.$date_of_conv.'</span> <!--<a rel="'.$i.'" href="#view-session-"'.$i.'">(view visit path)</a>--></p>
									</div>
								</div>
							</div>';
						$i--;
					
				} 
			} else {
				echo "<span id='wpl-message-none'>No conversions found!</span>";
			}
		
			?>
			
			</div> <!-- end lead conversions -->
			<div id="lead-page-views" class='lead-activity'>
				<h2>Page Views</h2>
			 <?php 
			
			$page_views = get_post_meta($post->ID,'page_views', true);
			
            $page_view_array = json_decode($page_views, true);
           
            // Sort Array by date
			 function wp_leads_sort_array_datetime($a,$b){
			        return strtotime($a['dates'])<strtotime($b['dates'])?1:-1;
			};
       		// uasort($page_view_array,'wp_leads_sort_array_datetime'); // Date sort  
          	if ($page_views) {
          		$main_count = 0;
          		foreach($page_view_array as $key=>$val)
                {
                	$main_count += count($page_view_array[$key]);
                }
               
             
          	 $count = $main_count;
          	 foreach($page_view_array as $key=>$val)
                {
                	
                    $id = $key;
       	
                    foreach ($val as $new_key => $date) {
                  		
                  		if (strpos($id,'cat_') !== false) {
                    	$cat_id = str_replace("cat_", "", $id);
                    	$title = get_cat_name($cat_id) . " Category Page";
                    	$tag_names = '';
                    	$page_url = get_category_link( $cat_id );

                    	} elseif (strpos($id,'tag_') !== false) {
                    	$tag_id = str_replace("tag_", "", $id);
                    	$tag = get_tag( $tag_id );
                    	$title = $tag->name . " - Tag Page";
                    	$tag_names = '';
                    	$page_url = get_tag_link($tag_id);
                    	} else {
                    	$title = get_the_title($id);
                    	$tag_names = wp_get_post_tags( $id, array( 'fields' => 'names' ) );
                    	$page_url = get_permalink( $id );
                   		}
                  
                    	$this_post_type = get_post_type($id);
                    	$date_raw = new DateTime($date);
                    	$date_of_conversion = $date_raw->format('F jS, Y \a\t g:ia (l)');
                    	$clean_date = $date_raw->format('Y-m-d H:i:s');
                   		// Display Data
                   		 echo '<div class="lead-timeline recent-conversion-item page-view-item '.$this_post_type.'" title="'.$page_url.'"  data-date="'.$clean_date.'">
								<a class="lead-timeline-img page-views" href="#non">
									
								</a>
									
								<div class="lead-timeline-body">
									<div class="lead-event-text">
									  <p><span class="lead-item-num">'.$count.'.</span><span class="lead-helper-text">Viewed page: </span><a href="'.$page_url.'" id="lead-session" rel="" target="_blank">'.$title.'</a><span class="conversion-date">'.$date_of_conversion.'</span></p>
									</div>
								</div>
							</div>';
                    	$count--;
                    }

                    //$type_of_page = $page_view_array[$key]['page_type'];

                 }


            } else {
                echo "<span id='wpl-message-none'>No Page View History Found</span>";
            }   

            ?>
			</div>
			<?php do_action('wpleads_after_activity_log'); // Custom Action for additional info at bottom of activity log?>
			</div> <!-- end #activites AKA Tab 2 -->			
		</div>

		<div class="wpl-tab-display" id="wpleads_lead_tab_raw_form_data" style="display:  <?php if ($active_tab == 'wpleads_lead_tab_raw_form_data') { echo 'block;'; } else { echo 'none;'; } ?>;">
			<div id="raw-data-display">
			<div class="nav-container">
				<nav>
			      <ul>
			        <li class="active"><a href="index.html">All</a></li>
			        <li><a href="index.html">Form Data</a></li>
			        <li><a href="index.html">Page Data</a></li>
			        <li><a href="index.html">Event Data</a></li>
			      </ul>
			    </nav>
			</div>
			
			<?php

			// Get Raw form Data
			$raw_data = get_post_meta($post->ID,'wpl-lead-raw-post-data', true);
			
			if ($raw_data)
			{
				$raw_data = json_decode($raw_data, true);
				echo "<h2>Form Inputs with Values</h2>";
				echo "<span id='click-to-map'></span>";
				 echo "<div id='wpl-raw-form-data-table'>";
				foreach($raw_data as  $key=>$value)
				{
					?>
					<div class="wpl-raw-data-tr">
						<span class="wpl-raw-data-td-label">
							<?php echo "Input name: <span class='lead-key-normal'>". $key . "</span> &rarr; values:"; ?>
						</span>
						<span class="wpl-raw-data-td-value">
							<?php
							if (is_array($value))
							{
								$value = array_filter($value);
								$value = array_unique($value);
								$num_loop = 1;
								foreach($value as $k=>$v)
								{
									echo "<span class='".$key. "-". $num_loop." possible-map-value'>".$v."</span>";
									$num_loop++;
								}
							}
							else
							{
								echo "<span class='".$key."-1 possible-map-value'>".$value."</span>";
							}
							?>
						</span>
						<span class="map-raw-field"><span class="map-this-text">Map this field to lead</span><span style="display:none;" class='lead_map_select'><select name="NOA" class="field_map_select"></select></span><span class="apply-map button button-primary" style="display:none;">Apply</span></span>
					</div>
				<?php 

				}
				echo "<div id='raw-array'>";
				echo "<h2>Raw Form Data Array</h2>";
				echo "<pre>";
				print_r($raw_data);
				echo "</pre>";
				echo "</div>";
				echo "</div>"; 
			}
			else
			{					
				echo "<span id='wpl-message-none'>No raw data found!</span>";			
			}
		
			?>			
			
			</div> <!-- end #raw-data-display -->			
		</div>
		
		<?php
		do_action('wpl_print_lead_tab_sections');
		?>
		
		</div><!-- end .meta-box-sortables -->
	</div><!-- end .metabox-holder -->
	<?php
}
function wpl_tag_cloud() {
	global $post;
	$page_views = get_post_meta($post->ID,'page_views', true);
    $page_view_array = json_decode($page_views, true);
    if($page_views){ 		
     	// Collect all viewed page IDs	
          foreach($page_view_array as $key=>$val)
                {
                    $id = $key;
                    $ids[] = $key; 
                }
        // Get Tags from all pages viewed
     
           foreach($ids as $key=>$val)
                {
                //echo $val;
                $array = wp_get_post_tags( $val, array( 'fields' => 'names' ) );
	                if(!empty($array)){
	                	$tag_names[] = wp_get_post_tags( $val, array( 'fields' => 'names' ) );	
	                }
              
                } 
        // Merge and count 
        $final_tags = array();
        if(!empty($tag_names)){
           	foreach($tag_names as $array){   

			    foreach($array as $key=>$value){

			        $final_tags[] = $value;
			    }
			}
		}
        $return_tags = array_count_values($final_tags);
    } else {
    	$return_tags = array(); // empty
    }
 
  return $return_tags; // return tag array
           
}



function wpl_manage_lead_js($tabs)
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
		jQuery('.wpl-nav-tab').live('click', function() {
		
			var this_id = this.id.replace('tabs-','');
			//alert(this_id);
			jQuery('.wpl-tab-display').css('display','none');
			jQuery('#'+this_id).css('display','block');
			jQuery('.wpl-nav-tab').removeClass('nav-tab-special-active');
			jQuery('.wpl-nav-tab').addClass('nav-tab-special-inactive');
			jQuery('#tabs-'+this_id).addClass('nav-tab-special-active');						
			jQuery('#id-open-tab').val(this_id);
		});
	});			
	</script>
	<?php
}

add_action('save_post', 'wpleads_save_user_fields');
function wpleads_save_user_fields($post_id) {
	
	global $post;
	
	if (!isset($post)||isset($_POST['split_test']))
		return;
		
	if ($post->post_type=='revision' ||  'trash' == get_post_status( $post_id ))
	{
		return;
	}
	if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )||( isset($_POST['post_type']) && $_POST['post_type']=='revision' ))
	{
		return;
	}
		
	if ($post->post_type=='wp-lead')
	{
		$wpleads_user_fields = wp_leads_get_lead_fields();
		foreach ($wpleads_user_fields as $key=>$field)
		{	

			$old = get_post_meta($post_id, $field['key'], true);				
			if (isset($_POST[$field['key']])) 
			{
				$new = $_POST[$field['key']];	
			
				if (is_array($new))
				{
					//echo $field['name'];exit;
					array_filter($new);
					$new = implode(';',$new);
					update_post_meta($post_id, $field['key'], $new);
				}
				else if (isset($new) && $new != $old ) {
					update_post_meta($post_id, $field['key'], $new);
				}
				else if ('' == $new && $old) {
					//echo "here";exit;
					delete_post_meta($post_id, $field['key'], $old);
				}
			}
		}	

		
	}
}


/* ADD CONVERSIONS METABOX */
// 
// Currently off for debuging
// Need to revamp this. Mysql custom table isn't cutting it
// 
add_action('add_meta_boxes', 'wplead_add_conversion_path');
function wplead_add_conversion_path() {
	global $post;
	
	add_meta_box(
		'wplead_metabox_conversion', // $id
		__( 'Conversion Paths' , 'wplead_metabox_conversion' ),
		'wpleads_display_conversion_path', // $callback
		'wp-lead', // $page
		'normal', // $context
		'high'); // $priority 
}

// Render Conversion Paths
function wpleads_display_conversion_path() {
	global $post; 
	global $wpdb; 
	
	$query = 'SELECT * FROM '.$wpdb->prefix.'lead_tracking WHERE lead_id = "'.$post->ID.'" AND nature="conversion" ORDER BY id DESC';
	$result = mysql_query($query);
	print_r($result);
	if (!$result){ echo $sql; echo mysql_error(); exit; }

	$num_conversion = mysql_num_rows($result);
	if (empty($num_conversion)) {
		echo "<h2 style='background:transparent;'>No Conversions Tracked. This person could have javascript disabled or you have a javascript error on your site.</h2>";
	}
	$array_page_view_total = array();
	$session_count=1;
	while ($array = mysql_fetch_array($result))
	{
		//echo "here";
		$session_count++;
		$row_id = $array['id'];
		$old = null;
		$date = date_create($array['date']);
		$data = json_decode( $array['data'] , true);
	
		$wordpress_timezone = get_option('gmt_offset');
		$date1 = new DateTime($array['date']);
		$final_date1 = $date1->format('Y-m-d G:i:s');
		$date2 = new DateTime(date('Y-m-d G:i:s'));
		$final_date2 = $date2->format('Y-m-d G:i:s');
		$date_obj = leads_time_diff($final_date1, $final_date2);
		$years = $date_obj['years'];
		$months = $date_obj['months'];
		$days = $date_obj['days'];
		$hours = $date_obj['hours'] + $wordpress_timezone;
		$minutes = $date_obj['minutes'];

		$year_text = $date_obj['y-text'];
		$month_text = $date_obj['m-text'];
		$day_text = $date_obj['d-text']; 
		$hours_text = $date_obj['h-text'];
		$minute_text = $date_obj['mm-text']; 
		//print_r($data);exit;
	 
		$i = 0;
		$sessions = array();
		foreach ($data as $key => $value)
		{	
			//print_r($value);
			if (in_array($value['session_id'],$sessions))
				continue; 
			
			if (array_key_exists('converted_page', $value))
			{
			
				echo '<a class="session-anchor" id="view-session-'.$key.'""></a><div id="conversion-tracking" class="wpleads-conversion-tracking-table" summary="Conversion Tracking">
				
				<div class="conversion-tracking-header">
						<h2><strong>Visit '.$num_conversion.'</strong> on <span class="shown_date">'.date_format($date, 'F jS, Y \a\t g:ia (l)').'</span><span class="toggle-conversion-list">-</span></h2> <span class="hidden_date date_'.$num_conversion.'">'.date_format($date, 'F jS, Y \a\t g:ia').'</span>
				</div>';
				echo '<div class="conversion-session-view session_id_'.$num_conversion.'">
					<div class="session-stats">
					<span class="session-stats-header">Session Stats</span>
					
					<span id="session-time-since"><span class="touchpoint-year"><span class="touchpoint-value">' . $years . '</span> '.$year_text.' </span><span class="touchpoint-month"><span class="touchpoint-value">' . $months.'</span> '.$month_text.' </span><span class="touchpoint-day"><span class="touchpoint-value">'.$days.'</span> '.$day_text.' </span><span class="touchpoint-hour"><span class="touchpoint-value">'.$hours.'</span> '.$hours_text.' </span><span class="touchpoint-minute"><span class="touchpoint-value">'.$minutes.'</span> '.$minute_text.'</span> Ago</span>
						<span class="session-head">Event Freshness</span>
						<div id="session-pageviews">
						<span id="pages-view-in-session">10</span>
						<span class="session-head page-view-sess">Pages Viewed in Session</span>
						</div>
					</div>';
					$num_conversion--;	
				
				//print_r($data);exit;
				echo "<div class='leads-visit-list'>";
				
				$pageviews = $value['pageviews'];
				$converted_page_id = $value['converted_page']['page_id'];
				$converted_page_name = get_the_title($converted_page_id);
				$converted_page_permalink = get_permalink($converted_page_id);
				//$tag_ids = wp_get_post_tags( $converted_page_id, array( 'fields' => 'ids' ) );
	

				//print_r($pageviews);exit;
				foreach ($pageviews as $k => $pageview)
				{
					if ($old&&$old==$pageview['current_page'])
					{
						continue;
					}
					else
					{
						$old = $pageview['current_page'];
					}
					
					$i++;		
					
					if ($k==0)
					{
						if ($pageview['original_referrer'])
						{
							?>
							<div class="lp-page-view-item">
								
									<span class='marker'><?php echo $i; ?></span> <a href='<?php echo $pageview['original_referrer']; ?>' title='<?php echo $pageview['original_referrer']; ?>' target='_blank'><?php echo $pageview['original_referrer']; ?></a>
												
							</div>					
							<?php
							$i++;
						}
						?>
						<div class="lp-page-view-item">
							
								<span class='marker'><?php echo $i; ?></span> <a href='<?php echo $pageview['current_page'] ?>' title='<?php echo $pageview['current_page'] ?>' target='_blank'><?php echo $pageview['current_page']; ?></a>
											
						</div>
						<?php
					}
					else
					{	
						// Get Tags from pages
						$this_page = $pageview['current_page'];
						$this_page = preg_replace('/\?.*/', '', $this_page);
						//echo $this_page;
						$page_id = wpl_url_to_postid($this_page);
						$tags = wp_get_post_tags( $page_id, array( 'fields' => 'names' ) );
						if(!empty($tags)){
							foreach ($tags as $tag) {
								echo $tag . ", ";
							}
						}
						// End get tags from pages
					?>
					<div class="lp-page-view-item">
						
							<span class='marker'><?php echo $i; ?></span> <a href='<?php echo $this_page; ?>' title='<?php echo $this_page; ?>' target='_blank'><?php echo $this_page; ?></a>
										
					</div>
					<?php	
					}
				}
				
				?>
				<div class="lp-page-conversion-item">
		
					<div id='end-conversion-point'>
					<span>Converted on:</span> <a href='<?php echo $converted_page_permalink;?>' target='_blank'><?php echo $converted_page_name; ?></a></span>
					</div>
				
				</div>
			</div><!-- .leads-visit-list end -->
			<?php				
			}
			$sessions[] = $value['session_id'];
			
		}
		?>		
		
		</div><!-- end .conversion-session-view -->
		</div><!-- end #conversion-tracking -->
		
		<?php
	}
	//update_option($post->ID,'wpl-lead-conversions',implode(',',$conversions));
	// echo count($array_page_view_total);
}

 ?>