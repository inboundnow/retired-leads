<?php
/* INCLUDE FILE WHERE MAIN USERFIELDS ARE DEFINED */
include_once('module.userfields.php');

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
    if ( isset ($post) && $post->post_type == 'wp-lead' )
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
	__( "Lead Stats", WPL_TEXT_DOMAIN ),
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
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

	$time_diff['years'] = $years;
	$time_diff['y-text'] = ($years > 1) ? __('Years' , WPL_TEXT_DOMAIN) : __('Year' , WPL_TEXT_DOMAIN);
	$time_diff['months'] = $months;
	$time_diff['m-text'] = ($months > 1) ? __('Months' , WPL_TEXT_DOMAIN) : __('Month' , WPL_TEXT_DOMAIN);
	$time_diff['days'] = $days;
	$time_diff['d-text'] = ($days > 1) ? __('Days' , WPL_TEXT_DOMAIN) : __('Day' , WPL_TEXT_DOMAIN);
	$time_diff['hours'] = $hours;
	$time_diff['h-text'] = ($hours > 1) ? __('Hours' , WPL_TEXT_DOMAIN) : __('Hour' , WPL_TEXT_DOMAIN);
	$time_diff['minutes'] = $minutes;
	$time_diff['mm-text'] = ($minutes > 1) ? __('Minutes' , WPL_TEXT_DOMAIN) : __('Minute' , WPL_TEXT_DOMAIN);
	$time_diff['seconds'] = $seconds;
	$time_diff['sec-text'] = ($seconds > 1) ? __('Seconds' , WPL_TEXT_DOMAIN) : __('Second' , WPL_TEXT_DOMAIN);

	return $time_diff;
}

