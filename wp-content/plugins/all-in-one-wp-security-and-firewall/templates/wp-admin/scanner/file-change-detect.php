<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<?php
	// Display an alert warning message if a file change was detected
	if ($aiowps_fcds_change_detected) {
		echo '<div id="aiowps_fcds_change_detected" class="error"><p><strong>' . esc_html__('The scan has detected that there was a change in your website\'s files.', 'all-in-one-wp-security-and-firewall') .' <a href="#" data-reset_change_detected="1" class="aiowps_view_last_fcd_results" >'.esc_html__('View the scan results and clear this message', 'all-in-one-wp-security-and-firewall').'</a></strong></p></div>';
	}
?>
<div class="aio_blue_box">
	<?php
		echo '<p>' . esc_html__('If given an opportunity, hackers can insert their code or files into your system which they can then use to carry out malicious acts on your site.', 'all-in-one-wp-security-and-firewall') .'<br>' . esc_html__('Knowing about file changes can help you prevent hackers from damaging your website.', 'all-in-one-wp-security-and-firewall') .'<br>' . esc_html__('WordPress core files, plugin files, and files with extensions such as ".php" and ".js" should not change often.', 'all-in-one-wp-security-and-firewall') .'<br>' . esc_html__('If a change occurs, the scan identifies the affected file.', 'all-in-one-wp-security-and-firewall') .'<br>' . esc_html__('The "File Change Detection Feature" will notify you of any file change which occurs on your system, including the addition and deletion of files by performing a regular automated or manual scan of your system\'s files.', 'all-in-one-wp-security-and-firewall') .'<br>' . esc_html__('You can exclude files or folders that change often during normal operation.', 'all-in-one-wp-security-and-firewall') .'<br>' . esc_html__('For example, log files and cache files may change frequently.', 'all-in-one-wp-security-and-firewall') . '</p>';
	?>
</div>
<div class="postbox aiowps_next_scheduled_scan_wrapper">
	<div class="schedule">
		<div class="aiowps_next_scheduled_entity">
			<div class="aiowps_next_scheduled_heading">
				<strong><?php echo esc_html__('Next file scan', 'all-in-one-wp-security-and-firewall').':';?></strong>
			</div>
			<div id="aiowps-next-files-scan-inner">
				<?php
					if (false === $next_fcd_scan_time) {
						echo '<span>' . esc_html__('Nothing is currently scheduled', 'all-in-one-wp-security-and-firewall') . '</span>';
					} else {
						echo '<span class="aiowps_next_scheduled_date_time">' . esc_html($next_fcd_scan_time) . '</span>';
					}
				?>
			</div>
		</div>
		<div class="aiowps_next_scheduled_entity">
			<div class="aiowps_next_scheduled_heading">
				<strong><?php echo esc_html__('Previous file scan results', 'all-in-one-wp-security-and-firewall').':';?></strong>
			</div>
			<div id="aiowps-previous-files-scan-inner">
				<span>
				<?php
					if ($previous_scan) {
						echo '<a href="#" class="aiowps_view_last_fcd_results">' . esc_html__('View the last file scan results', 'all-in-one-wp-security-and-firewall') . '</a>';
					} else {
						esc_html_e('No previous scan results', 'all-in-one-wp-security-and-firewall');
					}
				?>
				</span>
			</div>
		</div>
		<div class="aiowps_time_now_wrapper">
			<?php
			// wp_date() is WP 5.3+, but performs translation into the site locale
			$current_time = AIOWPSecurity_Utility::convert_timestamp(null, 'D, F j, Y H:i');
			?>
			<span class="aiowps_time_now_label"><?php echo esc_html__('Time now', 'all-in-one-wp-security-and-firewall').': ';?></span>
			<span class="aiowps_time_now"><?php echo esc_html($current_time);?></span>
		</div>
	</div>
	<div class="aiowps_scan_btn_wrapper">
		<button id="aiowps_manual_fcd_scan" type="button" class="button button-primary button-large button-hero"><?php esc_html_e('Scan now', 'all-in-one-wp-security-and-firewall'); ?></button>
		<p id="aiowps_manual_fcd_scan_spinner" style="display:none;"><span class="aiowps_spinner spinner visible"></span><?php esc_html_e('Processing...', 'all-in-one-wp-security-and-firewall'); ?></p>
		<p><?php echo '<a href="#" id="aiowps_cancel_fcd_scan" style="display: none; color: red;" data-message="'.esc_attr__('Are you sure you want to stop the file scan?', 'all-in-one-wp-security-and-firewall').'">' . esc_html__('Cancel scan', 'all-in-one-wp-security-and-firewall') . '</a>'; ?></p>
		<p><?php echo esc_html__('or schedule regular file scans below.', 'all-in-one-wp-security-and-firewall');?></p>
	</div>
	<div id="aiowps_activejobs_table">
		<p id="aiowps_activejobs_spinner" style="display:none;"><span class="aiowps_spinner spinner visible"></span><?php esc_html_e('Processing...', 'all-in-one-wp-security-and-firewall'); ?></p>
	</div>
	<div id="aiowps_previous_scan_wrapper">
	</div>
