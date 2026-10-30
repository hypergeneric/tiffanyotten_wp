<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (trait_exists('AIOWPSecurity_Comment_Commands_Trait')) return;

trait AIOWPSecurity_Comment_Commands_Trait {

	/**
	 * Renders the comment spammer IP table in the WordPress admin area.
	 *
	 * This function processes the POST data if available and initializes the
	 * AIOWPSecurity_List_Comment_Spammer_IP class to render the list of IP addresses
	 * identified as comment spammers. The list is displayed via an AJAX response.
	 *
	 * @param array $data - the request data contains the post data
	 *
	 * @return void
	 */
	public function render_comment_spammer_ip_table($data) {
		$data = empty($data) ? array() : $data;

		// Needed for rendering the spammer ip results table
		include_once(AIO_WP_SECURITY_PATH.'/admin/wp-security-list-comment-spammer-ip.php');
		$spammer_ip_list = new AIOWPSecurity_List_Comment_Spammer_IP($data);
		$spammer_ip_list->ajax_response();
	}

	/**
	 * Perform the saving of comment spam prevention settings
	 *
	 * @param array $data - the request data contains the post data
	 *
	 * @return array
	 */
	public function perform_comment_spam_prevention($data) {
		$values = array();

		// Save settings
		$options = array();
		$info = array();

		$options['aiowps_enable_spambot_detecting'] = isset($data["aiowps_enable_spambot_detecting"]) ? '1' : '';
		$options['aiowps_spambot_detect_usecookies'] = isset($data["aiowps_spambot_detect_usecookies"]) ? '1' : '';
		$options['aiowps_spam_comments_should'] = !empty($data["aiowps_spam_comments_should"]) ? '1' : '0';
		$options['aiowps_enable_trash_spam_comments'] = isset($data['aiowps_enable_trash_spam_comments']) ? '1' : '';
		if (isset($data['aiowps_trash_spam_comments_after_days'])) {
			$aiowps_trash_spam_comments_after_days = sanitize_text_field($data['aiowps_trash_spam_comments_after_days']);
			if (isset($data['aiowps_enable_trash_spam_comments']) && !is_numeric($aiowps_trash_spam_comments_after_days)) {
				$error = __('You entered a non-numeric value for the "move spam comments to trash after number of days" field; it has been set to the default value.', 'all-in-one-wp-security-and-firewall');
				//Set it to the default value for this field
				$info[] = $error;
				$aiowps_trash_spam_comments_after_days = 14;
			}
			$aiowps_trash_spam_comments_after_days = absint($aiowps_trash_spam_comments_after_days);
			$options['aiowps_trash_spam_comments_after_days'] = $aiowps_trash_spam_comments_after_days;

			$values = array(
				'aiowps_trash_spam_comments_after_days' => $aiowps_trash_spam_comments_after_days
			);
		}

		// Commit the config settings
		$this->save_settings($options);
		AIOWPSecurity_Comment::trash_spam_comments();

		$args = array(
			'badges' => array('detect-spambots'),
			'info' => $info,
			'values' => $values
		);

		return $this->handle_response(true, '', $args);
	}

	/**
	 * Perform the saving of comment auto block spammers ip settings
	 *
	 * @param array $data - the request data contains the post data
	 *
	 * @return array
	 */
	public function perform_auto_block_spam_ip($data) {

		$info = array();
		$enable_auto_block_ip = isset($data["aiowps_enable_autoblock_spam_ip"]) ? '1' : '';

		$spam_ip_min_comments = sanitize_text_field($data['aiowps_spam_ip_min_comments_block']);
		if (!is_numeric($spam_ip_min_comments)) {
			$info[] = __('You entered a non-numeric value for the "minimum number of spam comments" field; it has been set to the default value.', 'all-in-one-wp-security-and-firewall');
			$spam_ip_min_comments = '3';// Set it to the default value for this field
		} elseif ((int) $spam_ip_min_comments < 1) {
			$info[] = __('You must enter an integer greater than zero for the "minimum number of spam comments" field; it has been set to the default value.', 'all-in-one-wp-security-and-firewall');
			$spam_ip_min_comments = '3';// Set it to the default value for this field
		}

		// Save all the form values to the options
		$options = array(
			'aiowps_enable_autoblock_spam_ip' => $enable_auto_block_ip,
			'aiowps_spam_ip_min_comments_block' => absint($spam_ip_min_comments),
		);
		
		$this->save_settings($options);

		$args = array(
			'badges' => array('auto-block-spam-ips'),
			'info' => $info,
			'values' => array('aiowps_spam_ip_min_comments_block' => absint($spam_ip_min_comments))
		);

		return $this->handle_response(true, '', $args);
	}

