<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Handles the registered users pending manual approval list table.
 */
class AIOWPSecurity_List_Registered_Users extends AIOWPSecurity_Ajax_Data_Table {

	/**
	 * Constructs the table object and sets up its attributes.
	 *
	 * @param array $data - An array containing additional data for the table. Default is an empty array.
	 * @return void
	 */
	public function __construct($data = array()) {
		//Set parent defaults
		parent::__construct(array(
			'singular'  => 'item',     //singular name of the listed records
			'plural'    => 'items',    //plural name of the listed records
			'ajax'      => true,       //does this table support ajax?
			'data'      => $data       //Request data
		));
	}

	/**
	 * Returns cb column html to be rendered.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_cb($item) {
		printf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			esc_attr($this->_args['singular']), // Let's simply repurpose the table's singular label
			esc_attr($item['ID'])
		);
	}

	/**
	 * Renders ID column html.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_ID($item) {
		//Build row actions
		$actions = array(
			'view' => array(
				'text' => __('View', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'href' => 'user-edit.php?user_id=' . $item['ID'],
					'target' => '_blank',
				),
			),
			'approve_acct' => array(
				'text' => __('Approve', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-approve-user-acct',
					'data-id' => $item['ID'],
					'data-message' => __('Are you sure you want to approve this account?', 'all-in-one-wp-security-and-firewall'),
				),
			),
			'delete_acct' => array(
				'text' => __('Delete', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-delete-user-acct',
					'data-id' => $item['ID'],
					'data-message' => __('Are you sure you want to delete this account?', 'all-in-one-wp-security-and-firewall'),
				),
			),
			'block_ip' => array(
				'text' => __('Block IP', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-block-ip',
					'data-ip' => $item['ip_address'],
					'data-message' => __('Are you sure you want to block this IP address?', 'all-in-one-wp-security-and-firewall'),
				),
			),
		);

		echo esc_html($item['ID']);
		$this->row_actions($actions);
	}

	/**
	 * Renders IP address column html.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_ip_address($item) {
		if (AIOWPSecurity_Blocking::is_ip_blocked($item['ip_address'])) {
			echo esc_html($item['ip_address']).'<br /><span class="aiowps-label aiowps-label-success">'.esc_html__('blocked', 'all-in-one-wp-security-and-firewall').'</span>';
		} else {
			echo esc_html($item['ip_address']);
		}
	}

	/**
	 * Returns array of columns to be rendered.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox">', // Render a checkbox
			'ID' => __('User ID', 'all-in-one-wp-security-and-firewall'),
			'user_login' => __('Login name', 'all-in-one-wp-security-and-firewall'),
			'user_email' => __('Email', 'all-in-one-wp-security-and-firewall'),
			'user_registered' => __('Register date', 'all-in-one-wp-security-and-firewall'),
			'account_status' => __('Account status', 'all-in-one-wp-security-and-firewall'),
			'ip_address' => __('IP address', 'all-in-one-wp-security-and-firewall')
		);
		return $columns;
	}
	
	/**
	 * Sets which of the columns the table data can be sorted by
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'ID' => array('ID', false),
			'user_login' => array('user_login', false),
			'user_email' => array('user_email', false),
			'user_registered' => array('user_registered', false),
		);
		return $sortable_columns;
	}
	
	public function get_bulk_actions() {
		$actions = array(
			'approve' => __('Approve', 'all-in-one-wp-security-and-firewall'),
			'delete' => __('Delete', 'all-in-one-wp-security-and-firewall'),
			'block' => __('Block IP', 'all-in-one-wp-security-and-firewall')
		);
		return $actions;
	}

	/**
	 * Process bulk actions from the menu.
	 *
	 * This method processes bulk actions such as approving, deleting accounts
	 * or blocking the IP addresses of the selected registrants.
	 *
	 * @param string $action The bulk action to be performed.
	 *                       Possible values are 'approve', 'delete' or 'block'.
	 * @param array  $items  Optional. An array of user IDs the action applies to.
	 *                       Defaults to an empty array.
	 *
	 * @return void
	 */
	private function process_bulk_action($action, $items = array()) {
		$valid_actions = array('approve', 'delete', 'block');

		if (!in_array($action, $valid_actions)) {
			AIOS_Helper::set_message('aios_list_message', __('Invalid action provided for registered user.', 'all-in-one-wp-security-and-firewall'), 'error');
			return;
		}

		if (empty($items) || !is_array($items)) {
			AIOS_Helper::set_message('aios_list_message', __('Select records using the checkboxes', 'all-in-one-wp-security-and-firewall'), 'error');
			return;
		}

		if ('approve' === $action) {
			$this->approve_selected_accounts($items);
		} elseif ('delete' === $action) {
			$this->delete_selected_accounts($items);
		} elseif ('block' === $action) {
			$this->block_selected_ips($items);
		}
	}

