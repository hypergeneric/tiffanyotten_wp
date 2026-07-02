<?php

// right now, i'm just adding things directly to the header, but we can possibly move them to enqueue
function tiffanyotten_alt_load_scripts() {
	$stylesheet = '/assets/css/style.min.css';
	if ( file_exists( get_stylesheet_directory() . '/assets/css-dev/style.css' ) ) {
		$stylesheet = '/assets/css-dev/style.css';
	}
	$footerjs = '/assets/js/footer.min.js';
	if ( file_exists( get_stylesheet_directory() . '/assets/js-dev/footer.min.js' ) ) {
		$footerjs = '/assets/js-dev/footer.min.js';
	}
	wp_enqueue_style( 'tiffanyotten-style', get_template_directory_uri() . $stylesheet, array(), filemtime( get_stylesheet_directory() . $stylesheet ) );
	wp_enqueue_script( 'tiffanyotten-script-footer', get_template_directory_uri() . $footerjs, array(), filemtime( get_stylesheet_directory() . $footerjs ), true );
	wp_localize_script( 'tiffanyotten-script-footer', 'ajax_object', array(
		'rest_url' => get_rest_url( null, '/custom/v2' )
	));
}
add_action( 'wp_enqueue_scripts', 'tiffanyotten_alt_load_scripts' );

// kill old styles
function tiffanyotten_dequeue_unnecessary_styles() {
	$remove = [];
	foreach ( $remove as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}
}
add_action( 'wp_print_styles', 'tiffanyotten_dequeue_unnecessary_styles' );

// kill old scripts
function tiffanyotten_dequeue_unnecessary_scripts() {
	$remove = [];
	foreach ( $remove as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}
}
add_action( 'wp_print_scripts', 'tiffanyotten_dequeue_unnecessary_scripts' );

function remove_json_api () {
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
	add_filter( 'embed_oembed_discover', '__return_false' );
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'after_setup_theme', 'remove_json_api' );

// add admin styles
add_action( 'admin_enqueue_scripts', function () {
	$stylesheet = '/admin/css/style.min.css';
	if ( file_exists( get_stylesheet_directory() . '/admin/css-dev/style.css' ) ) {
		$stylesheet = '/admin/css-dev/style.css';
	}
	wp_enqueue_style( 'tiffanyotten-admin-style', get_template_directory_uri() . $stylesheet, array(), filemtime( get_stylesheet_directory() . $stylesheet ) );
	wp_enqueue_script( 'tiffanyotten-lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js' );
} );