<?php

// setup some basics
add_action( 'after_setup_theme', 'tiffanyotten_setup', 99999 );
function tiffanyotten_setup() {
	load_theme_textdomain( 'tiffanyotten', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form' ) );
	global $content_width;
	if ( ! isset( $content_width ) ) { $content_width = 1920; }
	remove_theme_support( 'widgets-block-editor' );
	remove_theme_support( 'post-formats' );
}

// disable post formats
function disable_post_formats() {
	// Remove support for post formats
	remove_theme_support('post-formats');
	// Hide the post format UI from the admin
	add_filter('enable_post_format_ui', '__return_false');
}
add_action('after_setup_theme', 'disable_post_formats');

//Disable comments on pages by default
function theme_page_comment_status( $post_ID, $post, $update ) {
	if ( !$update ) {
		remove_action( 'save_post_page', 'theme_page_comment_status', 10 );
		wp_update_post( array(
			'ID' => $post->ID,
			'comment_status' => 'closed',
		) );
		add_action( 'save_post_page', 'theme_page_comment_status', 10, 3 );
	}
}
add_action( 'save_post_page', 'theme_page_comment_status', 10, 3 );

// make sure SVG's are GTG
add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['json']  = 'application/json';
	$mimes['svg']   = 'image/svg+xml';
	$mimes['woff2'] = 'font/woff2';

	return $mimes;
} );

add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes, $real_mime ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}

	$wp_file_type = wp_check_filetype( $filename, $mimes );

	if ( 'svg' === $wp_file_type['ext'] ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	if ( 'json' === $wp_file_type['ext'] ) {
		$data['ext']  = 'json';
		$data['type'] = 'application/json';
	}

	if ( 'woff2' === $wp_file_type['ext'] ) {
		$data['ext']  = 'woff2';
		$data['type'] = 'font/woff2';
	}

	return $data;
}, 10, 5 );

// disable image scaling
add_filter( 'big_image_size_threshold', '__return_false' );

// attempt to put yoast on the bottom
add_filter( 'wpseo_metabox_prio', function () {
	return 'low';
});

/**
 * Enable unfiltered_html capability for Editors.
 *
 * @param  array  $caps    The user's capabilities.
 * @param  string $cap     Capability name.
 * @param  int    $user_id The user ID.
 * @return array  $caps    The user's capabilities, with 'unfiltered_html' potentially added.
 */
function km_add_unfiltered_html_capability_to_editors( $caps, $cap, $user_id ) {
	if ( 'unfiltered_html' === $cap && user_can( $user_id, 'edit_posts' ) ) {
		$caps = array( 'unfiltered_html' );
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'km_add_unfiltered_html_capability_to_editors', 1, 3 );

add_action( 'after_setup_theme', 'cr_disable_theme_admin_features', 100 );
add_action( 'widgets_init', 'cr_unregister_theme_sidebars', 100 );
add_action( 'admin_menu', 'cr_remove_theme_admin_pages', 999 );
add_action( 'admin_init', 'cr_block_theme_admin_pages' );

function cr_disable_theme_admin_features() {
	remove_theme_support( 'customize-selective-refresh-widgets' );
	remove_theme_support( 'widgets-block-editor' );
	remove_theme_support( 'menus' );
}

function cr_unregister_theme_sidebars() {
	global $wp_registered_sidebars;

	if ( empty( $wp_registered_sidebars ) || ! is_array( $wp_registered_sidebars ) ) {
		return;
	}

	foreach ( array_keys( $wp_registered_sidebars ) as $sidebar_id ) {
		unregister_sidebar( $sidebar_id );
	}
}

function cr_remove_theme_admin_pages() {
	remove_submenu_page( 'themes.php', 'customize.php' );
	remove_submenu_page( 'themes.php', 'widgets.php' );
	remove_submenu_page( 'themes.php', 'nav-menus.php' );
}

function cr_block_theme_admin_pages() {
	global $pagenow;

	$blocked_pages = [
		'customize.php',
		'widgets.php',
		'nav-menus.php',
	];

	if ( in_array( $pagenow, $blocked_pages, true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}

add_filter( 'map_meta_cap', 'cr_disable_customize_capability', 10, 4 );

function cr_disable_customize_capability( $caps, $cap, $user_id, $args ) {
	if ( 'customize' === $cap ) {
		return [ 'do_not_allow' ];
	}

	return $caps;
}