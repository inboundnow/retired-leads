<?php

function rules_get_rules_options()
{
	global $post;

	$rules = get_posts('post_type=rule&posts_per_page=-1');
	if($rules)
	{
		foreach ( $rules as $rule )
		{

			if ($post->ID==$rule->ID)
				continue;

			if ($rule->post_status!='publish'||empty($rule->post_title))
				continue;

			$options[$rule->ID] = $rule->post_title;
		}

	}
	//exit;
	if (!isset($options))
		$options['0'] = "No rules available.";


	return $options;
}


add_filter('wpleads_lead_rule_fields','rule_add_settings_rule_prerequisites');
function rule_add_settings_rule_prerequisites($rule_fields)
{
	$rule_fields['condition_rule_prerequisites']['label'] = 'Rule Prerequisites';
	$rule_fields['condition_rule_prerequisites']['name'] = 'rule_condition_rule_prerequisites';
	$rule_fields['condition_rule_prerequisites']['id'] = 'rule_condition_rule_prerequisites';
	$rule_fields['condition_rule_prerequisites']['position'] = 'actions';
	$rule_fields['condition_rule_prerequisites']['priority'] =  69;
	$rule_fields['condition_rule_prerequisites']['nature'] = "checkbox";
	$rule_fields['condition_rule_prerequisites']['style'] = "column";
	$rule_fields['condition_rule_prerequisites']['class'] = "rules_checkbox";
	$rule_fields['condition_rule_prerequisites']['show'] = true;
	$rule_fields['condition_rule_prerequisites']['tooltip'] = "Leads must also have completed the following rules in order for this rule to be considered.";
	$list_options = rules_get_rules_options();
	$rule_fields['condition_rule_prerequisites']['options'] = $list_options;

	return $rule_fields;
}

add_filter('wpleads_lead_rules_action_gateway','rule_check_rule_prerequisites', 10, 3);

function rule_check_rule_prerequisites($gateway_open, $lead_id, $rule)
{

	$rules = $rule['rule_condition_rule_prerequisites_0'][0];
	$rules = explode(';',$rules);
	$rules = array_filter($rules);
	if (is_array($rules)&&count($rules)>0)
	{
		foreach ($rules as $k=>$rule_id)
		{
			$check_lead = get_post_meta($lead_id, 'rule_executed_'.$rule_id);
			if (!$check_lead)
			{
				$gateway_open = false;
				break;
			}
		}
	}

	return $gateway_open;
}