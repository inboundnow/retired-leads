<?php

/* Define core rules */
function wpleads_lead_rules_get_fields()
{

	$rule_fields['rules_if']['label'] = 'IF condition';
	$rule_fields['rules_if']['name'] = 'rule_if';
	$rule_fields['rules_if']['id'] = 'rule_if';
	$rule_fields['rules_if']['position'] = 'conditions';
	$rule_fields['rules_if']['priority'] = 10;
	$rule_fields['rules_if']['nature'] = "dropdown";
	$rule_fields['rules_if']['class'] = "rules_dropdown";
	$rule_fields['rules_if']['show'] = true;
	$rule_fields['rules_if']['tooltip'] = "This condition allows us to set the primary criteria for lead sorting or points awarding.";
	$rules_if_options = array(
							'page_views_general'=>'Visitor views any page',
							'page_views_category_specific'=>'Visitor views category specific page',
							'page_conversions_general'=>'Visitor converts on any page',
							'page_conversions_category_specific'=>'Visitor converts on category specific page',
							//'sessions_recorded'=>'Total site browsing sessions equals',
							'rules_executed'=>'Total number of successful rule matches is at least'
							);

	$rules_if_options = apply_filters('rules_if_options',$rules_if_options);

	$rule_fields['rules_if']['options'] = $rules_if_options;

	$rule_fields['condition_count']['label'] = '# Action Requirments';
	$rule_fields['condition_count']['name'] = 'rule_condition_number';
	$rule_fields['condition_count']['id'] = 'rule_condition_number';
	$rule_fields['condition_count']['position'] = 'conditions';
	$rule_fields['condition_count']['priority'] = 20;
	$rule_fields['condition_count']['nature'] = "text";
	$rule_fields['condition_count']['class'] = "rules_text_input";
	$rule_fields['condition_count']['tooltip'] = "This condition sets the standard that the IF statement above must achieve before a rule is executed.";
	$rule_fields['condition_count']['show'] = true;


	$rule_fields['condition_category']['label'] = 'Category Condition';
	$rule_fields['condition_category']['name'] = 'rule_condition_category';
	$rule_fields['condition_category']['id'] = 'rule_condition_category';
	$rule_fields['condition_category']['position'] = 'conditions';
	$rule_fields['condition_category']['priority'] = 30;
	$rule_fields['condition_category']['nature'] = "dropdown";
	$rule_fields['condition_category']['class'] = "rules_dropdown";
	$rule_fields['condition_category']['show'] = false;
	$rule_fields['condition_category']['tooltip'] = "When an IF statment requires category specific reader behavior this dropdown helps us target our category of interest.";
	$categories_option_array = rules_get_categories_array();
	$rule_fields['condition_category']['options'] = $categories_option_array;

	$rule_fields['condition_meta_rule_check']['label'] = 'Rule Limitation';
	$rule_fields['condition_meta_rule_check']['name'] = 'rule_condition_rule_check';
	$rule_fields['condition_meta_rule_check']['id'] = 'rule_condition_rule_check';
	$rule_fields['condition_meta_rule_check']['default'] = 'on';
	$rule_fields['condition_meta_rule_check']['position'] = 'actions';
	$rule_fields['condition_meta_rule_check']['priority'] = 40;
	$rule_fields['condition_meta_rule_check']['nature'] = "checkbox";
	$rule_fields['condition_meta_rule_check']['class'] = "rules_dropdown";
	$rule_fields['condition_meta_rule_check']['show'] = true;
	$rule_fields['condition_meta_rule_check']['tooltip'] = "To protect rule actions from firing multiple times on the same user turn this setting to on. ";
	$list_options = array('on'=>'Only run rule on lead once.');

	$rule_fields['condition_meta_rule_check']['options'] = $list_options;

	$rule_fields['condition_list_add']['label'] = 'Add to WordPress Lists';
	$rule_fields['condition_list_add']['name'] = 'rule_condition_list_add';
	$rule_fields['condition_list_add']['id'] = 'rule_condition_list_add';
	$rule_fields['condition_list_add']['position'] = 'actions';
	$rule_fields['condition_list_add']['priority'] = 50;
	$rule_fields['condition_list_add']['nature'] = "checkbox";
	$rule_fields['condition_list_add']['class'] = "rules_dropdown";
	$rule_fields['condition_list_add']['show'] = true;
	$rule_fields['condition_list_add']['tooltip'] = "When all rule conditions are met by a lead then sort the lead into these WordPress lists. ";
	$list_options = wpleads_get_lead_lists_as_array();
	$rule_fields['condition_list_add']['options'] = $list_options;

	$rule_fields['condition_list_remove']['label'] = 'Remove from WordPress Lists';
	$rule_fields['condition_list_remove']['name'] = 'rule_condition_list_remove';
	$rule_fields['condition_list_remove']['id'] = 'rule_condition_list_remove';
	$rule_fields['condition_list_remove']['position'] = 'actions';
	$rule_fields['condition_list_remove']['priority'] = 51;
	$rule_fields['condition_list_remove']['nature'] = "checkbox";
	$rule_fields['condition_list_remove']['class'] = "rules_dropdown";
	$rule_fields['condition_list_remove']['show'] = true;
	$rule_fields['condition_list_remove']['tooltip'] = "When all rule conditions are met by a lead then remove the lead from these WordPress lists. ";
	$rule_fields['condition_list_remove']['options'] = $list_options;


	$rule_fields['condition_points']['label'] = 'Award Points';
	$rule_fields['condition_points']['name'] = 'rule_condition_points';
	$rule_fields['condition_points']['id'] = 'rule_condition_points';
	$rule_fields['condition_points']['position'] = 'actions';
	$rule_fields['condition_points']['priority'] = 60;
	$rule_fields['condition_points']['nature'] = "text";
	$rule_fields['condition_points']['class'] = "rules_input";
	$rule_fields['condition_points']['show'] = true;
	$rule_fields['condition_points']['tooltip'] = "When all rule conditions are met by a lead then award the user this many points. You may use - symbols before an interger for point removal.";

	$rule_fields = apply_filters('wpleads_lead_rule_fields',$rule_fields);

	foreach ($rule_fields as $key =>$array)
	{
		$rule_array_prioritized[$key] = $array['priority'];
	}


	asort($rule_array_prioritized);
	foreach ($rule_array_prioritized as $key => $value)
	{
		$rule_fields_final[$key] = $rule_fields[$key];
	}

	return $rule_fields_final;
}



