<?php
if (!defined('ABSPATH') && !defined('AIOWPS_FIREWALL_DIR')) {
	exit; //Exit if accessed directly
}

require_once(__DIR__.'/wp-security-abstract-ids.php');

/**
 * Helper class for firewall as shared library
 * Use core php not wordpress.
 */
if (class_exists('AIOS_Helper')) return;

class AIOS_Helper {

	private static $messages = array();

	/**
	 * Constants for the different return values of `is_ip_address_within_list`.
	 *
	 * @var int
	 */
	const IP_NOT_IN_LIST = 0;
	const IP_EXACT_MATCH = 1;
	const IP_RANGE_MATCH = 2;

	/**
	 * Maps a firewall rule to its admin URL
	 *
	 * @param string $rule - The key to the rule's URL. The key format is '<rule_name>::<rule_family>'
	 * @return string
	 */
	public static function get_firewall_rule_location($rule) {
		//normalise key
		$rule = strtolower($rule);

		$basic_firewall = array(
			'completely block xmlrpc::general' => 'page=aiowpsec_firewall&tab=php-rules',
		);
		$additional_firewall = array(
			'advanced character filter::general' => 'page=aiowpsec_firewall&tab=php-rules&subtab=advanced-character-filter',
			'bad query strings::general' => 'page=aiowpsec_firewall&tab=php-rules&subtab=bad-query-strings',
			'proxy comment posting::general' => 'page=aiowpsec_firewall&tab=php-rules&subtab=proxy-comment',
		);
		$bruteforce = array(
			'cookie based prevent bruteforce::bruteforce' => 'page=aiowpsec_brute_force&tab=cookie-based-brute-force-prevention',
		);
		$blacklist = array(
			'blocked ips::blacklist' => 'page=aiowpsec_firewall&tab=block-and-allow-lists',
			'blocked user agents::blacklist' => 'page=aiowpsec_firewall&tab=block-and-allow-lists',
		);
		$firewall_6g = array(
			'block request methods::6g' => 'page=aiowpsec_firewall&tab=php-rules&subtab=ng',
			'block query strings::6g' => 'page=aiowpsec_firewall&tab=php-rules&subtab=ng',
			'block referrer strings::6g' => 'page=aiowpsec_firewall&tab=php-rules&subtab=ng',
			'block request strings::6g' => 'page=aiowpsec_firewall&tab=php-rules&subtab=ng',
			'block user-agents::6g' => 'page=aiowpsec_firewall&tab=php-rules&subtab=ng',
		);

		// merge all the locations to one
		$locations = array_merge($firewall_6g, $blacklist, $bruteforce, $basic_firewall, $additional_firewall);
		return isset($locations[$rule]) ? $locations[$rule] : '';
	}
	
	/**
	 * Get server detected visitor IP Address.
	 *
	 * @return String visitor IP Address.
	 */
	public static function get_server_detected_user_ip_address() {
		$aiowps_firewall_config = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);

		// check if user configured custom IP retrieval method
		$ip_method_id = empty($aiowps_firewall_config) ? '' : $aiowps_firewall_config->get_value('aios_ip_retrieve_method');

		$visitor_ip = '';
		$ip_retrieve_methods = AIOS_Abstracted_Ids::get_ip_retrieve_methods();

