<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<h2><?php esc_html_e('404 detection configuration', 'all-in-one-wp-security-and-firewall'); ?></h2>
<div class="aio_blue_box">
	<p>
	<?php esc_html_e('A 404 or Not Found error occurs when somebody tries to access a non-existent page on your website.', 'all-in-one-wp-security-and-firewall'); ?>
	<br>
	<?php esc_html_e('Typically, most 404 errors happen quite innocently when people have mistyped a URL or used an old link to page which doesn\'t exist anymore.', 'all-in-one-wp-security-and-firewall'); ?>
	<br>
	<?php esc_html_e('However, in some cases you may find many repeated 404 errors which occur in a relatively short space of time and from the same IP address which are all attempting to access a variety of non-existent page URLs.', 'all-in-one-wp-security-and-firewall'); ?>
	<br>
	<?php esc_html_e('Such behaviour can mean that a hacker might be trying to find a particular page or URL for sinister reasons.', 'all-in-one-wp-security-and-firewall'); ?>
	<br>
	<?php
	$aios_smart_404_notice  = __('With this feature enabled, you can use the table below to manually temporarily block IP addresses.', 'all-in-one-wp-security-and-firewall');
	$aios_smart_404_notice .= '<br>';
	$aios_smart_404_notice .= sprintf(
		/* translators: 1: Open strong tag, 2: Close strong tag. */
		__('The %1$sSmart 404%2$s feature in Premium automatically detects and blocks these IP addresses.', 'all-in-one-wp-security-and-firewall'),
		'<strong>',
		'</strong>'
	);

	$aios_smart_404_notice = apply_filters('aios_smart_404_notice', $aios_smart_404_notice);

	echo wp_kses_post($aios_smart_404_notice);
	?>
	</p>
</div>
<?php if (!defined('AIOWPSECURITY_NOADS_B') || !AIOWPSECURITY_NOADS_B) { ?>
	<div class="aio_grey_box">
		<p>
		<?php
		printf(
			/* translators: 1: Opening link tag 2: closing link tag */
			esc_html__('You may also be interested in %1$sAll-In-One Security Premium%2$s.', 'all-in-one-wp-security-and-firewall'),
			'<strong><a href="https://teamupdraft.com/all-in-one-security/pricing/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=premium-cta-on-brute-force-tab&utm_creative_format=text" target="_blank">',
			'</a></strong>'
		);
		echo '<br>';
		printf(
			/* translators: 1: open strong tag, 2: close strong tag. */
			esc_html__('This plugin adds a number of extra features including %1$ssmart 404 blocking%2$s and %1$scountry IP blocking%2$s.', 'all-in-one-wp-security-and-firewall'),
			'<strong>',
			'</strong>'
		);
		?>
		</p>
	</div>
<?php } ?>
<div class="postbox">
<h3 class="hndle"><?php esc_html_e('404 detection options', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="firewall-enable-404-blocking-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-enable-404-blocking"); ?>
		</div>
		<form action="" id="aios-404-detection-settings-form">
		<table class="form-table">
			<tr valign="top">
					<th scope="row"><?php esc_html_e('Enable 404 IP detection', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this option to detect IP addresses that return 404 errors.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_404_IP_lockout', '1' == $aio_wp_security->configs->get_value('aiowps_enable_404_IP_lockout')); ?>
						</div>
					</td>
			</tr>
			<!-- currently this option is automatically set when the aiowps_enable_404_IP_lockout feature is turned on
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable 404 event logging', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
				<input name="aiowps_enable_404_logging" type="checkbox"<?php if ($aio_wp_security->configs->get_value('aiowps_enable_404_logging')=='1') echo ' checked="checked"'; ?> value="1"/>
				<span class="description"><?php esc_html_e('Check this if you want to enable the logging of 404 events', 'all-in-one-wp-security-and-firewall'); ?></span>
				</td>
			</tr>
			-->
			<tr valign="top">
				<th scope="row"><label for="aiowps_404_lockout_time_length"><?php esc_html_e('Time length of 404 lockout (minutes)', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
				<td><input id="aiowps_404_lockout_time_length" type="number" min="1" name="aiowps_404_lockout_time_length" value="<?php echo esc_attr($aio_wp_security->configs->get_value('aiowps_404_lockout_time_length')); ?>" />
				<span class="description"><?php esc_html_e('Set the length of time for which a locked IP address will be prevented from visiting your site.', 'all-in-one-wp-security-and-firewall'); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="aiowps_404_lock_redirect_url"><?php esc_html_e('404 lockout redirect URL', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
				<td><input id="aiowps_404_lock_redirect_url" type="text" size="50" name="aiowps_404_lock_redirect_url" value="<?php echo esc_url($aio_wp_security->configs->get_value('aiowps_404_lock_redirect_url'), array('http', 'https')); ?>" />
				<span class="description"><?php esc_html_e('A locked visitor will be automatically redirected to this URL.', 'all-in-one-wp-security-and-firewall'); ?></span>
				</td>
			</tr>
		</table>
		<input type="submit" name="aiowps_save_404_detect_options" value="<?php esc_html_e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
	</form>
	</div>
</div>
<div class="aios-404-detection-container <?php if ('1' !== $aio_wp_security->configs->get_value('aiowps_enable_404_IP_lockout')) echo 'aio_hidden'; ?>">
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('404 event logs', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside" id="404-log-list-table">
		<?php
		// translators: %1$d - Purge event records after number of days.
		echo '<p>' . esc_html__('This list displays the 404 event logs when somebody tries to access a non-existent page on your website.', 'all-in-one-wp-security-and-firewall').'<br/><strong>'.sprintf(esc_html__('404 event logs that are older than %1$d days are purged automatically.', 'all-in-one-wp-security-and-firewall'), esc_html(apply_filters('aios_purge_events_records_after_days', AIOS_PURGE_EVENTS_RECORDS_AFTER_DAYS))).'</strong></p>';

		// Fetch, prepare, sort, and filter our data...
		$event_list_404->prepare_items();
		// echo "put table of locked entries here";
		?>
		<form id="tables-filter" method="post">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo esc_attr($page); ?>" />
			<?php $event_list_404->search_box(__('Search', 'all-in-one-wp-security-and-firewall'), 'search_404_events'); ?>
			<?php
			if (isset($tab)) {
				echo '<input type="hidden" name="tab" value="'.esc_attr($tab).'" />';
			}
			?>
			<!-- Now we can render the completed list table -->
			<?php $event_list_404->display(); ?>
		</form>
	</div>
</div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Export to CSV', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<form id="aios-export-404-csv-form" method="POST" action="">
			<?php wp_nonce_field('aiowpsec-export-404-event-logs-to-csv-nonce'); ?>
			<table class="form-table">
				<tr valign="top">
				<span class="description"><?php esc_html_e('Press this button if you wish to download this log in CSV format.', 'all-in-one-wp-security-and-firewall'); ?></span>
				</tr>
			</table>
			<input type="submit" name="aiowps_export_404_event_logs_to_csv" value="<?php esc_html_e('Export to CSV', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary"/>
		</form>
	</div>
</div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Delete all 404 event logs', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<form action="" id="aios-delete-404-form">
			<table class="form-table">
				<tr valign="top">
				<span class="description"><?php esc_html_e('Press this button if you wish to purge all 404 event logs from the DB.', 'all-in-one-wp-security-and-firewall'); ?></span>
				</tr>
			</table>
			<input type="submit" name="aiowps_delete_404_event_records" value="<?php esc_html_e('Delete all 404 event logs', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete all records?', 'all-in-one-wp-security-and-firewall'));?>')">
		</form>
	</div>
</div>
</div>