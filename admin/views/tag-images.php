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

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p>Tag "Images" and other attachments that are not specifically assigned to a post or page.</p>
<?php /*
$posts_args = array(
	'post_type' => 'post',
);
$posts_tipp = new WP_Query( $posts_args );

if ($posts_tipp->have_posts()) : 
	while ($posts_tipp->have_posts()) : $posts_tipp->the_post();
		global $post;
*/
		$media_items = $this->action_get_media( '0' );
?>
	<div class="tipp_post_wrapper">
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
<?php /*
	endwhile;
endif;*/
?>

</div>
