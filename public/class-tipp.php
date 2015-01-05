<?php
/**
 * Little Hippo front end class
 *
 * @package   Little Hippo
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

/**
 * @package   Tipp
 * @author    Eric Buckley <eric@dosa.io>
 */
class Tipp {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.0.0
	 * @var     string
	 */
	const VERSION = '1.1.4';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    0.0.0
	 * @var      string
	 */
	protected $plugin_slug = 'little-hippo';

	/**
	 * Instance of this class.
	 *
	 * @since    0.3.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.3.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Cleanup the WordPress Head area
		if (get_option('hippo_head_cleanup') == 'on'){
			add_action('init', array( $this, 'hippo_head_cleanup' ) );
		}
		// The head function calls action hippo_wp_head, where we can hook all more functionality
		if ( get_option('seo_plugin') === 'HIPPO' ):

			add_action( 'wp_head', array( $this, 'hippo_head' ), 1 );
			add_action( 'hippo_wp_head', array( $this, 'hippo_metadata' ), 2 );
			add_action( 'hippo_wp_head', array( $this, 'hippo_ga' ), 10 );
			add_action( 'hippo_wp_head', array( $this, 'hippo_facebook' ), 20 );

			add_filter( 'wp_title', array( $this, 'show_meta_title' ), 90, 1 );

		endif;

		if (get_option('hippo_outbound_nf') == 'on') {
			add_filter( 'the_content', array( $this, 'hippo_nf_parse' ) );
		}

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide  ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();
				}
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	public function hippo_head() {
		global $wp_query;

		$temp_wp_query = null;

		if ( ! $wp_query->is_main_query() ) {
			$temp_wp_query = $wp_query;
			wp_reset_query();
		}

		do_action( 'hippo_wp_head' );

		echo "\t<!-- end little hippo head functions -->\n\n";

		if ( ! empty( $temp_wp_query ) ) {
			$GLOBALS['wp_query'] = $temp_wp_query;
			unset( $temp_wp_query );
		}

		return;
	}

	public function hippo_head_cleanup() {
		// category feeds
		// remove_action( 'wp_head', 'feed_links_extra', 3 );
		// post and comment feeds
		// remove_action( 'wp_head', 'feed_links', 2 );
		// EditURI link
		remove_action( 'wp_head', 'rsd_link' );
		// windows live writer
		remove_action( 'wp_head', 'wlwmanifest_link' );
		// index link
		remove_action( 'wp_head', 'index_rel_link' );
		// previous link
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		// start link
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		// links for adjacent posts
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		// WP version
		remove_action( 'wp_head', 'wp_generator' );
	} /* end hippo head cleanup */


