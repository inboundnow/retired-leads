<?php

function automation_get_automation_options()
{
	global $post;

	$rules = get_posts('post_type=automation&posts_per_page=-1');
	if($rules)
	{
		foreach ( $rules as $rule )
		{

			if ($post->ID==$automation->ID)
				continue;

			if ($automation->post_status!='publish'||empty($automation->post_title))
				continue;

			$options[$automation->ID] = $automation->post_title;
		}

	}
	//exit;
	if (!isset($options))
		$options['0'] = "No rules available.";


	return $options;
}


add_filter('wpleads_automation_fields','automation_add_settings_automation_prerequisites');
function automation_add_settings_automation_prerequisites($automation_fields)
{
	$automation_fields['condition_automation_prerequisites']['label'] = 'Rule Prerequisites';
	$automation_fields['condition_automation_prerequisites']['name'] = 'automation_condition_automation_prerequisites';
	$automation_fields['condition_automation_prerequisites']['id'] = 'automation_condition_automation_prerequisites';
	$automation_fields['condition_automation_prerequisites']['position'] = 'actions';
	$automation_fields['condition_automation_prerequisites']['priority'] =  69;
	$automation_fields['condition_automation_prerequisites']['nature'] = "checkbox";
	$automation_fields['condition_automation_prerequisites']['style'] = "column";
	$automation_fields['condition_automation_prerequisites']['class'] = "automation_checkbox";
	$automation_fields['condition_automation_prerequisites']['show'] = true;
	$automation_fields['condition_automation_prerequisites']['tooltip'] = "Leads must also have completed the following rules in order for this rule to be considered.";
	$list_options = automation_get_automation_options();
	$automation_fields['condition_automation_prerequisites']['options'] = $list_options;

	return $automation_fields;
}

add_filter('wpleads_lead_automation_action_gateway','automation_check_automation_prerequisites', 10, 3);

function automation_check_automation_prerequisites($gateway_open, $lead_id, $rule)
{

	$rules = $rule['automation_condition_automation_prerequisites_0'][0];
	$rules = explode(';',$rules);
	$rules = array_filter($rules);
	if (is_array($rules)&&count($rules)>0)
	{
		foreach ($rules as $k=>$automation_id)
		{
			$check_lead = get_post_meta($lead_id, 'automation_executed_'.$automation_id);
			if (!$check_lead)
			{
				$gateway_open = false;
				break;
			}
		}
	}

	return $gateway_open;
}