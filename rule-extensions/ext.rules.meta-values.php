<?php
delete_transient('automation_meta_keys');
function automation_get_meta_keys_sql($post_type){
    global $wpdb;
    $query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key)
        FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
        WHERE $wpdb->posts.post_type = '%s'
        AND $wpdb->postmeta.meta_key != ''
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
    ";
    $meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));

   //print_r($meta_keys);

   $array = $meta_keys;
   $comautomation_separated = implode(",", $array);
   $haystack = $comautomation_separated;
	// echo $comautomation_separated;
   //$filter = array('automation_list_sorting','wpl-lead-raw-post-data','wpleads_conversion_data','automation_executed','wpleads_landing_page', 'lt_event_tracked_');


	foreach ($meta_keys as $key=>$value)
	{

		if(stristr($value, 'automation_list_sorting') != FALSE) {
		       $bad_array[] = $value;
		}
		if(stristr($value, 'wpl-lead-raw-post-data') != FALSE) {
		       $bad_array[] = $value;
		}
		if(stristr($value, 'automation_executed') != FALSE) {
		       $bad_array[] = $value;
		}
		if(stristr($value, 'lt_event_tracked_') != FALSE) {
		       $bad_array[] = $value;
		}
		if(stristr($value, 'wpleads_landing_page_') != FALSE) {
		       $bad_array[] = $value;
		}
		if(stristr($value, 'times') != FALSE) {
		       $bad_array[] = $value;
		}

	}
	array_push($bad_array, "wpl-lead-page-view-count", "wpl-lead-conversions", "wpl-lead-conversion-count", "lp_page_views_count", "automation_automation_accomplished", "wp_cta_page_conversions_count", "wp_cta_page_views_count", "wp_cta_trigger_count", "wpleads_uid", "wp_leads_uid");
   	$result = array_diff($meta_keys, $bad_array);

    set_transient('automation_meta_keys', $result, 60*60*24);
    return $meta_keys;
}

function automation_get_meta_keys($post_type){
    $cache = get_transient('automation_meta_keys');
    $meta_keys = $cache ? $cache : automation_get_meta_keys_sql($post_type);


	foreach ($meta_keys as $key=>$value)
	{
			$clean_val = str_replace("wpleads_", "", $value);
			$clean_val_two = str_replace("_", " ", $clean_val);
			$meta_keys_final[$value] = ucfirst($clean_val_two);
	}

    return $meta_keys_final;
}


add_filter('automation_if_options','automation_add_if_statement_metafields');
function automation_add_if_statement_metafields($automation_if_options)
{
	//$meta_keys = automation_get_meta_keys('lead');
	$automation_if_options['meta_value_is'] = "Lead meta value";

	return $automation_if_options;
}

add_filter('wpleads_automation_fields','automation_add_settings_metafields');
function automation_add_settings_metafields($automation_fields)
{
	$automation_fields['condition_meta_condtion']['label'] = 'Meta Value Condition';
	$automation_fields['condition_meta_condtion']['name'] = 'automation_condition_meta_condition';
	$automation_fields['condition_meta_condtion']['id'] = 'automation_condition_meta_condition';
	$automation_fields['condition_meta_condtion']['position'] = 'conditions';
	$automation_fields['condition_meta_condtion']['priority'] =  21;
	$automation_fields['condition_meta_condtion']['nature'] = "dropdown";
	$automation_fields['condition_meta_condtion']['class'] = "automation_dropdown";
	$automation_fields['condition_meta_condtion']['show'] = false;
	$automation_fields['condition_meta_condtion']['tooltip'] = "Select the meta value comparison condition.";
	$list_options = array('equals'=>'Equals','is_greater_than'=>'Is Greater Than','is_less_than'=>'Is Less Than','contains'=>'Contains');
	$automation_fields['condition_meta_condtion']['options'] = $list_options;

	$automation_fields['condition_meta_key']['label'] = 'Meta Key to Target';
	$automation_fields['condition_meta_key']['name'] = 'automation_condition_meta_key';
	$automation_fields['condition_meta_key']['id'] = 'automation_condition_meta_key';
	$automation_fields['condition_meta_key']['position'] = 'conditions';
	$automation_fields['condition_meta_key']['priority'] = 22;
	$automation_fields['condition_meta_key']['nature'] = "dropdown";
	$automation_fields['condition_meta_key']['class'] = "automation_dropdown";
	$automation_fields['condition_meta_key']['show'] = false;
	$automation_fields['condition_meta_key']['tooltip'] = "Select lead meta key to search.";
	$list_options = automation_get_meta_keys('wp-lead');
	$automation_fields['condition_meta_key']['options'] = $list_options;


	$automation_fields['condition_meta_value']['label'] = 'Meta Comparision Value';
	$automation_fields['condition_meta_value']['name'] = 'automation_condition_meta_compare_value';
	$automation_fields['condition_meta_value']['id'] = 'automation_condition_meta_compare_value';
	$automation_fields['condition_meta_value']['position'] = 'conditions';
	$automation_fields['condition_meta_value']['priority'] = 23;
	$automation_fields['condition_meta_value']['nature'] = "text";
	$automation_fields['condition_meta_value']['class'] = "automation_dropdown";
	$automation_fields['condition_meta_value']['show'] = false;
	$automation_fields['condition_meta_value']['tooltip'] = "Type in the value you would like to compare against the discovered meta value associated with the meta key we are targeting. ";

	return $automation_fields;
}

