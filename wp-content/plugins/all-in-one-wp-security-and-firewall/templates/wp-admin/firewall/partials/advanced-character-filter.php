<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="advanced-character-filter">
		<h3 class="hndle"><?php esc_html_e('Advanced character string filter', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
		<div id="firewall-advanced-character-string-filter-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-advanced-character-string-filter"); ?>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable advanced character string filter', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(__('This will block character sequences which resemble XSS attacks.', 'all-in-one-wp-security-and-firewall'), 'aiowps_advanced_char_string_filter', $aiowps_advanced_char_string_filter);
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('This is an advanced character string filter to prevent malicious string attacks on your site coming from Cross Site Scripting (XSS).', 'all-in-one-wp-security-and-firewall'),
							'',
							__('This setting matches for common malicious string patterns and exploits and will produce a 403 error for the hacker attempting the query.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Note: Some strings for this setting might break some functionality.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
		</div></div>
