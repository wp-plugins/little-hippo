<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   tipp
 * @author    Eric Buckley <ebuckley@siamcomm.com>
 * @license   GPL-2.0+
 * @link      http://www.siamcomm.com/tools/tipp
 * @copyright 2014 Siam Communications & Eric Buckley
 */

?>

<div class="wrap" id="tipp_content">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p><?php _e('Tag Posts and any media attached to them', $this->plugin_slug); ?>.</p>
<?php
$tp = wp_count_posts();
$ap = intval($tp->publish) + intval($tp->draft) + intval($tp->future);
$ppp = 10;
$cp = isset( $_GET['pagenum'] ) ? intval($_GET['pagenum']) : 1;

$posts_args = array(
	'post_type' 		=> 'post',
	'posts_per_page' 	=> $ppp,
	'post_status'		=> 'publish',
	'offset'			=> $ppp * ($cp - 1),
);
$posts_tipp = new WP_Query( $posts_args );

$pages = ceil($tp->publish/$ppp); // Change $tp object to $ap to paginate for all posts

if ($posts_tipp->have_posts()) :
	echo $this->tipp_pagination($pages, $_GET['page'], $cp);
	while ($posts_tipp->have_posts()) : $posts_tipp->the_post();
		global $post;

		$att_images = '';
		$media_items = $this->action_get_media( $post->ID );
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
			$image = wp_get_image_editor( $image_attributes[0] );
			$upd_tags_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_update_tags'), 'tipp-upd_tags' ));
			$ttclass = 'bigimage-' . $attachment->ID;

			include('content-tag_attach.php');
		}
?>

		</div>
	</div>
<?php
	endwhile;
	echo $this->tipp_pagination($pages, $_GET['page'], $cp);
endif;
?>
</div>