function wplead_quick_stats_metabox() {
	global $post;
	global $wpdb;

	//define last touch point
	$form_data = get_post_meta($post->ID,'FormData', true);
	//print_r($form_data);

	$last_conversion = get_post_meta($post->ID,'wpleads_conversion_data', true);
	$last_conversion = json_decode($last_conversion, true);
	//print_r($last_conversion);
		if (is_array($last_conversion)){
		$count_conversions = count($last_conversion);
		} else {
		$count_conversions = get_post_meta($post->ID,'wpleads_conversion_count', true);
		}
	$the_date = $last_conversion[$count_conversions]['datetime']; // actual

	$email = get_post_meta( $post->ID , 'wpleads_email_address', true );
	$first_name = get_post_meta( $post->ID , 'wpleads_first_name',true );
	$last_name = get_post_meta( $post->ID , 'wpleads_last_name', true );

	$page_views = get_post_meta($post->ID,'page_views', true);
    $page_view_array = json_decode($page_views, true);
    $main_count = 0;
    $page_view_count = 0;
	    if (is_array($page_view_array)){
	    	foreach($page_view_array as $key=>$val) {
	         $page_view_count += count($page_view_array[$key]);
	        }
	    update_post_meta($post->ID,'wpleads_page_view_count', $page_view_count);
	   	} else {
	      	$page_view_count = get_post_meta($post->ID,'wpleads_page_view_count', true);
	    }

	?>
	<div>
		<div class="inside" style='margin-left:-8px;text-align:center;'>
			<div id="quick-stats-box">
			<?php do_action('wpleads_before_quickstats', $post);?>
			<div id="page_view_total"><?php _e('Total Page Views ' , WPL_TEXT_DOMAIN);?><span id="p-view-total"><?php echo $page_view_count; ?></span>
			</div>

			<div id="conversion_count_total"><?php _e('# of Conversions ' , WPL_TEXT_DOMAIN);?><span id="conversion-total"><?php echo $count_conversions; ?></span>
			</div>

		<?php if (!empty($the_date)) {
		$time = current_time( 'timestamp', 0 ); // Current wordpress time from settings
		$wordpress_date_time = date("Y-m-d G:i:s", $time);

		$today = new DateTime($wordpress_date_time);
		$today = $today->format('Y-m-d G:i:s');
		$date_obj = leads_time_diff($the_date, $today);
		$wordpress_timezone = get_option('gmt_offset');
		$years = $date_obj['years'];
		$months = $date_obj['months'];
		$days = $date_obj['days'];
		$hours = $date_obj['hours'];
		$minutes = $date_obj['minutes'];
		$year_text = $date_obj['y-text'];
		$month_text = $date_obj['m-text'];
		$day_text = $date_obj['d-text'];
		$hours_text = $date_obj['h-text'];
		$minute_text = $date_obj['mm-text']; ?>

		<?php // echo "<br>c date:".$the_date . "<br>wpdate:" . $wordpress_date_time; ?>
			<div id="last_touch_point"><?php _e('Time Since Last Conversion' , WPL_TEXT_DOMAIN);?>

				<span id="touch-point">

					<?php

					echo "<span class='touchpoint-year'><span class='touchpoint-value'>" . $years . "</span> ".$year_text." </span><span class='touchpoint-month'><span class='touchpoint-value'>" . $months."</span> ".$month_text." </span><span class='touchpoint-day'><span class='touchpoint-value'>".$days."</span> ".$day_text." </span><span class='touchpoint-hour'><span class='touchpoint-value'>".$hours."</span> ".$hours_text." </span><span class='touchpoint-minute'><span class='touchpoint-value'>".$minutes."</span> ".$minute_text."</span>";
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

	$geo_result = wp_remote_get('http://www.geoplugin.net/php.gp?ip='.$ip_address);
	$geo_result_body = $geo_result['body'];

	$geo_array = unserialize($geo_result_body);

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
					echo "<div class='lead-geo-field'><span class='geo-label'>".__('City:' , WPL_TEXT_DOMAIN)."</span>" . $geo_array['geoplugin_city'] . "</div>"; }
					if (isset($geo_array['geoplugin_regionName']) && $geo_array['geoplugin_regionName'] != ""){
					echo "<div class='lead-geo-field'><span class='geo-label'>".__('State:' , WPL_TEXT_DOMAIN)."</span>" . $geo_array['geoplugin_regionName'] . "</div>";
					}
					if (isset($geo_array['geoplugin_areaCode']) && $geo_array['geoplugin_areaCode'] != ""){
					echo "<div class='lead-geo-field'><span class='geo-label'>".__('Area Code:' , WPL_TEXT_DOMAIN)."</span>" . $geo_array['geoplugin_areaCode'] . "</div>";
					}
					if (isset($geo_array['geoplugin_countryName']) && $geo_array['geoplugin_countryName'] != ""){
					echo "<div class='lead-geo-field'><span class='geo-label'>".__('Country:' , WPL_TEXT_DOMAIN)."</span>" . $geo_array['geoplugin_countryName'] . "</div>";
					}
					if (isset($geo_array['geoplugin_regionName']) && $geo_array['geoplugin_regionName'] != ""){
					echo "<div class='lead-geo-field'><span class='geo-label'>".__('IP Address:' , WPL_TEXT_DOMAIN)."</span>" . $ip_address . "</div>";
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
				if (($latitude != 0) && ($longitude != 0))
				{
					echo '<a class="maps-link" href="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q='.$latitude.','.$longitude.'&z=12" target="_blank">'.__('View Map:' , WPL_TEXT_DOMAIN).'</a>';
					echo '<div id="lead-google-map">
							<iframe width="278" height="276" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;q='.$latitude.','.$longitude.'&amp;aq=&amp;output=embed&amp;z=11"></iframe>
							</div>';
				}
				else
				{
					echo "<h2>".__('No Geo data collected' , WPL_TEXT_DOMAIN)."</h2>";
				}
		?>
				</div>
			</div>
		</div>
	</div>
	<?php
}


/* Top Metabox */
add_action( 'edit_form_after_title', 'wp_leads_header_area' );
function wp_leads_header_area() {
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
		<!-- REWRITE FOR FILTERS -->
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


add_action( 'save_post', 'wp_leads_save_header_area' );
function wp_leads_save_header_area( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;

    $key = 'wp_lead_status';

    if ( isset ( $_POST[ $key ] ) )
        return update_post_meta( $post_id, $key, $_POST[ $key ] );

    delete_post_meta( $post_id, $key );
}

function wp_leads_grab_extra_data() {

    // do not load on admin
    if (!is_admin() ) {
        return;
    }
    global $post;
    $email = get_post_meta($post->ID , 'wpleads_email_address', true );
    $api_key = get_option( 'wpl-main-extra-lead-data' , "");

    if($api_key === "" || empty($api_key)) {
    	echo "<div class='lead-notice'>Please <a href='".esc_url( admin_url( add_query_arg( array( 'post_type' => 'wp-lead', 'page' => 'wpleads_global_settings' ), 'edit.php' ) ) )."'>enter your Full Contact API key</a> for additional lead data. <a href='http://www.inboundnow.com/collecting-advanced-lead-intelligence-wordpress-free/' target='_blank'>Read more</a></div>" ;
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
            	//echo "<div class='lead-notice'>No additional data found for this email address. It could be malformed (code: " . $status_code . ")</div>";
                $person_obj = array(); // return empty on failure

            } else {
                //echo "<div class='lead-notice'>Error with Email Parse. Not found in social database (code: " . $status_code . ")</div>";
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
			if ( !$wpleads_user_fields[$key]['value'] && isset($wpleads_user_fields[$key]['default']) )
				$wpleads_user_fields[$key]['value'] = $wpleads_user_fields[$key]['default'];
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

			if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
			    $gravatar = $default;
			   	$gravatar2 = WPL_URL . '/images/gravatar_default_32-2x.png';
			}
			// If social picture exists use it
			if(preg_match("/gravatar_default_/", $gravatar) && $extra_image != ""){
				$gravatar = $extra_image;
				$gravatar2 = $extra_image;
			}
			?>
			<div id="lead_image">
				<div id="lead_image_container">
				<?php if ($first_name != "" && $last_name != ""){ ?>
				<div id="lead_name_overlay"><?php echo $first_name . " " . $last_name;?>
				</div>
				<?php } ?>
					<?php
						if(preg_match("/gravatar_default_/", $gravatar) && $extra_image != ""){
							$gravatar = $extra_image;
						}
						echo'<img src="'.$gravatar.'" width="150" id="lead-main-image" title="'.$first_name.' '.$last_name.'"></a>';
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

			<?php
			$conversions = get_post_meta($post->ID,'wpleads_conversion_data', true);
			$conversions_array = json_decode($conversions, true);
           	//print_r($conversions);
            // Sort Array by date
			 function leads_sort_array_datetime($a,$b){
			        return strtotime($a['datetime'])<strtotime($b['datetime'])?1:-1;
			};

			if (is_array($conversions_array))
          	{
				uasort($conversions_array,'leads_sort_array_datetime'); // Date sort
				$conversion_count = count($conversions_array);

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

		 	   	if ($page_view_array) {

			 	    $new_array = array();
			 	    $loop = 0;
			 		// Combine and loop through all page view objects
			 		 foreach($page_view_array as $key=>$val)
			 	      {
			 	      	foreach($page_view_array[$key] as $test){
			 	      			$new_array[$loop]['page'] = $key;
			 	      			$new_array[$loop]['date'] = $test;
			 	      	      	$loop++;
			 	      	}

			 	      }
			 	    // Merge conversion and page view json objects
			 	   // print_r($new_array);
			 	    //$timeout = 1800; // thirty minutes in seconds
			 	    //$test = abs(strtotime(Last Time) - strtotime(NEW TIME));
			 	   	/* $test = abs(strtotime("2013-11-19 12:58:12") - strtotime("2013-11-20 4:57:02 UTC")); */
			 	  	uasort($new_array,'c_table_leads_sort_array_datetime_reverse'); // Date sort

			 	  	//print_r($new_array);
			 	  	$new_key_array = array();
			 	  	$num = 0;
			 	  	foreach ( $new_array as $key => $val ) {
			 			$new_key_array[ $num ] = $val;
			 			$num++;
			 	  	}
			 	  	//print_r($new_key_array);
			 	  	$new_loop = 1;
			 	  	$total_session_count = 0;
			 	    foreach ($new_key_array as $key => $value) {

			 	    	$last_item = $key - 1;

			 	    	$next_item = $key + 1;
			 	    	$conversion = (isset($new_key_array[$key]['conversion'])) ? 'lead-conversion-mark' : '';
			 	    	$conversion_text = (isset($new_key_array[$key]['conversion'])) ? '<span class="conv-text">(Conversion Event)</span>' : '';
			 	    	$close_div = ($total_session_count != 0) ? '</div></div>' : '';
			 	    	//echo $new_key_array[$new_loop]['date'];
			 	    	if(isset($new_key_array[$last_item]['date'])){
			 	    		$timeout = abs(strtotime($new_key_array[$last_item]['date']) - strtotime($new_key_array[$key]['date']));
			 	    	} else{
			 	    		$timeout = 3601;
			 	    	}

			 	    	$date =  date_create($new_key_array[$key]['date']);
			 	    	$page_id = $new_key_array[$key]['page'];
			 	    	$this_post_type = '';
			 	    		if (strpos($page_id,'cat_') !== false) {
			 	        	  	$cat_id = str_replace("cat_", "", $page_id);
			 	        	  	$page_name = get_cat_name($cat_id) . " Category Page";
			 	        	  	$tag_names = '';
			 	        	  	$page_permalink = get_category_link( $cat_id );
			 	    	  	} elseif (strpos($page_id,'tag_') !== false) {
			 	        	  	$tag_id = str_replace("tag_", "", $page_id);
			 	        	  	$tag = get_tag( $tag_id );
			 	        	  	$page_name = $tag->name . " - Tag Page";
			 	        	  	$tag_names = '';
			 	        	  	$page_permalink = get_tag_link($tag_id);
			 	    	  	} else {
			 	    		$page_title = get_the_title($page_id);
			 	    		$page_name = ($page_id != 0) ? $page_title : 'N/A';
			 	    		$page_permalink = get_permalink($page_id);
			 	    		$this_post_type = get_post_type($page_id);
			 	    	}

			 	    	$timeon_page = $timeout / 60;
			 	    	$date_print = date_create($new_key_array[$key]['date']);
			 	    	//$last_date = date_create($new_key_array[$last_item]['date']);
			 	    	//$date_raw = new DateTime($new_key_array[$key]['date']);
			 	    	//

			 	    	//echo "<br>".$timeout."<-timeout on key " . $new_loop . " " . $new_key_array[$key]['date'] . ' on page: ' . $new_key_array[$key]['page'];
			 	    	//$second_diff = abs(date_format($last_date, 's') - date_format($date_print, 's'));
			 	    	if(isset($new_key_array[$last_item]['date'])){
			 	    	$second_diff = leads_time_diff($new_key_array[$last_item]['date'], $new_key_array[$key]['date']);
			 	    	} else {
			 	    	$second_diff['minutes'] = 0;
			 	    	$second_diff['seconds'] = 0;
			 	    	}
			 	    	//print_r($second_diff);
			 	    	//$second_diff = date('i:s',$second_diff);
			 	    	$minute = ($second_diff['minutes'] != 0) ? "<strong>" . $second_diff['minutes'] . "</strong> " : '';
			 	    	$minute_text = ($second_diff['minutes'] != 0) ? $second_diff['mm-text'] . " " : '';
			 	    	$second = ($second_diff['seconds'] != 0) ? "<strong>" . $second_diff['seconds'] . "</strong> " : 'Less than 1 second';
			 	    	$second_text = ($second_diff['seconds'] != 0) ? $second_diff['sec-text'] . " " : '';


			 	    	$clean_date = date_format($date_print, 'Y-m-d H:i:s');

                   		// Display Data
                   		 echo '<div class="lead-timeline recent-conversion-item page-view-item '.$this_post_type.'" title="'.$page_permalink.'"  data-date="'.$clean_date.'">
								<a class="lead-timeline-img page-views" href="#non">

								</a>

								<div class="lead-timeline-body">
									<div class="lead-event-text">
									  <p><span class="lead-item-num">'.$new_loop.'.</span><span class="lead-helper-text">Viewed page: </span><a href="'.$page_permalink.'" id="lead-session" rel="" target="_blank">'.$page_title .'</a><span class="conversion-date">'.date_format($date_print, 'F jS, Y \a\t g:i:s a').'</span></p>
									</div>
								</div>
							</div>';

			 	    	$new_loop++;

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
			$raw_data = get_post_meta($post->ID,'wpleads_raw_post_data', true);

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


function wp_leads_sort_fields($a,$b){
        return $a['priority'] > $b['priority']?1:-1;
};

function wpleads_render_setting($fields)
{
	//print_r($fields);
	uasort($fields,'wp_leads_sort_fields');
	echo "<table id='wpleads_main_container'>";

	foreach ($fields as $field)
	{
		$id = strtolower($field['key']);
		echo '<tr class="'.$id.'">
			<th class="wpleads-th" ><label for="'.$id.'">'.__( $field['label'],'wpleads').':</label></th>
			<td class="wpleads-td" id="wpleads-td-'.$id.'">';
		switch(true) {
			case strstr($field['type'],'textarea'):
				$parts = explode('-',$field['type']);
				(isset($parts[1])) ? $rows= $parts[1] : $rows = '10';
				echo '<textarea name="'.$id.'" id="'.$id.'" rows='.$rows.'" style="width:99%" >'.$field['value'].'</textarea>';
				break;
			case strstr($field['type'],'text'):
				$parts = explode('-',$field['type']);
				(isset($parts[1])) ? $size = $parts[1] : $size = 35;

				echo '<input type="text" name="'.$id.'" id="'.$id.'" value="'.$field['value'].'" size="'.$size.'" />';
				break;
			case strstr($field['type'],'links'):
				$parts = explode('-',$field['type']);
				(isset($parts[1])) ? $channel= $parts[1] : $channel = 'related';
				$links = explode(';',$field['value']);
				$links = array_filter($links);

				echo "<div style='text-align:right;float:right'><span class='add-new-link'>".__( 'Add New Link')." <img src='".WPL_URL."/images/add.png' title='".__( 'add link' ) ."' align='ABSMIDDLE' class='wpleads-add-link' 'id='{$id}-add-link'></span></div>";
				echo "<div class='wpleads-links-container' id='{$id}-container'>";

				$remove_icon = WPL_URL.'/images/remove.png';

				if (count($links)>0)
				{
					foreach ($links as $key=>$link)
					{
						$icon = wpleads_get_link_icon($link);
						$icon = apply_filters('wpleads_links_icon',$icon);
						echo '<span id="'.$id.'-'.$key.'"><img src="'.$remove_icon.'" class="wpleads_remove_link" id = "'.$key.'" title="Remove Link">';
						echo '<a href="'.$link.'" target="_blank"><img src="'.$icon.'" align="ABSMIDDLE" class="wpleads_link_icon"><input type="hidden" name="'.$id.'['.$key.']" value="'.$link.'" size="70"  class="wpleads_link"  />'.$link.'</a> ';
						echo "</span><br>";

					}
				}
				else
				{
					echo '<input type="text" name="'.$id.'[]" value="" size="70" />';
				}
				echo '</div>';
				break;
			// wysiwyg
			case strstr($field['type'],'wysiwyg'):
				wp_editor( $field['value'], $id, $settings = array() );
				echo	'<p class="description">'.$field['desc'].'</p>';
				break;
			// media
			case strstr($field['type'],'media'):
				//echo 1; exit;
				echo '<label for="upload_image">';
				echo '<input name="'.$id.'"  id="'.$id.'" type="text" size="36" name="upload_image" value="'.$field['value'].'" />';
				echo '<input class="upload_image_button" id="uploader_'.$id.'" type="button" value="Upload Image" />';
				echo '<p class="description">'.$field['desc'].'</p>';
				break;
			// checkbox
			case strstr($field['type'],'checkbox'):
				$i = 1;
				echo "<table class='wpl_check_box_table'>";
				if (!isset($field['value'])){$field['value']=array();}
				elseif (!is_array($field['value'])){
					$field['value'] = array($field['value']);
				}
				foreach ($field['options'] as $value=>$field['label']) {
					if ($i==5||$i==1)
					{
						echo "<tr>";
						$i=1;
					}
						echo '<td><input type="checkbox" name="'.$id.'[]" id="'.$id.'" value="'.$value.'" ',in_array($value,$field['value']) ? ' checked="checked"' : '','/>';
						echo '<label for="'.$value.'">&nbsp;&nbsp;'.$field['label'].'</label></td>';
					if ($i==4)
					{
						echo "</tr>";
					}
					$i++;
				}
				echo "</table>";
				echo '<div class="wpl_tooltip tool_checkbox" title="'.$field['desc'].'"></div>';
				break;
			// radio
			case strstr($field['type'],'radio'):
				foreach ($field['options'] as $value=>$field['label']) {
					//echo $field['value'].":".$id;
					//echo "<br>";
					echo '<input type="radio" name="'.$id.'" id="'.$id.'" value="'.$value.'" ',$field['value']==$value ? ' checked="checked"' : '','/>';
					echo '<label for="'.$value.'">&nbsp;&nbsp;'.$field['label'].'</label> &nbsp;&nbsp;&nbsp;&nbsp;';
				}
				echo '<div class="wpl_tooltip" title="'.$field['desc'].'"></div>';
				break;
			// select
			case $field['type'] == 'dropdown':
				echo '<select name="'.$id.'" id="'.$id.'" >';
				foreach ($field['options'] as $value=>$field['label']) {
					echo '<option', $field['value'] == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$field['label'].'</option>';
				}
				echo '</select><div class="wpl_tooltip" title="'.$field['desc'].'"></div>';
				break;
			case $field['type']=='dropdown-country':
				echo '<input type="hidden" id="hidden-country-value" value="'.$field['value'].'">';
				echo '<select name="'.$id.'" id="'.$id.'" class="wpleads-country-dropdown">';
					?>
					<option value="">Country...</option>
					<option value="AF">Afghanistan</option>
					<option value="AL">Albania</option>
					<option value="DZ">Algeria</option>
					<option value="AS">American Samoa</option>
					<option value="AD">Andorra</option>
					<option value="AG">Angola</option>
					<option value="AI">Anguilla</option>
					<option value="AG">Antigua &amp; Barbuda</option>
					<option value="AR">Argentina</option>
					<option value="AA">Armenia</option>
					<option value="AW">Aruba</option>
					<option value="AU">Australia</option>
					<option value="AT">Austria</option>
					<option value="AZ">Azerbaijan</option>
					<option value="BS">Bahamas</option>
					<option value="BH">Bahrain</option>
					<option value="BD">Bangladesh</option>
					<option value="BB">Barbados</option>
					<option value="BY">Belarus</option>
					<option value="BE">Belgium</option>
					<option value="BZ">Belize</option>
					<option value="BJ">Benin</option>
					<option value="BM">Bermuda</option>
					<option value="BT">Bhutan</option>
					<option value="BO">Bolivia</option>
					<option value="BL">Bonaire</option>
					<option value="BA">Bosnia &amp; Herzegovina</option>
					<option value="BW">Botswana</option>
					<option value="BR">Brazil</option>
					<option value="BC">British Indian Ocean Ter</option>
					<option value="BN">Brunei</option>
					<option value="BG">Bulgaria</option>
					<option value="BF">Burkina Faso</option>
					<option value="BI">Burundi</option>
					<option value="KH">Cambodia</option>
					<option value="CM">Cameroon</option>
					<option value="CA">Canada</option>
					<option value="IC">Canary Islands</option>
					<option value="CV">Cape Verde</option>
					<option value="KY">Cayman Islands</option>
					<option value="CF">Central African Republic</option>
					<option value="TD">Chad</option>
					<option value="CD">Channel Islands</option>
					<option value="CL">Chile</option>
					<option value="CN">China</option>
					<option value="CI">Christmas Island</option>
					<option value="CS">Cocos Island</option>
					<option value="CO">Colombia</option>
					<option value="CC">Comoros</option>
					<option value="CG">Congo</option>
					<option value="CK">Cook Islands</option>
					<option value="CR">Costa Rica</option>
					<option value="CT">Cote D'Ivoire</option>
					<option value="HR">Croatia</option>
					<option value="CU">Cuba</option>
					<option value="CB">Curacao</option>
					<option value="CY">Cyprus</option>
					<option value="CZ">Czech Republic</option>
					<option value="DK">Denmark</option>
					<option value="DJ">Djibouti</option>
					<option value="DM">Dominica</option>
					<option value="DO">Dominican Republic</option>
					<option value="TM">East Timor</option>
					<option value="EC">Ecuador</option>
					<option value="EG">Egypt</option>
					<option value="SV">El Salvador</option>
					<option value="GQ">Equatorial Guinea</option>
					<option value="ER">Eritrea</option>
					<option value="EE">Estonia</option>
					<option value="ET">Ethiopia</option>
					<option value="FA">Falkland Islands</option>
					<option value="FO">Faroe Islands</option>
					<option value="FJ">Fiji</option>
					<option value="FI">Finland</option>
					<option value="FR">France</option>
					<option value="GF">French Guiana</option>
					<option value="PF">French Polynesia</option>
					<option value="FS">French Southern Ter</option>
					<option value="GA">Gabon</option>
					<option value="GM">Gambia</option>
					<option value="GE">Georgia</option>
					<option value="DE">Germany</option>
					<option value="GH">Ghana</option>
					<option value="GI">Gibraltar</option>
					<option value="GB">Great Britain</option>
					<option value="GR">Greece</option>
					<option value="GL">Greenland</option>
					<option value="GD">Grenada</option>
					<option value="GP">Guadeloupe</option>
					<option value="GU">Guam</option>
					<option value="GT">Guatemala</option>
					<option value="GN">Guinea</option>
					<option value="GY">Guyana</option>
					<option value="HT">Haiti</option>
					<option value="HW">Hawaii</option>
					<option value="HN">Honduras</option>
					<option value="HK">Hong Kong</option>
					<option value="HU">Hungary</option>
					<option value="IS">Iceland</option>
					<option value="IN">India</option>
					<option value="ID">Indonesia</option>
					<option value="IA">Iran</option>
					<option value="IQ">Iraq</option>
					<option value="IR">Ireland</option>
					<option value="IM">Isle of Man</option>
					<option value="IL">Israel</option>
					<option value="IT">Italy</option>
					<option value="JM">Jamaica</option>
					<option value="JP">Japan</option>
					<option value="JO">Jordan</option>
					<option value="KZ">Kazakhstan</option>
					<option value="KE">Kenya</option>
					<option value="KI">Kiribati</option>
					<option value="NK">Korea North</option>
					<option value="KS">Korea South</option>
					<option value="KW">Kuwait</option>
					<option value="KG">Kyrgyzstan</option>
					<option value="LA">Laos</option>
					<option value="LV">Latvia</option>
					<option value="LB">Lebanon</option>
					<option value="LS">Lesotho</option>
					<option value="LR">Liberia</option>
					<option value="LY">Libya</option>
					<option value="LI">Liechtenstein</option>
					<option value="LT">Lithuania</option>
					<option value="LU">Luxembourg</option>
					<option value="MO">Macau</option>
					<option value="MK">Macedonia</option>
					<option value="MG">Madagascar</option>
					<option value="MY">Malaysia</option>
					<option value="MW">Malawi</option>
					<option value="MV">Maldives</option>
					<option value="ML">Mali</option>
					<option value="MT">Malta</option>
					<option value="MH">Marshall Islands</option>
					<option value="MQ">Martinique</option>
					<option value="MR">Mauritania</option>
					<option value="MU">Mauritius</option>
					<option value="ME">Mayotte</option>
					<option value="MX">Mexico</option>
					<option value="MI">Midway Islands</option>
					<option value="MD">Moldova</option>
					<option value="MC">Monaco</option>
					<option value="MN">Mongolia</option>
					<option value="MS">Montserrat</option>
					<option value="MA">Morocco</option>
					<option value="MZ">Mozambique</option>
					<option value="MM">Myanmar</option>
					<option value="NA">Nambia</option>
					<option value="NU">Nauru</option>
					<option value="NP">Nepal</option>
					<option value="AN">Netherland Antilles</option>
					<option value="NL">Netherlands (Holland, Europe)</option>
					<option value="NV">Nevis</option>
					<option value="NC">New Caledonia</option>
					<option value="NZ">New Zealand</option>
					<option value="NI">Nicaragua</option>
					<option value="NE">Niger</option>
					<option value="NG">Nigeria</option>
					<option value="NW">Niue</option>
					<option value="NF">Norfolk Island</option>
					<option value="NO">Norway</option>
					<option value="OM">Oman</option>
					<option value="PK">Pakistan</option>
					<option value="PW">Palau Island</option>
					<option value="PS">Palestine</option>
					<option value="PA">Panama</option>
					<option value="PG">Papua New Guinea</option>
					<option value="PY">Paraguay</option>
					<option value="PE">Peru</option>
					<option value="PH">Philippines</option>
					<option value="PO">Pitcairn Island</option>
					<option value="PL">Poland</option>
					<option value="PT">Portugal</option>
					<option value="PR">Puerto Rico</option>
					<option value="QA">Qatar</option>
					<option value="ME">Republic of Montenegro</option>
					<option value="RS">Republic of Serbia</option>
					<option value="RE">Reunion</option>
					<option value="RO">Romania</option>
					<option value="RU">Russia</option>
					<option value="RW">Rwanda</option>
					<option value="NT">St Barthelemy</option>
					<option value="EU">St Eustatius</option>
					<option value="HE">St Helena</option>
					<option value="KN">St Kitts-Nevis</option>
					<option value="LC">St Lucia</option>
					<option value="MB">St Maarten</option>
					<option value="PM">St Pierre &amp; Miquelon</option>
					<option value="VC">St Vincent &amp; Grenadines</option>
					<option value="SP">Saipan</option>
					<option value="SO">Samoa</option>
					<option value="AS">Samoa American</option>
					<option value="SM">San Marino</option>
					<option value="ST">Sao Tome &amp; Principe</option>
					<option value="SA">Saudi Arabia</option>
					<option value="SN">Senegal</option>
					<option value="SC">Seychelles</option>
					<option value="SL">Sierra Leone</option>
					<option value="SG">Singapore</option>
					<option value="SK">Slovakia</option>
					<option value="SI">Slovenia</option>
					<option value="SB">Solomon Islands</option>
					<option value="OI">Somalia</option>
					<option value="ZA">South Africa</option>
					<option value="ES">Spain</option>
					<option value="LK">Sri Lanka</option>
					<option value="SD">Sudan</option>
					<option value="SR">Suriname</option>
					<option value="SZ">Swaziland</option>
					<option value="SE">Sweden</option>
					<option value="CH">Switzerland</option>
					<option value="SY">Syria</option>
					<option value="TA">Tahiti</option>
					<option value="TW">Taiwan</option>
					<option value="TJ">Tajikistan</option>
					<option value="TZ">Tanzania</option>
					<option value="TH">Thailand</option>
					<option value="TG">Togo</option>
					<option value="TK">Tokelau</option>
					<option value="TO">Tonga</option>
					<option value="TT">Trinidad &amp; Tobago</option>
					<option value="TN">Tunisia</option>
					<option value="TR">Turkey</option>
					<option value="TU">Turkmenistan</option>
					<option value="TC">Turks &amp; Caicos Is</option>
					<option value="TV">Tuvalu</option>
					<option value="UG">Uganda</option>
					<option value="UA">Ukraine</option>
					<option value="AE">United Arab Emirates</option>
					<option value="GB">United Kingdom</option>
					<option value="US">United States of America</option>
					<option value="UY">Uruguay</option>
					<option value="UZ">Uzbekistan</option>
					<option value="VU">Vanuatu</option>
					<option value="VS">Vatican City State</option>
					<option value="VE">Venezuela</option>
					<option value="VN">Vietnam</option>
					<option value="VB">Virgin Islands (Brit)</option>
					<option value="VA">Virgin Islands (USA)</option>
					<option value="WK">Wake Island</option>
					<option value="WF">Wallis &amp; Futana Is</option>
					<option value="YE">Yemen</option>
					<option value="ZR">Zaire</option>
					<option value="ZM">Zambia</option>
					<option value="ZW">Zimbabwe</option>
					</select>
					<?php
				break;
		} //end switch
		echo '</td></tr>';
	}

	echo '</table>';
}


function wpleads_get_link_icon($link)
{
	switch (true){
		case strstr($link,'facebook.com'):
			$icon = WPL_URL.'/images/icons/facebook.png';
			break;
		case strstr($link,'linkedin.com'):
			$icon = WPL_URL.'/images/icons/linkedin.png';
			break;
		case strstr($link,'twitter.com'):
			$icon = WPL_URL.'/images/icons/twitter.png';
			break;
		case strstr($link,'pinterest.com'):
			$icon = WPL_URL.'/images/icons/pinterest.png';
			break;
		case strstr($link,'plus.google.'):
			$icon = WPL_URL.'/images/icons/google.png';
			break;
		case strstr($link,'youtube.com'):
			$icon = WPL_URL.'/images/icons/youtube.png';
			break;
		case strstr($link,'reddit.com'):
			$icon = WPL_URL.'/images/icons/reddit.png';
			break;
		case strstr($link,'badoo.com'):
			$icon = WPL_URL.'/images/icons/badoo.png';
			break;
		case strstr($link,'meetup.com'):
			$icon = WPL_URL.'/images/icons/meetup.png';
			break;
		case strstr($link,'livejournal.com'):
			$icon = WPL_URL.'/images/icons/livejournal.png';
			break;
		case strstr($link,'myspace.com'):
			$icon = WPL_URL.'/images/icons/myspace.png';
			break;
		case strstr($link,'deviantart.com'):
			$icon = WPL_URL.'/images/icons/deviantart.png';
			break;
		default:
			$icon = WPL_URL.'/images/icons/link.png';
			break;
	}

	return $icon;
}

function wpl_tag_cloud() {
	global $post;
	$page_views = get_post_meta($post->ID,'page_views', true);
    $page_view_array = json_decode($page_views, true);
    if($page_views)
	{
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
			if(!empty($array))
				$tag_names[] = wp_get_post_tags( $val, array( 'fields' => 'names' ) );


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
    }
	else
	{
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

					if ($field['key']=='wpleads_email_address')
					{
						$args = array( 'ID'=>$post_id , 'post_title' => $new );
						wp_update_post($args);
					}

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
		__( 'Visitor Path Sessions - <span class="session-desc">(Sessions expire after 1 hour of inactivity)</span> <span class="minimize-paths button">Shrink Session View</span>' , 'wplead_metabox_conversion' ),
		'wpleads_display_conversion_path', // $callback
		'wp-lead', // $page
		'normal', // $context
		'high'); // $priority
}

function c_table_leads_sort_array_datetime_reverse($a,$b) {
	return strtotime($a['date'])>strtotime($b['date'])?1:-1;
}

function c_table_leads_sort_array_datetime($a,$b){
        return strtotime($a['date'])<strtotime($b['date'])?1:-1;
}

// Render Conversion Paths
function wpleads_display_conversion_path() {
	global $post;
	global $wpdb;

	$conversions = get_post_meta($post->ID,'wpleads_conversion_data', true);
	$conversions_array = json_decode($conversions, true);
	$c_array = array();
	if (is_array($conversions_array)) {
		uasort($conversions_array,'leads_sort_array_datetime'); // Date sort
		$conversion_count = count($conversions_array);
		//print_r($conversions_array);
		$i = $conversion_count;

		$c_count = 0;
		foreach ($conversions_array as $key => $value)
		{
			$c_array[$c_count]['page'] = $value['id'];
			$c_array[$c_count]['date'] = $value['datetime'];
			$c_array[$c_count]['conversion'] = 'yes';
			$c_array[$c_count]['variation'] = $value['variation'];
	      	$c_count++;

		}

	}

	$page_views = get_post_meta($post->ID,'page_views', true);
   	$page_view_array = json_decode($page_views, true);

   	if (!is_array($page_view_array)) {
   		echo "No Data";
   		return;
   	}

    $new_array = array();
    $loop = 0;
	// Combine and loop through all page view objects
	 foreach($page_view_array as $key=>$val)
      {
      	foreach($page_view_array[$key] as $test){
      			$new_array[$loop]['page'] = $key;
      			$new_array[$loop]['date'] = $test;
      	      	$loop++;
      	}

      }
    $new_array = array_merge($c_array, $new_array); // Merge conversion and page view json objects
   // print_r($new_array);
    //$timeout = 1800; // thirty minutes in seconds
    //$test = abs(strtotime(Last Time) - strtotime(NEW TIME));
   	/* $test = abs(strtotime("2013-11-19 12:58:12") - strtotime("2013-11-20 4:57:02 UTC")); */
  	uasort($new_array,'c_table_leads_sort_array_datetime'); // Date sort

  	//print_r($new_array);
  	$new_key_array = array();
  	$num = 0;
  	foreach ( $new_array as $key => $val ) {
		$new_key_array[ $num ] = $val;
		$num++;
  	}
  	//print_r($new_key_array);
  	$new_loop = 1;
  	$total_session_count = 0;
    foreach ($new_key_array as $key => $value) {

    	$last_item = $key - 1;

    	$next_item = $key + 1;
    	$conversion = (isset($new_key_array[$key]['conversion'])) ? 'lead-conversion-mark' : '';
    	$conversion_text = (isset($new_key_array[$key]['conversion'])) ? '<span class="conv-text">(Conversion Event)</span>' : '';
    	$close_div = ($total_session_count != 0) ? '</div></div>' : '';
    	//echo $new_key_array[$new_loop]['date'];
    	if(isset($new_key_array[$last_item]['date'])){
    		$timeout = abs(strtotime($new_key_array[$last_item]['date']) - strtotime($new_key_array[$key]['date']));
    	} else{
    		$timeout = 3601;
    	}

    	$date =  date_create($new_key_array[$key]['date']);
    	$break = 'off';
    	if ($timeout >= 3600) {
    		echo $close_div . '<a class="session-anchor" id="view-session-'.$total_session_count.'""></a><div id="conversion-tracking" class="wpleads-conversion-tracking-table" summary="Conversion Tracking">

    		<div class="conversion-tracking-header">
    				<h2><span class="toggle-conversion-list">-</span><strong>Visit <span class="visit-number"></span></strong> on <span class="shown_date">'.date_format($date, 'F jS, Y \a\t g:ia (l)').'</span><span class="time-on-page-label">Time spent on page</span></h2> <span class="hidden_date date_'.$total_session_count.'">'.date_format($date, 'F jS, Y \a\t g:ia:s').'</span>
    		</div><div class="session-item-holder">';

    		$total_session_count++;
    		//echo "</div>";
    		$break = "on";
    	}
    	$page_id = $new_key_array[$key]['page'];
    		if (strpos($page_id,'cat_') !== false) {
        	  	$cat_id = str_replace("cat_", "", $page_id);
        	  	$page_name = get_cat_name($cat_id) . " Category Page";
        	  	$tag_names = '';
        	  	$page_permalink = get_category_link( $cat_id );
    	  	} elseif (strpos($page_id,'tag_') !== false) {
        	  	$tag_id = str_replace("tag_", "", $page_id);
        	  	$tag = get_tag( $tag_id );
        	  	$page_name = $tag->name . " - Tag Page";
        	  	$tag_names = '';
        	  	$page_permalink = get_tag_link($tag_id);
    	  	} else {
    		$page_title = get_the_title($page_id);
    		$page_name = ($page_id != 0) ? $page_title : 'N/A';
    		$page_permalink = get_permalink($page_id);
    	}
    	$timeon_page = $timeout / 60;
    	$date_print = date_create($new_key_array[$key]['date']);
    	//$last_date = date_create($new_key_array[$last_item]['date']);


    	//echo "<br>".$timeout."<-timeout on key " . $new_loop . " " . $new_key_array[$key]['date'] . ' on page: ' . $new_key_array[$key]['page'];
    	//$second_diff = abs(date_format($last_date, 's') - date_format($date_print, 's'));
    	if(isset($new_key_array[$last_item]['date'])){
    	$second_diff = leads_time_diff($new_key_array[$last_item]['date'], $new_key_array[$key]['date']);
    	} else {
    	$second_diff['minutes'] = 0;
    	$second_diff['seconds'] = 0;
    	}
    	//print_r($second_diff);
    	//$second_diff = date('i:s',$second_diff);
    	$minute = ($second_diff['minutes'] != 0) ? "<strong>" . $second_diff['minutes'] . "</strong> " : '';
    	$minute_text = ($second_diff['minutes'] != 0) ? $second_diff['mm-text'] . " " : '';
    	$second = ($second_diff['seconds'] != 0) ? "<strong>" . $second_diff['seconds'] . "</strong> " : 'Less than 1 second';
    	$second_text = ($second_diff['seconds'] != 0) ? $second_diff['sec-text'] . " " : '';
    	if ($break === "on") {
    		$minute = "";
    		$minute_text =  "";
    		$second =  "";
    		$second_text =  "Session Timeout";
    	}
    	if ($page_id != "0" && $page_id != "null"){
    	echo "<div class='lp-page-view-item ".$conversion."'>
			<span class='marker'></span> <a href='".$page_permalink."' title='View This Page' target='_blank'>".$page_name."</a> on <span>".date_format($date_print, 'F jS, Y \a\t g:i:s a')."</span>
			".$conversion_text."
			<span class='time-on-page'>". $minute . $minute_text .  $second . $second_text . "</span>
			</div>";
		}
    	$new_loop++;

     }
?>

		</div><!-- end .conversion-session-view -->
		</div><!-- end #conversion-tracking -->

		<?php

	//update_option($post->ID,'wpl-lead-conversions',implode(',',$conversions));
	// echo count($array_page_view_total);
}

 ?>