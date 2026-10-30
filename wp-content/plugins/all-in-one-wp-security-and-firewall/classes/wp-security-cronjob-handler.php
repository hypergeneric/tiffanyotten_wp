<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

/**
 * Handles all cron jobs
 */
class AIOWPSecurity_Cronjob_Handler {
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter('cron_schedules', array('AIOWPSecurity_Cronjob_Handler', 'cron_schedules'));

		add_action('aios_15_minutes_cron_event', array($this, 'aios_15_minutes_cron_event'));
		add_action('aios_hourly_cron_event', array($this, 'aios_hourly_cron_event_handler'));
		add_action('aios_daily_cron_event', array($this, 'aios_daily_cron_event_handler'));
		add_action('aios_change_auth_keys_and_salt', 'AIOWPSecurity_Utility::change_salt_postfixes');
		add_action('aios_send_lockout_email', array($this, 'send_lockout_email'));
		add_action('aios_update_googlebot_ip_ranges', array($this, 'aios_update_googlebot_ip_ranges'));
		add_action('aios_update_bingbot_ip_ranges', array($this, 'aios_update_bingbot_ip_ranges'));
		add_action('aios_perform_fcd_scan_tasks', array($this, 'aios_scheduled_fcd_scan_handler'));
		add_action('aios_firewall_session_prune', array($this, 'do_firewall_session_prune'));
	}

	/**
	 * Adds a custom cron schedule for every 5 minutes.
	 *
	 * @param array $schedules An array of cron schedules.
	 * @return array Filtered array of cron schedules.
	 */
	public static function cron_schedules($schedules) {
		$schedules['aios-every-15-minutes'] = array(
			'interval' => 900, // 15 * 60
			'display' => __('Every 15 minutes', 'all-in-one-wp-security-and-firewall')
		);
		return $schedules;
	}

	/**
	 * Run cron event every 5 minute.
	 *
	 * @return void
	 */
	public function aios_15_minutes_cron_event() {
		global $aio_wp_security;

		if (!class_exists('Updraft_Semaphore_3_0')) {
			require_once(AIO_WP_SECURITY_PATH.'/vendor/team-updraft/common-libs/src/updraft-semaphore/class-updraft-semaphore.php');
		}

		$fifteen_minutes_cron_semaphore = new Updraft_Semaphore_3_0('aios_15_minutes_cron_event', 60);

		if ($fifteen_minutes_cron_semaphore->lock(2)) {
			try {
				$aio_wp_security->user_login_obj->send_login_lockout_emails();
			} catch (Exception $e) {
				$log_message = 'Exception ('.get_class($e).') occurred during the 5 minutes cron event action call: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				$aio_wp_security->debug_logger->log_debug($log_message, 4);
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') occurred during the the 5 minutes cron event action call. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				$aio_wp_security->debug_logger->log_debug($log_message, 4);
			}

			$fifteen_minutes_cron_semaphore->release();
		} else {
			$aio_wp_security->debug_logger->log_debug('The 15 minutes cron event lock could not get the lock.');
		}
	}

	public function aios_hourly_cron_event_handler() {
		//Do stuff that needs checking hourly
		AIOWPSecurity_Comment::trash_spam_comments();
		do_action('aios_perform_fcd_scan_tasks');
		do_action('aios_delete_expired_logged_in_users_event');
	}

	/**
	 * Run daily cron event
	 *
	 * @return void
	 */
	public function aios_daily_cron_event_handler() {
		do_action('aios_perform_db_cleanup_tasks');
		do_action('aios_send_lockout_email');
		do_action('aios_perform_update_antibot_keys');
		do_action('aios_update_googlebot_ip_ranges');
		do_action('aios_update_bingbot_ip_ranges');
		do_action('aios_clean_up_old_tasks');
		do_action('aios_firewall_session_prune');
	}

	/**
	 * Send email notification to an user who has flag is_lockout_email_sent is 0.
	 *
	 * @return Void
	 */
	public function send_lockout_email() {
		global $aio_wp_security;
		$aio_wp_security->user_login_obj->send_login_lockout_emails();
	}

	/**
	 * Update Googlebot IP ranges for the firewall configuration.
	 *
	 * This function updates the Googlebot IP ranges in the firewall configuration.
	 * It retrieves the Googlebot IP ranges and validates.
	 * they are saved to the firewall configuration. If there is an error in retrieving
	 * the IP ranges, a debug message is logged.
	 *
	 * @global object $aio_wp_security The main AIO WP Security object.
	 * @global object $aiowps_firewall_config The AIO WP Security firewall configuration object.
	 *
	 * @return void
	 */
	public function aios_update_googlebot_ip_ranges() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		$validated_ip_list_array = AIOWPSecurity_Utility::get_googlebot_ip_ranges();

		if (is_wp_error($validated_ip_list_array)) {
			$aio_wp_security->debug_logger->log_debug('The attempt to save the IP addresses failed, because it was not possible to validate the Googlebot IP addresses: ' . $validated_ip_list_array->get_error_message(), 4);
			return;
		}

		$aiowps_firewall_config->set_value('aiowps_googlebot_ip_ranges', $validated_ip_list_array);
	}
	
	/**
	 * Update Bingbot IP ranges for the firewall configuration.
	 *
	 * This function updates the Bingbot IP ranges in the firewall configuration.
	 * It retrieves the Bingbot IP ranges and validates.
	 * They are saved to the firewall configuration.
	 * If there is an error in retrieving, a debug message is logged.
	 *
	 * @global object $aio_wp_security The AIO WP Security object.
	 * @global object $aiowps_firewall_config The AIO WP Security firewall configuration object.
	 *
	 * @return void
	 */
	public function aios_update_bingbot_ip_ranges() {
		global $aio_wp_security;
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
		
		$validated_ip_list_array = AIOWPSecurity_Utility::get_bingbot_ip_ranges();

		if (is_wp_error($validated_ip_list_array)) {
			$aio_wp_security->debug_logger->log_debug('The attempt to save the IP addresses failed, because it was not possible to validate the Bingbot IP addresses: ' . $validated_ip_list_array->get_error_message(), 4);
			return;
		}

		$aiowps_firewall_config->set_value('aiowps_bingbot_ip_ranges', $validated_ip_list_array);
	}

	/**
	 * This function is called via the following filter 'aios_perform_fcd_scan_tasks' and will start the file scan
	 *
	 * @return void
	 */
	public function aios_scheduled_fcd_scan_handler() {
		global $aio_wp_security;
		$task_manager = AIOWPSecurity_Task_Manager::get_instance();

		if ('1' != $aio_wp_security->configs->get_value('aiowps_enable_automated_fcd_scan') || $task_manager->fetch_active_task('file_scan')) return;

		$aio_wp_security->debug_logger->log_debug_cron(__METHOD__ . " - Scheduled fcd_scan is enabled. Checking now to see if scan needs to be done...");

		$current_time = time();
		$next_fcd_scan_time = AIOWPSecurity_File_Scanner::get_next_scheduled_scan();

		if ($next_fcd_scan_time <= $current_time) {
			$task_name = 'aiowps_file_scan_' . $task_manager->generate_unique_task_name();
			$task = AIOWPSecurity_File_Scan_Task::create_task('file_scan', $task_name, array('anonymous_user_allowed' => true));

			if (!$task) {
				$aio_wp_security->debug_logger->log_debug(__METHOD__ . ' - Scheduled filescan operation failed.', 4);
				return;
			}

			if (!wp_next_scheduled('aios_process_file_scan_tasks')) {
				wp_schedule_single_event(time() - 1, 'aios_process_file_scan_tasks');
			}
			AIOWPSecurity_Utility::spawn_cron();

			$aio_wp_security->configs->set_value('aiowps_last_fcd_scan_time', $current_time, true);
		}
	}

	/**
	 * Prune any session files
	 *
	 * @return void
	 */
	public function do_firewall_session_prune() {
		global $aiowps_firewall_session;

		if (isset($aiowps_firewall_session)) $aiowps_firewall_session->prune();
	}
}
