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
						$image   = get_sub_field('image');
						$title   = get_sub_field('title');
						$blurb   = get_sub_field('blurb');
						$linkage = get_sub_field('linkage');
						$tag     = $linkage ? 'a' : 'div';
					?>
						<<?php echo $tag; ?> <?php if ( $linkage ) : ?>href="<?php echo $linkage['url']; ?>" target="<?php echo $linkage['target']; ?>" title="<?php echo $linkage['title']; ?>"<?php endif; ?> class="entry">
							<span class="entry-inner">
								<?php if ($image) { ?>
								<span class="image-wrap">
									<?php echo tiffanyotten_print_img_src($image); ?>
								</span>
								<?php } ?>
								<div class="entry-text">
									<?php if ($title) { ?>
										<h3 class="h3"><?php echo $title; ?></h3>
									<?php } ?>
									<?php if ($blurb) { ?>
										<div class="p body-text"><?php echo $blurb; ?></div>
									<?php } ?>
									<?php if ( $linkage ) : ?>
										<span class="cta primary"><?php echo $linkage['title']; ?></span>
									<?php endif; ?>
								</div>
							</span>
						</<?php echo $tag; ?>>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>