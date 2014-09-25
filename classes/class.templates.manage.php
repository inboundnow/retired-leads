<?php

/**
 * 3rd Party Template Management  
 *
 * @package	Calls To Action
 * @subpackage	Templates
*/

if ( !class_exists('CTA_Template_Manager') ) {

	class CTA_Template_Manager {
	
		/**
		*	Initializes class
		*/
		public function __construct() {
			self::add_hooks();
		}
		
		/**
		*	Loads hooks and filters
		*/
		public static function add_hooks() {
			add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'enqueue_scripts' ) );
			
			/* prepare handler hook for uploads page */
			add_action('admin_menu', array( __CLASS__ , 'add_pages') );
		}
		
		
		/**
		*	Load CSS & JS
		*/
		public static function enqueue_scripts() {
			$screen = get_current_screen();
			//var_dump($screen);
			
			/* Load assets for upload page */
			if ( ( isset($screen) && $screen->base == 'wp-call-to-action_page_wp_cta_templates_upload' ) ){
				wp_enqueue_script('wp-cta-js-templates-upload', WP_CTA_URLPATH . 'js/admin/admin.templates-upload.js');
			}
			
			/* Load assets for Templates listing page */
			if ( ( isset($screen) && $screen->base == 'wp-call-to-action_page_wp_cta_manage_templates' ) ){
				wp_enqueue_style('wp-cta-css-templates', WP_CTA_URLPATH . 'css/admin-templates.css');
				wp_enqueue_script('wp-cta-js-templates', WP_CTA_URLPATH . 'js/admin/admin.templates.js');
			}
			
			/* Load assets for store search */
			if ( ( isset($screen) && $screen->base == 'wp-call-to-action_page_wp_cta_store' ) ){
					wp_enqueue_script('easyXDM', WP_CTA_URLPATH . 'js/libraries/easyXDM.debug.js');
			}
		}
		
		/**
		*  Adds additional management pages
		*/
		public static function add_pages() {
			if ( !current_user_can('manage_options') ) {
				return;
			}
	
			/** Template upload page */
			global $_registered_pages;			
			$hookname = get_plugin_page_hookname('wp_cta_templates_upload', 'edit.php?post_type=wp-call-to-action');
			if (!empty($hookname)) {
				add_action( $hookname , array( __CLASS__ , 'display_upload_page') );
			}			
			$_registered_pages[$hookname] = true;
			
			/** Template search page */
			global $_registered_pages;			
			$hookname = get_plugin_page_hookname('wp_cta_store', 'edit.php?post_type=wp-call-to-action');
			if (!empty($hookname)) {
				add_action( $hookname , array( __CLASS__ , 'display_store_search') );
			}			
			$_registered_pages[$hookname] = true;
			
		}
		
		/**
		*  Displays template upload UI
		*/
		public static function display_upload_page() {
			$screen = get_current_screen();
			
			if ( ( isset($screen) && $screen->base != 'wp-call-to-action_page_wp_cta_templates_upload' ) ){
				return;
			}
			
			self::run_upload_commands();
			self::display_upload_form();
			self::search_templates();
		}
		
		/**
		*  Displays upload form
		*/
		public static function display_upload_form() {
		?>
			<div class="wrap templates_upload">
				<div class="icon32" id="icon-plugins"><br></div><h2><?php _e( 'Install Templates' , 'cta' ); ?></h2>
				
				<ul class="subsubsub">
					<li class="plugin-install-dashboard"><a href="#search" id='menu_search'><?php _e( 'Search' , 'cta' ) ; ?></a> |</li>
					<li class="plugin-install-upload"><a class="current" href="#upload" id='menu_upload'><?php _e( 'Upload' , 'cta' ) ; ?></a> </li>
				</ul>
			
				<br class="clear">
					<h4><?php _e( 'Install Calls to Action template by uploading them here in .zip format' , 'cta' ) ; ?></h4>
					
					 <p class="install-help"><?php _e( 'Warning: Do not upload landing page extensions here or you will break the plugin! <br>Extensions are uploaded in the WordPress plugins section.' , 'cta' ) ; ?>
					</p>
					<form action="" class="wp-upload-form" enctype="multipart/form-data" method="post">
						<input type="hidden" value="<?php echo wp_create_nonce('wp-cta-nonce'); ?>" name="wp_cta_wpnonce" id="_wpnonce">
						<input type="hidden" value="/wp-admin/plugin-install.php?tab=upload" name="_wp_http_referer">
						<label for="pluginzip" class="screen-reader-text"><?php _e('Template zip file' , 'cta' ) ; ?></label>
						<input type="file" name="templatezip" id="templatezip">
						<input type="submit" value="<?php _e('Install Now' , 'cta' ) ; ?>" class="button" id="install-template-submit" name="install-template-submit" disabled="">	
					</form>
			</div>
		<?php
		}
		
		/**
		*  Listens for request to upload a template
		*/
		public static function run_upload_commands() {
			if (!$_FILES) {
				return;
			}
			$name = $_FILES['templatezip']['name'];
			$name = preg_replace('/\((.*?)\)/','',$name);
			$name = str_replace(array(' ','.zip'),'',$name);
			$name = trim($name);

			if (!wp_verify_nonce($_POST["wp_cta_wpnonce"], 'wp-cta-nonce')) {
				return NULL;
			}
			
			include_once( ABSPATH . 'wp-admin/includes/class-pclzip.php');
			
			$zip = new PclZip( $_FILES['templatezip']["tmp_name"]);
			

			if ( !is_dir( WP_CTA_UPLOADS_PATH ) )
			{
				wp_mkdir_p( WP_CTA_UPLOADS_PATH );
			}
			
			if (($list = $zip->listContent()) == 0) 
			{
				die(__('There was a problem. Please try again!' , 'cta' ));
			}
			 
			$is_template = false;
			foreach ($list as $key=>$val)
			{
				foreach ($val as $k=>$val)
				{
					if (strstr($val,'/config.php'))
					{
						$is_template = true;
						break;
					}
					else if($is_template==true)
					{
						break;
					}
				}
			}
			
			if (!$is_template) {
				echo "<br><br><br><br>";
				die(__( 'WARNING! This zip file does not seem to be a call to action template file! If you are trying to install an inbound now extension please use the Plugin\'s upload section! Please press the back button and try again!' , 'cta' ));
			}
			
			if ($result = $zip->extract(PCLZIP_OPT_PATH, WP_CTA_UPLOADS_PATH ,  PCLZIP_OPT_REPLACE_NEWER  ) == 0) {
				die(__( 'There was a problem. Please try again!' , 'cta' ));
			} else 	{
				unlink( $_FILES['templatezip']["tmp_name"]);
				echo '<div class="updated"><p>'. __( 'Template uploaded successfully!' , 'cta' ) .'</div>';
			}
		}
		
		/**
		*  Prompt to search the inbound now marketplace for call to action templates
		*/
		public static function search_templates() {
			?>
			
			<div class="wrap templates_search" style='display:none'>
				<div class="icon32" id="icon-plugins"><br></div><h2><?php _e('Search Templates' , 'cta' ) ; ?></h2>

				<ul class="subsubsub">
						<li class="plugin-install-dashboard"><a href="#search" id='menu_search'><?php _e('Search' , 'cta' ) ; ?></a> |</li>
						<li class="plugin-install-upload"><a class="current" href="#upload" id='menu_upload'><?php _e('Upload' , 'cta' ) ; ?></a> </li>
				</ul>
				
				<br class="clear">
					<p class="install-help"><?php _e('Search the Inboundnow marketplace for free and premium templates.' , 'cta' ) ; ?></p>
					<form action="edit.php?post_type=wp-call-to-action&page=wp_cta_store" method="POST" id="">
						<input type="search" autofocus="autofocus" value="" name="search">
						<label for="plugin-search-input" class="screen-reader-text"><?php _e('Search Templates' , 'cta' ) ; ?></label>
						<input type="submit" value="Search Templates" class="button" id="plugin-search-input" name="plugin-search-input">	
					</form>
			</div>
			
			<?php
		}
		
		/**
		*  Displays store search
		*/
		public static function display_store_search() {
			if (isset($_POST['search'])) {
				//echo 1; exit; 
				$search = urlencode($_POST['search']);
				$url = WP_CTA_STORE_URL."?type=templates&s=".$search;

				?>
				<div style='text-align:right;margin:10px;'>
					<form action="edit.php?post_type=wp-call-to-action&page=wp_cta_store" method="POST" id="">
						<input type="search" autofocus="autofocus"  name="search" value="<?php echo $_POST['search']; ?>">
						<label for="plugin-search-input" class="screen-reader-text">Search Templates</label>
						<input type="submit" value="Search Templates" class="button" id="plugin-search-input" name="plugin-search-input">	
					</form>
				</div>
				<?php
			}
			else {
				$url = WP_CTA_STORE_URL;
			}
			?>
			<script type='text/javascript'>
				jQuery(document).ready(function($) {

					new easyXDM.Socket({
						remote: "<?php echo $url; ?>",  
						container: document.getElementById("wp-cta-store-iframe-container"),
						onMessage: function(message, origin){
							var height = Number(message) + 1000;
							this.container.getElementsByTagName("iframe")[0].scrolling="no";
							this.container.getElementsByTagName("iframe")[0].style.height = height + "px";
						}
					});
				
				});
			</script>
			<div id="wp-cta-store-iframe-container">
			</div>

			<?php
		}

		
	}
	
	
	
	/**
	*	Loads CTA_Template_Manager on admin_init
	*/
	function load_CTA_Template_Manager() {
		$CTA_Template_Manager = new CTA_Template_Manager;
	}
	add_action( 'init' , 'load_CTA_Template_Manager' );

}

