<?php


function rules_get_cta_options()
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


add_filter('rules_if_options','rule_add_if_statement_cta_tracking');
function rule_add_if_statement_cta_tracking($rules_if_options)
{
	//$meta_keys = rules_get_meta_keys('lead');
	$rules_if_options['tracked_ctas'] = "Call to Actions Clicked";
	
	return $rules_if_options;
}

add_filter('rule_fields','rule_add_settings_cta_tracking');
function rule_add_settings_cta_tracking($rule_fields)
{
	$rule_fields['condition_cta_tracking']['label'] = 'CTAs clicked';
	$rule_fields['condition_cta_tracking']['name'] = 'rule_condition_cta_tracking';
	$rule_fields['condition_cta_tracking']['id'] = 'rule_condition_cta_tracking';
	$rule_fields['condition_cta_tracking']['position'] = 'conditions';
	$rule_fields['condition_cta_tracking']['priority'] =  20;
	$rule_fields['condition_cta_tracking']['nature'] = "checkbox";
	$rule_fields['condition_cta_tracking']['style'] = "column";
	$rule_fields['condition_cta_tracking']['class'] = "rules_checkbox";
	$rule_fields['condition_cta_tracking']['show'] = false;
	$rule_fields['condition_cta_tracking']['tooltip'] = "Check which events a lead must have completed in order to qualify for rule processing.";
	$list_options = rules_get_cta_options();
	$rule_fields['condition_cta_tracking']['options'] = $list_options;

	return $rule_fields;
}


add_filter('wpleads_lead_rules_extend_check', 'rule_execute_cta_tracking', 10, 4);
function rule_execute_cta_tracking($conditions_met, $rule_block_ids, $lead_id, $rule)
{
	foreach ($rule_block_ids as $condtion_key => $cid)
	{
		if ($rule['rule_if_'.$cid][0] == 'tracked_ctas') 
		{
			$perform_action = false;
			
			$ctas = $rule['rule_condition_cta_tracking_'.$cid][0];
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


add_action ('rules_rule_js','rule_add_js_cta_tracking');
function rule_add_js_cta_tracking()
{
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function() 
	{		
 
		jQuery(document).on('change', '.rule_if', function() { 
			var this_id = jQuery(this).val();
			var this_rel = jQuery(this).attr('rel');

			if (this_id.indexOf("tracked_ctas") >= 0)
			{
				jQuery('#tr_rule_condition_cta_tracking'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_number'+'_'+this_rel).addClass('rule-hidden-steps');
			}
			else
			{
				jQuery('#tr_rule_condition_cta_tracking'+'_'+this_rel).addClass('rule-hidden-steps');
			}
		});
		
		jQuery('.rule_if').each(function(index,value){
			var selectedIF = jQuery(this).find(":selected").val();
			var this_rel = jQuery(this).attr('rel');
			if (selectedIF.indexOf("tracked_ctas") >= 0)
			{
				jQuery('#tr_rule_condition_cta_tracking'+'_'+this_rel).removeClass('rule-hidden-steps');
				jQuery('#tr_rule_condition_number'+'_'+this_rel).addClass('rule-hidden-steps');
			}
			else
			{
				jQuery('#tr_rule_condition_cta_tracking'+'_'+this_rel).addClass('rule-hidden-steps');
			}
		});
		
	});
	</script>
	<?php
}