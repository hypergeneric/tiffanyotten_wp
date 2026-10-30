<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<h3 class="hndle"><?php esc_html_e('Block fake Googlebots settings', 'all-in-one-wp-security-and-firewall'); ?></h3>	
<div class="inside">
	<div id="firewall-block-fake-googlebots-badge">
		<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-block-fake-googlebots"); ?>
	</div>
	<div class="aio_blue_box">
		<p>
			<?php esc_html_e('This feature allows you to block bots which are impersonating as a Googlebot but actually aren\'t. (In other words they are fake Google bots)', 'all-in-one-wp-security-and-firewall'); ?>
		</p>
		<p>
			<?php esc_html_e('Googlebots have a unique identity which cannot easily be forged and this feature will identify any fake Google bots and block them from reading your site\'s pages.', 'all-in-one-wp-security-and-firewall'); ?>
		</p>
	</div>
	<div class="aio_yellow_box">
		<p>
			<strong><?php esc_html_e('Attention:', 'all-in-one-wp-security-and-firewall'); ?></strong> 
			<?php esc_html_e('Sometimes non-malicious Internet organizations might have bots which impersonate as a "Googlebot".', 'all-in-one-wp-security-and-firewall'); ?>
		</p>
		<p>
			<?php esc_html_e('Just be aware that if you activate this feature the plugin will block all bots which use the "Googlebot" string in their User Agent information but are NOT officially from Google (irrespective of whether they are malicious or not).', 'all-in-one-wp-security-and-firewall'); ?>
		</p>
		<p>
			<?php esc_html_e('All other bots from other organizations such as "Yahoo", "Bing" etc will not be affected by this feature.', 'all-in-one-wp-security-and-firewall'); ?>
		</p>
	</div>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php esc_html_e('Block fake Googlebots', 'all-in-one-wp-security-and-firewall'); ?>:</th>
			<td>
				<div class="aiowps_switch_container">
					<?php
					AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you want to block all fake Googlebots.', 'all-in-one-wp-security-and-firewall'), 'aiowps_block_fake_googlebots', $aiowps_block_fake_googlebots);

					AIOWPSecurity_Utility_UI::tooltip(array(
						__('This feature will check if the User Agent information of a bot contains the string "Googlebot".', 'all-in-one-wp-security-and-firewall'),
						'',
						__('It will then perform a few tests to verify if the bot is legitimately from Google and if so it will allow the bot to proceed.', 'all-in-one-wp-security-and-firewall'),
						'',
						__('If the bot fails the checks then the plugin will mark it as being a fake Googlebot and it will block it', 'all-in-one-wp-security-and-firewall'),
					));
					?>
				</div>
			</td>
		</tr>
	</table>
</div>