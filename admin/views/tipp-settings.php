<?php
/**
 * Represents the view for the administration settings.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Tipp_Admin Settings
 * @author    Eric Buckley <eric@dosa.io>
 * @license   GPL-2.0+
 * @link      http://littlehippo.co
 * @copyright 2014 DSA Co Ltd & Eric Buckley
 */

$images_basename = get_bloginfo('url') . '/wp-content/plugins/' . plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . '/assets/images' );
$to_trash_url = esc_url(wp_nonce_url( admin_url('admin-ajax.php?action=tipp_empty_trash'), 'tipp-to_trash'));
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<div class="col-xs-8">
		<div class="panel panel-default hide">
			<div class="panel-heading">
				<div class="panel-title"><?php _e('Subscribe to our Newsletter', $this->plugin_slug); ?></div>
			</div>
			<div class="panel-body">
				<p><?php _e('Join our mailing list for tips, tricks and WordPress SEO secrets', $this->plugin_slug); ?>.</p>
			</div>
		</div>

		<?php if (get_option('seo_plugin') !== 'HIPPO'): ?>
		<div class="alert alert-danger">
			<p><?php _e('You are using the', $this->plugin_slug); ?> <b><?php echo get_option('seo_plugin_name'); ?></b> <?php _e('SEO Plug-in', $this->plugin_slug); ?>.</p>
			<p><?php _e('Those SEO fields will be used for Pages, Posts and Custom Post Type Meta data and NOT those assigned via Little Hippo', $this->plugin_slug); ?>.</p>
		</div>
		<?php endif; ?>

	<form method="post" action="options.php" class="" role="form">
		<?php settings_fields( 'hippo_settings' ); ?>
		<?php do_settings_sections( 'hippo_settings' ); ?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title"><?php _e('General', $this->plugin_slug); ?></div>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="checkbox">
						<label class="col-sm-10 control-label">
						<input type="checkbox" name="hippo_head_cleanup" <?php echo (get_option('hippo_head_cleanup')) ? 'checked="checked" ': ''; ?>/> <?php _e('WP Head Cleanup (remove version, rel links, etc.)', $this->plugin_slug); ?>
						</label>
						<div class="clear-fix">&nbsp;</div>
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label class="col-sm-10 control-label">
						<input type="checkbox" name="hippo_help_off" <?php echo (get_option('hippo_help_off')) ? 'checked="checked" ': ''; ?>/> <?php _e('Turn off Assist and Tip notifications.', $this->plugin_slug); ?>
						</label>
						<div class="clear-fix">&nbsp;</div>
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label class="col-sm-11 control-label">
						<input type="checkbox" name="hippo_outbound_nf" <?php echo (get_option('hippo_outbound_nf')) ? 'checked="checked" ': ''; ?>/> <?php _e('No-Follow outbound links', $this->plugin_slug); ?> (<i><strong><?php _e('NOTE', $this->plugin_slug); ?>:</strong> <?php _e('Only works for links inserted into the CONTENT area', $this->plugin_slug); ?></i>.)
						</label>
						<div class="clear-fix">&nbsp;</div>
					</div>
				</div>
				<h4><?php _e('Cleanup',$this->plugin_slug); ?></h4>
				<div class="form-group">
					<label class="col-sm-3 control-label text-right"><?php _e('Post Revisions Kept', $this->plugin_slug); ?><br />(<?php _e('0 to disable', $this->plugin_slug); ?>):</label>
					<div class="col-sm-6">
					<?php $hippo_rev = (!get_option('hippo_revisions') || get_option('hippo_revisions') === '' || is_null(get_option('hippo_revisions'))) ? 5: get_option('hippo_revisions'); ?>
						<input type="text" class="form-control" name="hippo_revisions" value="<?php echo esc_attr( $hippo_rev ); ?>" />
					</div>
					<div class="col-sm-3">&nbsp;</div>
				</div>
				<div><p>&nbsp;</p></div>
				<div class="form-group">
					<label class="col-sm-3 control-label text-right"><?php _e('Auto-Save Interval (seconds)', $this->plugin_slug); ?>:</label>
					<div class="col-sm-6">
					<?php $hippo_as_int = (!get_option('hippo_autosave_interval') || get_option('hippo_autosave_interval') === '' || is_null(get_option('hippo_autosave_interval'))) ? 300: get_option('hippo_autosave_interval'); ?>
						<input type="text" class="form-control" name="hippo_autosave_interval" value="<?php echo esc_attr( $hippo_as_int ); ?>" />
					</div>
					<div class="col-sm-3">&nbsp;</div>
				</div>
				<div><p>&nbsp;</p></div>
				<p><button type="button" class="btn btn-warning btn-sm lh-empty-trash" href="<?php echo $to_trash_url; ?>"><?php _e('Empty Trash', $this->plugin_slug); ?></button> <span class="trash-loader hide" id="trash_activity"><img src="<?php echo plugins_url( '../assets/images/ajax-loader.gif', __FILE__ ); ?>" /></span><span class="trash_status hide"></span></p>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title"><?php _e('Default Titles & Metas', $this->plugin_slug); ?></div>
			</div>
			<div class="panel-body">
				<ul class="nav nav-tabs" role="tablist" id="default_meta">
					<li class="active"><a href="#homedef" role="tab" data-toggle="tab"><?php _e('Home Page', $this->plugin_slug); ?></a></li>
					<li><a href="#arcdef" role="tab" data-toggle="tab"><?php _e('Archives', $this->plugin_slug); ?></a></li>
					<li><a href="#catdef" role="tab" data-toggle="tab"><?php _e('Categories', $this->plugin_slug); ?></a></li>
					<li><a href="#tagdef" role="tab" data-toggle="tab"><?php _e('Tags', $this->plugin_slug); ?></a></li>
					<li><a href="#taxdef" role="tab" data-toggle="tab"><?php _e('Taxonomies', $this->plugin_slug); ?></a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane fade in active" id="homedef">
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Home Page Title', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $home_val = (get_option('def_home_title') === '' || is_null(get_option('def_home_title'))) ? '%seo_title%': get_option('def_home_title'); ?>
								<input type="text" class="form-control" name="def_home_title" value="<?php echo esc_attr( $home_val ); ?>" />
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Home Page Description', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $home_des = (get_option('def_home_desc') === '' || is_null(get_option('def_home_desc'))) ? '%seo_desc%': get_option('def_home_desc'); ?>
								<textarea class="form-control" rows="3" name="def_home_desc"><?php echo esc_attr( $home_des ); ?></textarea>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="arcdef">
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Title', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $arc_val = (get_option('def_arc_title') === '' || is_null(get_option('def_arc_title'))) ? '%site_title%': get_option('def_arc_title'); ?>
								<input type="text" class="form-control" name="def_arc_title" value="<?php echo esc_attr( $arc_val ); ?>" />
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Description', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $arc_des = (get_option('def_arc_desc') === '' || is_null(get_option('def_arc_desc'))) ? '%site_desc%': get_option('def_arc_desc'); ?>
								<textarea rows="3" class="form-control" name="def_arc_desc"><?php echo esc_attr( $arc_des ); ?></textarea>
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Robots', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<input type="checkbox" class="form-control" name="def_arc_robots" <?php echo (get_option('def_arc_robots')) ? 'checked="checked" ': ''; ?> /> (<?php _e('This will add rel="noindex,follow" to ALL Archive pages', $this->plugin_slug); ?>)
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="catdef">
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Title', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $cat_val = (get_option('def_cat_title') === '' || is_null(get_option('def_cat_title'))) ? '%cat_name%': get_option('def_cat_title'); ?>
								<input type="text" class="form-control" name="def_cat_title" value="<?php echo esc_attr( $cat_val ); ?>" />
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Description', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $cat_des = (get_option('def_cat_desc') === '' || is_null(get_option('def_cat_desc'))) ? '%site_desc%': get_option('def_cat_desc'); ?>
								<textarea rows="3" class="form-control" name="def_cat_desc"><?php echo esc_attr( $cat_des ); ?></textarea>
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Robots', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<input type="checkbox" class="form-control" name="def_cat_robots" <?php echo (get_option('def_cat_robots')) ? 'checked="checked" ': ''; ?> /> (<?php _e('This will add rel="noindex,follow" to all CATEGORY Archives', $this->plugin_slug); ?>)
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="tagdef">
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Title', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $tag_val = (get_option('def_tag_title') === '' || is_null(get_option('def_tag_title'))) ? '%tag_title%': get_option('def_tag_title'); ?>
								<input type="text" class="form-control" name="def_tag_title" value="<?php echo esc_attr( $tag_val ); ?>" />
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Description', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $tag_des = (get_option('def_tag_desc') === '' || is_null(get_option('def_tag_desc'))) ? '%site_desc%': get_option('def_tag_desc'); ?>
								<textarea rows="3" class="form-control" name="def_tag_desc"><?php echo esc_attr( $tag_des ); ?></textarea>
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Robots', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<input type="checkbox" class="form-control" name="def_tag_robots" <?php echo (get_option('def_tag_robots')) ? 'checked="checked" ': ''; ?> /> (<?php _e('This will add rel="noindex,follow" to all TAG Archives', $this->plugin_slug); ?>)
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="taxdef">
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Title', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $tax_val = (get_option('def_tax_title') === '' || is_null(get_option('def_tax_title'))) ? '%tax_title%': get_option('def_tax_title'); ?>
								<input type="text" class="form-control" name="def_tax_title" value="<?php echo esc_attr( $tax_val ); ?>" />
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Default Description', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<?php $tax_des = (get_option('def_tax_desc') === '' || is_null(get_option('def_tax_desc'))) ? '%site_desc%': get_option('def_tax_desc'); ?>
								<textarea rows="3" class="form-control" name="def_tax_desc"><?php echo esc_attr( $tax_des ); ?></textarea>
							</div>
						</div>
						<div><p>&nbsp;</p></div>
						<div class="form-group">
							<label class="col-sm-3 control-label text-right"><?php _e('Robots', $this->plugin_slug); ?>:</label>
							<div class="col-sm-9">
								<input type="checkbox" class="form-control" name="def_tax_robots" <?php echo (get_option('def_tax_robots')) ? 'checked="checked" ': ''; ?> /> (<?php _e('This will add rel="noindex,follow" to all TAXONOMY Archives', $this->plugin_slug); ?>)
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title"><?php _e('Tracking', $this->plugin_slug); ?></div>
			</div>
			<div class="panel-body">
				<div class="form-group settings-tracking">
					<label class="col-sm-3 control-label text-right"><?php _e('Google Analytics ID', $this->plugin_slug); ?>:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="hippo_ga_id" value="<?php echo esc_attr( get_option('hippo_ga_id') ); ?>" />
						<p><?php _e('Enter your Google Tracking ID. It is formatted like this', $this->plugin_slug); ?> UA-######-#.</p>
					</div>
				</div>
				<div class="form-group settings-tracking">
					<label class="col-sm-3 control-label text-right"><?php _e('Bounce Rate Timeout', $this->plugin_slug); ?>:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="hippo_ga_te_to" value="<?php echo esc_attr( get_option('hippo_ga_te_to', 10000) ); ?>" />
						<p><?php _e('Number of milli-seconds before a click is NOT considered a bounce', $this->plugin_slug); ?>.</p>
					</div>
				</div>
				<div class="form-group settings-tracking">
					<div class="checkbox">
						<label class="col-sm-9 control-label">
						<input type="checkbox" name="hippo_ga_off" <?php echo (get_option('hippo_ga_off')) ? 'checked="checked" ': ''; ?>/> <?php _e('Click here to turn OFF Little Hippo Auto Insertion of the Tracking code', $this->plugin_slug); ?>
						</label>
						<p class="clearfix">&nbsp;</p>
						<p class="clearfix text-danger"><strong><?php _e('WARNING', $this->plugin_slug); ?>:</strong> <?php _e('Having the tracking code inserted more than once will cause your website to return errors and possibly drop in search engine rankings. If you have another plug-in that inserts the code, you can turn off the Little Hippo auto-insertion here', $this->plugin_slug); ?>.</p>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title"><?php _e('Facebook OG Tags', $this->plugin_slug); ?></div>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Default Site Image', $this->plugin_slug); ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="fb_og_default_img" id="upload_image" value="<?php echo esc_attr( get_option('fb_og_default_img') ); ?>" />
						<input id="upload_image_button" class="button" type="button" value="Upload Image" />
					</div>
					<p class="clearfix">&nbsp;</p>
					<div class="col-sm-3">&nbsp;</div>
					<div class="col-sm-9">
						<?php 
						$fb_img = get_option('fb_og_default_img'); 
						if (isset($fb_img) && $fb_img !== ''): ?>
						<img src="<?php echo get_option('fb_og_default_img'); ?>" width="300" />
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>


		<?php submit_button(); ?>

	</form>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="panel-title"><?php _e('About Little Hippo', $this->plugin_slug); ?> <small>ver. <?php echo constant("TIPP::VERSION"); ?></small></div>
			</div>
			<div class="panel-body">
				<p><a href="http://littlehippo.co">Little Hippo SEO Tools</a> by <a href="http://dosa.io">DoSA</a>, <a href="http://www.dsa-global.com">DSA-Global</a></p>
				<p><?php _e('To ensure that we can continue development on this tool, and we are looking to build the best darn SEO tool that we can, we need you to assist us in the development', $this->plugin_slug); ?>.</p>
				<div class="col-xs-6">
					<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=payments%40dosa%2eio&lc=US&item_name=Feed%20the%20Hippo%20Donation&amount=5%2e00&currency_code=USD&button_subtype=services&no_note=0&tax_rate=0%2e000&shipping=0%2e00&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHostedGuest" target="_new"><img src="<?php echo $images_basename; ?>/Feed-the-Hippo.png" class="img-responsive" /></a>
				</div>
				<div class="col-xs-6">
					<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=payments%40dosa%2eio&lc=US&item_name=Feed%20the%20Herd%20Donation&amount=20%2e00&currency_code=USD&button_subtype=services&no_note=0&tax_rate=0%2e000&shipping=0%2e00&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHostedGuest" target="_new"><img src="<?php echo $images_basename; ?>/Feed-the-Herd.png" class="img-responsive" /></a>
				</div>
				<p>&nbsp;</p>
				<a class="coinbase-button" data-code="6def3d169929c068c403d755e08ffffc" data-button-style="donation_small" href="https://coinbase.com/checkouts/6def3d169929c068c403d755e08ffffc">Feed the Hippo a little Bitcoin</a>
				<script src="https://coinbase.com/assets/button.js" type="text/javascript"></script>
			</div>
		</div>
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="panel-title"><?php _e('Support', $this->plugin_slug); ?></small></div>
			</div>
			<div class="panel-body">
				<div class="col-xs-6">
					<a href="mailto:info@dosa.io"><img src="<?php echo $images_basename; ?>/Contact-DSA-Border.png" class="img-responsive" width="100%" /></a>
				</div>
				<div class="col-xs-6">
					<a href="https://quillengage.narrativescience.com" target="new" rel="nofollow"><img src="<?php echo $images_basename; ?>/QuillEngage.png" class="img-responsive" width="100%" /></a>
				</div>
			</div>
		</div>

	</div>
</div>