add_action ('automation_js','automation_add_js_metafields');
function automation_add_js_metafields()
{
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function()
	{
		jQuery(document).on('change', '.automation_if', function() {
			var this_id = jQuery(this).val();
			var this_rel = jQuery(this).attr('rel');

			if (this_id.indexOf("meta_value_is") >= 0)
			{
				jQuery('#tr_automation_condition_meta_key'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_condition'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_compare_value'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_number'+'_'+this_rel).addClass('automation-hidden-steps');
			}
			else
			{
				jQuery('#tr_automation_condition_meta_key'+'_'+this_rel).addClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_condition'+'_'+this_rel).addClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_compare_value'+'_'+this_rel).addClass('automation-hidden-steps');
			}
		});

		jQuery('.automation_if').each(function(index,value){
			var selectedIF = jQuery(this).find(":selected").val();
			var this_rel = jQuery(this).attr('rel');
			if (selectedIF.indexOf("meta_value_is") >= 0)
			{
				jQuery('#tr_automation_condition_meta_key'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_condition'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_compare_value'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_number'+'_'+this_rel).addClass('automation-hidden-steps');
			}
			else
			{
				jQuery('#tr_automation_condition_meta_key'+'_'+this_rel).addClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_condition'+'_'+this_rel).addClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_meta_compare_value'+'_'+this_rel).addClass('automation-hidden-steps');
			}
		});
	});
	</script>
	<?php
}

add_filter('wpleads_lead_automation_extend_check', 'automation_execute_metafields', 10, 4);
function automation_execute_metafields($conditions_met, $automation_block_ids, $lead_id, $rule)
{

	foreach ($automation_block_ids as $condtion_key => $cid)
	{
		if ($rule['automation_if_'.$cid][0] == 'meta_value_is')
		{
			switch ($rule['automation_condition_meta_condition_'.$cid][0]){
				case "equals":
					echo 'Meta Key:'.$rule['automation_condition_meta_key_'.$cid][0];
					echo "<br>";
					echo 'Compare Meta Value:'.$rule['automation_condition_meta_compare_value_'.$cid][0];
					echo "<br>";
					$meta_value = get_post_meta($lead_id, $rule['automation_condition_meta_key_'.$cid][0], true);
					echo 'Actual Meta Value:'.$meta_value;
					echo "<br>";
					if ($meta_value==$rule['automation_condition_meta_compare_value_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
				case "is_greater_than":
					$meta_value = get_post_meta($lead_id, $rule['automation_condition_meta_key_'.$cid][0], true);

					if ($meta_value>$rule['automation_condition_meta_compare_value_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
				case "is_less_than":
					$meta_value = get_post_meta($lead_id, $rule['automation_condition_meta_key_'.$cid][0], true);
					if ($meta_value<$rule['automation_condition_meta_compare_value_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
				case "contains":
					$meta_value = get_post_meta($lead_id, $rule['automation_condition_meta_key_'.$cid][0], true);
					if (stristr($meta_value,$rule['automation_condition_meta_compare_value_'.$cid][0]))
					{
						$conditions_met[$cid] = true;
					}
					break;
			}

			//print_r($conditions_met);
		}
	}

	return $conditions_met;
}