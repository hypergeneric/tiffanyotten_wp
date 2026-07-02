<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

$eyebrow           = tiffanyotten_block_value( 'eyebrow', $args );
$title             = tiffanyotten_block_value( 'title', $args );
$title_size        = tiffanyotten_block_value( 'title_size', $args );
$blurb             = tiffanyotten_block_value( 'blurb', $args );
$blurb_size        = tiffanyotten_block_value( 'blurb_size', $args );

$form_embed        = tiffanyotten_block_value( 'form_embed', $args );

$hero_image        = tiffanyotten_block_value('hero_image', $args);
$hero_image_mobile = tiffanyotten_block_value('hero_image_mobile', $args);
$hero_video        = tiffanyotten_block_value('hero_video', $args);
$hero_video_url    = tiffanyotten_block_value('hero_video_url', $args);

$hero_image['mobile']  = $hero_image_mobile ? $hero_image_mobile : false;

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<div class="bg-deco"></div>
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<div class="contentf flex">

				<div class="content-inner flex flex-v">

					<?php if ( $eyebrow || $title || $blurb ) : ?>
						<div class="heading">
							<?php get_template_part( 'templates/_partials/heading', null, [
								'eyebrow' => $eyebrow,
								'title' => $title,
								'title_size' => $title_size,
								'blurb' => $blurb,
								'blurb_size' => $blurb_size,
							] ); ?>
						</div>
					<?php endif; ?>

					<div class="form-wrapper">
						<?php echo $form_embed; ?>
					</div>

				</div>

				<?php if ($hero_image || $hero_video) : ?>
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