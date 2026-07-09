<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$heading             = tiffanyotten_heading_args( $args );
$cta                 = tiffanyotten_cta_args( $args );
$layout			     = tiffanyotten_block_value('layout', $args);

$heading_class = "";
if ( $layout ) {
	$heading_class = ' layout-' . $layout;
}

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>"> 
	<div class="bg-deco"></div>
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if ( $heading['eyebrow'] || $heading['title'] || $heading['blurb'] || $cta['primary_cta'] || $cta['secondary_cta'] ) : ?>
				<div class="heading<?php echo esc_attr($heading_class); ?>">
					<?php get_template_part( 'templates/_partials/heading', null, $heading ); ?>
					<?php if ( $cta['primary_cta'] || $cta['secondary_cta'] ) : ?>
						<?php get_template_part( 'templates/_partials/cta-group', null, $cta ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>

</section>