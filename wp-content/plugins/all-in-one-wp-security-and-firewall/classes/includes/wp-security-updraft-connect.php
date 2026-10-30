<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('AIOWPSecurity_Updraft_Connect')) return;

/**
 * Handles the login communication with the TeamUpdraft Cloud.
 *
 * Modelled on UpdraftPlus_Login: a request is posted to the mothership
 * with a udm_action and the response is pre-checked and wrapped before
 * being handed back to the caller.
 */
class AIOWPSecurity_Updraft_Connect {

	/**
	 * Option key for storing the API token
	 */
	const TOKEN_OPTION_KEY = 'aiowps_updraft_redirect_token';

	/**
	 * Option key for storing the connected email
	 */
	const EMAIL_OPTION_KEY = 'aiowps_updraft_connected_email';

	/**
	 * Request type for an existing-account login
	 */
	const TYPE_LOGIN = 'login';

	/**
	 * Request type for creating a new account
	 */
	const TYPE_REGISTER = 'register';

	/**
	 * Constructor for the class
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter('aios_updraft_connect_data', array($this, 'add_site_url_to_data'));
	}

	/**
	 * Adds the site URL to the data being sent to the API
	 *
	 * @param array $data The data to be sent
	 *
	 * @return array
	 */
	public function add_site_url_to_data($data) {
		if (empty($data['site_url'])) {
			$data['site_url'] = trailingslashit(network_site_url());
		}
		return $data;
	}

	/**
	 * Get the base (mothership) URL for the TeamUpdraft Cloud API
	 *
	 * @return string
	 */
	private function get_url() {
		return apply_filters('aios_updraft_connect_url', AIOWPS_UPDRAFT_CONNECT_URL);
	}

	/**
	 * Get the stored authentication token
	 *
	 * @return string|null
	 */
	private function get_auth_token() {
		global $aio_wp_security;
		$token = $aio_wp_security->configs->get_value(self::TOKEN_OPTION_KEY);
		return !empty($token) ? $token : null;
	}

	/**
	 * Pulls the appropriate message for the given code and translate it before
	 * returning it to the caller
	 *
	 * @param string $code The code of the message to pull
	 *
	 * @return string The translated message
	 */
	protected function translate_message($code) {
		switch ($code) {
			case 'generic':
			default:
				return __('An error has occurred while processing your request.', 'all-in-one-wp-security-and-firewall') . ' ' .
					__('The server might be busy or you have lost your connection to the internet at the time of the request.', 'all-in-one-wp-security-and-firewall') . ' ' .
					__('Please try again later.', 'all-in-one-wp-security-and-firewall');
		}
	}

	/**
	 * Handles the actual sending of the request to the remote server (TeamUpdraft Cloud) and pre-checks
	 * the response, wrapping it up for the caller before sending it back
	 *
	 * @param array  $data   The submitted form data
	 * @param string $action The name of the action or service that will be triggered in the TeamUpdraft Cloud
	 *
	 * @return array|WP_Error The response from the server, or a WP_Error on failure
	 */
	protected function send_remote_request($data, $action) {
		$token = $this->get_auth_token();

		$headers = array();
		if (!empty($token)) {
			$headers['X-TUAPI-TOKEN'] = $token;
		}

		$result = wp_remote_post(trailingslashit($this->get_url()) . '?udm_action=' . $action,
			array(
				'timeout' => 20,
				'headers' => apply_filters('aios_updraft_connect_auth_headers', $headers),
				'body'    => $data,
			)
		);

		// If we got an error then we return the WP_Error object itself
		// and let the caller handle it.
		if (is_wp_error($result)) return $result;

		$body = wp_remote_retrieve_body($result);
		$response = json_decode($body, true);

		if (!is_array($response) || !isset($response['mothership']) || !isset($response['status'])) {

			if (preg_match('/has banned your IP address \(([\.:0-9a-f]+)\)/', $body, $matches)) {
				return new WP_Error('banned_ip', sprintf(
					__("TeamUpdraft.com has responded with 'Access Denied'.", 'all-in-one-wp-security-and-firewall') . '<br>' .
					/* translators: %s: Server IP address */
					__("It appears that your web server's IP Address (%s) is blocked.", 'all-in-one-wp-security-and-firewall') . ' ' .
					__('This most likely means that you share a webserver with a hacked website that has been used in previous attacks.', 'all-in-one-wp-security-and-firewall') .
					'<br> <a href="' . esc_url(apply_filters('updraftplus_com_link', 'https://teamupdraft.com/blog/unblock-ip-address/')) . '" target="_blank">' .
					__('To remove the block, please go here.', 'all-in-one-wp-security-and-firewall') . '</a> ',
					$matches[1]
				));
			} else {
				/* translators: %s: Response data from TeamUpdraft.com */
				return new WP_Error('unknown_response', sprintf(__('TeamUpdraft.com returned a response which we could not understand (data: %s)', 'all-in-one-wp-security-and-firewall'), $body));
			}
		}

		return $response;
	}

