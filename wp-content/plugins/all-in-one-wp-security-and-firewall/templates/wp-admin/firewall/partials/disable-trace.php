<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox">
		<h3 class="hndle"><?php esc_html_e('Trace and track', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
			<div id="firewall-disable-trace-track-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-disable-trace-track"); ?>
			</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Disable trace and track', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this to disable trace and track.', 'all-in-one-wp-security-and-firewall'), 'aiowps_disable_trace_and_track', '1' == $aio_wp_security->configs->get_value('aiowps_disable_trace_and_track'));
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('HTTP Trace attack (XST) can be used to return header requests and grab cookies and other information.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('This hacking technique is usually used together with cross site scripting attacks (XSS).', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Disabling trace and track on your site will help prevent HTTP Trace attacks.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
		</div></div>
