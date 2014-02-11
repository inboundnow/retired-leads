<?php

add_action('lp_lead_table_data_is_details_column','wpleads_add_user_edit_button');
function wpleads_add_user_edit_button($item)
{
	$image = WPL_URL.'/images/icons/edit_user.png';
	echo '&nbsp;&nbsp;<a href="'.get_admin_url().'post.php?post='.$item['ID'].'&action=edit" target="_blank"><img src="'.$image.'" title="Edit Lead"></a>';
}

add_action('lp_module_lead_splash_post','wpleads_add_user_conversion_data_to_splash');
function wpleads_add_user_conversion_data_to_splash($data)
{
	$conversion_data = $data['lead_custom_fields']['wpleads_conversion_data'];
	//$test = get_post_meta($data['lead_id'],'wpl-lead-conversions', true);
	//print_r($test);
	echo "<h3  class='lp-lead-splash-h3'>Recent Conversions:</h3>";
	echo "<table>";
	echo "<tr>";
				echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-0'>#</td>";
				echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-1'>Location</td>";
				echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-2'>Datetime</td>";
				echo "<td class='lp-lead-splash-td' 'id='lp-lead-splash-3'>First-time?</td>";
	echo "<tr>";
	foreach ($conversion_data as $key=>$value)
	{
		$i = $key+1;
		//print_r($conversion_data);
		$value = json_decode($value, true);
		//print_r($value);
		foreach ($value as $k=>$row)
		{


			echo "<tr>";
				echo "<td>";
					echo "[$i]";
					//echo $row['id'];
					//print_r($row);exit;
				echo "</td>";
				echo "<td>";
					echo "<a href='".get_permalink($row['id'])."' target='_blank'>".get_the_title(intval($row['id']))."</a>";
				echo "</td>";
				echo "<td>";
					echo $row['datetime'];
				echo "</td>";
				echo "<td>";
					if ($row['first_time']==1)
					{
						echo "yes";
					}
				echo "</td>";
			echo "<tr>";
			$i++;
		}
	}

	echo "</table>";
}
