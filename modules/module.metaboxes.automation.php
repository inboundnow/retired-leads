<?php

/* Define core rules */
function wpleads_lead_automation_get_fields() {
	/* New structure */
	$automation_fields['automation_if'] =
	array('label' => __( 'IF condition' , 'leads' ) ,
		  'id' => 'automation_if',
		  'position' => 'conditions',
		  'priority' => 10,
		  'type' => 'dropdown',
		  'class' => 'automation_dropdown',
		  'show' => true,
		  'tooltip' => __( "This condition allows us to set the primary criteria for lead sorting or points awarding."  , 'leads' ),
		  'options' => array(
							'page_views_general'=> __( 'Visitor views any page' , 'leads' ),
							'page_views_category_specific'=> __( 'Visitor views category specific page' , 'leads' ),
							'page_conversions_general'=> __( 'Visitor converts on any page' , 'leads' ),
							'page_conversions_category_specific'=> __( 'Visitor converts on category specific page' , 'leads' ),
							//'sessions_recorded'=> __( 'Total site browsing sessions equals' , 'leads' ),
							'automation_executed'=> __( 'Total number of successful rule matches is at least' , 'leads' )
							),
	);
	$automation_if_options = apply_filters('automation_if_options',$automation_fields['automation_if']['options']);

	$automation_fields['condition_count'] =
	array('label' => __( '# Action Requirments' , 'leads' ) ,
		  'id' => 'automation_condition_number',
		  'position' => 'conditions',
		  'priority' => 20,
		  'type' => 'text',
		  'class' => 'automation_text_input',
		  'show' => true,
		  'tooltip' => __( "This condition sets the standard that the IF statement above must achieve before a rule is executed." , 'leads' ),
		  'options' => array(
							'page_views_general'=> __( 'Visitor views any page' , 'leads' ),
							'page_views_category_specific'=> __( 'Visitor views category specific page' , 'leads' ),
							'page_conversions_general'=> __( 'Visitor converts on any page' , 'leads' ),
							'page_conversions_category_specific'=> __( 'Visitor converts on category specific page' , 'leads' ),
							//'sessions_recorded'=> __( 'Total site browsing sessions equals' , 'leads' ),
							'automation_executed'=> __( 'Total number of successful rule matches is at least' , 'leads' )
							),
	);
	$categories_option_array = automation_get_categories_array();
	$automation_fields['condition_category'] =
	array('label' => __( 'Category Condition' , 'leads' ) ,
		  'id' => 'automation_condition_category',
		  'position' => 'conditions',
		  'priority' => 30,
		  'type' => 'dropdown',
		  'class' => 'automation_dropdown',
		  'show' => false,
		  'tooltip' => __( "When an IF statment requires category specific reader behavior this dropdown helps us target our category of interest." , 'leads' ),
		  'options' => $categories_option_array,
	);

	$automation_fields['condition_meta_automation_check'] =
	array('label' => __( 'Rule Limitation' , 'leads' ) ,
		  'id' => 'automation_condition_automation_check',
		  'position' => 'actions',
		  'default' => 'on',
		  'priority' => 40,
		  'type' => 'checkbox',
		  'class' => 'automation_dropdown',
		  'show' => true,
		  'tooltip' => __( "To protect rule actions from firing multiple times on the same user turn this setting to on." , 'leads' ),
		  'options' => array('on'=> __( 'Only run rule on lead once.' , 'leads' )),
	);
	$list_options = wpleads_get_lead_lists_as_array();
	$automation_fields['condition_list_add'] =
	array('label' => __( 'Add to List(s)' , 'leads' ) ,
		  'id' => 'automation_condition_list_add',
		  'position' => 'actions',
		  'priority' => 50,
		  'type' => 'checkbox',
		  'class' => 'automation_dropdown',
		  'show' => true,
		  'tooltip' => __( "When all rule conditions are met by a lead then sort the lead into these lead lists." , 'leads' ),
		  'options' => $list_options,
	);

	$automation_fields['condition_list_remove'] =
	array('label' => __( 'Remove from List(s)' , 'leads' ) ,
		  'id' => 'automation_condition_list_remove',
		  'position' => 'actions',
		  'priority' => 51,
		  'type' => 'checkbox',
		  'class' => 'automation_dropdown',
		  'show' => true,
		  'tooltip' => __( "When all rule conditions are met by a lead then remove the lead out of these lead lists." , 'leads' ),
		  'options' => $list_options,
	);

	$automation_fields['condition_points'] =
	array('label' => __( 'Adjust Lead Score' , 'leads' ) ,
		  'id' => 'automation_condition_points',
		  'position' => 'actions',
		  'priority' => 60,
		  'type' => 'text',
		  'class' => 'automation_input',
		  'show' => true,
		  'tooltip' => __( "When all rule conditions are met by a lead then award the user this many points. You may use - symbols before an interger for point removal." , 'leads' )
	);

	$automation_fields = apply_filters('wpleads_automation_fields',$automation_fields);

	foreach ($automation_fields as $key =>$array) {
		$automation_array_prioritized[$key] = $array['priority'];
	}

	asort($automation_array_prioritized);

	foreach ($automation_array_prioritized as $key => $value) {
		$automation_fields_final[$key] = $automation_fields[$key];
	}

	return $automation_fields_final;
}



