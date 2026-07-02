<?php

/**
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$args = isset( $args ) ? $args : null;
$block = isset( $block ) ? $block : ( isset( $args['block'] ) ? $args['block'] : null );
list( $blockid, $blockslug ) = tiffanyotten_get_block_meta( $block );

$eyebrow           = tiffanyotten_block_value('eyebrow', $args);
$title             = tiffanyotten_block_value('title', $args);
$content           = tiffanyotten_block_value('content', $args);
$signers           = tiffanyotten_block_value('signers', $args);
$background_color  = tiffanyotten_block_value( 'background_color', $args );
$background_image  = tiffanyotten_block_value( 'background_image', $args );

$theme = tiffanyotten_light_or_dark( $background_color );

?>
<section id="<?php echo esc_attr( $blockid ); ?>" 
	class="<?php echo esc_attr( $blockslug ); ?> <?php echo $background_image ? 'lazy' : ''; ?> <?php echo $theme; ?>" 
	style="background-color:<?php echo $background_color; ?>" 
	data-bg="<?php echo $background_image ? $background_image['url'] : ''; ?>">
	<div class="container">
		<div class="inner">

			<?php if ($eyebrow || $title) : ?>
			<div class="heading">
				<?php if ($eyebrow) : ?>
					<div class="eyebrow"><?php echo $eyebrow; ?></div>
				<?php endif; ?>
				<?php if ($title) : ?>
					<h2 class="h2"><?php echo $title; ?></h2>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if ($content) : ?>
			<div class="entry-content">
				<?php echo $content; ?>
			</div>
			<?php endif; ?>

			<?php if ( have_rows( 'signers' ) ) : ?>
			<div class="signers">
				<?php while ( have_rows( 'signers' ) ) : the_row(); 
					$name      = get_sub_field( 'name' );
					$position  = get_sub_field( 'position' );
					$signature = get_sub_field( 'signature' );
					$headshot  = get_sub_field( 'headshot' );
				?>
				<div class="signer">
					<p class="">Signed,</p>
					<?php echo tiffanyotten_print_img_src($signature, ['signature']); ?>
					<?php if ($name) : ?>
						<p class="bold"><?php echo $name; ?></p>
					<?php endif; ?>
					<?php if ($position) : ?>
						<p class=""><?php echo $position; ?></p>
					<?php endif; ?>
					<?php echo tiffanyotten_print_img_src($headshot, ['headshot']); ?>
				</div>
				<?php endwhile; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
</section>
