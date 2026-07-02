<?php

function tiffanyotten_has_acf_blocks( $post_id ) {
	// Retrieve the post content by ID
	$post_content = get_post_field( 'post_content', $post_id );

	// Check if the content contains any ACF block indicators
	return ( strpos( $post_content, '<!-- wp:acf/' ) !== false );
}

function tiffanyotten_get_block_theme( $block, $background_color = null, $context = [], $default = 'dark' ) {
	if ( ! empty( $background_color ) ) {
		return tiffanyotten_light_or_dark( $background_color );
	}

	if ( ! empty( $context['tiffanyotten/containerData']['background_color'] ) ) {
		return tiffanyotten_light_or_dark( $context['tiffanyotten/containerData']['background_color'] );
	}

	if ( ! empty( $block['attrs']['data']['background_color'] ) ) {
		return tiffanyotten_light_or_dark( $block['attrs']['data']['background_color'] );
	}

	return $default;
}

function tiffanyotten_get_first_acf_block( $post_id = null ) {
	if ( $post_id === null ) {
		$post_id = get_the_ID();
	}

	$post_content = get_post_field( 'post_content', $post_id );
	if ( ! $post_content ) return null;

	$blocks = parse_blocks( $post_content );

	foreach ( $blocks as $block ) {
		if ( isset( $block['blockName'] ) && strpos( $block['blockName'], 'acf/' ) === 0 ) {
			return $block['attrs'] ?? null;
		}
	}

	return null;
}

function tiffanyotten_render_block( $type, $id, $args=[] ) {
	register_acf_block_enqueue( [
		'render_template' => 'blocks/' . $type . '/' . $type . '.php'
	] );
	$args['block'] = [
		'name' => $type,
		'id' => $id,
	];
	get_template_part( 'blocks/' . $type . '/' . $type, null, $args );
}

function tiffanyotten_block_value( $var, $args ) {
	return isset( $args[$var] ) ? $args[$var] : get_field($var);
}

function tiffanyotten_get_block_meta( $block, $classes=[], $args=null, $context=null ) {
	$slug    = str_contains( $block['name'], '/' ) ? explode( "/", $block['name'] )[1] : $block['name'];
	$id      = $slug . '-' . $block['id'];
	if ( ! empty( $block['anchor'] ) ) {
		$id = $block['anchor'];
	}
	$classes[] = $slug;
	$background_color  = tiffanyotten_block_value('background_color', $args);
	$theme             = tiffanyotten_get_block_theme( $block, $background_color, $context );
	if ( $theme ) {
		$classes[] = $theme;
	}
	$padding_top       = tiffanyotten_block_value('padding_top', $args);
	$padding_bottom    = tiffanyotten_block_value('padding_bottom', $args);
	if ( $padding_top ) {
		$classes[] = 'spacing-top-' . $padding_top;
	}
	if ( $padding_bottom ) {
		$classes[] = 'spacing-bottom-' . $padding_bottom;
	}
	if ( ! empty( $block['className'] ) ) {
		$classes[] = $block['className'];
	}
	if ( ! empty( $block['align'] ) ) {
		$classes[] = 'align' . $block['align'];
	}
	$className = trim ( implode( " ", $classes ) );
	return [ $id, $className ];
}

function tiffanyotten_include_js_components( $scripts=null ) {
	$tiffanyotten_js_scripts = wp_cache_get( 'tiffanyotten_js_scripts' );
	if ( false === $tiffanyotten_js_scripts ) {
		$tiffanyotten_js_scripts = [];
	}
	if ( ! is_null( $scripts ) ) {
		if ( ! is_array( $scripts ) ) {
			$scripts = [ $scripts ];
		}
		$tiffanyotten_js_scripts = array_merge( $tiffanyotten_js_scripts, $scripts );
		$tiffanyotten_js_scripts = array_unique( $tiffanyotten_js_scripts );
		wp_cache_set( 'tiffanyotten_js_scripts', $tiffanyotten_js_scripts );
	}
}

function tiffanyotten_print_js_components() {
	$tiffanyotten_js_scripts = wp_cache_get( 'tiffanyotten_js_scripts' );
	if ( false !== $tiffanyotten_js_scripts ) {
		$script_paths  = [];
		$script_hashes = [];
		$script_stamps = [];
		foreach ( $tiffanyotten_js_scripts as $script ) {
			if ( tiffanyotten_Config::config()->production ) {
				$filepath = get_template_directory() . '/assets/js/' . str_replace( ".js", ".min.js", $script );
			} else {
				$filepath = get_template_directory() . '/assets/js-dev/' . $script;
			}
			if ( ! file_exists( $filepath ) ) {
				$filepath = get_template_directory() . '/' . $script;
			}
			if ( file_exists( $filepath ) ) {
				$script_hashes[] = $script;
				$script_stamps[] = filemtime( $filepath );
				$script_paths[]  = $filepath;
			}
		}
		if ( count( $script_paths ) == 0 ) {
			return;
		}
		$upload_dir = wp_upload_dir();
		$file_path  = '/tiffanyotten-js-bundles/' . md5( implode( '-', $script_hashes ) ) . '/';
		$file_name  = md5( implode( '-', $script_stamps ) ) . '.js';
		if ( ! file_exists( $upload_dir['basedir'] . $file_path . $file_name ) ) {
			$script_code = '';
			foreach ( $script_paths as $script ) {
				$script_code .= '/* --- ' . $script . " --- */\n";
				$script_code .= file_get_contents( $script ) . "\n";
			}
			wp_mkdir_p( $upload_dir['basedir'] . $file_path );
			file_put_contents( $upload_dir['basedir'] . $file_path . $file_name, $script_code );
		}
		print "\n" . '<!-- tiffanyotten Components -->' . "\n";
		print '<script defer src="' . $upload_dir['baseurl'] . $file_path . $file_name . '"></script>';
	}
}

