<?php
/**
 * Represents the view for tagging Custom Post Types.
 *
 * @package   TippAdmin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */
?>

<div class="wrap" id="tipp_content">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p><?php _e('Tag all Custom Post Types and any media attached to them', $this->plugin_slug); ?>.</p>
<?php
$cpt_args = array(
	'public' => true,
	'_builtin' => false
);
$tipp_pt = array();
$tab_active = ' class="active"';
$post_types = get_post_types( $cpt_args, 'names' ); 
$tt = (isset($_GET['tipptype'])) ? $_GET['tipptype']: 'first'; 

if ($post_types) :
?>

	<ul class="nav nav-tabs" role="tablist">
<?php
foreach ( $post_types as $post_type ) {
	$tipp_pt[] = $post_type; ?>
		<li<?php echo ($tt === $post_type || $tt === 'first') ? ' class="active"': ''; ?>><a href="<?php echo admin_url('admin.php?page=tipp-tag-custom&tipptype=' . $post_type); ?>"><?php echo ucfirst($post_type); ?></a></li>
<?php
	$tab_active = '';
	$tt = ($tt === 'first') ? '': $tt;
}
?>

	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="<?php echo $post_type; ?>">
<?php
	$post_type = (isset($_GET['tipptype'])) ? $_GET['tipptype']: $tipp_pt[0];
	$tp = wp_count_posts($post_type);
	$ap = intval($tp->publish) + intval($tp->draft) + intval($tp->future);
	$ppp = 10;
	$cp = isset( $_GET['pagenum'] ) ? intval($_GET['pagenum']) : 1;

	$posts_args = array(
		'post_type' 		=> $post_type,
		'posts_per_page' 	=> $ppp,
		'post_status'		=> 'publish',
		'offset'			=> $ppp * ($cp - 1),
	);
	$posts_tipp = new WP_Query( $posts_args );

	$pages = ceil($tp->publish/$ppp); // Change $tp object to $ap to paginate for all posts

if ($posts_tipp->have_posts()) : 
	echo $this->tipp_pagination($pages, $_GET['page'], $cp, $post_type);
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
			$upd_tags_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_update_tags'), 'tipp-upd_tags'));
			$ttclass = 'bigimage-' . $attachment->ID;

			include('content-tag_attach.php');
		}
?>

		</div>
	</div>
<?php
	endwhile;
	echo $this->tipp_pagination($pages, $_GET['page'], $cp, $post_type);
endif;

else:
	echo '<div class="error"><p>' . __('There are no custom post types defined', $this->plugin_slug) . '.</p></div>';
endif; // have $post_types
?>
		</div>
	</div>
</div>