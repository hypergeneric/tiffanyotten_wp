<?php
/**
 * Sitewide Gravity Forms popup.
 *
 * Open via CTA links pointing to the configured hash (default #form-popup)
 * or elements with class .js-form-popup.
 *
 * @package tiffanyotten
 */

$form_id = tiffanyotten_popup_form_id();

if ( ! $form_id || ! function_exists( 'gravity_form' ) ) {
	return;
}

$heading = get_field( 'popup_form_heading', 'options' );
$intro   = get_field( 'popup_form_intro', 'options' );
$hash    = tiffanyotten_popup_form_hash();

$heading = is_string( $heading ) ? trim( $heading ) : '';
$intro   = is_string( $intro ) ? trim( $intro ) : '';

?>
<dialog
	id="form-popup"
	class="form-popup"
	data-form-hash="<?php echo esc_attr( $hash ); ?>"
	<?php if ( $heading ) : ?>
		aria-labelledby="form-popup-title"
	<?php else : ?>
		aria-label="<?php esc_attr_e( 'Contact form', 'tiffanyotten' ); ?>"
	<?php endif; ?>
>
	<div class="form-popup__inner">
		<button type="button" class="form-popup__close" aria-label="<?php esc_attr_e( 'Close form', 'tiffanyotten' ); ?>">
			<span><?php esc_html_e( 'Close', 'tiffanyotten' ); ?></span>
		</button>

		<?php if ( $heading || $intro ) : ?>
			<div class="form-popup__heading">
				<?php if ( $heading ) : ?>
					<h2 id="form-popup-title"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<p><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="form-popup__form">
			<?php tiffanyotten_render_gravity_form( $form_id ); ?>
		</div>
	</div>
</dialog>
