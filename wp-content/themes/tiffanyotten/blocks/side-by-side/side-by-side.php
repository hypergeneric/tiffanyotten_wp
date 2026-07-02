<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

$orientation      = tiffanyotten_block_value('orientation', $args);
$image_sizing     = tiffanyotten_block_value('image_sizing', $args);
$minimum_height   = tiffanyotten_block_value('minimum_height', $args);
$hero_mobile_top  = tiffanyotten_block_value('hero_mobile_top', $args);

$eyebrow          = tiffanyotten_block_value('eyebrow', $args);
$title            = tiffanyotten_block_value('title', $args);
$title_size       = tiffanyotten_block_value('title_size', $args);
$blurb            = tiffanyotten_block_value('blurb', $args);
$blurb_size       = tiffanyotten_block_value('blurb_size', $args);
$primary_cta      = tiffanyotten_block_value('primary_cta', $args);
$secondary_cta    = tiffanyotten_block_value('secondary_cta', $args);

$hero_icon         = tiffanyotten_block_value('hero_icon', $args);
$hero_image        = tiffanyotten_block_value('hero_image', $args);
$hero_image_mobile = tiffanyotten_block_value('hero_image_mobile', $args);
$hero_video        = tiffanyotten_block_value('hero_video', $args);
$hero_video_url    = tiffanyotten_block_value('hero_video_url', $args);

$hero_image['mobile']  = $hero_image_mobile ? $hero_image_mobile : false;

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [ 'image-'.$image_sizing, 'orient-'.$orientation, ( $hero_icon ? 'hero-icon' : '' ), ( $hero_mobile_top ? 'hero-top' : '' ) ], $args, $context);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<div class="alt-deco"></div>
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<div class="content" style="min-height: <?php echo $minimum_height; ?>px;">

				<?php if ( $eyebrow || $title || $blurb ) : ?>
					<div class="heading">
						<span class="entry-num-border"></span>
						<?php if ($hero_icon) : ?>
							<div class="icon">
								<?php echo tiffanyotten_print_img_src( $hero_icon ); ?>
							</div>
						<?php endif; ?>
						<?php get_template_part( 'templates/_partials/heading', null, [
							'eyebrow' => $eyebrow,
							'title' => $title,
							'title_size' => $title_size,
							'blurb' => $blurb,
							'blurb_size' => $blurb_size,
						] ); ?>
						<?php if ( $primary_cta || $secondary_cta ) : ?>
							<?php get_template_part( 'templates/_partials/cta-group', null, [
								'primary_cta' => $primary_cta,
								'secondary_cta' => $secondary_cta,
							] ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ($hero_image || $hero_video || $hero_video_url) : ?>
					<?php get_template_part( 'templates/_partials/hero-graphic', null, [
						'hero_image'     => $hero_image,
						'hero_video'     => $hero_video,
						'hero_video_url' => $hero_video_url,
					] ); ?>
				<?php endif; ?>

			</div>

		</div>
	</div>
</section>