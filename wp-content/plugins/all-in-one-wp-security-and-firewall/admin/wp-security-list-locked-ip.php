<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Locked IP list table class.
 *
 * Handles rendering, bulk actions, pagination, and AJAX responses for the locked IP table.
 */
class AIOWPSecurity_List_Locked_IP extends AIOWPSecurity_Ajax_Data_Table {

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
	 * Renders created column in datetime format as per user setting time zone.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_created($item) {
		echo esc_html(AIOWPSecurity_Utility::convert_timestamp($item['created']));
	}
	
	/**
	 * Renders released column in datetime format as per user setting time zone.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_released($item) {
		echo esc_html(AIOWPSecurity_Utility::convert_timestamp($item['released']));
	}

	/**
	 * Renders the locked ip actions column in the table
	 *
	 * @param array $item - Contains the current item data
	 *
	 * @return void
	 */
	public function column_failed_login_ip($item) {
		$actions = array(
			'unlock' => array(
				'text' => __('Unlock', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-unlock-ip-button',
					'data-ip' => $item['failed_login_ip'],
					'data-message' => __('Are you sure you want to unlock this address range?', 'all-in-one-wp-security-and-firewall')
					
				)
			),
			'delete' => array(
				'text' => __('Delete', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-delete-locked-ip-record',
					'data-id' => $item['id'],
					'data-message' => __('Are you sure you want to delete this item?', 'all-in-one-wp-security-and-firewall')
					
				)
			),
		);
		
		echo esc_html($item['failed_login_ip']);
		$this->row_actions($actions);
	}

	/**
	 * Renders ip_lookup_result column html.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_ip_lookup_result($item) {
		if (empty($item['ip_lookup_result'])) {
			esc_html_e('There is no IP lookup result available.', 'all-in-one-wp-security-and-firewall');
			return;
		}
		
		$ip_lookup_result = json_decode($item['ip_lookup_result'], true);

		// check that the json decode worked
		if (null === $ip_lookup_result) {
			esc_html_e('There is no IP lookup result available.', 'all-in-one-wp-security-and-firewall');
			return;
		}

		foreach ($ip_lookup_result as $key => $value) {
			$ip_lookup_result[$key] = empty($value) ? esc_html__('Not Found', 'all-in-one-wp-security-and-firewall') : esc_html($value);
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- PCP warning. Part of error reporting system.
		$ip_lookup_result = print_r($ip_lookup_result, true);

		printf('<a href="#TB_inline?&inlineId=trace-%s" title="%s" class="thickbox">%s</a>',
			esc_attr($item['id']),
			esc_attr__('IP lookup result', 'all-in-one-wp-security-and-firewall'),
			esc_html__('Show result', 'all-in-one-wp-security-and-firewall')
		);
		
		printf('<div id="trace-%s" style="display: none"><pre>%s</pre></div>',
			esc_attr($item['id']),
			esc_html($ip_lookup_result)
		);

	}

	/**
	 * Sets the columns for the table
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />', //Render a checkbox
			'failed_login_ip' => __('Locked IP/range', 'all-in-one-wp-security-and-firewall'),
			'user_id' => __('User ID', 'all-in-one-wp-security-and-firewall'),
			'user_login' => __('Username', 'all-in-one-wp-security-and-firewall'),
			'lock_reason' => __('Reason', 'all-in-one-wp-security-and-firewall'),
			'created' => __('Date locked', 'all-in-one-wp-security-and-firewall'),
			'released' => __('Release date', 'all-in-one-wp-security-and-firewall'),
			'ip_lookup_result' => __('IP lookup result', 'all-in-one-wp-security-and-firewall')
		);
	}

	/**
	 * This function returns sortable columns
	 *
	 * @return array[]
	 */
	public function get_sortable_columns() {
		return array(
			'failed_login_ip' => array('failed_login_ip',false),
			'user_id' => array('user_id',false),
			'user_login' => array('user_login',false),
			'lock_reason' => array('lock_reason',false),
			'created' => array('created',false),
			'released' => array('released',false)
		);
	}

