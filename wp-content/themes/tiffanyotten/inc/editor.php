<?php

add_filter( 'mce_buttons_2', 'tiffanyotten_mce_add_styleselect' );

function tiffanyotten_mce_add_styleselect( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}

add_filter( 'acf/fields/wysiwyg/toolbars', 'tiffanyotten_acf_add_styleselect' );

function tiffanyotten_acf_add_styleselect( $toolbars ) {
	foreach ( $toolbars as $name => $rows ) {
		if ( ! empty( $rows[1] ) && ! in_array( 'styleselect', $rows[1], true ) ) {
			array_unshift( $toolbars[ $name ][1], 'styleselect' );
		}
	}
	return $toolbars;
}

add_filter( 'tiny_mce_before_init', 'tiffanyotten_mce_style_formats' );

function tiffanyotten_mce_style_formats( $settings ) {
	$settings['style_formats'] = wp_json_encode( [
		[
			'title'   => 'Highlight',
			'inline'  => 'span',
			'classes' => 'highlight',
		],
		[
			'title'    => 'Checklist',
			'selector' => 'ul',
			'classes'  => 'checklist',
		],
	] );
	return $settings;
}