function tiffanyotten_enqueue_css_block( $path, $ext ) {
	if ( tiffanyotten_Config::config()->production ) {
		$filepath = get_template_directory() . '/assets/css/' . $path . ".min.css";
		$fileurl  = get_template_directory_uri() . '/assets/css/' . $path . ".min.css";
	} else {
		$filepath = get_template_directory() . '/assets/css-dev/' . $path . ".css";
		$fileurl  = get_template_directory_uri() . '/assets/css-dev/' . $path . ".css";
	}
	if ( ! file_exists( $filepath ) ) {
		$filepath = get_template_directory() . '/' . $path . ".$ext";
		$fileurl  = get_template_directory_uri() . '/' . $path . ".$ext";
	}
	wp_enqueue_style( "tiffanyotten-block-style-" . md5( $path . $ext ) , $fileurl, array(), filemtime( $filepath ) );
}

function register_acf_block_enqueue( $settings ) {
	if ( empty( $settings['render_template'] ) ) {
		return;
	}

	$block_name = basename( $settings['render_template'], '.php' );
	$block_path = dirname( $settings['render_template'] ) . '/';
	$block_base = get_template_directory() . '/' . $block_path;

	if ( file_exists( $block_base . $block_name . '.css' ) ) {
		tiffanyotten_enqueue_css_block( $block_path . $block_name, 'css' );
	} elseif ( file_exists( $block_base . $block_name . '.scss' ) ) {
		tiffanyotten_enqueue_css_block( $block_path . $block_name, 'scss' );
	}

	if ( file_exists( $block_base . $block_name . '.js' ) ) {
		tiffanyotten_include_js_components( $block_path . $block_name . '.js' );
	}
}

function tiffanyotten_get_registered_acf_block_names() {
	$blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

	$acf_blocks = [];

	foreach ( $blocks as $block_name => $block_type ) {
		if ( strpos( $block_name, 'acf/' ) !== 0 ) {
			continue;
		}

		if ( $block_name === 'acf/container' ) {
			continue;
		}

		$acf_blocks[] = $block_name;
	}

	return $acf_blocks;
}

function tiffanyotten_register_acf_block_fallback( $block_name, $block_dir ) {
	$template_path = $block_dir . '/' . $block_name . '.php';

	if ( ! file_exists( $template_path ) ) {
		return;
	}

	$block_label = str_replace( '-', ' ', $block_name );
	$block_title = ucwords( $block_label );

	acf_register_block_type( [
		'name'            => $block_name,
		'title'           => __( $block_title ),
		'description'     => __( 'A custom ' . $block_label . ' block.' ),
		'render_template' => 'blocks/' . $block_name . '/' . $block_name . '.php',
		'category'        => 'tiffanyotten',
		'enqueue_assets'  => 'register_acf_block_enqueue',
		'supports'        => [
			'anchor' => true,
		],
	] );
}

function register_acf_block_types() {
	$blocks_dir = get_template_directory() . '/blocks/';

	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$acf_blocks = scandir( $blocks_dir );

	foreach ( $acf_blocks as $acf_block ) {
		if ( $acf_block === '.' || $acf_block === '..' ) {
			continue;
		}

		$block_dir = $blocks_dir . $acf_block;

		if ( ! is_dir( $block_dir ) ) {
			continue;
		}

		$block_json = $block_dir . '/block.json';

		if ( file_exists( $block_json ) ) {
			register_block_type( $block_dir );
			continue;
		}

		tiffanyotten_register_acf_block_fallback( $acf_block, $block_dir );
	}
}

// Check if function exists and hook into setup.
if ( function_exists( 'acf_register_block_type' ) ) {
	add_action('acf/init', 'register_acf_block_types');
}

function tiffanyotten_enqueue_assets_for_rendered_acf_block( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || strpos( $block['blockName'], 'acf/' ) !== 0 ) {
		return $block_content;
	}

	$block_name = substr( $block['blockName'], 4 );

	register_acf_block_enqueue( [
		'render_template' => 'blocks/' . $block_name . '/' . $block_name . '.php',
	] );

	return $block_content;
}

add_filter( 'render_block', 'tiffanyotten_enqueue_assets_for_rendered_acf_block', 10, 2 );

function tiffanyotten_enqueue_all_acf_block_editor_assets() {
	$blocks_dir = get_template_directory() . '/blocks/';

	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$acf_blocks = scandir( $blocks_dir );

	foreach ( $acf_blocks as $acf_block ) {
		if ( $acf_block === '.' || $acf_block === '..' ) {
			continue;
		}

		$block_dir = $blocks_dir . $acf_block;

		if ( ! is_dir( $block_dir ) ) {
			continue;
		}

		register_acf_block_enqueue( [
			'render_template' => 'blocks/' . $acf_block . '/' . $acf_block . '.php',
		] );
	}
}

add_action( 'enqueue_block_editor_assets', 'tiffanyotten_enqueue_all_acf_block_editor_assets' );
