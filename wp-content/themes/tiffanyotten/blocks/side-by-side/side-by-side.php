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
$cta              = tiffanyotten_cta_args( $args );

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
						<?php if ( $cta['primary_cta'] || $cta['secondary_cta'] ) : ?>
							<?php get_template_part( 'templates/_partials/cta-group', null, $cta ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $media_type === 'content' ) : ?>
					<div class="graphic media-body">
						<?php get_template_part( 'templates/_partials/heading', null, $media_heading ); ?>
						<?php if ( $media_link ) : ?>
							<a class="media-link" href="<?php echo esc_url( $media_link['url'] ); ?>" target="<?php echo esc_attr( $media_link['target'] ); ?>"><?php echo $media_link['title']; ?><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="13" cy="13" r="12" stroke="currentColor" stroke-width="2"/><path d="M6.36401 6.36401C5.81173 6.36401 5.36401 6.81173 5.36401 7.36401C5.36401 7.9163 5.81173 8.36401 6.36401 8.36401L6.36401 7.36401L6.36401 6.36401ZM8.07112 8.07112C8.46164 7.6806 8.46164 7.04743 8.07112 6.65691L1.70716 0.292946C1.31663 -0.0975781 0.68347 -0.097578 0.292945 0.292946C-0.0975791 0.683471 -0.0975791 1.31664 0.292945 1.70716L5.9498 7.36401L0.292946 13.0209C-0.097578 13.4114 -0.0975779 14.0446 0.292946 14.4351C0.683471 14.8256 1.31664 14.8256 1.70716 14.4351L8.07112 8.07112ZM6.36401 7.36401L6.36401 8.36401L7.36401 8.36401L7.36401 7.36401L7.36401 6.36401L6.36401 6.36401L6.36401 7.36401Z" fill="currentColor" transform="translate(9 5.5)"/></svg></a>
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
							<a class="media-link" href="<?php echo esc_url( $media_link['url'] ); ?>" target="<?php echo esc_attr( $media_link['target'] ); ?>"><?php echo $media_link['title']; ?><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="13" cy="13" r="12" stroke="currentColor" stroke-width="2"/><path d="M6.36401 6.36401C5.81173 6.36401 5.36401 6.81173 5.36401 7.36401C5.36401 7.9163 5.81173 8.36401 6.36401 8.36401L6.36401 7.36401L6.36401 6.36401ZM8.07112 8.07112C8.46164 7.6806 8.46164 7.04743 8.07112 6.65691L1.70716 0.292946C1.31663 -0.0975781 0.68347 -0.097578 0.292945 0.292946C-0.0975791 0.683471 -0.0975791 1.31664 0.292945 1.70716L5.9498 7.36401L0.292946 13.0209C-0.097578 13.4114 -0.0975779 14.0446 0.292946 14.4351C0.683471 14.8256 1.31664 14.8256 1.70716 14.4351L8.07112 8.07112ZM6.36401 7.36401L6.36401 8.36401L7.36401 8.36401L7.36401 7.36401L7.36401 6.36401L6.36401 6.36401L6.36401 7.36401Z" fill="currentColor" transform="translate(9 5.5)"/></svg></a>
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