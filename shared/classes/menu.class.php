<?php
/* Inbound Now Menu Class */
if ( ! defined( 'LANDINGPAGES_TEXT_DOMAIN' ) ) {
  define('LANDINGPAGES_TEXT_DOMAIN', 'landing-pages' );
}
if (!class_exists('InboundMenu')) {
  class InboundMenu {
    static $add_menu;

  /*  Contruct
   *  --------------------------------------------------------- */
    static function init() {
      self::$add_menu = true;

      add_action('admin_bar_menu', array( __CLASS__, 'loads' ), 98);
      add_action( 'wp_head', array(__CLASS__, 'menu_admin_head'));
      add_action( 'admin_head', array(__CLASS__, 'menu_admin_head'));
    }

    /*  Loads
    *  --------------------------------------------------------- */
    static function loads($hook) {
      if ( ! self::$add_menu )
      return;
        global $wp_admin_bar, $locale, $edd_options, $eddtb_edd_name, $eddtb_edd_name_tooltip;

        /** Get the proper 'Download' post type ID/tag */
        if ( post_type_exists( 'landing-page' ) ) {
          $eddtb_download_cpt = 'landing-page';
        } elseif ( post_type_exists( 'landing-page' ) ) {
          $eddtb_download_cpt = 'landing-page';
        } else {
          $eddtb_download_cpt = '';
        }

        // CHECK FOR ACTIVE PLUGINS
        $leads_status = FALSE; $landing_page_status = FALSE; $cta_status = FALSE;
        if (function_exists( 'is_plugin_active' ) && is_plugin_active('leads/wordpress-leads.php')) {
          $leads_status = TRUE;
          $leads_version_number = defined( 'LEADS_CURRENT_VERSION' ) ? 'v' . LEADS_CURRENT_VERSION : '';
        }
        if (function_exists( 'is_plugin_active' ) && is_plugin_active('landing-pages/landing-pages.php')) {
          $landing_page_status = TRUE;
          $landing_page_version_number = defined( 'LANDINGPAGES_CURRENT_VERSION' ) ? 'v' . LANDINGPAGES_CURRENT_VERSION : '';

        }
        if (function_exists( 'is_plugin_active' ) && is_plugin_active('cta/wordpress-cta.php')) {
          $cta_status = TRUE;
          $cta_number = defined( 'WP_CTA_CURRENT_VERSION' ) ? 'v' . WP_CTA_CURRENT_VERSION : '';
        }

        if ( $leads_status == FALSE && $landing_page_status == FALSE && $cta_status == FALSE  ) {

          return; // end plugin is

        }
        /** EDD version number for later use */
        $edd_version_number = defined( 'EDD_VERSION' ) ? 'v' . EDD_VERSION : '';

        /** Resources links EDD settings check*/
        $eddtb_resources_check = 'default';

        if ( ( ! isset( $edd_options['eddtb_remove_resources'] ) && ! isset( $edd_options['eddtb_remove_translation_resources'] ) )
          || ( isset( $edd_options['eddtb_remove_resources'] ) && ! isset( $edd_options['eddtb_remove_translation_resources'] ) )
          || ( ! isset( $edd_options['eddtb_remove_resources'] ) && isset( $edd_options['eddtb_remove_translation_resources'] ) )

        ) {

          $eddtb_resources_check = 'eddtb_resources_yes';

        }  // end-if resources settings check


        /**
         * Allows for filtering the general user role/capability to display main & sub-level items
         *
         * Default capability: 'edit_posts' (we need this for the "Downloads" post type set by EDD itself!)
         *
         * @since 1.0.0
         */
        $eddtb_cap_default = class_exists( 'EDD_Roles' ) ? 'edit_products' : 'edit_posts';
        $eddtb_filter_capability = apply_filters( 'eddtb_filter_capability_all', $eddtb_cap_default );

        // Exit if admin bar not there
        if ( ! is_user_logged_in() || ! is_admin_bar_showing() ) {
          return;
        }

        /** Set unique prefix */
        $prefix = 'inbound-';
        /** Create parent menu item references */
        $inbounddocs = $prefix . 'inbounddocs';         // sub level: edd docs
        $inbounddocsquick = $prefix . 'inbounddocsquick';     // third level: docs quick links
        $inbounddocssections = $prefix . 'inbounddocssections';     // third level: docs sections
        $inboundreports = $prefix . 'inboundreports';       // sub level: edd reports
        $inboundgroup = $prefix . 'inboundgroup';       // sub level: edd group (resources)
        $inboundbar = $prefix . 'admin-bar';        // root level
        $inboundsupport = $prefix . 'inboundsupport';       // sub level: edd support
        $inboundsupportsections = $prefix . 'inboundsupportsections';   // third level: support sections
        $inboundsupportaccount = $prefix . 'inboundsupportaccount';   // third level: support user account
        $inboundsites = $prefix . 'inboundsites';       // sub level: edd sites
        $inboundsitesaccount = $prefix . 'inboundsitesaccount';
        $inboundsitesextensions = $prefix . 'inboundsitesextensions';   // third level: edd extensions
        $landingpages_menu = $prefix . 'landingpages';
        $cta_menu = $prefix . 'cta';
        $leads_menu = $prefix . 'leads';
        $form_menu = $prefix . 'inboundforms';
        $settings_menu = $prefix . 'inboundsettings';
        $templates_menu = $prefix . 'inboundtemplates';
        $landingpagesettings = $prefix . 'landingpagesettings';
        $ctasettings = $prefix . 'ctasettings';
        $landing_pages_templates = $prefix . 'landingpagetemplates';
        $cta_templates = $prefix . 'ctatemplates';

        /** Make the "EDD" name filterable within menu items */
        $eddtb_edd_name = apply_filters( 'eddtb_filter_edd_name', __( 'EDD', 'edd-toolbar' ) );

        /** Make the "Easy Digital Downloads" name's tooltip filterable within menu items */
        $eddtb_edd_name_tooltip = apply_filters( 'eddtb_filter_edd_name_tooltip', _x( 'Easy Digital Downloads', 'Translators: For the tooltip', 'edd-toolbar' ) );


        /** "Sub Forum" string */
        $eddtb_sub_forum = __( 'Sub Forum', 'edd-toolbar' ) . ': ';

        /** For the Documentation search */
        $eddtb_search_docs = __( 'Search Docs', 'edd-toolbar' );
        $eddtb_go_button = '<input type="submit" value="' . __( 'GO', 'edd-toolbar' ) . '" class="eddtb-search-go"  /></form>';

        /** Show these items only if Inbound Now plugin is actually installed */
        if ( $leads_status == TRUE || $landing_page_status == TRUE || $cta_status == TRUE ) {

          /** Set EDD active variable for later user */
          $edd_active = 'edd_is_active';

          /** EDD main downloads section */
          if ( current_user_can( 'edit_posts' ) && $landing_page_status) {

            $menu_items['landingpages'] = array(
              'parent' => $inboundbar,
              'title'  => __( 'Landing Pages', LANDINGPAGES_TEXT_DOMAIN ),
              'href'   => admin_url( 'edit.php?post_type=landing-page' ),
              'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'edd-toolbar' ) )
            );
            $menu_items['landingpages-view'] = array(
              'parent' => $landingpages_menu,
              'title'  => __( 'View Landing Pages List', LANDINGPAGES_TEXT_DOMAIN ),
              'href'   => admin_url( 'edit.php?post_type=landing-page' ),
              'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'edd-toolbar' ) )
            );
            $menu_items['landingpages-add'] = array(
              'parent' => $landingpages_menu,
              'title'  => __( 'Add New Landing Page', LANDINGPAGES_TEXT_DOMAIN ),
              'href'   => admin_url( 'post-new.php?post_type=landing-page' ),
              'meta'   => array( 'target' => '', 'title' => __( 'Add new Landing Page', 'edd-toolbar' ) )
            );

            $menu_items['landingpages-categories'] = array(
                'parent' => $landingpages_menu,
                'title'  => __( 'Categories', LANDINGPAGES_TEXT_DOMAIN ),
                'href'   => admin_url( 'edit-tags.php?taxonomy=landing_page_category&post_type=landing-page' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Categories', 'edd-toolbar' ) )
              );
            if ( current_user_can( 'manage_options' )) {
            $menu_items['landingpages-settings'] = array(
                'parent' => $landingpages_menu,
                'title'  => __( 'Settings', LANDINGPAGES_TEXT_DOMAIN ),
                'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_global_settings' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Manage Landing Page Settings', 'edd-toolbar' ) )
              );
            }
      //
          }

          /** EDD main downloads section */
          if ( current_user_can( 'edit_posts' ) && $cta_status) {

            $menu_items['cta'] = array(
              'parent' => $inboundbar,
              'title'  => __( 'Call to Actions', LANDINGPAGES_TEXT_DOMAIN ),
              'href'   => admin_url( 'edit.php?post_type=wp-call-to-action' ),
              'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'edd-toolbar' ) )
            );
            $menu_items['cta-view'] = array(
              'parent' => $cta_menu,
              'title'  => __( 'View Calls to Action List', LANDINGPAGES_TEXT_DOMAIN ),
              'href'   => admin_url( 'post-new.php?post_type=wp-call-to-action' ),
              'meta'   => array( 'target' => '', 'title' => __( 'View All Landing Pages', 'edd-toolbar' ) )
            );
            $menu_items['cta-add'] = array(
              'parent' => $cta_menu,
              'title'  => __( 'Add New Call to Action', LANDINGPAGES_TEXT_DOMAIN ),
              'href'   => admin_url( 'post-new.php?post_type=wp-call-to-action' ),
              'meta'   => array( 'target' => '', 'title' => __( 'Add new call to action', 'edd-toolbar' ) )
            );

            $menu_items['cta-categories'] = array(
                'parent' => $cta_menu,
                'title'  => __( 'Categories', LANDINGPAGES_TEXT_DOMAIN ),
                'href'   => admin_url( 'edit-tags.php?taxonomy=wp_call_to_action_category&post_type=wp-call-to-action' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Categories', 'edd-toolbar' ) )
              );
            if ( current_user_can( 'manage_options' )) {
            $menu_items['cta-settings'] = array(
                'parent' => $cta_menu,
                'title'  => __( 'Settings', LANDINGPAGES_TEXT_DOMAIN ),
                'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_global_settings' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Manage Call to Action Settings', 'edd-toolbar' ) )
              );
            }

          }

          /** admin settings sections */
          if ( current_user_can( 'manage_options' )) {
            // Leads Menu
            if ($leads_status) {
            $menu_items['leads'] = array(
                'parent' => $inboundbar,
                'title'  => __( 'Leads', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=inbound-forms' ),
                'meta'   => array( 'target' => '', 'title' => _x( 'Manage Forms', 'edd-toolbar' ) )
            );
              $menu_items['leads-view'] = array(
                  'parent' => $leads_menu,
                  'title'  => __( 'View All Leads', LANDINGPAGES_TEXT_DOMAIN ),
                  'href'   => admin_url( 'edit.php?post_type=wp-lead' ),
                  'meta'   => array( 'target' => '', 'title' => __( 'View All Forms', 'edd-toolbar' ) )
                );
              $menu_items['leads-add'] = array(
                'parent' => $leads_menu,
                'title'  => __( 'Manually Create New Lead', LANDINGPAGES_TEXT_DOMAIN ),
                'href'   => admin_url( 'post-new.php?post_type=wp-lead' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Add new lead', 'edd-toolbar' ) )
              );
            }


            $menu_items['inboundforms'] = array(
                'parent' => $inboundbar,
                'title'  => __( 'Manage Forms', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=inbound-forms' ),
                'meta'   => array( 'target' => '', 'title' => _x( 'Manage Forms', 'edd-toolbar' ) )
            );
              $menu_items['inboundforms-view'] = array(
                  'parent' => $form_menu,
                  'title'  => __( 'View All Forms', LANDINGPAGES_TEXT_DOMAIN ),
                  'href'   => admin_url( 'edit.php?post_type=inbound-forms' ),
                  'meta'   => array( 'target' => '', 'title' => __( 'View All Forms', 'edd-toolbar' ) )
                );
              $menu_items['inboundforms-add'] = array(
                'parent' => $form_menu,
                'title'  => __( 'Create New Form', LANDINGPAGES_TEXT_DOMAIN ),
                'href'   => admin_url( 'post-new.php?post_type=inbound-forms' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Add new call to action', 'edd-toolbar' ) )
              );
          /** Template Setup */
          if ($landing_page_status || $cta_status) {

            $menu_items['inboundtemplates'] = array(
                'parent' => $inboundbar,
                'title'  => __( 'Manage Templates', 'edd-toolbar' ),
                'href'   => "",
                'meta'   => array( 'target' => '', 'title' => _x( 'Manage Templates', 'edd-toolbar' ) )
            );
              $menu_items['getmoretemplates'] = array(
                'parent' => $templates_menu,
                'title'  => __( 'Download More Templates', 'edd-toolbar' ),
                'href'   => "http://www.inboundnow.com/market",
                'meta'   => array( 'target' => '', 'title' => __( 'Download More Templates', 'edd-toolbar' ) )
              );
            if ($landing_page_status){
              $menu_items['landingpagetemplates'] = array(
                'parent' => $templates_menu,
                'title'  => __( 'Landing Page Templates', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_manage_templates' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Settings', 'edd-toolbar' ) )
              );

              $menu_items['landingpagetemplates-main'] = array(
                'parent' => $landing_pages_templates,
                'title'  => __( 'Add New Landing Page Templates', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_manage_templates' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Global Settings', 'edd-toolbar' ) )
              );
            }
            if ($cta_status){
              $menu_items['ctatemplates'] = array(
                'parent' => $templates_menu,
                'title'  => __( 'Call to Action Templates', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_manage_templates' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Global Settings', 'edd-toolbar' ) )
              );

              $menu_items['ctatemplates-main'] = array(
                  'parent' => $cta_templates,
                  'title'  => __( 'Add New CTA Templates', 'edd-toolbar' ),
                  'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_manage_templates' ),
                  'meta'   => array( 'target' => '', 'title' => __( 'Global Settings', 'edd-toolbar' ) )
              );
            }

          }
          /** Settings Items */
            $menu_items['inboundsettings'] = array(
                'parent' => $inboundbar,
                'title'  => __( 'Global Settings', 'edd-toolbar' ),
                'href'   => "",
                'meta'   => array( 'target' => '', 'title' => _x( 'Manage Settings', 'edd-toolbar' ) )
            );
            if ($landing_page_status){
              $menu_items['landingpagesettings'] = array(
                'parent' => $settings_menu,
                'title'  => __( 'Landing Page Settings', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_global_settings' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Landing Page Settings', 'edd-toolbar' ) )
              );
              /*
              $menu_items['landingpagesettings-main'] = array(
                'parent' => $landingpagesettings,
                'title'  => __( 'Main', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=landing-page&page=lp_global_settings' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Global Settings', 'edd-toolbar' ) )
              ); */
            }
            if ($cta_status){
              $menu_items['ctasettings'] = array(
                'parent' => $settings_menu,
                'title'  => __( 'Call to Action Settings', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_global_settings' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Call to Action Settings', 'edd-toolbar' ) )
              );
              /*
              $menu_items['ctasettings-main'] = array(
                  'parent' => $ctasettings,
                  'title'  => __( 'Main', 'edd-toolbar' ),
                  'href'   => admin_url( 'edit.php?post_type=wp-call-to-action&page=wp_cta_global_settings' ),
                  'meta'   => array( 'target' => '', 'title' => __( 'Global Settings', 'edd-toolbar' ) )
              ); */
            }
            if ($leads_status){
              $menu_items['leadssettings'] = array(
                'parent' => $settings_menu,
                'title'  => __( 'Lead Settings', 'edd-toolbar' ),
                'href'   => admin_url( 'edit.php?post_type=wp-lead&page=wpleads_global_settings' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Lead Settings', 'edd-toolbar' ) )
              );
            }

            $menu_items['inboundreports'] = array(
              'parent' => $inboundbar,
              'title'  => __( 'Analytics (coming soon)', 'edd-toolbar' ),
              'href'   => '#',
              'meta'   => array( 'target' => '', 'title' => __( 'Analytics (coming soon)', 'edd-toolbar' ) )
            );

            if (function_exists( 'is_plugin_active' ) && is_plugin_active('wordpress-seo/wp-seo.php')) {
              $menu_items['inboundseo'] = array(
                'parent' => $inboundbar,
                'title'  => __( 'SEO by Yoast', 'edd-toolbar' ),
                'href'   => admin_url( 'admin.php?page=wpseo_dashboard' ),
                'meta'   => array( 'target' => '', 'title' => __( 'Manage SEO Settings', 'edd-toolbar' ) )
              );
            }

            $inboundsecondary_menu_items['inboundsupport'] = array(
              'parent' => $inboundgroup,
              'title'  => __( 'Support Forum', 'edd-toolbar' ),
              'href'   => 'https://www.inboundnow.com/support/',
              'meta'   => array( 'target' => '_blank' , 'title' => __( 'Support Forum', 'edd-toolbar' ) )
            );

            /** Documentation menu items */
            $inboundsecondary_menu_items['inbounddocs'] = array(
              'parent' => $inboundgroup,
              'title'  => __( 'Documentation', 'edd-toolbar' ),
              'href'   => 'http://docs.inboundnow.com/',
              'meta'   => array( 'title' => __( 'Documentation', 'edd-toolbar' ) )
            );

            /** Docs search form */
            $inboundsecondary_menu_items['inbounddocs-searchform'] = array(
              'parent' => $inboundgroup,
              'title' => '<form method="get" action="http://www.inboundnow.com/support/search/?action=bbp-search-request" class=" " target="_blank">
              <input type="text" placeholder="' . $eddtb_search_docs . '" onblur="this.value=(this.value==\'\') ? \'' . $eddtb_search_docs . '\' : this.value;" onfocus="this.value=(this.value==\'' . $eddtb_search_docs . '\') ? \'\' : this.value;" value="' . $eddtb_search_docs . '" name="bbp_search" value="' . esc_attr( 'Search Docs', 'edd-toolbar' ) . '" class="text eddtb-search-input" />
              <input type="hidden" name="post_type[]" value="docs" />
              <input type="hidden" name="post_type[]" value="page" />' . $eddtb_go_button,
              'href'   => false,
              'meta'   => array( 'target' => '', 'title' => _x( 'Search Docs', 'Translators: For the tooltip', 'edd-toolbar' ) )
            );

            /** Easy Digital Downloads HQ menu items */
            $inboundsecondary_menu_items['inboundsites'] = array(
              'parent' => $inboundgroup,
              'title'  => __( 'Inbound Now Plugin HQ', 'edd-toolbar' ),
              'href'   => 'https://www.inboundnow.com/',
              'meta'   => array( 'title' => $eddtb_edd_name_tooltip . ' ' . __( 'Plugin HQ', 'edd-toolbar' ) )
            );

            /** HQ: GitHub */
            $inboundsecondary_menu_items['inboundsites-dev'] = array(
              'parent' => $inboundsites,
              'title'  => __( 'GitHub Repository Developer Center', 'edd-toolbar' ),
              'href'   => 'https://github.com/inboundnow',
              'meta'   => array( 'title' => __( 'GitHub Repository Developer Center', 'edd-toolbar' ) )
            );

            /** HQ: Blog */
            $inboundsecondary_menu_items['inboundsites-blog'] = array(
              'parent' => $inboundsites,
              'title'  => __( 'Official Blog', 'edd-toolbar' ),
              'href'   => 'https://www.inboundnow.com/blog/',
              'meta'   => array( 'title' => __( 'Official Blog', 'edd-toolbar' ) )
            );

            /** HQ: Site Account */
            $inboundsecondary_menu_items['inboundsitesaccount'] = array(
              'parent' => $inboundsites,
              'title'  => __( 'My Account', 'edd-toolbar' ),
              'href'   => 'https://www.inboundnow.com/marketplace/account/',
              'meta'   => array( 'title' => __( 'My Account', 'edd-toolbar' ) )
            );

              $inboundsecondary_menu_items['inboundsitesaccount-history'] = array(
                'parent' => $inboundsitesaccount,
                'title'  => __( 'Purchase History', 'edd-toolbar' ),
                'href'   => 'https://www.inboundnow.com/marketplace/account/purchase-history/',
                'meta'   => array( 'title' => __( 'Purchase History', 'edd-toolbar' ) )
              );
            /*
            $menu_items['edd-systeminfo'] = array(
              'parent' => $inboundgroup,
              'title'  => __( 'System Info (Debug)', 'edd-toolbar' ),
              'href'   => admin_url( 'edit.php?post_type=landing-page&page=edd-system-info' ),
              'meta'   => array( 'target' => '', 'title' => __( 'System Info (Debug)', 'edd-toolbar' ) )
            );
            */


          } // end-if global settings/options cap checks (including backward compatibility)

        } else {

          /** If Easy Digital Downloads is not active, to avoid PHP notices */
          if ( 'eddtb_resources_yes' == $eddtb_resources_check && $inboundsecondary_menu_items ) {
            $menu_items = $inboundsecondary_menu_items;
          }

          /** If Easy Digital Downloads is not active and no icon filter is active, then display no icon */
          if ( ! has_filter( 'eddtb_filter_main_icon' ) ) {
            add_filter( 'eddtb_filter_main_item_icon_display', '__eddtb_no_icon_display' );
          }

        }



        /** Allow menu items to be filtered, but pass in parent menu item IDs */
        $menu_items = (array) apply_filters( 'ddw_eddtb_menu_items', $menu_items, ( 'eddtb_resources_yes' == $eddtb_resources_check ) ? $inboundsecondary_menu_items : '',
          $prefix,
          $inboundbar,
          $inboundsupport,
          $inboundsupportsections,
          $inboundsupportaccount,
          $inbounddocs,
          $inbounddocsquick,
          $inbounddocssections,
          $inboundsites,
          $inboundsitesaccount,
          $inboundsitesextensions,
          $inboundreports,
          $inboundgroup,
          // NEW PREFIXES
          $landingpages_menu,
          $cta_menu,
          $form_menu,
          $settings_menu,
          $templates_menu,
          $landingpagesettings,
          $ctasettings,
          $landing_pages_templates,
          $cta_templates
        );  // end of array


          /** Filter the main item icon's class/display */
          $eddtb_main_item_icon_display = apply_filters( 'eddtb_filter_main_item_icon_display', 'icon-edd' );

          $wp_admin_bar->add_menu( array(
            'id'    => $inboundbar,
            'title' => __( ' Marketing', 'edd-toolbar' ),
            'href'  => "",
            'meta'  => array( 'class' => $eddtb_main_item_icon_display, 'title' => 'Inbound Marketing Admin' )
          ) );


        /** Loop through the menu items */
        foreach ( $menu_items as $id => $menu_item ) {

          /** Add in the item ID */
          $menu_item['id'] = $prefix . $id;

          /** Add meta target to each item where it's not already set, so links open in new window/tab */
          if ( ! isset( $menu_item['meta']['target'] ) )
            $menu_item['meta']['target'] = '_blank';

          /** Add class to links that open up in a new window/tab */
          if ( '_blank' === $menu_item['meta']['target'] ) {
            if ( ! isset( $menu_item['meta']['class'] ) )
              $menu_item['meta']['class'] = '';
            $menu_item['meta']['class'] .= $prefix . 'eddtb-new-tab';
          }

          /** Add menu items */
          $wp_admin_bar->add_menu( $menu_item );

        }  // end foreach menu items


        /**
         * Action Hook 'eddtb_custom_main_items'
         * allows for hooking other main items in
         *
         * @since 1.2.0
         */
        do_action( 'eddtb_custom_main_items' );


        /**  add special blue links
        $wp_admin_bar->add_group( array(
          'parent' => $eddsettings,
          'id'     => $eddspecials
        ) );
        */

        /** Adds search box and sub */
        $wp_admin_bar->add_group( array(
          'parent' => $inboundbar,
          'id'     => $inboundgroup,
          'meta'   => array( 'class' => 'ab-sub-secondary' )
        ) );


        // Load grey secondary items
        foreach ( $inboundsecondary_menu_items as $id => $inboundgroup_menu_item ) {

          /** EDD Group: Add in the item ID */
          $inboundgroup_menu_item['id'] = $prefix . $id;

          /** EDD Group: Add meta target to each item where it's not already set, so links open in new window/tab */
          if ( ! isset( $inboundgroup_menu_item['meta']['target'] ) )
            $inboundgroup_menu_item['meta']['target'] = '_blank';

          /** EDD Group: Add class to links that open up in a new window/tab */
          if ( '_blank' === $inboundgroup_menu_item['meta']['target'] ) {

            if ( ! isset( $inboundgroup_menu_item['meta']['class'] ) ) {
              $inboundgroup_menu_item['meta']['class'] = '';
            }

            $inboundgroup_menu_item['meta']['class'] .= $prefix . 'eddtb-new-tab';

          }

          /** EDD Group: Add menu items */
          $wp_admin_bar->add_menu( $inboundgroup_menu_item );

        }  // end foreach EDD Group


        /**
         * Action Hook 'eddtb_custom_group_items'
         * allows for hooking other EDD Group items in
         *
         * @since 1.2.0
         */
      //  do_action( 'eddtb_custom_group_items' );

    }

    static function menu_admin_head() {
      /** No styles if admin bar is disabled or user is not logged in or items are disabled via constant */
      if ( ! is_admin_bar_showing() || ! is_user_logged_in() ) {
        return;
      }
      if ( defined( 'WPL_URL' )) {
         $final_path = WPL_URL . "/";
      } else if (defined( 'LANDINGPAGES_URLPATH' )){
        $final_path = LANDINGPAGES_URLPATH;
      } else if (defined( 'WP_CTA_URLPATH' )){
        $final_path = WP_CTA_URLPATH;
      } else {
        $final_path = preg_replace("/\/shared\/inbound-shortcodes\//", "/", INBOUND_FORMS);
      }
      ?>
    <script type="text/javascript">
    /* <![CDATA[ */
    // Load inline scripts var freshthemes_theme_dir = "<?php // echo INBOUND_FORMS; ?>", test = "<?php // _e('Insert Shortcode', INBOUND_LABEL); ?>";
    /* ]]> */
    </script>
      <style type="text/css">
        #wpadminbar.nojs .ab-top-menu > li.menupop.icon-edd:hover > .ab-item,
        #wpadminbar .ab-top-menu > li.menupop.icon-edd.hover > .ab-item,
        #wpadminbar.nojs .ab-top-menu > li.menupop.icon-edd > .ab-item,
        #wpadminbar .ab-top-menu > li.menupop.icon-edd > .ab-item {

          background-image: url(<?php echo $final_path . 'shared/inbound-shortcodes/shortcodes-blue.png';?>);

          background-repeat: no-repeat;
          background-position: 0.15em 50%;
          padding-left: 22px;
        }
        #wp-admin-bar-ddw-edd-languages-de > .ab-item:before,
        #wp-admin-bar-ddw-edd-translations-forum > .ab-item:before {
          color: #ff9900;
          content: '• ';
        }
        #wpadminbar .eddtb-search-input {
          width: 140px;
        }
        #wp-admin-bar-ddw-edd-inboundsupportsections .ab-item,
        #wp-admin-bar-ddw-edd-inbounddocsquick .ab-item,
        #wp-admin-bar-ddw-edd-inbounddocssections .ab-item,
        #wpadminbar .eddtb-search-input,
        #wpadminbar .eddtb-search-go {
          color: #21759b !important;
          text-shadow: none;
        }
        #wpadminbar .eddtb-search-input,
        #wpadminbar .eddtb-search-go {
          background-color: #fff;
          height: 18px;
          line-height: 18px;
          padding: 1px 4px;
        }
        #wpadminbar .eddtb-search-go {
          -webkit-border-radius: 11px;
             -moz-border-radius: 11px;
                  border-radius: 11px;
          font-size: 0.67em;
          margin: 0 0 0 2px;
        }
        @font-face {
          font-family: 'FontAwesome';
          src: url('<?php echo $final_path . "shared/fonts/fontawesome/fontawesome-webfont.eot";?>');
          src: url('<?php echo $final_path . "shared/fonts/fontawesome/fontawesome-webfont.eot";?>') format('embedded-opentype'),
          url('<?php echo $final_path . "shared/fonts/fontawesome/fontawesome-webfont.woff?v=3.0.2"?>') format('woff'),
          url('<?php echo $final_path . "shared/fonts/fontawesome/fontawesome-webfont.ttf?v=3.0.2"?>') format('truetype');
          font-weight: normal;
          font-style: normal;
        }
        #wp-admin-bar-inbound-cta a:first-child, #wp-admin-bar-inbound-inboundtemplates .ab-item.ab-empty-item, #wp-admin-bar-inbound-inboundsettings .ab-item.ab-empty-item, #wp-admin-bar-inbound-inboundreports a:first-child {
          padding-left: 30px;
        }
        #wp-admin-bar-inbound-inboundtemplates .ab-item.ab-empty-item:hover, #wp-admin-bar-inbound-inboundsettings .ab-item.ab-empty-item:hover {
          color: #2ea2cc;
        }
        #wp-admin-bar-inbound-leads a:first-child, #wp-admin-bar-inbound-inboundseo a:first-child {
          padding-left: 31px;
        }
        #wp-admin-bar-inbound-landingpages a:first-child, #wp-admin-bar-inbound-inboundforms a:first-child{
          padding-left: 31px;
        }
        #wp-admin-bar-inbound-cta .ab-submenu a, #wp-admin-bar-inbound-leads .ab-submenu a,  #wp-admin-bar-inbound-landingpages .ab-submenu a , #wp-admin-bar-inbound-inboundforms .ab-submenu a, #wp-admin-bar-inbound-inboundtemplates .ab-submenu a,  #wp-admin-bar-inbound-inboundreports .ab-submenu a, #wp-admin-bar-inbound-inboundseo .ab-submenu a{
          padding-left: 10px;
        }
         #wp-admin-bar-inbound-cta:before, #wp-admin-bar-inbound-leads:before, #wp-admin-bar-inbound-landingpages:before, #wp-admin-bar-inbound-inboundforms:before, #wp-admin-bar-inbound-inboundtemplates:before, #wp-admin-bar-inbound-inboundsettings:before, #wp-admin-bar-inbound-inboundreports:before, #wp-admin-bar-inbound-inboundseo:before  {
          font-family: "FontAwesome" !important;
          content: "\f05b" !important;
          font: 100 19px/1 "FontAwesome" !important;
          padding-top: 4px;
          width: 30px;
          display: inline-block;
          height: 30px;
          position: absolute;
          left: 6px;
        }
        #wp-admin-bar-inbound-leads:before {
          content: "\f0c0" !important;
          font: 100 17px/1 "FontAwesome" !important;
        }
        #wp-admin-bar-inbound-landingpages:before {
          content: "\f15c" !important;
          left: 7px;
          font-size: 21px !important;
          }
        #wp-admin-bar-inbound-inboundforms:before {
            font: 400 18px/1 dashicons!important;
            content: "\f163" !important;
          }
        #wp-admin-bar-inbound-inboundtemplates:before {
            content: "\f0c5" !important;
            font-size: 18px !important;
          }
        #wp-admin-bar-inbound-inboundsettings:before {
          content: "\f013" !important;
          left: 7px !important;
        }
        #wp-admin-bar-inbound-inboundreports:before {
          content: "\f012" !important;
          font-size: 17px !important;
        }
        #wp-admin-bar-inbound-inboundseo:before {
          content: "\f002" !important;
          font-size: 17px !important;
        }
        #wp-admin-bar-inbound-cta a {
          vertical-align: top;
        }
        #adminmenu .menu-icon-wp-call-to-action div.wp-menu-image:before {
          font-family: "FontAwesome" !important;
          content: "\f05b";
          font: 400 24px/1 "FontAwesome" !important;
          padding-top: 6px;

        }
      </style>
   <?php }



  }
}
/*  Initialize InboundNow Menu
 *  --------------------------------------------------------- */

InboundMenu::init();

?>