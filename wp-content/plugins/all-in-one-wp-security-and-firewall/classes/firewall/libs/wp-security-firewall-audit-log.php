<?php
namespace AIOWPS\Firewall;

class Audit_Log {

	/**
	 * Store the audit log's entries
	 *
	 * @var array
	 */
	protected array $entries;

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
	 * The different levels for the audit log entry
	 */
	const LEVEL_INFO    = "info";
	const LEVEL_WARNING = "warning";
	const LEVEL_FATAL   = "fatal";
	const LEVEL_ERROR   = "error";
	const LEVEL_DEBUG   = "debug";
	const LEVEL_TRACE   = "trace";

	/**
	 * Construct our object
	 */
	public function __construct() {
		$this->entries = array();

		$this->batch_size = defined('AIOS_FIREWALL_LOG_BATCH_SIZE') ? (int) abs(constant('AIOS_FIREWALL_LOG_BATCH_SIZE')) : self::DEFAULT_BATCH_SIZE;

		register_shutdown_function(function() {
			$this->save();
		});
	}

	/**
	 * Add an entry to the in-memory audit log entry list
	 *
	 * @param string $event_type
	 * @param string $level
	 * @param array  $details
	 * @return void
	 */
	public function log(string $event_type, string $level = self::LEVEL_INFO, array $details = array()) : void {
		$entry = new \stdClass();

		$entry->type    = $event_type;
		$entry->level   = $level;
		$entry->details = $details;
		$entry->created = time();

		$this->entries[] = $entry;
	}

	/**
	 * Save the entries to the audit log
	 *
	 * @return void
	 */
	private function save() : void {

		if (empty($this->entries)) return;

		if (!Context::wordpress_safe()) {
			if (!Utility::attempt_to_access_wpdb()) {
				error_log('AIOS: Unable to save the firewall\'s audit log to the database, because wpdb is inaccessible.');
				return;
			}
		}

		global $wpdb;
		$table = defined('AIOWPSEC_TBL_AUDIT_LOG') ? constant('AIOWPSEC_TBL_AUDIT_LOG') : $wpdb->get_blog_prefix(0).'aiowps_audit_log';

		$ip         = \AIOS_Helper::get_user_ip_address();
		$network_id = get_current_network_id();
		$blog_id    = get_current_blog_id();
		$username   = $this->get_username();

		foreach (array_chunk($this->entries, $this->batch_size) as $entries) {

			$stmt   = "INSERT INTO `{$table}` (network_id, site_id, username, ip, level, event_type, details, stacktrace, created, country_code) VALUES ";
			$values = array();

			foreach ($entries as $entry) {
				$stmt .= "(%d, %d, %s, %s, %s, %s, %s, %s, %d, %s),";

				$values[] = $network_id;
				$values[] = $blog_id;
				$values[] = $username;
				$values[] = $ip;
				$values[] = $entry->level;
				$values[] = $entry->type;
				$values[] = json_encode(array('firewall_event' => $entry->details));
				$values[] = "";
				$values[] = $entry->created;
				$values[] = "";
			}

			$stmt = rtrim($stmt, ',');

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared -- False positive; the sql query is being dynamically built.
			$wpdb->query($wpdb->prepare($stmt, $values));
		}
	}

	/**
	 * Attempts to read and verify the username from the cookie
	 *
	 * @return string - Username if verified; blank otherwise.
	 */
	private function get_username() : string {

		$cookie = $this->parse_logged_in_cookie();

		if (is_null($cookie)) return '';

		if ($this->is_cookie_valid($cookie)) return $cookie->username;
		
		return '';
	}

	/**
	 * Parse WordPress's logged in cookie into its elements
	 *
	 * @return object|null - An object whose properties are the elements of the cookie; null on failure
	 */
	private function parse_logged_in_cookie() : ?object {
		
		$cookie_key = defined("COOKIEHASH") ? "wordpress_logged_in_" . COOKIEHASH : "";

		if (empty($cookie_key)) {

			$cookie_key = "wordpress_logged_in_";

			$site_url = \get_site_option("siteurl");

			if (!empty($site_url)) $cookie_key .= md5($site_url);
		}

		if (!isset($_COOKIE[$cookie_key])) return null;

		$cookie_value = $_COOKIE[$cookie_key];

		$elements = explode('|', $cookie_value, 4);

		if (count($elements) !== 4) return null;

		$scheme = "logged_in";
		list($username, $expiration, $token, $hmac) = $elements;

		return (object) compact('username', 'expiration', 'token', 'hmac', 'scheme');
	}

