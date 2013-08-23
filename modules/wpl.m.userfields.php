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

/* Add in custom lead fields */
/* 
add_filter('wp_leads_add_lead_field', 'custom_add_second_field', 10, 1);
function custom_add_second_field($lead_fields) {

		
         $new_fields =  array( 
         					array(
						        'label' => 'Upper Company',
						        'key'  => 'wpleads_upper_company',
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
						        'label' => 'Vertical',
						        'key'  => 'wpleads_vertical',
						        'priority' => 19,
						        'type'  => 'text'
						        ),
         					array(
						        'label' => 'LNR Recipient',
						        'key'  => 'wpleads_lnr_recipient',
						        'priority' => 19,
						        'type'  => 'text'
						        ),
         					array(
						        'label' => 'LNR Sent',
						        'key'  => 'wpleads_lnr_sent',
						        'priority' => 19,
						        'type'  => 'text'
						        ),
         					array(
						        'label' => 'Salutation',
						        'key'  => 'wpleads_salutation',
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

} */


?>