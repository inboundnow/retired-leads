<?php

/*
TODO:
- Get multiple list query working.
- Fix the actionat the bottom and jquery


 */
define('BATCH_PATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ) );

function lead_dropdown_generator() {
		global $wpdb;

				$post_type = 'wp-lead';
				$query = "
					SELECT DISTINCT($wpdb->postmeta.meta_key)
					FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta
					ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE $wpdb->posts.post_type = 'wp-lead'
					AND $wpdb->postmeta.meta_key != ''
					AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
					AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
				";
				$sql = 'SELECT DISTINCT meta_key FROM '.$wpdb->postmeta;
				$fields = $wpdb->get_col($wpdb->prepare($query, $post_type));
				//print_r($fields);
				// $fields = $wpdb->get_results($sql, ARRAY_N);
			?>
				<select multiple name="wp_leads_filter_field" id="lead-meta-filter">

				<?php
					$current = isset($_GET['wp_leads_filter_field'])? $_GET['wp_leads_filter_field']:'';
					$current_v = isset($_GET['wp_leads_filter_field_val'])? $_GET['wp_leads_filter_field_val']:'';
					$nice_names = array(
						"wpleads_first_name" => "First Name",
						"wpleads_last_name" => "Last Name",
						"wpleads_email_address" => "Email Address",
						"wpleads_city" => "City",
						"wpleads_areaCode" => "Area Code",
						"wpleads_country_name" => "Country Name",
						"wpleads_region_code" => "State Abbreviation",
						"wpleads_region_name" => "State Name",
						"wp_lead_status" => "Lead Status",
						"events_triggered" => "Number of Events Triggered",
						"lp_page_views_count" => "Page View Count",
						"wpleads_conversion_count" => "Number of Conversions"
					);

					$nice_names = apply_filters('wpleads_sort_by_custom_field_nice_names',$nice_names);

					foreach ($fields as $field) {
						//echo $field;
						if (array_key_exists($field, $nice_names)) {
							$label = $nice_names[$field];
							echo "<option value='$field' ".selected( $current, $field ).">$label</option>";
						}

					}
				?>
				</select>
				<style type="text/css" media="screen">
				/*<![CDATA[*/
					.select2-container {
						width: 50%;
						padding-top: 15px;
					}
				/*]]>*/
				</style>
				<?php
}

add_action('wp_ajax_leads_delete_from_list', 'leads_delete_from_list');           // for logged in user
add_action('wp_ajax_nopriv_leads_delete_from_list', 'leads_delete_from_list');

function leads_delete_from_list(){
	//check_ajax_referer('leads_ajax_load_more_leads');

	$lead_id = (isset($_POST['lead_id'])) ? $_POST['lead_id'] : '';
	$list_id = (isset($_POST['list_id'])) ? $_POST['list_id'] : '';

	$id = $lead_id;
	// $cats = wp_get_post_terms($id, "wplead_list_category_action"); // gets all cats

	$current_terms = wp_get_post_terms( $id, 'wplead_list_category', 'id' );
	$current_terms_count = count($terms);
	//print_r($current_terms);
	$all_remove_terms = '';
	foreach ($current_terms as $term ) {
		$add = $term->term_id;
		$all_remove_terms .= $add . ' ,';
	}
	$final = explode(' ,', $all_remove_terms);

	$final = array_filter($final, 'strlen');

	//$cats = wp_get_post_categories($id);
	if (in_array($list_id, $final) ) {
		$new = array_flip ( $final );
		unset($new[$list_id]);
		$save = array_flip ( $new );
		wp_set_object_terms( $id, $save, 'wplead_list_category');
	}


}

add_action('wp_ajax_leads_ajax_load_more_leads', 'leads_ajax_load_more_leads');           // for logged in user
add_action('wp_ajax_nopriv_leads_ajax_load_more_leads', 'leads_ajax_load_more_leads');

function leads_ajax_load_more_leads(){
	//check_ajax_referer('leads_ajax_load_more_leads');

	$order = (isset($_POST['order'])) ? $_POST['order'] : 'DESC';
	$orderby = (isset($_POST['orderby'])) ? $_POST['orderby'] : 'date';
	$cat = (isset($_POST['cat'])) ? $_POST['cat'] : '';
	$tag = (isset($_POST['tag'])) ? $_POST['tag'] : '';
	$paged = (isset($_POST['pull_page'])) ? $_POST['pull_page'] : "";
	$relation = (isset($_POST['relation'])) ? $_POST['relation'] : "AND";
    $args = array(
    	'post_type' => 'wp-lead',
    	'order' => strtoupper($order),
    	'orderby' => $orderby,
    	'posts_per_page' => 60,

    );
    // fix the bullshit

    // magic fix http://wordpress.stackexchange.com/questions/96584/how-do-i-filter-posts-by-taxomony-using-ajax
   if ( $cat != 'all'){
   		/* OLD Tax setup
  		//$args['term'] = $cat;
    	/*$args['tax_query'] = array(
    							array(
    								'taxonomy' => 'wplead_list_category',
    								'field' => 'id',
    								'terms' => $cat,
    								'operator' => 'IN'
    							)
    						);
    	end OLD Tax setup */
		$tax_query = array( 'relation' => $relation );
		$new_cat = str_replace("%2C", ",", $cat);
		$taxonomy_array = explode(",", $new_cat); // fix array posted
		//$args['term'] = $taxonomy_array;
		//
		//echo json_encode($taxonomy_array,JSON_FORCE_OBJECT);
		//wp_die();
		$loop_count_two = 0;
		foreach($taxonomy_array as $taxonomy_array_value => $test){

		        $tax_query[] = array(
		        'taxonomy' => 'wplead_list_category',
		        'field'    => 'id',
		        'terms'    => $test,
		        'operator' => 'IN'

		    );

		}
	    $args['tax_query'] = $tax_query;
    }
    //echo json_encode($args,JSON_FORCE_OBJECT);
    //wp_die();
    $term_id = $cat;
 /*  $args = array(
   'post_type' => 'wp-lead',
   'term' => 54,
   'posts_per_page' => -1,
   'order' => 'DESC',
   'tax_query' => array(
	                 array(
	                     'taxonomy' => 'wplead_list_category',
	                     'field'    => 'id',
	                     'terms'    => 54,
	                     'operator' => 'IN'
	                     ),
     		)
    ); */

    // Add tag to query
    if (isset($tag) && $tag != "" ){
    	//$args['tag'] = $_POST['tag'];
    }
    if (isset($paged) && $paged != "" ){
    	$args['paged'] = $paged;
    }

    $output =  $args;
  	//echo json_encode($output,JSON_FORCE_OBJECT);
   //wp_die();

    $query = new WP_Query( $args );
    $posts = $query->posts;
    $i = 0;

    $loop_page = $paged - 1;

    $loop_count = $loop_page * 60;
    $loop_count = $loop_count + 1;
	foreach ( $posts as $post ) {

		//$categories = wp_get_post_categories($post->ID);
		$this_tax = "wplead_list_category";


		$terms = wp_get_post_terms( $post->ID, $this_tax, 'id' );
		$cats = '';
		$lead_ID = $post->ID;
     	foreach ( $terms as $term ) {
		  	$term_link = get_term_link( $term, $this_tax );
		    if( is_wp_error( $term_link ) )
		        continue;
		    //We successfully got a link. Print it out.
		    $cats .= '<span class="list-pill">' . $term->name . ' <i title="Remove This lead from the '.$term->name.' list" class="remove-from-list" data-lead-id="'.$lead_ID.'" data-list-id="'.$term->term_id.'"></i></span> ';
		}

		$_tags = wp_get_post_tags($post->ID);
		$tags = '';
		foreach ( $_tags as $tag ) {
			$tags .= "<a href='?page=lead_management&t=$tag->slug'>$tag->name</a>, ";
		}
		$tags = substr($tags, 0, strlen($tags) - 2);
		if ( empty ($tags) ) {
			$tags = 'No Tags';
		}
		$alt_class = ($i%2 == 0) ? ' class="alternate"' : '' ;

		echo '<tr'.$alt_class.'>
					<td><input class="lead-select-checkbox" type="checkbox" name="ids[]" value="' . $post->ID . '" /></td>
					<td class="count-sort"><span>'.$loop_count.'</span></td>
					<td>

		';
		$i++;
		if ( '0000-00-00 00:00:00' == $post->post_date ) {

		} else {

		 echo date(__('Y/m/d'), strtotime($post->post_date));

		}

		echo '</td>
					<td><span class="lead-email">' . $post->post_title . '</span></td>
					<td>' . $cats . '</td>
					<td>' . $tags . '</td>

					<td><a class="thickbox" href="post.php?action=edit&post=' . $post->ID . '&amp;small_lead_preview=true&amp;TB_iframe=true&amp;width=1345&amp;height=244">View</a></td>

					<td>' . $post->ID . '</td>
				</tr>
		';
		$loop_count++;
	}

}

function test_query(){

}
function leads_count_posts_in_term($taxonomy, $term, $type="post"){
	global $wpdb;
	$query = "
	SELECT COUNT( DISTINCT cat_posts.ID ) AS post_count
	FROM $wpdb->term_taxonomy AS cat_term_taxonomy INNER JOIN $wpdb->terms AS cat_terms ON
	cat_term_taxonomy.term_id = cat_terms.term_id
	INNER JOIN $wpdb->term_relationships AS cat_term_relationships
	ON cat_term_taxonomy.term_taxonomy_id = cat_term_relationships.term_taxonomy_id
	INNER JOIN $wpdb->posts AS cat_posts
	ON cat_term_relationships.object_id = cat_posts.ID
	WHERE cat_posts.post_status = 'publish'
	AND cat_posts.post_type = '".$type."'
	AND cat_term_taxonomy.taxonomy = '".$taxonomy."'
	AND cat_terms.slug IN ('".$term."')
	";
	return $wpdb->get_var($query);
}

function lead_select_taxonomy_dropdown($taxonomy, $select_type = 'multiple', $custom_class = '[]') {
	$type = ($select_type === 'multiple') ? 'multiple' : '';
	$id = 'cat';
	if ($select_type == 'single'){
		$id = 'bottom-cat';
	}
	?>
	<select <?php echo $type;?> name="wplead_list_category<?php echo $custom_class;?>" id="<?php echo $id;?>" class="postform <?php echo $custom_class;?>">
	<?php if (isset($_GET['wplead_list_category']) && $_GET['wplead_list_category'][0] === 'all' ){
		$all_selected = 'selected="selected"';
	} else {
		$all_selected = '';
	}
	if ($select_type != 'single'){ ?>
		<option class="" value="all" <?php echo $all_selected;?>>All Leads in Database</option>
	<?php }

	$args = array(
	    'hide_empty'    => false,
	);
	$terms = get_terms($taxonomy, $args);
	if (isset($_GET['wplead_list_category'])) {
		$list_array = $_GET['wplead_list_category'];
	}

	foreach ($terms as $term) {

		$count = leads_count_posts_in_term('wplead_list_category', $term->slug, 'wp-lead');
		$selected = (isset($_GET['wplead_list_category']) && in_array($term->term_id, $list_array) ) ? 'selected="selected"' : '';

		echo '<option class="" value="'.$term->term_id.'" '.$selected.'>'. $term->name.' ('.$count.')</option>';
	}
	echo '</select>';

	?>
<?php }

/*
function add_sketchbook_category_automatically($post_ID) {
	global $wpdb;
	if(!has_term('','category',$post_ID)){
		$cat = array(4);
		wp_set_object_terms($post_ID, $cat, 'category');
	}
}
add_action('publish_sketchbook', 'add_sketchbook_category_automatically');
*/

//add_action( 'wp_head', 'add_all_leads_to_master_list' );
function delete_leads() {
	global $post;
	$args = array(
	'posts_per_page'  => 20,
	'post_type'=> 'wp-lead');

$mycustomposts = get_posts($args);
//print_r($mycustomposts);
   foreach( $mycustomposts as $mypost ) {
     // Delete's each post.
     //wp_delete_post( $mypost->ID, true);
     wp_set_object_terms( $mypost->ID, 'all-leads', 'wplead_list_category');

    // Set to False if you want to send them to Trash.
   }
// 50 custom post types are being deleted everytime you refresh the page.
}
function generate_dummy_email() {

	// array of possible top-level domains
	 $tlds = array("com", "net", "gov", "org", "edu", "biz", "info");

	 // string of possible characters
	 $char = "0123456789abcdefghijklmnopqrstuvwxyz";

	 // start output
	 echo "<p>\n";

	 // main loop - this gives 1000 addresses
	 for ($j = 0; $j < 1000; $j++) {

	   // choose random lengths for the username ($ulen) and the domain ($dlen)
	   $ulen = mt_rand(5, 10);
	   $dlen = mt_rand(7, 17);

	   // reset the address
	   $a = "";

	   // get $ulen random entries from the list of possible characters
	   // these make up the username (to the left of the @)
	   for ($i = 1; $i <= $ulen; $i++) {
	     $a .= substr($char, mt_rand(0, strlen($char)), 1);
	   }

	   // wouldn't work so well without this
	   $a .= "@";

	   // now get $dlen entries from the list of possible characters
	   // this is the domain name (to the right of the @, excluding the tld)
	   for ($i = 1; $i <= $dlen; $i++) {
	     $a .= substr($char, mt_rand(0, strlen($char)), 1);
	   }

	   // need a dot to separate the domain from the tld
	   $a .= ".";

	   // finally, pick a random top-level domain and stick it on the end
	   $a .= $tlds[mt_rand(0, (sizeof($tlds)-1))];

	   // done - echo the address inside a link
	  return $a;

	 }

	 // tidy up - finish the paragraph
}
//generate_dummy_leads();
function generate_dummy_leads(){


	for ($i=0; $i < 50; $i++) {
		$email = generate_dummy_email();
		$post = array(
			'post_title'		=> $email,
			'post_status'		=> 'publish',
			'post_type'		=> 'wp-lead',
			'post_author'		=> 1
		);

		//$post = add_filter('lp_leads_post_vars',$post);
		wp_insert_post($post);
	}

}

add_action('admin_enqueue_scripts', 'lead_management_js');
function lead_management_js() {
		$screen = get_current_screen();

		if ( $screen->id != 'wp-lead_page_lead_management')
		        return; // exit if incorrect screen id
		wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
		wp_enqueue_script('selectjs', WPL_URL . '/shared/js/select2.min.js');
		wp_enqueue_style('selectjs', WPL_URL . '/shared/css/select2.css');
		wp_enqueue_script('tablesort', WPL_URL . '/js/management/tablesort.min.js');

		wp_enqueue_script('light-table-filter', WPL_URL . '/js/management/light-table-filter.min.js');
		wp_register_script( 'modernizr', WPL_URL . '/js/management/modernizr.custom.js' );
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script('tablesort', WPL_URL . '/js/management/tablesort.min.js');
		wp_enqueue_script('jquery-dropdown', WPL_URL . '/js/management/jquery.dropdown.js');
		wp_enqueue_script('bulk-manage-leads', WPL_URL . '/js/management/admin.js');
		wp_localize_script( 'bulk-manage-leads' , 'bulk_manage_leads', array( 'admin_url' => admin_url( 'admin-ajax.php' )));
		wp_enqueue_script('jqueryui');
		wp_enqueue_script('jquery-ui-selectable'); // FINSIH THIS http://jqueryui.com/selectable/
		wp_enqueue_style('wpleads-list-css', WPL_URL.'/css/admin-management.css');
		wp_admin_css('thickbox');
		add_thickbox();
}

function lead_management_admin_screen() {
	global $wpdb;
	InboundCompatibility::inbound_compatibilities_mode(); // Load only our scripts
	if (isset($_GET['testthis'])) {
       test_query();
	}

	// Maybe make this an option some time
	$per_page = 60;
	$paged = empty($_GET['paged']) ? 1 : intval($_GET['paged']);

	$orderbys = array(
		'Date First Created'   => 'date',
		'Date Last Modified' => 'modified',
		'Alphabetical Sort'         => 'title',
		'Status'        => 'post_status'
	);
	$orderbys_flip = array_flip($orderbys);

	// Sorting
	$orderby = '';
	if (isset($_GET['orderby'])) {
		$orderby = $_GET['orderby'];
	}
	$order = "";
	if (isset($_GET['order'])) {
		$order = strtoupper($_GET['order']);
	}



	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$posts = array();


	if (isset($_GET['num'])) {
		$num = intval($_GET['num']);
	} else {
		$num = 0;
	}

	if (isset($_GET['what'])) {
	$what = htmlentities($_GET['what']);
	} else {
		$what = "";
	}
	if (isset($_GET['on'])) {
	$on = htmlentities($_GET['on']);
	} else {
	$on = "";
	}

	$message = '';
	// Deal with any update messages we might have:
	if (isset($_GET['done'])) {
		switch ( $_GET['done'] ) {
			case 'add':
				$message = "Added $num posts to the list &ldquo;$what&rdquo;.";
				break;
			case 'remove':
				$message = "Removed $num posts from the list &ldquo;$what&rdquo;.";
				break;
			case 'tag':
				$message = "Tagged $num posts with &ldquo;$what&rdquo; on $on.";
				break;
			case 'untag':
				$message = "Untagged $num posts with &ldquo;$what&rdquo;.";
				break;
			case 'delete_leads':
				$message = "$num leads permanently deleted";
				break;

		}
	}

	if ( !empty($message) ) {
		echo "<div id='message' class='updated fade'><p><strong>$message</strong></p></div>";
	}

	// Create the hidden input which passes our current filter to the action script.
	if ( !empty($_GET['wplead_list_category']) )
		$filter = '<input id="hidden-cat" type="hidden" name="cat_two" value="' . urlencode(implode(',', $_GET['wplead_list_category'])) . '" />';
	if ( isset($_GET['s']) && !empty($_GET['s']) )
		$filter = '<input type="hidden" name="s" value="' . urlencode($_GET['s']) . '" />';
	if ( isset($_GET['t']) && !empty($_GET['t']) )
		$filter = '<input type="hidden" name="t" value="' . urlencode($_GET['t']) . '" />';

	echo '
		<div class="wrap">
			<h2>Lead Management - Bulk Update Leads</h2>
	';

	if ( empty($_GET['wplead_list_category']) && empty($_GET['s']) && empty($_GET['t']) ) {
		echo '
			<p>To get started, select the lead criteria below to see all matching leads.</p>
		';
	}

	$page_output = (isset($_GET['paged'])) ? $_GET['paged'] : '1';
	echo "<div id='paged-current'>" . $page_output . "</div>";
	// Filtering

	echo '
	<div id="filters" class="tablenav inbound-lead-filters">
		<form id="lead-management-form" method="get" action="edit.php">
		<input type="hidden" name="page" value="lead_management" />
		<input type="hidden" name="post_type" value="wp-lead" />
	';
	// Category drop-down
	echo '<div id="top-filters"><div  id="inbound-lead-lists-dropdown">
		<label for="cat">Select Lead List(s):</label>';
		//wp_lead_lists_dropdown();
		lead_select_taxonomy_dropdown( 'wplead_list_category' );
	echo '</div>';

	if (isset($_GET['relation'])) {
		$relation  = $_GET['relation'];
	} else {
		$relation = 'AND';
	}
	echo '
		<div id="and-or-params">
		<label for="orderby">Show:</label>
			<select name="relation" id="relation">

						<option value="AND"' . ( $relation == 'AND' ? ' selected="selected"' : '' ) . '>Only Leads that are in <u>ALL</u> of the selected lists</option>
						<option value="OR"' . ( $relation == 'OR' ? ' selected="selected"' : '' ) . '>ALL Leads Matching ANY of the selected Lists</option>

			</select>
		</div></div>';
	// Sorting
	echo '<div id="bottom-filters">
		<div class="filter" id="lead-sort-by">
			<label for="orderby">Sort by:</label>
			<select name="orderby" id="orderby">
	';
	foreach ( $orderbys as $title => $value ) {
		$selected = ( $orderby == $value ) ? ' selected="selected"' : '';
		echo "<option value='$value'$selected>$title</option>\n";
	}
	echo '
			</select>
			<select name="order" id="order">
			<option value="asc"' . ( $order == 'ASC' ? ' selected="selected"' : '' ) . '>Asc.</option>
			<option value="desc"' . ( $order == 'DESC' ? ' selected="selected"' : '' ) . '>Desc.</option>
			</select>
		</div>
	';


	if (isset($_GET['s'])) {
		$s  = $_GET['s'];
	} else {
		$s = '';
	}

	// ...then the keyword search.
	echo '
		<div class="filter" style="display:none;">
			<label for="s">Keyword:</label>
			<input type="text" name="s" id="s" value="' . htmlentities($s) . '" title="Use % for wildcards." />
		</div>
	';

	if (isset($_GET['t'])) {
		$t  = $_GET['t'];
	} else {
		$t = '';
	}
	// ...then the tag filter.
	echo '
		<div class="filter" id="lead-tag-filter">
			<label for="s">Tag:</label>
			<input type="text" name="t" id="t" value="' . htmlentities($t) . '" title="\'foo, bar\': posts tagged with \'foo\' or \'bar\'. \'foo+bar\': posts tagged with both \'foo\' and \'bar\'." />
		</div>
	';

	echo '
		<div class="filter">
			<input type="submit" class="button-primary" value="Search Leads" name="submit" />
		</div>';

	echo '</div>
	</form>';


	// Fetch our posts.

	if ( !empty($_GET['wplead_list_category']) || !empty($_GET['s']) || !empty($_GET['t']) || !empty($_GET['on']) ) {
		// A cat has been given; fetch posts that are in that category.
		$q = "paged=$paged&posts_per_page=$per_page&orderby=$orderby&order=$order&post_type=wp-lead";
		if ( !empty($_GET['wplead_list_category']) ) {
			$cat = $_GET['wplead_list_category'];
			$prefix = '';
			$final_cats = "";
			foreach ($cat as $key => $value) {
			    $final_cats .= $prefix . $value;
			    $prefix = ', ';
			}
			$q = "&tax=$cat";
		}


		//print_r($final_cats); exit;
		// A keyword has been given; get posts whose content contains that keyword.
		if ( !empty($_GET['s']) ) {
			$q .= "&s=" . urlencode($_GET['s']);
		}

		// A tag has been given; get posts tagged with that tag.
		if ( !empty($_GET['t']) ) {
			$t = preg_replace('#[^a-z0-9\-\,\+]*#i', '', $_GET['t']);
			$q .= "&tag=$t";
		}


		//$query = new WP_Query;
		//$posts = $query->query($q);

		$args = array(
			'post_type' => 'wp-lead',
			'order' => $order,
			'orderby' => $orderby,
			'posts_per_page' => $per_page,
		);
		// if finished show results
		if (isset($_GET['on'])){
			$on_val = explode(",", $on);
			$prefix = '';
			$final_on = "";
			foreach ($on_val as $key => $value) {
			    $final_on .= $prefix . $value;
			    $prefix = ', ';
			}
			$args['post__in'] = $on_val;
			$args['order'] = 'DESC';
			$args['orderby'] = 'date';
			//$args['posts_per_page'] = -1;

		}


		if ((isset($_GET['wplead_list_category'])) && $_GET['wplead_list_category'][0] != "all" ){
			/*$args['tax_query'] = array(
							'relation' => 'AND',
									array(
										'taxonomy' => 'wplead_list_category',
										'field' => 'id',
										'terms' => array( $final_cats ),
									)
								); */
				/* Dynamic tax query */
				$tax_query = array( 'relation' => $relation );
				$taxonomy_array = $_GET['wplead_list_category'];
				foreach($taxonomy_array as $taxonomy_array_value){

				        $tax_query[] = array(
				        'taxonomy' => 'wplead_list_category',
				        'field'    => 'id',
				        'terms'    => array($taxonomy_array_value)

				    );

				}
			    $args['tax_query'] = $tax_query;
		}

	   // echo "<pre>";
	  /* print_r($args);
	   echo "<br><br>";
	   print_r($arg_s); exit;*/
		// Add tag to query
		if ((isset($_GET['t'])) && $_GET['t'] != "" ){
			$args['tag'] = $_GET['t'];
		}
		if ((isset($_GET['paged'])) && $_GET['paged'] != "1" ){
			$args['paged'] = $paged;
		}

		$query = new WP_Query( $args );
		$posts = $query->posts;
		//	print_r($posts); exit;
		// Pagination
		$pagination = '';
		if ( $query->max_num_pages > 1 ) {
			$current = preg_replace('/&?paged=[0-9]+/i', '', strip_tags($_SERVER['REQUEST_URI'])); // I'll happily take suggestions on a better way to do this, but it's 3am so

			$pagination .= "<div class='tablenav-pages'>";

			if ( $paged > 1 ) {
				$prev = $paged - 1;
				$pagination .= "<a class='prev page-numbers' href='$current&amp;paged=$prev'>&laquo; Previous</a>";
			}

			for ( $i = 1; $i <= $query->max_num_pages; $i++ ) {
				if ( $i == $paged ) {
					$pagination .= "<span class='page-numbers current'>$i</span>";
				} else {
					$pagination .= "<a class='page-numbers' href='$current&amp;paged=$i'>$i</a>";
				}
			}

			if ( $paged < $query->max_num_pages ) {
				$next = $paged + 1;
				$pagination .= "<a class='next page-numbers' href='$current&amp;paged=$next'>Next &raquo;</a>";
			}

			$pagination .= "</div>";
		}

		echo $pagination;
	}

	echo "</div>"; // tablenav
	//lead_dropdown_generator();
	// No posts have been fetched, let's tell the user:
	if ( empty($_GET['wplead_list_category']) && empty($_GET['s']) && empty($_GET['t']) && !isset($_GET['on']) ) {
		echo '';
		// List all leads?
	} else {
		// Criteria were given, but no posts were matched.
		if ( empty($posts) ) {
			echo '
				<p>No posts matched that criteria, sorry! Try again with something different.</p>
			';
		}
		// Criteria were given, posts were matched... let's go!
		else {
			$all_cats = $_GET['wplead_list_category'];
			$prefix = "";
			$name = "";
			if (isset($_GET['wplead_list_category']) && $_GET['wplead_list_category'][0] != 'all') {
				foreach ($all_cats as $key => $value) {
					$term = get_term( $_GET['wplead_list_category'][$key], 'wplead_list_category' );
				    $name .= $prefix . $term->name;
				    $prefix = ' and ';
				}
			} else {
				$name = "Total";
			}

			echo '
				<form method="get" action="'.admin_url( 'admin.php' ).'">
				<input type="hidden" name="action" value="lead_action" />
				<div id="posts">

				<table class="widefat" id="lead-manage-table">';
				if (!isset($_GET['on'])){
				echo'<caption style="margin-top:0px;">' .
						sprintf(
							'<h2><strong><span id="lead-total-found">%s</span> Leads Found</strong> in <strong>%s</strong></h2><strong>Additional Search Criteria:</strong> tagged with <strong><u>%s</u></strong>, %s ordered by <strong><u>%s</u></strong> %s.',
							$query->found_posts,
							!empty($_GET['wplead_list_category']) ? $name : 'any category',
							!empty($_GET['t']) ? htmlentities($_GET['t']) : 'any tag',
							!empty($_GET['s']) ? 'containing the string <strong>' . htmlentities($_GET['s']) . '</strong>, ' : '',
							strtolower($orderbys_flip[$orderby]),
							$order == 'asc' ? 'ascending' : 'descending'
						);
				} else {
					echo'<caption style="margin-top:0px;">';
				}
				echo '<div><input type="search" class="light-table-filter" data-table="widefat" placeholder="Filter Results Below" /><span id="search-icon"></span><span style="float:right;margin-top: 19px;margin-right: 3px;" id="display-lead-count"><i class="lead-spinner"></i><span id="lead-count-text">Grabbing Matching Leads</span></span></div>
				</caption>

					<thead>
						<tr>
							<th class="checkbox-header no-sort" scope="col"><input type="checkbox" id="toggle" title="Select all posts" /></th>
							<th class="count-sort-header" scope="col">#</th>
							<th scope="col">Date</th>
							<th scope="col">Email</th>
							<th scope="col">Current Lists</th>
							<th scope="col">Current Tags</th>
							<th scope="col" class="no-sort">View</th>
							<th scope="col">ID</th>
						</tr>
					</thead>
					<tbody id="the-list">
			';
			$loop_count = 1;
			foreach ( (array) $posts as $post ) {

				//$categories = wp_get_post_categories($post->ID);
				$this_tax = "wplead_list_category";
				$terms = wp_get_post_terms( $post->ID, $this_tax, 'id' );
				$cats = '';
				$lead_ID = $post->ID;
	         	foreach ( $terms as $term ) {
				  	$term_link = get_term_link( $term, $this_tax );
				    if( is_wp_error( $term_link ) )
				        continue;
				    //We successfully got a link. Print it out.

				    $cats .= '<span class="list-pill">' . $term->name . ' <i title="Remove This lead from the '.$term->name.' list" class="remove-from-list" data-lead-id="'.$lead_ID.'" data-list-id="'.$term->term_id.'"></i></span> ';
				}


				$_tags = wp_get_post_tags($post->ID);
				$tags = '';
				foreach ( $_tags as $tag ) {
					$tags .= "<a href='?page=lead_management&t=$tag->slug'>$tag->name</a>, ";
				}
				$tags = substr($tags, 0, strlen($tags) - 2);
				if ( empty ($tags) ) {
					$tags = 'No Tags';
				}

				echo '
						<tr' . ( $i++ % 2 == 0  ? ' class="alternate"' : '' ) .'>
							<td><input class="lead-select-checkbox" type="checkbox" name="ids[]" value="' . $post->ID . '" /></td>
							<td class="count-sort"><span>'.$loop_count.'</span></td>
							<td>

				';

				if ( '0000-00-00 00:00:00' == $post->post_date ) {
					_e('Unpublished');
				} else {

				  echo date(__('Y/m/d'), strtotime($post->post_date));

				}

				echo '</td>
							<td><span class="lead-email">' . $post->post_title . '</span></td>
							<td>' . $cats . '</td>
							<td>' . $tags . '</td>

							<td><a class="thickbox" href="post.php?action=edit&post=' . $post->ID . '&amp;small_lead_preview=true&amp;TB_iframe=true&amp;width=1345&amp;height=244">View</a></td>

							<td>' . $post->ID . '</td>
						</tr>
				';
				$loop_count++;
			}
			echo '
					</tbody>
				</table>
			';

			// Now, our actions.
			echo '
			<div id="all-actions" class="tablenav">

			<div id="inbound-lead-management"><span class="lead-actions-title">What do you want to do with the selected leads?</span>

			<div id="controls">';
			lead_management_drop_down();
		echo '
			</div>
				' . $filter . '
				<div id="lead-action-triggers">

				<div class="action" id="lead-update-lists">
					<label for="lead-update-lists" >Choose List:</label>';
					lead_select_taxonomy_dropdown( 'wplead_list_category', 'single', '_action' );
			echo '<input type="submit" name="add" value="Add to" title="Add the selected posts to this category." />
					<input type="submit" name="remove" value="Remove from" title="Remove the selected posts from this category." />
				</div>

				<div class="action" id="lead-update-tags">
					<label for="lead-update-tags">Tags:</label>
					<input type="text" name="tags" title="Separate multiple tags with commas." />
					<input type="submit" name="replace_tags" value="Replace" title="Replace the selected posts\' current tags with these ones." />
					<input type="submit" name="tag" value="Add" title="Add these tags to the selected posts without altering the posts\' existing tags." />
					<input type="submit" name="untag" value="Remove" title="Remove these tags from the selected posts." />
				</div>

				<div class="action" id="lead-update-meta">
					<label for="lead-update-meta">Meta:</label>
					<input type="text" name="meta_val" title="Separate multiple tags with commas." />
					<input type="submit" name="replace_meta" value="Replace" title="Replace the selected posts\' current meta values with these ones." />
					<input type="submit" name="meta" value="Add" title="Add these meta values to the selected posts without altering the posts\' existing tags." />
					<input type="submit" name="unmeta" value="Remove" title="Remove these meta values from the selected posts." />
				</div>

				<div class="action" id="lead-delete">
					<label for="lead-delete" id="del-label"><span style="color:red;">DELETE LEADS (There is no UNDO):</span></label>

					<input type="submit" name="delete_leads" value="Permanently Delete Selected Leads" title="This will delete the selected leads from your database. There is no undo." />

				</div></div>

				' . $pagination . '
			</div>
			';

			wp_nonce_field('lead_management-edit');
			echo '
				</form>
				</div>
			';
		}
	}
}

//add_action('wp_lead_before_dashboard', 'marketing_dashboard_before_callback');

function lead_management_drop_down() {
//wp_register_script( 'modernizr', LINUS_DASHBOARD_URLPATH . 'js/modernizr.custom.js' );
//wp_enqueue_script( 'modernizr' );
$url = get_option('siteurl');
?>
<section id="set-3">
<div class="fleft">
					<select id="cd-dropdown" class="cd-select">
						<option value="-1" selected class="db-drop-label">Choose action to apply to selected leads</option>

						<option value="lead-update-lists"  class="action-symbol lead-update-lists-symbol db-drop-label">Add or Remove Selected Leads from Lists</option>
						<option value="lead-update-tags"  class="action-symbol lead-update-tags-symbol db-drop-label">Add or Remove Tags to Selected Leads</option>
						<option value="lead-delete"  class="action-symbol lead-update-delete-symbol db-drop-label">Permanently Delete Selected Leads</option>
					</select>
				</div>


			</section>
<style type="text/css">

</style>
<script>
   jQuery(document).ready(function($) {
   	$( function() {

   					$( '#cd-dropdown' ).dropdown();

   				});
      // bind change event to select
      jQuery("body").on('click', '.cd-dropdown li', function () {
      		 var value = $(this).attr('data-value'); // get selected value
      		 console.log(value);

      		 if (value) { // require a URL
              $(".action").hide();
              $("#" + value).show();
          }
          return false;
		});
    });
</script>
<?php }


add_action( 'admin_action_lead_action', 'lead_action_admin_action' );
function lead_action_admin_action() {

	if ( !current_user_can('level_9') )
		die ( __('Cheatin&#8217; uh?') );

	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	// Check if we've been submitted a tag/remove.
	if ( !empty($_GET['ids']) ) {
		check_admin_referer('lead_management-edit');
		if(is_array($_GET['ids'])){
			$pass_ids = implode(',', $_GET['ids']);
		} else {
			$pass_ids = $_GET['ids'];
		}

		$cat = intval($_GET['wplead_list_category_action']);
		$num = count($_GET['ids']);


		if ( !empty($_GET['wplead_list_category_action']) )
			$query = '&cat=' . $_GET['wplead_list_category_action'];
		if ( !empty($_GET['s']) )
			$query = '&s=' . $_GET['s'];
		if ( !empty($_GET['t']) )
			$query = '&t=' . $_GET['t'];

		$term = get_term( $_GET['wplead_list_category_action'], 'wplead_list_category' );
		$name = $term->slug;
		$this_tax = "wplead_list_category";
		// We've been told to tag these posts with the given category.
		if ( !empty($_GET['add']) ) {

			foreach ( (array) $_GET['ids'] as $id ) {
				$id = intval($id);
				// $cats = wp_get_post_terms($id, "wplead_list_category_action"); // gets all cats

				$current_terms = wp_get_post_terms( $id, $this_tax, 'id' );
				$current_terms_count = count($terms);
				//print_r($current_terms);
				$all_terms = array();
				foreach ($current_terms as $term ) {
					$add = $term->term_id;
					$all_terms[] = $add;
				}

				//$cats = wp_get_post_categories($id);
				if ( !in_array($cat, $all_terms) ) {
					$all_terms[] = $cat;
					//wp_set_post_categories($id, $cats);
					wp_set_object_terms( $id, $all_terms, 'wplead_list_category');
				}
			}
			wp_redirect(get_option('siteurl') . "/wp-admin/edit.php?post_type=wp-lead&page=lead_management&done=add&what=" . $name . "&num=$num$query");
			die;
		}
		// We've been told to remove these posts from the given category.
		elseif ( !empty($_GET['remove']) ) {
			// wp_delete_term($wplead_cat_id,'wplead_list_category_action');
			foreach ( (array) $_GET['ids'] as $id ) {
				$id = intval($id);
				// $cats = wp_get_post_terms($id, "wplead_list_category_action"); // gets all cats

				$current_terms = wp_get_post_terms( $id, $this_tax, 'id' );
				$current_terms_count = count($terms);
				//print_r($current_terms);
				$all_remove_terms = '';
				foreach ($current_terms as $term ) {
					$add = $term->term_id;
					$all_remove_terms .= $add . ' ,';
				}
				$final = explode(' ,', $all_remove_terms);

				$final = array_filter($final, 'strlen');

				//$cats = wp_get_post_categories($id);
				if (in_array($cat, $final) ) {
					$new = array_flip ( $final );
					unset($new[$cat]);
					$save = array_flip ( $new );
					//print_r($save);
					//wp_set_post_categories($id, $cats);
					wp_set_object_terms( $id, $save, 'wplead_list_category');
				}
			}
			wp_redirect(get_option('siteurl') . "/wp-admin/edit.php?post_type=wp-lead&page=lead_management&done=remove&what=" . $name . "&num=$num");
			die;
		}
		// We've been told to tag these posts
		elseif ( !empty($_GET['tag']) || !empty($_GET['replace_tags']) ) {
			$tags = $_GET['tags'];
			foreach ( (array) $_GET['ids'] as $id ) {
				$id = intval($id);
				$append = empty($_GET['replace_tags']);
				wp_set_post_tags($id, $tags, $append);
			}
			wp_redirect(get_option('siteurl') . "/wp-admin/edit.php?post_type=wp-lead&page=lead_management&done=tag&what=$tags&num=$num$query&on=$pass_ids");
			die;
		}
		// We've been told to untag these posts
		elseif ( !empty($_GET['untag']) ) {
			$tags = explode(',', $_GET['tags']);
			foreach ( (array) $_GET['ids'] as $id ) {
				$id = intval($id);
				$existing = wp_get_post_tags($id);
				$new = array();
				foreach ( (array) $existing as $_tag ) {
					foreach ( (array) $tags as $tag ) {
						if ( $_tag->name != $tag ) {
							$new[] = $_tag->name;
						}
					}
				}
				wp_set_post_tags($id, $new);
			}
			$tags = join(', ', $tags);
			wp_redirect(get_option('siteurl') . "/wp-admin/edit.php?post_type=wp-lead&page=lead_management&done=untag&what=$tags&num=$num$query");
			die;
		}
		// Delete selected leads
		elseif ( !empty($_GET['delete_leads']) ) {
			foreach ( (array) $_GET['ids'] as $id ) {
				$id = intval($id);
				wp_delete_post( $id, true);
			}
			wp_redirect(get_option('siteurl') . "/wp-admin/edit.php?post_type=wp-lead&page=lead_management&done=delete_leads&what=" . $name . "&num=$num$query");
			die;

		}
	}
	die("Invalid action.");
}

?>