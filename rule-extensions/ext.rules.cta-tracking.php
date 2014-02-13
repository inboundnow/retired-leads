<?php


function automation_get_cta_options()
{
	global $post;

	$ctas = get_posts('post_type=wp-call-to-action&posts_per_page=-1');
	if($ctas)
	{
		foreach ( $ctas as $cta )
		{

			if ($post->ID==$cta->ID)
				continue;

			$options[$cta->ID] = $cta->post_title;
		}

	}
	//exit;
	if (!isset($options))
		$options['0'] = "No events available.";


	return $options;
}


add_filter('automation_if_options','automation_add_if_statement_cta_tracking');
function automation_add_if_statement_cta_tracking($automation_if_options)
{
	//$meta_keys = automation_get_meta_keys('lead');
	$automation_if_options['tracked_ctas'] = "Call to Actions Clicked";

	return $automation_if_options;
}

add_filter('automation_fields','automation_add_settings_cta_tracking');
function automation_add_settings_cta_tracking($automation_fields)
{
	$automation_fields['condition_cta_tracking']['label'] = 'CTAs clicked';
	$automation_fields['condition_cta_tracking']['name'] = 'automation_condition_cta_tracking';
	$automation_fields['condition_cta_tracking']['id'] = 'automation_condition_cta_tracking';
	$automation_fields['condition_cta_tracking']['position'] = 'conditions';
	$automation_fields['condition_cta_tracking']['priority'] =  20;
	$automation_fields['condition_cta_tracking']['nature'] = "checkbox";
	$automation_fields['condition_cta_tracking']['style'] = "column";
	$automation_fields['condition_cta_tracking']['class'] = "automation_checkbox";
	$automation_fields['condition_cta_tracking']['show'] = false;
	$automation_fields['condition_cta_tracking']['tooltip'] = "Check which events a lead must have completed in order to qualify for rule processing.";
	$list_options = automation_get_cta_options();
	$automation_fields['condition_cta_tracking']['options'] = $list_options;

	return $automation_fields;
}


add_filter('wpleads_lead_automation_extend_check', 'automation_execute_cta_tracking', 10, 4);
function automation_execute_cta_tracking($conditions_met, $automation_block_ids, $lead_id, $rule)
{
	foreach ($automation_block_ids as $condtion_key => $cid)
	{
		if ($rule['automation_if_'.$cid][0] == 'tracked_ctas')
		{
			$perform_action = false;

			$ctas = $rule['automation_condition_cta_tracking_'.$cid][0];
			$ctas = explode(';',$ctas);
			$ctas = array_filter($ctas);

			if (is_array($ctas)&&count($ctas)>0)
			{
				foreach ($ctas as $k=>$cta_id)
				{

					$leads_triggered = get_post_meta($cta_id, 'leads_triggered', true);
					$leads_triggered = json_decode($leads_triggered,true);

					if (array_key_exists($lead_id,$leads_triggered))
					{
						$perform_action = true;
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


add_action ('automation_js','automation_add_js_cta_tracking');
function automation_add_js_cta_tracking()
{
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function()
	{

		jQuery(document).on('change', '.automation_if', function() {
			var this_id = jQuery(this).val();
			var this_rel = jQuery(this).attr('rel');

			if (this_id.indexOf("tracked_ctas") >= 0)
			{
				jQuery('#tr_automation_condition_cta_tracking'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_number'+'_'+this_rel).addClass('automation-hidden-steps');
			}
			else
			{
				jQuery('#tr_automation_condition_cta_tracking'+'_'+this_rel).addClass('automation-hidden-steps');
			}
		});

		jQuery('.automation_if').each(function(index,value){
			var selectedIF = jQuery(this).find(":selected").val();
			var this_rel = jQuery(this).attr('rel');
			if (selectedIF.indexOf("tracked_ctas") >= 0)
			{
				jQuery('#tr_automation_condition_cta_tracking'+'_'+this_rel).removeClass('automation-hidden-steps');
				jQuery('#tr_automation_condition_number'+'_'+this_rel).addClass('automation-hidden-steps');
			}
			else
			{
				jQuery('#tr_automation_condition_cta_tracking'+'_'+this_rel).addClass('automation-hidden-steps');
			}
		});

	});
	</script>
	<?php
}