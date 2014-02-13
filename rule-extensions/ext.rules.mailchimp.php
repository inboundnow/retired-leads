<?php

//INCLUDE MAILCHIMP WRAPPER
include_once('mailchimp-api-master/MailChimp.class.php');

if (is_admin())
{
	/*SETUP GLOBAL SETTINGS FOR MAILCHIMP*/
	$tab_slug = 'rules-main';
	$automation_global_settings[$tab_slug]['options'][] = automation_add_option($tab_slug,"text","mailchimp_apikey","enter api key here","Mailchimp API Key","Enter Mailchimp API Key to power extension: http://kb.mailchimp.com/article/where-can-i-find-my-api-key.", $options);
	/*SETUP END*/
	add_filter('automation_define_global_settings', 'automation_mailchimp_add_global_settings' , 10 , 1 );
	function automation_mailchimp_add_global_settings($automation_global_settings)
	{
		$tab_slug = 'rules-main';
		$automation_global_settings[$tab_slug]['settings'][] =	array(
																'id'  => 'mailchimp_apikey',
																'label' => 'Mailchimp API Key',
																'description' => "Enter Mailchimp API Key to power extension: http://kb.mailchimp.com/article/where-can-i-find-my-api-key.",
																'type'  => 'text'
															);

		return $automation_global_settings;

	}
}
function automation_get_mailchimp_lists()
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

add_filter('automation_fields','automation_add_settings_mailchimp');
function automation_add_settings_mailchimp($automation_fields)
{
	$automation_fields['mailchimp_lists']['label'] = 'Add to Mailchimp Lists';
	$automation_fields['mailchimp_lists']['name'] = 'automation_action_mailchimp_lists';
	$automation_fields['mailchimp_lists']['id'] = 'automation_action_mailchimp_lists';
	$automation_fields['mailchimp_lists']['position'] = 'actions';
	$automation_fields['mailchimp_lists']['priority'] =  51;
	$automation_fields['mailchimp_lists']['nature'] = "checkbox";
	$automation_fields['mailchimp_lists']['style'] = "column";
	$automation_fields['mailchimp_lists']['class'] = "automation_checkbox";
	$automation_fields['mailchimp_lists']['show'] = true;
	$automation_fields['mailchimp_lists']['tooltip'] = "Select which mailchimp lists to sort lead into for a successful rule.";

	$list_options = automation_get_mailchimp_lists();


	$automation_fields['mailchimp_lists']['options'] = $list_options;

	$automation_fields['mailchimp_lists_unsubscribe']['label'] = 'Remove from Mailchimp Lists';
	$automation_fields['mailchimp_lists_unsubscribe']['name'] = 'automation_action_mailchimp_lists_unsubscribe';
	$automation_fields['mailchimp_lists_unsubscribe']['id'] = 'automation_action_mailchimp_lists_unsubscribe';
	$automation_fields['mailchimp_lists_unsubscribe']['position'] = 'actions';
	$automation_fields['mailchimp_lists_unsubscribe']['priority'] =  52;
	$automation_fields['mailchimp_lists_unsubscribe']['nature'] = "checkbox";
	$automation_fields['mailchimp_lists_unsubscribe']['style'] = "column";
	$automation_fields['mailchimp_lists_unsubscribe']['class'] = "automation_checkbox";
	$automation_fields['mailchimp_lists_unsubscribe']['show'] = true;
	$automation_fields['mailchimp_lists_unsubscribe']['tooltip'] = "Select which mailchimp lists to remove lead from if present.";
	$automation_fields['mailchimp_lists_unsubscribe']['options'] = $list_options;

	return $automation_fields;
}

add_action('automation_cron_perform_action_post','automation_run_automation_mailchimp', 10, 2);
function automation_run_automation_mailchimp($lead_id, $rule)
{
	//subscribe to mc lists
	$lists = $rule['automation_action_mailchimp_lists_0'][0];
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
	$lists = $rule['automation_action_mailchimp_lists_unsubscribe_0'][0];
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