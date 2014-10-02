<?php
/**
 * Little Hippo Tools plug-in.
 *
 * @package   Tipp_Admin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package	  Tipp_Admin
 * @author    Eric Buckley <eric@dosa.io>
 */
class Tipp_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	// public $tipp_posts;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = Tipp::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page, options defaults and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 */

		add_action( 'contextual_help', array( $this, 'scg_screen_help' ), 10, 3 );
		add_action( 'wp_ajax_tipp_to_file', array( $this, 'tipp_title_to_file') );
		add_action( 'wp_ajax_tipp_to_title', array( $this, 'tipp_title_to_title') );
		add_action( 'wp_ajax_tipp_to_alt', array( $this, 'tipp_title_to_alt') );
		add_action( 'wp_ajax_tipp_update_meta', array( $this, 'tipp_update_meta') );
		add_action( 'wp_ajax_tipp_update_tags', array( $this, 'tipp_update_tags') );
		add_action( 'wp_ajax_tipp_save_all', array( $this, 'tipp_update_all') );
		add_action( 'wp_ajax_tipp_empty_trash', array( $this, 'hippo_empty_trash' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {
			wp_enqueue_style( $this->plugin_slug.'-bootstrap', plugins_url( 'assets/css/bootstrap.min.css', __FILE__ ), array(), Tipp::VERSION );
			wp_enqueue_style( $this->plugin_slug.'-tooltipcss', plugins_url('assets/css/tooltipster.css', __FILE__ ), array(), Tipp::VERSION );
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Tipp::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {
			wp_enqueue_script( $this->plugin_slug.'-bootstrapjs', plugins_url('assets/js/bootstrap.min.js', __FILE__ ), array('jquery'), Tipp::VERSION );
			wp_enqueue_script( $this->plugin_slug.'-textcounter', plugins_url('assets/js/jquery.charactercounter.js', __FILE__ ), array('jquery'), Tipp::VERSION );
			wp_enqueue_script( $this->plugin_slug.'-tooltip', plugins_url('assets/js/jquery.tooltipster.js', __FILE__ ), array('jquery'), Tipp::VERSION );
			wp_enqueue_script( $this->plugin_slug.'-admin-script', plugins_url('assets/js/admin.js', __FILE__ ), array('jquery','wp-ajax-response'), Tipp::VERSION );
			wp_localize_script( $this->plugin_slug.'-admin-script', 'TippSettings', $this->localize_vars() );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix[] = add_menu_page(
			__( 'Little Hippo', $this->plugin_slug ),
			__( 'Little Hippo', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' ),
			plugins_url('assets/images/littleHIPPO-Logo-red-20x18.png', __FILE__ ),
			76
		);
		$this->plugin_screen_hook_suffix[] = add_submenu_page(
			$this->plugin_slug,
			__( 'Tag Pages', $this->plugin_slug ),
			__( 'Pages', $this->plugin_slug ),
			'manage_options',
			'tipp-tag-pages',
			array( $this, 'display_plugin_pages_page' )
		);
		$this->plugin_screen_hook_suffix[] = add_submenu_page(
			$this->plugin_slug,
			__( 'Tag Posts', $this->plugin_slug ),
			__( 'Posts', $this->plugin_slug ),
			'manage_options',
			'tipp-tag-posts',
			array( $this, 'display_plugin_posts_page' )
		);
		$this->plugin_screen_hook_suffix[] = add_submenu_page(
			$this->plugin_slug,
			__( 'Tag Custom Posts', $this->plugin_slug ),
			__( 'Custom Posts', $this->plugin_slug ),
			'manage_options',
			'tipp-tag-custom',
			array( $this, 'display_plugin_custom_page' )
		);
		$this->plugin_screen_hook_suffix[] = add_submenu_page(
			$this->plugin_slug,
			__( 'Tag Images', $this->plugin_slug ),
			__( 'Images', $this->plugin_slug ),
			'manage_options',
			'tipp-tag-images',
			array( $this, 'display_plugin_images_page' )
		);
		$this->plugin_screen_hook_suffix[] = add_submenu_page(
			$this->plugin_slug,
			__( 'Little Hippo Settings', $this->plugin_slug ),
			__( 'Settings', $this->plugin_slug ),
			'manage_options',
			'tipp-settings',
			array( $this, 'display_plugin_settings_page' )
		);

		add_action('admin_init', array( $this, 'register_hippo_settings' ) );

	}

	function register_hippo_settings(){
		register_setting( 'hippo_settings', 'seo_plugin' );
		register_setting( 'hippo_settings', 'seo_plugin_name' );

		register_setting( 'hippo_settings', 'char_limit_title' );
		register_setting( 'hippo_settings', 'char_limit_alt' );
		register_setting( 'hippo_settings', 'char_limit_file' );
		register_setting( 'hippo_settings', 'char_min_title' );
		register_setting( 'hippo_settings', 'char_min_alt' );

		register_setting( 'hippo_settings', 'img_limit_title' );
		register_setting( 'hippo_settings', 'img_limit_alt' );
		register_setting( 'hippo_settings', 'img_min_title' );
		register_setting( 'hippo_settings', 'img_min_alt' );

		register_setting( 'hippo_settings', 'hippo_outbound_nf' );
		register_setting( 'hippo_settings', 'hippo_empty_trash' );
		register_setting( 'hippo_settings', 'hippo_ga_id' );
		register_setting( 'hippo_settings', 'hippo_ga_te_to' );
		register_setting( 'hippo_settings', 'hippo_ga_off' );

		register_setting( 'hippo_settings', 'def_home_title' );
		register_setting( 'hippo_settings', 'def_home_desc' );
		register_setting( 'hippo_settings', 'def_arc_title' );
		register_setting( 'hippo_settings', 'def_arc_desc' );
		register_setting( 'hippo_settings', 'def_arc_robots' );
		register_setting( 'hippo_settings', 'def_cat_title' );
		register_setting( 'hippo_settings', 'def_cat_desc' );
		register_setting( 'hippo_settings', 'def_cat_robots' );
		register_setting( 'hippo_settings', 'def_tag_title' );
		register_setting( 'hippo_settings', 'def_tag_desc' );
		register_setting( 'hippo_settings', 'def_tag_robots' );
		register_setting( 'hippo_settings', 'def_tax_title' );
		register_setting( 'hippo_settings', 'def_tax_desc' );
		register_setting( 'hippo_settings', 'def_tax_robots' );

		register_setting( 'hippo_settings', 'fb_og_default_img' );
		register_setting( 'hippo_settings', 'fb_og_default_loc' );
		register_setting( 'hippo_settings', 'fb_og_alt_locale' );

		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			$tipp_options['seo_plugin'] = 'AIOSP';
			$tipp_options['seo_plugin_name'] = 'All In One SEO Pack';
			$tipp_options['meta_title_field'] = '_aioseop_title';
			$tipp_options['meta_descr_field'] = '_aioseop_description';
			$tipp_options['meta_kword_field'] = '_aioseop_keywords';
		} elseif ( class_exists( 'WPSEO_Admin' ) ) {
			$tipp_options['seo_plugin'] = 'YOAST';
			$tipp_options['seo_plugin_name'] = 'WordPress SEO by Yoast';
			$tipp_options['meta_title_field'] = '_yoast_wpseo_title';
			$tipp_options['meta_descr_field'] = '_yoast_wpseo_metadesc';
			$tipp_options['meta_kword_field'] = '_yoast_wpseo_metakeywords';
		} else {
			$tipp_options['seo_plugin'] = 'HIPPO';
			$tipp_options['seo_plugin_name'] = 'Little Hippo';
			$tipp_options['meta_title_field'] = '_hippo_seo_title';
			$tipp_options['meta_descr_field'] = '_hippo_seo_metadesc';
			$tipp_options['meta_kword_field'] = '_hippo_seo_metakeywords';
		}
		update_option( 'seo_plugin', $tipp_options['seo_plugin'] );
		update_option( 'seo_plugin_name', $tipp_options['seo_plugin_name'] );
		update_option( 'meta_title_field', $tipp_options['meta_title_field'] );
		update_option( 'meta_descr_field', $tipp_options['meta_descr_field'] );
		update_option( 'meta_kword_field', $tipp_options['meta_kword_field'] );

		update_option( 'char_limit_title', 55 );
		update_option( 'char_limit_alt', 155 );
		update_option( 'char_limit_file', 255 );
		update_option( 'char_min_title', 26 );
		update_option( 'char_min_alt', 100 );

		update_option( 'img_limit_title', 65 );
		update_option( 'img_limit_alt', 255 );
		update_option( 'img_min_title', 30 );
		update_option( 'img_min_alt', 152 );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Render the Tag Images page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_images_page() {
		include_once( 'views/tag-images.php' );
	}

	/**
	 * Render the Tag Pages page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_pages_page() {
		include_once( 'views/tag-pages.php' );
	}

	/**
	 * Render the Tag Posts page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_posts_page() {
		include_once( 'views/tag-posts.php' );
	}

	/**
	 * Render the Custom Posts page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_custom_page() {
		include_once( 'views/tag-custom.php' );
	}

	/**
	 * Render the Settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_settings_page() {
		include_once( 'views/tipp-settings.php' );
	}

	public function tipp_get_cpt ($return = 'objects'){
		$cpt_args = array(
			'public' => true,
			'_builtin' => false
		);

		$post_types = get_post_types( $cpt_args, $return ); 

		return $post_types;
	}

	public function tipp_get_content_types ($return = 'names'){
		$pagetypes = array('page','post');

		$cpt_args = array(
			'public' => true,
			'_builtin' => false
		);
		$post_types = get_post_types( $cpt_args, $return ); 

		foreach ($post_types as $posttype){
			array_push( $pagetypes, $posttype );
		}

		return $pagetypes;
	}

	public function tipp_get_postbreakdown( $tipp_posttype = 'post' ){
		
		$post_stats = array();
		$count_posts = wp_count_posts( $tipp_posttype );

		if($tipp_posttype == 'image'){

		} else {
			$total = $count_posts->publish + $count_posts->future + $count_posts->draft + $count_posts->pending + $count_posts->private;
			if ($total > 0) {
				$post_stats['publish'] = $count_posts->publish/$total * 100;
				$post_stats['future'] = $count_posts->future/$total * 100;
				$post_stats['draft'] = $count_posts->draft/$total * 100;
				$post_stats['pending'] = $count_posts->pending/$total * 100;
				$post_stats['private'] = $count_posts->private/$total * 100;
				$post_stats['total'] = $total;
			} else {
				$post_stats['total'] = $total;
			}
		}

		return $post_stats;
	}

	/**
	 * Get all of the Meta Titles and return as array
	 *
	 * @since    1.0.0
	 */
	public function tipp_get_page_titles(){

		$pagetypes = $this->tipp_get_content_types();

		$status_array = array();

		foreach ($pagetypes as $pagetype){
			$tipp_args = array(
				'post_type' => $pagetype,
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'suppress_filters' => false,
			);
			$new_query = get_posts( $tipp_args );
			$stats = array();

			if ($new_query):

				// $stats['query'] = $new_query;
				$status['total'] = count($new_query);

				foreach ( $new_query as $post_item ){

					$metatitle = get_post_meta( $post_item->ID, get_option('meta_title_field'), true);
					$metadescr = get_post_meta( $post_item->ID, get_option('meta_descr_field'), true);
					$metakword = get_post_meta( $post_item->ID, get_option('meta_kword_field'), true);

					if ( is_string( $metatitle ) && $metatitle !== '' ) {
						$stats['mthas'] += 1;
					} else {
						$stats['mthasnot'] += 1;
					}
					if ( is_string( $metadescr ) && $metadescr !== '' ) {
						$stats['mdhas'] += 1;
					} else {
						$stats['mdhasnot'] += 1;
					}
					if ( is_string( $metakword ) && $metakword !== '' ) {
						$stats['mkhas'] += 1;
					} else {
						$stats['mkhasnot'] += 1;
					}
				}
				$status['title_good'] = ($stats['mthas'] > 0) ? 100 * ($stats['mthas']/$status['total']): 0;
				$status['title_warn'] = ($stats['mthasnot'] > 0) ? 100 * ($stats['mthasnot']/$status['total']): 0;
				$status['descr_good'] = ($stats['mdhas'] > 0) ? 100 * ($stats['mdhas']/$status['total']): 0;
				$status['descr_warn'] = ($stats['mdhasnot'] > 0) ? 100 * ($stats['mdhasnot']/$status['total']): 0;
				$status['kword_good'] = ($stats['mkhas'] > 0) ? 100 * ($stats['mkhas']/$status['total']): 0;
				$status['kword_warn'] = ($stats['mkhasnot'] > 0) ? 100 * ($stats['mkhasnot']/$status['total']): 0;

				$status_array[$pagetype] = $status;
			else:
			endif;
		}

		return $status_array;
	}

	public function tipp_get_imagestatus(){

		$args = array( 
			'post_type' => 'attachment', 
			'posts_per_page' => -1,
			'post_status' => 'inherit',
		);
		$tipp_media = get_posts( $args );

		$image_status['total'] = count($tipp_media);

		foreach ($tipp_media as $imagepost) {
			$image_title = $imagepost->post_title;
			$image_alt = get_post_meta( $imagepost->ID, '_wp_attachment_image_alt', true );
			if( !empty($image_title) ) {
				$image_title_has += 1;
			} else {
				$image_title_hasnot += 1;
			}
			if( !empty($image_alt) ) {
				$image_alt_has += 1;
			} else {
				$image_alt_hasnot += 1;
			}
		}

		$image_status['title_good'] = ($image_title_has > 0) ? 100 * ($image_title_has/$image_status['total']): 0;
		$image_status['title_warn'] = ($image_title_hasnot > 0) ? 100 * ($image_title_hasnot/$image_status['total']): 0;
		$image_status['alt_good'] = ($image_alt_has > 0) ? 100 * ($image_alt_has/$image_status['total']): 0;
		$image_status['alt_warn'] = ($image_alt_hasnot > 0) ? 100 * ($image_alt_hasnot/$image_status['total']): 0;

		return $image_status;
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'tipp-settings' => '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_slug . '-settings' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	public function action_get_media( $post_parent = null ) {
		$args = array( 
			'post_type' => 'attachment', 
			'posts_per_page' => -1,
			'post_status' => 'inherit',
			'post_parent' => $post_parent
		);
		$tipp_media = get_posts( $args );

		return $tipp_media;
	}

	public function tipp_title_to_file() {
		global $wpdb;

		$post_id = $_POST['postid'];

		$post_title = sanitize_file_name( get_the_title( $post_id ) );

		$media_attachments = $this->action_get_media($post_id);
		$new_filenames = array();

		foreach ($media_attachments as $attachment) {
			$new_filenames[$attachment->ID] = $post_title . '-' . $attachment->ID;
		}

		$res_args = array(
			'what' => 'title_to_file',
			'data' => json_encode($new_filenames)
		);
		$xmlResponse = new WP_Ajax_Response($res_args);
		$xmlResponse->send();

		exit;
	}

	public function tipp_title_to_title() {
		global $wpdb;

		$post_id = $_POST['postid'];

		$post_title = get_the_title( $post_id );

		$media_attachments = $this->action_get_media($post_id);
		$new_titles = array();

		foreach ($media_attachments as $attachment) {
			$new_titles[$attachment->ID] = $post_title . '-' . $attachment->ID;
		}

		$res_args = array(
			'what' => 'title_to_title',
			'data' => json_encode($new_titles)
		);
		$xmlResponse = new WP_Ajax_Response($res_args);
		$xmlResponse->send();

		exit;
	}

	public function tipp_title_to_alt() {
		global $wpdb;

		$post_id = $_POST['postid'];

		$post_title = get_the_title( $post_id );

		$media_attachments = $this->action_get_media($post_id);
		$new_titles = array();

		foreach ($media_attachments as $attachment) {
			$new_titles[$attachment->ID] = $post_title . '-' . $attachment->ID;
		}

		$res_args = array(
			'what' => 'title_to_alt',
			'data' => json_encode($new_titles)
		);
		$xmlResponse = new WP_Ajax_Response($res_args);
		$xmlResponse->send();

		exit;
	}

	public function tipp_update_meta() {

		if( !isset( $_POST['_tipp_nonce'] ) || !wp_verify_nonce($_POST['_tipp_nonce'], 'tipp-save_meta') ) die('Permissions check failed');

		if ($_POST['objid']) {
			$seodata = $this->get_seo_data($_POST['objid']);
			update_post_meta($_POST['objid'], $seodata['meta_title_field'],$_POST['meta_title_value']);
			update_post_meta($_POST['objid'], $seodata['meta_descr_field'],$_POST['meta_descr_value']);
			update_post_meta($_POST['objid'], $seodata['meta_kword_field'],$_POST['meta_kword_value']);
		}

		echo $_POST['objid'];

		die();
	}

	public function tipp_update_all() {

		if( !isset( $_POST['_tipp_nonce'] ) || !wp_verify_nonce($_POST['_tipp_nonce'], 'tipp-save_all') ) die('Permissions check failed');

		if ($_POST['objid']) {
			$seodata = $this->get_seo_data($_POST['objid']);
			update_post_meta($_POST['objid'], $seodata['meta_title_field'],$_POST['meta_title_value']);
			update_post_meta($_POST['objid'], $seodata['meta_descr_field'],$_POST['meta_descr_value']);
			update_post_meta($_POST['objid'], $seodata['meta_kword_field'],$_POST['meta_kword_value']);
		}

		$media_list = array();
		$media_items = $this->action_get_media($_POST['objid']);
		foreach ($media_items as $item) {
			$media_list[] = 'tipp_item_' . $item->ID;
		}

		echo json_encode($media_list);

		die();
	}

	public function tipp_update_tags() {

		if( !isset( $_POST['_tipp_nonce'] ) || !wp_verify_nonce($_POST['_tipp_nonce'], 'tipp-upd_tags') ) die('Permissions check failed');

		$args = array( 
			'post_type' => 'attachment', 
			'posts_per_page' => -1,
			'post_status' => 'inherit',
			'p' => $_POST['media']
		);
		$tipp_media = get_posts( $args );

		// print_r($tipp_media);

		$media_args = array(
			'ID'	=> $_POST['media'],
			'post_title' => $_POST['newtitle']
		);
		$update_result = wp_update_post( $media_args );

		if ($update_result) {
			$alt_result = update_post_meta( $_POST['media'], '_wp_attachment_image_alt', $_POST['newalt'] );
			$alt_result = ($alt_result) ? $update_result : 'Error saving Alt Tag';
		}

		echo $update_result;

		die();
	}

	public function scg_screen_help( $contextual_help, $screen_id, $screen ) {
 
		if ( ! method_exists( $screen, 'add_help_tab' ) )
			return $contextual_help;
 
		global $hook_suffix;
 
		// List screen properties
		$variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
			. sprintf( '<li> Screen id : %s</li>', $screen_id )
			. sprintf( '<li> Screen base : %s</li>', $screen->base )
			. sprintf( '<li> Parent base : %s</li>', $screen->parent_base )
			. sprintf( '<li> Parent file : %s</li>', $screen->parent_file )
			. sprintf( '<li> Hook suffix : %s</li>', $hook_suffix )
			. '</ul>';
 
		// Append global $hook_suffix to the hook stems
		$hooks = array(
			"load-$hook_suffix",
			"admin_print_styles-$hook_suffix",
			"admin_print_scripts-$hook_suffix",
			"admin_head-$hook_suffix",
			"admin_footer-$hook_suffix"
		);
 
		// If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
		if ( did_action( 'add_meta_boxes_' . $screen_id ) )
			$hooks[] = 'add_meta_boxes_' . $screen_id;
 
		if ( did_action( 'add_meta_boxes' ) )
			$hooks[] = 'add_meta_boxes';
 
		// Get List HTML for the hooks
		$hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode( '</li><li>', $hooks ) . '</li></ul>';
 
		// Combine $variables list with $hooks list.
		$help_content = $variables . $hooks;
 
		// Add help panel
		$screen->add_help_tab( array(
			'id'      => 'scg-screen-help',
			'title'   => 'Screen Information',
			'content' => $help_content,
		));
 
		return $contextual_help;
	}

	public function tipp_pagination($pages, $page_name, $cp = 1, $post_type = 'post', $spread = 3){
		// Setup and Display Pagination
		$pagin = array();
		$ptlink = ($post_type != 'post' || $post_type != 'page') ? '&tipptype='.$post_type: '';
		if ($pages > 10):
			for( $i = 1; $i <= $pages; $i++ ) {
				if ( ($i > $cp - $spread) && ($i < $cp + $spread) ) { 
					$url = admin_url('admin.php?page=' . $page_name . '&pagenum=' . $i . $ptlink);
					$active = ($cp == $i) ? ' class="active"' : '';
					$link = '<li' . $active . '><a href="' . $url . '">' . $i . '</a></li>';
					$pagin[] = $link;
				}
			}
		else:
			for( $i = 1; $i <= $pages; $i++ ) {
				$url = admin_url('admin.php?page=' . $page_name . '&pagenum=' . $i . $ptlink);
				$active = ($cp == $i) ? ' class="active"' : '';
				$link = '<li' . $active . '><a href="' . $url . '">' . $i . '</a></li>';
				$pagin[] = $link;
			}
		endif;
		if ($pages != 1):
			$pagination = '<ul class="pagination">';
			$pagination .= '<li><a href="' . admin_url('admin.php?page=' . $page_name) . '">&laquo;</a></li>';
			if ($pages > 10 && $cp > 4): 
				$pagination .= '<li><a href="" class="disabled">&hellip;</a></li>';
			endif;
			$pagination .= implode( '', $pagin );
			if ($pages > 10 && $cp < ($pages - $spread)): 
				$pagination .= '<li><a href="" class="disabled">&hellip;</a></li>';
			endif;
			$pagination .= '<li><a href="' . admin_url('admin.php?page=' . $page_name . '&pagenum=' . $pages) . '">&raquo;</a></li>';
			$pagination .= '</ul>';
			$pagination .= '<div class="clearfix"></div>';
		else:
			$pagination = '';
		endif;

		return $pagination;
	}

	public function localize_vars() { 
		return array( 
			'SiteUrl' => get_bloginfo('url'), 
			'AjaxUrl' => admin_url('admin-ajax.php'), 
			'OtherText' => __('my text', "my_localization_name") 
		);
	}

	public function get_seo_data( $postid=null ) {
		if ( !is_null($postid) ) {
			$seodata['name'] = get_option('seo_plugin');
			$seodata['meta_title_field'] = get_option('meta_title_field');
			$seodata['meta_descr_field'] = get_option('meta_descr_field');
			$seodata['meta_kword_field'] = get_option('meta_kword_field');

			$postobj 	= get_page( $postid );
			$title 		= get_post_meta( $postid, get_option('meta_title_field'), true );
			$desc 		= get_post_meta( $postid, get_option('meta_descr_field'), true );
			$keywords	= get_post_meta( $postid, get_option('meta_kword_field'), true );

			$desc_content = ltrim(trim(substr(wp_strip_all_tags($postobj->post_content, true), 0, 160)));

			$seodata['title'] = (is_null($title) || $title == '') ? get_the_title($postid) : $title;
			$seodata['desc'] = (is_null($desc) || $desc == '') ? $desc_content : $desc;
			$seodata['keywords'] = trim($keywords);

			return $seodata;
		} else {
			return;
		}
	}

	public function format_value( $value ){

		if ( intval($value) > 1000 ) {
			$value = number_format($value / 1000, 2);
			$str_value = $value . "K";
		} else {
			$str_value = $value;
		}

		return $str_value;
	}

	public function hippo_empty_trash(){

		$post_types = $this->tipp_get_content_types();

		$trash_args = array(
			'post_type'		=> $post_types,
			'post_status' 	=> 'trash',
			'numberposts' 	=> -1,
		);
		$trash_posts = get_posts( $trash_args );
		$trash_posts_count = count($trash_posts);

		if (isset($trash_posts)) {
			if(is_array($trash_posts)){
				foreach ($trash_posts as $key => $post) {
					wp_delete_post( $post->ID, true );
					$rem_items[$key] = $post->ID;
				}
			} else {
				wp_delete_post( $post->ID, true );
				$rem_items[] = $post->ID;
			}
		}

		$res_args = array(
			'what' => 'empty_trash',
			'data' => json_encode($rem_items)
		);
		$xmlResponse = new WP_Ajax_Response($res_args);
		$xmlResponse->send();

		exit;
	}

	function lh_get_attachment( $attachment_id ) {

		$attachment = get_post( $attachment_id );
		return array(
			'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'caption' => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'href' => get_permalink( $attachment->ID ),
			'src' => $attachment->guid,
			'title' => $attachment->post_title
		);
	}
}