	/**
	 * This function approves selected user accounts
	 *
	 * @param array|int $entries - this is an array of users or user_id to be approved
	 *
	 * @return void|string
	 */
	public function approve_selected_accounts($entries) {
		global $aio_wp_security;
		$meta_key = 'aiowps_account_status';
		$meta_value = 'approved'; // set account status
		$failed_accts = ''; // string to store comma separated accounts which failed to update
		$at_least_one_updated = false;
		if (is_array($entries)) {
			$entries = array_map('esc_sql', $entries); // Escape every array element
			//Let's go through each entry and approve
			foreach ($entries as $user_id) {
				$result = update_user_meta($user_id, $meta_key, $meta_value);
				if (false === $result) {
					$failed_accts .= ' '.$user_id.', ';
					$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_List_Registered_Users::approve_selected_accounts() - could not approve account ID: $user_id", 4);
				} else {
					$at_least_one_updated = true;
					$user = get_user_by('id', $user_id);
					if (false === $user) {
						//don't send mail
					} else {
						$this->send_email_upon_account_activation($user);
					}
				}
			}
			if ($at_least_one_updated) {
				AIOS_Helper::set_message('aios_list_message', __('The selected accounts were approved successfully.', 'all-in-one-wp-security-and-firewall'));
			}
			if ('' != $failed_accts) {
				//display any failed account updates
				rtrim($failed_accts);
				AIOS_Helper::set_message('aios_list_message', __('The following accounts failed to update successfully:', 'all-in-one-wp-security-and-firewall') . ' ' . $failed_accts, 'error');
			}
		}
	}

	/**
	 * This function sends an email to an approved user
	 *
	 * @param WP_User $user - the object for the approved user
	 *
	 * @return bool
	 */
	public function send_email_upon_account_activation($user) {
		global $aio_wp_security;
		if (!($user instanceof WP_User)) {
			return false;
		}
		
		$to_email_address = $user->user_email;
		$email_msg = '';
		$subject = '['.network_site_url().'] '. __('Your account is now active', 'all-in-one-wp-security-and-firewall');
		/* translators: %s: Username */
		$email_msg .= sprintf(__('Your account with username: %s is now active', 'all-in-one-wp-security-and-firewall'), $user->user_login) . "\n";
		$subject = apply_filters('aios_register_approval_email_subject', $subject);
		$email_msg = apply_filters('aios_register_approval_email_msg', $email_msg, $user); //also pass the WP_User object
		
		$mail_send = AIOWPSecurity_Reporting::notification(array('to' => $to_email_address, 'subject' => $subject, 'message' => $email_msg));
		if (false === $mail_send) {
			$aio_wp_security->debug_logger->log_debug("Manual account approval notification email failed to send to " . $to_email_address, 4);
		}
		return $mail_send;
	}

	/**
	 * This function deletes selected entries pending approval
	 *
	 * @param array|int $entries - this is an array of users or single user to be deleted
	 *
	 * @return void|string
	 */
	public function delete_selected_accounts($entries) {
		global $aio_wp_security;
		if (is_array($entries)) {
			$entries = array_map('esc_sql', $entries); // Escape every array element
			//Let's go through each entry and delete account
			foreach ($entries as $user_id) {
				$result = is_multisite() ? wpmu_delete_user($user_id) : wp_delete_user($user_id);
				if (true !== $result) {
					$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_List_Registered_Users::delete_selected_accounts() - could not delete account ID: $user_id", 4);
				}
			}
			AIOS_Helper::set_message('aios_list_message', __('The selected accounts were deleted successfully.', 'all-in-one-wp-security-and-firewall'));
		}
	}

