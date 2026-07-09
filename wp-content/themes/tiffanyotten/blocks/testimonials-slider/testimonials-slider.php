<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$heading      = tiffanyotten_heading_args( $args );
$footer_blurb = tiffanyotten_block_value( 'footer_blurb', $args );

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if ( $heading['eyebrow'] || $heading['title'] || $heading['blurb'] ) : ?>
				<div class="heading">
					<?php get_template_part( 'templates/_partials/heading', null, $heading ); ?>
				</div>
			<?php endif; ?>

			<?php if ( have_rows( 'entries' ) ) : ?>
				<div class="entries swiper-container">
					<div class="swiper-wrapper">
						<?php while ( have_rows( 'entries' ) ) : the_row();
							$quote            = get_sub_field( 'quote' );
							$attribution      = get_sub_field( 'attribution' );
							$attribution_meta = get_sub_field( 'attribution_meta' );
						?>
						<div class="entry swiper-slide">
							<div class="entry-text">
								<?php if ( $quote ) { ?>
									<blockquote class="blockquote quote"><?php echo $quote; ?></blockquote>
								<?php } ?>
								<?php if ( $attribution || $attribution_meta ) { ?>
									<p class="p attribution">
										<span class="quote-mark"></span>
										<?php echo $attribution; ?><?php echo $attribution_meta ? ( $attribution ? ', ' : '' ) . $attribution_meta : ''; ?>
									</p>
								<?php } ?>
							</div>
							<div class="entry-photo">
								<svg viewBox="0 0 208 246" xmlns="http://www.w3.org/2000/svg"><circle cx="104" cy="71.5" r="71.5" fill="#D9D9D9"/><path d="M208 246c0-57.4-46.6-104-104-104S0 188.6 0 246h208z" fill="#D9D9D9"/></svg>
							</div>
						</div>
						<?php endwhile; ?>
					</div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				</div>
				<script>
					document.addEventListener( "DOMContentLoaded", () => {
						var swiper = new Swiper ( $( '#<?php echo $blockid; ?> .swiper-container' ).get( 0 ), {
							slidesPerView: 1,
							spaceBetween: 32,
							navigation: {
								nextEl: '.swiper-button-next',
								prevEl: '.swiper-button-prev',
							},
							pagination: {
								el: ".swiper-pagination",
							},
						} );
					} );
				</script>
			<?php endif; ?>

			<?php if ( $footer_blurb ) : ?>
				<div class="p body-text footer-blurb"><?php echo $footer_blurb; ?></div>
			<?php endif; ?>

		</div>
	</div>
</section>
