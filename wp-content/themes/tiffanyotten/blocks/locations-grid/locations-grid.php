<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$terms = get_terms( array(
    'taxonomy'   => 'event_location',
    'hide_empty' => false,
) );

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if (have_rows('entries')) : ?>
				<div class="entries flex">
					<?php while (have_rows('entries')) : the_row();
						$title = get_sub_field('title');
						$image   = get_sub_field('image');
						$gmaps_image   = get_sub_field('google_maps_image');
						$address   = get_sub_field('address');
						$days_of_week   = get_sub_field('days_of_week');
						$hours   = get_sub_field('hours');
						$linkage = get_sub_field('linkage');
						$tag     = $linkage ? 'a' : 'div';
					?>
						<<?php echo $tag; ?> <?php if ( $linkage ) : ?>href="<?php echo $linkage['url']; ?>" target="<?php echo $linkage['target']; ?>" title="<?php echo $linkage['title']; ?>"<?php endif; ?> class="entry">
							<span class="entry-inner">
								<?php if ($image) { ?>
								<span class="image-wrap">
									<?php echo tiffanyotten_print_img_src($image); ?>
									<div class="google-maps-image">
										<?php echo tiffanyotten_print_img_src($gmaps_image); ?>
									</div>
								</span>
								<?php } ?>
								<div class="entry-text">
									<div class="top">
										<?php if ($title) { ?>
											<h3 class="h3"><?php echo $title; ?></h3>
										<?php } ?>
										<?php if ($address) { ?>
											<div class="p body-text"><?php echo $address; ?></div>
										<?php } ?>
									</div>
									<div class="bottom">
										<div class="hours">
											<?php if ($days_of_week) { ?>
												<div class="p body-text days-text"><?php echo $days_of_week; ?></div>
											<?php } ?>
											<?php if ($hours) { ?>
												<div class="p body-text"><?php echo $hours; ?></div>
											<?php } ?>
										</div>

										<?php if ( $linkage ) : ?>
											<span class="cta primary"><?php echo $linkage['title']; ?></span>
										<?php endif; ?>
									</div>
								</div>
							</span>
						</<?php echo $tag; ?>>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>