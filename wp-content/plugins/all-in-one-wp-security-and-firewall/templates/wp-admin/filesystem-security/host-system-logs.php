<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<h2><?php esc_html_e('System logs', 'all-in-one-wp-security-and-firewall'); ?></h2>
<div class="aio_blue_box">
	<?php
		echo '<p>'.esc_html__('Your hosting platform may create error or warning logs in a file called "error_log".', 'all-in-one-wp-security-and-firewall').'<br />'.esc_html__('Depending on the error or warning, your hosting server can create several copies of this file in different directories in your WordPress installation.', 'all-in-one-wp-security-and-firewall').'<br />'.esc_html__('Review these log files regularly to identify system problems that require attention.', 'all-in-one-wp-security-and-firewall').'</p>';
	?>
</div>
<div id="aios-host-system-logs-message" ></div>

<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('View system logs', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div id="aios-host-system-logs" class="inside">
		<form action="" id="aios-host-system-logs-form" method="POST">
			<input type="hidden" name="aiowps_search_error_files"value="1"/>
			<div>
				<label for="aiowps_system_log_file"><?php esc_html_e('Enter the system log file name', 'all-in-one-wp-security-and-firewall'); ?>:</label>
				<input id="aiowps_system_log_file" type="text" size="25" name="aiowps_system_log_file" value="<?php echo esc_html($sys_log_file); ?>" />
				<span class="description"><?php esc_html_e('The default is error_log.', 'all-in-one-wp-security-and-firewall'); ?></span>
			</div>
			<div class="aio_spacer_15"></div>
			<input type="submit"  value="<?php esc_html_e('View latest system logs', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary search-error-files">
			<div id="aiowps_activejobs_table">

			</div>
		</form>
	</div>
</div>
<div id="aios-host-system-logs-results" ></div>