<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<h2><?php esc_html_e('wp-config.php file operations', 'all-in-one-wp-security-and-firewall'); ?></h2>
<div class="aio_blue_box">
	<?php
	echo '<p>'.esc_html__('Your "wp-config.php" file is one of the most important files in your WordPress installation.', 'all-in-one-wp-security-and-firewall') . ' ' . esc_html__('It is a primary configuration file and contains crucial things such as details of your database and other critical components.', 'all-in-one-wp-security-and-firewall').'
	<br />'.esc_html__('This feature allows you to backup and save your currently active wp-config.php file should you need to re-use the the backed up file in the future.', 'all-in-one-wp-security-and-firewall').'
	<br />'.esc_html__('You can also restore your site\'s wp-config.php settings using a backed up wp-config.php file.', 'all-in-one-wp-security-and-firewall').'
	</p>';
	?>
</div>
<?php if (!empty($wp_config_file_details) && true == $wp_config_file_details['require_fix']) { ?>
<div class="aio_red_box">
	<p>
	<?php
		printf(
			/* translators: 1: File name, 2: File path, 3: Current file permission. */
			esc_html__('The %1$s file at %2$s has permissions set to %3$s, which may be insecure.', 'all-in-one-wp-security-and-firewall'),
			'wp-config.php',
			esc_html($wp_config_file_details['path']),
			esc_html($wp_config_file_details['actual_permission'])
		);
		echo ' ';
		printf(
			/* translators: 1: Opening link tag to File Permissions, 2: Closing link tag. */
			esc_html__('You can use the %1$sfile permissions%2$s feature to correct it', 'all-in-one-wp-security-and-firewall'),
			'<a href="'.esc_url(admin_url('admin.php?page='.AIOWPSEC_FILESYSTEM_MENU_SLUG.'&tab=file-permissions')).'" target="_blank">',
			'</a>'
		);
	?>
	</p>
</div>
<?php } ?>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Save the current wp-config.php file', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<form action="" method="POST" id="aiowpsec-save-wp-config-form">
			<p class="description"><?php esc_html_e('Press the button below to backup and download the contents of the currently active wp-config.php file.', 'all-in-one-wp-security-and-firewall'); ?></p>
			<input type="submit" name="aiowps_save_wp_config" value="<?php esc_html_e('Backup wp-config.php file', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
		</form>
	</div>
</div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Restore from a backed up wp-config file', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<form id="aiowps_restore_wp_config_form" action="" method="POST">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="aiowps_restore_wp_config_button"><?php esc_html_e('wp-config file to restore from', 'all-in-one-wp-security-and-firewall'); ?></label>:</th>
					<td>
						<input type="submit" name="aiowps_restore_wp_config_button" class="button button-primary" value="<?php esc_html_e('Restore your wp-config file', 'all-in-one-wp-security-and-firewall'); ?>">
						<input name="aiowps_restore_wp_config" type="hidden" value="1">
						<input name="aiowps_wp_config_file" type="file" id="aiowps_wp_config_file">
						<input name="aiowps_wp_config_file_contents" type="hidden" id="aiowps_wp_config_file_contents">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
