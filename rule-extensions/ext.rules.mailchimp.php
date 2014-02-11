<?php

//INCLUDE MAILCHIMP WRAPPER
include_once('mailchimp-api-master/MailChimp.class.php');

if (is_admin())
{
	/*SETUP GLOBAL SETTINGS FOR MAILCHIMP*/
	$tab_slug = 'rules-main';
	$rules_global_settings[$tab_slug]['options'][] = rules_add_option($tab_slug,"text","mailchimp_apikey","enter api key here","Mailchimp API Key","Enter Mailchimp API Key to power extension: http://kb.mailchimp.com/article/where-can-i-find-my-api-key.", $options);
	/*SETUP END*/
	add_filter('rules_define_global_settings', 'rules_mailchimp_add_global_settings' , 10 , 1 );
	function rules_mailchimp_add_global_settings($rules_global_settings)
	{
		$tab_slug = 'rules-main';		
		$rules_global_settings[$tab_slug]['settings'][] =	array(
																'id'  => 'mailchimp_apikey',
																'label' => 'Mailchimp API Key',
																'description' => "Enter Mailchimp API Key to power extension: http://kb.mailchimp.com/article/where-can-i-find-my-api-key.",
																'type'  => 'text'
															);
															
		return $rules_global_settings;
	
	}
}
function rules_get_mailchimp_lists()
{
	$apikey = get_option('rules-main-mailchimp_apikey' , true);

	if (!$apikey)
		return array();
	
	$MailChimp = new MailChimp_MA($apikey);
	$lists = $MailChimp->call('lists/list');
	//print_r($lists);
	
	if ( isset($lists['total']) && $lists['total'] >0 )
	{
		foreach ( $lists['data'] as $list )
		{
			
				
			$options[$list['id']] = $list['name'];
		}
	}
	
	if (!isset($options))
		$options['0'] = "No lists discovered.";
	
	return $options;
}

add_filter('rule_fields','rule_add_settings_mailchimp');
function rule_add_settings_mailchimp($rule_fields)
{
	$rule_fields['mailchimp_lists']['label'] = 'Add to Mailchimp Lists';
	$rule_fields['mailchimp_lists']['name'] = 'rule_action_mailchimp_lists';
	$rule_fields['mailchimp_lists']['id'] = 'rule_action_mailchimp_lists';
	$rule_fields['mailchimp_lists']['position'] = 'actions';
	$rule_fields['mailchimp_lists']['priority'] =  51; 
	$rule_fields['mailchimp_lists']['nature'] = "checkbox";
	$rule_fields['mailchimp_lists']['style'] = "column";
	$rule_fields['mailchimp_lists']['class'] = "rules_checkbox";
	$rule_fields['mailchimp_lists']['show'] = true;
	$rule_fields['mailchimp_lists']['tooltip'] = "Select which mailchimp lists to sort lead into for a successful rule.";

	$list_options = rules_get_mailchimp_lists();
	
		
	$rule_fields['mailchimp_lists']['options'] = $list_options;
	
	$rule_fields['mailchimp_lists_unsubscribe']['label'] = 'Remove from Mailchimp Lists';
	$rule_fields['mailchimp_lists_unsubscribe']['name'] = 'rule_action_mailchimp_lists_unsubscribe';
	$rule_fields['mailchimp_lists_unsubscribe']['id'] = 'rule_action_mailchimp_lists_unsubscribe';
	$rule_fields['mailchimp_lists_unsubscribe']['position'] = 'actions';
	$rule_fields['mailchimp_lists_unsubscribe']['priority'] =  52; 
	$rule_fields['mailchimp_lists_unsubscribe']['nature'] = "checkbox";
	$rule_fields['mailchimp_lists_unsubscribe']['style'] = "column";
	$rule_fields['mailchimp_lists_unsubscribe']['class'] = "rules_checkbox";
	$rule_fields['mailchimp_lists_unsubscribe']['show'] = true;
	$rule_fields['mailchimp_lists_unsubscribe']['tooltip'] = "Select which mailchimp lists to remove lead from if present.";
	$rule_fields['mailchimp_lists_unsubscribe']['options'] = $list_options;

	return $rule_fields;
}

add_action('rules_cron_perform_action_post','rule_run_rule_mailchimp', 10, 2);
function rule_run_rule_mailchimp($lead_id, $rule)
{
	//subscribe to mc lists
	$lists = $rule['rule_action_mailchimp_lists_0'][0];
	$lists = explode(';',$lists);
	$lists = array_filter($lists);

	if (is_array($lists)&&count($lists)>0)
	{
		$apikey = get_option('rules-main-mailchimp_apikey' , true);
		
		if (!$apikey)
			return;
			
		$MailChimp = new MailChimp_MA($apikey);
		$lead_first_name = get_post_meta($lead_id,'wpleads_first_name', true);
		$lead_last_name =  get_post_meta($lead_id,'wpleads_last_name', true);
		$lead_email =  get_post_meta($lead_id,'wpleads_email_address', true);
		
		foreach ($lists as $k=>$list_id)
		{
			$result = $MailChimp->call('lists/subscribe', array(
                'id'                => $list_id,
                'email'             => array('email'=>$lead_email),
                'merge_vars'        => array('FNAME'=>$lead_first_name, 'LNAME'=>$lead_last_name),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
            ));
		}
	}
	
	//unsubscribe from mc lists
	$lists = $rule['rule_action_mailchimp_lists_unsubscribe_0'][0];
	$lists = explode(';',$lists);
	$lists = array_filter($lists);

	if (is_array($lists)&&count($lists)>0)
	{
		$apikey = get_option('rules-main-mailchimp_apikey' , true);
		
		if (!$apikey)
			return;
			
		$MailChimp = MailChimp_MA($apikey);
		$lead_first_name = get_post_meta($lead_id,'wpleads_first_name', true);
		$lead_last_name =  get_post_meta($lead_id,'wpleads_last_name', true);
		$lead_email =  get_post_meta($lead_id,'wpleads_email_address', true);
		
		foreach ($lists as $k=>$list_id)
		{
			$result = $MailChimp->call('lists/unsubscribe', array(
                'id'                => $list_id,
                'email'             => array('email'=>$lead_email),
                'merge_vars'        => array('FNAME'=>$lead_first_name, 'LNAME'=>$lead_last_name),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
            ));
		}
	}
					
}