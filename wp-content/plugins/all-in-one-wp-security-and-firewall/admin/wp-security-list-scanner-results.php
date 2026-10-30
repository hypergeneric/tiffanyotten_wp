<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!trait_exists('AIOWPSecurity_Scanner_List_Trait')) require_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-scanner-list.php');

class AIOS_List_Scanner_Results extends AIOWPSecurity_Ajax_Data_Table {

	use AIOWPSecurity_Scanner_List_Trait;

	/**
	 * Constructs the table object and sets up its attributes.
	 *
	 * @param array $data - An array containing additional data for the table. Default is an empty array.
	 *
	 * @return void
	 */
	public function __construct($data = array()) {

		//Set parent defaults
		parent::__construct(array(
			'singular' => 'item',
			'plural' => 'items',
			'ajax' => true,
			'data' => $data,
			'custom_bulk_action_selector' => true
		));
	}

	/**
	 * Renders the detected_datetime column.
	 *
	 * @param array $item - Data for the columns on the current row.
	 *
	 * @return void
	 */
	public function column_detected_datetime($item) {
		echo esc_html(AIOWPSecurity_Utility::convert_timestamp($item['detected_datetime']));
		echo '<button type="button" class="toggle-row"><span class="screen-reader-text">'.esc_html__('Show more details', 'all-in-one-wp-security-and-firewall').'</span></button>';
	}

	/**
	 * Renders the location column.
	 *
	 * @param array $item - Data for the columns on the current row.
	 *
	 * @return void
	 */
	public function column_location($item) {
		$actions = array(
			'whitelist' => array(
				'text' => __('Whitelist', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-scanner-results-whitelist',
					'data-location' => $item['location'],
					'data-message' => __('Are you sure you want to whitelist this item?', 'all-in-one-wp-security-and-firewall')
				)
			)
		);

		if ('file' === $item['type']) {
			$actions['reverse'] = array(
				'text' => __('Reverse', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-scanner-results-reverse',
					'data-id' => $item['id'],
					'data-message' => __('Are you sure you want to reverse this file change?', 'all-in-one-wp-security-and-firewall')
				)
			);
		}

		if ('malware' === $item['type'] && AIOWPSecurity_Utility_Permissions::is_premium_installed()) {
			$actions['quarantine'] = array(
				'text' => __('Quarantine', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-scanner-results-quarantine',
					'data-location' => $item['location'],
					'data-message' => __('Are you sure you want to quarantine this file?', 'all-in-one-wp-security-and-firewall')
				)
			);
			$actions['repair'] = array(
				'text' => __('Repair', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-scanner-results-repair',
					'data-location' => $item['location'],
					'data-message' => __('Are you sure you want to repair this file?', 'all-in-one-wp-security-and-firewall')
				)
			);
			$actions['delete'] = array(
				'text' => __('Delete', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-scanner-results-delete-file',
					'data-location' => $item['location'],
					'data-message' => __('Are you sure you want to delete this file?', 'all-in-one-wp-security-and-firewall')
				)
			);
		}

		echo esc_html($item['location']);
		$this->row_actions($actions);
	}

	/**
	 * Sets the columns for the table.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />', // Render a checkbox
			'detected_datetime' => esc_html__('Detected', 'all-in-one-wp-security-and-firewall'),
			'severity' => esc_html__('Severity', 'all-in-one-wp-security-and-firewall'),
			'type' => esc_html__('Type', 'all-in-one-wp-security-and-firewall'),
			'location' => esc_html__('Location', 'all-in-one-wp-security-and-firewall'),
			'description' => esc_html__('Description', 'all-in-one-wp-security-and-firewall'),
		);
	}

	/**
	 * Sets which of the columns the table data can be sorted by.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'detected_datetime' => array('detected_datetime', false),
			'severity' => array('severity', false),
			'type' => array('type', false),
			'location' => array('location', false),
			'description' => array('description', false),
		);
	}

	/**
	 * Get bulk actions for the current WordPress screen.
	 *
	 * @return array An associative array of bulk actions where the keys are action names
	 *               and the values are associative arrays containing the action title and
	 *               whether it is locked due to premium not being installed.
	 */
	public function get_bulk_actions() {
		$bulk_actions = array(
			'bulk_whitelist' => array(
				'title' => __('Whitelist Selected', 'all-in-one-wp-security-and-firewall'),
				'premium' => false
			),
			'bulk_quarantine' => array(
				'title' => __('Quarantine Selected', 'all-in-one-wp-security-and-firewall'),
				'premium' => true
			),
			'bulk_repair' => array(
				'title' => __('Repair Selected', 'all-in-one-wp-security-and-firewall'),
				'premium' => true
			),
			'bulk_delete_files' => array(
				'title' => __('Delete Selected', 'all-in-one-wp-security-and-firewall'),
				'premium' => true
			)
		);

		return $bulk_actions;
	}