	/**
	 * Determines whether the cookie is valid
	 *
	 * @param object $cookie
	 * @return bool
	 */
	private function is_cookie_valid(object $cookie) : bool {
		return !$this->has_cookie_expired($cookie) && $this->verify_token($cookie) && $this->verify_hmac($cookie);
	}

	/**
	 * Returns partial characters from the user's pass hash in order to verify the hmac
	 *
	 * @param string $username
	 * @return string
	 */
	private function get_partial_hash(string $username) : string {
		global $wpdb;
		$table = $wpdb->get_blog_prefix(0)."users";

		$pass = $wpdb->get_var($wpdb->prepare("SELECT user_pass FROM `{$table}` WHERE user_login = %s", $username));

		if (is_null($pass)) return '';

		$is_legacy_hash = substr($pass, 0, 3) === '$P$' || substr($pass, 0, 4) === '$2y$';

		return $is_legacy_hash ? substr($pass, 8, 4) : substr($pass, -4);
	}

	/**
	 * Gets the user id from the username
	 *
	 * @param string $username
	 * @return int|null
	 */
	private function get_user_id(string $username) : ?int {
		global $wpdb;
		$table = $wpdb->get_blog_prefix(0)."users";

		return $wpdb->get_var($wpdb->prepare("SELECT id FROM `{$table}` WHERE user_login = %s", $username));
	}

	/**
	 * Gets the user's token from the user's metadata
	 *
	 * @param int $uid
	 * @return string|null
	 */
	private function get_session_tokens(int $uid) : ?string {
		global $wpdb;
		$table = $wpdb->get_blog_prefix(0)."usermeta";

		return $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM `{$table}` WHERE meta_key = 'session_tokens' AND user_id = %d", $uid));
	}

	/**
	 * Checks whether the cookie has expired
	 *
	 * @param object $cookie
	 * @return bool
	 */
	private function has_cookie_expired(object $cookie) : bool {
		return $cookie->expiration < time();
	}

	/**
	 * Verifies the cookie's integrity via its hmac signature
	 *
	 * @param object $cookie
	 * @return bool
	 */
	private function verify_hmac(object $cookie) : bool {
		global $aiowps_firewall_constants;

		$incs   = $aiowps_firewall_constants->WPINC ?? 'wp-includes';
		$wp_dir = Utility::get_wordpress_dir();

		// These includes are needed for the wp_salt function
		$includes = array(
			$wp_dir . '/' . $incs . '/l10n.php',
			$wp_dir . '/' . $incs . '/pluggable.php',
		);
		
		foreach ($includes as $include) {
			if (file_exists($include)) include_once $include;
		}

		if (!function_exists('\wp_salt')) return false;

		$salt = \wp_salt('logged_in');
		
		if (empty($salt)) return false;

		$partial_pass = $this->get_partial_hash($cookie->username);

		$key = hash_hmac('md5', $cookie->username . '|' . $partial_pass . '|' . $cookie->expiration . '|' . $cookie->token, $salt);

		$algo = function_exists('hash') ? 'sha256' : 'sha1';
		$hash = hash_hmac($algo, $cookie->username . '|' . $cookie->expiration . '|' . $cookie->token, $key);

		return hash_equals($hash, $cookie->hmac);
	}

	/**
	 * Verifies that the user's session is still valid
	 *
	 * @param object $cookie
	 * @return bool
	 */
	private function verify_token(object $cookie) : bool {

		$uid = $this->get_user_id($cookie->username);

		if (is_null($uid)) return false;

		$sessions = $this->get_session_tokens((int) $uid);

		if (is_null($sessions)) return false;

		// https://developer.wordpress.org/reference/functions/maybe_unserialize/
		if (is_serialized($sessions)) {
			$sessions = @unserialize(trim($sessions));
		}

		if (!is_array($sessions)) return false;

		$key = hash('sha256', $cookie->token);

		if (!isset($sessions[$key])) return false;

		$expire = $sessions[$key]['expiration'];

		return $expire > time();
	}
}
