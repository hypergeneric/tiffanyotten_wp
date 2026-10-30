<?php

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('AIOWPSecurity_Ajax')) :

	class AIOWPSecurity_Ajax {

		private $commands_object;

		private $nonce;

		private $subaction;

		private $data;

		private $results;

		/**
		 * Constructor
		 */
		private function __construct() {
			if (!class_exists('AIOWPSecurity_Commands')) include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-commands.php');
			$this->commands_object = new AIOWPSecurity_Commands();

			add_action('wp_ajax_aios_ajax', array($this, 'handle_ajax_requests'));
			add_action('wp_ajax_nopriv_get_antibot_keys', array($this->commands_object, 'get_antibot_keys'));
		}

		/**
		 * Return singleton instance
		 *
		 * @return AIOWPSecurity_Ajax Returns AIOWPSecurity_Ajax object
		 */
		public static function get_instance() {
			static $instance = null;
			if (null === $instance) {
				$instance = new self();
			}
			return $instance;
		}

		/**
		 * Handles ajax requests
		 *
		 * @return void
		 */
		public function handle_ajax_requests() {
			$this->set_nonce();
			$this->set_subaction();
			$this->set_data();

			$result = AIOWPSecurity_Utility_Permissions::check_nonce_and_user_cap($this->nonce, 'wp-security-ajax-nonce');
			if (is_wp_error($result)) {
				wp_send_json($this->commands_object->handle_response(
					false,
					$result->get_error_message()
				));
			}

			if (is_multisite() && !current_user_can('manage_network_options')) {
				if (!$this->is_valid_multisite_command()) {
					$this->send_invalid_multisite_command_error_response();
				}
			}

			if ($this->is_invalid_command()) {
				$this->add_invalid_command_error_log_entry();
				$this->set_invalid_command_error_response();
			} else {
				$this->execute_command();
				$this->set_error_response_on_wp_error();
				$this->maybe_set_empty_success_response();
			}

			$this->json_encode_results();

			$json_last_error = json_last_error();
			if ($json_last_error) {
				$this->set_error_response_on_json_encode_error($json_last_error);
			}

			echo $this->results; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Variable is an array containing escaped data.
			die;
		}

		/**
		 * Sets nonce property value
		 *
		 * @return void
		 */
		private function set_nonce() {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput -- It's the actual nonce. It does not need sanitizing.
			$this->nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];
		}

		/**
		 * Sets subaction property value
		 *
		 * @return void
		 */
		private function set_subaction() {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce already checked.
			$this->subaction = empty($_POST['subaction']) ? '' : sanitize_text_field(wp_unslash($_POST['subaction']));
		}

		/**
		 * Sets data property value
		 *
		 * @return void
		 */
		private function set_data() {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce already checked.
			$this->data = isset($_POST['data']) ? wp_unslash($_POST['data']) : null;
		}

		/**
		 * Checks whether the requested command may be run by a multisite user who cannot manage network options
		 *
		 * @return bool True if the command is in the (filtered) list of commands allowed for site admins, otherwise false
		 */
		private function is_valid_multisite_command() {
			/**
			 * Filters the commands allowed to the sub site admins. Other commands are only available to network admin. Only used in a multisite context.
			 */
			$allowed_commands = apply_filters('aios_multisite_allowed_commands', $this->get_multisite_allowed_commands());

			return in_array($this->subaction, $allowed_commands, true);
		}

		/**
		 * Gets the commands that a multisite user who cannot manage network options may run
		 *
		 * These commands only read or change data belonging to the current site. Commands that change network-wide data (firewall rules, .htaccess, wp-config.php, network users or premium settings) are left out so that only a network admin can run them.
		 *
		 * @return array Command names. On the main site, commands whose settings are also used network-wide are left out.
		 */
		private function get_multisite_allowed_commands() {
			$allowed_commands = array(
				// Logs, locked and blocked IP addresses.
				'delete_audit_log',
				'render_audit_log_tab',
				'export_audit_logs',
				'delete_404_log',
				'perform_delete_all_404_event_records',
				'render_404_log_tab',
				'export_404_detection_logs',
				'delete_locked_ip_record',
				'render_locked_ip_tab',
				'clear_debug_logs',
				'render_debug_log_tab',
				'send_report_email',
				'unlock_ip',
				'blocked_ip_list_unblock_ip',
				'render_permanent_block_list_tab',
				'render_smart_404_blocked_ips_table',
				'unblock_smart404_ip',
				'lock_ip',
				'dismiss_notice',
				// Brute force.
				'perform_rename_login_page',
				'perform_captcha_settings',
				'perform_login_whitelist_settings',
				'perform_404_settings',
				'perform_honeypot_settings',
				// User security.
				'perform_save_login_lockout_settings',
				'perform_save_login_lockout_whitelist_settings',
				'perform_save_force_logout_settings',
				'render_logged_in_users_tab',
				'perform_force_user_logout',
				'perform_save_http_authentication_settings',
				'perform_disable_application_password',
				// Spam prevention.
				'render_comment_spammer_ip_table',
				'perform_comment_spam_prevention',
				'perform_auto_block_spam_ip',
				'perform_ip_spam_search',
				'perform_block_spam_ip',
				// Firewall.
				'perform_save_rest_api_settings',
				// File security.
				'perform_save_copy_protection',
				'perform_save_frame_display_prevent',
				// Tools.
				'hibp_check_password',
				'perform_whois_lookup',
				'perform_general_visitor_lockout',
				'perform_general_visitor_lockout_dashboard_widget',
				// Settings.
				'perform_save_debug_settings',
				'perform_remove_wp_version_info_settings',
			);

			// The main site's copy of these settings is also used network-wide, so only a network admin may change or export it.
			if (!is_main_site()) {
				$allowed_commands = array_merge($allowed_commands, array(
					'perform_save_user_account_settings',
					'perform_restore_aiowps_settings',
					'perform_export_aios_settings',
					'perform_delete_plugin_settings',
				));
			}

			return $allowed_commands;
		}

		/**
		 * Sends the invalid multisite command error response
		 *
		 * @return void
		 */
		private function send_invalid_multisite_command_error_response() {
			wp_send_json($this->commands_object->handle_response(
				false,
				__('Only a network administrator can perform this action.', 'all-in-one-wp-security-and-firewall')
			));
		}

		/**
		 * Checks if applied ajax command is an invalid command or not
		 *
		 * @return bool Returns true if ajax command is an invalid command, false otherwise
		 */
		private function is_invalid_command() {
			return !is_callable(array($this->commands_object, $this->subaction));
		}

		/**
		 * Log an error message for invalid ajax command
		 *
		 * @return void
		 */
		private function add_invalid_command_error_log_entry() {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Part of error reporting.
			error_log("AIOS: ajax_handler: no such command (" . $this->subaction . ")");
		}

		/**
		 * Set `results` property with error response array for invalid ajax command
		 *
		 * @return void
		 */
		private function set_invalid_command_error_response() {
			$this->results = $this->commands_object->handle_response(
				false,
				/* translators: %s: Subaction */
				sprintf(__('The command "%s" was not found', 'all-in-one-wp-security-and-firewall'), $this->subaction)
			);
		}

		/**
		 * Execute the ajax command
		 *
		 * @return void
		 */
		private function execute_command() {
			$this->results = call_user_func(array($this->commands_object, $this->subaction), $this->data);
		}

		/**
		 * Set `results` property with error message
		 *
		 * @return void
		 */
		private function set_error_response_on_wp_error() {
			if (is_wp_error($this->results)) {
				$this->results = $this->commands_object->handle_response(
					false,
					$this->results->get_error_message()
				);
			}
		}

		/**
		 * Sets the `results` property to an empty success response, if it is not yet set.
		 *
		 * @return void
		 */
		private function maybe_set_empty_success_response() {
			if (empty($this->results)) {
				$this->results = $this->commands_object->handle_response(true, false);
			}
		}

		/**
		 * Sets `results` property with json encode error
		 *
		 * @param int $json_last_error
		 *
		 * @return void
		 */
		private function set_error_response_on_json_encode_error($json_last_error) {
			$this->results = $this->commands_object->handle_response(
				false,
				'json_encode error : ' . $json_last_error
			);

			$this->results = wp_json_encode($this->results);
		}

		/**
		 * Json encode the `results` property value
		 *
		 * @return void
		 */
		private function json_encode_results() {
			$this->results = wp_json_encode($this->results);
		}
	}

endif;
