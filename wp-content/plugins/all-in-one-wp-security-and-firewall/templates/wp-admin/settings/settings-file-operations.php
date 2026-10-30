<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<h2><?php esc_html_e('Export or import your AIOS settings', 'all-in-one-wp-security-and-firewall'); ?></h2>
<div class="aio_blue_box">
	<?php
	echo '<p>' . esc_html__('This section allows you to export or import your All-In-One Security settings.', 'all-in-one-wp-security-and-firewall');
	echo '<br />' .esc_html__('This can be handy if you wanted to save time by applying the settings from one site to another site.', 'all-in-one-wp-security-and-firewall').'
	<br />'.esc_html__('Note: Before importing, it is your responsibility to know what settings you are trying to import.', 'all-in-one-wp-security-and-firewall') . ' ' . esc_html__('Importing settings blindly can cause you to be locked out of your site.', 'all-in-one-wp-security-and-firewall').'
	<br />'.esc_html__('For Example: If a settings item relies on the domain URL then it may not work correctly when imported into a site with a different domain.', 'all-in-one-wp-security-and-firewall').'
	</p>';
	?>
</div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Export AIOS settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<form action="" method="POST" id="aiowpsec-export-settings-form">
			<table class="form-table">
				<tr valign="top">
					<span class="description"><?php echo esc_html__('To export your All-In-One Security settings press the button below.', 'all-in-one-wp-security-and-firewall'); ?></span>
				</tr>
			</table>
			<input type="submit" name="aiowps_export_settings" value="<?php esc_html_e('Export AIOS settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
		</form>
	</div>
</div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Import AIOS settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<form id="aiowps_restore_settings_form" action="" method="POST">
			<table class="form-table">
				<tr valign="top">
					<span class="description"><?php echo esc_html__('Use this section to import your All-In-One Security settings from a file.', 'all-in-one-wp-security-and-firewall'); ?></span>
					<th scope="row">
						<label for="aiowps_import_settings_file_button"><?php esc_html_e('Settings file to restore from', 'all-in-one-wp-security-and-firewall'); ?>:</label>
					</th>
					<td>
						<input type="submit" id="aiowps_import_settings_file_button" name="aiowps_import_settings_file_button" class="button button-primary" value="<?php esc_html_e('Select your import settings file', 'all-in-one-wp-security-and-firewall'); ?>">
						<input name="aiowps_import_settings" type="hidden" value="1">
						<input name="aiowps_import_settings_file" type="file" id="aiowps_import_settings_file">
						<input name="aiowps_import_settings_file_contents" type="hidden" id="aiowps_import_settings_file_contents">
					</td>
				</tr>
				<tr id="import_settings_details" class="aio_hidden">
					<th scope="row">
						&nbsp;
					</th>
					<td>
						<span class="description"><?php esc_html_e('It seems you are importing settings from another site domain.', 'all-in-one-wp-security-and-firewall'); ?><br><?php esc_html_e('Please confirm the details.', 'all-in-one-wp-security-and-firewall'); ?><br></span>
						<br>
						<div id="site_details_email">
						<strong><?php esc_html_e('Email', 'all-in-one-wp-security-and-firewall');?></strong>
						<br>
						<input type="email" id="aios_import_settings_email" name="aios_import_settings_email" value="">
						<br>
						<?php /* translators: %s: Admin email */ ?>
						<span class="description"><?php echo sprintf(esc_html__('Your admin email is %s which should be replaced in the imported settings above.', 'all-in-one-wp-security-and-firewall'), esc_html(get_bloginfo('admin_email')));?></span>
						</div>
						<br>
						<div id="site_details_cookie_based_brute_force" class="aio_hidden">
							<strong><?php esc_html_e('Secret word', 'all-in-one-wp-security-and-firewall');?></strong>
							<br>
							<input type="text" id="aios_import_settings_brute_force_secret_word" name="aios_import_settings_brute_force_secret_word" value="">
							<br>
							<span class="description"><?php esc_html_e('Cookie-based brute force prevention is enabled; you must use the secret word above to access the login page.', 'all-in-one-wp-security-and-firewall');?><br></span>
							<br>
						</div>
						
						<div id="site_details_renamed_login_page" class="aio_hidden">
							<strong><?php esc_html_e('Renamed login page url', 'all-in-one-wp-security-and-firewall');?></strong>
							<br>
							<input type="text" id="aios_import_settings_renamed_login_page" name="aios_import_settings_renamed_login_page" value="">
							<br>
						</div>
						<br>
						<div id="site_details_captcha_keys" class="aio_hidden">
							<strong><?php esc_html_e('Default CAPTCHA:', 'all-in-one-wp-security-and-firewall');?></strong> <span id="captcha_type" attr_recaptcha="<?php esc_attr_e('Google reCAPTCHA V2', 'all-in-one-wp-security-and-firewall');?>" attr_turnstile="<?php esc_attr_e('Cloudflare Turnstile', 'all-in-one-wp-security-and-firewall');?>"></span>
							<br>
							<input type="text" id="aios_import_settings_site_key" name="aios_import_settings_site_key" value="" size="50"> <?php esc_html_e('Site key', 'all-in-one-wp-security-and-firewall'); ?>
							<br>
							<input type="text" id="aios_import_settings_secret_key" name="aios_import_settings_secret_key" value="" size="50"> <?php esc_html_e('Secret key', 'all-in-one-wp-security-and-firewall'); ?>
							<br>
						</div>
						<br>
						<input name="aios_import_settings_confirm" id="aios_import_settings_confirm" type="hidden" value="0">
						<input type="button" id="aios_import_settings_confirm_button" name="aios_import_settings_confirm_button" class="button button-primary" value="<?php esc_html_e('Confirm', 'all-in-one-wp-security-and-firewall'); ?>">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>