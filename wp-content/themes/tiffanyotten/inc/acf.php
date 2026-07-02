<?php

// Only allow fields to be edited on development
if ( ! defined( 'WP_LOCAL_DEV' ) || ! WP_LOCAL_DEV ) {
	add_filter( 'acf/settings/show_admin', '__return_false' );
}

if ( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title'    => 'Site Options',
		'menu_title'    => 'Site Options',
		'menu_slug'     => 'global-options',
		'capability'    => 'edit_posts',
		'redirect'      => false
	));
	
	acf_add_options_sub_page(
		array(
			'page_title' 	=> 'Site Nav',
			'menu_title' 	=> 'Site Nav',
			'menu_slug' 	=> 'global_nav',
			'parent_slug'	=> 'global-options',
		)
	);

	acf_add_options_sub_page(
		array(
			'page_title' 	=> 'Site Footer',
			'menu_title' 	=> 'Site Footer',
			'menu_slug' 	=> 'global_footer',
			'parent_slug'	=> 'global-options',
		)
	);

	acf_add_options_sub_page(
		array(
			'page_title' 	=> 'Theme Settings',
			'menu_title' 	=> 'Theme Settings',
			'menu_slug' 	=> 'theme_settings',
			'parent_slug'	=> 'global-options',
		)
	);
}

add_action( 'acf/input/admin_footer', function () {
	$swatches = get_field( 'theme_background_color_swatches', 'option' );
	$palette  = [];

	if ( ! empty( $swatches ) && is_array( $swatches ) ) {
		foreach ( $swatches as $swatch ) {
			if ( empty( $swatch['color'] ) ) {
				continue;
			}

			$color = sanitize_hex_color( $swatch['color'] );

			if ( empty( $color ) ) {
				continue;
			}

			$palette[] = $color;
		}
	}

	$palette = array_values( array_unique( $palette ) );

	if ( empty( $palette ) ) {
		return;
	}
	?>
	<script type="text/javascript">
	(function($){
		acf.add_filter('color_picker_args', function(args, $field){
			args.palettes = <?php echo wp_json_encode( $palette ); ?>;

			return args;
		});
	})(jQuery);
	</script>
	<?php
} );

add_filter( 'acf/fields/wysiwyg/toolbars', function ( $toolbars ) {
	$toolbars['Theme Minimal'] = [];

	$toolbars['Theme Minimal'][1] = [
		'formatselect',
		'bold',
		'italic',
		'bullist',
		'numlist',
		'link',
	];

	return $toolbars;
} );