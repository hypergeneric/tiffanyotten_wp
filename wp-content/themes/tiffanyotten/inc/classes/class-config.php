<?php

class tiffanyotten_Config {

	private static $config_data;
	private static $instance = null;

	function __construct() {
		$config_url = get_template_directory() . '/' . '.config.json';
		if ( ! file_exists( $config_url ) ) {
			$config_url = get_template_directory() . '/' . '.config.default.json';
		}
		self::$config_data = json_decode( file_get_contents( $config_url ) );
	}

	public static function config() {
		if ( self::$instance == null ) {
			self::$instance = new tiffanyotten_Config();
		}
		return self::$config_data;
	}
	
}
