<?php
/**
 * Represents the view for tagging Posts/Images filtered by Issue Type.
 *
 * @package   TippAdmin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

global $dash;

$it = (isset($_GET['type'])) ? $_GET['type']: 'title_length'; // get the issue type, default title_length
$is_image = (substr($it, 0, 3) === 'img') ? true: false;
?>

<div class="wrap" id="tipp_content">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<ul class="nav nav-tabs" role="tablist">
		<li<?php echo ($it === 'title_length') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=title_length'); ?>">Title Length</a>
		</li>
		<li<?php echo ($it === 'title_missing') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=title_missing'); ?>">Title Missing</a>
		</li>
		<li<?php echo ($it === 'desc_length') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=desc_length'); ?>">Desc Length</a>
		</li>
		<li<?php echo ($it === 'desc_missing') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=desc_missing'); ?>">Desc Missing</a>
		</li>
		<li<?php echo ($it === 'img_title_length') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_title_length'); ?>">Img Title Length</a>
		</li>
		<li<?php echo ($it === 'img_title_missing') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_title_missing'); ?>">Img Title Missing</a>
		</li>
		<li<?php echo ($it === 'img_alt_length') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_alt_length'); ?>">Alt Length</a>
		</li>
		<li<?php echo ($it === 'img_alt_missing') ? ' class="active"': ''; ?>>
			<a href="<?php echo admin_url('admin.php?page=hippo_issues&type=img_alt_missing'); ?>">Alt Missing</a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="<?php echo $it; ?>">
<?php
if(!$is_image):
	$tp = $dash->meta_issues();
	$total_posts = $tp[$it];
	$posts_per_page = 10;
	$current_page = isset( $_GET['pagenum'] ) ? intval($_GET['pagenum']) : 1;
	$pages = ceil($total_posts/$posts_per_page);
	$offset = $posts_per_page * ($current_page - 1);

	$posts_tipp = $dash->meta_issues($it, $posts_per_page, $offset);

	if ($posts_tipp->have_posts()) : 
		echo $this->issues_pagination($pages, $_GET['page'], $current_page, $it);
		while ($posts_tipp->have_posts()) : $posts_tipp->the_post();
			global $post;

			$att_images = '';
			$media_items = ($is_image) ? NULL: $this->action_get_media( $post->ID );
			$to_file_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_to_file&pi='.$post->ID), 'tipp-to_file'));
			$to_title_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_to_title&pi='.$post->ID), 'tipp-to_title'));
			$to_alt_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_to_alt&pi='.$post->ID), 'tipp-to_alt'));
			$save_meta_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_update_meta&pi='.$post->ID), 'tipp-save_meta'));
			$save_all_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_save_all&pi='.$post->ID), 'tipp-save_all'));
			$seo_data = $this->get_seo_data($post->ID);
?>
	<div class="tipp_post_wrapper">
		<?php include('content-tag_items.php'); ?>

		<div class="media_wrapper">
<?php
		foreach ($media_items as $attachment) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$upd_tags_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_update_tags'), 'tipp-upd_tags'));
			$ttclass = 'bigimage-' . $attachment->ID;

			include('content-tag_attach.php');
		}
?>

		</div>
	</div>
<?php
		endwhile;
		echo $this->issues_pagination($pages, $_GET['page'], $current_page, $it);
	else:
?>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1">
			<p>&nbsp;</p>
			<div class="alert alert-info"><p><?php _e('Congratulations! You have no issues of this type', $this->plugin_slug); ?></p></div>
		</div>
	</div>
<?php
	endif; // have $post_types
else:
	$tp = $dash->image_issues();
	$total_posts = $tp[$it];
	$posts_per_page = 10;
	$current_page = isset( $_GET['pagenum'] ) ? intval($_GET['pagenum']) : 1;
	$pages = ceil($total_posts/$posts_per_page);
	$offset = $posts_per_page * ($current_page - 1);

	$posts_tipp = $dash->image_issues($it, $posts_per_page, $offset);
	$media = $posts_tipp->posts;

	if ($posts_tipp->have_posts()) : 
		echo $this->issues_pagination($pages, $_GET['page'], $current_page, $it);
?>

	<div class="tipp_post_wrapper">
		<div class="media_wrapper">
<?php
		foreach ($media as $attachment) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$upd_tags_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_update_tags'), 'tipp-upd_tags'));
			$ttclass = 'bigimage-' . $attachment->ID;

			include('content-tag_attach.php');
		}
?>

		</div>
	</div>
<?php
		echo $this->issues_pagination($pages, $_GET['page'], $current_page, $it);
	else:
?>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1">
			<p>&nbsp;</p>
			<div class="alert alert-info"><p><?php _e('Congratulations! You have no issues of this type', $this->plugin_slug); ?></p></div>
		</div>
	</div>
<?php
	endif;
endif;
?>
		</div>
	</div>
</div>