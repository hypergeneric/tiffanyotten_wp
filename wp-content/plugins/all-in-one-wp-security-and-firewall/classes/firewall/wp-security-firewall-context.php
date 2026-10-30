<?php
namespace AIOWPS\Firewall;

/**
 * The firewall can be loaded from several different entry points. This class detects from which one the firewall loaded from.
 */
class Context {

	/**
	 * Possible contexts where the firewall can be loaded
	 */
	const DIRECTIVE      = 'directive';
	const PLUGINS_LOADED = 'plugins_loaded';
	const WP_CONFIG      = 'wp-config';
	const MU_PLUGIN      = 'mu-plugin';

	/**
	 * Get the current entry point where the firewall was loaded from
	 *
	 * @return string
	 */
	public static function current() {

		$incs  = get_included_files();
		$index = self::get_bootstrap_index($incs);

		$is_setup = (-1 !== $index);

		if (!$is_setup) return self::PLUGINS_LOADED;

		if (0 === $index) return self::DIRECTIVE;

		if (preg_match('/wp-config\.php$/i', $incs[$index-1])) {
			return self::WP_CONFIG;
		}
		
		if (preg_match('/aios-firewall-loader\.php$/', $incs[$index-1])) {
			return self::MU_PLUGIN;
		}

		return self::DIRECTIVE;
		
	}

	/**
	 * Check if we're in a context safe to run WordPress functions
	 *
	 * @return boolean
	 */
	public static function wordpress_safe() {
		return (function_exists('do_action') && function_exists('apply_filters'));
	}

	/**
	 * Check whether the entry point for the firewall is from within WordPress
	 *
	 * @return boolean
	 */
	public static function wordpress_entry() {
		return (self::plugins_loaded() || self::mu_plugin());
	}

	/**
	 * Check if the current entry point is `plugins_loaded`
	 *
	 * @return boolean
	 */
	public static function plugins_loaded() {
		return (self::PLUGINS_LOADED === self::current());
	}

	/**
	 * Check if the current entry point is `directive` (i.e: auto_prepend_file)
	 *
	 * @return boolean
	 */
	public static function directive() {
		return (self::DIRECTIVE === self::current());
	}

	/**
	 * Check if the current entry point is `wp_config`
	 *
	 * @return boolean
	 */
	public static function wp_config() {
		return (self::WP_CONFIG === self::current());
	}

	/**
	 * Check if the current entry point is `mu_plugin`
	 *
	 * @return boolean
	 */
	public static function mu_plugin() {
		return (self::MU_PLUGIN === self::current());
	}

	/**
	 * Locate the bootstrap file's index
	 *
	 * @param array $incs
	 * @return int
	 */
	private static function get_bootstrap_index(array $incs) {
		foreach ($incs as $index => $file) {
		   if (preg_match('/aios-bootstrap\.php$/', $file)) {
				return $index;
		   }
		}
		return -1;
	}
}