	/**
	 * This function blocks selected ip
	 *
	 * @param array|int $entries - this is an array of ips or ip to be blocked
	 *
	 * @return void|string
	 */
	public function block_selected_ips($entries) {
		global $aio_wp_security;
		if (is_array($entries)) {
			$entries = array_filter($entries, function ($entry) {
				return AIOWPSecurity_Utility_IP::get_user_ip_address() != $entry;
			});

			if (empty($entries)) {
				AIOS_Helper::set_message('aios_list_message', __('Only invalid IP addresses were provided: you can not block your own IP address', 'all-in-one-wp-security-and-firewall'), 'error');
				return;
			}
			$entries = array_map('esc_sql', $entries); // Escape every array element
			//Let's go through each entry and block IP
			$total_success = 0;
			foreach ($entries as $id) {
				$ip_address = get_user_meta($id, 'aiowps_registrant_ip', true);
				$result = AIOWPSecurity_Blocking::add_ip_to_block_list($ip_address, 'registration_spam');
				if (false === $result) {
					if (AIOWPSecurity_Utility_IP::get_user_ip_address() == $ip_address) {
						AIOS_Helper::set_message('aios_list_message', __('You cannot block your own IP address:', 'all-in-one-wp-security-and-firewall') . ' ' . $ip_address, 'error');
					}
					$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_List_Registered_Users::block_selected_ips() - could not block IP : $ip_address", 4);
				} else {
					$total_success++;
				}
			}
			if ($total_success > 0) {
				$msg = __('The selected IP addresses were successfully added to the permanent block list.', 'all-in-one-wp-security-and-firewall');
				AIOS_Helper::set_message('aios_list_message', $msg);
			}
		}
	}

