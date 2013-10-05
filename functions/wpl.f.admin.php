<?php

// Add to the admin_init hook of your theme functions.php file 
add_action( 'admin_init', 'wp_leads_add_cats_and_tags' );
function wp_leads_add_cats_and_tags() {  
	// Add tag metabox to page
	register_taxonomy_for_object_type('post_tag', 'page'); 
	// Add category metabox to page
	register_taxonomy_for_object_type('category', 'page');  
}

/* Actions */
//add_action('wpleads_after_quickstats', 'wpleads_after_quick_stats_callback');
//add_action('wpleads_before_quickstats', 'wpleads_before_quick_stats_callback');
//add_action('wpleads_before_main_fields', 'wpleads_before_main_fields_callback');
//add_action('wpleads_after_main_fields', 'wpleads_after_main_fields_callback');
function wpleads_before_quick_stats_callback()
{
// echo 'hi';
}

function wpleads_remote_connect($url)
{
	$method1 = ini_get('allow_url_fopen') ? "Enabled" : "Disabled";
	if ($method1 == 'Disabled')
	{
		//do curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		$string = curl_exec($ch);
	}
	else
	{
		$string = file_get_contents($url);
	}
	
	return $string;
}

function wpleads_check_url_for_queries($referrer)
{
	//now check if google
	if (strstr($referrer,'q='))
	{		
		//get keywords
		preg_match('/q=(.*?)(&|\z)/', $referrer,$matches);
		$keywords = $matches[1];
		$keywords = urldecode($keywords);		
		$keywords = str_replace('+',' ',$keywords);
		
		//get search engine domain
		$parsed = parse_url($referrer);
		$domain = $parsed['host'];						
		
		return array($keywords,$domain);
		
	}
	
	return false;
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



function wpleads_add_option($key,$type,$id,$default=null,$label=null,$description=null, $options=null)
{
	switch ($type)
	{
		case "colorpicker":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'colorpicker',
			'default'  => $default
			);
			break;
		case "text":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'text',
			'default'  => $default
			);
			break;
		case "textarea":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'textarea',
			'default'  => $default
			);
			break;
		case "wysiwyg":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'wysiwyg',
			'default'  => $default
			);
			break;
		case "media":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'media',
			'default'  => $default
			);
			break;
		case "checkbox":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'checkbox',
			'default'  => $default,
			'options' => $options
			);
			break;
		case "radio":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'radio',
			'default'  => $default,
			'options' => $options
			);
			break;
		case "dropdown":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'dropdown',
			'default'  => $default,
			'options' => $options
			);
			break;
		case "datepicker":
			return array(
			'label' => $label,
			'desc'  => $description,
			'id'    => $key.'-'.$id,
			'type'  => 'datepicker',
			'default'  => $default
			);
			break;
	}
}

function wpleads_count_associated_lead_items($post_id, $get_transient = false)
{
	global $wpdb;
	$list = get_post($post_id);
	$list_slug = $list->post_name;
	
	if ($get_transient)
	{
		$num = get_transient('wpleads_count_associated_lead_items-'.$post_id);
		if ($num)
			return $num.' leads';
	}
	
	$args = array(
		'post_type' => 'wp-lead',
		'post_status' => 'published',
		'wplead_list_category' => $list_slug,
		'numberposts' => -1
	);
	
	$num = count( get_posts( $args ) );
	
	set_transient('wpleads_count_associated_lead_items-'.$post_id , $num , 60*60*1);
	
	return "$num leads";
}

