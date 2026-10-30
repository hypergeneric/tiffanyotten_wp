<?php

if (!defined('ABSPATH')) die('No direct access allowed.');

/**
 * Class to utilize the 'Have I Been Pwned?' API.
 */
if (class_exists('AIOS_HIBP')) return;

class AIOS_HIBP {

	/**
	 * Checks a password against the HIBP database.
	 *
	 * @param string $password
	 *
	 * @return bool
	 */
	public static function password_is_pwned($password) {
		global $aio_wp_security;

		$password_hash = sha1($password);
		$password_hash_prefix = substr($password_hash, 0, 5);
		$password_hash_suffix = substr($password_hash, 5);

		$response_body = AIOWPSecurity_Utility_API::get('https://api.pwnedpasswords.com/range/' . $password_hash_prefix);

		if (is_wp_error($response_body)) {
			$aio_wp_security->debug_logger->log_debug('Failed to query the HIBP api: ' . $response_body->get_error_message(), 4);
			return false;
		}

		$response_body_array = explode("\n", $response_body);

		foreach ($response_body_array as $suffix_and_count) {
			$suffix = explode(':', $suffix_and_count)[0];

			if (strtolower($suffix) == $password_hash_suffix) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if a password has been pwned when updating a user profile.
	 *
	 * @param WP_Error $errors
	 * @param bool     $update
	 * @param stdClass $user
	 *
	 * @return void
	 */
	public static function user_profile_update_check(&$errors, $update, &$user) {
		// Use get_error_code() instead of has_errors() for backward compatibility with WP 5.0.
		if ($errors->get_error_code() || empty($user->user_pass)) {
			return;
		}

		if (self::password_is_pwned($user->user_pass)) {
			$errors->add('pass', sprintf(
				/* Translators: 1: opening <strong> tag 2: closing <strong> tag */
				esc_html__('%1$sError:%2$s This password has been exposed in a data breach, according to Have I Been Pwned (HIBP).', 'all-in-one-wp-security-and-firewall'),
				'<strong>',
				'</strong>'
			));
		}
	}

	/**
	 * Checks if a password has been pwned when resetting a password.
	 *
	 * @param WP_Error $errors
	 *
	 * @return void
	 */
	public static function password_reset_check($errors) {
		// Use get_error_code() instead of has_errors() for backward compatibility with WP 5.0.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Not processing anything here
		if ($errors->get_error_code() || !isset($_POST['pass1']) || empty($_POST['pass1'])) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- No nonce available, not recommended to sanitize passwords.
		if (self::password_is_pwned(wp_unslash($_POST['pass1']))) {
			$errors->add('pass', sprintf(
				/* Translators: 1: opening <strong> tag 2: closing <strong> tag */
				esc_html__('%1$sError:%2$s This password has been exposed in a data breach, according to Have I Been Pwned (HIBP).', 'all-in-one-wp-security-and-firewall'),
				'<strong>',
				'</strong>'
			));
		}
	}
}
