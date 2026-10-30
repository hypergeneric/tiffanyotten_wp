<?php
namespace AIOWPS\Firewall;

class Debug_Log {

	/**
	 * Keep track of the debug log entries
	 *
	 * @var array
	 */
	protected $entries;

	/**
	 * The size to batch the log entries into SQL queries.
	 *
	 * @var int
	 */
	protected $batch_size;

	/**
	 * The default batch size for the log entries
	 */
	const DEFAULT_BATCH_SIZE = 500;

	/**
	 * The different types of possible debug logs
	 */
	const TYPE_DEBUG = 'debug';
	const TYPE_CRON  = 'cron_debug';

	/**
	 * The different levels of the log message
	 */
	const LEVEL_SUCCESS  = 'SUCCESS';
	const LEVEL_STATUS   = 'STATUS';
	const LEVEL_NOTICE   = 'NOTICE';
	const LEVEL_WARNING  = 'WARNING';
	const LEVEL_FAILURE  = 'FAILURE';
	const LEVEL_CRITICAL = 'CRITICAL';

	/**
	 * Constructs our object
	 */
	public function __construct() {
		$this->entries = array();

		$this->batch_size = defined('AIOS_FIREWALL_LOG_BATCH_SIZE') ? (int) abs(constant('AIOS_FIREWALL_LOG_BATCH_SIZE')) : self::DEFAULT_BATCH_SIZE;

		register_shutdown_function(function() {
			$this->save();
		});
	}

	/**
	 * Adds an entry to the debug log
	 *
	 * @param string $message
	 * @param string $level
	 * @param string $type
	 * @return void
	 */
	public function log($message, $level = self::LEVEL_SUCCESS, $type = self::TYPE_DEBUG) {
		$entry = new \stdClass();
		$entry->message   = $message;
		$entry->level     = $level;
		$entry->type      = $type;
		$entry->timestamp = time();

		$this->entries[] = $entry;
	}

	/**
	 * Saves the entries to the debug log
	 *
	 * @return void
	 */
	private function save() {
	
		if (empty($this->entries)) return;

		if (!Context::wordpress_safe()) {
			if (!Utility::attempt_to_access_wpdb()) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Used as a last resort since we can't access the debug log.
				error_log('AIOS: Unable to save the firewall\'s debug log to the database, because wpdb is inaccessible.');
				return;
			}
		}

		$opts = \get_option('aio_wp_security_configs');
		$is_enabled = isset($opts['aiowps_enable_debug']) ? (bool) $opts['aiowps_enable_debug'] : false;

		if (!$is_enabled) return;

		global $wpdb;
		$table = defined('AIOWPSEC_TBL_DEBUG_LOG') ? constant('AIOWPSEC_TBL_DEBUG_LOG') : $wpdb->get_blog_prefix(0).'aiowps_debug_log';

		$created    = current_time('mysql');
		$network_id = get_current_network_id();
		$blog_id    = get_current_blog_id();

		foreach (array_chunk($this->entries, $this->batch_size) as $entries) {

			$stmt   = "INSERT INTO `{$table}` (created, logtime, level, network_id, site_id, message,  type) VALUES ";
			$values = array();

			foreach ($entries as $entry) {
				$stmt .= '(%s, %s, %s, %d, %d, %s, %s),';
				$values[]  = $created;
				$values[]  = $entry->timestamp;
				$values[]  = $entry->level;
				$values[]  = $network_id;
				$values[]  = $blog_id;
				$values[]  = $entry->message;
				$values[]  = $entry->type;
			}

			$stmt = rtrim($stmt, ',');

			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- False positive.
			$wpdb->query($wpdb->prepare($stmt, $values));

		}
	}
}
