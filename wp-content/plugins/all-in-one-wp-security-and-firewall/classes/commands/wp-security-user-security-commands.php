<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (trait_exists('AIOWPSecurity_User_Security_Commands_Trait')) return;

trait AIOWPSecurity_User_Security_Commands_Trait {

	/**
	 * Renders the logged-in users table content.
	 *
	 * This function processes the POST data if available and initializes the
	 * AIOWPSecurity_List_Logged_In_Users class to render the list of currently
	 * logged-in users. The list is displayed via an AJAX response.
	 *
	 * @param array $data Logged-in users data.
	 *
	 * @return void
	 */
	public function render_logged_in_users_tab($data) {

		$data = empty($data) ? array() : $data;

		// Needed for rendering the logged in users table
		include_once(AIO_WP_SECURITY_PATH.'/admin/wp-security-list-logged-in-users.php');
		$logged_in_users_list = new AIOWPSecurity_List_Logged_In_Users($data);
		$logged_in_users_list->ajax_response();
	}

	/**
	 * Saves user account security settings.
	 *
	 * This function updates security settings related to user enumeration prevention
	 * and strong password enforcement in the AIO WP Security plugin.
	 *
	 * @param array $data An associative array containing the security settings:
	 *                    - 'aiowps_prevent_users_enumeration' (optional): Set to '1' to prevent user enumeration.
	 *                    - 'aiowps_enforce_strong_password' (optional): Set to '1' to enforce strong passwords.
	 *                    - 'aiowps_hibp_user_profile_update' (optional): Set to '1' to enforce HIBP check on profile password change.
	 *                    - 'aiowps_http_password_reset' (optional): Set to '1' to enforce HIBP check on user password reset.
	 * @return array The response array containing:
	 *               - 'badges' (array): A list of applied security badges.
	 */
	public function perform_save_user_account_settings($data) {
		global $aio_wp_security;

		// Save settings
		$aio_wp_security->configs->set_value('aiowps_prevent_users_enumeration', isset($data["aiowps_prevent_users_enumeration"]) ? '1' : '', true);
		$aio_wp_security->configs->set_value('aiowps_enforce_strong_password', isset($data['aiowps_enforce_strong_password']) ? '1' : '', true);
		$aio_wp_security->configs->set_value('aiowps_hibp_user_profile_update', isset($data['aiowps_hibp_user_profile_update']) ? '1' : '', true);
		$aio_wp_security->configs->set_value('aiowps_http_password_reset', isset($data['aiowps_http_password_reset']) ? '1' : '', true);
		$aio_wp_security->configs->save_config();
		
		$badges = array('enforce-strong-password', 'disable-users-enumeration', 'hibp');

		return $this->handle_response(true, '', array('badges' => $badges));
	}

