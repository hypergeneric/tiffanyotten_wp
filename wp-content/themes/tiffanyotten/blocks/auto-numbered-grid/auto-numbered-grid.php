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
						$title   = get_sub_field('title');
						$blurb   = get_sub_field('blurb');
					?>
						<div class="entry flex flex-v">
							<span class="entry-inner">
								<span class="entry-num-border"></span>
								<?php if ($title) { ?>
									<h3 class="h3"><?php echo esc_html( $title ); ?></h3>
								<?php } ?>
								<?php if ($blurb) { ?>
									<p class="p"><?php echo esc_html( $blurb ); ?></p>
								<?php } ?>
							</span>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>