	/**
	 * Processes the bulk action request.
	 *
	 * @param string $action       - The bulk action to be performed.
	 * @param array  $selected_ids - An array of record IDs on which the action will be performed. Default is an empty array.
	 *
	 * @return void
	 */
	private function process_bulk_action($action, $selected_ids = array()) {
		if (empty($selected_ids) || !is_array($selected_ids)) {
			AIOS_Helper::set_message('aios_list_message', __('Please select some records using the checkboxes.', 'all-in-one-wp-security-and-firewall'), 'error');
			return;
		}

		switch ($action) {
			case 'bulk_whitelist':
				// Mockup: replace with actual logic
				AIOS_Helper::set_message('aios_list_message', sprintf(__('Bulk whitelist files is not yet implemented.', 'all-in-one-wp-security-and-firewall'), $action), 'error');
				return;
			case 'bulk_quarantine':
				// Mockup: replace with actual logic
				AIOS_Helper::set_message('aios_list_message', sprintf(__('Bulk quarantine files is not yet implemented.', 'all-in-one-wp-security-and-firewall'), $action), 'error');
				return;
			case 'bulk_repair':
				// Mockup: replace with actual logic
				AIOS_Helper::set_message('aios_list_message', sprintf(__('Bulk repair files is not yet implemented.', 'all-in-one-wp-security-and-firewall'), $action), 'error');
				return;
			case 'bulk_delete_files':
				// Mockup: replace with actual logic
				AIOS_Helper::set_message('aios_list_message', sprintf(__('Bulk delete files is not yet implemented.', 'all-in-one-wp-security-and-firewall'), $action), 'error');
				return;
			default:
				/* translators: %s: Invalid requested action */
				AIOS_Helper::set_message('aios_list_message', sprintf(__('The requested bulk action is invalid: %s', 'all-in-one-wp-security-and-firewall'), $action), 'error');
				return;
		}
	}

	/**
	 * Retrieves scanner results and assigns to $this->items.
	 *
	 * @param bool $ignore_pagination
	 *
	 * @return void
	 */
	public function prepare_items($ignore_pagination = false) {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$selected_ids = array();

		if (isset($this->_args['data']['items'])) {
			if (is_array($this->_args['data']['items'])) {
				foreach ($this->_args['data']['items'] as $item) {
					$selected_ids[] = sanitize_text_field($item);
				}
			} else {
				$selected_ids[] = sanitize_text_field($this->_args['data']['items']);
			}
		}

		$no_action = '-1';

		if (isset($this->_args['data']['bulk_apply']) && 'true' === $this->_args['data']['bulk_apply']) {
			$action = isset($this->_args['data']['action']) ? sanitize_text_field($this->_args['data']['action']) : $no_action;

			if ($no_action !== $action) {
				$this->process_bulk_action($action, $selected_ids);
			} else {
				AIOS_Helper::set_message('aios_list_message', __('Please select a bulk action from the dropdown.', 'all-in-one-wp-security-and-firewall'), 'error');
			}
		}

		// Parameters that are going to be used to order the result
		$orderby = isset($this->_args['data']['orderby']) ? sanitize_text_field($this->_args['data']['orderby']) : '';
		$order = isset($this->_args['data']['order']) ? sanitize_text_field($this->_args['data']['order']) : '';

		$orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
		$order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

		// TO DO: Implement term search
		// $search_term = isset($this->_args['data']['s']) ? sanitize_text_field($this->_args['data']['s']) : '';

		// Mockup data - replace with database query
		$data = array(
			array(
				'id' => 5,
				'detected_datetime' => strtotime('2:35pm'),
				'severity' => 'critical',
				'type' => 'malware',
				'location' => 'wp-content/uploads/2024/malicious-script.php',
				'description' => 'Known malware signature detected.'
			),
			array(
				'id' => 4,
				'detected_datetime' => strtotime('10:21pm'),
				'severity' => 'high',
				'type' => 'file',
				'location' => 'wp-admin/admin-ajax.php',
				'description' => 'WordPress core file modified.'
			),
			array(
				'id' => 3,
				'detected_datetime' => strtotime('10:20am'),
				'severity' => 'medium',
				'type' => 'database',
				'location' => 'wp_users table',
				'description' => 'Potential SQL injection vulnerability detected.'
			),
			array(
				'id' => 2,
				'detected_datetime' => strtotime('10:15am'),
				'severity' => 'high',
				'type' => 'blacklist',
				'location' => 'Norton Safe Web',
				'description' => 'Your site is listed on the Norton Safe Web blacklist service.'
			),
			array(
				'id' => 1,
				'detected_datetime' => strtotime('9:03am'),
				'severity' => 'low',
				'type' => 'file',
				'location' => 'wp-content/uploads/2026/05/icon.jpg',
				'description' => 'File added.'
			),
		);

		if (!$ignore_pagination) {
			$current_page = $this->get_pagenum();
			$per_page = 100;
			$total_items = count($data);
			$data = array_slice($data, ($current_page - 1) * $per_page, $per_page);

			$this->set_pagination_args(array(
				'total_items' => $total_items,
				'per_page' => $per_page,
				'total_pages' => ceil($total_items / $per_page)
			));
		}

		$this->items = $data;
	}

}
