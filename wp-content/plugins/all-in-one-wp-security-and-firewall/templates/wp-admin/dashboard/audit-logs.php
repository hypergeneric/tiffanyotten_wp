<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<?php if (!is_multisite() || current_user_can('manage_network_options')) : ?>
	<div class="postbox">
		<h3 class="hndle"><?php esc_html_e('Audit log options', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
			<form id="audit-log-retention" method="post">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="aios_audit_log_retention"><?php esc_html_e('Retention period', 'all-in-one-wp-security-and-firewall'); ?></label>
						</th>
						<td>
							<?php
							$aios_is_premium = AIOWPSecurity_Utility_Permissions::is_premium_installed();

							if (defined('AIOWPSEC_PURGE_AUDIT_LOGS_AFTER_DAYS')) {
								$aios_audit_retention = AIOWPSEC_PURGE_AUDIT_LOGS_AFTER_DAYS;
								if (!is_int($aios_audit_retention) || $aios_audit_retention < 0 || $aios_audit_retention > 3650) {
									$aios_audit_retention = AIOWPS_AUDIT_LOG_DEFAULT_RETENTION;
								}
							} elseif ($aios_is_premium) {
								global $aio_wp_security_premium;
								$aios_audit_retention = $aio_wp_security_premium->configs->get_value('aios_audit_log_retention');

								if ('custom' === $aios_audit_retention) {
									$aios_audit_retention = $aio_wp_security_premium->configs->get_value('aios_audit_log_retention_custom_days');
								}
							} else {
								$aios_audit_retention = AIOWPS_AUDIT_LOG_DEFAULT_RETENTION;
							}

							$aios_retention_days = absint($aios_audit_retention);
							$aios_retention_label = sprintf(
								/* translators: %d: Number of retention days. */
								_n('%d day', '%d days', $aios_retention_days, 'all-in-one-wp-security-and-firewall'),
								$aios_retention_days
							);

							if ($aios_is_premium) {
								do_action('aios_audit_log_retention_settings');
							} else {
								?>
								<select id="aios_audit_log_retention" disabled>
									<option value="<?php echo esc_attr($aios_audit_retention); ?>">
										<?php
										if (0 === absint($aios_audit_retention)) {
											esc_html_e('No cleanup', 'all-in-one-wp-security-and-firewall');
										} else {
											echo esc_html($aios_retention_label);
										}
										?>
									</option>
								</select>
								<?php
							}
							?>

							<p class="description" id="aios-retention-description">
								<span id="aios-retention-cleanup-description" <?php echo 0 === absint($aios_audit_retention) ? 'hidden' : ''; ?>>
									<?php
									printf(
										/* translators: %s: Audit log retention period. */
										esc_html__('Audit log events older than %s are automatically deleted.', 'all-in-one-wp-security-and-firewall'),
										'<span id="aios-retention-period">' . esc_html($aios_retention_label) . '</span>'
									);
									echo ' ';
									esc_html_e('This keeps your database lean.', 'all-in-one-wp-security-and-firewall');
									?>
								</span>

								<span id="aios-retention-no-cleanup-description" <?php echo 0 !== absint($aios_audit_retention) ? 'hidden' : ''; ?>>
									<?php
									esc_html_e('Audit log events are never deleted automatically.', 'all-in-one-wp-security-and-firewall');
									echo ' ';
									esc_html_e('Your audit log will keep growing, which can increase your database size over time.', 'all-in-one-wp-security-and-firewall');
									?>
								</span>
							</p>
							<?php if (!$aios_is_premium) { ?>
							<div class="aio_blue_box aios_premium_upsell_box aios_audit_premium_upsell">
								<div class="aios-audit-premium-heading-block">
									<span class="dashicons dashicons-lock"></span>
									<h4 class="aios-premium-heading"><?php esc_html_e('Choose how long to keep your audit logs', 'all-in-one-wp-security-and-firewall'); ?></h4>
									<span class="aios-update-premium-badge"><?php esc_html_e('PREMIUM', 'all-in-one-wp-security-and-firewall'); ?></span>
								</div>
								<p>
									<?php
									if (0 === $aios_retention_days) {
										esc_html_e(
											'The free version does not automatically delete audit log events.',
											'all-in-one-wp-security-and-firewall'
										);
									} else {
										printf(
											/* translators: %s: Audit log retention period. */
											esc_html__('The free version keeps audit log events for %s.', 'all-in-one-wp-security-and-firewall'),
											esc_html($aios_retention_label)
										);
									}
									?>
									<?php esc_html_e('With AIOS Premium you control the retention period: 30 days, 90 days, one year, or forever.', 'all-in-one-wp-security-and-firewall'); ?>
								</p>
								<p>
									<?php
									$aios_premium_item_audit_bubble_link = add_query_arg(
										array(
											'utm_source'  => 'aios',
											'utm_medium'  => 'referral',
											'utm_content'  => 'audit-log-retention-upsell',
											'utm_campaign' => 'paac-free-audit-log',
											'utm_creative_format'  => 'button',
										),
										'https://teamupdraft.com/all-in-one-security/pricing/'
									);
									?>
									<a class="button button-primary" href="<?php echo esc_url($aios_premium_item_audit_bubble_link); ?>" target="_blank" rel="noopener">
										<?php esc_html_e('Unlock custom retention', 'all-in-one-wp-security-and-firewall'); ?>
									</a>
								</p>
							</div>
							<?php } ?>
						</td>
					</tr>
				</table>
				<?php if ($aios_is_premium && !defined('AIOWPSEC_PURGE_AUDIT_LOGS_AFTER_DAYS')) submit_button(__('Save settings', 'all-in-one-wp-security-and-firewall'), 'primary', 'aios_audit_retention_settings'); ?>
			</form>
		</div>
	</div>
<?php endif; ?>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Audit logs', 'all-in-one-wp-security-and-firewall');?></h3>
	<div class="inside" id="audit-log-list-table">
		<?php $audit_log_list->prepare_items(); ?>
		<form id="tables-filter" method="post">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo esc_attr($page); ?>" />
			<?php
				if (!empty($tab)) {
					echo '<input type="hidden" name="tab" value="'.esc_attr($tab).'" />';
				}
				$audit_log_list->search_box(__('Search', 'all-in-one-wp-security-and-firewall'), 'search_audit_events');
				$audit_log_list->display();
			?>
		</form>
	</div>
</div>