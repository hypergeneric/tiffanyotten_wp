<?php

/**
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$args = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);

list( $blockid, $blockslug ) = tiffanyotten_get_block_meta( $block );

$heading_image       = tiffanyotten_block_value( 'headline_image', $args );
$left_image_top      = tiffanyotten_block_value( 'left_image_top', $args );
$left_image_bottom   = tiffanyotten_block_value( 'left_image_bottom', $args );
$right_image       	 = tiffanyotten_block_value( 'right_image', $args );


?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if ($heading_image) : ?>
			<div class="heading-wrap">
					<div class="heading-image">
						<?php echo tiffanyotten_print_img_src( $heading_image ); ?>
					</div>
			</div>
			<?php endif; ?>

			<div class="gallery">
				<div class="gallery-left">
					<?php if ($left_image_top) : ?>
						<div class="left-image">
							<?php echo tiffanyotten_print_img_src( $left_image_top ); ?>
						</div>
					<?php endif; ?>
					<?php if ($left_image_bottom) : ?>
						<div class="left-image">
							<?php echo tiffanyotten_print_img_src( $left_image_bottom ); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="gallery-right">
					<?php if ($right_image) : ?>
						<div class="right-image">
							<?php echo tiffanyotten_print_img_src( $right_image ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
