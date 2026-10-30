<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="listing-directory-contents">
		<h3 class="hndle"><?php esc_html_e('Listing of directory contents', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
			<div id="firewall-disable-index-views-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-disable-index-views"); ?>
			</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Disable index views', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you want to disable directory and file listing.', 'all-in-one-wp-security-and-firewall'), 'aiowps_disable_index_views', '1' == $aiowps_disable_index_views);
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('By default, an Apache server will allow the listing of the contents of a directory if it doesn\'t contain an index.php file.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('This feature will prevent the listing of contents for all directories.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Note: In order for this feature to work "AllowOverride" of the Indexes directive must be enabled in your httpd.conf file.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Ask your hosting provider to check this if you don\'t have access to httpd.conf', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
		</div></div>
