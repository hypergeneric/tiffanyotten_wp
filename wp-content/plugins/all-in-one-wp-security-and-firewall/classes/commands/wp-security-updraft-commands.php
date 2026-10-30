<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (trait_exists('AIOWPSecurity_Updraft_Commands_Trait')) return;

trait AIOWPSecurity_Updraft_Commands_Trait {

	/**
	 * Instance of AIOWPSecurity_Updraft_Connect
	 *
	 * @var AIOWPSecurity_Updraft_Connect
	 */
	private $updraft_connect;

	/**
	 * Lazily instantiate and return the Updraft connect instance.
	 *
	 * A getter is used rather than a constructor because this trait is consumed by
	 * AIOWPSecurity_Commands, which defines its own constructor; a trait constructor
	 * would be silently overridden and never run.
	 *
	 * @return AIOWPSecurity_Updraft_Connect
	 */
	private function get_updraft_connect() {

		if (!isset($this->updraft_connect)) {
			if (!class_exists('AIOWPSecurity_Updraft_Connect')) require_once(AIO_WP_SECURITY_PATH.'/classes/includes/wp-security-updraft-connect.php');

			$this->updraft_connect = new AIOWPSecurity_Updraft_Connect();
		}

		return $this->updraft_connect;
	}

	/**
	 * Performs the login process for Updraft
	 *
	 * For an unknown email the server responds with the 'email_not_registered' code; we
	 * then run the registration with the same data so the whole connect happens in a single
	 * request (no second round-trip from the browser).
	 *
	 * @param array $data The submitted form data
	 *
	 * @return array
	 */
	public function perform_updraft_login($data) {
		$response = $this->get_updraft_connect()->login($data);

		if (isset($response['status']) && 'authenticated' === $response['status']) {
			return $this->handle_updraft_connected($response, $data, __('Successfully authenticated.', 'all-in-one-wp-security-and-firewall'));
		}

		// No account yet for this email - register it in the same request.
		if (isset($response['code']) && 'email_not_registered' === $response['code']) {
			return $this->perform_updraft_register($data);
		}

		return $this->handle_updraft_connect_error($response);
	}

	/**
	 * Performs the registration process for Updraft
	 *
	 * @param array $data The submitted form data
	 *
	 * @return array
	 */
	public function perform_updraft_register($data) {
		$response = $this->get_updraft_connect()->register($data);

		if (isset($response['status']) && 'registered' === $response['status']) {
			return $this->handle_updraft_connected($response, $data, __('A new account was created and connected successfully.', 'all-in-one-wp-security-and-firewall'));
		}

		return $this->handle_updraft_connect_error($response);
	}

	/**
	 * Stores the connection credentials and returns the connected-state response.
	 *
	 * @param array  $response The normalised response from the connect class
	 * @param array  $data     The submitted form data
	 * @param string $message  The success message to show the user
	 *
	 * @return array
	 */
	private function handle_updraft_connected($response, $data, $message) {
		global $aio_wp_security;

		$connected_email = sanitize_email($data['email']);

		$this->save_settings(array(
			AIOWPSecurity_Updraft_Connect::TOKEN_OPTION_KEY => sanitize_text_field($response['redirect_token']),
			AIOWPSecurity_Updraft_Connect::EMAIL_OPTION_KEY => $connected_email
		));

		$args = array(
			'content' => array(
				'aios_updraft_form_container' => $aio_wp_security->include_template('wp-admin/settings/partials/updraft-login-connected.php', true, array(
					'updraft_connected_email' => $connected_email,
				))
			),
			'extra_args' => $response
		);

		return $this->handle_response(true, $message, $args);
	}

	/**
	 * Builds the failure/intermediate response for a login or registration attempt.
	 *
	 * No content is returned so the form is not swapped out. When the account requires a
	 * two-factor code we prompt for it; otherwise the server's error message is surfaced.
	 * The full response travels back in 'extra_args' so the client can branch on the
	 * 'code' and 'tfa_enabled' values.
	 *
	 * @param array $response The normalised response from the connect class
	 *
	 * @return array
	 */
	private function handle_updraft_connect_error($response) {
		if (!empty($response['tfa_enabled'])) {
			$message = __('Please enter your two-factor authentication code to continue.', 'all-in-one-wp-security-and-firewall');
		} elseif (isset($response['message'])) {
			$message = $response['message'];
		} else {
			$message = __('An unknown status was returned from the server.', 'all-in-one-wp-security-and-firewall');
		}

		return $this->handle_response(false, $message, array('extra_args' => $response));
	}

	/**
	 * Disconnects the current site from Updraft
	 *
	 * @return array
	 */
	public function perform_updraft_disconnect() {
		global $aio_wp_security;

		// Ensures the AIOWPSecurity_Updraft_Connect class is loaded so its option-key
		// constants are available below.
		$this->get_updraft_connect();

		$this->save_settings(array(
			AIOWPSecurity_Updraft_Connect::TOKEN_OPTION_KEY => '',
			AIOWPSecurity_Updraft_Connect::EMAIL_OPTION_KEY => ''
		));

		$args = array(
			'content' => array(
				'aios_updraft_form_container' => $aio_wp_security->include_template('wp-admin/settings/partials/updraft-login-form.php', true, array())
			),
		);

		return $this->handle_response(true, __('Successfully disconnected.', 'all-in-one-wp-security-and-firewall'), $args);
	}
}
