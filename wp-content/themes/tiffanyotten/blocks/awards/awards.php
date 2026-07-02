<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$eyebrow 	= tiffanyotten_block_value('eyebrow', $args);
$blurb		= tiffanyotten_block_value('blurb', $args);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if ( $eyebrow ) : ?>
				<div class="eyebrow"><?php echo $eyebrow; ?></div>
			<?php endif; ?>

			<?php if (have_rows('entries')) : ?>
				<div class="entries flex">
					<?php while (have_rows('entries')) : the_row();
						$image    = get_sub_field('image');
						$title    = get_sub_field('title');
					?>
						<div class="entry flex flex-v">
							<span class="entry-inner">
								<?php if ($image) { ?>
									<div class="award-image"><?php echo tiffanyotten_print_img_src($image); ?></div>
								<?php } ?>
								<?php if ($title) { ?>
									<h5><?php echo esc_html($title); ?></h5>
								<?php } ?>
							</span>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

			<?php if ( $blurb ) : ?>
				<p class="p body-text"><?php echo $blurb; ?></p>
			<?php endif; ?>

		</div>
	</div>
</section>