add_action('add_meta_boxes', 'wpleads_lead_lists_add_metaboxes');
function wpleads_lead_lists_add_metaboxes() {
	global $post;

	/* main rule block */
	add_meta_box(
		'automation_metabox_main', // $id
		__( 'Automation Rule Overview', 'leads' ),
		'automation_display_metabox_main', // $callback
		'automation', // $page
		'normal', // $context
		'high'); // $priority

	/* sidebar */
	add_meta_box(
	'automation_metabox_run_tools_sidebar',
	__( "Rule Tools", 'leads' ),
	'automation_metabox_sidebar_tools',
	'automation' ,
	'side',
	'low' );
}


function automation_display_metabox_main() {
	//echo 1; exit;
	global $post;
	global $wpdb;

	do_action('automation_js');

	$automation_fields = wpleads_lead_automation_get_fields();

	//define tabs
	$tabs[] = array('id'=>'automation_condition_0','label'=> __( 'Condition 1' , 'leads' ) );


	//define open tab
	$active_tab = 'automation_condition_0';
	if (isset($_REQUEST['open-tab']))
	{
		$active_tab = $_REQUEST['open-tab'];
	}


	$automation_activation_status = get_post_meta($post->ID,'automation_active',true);
	$automation_conditions_nature = get_post_meta($post->ID,'automation_conditions_nature',true);

	$automation_block_ids = get_post_meta($post->ID,'automation_condition_blocks',true);
	$automation_block_ids = json_decode($automation_block_ids,true);

	if (!$automation_block_ids)
		$automation_block_ids = array('0'=>'0');
	//print_r($automation_block_ids);

	//prepare values for condition blocks
	foreach ($automation_block_ids as $key=>$id)
	{
		foreach ($automation_fields as $key=>$field)
		{
			if ($field['position']=='conditions')
			{
				$automation_fields[$key]['value'][$id] = get_post_meta( $post->ID , $automation_fields[$key]['id']."_".$id ,true );
			}
		}
	}

	//prepare values for action settings
	foreach ($automation_fields as $key=>$field)
	{
		if ($field['position']=='actions')
		{
			$automation_fields[$key]['value'][0] = get_post_meta( $post->ID , $automation_fields[$key]['id']."_0" ,true );
			//$automation_fields[$key]['value'][0] = $automation_fields[$key]['default'];
		}
	}

	//print_r($automation_fields);exit;
	$automation_fields = apply_filters('automation_fields',$automation_fields);

	// Use nonce for verification
	echo "<input type='hidden' name='automation_custom_fields_nonce' value='".wp_create_nonce('automation-nonce')."' />";
	echo "<input type='hidden' name='open-tab' id='id-open-tab' value='{$active_tab}'>";
	?>

	<div class='rules-div-add-new-automation-condition'>
		<select class="automation_dropdown automation_active" id="automation_active" name="automation_active">
			<option value="active" <?php if ($automation_activation_status=='active') echo "selected"; ?>><?php _e( 'Activate Rule' , 'leads' ); ?></option>
			<option value="inactive" <?php if ($automation_activation_status=='inactive') echo "selected"; ?>>Deactivate Rule</option>
		</select>
		<select name='automation_conditions_nature'>
			<option value='match_all' <?php if ($automation_conditions_nature=='match_all') echo "selected"; ?>><?php _e( 'Match all conditions.' , 'leads' ); ?></option>
			<option value='match_any' <?php if ($automation_conditions_nature=='match_any') echo "selected"; ?>><?php _e( 'Match any conditions.' , 'leads' ); ?></option>
		</select>
		<a id="ma-a-add-new-automation-condition"  name='' class="button"><?php _e( 'Add New Condtion' , 'leads' ); ?></a>
	</div>
	<div class="metabox-holder split-test-ui">

		<div class="meta-box-sortables ui-sortable">

			<h2 id="ma-st-tabs-0" class="nav-tab-wrapper nav-tab-wrapper-conditions">
				<?php
				$condition_count = count($automation_block_ids);
				foreach ($automation_block_ids as $key=>$cid)
				{
					$i = $key+1;
					?>
					<a  id='tabs-automation_condition_<?php echo $cid; ?>' rel='<?php echo $cid; ?>' class="ma-nav-tab nav-tab nav-tab-special<?php echo $key > 0 ? '-inactive' : '-active'; ?>">
						<?php sprintf(_e( "Condition %d", 'leads' ) , $i ); ?>
					</a>
					<?php

					if ($condition_count>1)
					{
					?>
					<span class='automation-delete-condition-button' id='automation-delete-condition-button-<?php echo $cid; ?>'>
						<img title="<?php _e('Remove Condition', 'leads' );?>" src='<?php echo WPL_URL.'/images/delete.png'; ?>'>
					</span>
					<?php
					}
				}
				?>
			</h2>

			<div id='automation_conditions_container'>



				<?php
				// Custom Before and After Hook here for custom fields shown on main view page

				automation_render_setting($automation_fields,'conditions','automation-tab-display', true);


				?>
			</div>
			<div class='automation-actions-block'>
				<h2 id="ma-st-tabs-0" class="nav-tab-wrapper nav-tab-wrapper-actions">
						<a  id='tabs-actions' style='padding-left:14px;'><?php _e('Actions','leads'); ?>:</a>
				</h2>
				<?php
				// Custom Before and After Hook here for custom fields shown on main view page

				automation_render_setting($automation_fields,'actions',' ');

				?>
			</div>
			<div style='display:none'>
			</div>

		<?php
		do_action('automation_print_lead_tab_sections');
		?>

		</div><!-- end .meta-box-sortables -->
	</div><!-- end .metabox-holder -->
	<?php
}

