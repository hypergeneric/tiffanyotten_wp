<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$image = tiffanyotten_block_value('image', $args);
$title = tiffanyotten_block_value('title', $args);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if ( $image || $title ) { ?>
				<div class="heading">
					<?php if ( $image ) { ?>
						<?php echo tiffanyotten_print_img_src($image); ?>
					<?php } ?>
					<?php if ( $title ) { ?>
						<p class="p large"><?php echo $title; ?></p>
					<?php } ?>

				</div>
			<?php } ?>


			<?php if ( have_rows( 'entries' ) ) : ?>
				<div class="entries swiper-container">
					<div class="swiper-wrapper">
						<?php $i = 0; while ( have_rows( 'entries' ) ) : the_row(); 
							$quote            = get_sub_field( 'quote' );
							$attribution      = get_sub_field( 'attribution' );
							$attribution_meta = get_sub_field( 'attribution_meta' );
						?>
						<div class="entry flex flex-v swiper-slide">
							<?php if ( $quote ) { ?>
								<div class="p large"><?php echo $quote; ?></div>
							<?php } ?>
							<?php if ( $attribution ) { ?>
								<p class="p bold"><?php echo $attribution; ?></p>
							<?php } ?>
							<?php if ( $attribution_meta ) { ?>
								<p class="p small"><?php echo $attribution_meta; ?></p>
							<?php } ?>
						</div>
						<?php $i += 1; endwhile; ?>
					</div>
					<div class="swiper-button-prev">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none">
							<path d="M0.219864 8.03104L6.96986 14.781C7.11059 14.9218 7.30147 15.0008 7.50049 15.0008C7.69951 15.0008 7.89038 14.9218 8.03111 14.781C8.17184 14.6403 8.25091 14.4494 8.25091 14.2504C8.25091 14.0514 8.17184 13.8605 8.03111 13.7198L2.5608 8.25042H17.2505C17.4494 8.25042 17.6402 8.1714 17.7808 8.03075C17.9215 7.8901 18.0005 7.69933 18.0005 7.50042C18.0005 7.3015 17.9215 7.11074 17.7808 6.97009C17.6402 6.82943 17.4494 6.75042 17.2505 6.75042H2.5608L8.03111 1.28104C8.17184 1.14031 8.25091 0.94944 8.25091 0.750417C8.25091 0.551394 8.17184 0.360523 8.03111 0.219792C7.89038 0.0790615 7.69951 0 7.50049 0C7.30147 0 7.11059 0.0790615 6.96986 0.219792L0.219864 6.96979C0.150131 7.03945 0.0948105 7.12216 0.0570679 7.21321C0.0193253 7.30426 -0.000101089 7.40186 -0.000101089 7.50042C-0.000101089 7.59898 0.0193253 7.69657 0.0570679 7.78762C0.0948105 7.87867 0.150131 7.96139 0.219864 8.03104Z" fill="#5B6A66"/>
						</svg>
					</div>
					<div class="swiper-button-next">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path d="M20.7806 12.531L14.0306 19.281C13.8899 19.4218 13.699 19.5008 13.5 19.5008C13.301 19.5008 13.1101 19.4218 12.9694 19.281C12.8286 19.1403 12.7496 18.9494 12.7496 18.7504C12.7496 18.5514 12.8286 18.3605 12.9694 18.2198L18.4397 12.7504H3.75C3.55109 12.7504 3.36032 12.6714 3.21967 12.5307C3.07902 12.3901 3 12.1993 3 12.0004C3 11.8015 3.07902 11.6107 3.21967 11.4701C3.36032 11.3294 3.55109 11.2504 3.75 11.2504H18.4397L12.9694 5.78104C12.8286 5.64031 12.7496 5.44944 12.7496 5.25042C12.7496 5.05139 12.8286 4.86052 12.9694 4.71979C13.1101 4.57906 13.301 4.5 13.5 4.5C13.699 4.5 13.8899 4.57906 14.0306 4.71979L20.7806 11.4698C20.8504 11.5394 20.9057 11.6222 20.9434 11.7132C20.9812 11.8043 21.0006 11.9019 21.0006 12.0004C21.0006 12.099 20.9812 12.1966 20.9434 12.2876C20.9057 12.3787 20.8504 12.4614 20.7806 12.531Z" fill="#F4EFEB"/>
						</svg>
					</div>
				</div>
				<script>
					document.addEventListener( "DOMContentLoaded", () => {
						var swiper = new Swiper ( $( '#<?php echo $blockid; ?> .swiper-container' ).get( 0 ), { 
							slidesPerView: 1,
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

		</div>
	</div>
</section>
