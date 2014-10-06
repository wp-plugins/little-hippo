<?php
/**
 * Little Hippo Tools plug-in.
 *
 * @package   TippUtils
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package	  TippUtils
 * @author    Eric Buckley <eric@dosa.io>
 */
class TippUtils {

	public $issues = null;
	public $posttypes = null;

	protected static $instance = null;
	protected $plugin_screen_hook_suffix = null;

	public function __construct() {
		$plugin = Tipp::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		$this->setup_objects();
		$this->meta_issues();
		$this->image_issues();
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function setup_objects() {

		// Setup the post types both internal and custom
		$internal 	= array( 'page' => 'Pages', 'post' => 'Posts' );
		$custom		= $this->get_post_types();
		foreach ($custom as $name => $data) {
			$internal[$name] = $data->labels->name;
		}

		$this->posttypes = $internal;
	}

	public function get_post_types($return = 'objects'){
		$cpt_args = array(
			'public' => true,
			'_builtin' => false
		);

		$post_types = get_post_types( $cpt_args, $return ); 

		return $post_types;
	}

	public function meta_issues($posttype = 'all'){

		if ($posttype == 'all' || is_null($posttype)) {
			foreach ($this->posttypes as $name => $label) {
				$meta_title_issues = $this->meta_title($name);
				$meta_desc_issues = $this->meta_desc($name);
				$this->issues[$name] = $meta_title_issues + $meta_desc_issues;
			}
		} else {
			$meta_title_issues = $this->meta_title($posttype);
			$meta_desc_issues = $this->meta_desc($posttype);
			$this->issues[$posttype] = $meta_title_issues + $meta_desc_issues;
		}

		$totals = array(
			'title_size' => 0,
			'title_empty' => 0,
			'desc_size' => 0,
			'desc_empty' => 0,
		);

		foreach ($this->issues as $type => $data) {
			if ($type !== 'images'){
				foreach ($data as $item => $value){
					$totals[$item] += $value;
				}
			}
		}

		$this->issues['totals'] = $totals;
		$out = null;

		foreach ($totals as $key => $value){
			switch ($key) {
				case 'title_empty':
					$status = ($value > 0) ? 'issue_error': 'issue_ok';
					$message = $value . " " . __('items are missing meta titles', $this->plugin_slug);
					$details = __("Page titles are critical to giving searchers quick insight into the content of a result. It is a primary piece of information they use to decide which result to click on, so it's important to use high-quality, descriptive titles on your pages.", $this->plugin_slug);
					break;
				
				case 'title_size':
					$status = ($value > 0) ? 'issue_warning': 'issue_ok';
					$message = $value . " " . __('items have a meta title that is too short or too long', $this->plugin_slug);
					$details = __("Search results limit the number of characters they display for page titles. It’s considered best practice to keep page titles to a length between 26 and 55 characters.", $this->plugin_slug);
					break;
				
				case 'desc_empty':
					$status = ($value > 0) ? 'issue_error': 'issue_ok';
					$message = $value . " " . __('items are missing meta descriptions', $this->plugin_slug);
					$details = __("The description attribute (a.k.a. meta description) is a short, helpful summary of your page’s content. It is a primary piece of information searchers use to decide which result to click on. Having a description attribute doesn't guarantee that a search engine will use it in its search results, but in most cases it will.", $this->plugin_slug);
					break;
				
				case 'desc_size':
					$status = ($value > 0) ? 'issue_warning': 'issue_ok';
					$message = $value . " " . __('items have a meta description that is too short or too long', $this->plugin_slug);
					$details = __("Search results limit the number of characters they display for meta descriptions. It’s considered best practice to keep meta descriptions to a minimum of 100 characters and a maximum 155 characters.", $this->plugin_slug);
					break;
			}

			$before = "<div class=\"issue_data ".$status."\">\n";
			$content = "\t<h4>".$message."</h4>\n";
			$content .= "\t<div class=\"issue_details\">" . $details . "</div>\n";
			$after = "</div>\n";

			$out .= $before . $content . $after;
		}

		return $out;
	}

	public function image_issues(){

		$image_issues = $this->image_alt_title();

		$this->issues['images'] = $image_issues;
		$out = null;

		foreach ($image_issues as $key => $value){
			switch ($key) {
				case 'title_empty':
					$status = ($value > 0) ? 'issue_error': 'issue_ok';
					$message = $value . " " . __('images are missing titles', $this->plugin_slug);
					$details = __("The title attribute provides search engines with useful information about the subject matter of the image. They use this information to help determine the best image to return for a searcher's query.", $this->plugin_slug);
					break;
				
				case 'title_size':
					$status = ($value > 0) ? 'issue_warning': 'issue_ok';
					$message = $value . " " . __('images have a title that is too short or too long', $this->plugin_slug);
					$details = __("While there are no requirements for the number of characters for image titles we suggest that you keep image titles to a length between 30 and 65 characters.", $this->plugin_slug);
					break;
				
				case 'alt_empty':
					$status = ($value > 0) ? 'issue_error': 'issue_ok';
					$message = $value . " " . __('images are missing alt tags', $this->plugin_slug);
					$details = __("The ALT attribute provides search engines with useful information about the subject matter of the image. They use this information to help determine the best image to return for a searcher's query.", $this->plugin_slug);
					break;
				
				case 'alt_size':
					$status = ($value > 0) ? 'issue_warning': 'issue_ok';
					$message = $value . " " . __('images have an alt tag that is too short or too long', $this->plugin_slug);
					$details = __("While there are no requirements for the number of characters for image alt tags, we suggest that you keep image alt tags to a length between 152 and 255 characters.", $this->plugin_slug);
					break;
			}

			$before = "<div class=\"issue_data ".$status."\">\n";
			$content = "\t<h4>".$message."</h4>\n";
			$content .= "\t<div class=\"issue_details\">" . $details . "</div>\n";
			$after = "</div>\n";

			$out .= $before . $content . $after;
		}

		return $out;
	}

	public function meta_title($posttype){
		// Setup the query vars
		$mt_args = array(
			'post_type' => $posttype,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'suppress_filters' => false,
		);
		$mt_query = get_posts( $mt_args );

		$meta_title_size = 0;
		$meta_title_miss = 0;

		// Loop through posts and check
		if ($mt_query) {
			foreach($mt_query as $post_item){
				$meta_title = get_post_meta( $post_item->ID, get_option('meta_title_field'), true);
				if( strlen($meta_title) > get_option('char_limit_title') || strlen($meta_title) < get_option('char_min_title') ){
					$meta_title_size = $meta_title_size + 1; 
				}
				if( strlen($meta_title) === 0 || is_null($meta_title) ) {
					$meta_title_miss = $meta_title_miss + 1;
				}
			}
		} else {

		}
		$mt_issue = array(
			'title_size' => $meta_title_size,
			'title_empty' => $meta_title_miss,
		);

		return $mt_issue;
	}

	public function meta_desc($posttype){
		// Setup the query vars
		$md_args = array(
			'post_type' => $posttype,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'suppress_filters' => false,
		);
		$md_query = get_posts( $md_args );

		$meta_desc_size = 0;
		$meta_desc_miss = 0;

		// Loop through posts and check
		if ($md_query) {
			foreach($md_query as $post_item){
				$meta_desc = get_post_meta( $post_item->ID, get_option('meta_descr_field'), true);
				if( strlen($meta_desc) > get_option('char_limit_alt') || strlen($meta_desc) < get_option('char_min_alt') ){
					$meta_desc_size = $meta_desc_size + 1; 
				}
				if( strlen($meta_desc) === 0 || is_null($meta_desc) ) {
					$meta_desc_miss = $meta_desc_miss + 1;
				}
			}
		} else {

		}
		$md_issue = array(
			'desc_size' => $meta_desc_size,
			'desc_empty' => $meta_desc_miss,
		);

		return $md_issue;
	}

	public function image_alt_title(){

		$attach_args = array(
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'posts_per_page' => -1,
		);
		$attach_query = get_posts( $attach_args );

		$alt_size = 0;
		$alt_miss = 0;
		$title_size = 0;
		$title_miss = 0;

		if ($attach_query) {
			foreach($attach_query as $attach_item){
				$image_alt = get_post_meta( $attach_item->ID, '_wp_attachment_image_alt', true);
				$image_title = $attach_item->post_title;

				if( strlen($image_alt) > get_option('img_limit_alt') || strlen($image_alt) < get_option('img_min_alt')  ){
					$alt_size = $alt_size + 1; 
				}
				if( strlen($image_title) > get_option('img_limit_title') || strlen($image_title) < get_option('img_min_title') ){
					$title_size = $title_size + 1; 
				}
				if( strlen($image_alt) === 0 || is_null($image_alt) ) {
					$alt_miss = $alt_miss + 1;
				}
				if( strlen($image_title) === 0 || is_null($image_title) ) {
					$title_miss = $title_miss + 1;
				}
			}
		} else {

		}
		$attach_issue = array(
			'alt_empty' => $alt_miss,
			'alt_size' => $alt_size,
			'title_empty' => $title_miss,
			'title_size' => $title_size,
		);

		return $attach_issue;
	}

}