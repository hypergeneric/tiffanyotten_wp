<?php
/**
 * Theme Options CSS Variables
 *
 * ACF field naming:
 * themeoption_{slug}_{type}
 *
 * Supported types:
 * int, float, color, str, image
 *
 * Special override field:
 * themeoptions_overrides
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MyTheme_Theme_Options_CSS {

	const OPTION_CSS				= 'mytheme_theme_options_css';
	const OPTION_TOKENS				= 'mytheme_theme_options_tokens';
	const OPTIONS_POST_ID			= 'option';
	const OVERRIDES_FIELD_NAME		= 'themeoptions_overrides';
	const STYLE_HANDLE				= 'mytheme-theme-options-css';

	protected $allowed_units = [
		'px',
		'%',
		'rem',
		'em',
		'vw',
		'vh',
		'vmin',
		'vmax',
		'ch',
		'ex',
		'deg',
		'ms',
		's',
	];

	protected $allowed_types = [
		'int',
		'float',
		'color',
		'str',
		'image',
	];

	public function __construct() {
		add_action( 'acf/save_post', [ $this, 'maybe_regenerate_css' ], 20 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_theme_css' ], 0 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_theme_css' ], 0 );
	}

	public function maybe_regenerate_css( $post_id ) {
		if ( ! $this->is_options_post_id( $post_id ) ) {
			return;
		}

		$this->regenerate_css();
	}

	public function regenerate_css() {
		$field_objects	= get_field_objects( self::OPTIONS_POST_ID );
		$tokens			= [];
		$css_vars		= [];
		$overrides		= '';

		if ( empty( $field_objects ) || ! is_array( $field_objects ) ) {
			update_option( self::OPTION_CSS, '', false );
			update_option( self::OPTION_TOKENS, [], false );
			return;
		}

		foreach ( $field_objects as $field ) {
			if ( empty( $field['name'] ) ) {
				continue;
			}

			$field_name = $field['name'];

			if ( 0 === strpos( $field_name, self::OVERRIDES_FIELD_NAME ) ) {
				$overrides .= "\n" . ( isset( $field['value'] ) ? trim( (string) $field['value'] ) : '' );
				continue;
			}

			$parsed = $this->parse_themeoption_field_name( $field_name );

			if ( ! $parsed ) {
				continue;
			}

			$value = $this->format_field_value( $field, $parsed['type'] );

			if ( '' === $value || null === $value ) {
				continue;
			}

			$css_var_name				= '--theme-' . str_replace( '_', '-', $parsed['slug'] );
			$css_vars[ $css_var_name ]	= $value;
			$tokens[ $parsed['slug'] ]	= $value;
		}

		$css = $this->build_css( $css_vars, $overrides );

		update_option( self::OPTION_CSS, $css, false );
		update_option( self::OPTION_TOKENS, $tokens, false );
	}

	public function enqueue_theme_css() {
		$css = get_option( self::OPTION_CSS, '' );

		if ( '' === trim( $css ) ) {
			$this->regenerate_css();
			$css = get_option( self::OPTION_CSS, '' );
		}

		if ( '' === trim( $css ) ) {
			return;
		}

		wp_register_style( self::STYLE_HANDLE, false, [], null );
		wp_enqueue_style( self::STYLE_HANDLE );
		wp_add_inline_style( self::STYLE_HANDLE, $css );
	}

	protected function is_options_post_id( $post_id ) {
		$valid = [
			'option',
			'options',
		];

		return in_array( $post_id, $valid, true );
	}

	protected function parse_themeoption_field_name( $field_name ) {
		$pattern = '/^themeoption_(.+)_(int|float|color|str|image)$/';

		if ( ! preg_match( $pattern, $field_name, $matches ) ) {
			return false;
		}

		$slug = trim( $matches[1] );
		$type = trim( $matches[2] );

		if ( '' === $slug || ! in_array( $type, $this->allowed_types, true ) ) {
			return false;
		}

		return [
			'slug' => $slug,
			'type' => $type,
		];
	}

	protected function format_field_value( $field, $type ) {
		$value = isset( $field['value'] ) ? $field['value'] : null;

		if ( null === $value || '' === $value ) {
			return '';
		}

		switch ( $type ) {
			case 'int':
				return $this->format_int_value( $value, $field );

			case 'float':
				return $this->format_float_value( $value, $field );

			case 'color':
				return $this->format_color_value( $value );

			case 'str':
				return $this->format_string_value( $value );

			case 'image':
				return $this->format_image_value( $value );
		}

		return '';
	}

	protected function format_image_value( $value ) {
		$url = '';

		if ( is_array( $value ) ) {
			if ( ! empty( $value['url'] ) ) {
				$url = $value['url'];
			} elseif ( ! empty( $value['ID'] ) ) {
				$url = wp_get_attachment_image_url( intval( $value['ID'] ), 'full' );
			} elseif ( ! empty( $value['id'] ) ) {
				$url = wp_get_attachment_image_url( intval( $value['id'] ), 'full' );
			}
		} elseif ( is_numeric( $value ) ) {
			$url = wp_get_attachment_image_url( intval( $value ), 'full' );
		} else {
			$url = trim( (string) $value );
		}

		if ( '' === $url ) {
			return '';
		}

		$url = esc_url_raw( $url );

		if ( '' === $url ) {
			return '';
		}

		return 'url("' . esc_url( $url ) . '")';
	}

	protected function format_int_value( $value, $field ) {
		if ( ! is_numeric( $value ) ) {
			return '';
		}

		$formatted = (string) intval( $value );
		$unit		= $this->get_field_unit( $field );

		if ( $unit ) {
			$formatted .= $unit;
		}

		return $formatted;
	}

	protected function format_float_value( $value, $field ) {
		if ( ! is_numeric( $value ) ) {
			return '';
		}

		$float_value = floatval( $value );
		$formatted	 = $this->normalize_float_string( $float_value );
		$unit		 = $this->get_field_unit( $field );

		if ( $unit ) {
			$formatted .= $unit;
		}

		return $formatted;
	}

	protected function format_color_value( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $value ) ) {
			return $value;
		}

		if ( preg_match( '/^rgba?\([^)]+\)$/', $value ) ) {
			return $value;
		}

		if ( preg_match( '/^hsla?\([^)]+\)$/', $value ) ) {
			return $value;
		}

		return '';
	}

	protected function format_string_value( $value ) {
		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}

		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		return $value;
	}

	protected function get_field_unit( $field ) {
		$unit = '';

		if ( isset( $field['append'] ) ) {
			$unit = trim( (string) $field['append'] );
		}

		if ( '' === $unit ) {
			return '';
		}

		if ( ! in_array( $unit, $this->allowed_units, true ) ) {
			return '';
		}

		return $unit;
	}

	protected function normalize_float_string( $value ) {
		$string = number_format( $value, 4, '.', '' );
		$string = rtrim( $string, '0' );
		$string = rtrim( $string, '.' );

		if ( '' === $string ) {
			$string = '0';
		}

		return $string;
	}

	protected function build_css( $css_vars, $overrides = '' ) {
		$css = '';

		if ( ! empty( $css_vars ) ) {
			$css .= ":root {\n";

			foreach ( $css_vars as $var_name => $value ) {
				$css .= "\t{$var_name}: {$value};\n";
			}

			$css .= "}\n";
		}

		$overrides = trim( (string) $overrides );

		if ( '' !== $overrides ) {
			$css .= "\n" . $overrides . "\n";
		}

		return trim( $css );
	}
}

new MyTheme_Theme_Options_CSS();