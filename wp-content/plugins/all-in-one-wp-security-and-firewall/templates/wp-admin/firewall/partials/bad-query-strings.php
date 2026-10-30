<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="bad-query-strings">
		<h3 class="hndle"><?php esc_html_e('Bad query strings', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
			<div id="firewall-deny-bad-queries-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-deny-bad-queries"); ?>
			</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Deny bad query strings', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('This will help protect you against malicious queries via XSS.', 'all-in-one-wp-security-and-firewall'), 'aiowps_deny_bad_query_strings', $aiowps_deny_bad_query_strings);

						AIOWPSecurity_Utility_UI::tooltip(array(
							__('This feature will prevent malicious string attacks on your site using XSS.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Note: Some of these strings might be used for plugins or themes and hence this might break some functionality.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
		</div></div>
