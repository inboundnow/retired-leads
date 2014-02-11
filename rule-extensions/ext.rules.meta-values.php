<?php
delete_transient('rules_meta_keys');
function rules_get_meta_keys_sql($post_type){
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
   $comrules_separated = implode(",", $array);
   $haystack = $comrules_separated;
	// echo $comrules_separated;
   //$filter = array('rules_list_sorting','wpl-lead-raw-post-data','wpleads_conversion_data','rule_executed','wpleads_landing_page', 'lt_event_tracked_');


	foreach ($meta_keys as $key=>$value)
	{

		if(stristr($value, 'rules_list_sorting') != FALSE) {
		       $bad_array[] = $value;
		}
		if(stristr($value, 'wpl-lead-raw-post-data') != FALSE) {
		       $bad_array[] = $value;
		}
		if(stristr($value, 'rule_executed') != FALSE) {
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
	array_push($bad_array, "wpl-lead-page-view-count", "wpl-lead-conversions", "wpl-lead-conversion-count", "lp_page_views_count", "rules_rules_accomplished", "wp_cta_page_conversions_count", "wp_cta_page_views_count", "wp_cta_trigger_count", "wpleads_uid", "wp_leads_uid");
   	$result = array_diff($meta_keys, $bad_array);

    set_transient('rules_meta_keys', $result, 60*60*24);
    return $meta_keys;
}

function rules_get_meta_keys($post_type){
    $cache = get_transient('rules_meta_keys');
    $meta_keys = $cache ? $cache : rules_get_meta_keys_sql($post_type);


	foreach ($meta_keys as $key=>$value)
	{
			$clean_val = str_replace("wpleads_", "", $value);
			$clean_val_two = str_replace("_", " ", $clean_val);
			$meta_keys_final[$value] = ucfirst($clean_val_two);
	}

    return $meta_keys_final;
}


add_filter('rules_if_options','rule_add_if_statement_metafields');
function rule_add_if_statement_metafields($rules_if_options)
{
	//$meta_keys = rules_get_meta_keys('lead');
	$rules_if_options['meta_value_is'] = "Lead meta value";

	return $rules_if_options;
}

add_filter('wpleads_lead_rule_fields','rule_add_settings_metafields');
function rule_add_settings_metafields($rule_fields)
{
	$rule_fields['condition_meta_condtion']['label'] = 'Meta Value Condition';
	$rule_fields['condition_meta_condtion']['name'] = 'rule_condition_meta_condition';
	$rule_fields['condition_meta_condtion']['id'] = 'rule_condition_meta_condition';
	$rule_fields['condition_meta_condtion']['position'] = 'conditions';
	$rule_fields['condition_meta_condtion']['priority'] =  21;
	$rule_fields['condition_meta_condtion']['nature'] = "dropdown";
	$rule_fields['condition_meta_condtion']['class'] = "rules_dropdown";
	$rule_fields['condition_meta_condtion']['show'] = false;
	$rule_fields['condition_meta_condtion']['tooltip'] = "Select the meta value comparison condition.";
	$list_options = array('equals'=>'Equals','is_greater_than'=>'Is Greater Than','is_less_than'=>'Is Less Than','contains'=>'Contains');
	$rule_fields['condition_meta_condtion']['options'] = $list_options;

	$rule_fields['condition_meta_key']['label'] = 'Meta Key to Target';
	$rule_fields['condition_meta_key']['name'] = 'rule_condition_meta_key';
	$rule_fields['condition_meta_key']['id'] = 'rule_condition_meta_key';
	$rule_fields['condition_meta_key']['position'] = 'conditions';
	$rule_fields['condition_meta_key']['priority'] = 22;
	$rule_fields['condition_meta_key']['nature'] = "dropdown";
	$rule_fields['condition_meta_key']['class'] = "rules_dropdown";
	$rule_fields['condition_meta_key']['show'] = false;
	$rule_fields['condition_meta_key']['tooltip'] = "Select lead meta key to search.";
	$list_options = rules_get_meta_keys('wp-lead');
	$rule_fields['condition_meta_key']['options'] = $list_options;


	$rule_fields['condition_meta_value']['label'] = 'Meta Comparision Value';
	$rule_fields['condition_meta_value']['name'] = 'rule_condition_meta_compare_value';
	$rule_fields['condition_meta_value']['id'] = 'rule_condition_meta_compare_value';
	$rule_fields['condition_meta_value']['position'] = 'conditions';
	$rule_fields['condition_meta_value']['priority'] = 23;
	$rule_fields['condition_meta_value']['nature'] = "text";
	$rule_fields['condition_meta_value']['class'] = "rules_dropdown";
	$rule_fields['condition_meta_value']['show'] = false;
	$rule_fields['condition_meta_value']['tooltip'] = "Type in the value you would like to compare against the discovered meta value associated with the meta key we are targeting. ";

	return $rule_fields;
}

add_action ('rules_rule_js','rule_add_js_metafields');
function rule_add_js_metafields()
{
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function()
	{
		jQuery(document).on('change', '.rule_if', function() {
			var this_id = jQuery(this).val();
			var this_rel = jQuery(this).attr('rel');

			if (this_id.indexOf("meta_value_is") >= 0)
			{
				jQuery('#tr_rule_condition_meta_key'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_condition'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_compare_value'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_number'+'_'+this_rel).addClass('rule-hidden-steps');
			}
			else
			{
				jQuery('#tr_rule_condition_meta_key'+'_'+this_rel).addClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_condition'+'_'+this_rel).addClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_compare_value'+'_'+this_rel).addClass('rule-hidden-steps');
			}
		});

		jQuery('.rule_if').each(function(index,value){
			var selectedIF = jQuery(this).find(":selected").val();
			var this_rel = jQuery(this).attr('rel');
			if (selectedIF.indexOf("meta_value_is") >= 0)
			{
				jQuery('#tr_rule_condition_meta_key'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_condition'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_compare_value'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_number'+'_'+this_rel).addClass('rule-hidden-steps');
			}
			else
			{
				jQuery('#tr_rule_condition_meta_key'+'_'+this_rel).addClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_condition'+'_'+this_rel).addClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_meta_compare_value'+'_'+this_rel).addClass('rule-hidden-steps');
			}
		});
	});
	</script>
	<?php
}

add_filter('wpleads_lead_rules_extend_check', 'rule_execute_metafields', 10, 4);
function rule_execute_metafields($conditions_met, $rule_block_ids, $lead_id, $rule)
{

	foreach ($rule_block_ids as $condtion_key => $cid)
	{
		if ($rule['rule_if_'.$cid][0] == 'meta_value_is')
		{
			switch ($rule['rule_condition_meta_condition_'.$cid][0]){
				case "equals":
					echo 'Meta Key:'.$rule['rule_condition_meta_key_'.$cid][0];
					echo "<br>";
					echo 'Compare Meta Value:'.$rule['rule_condition_meta_compare_value_'.$cid][0];
					echo "<br>";
					$meta_value = get_post_meta($lead_id, $rule['rule_condition_meta_key_'.$cid][0], true);
					echo 'Actual Meta Value:'.$meta_value;
					echo "<br>";
					if ($meta_value==$rule['rule_condition_meta_compare_value_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
				case "is_greater_than":
					$meta_value = get_post_meta($lead_id, $rule['rule_condition_meta_key_'.$cid][0], true);

					if ($meta_value>$rule['rule_condition_meta_compare_value_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
				case "is_less_than":
					$meta_value = get_post_meta($lead_id, $rule['rule_condition_meta_key_'.$cid][0], true);
					if ($meta_value<$rule['rule_condition_meta_compare_value_'.$cid][0])
					{
						$conditions_met[$cid] = true;
					}
					break;
				case "contains":
					$meta_value = get_post_meta($lead_id, $rule['rule_condition_meta_key_'.$cid][0], true);
					if (stristr($meta_value,$rule['rule_condition_meta_compare_value_'.$cid][0]))
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