	/**
	 * Executes the login process. Connects and sends a request to the TeamUpdraft Cloud
	 * and returns the response coming from the server.
	 *
	 * @param array $data The submitted form data containing:
	 *                    - email: User's email address
	 *                    - password: User's password
	 *
	 * @return array Response array with status and token on success, or error details
	 */
	public function login($data) {
		return $this->login_or_register($data, self::TYPE_LOGIN);
	}

	/**
	 * Executes the registration process. Connects and sends a request to the TeamUpdraft
	 * Cloud to create a new account and returns the response coming from the server.
	 *
	 * @param array $data The submitted form data containing:
	 *                    - email: User's email address
	 *                    - password: User's password
	 *
	 * @return array Response array with status and token on success, or error details
	 */
	public function register($data) {
		return $this->login_or_register($data, self::TYPE_REGISTER);
	}

	/**
	 * Connects and sends a login or registration request to the TeamUpdraft Cloud and
	 * normalises the response for the caller.
	 *
	 * @param array  $data The submitted form data
	 * @param string $type The request type: self::TYPE_LOGIN or self::TYPE_REGISTER. Defaults to login.
	 *
	 * @return array Response array with status and token on success, or error details
	 */
	private function login_or_register($data, $type = self::TYPE_LOGIN) {
		global $aio_wp_security;

		$data = apply_filters('aios_updraft_connect_data', $data);

		$is_register = self::TYPE_REGISTER === $type;
		$action = $is_register ? 'aiowps_user_register' : 'aiowps_user_login';

		$response = $this->send_remote_request($data, $action);

		if (is_wp_error($response)) {
			$aio_wp_security->debug_logger->log_debug(
				'Updraft Connect ' . ($is_register ? 'Register' : 'Login') . ' Error: ' . $response->get_error_message(),
				4
			);
			return array(
				'error'   => true,
				'code'    => $response->get_error_code(),
				'message' => $response->get_error_message(),
			);
		}

		// At this point send_remote_request() has guaranteed a 'status' key.
		switch ($response['status']) {
			case 'authenticated':
			case 'registered':
				// Successful connection; the auth credential is returned in 'redirect_token'.
				return array(
					'status'         => $response['status'],
					'redirect_token' => isset($response['redirect_token']) ? $response['redirect_token'] : '',
					'tfa_enabled'    => !empty($response['tfa_enabled']),
					'mothership'     => true, // For backwards compatibility
				);

			default:
				// Any non-success response is an error from the caller's point of view.
				// The 'tfa_enabled' flag is surfaced so the caller can prompt for a
				// two-factor code when the account requires one, regardless of the exact
				// status the server used.
				return array(
					'error'       => true,
					'code'        => isset($response['code']) ? $response['code'] : 'api_error',
					'message'     => isset($response['message']) ? $this->to_plain_text($response['message']) : $this->translate_message('generic'),
					'tfa_enabled' => !empty($response['tfa_enabled']),
				);
		}
	}

	/**
	 * Reduces a server-provided message (which may contain HTML) to plain text.
	 *
	 * The AJAX layer escapes messages before display, so any markup the cloud sends would
	 * otherwise be shown as literal tags. Stripping it here keeps the message readable and
	 * avoids passing untrusted markup towards the DOM.
	 *
	 * @param string $message The raw message from the server
	 *
	 * @return string
	 */
	private function to_plain_text($message) {
		$message = wp_strip_all_tags(html_entity_decode($message, ENT_QUOTES));
		return trim(preg_replace('/\s+/', ' ', $message));
	}

	/**
	 * Check if the user is currently connected/authenticated
	 *
	 * @return bool
	 */
	public function is_connected() {
		return !empty($this->get_auth_token());
	}

	/**
	 * Get the currently connected email address
	 *
	 * @return string|null
	 */
	public function get_connected_email() {
		global $aio_wp_security;
		$email = $aio_wp_security->configs->get_value(self::EMAIL_OPTION_KEY);
		return !empty($email) ? $email : null;
	}
}