add_action('add_meta_boxes', 'wpleads_lead_lists_add_metaboxes');
function wpleads_lead_lists_add_metaboxes() {
	global $post;

	/* main rule block */
	add_meta_box(
		'rules_metabox_main', // $id
		__( 'Rule Overview', 'rule_overview' ),
		'rules_display_metabox_main', // $callback
		'rule', // $page
		'normal', // $context
		'high'); // $priority

	/* sidebar */
	add_meta_box(
	'rules_metabox_run_tools_sidebar',
	__( "Rule Tools", 'ma' ),
	'rules_metabox_sidebar_tools',
	'rule' ,
	'side',
	'low' );
}


function rules_display_metabox_main() {
	//echo 1; exit;
	global $post;
	global $wpdb;

	do_action('rules_rule_js');

	$rule_fields = wpleads_lead_rules_get_fields();

	//define tabs
	$tabs[] = array('id'=>'rule_condition_0','label'=>'Condition 1');


	//define open tab
	$active_tab = 'rule_condition_0';
	if (isset($_REQUEST['open-tab']))
	{
		$active_tab = $_REQUEST['open-tab'];
	}


	$rule_activation_status = get_post_meta($post->ID,'rule_active',true);
	$rule_conditions_nature = get_post_meta($post->ID,'rules_conditions_nature',true);

	$rule_block_ids = get_post_meta($post->ID,'rule_condition_blocks',true);
	$rule_block_ids = json_decode($rule_block_ids,true);

	if (!$rule_block_ids)
		$rule_block_ids = array('0'=>'0');
	//print_r($rule_block_ids);

	//prepare values for condition blocks
	foreach ($rule_block_ids as $key=>$id)
	{
		foreach ($rule_fields as $key=>$field)
		{
			if ($field['position']=='conditions')
			{
				$rule_fields[$key]['value'][$id] = get_post_meta( $post->ID , $rule_fields[$key]['name']."_".$id ,true );
			}
		}
	}

	//prepare values for action settings
	foreach ($rule_fields as $key=>$field)
	{
		if ($field['position']=='actions')
		{
			$rule_fields[$key]['value'][0] = get_post_meta( $post->ID , $rule_fields[$key]['name']."_0" ,true );
			//$rule_fields[$key]['value'][0] = $rule_fields[$key]['default'];
		}
	}

	//print_r($rule_fields);exit;
	$rule_fields = apply_filters('rule_fields',$rule_fields);

	// Use nonce for verification
	echo "<input type='hidden' name='rules_custom_fields_nonce' value='".wp_create_nonce('rules-nonce')."' />";
	echo "<input type='hidden' name='open-tab' id='id-open-tab' value='{$active_tab}'>";

	?>
	<div class='rules-div-add-new-rule-condition'>
		<select class="rules_dropdown rule_active" id="rule_active" name="rule_active">
			<option value="active" <?php if ($rule_activation_status=='active') echo "selected"; ?>>Activate Rule</option>
			<option value="inactive" <?php if ($rule_activation_status=='inactive') echo "selected"; ?>>Deactivate Rule</option>
		</select>
		<select name='rules_conditions_nature'>
			<option value='match_all' <?php if ($rule_conditions_nature=='match_all') echo "selected"; ?>>Match all conditions.</option>
			<option value='match_any' <?php if ($rule_conditions_nature=='match_any') echo "selected"; ?>>Match any conditions.</option>
		</select>
		<a id="ma-a-add-new-rule-condition"  name='' class="button">Add New Condtion</a>
	</div>
	<div class="metabox-holder split-test-ui">

		<div class="meta-box-sortables ui-sortable">

			<h2 id="ma-st-tabs-0" class="nav-tab-wrapper nav-tab-wrapper-conditions">
				<?php
				$condition_count = count($rule_block_ids);
				foreach ($rule_block_ids as $key=>$cid)
				{
					$i = $key+1;
					?>
					<a  id='tabs-rule_condition_<?php echo $cid; ?>' rel='<?php echo $cid; ?>' class="ma-nav-tab nav-tab nav-tab-special<?php echo $key > 0 ? '-inactive' : '-active'; ?>">
						<?php _e( "Condition {$i}", INBOUNDNOW_LABEL ); ?>
					</a>
					<?php

					if ($condition_count>1)
					{
					?>
					<span class='rule-delete-condition-button' id='rule-delete-condition-button-<?php echo $cid; ?>'>
						<img title="<?php _e('Remove Condition', INDBOUNDNOW_LABEL );?>" src='<?php echo WPL_URL.'/images/delete.png'; ?>'>
					</span>
					<?php
					}
				}
				?>
			</h2>

			<div id='rule_conditions_container'>



				<?php
				// Custom Before and After Hook here for custom fields shown on main view page

				rules_render_setting($rule_fields,'conditions','rules-tab-display', true);


				?>
			</div>
			<div class='rules-rules-actions-block'>
				<h2 id="ma-st-tabs-0" class="nav-tab-wrapper nav-tab-wrapper-actions">
						<a  id='tabs-actions' style='padding-left:14px;'><?php _e('Actions',INBOUNNOW_LABEL); ?>:</a>
				</h2>
				<?php
				// Custom Before and After Hook here for custom fields shown on main view page

				rules_render_setting($rule_fields,'actions',' ');

				?>
			</div>
			<div style='display:none'>
			</div>

		<?php
		do_action('rules_print_lead_tab_sections');
		?>

		</div><!-- end .meta-box-sortables -->
	</div><!-- end .metabox-holder -->
	<?php
}

