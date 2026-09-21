<?php
/**
 * Plugin Name: Compiled Rogue Inventory Monitor
 * Description: Read-only WordPress software inventory endpoint for external vulnerability monitoring.
 * Version: 1.0.0
 * Author: Compiled Rogue
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the inventory endpoint.
 *
 * The plugin intentionally does nothing else:
 * - no cron
 * - no outbound requests
 * - no database writes
 * - no admin UI
 * - no update/remediation functionality
 */
add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'compiled-rogue/v1',
			'/inventory',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'compiled_rogue_inventory_response',
				'permission_callback' => 'compiled_rogue_inventory_authorize',
			]
		);
	}
);

/**
 * Authenticate an inventory request using the custom key header or a bearer token.
 *
 * Define the token in wp-config.php:
 *
 * define( 'COMPILED_ROGUE_MONITOR_KEY', 'your-long-random-secret' );
 *
 * The custom header is preferred because some Apache/PHP stacks strip the
 * standard Authorization header before WordPress receives it.
 *
 * @param WP_REST_Request $request REST request.
 * @return true|WP_Error
 */
function compiled_rogue_inventory_authorize( WP_REST_Request $request ) {
	if (
		! defined( 'COMPILED_ROGUE_MONITOR_KEY' )
		|| ! is_string( COMPILED_ROGUE_MONITOR_KEY )
		|| COMPILED_ROGUE_MONITOR_KEY === ''
	) {
		return new WP_Error(
			'compiled_rogue_monitor_unconfigured',
			'Inventory monitoring is not configured.',
			[ 'status' => 403 ]
		);
	}

	$custom_key = trim( (string) $request->get_header( 'x-compiled-rogue-key' ) );
	if ( $custom_key !== '' && hash_equals( COMPILED_ROGUE_MONITOR_KEY, $custom_key ) ) {
		return true;
	}

	$authorization = trim( (string) $request->get_header( 'authorization' ) );

	if ( ! preg_match( '/^Bearer\s+(.+)$/i', $authorization, $matches ) ) {
		return new WP_Error(
			'compiled_rogue_monitor_unauthorized',
			'Unauthorized.',
			[ 'status' => 401 ]
		);
	}

	$provided_key = trim( $matches[1] );

	if (
		$provided_key === ''
		|| ! hash_equals( COMPILED_ROGUE_MONITOR_KEY, $provided_key )
	) {
		return new WP_Error(
			'compiled_rogue_monitor_unauthorized',
			'Unauthorized.',
			[ 'status' => 401 ]
		);
	}

	return true;
}

/**
 * Return the installed WordPress software inventory.
 *
 * @return WP_REST_Response
 */
function compiled_rogue_inventory_response(): WP_REST_Response {
	// get_plugins() and get_mu_plugins() are defined here.
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$plugins    = compiled_rogue_inventory_plugins();
	$mu_plugins = compiled_rogue_inventory_mu_plugins();
	$themes     = compiled_rogue_inventory_themes();

	$response = [
		'schema_version' => 1,
		'generated_at'   => gmdate( 'c' ),
		'wordpress'      => [
			'version'   => get_bloginfo( 'version' ),
			'multisite' => is_multisite(),
		],
		'php'            => [
			'version' => PHP_VERSION,
		],
		'plugins'        => $plugins,
		'mu_plugins'     => $mu_plugins,
		'themes'         => $themes,
	];

	return rest_ensure_response( $response );
}

/**
 * Build an inventory of all installed standard plugins.
 *
 * @return array<int,array<string,mixed>>
 */
function compiled_rogue_inventory_plugins(): array {
	$installed       = get_plugins();
	$active_plugins  = (array) get_option( 'active_plugins', [] );
	$network_active  = is_multisite() ? array_keys( (array) get_site_option( 'active_sitewide_plugins', [] ) ) : [];
	$inventory       = [];

	foreach ( $installed as $file => $data ) {
		$inventory[] = [
			'file'           => $file,
			'slug'           => compiled_rogue_inventory_plugin_slug( $file ),
			'name'           => isset( $data['Name'] ) ? (string) $data['Name'] : '',
			'version'        => isset( $data['Version'] ) ? (string) $data['Version'] : '',
			'text_domain'    => isset( $data['TextDomain'] ) ? (string) $data['TextDomain'] : '',
			'active'         => in_array( $file, $active_plugins, true ) || in_array( $file, $network_active, true ),
			'network_active' => in_array( $file, $network_active, true ),
		];
	}

	usort(
		$inventory,
		static fn( array $a, array $b ): int => strcmp( $a['slug'], $b['slug'] )
	);

	return $inventory;
}

/**
 * Build an inventory of MU plugins.
 *
 * @return array<int,array<string,mixed>>
 */
function compiled_rogue_inventory_mu_plugins(): array {
	$installed = get_mu_plugins();
	$inventory = [];

	foreach ( $installed as $file => $data ) {
		$inventory[] = [
			'file'        => $file,
			'slug'        => compiled_rogue_inventory_plugin_slug( $file ),
			'name'        => isset( $data['Name'] ) ? (string) $data['Name'] : '',
			'version'     => isset( $data['Version'] ) ? (string) $data['Version'] : '',
			'text_domain' => isset( $data['TextDomain'] ) ? (string) $data['TextDomain'] : '',
			'active'      => true,
		];
	}

	usort(
		$inventory,
		static fn( array $a, array $b ): int => strcmp( $a['slug'], $b['slug'] )
	);

	return $inventory;
}

/**
 * Build an inventory of all installed themes.
 *
 * @return array<int,array<string,mixed>>
 */
function compiled_rogue_inventory_themes(): array {
	$installed         = wp_get_themes();
	$active_stylesheet = get_stylesheet();
	$active_template   = get_template();
	$inventory         = [];

	foreach ( $installed as $slug => $theme ) {
		$parent = $theme->parent();

		$inventory[] = [
			'slug'    => (string) $slug,
			'name'    => (string) $theme->get( 'Name' ),
			'version' => (string) $theme->get( 'Version' ),
			'active'  => $slug === $active_stylesheet || $slug === $active_template,
			'parent'  => $parent ? (string) $parent->get_stylesheet() : null,
		];
	}

	usort(
		$inventory,
		static fn( array $a, array $b ): int => strcmp( $a['slug'], $b['slug'] )
	);

	return $inventory;
}

/**
 * Derive a stable plugin slug from its plugin file.
 *
 * Examples:
 *   gravityforms/gravityforms.php -> gravityforms
 *   hello.php                     -> hello
 *
 * The original plugin file and text domain are also returned in the
 * inventory so the central service can maintain aliases for edge cases.
 *
 * @param string $file Plugin file path relative to the plugins directory.
 * @return string
 */
function compiled_rogue_inventory_plugin_slug( string $file ): string {
	$file = trim( $file, '/' );

	if ( strpos( $file, '/' ) !== false ) {
		return sanitize_key( strtok( $file, '/' ) );
	}

	return sanitize_key( pathinfo( $file, PATHINFO_FILENAME ) );
}
