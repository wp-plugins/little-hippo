<?php
/**
 * The view for Resetting the plug-in
 *
 * This resets the Little Hippo sIssues data.
 *
 * @package   Tipp_Admin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<?php 
$reset = (isset($_GET['reset'])) ? $_GET['reset']: '';
// $reset_url = esc_url(wp_nonce_url( admin_url('admin.php?page=hippo_reset&reset=remove'), 'hippo-reset' ));

if($reset === 'remove'):
	global $dash;

	$dash->hippo_remove_stats(); ?>

	<div class="alert alert-info">
		<p>The Little Hippo Environment has been reset.</p>
	</div>
	<p>&nbsp;</p>
	<div class="alert alert-warning">
		<p>The Little Hippo now needs to rescan your content for SEO issue.</p>
		<p>Click the button below to begin the rescan process.</p>
	</div>
	<a class="btn btn-primary btn-sm" href="<?php echo admin_url('admin.php?page=little-hippo'); ?>"><?php _e('Re-Scan', $this->plugin_slug); ?></a>

<?php
else: ?>

	<div class="alert alert-info">
		<p>Little Hippo will reset the environment. This will not delete any of your SEO data.</p>
		<p>However, this may take some time depending upon the amount of content you have in your WordPress CMS.</p>
		<p>If you wish to proceed, please click the button below.</p>
	</div>
	<a class="btn btn-primary btn-sm" href="<?php echo admin_url('admin.php?page=hippo_reset&reset=remove'); ?>"><?php _e('Reset', $this->plugin_slug); ?></a>
<?php endif; ?>
</div>
