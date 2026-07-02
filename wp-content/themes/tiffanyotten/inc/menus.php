<?php

function get_request_uri() {
	$options     = array( 'options' => array( 'default' => '' ) );
	$request_uri = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL, $options );
	if ( empty( $request_uri ) && isset( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL, $options );
	}
	return rawurldecode( $request_uri );
}

function is_active_page( $url, $current ) {
	// Define globally ignored query parameters
	$ignored_params = ['sortdir'];

	// Parse the URL and extract the path and query string
	$parsed_url       = parse_url( $url );
	$parsed_current   = parse_url( $current );

	$url_path         = isset( $parsed_url['path'] ) ? '/' . trim( $parsed_url['path'], '/' ) : '';
	$current_path     = isset( $parsed_current['path'] ) ? '/' . trim( $parsed_current['path'], '/' ) : '';

	// Parse query strings into arrays
	parse_str( isset( $parsed_url['query'] ) ? $parsed_url['query'] : '', $url_query_array );
	parse_str( isset( $parsed_current['query'] ) ? $parsed_current['query'] : '', $current_query_array );

	// Remove ignored parameters
	foreach ( $ignored_params as $param ) {
		unset( $url_query_array[ $param ], $current_query_array[ $param ] );
	}

	// Compare path and filtered query strings
	return $url_path === $current_path && $url_query_array === $current_query_array;
}