	/**
	 * Saves all the specified comment spam settings from UDC.
	 *
	 * @param array $data - the request data contains the post data.
	 *
	 * @return array|WP_Error - The response array containing the status, message, and values.
	 */
	public function perform_save_spam_prevention_settings($data) {
		if (AIOS_Helper::is_updraft_central_request()) {
			if (!AIOWPSecurity_Utility_Permissions::has_manage_cap()) {
				return new WP_Error(esc_html__('Sorry, you do not have enough privilege to execute the requested action.', 'all-in-one-wp-security-and-firewall'));
			}
		}

		$spam_prevention_response = $this->perform_comment_spam_prevention($data);
		$block_spam_response = $this->perform_auto_block_spam_ip($data);

		$args = array(
			'badges' => array('detect-spambots', 'auto-block-spam-ips'),
			'info' => array_merge($spam_prevention_response['info'], $block_spam_response['info']),
			'values' => array_merge($spam_prevention_response['values'], $block_spam_response['values'])
		);

		return $this->handle_response(true, '', $args);
	}

	/**
	 * Perform the ip spam comment search
	 *
	 * @param array $data - the request data contains the post data
	 *
	 * @return array
	 */
	public function perform_ip_spam_search($data) {

		$min_comments_per_ip = sanitize_text_field($data['aiowps_spam_ip_min_comments']);
		$message = false;

		if (!is_numeric($min_comments_per_ip)) {
			$message = __('You entered a non-numeric value for the minimum spam comments per IP field; it has been set to the default value.', 'all-in-one-wp-security-and-firewall');
			$min_comments_per_ip = '5'; // Set it to the default value for this field
		} elseif ((int) $min_comments_per_ip < 1) {
			$message = __('You must enter an integer greater than zero for the minimum spam comments per IP field; it has been set to the default value.', 'all-in-one-wp-security-and-firewall');
			$min_comments_per_ip = '5'; // Set it to the default value for this field
		}

		$min_comments_per_ip = absint($min_comments_per_ip);

		// Save all the form values to the options
		$this->save_settings(array(
			'aiowps_spam_ip_min_comments' => $min_comments_per_ip
		));

		$args = array(
			'values' => array('aiowps_spam_ip_min_comments' => $min_comments_per_ip)
		);

		return $this->handle_response(true, $message, $args);
	}

	/**
	 * Perform the action of blocking a spam IP address.
	 *
	 * This function takes an IP address as input, checks if it is valid and not the user's own IP,
	 * and then attempts to add it to the block list for spam. It returns the status and message of the operation.
	 *
	 * @param array $data The data containing the IP address to block.
	 *
	 * @return array The result of the block operation, including status, message, and updated blocked comments output.
	 */
	public function perform_block_spam_ip($data) {

		if (empty($data['ip'])) {
			return $this->handle_response(false, __('Invalid IP address provided.', 'all-in-one-wp-security-and-firewall'));
		}

		$ip = wp_strip_all_tags($data['ip']);

		if (AIOWPSecurity_Utility_IP::get_user_ip_address() == $ip) {
			return $this->handle_response(false, __('You cannot block your own IP address:', 'all-in-one-wp-security-and-firewall') . ' ' . $ip);
		}

		$result = AIOWPSecurity_Blocking::add_ip_to_block_list($ip, 'spam');
		$success = true;

		if ($result) {
			$message = __('The selected IP address is now permanently blocked.', 'all-in-one-wp-security-and-firewall');
		} else {
			$success = false;
			$message = __('The selected IP address could not be blocked due to one of the following reasons:', 'all-in-one-wp-security-and-firewall');
			$message .= ' ' . __('either it has already been blocked, or your user account lacks sufficient permissions to perform IP blocking.', 'all-in-one-wp-security-and-firewall');
		}

		return $this->handle_response($success, $message, array('content' => array('aios-blocked-comments-output' => $this->get_blocked_comments_output())));
	}

