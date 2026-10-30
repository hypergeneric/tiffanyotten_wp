<?php if (!defined('ABSPATH')) die('Access denied.'); ?>

<h2><?php esc_html_e('PHP firewall settings', 'all-in-one-wp-security-and-firewall'); ?></h2>
<?php // Only the WP REST API settings are shown outside the main site or to non-super admins, and they are saved with their own per-site command. ?>
<form action="" id="aios-php-firewall-settings-form" data-command="<?php echo esc_attr(AIOWPSecurity_Utility_Permissions::is_main_site_and_super_admin() ? 'perform_php_firewall_settings' : 'perform_save_rest_api_settings'); ?>">
	<?php

	$templates = array(
		'xmlrpc-pingback-protection' => array(
			'title' => __('Security enhancements', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_main_site_and_super_admin'),
		),
		'disable-rss-atom' => array(
			'title' => __('Feed control', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_main_site_and_super_admin'),
		),
		'proxy-comment' => array(
			'title' => __('Comment protection', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_main_site_and_super_admin'),
		),
		'bad-query-strings' => array(
			'title' => __('URL security', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_main_site_and_super_admin'),
		),
		'advanced-character-filter' => array(
			'title' => __('String filtering', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_main_site_and_super_admin'),
		),
		'ng' => array(
			'title' => __('nG firewall rules', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_main_site_and_super_admin'),
		),
		'wp-rest-api' => array(
			'title' => __('WP REST API', 'all-in-one-wp-security-and-firewall')
		),
		'internet-bots' => array(
			'title' => __('Internet bot settings', 'all-in-one-wp-security-and-firewall'),
			'display_condition_callback' => array('AIOWPSecurity_Utility_Permissions', 'is_main_site_and_super_admin'),
		)
	);

	$templates = apply_filters('aios_modify_php_firewall_rules_template', $templates);

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- PCP warning. No nonce.
	$subtab = isset($_GET['subtab']) ? sanitize_text_field(wp_unslash($_GET['subtab'])) : '';
	?>
	<div class="aiowps-postbox-container">
		<div class="aiowps-rules">
			<h3 class="hndle"><?php esc_html_e('Rules', 'all-in-one-wp-security-and-firewall'); ?></h3>
			<div id="aiowps-rule-search">
				<span class="dashicons dashicons-search"></span>
				<input type="text" placeholder="<?php esc_html_e('Search', 'all-in-one-wp-security-and-firewall'); ?>" class="aiowps-search">
				<span class="dashicons dashicons-no-alt clear-search"></span>
			</div>
			<ul class="aiowps-rule-list">
				<?php
				if (empty($templates)) return;
				$first_template = reset($templates);
				$first_title = $first_template['title'];
				
				foreach ($templates as $key => $template) {
					// Check if the current title is the first title
					$title = $template['title'];
					$class = ($key === $subtab || (empty($subtab) && $title === $first_title)) ? 'aiowps-active' : '';
					echo '<li data-template="'.esc_attr($key).'" class="'.esc_attr($class).'"><span class="aiowps-rule-title">'.esc_html($title).'</span></li>';
				}
				?>
			</ul>
		</div>
		<div class="aiowps-settings">
			<?php
			foreach ($templates as $key => $template) {
				$aio_wp_security->include_template('wp-admin/firewall/partials/' . esc_attr($key) . '.php', false, $php_firewall_data);
			}
			?>
		</div>
	</div>
	<div class="aiowps-actions">
		<input type="submit" name="aiowps_apply_php_firewall_settings" value="<?php esc_html_e('Save PHP firewall settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
	</div>
</form>