	/**
	 * Performs the action to change the admin username.
	 *
	 * @param array $data An array containing the data for changing the admin username.
	 *                    The array may contain the following keys:
	 *                    - 'aiowps_new_user_name': The new username to be set for the admin.
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               and a message indicating the result of the operation.
	 *               If the operation is successful, it also includes a badge representing the updated feature details.
	 */
	public function perform_change_admin_username($data) {
		global $wpdb, $aio_wp_security;

		$success = true;
		$message = __('The admin username has been changed successfully.', 'all-in-one-wp-security-and-firewall');
		$extra_args = array();

		if (!empty($data['aiowps_new_user_name'])) {
			$new_username = sanitize_text_field($data['aiowps_new_user_name']);
			if (validate_username($new_username)) {
				if (AIOWPSecurity_Utility::check_user_exists($new_username)) {
					$success = false;
					/* translators: %s: Username */
					$message = sprintf(__('Username: %s already exists, please enter another value.', 'all-in-one-wp-security-and-firewall'), $new_username);
				} else {
					// let's check if currently logged in username is 'admin'
					$user = wp_get_current_user();
					$user_login = $user->user_login;
					if ('admin' == strtolower($user_login)) {
						$username_is_admin = true;
					} else {
						$username_is_admin = false;
					}
					// Now let's change the username
					$result = $wpdb->query($wpdb->prepare("UPDATE `" . $wpdb->users . "` SET user_login = '" . esc_sql($new_username) . "' WHERE user_login=%s", "admin")); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- pcp check ignore this
					if (false === $result) {
						// There was an error updating the users table
						$message = __('The database update operation of the user account failed.', 'all-in-one-wp-security-and-firewall');
						$aio_wp_security->debug_logger->log_debug($message . ' ' . $wpdb->last_error, 4);
						return $this->handle_response(false, $message);
					}

					// multisite considerations
					if (is_multisite()) { // process sitemeta if we're in a multi-site situation
						$oldAdmins = $wpdb->get_var("SELECT meta_value FROM `" . $wpdb->sitemeta . "` WHERE meta_key = 'site_admins'"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- pcp check ignore this
						$newAdmins = str_replace('5:"admin"', strlen($new_username) . ':"' . esc_sql($new_username) . '"', $oldAdmins);
						$wpdb->query("UPDATE `" . $wpdb->sitemeta . "` SET meta_value = '" . esc_sql($newAdmins) . "' WHERE meta_key = 'site_admins'"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- pcp check ignore this
					}

					// If user is logged in with username "admin" then log user out and send to login page so they can login again
					if ($username_is_admin) {
						// Lets logout the user
						$aio_wp_security->debug_logger->log_debug("Logging user out with login ".$user_login. " because they changed their username.");
						$after_logout_url = AIOWPSecurity_Utility::get_current_page_url();
						$after_logout_payload = array('redirect_to' => $after_logout_url, 'msg' => $aio_wp_security->user_login_obj->key_login_msg.'=admin_user_changed');
						//Save some of the logout redirect data to a transient
						is_multisite() ? set_site_transient('aiowps_logout_payload', $after_logout_payload, 30 * 60) : set_transient('aiowps_logout_payload', $after_logout_payload, 30 * 60);

						$logout_url = AIOWPSEC_WP_URL.'?aiowpsec_do_log_out=1';
						$logout_url = AIOWPSecurity_Utility::add_query_data_to_url($logout_url, 'al_additional_data', '1');

						$extra_args['logout_user'] = true;
						$extra_args['logout_url'] = $logout_url;
					}
				}
			} else { // An invalid username was entered
				$success = false;
				$message = __('You entered an invalid username, please enter another value.', 'all-in-one-wp-security-and-firewall');
			}
		} else { // No username value was entered
			$success = false;
			$message = __('Enter a value for your username.', 'all-in-one-wp-security-and-firewall');
		}



		$args = array(
			'badges' => array('user-accounts-change-admin-user'),
			'content' => array(
				'change-admin-username-content' => $aio_wp_security->include_template('wp-admin/user-security/partials/wp-username-content.php', true)
			),
			'extra_args' => $extra_args
		);

		return $this->handle_response($success, $message, $args);
	}

	/**
	 * Performs the action to save the login lockout settings.
	 *
	 * @param array $data An array containing the data to be saved.
	 *
	 * @return array|WP_Error Returns an array containing the status of the operation ('success' or 'error'),
	 *               a message indicating the result of the operation,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_save_login_lockout_settings($data) {
		$invalid_fields = array();
		$info = array();

		$max_login_attempt_val = sanitize_text_field($data['aiowps_max_login_attempts']);
		if (!is_numeric($max_login_attempt_val) || 1 > $max_login_attempt_val) {
			$invalid_fields[] = 'max login attempts';
			$max_login_attempt_val = '3'; // Set it to the default value for this field
		}

		$login_retry_time_period = sanitize_text_field($data['aiowps_retry_time_period']);
		if (!is_numeric($login_retry_time_period) || 1 > $login_retry_time_period) {
			$invalid_fields[] = 'login retry time period';
			$login_retry_time_period = '5'; // Set it to the default value for this field
		}

		$lockout_time_length = sanitize_text_field($data['aiowps_lockout_time_length']);
		if (!is_numeric($lockout_time_length) || 1 > $lockout_time_length) {
			$invalid_fields[] = 'minimum lockout time length';
			$lockout_time_length = '5'; // Set it to the default value for this field
		}

		$max_lockout_time_length = sanitize_text_field($data['aiowps_max_lockout_time_length']);
		if (!is_numeric($max_lockout_time_length) || 1 > $max_lockout_time_length) {
			$invalid_fields[] = 'maximum lockout time length';
			$max_lockout_time_length = '60'; // Set it to the default value for this field
		}

		if ($lockout_time_length >= $max_lockout_time_length) {
			$invalid_fields[] = 'minimum lockout time length';
			$lockout_time_length = '5'; // Set it to the default value for this field
			$max_lockout_time_length = '60'; // Set it to the default value for this field
		}

		$email_addresses = isset($data['aiowps_email_address']) ? wp_unslash($data['aiowps_email_address']) : get_bloginfo('admin_email');
		$email_addresses_trimmed = AIOWPSecurity_Utility::explode_trim_filter_empty($email_addresses, "\n");
		// Read into array, sanitize, filter empty and keep only unique usernames.
		$email_address_list = array_unique(
			array_filter(
				array_map(
					'sanitize_email',
					$email_addresses_trimmed
				),
				'is_email'
			)
		);

		if (isset($data['aiowps_enable_email_notify']) && 1 == $data['aiowps_enable_email_notify'] && 0 == count($email_addresses_trimmed)) {
			$invalid_fields[] = 'email addresses';
		} elseif (isset($data['aiowps_enable_email_notify']) && 1 == $data['aiowps_enable_email_notify'] && (0 == count($email_address_list) || count($email_address_list) != count($email_addresses_trimmed))) {
			$invalid_fields[] = 'email addresses';
		}
		if (isset($data['aiowps_enable_email_notify']) && 0 == count($email_address_list)) {
			$email_address_list[] = get_bloginfo('admin_email');
		}

		// Instantly lockout specific usernames
		$instantly_lockout_specific_usernames = isset($data['aiowps_instantly_lockout_specific_usernames']) ? $data['aiowps_instantly_lockout_specific_usernames'] : '';
		// Read into array, sanitize, filter empty and keep only unique usernames.
		$instantly_lockout_specific_usernames = array_unique(
			array_filter(
				array_map(
					'sanitize_user',
					AIOWPSecurity_Utility::explode_trim_filter_empty($instantly_lockout_specific_usernames)
				),
				'strlen'
			)
		);

		if (!empty($invalid_fields)) {
			$invalid_fields = array_unique($invalid_fields);
			$invalid_fields = implode(", ", $invalid_fields);
			/* translators: %s: Options having invalid values */
			$info[] = sprintf(__('The following options had invalid values and have been set to the defaults: %s', 'all-in-one-wp-security-and-firewall'), $invalid_fields);
		}

		$options = array();

		// Save all the form values to the options
		$random_20_digit_string = AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20); // Generate random 20 char string for use during CAPTCHA encode/decode
		$options['aiowps_unlock_request_secret_key'] = $random_20_digit_string;