	/**
	 * Retrieves the output for displaying blocked comments due to spam.
	 *
	 * This function queries the database to get IP addresses that are permanently blocked due to spam.
	 * It returns HTML output that displays the count of IPs blocked today and the all-time total count.
	 *
	 * @global object $aio_wp_security The global instance of the aio_wp_security class.
	 * @global object $wpdb The global instance of the WordPress database class.
	 *
	 * @return string HTML output for the blocked comments section.
	 */
	private function get_blocked_comments_output() {
		global $aio_wp_security, $wpdb;

		$block_comments_output = '';
		$min_block_comments = $aio_wp_security->configs->get_value('aiowps_spam_ip_min_comments_block');

		if (!empty($min_block_comments)) {
			$now_date = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d');

			$sql = $wpdb->prepare(
				"SELECT COUNT(*) AS total_count, 
       			SUM(CASE WHEN DATE(FROM_UNIXTIME(created)) = %s THEN 1 ELSE 0 END) AS todays_blocked_count FROM ".AIOWPSEC_TBL_PERM_BLOCK." WHERE block_reason = %s",
				$now_date,
				'spam'
			);

			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- PCP warning. Ignore
			$result = $wpdb->get_row($sql);

			$block_comments_output = '<div class="aio_yellow_box">';
			if (empty($result) || 0 == $result->total_count) {
				$block_comments_output .= '<p><strong>'.esc_html__('You currently have no IP addresses permanently blocked due to spam.', 'all-in-one-wp-security-and-firewall').'</strong></p>';
			} else {
				$todays_blocked_count = $result->todays_blocked_count;
				$total_count = $result->total_count;

				$block_comments_output .= '<p><strong>'.esc_html__('Spammer IPs added to permanent block list today:', 'all-in-one-wp-security-and-firewall') . ' ' . esc_html($todays_blocked_count) . '</strong></p>';
				$block_comments_output .= '<hr><p><strong>'.esc_html__('All time total:', 'all-in-one-wp-security-and-firewall'). ' ' . $total_count.'</strong></p>';
				$block_comments_output .= '<p><a class="button" href="admin.php?page='.esc_attr(AIOWPSEC_MAIN_MENU_SLUG).'&tab=permanent-block" target="_blank">'.esc_html__('View blocked IPs', 'all-in-one-wp-security-and-firewall').'</a></p>';
			}
			$block_comments_output .= '</div>';
		}

		return $block_comments_output;
	}

	/**
	 * Retrieves data for comment spam data for UDC.
	 *
	 * @return array Array of settings data for comment spam data.
	 */
	public function get_comment_spam_data() {
		global $aio_wp_security;

		$aiowps_enable_spambot_detecting = $aio_wp_security->configs->get_value('aiowps_enable_spambot_detecting');
		$aiowps_spambot_detect_usecookies = $aio_wp_security->configs->get_value('aiowps_spambot_detect_usecookies');
		$aiowps_enable_trash_spam_comments = $aio_wp_security->configs->get_value('aiowps_enable_trash_spam_comments');
		$aiowps_spam_comments_should = $aio_wp_security->configs->get_value('aiowps_spam_comments_should');
		$aiowps_trash_spam_comments_after_days = $aio_wp_security->configs->get_value('aiowps_trash_spam_comments_after_days');

		$aiowps_enable_autoblock_spam_ip = $aio_wp_security->configs->get_value('aiowps_enable_autoblock_spam_ip');
		$aiowps_spam_ip_min_comments_block = $aio_wp_security->configs->get_value('aiowps_spam_ip_min_comments_block');

		return array(
			'aiowps_enable_spambot_detecting' => $aiowps_enable_spambot_detecting,
			'aiowps_spambot_detect_usecookies' => $aiowps_spambot_detect_usecookies,
			'aiowps_enable_trash_spam_comments' => $aiowps_enable_trash_spam_comments,
			'aiowps_spam_comments_should' => $aiowps_spam_comments_should,
			'aiowps_trash_spam_comments_after_days' => $aiowps_trash_spam_comments_after_days,
			'aiowps_enable_autoblock_spam_ip' => $aiowps_enable_autoblock_spam_ip,
			'aiowps_spam_ip_min_comments_block' => $aiowps_spam_ip_min_comments_block,
		);
	}
}