	/**
	 * Outputs the meta description element and text.
	 *
	 * @param bool $echo Whether or not to echo the description.
	 *
	 * @return string
	 */
	public function hippo_metadata( $echo = true ) {
		if ( get_query_var( 'paged' ) && get_query_var( 'paged' ) > 1 ) {
			return '';
		}

		global $post, $wp_query;

		$site_title = get_bloginfo('name');
		$site_descr = get_bloginfo('description','display');
		$seo_desc	= get_post_meta( $post->ID, get_option('meta_descr_field'), true );

		$metadesc  = '';
		$post_type = '';
		if ( is_object( $post ) && ( isset( $post->post_type ) && $post->post_type !== '' ) ) {
			$post_type = $post->post_type;
		}

		if ( is_singular() ) {
			$desc 			= get_post_meta( $post->ID, get_option('meta_descr_field'), true );
			$metakeywords 	= get_post_meta( $post->ID, get_option('meta_kword_field'), true );
		} else {
			if ( is_search() ) {

			} elseif ( is_home() ) {
				$desc_format = get_option('def_arc_desc');
				if ( strpos( $desc_format, '%site_title%' ) !== false ) $desc_format = str_replace( '%site_title%', $site_title, $desc_format );
				if ( strpos( $desc_format, '%site_desc%' ) !== false ) $desc_format = str_replace( '%site_desc%', $site_descr, $desc_format );
				if ( strpos( $desc_format, '%seo_desc%' ) !== false ) $desc_format = str_replace( '%seo_desc%', $seo_desc, $desc_format );

				$desc = $desc_format;
			} elseif ( is_archive() ) {

				if ( is_category() ) {
					$desc_format = get_option('def_cat_desc');
					$cat_name = single_cat_title("", false);
					if ( strpos($desc_format, '%site_title%' ) !== false ) $desc_format = str_replace( '%site_title%', $site_title, $desc_format );
					if ( strpos($desc_format, '%site_desc%' ) !== false ) $desc_format = str_replace( '%site_desc%', $site_descr, $desc_format );
					if ( strpos($desc_format, '%cat_name%' ) !== false ) $desc_format = str_replace( '%cat_name%', $cat_name, $desc_format );

					$desc = $desc_format;
				} elseif (is_tag()) {
					$desc_format = get_option('def_tag_desc');
					$tag_name = single_tag_title("", false);
					if (strpos($desc_format, '%site_title%') !== false ) $desc_format = str_replace( '%site_title%', $site_title, $desc_format );
					if ( strpos($desc_format, '%site_desc%') !== false ) $desc_format = str_replace( '%site_desc%', $site_descr, $desc_format );
					if ( strpos($desc_format, '%tag_name%') !== false ) $desc_format = str_replace( '%tag_name%', $tag_name, $desc_format );

					$desc = $desc_format;
				} elseif (is_tax()) {
					$desc_format = get_option('def_tax_desc');
					$tax_name = get_queried_object()->name;
					if (strpos($desc_format, '%site_title%') !== false ) $desc_format = str_replace( '%site_title%', $site_title, $desc_format );
					if ( strpos($desc_format, '%site_desc%') !== false ) $desc_format = str_replace( '%site_desc%', $site_descr, $desc_format );
					if ( strpos($desc_format, '%tag_name%') !== false ) $desc_format = str_replace( '%tax_name%', $tax_name, $desc_format );

					$desc = $desc_format;
				} else {
					$desc_format = get_option('def_arc_desc');
					if (strpos($desc_format, '%site_title%') !== false ) $desc_format = str_replace( '%site_title%', $site_title, $desc_format );
					if ( strpos($desc_format, '%site_desc%') !== false ) $desc_format = str_replace( '%site_desc%', $site_descr, $desc_format );
					if ( strpos($desc_format, '%seo_desc%') !== false ) $desc_format = str_replace( '%seo_desc%', $seo_desc, $desc_format );

					$desc = $desc_format;
				}

			} else {
				$desc = $site_descr;
			}
		}
		$metadesc = $desc;

		if ( is_string( $metadesc ) && $metadesc !== '' ) {
			echo "\n\t" . '<meta name="description" content="' . esc_attr( strip_tags( stripslashes( $metadesc ) ) ) . '" />' . "\n";
		} else {
			echo "\n\t<!-- no meta desc defined -->\n";
		}
		if ( is_string( $metakeywords ) && $metakeywords !== '' ) {
			echo "\t" . '<meta name="keywords" content="' . esc_attr( strip_tags( stripslashes( $metakeywords ) ) ) . '" />' . "\n";
		} else {
			echo "";
		}
	}