		$options['aiowps_enable_login_lockdown'] = isset($data["aiowps_enable_login_lockdown"]) ? '1' : '';
		$options['aiowps_allow_unlock_requests'] = isset($data["aiowps_allow_unlock_requests"]) ? '1' : '';
		$options['aiowps_max_login_attempts'] = absint($max_login_attempt_val);
		$options['aiowps_retry_time_period'] = absint($login_retry_time_period);
		$options['aiowps_lockout_time_length'] = absint($lockout_time_length);
		$options['aiowps_max_lockout_time_length'] = absint($max_lockout_time_length);
		$options['aiowps_set_generic_login_msg'] = isset($data["aiowps_set_generic_login_msg"]) ? '1' : '';
		$options['aiowps_enable_invalid_username_lockdown']= isset($data["aiowps_enable_invalid_username_lockdown"]) ? '1' : '';
		$options['aiowps_instantly_lockout_specific_usernames'] = $instantly_lockout_specific_usernames;
		$options['aiowps_enable_email_notify'] = isset($data["aiowps_enable_email_notify"]) ? '1' : '';
		$options['aiowps_enable_php_backtrace_in_email'] = isset($data['aiowps_enable_php_backtrace_in_email']) ? '1' : '';
		$options['aiowps_email_address'] = $email_address_list;
		$this->save_settings($options);

		$values = array();
		$values['aiowps_max_login_attempts'] = absint($max_login_attempt_val);
		$values['aiowps_retry_time_period'] = absint($login_retry_time_period);
		$values['aiowps_lockout_time_length'] = absint($lockout_time_length);
		$values['aiowps_max_lockout_time_length'] = absint($max_lockout_time_length);
		$values['aiowps_email_address'] = implode("\n", $email_address_list);

		$args = array(
			'badges' => array('user-login-login-lockdown'),
			'info' => $info,
			'values' => $values
		);

