<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$cons_title = tiffanyotten_block_value('cons_title', $args);
$pros_title = tiffanyotten_block_value('pros_title', $args);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<div class="entries flex">
				<div class="entry">
					<span class="entry-inner">
						<div class="entry-text">
							<?php if ($cons_title) { ?>
								<h5 class="h5"><?php echo $cons_title; ?></h5>
							<?php } ?>
							<?php if (have_rows('cons_entries')) : ?>
								<?php while (have_rows('cons_entries')) : the_row();
								$entry = get_sub_field('entry');
								?>
									<?php if ($entry) { ?>
										<p class="p body-text con-text"><?php echo $entry; ?></p>
									<?php } ?>
								<?php endwhile; ?>
							<?php endif; ?>
						</div>
					</span>
				</div>


				<div class="entry">
					<span class="entry-inner">
						<div class="entry-text">
							<?php if ($cons_title) { ?>
								<h5 class="h5"><?php echo $pros_title; ?></h5>
							<?php } ?>
							<?php if (have_rows('pros_entries')) : ?>
								<?php while (have_rows('pros_entries')) : the_row();
								$entry = get_sub_field('entry');
								?>
									<?php if ($entry) { ?>
										<p class="p body-text pro-text"><?php echo $entry; ?></p>
									<?php } ?>
								<?php endwhile; ?>
							<?php endif; ?>
						</div>
					</span>
				</div>
			</div>

		</div>
	</div>
</section>