	public function show_meta_title( $title ) {
		global $post, $paged, $wp_query;

		$site_title = get_bloginfo('name');
		$site_descr = get_bloginfo('description','display');

		if ( is_feed() )
			return $title;

		$postid = (!$post->ID) ? get_queried_object()->ID : $post->ID;

		if ( is_singular() ) {
			$title = get_post_meta( $postid, get_option('meta_title_field'), true );

			if ( is_home() || is_front_page() ) {
				$title_format = get_option( 'def_home_title' );
				$seo_title = get_post_meta( $postid, get_option('meta_title_field'), true );

				if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
				if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );
				if ( strpos( $title_format, '%seo_title%' ) !== false ) $title_format = str_replace( '%seo_title%', $seo_title, $title_format );

				$title = $title_format;
			}
		} else {
			if ( is_search() ) {

			} elseif ( is_front_page() ) {
				$title_format = get_option( 'def_home_title' );
				$seo_title = get_post_meta( $postid, get_option('meta_title_field'), true );

				if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
				if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );
				if ( strpos( $title_format, '%seo_title%' ) !== false ) $title_format = str_replace( '%seo_title%', $seo_title, $title_format );

				$title = $title_format;
			} elseif ( is_home() ) {
				$title_format = get_option('def_arc_title');

				if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
				if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );

