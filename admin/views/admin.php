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

global $dash;

$tipp_pages = $this->tipp_get_postbreakdown('page');
$tipp_posts = $this->tipp_get_postbreakdown('post');
$tipp_cpt 	= $this->tipp_get_cpt();
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<?php if(get_option('seo_plugin') === 'AIOSP' || get_option('seo_plugin') === 'YOAST'): ?>
	<div class="updated">
		<p><?php _e('You are using the', $this->plugin_slug); ?> <b><?php echo get_option('seo_plugin_name'); ?></b> <?php _e('SEO Plug-in', $this->plugin_slug); ?>.</p>
		<p><?php _e('These fields will be used for Pages, Posts and Custom Post Type Meta data', $this->plugin_slug); ?>.</p>
	</div>
<?php endif; ?>
<?php ob_start(); ?>

	<h3>Content Summary</h3>
	<div class="row">
		<div class="col-xs-2 text-right">
			<p>Pages (<?php echo $tipp_pages['total']; ?>)</p>
		</div>
		<div class="col-xs-9">
			<div class="progress">
				<div class="progress-bar progress-bar-success" style="width: <?php echo $tipp_pages['publish']; ?>%"><?php _e('Published', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-warning progress-bar-striped" style="width: <?php echo $tipp_pages['future']; ?>%"><?php _e('Future', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-warning" style="width: <?php echo $tipp_pages['draft']; ?>%">Draft</div>
				<div class="progress-bar progress-bar-info progress-bar-striped" style="width: <?php echo $tipp_pages['pending']; ?>%"><?php _e('Pending', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-info" style="width: <?php echo $tipp_pages['private']; ?>%"><?php _e('Private', $this->plugin_slug); ?></div>
			</div>
		</div>
		<div class="col-xs-1">&nbsp;</div>
	</div>
	<div class="row">
		<div class="col-xs-2 text-right">
			<p><?php _e('Posts', $this->plugin_slug); ?> (<?php echo $tipp_posts['total']; ?>)</p>
		</div>
		<div class="col-xs-9">
			<div class="progress">
				<div class="progress-bar progress-bar-success" style="width: <?php echo $tipp_posts['publish']; ?>%"><?php _e('Published', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-warning progress-bar-striped" style="width: <?php echo $tipp_posts['future']; ?>%"><?php _e('Future', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-warning" style="width: <?php echo $tipp_posts['draft']; ?>%"><?php _e('Draft', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-info progress-bar-striped" style="width: <?php echo $tipp_posts['pending']; ?>%"><?php _e('Pending', $this->plugin_slug); ?></div>
				<div class="progress-bar progress-bar-info" style="width: <?php echo $tipp_posts['private']; ?>%"><?php _e('Private', $this->plugin_slug); ?></div>
			</div>
		</div>
		<div class="col-xs-1">&nbsp;</div>
	</div>

<?php if ($tipp_cpt): ?>

<?php 
	foreach ($tipp_cpt as $customtype) {
	 	$cpt_status = $this->tipp_get_postbreakdown( $customtype->name );
	 	if ($cpt_status['total'] == 0): ?>
	<div class="row">
		<div class="col-xs-2 text-right">
			<p><?php echo $customtype->labels->name; ?> (<?php echo $cpt_status['total']; ?>)</p>
		</div>
		<div class="col-xs-9">
			<div class="progress">
				<div class="progress-bar progress-bar-danger" style="width: 100%"><?php _e('Nothing Found', $this->plugin_slug); ?></div>
			</div>
		</div>
	</div>

	 	<?php else: ?>
	<div class="row">
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
	</div>

<?php
		endif;
	} // end foreach
endif; // end of have custom post types
?>

	<hr>
	<h3><?php _e('Issues Summary', $this->plugin_slug); ?></h3>
<?php 
	$meta_issues = $dash->meta_issues();
	$img_issues = $dash->image_issues();
?>
 
	<div class="row">
		<div class="col-xs-2">&nbsp</div>
		<div class="col-xs-9">
			<h3><?php echo $this->format_value($meta_issues['total'] + $img_issues['total']) . " " . __('Total Issues Found', $this->plugin_slug); ?></h3>
			<ul class="nav nav-tabs" role="tablist" id="issues">
				<li class="active"><a href="#meta" role="tab" data-toggle="tab"><h4><b><?php echo $this->format_value($meta_issues['total']); ?></b> Meta Issues</h4></a></li>
				<li><a href="#images" role="tab" data-toggle="tab"><h4><b><?php echo $this->format_value($img_issues['total']); ?></b> <?php _e('Image Issues', $this->plugin_slug); ?></h4></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="meta">
					<div class="issue_data <?php echo ($meta_issues['title_length'] > 0) ? 'issue_warning': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=title_length'); ?>"><?php echo $meta_issues['title_length'] . " " . __('items have a meta title that is too short or too long', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("Search results limit the number of characters they display for page titles. It’s considered best practice to keep page titles to a length between 26 and 55 characters.", $this->plugin_slug); ?></div>
					</div>
					<div class="issue_data <?php echo ($meta_issues['title_missing'] > 0) ? 'issue_error': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=title_missing'); ?>"><?php echo $meta_issues['title_missing'] . " " . __('items are missing meta titles', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("Page titles are critical to giving searchers quick insight into the content of a result. It is a primary piece of information they use to decide which result to click on, so it's important to use high-quality, descriptive titles on your pages.", $this->plugin_slug); ?></div>
					</div>
					<div class="issue_data <?php echo ($meta_issues['desc_length'] > 0) ? 'issue_warning': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=desc_length'); ?>"><?php echo $meta_issues['desc_length'] . " " . __('items have a meta description that is too short or too long', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("Search results limit the number of characters they display for meta descriptions. It’s considered best practice to keep meta descriptions to a minimum of 100 characters and a maximum 155 characters.", $this->plugin_slug); ?></div>
					</div>
					<div class="issue_data <?php echo ($meta_issues['desc_missing'] > 0) ? 'issue_error': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=desc_missing'); ?>"><?php echo $meta_issues['desc_missing'] . " " . __('items are missing meta descriptions', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("The description attribute (a.k.a. meta description) is a short, helpful summary of your page’s content. It is a primary piece of information searchers use to decide which result to click on. Having a description attribute doesn't guarantee that a search engine will use it in its search results, but in most cases it will.", $this->plugin_slug); ?></div>
					</div>
				</div>
				<div class="tab-pane fade" id="images">
					<div class="issue_data <?php echo ($img_issues['img_title_length'] > 0) ? 'issue_warning': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_title_length'); ?>"><?php echo $img_issues['img_title_length'] . " " . __('images have a title that is too short or too long', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("While there are no requirements for the number of characters for image titles we suggest that you keep image titles to a length between 30 and 65 characters.", $this->plugin_slug); ?></div>
					</div>
					<div class="issue_data <?php echo ($img_issues['img_title_missing'] > 0) ? 'issue_error': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_title_missing'); ?>"><?php echo $img_issues['img_title_missing'] . " " . __('images are missing titles', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("The title attribute provides search engines with useful information about the subject matter of the image. They use this information to help determine the best image to return for a searcher's query.", $this->plugin_slug); ?></div>
					</div>
					<div class="issue_data <?php echo ($img_issues['img_alt_length'] > 0) ? 'issue_warning': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_alt_length'); ?>"><?php echo $img_issues['img_alt_length'] . " " . __('images have an alt tag that is too short or too long', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("While there are no requirements for the number of characters for image alt tags, we suggest that you keep image alt tags to a maximum length of 125 characters.", $this->plugin_slug); ?></div>
					</div>
					<div class="issue_data <?php echo ($img_issues['img_alt_missing'] > 0) ? 'issue_error': 'issue_ok'; ?>">
						<h4><a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_alt_missing'); ?>"><?php echo $img_issues['img_alt_missing'] . " " . __('images are missing alt tags', $this->plugin_slug); ?></a></h4>
						<div class="issue_details"><?php _e("The ALT attribute provides search engines with useful information about the subject matter of the image. They use this information to help determine the best image to return for a searcher's query.", $this->plugin_slug); ?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-1">&nbsp;</div>
	</div>
	<hr>
</div>
<?php ob_end_flush(); ?>