function rules_metabox_sidebar_tools() {
	global $post;

	$rules_queue = get_option('rules_queue');

	$rules_queue = json_decode($rules_queue, true);

	if ( is_array($rules_queue) && array_key_exists( $post->ID , $rules_queue))
	{
		$style = "display:inline";
		$text = __('Processing...',INBOUNNOW_LABEL);

		foreach ($rules_queue as $rule_id => $batches)
		{
			$batch_count[$rule_id] = count($batches);
		}

		$est_time_remaining = 0;
		foreach ($batch_count as $rule_id => $count)
		{
			$time_calc = $count * 2;
			$est_time_remaining = $est_time_remaining + $time_calc;
		}
	}
	else
	{
		$style = "display:none";
		$text = __( "Run rule on all leads." , INBOUNNOW_LABEL );
	}
	?>

	<?php

		if ($est_time_remaining)
		{
			_e("<small>Estimated Time Remaining: {$est_time_remaining} minutes. Reload page to recalulate time remaining. <a href='?wpleads_lead_rules_run_manual_cron=1' title='Click here to manually run back-end processing and speed up processing time by 2 minutes.' target='_blank'>Force Processing</a></small>" , INBOUNNOW_LABEL);
		}

	?>

	<a name='#rules_run_rules' id='run-rules' class='run-rules button button-large'><?php echo $text; ?></a>
	<span class='rules-processing' style='<?php echo $style; ?>;'>
		<img src='<?php echo WPL_URL.'/images/process_b.gif'; ?>' width='25' height='25' style='vertical-align: middle;padding-left:84px;cursor:pointer'>
	</span>


	<?php
	do_action('rules_metabox_sidebar_rule_tools', $post);
}