				$title = $title_format;
			} elseif ( is_archive() ) {

				if ( is_category() ) {
					$title_format = get_option('def_cat_title');
					$cat_name = single_cat_title("", false);
					if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
					if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );
					if ( strpos( $title_format, '%cat_name%' ) !== false ) $title_format = str_replace( '%cat_name%', $cat_name, $title_format );

					$title = $title_format;
				} elseif (is_tag()) {
					$title_format = get_option('def_tag_title');
					$tag_name = single_tag_title("", false);
					if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
					if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );
					if ( strpos( $title_format, '%tag_name%' ) !== false ) $title_format = str_replace( '%tag_name%', $tag_name, $title_format );

					$title = $title_format;
				} elseif (is_tax()) {
					$title_format = get_option('def_tax_title');
					$tax_name = get_queried_object()->name;
					if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
					if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );
					if ( strpos( $title_format, '%tax_name%' ) !== false ) $title_format = str_replace( '%tax_name%', $tax_name, $title_format );

					$title = $title_format;
				} else {
					$title_format = get_option('def_arc_title');
					if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
					if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );

					$title = $title_format;
				}

			} else {
				$title = $site_title . $sep . $site_descr;
			}
		}

		return $title;
	}

	public function hippo_ga(){
		$ga_id = get_option('hippo_ga_id');
		$ga_id_off = get_option('hippo_ga_off');
		$ga_te_to = (int)get_option('hippo_ga_te_to');

		if ( $ga_id && (trim($ga_id) !== '') && (substr($ga_id, 0, 3) === 'UA-') && ($ga_id_off !== 'on') ){
			echo "\n" . '<script>' . "\n";
			echo "// Google Analytics code added by Little Hippo\n";
			echo "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){\n";
			echo "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();\n";
			echo "a=s.createElement(o),m=s.getElementsByTagName(o)[0];\n";
			echo "a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\n";
			echo "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');\n";

			echo "ga('create', '" . $ga_id . "', {'siteSpeedSampleRate': 100}); \n";
			echo "ga('require', 'displayfeatures');\n";
			echo "ga('send', 'pageview');\n";

			echo "setTimeout('_gaq.push([\'_trackEvent\', \'NoBounce\', \'Over 10 seconds\'])'," . $ga_te_to . ");\n";

			echo "</script>\n";
		} else {
			echo "\t<!-- No GA Code provided in Little Hippo settings or value is invalid -->\n";
		}
	}

	public function hippo_facebook(){
		global $post, $wp_query;

		$front_test = get_option('show_on_front');
		$seo_desc = NULL;

		if($front_test === 'posts'){
			$title_format = get_option( 'def_home_title' );
			$seo_title = get_post_meta( $postid, get_option('meta_title_field'), true );
			$seo_desc = get_option( 'def_home_desc' );

			if ( strpos( $title_format, '%site_title%' ) !== false ) $title_format = str_replace( '%site_title%', $site_title, $title_format );
			if ( strpos( $title_format, '%site_desc%' ) !== false ) $title_format = str_replace( '%site_desc%', $site_descr, $title_format );
			if ( strpos( $title_format, '%seo_title%' ) !== false ) $title_format = str_replace( '%seo_title%', $seo_title, $title_format );
			if ( strpos( $seo_desc, '%seo_desc%' ) !== false ) $seo_desc = NULL;

			$page_title = $title_format;
		} else {
			$page_title = get_post_meta( $post->ID, get_option('meta_title_field'), true );
			if ( strpos( $seo_desc, '%seo_desc%' ) !== false ) $seo_desc = str_replace( '%seo_desc%', get_post_meta( $post->ID, get_option('meta_descr_field'), true ), $seo_desc );
		}
		$site_title = get_bloginfo('name');
		$site_url   = get_bloginfo('url');
		$site_desc  = (!is_null($seo_desc)) ? $seo_desc: get_post_meta( $post->ID, get_option('meta_descr_field'), true );
		$home_image = get_option('fb_og_default_img');
		$def_locale = get_locale();
		$alt_locale = get_option('fb_og_alt_locale'); //TODO

		if ( is_front_page() ) {
			if (!isset($page_title) || $page_title === '') {
				$page_title = get_the_title($post->ID);
			}
			if (!isset($site_desc) || $site_desc === '') {
				$site_desc = get_bloginfo('description');
			}
			echo "<meta property=\"og:title\" content=\"". $page_title . "\" />\n";
			echo "<meta property=\"og:site_name\" content=\"" . $site_title . "\" />\n";
			echo "<meta property=\"og:url\" content=\"" . $site_url . "\" />\n";
			echo "<meta property=\"og:description\" content=\"" . $site_desc . "\" />\n";
			echo "<meta property=\"og:image\" content=\"" . $home_image . "\"  >\n";
			echo "<meta property=\"og:type\" content=\"website\" />\n";
			echo "<meta property=\"og:locale\" content=\"" . $def_locale . "\" />\n";
			// echo "<meta property=\"og:locale:alternate\" content=\"" . $alt_locale . "\" />\n";
		} else {
			$site_url = get_the_permalink( $post->ID );
			if (!isset($page_title) || $page_title === '') {
				$page_title = get_the_title($post->ID);
			}
			if (!isset($site_desc) || $site_desc === '') {
				$site_desc = get_bloginfo('description');
			}
			if ( has_post_thumbnail($post->ID) ) {
				$feat_image_id = get_post_thumbnail_id($post->ID);
				$thumbnail = wp_get_attachment_image_src( $feat_image_id, 'full', false );
				$feat_image = $thumbnail[0];
			} else {
				$feat_image = $home_image;
			}
			echo "<meta property=\"og:title\" content=\"". $page_title . "\" />\n";
			echo "<meta property=\"og:site_name\" content=\"" . $site_title . "\" />\n";
			echo "<meta property=\"og:url\" content=\"" . $site_url . "\" />\n";
			echo "<meta property=\"og:description\" content=\"" . $site_desc . "\" />\n";
			echo "<meta property=\"og:image\" content=\"" . $feat_image . "\"  >\n";
		}
	}

	public function restructure_images($attr) {

		$attr['title'] = '';
		$attr['alt'] = '';

		return $html;
	}

	public function hippo_nf_parse( $content ) {

		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
		if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
			if( !empty($matches) ) {

				$srcUrl = get_option('home');
				for ($i=0; $i < count($matches); $i++) {

					$tag = $matches[$i][0];
					$tag2 = $matches[$i][0];
					$url = $matches[$i][0];

					$noFollow = '';

					$pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
					preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
					if( count($match) < 1 )
						$noFollow .= ' rel="nofollow" ';

					$pos = strpos($url,$srcUrl);
					if ($pos === false) {
						$tag = rtrim ($tag,'>');
						$tag .= $noFollow.'>';
						$content = str_replace($tag2,$tag,$content);
					}
				}
			}
		}

		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
}
