<?php
// @codingStandardsIgnoreStart
/*
Plugin Name: All-In-One Security (AIOS) – Security and Firewall
Version: 5.5.1
Plugin URI: https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/
Update URI: https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/
Author: TeamUpdraft, DavidAnderson
Author URI: https://teamupdraft.com/all-in-one-security/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=plugin-author-info&utm_creative_format=text
Description: Trusted by over 1 million sites, AIOS secures WordPress with a firewall, login protection, two-factor authentication and more.
Text Domain: all-in-one-wp-security-and-firewall
Domain Path: /languages
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
Requires at least: 5.3
Requires PHP: 7.4
Network: true
*/
// @codingStandardsIgnoreEnd

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $wp_version;

if (version_compare($wp_version, '5.3', '<')) {
	add_action('all_admin_notices', 'aiowps_wp_version_notice');
	return;
}

/**
 * The notice to display if the user does not have the required Wordpress version
 *
 * @return void
 */
function aiowps_wp_version_notice() {
	global $wp_version;

	if (!current_user_can('manage_options')) {
		return;
	}
	
	unset($_GET['activate']);

	?>
	<div class="notice notice-error is-dismissible">
		<p><strong><?php esc_html_e('All-In-One Security', 'all-in-one-wp-security-and-firewall'); ?></strong></p>
		<p><?php esc_html_e('All-In-One Security plugin has been deactivated.', 'all-in-one-wp-security-and-firewall');?></p>
		<?php /* translators: %s: Required WordPress version */ ?>
		<p><?php printf(esc_html__('This plugin requires WordPress version %s.', 'all-in-one-wp-security-and-firewall'), '<strong>5.3.0+</strong>'); ?></p>
		<?php /* translators: %s: WordPress version */ ?>
		<p><?php printf(esc_html__('Your current WordPress version is %s.', 'all-in-one-wp-security-and-firewall'), '<strong>'.esc_html($wp_version).'</strong>'); ?></p>
		<p><?php esc_html_e('Please update your WordPress version to meet the minimum requirements.', 'all-in-one-wp-security-and-firewall'); ?></p>
	</div>
	<?php
	aiowps_maybe_network_deactivate();
}

if (version_compare(phpversion(), '7.4.0', '<')) {
	add_action('all_admin_notices', 'aiowps_php_version_notice');
	return;
}

/**
 * The notice to display if the user does not have the required PHP version
 *
 * @return void
 */
function aiowps_php_version_notice() {

	if (!current_user_can('manage_options')) {
		return;
	}
	?>
	<div class="notice notice-error is-dismissible">
		<p><strong><?php esc_html_e('All-In-One Security', 'all-in-one-wp-security-and-firewall'); ?></strong></p>
		<p><?php esc_html_e('All-In-One Security plugin has been deactivated.', 'all-in-one-wp-security-and-firewall');?></p>
		<?php /* translators: %s: Required PHP version */ ?>
		<p><?php printf(esc_html__('This plugin requires PHP version %s.', 'all-in-one-wp-security-and-firewall'), '<strong>7.4+</strong>'); ?></p>
		<?php /* translators: %s: PHP version */ ?>
		<p><?php printf(esc_html__('Your current PHP version is %s.', 'all-in-one-wp-security-and-firewall'), '<strong>'.esc_html(phpversion()).'</strong>'); ?></p>
		<p><?php esc_html_e('Contact your web hosting provider to upgrade the PHP version.', 'all-in-one-wp-security-and-firewall'); ?></p>
	</div>
	<?php
	aiowps_maybe_network_deactivate();
}

/**
 * Deactivate the plugin and deactivate the plugin for multisite, if applicable
 *
 * @return void
 */
function aiowps_maybe_network_deactivate() {
	$plugin = plugin_basename(__FILE__);

	if (is_multisite() && is_plugin_active_for_network($plugin)) {
		deactivate_plugins($plugin, true, true);
	} else {
		deactivate_plugins($plugin, true, false);
	}
}

require_once(__DIR__.'/wp-security-core.php');

register_activation_hook(__FILE__, array('AIO_WP_Security', 'activate_handler'));//activation hook
register_deactivation_hook(__FILE__, array('AIO_WP_Security', 'deactivation_handler'));// deactivation hook
register_uninstall_hook(__FILE__, array('AIO_WP_Security', 'uninstall_handler'));// uninstallation hook

function aiowps_show_plugin_settings_link($links, $file) {
	if (plugin_basename(__FILE__) == $file) {
		$settings_link = '<a href="admin.php?page=aiowpsec_settings">' . __('Settings', 'all-in-one-wp-security-and-firewall') . '</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links', 'aiowps_show_plugin_settings_link', 10, 2);

function aiowps_ms_handle_new_site($new_site) {
	global $wpdb;
	$plugin_basename = plugin_basename(__FILE__);
	if (is_plugin_active_for_network($plugin_basename)) {
		if (!class_exists('AIOWPSecurity_Installer')) {
			include_once('classes/wp-security-installer.php');
		}
		$old_blog = $wpdb->blogid;
		switch_to_blog($new_site->blog_id);
		AIOWPSecurity_Installer::create_db_tables();
		switch_to_blog($old_blog);
	}


}
// The priority is 20 instead of 10 because all subsite's tables are created by `add_action( 'wp_initialize_site', 'wp_initialize_site', 10, 2 );`. We should call  the `aiowps_ms_handle_new_site` function after all subsite's tables are created because the  `aiowps_ms_handle_new_site` function adds option in subsite's table.
add_action('wp_initialize_site', 'aiowps_ms_handle_new_site', 20, 1);
