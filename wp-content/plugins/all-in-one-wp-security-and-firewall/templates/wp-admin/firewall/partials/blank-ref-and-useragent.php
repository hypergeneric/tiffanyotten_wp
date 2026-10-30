<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<h3 class="hndle"><?php esc_html_e('Ban POST request settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
<div class="inside">
	<div id="firewall-ban-post-blank-headers-badge">
		<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-ban-post-blank-headers"); ?>
	</div>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php esc_html_e('Ban POST requests that have a blank user-agent and referer', 'all-in-one-wp-security-and-firewall'); ?>:</th>
			<td>
				<div class="aiowps_switch_container">
				<?php
				AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you want to ban POST requests that have a blank user-agent and referer.', 'all-in-one-wp-security-and-firewall'), 'aiowps_ban_post_blank_headers', $aiowps_ban_post_blank_headers);
				
				AIOWPSecurity_Utility_UI::tooltip(array(
					__('This feature will check whether the user-agent and referer HTTP headers are blank.', 'all-in-one-wp-security-and-firewall'),
					'',
					__('If they are both blank, the IP address associated with the request will be added to your permanent block list.', 'all-in-one-wp-security-and-firewall'),
				));
				?>
				</div>
			</td>
		</tr>
	</table>
</div>