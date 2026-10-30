<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<form action="" id="aios-user-accounts-settings-form">
<?php
	// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- PCP error. Variable is not global.
	$templates = array(
		'wp-username' => array(
			'title' => __('Admin user security', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => 'is_super_admin',
		),
		'display-name' => array(
			'title' => __('Display name security', 'all-in-one-wp-security-and-firewall')
		),
		'user-enumeration' => array(
			'title' => __('User enumeration settings', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_subsite_or_super_admin'),
		),
		'enforce-strong-password' => array(
			'title' => __('Password settings', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_subsite_or_super_admin'),
		)
	);

	$templates = array_filter($templates, function($template) {
		return AIOWPSecurity_Utility::apply_callback_filter($template, 'display_condition_callback');
	});

	$template_keys = array_keys($templates);
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- PCP warning. No nonce.
	$raw_subtab = isset($_GET['subtab']) ? sanitize_text_field(wp_unslash($_GET['subtab'])) : '';
	$subtab = array_key_exists($raw_subtab, $templates) ? $raw_subtab : $template_keys[0];
?>
	<div class="aiowps-postbox-container">
		<div class="aiowps-rules">
			<h3 class="hndle"><?php esc_html_e('Settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
			<div id="aiowps-rule-search">
				<span class="dashicons dashicons-search"></span>
				<input type="text" placeholder="<?php esc_attr_e('Search', 'all-in-one-wp-security-and-firewall'); ?>" class="aiowps-search">
				<span class="dashicons dashicons-no-alt clear-search"></span>
			</div>
			<ul class="aiowps-rule-list">
				<?php foreach ($templates as $key => $template) { ?>
					<li data-template="<?php echo esc_attr($key); ?>"<?php echo $key === $subtab ? ' class="aiowps-active"' : ''; ?>>
						<span class="aiowps-rule-title"><?php echo esc_html($template['title']); ?></span>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="aiowps-settings">
			<?php foreach ($templates as $key => $template) {
				$aio_wp_security->include_template(
					"wp-admin/user-security/partials/$key.php",
					false,
					array(
						'aiowps_feature_mgr' => $aiowps_feature_mgr,
						'user_accounts' => $user_accounts,
						'AIOWPSecurity_User_Security_Menu' => $AIOWPSecurity_User_Security_Menu,
					)
				);
			}
			// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			?>
		</div>
	</div>
	<?php if (AIOWPSecurity_Utility_Permissions::is_subsite_or_super_admin()) { ?>
	<div class="aiowps-actions">
		<input type="submit" name="aiowps_apply_user_account_settings" value="<?php esc_attr_e('Save user account settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
	</div>
	<?php } ?>
</form>