function automation_metabox_sidebar_tools() {
	global $post;

	$automation_queue = get_option('automation_queue');

	$automation_queue = json_decode($automation_queue, true);
	$est_time_remaining = 0;
	if ( is_array($automation_queue) && array_key_exists( $post->ID , $automation_queue))
	{
		$style = "display:inline";
		$text = __('Processing...','leads');

		foreach ($automation_queue as $automation_id => $batches)
		{
			$batch_count[$automation_id] = count($batches);
		}


		foreach ($batch_count as $automation_id => $count)
		{
			$time_calc = $count * 2;
			$est_time_remaining = $est_time_remaining + $time_calc;
		}
	}
	else
	{
		$style = "display:none";
		$text = __( "Run rule on all leads." , 'leads' );
	}
	?>

	<?php

		if ($est_time_remaining)
		{
			echo '<small>';
			sprintf( _e("Estimated Time Remaining: %d minutes. Reload page to recalulate time remaining." , 'leads' ) , $est_time_remaining );
			echo "<a href='?wpleads_lead_automation_run_manual_cron=1' title='Click here to manually run back-end processing and speed up processing time by 2 minutes.' target='_blank'>";
			_e( "Force Processing" , 'leads' );
			echo "</a></small>";
		}

	?>

	<a name='#automation_run_rules' id='run-automation' class='run-automation button button-large'><?php echo $text; ?></a>
	<span class='rules-processing' style='<?php echo $style; ?>;'>
		<img src='<?php echo WPL_URL.'/images/process_b.gif'; ?>' width='25' height='25' style='vertical-align: middle;padding-left:84px;cursor:pointer'>
	</span>


	<?php do_action('automation_metabox_sidebar_automation_tools', $post);
}


