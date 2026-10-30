<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="postbox aio_hidden" data-template="enforce-strong-password">
	<h3 class="hndle"><?php esc_html_e('Password settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="enforce-strong-password-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("enforce-strong-password"); ?>
		</div>
		<div class="aio_blue_box">
			<?php
			echo '<p>'.esc_html__('This feature allows you to enforce the use of strong user passwords', 'all-in-one-wp-security-and-firewall').'</p>';
			echo '<p>'.esc_html__('When enabled, this feature will hide the "confirm weak password" checkbox on forms.', 'all-in-one-wp-security-and-firewall').'</p>';
			?>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enforce strong password', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this if you want to force your users to use a strong password.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enforce_strong_password', '1' == $aio_wp_security->configs->get_value('aiowps_enforce_strong_password')); ?>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<h3 class="hndle"><?php esc_html_e('HIBP password settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="hibp-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge('hibp'); ?>
		</div>
		<div class="aio_blue_box">
			<p>
			<?php
			printf(
				/* Translators: Link to "HIBP" */
				esc_html__('%s (Have I Been Pwned?) is a website that allows people to check if their email or password has shown up in a data breach.', 'all-in-one-wp-security-and-firewall'),
				'<a href="https://haveibeenpwned.com/Passwords" target="_blank">HIBP</a>'
			);
			?>
			</p>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enforce on profile update', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this if you want to enforce passwords not being in the HIBP database when updating user profiles.', 'all-in-one-wp-security-and-firewall'), 'aiowps_hibp_user_profile_update', '1' == $aio_wp_security->configs->get_value('aiowps_hibp_user_profile_update')); ?>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enforce on password reset', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this if you want to enforce passwords not being in the HIBP database when resetting passwords.', 'all-in-one-wp-security-and-firewall'), 'aiowps_http_password_reset', '1' == $aio_wp_security->configs->get_value('aiowps_http_password_reset')); ?>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
