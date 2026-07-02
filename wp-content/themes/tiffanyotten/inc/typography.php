<?php
/**
 * Enqueue typography font assets from ACF options.
 */
add_action( 'wp_enqueue_scripts', 'cr_enqueue_theme_typography', 20 );
add_action( 'enqueue_block_editor_assets', 'cr_enqueue_theme_typography', 20 );

function cr_enqueue_theme_typography() {
	$fontfaces = get_field( 'theme_typography_fontfaces', 'option' );

	if ( empty( $fontfaces ) || ! is_array( $fontfaces ) ) {
		return;
	}

	$google_urls         = [];
	$css                 = [];
	$font_class_handles  = [];

	foreach ( $fontfaces as $fontface ) {
		$source          = ! empty( $fontface['source'] ) ? $fontface['source'] : '';
		$google_font_url = ! empty( $fontface['google_font_url'] ) ? trim( $fontface['google_font_url'] ) : '';
		$local_font_file = ! empty( $fontface['local_font_file'] ) ? $fontface['local_font_file'] : '';
		$font_handle     = ! empty( $fontface['font_handle'] ) ? sanitize_html_class( $fontface['font_handle'] ) : '';
		$font_style      = ! empty( $fontface['font_style'] ) ? trim( $fontface['font_style'] ) : '';

		if ( empty( $font_handle ) ) {
			continue;
		}

		if ( 'google' === $source ) {
			if ( ! empty( $google_font_url ) ) {
				$google_urls[] = esc_url_raw( $google_font_url );
			}

			if ( ! empty( $font_style ) ) {
				$css[] = cr_get_font_class_css( $font_handle, $font_style );
			}

			continue;
		}

		if ( 'local' === $source ) {
			$local_font_url = cr_get_acf_file_url( $local_font_file );

			if ( empty( $local_font_url ) || empty( $font_style ) ) {
				continue;
			}

			$css[] = cr_get_font_face_css( $font_handle, $font_style, $local_font_url );

			$font_class_handles[ $font_handle ] = $font_handle;
		}
	}

	foreach ( $font_class_handles as $font_handle ) {
		$css[] = cr_get_local_font_class_css( $font_handle );
	}

	if ( ! empty( $google_urls ) ) {
		$google_urls = array_unique( $google_urls );

		foreach ( $google_urls as $index => $google_url ) {
			wp_enqueue_style(
				'theme-google-font-' . $index,
				$google_url,
				[],
				null
			);
		}
	}

	$selector_prefix       = is_admin() ? '.acf-block-preview' : '';
	$typography_sizes_css  = cr_get_theme_typography_sizes_css( $selector_prefix );

	if ( ! empty( $typography_sizes_css ) ) {
		$css[] = $typography_sizes_css;
	}

	if ( ! empty( $css ) ) {
		wp_register_style( 'theme-typography', false, [], null );
		wp_enqueue_style( 'theme-typography' );
		wp_add_inline_style( 'theme-typography', cr_minify_css( implode( "\n\n", $css ) ) );
	}
}

function cr_minify_css( $css ) {
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );
	$css = preg_replace( '/\s+/', ' ', $css );
	$css = preg_replace( '/\s*([{}:;,>])\s*/', '$1', $css );
	$css = preg_replace( '/;}/', '}', $css );

	return trim( $css );
}

function cr_get_local_font_class_css( $font_handle ) {
	return sprintf(
		".%s {\n\tfont-family: \"%s\";\n}",
		$font_handle,
		esc_attr( $font_handle )
	);
}

/**
 * Create a utility class for a font handle.
 */
function cr_get_font_class_css( $font_handle, $font_style ) {
	return sprintf(
		".%s {\n%s\n}",
		$font_handle,
		cr_normalize_css_declarations( $font_style )
	);
}

/**
 * Create a @font-face rule for a local font file.
 */
function cr_get_font_face_css( $font_handle, $font_style, $font_url ) {
	return sprintf(
		"@font-face {\n\tfont-family: '%s';\n\tsrc: url('%s') format('%s');\n%s\n}",
		esc_attr( $font_handle ),
		esc_url( $font_url ),
		esc_attr( cr_get_font_format_from_url( $font_url ) ),
		cr_normalize_css_declarations( $font_style )
	);
}

/**
 * Normalize multiline CSS declarations.
 */
function cr_normalize_css_declarations( $css ) {
	$lines      = preg_split( '/\r\n|\r|\n/', trim( $css ) );
	$normalized = [];

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( empty( $line ) ) {
			continue;
		}

		$normalized[] = "\t" . $line;
	}

	return implode( "\n", $normalized );
}

/**
 * Get a usable URL from an ACF file field.
 */
function cr_get_acf_file_url( $file ) {
	if ( empty( $file ) ) {
		return '';
	}

	if ( is_array( $file ) && ! empty( $file['url'] ) ) {
		return $file['url'];
	}

	if ( is_numeric( $file ) ) {
		return wp_get_attachment_url( $file );
	}

	if ( is_string( $file ) ) {
		return $file;
	}

	return '';
}

/**
 * Determine CSS font format from file extension.
 */
function cr_get_font_format_from_url( $url ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	$ext  = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

	$formats = [
		'woff2' => 'woff2',
		'woff'  => 'woff',
		'ttf'   => 'truetype',
		'otf'   => 'opentype',
		'eot'   => 'embedded-opentype',
	];

	return ! empty( $formats[ $ext ] ) ? $formats[ $ext ] : 'woff2';
}

add_filter( 'acf/load_field/name=font_face', 'cr_load_typography_font_face_choices' );

