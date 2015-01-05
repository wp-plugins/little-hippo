<?php
/**
 * The Tag Images Pages & Posts plug-in.
 *
 * A plug-in to let you manage meta tags for images in bulk via the WordPress dashboard.
 * This also allows you to edit meta tags for posts and pages based on the Title.
 *
 * @package   tipp
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://http://www.littlehippo.co/
 * @copyright 2014 DSA Co. Ltd. & Eric Buckley
 *
 * @wordpress-plugin
 * Plugin Name:       Little Hippo (Makes your BIG job small)
 * Plugin URI:        http://http://www.littlehippo.co/
 * Description:       Manage meta tags for images, pages and posts in bulk via the WordPress dashboard
 * Version:           1.1.4
 * Author:            DoSA (Do Something Awesome)
 * Author URI:        http://www.dsa-global.com/
 * Text Domain:       tipp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-tipp.php' );

register_activation_hook( __FILE__, array( 'Tipp', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Tipp', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Tipp', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() ) {

	// include( plugin_dir_path( __FILE__ ) . 'admin/includes/utility.php' );
	include( plugin_dir_path( __FILE__ ) . 'admin/includes/dashboard.php' );
	$dash = HippoDash::get_instance();

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-tipp-admin.php' );
	add_action( 'plugins_loaded', array( 'Tipp_Admin', 'get_instance' ) );

	if( get_option('seo_plugin') === 'HIPPO' ){
		require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/metabox/hippo-metaboxes.php' );
	}

}