/* display metabox setting */

function rules_render_setting($fields, $position=null, $container_class = '', $declare_rule_block = false)
{
	global $post;
	//print_r($fields);
	//echo $post->ID;exit;
	$rule_block_ids = get_post_meta($post->ID,'rule_condition_blocks',true);
	$rule_block_ids = json_decode($rule_block_ids,true);

	if (!$rule_block_ids||$position=='actions')
		$rule_block_ids = array('0'=>'0');


	foreach ($rule_block_ids as $key => $cid)
	{

		if ($declare_rule_block)
			echo "<input type='hidden' name='rule_condition_blocks[]' id='rules_container_hidden_input_{$cid}' value='{$cid}'>";

		($key>0) ? $style = 'display:none;' : $style = '';
			echo "<table id='rules_main_container_{$position}_{$cid}' class='{$container_class}' style='{$style}'>";


		foreach ($fields as $field_key => $field_array)
		{
			if ($field_array['position']!=$position)
				continue;



			$id = strtolower($field_array['name']);

			$id = $id."_{$cid}";

			(!$field_array['show']) ? $hide_toggle = "rule-hidden-steps" : $hide_toggle = "";

			echo '<tr class="'.$hide_toggle.'" id="tr_'.$id.'">
				<th class="ma-th" ><label for="'.$id.'">'.__( $field_array['label'] , INBOUNDNOW_LABEL ).':</label></th>
				<td class="ma-td" id="ma-td-'.$id.'">';
			switch(true) {
				case strstr($field_array['nature'],'textarea'):
					$parts = explode('-',$field_array['nature']);
					(isset($parts[1])) ? $rows= $parts[1] : $rows = '10';
					echo '<textarea name="'.$id.'" rel="'.$cid.'" id="'.$id.'" rows='.$rows.'" class="'.$field_array['class'].' '.$field_array['name'].'" >'.$field_array['value'][$cid].'</textarea>';
					echo '<div class="rules_tooltip tool_textarea" title="'.$field_array['tooltip'].'"></div>';
					break;
				case strstr($field_array['nature'],'text'):
					$parts = explode('-',$field_array['nature']);
					echo '<input type="text" name="'.$id.'" id="'.$id.'"  rel="'.$cid.'" value="'.$field_array['value'][$cid].'" class="'.$field_array['class'].' '.$field_array['name'].'"/>';
					echo '<div class="rules_tooltip tool_text" title="'.$field_array['tooltip'].'"></div>';
					break;
				// wysiwyg
				case strstr($field_array['nature'],'wysiwyg'):
					wp_editor( $field_array['value'][$cid], $id, $settings = array() );
					echo '<div class="rules_tooltip tool_wysiwyg" title="'.$field_array['tooltip'].'"></div>';
					break;
				// checkbox
				case strstr($field_array['nature'],'checkbox'):

					if (strstr($field_array['value'][$cid],';'))
					{
						$field_array['value'][$cid] = explode(';',$field_array['value'][$cid]);
					}

					if (!is_array($field_array['value'][$cid])){
						$field_array['value'][$cid] = array($field_array['value'][$cid]);
					}

					echo "<table class='rules_check_box_table'>";


					foreach ($field_array['options'] as $value=>$field_array['label']) {

							echo "<tr>";

							echo '<td><input type="checkbox"  rel="'.$cid.'" name="'.$id.'[]" id="'.$id.'" value="'.$value.'" ',in_array($value,$field_array['value'][$cid]) ? ' checked="checked"' : '','  class="'.$field_array['class'].' '.$field_array['name'].'"/>';
							echo '<label for="'.$value.'">&nbsp;&nbsp;'.$field_array['label'].'</label></td>';


							echo "</tr>";
					}
					echo "</table>";
					echo '<div class="rules_tooltip tool_checkbox" title="'.$field_array['tooltip'].'"></div>';
					break;
				// radio
				case strstr($field_array['nature'],'radio'):
					foreach ($field_array['options'] as $value=>$field_array['label']) {
						//echo $field_array['value'][$cid].":".$value;
						//echo "<br>";
						echo '<input type="radio" name="'.$id.'" id="'.$id.'"  rel="'.$cid.'" value="'.$value.'" ',$field_array['value'][$cid]==$value ? ' checked="checked"' : '','  class="'.$field_array['class'].' '.$field_array['name'].'" />';
						echo '<label for="'.$value.'">&nbsp;&nbsp;'.$field_array['label'].'</label> &nbsp;&nbsp;&nbsp;&nbsp;';
					}
					echo '<div class="rules_tooltip tool_radio" title="'.$field_array['tooltip'].'"></div>';
					break;
				// select
				case $field_array['nature'] == 'dropdown':
					echo '<select name="'.$id.'" id="'.$id.'"  rel="'.$cid.'" class="'.$field_array['class'].' '.$field_array['name'].'" >';
					foreach ($field_array['options'] as $value=>$field_array['label']) {
						echo '<option', $field_array['value'][$cid] == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$field_array['label'].'</option>';
					}
					echo '</select>';
					echo '<div class="rules_tooltip tool_dropdown" title="'.$field_array['tooltip'].'"></div>';
					break;
			} //end switch
			echo '</td></tr>';
		}

		echo '</table>';

	}

}

