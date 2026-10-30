<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Add salt postfix', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="enable-salt-postfix-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("enable-salt-postfix"); ?>
		</div>
		<form action="" method="POST" id="aios-enable-salt-postfix-form">
			<div class="aio_blue_box">
				<?php
				echo '<p>'.esc_html__('WordPress "salts" are secret phrases which are combined with user passwords when those passwords are stored, with the end result that they become much harder for an attacker to crack even if he managed to steal the database of your website.', 'all-in-one-wp-security-and-firewall').' <a href="https://teamupdraft.com/blog/wordpress-salts-security-added-to-aios-free-and-premium/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=salts-security-info&utm_creative_format=text" target="_blank">'.esc_html__('Learn more about WordPress Salts.', 'all-in-one-wp-security-and-firewall').'</a></p>';
				?>
			</div>
			<div class="aio_orange_box">
				<p>
					<?php
					esc_html_e('When you enable this feature, you and all other logged-in users will be logged out so that AIOS can append the additional code (the salt) to all users’ login information.', 'all-in-one-wp-security-and-firewall');
					?>
				</p>
			</div>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label>
							<?php esc_html_e('Enable salt postfix', 'all-in-one-wp-security-and-firewall'); ?>:
						</label>
					</th>
					<td>
						<div class="aiowps_switch_container">
							<?php
							AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you want to activate the salt postfix feature.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_salt_postfix', '1' == $aio_wp_security->configs->get_value('aiowps_enable_salt_postfix'));
							
							AIOWPSecurity_Utility_UI::tooltip(array(
								__('This setting will suffix the salt with an additional 64 characters.', 'all-in-one-wp-security-and-firewall'),
								'',
								__("It improves your WordPress site's cryptographic mechanism.", 'all-in-one-wp-security-and-firewall'),
							));
							?>
						</div>
					</td>
				</tr>

			</table>

			<div class="submit">
				<input type="submit" class="button-primary" name="aios_save_salt_postfix_settings" value="<?php esc_html_e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>">
			</div>
		</form>
	</div>
</div>