</div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('File change detection settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="scan-file-change-detection-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge('scan-file-change-detection'); ?>
		</div>
		<div id="aios-file-change-info-box"></div>
		<form action="" method="POST" id="aiowpsec-scheduled-fcd-scan-form">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Enable automated file change detection scan', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this to scan your files automatically at the interval you set below.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_automated_fcd_scan', '1' == $aiowps_enable_automated_fcd_scan); ?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="aiowps_fcd_scan_frequency"><?php esc_html_e('Scan time interval', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
					<td><input id="aiowps_fcd_scan_frequency" type="number" min="1" name="aiowps_fcd_scan_frequency" value="<?php echo esc_attr($aiowps_fcd_scan_frequency); ?>" />
						<select id="backup_interval" name="aiowps_fcd_scan_interval">
							<option value="0" <?php selected($aiowps_fcd_scan_interval, '0'); ?>><?php esc_html_e('Hours', 'all-in-one-wp-security-and-firewall'); ?></option>
							<option value="1" <?php selected($aiowps_fcd_scan_interval, '1'); ?>><?php esc_html_e('Days', 'all-in-one-wp-security-and-firewall'); ?></option>
							<option value="2" <?php selected($aiowps_fcd_scan_interval, '2'); ?>><?php esc_html_e('Weeks', 'all-in-one-wp-security-and-firewall'); ?></option>
						</select>
					<span class="description"><?php esc_html_e('Set how often you want a scan to run.', 'all-in-one-wp-security-and-firewall'); ?></span>
					</td> 
				</tr>
				<tr valign="top">
					<th scope="row"><label for="aiowps_fcd_exclude_filetypes"><?php esc_html_e('File types to ignore', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
					<td>
						<textarea id="aiowps_fcd_exclude_filetypes" name="aiowps_fcd_exclude_filetypes" rows="5" cols="50"><?php echo esc_textarea($aiowps_fcd_exclude_filetypes); ?></textarea>
						<br>
						<span class="description"><?php esc_html_e('Enter each file type or extension on a new line to exclude it from the file change detection scan.', 'all-in-one-wp-security-and-firewall'); ?></span>
						<?php
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('You can exclude file types from the scan which would not normally pose any security threat if they were changed.', 'all-in-one-wp-security-and-firewall'),
							__('These can include things such as image files.', 'all-in-one-wp-security-and-firewall'),
							'',
							/* translators: 1. JPG, 2. PNG, 3. BMP. */
							sprintf(__('Example: If you want the scanner to ignore files of type %1$s, %2$s, and %3$s, then you would enter the following:', 'all-in-one-wp-security-and-firewall'), 'jpg', 'png', 'bmp'),
							'',
							'jpg',
							'png',
							'bmp',
						));
						?>
					</td> 
				</tr>
				<tr valign="top">
					<th scope="row"><label for="aiowps_fcd_exclude_files"><?php esc_html_e('Files/Directories to ignore', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
					<td>
						<textarea id="aiowps_fcd_exclude_files" name="aiowps_fcd_exclude_files" rows="5" cols="50"><?php echo esc_textarea($aiowps_fcd_exclude_files); ?></textarea>
						<br>
						<span class="description"><?php esc_html_e('Enter each file or directory on a new line to exclude it from the file change detection scan.', 'all-in-one-wp-security-and-firewall'); ?></span>
						<?php
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('You can exclude specific files/directories from the scan which would not normally pose any security threat if they were changed.', 'all-in-one-wp-security-and-firewall'),
							__('These can include things such as log files.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Example: If you want the scanner to ignore certain files in different directories or whole directories, then you would enter the following:', 'all-in-one-wp-security-and-firewall'),
							'',
							'cache/config/master.php',
							__('somedirectory', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</td> 
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="aiowps_fcd_scan_email_address"><?php esc_html_e('Send an email when a file change is detected', 'all-in-one-wp-security-and-firewall'); ?>:</label>
					</th>
					<td>
						<div class="aiowps_switch_container">
							<?php AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this to receive an email when a file change is detected.', 'all-in-one-wp-security-and-firewall'), 'aiowps_send_fcd_scan_email', '1' == $aiowps_send_fcd_scan_email); ?>
						</div>
						<br>
							<textarea name="aiowps_fcd_scan_email_address" id="aiowps_fcd_scan_email_address" rows="5" cols="50"><?php echo esc_textarea(wp_unslash(AIOWPSecurity_Utility::get_textarea_str_val($aiowps_fcd_scan_email_address))); ?></textarea>
						<br>
						<span class="description"><?php esc_html_e('Enter one or more email addresses on a new line.', 'all-in-one-wp-security-and-firewall'); ?></span>
					</td>
				</tr>
			</table>
			<input type="submit" name="aiowps_schedule_fcd_scan" value="<?php esc_html_e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary" />
		</form>
	</div>
</div>