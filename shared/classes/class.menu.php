<?php
/* Inbound Now Menu Class */

if (!class_exists('Inbound_Menu')) {
	class Inbound_Menu {

		static $add_menu;
		static $go_button;
		static $inboundnow_menu_key;
		static $inboundnow_menu_secondary_group_key;
		static $load_forms;
		static $load_landingpages;
		static $load_callstoaction;
		static $load_leads;

		public static function init() {
			 // Exit if admin bar not there
			if ( ! is_user_logged_in() || ! is_admin_bar_showing() || !current_user_can('activate_plugins') ) {
			  return;
			}

			self::$add_menu = true;
			self::$go_button = '<input type="submit" value="' . __( 'GO', 'cta' ) . '" class="inbound-search-go"  /></form>';
			self::$inboundnow_menu_key = 'inbound-admin-bar';
			self::$inboundnow_menu_secondary_group_key = 'inbound-secondary';
			self::hooks();
			add_action( 'admin_bar_menu', array( __CLASS__ , 'load_inboundnow_menu' ), 98);
		}

		public static function load_inboundnow_menu() {
			global $wp_admin_bar;

			$primary_menu_items = apply_filters( 'inboundnow_menu_primary' , array() );
			$secondary_menu_items = apply_filters( 'inboundnow_menu_secondary' , array() );

			/* Add Parent Nav Menu - Inbound Marketing*/
			$wp_admin_bar->add_menu( array(
				'id'    => self::$inboundnow_menu_key,
				'title' => __( ' Marketing', 'cta' ),
				'href'  => "",
				'meta'  => array( 'class' => 'inbound-nav-marketing', 'title' => 'Inbound Marketing Admin' )
			) );

			//print_r($primary_menu_items);exit;

			/** Add Primary Menu Items */
			foreach ( $primary_menu_items as $id => $menu_item ) {
				/** Add in the item ID */
				$menu_item['id'] =  $id;

				/** Add meta target to each item where it's not already set, so links open in new window/tab */
				if ( ! isset( $menu_item['meta']['target'] ) ) {
					$menu_item['meta']['target'] = '_blank';
				}

				/** Add class to links that open up in a new window/tab */
				if ( '_blank' === $menu_item['meta']['target'] ) {

					if ( ! isset( $menu_item['meta']['class'] ) ) {
						$menu_item['meta']['class'] = '';
					}

					$menu_item['meta']['class'] .= 'inbound-new-tab';
				}

				/** Add menu items */
				$wp_admin_bar->add_node( $menu_item );
			}

			//var_dump($wp_admin_bar);exit;

			/* Add Secondary Menu Item Group */
			$wp_admin_bar->add_group( array(
				'parent' => self::$inboundnow_menu_key,
				'id'     => self::$inboundnow_menu_secondary_group_key,
				'meta'   => array( 'class' => 'ab-sub-secondary' )
			) );

			foreach ( $secondary_menu_items as $id => $menu_item )
			{
				$menu_item['id'] =  $id;

				if ( ! isset( $menu_item['meta']['target'] ) ) {
					$menu_item['meta']['target'] = '_blank';
				}

				if ( '_blank' === $menu_item['meta']['target'] )
				{
					if ( ! isset( $menu_item['meta']['class'] ) ) {
					  $menu_item['meta']['class'] = '';
					}

					$menu_item['meta']['class'] .= ' inbound-new-tab';
				}

				$wp_admin_bar->add_node( $menu_item );
			}

		}

		public static function hooks() {
			/* add filters here */
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_callstoaction') , 10 );
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_landingpages') , 10 );
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_leads') , 10 );
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_forms') , 10 );
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_manage_templates') , 10 );
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_settings') , 10 );
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_analytics') , 10 );
			add_filter('inboundnow_menu_primary' , array( __CLASS__ , 'load_seo') , 10 );


			add_filter('inboundnow_menu_secondary' , array( __CLASS__ , 'load_support') , 10 );
			add_filter('inboundnow_menu_secondary' , array( __CLASS__ , 'load_inbound_hq') , 10 );
			add_filter('inboundnow_menu_secondary' , array( __CLASS__ , 'load_debug') , 10 );
		}


		public static function load_leads( $menu_items ) {
			/* Check if Leads Active */
			if (function_exists( 'is_plugin_active' ) && !is_plugin_active('leads/wordpress-leads.php')) {
				return $menu_items;
			}

			$leads_key = 'inbound-leads';
			self::$load_forms = true;
			self::$load_leads = true;

			/* 1 - Lead Parent */
			$menu_items[ $leads_key ] = array(
				'parent' => self::$inboundnow_menu_key,
				'title'  => __( 'Leads', 'cta' ),
				'href'   => admin_url( 'edit.php?post_type=wp-lead' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'Manage Forms', 'cta' ) )
			);

			/* 1.1 - Leads search form */
			$leads_search_text = __( 'Search All Leads' , 'cta' );
			$menu_items['inbound-leads-search'] = array(
				'parent' => $leads_key,
				'title' => '<form id="inbound-menu-form" method="get" action="'.admin_url( 'edit.php?post_type=wp-lead' ).'" class=" " target="_blank">
				<input id="search-inbound-menu" type="text" placeholder="' . $leads_search_text . '" onblur="this.value=(this.value==\'\') ? \'' . $leads_search_text . '\' : this.value;" onfocus="this.value=(this.value==\'' . $leads_search_text . '\') ? \'\' : this.value;" value="' . $leads_search_text . '" name="s" value="' . esc_attr( 'Search Leads', 'cta' ) . '" class="text inbound-search-input" />
				<input type="hidden" name="post_type" value="wp-lead" />
				<input type="hidden" name="post_status" value="all" />
				' . self::$go_button ,
				'href'   => false,
				'meta'   => array( 'target' => '', 'title' => _x( 'Search Leads', 'Translators: For the tooltip', 'cta' ) )
			);

			/* 1.2 - View All Leads */
			$menu_items['inbound-leads-view'] = array(
				'parent' => $leads_key,
				'title'  => __( 'View All Leads', 'cta' ),
				'href'   => admin_url( 'edit.php?post_type=wp-lead' ),
				'meta'   => array( 'target' => '', 'title' => __( 'View All Forms', 'cta' ) )
			);

			/* 1.3 - View Lead Lists */
			$menu_items['inbound-leads-list'] = array(
				'parent' => $leads_key,
				'title'  => __( 'View Lead Lists', 'cta' ),
				'href'   => admin_url( 'edit-tags.php?taxonomy=wplead_list_category&post_type=wp-lead' ),
				'meta'   => array( 'target' => '', 'title' => __( 'View Lead Lists', 'cta' ) )
			);

			/* 1.4 - Create New Lead */
			$menu_items['inbound-leads-add'] = array(
				'parent' => $leads_key,
				'title'  => __( 'Create New Lead', 'cta' ),
				'href'   => admin_url( 'post-new.php?post_type=wp-lead' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new lead', 'cta' ) )
			);

			return $menu_items;
		}

		public static function load_callstoaction( $menu_items ) {
			/* Check if Calls To Action Active */
			if (function_exists( 'is_plugin_active' ) && !is_plugin_active('cta/wordpress-cta.php')) {
				return $menu_items;
			}

			$cta_key = 'inbound-cta';
			self::$load_forms = true;
			self::$load_callstoaction = true;

			/* 1 - Calls to Action */
			$menu_items[ $cta_key ] = array(
			  'parent' => self::$inboundnow_menu_key,
			  'title'  => __( 'Call to Actions', 'cta' ),
			  'href'   => admin_url( 'edit.php?post_type=wp-call-to-action' ),
			  'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'cta' ) )
			);

			/* 1.1 - View Calls to Action */
			$menu_items['inbound-cta-view'] = array(
			  'parent' => $cta_key,
			  'title'  => __( 'View Calls to Action List', 'cta' ),
			  'href'   => admin_url( 'post-new.php?post_type=wp-call-to-action' ),
			  'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'cta' ) )
			);

			/* 1.2 - Add Calls to Action */
			$menu_items['inbound-cta-add'] = array(
			  'parent' => $cta_key,
			  'title'  => __( 'Add New Call to Action', 'cta' ),
			  'href'   => admin_url( 'post-new.php?post_type=wp-call-to-action' ),
			  'meta'   => array( 'target' => '', 'title' => __( 'Add new call to action', 'cta' ) )
			);

			/* 1.3 - Calls to Action Categories */
			$menu_items['inbound-cta-categories'] = array(
				'parent' => $cta_key,
				'title'  => __( 'Categories', 'cta' ),
				'href'   => admin_url( 'edit-tags.php?taxonomy=wp_call_to_action_category&post_type=wp-call-to-action' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Categories', 'cta' ) )
			);

			/* 1.4 - Settings */
			if ( current_user_can( 'manage_options' )) {
				$menu_items['inbound-cta-settings'] = array(
					'parent' => $cta_key,
					'title'  => __( 'Settings', 'cta' ),
					'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_global_settings' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Manage Call to Action Settings', 'cta' ) )
				);
			}

			return $menu_items;
		}

		public static function load_landingpages( $menu_items )
		{
			/* Check if Landing Pages Active */
			if (function_exists( 'is_plugin_active' ) && !is_plugin_active('landing-pages/landing-pages.php')) {
				return  $menu_items;
			}

			$landing_pages_key = 'inbound-landingpages';
			self::$load_forms = true;
			self::$load_landingpages = true;

			/* 1 - Landing Pages */
			$menu_items[ $landing_pages_key ] = array(
				  'parent' => self::$inboundnow_menu_key,
				  'title'  => __( 'Landing Pages', 'cta' ),
				  'href'   => admin_url( 'edit.php?post_type=landing-page' ),
				  'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'cta' ) )
			);

			/* 1.1 - View Landing Pages */
			$menu_items['inbound-landingpages-view'] = array(
			  'parent' => $landing_pages_key,
			  'title'  => __( 'View Landing Pages List', 'cta' ),
			  'href'   => admin_url( 'edit.php?post_type=landing-page' ),
			  'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'cta' ) )
			);

			/* 1.2 - Add New Landing Pages */
			$menu_items['inbound-landingpages-add'] = array(
			  'parent' => $landing_pages_key,
			  'title'  => __( 'Add New Landing Page', 'cta' ),
			  'href'   => admin_url( 'post-new.php?post_type=landing-page' ),
			  'meta'   => array( 'target' => '', 'title' => __( 'Add new Landing Page', 'cta' ) )
			);

			/* 1.3 - Landing Pages Categories */
			$menu_items['inbound-landingpages-categories'] = array(
				'parent' => $landing_pages_key,
				'title'  => __( 'Categories', 'cta' ),
				'href'   => admin_url( 'edit-tags.php?taxonomy=landing_page_category&post_type=landing-page' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Categories', 'cta' ) )
			);

			/* 1.4 - Landing Pages Settings */
			if ( current_user_can( 'manage_options' )) {
				$menu_items['inbound-landingpages-settings'] = array(
					'parent' => $landing_pages_key,
					'title'  => __( 'Settings', 'cta' ),
					'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_global_settings' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Manage Landing Page Settings', 'cta' ) )
				);
			}


			return $menu_items;
		}

		public static function load_forms( $menu_items )
		{
			/* Check if Leads Active */
			if (!self::$load_forms) {
				return $menu_items;
			}

			$forms_key = 'inbound-forms';

			/* 1 - Manage Forms  */
			$menu_items[ $forms_key ] = array(
				'parent' => self::$inboundnow_menu_key,
				'title'  => __( 'Manage Forms', 'cta' ),
				'href'   => admin_url( 'edit.php?post_type=inbound-forms' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'Manage Forms', 'cta' ) )
			);

			/* 1.1 - View All Forms */
			$menu_items['inbound-forms-view'] = array(
				  'parent' => $forms_key,
				  'title'  => __( 'View All Forms', 'cta' ),
				  'href'   => admin_url( 'edit.php?post_type=inbound-forms' ),
				  'meta'   => array( 'target' => '', 'title' => __( 'View All Forms', 'cta' ) )
			);

			/* 1.1.x Get Forms and List */
			$forms = get_posts(array('post_type'=>'inbound-forms','post_status'=>'published'));
			foreach ($forms as $form)
			{
				$menu_items['inbound-form-'.$form->ID] = array(
				  'parent' => 'inbound-forms-view',
				  'title'  => $form->post_title,
				  'href'   => admin_url( 'post.php?post='.$form->ID.'&action=edit' ),
				  'meta'   => array( 'target' => '_blank', 'title' => $form->post_title )
				);
			}

			/* 1.2 - Create New Form */
			$menu_items['inbound-forms-add'] = array(
				'parent' => $forms_key,
				'title'  => __( 'Create New Form', 'cta' ),
				'href'   => admin_url( 'post-new.php?post_type=inbound-forms' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new call to action', 'cta' ) )
			);

			return $menu_items;
		}

		public static function load_manage_templates( $menu_items )
		{
			if ( !isset(self::$load_landingpages) || !isset(self::$load_callstoaction) ) {
				return $menu_items;
			}

			$templates_key = 'inbound-templates';

			/* 1 - Manage Templates */
			$menu_items[ $templates_key ] = array(
				'parent' => self::$inboundnow_menu_key,
				'title'  => __( 'Manage Templates', 'cta' ),
				'href'   => "",
				'meta'   => array( 'target' => '', 'title' => _x( 'Manage Templates', 'cta' ) )
			);

			/* 1.1 - Get More Templates */
			$menu_items['inbound-gettemplates'] = array(
				'parent' => $templates_key,
				'title'  => __( 'Download More Templates', 'cta' ),
				'href'   => "http://www.inboundnow.com/market",
				'meta'   => array( 'target' => '', 'title' => __( 'Download More Templates', 'cta' ) )
			);

			/* 1.1 - Landing Page Templates */
			if (isset(self::$load_landingpages)) {
				$menu_items['inbound-landingpagetemplates'] = array(
					'parent' => $templates_key,
					'title'  => __( 'Landing Page Templates', 'cta' ),
					'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_manage_templates' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Templates', 'cta' ) )
				);
			}

			/* 1.1 - Call To Action Templates */
			if (isset(self::$load_callstoaction)) {
				$menu_items['inbound-ctatemplates'] = array(
					'parent' => $templates_key,
					'title'  => __( 'Call to Action Templates', 'cta' ),
					'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_manage_templates' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Call to Action Templates', 'cta' ) )
				);
			}

			return $menu_items;
		}

		public static function load_settings( $menu_items )
		{
			$settings_key = 'inbound-settings';

			/* 1 - Global Settings */
			$menu_items[ $settings_key ] = array(
				'parent' => self::$inboundnow_menu_key,
				'title'  => __( 'Global Settings', 'cta' ),
				'href'   => "",
				'meta'   => array( 'target' => '', 'title' => _x( 'Manage Settings', 'cta' ) )
			);

			/* 1.1 - Call to Action Settings */
			if (self::$load_callstoaction) {
				$menu_items['inbound-ctasettings'] = array(
					'parent' => $settings_key,
					'title'  => __( 'Call to Action Settings', 'cta' ),
					'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_global_settings' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Call to Action Settings', 'cta' ) )
				);
			}

			if (self::$load_landingpages) {
				$menu_items['inbound-landingpagesettings'] = array(
					'parent' => $settings_key,
					'title'  => __( 'Landing Page Settings', 'cta' ),
					'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_global_settings' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Settings', 'cta' ) )
				);
			}

			if (self::$load_leads) {
				$menu_items['inbound-leadssettings'] = array(
					'parent' => $settings_key,
					'title'  => __( 'Lead Settings', 'cta' ),
					'href'   => admin_url( 'edit.php?post_type=wp-lead&page=wpleads_global_settings' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Lead Settings', 'cta' ) )
				);
			}

			return $menu_items;
		}

		public static function load_analytics( $menu_items )
		{
			$analytics_key = 'inbound-analytics';

			/* 1 - Analytics */
			$menu_items[ $analytics_key ] = array(
			  'parent' => self::$inboundnow_menu_key,
			  'title'  => __( 'Analytics (coming soon)', 'cta' ),
			  'href'   => '#',
			  'meta'   => array( 'target' => '', 'title' => __( 'Analytics (coming soon)', 'cta' ) )
			);

			return $menu_items;
		}

		public static function load_seo( $menu_items )
		{
			$seo_key = 'inbound-seo';

			if (function_exists( 'is_plugin_active' ) && is_plugin_active('wordpress-seo/wp-seo.php')) {
				$menu_items[ $seo_key ] = array(
					'parent' => self::$inboundnow_menu_key,
					'title'  => __( 'SEO by Yoast', 'cta' ),
					'href'   => admin_url( 'admin.php?page=wpseo_dashboard' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Manage SEO Settings', 'cta' ) )
				);
			}

			return $menu_items;
		}

		public static function load_support( $secondary_menu_items )
		{
			$support_key = 'inbound-support';

			/* 1 - Support Form */
			$secondary_menu_items[ $support_key ] = array(
				'parent' => self::$inboundnow_menu_secondary_group_key,
				'title'  => __( 'Support Forum', 'cta' ),
				'href'   => 'https://www.inboundnow.com/support/',
				'meta'   => array( 'target' => '_blank' , 'title' => __( 'Support Forum', 'cta' ) )
			);

			/* 1 - Documentation */
			$secondary_menu_items['inbound-docs'] = array(
				'parent' => self::$inboundnow_menu_secondary_group_key,
				'title'  => __( 'Documentation', 'cta' ),
				'href'   => 'http://docs.inboundnow.com/',
				'meta'   => array( 'title' => __( 'Documentation', 'cta' ) )
			);

			/* 1 - Doc Search */
			$search_docs_text = __( 'Search Docs', 'cta' );

			$secondary_menu_items['inbound-docs-searchform'] = array(
			  'parent' => self::$inboundnow_menu_secondary_group_key,
			  'title' => '<form method="get" id="inbound-menu-form" action="http://www.inboundnow.com/support/search/?action=bbp-search-request" class=" " target="_blank">
			  <input id="search-inbound-menu" type="text" placeholder="' . $search_docs_text . '" onblur="this.value=(this.value==\'\') ? \'' . $search_docs_text . '\' : this.value;" onfocus="this.value=(this.value==\'' . $search_docs_text . '\') ? \'\' : this.value;" value="' . $search_docs_text . '" name="bbp_search" value="' . esc_attr( 'Search Docs', 'cta' ) . '" class="text inbound-search-input" />
			  <input type="hidden" name="post_type[]" value="docs" />
			  <input type="hidden" name="post_type[]" value="page" />' . self::$go_button,
			  'href'   => false,
			  'meta'   => array( 'target' => '', 'title' => _x( 'Search Docs', 'Translators: For the tooltip', 'cta' ) )
			);

			return $secondary_menu_items;
		}

		public static function load_inbound_hq( $secondary_menu_items )
		{
			$hq_key = 'inbound-hq';

			/* 1 - Inbound Now Plugin HQ */
			$secondary_menu_items[ $hq_key ] = array(
			  'parent' => self::$inboundnow_menu_secondary_group_key,
			  'title'  => __( 'Inbound Now Plugin HQ', 'cta' ),
			  'href'   => 'https://www.inboundnow.com/',
			  'meta'   => array( 'title' => __( 'Inbound Now Plugin HQ', 'cta' ) )
			);

			/* 1.1 - GitHub Link */
			$secondary_menu_items['inbound-sites-dev'] = array(
				'parent' => $hq_key,
				'title'  => __( 'GitHub Repository Developer Center', 'cta' ),
				'href'   => 'https://github.com/inboundnow',
				'meta'   => array( 'title' => __( 'GitHub Repository Developer Center', 'cta' ) )
			);

			/* 1.2 - Offical Blog */
			$secondary_menu_items['inbound-sites-blog'] = array(
				'parent' => $hq_key,
				'title'  => __( 'Official Blog', 'cta' ),
				'href'   => 'https://www.inboundnow.com/blog/',
				'meta'   => array( 'title' => __( 'Official Blog', 'cta' ) )
			);

			/* 1.3 - My Account */
			$secondary_menu_items['inboundsitesaccount'] = array(
				'parent' => $hq_key,
				'title'  => __( 'My Account', 'cta' ),
				'href'   => 'https://www.inboundnow.com/marketplace/account/',
				'meta'   => array( 'title' => __( 'My Account', 'cta' ) )
			);

			/* 1.3.1 - Purchase History */
			$secondary_menu_items['inboundsitesaccount-history'] = array(
				'parent' => 'inboundsitesaccount',
				'title'  => __( 'Purchase History', 'cta' ),
				'href'   => 'https://www.inboundnow.com/marketplace/account/purchase-history/',
				'meta'   => array( 'title' => __( 'Purchase History', 'cta' ) )
			);

			return $secondary_menu_items;
		}

		public static function load_debug( $secondary_menu_items )
		{
			$debug_key = 'inbound-debug';

			/* 1 - Debug Tools */
			$secondary_menu_items[ $debug_key ] = array(
			  'parent' => self::$inboundnow_menu_secondary_group_key,
			  'title'  => __( '<span style="color:#fff;font-size: 13px;margin-top: -1px;display: inline-block;">Debug Tools</span>', 'cta' ),
			  'href'   => "#",
			  'meta'   => ""
			);

			/* 1.1 - 1.2 - Link Setup */
			$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			$param = (preg_match("/\?/", $actual_link)) ? "&" : '?';
			if (preg_match("/inbound-dequeue-scripts/", $actual_link)) {
				$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			} else {
				$actual_link = $actual_link . $param .'inbound-dequeue-scripts';
			}

			$actual_link_two = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$param_two = (preg_match("/\?/", $actual_link_two)) ? "&" : '?';
			if (preg_match("/inbound_js/", $actual_link_two)) {
				$actual_link_two = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			} else {
				$actual_link_two = $actual_link_two . $param_two .'inbound_js';
			}

			 /* 1.1 - Check for JS Errors */
			$secondary_menu_items['inbound-debug-checkjs'] = array(
			  'parent' => $debug_key,
			  'title'  => __( 'Check for Javascript Errors', 'cta' ),
			  'href'   => $actual_link_two,
			  'meta'   => array( 'title' =>  __( 'Click here to check javascript errors on this page', 'cta' ) )
			);

			/* 1.2 - Check for JS Errors */
			$secondary_menu_items['inbound-debug-turnoffscripts'] = array(
			  'parent' => $debug_key,
			  'title'  => __( 'Remove Javascript Errors', 'cta' ),
			  'href'   => $actual_link,
			  'meta'   => array( 'title' =>  __( 'Click here to remove broken javascript to fix issues', 'cta' ) )
			);

			return apply_filters('inbound_menu_debug' , $secondary_menu_items , $debug_key );
		}
	}

	add_action('init' , array( 'Inbound_Menu' , 'init' ) );
}
