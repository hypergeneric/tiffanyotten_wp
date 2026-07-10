<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

$columns     = tiffanyotten_block_value('columns', $args);
$layout      = tiffanyotten_block_value('layout', $args);
$card_shadow = tiffanyotten_block_value('card_shadow', $args);

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [
	'cols-' . ( $columns ? $columns : '2' ),
	'layout-' . ( $layout ? $layout : 'stacked' ),
	( $card_shadow ? 'cards-shadow' : '' ),
], $args, $context);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if (have_rows('entries')) : ?>
				<div class="entries flex">
					<?php while (have_rows('entries')) : the_row();
						$eyebrow    = get_sub_field('eyebrow');
						$eyebrow_bg = get_sub_field('eyebrow_background_color');
						$eyebrow_tx = get_sub_field('eyebrow_text_color');
						$image      = get_sub_field('image');
						$title      = get_sub_field('title');
						$blurb      = get_sub_field('blurb');
						$linkage    = get_sub_field('linkage');
						$card_color = get_sub_field('card_color');
						$tag        = $linkage ? 'a' : 'div';
						$entry_class = 'entry';
						$entry_style = '';
						if ( $card_color ) {
							$entry_class .= ' ' . tiffanyotten_light_or_dark( $card_color );
							$entry_style  = 'background-color:' . $card_color . ';';
						}
						$eyebrow_style = '';
						if ( $eyebrow_bg ) {
							$eyebrow_style .= 'background-color:' . $eyebrow_bg . ';';
						}
						if ( $eyebrow_tx ) {
							$eyebrow_style .= 'color:' . $eyebrow_tx . ';';
						}
					?>
						<<?php echo $tag; ?> <?php if ( $linkage ) : ?>href="<?php echo $linkage['url']; ?>" target="<?php echo $linkage['target']; ?>" title="<?php echo $linkage['title']; ?>"<?php endif; ?> class="<?php echo esc_attr($entry_class); ?>"<?php if ( $entry_style ) : ?> style="<?php echo esc_attr($entry_style); ?>"<?php endif; ?>>
							<span class="entry-inner">
								<?php if ($image) { ?>
								<span class="image-wrap">
									<?php echo tiffanyotten_print_img_src($image, [], "", true); ?>
								</span>
								<?php } ?>
								<div class="entry-text">
									<?php if ($eyebrow) { ?>
										<span class="eyebrow eyebrow-chip"<?php if ( $eyebrow_style ) : ?> style="<?php echo esc_attr($eyebrow_style); ?>"<?php endif; ?>><?php echo $eyebrow; ?></span>
									<?php } ?>
									<?php if ($title) { ?>
										<h3 class="h4"><?php echo $title; ?></h3>
									<?php } ?>
									<?php if ($blurb) { ?>
										<div class="p small body-text"><?php echo $blurb; ?></div>
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