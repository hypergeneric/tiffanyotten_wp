<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 * Permanently blocked IP list table class.
 *
 * Handles rendering, searching, sorting, and bulk actions for blocked IP records.
 */
class AIOWPSecurity_List_Blocked_IP extends AIOWPSecurity_Ajax_Data_Table {

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
	 * Renders the permanent blocked ip actions column in the table
	 *
	 * @param array $item - Contains the current item data
	 *
	 * @return void
	 */
	public function column_id($item) {
		$actions = array(
			'unblock' => array(
				'text' => __('Unblock', 'all-in-one-wp-security-and-firewall'),
				'attributes' => array(
					'class' => 'aios-unblock-permanent-ip',
					'data-id' => $item['id'],
					'data-message' => __('Are you sure you want to unblock this IP address?', 'all-in-one-wp-security-and-firewall')
				),
			)
		);

		echo esc_html($item['id']);
		$this->row_actions($actions);
	}

	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />', //Render a checkbox
			'id' => 'ID',
			'blocked_ip' => __('Blocked IP', 'all-in-one-wp-security-and-firewall'),
			'block_reason' => __('Reason', 'all-in-one-wp-security-and-firewall'),
			'created' => __('Date and Time', 'all-in-one-wp-security-and-firewall')
		);
	}

	public function get_sortable_columns() {
		return array(
			'id' => array('id', false),
			'blocked_ip' => array('blocked_ip', false),
			'block_reason' => array('block_reason', false),
			'created' => array('created', false)
		);
	}

	/**
	 * Get the available bulk actions.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'unblock' => __('Unblock', 'all-in-one-wp-security-and-firewall'),
			'unblock_filtered' => __('Unblock filtered', 'all-in-one-wp-security-and-firewall'),
		);
	}

	/**
	 * Process bulk actions from the menu.
	 *
	 * This method processes bulk actions, such as unblocking IP addresses.
	 * Depending on the action passed, it will perform the corresponding task
	 * on the selected items.
	 *
	 * @param string $search_term - The search term used for filtering records.
	 * @param array  $filters     - An array containing filters applied to the records.
	 *                            Currently supports the 'Reason' filter.
	 * @param string $action      - The bulk action to be performed.
	 * @param array  $items       - Optional. An array of records (e.g., IP addresses) to be unblocked. Default is an empty array.
	 *
	 * @global string $aios_list_message Global variable used to store a message to be displayed to the user.
	 *
	 * @return void
	 */
	private function process_bulk_action($search_term, $filters, $action, $items = array()) {
		global $wpdb;

		switch ($action) {
			case 'unblock':
				if (empty($items)) {
				AIOS_Helper::set_message('aios_list_message', __('Select records using the checkboxes', 'all-in-one-wp-security-and-firewall'), 'error');
				} else {
					$this->unblock_ip_address($items);
				}
				break;
			case 'unblock_filtered':
				if (!empty($filters) || '' !== $search_term) {
					$block_table = AIOWPSEC_TBL_PERM_BLOCK;
					$where_sql = $this->get_permanent_blocked_ip_list_where_sql($search_term, $filters);
					$results = $wpdb->get_results("SELECT id FROM {$block_table} {$where_sql}", 'ARRAY_A'); // phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Direct call necessary. Table name is internal and WHERE clause is built from validated/sanitized filter values.
					$items = array_column($results, 'id');

					$this->unblock_ip_address($items);
				} else {
					AIOS_Helper::set_message('aios_list_message', __('Select a reason filter or search term.', 'all-in-one-wp-security-and-firewall'), 'error');
				}
				break;

			default:
				return;
		}
	}

	/**
	 * Get all available block reasons.
	 *
	 * @return array
	 */
	private function get_block_reasons() {
		global $wpdb;

		$block_table = AIOWPSEC_TBL_PERM_BLOCK;
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- Direct query necessary for filter options. Table name is an internal plugin constant and the query contains no user input.
		$reasons = $wpdb->get_col("SELECT DISTINCT block_reason FROM {$block_table} WHERE block_reason != '' ORDER BY block_reason");

		return is_array($reasons) ? $reasons : array();
	}

	/**
	 * Render the block reason filter dropdown.
	 *
	 * @return void
	 */
	private function render_reason_filter() {
		$reasons = $this->get_block_reasons();
		if (empty($reasons)) return;
		?>
		<div class="alignleft actions permanent-block-filter-controls">
			<select name="reason-filter" class="permanent-block-filter-reason">
				<option value="-1" <?php selected(!isset($this->_args['data']['reason-filter'])); ?>>
					<?php esc_html_e('All reasons', 'all-in-one-wp-security-and-firewall'); ?>
				</option>
			<?php foreach ($reasons as $reason) { ?>
				<option value="<?php echo esc_attr($reason); ?>" <?php selected(isset($this->_args['data']['reason-filter']) ? $this->_args['data']['reason-filter'] : '', $reason); ?>>
					<?php echo esc_html($reason); ?>
				</option>
			<?php } ?>
			</select>
			<?php submit_button(esc_html__('Filter', 'all-in-one-wp-security-and-firewall'), 'action', '', false); ?>
		</div>
		<?php
	}

	/**
	 * Outputs extra controls to be displayed between bulk actions and pagination
	 *
	 * @param string $which - where we are outputting content (top or bottom).
	 *
	 * @return void
	 */
	protected function extra_tablenav($which) {
		if ('top' !== $which) return;

		$this->render_reason_filter();
	}

	/**
	 * Deletes one or more records from the AIOWPSEC_TBL_PERM_BLOCK table.
	 *
	 * @param array|string|integer $entries - ids or a single id
	 *
	 * @return void|string
	 */
	public function unblock_ip_address($entries) {
		global $wpdb, $aio_wp_security;

		if (is_array($entries)) {
			$entries = array_filter($entries, 'is_numeric'); // Discard non-numeric ID values.
			if (empty($entries)) {
				AIOS_Helper::set_message('aios_list_message', __('Failed to unblock and delete the selected record(s).', 'all-in-one-wp-security-and-firewall'), 'error');
				return;
			}

			// Chunk to 1000 to avoid hitting MySQL's max_allowed_packet limit on large selections.
			$chunks = array_chunk($entries, 1000);

			foreach ($chunks as $chunk) {
				$in_clause = AIOWPSecurity_Utility::prepare_numeric_in_clause($chunk);
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- Query is prepared via prepare_numeric_in_clause().
				$result = $wpdb->query("DELETE FROM `" . AIOWPSEC_TBL_PERM_BLOCK . "` WHERE id $in_clause");

				if (!$result) {
					$aio_wp_security->debug_logger->log_debug('Database error occurred when deleting rows from Perm Block table. Database error: '.$wpdb->last_error, 4);
					AIOS_Helper::set_message('aios_list_message', __('Failed to unblock and delete the selected record(s).', 'all-in-one-wp-security-and-firewall'), 'error');
					return;
				}
			}

			AIOS_Helper::set_message('aios_list_message', __('Successfully unblocked and deleted the selected record(s).', 'all-in-one-wp-security-and-firewall'));
		} elseif (!empty($entries)) {
			// Delete single record.
			$delete_command = "DELETE FROM " . AIOWPSEC_TBL_PERM_BLOCK . " WHERE id = %d";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- ID is sanitized with absint() and passed through $wpdb->prepare().
			$result = $wpdb->query($wpdb->prepare($delete_command, absint($entries)));
			if (false === $result) {
				// Error on single delete.
				$aio_wp_security->debug_logger->log_debug('Database error occurred when deleting rows from Perm Block table. Database error: '.$wpdb->last_error, 4);
			}

			return $result;
		}
	}

	/**
	 * Build and return the SQL WHERE statement for the permanent blocked IP list.
	 *
	 * @param string $search_term - the search term applied.
	 * @param array  $filters     - the filters applied.
	 *                            Currently supports the 'Reason' filter.
	 *
	 * @return string - the SQL WHERE statement
	 */
	private function get_permanent_blocked_ip_list_where_sql($search_term, $filters) {
		global $wpdb;

		$where = array();

		if (!empty($filters['block_reason'])) {
			$where[] = $wpdb->prepare('`block_reason` = %s', $filters['block_reason']);
		}

		if ('' !== $search_term) {
			$like   = '%' . $wpdb->esc_like($search_term) . '%';
			$search = array();

			// We don't use FILTER_VALIDATE_IP here as we want to support partial IP searches.
			if (preg_match('/^[0-9a-f:.]+$/i', $search_term)) {
				$search[] = $wpdb->prepare('`blocked_ip` LIKE %s', $like);
			}

			$search[] = $wpdb->prepare('`block_reason` LIKE %s', $like);
			$search[] = $wpdb->prepare('`country_origin` LIKE %s', $like);

			$where[] = '(' . implode(' OR ', $search) . ')';
		}

		return empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
	}

	/**
	 * Grabs the data from database and handles the pagination
	 *
	 * @param boolean $ignore_pagination - whether to not paginate
	 *
	 * @return void
	 */
	public function prepare_items($ignore_pagination = false) {
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 100;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$search = isset($this->_args['data']['s']) ? sanitize_text_field($this->_args['data']['s']) : '';
		$no_action = '-1';
		$filters = array();
		if (isset($this->_args['data']['reason-filter']) && $no_action != $this->_args['data']['reason-filter']) $filters['block_reason'] = sanitize_text_field($this->_args['data']['reason-filter']);

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
				$this->process_bulk_action($search, $filters, $action, $items);
			} else {
				AIOS_Helper::set_message('aios_list_message', __('Select a bulk action from the dropdown.', 'all-in-one-wp-security-and-firewall'), 'error');
			}
		}

		// Reset the filter if the selected reason no longer exists after a bulk action.
		if (isset($filters['block_reason']) && !in_array($filters['block_reason'], $this->get_block_reasons(), true)) {
			unset($filters['block_reason']);
			$this->_args['data']['reason-filter'] = $no_action;
		}

		global $wpdb;
		$block_table_name = AIOWPSEC_TBL_PERM_BLOCK;

		// Ordering parameters
		// Parameters that are going to be used to order the result.
		$orderby = isset($this->_args['data']["orderby"]) ? sanitize_text_field($this->_args['data']["orderby"]) : 'id';
		$order = isset($this->_args['data']["order"]) ? sanitize_text_field($this->_args['data']["order"]) : 'DESC';

		$orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
		$order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

		$current_page = $this->get_pagenum();
		$offset = ($current_page - 1) * $per_page;

		$search_query = $this->get_permanent_blocked_ip_list_where_sql($search, $filters);

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is an internal constant and the WHERE clause is built from sanitized search and filter values.
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $block_table_name $search_query");

		if ($ignore_pagination) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is internal constant and query values are sanitized/validated.
			$data = $wpdb->get_results("SELECT * FROM $block_table_name $search_query ORDER BY $orderby $order", 'ARRAY_A');
		} else {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is internal constant, query values are sanitized/validated, and pagination values are generated internally.
			$data = $wpdb->get_results("SELECT * FROM $block_table_name $search_query ORDER BY $orderby $order LIMIT $per_page OFFSET $offset", 'ARRAY_A');
		}

		$this->items = $data;

		if ($ignore_pagination) return;

		$this->set_pagination_args(array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page' => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
		));
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

		ob_start();
		$this->render_reason_filter();
		$response['reason_filter'] = ob_get_clean();

		if ($return_instead_of_echo) return $response;

		die(wp_json_encode($response));
	}
}
