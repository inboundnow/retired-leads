<?php

/**
 * Function for rendering lead fields. Filterable
 * @return array - Fields for lead data
 */
function wp_leads_get_lead_fields(){

	$lead_fields = array(
	    array(
	        'label' => 'First Name',
	        'key'  => 'wpleads_first_name',
	        'priority' => 15,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Last Name',
	        'key'  => 'wpleads_last_name',
	        'priority' => 45,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Email',
	        'key'  => 'wpleads_email_address',
	        'priority' => 60,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Website',
	        'key'  => 'wpleads_website',
	        'priority' => 60,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Company Name',
	        'key'  => 'wpleads_company_name',
	        'priority' => 75,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Mobile Phone',
	        'key'  => 'wpleads_mobile_phone',
	        'priority' => 90,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Work Phone',
	        'key'  => 'wpleads_work_phone',
	        'priority' => 105,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Address',
	        'key'  => 'wpleads_address_line_1',
	        'priority' => 120,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Address Continued',
	        'key'  => 'wpleads_address_line_2',
	        'priority' => 135,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'City',
	        'key'  => 'wpleads_city',
	        'priority' => 150,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'State/Region',
	        'key'  => 'wpleads_region_name',
	        'priority' => 165,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Zip-code',
	        'key'  => 'wpleads_zip',
	        'priority' => 180,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Country',
	        'key'  => 'wpleads_country_code',
	        'priority' => 195,
	        'type'  => 'text'
	        ),
	    array(
	        'label' => 'Related Websites',
	        'key'  => 'wpleads_websites',
	        'priority' => 215,
	        'type'  => 'links'
	        ),
	    array(
	        'label' => 'Notes',
	        'key'  => 'wpleads_notes',
	        'priority' => 230,
	        'type'  => 'textarea'
	        ),

	);

	$lead_fields = apply_filters('wp_leads_add_lead_field',$lead_fields);

	return $lead_fields;
}

// Create Field Mapping Array
add_action( 'init', 'wp_leads_set_lead_fields');
function wp_leads_set_lead_fields() {
	if (isset($_GET['clear_lead_custom_field_cache'])) {
	delete_transient( 'wp-lead-fields');
	}
	$lead_fields = get_transient( 'wp-lead-fields' );
	if ( false === $lead_fields ) {
		$lead_fields = wp_leads_get_lead_fields();
		$clean_array = array();
		$clean_array[''] = 'No Mapping'; // default empty
		foreach ($lead_fields as $key=>$field)
		{
				$label = $field['label'];
				$key = $field['key'];
				$clean_array[$key] = $label;
		}
		set_transient( 'wp-lead-fields', $clean_array, 24 * HOUR_IN_SECONDS );
	}
}
/**
 * Add in custom lead fields
 *
 * This function adds additional fields to your lead profiles.
 * Label: Name of the Field
 * key: Meta key associated with data
 * priority: Where you want the fields placed. See https://github.com/inboundnow/leads/blob/master/modules/wpl.m.userfields.php#L7 for current weights
 * type: type of user area. 'text' or 'textarea'
 */
/*
add_filter('wp_leads_add_lead_field', 'custom_add_more_lead_fields', 10, 1);
function custom_add_more_lead_fields($lead_fields) {

 $new_fields =  array(
 					array(
				        'label' => 'Timmmm Company',
				        'key'  => 'wpleads_ip_addressy',
				        'priority' => 18,
				        'type'  => 'text'
				        ),
 					array(
				        'label' => 'Lead Source',
				        'key'  => 'wpleads_lead_source',
				        'priority' => 19,
				        'type'  => 'text'
				        ),
 					array(
				        'label' => 'New Field',
				        'key'  => 'wpleads_lead_source',
				        'priority' => 19,
				        'type'  => 'text'
				        ),
 					array(
				        'label' => 'Description',
				        'key'  => 'wpleads_description',
				        'priority' => 19,
				        'type'  => 'textarea'
				        )
				    );

		foreach ($new_fields as $key => $value) {
			array_push($lead_fields, $new_fields[$key]);
		}

        return $lead_fields;

}
*/
?>