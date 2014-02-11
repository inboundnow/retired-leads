jQuery(document).ready(function () {

	jQuery(document.body).on('click', '.run-rules' , function(){	
		jQuery('.run-rules').text('Reloading please wait...');
		jQuery('.run-rules').removeClass('.run-rules');
		jQuery('.run-rules').css('cursor','wait');
		/* jQuery('.rules-processing').css('display','inline'); */
		
		jQuery.ajax({
			type: 'POST',
			url: rules_rule.admin_url,
			data: {
				action: 'rules_run_rules_on_all_leads',
				rule_id: rules_rule.rule_id
			},
			success: function(data){
					//alert('Reload page to estimate progress.');
					location.reload();
				   },
			error: function(MLHttpRequest, textStatus, errorThrown){
					//alert(MLHttpRequest+' '+errorThrown+' '+textStatus);							
				}

		});
	});
	
	jQuery(document.body).on('click', '.rule-delete-condition-button' , function(){	
		var this_id = this.id.replace('rule-delete-condition-button-','');
		jQuery('#tabs-rule_condition_' + this_id ).remove();
		jQuery('#rules_main_container_conditions_' + this_id ).remove();		
		jQuery('#rules_container_hidden_input_'+this_id).remove();
		
		var switch_id = jQuery('#ma-st-tabs-0 a:first').attr('rel');
		jQuery('#tabs-rule_condition_' + switch_id ).click();
		
	});


	jQuery('#rule-delete-condition-button-0').show();
	jQuery('#ma-a-add-new-rule-condition').live('click', function() {
		//add new tab
		var tab_html = jQuery('#tabs-rule_condition_0').clone().wrap('<div></div>').parent().html();
		var tab_new_count =  jQuery('.nav-tab-wrapper-conditions').find('.ma-nav-tab').size();
		var tab_new_label_id =  tab_new_count + 1;
		
		//get id of last item in nav contatiner
		var last_rel_id = jQuery('.nav-tab-wrapper-conditions a:last').attr('rel');
		var new_rel_id = parseInt(last_rel_id) + 1;
		//alert(new_rel_id);
		

		
		var new_html = tab_html.replace('_0', '_'+new_rel_id);
		new_html = new_html.replace('-0', '-'+new_rel_id);
		new_html = new_html.replace('rel="0"', 'rel="'+new_rel_id+'"');
		new_html = new_html.replace('Condition 1', 'Condition '+tab_new_label_id);
		jQuery('.nav-tab-wrapper-conditions').append(new_html);
		
		
		//toggle new tab visible and hide other conditions
		jQuery('.rules-tab-display').css('display','none');
		jQuery('.ma-nav-tab').removeClass('nav-tab-special-active');
		jQuery('#tabs-rule_condition_'+new_rel_id).addClass('nav-tab-special-active');
		
		//add new condition content
		var condition_html = jQuery('#rules_main_container_conditions_0').clone().wrap('<div></div>').parent().html();
		var condition_new_html = condition_html.replace(/_0/g , "_"+new_rel_id);
		condition_new_html = condition_new_html.replace(/-0/g, "-"+new_rel_id);
		condition_new_html = condition_new_html.replace(/rel="0"/g, 'rel="'+new_rel_id+'"');
		condition_new_html = "<input type='hidden' name='rule_condition_blocks[]' id='id-open-tab' value='"+new_rel_id+"'>"+condition_new_html;
		jQuery('#rule_conditions_container').append(condition_new_html);		
		
		//toggle the new condition visible
		jQuery('#rules_main_container_conditions_'+new_rel_id).css('display','block');
	});

	//tabb through conditions
	jQuery(document).on('click','.ma-nav-tab' , function() {

		var this_id = this.id.replace('tabs-rule_condition_','');

		jQuery('.rules-tab-display').css('display','none');
		jQuery('.rule-delete-condition-button').css('display','none');
		jQuery('#rules_main_container_conditions_'+this_id).css('display','block');
		jQuery('#rule-delete-condition-button-'+this_id).show();
		jQuery('.ma-nav-tab').removeClass('nav-tab-special-active');
		jQuery('#tabs-rule_condition_'+this_id).addClass('nav-tab-special-active');						
	});
	
	//remove condition
	jQuery(document).on('click','.ma-remove-condition' , function() {
	
		var this_id = this.id.replace('rules-a-remove-rule-condition-','');
		jQuery('#rules_main_container_conditions_'+this_id).remove();	
		jQuery('#rules_main_container_conditions_0').css('display','block');	
		jQuery('#tabs-rule_condition_'+this_id).remove();
		jQuery('#rules_container_hidden_input_'+this_id).remove();
		jQuery('#tabs-rule_condition_0').addClass('nav-tab-special-active');
	});

	jQuery(document).on('change', '.rule_if', function() { 
		var this_id = jQuery(this).val();
		var this_rel = jQuery(this).attr('rel');
		
		//alert(this_id.indexOf("list_specific"));
		if (this_id.indexOf("category_specific") >= 0)
		{
			jQuery('#tr_rule_condition_category'+'_'+this_rel).removeClass('rule-hidden-steps');
		}			
		else
		{
			jQuery('#tr_rule_condition_category'+'_'+this_rel).addClass('rule-hidden-steps');
		}
		
		if (this_id.indexOf("page_views_") >= 0)
		{
			jQuery('#tr_rule_condition_number'+'_'+this_rel).removeClass('rule-hidden-steps');
		}
		else if (this_id.indexOf("page_conversions_") >= 0)
		{
			jQuery('#tr_rule_condition_number'+'_'+this_rel).removeClass('rule-hidden-steps');
		}
		else if (this_id.indexOf("sesions_recorded_") >= 0)
		{
			jQuery('#tr_rule_condition_number'+'_'+this_rel).removeClass('rule-hidden-steps');
		}
		
	});
	
	
	//set default css if rule is pre-defined
	jQuery('.rule_if').each(function(index,value){
		var selectedIF = jQuery(this).find(":selected").val();
		var this_rel = jQuery(this).attr('rel');
		//alert(selectedIF);
		if (selectedIF.indexOf("category_specific") >= 0)
		{
			jQuery('#tr_rule_condition_category'+'_'+this_rel).removeClass('rule-hidden-steps');
		}
		else
		{
			jQuery('#tr_rule_condition_category'+'_'+this_rel).addClass('rule-hidden-steps');
		}
	});
});