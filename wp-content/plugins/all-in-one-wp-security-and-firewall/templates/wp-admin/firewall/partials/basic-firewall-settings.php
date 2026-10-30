<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="basic-firewall-settings">
		<h3 class="hndle"><?php esc_html_e('Basic firewall settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
			<div id="firewall-basic-rules-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-basic-rules"); ?>
			</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable basic firewall protection', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this to apply basic firewall protection to your site.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_basic_firewall', '1' == $aiowps_enable_basic_firewall);
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('This setting will implement the following basic firewall protection mechanisms on your site:', 'all-in-one-wp-security-and-firewall'),
							'',
							__('1) Protect your htaccess file by denying access to it.', 'all-in-one-wp-security-and-firewall'),
							__('2) Disable the server signature.', 'all-in-one-wp-security-and-firewall'),
							sprintf(
								/* translators: %s: Upload limit. */
								__('3) Limit file upload size (%sMB).', 'all-in-one-wp-security-and-firewall'),
								esc_html(AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB)
							),
							__('4) Protect your wp-config.php file by denying access to it.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('The above firewall features will be applied via your .htaccess file and should not affect your site\'s overall functionality.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('You are still advised to take a backup of your active .htaccess file just in case.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="aiowps_max_file_upload_size"><?php esc_html_e('Max file upload size (MB)', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
				<td><input id="aiowps_max_file_upload_size" type="number" min="0" max="<?php echo esc_attr($wp_max_upload_size);?>" step="1" name="aiowps_max_file_upload_size" value="<?php echo esc_attr($aiowps_max_file_upload_size); ?>" />
				<?php /* translators: %s: Upload limit. */ ?>
				<span class="description"><?php echo sprintf(esc_html__('This value is used to enforce a lower upload limit via the .htaccess file than the one currently allowed by your WordPress and server settings. (Defaults to %sMB if left blank)', 'all-in-one-wp-security-and-firewall'), esc_html(AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB)); ?></span>
				<div class="aio_red_box <?php echo ($aiowps_max_file_upload_size > 0 && $wp_max_upload_size == $aiowps_max_file_upload_size) ? "hidden" : "aios_table_cell"; ?>">
				<?php /* translators: %s: Maximum upload size limit. */ ?>
				<?php echo sprintf(esc_html__('This setting will override and limit the current maximum file upload size of %sMB.', 'all-in-one-wp-security-and-firewall'), esc_html($wp_max_upload_size)); ?>
				</div>
				</td>
			</tr>

		</table>
		</div></div>
