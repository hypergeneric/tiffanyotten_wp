<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="inside">
	<div class="postbox">
		<h3 class="hndle"><?php esc_html_e('Debug log options', 'all-in-one-wp-security-and-firewall');?></h3>
		<div class="inside">
			<form action="" id="aios-clear-debug-logs" method="POST">
				<input name="aiowpsec_clear_logs" type="submit" value="<?php esc_attr_e('Clear logs', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary aios-clear-debug-logs" data-message="<?php esc_attr_e('Are you sure you want to clear all the debug logs?', 'all-in-one-wp-security-and-firewall'); ?>">
			</form>
		</div>
	</div>
</div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Debug logs', 'all-in-one-wp-security-and-firewall');?></h3>
	<div class="inside" id="debug-list-table">
		<?php
			$debug_log_list->prepare_items();
			$debug_log_list->display();
		?>
	</div>
</div>

<div class="aio_blue_box">
	<p>
		<?php esc_html_e('This section displays information valuable for diagnosing conflicts, configuration discrepancies, or compatibility concerns with other plugins, themes, or the hosting environment.', 'all-in-one-wp-security-and-firewall'); ?>
	</p>
	<p>
		<?php esc_html_e('You can use this information to help troubleshoot issues you may be experiencing with your WordPress site or send a report to the AIOS team.', 'all-in-one-wp-security-and-firewall'); ?>
	</p>
</div>
<?php
	do_action('aios_before_report_sections');

	echo $aio_wp_security->debug_obj->generate_report();

	do_action('aios_after_report_sections');
?>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Copy/send report', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside" id="report-actions">
		<?php echo $aio_wp_security->debug_obj->generate_report_textarea(esc_html__('All-In-One Security diagnostics report', 'all-in-one-wp-security-and-firewall')); ?>
		<div>
			<button class="button" id="copy-report">
				<?php esc_html_e('Copy to clipboard', 'all-in-one-wp-security-and-firewall'); ?>
			</button>
	</div>
		<?php do_action('aios_additional_report_actions'); ?>
	</div>
</div>