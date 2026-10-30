<?php
namespace AIOWPS\Firewall;

defined('AIOWPS_FIREWALL_DIR') || die('No direct access allowed.');

/**
 * Holds the different types supported for the session storage
 */
class Session_Storage_Type {
	/**
	 * Filesystem type for session storage implementation
	 */
	const FILESYSTEM = 'filesystem';

	/**
	 * Ensures the type is valid
	 *
	 * @param string $type
	 * @return boolean
	 */
	public static function is_valid($type) {
		return in_array($type, (new \ReflectionClass(self::class))->getConstants());
	}

	/**
	 * Get the set storage type
	 *
	 * @return string
	 */
	public static function get() {
		global $aiowps_firewall_constants;

		$storage_type = isset($aiowps_firewall_constants->AIOS_SESSION_STORAGE_TYPE) ? strtolower($aiowps_firewall_constants->AIOS_SESSION_STORAGE_TYPE) : self::FILESYSTEM;

		return self::is_valid($storage_type) ? $storage_type : self::FILESYSTEM;
	}
}