		if (empty($ip_method_id) || !isset($ip_retrieve_methods[$ip_method_id])) {
			$ip_method_id = 0;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- WordPress API cannot be used here as it's loaded independently of WordPress
		$visitor_ip = isset($_SERVER[$ip_retrieve_methods[$ip_method_id]]) ? $_SERVER[$ip_retrieve_methods[$ip_method_id]] : '';

		// Check if multiple IPs were given - these will be present as comma-separated list
		if (preg_match('/^([^,]+),/', $visitor_ip, $matches)) $visitor_ip = $matches[1];

		// Now remove port portion if ipv4 address with port, for ipv6 it was making issue so using filter_var valid ip checked first.
		if (!filter_var($visitor_ip, FILTER_VALIDATE_IP) && preg_match('/(.+):\d+$/', $visitor_ip, $matches)) $visitor_ip = $matches[1];

		if (!filter_var($visitor_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($visitor_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- WordPress API cannot be used here as it's loaded independently of WordPress
			$visitor_ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
		}

		return $visitor_ip;
	}
	
	/**
	 * Get user IP Address.
	 *
	 * @return string User IP Address.
	 */
	public static function get_user_ip_address() {
		static $visitor_ip;
		if (isset($visitor_ip)) {
			//already set in the page request
			return $visitor_ip;
		}
		
		$visitor_ip = self::get_server_detected_user_ip_address();
		if (in_array($visitor_ip, array('', '127.0.0.1', '::1'))) {
			$external_ip_address = self::get_external_ip_address();
			if (false != $external_ip_address) {
				$visitor_ip = $external_ip_address;
			}
		}

		return $visitor_ip;
	}

	/**
	 * Normalise all local IP addresses to the value stored in $normalise_value.
	 *
	 * @param string $ip              - The IP address to check and normalise
	 * @param string $normalise_value - The value to normalise the IP address
	 * @return string
	 */
	public static function normalise_local_ip($ip, $normalise_value = 'localhost') {
		return self::is_ip_local($ip) ? $normalise_value : $ip;
	}

	/**
	 * Determines whether the IP address is a local address (either a loopback address or localhost)
	 *
	 * @param string $ip - The IP address to check
	 * @return boolean|null
	 */
	public static function is_ip_local($ip) {

		if (strtolower($ip) === 'localhost') return true;

		if (!(include_once AIOWPS_FIREWALL_DIR.'/../../vendor/mlocati/ip-lib/ip-lib.php')) return null;

		$check_ip = \IPLib\Factory::parseAddressString($ip);

		if (is_null($check_ip)) return null;

		return $check_ip->getRangeType() === \IPLib\Range\Type::T_LOOPBACK;
	}

	/**
	 * Checks if a range contains an IPvX address.
	 * This can also be used to compare IPvX addresses since IPLib has a `\IPLib\Range\Single` type.
	 *
	 * @param \IPLib\Range\AbstractRange|void      $range
	 * @param \IPLib\Address\AddressInterface|void $ip
	 *
	 * @return int
	 */
	public static function range_contains_ip($range, $ip) {
		if (null == $range || null == $ip) return self::IP_NOT_IN_LIST;

		if (6 == $range->getAddressType() && 4 == $ip->getAddressType()) {
			$range = \IPLib\Factory::getRangeFromBoundaries($range->getStartAddress()->toIPv4(), $range->getEndAddress()->toIPv4());
		} elseif (4 == $range->getAddressType() && 6 == $ip->getAddressType()) {
			$ip = $ip->toIPv4();
		}

		if (null == $range || null == $ip) return self::IP_NOT_IN_LIST;

		if ($range->contains($ip)) {
			if ($range instanceof \IPLib\Range\Single) {
				return self::IP_EXACT_MATCH;
			} else {
				return self::IP_RANGE_MATCH;
			}
		} else {
			return self::IP_NOT_IN_LIST;
		}
	}

	/**
	 * Check if IP address is within a given list.
	 *
	 * @param string $needle_ip
	 * @param array  $ip_address_list
	 *
	 * @return int
	 */
	public static function is_ip_address_within_list($needle_ip, $ip_address_list) {
		if (empty($ip_address_list)) return self::IP_NOT_IN_LIST;

		if (!(include_once AIOWPS_FIREWALL_DIR.'/../../vendor/mlocati/ip-lib/ip-lib.php')) {
			throw new \Exception("AIOS_Helper::is_ip_address_within_list failed to load ip-lib.php");
		}

		$needle_iplib_address = \IPLib\Factory::parseAddressString($needle_ip);

		$exact_match = false;

		foreach ($ip_address_list as $ip_address) {
			if ($ip_address == $needle_ip) {
				$exact_match = true; // Not returning immediately because there might be a range match further into the array.
			} else {
				switch (self::range_contains_ip(\IPLib\Factory::parseRangeString($ip_address), $needle_iplib_address)) {
					case self::IP_RANGE_MATCH:
						return self::IP_RANGE_MATCH;
					case self::IP_EXACT_MATCH:
						$exact_match = true; // Not returning immediately because there might be a range match further into the array.
				}
			}
		}

		return $exact_match ? self::IP_EXACT_MATCH : self::IP_NOT_IN_LIST;
	}

	/**
	 * Check if user IP Address is within a given list.
	 *
	 * @param array $ip_address_list
	 *
	 * @return boolean
	 */
	public static function is_user_ip_address_within_list($ip_address_list) {
		if (empty($ip_address_list)) return false;

		return self::is_ip_address_within_list(self::get_user_ip_address(), $ip_address_list) != self::IP_NOT_IN_LIST;
	}

	/**
	 * Get user IP Address using an external service.
	 * This can be used as a fallback for users on localhost where
	 * get_ip_address() will be a local IP and non-geolocatable.
	 *
	 * @return string|boolean external ip address if from one of lookup service gets response otherwise false.
	 */
	public static function get_external_ip_address() {
		$aiowps_firewall_constants = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONSTANTS);
		$external_ip_address = false;
		
		//Running from cronjob REQUEST_URI is empty or DOING_CRON, if from command line cli is PHP_SAPI, constant set by user
		if (empty($_SERVER['REQUEST_URI']) || (defined('DOING_CRON') && DOING_CRON) || 'cli' == PHP_SAPI || $aiowps_firewall_constants->AIOS_DISABLE_EXTERNAL_IP_ADDR) return $external_ip_address;
		
		$ip_lookup_services = AIOS_Abstracted_Ids::get_ip_lookup_services();
		$ip_lookup_services_keys = array_keys($ip_lookup_services);
		shuffle($ip_lookup_services_keys);

		foreach ($ip_lookup_services_keys as $service_name) {
			$service_endpoint = $ip_lookup_services[$service_name];
			$response = self::request_remote($service_endpoint);
			if (!empty($response) && filter_var($response, FILTER_VALIDATE_IP)) {
				$external_ip_address = $response;
				break;
			}
		}
		return $external_ip_address;
	}
	
	/**
	 * Remote url request.
	 *
	 * @global \AIOWPS\Firewall\Constants $aiowps_firewall_constants
	 *
	 * @param string $url  url to be requested.
	 * @param array  $args request args array.
	 *
	 * @return string response.
	 */
	public static function request_remote($url, $args = array()) {
		$aiowps_firewall_constants = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONSTANTS);
		$response = '';
		$aiowps_firewall_utility = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::UTILITY);
		$root = $aiowps_firewall_utility::get_root_dir();
		$includes = isset($aiowps_firewall_constants->WPINC) ? $aiowps_firewall_constants->WPINC : 'wp-includes';
		
