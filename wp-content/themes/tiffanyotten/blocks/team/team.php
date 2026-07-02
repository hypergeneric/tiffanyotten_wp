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

$padding_top       = tiffanyotten_block_value( 'padding_top', $args );
$padding_bottom    = tiffanyotten_block_value( 'padding_bottom', $args );
$eyebrow           = tiffanyotten_block_value( 'eyebrow', $args );
$title             = tiffanyotten_block_value( 'title', $args );
$blurb             = tiffanyotten_block_value( 'blurb', $args );
$cta               = tiffanyotten_block_value( 'cta', $args );
$hero_icon         = tiffanyotten_block_value( 'hero_icon', $args );
$entries           = tiffanyotten_block_value( 'entries', $args );

?>
<section id="<?php echo esc_attr( $blockid ); ?>" 
	class="<?php echo esc_attr( $blockslug ); ?> dark spacing-top-<?php echo esc_attr( $padding_top ); ?> spacing-bottom-<?php echo esc_attr( $padding_bottom ); ?>">
	<div class="container">
		<div class="inner">

			<?php if ($eyebrow || $title || $blurb) : ?>
			<div class="heading-wrap">
				<div class="heading">
					<?php if ($eyebrow) : ?>
						<div class="eyebrow"><?php echo $eyebrow; ?></div>
					<?php endif; ?>
					<?php if ($title) : ?>
						<h4 class="h4"><?php echo $title; ?></h4>
					<?php endif; ?>
					<?php if ($blurb) : ?>
						<p class="body-text"><?php echo $blurb; ?></p>
					<?php endif; ?>
				</div>
				<?php if ($hero_icon) : ?>
					<div class="icon">
						<?php echo tiffanyotten_print_img_src( $hero_icon ); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if (have_rows('entries')) : ?>
			<div class="entries">
				<?php while (have_rows('entries')) : the_row();
					$image           = get_sub_field('image');
					$video_file      = get_sub_field('video_file');
					$video_url       = get_sub_field('video_url');
					$name            = get_sub_field('name');
					$postion         = get_sub_field('postion');
					$story           = get_sub_field('story');
					$linkedin_url    = get_sub_field('linkedin_url');
					$x_url			 = get_sub_field('x_url');
					$instagram_url	 = get_sub_field('instagram_url');
					$book_a_call_url = get_sub_field('book_a_call_url');
					if ( $video_url ) {
						$video_file = $video_url;
					}
				?>
				<div class="entry">
					<div class="headshot">
						<?php echo tiffanyotten_print_img_src( $image ); ?>
					</div>
					<div class="context-wrap">
						<div class="context">
							<div class="context-top">
								<div class="name-title">
									<p class="p bold"><?php echo $name; ?></p>
									<?php if ( $postion ) { ?>
										<p class="p position"><?php echo $postion; ?></p>
									<?php } ?>
								</div>

								<div class="social">
									<?php if ($linkedin_url) : ?>
										<a class="linkedin" href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank"></a>
									<?php endif; ?>
									<?php if ($x_url) : ?>
										<a class="twitter" href="<?php echo esc_url( $x_url ); ?>" target="_blank"></a>
									<?php endif; ?>
									<?php if ($instagram_url) : ?>
										<a class="instagram" href="<?php echo esc_url( $instagram_url ); ?>" target="_blank"></a>
									<?php endif; ?>
								</div>
							</div>
							<?php if ( $story ) { ?>
								<p class="p story"><?php echo $story; ?></p>
							<?php } ?>
						</div>
						<div class="extra">
							<?php if ( $video_file ) { ?>
								<div class="graphic">
									<?php echo tiffanyotten_print_video_src($video_file, null, ['controls']); ?>
								</div>
							<?php } ?>
							<?php if ($book_a_call_url) : ?>
								<div class="cta-wrap">
									<a href="<?php echo $book_a_call_url['url']; ?>" target="<?php echo $book_a_call_url['target']; ?>" class="cta smaller"><?php echo $book_a_call_url['title']; ?></a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endwhile; ?>
			</div>
			<?php endif; ?>

			<span class="border"></span>
		</div>
	</div>
</section>