	/**
	 * This returns the bulk actions for the table
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'unlock' => __('Unlock', 'all-in-one-wp-security-and-firewall'),
			'delete' => __('Delete', 'all-in-one-wp-security-and-firewall'),
		);
	}

	/**
	 * Processes bulk actions for the locked IP list table.
	 *
	 * Handles supported bulk actions such as deleting records and unlocking IP addresses for the selected table items.
	 *
	 * @param string $action - The bulk action to be performed.
	 * @param array  $items  - An array of record IDs on which the action will be performed. Default is an empty array.
	 *
	 * @return void
	 */
	private function process_bulk_action($action, $items = array()) {

		if (empty($items)) {
			AIOS_Helper::set_message('aios_list_message', __('Select records using the checkboxes', 'all-in-one-wp-security-and-firewall'), 'error');
			return;
		}

		// Process delete bulk actions.
		if ('delete' === $action) {
			$this->delete_lockout_records($items);
		}

		// Process unlock bulk actions.
		if ('unlock' === $action) {
			$this->unlock_ips($items);
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended -- PCP warning. This is the nonce.
	}

	/**
	 * Unlocks multiple IP addresses by modifying the released column of records in the AIOWPSEC_TBL_LOGIN_LOCKOUT table.
	 *
	 * @param array $entries IDs that correspond to IP addresses in the AIOWPSEC_TBL_LOGIN_LOCKOUT table.
	 *
	 * @return void
	 */
	public function unlock_ips($entries) {
		global $wpdb;

		$lockout_table = AIOWPSEC_TBL_LOGIN_LOCKOUT;

		// Unlock multiple records
		$in_clause = AIOWPSecurity_Utility::prepare_numeric_in_clause($entries);
		if (false === $in_clause) {
			return;
		}
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- Query is prepared via prepare_numeric_in_clause().
		$result = $wpdb->query("UPDATE `$lockout_table` SET released = UNIX_TIMESTAMP() WHERE id $in_clause");

		if (false !== $result) {
			AIOS_Helper::set_message('aios_list_message', __('The selected IP entries were unlocked successfully.', 'all-in-one-wp-security-and-firewall'));
		} else {
			AIOS_Helper::set_message('aios_list_message', __('The selected IP entries could not be unlocked', 'all-in-one-wp-security-and-firewall'), 'error');
		}
	}

	/**
	 * Deletes one or more records from the AIOWPSEC_TBL_LOGIN_LOCKOUT table.
	 *
	 * @param array|string|integer $entries - ids or a single id.
	 *
	 * @return void
	 */
	public function delete_lockout_records($entries) {
		global $wpdb, $aio_wp_security;
		$lockout_table = AIOWPSEC_TBL_LOGIN_LOCKOUT;
		$result = false;

		if (is_array($entries)) {
			// Delete multiple records
			$in_clause = AIOWPSecurity_Utility::prepare_numeric_in_clause($entries);
			if (false === $in_clause) {
				AIOS_Helper::set_message('aios_list_message', AIOWPSecurity_Admin_Menu::show_msg_record_not_deleted_st(true, true), 'error');
				return;
			}
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- Query is prepared via prepare_numeric_in_clause().
			$result = $wpdb->query("DELETE FROM `$lockout_table` WHERE id $in_clause");

		} elseif (!empty($entries)) {
			// Delete single record
			$entry_id = absint($entries);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- PCP warning. Ignore.
			$result = $wpdb->delete($lockout_table, array('id' => $entry_id), array('%d'));

		}

		if (false !== $result) {
			AIOS_Helper::set_message('aios_list_message', AIOWPSecurity_Admin_Menu::show_msg_record_deleted_st(true, true));
		} else {
			// Error on bulk/single delete.
			$aio_wp_security->debug_logger->log_debug('Database error occurred when deleting rows from login lockout table. Database error: '.$wpdb->last_error, 4);
			AIOS_Helper::set_message('aios_list_message', AIOWPSecurity_Admin_Menu::show_msg_record_not_deleted_st(true, true), 'error');

		}
	}

	/**
	 * Retrieves all items from AIOWPSEC_TBL_LOGIN_LOCKOUT. It may paginate and then assigns to $this->items.
	 *
	 * @param Boolean $ignore_pagination - whether to not paginate
	 *
	 * @return Void
	 */
	public function prepare_items($ignore_pagination = false) {
		global $wpdb;

		$lockout_table = AIOWPSEC_TBL_LOGIN_LOCKOUT;
		$no_action = '-1';
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

		// How many records per page to show
		$per_page = 100;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		// Parameters that are going to be used to order the result.
		$orderby = isset($this->_args['data']["orderby"]) ? sanitize_text_field($this->_args['data']["orderby"]) : 'created';
		$order = isset($this->_args['data']["order"]) ? sanitize_text_field($this->_args['data']["order"]) : 'DESC';

		$orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
		$order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

		$current_page = $this->get_pagenum();
		$offset = ($current_page - 1) * $per_page;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- PCP warning. Ignore.
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$lockout_table} WHERE `released` > UNIX_TIMESTAMP()");

		if ($ignore_pagination) {
			// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- PCP warning. Ignore.
			$data = $wpdb->get_results("SELECT * FROM {$lockout_table} WHERE `released` > UNIX_TIMESTAMP() ORDER BY {$orderby} {$order}", 'ARRAY_A');
		} else {
			// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- PCP warning. Ignore.
			$data = $wpdb->get_results("SELECT * FROM {$lockout_table} WHERE `released` > UNIX_TIMESTAMP() ORDER BY {$orderby} {$order} LIMIT {$per_page} OFFSET {$offset}", 'ARRAY_A');
		}

		$this->items = $data;

		if ($ignore_pagination) return;

		$this->set_pagination_args(array(
			'total_items' => $total_items,  // WE have to calculate the total number of items
			'per_page'    => $per_page,  // WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items / $per_page)  // WE have to calculate the total number of pages
		));
	}
}
