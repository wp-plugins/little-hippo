<?php
/**
 * A reusable template that displays the Post/Page/CPT associated attachments
 *
 * @package   Tipp_Admin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */
?>

			<form class="form-inline" role="form" action="" id="image-<?php echo $post->ID; ?>-<?php echo $attachment->ID; ?>">
			<div class="tipp_item row" id="tipp_item_<?php echo $attachment->ID; ?>">
				<div class="tipp_thumb tipp_data col-sm-1">
					<a href="<?php echo $image_attributes[0]; ?>"><?php echo wp_get_attachment_image( $attachment->ID, array(75,75), false, array( 'class' => 'img-responsive ' . $ttclass ) ); ?></a>
				</div>
				<div class="tipp_alt tipp_data col-sm-7">
					<div class="form-group form-group-sm col-sm-6">
						<label><?php _e('File', $this->plugin_slug ); ?>: </label>
						<input class="form-control input-sm half image-file" id="image-file-<?php echo $attachment->ID; ?>" name="image-file-<?php echo $attachment->ID; ?>" value="<?php echo basename( $image_attributes[0] ); ?>" disabled="disabled" />
					</div>
					<div class="form-group form-group-sm col-sm-6">
						<label><?php _e('Title', $this->plugin_slug ); ?>: </label>
						<div class="input-group half">
							<input class="form-control input-sm half image-title" id="image-title-<?php echo $attachment->ID; ?>" name="image-title-<?php echo $attachment->ID; ?>" value="<?php echo $attachment->post_title; ?>" />
						</div>
					</div>
					<div class="form-group form-group-sm col-sm-12 clearfix">
						<label>&nbsp;<?php _e('Alt', $this->plugin_slug ); ?>: </label>
						<div class="input-group full">
							<input class="form-control input-sm full image-alt" id="image-alt-<?php echo $attachment->ID; ?>" name="image-alt-<?php echo $attachment->ID; ?>" value="<?php echo get_post_meta($attachment->ID, '_wp_attachment_image_alt', true); ?>" />
						</div>
					</div>
				</div>
				<div class="col-sm-2" id="al-wrapper">
					<div class="ajax-loader hide" id="loader-<?php echo $attachment->ID; ?>"><img src="<?php echo plugins_url( '../assets/images/ajax-loader.gif', __FILE__ ); ?>" /></div>
				</div>
				<div class="tipp_cmd tipp_data col-sm-2" id="tipp_data_<?php echo $attachment->ID; ?>">
					<button type="button" class="btn btn-primary btn-sm btn-block hide"><?php _e('Rename File from Title', $this->plugin_slug); ?></button>
					<button type="button" class="btn btn-primary btn-sm btn-block update_tags" id="update_tags_<?php echo $attachment->ID; ?>" href="<?php echo $upd_tags_url; ?>"><?php _e('Update Image Tags', $this->plugin_slug); ?></button>
				</div>
			</div>
			<script>
			jQuery(function(){
				$ = jQuery;
				$("#image-title-<?php echo $attachment->ID; ?>").characterCounter({
					counterCssClass: "chars-title-<?php echo $attachment->ID; ?>",
					limit: <?php echo get_option('img_limit_title'); ?>
				});
				$("#image-alt-<?php echo $attachment->ID; ?>").characterCounter({
					counterCssClass: "chars-alt-<?php echo $attachment->ID; ?>",
					limit: <?php echo get_option('img_limit_alt'); ?>
				});
				$(".bigimage-<?php echo $attachment->ID; ?>").tooltipster({
					content: $('<img src="<?php echo $image_attributes[0]; ?>" width="500" />'),
					animation: 'grow',
					position: 'right'
				});
			});
			</script>
			</form>
