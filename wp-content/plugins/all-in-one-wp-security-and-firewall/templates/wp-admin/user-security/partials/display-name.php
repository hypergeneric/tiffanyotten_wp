<?php if (!defined('ABSPATH')) die('No direct access.');
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- PCP error. Variables are not global.
?>
<div class="postbox aio_hidden" data-template="display-name">
	<h3 class="hndle"><?php esc_html_e('Modify accounts with identical login name and display name', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<?php $aiowps_feature_mgr->output_feature_details_badge("user-accounts-display-name"); ?>
		<div class="aio_blue_box">
			<p>
				<?php esc_html_e('When you submit a post or answer a comment, WordPress will usually display your "nickname".', 'all-in-one-wp-security-and-firewall'); ?>
				<br>
				<?php esc_html_e('By default the nickname is set to the login (or user) name of your account.', 'all-in-one-wp-security-and-firewall'); ?>
				<br>
				<?php esc_html_e('From a security perspective, leaving your nickname the same as your user name is bad practice because it gives a hacker at least half of your account\'s login credentials.', 'all-in-one-wp-security-and-firewall'); ?>
				<br>
				<br>
				<?php
				printf(
					/* translators: 1. Open strong tag, 2. Close strong tag. */
					esc_html__('Therefore to further tighten your site\'s security you are advised to change your %1$snickname%2$s and %1$sDisplay name%2$s to be different from your %1$sUsername%2$s.', 'all-in-one-wp-security-and-firewall'),
					'<strong>',
					'</strong>'
				);
				?>
			</p>
		</div>
		<?php
		// now let's find any accounts which have login name same as display name
		$login_nick_name_accounts = AIOWPSecurity_Utility::check_identical_login_and_nick_names();
		if ($login_nick_name_accounts) {
		?>
		<div class="aio_red_box">
			<p>
				<?php esc_html_e('Your site currently has the following accounts with identical login and display names.', 'all-in-one-wp-security-and-firewall'); ?>
				<span class="description">
					<?php esc_html_e('Follow the link to edit the user profile of that particular user account, change Nickname, choose a different Display name compared to Username, and press the "Update Profile" button.', 'all-in-one-wp-security-and-firewall'); ?>
				</span>
			</p>
		</div>
		<table class="form-table">
		<?php
		$edit_user_page = network_site_url('wp-admin/user-edit.php?user_id='); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- PCP error. Variable is not global.
		foreach ($login_nick_name_accounts as $usr) {
		?>
			<tr valign="top">
				<td>
					<a href="<?php echo esc_url($edit_user_page) . esc_attr($usr['ID']); ?>" target="_blank"><?php echo esc_html($usr['user_login']); ?></a>
				</td>
			</tr>
		<?php } ?>
		</table>
		<?php
		} else {
		?>
		<div id="aios_message" class="aio_green_box">
			<p>
				<strong><?php esc_html_e('No action required.', 'all-in-one-wp-security-and-firewall'); ?></strong>
				<br>
				<?php esc_html_e('Your site does not have a user account where the display name is identical to the username.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
		</div>
		<?php
		}
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		?>
	</div>
</div>