	/**
	 * Grabs the data from database and handles the pagination
	 *
	 * @param boolean $ignore_pagination - whether to not paginate
	 * @return void
	 */
	public function prepare_items($ignore_pagination = false) {
		//First, lets decide how many records per page to show
		$per_page = 100;
		$columns = $this->get_columns();
		$current_page = $this->get_pagenum();
		$offset = ($current_page - 1) * $per_page;
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$search = isset($this->_args['data']['s']) ? sanitize_text_field($this->_args['data']['s']) : '';
		$no_action = '-1';

		// Parameters that are going to be used to order the result.
		$orderby = isset($this->_args['data']['orderby']) ? sanitize_text_field($this->_args['data']['orderby']) : 'user_registered';
		$order = isset($this->_args['data']['order']) ? sanitize_text_field($this->_args['data']['order']) : 'DESC';

		$this->_column_headers = array($columns, $hidden, $sortable);

		$items = array();

		if (isset($this->_args['data']['items'])) {
			if (is_array($this->_args['data']['items'])) {
				foreach ($this->_args['data']['items'] as $item) {
					$items[] = sanitize_text_field($item);
				}
			} else {
				$items[] = sanitize_text_field($this->_args['data']['items']);
			}
		}

		if (isset($this->_args['data']['bulk_apply']) && 'true' === $this->_args['data']['bulk_apply']) {
			$action = isset($this->_args['data']['action']) ? sanitize_text_field($this->_args['data']['action']) : $no_action;

			if ($no_action !== $action) {
				$this->process_bulk_action($action, $items);
			} else {
				AIOS_Helper::set_message('aios_list_message', __('Select a bulk action from the dropdown.', 'all-in-one-wp-security-and-firewall'), 'error');
			}
		}

		//Get registered users which have the special 'aiowps_account_status' meta key set to 'pending'
		if ($ignore_pagination) {
			$result = $this->get_registered_user_data('pending', $search, null, 0, $orderby, $order);
		} else {
			$result = $this->get_registered_user_data('pending', $search, $per_page, $offset, $orderby, $order);
		}

		$total_items = $result['total'];
		$this->items = $result['data'];

		if ($ignore_pagination) return;

		$this->set_pagination_args(array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		));
	}

	/**
	 * Returns all users who have the special 'aiowps_account_status' meta key
	 *
	 * @param string $status   - the status we want to search for
	 * @param string $search   - the search query
	 * @param null   $per_page - how many results per page
	 * @param int    $offset   - the page offset
	 * @param string $orderby  - the column to order the results by
	 * @param string $order    - the order direction, either 'ASC' or 'DESC'
	 *
	 * @return array - an array of users that match the search
	 */
	public function get_registered_user_data($status = '', $search = '', $per_page = null, $offset = 0, $orderby = 'user_registered', $order = 'DESC') {
		global $wpdb;

		$sortable = $this->get_sortable_columns();
		// Fall back to the registration date when an unrecognised column is requested.
		if (!array_key_exists($orderby, $sortable)) $orderby = 'user_registered';
		$order = AIOWPSecurity_Utility::sanitize_value_by_array(strtoupper($order), array('DESC' => '1', 'ASC' => '1'));

		// The account status and the registrant IP both live in usermeta. Join them explicitly so that
		// the search can cover the IP alongside the user table columns, and so that the total count
		// and pagination reflect the filtered set.
		$from_sql = "FROM {$wpdb->users} u
			INNER JOIN {$wpdb->usermeta} status_meta ON status_meta.user_id = u.ID AND status_meta.meta_key = 'aiowps_account_status'
			LEFT JOIN {$wpdb->usermeta} ip_meta ON ip_meta.user_id = u.ID AND ip_meta.meta_key = 'aiowps_registrant_ip'";

		$where_sql = $wpdb->prepare("WHERE status_meta.meta_value = %s", $status);

		if (!empty($search)) {
			$like = '%' . $wpdb->esc_like($search) . '%';
			$where_sql .= $wpdb->prepare(
				" AND (u.ID LIKE %s OR u.user_login LIKE %s OR u.user_email LIKE %s OR u.user_registered LIKE %s OR ip_meta.meta_value LIKE %s)",
				$like,
				$like,
				$like,
				$like,
				$like
			);
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- $from_sql and $where_sql are built with prepare() above; direct access is required to join the usermeta values.
		$user_total = (int) $wpdb->get_var("SELECT COUNT(DISTINCT u.ID) $from_sql $where_sql");

		$limit_sql = '';
		if (null !== $per_page) {
			$limit_sql = $wpdb->prepare("LIMIT %d OFFSET %d", $per_page, $offset);
		}

		// $orderby is restricted to the sortable columns above and $order to ASC/DESC, so both are safe to interpolate.
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- See above; identifiers cannot be parameterised.
		$user_results = $wpdb->get_results("SELECT DISTINCT u.ID, u.user_login, u.user_email, u.user_registered $from_sql $where_sql ORDER BY u.$orderby $order $limit_sql", ARRAY_A);

		$final_data = array();
		foreach ($user_results as $user) {
			$user['ID'] = (int) $user['ID'];
			$user['account_status'] = get_user_meta($user['ID'], 'aiowps_account_status', true);
			$ip = get_user_meta($user['ID'], 'aiowps_registrant_ip', true);
			$user['ip_address'] = empty($ip) ? '' : $ip;
			$final_data[] = $user;
		}

		return array(
			'data' => $final_data,
			'total' => $user_total,
		);
	}

	/**
	 * Handle an incoming ajax request.
	 *
	 * @param bool $return_instead_of_echo Whether to return data or echo it.
	 *
	 * @return array|void
	 */
	public function ajax_response($return_instead_of_echo = false) {
		$response = parent::ajax_response(true);

		if ($response['show_search_box']) {
			// Render the search box for the Refresh data AJAX response.
			ob_start();
			$this->search_box(esc_html__('Search', 'all-in-one-wp-security-and-firewall'), 'search_user_registration');
			$response['search_box'] = ob_get_clean();
		}

		if ($return_instead_of_echo) return $response;

		die(wp_json_encode($response));
	}
}