function cr_load_typography_font_face_choices( $field ) {
	$field['choices'] = [];

	$fontfaces = get_field( 'theme_typography_fontfaces', 'option' );

	if ( empty( $fontfaces ) || ! is_array( $fontfaces ) ) {
		return $field;
	}

	foreach ( $fontfaces as $fontface ) {
		if ( empty( $fontface['font_handle'] ) ) {
			continue;
		}

		$font_handle = sanitize_html_class( $fontface['font_handle'] );

		if ( empty( $font_handle ) ) {
			continue;
		}

		$field['choices'][ $font_handle ] = $font_handle;
	}

	return $field;
}

function cr_log_interval( $total_intervals, $start, $end, $fixed = 0 ) {
	$is_neg = 0;

	$start = (float) $start;
	$end   = (float) $end;

	if ( $total_intervals <= 1 ) {
		return [ number_format( $end, $fixed, '.', '' ) ];
	}

	if ( $start <= 0 || $end <= 0 ) {
		$is_neg = ( min( $start, $end ) * -1 ) + 1;
		$start += $is_neg;
		$end   += $is_neg;
	}

	$min_log = log( $start );
	$scale   = ( log( $end ) - $min_log ) / ( $total_intervals - 1 );
	$result  = [];

	for ( $i = 1; $i <= $total_intervals; $i++ ) {
		$val = exp( $min_log + $scale * ( $i - 1 ) );

		if ( 0 !== $is_neg ) {
			$val -= $is_neg;
		}

		$result[] = number_format( $val, $fixed, '.', '' );
	}

	return array_reverse( $result );
}

function cr_get_theme_typography_sizes_css( $selector_prefix = '' ) {
	$sizes = get_field( 'theme_typography_sizes', 'option' );

	if ( empty( $sizes ) || ! is_array( $sizes ) ) {
		return '';
	}

	$breakpoints = [ 1400, 1280, 1080, 900, 640, 480 ];
	$css         = [];

	foreach ( $sizes as $size ) {
		$tag                    = ! empty( $size['tag'] ) ? ( $size['tag'] ) : '';
		$font_face              = ! empty( $size['font_face'] ) ? sanitize_text_field( $size['font_face'] ) : '';
		$text_transform         = ! empty( $size['text_transform'] ) ? sanitize_text_field( $size['text_transform'] ) : '';
		$weight                 = ! empty( $size['weight'] ) ? sanitize_text_field( $size['weight'] ) : '';
		$size_desktop           = isset( $size['size_desktop'] ) ? (float) $size['size_desktop'] : 0;
		$size_mobile            = isset( $size['size_mobile'] ) ? (float) $size['size_mobile'] : 0;
		$line_height_desktop    = isset( $size['line_height_desktop'] ) ? (float) $size['line_height_desktop'] : 0;
		$line_height_mobile     = isset( $size['line_height_mobile'] ) ? (float) $size['line_height_mobile'] : 0;
		$letter_spacing_desktop = isset( $size['letter_spacing_desktop'] ) ? (float) $size['letter_spacing_desktop'] : 0;
		$letter_spacing_mobile  = isset( $size['letter_spacing_mobile'] ) ? (float) $size['letter_spacing_mobile'] : 0;

		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'p.small', 'p.large', 'blockquote', 'eyebrow', 'navitem' ];

		if ( empty( $tag ) || empty( $font_face ) || ! in_array( $tag, $allowed_tags, true ) ) {
			continue;
		}

		if ( $tag == 'p' ) {
			$tag = "p, ul, ol";
		} else if ( $tag == 'eyebrow' ) {
			$tag = ".eyebrow";
		} else {
			$tag = $tag . ", ." . $tag;
		}

		$selector = trim( $selector_prefix . ' ' . $tag );

		$font_sizes      = cr_log_interval( count( $breakpoints ), $size_mobile, $size_desktop, 0 );
		$line_heights    = cr_log_interval( count( $breakpoints ), $line_height_mobile, $line_height_desktop, 2 );
		$letter_spacings = cr_log_interval( count( $breakpoints ), $letter_spacing_mobile, $letter_spacing_desktop, 2 );

		$last_font_size      = $font_sizes[0];
		$last_line_height    = $line_heights[0];
		$last_letter_spacing = $letter_spacings[0];

		$css[] = sprintf(
			"%s {\n\tfont-size: %spx;\n\tfont-weight: %s;\n\ttext-transform: %s;\n\tfont-family: \"%s\";\n\tline-height: %s;\n\tletter-spacing: %spx;\n}",
			$selector,
			$last_font_size,
			esc_html( $weight ),
			esc_html( $text_transform ),
			esc_html( $font_face ),
			$last_line_height,
			$last_letter_spacing
		);

		for ( $i = 1; $i < count( $font_sizes ); $i++ ) {
			$rules = [];

			if ( $font_sizes[ $i ] !== $last_font_size ) {
				$rules[] = sprintf( "\t\tfont-size: %spx;", $font_sizes[ $i ] );
			}

			if ( $line_heights[ $i ] !== $last_line_height ) {
				$rules[] = sprintf( "\t\tline-height: %s;", $line_heights[ $i ] );
			}

			if ( $letter_spacings[ $i ] !== $last_letter_spacing ) {
				$rules[] = sprintf( "\t\tletter-spacing: %spx;", $letter_spacings[ $i ] );
			}

			if ( ! empty( $rules ) ) {
				$css[] = sprintf(
					"@media (max-width: %spx) {\n\t%s {\n%s\n\t}\n}",
					$breakpoints[ $i ],
					$selector,
					implode( "\n", $rules )
				);
			}

			$last_font_size      = $font_sizes[ $i ];
			$last_line_height    = $line_heights[ $i ];
			$last_letter_spacing = $letter_spacings[ $i ];
		}
	}

	return implode( "\n\n", $css );
}