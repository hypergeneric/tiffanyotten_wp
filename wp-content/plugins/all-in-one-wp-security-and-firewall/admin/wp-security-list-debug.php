<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

/**
 * Debug log list table class.
 *
 * Handles rendering and pagination for the debug log table.
 */
class AIOWPSecurity_List_Debug_Log extends AIOWPSecurity_Ajax_Data_Table {

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
	 * Renders logtime column in datetime format as per user setting time zone.
	 *
	 * @param array $item - data for the columns on the current row
	 *
	 * @return void
	 */
	public function column_logtime($item) {
		echo esc_html(AIOWPSecurity_Utility::convert_timestamp($item['logtime']));
	}

	/**
	 * Sets the columns for the table
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'logtime' => __('Date and time', 'all-in-one-wp-security-and-firewall'),
			'level' => __('Level', 'all-in-one-wp-security-and-firewall'),
			'network_id' => __('Network ID', 'all-in-one-wp-security-and-firewall'),
			'site_id' => __('Site ID', 'all-in-one-wp-security-and-firewall'),
			'message' => __('Message', 'all-in-one-wp-security-and-firewall'),
			'type' => __('Type', 'all-in-one-wp-security-and-firewall')
		);
	}

	/**
	 * Sets which of the columns the table data can be sorted by
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'logtime' => array('logtime', false),
			'level' => array('level', false),
			'network_id' => array('network_id', false),
			'site_id' => array('site_id', false),
			'message'=>array('message', false),
			'type' => array('type', false)
		);
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
		if (defined('AIOWPSEC_DEBUG_LOG_PER_PAGE')) {
			$per_page = absint(AIOWPSEC_DEBUG_LOG_PER_PAGE);
		}

		$per_page = empty($per_page) ? 15 : $per_page;
		$current_page = $this->get_pagenum();
		$offset = ($current_page - 1) * $per_page;
		$columns = $this->get_columns();
		$hidden = array('id'); // we really don't need the IDs of the log entries displayed
		if (!is_multisite()) {
			$hidden[] = 'network_id';
			$hidden[] = 'site_id';
		}
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		global $wpdb;

		$debug_log_tbl = AIOWPSEC_TBL_DEBUG_LOG;

		/* -- Ordering parameters -- */

		// Parameters that are going to be used to order the result
		$orderby = isset($this->_args['data']["orderby"]) ? sanitize_text_field($this->_args['data']["orderby"]) : '';
		$order = isset($this->_args['data']["order"]) ? sanitize_text_field($this->_args['data']["order"]) : '';

		$orderby = AIOWPSecurity_Utility::sanitize_value_by_array($orderby, $sortable);
		$order = AIOWPSecurity_Utility::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

		$orderby = sanitize_sql_orderby($orderby);
		$order = sanitize_sql_orderby($order);

		$where_sql = '';
		$params = array();

		if (!is_super_admin()) {
			$where_sql = 'WHERE site_id = %d';
			$params[] = get_current_blog_id();
		}

		$query = "SELECT * FROM {$debug_log_tbl} {$where_sql} ORDER BY {$orderby} {$order}";

		if (!$ignore_pagination) {
			$query .= " LIMIT %d OFFSET %d";
			$params[] = $per_page;
			$params[] = $offset;
		}

		/* phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching */
		$data = $wpdb->get_results($wpdb->prepare($query, $params), 'ARRAY_A');

		$count_query = "SELECT COUNT(*) FROM {$debug_log_tbl} {$where_sql}";

		if (!empty($where_sql)) {
			$count_query = $wpdb->prepare($count_query, $params[0]);
		}

		$total_items = $wpdb->get_var($count_query);
		/* phpcs:enable PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching */

		$this->items = $data;

		if ($ignore_pagination) return;

		$this->set_pagination_args(array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page' => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
		));
	}
}
