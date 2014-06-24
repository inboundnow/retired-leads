<?php
/* Load Shared Files */
if (!class_exists('Inbound_Load_Shared')) {
class Inbound_Load_Shared {
	static $load_shared;

	static function init() {
		add_action( 'plugins_loaded', array(__CLASS__, 'inbound_load_shared_files') , 1 );
	}

	static function inbound_load_shared_files($atts) {
		self::$load_shared = true;
		/* Inbound Core Shared Files. Lead files take presidence */
		/* Check if Shared Files Already Loaded */
		if (defined('INBOUDNOW_SHARED'))
			return;

		/* Define Shared Constant for Load Prevention*/
		define('INBOUDNOW_SHARED','loaded');

		include_once('shared/classes/class.post-type.wp-lead.php'); 	
		include_once('shared/classes/class.form.php');  // Mirrored forms		
		include_once('shared/classes/class.menu.php');  // Inbound Marketing Menu
		include_once('shared/classes/class.feedback.php');  // Inbound Feedback Form
		include_once('shared/classes/class.debug.php');  // Inbound Debug & Scripts Class
		include_once('shared/classes/class.compatibility.php');  // Inbound Compatibility Class
		include_once('shared/classes/class.templating-engine.php');  // {{token}} Replacement Engine
		require_once('shared/classes/class.shortcodes.email-template.php'); 
		require_once('shared/classes/class.lead-fields.php');  
		require_once('shared/classes/class.inbound-forms.akismet.php');  
		include_once('shared/tracking/store.lead.php'); // Lead Storage from landing pages
		
		include_once('shared/shortcodes/inbound-shortcodes.php');  // Shared Shortcodes
		include_once('shared/extend/inboundnow.extend.php');
		include_once('shared/extend/inboundnow.extension-licensing.php'); // Legacy - Inboundnow Package Licensing
		include_once('shared/extend/inboundnow.extension-updating.php'); // Legacy -Inboundnow Package Updating
		include_once('shared/extend/inboundnow.global-settings.php'); // Inboundnow Global Settings
		include_once('shared/metaboxes/template.metaboxes.php');  // Shared Shortcodes
		include_once('shared/functions/global.shared.functions.php'); // Global Shared Utility functions
		include_once('shared/assets/assets.loader.class.php');  // Load Shared CSS and JS Assets

	}
}
Inbound_Load_Shared::init();
}