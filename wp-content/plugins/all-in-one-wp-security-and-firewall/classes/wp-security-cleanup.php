<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

/**
 * AIOWPSecurity_Cleanup class for clean up database etc.
 *
 * @access public
 */
class AIOWPSecurity_Cleanup {
	
	/**
	 * Class constructor added action
	 */
	public function __construct() {
		add_action('aios_perform_db_cleanup_tasks', array($this, 'aiowps_scheduled_db_cleanup_handler'));
	}
	
	/**
	 * Clean up unnecessary old data from aiowps tables.
	 *
	 * @return void
	 */
	public function aiowps_scheduled_db_cleanup_handler() {
		//Check the events table because this can grow quite large especially when 404 events are being logged
		$events_table_name = AIOWPSEC_TBL_EVENTS;
		$purge_events_records_after_days = AIOS_PURGE_EVENTS_RECORDS_AFTER_DAYS; //purge older records in the events table
		$purge_events_records_after_days = apply_filters('aios_purge_events_records_after_days', $purge_events_records_after_days);
		AIOWPSecurity_Utility::purge_table_records($events_table_name, $purge_events_records_after_days, 'created');

		//Check the login lockout table
		$login_lockout_table_name = AIOWPSEC_TBL_LOGIN_LOCKOUT;
		$purge_login_lockout_records_after_days = AIOS_PURGE_LOGIN_LOCKOUT_RECORDS_AFTER_DAYS; //purge older records in the events table
		$purge_login_lockout_records_after_days = apply_filters('aios_purge_login_lockout_records_after_days', $purge_login_lockout_records_after_days);
		AIOWPSecurity_Utility::purge_table_records($login_lockout_table_name, $purge_login_lockout_records_after_days, 'created');

		//Check the global meta table
		$global_meta_table_name = AIOWPSEC_TBL_GLOBAL_META_DATA;
		$purge_global_meta_records_after_days = AIOS_PURGE_GLOBAL_META_DATA_RECORDS_AFTER_DAYS; //purge older records in global meta table
		$purge_global_meta_records_after_days = apply_filters('aios_purge_global_meta_records_after_days', $purge_global_meta_records_after_days);
		AIOWPSecurity_Utility::purge_table_records($global_meta_table_name, $purge_global_meta_records_after_days, 'created');
		
		//Check the debug log table
		$purge_debug_table_after_days = 90;

		if (defined('AIOWPSEC_PURGE_DEBUG_LOGS_AFTER_DAYS')) {
			$purge_debug_table_after_days = absint(AIOWPSEC_PURGE_DEBUG_LOGS_AFTER_DAYS);
		}
		$purge_debug_table_after_days = empty($purge_debug_table_after_days) ? 90 : $purge_debug_table_after_days;
		
		$debug_table_name = AIOWPSEC_TBL_DEBUG_LOG;
		AIOWPSecurity_Utility::purge_table_records($debug_table_name, $purge_debug_table_after_days, 'logtime');

		// Check the audit log table.
		$purge_audit_table_after_days = absint(AIOWPS_AUDIT_LOG_DEFAULT_RETENTION);

		if (defined('AIOWPSEC_PURGE_AUDIT_LOGS_AFTER_DAYS')) $purge_audit_table_after_days = AIOWPSEC_PURGE_AUDIT_LOGS_AFTER_DAYS;

		$purge_audit_table_after_days = apply_filters('aios_purge_audit_log_records_after_days', $purge_audit_table_after_days);

		if (!is_int($purge_audit_table_after_days) || $purge_audit_table_after_days < 0 || $purge_audit_table_after_days > 3650) {
			$purge_audit_table_after_days = absint(AIOWPS_AUDIT_LOG_DEFAULT_RETENTION);
		}

		// Purge audit logs when retention is not set to "No cleanup" (0).
		if ($purge_audit_table_after_days > 0) {
			$audit_table_name = AIOWPSEC_TBL_AUDIT_LOG;
			AIOWPSecurity_Utility::purge_table_records($audit_table_name, $purge_audit_table_after_days, 'created');
		}

		// Delete any expired _aiowps_captcha_string_info_xxxx option.
		AIOWPSecurity_Utility::delete_expired_captcha_options();
		//Keep adding other DB cleanup tasks as they arise...
	}
}
