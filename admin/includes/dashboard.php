<?php
/**
 * Little Hippo Tools plug-in Dashboard Class.
 *
 * @package   TippAdmin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package	  HippoDash
 * @author    Eric Buckley <eric@dosa.io>
 */
class HippoDash {

	protected static $instance = null;
	protected $plugin_screen_hook_suffix = null;

	public function __construct() {
		$plugin = Tipp::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function avail_posttypes( $q = false ) {

		if ($q){
			$internal 	= array( 'page', 'post' );
			$custom		= $this->get_custom_post_types();
			foreach ($custom as $name => $data) {
				$internal[] = $name;
			}
		} else {
			$internal 	= array( 'page' => 'Pages', 'post' => 'Posts' );
			$custom		= $this->get_custom_post_types();
			foreach ($custom as $name => $data) {
				$internal[$name] = $data->labels->name;
			}
		}

		return $internal;
	}

	public function get_custom_post_types($return = 'objects'){
		$cpt_args = array(
			'public' => true,
			'_builtin' => false
		);

		$post_types = get_post_types( $cpt_args, $return ); 

		return $post_types;
	}

	public function check_validation_init( $post_id = NULL ){
		if ($post_id === NULL ){
			$test_args = array( 'numberposts' => 5, 'order' => 'ASC' );
			$test_posts = get_posts( $test_args );

			$fields_result = array();
			$results = array();

			foreach ($test_posts as $post) {
				$fields_result = get_post_custom_keys($post->ID);
				if( in_array('_hippo_metatitle_missing', $fields_result, true) ){
					$results[] = true;
				} else {
					$results[] = false;
				}
			}

			if (in_array(true, $results, true)) {
				return true;
			} else {
				return false;
			}
		} else {
			$test_args = array( 'p' => $post_id, );
			$test_post = get_posts( $test_args );

			$fields_result = get_post_custom_keys($test_post->ID);
			if ( is_array($fields_result) ) {
				if( in_array('_hippo_metatitle_missing', $fields_result, true) ){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	public function get_issues_count(){

		$meta_issues = $this->meta_issues();
		$img_issues = $this->image_issues();
		$totals = $meta_issues['total'] + $img_issues['total'];

		$issues = $this->format_value($totals);

		return $issues;
	}

	private function format_value( $value ){

		if ( intval($value) > 1000 ) {
			$value = number_format($value / 1000, 2);
			$str_value = $value . "K";
		} else {
			$str_value = $value;
		}

		return $str_value;
	}

	public function hippo_firstpass(){

		$avail_posttypes = $this->avail_posttypes( true );
		$hippo_args = array(
			'post_type'		=> $avail_posttypes,
			'post_status'	=> 'any',
			'numberposts'	=> -1,
			'cache_results' => false,
			'no_found_rows' => true,
			'fields'		=> 'ids',
		);
		$hippo_posts = get_posts($hippo_args);

		foreach ($hippo_posts as $post) {
			$this->hippo_add_metadata( $post );
		}

		$hippo_args = array(
			'post_type'		=> 'attachment',
			'numberposts'	=> -1,
			'cache_results' => false,
			'no_found_rows' => true,
			'fields'		=> 'ids',
		);
		$hippo_posts = get_posts($hippo_args);

		foreach ($hippo_posts as $post) {
			$this->hippo_add_image_metadata( $post );
		}
	}

	public function hippo_add_metadata( $pid ){
		if (!$this->check_validation_init($pid)){
			if( !update_post_meta( $pid, '_hippo_metatitle_missing', $this->meta_title( $pid ), true ) ){
				add_post_meta( $pid, '_hippo_metatitle_missing', $this->meta_title( $pid ), true );	
			}
			if( !update_post_meta( $pid, '_hippo_metadesc_missing', $this->meta_desc( $pid ), true ) ){
				add_post_meta( $pid, '_hippo_metadesc_missing', $this->meta_desc( $pid ), true );
			}
			if( !update_post_meta( $pid, '_hippo_metatitle_length', $this->meta_title( $pid, true ), true ) ){
				add_post_meta( $pid, '_hippo_metatitle_length', $this->meta_title( $pid, true ), true );
			}
			if( !update_post_meta( $pid, '_hippo_metadesc_length', $this->meta_desc( $pid, true ), true ) ){
				add_post_meta( $pid, '_hippo_metadesc_length', $this->meta_desc( $pid, true ), true );
			}
		}
	}

	public function hippo_add_image_metadata( $pid ){
		if (!$this->check_validation_init($pid)){
			if( !update_post_meta( $pid, '_hippo_imgtitle_missing', $this->img_title( $pid ), true ) ){
				add_post_meta( $pid, '_hippo_imgtitle_missing', $this->img_title( $pid ), true );	
			}
			if( !update_post_meta( $pid, '_hippo_imgalt_missing', $this->img_alt( $pid ), true ) ){
				add_post_meta( $pid, '_hippo_imgalt_missing', $this->img_alt( $pid ), true );
			}
			if( !update_post_meta( $pid, '_hippo_imgtitle_length', $this->img_title( $pid, true ), true ) ){
				add_post_meta( $pid, '_hippo_imgtitle_length', $this->img_title( $pid, true ), true );
			}
			if( !update_post_meta( $pid, '_hippo_imgalt_length', $this->img_alt( $pid, true ), true ) ){
				add_post_meta( $pid, '_hippo_imgalt_length', $this->img_alt( $pid, true ), true );
			}
		}
	}

	public function meta_issues( $type = NULL, $ppp = -1, $of = NULL ){
		$title_missing_args = array(
			'post_type'		=> $this->avail_posttypes( true ),
			'posts_per_page'=> $ppp,
			'post_status'	=> 'publish',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_metatitle_missing',
					'value' => 1,
				),
			),
		);
		$title_length_args = array(
			'post_type'		=> $this->avail_posttypes( true ),
			'posts_per_page'=> $ppp,
			'post_status'	=> 'publish',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_metatitle_length',
					'value' => 1,
				),
			),
		);
		$desc_missing_args = array(
			'post_type'		=> $this->avail_posttypes( true ),
			'posts_per_page'=> $ppp,
			'post_status'	=> 'publish',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_metadesc_missing',
					'value' => 1,
				),
			),
		);
		$desc_length_args = array(
			'post_type'		=> $this->avail_posttypes( true ),
			'posts_per_page'=> $ppp,
			'post_status'	=> 'publish',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_metadesc_length',
					'value' => 1,
				),
			),
		);
		if(!is_null($of)){
			$title_missing_args['offset'] = $of;
			$title_length_args['offset'] = $of;
			$desc_missing_args['offset'] = $of;
			$desc_length_args['offset'] = $of;
		}

		$title_missing 	= new WP_Query($title_missing_args);
		$title_length 	= new WP_Query($title_length_args);
		$desc_missing 	= new WP_Query($desc_missing_args);
		$desc_length 	= new WP_Query($desc_length_args);

		$meta_issues['title_missing'] = $title_missing->post_count;
		$meta_issues['title_length'] = $title_length->post_count;
		$meta_issues['desc_missing'] = $desc_missing->post_count;
		$meta_issues['desc_length'] = $desc_length->post_count;

		$totals = array_sum($meta_issues);
		$meta_issues['total'] = $totals;

		switch ($type) {
			case 'title_missing':
				return $title_missing;
				break;
			case 'title_length':
				return $title_length;
				break;
			case 'desc_missing':
				return $desc_missing;
				break;
			case 'desc_length':
				return $desc_length;
				break;
			default:
				return $meta_issues;
				break;
		}

		return $meta_issues;
	}

	public function image_issues( $type = NULL, $ppp = -1, $of = NULL  ){
		$title_missing_args = array(
			'post_type'		=> 'attachment',
			'posts_per_page'=> $ppp,
			'post_status'	=> 'inherit',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_imgtitle_missing',
					'value' => 1,
				),
			),
		);
		$title_length_args = array(
			'post_type'		=> 'attachment',
			'posts_per_page'=> $ppp,
			'post_status'	=> 'inherit',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_imgtitle_length',
					'value' => 1,
				),
			),
		);
		$alt_missing_args = array(
			'post_type'		=> 'attachment',
			'posts_per_page'=> $ppp,
			'post_status'	=> 'inherit',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_imgalt_missing',
					'value' => 1,
				),
			),
		);
		$alt_length_args = array(
			'post_type'		=> 'attachment',
			'posts_per_page'=> $ppp,
			'post_status'	=> 'inherit',
			'meta_query'	=> array(
				array(
					'key'	=> '_hippo_imgalt_length',
					'value' => 1,
				),
			),
		);
		if(!is_null($of)){
			$title_missing_args['offset'] = $of;
			$title_length_args['offset'] = $of;
			$alt_missing_args['offset'] = $of;
			$alt_length_args['offset'] = $of;
		}

		$title_missing 	= new WP_Query($title_missing_args);
		$title_length 	= new WP_Query($title_length_args);
		$alt_missing 	= new WP_Query($alt_missing_args);
		$alt_length 	= new WP_Query($alt_length_args);

		$img_issues['img_title_missing'] = $title_missing->post_count;
		$img_issues['img_title_length'] = $title_length->post_count;
		$img_issues['img_alt_missing'] = $alt_missing->post_count;
		$img_issues['img_alt_length'] = $alt_length->post_count;

		$totals = array_sum($img_issues);
		$img_issues['total'] = $totals;

		switch ($type) {
			case 'img_title_missing':
				return $title_missing;
				break;
			case 'img_title_length':
				return $title_length;
				break;
			case 'img_alt_missing':
				return $alt_missing;
				break;
			case 'img_alt_length':
				return $alt_length;
				break;
			default:
				return $img_issues;
				break;
		}

		return $img_issues;
	}

	public function meta_title( $pid, $length = false ){
		if($pid){
			$meta_title = sanitize_text_field(get_post_meta($pid, get_option('meta_title_field'), true));
			if($length){
				if(strlen($meta_title) > get_option('char_limit_title') || strlen($meta_title) < get_option('char_min_title')) { 
					return 1;
				} else {
					return 0;
				}
			} else {
				if(strlen($meta_title) === 0){
					return 1;
				} else {
					return 0;
				}
			}
		}

		return;
	}

	public function meta_desc( $pid, $length = false ){
		if($pid){
			$meta_desc = sanitize_text_field(get_post_meta($pid, get_option('meta_descr_field'), true));
			if($length){
				if(strlen($meta_desc) > get_option('char_limit_alt') + 1 || strlen($meta_desc) < get_option('char_min_alt')) { 
					return 1;
				} else {
					return 0;
				}
			} else {
				if(strlen($meta_desc) === 0){
					return 1;
				} else {
					return 0;
				}
			}
		}

		return;
	}

	public function img_title( $pid, $length = false ){
		if($pid){
			$img_title = sanitize_text_field(trim(get_post($pid)->post_title));
			if($length){
				if(strlen($img_title) > get_option('img_limit_title') || strlen($img_title) < get_option('img_min_title')) { 
					return 1;
				} else {
					return 0;
				}
			} else {
				if(strlen($img_title) === 0){
					return 1;
				} else {
					return 0;
				}
			}
		}

		return;
	}

	public function img_alt( $pid, $length = false ){
		if($pid){
			$image_alt = get_post_meta( $pid, '_wp_attachment_image_alt', true );
			if($length){
				if(strlen($image_alt) > get_option('img_limit_alt') || strlen($image_alt) < get_option('img_min_alt')) { 
					return 1;
				} else {
					return 0;
				}
			} else {
				if(strlen($image_alt) === 0){
					return 1;
				} else {
					return 0;
				}
			}
		}

		return;
	}

	function hippo_remove_stats(){
		// if( !isset( $_POST['_tipp_nonce'] ) || !wp_verify_nonce($_POST['_tipp_nonce'], 'hippo-reset') ) die('Permissions check failed');

		$avail_posttypes = $this->avail_posttypes( true );
		$hippo_args = array(
			'post_type'		=> $avail_posttypes,
			'post_status'	=> 'any',
			'numberposts'	=> -1,
			'cache_results' => false,
			'no_found_rows' => true,
			'fields'		=> 'ids',
		);
		$hippo_posts = get_posts($hippo_args);

		foreach ($hippo_posts as $post) {
			delete_post_meta($post, '_hippo_metatitle_missing');
			delete_post_meta($post, '_hippo_metadesc_missing');
			delete_post_meta($post, '_hippo_metatitle_length');
			delete_post_meta($post, '_hippo_metadesc_length');
		}

		$hippo_args = array(
			'post_type'		=> 'attachment',
			'numberposts'	=> -1,
			'cache_results' => false,
			'no_found_rows' => true,
			'fields'		=> 'ids',
		);
		$hippo_posts = get_posts($hippo_args);

		foreach ($hippo_posts as $post) {
			delete_post_meta($post, '_hippo_imgtitle_missing');
			delete_post_meta($post, '_hippo_imgalt_missing');
			delete_post_meta($post, '_hippo_imgtitle_length');
			delete_post_meta($post, '_hippo_imgalt_length');
		}
	}
}