/* display metabox setting */

function automation_render_setting($fields, $position=null, $container_class = '', $declare_automation_block = false)
{
	global $post;

	$automation_block_ids = get_post_meta($post->ID,'automation_condition_blocks',true);
	$automation_block_ids = json_decode($automation_block_ids,true);

	if (!$automation_block_ids||$position=='actions'){
		$automation_block_ids = array('0'=>'0');
	}

	foreach ($automation_block_ids as $key => $cid)
	{

		if ($declare_automation_block){
			echo "<input type='hidden' name='automation_condition_blocks[]' id='automation_container_hidden_input_{$cid}' value='{$cid}'>";
		}

		($key>0) ? $style = 'display:none;' : $style = '';

		echo "<table id='automation_main_container_{$position}_{$cid}' class='{$container_class}' style='{$style}'>";


		foreach ($fields as $field_key => $field_array)
		{
			if ($field_array['position']!=$position){
				continue;
			}
			//print_r($field_array);


			$id = strtolower($field_array['id']);

			$id = $id."_{$cid}";

			(!$field_array['show']) ? $hide_toggle = "automation-hidden-steps" : $hide_toggle = "";

			echo '<tr class="'.$hide_toggle.'" id="tr_'.$id.'">
				<th class="ma-th" ><label for="'.$id.'">'.$field_array['label'].':</label></th>
				<td class="ma-td" id="ma-td-'.$id.'">';
			switch(true) {
				case strstr($field_array['type'],'textarea'):
					$parts = explode('-',$field_array['type']);
					(isset($parts[1])) ? $rows= $parts[1] : $rows = '10';
					echo '<textarea name="'.$id.'" rel="'.$cid.'" id="'.$id.'" rows='.$rows.'" class="'.$field_array['class'].' '.$field_array['id'].'" >'.$field_array['value'][$cid].'</textarea>';
					echo '<div class="automation_tooltip tool_textarea" title="'.$field_array['tooltip'].'"></div>';
					break;
				case strstr($field_array['type'],'text'):
					$parts = explode('-',$field_array['type']);
					echo '<input type="text" name="'.$id.'" id="'.$id.'"  rel="'.$cid.'" value="'.$field_array['value'][$cid].'" class="'.$field_array['class'].' '.$field_array['id'].'"/>';
					echo '<div class="automation_tooltip tool_text" title="'.$field_array['tooltip'].'"></div>';
					break;
				// wysiwyg
				case strstr($field_array['type'],'wysiwyg'):
					wp_editor( $field_array['value'][$cid], $id, $settings = array() );
					echo '<div class="automation_tooltip tool_wysiwyg" title="'.$field_array['tooltip'].'"></div>';
					break;
				// checkbox
				case strstr($field_array['type'],'checkbox'):

					if (strstr($field_array['value'][$cid],';'))
					{
						$field_array['value'][$cid] = explode(';',$field_array['value'][$cid]);
					}

					if (!is_array($field_array['value'][$cid])){
						$field_array['value'][$cid] = array($field_array['value'][$cid]);
					}

					echo "<table class='automation_check_box_table'>";


					foreach ($field_array['options'] as $value=>$field_array['label']) {

							echo "<tr>";

							echo '<td><input type="checkbox"  rel="'.$cid.'" name="'.$id.'[]" id="'.$id.'" value="'.$value.'" ',in_array($value,$field_array['value'][$cid]) ? ' checked="checked"' : '','  class="'.$field_array['class'].' '.$field_array['id'].'"/>';
							echo '<label for="'.$value.'">&nbsp;&nbsp;'.$field_array['label'].'</label></td>';


							echo "</tr>";
					}
					echo "</table>";
					echo '<div class="automation_tooltip tool_checkbox" title="'.$field_array['tooltip'].'"></div>';
					break;
				// radio
				case strstr($field_array['type'],'radio'):
					foreach ($field_array['options'] as $value=>$field_array['label']) {
						//echo $field_array['value'][$cid].":".$value;
						//echo "<br>";
						echo '<input type="radio" name="'.$id.'" id="'.$id.'"  rel="'.$cid.'" value="'.$value.'" ',$field_array['value'][$cid]==$value ? ' checked="checked"' : '','  class="'.$field_array['class'].' '.$field_array['id'].'" />';
						echo '<label for="'.$value.'">&nbsp;&nbsp;'.$field_array['label'].'</label> &nbsp;&nbsp;&nbsp;&nbsp;';
					}
					echo '<div class="automation_tooltip tool_radio" title="'.$field_array['tooltip'].'"></div>';
					break;
				// select
				case $field_array['type'] == 'dropdown':
					echo '<select name="'.$id.'" id="'.$id.'"  rel="'.$cid.'" class="'.$field_array['class'].' '.$field_array['id'].'" >';
					foreach ($field_array['options'] as $value=>$field_array['label']) {
						echo '<option', $field_array['value'][$cid] == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$field_array['label'].'</option>';
					}
					echo '</select>';
					echo '<div class="automation_tooltip tool_dropdown" title="'.$field_array['tooltip'].'"></div>';
					break;
			} //end switch
			echo '</td></tr>';
		}

		echo '</table>';

	}

}

