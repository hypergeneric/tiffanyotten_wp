<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);



?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if (have_rows('entries')) : ?>
				<div class="entries flex">
					<?php while (have_rows('entries')) : the_row();
						$icon        = get_sub_field('icon');
						$eyebrow     = get_sub_field('eyebrow');
						$title       = get_sub_field('title');
						$title_size  = get_sub_field('title_size');
						$blurb       = get_sub_field('blurb');
						$blurb_size  = get_sub_field('blurb_size');
						$layout		 = get_sub_field('layout');
					?>
						<div class="entry flex flex-v">
							<span class="entry-inner">
								<?php if ($icon) { ?>
									<div class="icon"><?php echo tiffanyotten_print_img_src($icon); ?></div>
								<?php } ?>
								<?php get_template_part( 'templates/_partials/heading', null, [
									'eyebrow' => $eyebrow,
									'title' => $title,
									'title_size' => $title_size,
									'blurb' => $blurb,
									'blurb_size' => $blurb_size,
								] ); ?>
							</span>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>