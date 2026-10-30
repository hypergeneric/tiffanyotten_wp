<?php
namespace AIOWPS\Firewall;

defined('AIOWPS_FIREWALL_DIR') || die('No direct access allowed.');

class Session_Utility {

	/**
	 * Get the salt used for the session storage
	 *
	 * @param boolean $generate - Whether to generate a new salt if one is not already found
	 * @return string
	 */
	public static function get_salt($generate = true) {
		global $aiowps_firewall_config;

		$salt = $aiowps_firewall_config->get_value('firewall_session_salt');

		if (empty($salt) && (bool) $generate) {
			$salt = Utility::generate_salt();
			$aiowps_firewall_config->set_value('firewall_session_salt', $salt);
		}

		return $salt;
	}

}