		return $this->handle_response(true, '', $args);
	}

	/**
	 * Performs the action to save the login lockout whitelist settings.
	 *
	 * @param array $data An array containing the data to be saved.
	 *                    The array may contain the following keys:
	 *                    - 'aiowps_lockdown_enable_whitelisting': A boolean indicating whether whitelisting is enabled.
	 *                    - 'aiowps_lockdown_allowed_ip_addresses': The allowed IP addresses for whitelisting.
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               a message indicating the result of the operation,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_save_login_lockout_whitelist_settings($data) {
		global $aio_wp_security;

		$options = array();
		$result = 1;
		$success = true;
		$message = '';

		if (!empty($data['aiowps_lockdown_allowed_ip_addresses'])) {
			$ip_addresses = sanitize_textarea_field(wp_unslash($data['aiowps_lockdown_allowed_ip_addresses']));
			$ip_list_array = AIOWPSecurity_Utility_IP::create_ip_list_array_from_string_with_newline($ip_addresses);
			$validated_ip_list_array = AIOWPSecurity_Utility_IP::validate_ip_list($ip_list_array, 'whitelist');
			if (is_wp_error($validated_ip_list_array)) {
				$result = -1;
				$success = false;
				$message = $validated_ip_list_array->get_error_message();
			} else {
				$allowed_ip_data = implode("\n", $validated_ip_list_array);
				$options['aiowps_lockdown_allowed_ip_addresses'] = $allowed_ip_data;
			}
		} else {
			$options['aiowps_lockdown_allowed_ip_addresses'] = ''; //Clear the IP address config value
		}

		if (1 == $result) {
			$aio_wp_security->configs->set_value('aiowps_lockdown_enable_whitelisting', isset($data["aiowps_lockdown_enable_whitelisting"]) ? '1' : '', true);
		}

		$this->save_settings($options);

		return $this->handle_response($success, $message, array('badges' => array('user-login-lockout-ip-whitelisting')));
	}

	/**
	 * Saves the force logout settings.
	 *
	 * @deprecated Use perform_save_force_logout_settings() instead. Retained for backward compatibility with UpdraftCentral.
	 *
	 * @param array $data An array containing the data to be saved.
	 *
	 * @return array|WP_Error See perform_save_force_logout_settings().
	 */
	public function perform_force_logout($data) {
		return $this->perform_save_force_logout_settings($data);
	}

	/**
	 * Saves the force logout settings.
	 *
	 * @param array $data An array containing the data to be saved.
	 *                    The array may contain the following keys:
	 *                    - 'aiowps_logout_time_period': The time period (in minutes) for logout.
	 *                    - 'aiowps_enable_forced_logout': A boolean indicating whether forced logout is enabled.
	 * @return array|WP_Error Returns an array containing the status of the operation ('success' or 'error'),
	 *               an array of messages indicating the result of the operation,
	 *               the value representing the logout time period,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_save_force_logout_settings($data) {
		global $aio_wp_security;

		if (AIOS_Helper::is_updraft_central_request()) {
			if (!AIOWPSecurity_Utility_Permissions::has_manage_cap()) {
				return new WP_Error(esc_html__('Sorry, you do not have enough privilege to execute the requested action.', 'all-in-one-wp-security-and-firewall'));
			}
		}

		$options = array();
		$info = array();
		$extra_args = array();

		$logout_time_period = sanitize_text_field($data['aiowps_logout_time_period']);
		if (isset($data["aiowps_enable_forced_logout"]) && (!is_numeric($logout_time_period) || $logout_time_period < 1)) {
			$info[] = __('You entered a non numeric or negative value for the logout time period field, it has been set to the default value.', 'all-in-one-wp-security-and-firewall');
			$logout_time_period = '60'; // Set it to the default value for this field
		}

		// Save all the form values to the options
		$options['aiowps_logout_time_period'] = absint($logout_time_period);
		$options['aiowps_enable_forced_logout'] = isset($data["aiowps_enable_forced_logout"]) ? '1' : '';
		$this->save_settings($options);

		$values = array('aiowps_logout_time_period' => absint($logout_time_period));

		if ('1' === $options['aiowps_enable_forced_logout']) {
			$extra_args['logout_user'] = $this->check_logout_user();
			$extra_args['logout_url'] = $aio_wp_security->user_login_obj->aios_force_logout_action_handler(true);
		}

		$args = array(
			'badges' => array('user-login-force-logout'),
			'info' => $info,
			'values' => $values,
			'extra_args' => $extra_args
		);

		return $this->handle_response(true, '', $args);
	}

	/**
	 * Performs the action to save the HTTP authentication settings.
	 *
	 * @param array $data An array containing the data to be saved.
	 *
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               a message indicating the result of the operation,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_save_http_authentication_settings($data) {
		global $aio_wp_security;

		$error_messages = array();

		$aio_wp_security->configs->set_value('aiowps_http_authentication_admin', '');

		if (isset($data['aiowps_http_authentication_admin'])) {
			if (!is_ssl()) {
				$error_messages[] = __('Your site is currently not using https.', 'all-in-one-wp-security-and-firewall');
			} else {
				$aio_wp_security->configs->set_value('aiowps_http_authentication_admin', '1');
			}
		}

		$aio_wp_security->configs->set_value('aiowps_http_authentication_frontend', '');

		if (isset($data['aiowps_http_authentication_frontend'])) {
			if (!is_ssl()) {
				$error_messages[] = __('Your site is currently not using https.', 'all-in-one-wp-security-and-firewall');
			} else {
				$aio_wp_security->configs->set_value('aiowps_http_authentication_frontend', '1');
			}
		}

		if (empty($data['aiowps_http_authentication_username'])) {
			$error_messages[] = __('Enter a value for the HTTP authentication username.', 'all-in-one-wp-security-and-firewall');
		} else {
			$aio_wp_security->configs->set_value('aiowps_http_authentication_username', sanitize_text_field($data['aiowps_http_authentication_username']));
		}

		if (!empty($data['aiowps_http_authentication_password'])) {
			$aio_wp_security->configs->set_value('aiowps_http_authentication_password', wp_hash_password(wp_unslash($data['aiowps_http_authentication_password'])));
		}

		$aio_wp_security->configs->set_value('aiowps_http_authentication_failure_message', htmlentities(wp_unslash($data['aiowps_http_authentication_failure_message']), ENT_COMPAT, 'UTF-8'));

		$aio_wp_security->configs->save_config();

		if (!empty($error_messages)) {
			return $this->handle_response(false, implode("\n", array_unique($error_messages)), array('badges' => array('http-authentication-admin-frontend')));
		}

		return $this->handle_response(true, '', array('badges' => array('http-authentication-admin-frontend')));
	}

	/**
	 * Performs the action to disable application password.
	 *
	 * @param array $data An array containing the data to be saved.
	 *                    The array may contain the following key:
	 *                    - 'aiowps_disable_application_password': A boolean indicating whether application password is disabled.
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               a message indicating the result of the operation,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_disable_application_password($data) {
		global $aio_wp_security;

		// Save all the form values to the options
		$aio_wp_security->configs->set_value('aiowps_disable_application_password', isset($data['aiowps_disable_application_password']) ? '1' : '', true);

		return $this->handle_response(true, '', array('badges' => array('disable-application-password')));
	}

	/**
	 * Performs the action to add salt postfix.
	 *
	 * @param array $data An array containing the data to be saved.
	 *                    The array may contain the following key:
	 *                    - 'aiowps_enable_salt_postfix': A boolean indicating whether salt postfix is enabled.
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               a message indicating the result of the operation,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_add_salt_postfix($data) {
		global $aio_wp_security;

		// Save settings
		$aiowps_enable_salt_postfix = isset($data['aiowps_enable_salt_postfix']) ? '1' : '';
		if ($aiowps_enable_salt_postfix == $aio_wp_security->configs->get_value('aiowps_enable_salt_postfix')) {
			$is_setting_changed = true;
		} else {
			$is_setting_changed = false;
		}

		$aio_wp_security->configs->set_value('aiowps_enable_salt_postfix', $aiowps_enable_salt_postfix, true);
		$ret_schedule = $this->schedule_change_auth_keys_and_salt();

		if (is_wp_error($ret_schedule)) {
			$aio_wp_security->debug_logger->log_debug($ret_schedule->get_error_message(), 4);
		}

		if ('1' == $aiowps_enable_salt_postfix && $is_setting_changed) {
			AIOWPSecurity_Utility::change_salt_postfixes();
		}

		$args = array(
			'badges' => array('enable-salt-postfix')
		);

		return $this->handle_response(true, '', $args);
	}

	/**
	 * Performs actions on logged-in users.
	 *
	 * @deprecated Use perform_force_user_logout() instead, which takes no 'action' key. Retained for backward compatibility with UpdraftCentral.
	 *
	 * @param array $data An array containing the data for the action to be performed.
	 *                    The array may contain the following keys:
	 *                    - 'action': The action to be performed on logged-in users (e.g., 'force_user_logout').
	 *                    - 'logged_in_id': The ID of the logged-in user on which the action will be performed.
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               and a message indicating the result of the operation.
	 */
	public function perform_logged_in_user_action($data) {
		// Older callers pass an 'action' key; keep validating it so unrecognised actions still error rather than silently logging a user out.
		if (isset($data['action']) && !in_array($data['action'], array('force_user_logout'))) {
			return $this->handle_response(false, __('Invalid action provided for logged in user.', 'all-in-one-wp-security-and-firewall'));
		}

		return $this->perform_force_user_logout($data);
	}

	/**
	 * Forces a specific logged-in user to log out.
	 *
	 * @param array $data An array containing the data for the action to be performed.
	 *                    The array may contain the following key:
	 *                    - 'logged_in_id': The ID of the logged-in user to force logout.
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               and a message indicating the result of the operation.
	 */
	public function perform_force_user_logout($data) {
		global $aio_wp_security;

		/* UDC will error out without this */
		if (AIOS_Helper::is_updraft_central_request()) {
			$this->init_wp_list();
		}

		include_once AIO_WP_SECURITY_PATH.'/admin/wp-security-list-logged-in-users.php'; // For rendering the AIOWPSecurity_List_Table
		$user_list = new AIOWPSecurity_List_Logged_In_Users();

		if (empty($data['logged_in_id'])) {
			return $this->handle_response(false, __('No user ID was provided', 'all-in-one-wp-security-and-firewall'));
		}
		$user_id = absint($data['logged_in_id']);
		$error = '';

		if (empty($user_id)) {
			$error = __("Invalid user ID provided.", 'all-in-one-wp-security-and-firewall');
		} elseif (get_current_user_id() === $user_id) {
			$error = __("You cannot log yourself out", 'all-in-one-wp-security-and-firewall');
		} elseif (is_super_admin($user_id)) {
			$error = __("Super admins cannot be logged out.", 'all-in-one-wp-security-and-firewall');
		} elseif (!AIOWPSecurity_Utility::is_user_member_of_blog($user_id)) {
			$error = __("You cannot log out a user from a different subsite.", 'all-in-one-wp-security-and-firewall');
		}

		if (!empty($error)) {
			return $this->handle_response(false, $error);
		}

		$result = $aio_wp_security->user_login_obj->delete_logged_in_user($user_id);

		if ($result) {
			$user_list->logout_user($user_id);
			$message = __('The selected user has been logged out successfully.', 'all-in-one-wp-security-and-firewall');
			$success = true;
		} else {
			$message = __('Failed to log out the selected user.', 'all-in-one-wp-security-and-firewall');
			$success = false;
		}

		return $this->handle_response($success, $message);
	}

	/**
	 * Performs the action to configure manual registration approval settings.
	 *
	 * @param array $data An array containing the data to be saved.
	 *                    The array may contain the following key:
	 *                    - 'aiowps_enable_manual_registration_approval': A boolean indicating whether manual registration approval is enabled.
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               a message indicating the result of the operation,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_manual_approval_settings($data) {
		global $aio_wp_security;

		// Save settings
		$aio_wp_security->configs->set_value('aiowps_enable_manual_registration_approval', isset($data["aiowps_enable_manual_registration_approval"]) ? '1' : '', true);

		return $this->handle_response(true, '', array('badges' => array('manually-approve-registrations')));
	}

	/**
	 * Renders the manual approval (registered users) table content.
	 *
	 * Initialises the AIOWPSecurity_List_Registered_Users class to render the list of
	 * users pending manual approval. Any bulk action supplied in the request data is
	 * processed by the table while preparing its items. The list is displayed via an
	 * AJAX response.
	 *
	 * @param array $data Registered users table data.
	 *
	 * @return void
	 */
	public function render_manual_approval_tab($data) {

		$data = empty($data) ? array() : $data;

		// Needed for rendering the registered users table
		include_once(AIO_WP_SECURITY_PATH.'/admin/wp-security-list-registered-users.php');
		$user_list = new AIOWPSecurity_List_Registered_Users($data);
		$user_list->ajax_response();
	}

	/**
	 * Performs a bulk action on the manual approval (registered users) table.
	 *
	 * @param array $data An array containing the data for the action to be performed.
	 *                    The array may contain the following keys:
	 *                    - 'action': The bulk action to perform ('approve', 'delete' or 'block').
	 *                    - 'items': An array of user IDs the action applies to.
	 * @return void
	 */
	public function perform_manual_approval_bulk_action($data) {

		$data = empty($data) ? array() : $data;

		// The table processes the bulk action while preparing its items, so flag it accordingly.
		$data['bulk_apply'] = 'true';

		$this->render_manual_approval_tab($data);
	}

	/**
	 * Performs actions on manual approval items (e.g., approve account, delete account, block IP).
	 *
	 * @param array $data An array containing the data for the action to be performed.
	 *                    The array may contain the following keys:
	 *                    - 'action': The action to be performed on the manual approval item (e.g., 'approve_acct', 'delete_acct', 'block_ip').
	 *                    - 'user_id': The ID of the user for whom the action will be performed (applicable for 'approve_acct' and 'delete_acct' actions).
	 *                    - 'ip_address': The IP address to be blocked (applicable for 'block_ip' action).
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               and a message indicating the result of the operation.
	 */
	public function perform_manual_approval_item_action($data) {
		global $aio_wp_security;

		include_once AIO_WP_SECURITY_PATH.'/admin/wp-security-list-registered-users.php'; // For rendering the AIOWPSecurity_List_Table
		$user_list = new AIOWPSecurity_List_Registered_Users();
		$success = false;
		$message = '';
		$args = array();

		$valid_actions = array('approve_acct', 'delete_acct', 'block_ip');
		if (empty($data['action']) || !in_array($data['action'], $valid_actions)) { // more actions can be added
			return $this->handle_response(false, __('Invalid action provided for registered user.', 'all-in-one-wp-security-and-firewall'));
		}

		switch ($data['action']) {
			case 'approve_acct':
				if (empty($data['user_id'])) {
					return $this->handle_response(false, __('No valid user ID was provided', 'all-in-one-wp-security-and-firewall'));
				}
				$user_id = esc_sql(wp_strip_all_tags($data['user_id']));
				$meta_key = 'aiowps_account_status';
				$meta_value = 'approved'; // set account status

				// Approve single account
				$result = update_user_meta($user_id, $meta_key, $meta_value);
				if ($result) {
					$user = get_user_by('id', $user_id);
					$user_list->send_email_upon_account_activation($user);
					$message = __('The selected account was approved successfully.', 'all-in-one-wp-security-and-firewall');
					$success = true;
				} elseif (false === $result) {
					$aio_wp_security->debug_logger->log_debug("could not approve account ID: $user_id", 4);
					$message = __('The selected account could not be approved.', 'all-in-one-wp-security-and-firewall');
				}
				break;
			case 'delete_acct':
				if (empty($data['user_id'])) {
					return $this->handle_response(false, __('No valid user ID was provided', 'all-in-one-wp-security-and-firewall'));
				}

				$user_id = esc_sql(wp_strip_all_tags($data['user_id']));
				// Delete single account
				$result = is_multisite() ? wpmu_delete_user($user_id) : wp_delete_user($user_id);
				if (true === $result) {
					$message = __('The selected account was deleted successfully.', 'all-in-one-wp-security-and-firewall');
					$success = true;
				} else {
					$aio_wp_security->debug_logger->log_debug("could not delete account ID: $user_id", 4);
					$message = __('The selected account could not be deleted.', 'all-in-one-wp-security-and-firewall');
				}
				break;
			case 'block_ip':
				if (empty($data['ip_address'])) {
					return $this->handle_response(false, __('No valid IP address was provided', 'all-in-one-wp-security-and-firewall'));
				}

				$ip = esc_sql(wp_strip_all_tags($data['ip_address']));

				if (AIOWPSecurity_Utility_IP::get_user_ip_address() == $ip) {
					$message = __('You cannot block your own IP address:', 'all-in-one-wp-security-and-firewall') . ' ' . $ip;
					break;
				}

				// Block single IP
				$result = AIOWPSecurity_Blocking::add_ip_to_block_list($ip, 'registration_spam');
				if (true === $result) {
					$message = __('The selected IP was successfully added to the permanent block list.', 'all-in-one-wp-security-and-firewall');
					$blocked_ips_url = add_query_arg(array('page' => AIOWPSEC_MAIN_MENU_SLUG, 'tab' => 'permanent-block'), admin_url('admin.php'));
					$args['extra_args'] = array('link' => array('url' => esc_url_raw($blocked_ips_url, array('http', 'https')), 'text' => esc_html__('View Blocked IPs', 'all-in-one-wp-security-and-firewall')));
					$success = true;
				} else {
					$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_List_Registered_Users::block_selected_ips() - could not block IP: $ip", 4);
					$message = __('The selected IP could not be added to the permanent block list.', 'all-in-one-wp-security-and-firewall');
				}
				break;
		}

		return $this->handle_response($success, $message, $args);
	}

	/**
	 * Schedule weekly aios_change_auth_keys_and_salt cron event.
	 *
	 * @return Boolean|WP_Error  True if event successfully scheduled. False or WP_Error on failure.
	 */
	private function schedule_change_auth_keys_and_salt() {
		$previous_time = wp_next_scheduled('aios_change_auth_keys_and_salt');

		if (false !== $previous_time) {
			// Clear schedule so that we don't stack up scheduled backups
			wp_clear_scheduled_hook('aios_change_auth_keys_and_salt');
		}
		$gmt_offset_in_seconds = floatval(get_option('gmt_offset')) * 3600;
		$first_time = strtotime('next Sunday '.apply_filters('aios_salt_change_schedule_time', '3:00 am')) + $gmt_offset_in_seconds;
		return wp_schedule_event($first_time, 'weekly', 'aios_change_auth_keys_and_salt');
	}

	/**
	 * Checks if the current user should be automatically logged out based on last login time.
	 *
	 * This method compares the current time with the last login time of the user and determines
	 * if the user should be logged out based on a configured logout time period.
	 *
	 * @return bool Returns true if the user should be logged out, false otherwise.
	 */
	private function check_logout_user() {
		global $aio_wp_security;

		// Get the current user
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		// Get the current and last login times
		$current_time = current_time('mysql', true);
		$login_time = $aio_wp_security->user_login_obj->get_wp_user_aiowps_last_login_time($user_id);

		// Return false if login time is empty (no last login recorded)
		if (empty($login_time)) {
			return false;
		}

		// Calculate the time difference between current time and last login time
		$diff = strtotime($current_time) - strtotime($login_time);

		// Get the configured logout time period in seconds
		$logout_time_interval_value = $aio_wp_security->configs->get_value('aiowps_logout_time_period');
		$logout_time_interval_val_seconds = $logout_time_interval_value * 60;

		// Return true if the time difference exceeds the logout time interval, indicating the user should be logged out
		return $diff > $logout_time_interval_val_seconds;
	}

	/**
	 * Saves all the login lockout settings from UDC.
	 *
	 * @param array $data An array containing the data to be saved.
	 *                    The array may contain the following keys:
	 *                    - 'aiowps_enable_login_lockdown': A boolean indicating whether login lockdown is enabled.
	 *                    - 'aiowps_allow_unlock_requests': A boolean indicating whether unlock requests are allowed.
	 *                    - 'aiowps_max_login_attempts': The maximum number of login attempts allowed before lockout.
	 *                    - 'aiowps_retry_time_period': The time period (in minutes) after which the user is allowed to retry login.
	 *                    - 'aiowps_lockout_time_length': The time period (in minutes) during which the user is locked out after the maximum number of login attempts is reached.
	 *                    - 'aiowps_lockout_message': The message to be displayed to the user when they are locked out.
	 *                    - 'aiowps_lockout_redirect_url': The URL to redirect the user to after lockout.
	 *                    - 'aiowps_lockout_redirect_url_text': The text to be displayed on the redirect URL textbox.
	 *
	 * @return array|WP_Error
	 */
	public function perform_login_lockout_save_settings($data) {
		if (AIOS_Helper::is_updraft_central_request()) {
			if (!AIOWPSecurity_Utility_Permissions::has_manage_cap()) {
				return new WP_Error(esc_html__('Sorry, you do not have enough privilege to execute the requested action.', 'all-in-one-wp-security-and-firewall'));
			}
		}

		/* Required or UDC will error out. */
		include_once(AIO_WP_SECURITY_PATH . '/admin/wp-security-admin-menu.php');

		$response = $this->perform_save_login_lockout_whitelist_settings($data);

		if ('error' === $response['status']) {
			return $response;
		}

		return $this->perform_save_login_lockout_settings($data);
	}

	/**
	 * Return user security data.
	 *
	 * @return array Array of option values,
	 */
	public function get_user_security_data() {
		global $aio_wp_security;

		/* Login lockout */
		$aiowps_enable_login_lockdown = $aio_wp_security->configs->get_value('aiowps_enable_login_lockdown');
		$aiowps_allow_unlock_requests = $aio_wp_security->configs->get_value('aiowps_allow_unlock_requests');
		$aiowps_max_login_attempts = $aio_wp_security->configs->get_value('aiowps_max_login_attempts');
		$aiowps_retry_time_period = $aio_wp_security->configs->get_value('aiowps_retry_time_period');
		$aiowps_lockout_time_length = $aio_wp_security->configs->get_value('aiowps_lockout_time_length');
		$aiowps_max_lockout_time_length = $aio_wp_security->configs->get_value('aiowps_max_lockout_time_length');
		$aiowps_set_generic_login_msg = $aio_wp_security->configs->get_value('aiowps_set_generic_login_msg');
		$aiowps_enable_invalid_username_lockdown = $aio_wp_security->configs->get_value('aiowps_enable_invalid_username_lockdown');
		$aiowps_instantly_lockout_specific_usernames = $aio_wp_security->configs->get_value('aiowps_instantly_lockout_specific_usernames');
		$aiowps_enable_email_notify = $aio_wp_security->configs->get_value('aiowps_enable_email_notify');
		$aiowps_email_address = $aio_wp_security->configs->get_value('aiowps_email_address');
		$aiowps_enable_php_backtrace_in_email = $aio_wp_security->configs->get_value('aiowps_enable_php_backtrace_in_email');
		$aiowps_lockdown_enable_whitelisting = $aio_wp_security->configs->get_value('aiowps_lockdown_enable_whitelisting');
		$aiowps_lockdown_allowed_ip_addresses = $aio_wp_security->configs->get_value('aiowps_lockdown_allowed_ip_addresses');

		/* Force logout */
		$aiowps_enable_forced_logout = $aio_wp_security->configs->get_value('aiowps_enable_forced_logout');
		$aiowps_logout_time_period = $aio_wp_security->configs->get_value('aiowps_logout_time_period');

		return array(
			'aiowps_enable_login_lockdown' => $aiowps_enable_login_lockdown,
			'aiowps_allow_unlock_requests'=> $aiowps_allow_unlock_requests,
			'aiowps_max_login_attempts' => $aiowps_max_login_attempts,
			'aiowps_retry_time_period' => $aiowps_retry_time_period,
			'aiowps_lockout_time_length' => $aiowps_lockout_time_length,
			'aiowps_max_lockout_time_length' => $aiowps_max_lockout_time_length,
			'aiowps_set_generic_login_msg' => $aiowps_set_generic_login_msg,
			'aiowps_enable_invalid_username_lockdown' => $aiowps_enable_invalid_username_lockdown,
			'aiowps_instantly_lockout_specific_usernames' => $aiowps_instantly_lockout_specific_usernames,
			'aiowps_enable_email_notify' => $aiowps_enable_email_notify,
			'aiowps_email_address' => AIOWPSecurity_Utility::get_textarea_str_val($aiowps_email_address),
			'aiowps_enable_php_backtrace_in_email' => $aiowps_enable_php_backtrace_in_email,
			'aiowps_lockdown_enable_whitelisting' => $aiowps_lockdown_enable_whitelisting,
			'aiowps_lockdown_allowed_ip_addresses' => $aiowps_lockdown_allowed_ip_addresses,
			'aiowps_enable_forced_logout' => $aiowps_enable_forced_logout,
			'aiowps_logout_time_period' => $aiowps_logout_time_period,
		);
	}

	/**
	 * Return logged-in user data.
	 *
	 * @return array Array of option values,
	 */
	public function get_logged_in_users_data() {
		/* UDC will error out without this */
		if (AIOS_Helper::is_updraft_central_request()) {
			$this->init_wp_list();
		}

		include_once AIO_WP_SECURITY_PATH . '/admin/wp-security-list-logged-in-users.php';

		$user_list = new AIOWPSecurity_List_Logged_In_Users();
		$user_list->prepare_items();

		$column_list = array();

		foreach ($user_list->get_columns() as $column => $value) {
			if (!empty($column)) {
				if ('cb' !== $column) {
					$column_list[$column] = array('label' => $value);
				}
			}
		}

		return array(
			'logged_in_user_data' => array(
				'users' => $user_list->items,
				'columns' => $column_list,
				'bulk_actions' => $user_list->get_bulk_actions(),
			)
		);
	}

	/**
	 * Whitelists user's IP address
	 *
	 * @return array Returns an array containing the status of the operation ('success' or 'error'),
	 *               a message indicating the result of the operation,
	 *               and a badge representing the updated feature details.
	 */
	public function perform_whitelist_user_ip() {
		if (!AIOWPSecurity_Utility_Permissions::has_manage_cap()) {
			return $this->handle_response(false, __('You don\'t have enough permissions to whitelist your IP address.', 'all-in-one-wp-security-and-firewall'));
		}

		$aiowps_firewall_allow_list = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::ALLOW_LIST);

		$whitelisted_ips = $aiowps_firewall_allow_list::get_ips();
		$is_whitelisted = $aiowps_firewall_allow_list::is_ip_allowed();

		if ($is_whitelisted) {
			return $this->handle_response(false, __('Your IP address is already whitelisted.', 'all-in-one-wp-security-and-firewall'));
		}

		$user_ip = AIOWPSecurity_Utility_IP::get_user_ip_address();

		if (empty($user_ip)) {
			return $this->handle_response(false, __('Your IP address could not be detected.', 'all-in-one-wp-security-and-firewall'));
		}

		$whitelisted_ips .= (empty($whitelisted_ips) ? '' : "\n") . $user_ip;

		if (!$aiowps_firewall_allow_list::add_ips($whitelisted_ips)) {
			return $this->handle_response(false, __('There was an error whitelisting your IP address, please try again.', 'all-in-one-wp-security-and-firewall'));
		}

		return $this->handle_response(true, __('Your IP address has been whitelisted successfully.', 'all-in-one-wp-security-and-firewall'));
	}
}
