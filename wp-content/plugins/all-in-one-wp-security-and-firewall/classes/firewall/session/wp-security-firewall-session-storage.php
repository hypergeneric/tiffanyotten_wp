<?php
namespace AIOWPS\Firewall;

defined('AIOWPS_FIREWALL_DIR') || die('No direct access allowed.');

/**
 * Base class for each session storage implementation
 */
abstract class Session_Storage {

	/**
	 * Configures the common objects for each implementation
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->init($config);
	}

	/**
	 * Clears the value of the given key
	 *
	 * @param string $key
	 * @return void
	 */
	abstract public function clear($key);

	/**
	 * Clears any outdated sessions
	 *
	 * @return void
	 */
	abstract public function prune();

	/**
	 * Clears all sessions
	 *
	 * @return void
	 */
	abstract public function purge();

	/**
	 * Purges all the session contents and destroys the session storage
	 *
	 * @return bool
	 */
	abstract public function destroy();

	/**
	 * Safely read the session data at the index of $key
	 *
	 * @param string $key
	 * @return mixed|bool - Returns the data on success; false on failure
	 */
	abstract public function read($key);
	
	/**
	 * Atomically update a counter value stored at $key
	 *
	 * @param string $key
	 * @return void
	 */
	abstract public function increment($key);

	/**
	 * Atomically update session data
	 *
	 * @param callable $cb - The callback that modifies the locked contents
	 * @return mixed|bool
	 */
	abstract public function update(callable $cb);

	/**
	 * Initialise the session implementation
	 *
	 * @param array $config
	 * @return void
	 */
	abstract protected function init(array $config);

	/**
	 * Generates the name for the particular session
	 *
	 * @return string
	 */
	protected function get_session_name() {
		return str_replace(array('.', ':'), '_', \AIOS_Helper::normalise_local_ip(\AIOS_Helper::get_user_ip_address()));
	}
}
