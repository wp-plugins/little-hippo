<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Little Hippo
 * @package  Metaboxes for Little Hippo
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'hippo_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function hippo_metaboxes( array $meta_boxes ) {
	global $dash;

	$pt = $dash->avail_posttypes( true );

	$seo_pages = array('page','post');
	$cpt_args = array(
		'public' => true,
		'_builtin' => false
	);

	$post_types = get_post_types( $cpt_args, 'names' );
	foreach ($post_types as $posttype){
		array_push( $seo_pages, $posttype );
	}

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_hippo_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$meta_boxes['hippo_seo'] = array(
		'id'         => 'hippo_seo',
		'title'      => __( 'Little Hippo SEO', 'cmb' ),
		'pages'      => $pt, // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => __( 'Meta Title', 'cmb' ),
				'desc' => __( 'Enter the value for the Meta Title tag (55 characters max. recommended)', 'cmb' ),
				'id'   => $prefix . 'seo_title',
				'type' => 'text',
			),
			array(
				'name' => __( 'Meta Description', 'cmb' ),
				'desc' => __( 'Maximum of 155 characters recommended for the meta description.', 'cmb' ),
				'id'   => $prefix . 'seo_metadesc',
				'type' => 'textarea_small',
			),
		),
	);

	return $meta_boxes;
}

add_action( 'init', 'hippo_initialize_cmb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function hippo_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}