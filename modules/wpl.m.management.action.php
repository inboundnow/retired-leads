<?php

// ADD new list $term = wp_insert_term( $list_title, 'wplead_list_category_action', $args = array('slug'=>$list_slug) );

$admin = realpath(dirname(__FILE__) . '/../../../../') . '/wp-admin';
chdir($admin);
require_once $admin . '/admin.php';

if ( !current_user_can('level_9') )
	die ( __('Cheatin&#8217; uh?') );

$_POST = stripslashes_deep($_POST);
$_GET = stripslashes_deep($_GET);

// Check if we've been submitted a tag/remove.
if ( !empty($_GET['ids']) ) {
	check_admin_referer('lead_management-edit');

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
		wp_redirect(get_option('siteurl') . "/wp-admin/edit.php?post_type=wp-lead&page=lead_management&done=tag&what=$tags&num=$num$query");
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


?>