function rules_get_categories_array()
{
	$post_catgegories_objects = get_categories( array('type'=>'post','orderby'=>'name','hide_empty'=>'0' ));

	foreach ($post_catgegories_objects as $cat)
	{
		$post_catgegories[$cat->term_id.":category"] = $cat->name." (id:".$cat->term_id.";post_type:post)";
	}

	//var_dump($post_type);exit;
	$cpt_categegories = array();
	$taxonomies = get_taxonomies( array( 'public'   => true, '_builtin' => false) );

	foreach ($taxonomies as $key=>$taxonomy)
	{
		if (stristr($taxonomy,'cat'))
		{
			$post_type = get_taxonomy( $taxonomy )->object_type[0];
			$tax_terms = get_terms($taxonomy);
			//print_r($tax_terms); exit;
			foreach ($tax_terms as $term)
			{
				$cpt_categegories[$term->term_id.":".$taxonomy] = $term->name." (id:".$term->term_id.";post_type:".$post_type.")";
			}
		}
	}

	//print_r($cpt_categegories);
	//print_r($post_catgegories);

	$all_categories = array_merge($post_catgegories,$cpt_categegories);

	return $all_categories;
}

add_action('save_post', 'rule_save_post');
function rule_save_post($post_id) {
	global $post;

	if (!isset($post))
		return;

	if ($post->post_type=='revision' ||  'trash' == get_post_status( $post_id ))
	{
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	{
		return;
	}

	if ($post->post_type=='rule')
	{
		$rule_fields = wpleads_lead_rules_get_fields();
		//for debugging
		//update_option('rule_rules',"");

		 $rule_condition_blocks = (isset($_POST['rule_condition_blocks'])) ? $_POST['rule_condition_blocks'] : array();

		//prepare rule conditions for saving/updating
		foreach ($rule_condition_blocks as $key=>$cid)
		{

			foreach ($rule_fields as $field_key=>$field_array)
			{
				if ($field_array['position']!='conditions')
					continue;

				$old = get_post_meta($post_id, $field_array['name']."_".$cid, true);
				if (isset($_POST[$field_array['name']."_".$cid]))
				{

					$new = $_POST[$field_array['name']."_".$cid];

					if (is_array($new))
					{
						//echo $field_array['name'];exit;
						array_filter($new);
						$new = implode(';',$new);
						update_post_meta($post_id, $field_array['name']."_".$cid, $new);
					}
					else if (isset($new) && $new != $old ) {
						update_post_meta($post_id, $field_array['name']."_".$cid, $new);
					}
					else if ('' == $new && $old) {
						//echo "here";exit;
						delete_post_meta($post_id, $field_array['name']."_".$cid, $old);
					}
				}
				else
				{
					update_post_meta($post_id, $field_array['name']."_".$cid,false);
				}
			}
		}


		//prepare rule actions for saving/updating
		foreach ($rule_fields as $field_key=>$field_array)
		{

			if ($field_array['position']!='actions')
				continue;

			$old = get_post_meta($post_id, $field_array['name']."_0", true);
			if (isset($_POST[$field_array['name']."_0"]))
			{
				$new = $_POST[$field_array['name']."_0"];

				if (is_array($new))
				{
					//echo $field_array['name'];exit;
					array_filter($new);
					$new = implode(';',$new);
					update_post_meta($post_id, $field_array['name']."_0", $new);
				}
				else if (isset($new) && $new != $old ) {
					update_post_meta($post_id, $field_array['name']."_0", $new);
				}
				else if ('' == $new && $old) {
					//echo "here";exit;
					delete_post_meta($post_id, $field_array['name']."_0", $old);
				}
			}
			else
			{
				update_post_meta($post_id, $field_array['name']."_0",false);
			}
		}

		//save rule blocks
		$rule_condition_blocks = json_encode($rule_condition_blocks);
		update_post_meta($post->ID,'rule_condition_blocks',$rule_condition_blocks);

		//save rule activation status
		$status = (isset($_POST['rule_active'])) ? $_POST['rule_active'] : '';
		update_post_meta($post->ID,'rule_active', $status );

		//save condtion check nature
		$nature = (isset($_POST['rules_conditions_nature'])) ? $_POST['rules_conditions_nature'] : '';
		update_post_meta($post->ID,'rules_conditions_nature', $nature );

		do_action('rules_save_rule_post',$post_id);
	}
}
/*-------------------------------------------------------WORKSPACE-------------------------------------------------------*/
//print all global fields for post
/*
global $wpdb;
$data   =   array();
$wpdb->query("
	SELECT `meta_key`, `meta_value`
	FROM $wpdb->postmeta
	WHERE `post_id` = ".$_GET['post']."
");
foreach($wpdb->last_result as $k => $v){
	$data[$v->meta_key] =   $v->meta_value;
};
if (isset($_GET['post']))
{
	print_r( $data);
}
*/