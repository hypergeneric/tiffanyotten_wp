<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Comment spammer IP list table class.
 *
 * Handles rendering, sorting, pagination, and bulk actions
 * for spammer IP addresses detected from spam comments.
 */
class AIOWPSecurity_List_Comment_Spammer_IP extends AIOWPSecurity_Ajax_Data_Table {

	/**
	 * Constructs the table object and sets up its attributes.
	 *
	 * @param array $data - An array containing additional data for the table. Default is an empty array.
	 * @return void
	 */
	public function __construct($data = array()) {

		// Set parent defaults.
		parent::__construct(array(
			'singular' => 'item', // singular name of the listed records.
			'plural' => 'items',  // plural name of the listed records.
			'ajax' => true,       // does this table support ajax?
			'data' => $data       // Request data.
		));
	}

	/**
	 * Renders the `comment_author_IP` column's html.
	 *
	 * @param array $item - Data for the columns on the current row.
	 *
	 * @return void
	 */
	public function column_comment_author_IP($item) {

		echo esc_html($item['comment_author_IP']);

		if ('blocked' !== $item['status']) {
			$actions = array(
				'block' => array(
					'text' => __('Block', 'all-in-one-wp-security-and-firewall'),
					'attributes' => array(
						'class' => 'aios-block-author-ip',
						'data-ip' => $item['comment_author_IP'],
						'data-message' => __('Are you sure you want to permanently block this IP address?', 'all-in-one-wp-security-and-firewall'),
					)
				)
			);
			$this->row_actions($actions);
		}
	}

	public function column_cb($item) {
		printf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			esc_attr($this->_args['singular']),  //Let's simply repurpose the table's singular label
			esc_attr($item['comment_author_IP'])
		);
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />', //Render a checkbox
			'comment_author_IP' => __('Spammer IP', 'all-in-one-wp-security-and-firewall'),
			'amount' => __('Number of spam comments from this IP', 'all-in-one-wp-security-and-firewall'),
			'status' => __('Status', 'all-in-one-wp-security-and-firewall'),
		);
		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'comment_author_IP' => array('comment_author_IP',false),
			'amount' => array('amount',false),
			'status' => array('status',false),
		);
		return $sortable_columns;
	}

	/**
	 * Returns the bulk actions of the table as an associative array of names => labels.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'block' => __('Block', 'all-in-one-wp-security-and-firewall')
		);
	}

	/**
	 * Processes bulk actions for blocking items, such as spammer IPs.
	 *
	 * This function handles the "block" bulk action. If no items are selected, it
	 * sets an error message. Otherwise, it calls the method to block the selected
	 * IPs or items.
	 *
	 * @param string $action The bulk action to be performed (e.g., 'block').
	 * @param array  $items  The list of items to be processed (e.g., IP addresses). Default is an empty array.
	 *
	 * @return void
	 */
	private function process_bulk_action($action, $items = array()) {
		if ('block' === $action) {
			// Process block bulk actions
			if (empty($items)) {
				AIOS_Helper::set_message('aios_list_message', __('Select records using the checkboxes', 'all-in-one-wp-security-and-firewall'), 'error');
			} else {
				$this->block_spammer_ip_records($items);
			}
		}
	}

	/**
	 * This function will add the selected IP addresses to the blacklist.
	 *
	 * @param int|array $entries - either an array of IDs or a single ID of ip to be blocked
	 *
	 * @return void
	 */
	public function block_spammer_ip_records($entries) {
		if (is_array($entries)) {
			$error = 0;
			$entries = array_filter(filter_var_array($entries, FILTER_VALIDATE_IP));
			//Bulk selection using checkboxes were used
			foreach ($entries as $ip_add) {
				$result = AIOWPSecurity_Blocking::add_ip_to_block_list($ip_add, 'spam');
				if (!$result) $error++;
			}

			if ($error > 0) {
				AIOS_Helper::set_message('aios_list_message', __('Some of the selected IP addresses could not be blocked because they are your own.', 'all-in-one-wp-security-and-firewall'), 'error');
			} else {
				AIOS_Helper::set_message('aios_list_message', __('The selected IP addresses are now permanently blocked.', 'all-in-one-wp-security-and-firewall'));
			}
		}
	}

	/**
	 * This function prepare the items rendered on the table
	 *
	 * @return void
	 */
	public function prepare_items() {
		//First, lets decide how many records per page to show
		$per_page = 100;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$no_action = '-1';
		$items = array();

		if (isset($this->_args['data']['items'])) {
			if (is_array($this->_args['data']['items'])) {
				foreach ($this->_args['data']['items'] as $item) {
					$sanitized_item = sanitize_text_field($item);
					$items[] = $sanitized_item;
				}
			} else {
				$sanitized_item = sanitize_text_field($this->_args['data']['items']);
				$items[] = $sanitized_item;
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

		global $wpdb;
		global $aio_wp_security;
		$minimum_comments_per_ip = $aio_wp_security->configs->get_value('aiowps_spam_ip_min_comments');
		if (empty($minimum_comments_per_ip)) {
			$minimum_comments_per_ip = 5;
		}
		
		// Parameters that are going to be used to order the result
		$orderby = isset($this->_args['data']['orderby']) ? sanitize_text_field($this->_args['data']['orderby']) : '';
		$order = isset($this->_args['data']['order']) ? sanitize_text_field($this->_args['data']['order']) : '';

		$orderby = empty($orderby) ? 'amount' : $orderby;

		$orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
		$order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

		// status is not a key in the database so we don't want to sort the database results, but sort the array later
		if ('status' == $orderby) {
			$sql = $wpdb->prepare("SELECT comment_author_IP, COUNT(*) AS amount
				FROM     $wpdb->comments 
				WHERE    comment_approved = 'spam'
				GROUP BY comment_author_IP
				HAVING   amount >= %d
				", $minimum_comments_per_ip);
		} else {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $orderby cannot be prepared.
			$sql = $wpdb->prepare("SELECT comment_author_IP, COUNT(*) AS amount
				FROM     $wpdb->comments 
				WHERE    comment_approved = 'spam'
				GROUP BY comment_author_IP
				HAVING   amount >= %d
				ORDER BY $orderby $order
				", $minimum_comments_per_ip);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $orderby cannot be prepared.
		}

		// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- Preparing done in conditional above.
		$data = $wpdb->get_results($sql, ARRAY_A);

		// Get all permanently blocked IP addresses
		$block_list = AIOWPSecurity_Blocking::get_list_blocked_ips();

		foreach ($data as $key => $value) {
			if (in_array($value['comment_author_IP'], $block_list)) {
				$data[$key]['status'] = 'blocked';
			} else {
				$data[$key]['status'] = 'not blocked';
			}
		}

		if ('status' == $orderby) {
			$keys = array_column($data, 'status');
			if ('asc' == $order) {
				array_multisort($keys, SORT_ASC, SORT_STRING, $data);
			} else {
				array_multisort($keys, SORT_DESC, SORT_STRING, $data);
			}
		}

		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
		$this->items = $data;
		$this->set_pagination_args(array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page' => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
		));
	}
}
