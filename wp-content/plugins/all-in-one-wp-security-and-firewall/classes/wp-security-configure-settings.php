<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

use AIOWPS\Firewall\Allow_List;

class AIOWPSecurity_Configure_Settings {

	/**
	 * Set default settings.
	 *
	 * @return boolean True if the settings options was updated, false otherwise.
	 */
	public static function set_default_settings() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);

		$blog_email_address = get_bloginfo('admin_email'); // Get the blog admin email address - we will use as the default value

		//Debug
		$aio_wp_security->configs->set_value('aiowps_enable_debug', '');//Checkbox

		//PHP backtrace
		$aio_wp_security->configs->set_value('aiowps_enable_php_backtrace_in_email', '');//Checkbox

		//WP Generator Meta Tag feature
		$aio_wp_security->configs->set_value('aiowps_remove_wp_generator_meta_info', '');//Checkbox

		//Prevent Image Hotlinks
		$aio_wp_security->configs->set_value('aiowps_prevent_hotlinking', '');//Checkbox
		//General Settings Page

		//User password feature
		
		//Lockout feature
		$aio_wp_security->configs->set_value('aiowps_enable_login_lockdown', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_allow_unlock_requests', '1'); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_max_login_attempts', '3');
		$aio_wp_security->configs->set_value('aiowps_retry_time_period', '5');
		$aio_wp_security->configs->set_value('aiowps_lockout_time_length', '5');
		$aio_wp_security->configs->set_value('aiowps_max_lockout_time_length', '60');
		$aio_wp_security->configs->set_value('aiowps_set_generic_login_msg', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_email_notify', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_email_address', $blog_email_address);//text field
		$aio_wp_security->configs->set_value('aiowps_enable_forced_logout', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_logout_time_period', '60');
		$aio_wp_security->configs->set_value('aiowps_enable_invalid_username_lockdown', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_instantly_lockout_specific_usernames', array()); // Textarea (list of strings)
		$aio_wp_security->configs->set_value('aiowps_unlock_request_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20));//Hidden secret value which will be used to do some unlock request processing. This will be assigned a random string generated when lockdown settings saved
		$aio_wp_security->configs->set_value('aiowps_lockdown_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_lockdown_allowed_ip_addresses', '');

		// HTTP authentication
		$aio_wp_security->configs->set_value('aiowps_http_authentication_admin', ''); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_http_authentication_frontend', ''); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_http_authentication_username', 'root');
		$aio_wp_security->configs->set_value('aiowps_http_authentication_password', wp_hash_password('aios_http_password!'));
		$aio_wp_security->configs->set_value('aiowps_converted_http_authentication_password', '1');
		$aio_wp_security->configs->set_value('aiowps_http_authentication_failure_message', '<h1>Unauthorized</h1>');

		// CAPTCHA feature
		$aio_wp_security->configs->set_value('aiowps_default_captcha', '');
		$aio_wp_security->configs->set_value('aiowps_enable_login_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_custom_login_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_password_protected_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_woo_login_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_woo_lostpassword_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_woo_register_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_woo_checkout_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_lost_password_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_contact_form_7_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_captcha_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20)); // Hidden secret value which will be used to do some CAPTCHA processing. This will be assigned a random string generated when CAPTCHA settings saved

		//Login Whitelist feature
		$aio_wp_security->configs->set_value('aiowps_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_allowed_ip_addresses', '');

		//User registration
		$aio_wp_security->configs->set_value('aiowps_enable_manual_registration_approval', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_registration_page_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_registration_honeypot', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enforce_strong_password', '');//Checkbox

		//DB Security feature
		//$aio_wp_security->configs->set_value('aiowps_new_manual_db_pefix', ''); //text field
		$aio_wp_security->configs->set_value('aiowps_enable_random_prefix', '');//Checkbox

		//Filesystem Security feature
		AIOWPSecurity_Utility::enable_file_edits();
		$aio_wp_security->configs->set_value('aiowps_disable_file_editing', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_prevent_default_wp_file_access', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_auto_delete_default_wp_files', ''); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_system_log_file', 'error_log');

		//Blacklist feature
		$aio_wp_security->configs->set_value('aiowps_enable_blacklisting', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_banned_ip_addresses', '');
		$aio_wp_security->configs->set_value('aiowps_banned_user_agents', '');

		//Firewall features
		$aio_wp_security->configs->set_value('aiowps_enable_basic_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_max_file_upload_size', AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB); //Default
		$aio_wp_security->configs->set_value('aiowps_disable_xmlrpc_pingback_methods', '');//Checkbox - Disables only pingback methods in XMLRPC functionality
		$aio_wp_security->configs->set_value('aiowps_disable_rss_and_atom_feeds', ''); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_block_debug_log_file_access', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_index_views', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_trace_and_track', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_5g_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_6g_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_custom_rules', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_place_custom_rules_at_top', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_custom_rules', '');

		// Upgrade unsafe HTTP calls
		$aio_wp_security->configs->set_value('aiowps_upgrade_unsafe_http_calls', ''); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_upgrade_unsafe_http_calls_url_exceptions', '');

		//404 detection
		$aio_wp_security->configs->set_value('aiowps_enable_404_logging', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_404_IP_lockout', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_404_lockout_time_length', '60');
		$aio_wp_security->configs->set_value('aiowps_404_lock_redirect_url', 'http://127.0.0.1');

		//Brute Force features
		$aio_wp_security->configs->set_value('aiowps_enable_rename_login_page', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_rename_login_redirect', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_rename_login_redirect_slug', '');
		$aio_wp_security->configs->set_value('aiowps_enable_login_honeypot', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_application_password', '');//Checkbox

		$aio_wp_security->configs->set_value('aiowps_enable_brute_force_attack_prevention', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_brute_force_secret_word', '');
		$aio_wp_security->configs->set_value('aiowps_cookie_brute_test', '');
		$aio_wp_security->configs->set_value('aiowps_cookie_based_brute_force_redirect_url', 'http://127.0.0.1');
		$aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_pw_protected_exception', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_ajax_exception', '');//Checkbox

		//Maintenance menu - Visitor lockout feature
		$aio_wp_security->configs->set_value('aiowps_site_lockout', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_site_lockout_msg', '');//Text area/msg box

		// Spam prevention menu
		$aio_wp_security->configs->set_value('aiowps_enable_comment_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_autoblock_spam_ip', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_spam_ip_min_comments_block', '');
		$aio_wp_security->configs->set_value('aiowps_enable_bp_register_captcha', '');
		$aio_wp_security->configs->set_value('aiowps_enable_bbp_new_topic_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_spambot_detecting', '');
		$aio_wp_security->configs->set_value('aiowps_spambot_detect_usecookies', '');
		$aio_wp_security->configs->set_value('aiowps_spam_comments_should', '');
		
		$aio_wp_security->configs->set_value('aiowps_enable_trash_spam_comments', '');
		$aio_wp_security->configs->set_value('aiowps_trash_spam_comments_after_days', '14');

		//Filescan features
		//File change detection feature
		$aio_wp_security->configs->set_value('aiowps_enable_automated_fcd_scan', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_fcd_scan_frequency', '4');
		$aio_wp_security->configs->set_value('aiowps_fcd_scan_interval', '2'); //Dropdown box where (0,1,2) => (hours,days,weeks)
		$aio_wp_security->configs->set_value('aiowps_fcd_exclude_filetypes', '');
		$aio_wp_security->configs->set_value('aiowps_fcd_exclude_files', '');
		$aio_wp_security->configs->set_value('aiowps_send_fcd_scan_email', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_fcd_scan_email_address', $blog_email_address);
		$aio_wp_security->configs->set_value('aiowps_fcds_change_detected', false); //used to display a global alert on site when file change detected

		//Misc Options
		//Copy protection feature
		$aio_wp_security->configs->set_value('aiowps_copy_protection', '');//Checkbox
		//Prevent others from displaying your site in iframe
		$aio_wp_security->configs->set_value('aiowps_prevent_site_display_inside_frame', '');//Checkbox
		//Prevent users enumeration
		$aio_wp_security->configs->set_value('aiowps_prevent_users_enumeration', '');//Checkbox

		//REST API Security
		$aio_wp_security->configs->set_value('aiowps_disallow_unauthorized_rest_requests', '');//Checkbox
		$aio_wp_security->configs->set_value('aios_roles_disallowed_rest_requests', array());
		$aio_wp_security->configs->set_value('aios_whitelisted_rest_routes', array());

		// IP retrieval setting
		$aio_wp_security->configs->set_value('aiowps_ip_retrieve_method', '0'); // Default is $_SERVER['REMOTE_ADDR']

		// Cloudflare Turnstile
		$aio_wp_security->configs->set_value('aiowps_turnstile_site_key', '');
		$aio_wp_security->configs->set_value('aiowps_turnstile_secret_key', '');

		// Google reCAPTCHA
		$aio_wp_security->configs->set_value('aiowps_recaptcha_site_key', '');
		$aio_wp_security->configs->set_value('aiowps_recaptcha_secret_key', '');
		$aio_wp_security->configs->set_value('aiowps_default_recaptcha', ''); // Not used since 5.1.2

		// Deactivation Handler
		$aio_wp_security->configs->set_value('aiowps_on_uninstall_delete_db_tables', '1'); //Checkbox
		$aio_wp_security->configs->set_value('aiowps_on_uninstall_delete_configs', '1'); //Checkbox

		// Reset the PHP end of support notice
		$aio_wp_security->configs->delete_value('php_73_eol_dismiss_forever');

		//TODO - keep adding default options for any fields that require it

		if (is_main_site()) {
			$aiowps_firewall_config->set_value('aiowps_enable_blacklisting', ''); // Checkbox
			$aiowps_firewall_config->set_value('aiowps_enable_pingback_firewall', false);//Checkbox - blocks all access to XMLRPC
			$aiowps_firewall_config->set_value('aiowps_forbid_proxy_comments', false);//Checkbox
			$aiowps_firewall_config->set_value('aiowps_deny_bad_query_strings', false);//Checkbox
			$aiowps_firewall_config->set_value('aiowps_advanced_char_string_filter', false);//Checkbox
			$aiowps_firewall_config->set_value('aiowps_ban_post_blank_headers', false); // Checkbox
			$aiowps_firewall_config->set_value('aiowps_block_fake_googlebots', false); // Checkbox
			$aiowps_firewall_config->set_value('aiowps_googlebot_ip_ranges', array());

			self::turn_off_all_6g_firewall_configs();
			self::set_cookie_based_bruteforce_firewall_configs();
			self::set_user_agent_firewall_configs();
			self::set_ip_retrieve_method_configs();
			self::set_blacklist_ip_firewall_configs();
		}

		// Save it
		return $aio_wp_security->configs->save_config();
	}

	/**
	 * Add config settings.
	 *
	 * @param boolean $is_activating - Plugin activation add_option_values called
	 *
	 * @return Void
	 */
	public static function add_option_values($is_activating = false) {

		global $aio_wp_security;

		$blog_email_address = get_bloginfo('admin_email'); //Get the blog admin email address - we will use as the default value

		$aio_wp_security->configs->load_config();

		//Debug
		$aio_wp_security->configs->add_value('aiowps_enable_debug', '');//Checkbox

		//PHP backtrace
		$aio_wp_security->configs->add_value('aiowps_enable_php_backtrace_in_email', '');//Checkbox

		//WP Generator Meta Tag feature
		$aio_wp_security->configs->add_value('aiowps_remove_wp_generator_meta_info', '');//Checkbox

		//Prevent Image Hotlinks
		$aio_wp_security->configs->add_value('aiowps_prevent_hotlinking', '');//Checkbox

		//General Settings Page

		//User password feature
		
		//Lockout feature
		$aio_wp_security->configs->add_value('aiowps_enable_login_lockdown', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_allow_unlock_requests', '1'); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_max_login_attempts', '3');
		$aio_wp_security->configs->add_value('aiowps_retry_time_period', '5');
		$aio_wp_security->configs->add_value('aiowps_lockout_time_length', '5');
		$aio_wp_security->configs->add_value('aiowps_max_lockout_time_length', '60');
		$aio_wp_security->configs->add_value('aiowps_set_generic_login_msg', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_email_notify', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_email_address', $blog_email_address);//text field
		$aio_wp_security->configs->add_value('aiowps_enable_forced_logout', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_logout_time_period', '60');
		$aio_wp_security->configs->add_value('aiowps_enable_invalid_username_lockdown', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_instantly_lockout_specific_usernames', array()); // Textarea (list of strings)
		$aio_wp_security->configs->add_value('aiowps_unlock_request_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20));//Hidden secret value which will be used to do some unlock request processing. This will be assigned a random string generated when lockdown settings saved
		$aio_wp_security->configs->add_value('aiowps_lockdown_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_lockdown_allowed_ip_addresses', '');

		// HTTP authentication
		$aio_wp_security->configs->add_value('aiowps_http_authentication_admin', ''); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_http_authentication_frontend', ''); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_http_authentication_username', 'root');
		$aio_wp_security->configs->add_value('aiowps_http_authentication_password', wp_hash_password('aios_http_password!'));
		$aio_wp_security->configs->add_value('aiowps_converted_http_authentication_password', '1');
		$aio_wp_security->configs->add_value('aiowps_http_authentication_failure_message', '<h1>Unauthorized</h1>');

		//Login Whitelist feature
		$aio_wp_security->configs->add_value('aiowps_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_allowed_ip_addresses', '');

		// CAPTCHA feature
		$aio_wp_security->configs->add_value('aiowps_default_captcha', '');
		$aio_wp_security->configs->add_value('aiowps_enable_login_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_custom_login_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_password_protected_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_woo_login_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_woo_register_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_woo_checkout_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_woo_lostpassword_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_contact_form_7_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_captcha_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20)); // Hidden secret value which will be used to do some CAPTCHA processing. This will be assigned a random string generated when CAPTCHA settings saved

		//User registration
		$aio_wp_security->configs->add_value('aiowps_enable_manual_registration_approval', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_registration_page_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_registration_honeypot', ''); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_enforce_strong_password', ''); // Checkbox

		//DB Security feature
		//$aio_wp_security->configs->add_value('aiowps_new_manual_db_pefix', ''); //text field
		$aio_wp_security->configs->add_value('aiowps_enable_random_prefix', '');//Checkbox

		//Filesystem Security feature
		$aio_wp_security->configs->add_value('aiowps_disable_file_editing', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_prevent_default_wp_file_access', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_auto_delete_default_wp_files', ''); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_system_log_file', 'error_log');

		//Blacklist feature
		$aio_wp_security->configs->add_value('aiowps_enable_blacklisting', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_banned_ip_addresses', '');

		//Firewall features
		$aio_wp_security->configs->add_value('aiowps_enable_basic_firewall', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_max_file_upload_size', AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB);
		$aio_wp_security->configs->add_value('aiowps_disable_xmlrpc_pingback_methods', '');//Checkbox - Disables only pingback methods in XMLRPC functionality
		$aio_wp_security->configs->add_value('aiowps_disable_rss_and_atom_feeds', ''); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_block_debug_log_file_access', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_disable_index_views', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_disable_trace_and_track', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_5g_firewall', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_6g_firewall', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_custom_rules', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_place_custom_rules_at_top', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_custom_rules', '');

		// Upgrade unsafe HTTP calls
		$aio_wp_security->configs->add_value('aiowps_upgrade_unsafe_http_calls', ''); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_upgrade_unsafe_http_calls_url_exceptions', '');

		//404 detection
		$aio_wp_security->configs->add_value('aiowps_enable_404_logging', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_404_IP_lockout', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_404_lockout_time_length', '60');
		$aio_wp_security->configs->add_value('aiowps_404_lock_redirect_url', 'http://127.0.0.1');

		//Brute Force features
		$aio_wp_security->configs->add_value('aiowps_enable_rename_login_page', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_rename_login_redirect', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_rename_login_redirect_slug', '');
		$aio_wp_security->configs->add_value('aiowps_enable_login_honeypot', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_disable_application_password', ''); // Checkbox

		$aio_wp_security->configs->add_value('aiowps_enable_brute_force_attack_prevention', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_brute_force_secret_word', '');
		$aio_wp_security->configs->add_value('aiowps_cookie_brute_test', '');
		$aio_wp_security->configs->add_value('aiowps_cookie_based_brute_force_redirect_url', 'http://127.0.0.1');
		$aio_wp_security->configs->add_value('aiowps_brute_force_attack_prevention_pw_protected_exception', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_brute_force_attack_prevention_ajax_exception', '');//Checkbox

		//Maintenance menu - Visitor lockout feature
		$aio_wp_security->configs->add_value('aiowps_site_lockout', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_site_lockout_msg', '');//Text area/msg box

		// Spam prevention menu
		$aio_wp_security->configs->add_value('aiowps_enable_spambot_blocking', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_comment_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_spam_ip_min_comments_block', '');
		$aio_wp_security->configs->add_value('aiowps_enable_bp_register_captcha', '');
		$aio_wp_security->configs->add_value('aiowps_enable_bbp_new_topic_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_spambot_detecting', '');
		$aio_wp_security->configs->add_value('aiowps_spambot_detect_usecookies', '');
		$aio_wp_security->configs->add_value('aiowps_spam_comments_should', '');
		$aio_wp_security->configs->add_value('aiowps_enable_trash_spam_comments', '');
		$aio_wp_security->configs->add_value('aiowps_trash_spam_comments_after_days', '14');

		//Filescan features
		//File change detection feature
		$aio_wp_security->configs->add_value('aiowps_enable_automated_fcd_scan', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_fcd_scan_frequency', '4');
		$aio_wp_security->configs->add_value('aiowps_fcd_scan_interval', '2'); //Dropdown box where (0,1,2) => (hours,days,weeks)
		$aio_wp_security->configs->add_value('aiowps_fcd_exclude_filetypes', '');
		$aio_wp_security->configs->add_value('aiowps_fcd_exclude_files', '');
		$aio_wp_security->configs->add_value('aiowps_send_fcd_scan_email', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_fcd_scan_email_address', $blog_email_address);
		$aio_wp_security->configs->add_value('aiowps_fcds_change_detected', false); //used to display a global alert on site when file change detected

		//Misc Options
		//Copy protection feature
		$aio_wp_security->configs->add_value('aiowps_copy_protection', '');//Checkbox
		//Prevent others from displaying your site in iframe
		$aio_wp_security->configs->add_value('aiowps_prevent_site_display_inside_frame', '');//Checkbox
		//Prevent users enumeration
		$aio_wp_security->configs->add_value('aiowps_prevent_users_enumeration', '');//Checkbox

		//REST API Security
		$aio_wp_security->configs->add_value('aiowps_disallow_unauthorized_rest_requests', '');//Checkbox
		$aio_wp_security->configs->add_value('aios_roles_disallowed_rest_requests', array());
		$aio_wp_security->configs->add_value('aios_whitelisted_rest_routes', array());

		// IP retrieval setting
		// Commented the below code line because the IP retrieve method will be configured when the AIOS plugin is activated for the first time.
		// $aio_wp_security->configs->add_value('aiowps_ip_retrieve_method', '0'); // Default is $_SERVER['REMOTE_ADDR']

		// Cloudflare Turnstile
		$aio_wp_security->configs->add_value('aiowps_turnstile_site_key', '');
		$aio_wp_security->configs->add_value('aiowps_turnstile_secret_key', '');

		// Google reCAPTCHA
		$aio_wp_security->configs->add_value('aiowps_recaptcha_site_key', '');
		$aio_wp_security->configs->add_value('aiowps_recaptcha_secret_key', '');
		$aio_wp_security->configs->add_value('aiowps_default_recaptcha', ''); // Not used since 5.1.2

		// Deactivation Handler
		$aio_wp_security->configs->add_value('aiowps_on_uninstall_delete_db_tables', '1'); //Checkbox
		$aio_wp_security->configs->add_value('aiowps_on_uninstall_delete_configs', '1'); //Checkbox

		$aio_wp_security->configs->add_value('installed-at', current_time('timestamp', true));

		//TODO - keep adding default options for any fields that require it

		//Save it
		$aio_wp_security->configs->save_config();

		// For Cookie based brute force prevention backward compatibility
		if (!headers_sent() && '1' == $aio_wp_security->configs->get_value('aiowps_enable_brute_force_attack_prevention')) {
			$brute_force_secret_word = $aio_wp_security->configs->get_value('aiowps_brute_force_secret_word');
			if (empty($brute_force_secret_word)) {
				$brute_force_secret_word = AIOS_DEFAULT_BRUTE_FORCE_FEATURE_SECRET_WORD;
			}
			AIOWPSecurity_Utility::set_cookie_value(AIOWPSecurity_Utility::get_brute_force_secret_cookie_name(), AIOS_Helper::get_hash($brute_force_secret_word));
		}
		
		// Login whitelisting started to work on non-apache server from db_version 1.9.5
		if (is_main_site() && version_compare(get_option('aiowpsec_db_version'), '1.9.6', '<') && '1' == $aio_wp_security->configs->get_value('aiowps_enable_whitelisting') && !empty($aio_wp_security->configs->get_value('aiowps_allowed_ip_addresses'))) {
			$aio_wp_security->configs->set_value('aiowps_enable_whitelisting', '0');
			$aio_wp_security->configs->set_value('aiowps_is_login_whitelist_disabled_on_upgrade', '1');
			$aio_wp_security->configs->save_config();
		}
		
		if (is_main_site() && version_compare(get_option('aiowpsec_db_version'), '2.0.0', '<') && '1' == $aio_wp_security->configs->get_value('aiowps_enable_blacklisting') && !empty($aio_wp_security->configs->get_value('aiowps_banned_ip_addresses')) && (false !== strpos($aio_wp_security->configs->get_value('aiowps_banned_ip_addresses'), '*') || false !== strpos($aio_wp_security->configs->get_value('aiowps_banned_ip_addresses'), '/'))) {
			$aio_wp_security->configs->set_value('aiowps_enable_blacklisting', '0');
			$aio_wp_security->configs->set_value('aiowps_is_ip_blacklist_settings_notice_on_upgrade', '1');
			$aio_wp_security->configs->save_config();
			self::set_user_agent_firewall_configs();
			self::send_blacklist_manager_disabled_email();
		}
		
		if (is_main_site() && version_compare(get_option('aiowpsec_db_version'), '2.0.0', '<') && '1' == $aio_wp_security->configs->get_value('aiowps_enable_spambot_blocking')) {
			$aio_wp_security->configs->set_value('aiowps_enable_spambot_detecting', '1');
			$aio_wp_security->configs->set_value('aiowps_spambot_detect_usecookies', '');
			$aio_wp_security->configs->set_value('aiowps_spam_comments_should', '0');
			$aio_wp_security->configs->save_config();
		}


		if (is_main_site() && version_compare(get_option('aiowpsec_db_version'), '2.0.3', '<')) {
			$aio_wp_security->configs->set_value('aiowps_enable_pingback_firewall', '0');//Checkbox - blocks all access to XMLRPC
			$aio_wp_security->configs->set_value('aiowps_forbid_proxy_comments', '0');//Checkbox
			$aio_wp_security->configs->set_value('aiowps_deny_bad_query_strings', '0');//Checkbox
			$aio_wp_security->configs->set_value('aiowps_advanced_char_string_filter', '0');//Checkbox
			$aio_wp_security->configs->save_config();
		}

		if (is_main_site() && $is_activating) {
			AIOWPSecurity_Utility_Htaccess::write_to_htaccess(false);
		}

		// Add expiration for antibot keys for previous versions
		if (version_compare(get_option('aiowpsec_db_version'), '2.1.1', '<')) {
			AIOWPSecurity_Comment::generate_antibot_keys(true);
		}
		
		// Add ContactForm7 related authentication scheme for salt postfix
		if (version_compare(get_option('aiowpsec_db_version'), '2.1.4', '<') && '1' == $aio_wp_security->configs->get_value('aiowps_enable_salt_postfix')) {
			$salt_postfixes = $aio_wp_security->configs->get_value('aiowps_salt_postfixes');
			$salt_postfixes['wpcf7_submission'] = wp_generate_password(64, true, true);
			$aio_wp_security->configs->set_value('aiowps_salt_postfixes', $salt_postfixes, true);
		}
	}

	/**
	 * Method to update the plugin db version.
	 *
	 * @return void
	 */
	public static function update_aiowpsec_db_version() {
		update_option('aiowpsec_db_version', AIO_WP_SECURITY_DB_VERSION);
	}

	/**
	 * Upgrades from the old config to the firewall's config
	 *
	 * @return void
	 */
	public static function upgrade_basic_firewall_rules_configs() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);

		$settings = array(
			'aiowps_enable_pingback_firewall',
			'aiowps_forbid_proxy_comments',
			'aiowps_deny_bad_query_strings',
			'aiowps_advanced_char_string_filter',
		);

		// The settings that have been activated by the user
		$active = array();

		foreach ($settings as $setting) {
			if (('1' === $aio_wp_security->configs->get_value($setting))) {
				$active[] = $setting;
				$aiowps_firewall_config->set_value($setting, false);
				$aio_wp_security->configs->delete_value($setting);
				$aio_wp_security->configs->save_config();
			}

		}

		if (!empty($active)) {
			$aio_wp_security->configs->set_value('aiowps_firewall_active_upgrade', wp_json_encode($active));
			$aio_wp_security->configs->save_config();
			self::send_basic_firewall_upgrade_email();
		}
	}

	/**
	 * Send an email notifying that the upgraded settings have been disabled
	 *
	 * @return void
	 */
	private static function send_basic_firewall_upgrade_email() {
		global $aio_wp_security;
		$dashboard_link = 'admin.php?page=aiowpsec';
		$dashboard_link = is_multisite() ? network_admin_url($dashboard_link) : admin_url($dashboard_link);
		$subject = __('Basic firewall settings disabled', 'all-in-one-wp-security-and-firewall');
		/* translators: %s: Dashboard link. */
		$email_msg = __('Our basic firewall rules have been upgraded and to prevent any unexpected site issues we have disabled the features.', 'all-in-one-wp-security-and-firewall') . "\n\n" . __('You can enable the features again by logging into your WordPress dashboard.', 'all-in-one-wp-security-and-firewall') . "\n\n" .sprintf(__('Go to dashboard: %s', 'all-in-one-wp-security-and-firewall'), $dashboard_link) . "\n\n" . __('Once logged in you will see a notification where you can decide on which course of action you wish to take.', 'all-in-one-wp-security-and-firewall') . "\n";
		$email = get_bloginfo('admin_email');
		$mail_data = array(
			'to' => $email,
			'subject' => $subject,
			'message' => $email_msg
		);

		$mail_sent = AIOWPSecurity_Reporting::notification($mail_data);

		if (false === $mail_sent) {
			$aio_wp_security->debug_logger->log_debug("Basic firewall rules notification email failed to send to " . $email, 4);
		}
	}
	
	/**
	 * This function send blacklist ip manager disabled email.
	 *
	 * @return void
	 */
	public static function send_blacklist_manager_disabled_email() {
		global $aio_wp_security;
		$dashboard_link = 'admin.php?page=aiowpsec';
		$dashboard_link = is_multisite() ? network_admin_url($dashboard_link) : admin_url($dashboard_link);
		$subject = '['. get_option('siteurl'). '] '. __('Blacklist manager disabled notification', 'all-in-one-wp-security-and-firewall');
		/* translators: %s: Dashboard link */
		$email_msg = __('The blacklist manager feature has been updated and to prevent any unexpected site lockouts we have disabled the feature.', 'all-in-one-wp-security-and-firewall') . "\n\n" . __('You can enable the feature again by logging into your WordPress dashboard.', 'all-in-one-wp-security-and-firewall') . "\n\n" .sprintf(__('Go to dashboard: %s', 'all-in-one-wp-security-and-firewall'), $dashboard_link) . "\n\n" . __('Once logged in before turning the blacklist manger on please double check your settings to ensure you have not entered your own details.', 'all-in-one-wp-security-and-firewall') . "\n";
		$email = get_bloginfo('admin_email');
		$mail_data = array(
			'to' => $email,
			'subject' => $subject,
			'message' => $email_msg
		);

		$mail_sent = AIOWPSecurity_Reporting::notification($mail_data);

		if (false === $mail_sent) {
			$aio_wp_security->debug_logger->log_debug("Blacklist IP manager disabled notification email failed to send to " . $email, 4);
		}
	}
	
	/**
	 * Firewall configs set based on version.
	 *
	 * @return void
	 */
	public static function set_firewall_configs() {
		if (is_main_site()) {
			$firewall_version = get_option('aiowpsec_firewall_version');
			if (version_compare($firewall_version, '1.0.1', '<')) {
				self::set_cookie_based_bruteforce_firewall_configs();
			}
			if (version_compare($firewall_version, '1.0.3', '<')) {
				self::set_ip_retrieve_method_configs();
			}
			if (version_compare($firewall_version, '1.0.4', '<')) {
				self::set_blacklist_ip_firewall_configs();
			}
			if (version_compare($firewall_version, '1.0.5', '<')) {
				self::upgrade_basic_firewall_rules_configs();
			}
			if (version_compare($firewall_version, '1.0.6', '<')) { //1.0.2 set but here making sure the blank user agent is not saved in settings.php which  may show a 403 error due to not empty user agent check removed from the rule
				self::set_user_agent_firewall_configs();
			}
			if (version_compare($firewall_version, '1.0.8', '<')) {
				self::port_block_fake_googlebots_config();
			}
			if (version_compare($firewall_version, '1.0.9', '<')) {
				self::set_genuine_bots_config();
			}
			if (version_compare($firewall_version, '1.0.10', '<')) {
				self::set_blacklist_enabled_firewall_config();
			}
		}
		update_option('aiowpsec_firewall_version', AIO_WP_SECURITY_FIREWALL_VERSION);
	}

	/**
	 * Sets the Firewall config for whether the Blacklist is enabled based on the AIOWPSecurity_Config.
	 *
	 * @return void
	 */
	public static function set_blacklist_enabled_firewall_config() {
		global $aio_wp_security;

		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);

		$aiowps_firewall_config->set_value('aiowps_enable_blacklisting', $aio_wp_security->configs->get_value('aiowps_enable_blacklisting'));
	}

	/**
	 * Blacklist IP firewall configs set.
	 *
	 * @return void.
	 */
	public static function set_blacklist_ip_firewall_configs() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		$aiowps_firewall_config->set_value('aiowps_ip_retrieve_method', $aio_wp_security->configs->get_value('aiowps_ip_retrieve_method'));

		if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_blacklisting') && !empty($aio_wp_security->configs->get_value('aiowps_banned_ip_addresses'))) {
			$aiowps_firewall_config->set_value('aiowps_blacklist_ips', explode("\n", preg_replace("/\r/", "", trim($aio_wp_security->configs->get_value('aiowps_banned_ip_addresses')))));
		} else {
			$aiowps_firewall_config->set_value('aiowps_blacklist_ips', array());
		}
	}
	
	/**
	 * Cookie based bruteforce firewall configs set.
	 *
	 * @return void.
	 */
	public static function set_cookie_based_bruteforce_firewall_configs() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		
		$aiowps_firewall_config->set_value('aios_enable_rename_login_page', $aio_wp_security->configs->get_value('aiowps_enable_rename_login_page'));
		
		$aiowps_firewall_config->set_value('aios_login_page_slug', $aio_wp_security->configs->get_value('aiowps_login_page_slug'));
		
		$aios_enable_brute_force_attack_prevention = $aio_wp_security->configs->get_value('aiowps_enable_brute_force_attack_prevention');
		$aiowps_firewall_config->set_value('aios_enable_brute_force_attack_prevention', $aios_enable_brute_force_attack_prevention);
		
		$aiowps_firewall_config->set_value('aios_brute_force_secret_word', $aio_wp_security->configs->get_value('aiowps_brute_force_secret_word'));
		
		$aiowps_firewall_config->set_value('aios_cookie_based_brute_force_redirect_url', $aio_wp_security->configs->get_value('aiowps_cookie_based_brute_force_redirect_url'));
		
		$aiowps_firewall_config->set_value('aios_brute_force_attack_prevention_pw_protected_exception', $aio_wp_security->configs->get_value('aiowps_brute_force_attack_prevention_pw_protected_exception'));
		
		$aiowps_firewall_config->set_value('aios_brute_force_attack_prevention_ajax_exception', $aio_wp_security->configs->get_value('aiowps_brute_force_attack_prevention_ajax_exception'));
		
		$aiowps_firewall_config->set_value('aios_brute_force_secret_cookie_name', AIOWPSecurity_Utility::get_brute_force_secret_cookie_name());
	}
	
	/**
	 * User agent firewall configs set.
	 *
	 * @return void.
	 */
	public static function set_user_agent_firewall_configs() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_blacklisting') && !empty($aio_wp_security->configs->get_value('aiowps_banned_user_agents'))) {
			$aiowps_firewall_config->set_value('aiowps_blacklist_user_agents', explode("\n", preg_replace("/\r/", "", trim($aio_wp_security->configs->get_value('aiowps_banned_user_agents')))));
		} else {
			$aiowps_firewall_config->set_value('aiowps_blacklist_user_agents', array());
		}
	}

	/**
	 * Port block fake Googlebots config to firewall config.
	 *
	 * @global AIO_WP_Security $aio_wp_security
	 * @global AIOWPS\Firewall\Config $aiowps_firewall_config
	 *
	 * @return void
	 */
	private static function port_block_fake_googlebots_config() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);

		if ('1' == $aio_wp_security->configs->get_value('aiowps_block_fake_googlebots')) {
			$aiowps_firewall_config->set_value('aiowps_block_fake_googlebots', true);

			$validated_ip_list_array = AIOWPSecurity_Utility::get_googlebot_ip_ranges();

			if (!is_wp_error($validated_ip_list_array)) {
				$aiowps_firewall_config->set_value('aiowps_googlebot_ip_ranges', $validated_ip_list_array);
			}
		} else {
			$aiowps_firewall_config->set_value('aiowps_block_fake_googlebots', false);
		}
	}
	
	/**
	 * Set Genuine bots config to firewall config.
	 *
	 * @return void
	 */
	private static function set_genuine_bots_config() {
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		
		$validated_googlebot_ips = AIOWPSecurity_Utility::get_googlebot_ip_ranges();
		if (!is_wp_error($validated_googlebot_ips)) {
			$aiowps_firewall_config->set_value('aiowps_googlebot_ip_ranges', $validated_googlebot_ips);
		}
		
		$validated_bingbot_ips = AIOWPSecurity_Utility::get_bingbot_ip_ranges();
		if (!is_wp_error($validated_bingbot_ips)) {
			$aiowps_firewall_config->set_value('aiowps_bingbot_ip_ranges', $validated_bingbot_ips);
		}
	}

	/**
	 * IP retrieve method configs set.
	 *
	 * @return void.
	 */
	public static function set_ip_retrieve_method_configs() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		$aiowps_firewall_config->set_value('aios_ip_retrieve_method', $aio_wp_security->configs->get_value('aiowps_ip_retrieve_method'));
	}
	
	/**
	 * Turn off all security features.
	 *
	 * @return void.
	 */
	public static function turn_off_all_security_features() {
		global $aio_wp_security;
		AIOWPSecurity_Configure_Settings::set_default_settings();

		//Refresh the .htaccess file based on the new settings
		$serverType = AIOWPSecurity_Utility::get_server_type();
		if (!in_array($serverType, array('-1', 'nginx', 'iis'))) {
			$res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();
		} else {
			$res = true;
		}
		if (!$res) {
			$aio_wp_security->debug_logger->log_debug(__METHOD__ . " - Could not write to the .htaccess file. Check the file permissions.", 4);
		}
	}
	
	/**
	 * Turn off 6g firewall configs.
	 *
	 * @return void.
	 */
	public static function turn_off_all_6g_firewall_configs() {
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		$aiowps_firewall_config->set_value('aiowps_6g_block_request_methods', array());
		$aiowps_firewall_config->set_value('aiowps_6g_block_query', false);
		$aiowps_firewall_config->set_value('aiowps_6g_block_request', false);
		$aiowps_firewall_config->set_value('aiowps_6g_block_referrers', false);
		$aiowps_firewall_config->set_value('aiowps_6g_block_agents', false);
	}
	
	
	/**
	 * Turn off all firewall configs.
	 *
	 * @return void.
	 */
	public static function turn_off_firewall_configs() {
		global $aiowps_firewall_config, $aio_wp_security;
		$aio_wp_security->configs->set_value('aiowps_disable_xmlrpc_pingback_methods', '');
		$aio_wp_security->configs->set_value('aiowps_disable_rss_and_atom_feeds', '');
		$aio_wp_security->configs->set_value('aiowps_enable_basic_firewall', '');
		$aio_wp_security->configs->set_value('aiowps_max_file_upload_size', AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB); //Default
		$aio_wp_security->configs->set_value('aiowps_block_debug_log_file_access', '');
		$aio_wp_security->configs->set_value('aiowps_disable_index_views', '');
		$aio_wp_security->configs->set_value('aiowps_disable_trace_and_track', '');
		$aio_wp_security->configs->set_value('aiowps_enable_blacklisting', '');
		$aio_wp_security->configs->set_value('aiowps_enable_5g_firewall', '');
		$aio_wp_security->configs->set_value('aiowps_disallow_unauthorized_rest_requests', '');
		$aio_wp_security->configs->save_config();

		$aiowps_firewall_config->set_value('aiowps_enable_blacklisting', '');

		self::turn_off_all_6g_firewall_configs();
		$aiowps_firewall_config->set_value('aiowps_enable_pingback_firewall', false);
		$aiowps_firewall_config->set_value('aiowps_forbid_proxy_comments', false);
		$aiowps_firewall_config->set_value('aiowps_deny_bad_query_strings', false);
		$aiowps_firewall_config->set_value('aiowps_advanced_char_string_filter', false);
		$aiowps_firewall_config->set_value('aiowps_ban_post_blank_headers', false);
		$aiowps_firewall_config->set_value('aiowps_block_fake_googlebots', false);
		$aiowps_firewall_config->set_value('aiowps_googlebot_ip_ranges', array());
		$aiowps_firewall_config->set_value('aiowps_blacklist_user_agents', array());
		$aiowps_firewall_config->set_value('aiowps_blacklist_ips', array());
		Allow_List::add_ips(array()); // Remove firewall whitelisted IPs
	}

	/**
	 * Sanitize settings value when imported from file
	 *
	 * @param mixed $settings Imported Settings
	 *
	 * @return mixed $configs Sanitized settings
	 */
	public static function sanitize_setting_values($settings) {
		$configs = array();
		if (!empty($settings['aiowps_enable_debug'])) $configs['aiowps_enable_debug'] = sanitize_text_field($settings['aiowps_enable_debug']);
		if (!empty($settings['aiowps_enable_php_backtrace_in_email'])) $configs['aiowps_enable_php_backtrace_in_email'] = sanitize_text_field($settings['aiowps_enable_php_backtrace_in_email']);
		if (!empty($settings['aiowps_remove_wp_generator_meta_info'])) $configs['aiowps_remove_wp_generator_meta_info'] = sanitize_text_field($settings['aiowps_remove_wp_generator_meta_info']);
		if (!empty($settings['aiowps_prevent_hotlinking'])) $configs['aiowps_prevent_hotlinking'] = sanitize_text_field($settings['aiowps_prevent_hotlinking']);
		if (!empty($settings['aiowps_enable_login_lockdown'])) $configs['aiowps_enable_login_lockdown']  = sanitize_text_field($settings['aiowps_enable_login_lockdown']);
		if (!empty($settings['aiowps_allow_unlock_requests'])) $configs['aiowps_allow_unlock_requests'] = sanitize_text_field($settings['aiowps_allow_unlock_requests']);
		if (!empty($settings['aiowps_max_login_attempts'])) $configs['aiowps_max_login_attempts'] = sanitize_text_field($settings['aiowps_max_login_attempts']);
		if (!empty($settings['aiowps_retry_time_period'])) $configs['aiowps_retry_time_period'] = sanitize_text_field($settings['aiowps_retry_time_period']);
		if (!empty($settings['aiowps_lockout_time_length'])) $configs['aiowps_lockout_time_length'] = sanitize_text_field($settings['aiowps_lockout_time_length']);
		if (!empty($settings['aiowps_max_lockout_time_length'])) $configs['aiowps_max_lockout_time_length'] = sanitize_text_field($settings['aiowps_max_lockout_time_length']);
		if (!empty($settings['aiowps_set_generic_login_msg'])) $configs['aiowps_set_generic_login_msg'] = sanitize_textarea_field($settings['aiowps_set_generic_login_msg']);
		if (!empty($settings['aiowps_enable_email_notify'])) $configs['aiowps_enable_email_notify'] = sanitize_text_field($settings['aiowps_enable_email_notify']);
		if (!empty($settings['aiowps_email_address'])) $configs['aiowps_email_address'] = is_array($settings['aiowps_email_address']) ? array_map('sanitize_email', $settings['aiowps_email_address']) : sanitize_email($settings['aiowps_email_address']);
		if (!empty($settings['aiowps_enable_forced_logout'])) $configs['aiowps_enable_forced_logout'] = sanitize_text_field($settings['aiowps_enable_forced_logout']);
		if (!empty($settings['aiowps_logout_time_period'])) $configs['aiowps_logout_time_period'] = sanitize_text_field($settings['aiowps_logout_time_period']);
		if (!empty($settings['aiowps_enable_invalid_username_lockdown'])) $configs['aiowps_enable_invalid_username_lockdown'] = sanitize_text_field($settings['aiowps_enable_invalid_username_lockdown']);
		if (!empty($settings['aiowps_instantly_lockout_specific_usernames'])) $configs['aiowps_instantly_lockout_specific_usernames'] = array_map('sanitize_text_field', $settings['aiowps_instantly_lockout_specific_usernames']);
		if (!empty($settings['aiowps_unlock_request_secret_key'])) $configs['aiowps_unlock_request_secret_key'] = sanitize_text_field($settings['aiowps_unlock_request_secret_key']);
		if (!empty($settings['aiowps_lockdown_enable_whitelisting'])) $configs['aiowps_lockdown_enable_whitelisting'] = sanitize_text_field($settings['aiowps_lockdown_enable_whitelisting']);
		if (!empty($settings['aiowps_lockdown_allowed_ip_addresses'])) $configs['aiowps_lockdown_allowed_ip_addresses'] = sanitize_textarea_field($settings['aiowps_lockdown_allowed_ip_addresses']);
		if (!empty($settings['aiowps_http_authentication_admin'])) $configs['aiowps_http_authentication_admin'] = sanitize_text_field($settings['aiowps_http_authentication_admin']);
		if (!empty($settings['aiowps_http_authentication_frontend'])) $configs['aiowps_http_authentication_frontend'] = sanitize_text_field($settings['aiowps_http_authentication_frontend']);
		if (!empty($settings['aiowps_http_authentication_username'])) $configs['aiowps_http_authentication_username'] = sanitize_text_field($settings['aiowps_http_authentication_username']);
		if (!empty($settings['aiowps_http_authentication_password'])) $configs['aiowps_http_authentication_password'] = sanitize_text_field($settings['aiowps_http_authentication_password']);
		if (!empty($settings['aiowps_hibp_user_profile_update'])) $configs['aiowps_hibp_user_profile_update'] = sanitize_text_field($settings['aiowps_hibp_user_profile_update']);
		if (!empty($settings['aiowps_http_password_reset'])) $configs['aiowps_http_password_reset'] = sanitize_text_field($settings['aiowps_http_password_reset']);
		if (!empty($settings['aiowps_http_authentication_failure_message'])) $configs['aiowps_http_authentication_failure_message'] = wp_kses_post($settings['aiowps_http_authentication_failure_message']);
		if (!empty($settings['aiowps_default_captcha'])) $configs['aiowps_default_captcha'] = sanitize_text_field($settings['aiowps_default_captcha']);
		if (!empty($settings['aiowps_enable_login_captcha'])) $configs['aiowps_enable_login_captcha'] = sanitize_text_field($settings['aiowps_enable_login_captcha']);
		if (!empty($settings['aiowps_enable_custom_login_captcha'])) $configs['aiowps_enable_custom_login_captcha'] = sanitize_text_field($settings['aiowps_enable_custom_login_captcha']);
		if (!empty($settings['aiowps_enable_password_protected_captcha'])) $configs['aiowps_enable_password_protected_captcha'] = sanitize_text_field($settings['aiowps_enable_password_protected_captcha']);
		if (!empty($settings['aiowps_enable_woo_login_captcha'])) $configs['aiowps_enable_woo_login_captcha'] = sanitize_text_field($settings['aiowps_enable_woo_login_captcha']);
		if (!empty($settings['aiowps_enable_woo_lostpassword_captcha'])) $configs['aiowps_enable_woo_lostpassword_captcha'] = sanitize_text_field($settings['aiowps_enable_woo_lostpassword_captcha']);
		if (!empty($settings['aiowps_enable_woo_register_captcha'])) $configs['aiowps_enable_woo_register_captcha'] = sanitize_text_field($settings['aiowps_enable_woo_register_captcha']);
		if (!empty($settings['aiowps_enable_woo_checkout_captcha'])) $configs['aiowps_enable_woo_checkout_captcha'] = sanitize_text_field($settings['aiowps_enable_woo_checkout_captcha']);
		if (!empty($settings['aiowps_enable_lost_password_captcha'])) $configs['aiowps_enable_lost_password_captcha'] = sanitize_text_field($settings['aiowps_enable_lost_password_captcha']);
		if (!empty($settings['aiowps_enable_contact_form_7_captcha'])) $configs['aiowps_enable_contact_form_7_captcha'] = sanitize_text_field($settings['aiowps_enable_contact_form_7_captcha']);
		if (!empty($settings['aiowps_captcha_secret_key'])) $configs['aiowps_captcha_secret_key'] = sanitize_text_field($settings['aiowps_captcha_secret_key']);
		if (!empty($settings['aiowps_enable_whitelisting'])) $configs['aiowps_enable_whitelisting'] = sanitize_text_field($settings['aiowps_enable_whitelisting']);
		if (!empty($settings['aiowps_allowed_ip_addresses'])) $configs['aiowps_allowed_ip_addresses'] = sanitize_textarea_field($settings['aiowps_allowed_ip_addresses']);
		if (!empty($settings['aiowps_enable_manual_registration_approval'])) $configs['aiowps_enable_manual_registration_approval'] = sanitize_text_field($settings['aiowps_enable_manual_registration_approval']);
		if (!empty($settings['aiowps_enable_salt_postfix'])) $configs['aiowps_enable_salt_postfix'] = sanitize_text_field($settings['aiowps_enable_salt_postfix']);
		if (!empty($settings['aiowps_enable_registration_page_captcha'])) $configs['aiowps_enable_registration_page_captcha'] = sanitize_text_field($settings['aiowps_enable_registration_page_captcha']);
		if (!empty($settings['aiowps_enable_registration_honeypot'])) $configs['aiowps_enable_registration_honeypot'] = sanitize_text_field($settings['aiowps_enable_registration_honeypot']);
		if (!empty($settings['aiowps_enforce_strong_password'])) $configs['aiowps_enforce_strong_password'] = sanitize_text_field($settings['aiowps_enforce_strong_password']);
		if (!empty($settings['aiowps_enable_random_prefix'])) $configs['aiowps_enable_random_prefix'] = sanitize_text_field($settings['aiowps_enable_random_prefix']);
		if (!empty($settings['aiowps_disable_file_editing'])) $configs['aiowps_disable_file_editing'] = sanitize_text_field($settings['aiowps_disable_file_editing']);
		if (!empty($settings['aiowps_prevent_default_wp_file_access'])) $configs['aiowps_prevent_default_wp_file_access'] = sanitize_text_field($settings['aiowps_prevent_default_wp_file_access']);
		if (!empty($settings['aiowps_auto_delete_default_wp_files'])) $configs['aiowps_auto_delete_default_wp_files'] = sanitize_text_field($settings['aiowps_auto_delete_default_wp_files']);
		if (!empty($settings['aiowps_system_log_file'])) $configs['aiowps_system_log_file'] = sanitize_text_field($settings['aiowps_system_log_file']);
		if (!empty($settings['aiowps_enable_blacklisting'])) $configs['aiowps_enable_blacklisting'] = sanitize_text_field($settings['aiowps_enable_blacklisting']);
		if (!empty($settings['aiowps_banned_ip_addresses'])) $configs['aiowps_banned_ip_addresses'] = sanitize_textarea_field($settings['aiowps_banned_ip_addresses']);
		if (!empty($settings['aiowps_banned_user_agents'])) $configs['aiowps_banned_user_agents'] = sanitize_textarea_field($settings['aiowps_banned_user_agents']);
		if (!empty($settings['aiowps_enable_basic_firewall'])) $configs['aiowps_enable_basic_firewall'] = sanitize_text_field($settings['aiowps_enable_basic_firewall']);
		if (!empty($settings['aiowps_max_file_upload_size'])) $configs['aiowps_max_file_upload_size'] = sanitize_text_field($settings['aiowps_max_file_upload_size']);
		if (!empty($settings['aiowps_disable_xmlrpc_pingback_methods'])) $configs['aiowps_disable_xmlrpc_pingback_methods'] = sanitize_text_field($settings['aiowps_disable_xmlrpc_pingback_methods']);
		if (!empty($settings['aiowps_disable_rss_and_atom_feeds'])) $configs['aiowps_disable_rss_and_atom_feeds'] = sanitize_text_field($settings['aiowps_disable_rss_and_atom_feeds']);
		if (!empty($settings['aiowps_block_debug_log_file_access'])) $configs['aiowps_block_debug_log_file_access'] = sanitize_text_field($settings['aiowps_block_debug_log_file_access']);
		if (!empty($settings['aiowps_disable_index_views'])) $configs['aiowps_disable_index_views'] = sanitize_text_field($settings['aiowps_disable_index_views']);
		if (!empty($settings['aiowps_disable_trace_and_track'])) $configs['aiowps_disable_trace_and_track'] = sanitize_text_field($settings['aiowps_disable_trace_and_track']);
		if (!empty($settings['aiowps_enable_5g_firewall'])) $configs['aiowps_enable_5g_firewall'] = sanitize_text_field($settings['aiowps_enable_5g_firewall']);
		if (!empty($settings['aiowps_enable_6g_firewall'])) $configs['aiowps_enable_6g_firewall'] = sanitize_text_field($settings['aiowps_enable_6g_firewall']);
		if (!empty($settings['aiowps_enable_custom_rules'])) $configs['aiowps_enable_custom_rules'] = sanitize_text_field($settings['aiowps_enable_custom_rules']);
		if (!empty($settings['aiowps_place_custom_rules_at_top'])) $configs['aiowps_place_custom_rules_at_top'] = sanitize_text_field($settings['aiowps_place_custom_rules_at_top']);
		if (!empty($settings['aiowps_custom_rules'])) $configs['aiowps_custom_rules'] = $settings['aiowps_custom_rules']; // for .htaccess rules no sanization should be used it may break rules.
		if (!empty($settings['aiowps_upgrade_unsafe_http_calls'])) $configs['aiowps_upgrade_unsafe_http_calls'] = sanitize_text_field($settings['aiowps_upgrade_unsafe_http_calls']);
		if (!empty($settings['aiowps_upgrade_unsafe_http_calls_url_exceptions'])) $configs['aiowps_upgrade_unsafe_http_calls_url_exceptions'] = sanitize_textarea_field($settings['aiowps_upgrade_unsafe_http_calls_url_exceptions']);
		if (!empty($settings['aiowps_enable_404_logging'])) $configs['aiowps_enable_404_logging'] = sanitize_text_field($settings['aiowps_enable_404_logging']);
		if (!empty($settings['aiowps_enable_404_IP_lockout'])) $configs['aiowps_enable_404_IP_lockout'] = sanitize_text_field($settings['aiowps_enable_404_IP_lockout']);
		if (!empty($settings['aiowps_404_lockout_time_length'])) $configs['aiowps_404_lockout_time_length'] = sanitize_text_field($settings['aiowps_404_lockout_time_length']);
		if (!empty($settings['aiowps_404_lock_redirect_url'])) $configs['aiowps_404_lock_redirect_url'] = esc_url_raw($settings['aiowps_404_lock_redirect_url']);
		if (!empty($settings['aiowps_enable_rename_login_page'])) $configs['aiowps_enable_rename_login_page'] = sanitize_text_field($settings['aiowps_enable_rename_login_page']);
		if (!empty($settings['aiowps_login_page_slug'])) $configs['aiowps_login_page_slug'] = sanitize_text_field($settings['aiowps_login_page_slug']);
		if (!empty($settings['aiowps_enable_rename_login_redirect'])) $configs['aiowps_enable_rename_login_redirect'] = sanitize_text_field($settings['aiowps_enable_rename_login_redirect']);
		// Uses isset() rather than !empty() so that an explicitly blank exported value clears any redirect configured on the importing site, instead of being skipped.
		if (isset($settings['aiowps_rename_login_redirect_slug'])) {
			$rename_login_redirect_slug = sanitize_text_field($settings['aiowps_rename_login_redirect_slug']);
			// An imported value that is external or resolves back to wp-admin/the login page would create a redirect loop (or a pointless one), so it's dropped rather than imported as-is.
			if ('' !== $rename_login_redirect_slug && (AIOWPSecurity_Utility::is_external_redirect_slug($rename_login_redirect_slug) || AIOWPSecurity_Utility::is_admin_or_login_redirect_slug($rename_login_redirect_slug))) {
				$rename_login_redirect_slug = '';
			}
			$configs['aiowps_rename_login_redirect_slug'] = $rename_login_redirect_slug;
		}
		if (!empty($settings['aiowps_enable_login_honeypot'])) $configs['aiowps_enable_login_honeypot'] = sanitize_text_field($settings['aiowps_enable_login_honeypot']);
		if (!empty($settings['aiowps_disable_application_password'])) $configs['aiowps_disable_application_password'] = sanitize_text_field($settings['aiowps_disable_application_password']);
		if (!empty($settings['aiowps_enable_brute_force_attack_prevention'])) $configs['aiowps_enable_brute_force_attack_prevention'] = sanitize_text_field($settings['aiowps_enable_brute_force_attack_prevention']);
		if (!empty($settings['aiowps_brute_force_secret_word'])) $configs['aiowps_brute_force_secret_word'] = sanitize_text_field($settings['aiowps_brute_force_secret_word']);
		if (!empty($settings['aiowps_cookie_brute_test'])) $configs['aiowps_cookie_brute_test'] = sanitize_text_field($settings['aiowps_cookie_brute_test']);
		if (!empty($settings['aiowps_cookie_based_brute_force_redirect_url'])) $configs['aiowps_cookie_based_brute_force_redirect_url'] = esc_url_raw($settings['aiowps_cookie_based_brute_force_redirect_url']);
		if (!empty($settings['aiowps_brute_force_attack_prevention_pw_protected_exception'])) $configs['aiowps_brute_force_attack_prevention_pw_protected_exception'] = sanitize_text_field($settings['aiowps_brute_force_attack_prevention_pw_protected_exception']);
		if (!empty($settings['aiowps_brute_force_attack_prevention_ajax_exception'])) $configs['aiowps_brute_force_attack_prevention_ajax_exception'] = sanitize_text_field($settings['aiowps_brute_force_attack_prevention_ajax_exception']);
		if (!empty($settings['aiowps_site_lockout'])) $configs['aiowps_site_lockout'] = sanitize_text_field($settings['aiowps_site_lockout']);
		if (!empty($settings['aiowps_site_lockout_msg'])) $configs['aiowps_site_lockout_msg'] = wp_kses_post($settings['aiowps_site_lockout_msg']);
		if (!empty($settings['aiowps_enable_comment_captcha'])) $configs['aiowps_enable_comment_captcha'] = sanitize_text_field($settings['aiowps_enable_comment_captcha']);
		if (!empty($settings['aiowps_enable_autoblock_spam_ip'])) $configs['aiowps_enable_autoblock_spam_ip'] = sanitize_text_field($settings['aiowps_enable_autoblock_spam_ip']);
		if (!empty($settings['aiowps_spam_ip_min_comments_block'])) $configs['aiowps_spam_ip_min_comments_block'] = sanitize_text_field($settings['aiowps_spam_ip_min_comments_block']);
		if (!empty($settings['aiowps_enable_bp_register_captcha'])) $configs['aiowps_enable_bp_register_captcha'] = sanitize_text_field($settings['aiowps_enable_bp_register_captcha']);
		if (!empty($settings['aiowps_enable_bbp_new_topic_captcha'])) $configs['aiowps_enable_bbp_new_topic_captcha'] = sanitize_text_field($settings['aiowps_enable_bbp_new_topic_captcha']);
		if (!empty($settings['aiowps_enable_spambot_detecting'])) $configs['aiowps_enable_spambot_detecting'] = sanitize_text_field($settings['aiowps_enable_spambot_detecting']);
		if (!empty($settings['aiowps_spambot_detect_usecookies'])) $configs['aiowps_spambot_detect_usecookies'] = sanitize_text_field($settings['aiowps_spambot_detect_usecookies']);
		if (!empty($settings['aiowps_spam_comments_should'])) $configs['aiowps_spam_comments_should'] = sanitize_text_field($settings['aiowps_spam_comments_should']);
		if (!empty($settings['aiowps_enable_trash_spam_comments'])) $configs['aiowps_enable_trash_spam_comments'] = sanitize_text_field($settings['aiowps_enable_trash_spam_comments']);
		if (!empty($settings['aiowps_trash_spam_comments_after_days'])) $configs['aiowps_trash_spam_comments_after_days'] = sanitize_text_field($settings['aiowps_trash_spam_comments_after_days']);
		if (!empty($settings['aiowps_enable_automated_fcd_scan'])) $configs['aiowps_enable_automated_fcd_scan'] = sanitize_text_field($settings['aiowps_enable_automated_fcd_scan']);
		if (!empty($settings['aiowps_fcd_scan_frequency'])) $configs['aiowps_fcd_scan_frequency'] = sanitize_text_field($settings['aiowps_fcd_scan_frequency']);
		if (!empty($settings['aiowps_fcd_scan_interval'])) $configs['aiowps_fcd_scan_interval'] = sanitize_text_field($settings['aiowps_fcd_scan_interval']);
		if (!empty($settings['aiowps_fcd_exclude_filetypes'])) $configs['aiowps_fcd_exclude_filetypes'] = sanitize_textarea_field($settings['aiowps_fcd_exclude_filetypes']);
		if (!empty($settings['aiowps_fcd_exclude_files'])) $configs['aiowps_fcd_exclude_files'] = sanitize_textarea_field($settings['aiowps_fcd_exclude_files']);
		if (!empty($settings['aiowps_send_fcd_scan_email'])) $configs['aiowps_send_fcd_scan_email'] = sanitize_text_field($settings['aiowps_send_fcd_scan_email']);
		if (!empty($settings['aiowps_fcd_scan_email_address'])) $configs['aiowps_fcd_scan_email_address'] = sanitize_textarea_field($settings['aiowps_fcd_scan_email_address']);
		$configs['aiowps_fcds_change_detected'] = !empty($settings['aiowps_fcds_change_detected']);
		if (!empty($settings['aiowps_copy_protection'])) $configs['aiowps_copy_protection'] = sanitize_text_field($settings['aiowps_copy_protection']);
		if (!empty($settings['aiowps_prevent_site_display_inside_frame'])) $configs['aiowps_prevent_site_display_inside_frame'] = sanitize_text_field($settings['aiowps_prevent_site_display_inside_frame']);
		if (!empty($settings['aiowps_prevent_users_enumeration'])) $configs['aiowps_prevent_users_enumeration'] = sanitize_text_field($settings['aiowps_prevent_users_enumeration']);
		if (!empty($settings['aiowps_disallow_unauthorized_rest_requests'])) $configs['aiowps_disallow_unauthorized_rest_requests'] = sanitize_text_field($settings['aiowps_disallow_unauthorized_rest_requests']);
		if (!empty($settings['aios_roles_disallowed_rest_requests'])) $configs['aios_roles_disallowed_rest_requests'] = array_map('sanitize_text_field', $settings['aios_roles_disallowed_rest_requests']);
		if (!empty($settings['aios_whitelisted_rest_routes'])) $configs['aios_whitelisted_rest_routes'] = array_map('sanitize_text_field', $settings['aios_whitelisted_rest_routes']);
		if (!empty($settings['aiowps_ip_retrieve_method'])) $configs['aiowps_ip_retrieve_method'] = sanitize_text_field($settings['aiowps_ip_retrieve_method']);
		if (!empty($settings['aiowps_turnstile_site_key'])) $configs['aiowps_turnstile_site_key'] = sanitize_text_field($settings['aiowps_turnstile_site_key']);
		if (!empty($settings['aiowps_turnstile_secret_key'])) $configs['aiowps_turnstile_secret_key'] = sanitize_text_field($settings['aiowps_turnstile_secret_key']);
		if (!empty($settings['aiowps_recaptcha_site_key'])) $configs['aiowps_recaptcha_site_key'] = sanitize_text_field($settings['aiowps_recaptcha_site_key']);
		if (!empty($settings['aiowps_recaptcha_secret_key'])) $configs['aiowps_recaptcha_secret_key'] = sanitize_text_field($settings['aiowps_recaptcha_secret_key']);
		if (!empty($settings['aiowps_default_recaptcha'])) $configs['aiowps_default_recaptcha'] = sanitize_text_field($settings['aiowps_default_recaptcha']);
		if (!empty($settings['aiowps_on_uninstall_delete_db_tables'])) $configs['aiowps_on_uninstall_delete_db_tables'] = sanitize_text_field($settings['aiowps_on_uninstall_delete_db_tables']);
		if (!empty($settings['aiowps_on_uninstall_delete_configs'])) $configs['aiowps_on_uninstall_delete_configs'] = sanitize_text_field($settings['aiowps_on_uninstall_delete_configs']);
		return $configs;
	}
	
	/**
	 * Sanitize firewall settings value when imported from file
	 *
	 * @param mixed $settings Imported Settings
	 *
	 * @return mixed $configs Sanitized settings
	 */
	public static function sanitize_firewall_setting_values($settings) {
		$configs = array();
		$configs['aios_enable_rename_login_page'] = empty($settings['aios_enable_rename_login_page']) ? '' : sanitize_text_field($settings['aios_enable_rename_login_page']);
		$configs['aios_login_page_slug'] = empty($settings['aios_login_page_slug']) ? '' : sanitize_text_field($settings['aios_login_page_slug']);
		$configs['aios_enable_brute_force_attack_prevention'] = empty($settings['aios_enable_brute_force_attack_prevention']) ? '' : sanitize_text_field($settings['aios_enable_brute_force_attack_prevention']);
		$configs['aios_brute_force_secret_word'] = empty($settings['aios_brute_force_secret_word']) ? '' : sanitize_text_field($settings['aios_brute_force_secret_word']);
		$configs['aios_cookie_based_brute_force_redirect_url'] = empty($settings['aios_cookie_based_brute_force_redirect_url']) ? '' : esc_url_raw($settings['aios_cookie_based_brute_force_redirect_url']);
		$configs['aios_brute_force_attack_prevention_pw_protected_exception'] = empty($settings['aios_brute_force_attack_prevention_pw_protected_exception']) ? '' : sanitize_text_field($settings['aios_brute_force_attack_prevention_pw_protected_exception']);
		$configs['aios_brute_force_attack_prevention_ajax_exception'] = empty($settings['aios_brute_force_attack_prevention_ajax_exception']) ? '' : sanitize_text_field($settings['aios_brute_force_attack_prevention_ajax_exception']);
		$configs['aios_brute_force_secret_cookie_name'] = empty($settings['aios_brute_force_secret_cookie_name']) ? '' : sanitize_text_field($settings['aios_brute_force_secret_cookie_name']);
		$configs['aios_ip_retrieve_method'] = empty($settings['aios_ip_retrieve_method']) ? '' : sanitize_text_field($settings['aios_ip_retrieve_method']);
		$configs['aiowps_ip_retrieve_method'] = empty($settings['aiowps_ip_retrieve_method']) ? '0' : sanitize_text_field($settings['aiowps_ip_retrieve_method']);
		$configs['aiowps_blacklist_ips'] = empty($settings['aiowps_blacklist_ips']) ? '' : array_map('sanitize_text_field', $settings['aiowps_blacklist_ips']);
		$configs['aiowps_blacklist_user_agents'] = empty($settings['aiowps_blacklist_user_agents']) ? '' : array_map('sanitize_text_field', $settings['aiowps_blacklist_user_agents']);
		$configs['aiowps_block_fake_googlebots'] = !empty($settings['aiowps_block_fake_googlebots']);
		$configs['aiowps_googlebot_ip_ranges'] = empty($settings['aiowps_googlebot_ip_ranges']) ? '' : array_map('sanitize_text_field', $settings['aiowps_googlebot_ip_ranges']);
		$configs['aiowps_bingbot_ip_ranges'] = empty($settings['aiowps_bingbot_ip_ranges']) ? '' : array_map('sanitize_text_field', $settings['aiowps_bingbot_ip_ranges']);
		$configs['aiowps_enable_pingback_firewall'] = !empty($settings['aiowps_enable_pingback_firewall']);
		$configs['aiowps_forbid_proxy_comments'] = !empty($settings['aiowps_forbid_proxy_comments']);
		$configs['aiowps_deny_bad_query_strings'] = !empty($settings['aiowps_deny_bad_query_strings']);
		$configs['aiowps_forbid_proxy_comments'] = !empty($settings['aiowps_forbid_proxy_comments']);
		$configs['aiowps_advanced_char_string_filter'] = !empty($settings['aiowps_advanced_char_string_filter']);
		$configs['aiowps_6g_block_request_methods'] = empty($settings['aiowps_6g_block_request_methods']) ? '' : array_map('sanitize_text_field', $settings['aiowps_6g_block_request_methods']);
		$configs['aiowps_6g_block_query'] = !empty($settings['aiowps_6g_block_query']);
		$configs['aiowps_6g_block_request'] = !empty($settings['aiowps_6g_block_request']);
		$configs['aiowps_6g_block_referrers'] = !empty($settings['aiowps_6g_block_referrers']);
		$configs['aiowps_6g_block_agents'] = !empty($settings['aiowps_6g_block_agents']);
		$configs['aiowps_ban_post_blank_headers'] = !empty($settings['aiowps_ban_post_blank_headers']);
		return $configs;
	}
}
