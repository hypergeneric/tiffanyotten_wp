<?php
namespace AIOWPS\Firewall;

defined('AIOWPS_FIREWALL_DIR') || die('No direct access allowed.');

class Session_Storage_Factory {

	/**
	 * Maps the session storage implementation to its class
	 *
	 * @var array
	 */
	protected static $classmap = array(
		Session_Storage_Type::FILESYSTEM => Filesystem_Session::class,
	);

	/**
	 * Create the session storage based on its type
	 *
	 * @param string $type - The session storage type
	 * @return Session_Storage
	 */
	public static function create($type) {
		$method = "create_session_storage_{$type}";
		return (method_exists(self::class, $method) ? self::$method() : self::create(Session_Storage_Type::FILESYSTEM));

	}

	/**
	 * Creates the filesystem session storage
	 *
	 * @return Session_Storage
	 */
	protected static function create_session_storage_filesystem() {
		global $aiowps_firewall_constants, $aiowps_firewall_config;

		$workspace = Utility::trailingslashit(pathinfo(Loader::$workspace, PATHINFO_DIRNAME));

		$session_path = $aiowps_firewall_config->get_value('firewall_session_path');
		
		if (is_null($session_path) || !file_exists($session_path) || !is_dir($session_path)) {

			$salt = Session_Utility::get_salt();

			$session_path = $workspace.'sessions_'.hash('sha256', microtime() . $salt);
			$aiowps_firewall_config->set_value('firewall_session_path', $session_path);
		}

		$workspace = isset($aiowps_firewall_constants->AIOS_SESSION_PATH) ? $aiowps_firewall_constants->AIOS_SESSION_PATH : $session_path;
				
		$config = array(
			'workspace' => Utility::normalize_path($workspace),
			'prune_period' => isset($aiowps_firewall_constants->AIOS_SESSION_PRUNE_DAYS) ? abs($aiowps_firewall_constants->AIOS_SESSION_PRUNE_DAYS) : 5,
		);

		$class = self::$classmap[Session_Storage_Type::FILESYSTEM];
		return new $class($config);
	}
}
