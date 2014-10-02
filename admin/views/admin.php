<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Tipp_Admin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

$tipp_pages = $this->tipp_get_postbreakdown('page');
$tipp_posts = $this->tipp_get_postbreakdown('post');
$tipp_cpt 	= $this->tipp_get_cpt();
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div class="updated">
		<p>You are using the <b><?php echo get_option('seo_plugin_name'); ?></b> SEO Plug-in.</p>
		<p>These fields will be used for Pages, Posts and Custom Post Type Meta data.</p>
	</div>

	<h3>Content Summary</h3>
	<div class="row">
		<div class="col-xs-2 text-right">
			<p>Pages (<?php echo $tipp_pages['total']; ?>)</p>
		</div>
		<div class="col-xs-9">
			<div class="progress">
				<div class="progress-bar progress-bar-success" style="width: <?php echo $tipp_pages['publish']; ?>%">Published</div>
				<div class="progress-bar progress-bar-warning progress-bar-striped" style="width: <?php echo $tipp_pages['future']; ?>%">Future</div>
				<div class="progress-bar progress-bar-warning" style="width: <?php echo $tipp_pages['draft']; ?>%">Draft</div>
				<div class="progress-bar progress-bar-info progress-bar-striped" style="width: <?php echo $tipp_pages['pending']; ?>%">Pending</div>
				<div class="progress-bar progress-bar-info" style="width: <?php echo $tipp_pages['private']; ?>%">Private</div>
			</div>
		</div>
		<div class="col-xs-2 text-right">
			<p>Posts (<?php echo $tipp_posts['total']; ?>)</p>
		</div>
		<div class="col-xs-9">
			<div class="progress">
				<div class="progress-bar progress-bar-success" style="width: <?php echo $tipp_posts['publish']; ?>%">Published</div>
				<div class="progress-bar progress-bar-warning progress-bar-striped" style="width: <?php echo $tipp_posts['future']; ?>%">Future</div>
				<div class="progress-bar progress-bar-warning" style="width: <?php echo $tipp_posts['draft']; ?>%">Draft</div>
				<div class="progress-bar progress-bar-info progress-bar-striped" style="width: <?php echo $tipp_posts['pending']; ?>%">Pending</div>
				<div class="progress-bar progress-bar-info" style="width: <?php echo $tipp_posts['private']; ?>%">Private</div>
			</div>
		</div>
	</div>
<?php if ($tipp_cpt): ?>

	<div class="row">
<?php 
	foreach ($tipp_cpt as $customtype) {
	 	$cpt_status = $this->tipp_get_postbreakdown( $customtype->name );
	 	if ($cpt_status['total'] == 0): ?>
		<div class="col-xs-2 text-right">
			<p><?php echo $customtype->labels->name; ?> (<?php echo $cpt_status['total']; ?>)</p>
		</div>
		<div class="col-xs-9">
			<div class="progress">
				<div class="progress-bar progress-bar-danger" style="width: 100%">Nothing Found</div>
			</div>
		</div>

	 	<?php else: ?>
		<div class="col-xs-2 text-right">
			<p><?php echo $customtype->labels->name; ?> (<?php echo $cpt_status['total']; ?>)</p>
		</div>
		<div class="col-xs-9">
			<div class="progress">
				<div class="progress-bar progress-bar-success" style="width: <?php echo $cpt_status['publish']; ?>%"><?php _e('Published', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-warning progress-bar-striped" style="width: <?php echo $cpt_status['future']; ?>%"><?php _e('Future', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-warning" style="width: <?php echo $cpt_status['draft']; ?>%"><?php _e('Draft', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-info progress-bar-striped" style="width: <?php echo $cpt_status['pending']; ?>%"><?php _e('Pending', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-info" style="width: <?php echo $cpt_status['private']; ?>%"><?php _e('Private', $this->plugin_slug); ?></div>
			</div>
		</div>

<?php
		endif;
	}
endif; // end of have custom post types
?>

	</div>
	<hr>
	<h3><?php _e('Issues Summary', $this->plugin_slug); ?></h3>
<?php 
	$lh_utils = new TippUtils;

	// $lh_utils->meta_issues(); 
	// $lh_utils->image_issues();

	$totals = $lh_utils->issues['totals'];
	$total_meta = array_sum($totals);
	$total_images = array_sum($lh_utils->issues['images']);

	ob_start();
	?>

	<div class="row">
		<div class="col-xs-2">&nbsp</div>
		<div class="col-xs-9">
			<h3><?php echo $this->format_value($total_meta + $total_images) . " " . __('Total Issues Found', $this->plugin_slug); ?></h3>
			<ul class="nav nav-tabs" role="tablist" id="issues">
				<li class="active"><a href="#meta" role="tab" data-toggle="tab"><h4><b><?php echo $this->format_value($total_meta); ?></b> Meta Issues</h4></a></li>
				<li><a href="#images" role="tab" data-toggle="tab"><h4><b><?php echo $this->format_value($total_images); ?></b> Image Issues</h4></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="meta">
					<?php echo $lh_utils->meta_issues(); ?>
				</div>
				<div class="tab-pane fade" id="images">
					<?php echo $lh_utils->image_issues(); ?>
				</div>
			</div>
		</div>
		<div class="col-xs-1">&nbsp;</div>
	</div>
	<?php ob_end_flush(); ?>

	<hr>
</div>
