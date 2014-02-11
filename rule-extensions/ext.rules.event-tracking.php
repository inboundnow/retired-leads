<?php


function rules_get_events_options()
{
	global $post;
	
	$events = get_posts('post_type=tracking-event&posts_per_page=-1');
	if($events)
	{ 
		foreach ( $events as $event )
		{
			
			if ($post->ID==$event->ID)
				continue;
				
			$options[$event->ID] = $event->post_title;
		}
		
	}
	//exit;
	if (!isset($options)) 
		$options['0'] = "No events available.";
	
	
	return $options;	
}


add_filter('rules_if_options','rule_add_if_statement_event_tracking');
function rule_add_if_statement_event_tracking($rules_if_options)
{
	//$meta_keys = rules_get_meta_keys('lead');
	$rules_if_options['tracked_events'] = "Events tracked";
	
	return $rules_if_options;
}

add_filter('rule_fields','rule_add_settings_event_tracking');
function rule_add_settings_event_tracking($rule_fields)
{
	$rule_fields['condition_event_tracking_require']['label'] = 'If Leads <b><u>has</u></b> Completed events';
	$rule_fields['condition_event_tracking_require']['name'] = 'rule_condition_event_tracking_require';
	$rule_fields['condition_event_tracking_require']['id'] = 'rule_condition_event_tracking_require';
	$rule_fields['condition_event_tracking_require']['position'] = 'conditions';
	$rule_fields['condition_event_tracking_require']['priority'] =  20;
	$rule_fields['condition_event_tracking_require']['nature'] = "checkbox";
	$rule_fields['condition_event_tracking_require']['style'] = "column";
	$rule_fields['condition_event_tracking_require']['class'] = "rules_checkbox";
	$rule_fields['condition_event_tracking_require']['show'] = false;
	$rule_fields['condition_event_tracking_require']['tooltip'] = "Check which events a lead must have completed in order to qualify for rule processing.";
	$list_options = rules_get_events_options();
	$rule_fields['condition_event_tracking_require']['options'] = $list_options;

	$rule_fields['condition_event_tracking_ignore']['label'] = 'If Leads has <b><u>NOT</u></b> Completed events';
	$rule_fields['condition_event_tracking_ignore']['name'] = 'rule_condition_event_tracking_ignore';
	$rule_fields['condition_event_tracking_ignore']['id'] = 'rule_condition_event_tracking_ignore';
	$rule_fields['condition_event_tracking_ignore']['position'] = 'conditions';
	$rule_fields['condition_event_tracking_ignore']['priority'] =  21;
	$rule_fields['condition_event_tracking_ignore']['nature'] = "checkbox";
	$rule_fields['condition_event_tracking_ignore']['style'] = "column";
	$rule_fields['condition_event_tracking_ignore']['class'] = "rules_checkbox";
	$rule_fields['condition_event_tracking_ignore']['show'] = false; 
	$rule_fields['condition_event_tracking_ignore']['tooltip'] = "Check which completed events to ignore when qualifying a lead for this rule .";
	$list_options = rules_get_events_options();
	$rule_fields['condition_event_tracking_ignore']['options'] = $list_options;
	
	return $rule_fields;
}


add_filter('wpleads_lead_rules_extend_check', 'rule_execute_event_tracking', 10, 4);
function rule_execute_event_tracking($conditions_met, $rule_block_ids, $lead_id, $rule)
{
	foreach ($rule_block_ids as $condtion_key => $cid)
	{
		if ($rule['rule_if_'.$cid][0] == 'tracked_events') 
		{
			$perform_action = false;
			
			$events_require = $rule['rule_condition_event_tracking_require_'.$cid][0];
			$events_require = explode(';',$events_require);
			$events_require = array_filter($events_require);

			if (is_array($events_require)&&count($events_require)>0)
			{
				foreach ($events_require as $k=>$event_id)
				{
					
					$leads_triggered = get_post_meta($event_id, 'leads_triggered', true);
					$leads_triggered = json_decode($leads_triggered,true);
					
					if (array_key_exists($lead_id,$leads_triggered))
					{
						echo  "Required Tracked Event Discovered: Event ".$event_id."  \r\n";
						$perform_action = true;
						break;
					}
					
				}
			}
			
			$events_ignore = $rule['rule_condition_event_tracking_ignore_'.$cid][0];
			$events_ignore = explode(';',$events_ignore);
			$events_ignore = array_filter($events_ignore);

			if (is_array($events_ignore)&&count($events_ignore)>0)
			{
				foreach ($events_ignore as $k=>$event_id)
				{			
					$leads_triggered = get_post_meta($event_id, 'leads_triggered', true);
					$leads_triggered = json_decode($leads_triggered,true);
					
					if (array_key_exists($lead_id,$leads_triggered))
					{
						echo  "Ignored Tracked Event Discovered: Event ".$event_id."  \r\n";
						$perform_action = false;
						break;
					}
					
				}
			}
			
			if ($perform_action)
			{
				$conditions_met[$cid] = true;
			}				
		}
	}
	
	return $conditions_met;
}


add_action ('rules_rule_js','rule_add_js_event_tracking');
function rule_add_js_event_tracking()
{
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function() 
	{		

		jQuery(document).on('change', '.rule_if', function() { 
			var this_id = jQuery(this).val();
			var this_rel = parseInt(jQuery(this).attr('rel'));

			if (this_id.indexOf("tracked_events") >= 0)
			{
				jQuery('#tr_rule_condition_event_tracking_require'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_event_tracking_ignore'+'_'+this_rel).removeClass('rule-hidden-steps');
				
				jQuery('#tr_rule_condition_number_'+this_rel).addClass('rule-hidden-steps');
			}
			else
			{
				jQuery('#tr_rule_condition_event_tracking_require'+'_'+this_rel).addClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_event_tracking_ignore'+'_'+this_rel).addClass('rule-hidden-steps');
			}
		});
		
		jQuery('.rule_if').each(function(index,value){
			var selectedIF = jQuery(this).find(":selected").val();
			var this_rel = jQuery(this).attr('rel');
			if (selectedIF.indexOf("tracked_events") >= 0)
			{
				jQuery('#tr_rule_condition_event_tracking_require'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_event_tracking_ignore'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_number_'+this_rel).addClass('rule-hidden-steps');
			}
			else
			{
				jQuery('#tr_rule_condition_event_tracking_require'+'_'+this_rel).addClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_event_tracking_ignore'+'_'+this_rel).addClass('rule-hidden-steps');				
			}
		});
		
	});
	</script>
	<?php
}