function automation_get_categories_array()
{
	$post_catgegories_objects = get_categories( array('type'=>'post','orderby'=>'id','hide_empty'=>'0' ));

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

add_action('save_post', 'automation_save_post');
function automation_save_post($post_id) {
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

	if ($post->post_type=='automation')
	{
		$automation_fields = wpleads_lead_automation_get_fields();
		//for debugging
		//update_option('automation_rules',"");

		 $automation_condition_blocks = (isset($_POST['automation_condition_blocks'])) ? $_POST['automation_condition_blocks'] : array();

		//prepare rule conditions for saving/updating
		foreach ($automation_condition_blocks as $key=>$cid)
		{

			foreach ($automation_fields as $field_key=>$field_array)
			{
				if ($field_array['position']!='conditions')
					continue;

				$old = get_post_meta($post_id, $field_array['id']."_".$cid, true);
				if (isset($_POST[$field_array['id']."_".$cid]))
				{

					$new = $_POST[$field_array['id']."_".$cid];

					if (is_array($new))
					{
						//echo $field_array['id'];exit;
						array_filter($new);
						$new = implode(';',$new);
						update_post_meta($post_id, $field_array['id']."_".$cid, $new);
					}
					else if (isset($new) && $new != $old ) {
						update_post_meta($post_id, $field_array['id']."_".$cid, $new);
					}
					else if ('' == $new && $old) {
						//echo "here";exit;
						delete_post_meta($post_id, $field_array['id']."_".$cid, $old);
					}
				}
				else
				{
					update_post_meta($post_id, $field_array['id']."_".$cid,false);
				}
			}
		}


		//prepare rule actions for saving/updating
		foreach ($automation_fields as $field_key=>$field_array)
		{

			if ($field_array['position']!='actions')
				continue;

			$old = get_post_meta($post_id, $field_array['id']."_0", true);
			if (isset($_POST[$field_array['id']."_0"]))
			{
				$new = $_POST[$field_array['id']."_0"];

				if (is_array($new))
				{
					//echo $field_array['id'];exit;
					array_filter($new);
					$new = implode(';',$new);
					update_post_meta($post_id, $field_array['id']."_0", $new);
				}
				else if (isset($new) && $new != $old ) {
					update_post_meta($post_id, $field_array['id']."_0", $new);
				}
				else if ('' == $new && $old) {
					//echo "here";exit;
					delete_post_meta($post_id, $field_array['id']."_0", $old);
				}
			}
			else
			{
				update_post_meta($post_id, $field_array['id']."_0",false);
			}
		}

		//save rule blocks
		$automation_condition_blocks = json_encode($automation_condition_blocks);
		update_post_meta($post->ID,'automation_condition_blocks',$automation_condition_blocks);

		//save rule activation status
		$status = (isset($_POST['automation_active'])) ? $_POST['automation_active'] : '';
		update_post_meta($post->ID,'automation_active', $status );

		//save condtion check nature
		$nature = (isset($_POST['automation_conditions_nature'])) ? $_POST['automation_conditions_nature'] : '';
		update_post_meta($post->ID,'automation_conditions_nature', $nature );

		do_action('automation_save_automation_post',$post_id);
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