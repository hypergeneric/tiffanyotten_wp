<?php if (!defined('ABSPATH')) die('No direct access.'); ?>

<div class="aio_blue_box">
	<p><?php echo esc_html__('Connect a TeamUpdraft account to enable features that use the TeamUpdraft cloud.', 'all-in-one-wp-security-and-firewall') . ' ' . esc_html__("If you don't have an account yet, one will be created for you.", 'all-in-one-wp-security-and-firewall'); ?></p>
</div>
<form action="" method="POST" id="aios-team-updraft-login-form">
	<table class="form-table">
		<tr valign="top" class="aiowps_updraft_credential_row">
			<th scope="row"><label for="aiowps_updraft_email"><?php esc_html_e('Email', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
			<td><input id="aiowps_updraft_email" type="email" name="aiowps_updraft_email" value="" required/></td>
		</tr>
		<tr valign="top" class="aiowps_updraft_credential_row">
			<th scope="row"><label for="aiowps_updraft_password"><?php esc_html_e('Password', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
			<td>
				<input type="password" autocomplete="new-password" id="aiowps_updraft_password" name="aiowps_updraft_password" class="input password-input" value="" required>
			</td>
		</tr>
		<tr valign="top" class="aiowps_updraft_credential_row">
			<th scope="row"><label for="aiowps_updraft_terms"><?php esc_html_e('Terms and conditions', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
			<td>
				<input id="aiowps_updraft_terms" type="checkbox" name="aiowps_updraft_terms" value="1">
				<a href="https://teamupdraft.com/privacy" target="_blank"><?php esc_html_e('I consent to teamupdraft.com account terms and policies', 'all-in-one-wp-security-and-firewall'); ?></a>
			</td>
		</tr>
		<tr valign="top" id="aiowps_updraft_two_factor_row" style="display: none;">
			<th scope="row"><label for="aiowps_updraft_two_factor_code"><?php esc_html_e('Two-factor authentication code', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
			<td>
				<input id="aiowps_updraft_two_factor_code" type="text" name="aiowps_updraft_two_factor_code" value="" autocomplete="one-time-code" inputmode="numeric">
			</td>
		</tr>
	</table>
	<?php submit_button(__('Connect', 'all-in-one-wp-security-and-firewall'), 'primary', 'aios_updraft_login_submit', true, array('id' => 'aios_updraft_login_submit')); ?>
</form>
