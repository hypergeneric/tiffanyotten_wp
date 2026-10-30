<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="block-debug-log">
		<h3 class="hndle"><?php esc_html_e('Block access to debug log file', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
			<div id="firewall-block-debug-file-access-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-block-debug-file-access"); ?>
			</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Block access to debug.log file', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this if you want to block access to the debug.log file that WordPress creates when debug logging is enabled.', 'all-in-one-wp-security-and-firewall'), 'aiowps_block_debug_log_file_access', '1' == $aiowps_block_debug_log_file_access);
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('WordPress has an option to turn on the debug logging to a file located in wp-content/debug.log.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('This file may contain sensitive information.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Using this option will block external access to this file.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('You can still access this file by logging into your site via FTP.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
		</div></div>
		