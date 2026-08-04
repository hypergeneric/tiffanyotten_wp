<?php
/**
 * Gravity Forms: popup, ACF choices, enqueue, render helpers.
 */

function tiffanyotten_popup_form_id() {
	return (int) apply_filters( 'tiffanyotten_popup_form_id', absint( get_field( 'popup_form_id', 'options' ) ) );
}

function tiffanyotten_popup_form_hash() {
	$hash = get_field( 'popup_form_hash', 'options' );
	$hash = is_string( $hash ) ? trim( $hash ) : '';
	$hash = ltrim( $hash, '#' );
	$hash = preg_replace( '/[^a-zA-Z0-9_-]/', '', $hash );

	if ( '' === $hash ) {
		$hash = 'form-popup';
	}

	return (string) apply_filters( 'tiffanyotten_popup_form_hash', '#' . $hash );
}

function tiffanyotten_gravity_form_choices() {
	$choices = [
		'' => __( '— Select a form —', 'tiffanyotten' ),
	];

	if ( ! class_exists( 'GFAPI' ) ) {
		return $choices;
	}

	$forms = GFAPI::get_forms( true, false, 'title', 'ASC' );

	if ( empty( $forms ) || ! is_array( $forms ) ) {
		return $choices;
	}

	foreach ( $forms as $form ) {
		if ( empty( $form['id'] ) ) {
			continue;
		}
		$label = ! empty( $form['title'] ) ? $form['title'] : sprintf( __( 'Form %d', 'tiffanyotten' ), (int) $form['id'] );
		$choices[ (string) (int) $form['id'] ] = sprintf( '%s (ID %d)', $label, (int) $form['id'] );
	}

	return $choices;
}

function tiffanyotten_acf_load_gravity_form_choices( $field ) {
	$field['choices'] = tiffanyotten_gravity_form_choices();
	return $field;
}
add_filter( 'acf/load_field/name=popup_form_id', 'tiffanyotten_acf_load_gravity_form_choices' );
add_filter( 'acf/load_field/name=gravity_form_id', 'tiffanyotten_acf_load_gravity_form_choices' );

function tiffanyotten_render_gravity_form( $form_id, $echo = true ) {
	$form_id = absint( $form_id );

	if ( ! $form_id || ! function_exists( 'gravity_form' ) ) {
		return $echo ? null : '';
	}

	return gravity_form( $form_id, false, false, false, null, true, 0, $echo );
}

function tiffanyotten_find_form_ids_in_blocks( $blocks ) {
	$ids = [];

	if ( empty( $blocks ) || ! is_array( $blocks ) ) {
		return $ids;
	}

	foreach ( $blocks as $block ) {
		$name = isset( $block['blockName'] ) ? $block['blockName'] : '';

		if ( 'acf/form-embed' === $name ) {
			$data = isset( $block['attrs']['data'] ) && is_array( $block['attrs']['data'] ) ? $block['attrs']['data'] : [];
			if ( ! empty( $data['gravity_form_id'] ) ) {
				$ids[] = absint( $data['gravity_form_id'] );
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$ids = array_merge( $ids, tiffanyotten_find_form_ids_in_blocks( $block['innerBlocks'] ) );
		}
	}

	return $ids;
}

function tiffanyotten_form_ids_to_enqueue() {
	$ids = [];

	$popup_id = tiffanyotten_popup_form_id();
	if ( $popup_id ) {
		$ids[] = $popup_id;
	}

	if ( is_singular() ) {
		$post = get_post();
		if ( $post && ! empty( $post->post_content ) && function_exists( 'has_blocks' ) && has_blocks( $post->post_content ) ) {
			$ids = array_merge( $ids, tiffanyotten_find_form_ids_in_blocks( parse_blocks( $post->post_content ) ) );
		}
	}

	$ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );

	return apply_filters( 'tiffanyotten_form_ids_to_enqueue', $ids );
}

function tiffanyotten_enqueue_gravity_form_assets() {
	if ( is_admin() || ! function_exists( 'gravity_form_enqueue_scripts' ) ) {
		return;
	}

	foreach ( tiffanyotten_form_ids_to_enqueue() as $form_id ) {
		gravity_form_enqueue_scripts( $form_id, true );
	}
}
add_action( 'get_header', 'tiffanyotten_enqueue_gravity_form_assets' );

function tiffanyotten_localize_form_popup() {
	if ( ! wp_script_is( 'tiffanyotten-script-footer', 'enqueued' ) && ! wp_script_is( 'tiffanyotten-script-footer', 'registered' ) ) {
		return;
	}

	wp_localize_script(
		'tiffanyotten-script-footer',
		'tiffanyottenFormPopup',
		[
			'hash' => tiffanyotten_popup_form_hash(),
		]
	);
}
add_action( 'wp_enqueue_scripts', 'tiffanyotten_localize_form_popup', 30 );

function tiffanyotten_render_form_popup() {
	get_template_part( 'templates/_partials/form-popup' );
}
add_action( 'wp_footer', 'tiffanyotten_render_form_popup', 5 );
