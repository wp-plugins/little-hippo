<?php
/**
 * A reusable template that displays the Post/Page/CPT data for editing
 *
 * @package   Tipp_Admin
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */
?>

<div class="panel panel-default post_data post_data_<?php echo $post->ID; ?>">
	<div class="panel-heading">
		<h3><small>Title: </small><?php the_title(); ?> 
			<small><a href="<?php the_permalink(); ?>" target="new"><span class="label label-info"><?php _e('View', $this->plugin_slug ); ?> <span class="glyphicon glyphicon-new-window"></span></span></a></small> 
			<small><a href="<?php echo admin_url('post.php?post=' . $post->ID . '&action=edit'); ?>" target="new"><span class="label label-success"><?php _e('Edit', $this->plugin_slug ); ?> <span class="glyphicon glyphicon-new-window"></span></span></a></small>
		</h3>
	</div>
	<div class="panel-body">
		<form class="form-inline row" role="form" action="" id="tip_post_meta-<?php echo $post->ID; ?>">
			<div class="form-group form-group-sm col-xs-12">
				<label><?php _e('Meta Title', $this->plugin_slug ); ?>: </label>
				<div class="input-group seo-title full">
				<input class="form-control input-sm" type="text" id="seo-title-<?php echo $post->ID; ?>" name="seo-title-<?php echo $post->ID; ?>" value="<?php echo $seo_data['title']; ?>" />
				</div>
			</div>
			<div class="form-group form-group-sm col-xs-12">
				<label><?php _e('Meta Desc', $this->plugin_slug ); ?>: </label>
				<div class="input-group seo-descr full">
				<input class="form-control input-sm" type="text" id="seo-descr-<?php echo $post->ID; ?>" name="seo-descr-<?php echo $post->ID; ?>" value="<?php echo $seo_data['desc']; ?>" />
				</div>
			</div>
			<div class="form-group form-group-sm col-xs-12 hide">
				<label><?php _e('Meta Keywords', $this->plugin_slug ); ?>: </label>
				<input class="form-control input-sm full" type="text" id="seo-kword-<?php echo $post->ID; ?>" name="seo-kword-<?php echo $post->ID; ?>" value="<?php echo $seo_data['keywords']; ?>" />
			</div>
			<script>
			jQuery(function(){
				$ = jQuery;
				$("#seo-title-<?php echo $post->ID; ?>").characterCounter({
					counterCssClass: "seo-title-<?php echo $post->ID; ?>",
					limit: 55
				});
				$("#seo-descr-<?php echo $post->ID; ?>").characterCounter({
					counterCssClass: "seo-descr-<?php echo $post->ID; ?>",
					limit: 155
				});
			});
			</script>

	</div><!-- panel-body -->
			<div class="panel-footer post-commands">
				<span><?php _e('Copy to:', $this->plugin_slug ); ?> </span>
				<button type="button" class="btn btn-default btn-sm to_all" href="#" disabled="disabled"><?php _e('All', $this->plugin_slug ); ?></button>
				<button type="button" class="btn btn-default btn-sm to_file" href="<?php echo $to_file_url; ?>" disabled="disabled"><?php _e('File', $this->plugin_slug); ?></button>
				<button type="button" class="btn btn-primary btn-sm to_title" href="<?php echo $to_title_url; ?>"><?php _e('Title', $this->plugin_slug ); ?></button>
				<button type="button" class="btn btn-primary btn-sm to_alt" href="<?php echo $to_alt_url; ?>"><?php _e('Alt', $this->plugin_slug ); ?></button>
				<button type="button" id="save_meta_<?php echo $post->ID; ?>" class="btn btn-primary btn-sm save_meta" href="<?php echo $save_meta_url; ?>"><?php _e('Save Meta', $this->plugin_slug ); ?></button>
				<button type="button" id="save_mall_<?php echo $post->ID; ?>" class="btn btn-success btn-sm save_all" href="<?php echo $save_all_url; ?>"><?php _e('Save All', $this->plugin_slug ); ?></button>
			</div>
		</form>
</div><!-- panel -->
