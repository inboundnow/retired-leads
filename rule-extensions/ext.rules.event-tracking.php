<?php


function automation_get_events_options()
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


add_filter('automation_if_options','automation_add_if_statement_event_tracking');
function automation_add_if_statement_event_tracking($automation_if_options)
{
	//$meta_keys = automation_get_meta_keys('lead');
	$automation_if_options['tracked_events'] = "Events tracked";

	return $automation_if_options;
}

add_filter('automation_fields','automation_add_settings_event_tracking');
function automation_add_settings_event_tracking($automation_fields)
{
	$automation_fields['condition_event_tracking_require']['label'] = 'If Leads <b><u>has</u></b> Completed events';
	$automation_fields['condition_event_tracking_require']['name'] = 'automation_condition_event_tracking_require';
	$automation_fields['condition_event_tracking_require']['id'] = 'automation_condition_event_tracking_require';
	$automation_fields['condition_event_tracking_require']['position'] = 'conditions';
	$automation_fields['condition_event_tracking_require']['priority'] =  20;
	$automation_fields['condition_event_tracking_require']['nature'] = "checkbox";
	$automation_fields['condition_event_tracking_require']['style'] = "column";
	$automation_fields['condition_event_tracking_require']['class'] = "automation_checkbox";
	$automation_fields['condition_event_tracking_require']['show'] = false;
	$automation_fields['condition_event_tracking_require']['tooltip'] = "Check which events a lead must have completed in order to qualify for rule processing.";
	$list_options = automation_get_events_options();
	$automation_fields['condition_event_tracking_require']['options'] = $list_options;

	$automation_fields['condition_event_tracking_ignore']['label'] = 'If Leads has <b><u>NOT</u></b> Completed events';
	$automation_fields['condition_event_tracking_ignore']['name'] = 'automation_condition_event_tracking_ignore';
	$automation_fields['condition_event_tracking_ignore']['id'] = 'automation_condition_event_tracking_ignore';
	$automation_fields['condition_event_tracking_ignore']['position'] = 'conditions';
	$automation_fields['condition_event_tracking_ignore']['priority'] =  21;
	$automation_fields['condition_event_tracking_ignore']['nature'] = "checkbox";
	$automation_fields['condition_event_tracking_ignore']['style'] = "column";
	$automation_fields['condition_event_tracking_ignore']['class'] = "automation_checkbox";
	$automation_fields['condition_event_tracking_ignore']['show'] = false;
	$automation_fields['condition_event_tracking_ignore']['tooltip'] = "Check which completed events to ignore when qualifying a lead for this rule .";
	$list_options = automation_get_events_options();
	$automation_fields['condition_event_tracking_ignore']['options'] = $list_options;

	return $automation_fields;
}


add_filter('wpleads_lead_automation_extend_check', 'automation_execute_event_tracking', 10, 4);
function automation_execute_event_tracking($conditions_met, $automation_block_ids, $lead_id, $rule)
{
	foreach ($automation_block_ids as $condtion_key => $cid)
	{
		if ($rule['automation_if_'.$cid][0] == 'tracked_events')
		{
			$perform_action = false;

			$events_require = $rule['automation_condition_event_tracking_require_'.$cid][0];
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

			$events_ignore = $rule['automation_condition_event_tracking_ignore_'.$cid][0];
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


add_action ('automation_js','automation_add_js_event_tracking');
function automation_add_js_event_tracking()
{
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function()
	{

		jQuery(document).on('change', '.automation_if', function() {
			var this_id = jQuery(this).val();
			var this_rel = parseInt(jQuery(this).attr('rel'));

			if (this_id.indexOf("tracked_events") >= 0)
			{
				jQuery('#tr_automation_condition_event_tracking_require'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_event_tracking_ignore'+'_'+this_rel).removeClass('automation-hidden-steps');

				jQuery('#tr_automation_condition_number_'+this_rel).addClass('automation-hidden-steps');
			}
			else
			{
				jQuery('#tr_automation_condition_event_tracking_require'+'_'+this_rel).addClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_event_tracking_ignore'+'_'+this_rel).addClass('automation-hidden-steps');
			}
		});

		jQuery('.automation_if').each(function(index,value){
			var selectedIF = jQuery(this).find(":selected").val();
			var this_rel = jQuery(this).attr('rel');
			if (selectedIF.indexOf("tracked_events") >= 0)
			{
				jQuery('#tr_automation_condition_event_tracking_require'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_event_tracking_ignore'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_number_'+this_rel).addClass('automation-hidden-steps');
			}
			else
			{
				jQuery('#tr_automation_condition_event_tracking_require'+'_'+this_rel).addClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_event_tracking_ignore'+'_'+this_rel).addClass('automation-hidden-steps');
			}
		});

	});
	</script>
	<?php
}