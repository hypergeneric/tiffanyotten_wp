<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Handles the logged in users list table.
 */
class AIOWPSecurity_List_Logged_In_Users extends AIOWPSecurity_Ajax_Data_Table {

	/**
	 * Constructs the table object and sets up its attributes.
	 *
	 * @param array $data - An array containing additional data for the table. Default is an empty array.
	 * @return void
	 */
	public function __construct($data = array()) {

		// Set parent defaults
		parent::__construct(array(
			'singular' => 'item', // singular name of the listed records
			'plural' => 'items',  // plural name of the listed records
			'ajax' => true,       // does this table support ajax?
			'data' => $data       // Request data
		));
	}

	/**
	 * Renders user id column html.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_user_id($item) {
		//Build row actions
		$actions = array(
			'logout' => array(
				'text' => __('Force logout', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-force-logout-user',
					'data-user-id' => $item['user_id'],
					'data-message' => __('Are you sure you want to force this user to be logged out of this session?', 'all-in-one-wp-security-and-firewall')
				)
			)
		);

		echo esc_html($item['user_id']);
		$this->row_actions($actions);
	}

	/**
	 * Sets the columns for the table
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox">',
			'user_id'    => __('User ID', 'all-in-one-wp-security-and-firewall'),
			'username'   => __('Login name', 'all-in-one-wp-security-and-firewall'),
			'ip_address' => __('IP address', 'all-in-one-wp-security-and-firewall'),
			'site_id'    => __('Site ID', 'all-in-one-wp-security-and-firewall'),
		);
		return $columns;
	}

	/**
	 * Returns cb column html to be rendered.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return string - the html to be rendered
	 */
	public function column_cb($item) {
		printf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			esc_attr($this->_args['singular']), // Let's simply repurpose the table's singular label
			esc_attr($item['user_id'])
		);
	}

	/**
	 * Sets which of the columns the table data can be sorted by
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'user_id' => array('user_id',false),
			'username' => array('username',false),
			'ip_address' => array('ip_address',false),
			'site_id' => array('site_id',false),
		);
	}

	/**
	 * Adds a bulk action user interface
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'force_logout_all' => __('Logout all', 'all-in-one-wp-security-and-firewall'),
			'force_logout_selected' => __('Logout selected', 'all-in-one-wp-security-and-firewall'),
		);
	}

	/**
	 * Process bulk actions from the menu.
	 *
	 * This method processes bulk actions such as forcing user logouts.
	 * Depending on the action passed, it will either log out all users
	 * or only selected users.
	 *
	 * @param string $action The bulk action to be performed.
	 *                       Possible values are 'force_logout_all' or 'force_logout_selected'.
	 * @param array  $items  Optional. An array of user IDs to be logged out when the action is 'force_logout_selected'.
	 *                       Defaults to an empty array.
	 *
	 * @global string $aios_list_message Global variable used to store a message to be displayed to the user.
	 *
	 * @return void
	 */
	private function process_bulk_action($action, $items = array()) {
		if ('force_logout_all' === $action) {
			$this->force_user_logout(array(), true);
		} elseif ('force_logout_selected' === $action) {
			if (empty($items)) {
				AIOS_Helper::set_message('aios_list_message', __('Select some users using the checkboxes', 'all-in-one-wp-security-and-firewall'), 'error');
			} else {
				if (is_array($items)) $this->force_user_logout($items);
			}
		}
	}

	/**
	 * This function will force selected user(s) to be logged out.
	 *
	 * @param int|array $users      - id of selected user or array of user ids to be logged out
	 * @param bool      $logout_all - Boolean to show if all users should be logged out
	 *
	 * @return void|string
	 */
	public function force_user_logout($users, $logout_all = false) {
		global $wpdb, $aio_wp_security;

		$logged_in_users_table = AIOWPSEC_TBL_LOGGED_IN_USERS;
		$current_user_id = get_current_user_id();

		if ($logout_all) {
			// get all user_id(except for the admin) in the table and make it an array for users.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- No suitable alternative available because direct database access is required for this custom table query.
			$users = $wpdb->get_col(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name interpolation is required because %i is only supported in WordPress 6.2+.
					"SELECT user_id FROM $logged_in_users_table WHERE user_id != %d",
					$current_user_id
				)
			);

			$message = __('All users have been successfully logged out.', 'all-in-one-wp-security-and-firewall');
		}

		if (is_array($users)) {

			if (empty($users)) {
				AIOS_Helper::set_message('aios_list_message', __("No valid user id was passed", 'all-in-one-wp-security-and-firewall'), 'error');
				return;
			}

			$errors = 0;

			// Escape the user IDs for security
			$users = array_map('esc_sql', $users);

			foreach ($users as $user_id) {
				if (is_numeric($user_id) && !is_super_admin($user_id) && AIOWPSecurity_Utility::is_user_member_of_blog($user_id) && $current_user_id != $user_id) {
					if ($aio_wp_security->user_login_obj->delete_logged_in_user($user_id)) {
						$this->logout_user($user_id);
						continue;
					}
				}
				$errors++;
			}

			if ($errors > 0) {
				AIOS_Helper::set_message('aios_list_message', __("Some users were not logged out due to the ID being invalid, or them being a super admin or a member of a different subsite on a multisite", 'all-in-one-wp-security-and-firewall'), 'error');
				return;
			}

			AIOS_Helper::set_message('aios_list_message', $logout_all ? $message : __('The selected user(s) has been successfully logged out', 'all-in-one-wp-security-and-firewall'));
		}
	}

	/**
	 * This function handles logging out a user using user_id
	 *
	 * @param int $user_id - id of user being logged out
	 *
	 * @return void
	 */
	public function logout_user($user_id) {
		$user_id = absint($user_id);
		$manager = WP_Session_Tokens::get_instance($user_id);
		$manager->destroy_all();
	}

	/**
	 * Prepares the items for the logged in users table
	 *
	 * @param bool $ignore_pagination - this is to check if data should be paginated or not
	 *
	 * @return void
	 */
	public function prepare_items($ignore_pagination = false) {
		global $wpdb;

		//First, lets decide how many records per page to show
		$per_page = 100;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$logged_in_users_table = AIOWPSEC_TBL_LOGGED_IN_USERS;
		$current_page = $this->get_pagenum();
		$offset = ($current_page - 1) * $per_page;

		// Parameters that are going to be used to order the result.
		$orderby = isset($this->_args['data']['orderby']) ? sanitize_text_field($this->_args['data']['orderby']) : 'created';
		$order = isset($this->_args['data']['order']) ? sanitize_text_field($this->_args['data']['order']) : 'DESC';

		$orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
		$order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

		$orderby = sanitize_sql_orderby($orderby);
		$order = sanitize_sql_orderby($order);

		$no_action = '-1';

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
		} else {
			$items = null;
		}

		if (isset($this->_args['data']['bulk_apply']) && 'true' === $this->_args['data']['bulk_apply']) {
			$action = isset($this->_args['data']['action']) ? sanitize_text_field($this->_args['data']['action']) : $no_action;

			if ($no_action !== $action) {
				$this->process_bulk_action($action, $items);
			} else {
				AIOS_Helper::set_message('aios_list_message', __('Select a bulk action from the dropdown.', 'all-in-one-wp-security-and-firewall'), 'error');
			}
		}

		$where_sql = $this->get_where_sql();
		// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- Table name interpolation is required because %i is only supported in WordPress 6.2+.
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM `{$logged_in_users_table}` $where_sql");

		if ($ignore_pagination) {
			// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- Table name interpolation is required because %i is only supported in WordPress 6.2+.
			$data = $wpdb->get_results("SELECT * FROM `{$logged_in_users_table}` $where_sql ORDER BY $orderby $order", 'ARRAY_A');
		} else {
			// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- Table name interpolation is required because %i is only supported in WordPress 6.2+.
			$data = $wpdb->get_results("SELECT * FROM `{$logged_in_users_table}` $where_sql ORDER BY $orderby $order LIMIT $per_page OFFSET $offset", 'ARRAY_A');
		}

		$this->items = $data;

		if ($ignore_pagination) return;

		$this->set_pagination_args(array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		));
	}

	/**
	 * This function will build and return the SQL WHERE statement
	 *
	 * @return string - the SQL WHERE statement
	 */
	private function get_where_sql() {
		if (is_main_site() && is_super_admin()) return '';

		return is_multisite() ? sprintf("WHERE site_id = %d", get_current_blog_id()) : '';
	}
}