		//WP 6.2+ the request library updated to 2.0.5, class and interface files/directory moved and namespaced name used.
		if (!class_exists('WpOrg\Requests\Autoload')) {
			if (file_exists($root . $includes . '/Requests/src/Autoload.php')) {
				require_once($root . $includes . '/Requests/src/Autoload.php');
				WpOrg\Requests\Autoload::register();
				WpOrg\Requests\Requests::set_certificate_path($root . $includes . '/certificates/ca-bundle.crt');
			} elseif (!class_exists('Requests') && file_exists($root . $includes . '/class-requests.php')) {
				require_once($root . $includes . '/class-requests.php');
				Requests::register_autoloader();
				Requests::set_certificate_path($root . $includes . '/certificates/ca-bundle.crt');
			}
		}
		$timeout = isset($aiowps_firewall_constants->AIOS_REQUEST_TIMEOUT) ? $aiowps_firewall_constants->AIOS_REQUEST_TIMEOUT : 4;
		
		try {
			$headers = isset($args['headers']) ? $args['headers'] : array();
			$data = isset($args['body']) ? $args['body'] : array();
			$method = isset($args['method']) ? $args['method'] : 'GET';
			$options = array('timeout' => $timeout);
			//WP 6.2+ the request library 2.0.5 namespaced name WpOrg\Requests\Requests instead just Requests
			if (class_exists('WpOrg\Requests\Requests')) {
				$request_response = WpOrg\Requests\Requests::request($url, $headers, $data, $method, $options);
			} elseif (class_exists('Requests')) {
				$request_response = Requests::request($url, $headers, $data, $method, $options);
			}
			$response = $request_response->body;
		} catch (\Exception $e) {
			$error_message = $e->getMessage();
			// timed out exception ignore it.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- PCP warning. Part of AIOS error reporting.
			if (!preg_match('/timed out/i', $error_message)) error_log('AIOS_Helper::request_remote exception - ' . $error_message);
		} catch (\Error $e) { // phpcs:ignore PHPCompatibility.Classes.NewClasses.errorFound -- this won't run on PHP 5.6 so we still want to catch it on other versions
			error_log('AIOS_Helper::request_remote error - ' . $e->getMessage()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- PCP warning. Part of AIOS error reporting.
		}
		
		if (empty($response) && ini_get('allow_url_fopen')) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Cannot use WP API since Firewall is loaded outside WordPress.
			$response = @file_get_contents($url, false, stream_context_create(array('http' => array("timeout" => $timeout)))); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this to silence request failed warning for IP lookup services
		}
		return $response;
	}

	/**
	 * Performs reverse IP lookup for the given IP address
	 *
	 * @param string $ip_address - IP address to perform reverse lookup
	 *
	 * @return array - Reverse lookup data
	 */
	public static function get_ip_reverse_lookup($ip_address) {
		global $aio_wp_security;
		$reverse_lookup_data = array();
		$ip_reverse_lookup_services = AIOS_Abstracted_Ids::get_reverse_ip_lookup_services();
		$ip_reverse_lookup_services_keys = array_keys($ip_reverse_lookup_services);
		shuffle($ip_reverse_lookup_services_keys);

		foreach ($ip_reverse_lookup_services_keys as $service_name) {
			$endpoint = $ip_reverse_lookup_services[$service_name];
			$url = sprintf($endpoint, $ip_address);
			$response = wp_safe_remote_get($url, array( 'timeout' => 2 ));

			if (!is_wp_error($response) && $response['body']) {
				$data = json_decode($response['body'], true);
				if (!$data) {
					$aio_wp_security->debug_logger->log_debug("Error decoding IP lookup result", 4);
					return $reverse_lookup_data;
				}
				switch ($service_name) {
					case 'ip-api':
					$fields_to_copy = array('org', 'as');
					foreach ($fields_to_copy as $field) {
							$reverse_lookup_data[$field] = empty($data[$field]) ? null : $data[$field];
					}
						break;
					case 'ipinfo':
					$reverse_lookup_data['org'] = empty($data['org']) ? null : $data['org'];
					$reverse_lookup_data['as'] = $reverse_lookup_data['org'];
						break;
					default:
						break;
				}

				$reverse_lookup_data = apply_filters('aios_login_lockdown_lookup_result', $reverse_lookup_data, $data, $service_name);
				break;
			}
		}

		return $reverse_lookup_data;
	}

	/**
	 * Gets hash of given string using auth Authentication scheme
	 *
	 * @param string $data - Plain text to hash.
	 * @param string $algo - Algorithm for hash.
	 * @param string $salt - Salt to hash.
	 *
	 * @return string - Hash of $data
	 */
	public static function get_hash($data, $algo = 'md5', $salt = '') {
		$aiowps_firewall_constants = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONSTANTS);
		if (empty($salt)) $salt = $aiowps_firewall_constants->AUTH_KEY.$aiowps_firewall_constants->AUTH_SALT;
		return hash_hmac($algo, $data, $salt);
	}

	/**
	 * Set a message for a specific context.
	 *
	 * @param string $context The unique context identifier for the message.
	 * @param string $message The message to store for the given context.
	 * @param string $type    The message type to store for the given context.
	 *
	 * @return void
	 */
	public static function set_message($context, $message, $type = 'info') {
		self::$messages[$context] = array('message' => $message, 'type' => $type);
	}

	/**
	 * Get a message for a specific context.
	 *
	 * @param string $context The unique context identifier for the message.
	 *
	 * @return array|null The message for the given context, or null if not found.
	 */
	public static function get_message($context) {
		if (!isset(self::$messages[$context])) {
			return null;
		}

		$message = self::$messages[$context];

		if (isset($message['message'])) {
			$message['message'] = nl2br(esc_html($message['message']));
		}

		return $message;
	}

	/**
	 * Clear messages (optional, for cleanup purposes).
	 *
	 * @return void
	 */
	public static function clear_messages() {
		self::$messages = array();
	}

	/**
	 * This function checks if the current request is an UpdraftCentral request by looking for a specific constant.
	 *
	 * @return boolean - True if the request is from UpdraftCentral, false otherwise.
	 */
	public static function is_updraft_central_request() {
		return defined('UPDRAFTCENTRAL_COMMAND') && UPDRAFTCENTRAL_COMMAND;
	}
}
