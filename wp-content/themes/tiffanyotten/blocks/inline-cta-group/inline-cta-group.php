<?php

/**
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$primary_cta      = tiffanyotten_block_value('primary_cta', $args);
$secondary_cta    = tiffanyotten_block_value('secondary_cta', $args);
$alignment		  = tiffanyotten_block_value('alignment', $args);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>"> 
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner <?php echo esc_attr($alignment); ?>">

			<?php if ( $primary_cta || $secondary_cta ) : ?>
				<?php get_template_part( 'templates/_partials/cta-group', null, [
					'primary_cta' => $primary_cta,
					'secondary_cta' => $secondary_cta,
				] ); ?>
			<?php endif; ?>

		</div>
	</div>
</section>