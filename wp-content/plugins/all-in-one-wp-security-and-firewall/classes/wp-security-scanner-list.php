<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (trait_exists('AIOWPSecurity_Scanner_List_Trait')) return;

trait AIOWPSecurity_Scanner_List_Trait {

	/**
	 * Renders the severity column.
	 *
	 * @param array $item - Data for the columns on the current row.
	 *
	 * @return void
	 */
	public function column_severity($item) {
		switch ($item['severity']) {
			case 'critical':
				echo '<span class="aios-scanner-results-severity aios-scanner-critical-severity">'.esc_html__('Critical', 'all-in-one-wp-security-and-firewall').'</span>';
				break;
			case 'high':
				echo '<span class="aios-scanner-results-severity aios-scanner-high-severity">'.esc_html__('High', 'all-in-one-wp-security-and-firewall').'</span>';
				break;
			case 'medium':
				echo '<span class="aios-scanner-results-severity aios-scanner-medium-severity">'.esc_html__('Medium', 'all-in-one-wp-security-and-firewall').'</span>';
				break;
			case 'obfuscation':
				echo '<span class="aios-scanner-results-severity aios-scanner-medium-severity">'.esc_html__('Obfuscation', 'all-in-one-wp-security-and-firewall').'</span>';
				break;
			case 'low':
				echo '<span class="aios-scanner-results-severity aios-scanner-low-severity">'.esc_html__('Low', 'all-in-one-wp-security-and-firewall').'</span>';
				break;
		}
	}

	/**
	 * Renders the type column.
	 *
	 * @param array $item - Data for the columns on the current row.
	 *
	 * @return void
	 */
	public function column_type($item) {
		switch ($item['type']) {
			case 'file':
				esc_html_e('File change', 'all-in-one-wp-security-and-firewall');
				break;
			case 'database':
				esc_html_e('Database vulnerability', 'all-in-one-wp-security-and-firewall');
				break;
			case 'malware':
				esc_html_e('Malware', 'all-in-one-wp-security-and-firewall');
				break;
			case 'blacklist':
				esc_html_e('Blacklisted', 'all-in-one-wp-security-and-firewall');
				break;
		}
	}
}
