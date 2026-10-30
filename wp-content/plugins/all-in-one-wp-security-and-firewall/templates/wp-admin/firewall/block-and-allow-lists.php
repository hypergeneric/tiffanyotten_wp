<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<h2><?php esc_html_e('Ban IPs or user agents', 'all-in-one-wp-security-and-firewall'); ?></h2>
<div class="aio_blue_box">
	<p>
	<?php
		esc_html_e('The All-In-One Security blacklist feature gives you the option of banning certain host IP addresses or ranges and also user agents.', 'all-in-one-wp-security-and-firewall');
		echo '<br>';
		esc_html_e('This feature will deny total site access for users which have IP addresses or user agents matching those which you have configured in the settings below.', 'all-in-one-wp-security-and-firewall');
		echo '<br>';
		esc_html_e('Black-listed visitors will be blocked as soon as WordPress loads, preventing them from gaining any further access.', 'all-in-one-wp-security-and-firewall');
	?>
	</p>
</div>
<?php if (!defined('AIOWPSECURITY_NOADS_B') || !AIOWPSECURITY_NOADS_B) { ?>
	<div class="aio_grey_box">
		<p>
		<?php
			printf(
				/* translators: 1: Opening link & strong tags 2: closing link & strong tags */
				esc_html__('You may also be interested in %1$sAll-In-One Security Premium%2$s.', 'all-in-one-wp-security-and-firewall'),
				'<strong><a href="https://teamupdraft.com/all-in-one-security/pricing/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=firewall-tab-cta-for-premium&utm_creative_format=text" target="_blank">',
				'</a></strong>'
			);
		?>
			<br>
			<?php
			printf(
				/* translators: 1: strong tag 2: closing strong tag */
				esc_html__('This plugin adds a number of extra features including %1$ssmart 404 blocking%2$s and %1$scountry IP blocking%2$s.', 'all-in-one-wp-security-and-firewall'),
				'<strong>',
				'</strong>'
			);
			?>
		</p>
	</div>
<?php } ?>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('IP hosts and user agent blacklist settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="blacklist-manager-ip-user-agent-blacklisting-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("blacklist-manager-ip-user-agent-blacklisting"); ?>
		</div>
		<form action="" id="aios-blacklist-settings-form">
			<div class="aio_orange_box">
				<p>
					<?php
					esc_html_e('This feature can lock you out of admin if it doesn\'t work correctly on your site.', 'all-in-one-wp-security-and-firewall');
					echo ' ';
					printf(
						/* translators: 1: Opening link tag 2: closing link tag */
						esc_html__('%1$sPlease read the following message%2$s before activating this feature.', 'all-in-one-wp-security-and-firewall'),
						'<a href="https://teamupdraft.com/docs/aios/how-to/protecting-your-website-against-brute-force-attacks/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=you-must-read-this-to-activate-blacklist-feature&utm_creative_format=text#using-the-intermediate-and-advanced-features-in-aios" target="_blank">',
						'</a>'
					);
					?>
				</p>
			</div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Enable IP or user agent blacklisting', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this if you want the banning (or blacklisting) of selected IP addresses and/or user agents specified in the settings below', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_blacklisting', '1' == $aiowps_enable_blacklisting); ?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<?php
					AIOWPSecurity_Utility_UI::ip_input_textarea(__('Enter IP addresses:', 'all-in-one-wp-security-and-firewall'), 'aiowps_banned_ip_addresses', $aiowps_banned_ip_addresses, __('Enter one or more IP addresses or IP ranges.', 'all-in-one-wp-security-and-firewall'));
					?>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="aiowps_banned_user_agents"><?php esc_html_e('Enter user agents:', 'all-in-one-wp-security-and-firewall'); ?></label></th>
					<td>
						<textarea id="aiowps_banned_user_agents" name="aiowps_banned_user_agents" rows="5" cols="50"><?php echo esc_textarea($aiowps_banned_user_agents); ?></textarea>
						<br>
						<span class="description"><?php esc_html_e('Enter one or more user agent strings.', 'all-in-one-wp-security-and-firewall');?></span>
						<?php
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('The user agent string will be checked in a case-insensitive manner.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Each user agent string must be on a new line.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Example 1 - A single user agent string to block:', 'all-in-one-wp-security-and-firewall'),
							'',
							'SquigglebotBot',
							'',
							__('Example 2 - A list of more than 1 user agent strings to block:', 'all-in-one-wp-security-and-firewall'),
							'',
							'baiduspider',
							'SquigglebotBot',
							'SurveyBot',
							'VoidEYE',
							'webcrawl.net',
							'YottaShopping_Bot',
						));
						?>
					</td>
				</tr>
			</table>
			<?php submit_button(__('Save settings', 'all-in-one-wp-security-and-firewall'), 'primary', 'aiowps_save_blacklist_settings');?>
		</form>
	</div>
</div>
<?php
$aio_wp_security->include_template('wp-admin/firewall/partials/allowlist.php', false, compact('allowlist'));
