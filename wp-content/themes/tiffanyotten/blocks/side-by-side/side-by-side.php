<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

$orientation      = tiffanyotten_block_value('orientation', $args);
$media_type       = tiffanyotten_block_value('media_type', $args);
$media_type       = $media_type ? $media_type : 'media';
$image_sizing     = tiffanyotten_block_value('image_sizing', $args);
$image_sizing     = $media_type === 'media' ? $image_sizing : 'contain';
$minimum_height   = tiffanyotten_block_value('minimum_height', $args);
$hero_mobile_top  = tiffanyotten_block_value('hero_mobile_top', $args);

$heading          = tiffanyotten_heading_args( $args );
$primary_cta      = tiffanyotten_block_value('primary_cta', $args);
$secondary_cta    = tiffanyotten_block_value('secondary_cta', $args);

$hero_icon         = tiffanyotten_block_value('hero_icon', $args);
$hero_image        = tiffanyotten_block_value('hero_image', $args);
$hero_image_mobile = tiffanyotten_block_value('hero_image_mobile', $args);
$hero_video        = tiffanyotten_block_value('hero_video', $args);
$hero_video_url    = tiffanyotten_block_value('hero_video_url', $args);

$media_heading     = tiffanyotten_heading_args( $args, 'media_' );
$logos_title       = tiffanyotten_block_value('logos_title', $args);
$logos             = tiffanyotten_block_value('logos', $args);
$media_link        = tiffanyotten_block_value('media_link', $args);

$hero_image['mobile']  = $hero_image_mobile ? $hero_image_mobile : false;

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [ 'image-'.$image_sizing, 'orient-'.$orientation, 'media-'.$media_type, ( $hero_icon ? 'hero-icon' : '' ), ( $hero_mobile_top ? 'hero-top' : '' ) ], $args, $context);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<div class="alt-deco"></div>
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<div class="content" style="min-height: <?php echo $minimum_height; ?>px;">

				<?php if ( $heading['eyebrow'] || $heading['title'] || $heading['blurb'] ) : ?>
					<div class="heading">
						<span class="entry-num-border"></span>
						<?php if ($hero_icon) : ?>
							<div class="icon">
								<?php echo tiffanyotten_print_img_src( $hero_icon ); ?>
							</div>
						<?php endif; ?>
						<?php get_template_part( 'templates/_partials/heading', null, $heading ); ?>
						<?php if ( $primary_cta || $secondary_cta ) : ?>
							<?php get_template_part( 'templates/_partials/cta-group', null, [
								'primary_cta' => $primary_cta,
								'secondary_cta' => $secondary_cta,
							] ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $media_type === 'content' ) : ?>
					<div class="graphic media-body">
						<?php get_template_part( 'templates/_partials/heading', null, $media_heading ); ?>
						<?php if ( $media_link ) : ?>
							<a class="media-link" href="<?php echo esc_url( $media_link['url'] ); ?>" target="<?php echo esc_attr( $media_link['target'] ); ?>"><?php echo $media_link['title']; ?></a>
						<?php endif; ?>
					</div>
				<?php elseif ( $media_type === 'logos' ) : ?>
					<div class="graphic">
						<div class="logos-card">
							<?php if ( $logos_title ) : ?>
								<p class="logos-title"><?php echo $logos_title; ?></p>
							<?php endif; ?>
							<?php if ( $logos ) : ?>
								<div class="logos">
									<?php foreach ( $logos as $logo ) : ?>
										<div class="logo"><?php echo tiffanyotten_print_img_src( $logo ); ?></div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
						<?php if ( $media_link ) : ?>
							<a class="media-link" href="<?php echo esc_url( $media_link['url'] ); ?>" target="<?php echo esc_attr( $media_link['target'] ); ?>"><?php echo $media_link['title']; ?></a>
						<?php endif; ?>
					</div>
				<?php elseif ($hero_image || $hero_video || $hero_video_url) : ?>
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