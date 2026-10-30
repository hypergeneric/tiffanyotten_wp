<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="postbox aio_hidden" data-template="wp-file-access">
	<h3 class="hndle"><?php esc_html_e('Delete default WP files', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="auto-delete-wp-files-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("auto-delete-wp-files"); ?>
		</div>
		<div class="aio_blue_box">
			<p>
				<?php
					printf(
						/* translators: 1: file name: readme.txt, 2: file name: license.txt, 3: file name: wp-config-sample.php */
						esc_html__('This feature allows you to auto delete files such as %1$s, %2$s and %3$s which are delivered with all WP installations.', 'all-in-one-wp-security-and-firewall'),
						'readme.html',
						'license.txt',
						'wp-config-sample.php'
					);
				?>
			</p>
			<p>
				<?php esc_html_e('By deleting these files you are hiding some key pieces of information (such as WordPress version info) from potential hackers.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php printf(
						/* translators: 1: file name: readme.txt, 2: file name: license.txt, 3: file name: wp-config-sample.php */
						esc_html__('Delete %1$s, %2$s, and %3$s:', 'all-in-one-wp-security-and-firewall'),
						'readme.html',
						'license.txt',
						'wp-config-sample.php'
					);
					?>
				</th>
				<td>
					<div class="aiowps_switch_container">
						<button style="margin-right: 15px" type="button" id="aiowps_delete_default_wp_files" class="button-primary"><?php esc_html_e('Delete', 'all-in-one-wp-security-and-firewall'); ?></button>
						<?php AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Automatically delete the files after a WP core update.', 'all-in-one-wp-security-and-firewall'), 'aiowps_auto_delete_default_wp_files', '1' == $aio_wp_security->configs->get_value('aiowps_auto_delete_default_wp_files')); ?>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>