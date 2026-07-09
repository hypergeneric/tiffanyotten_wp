<?php

	$primary_cta    = isset( $args['primary_cta'] ) ? $args['primary_cta'] : null;
	$secondary_cta    = isset( $args['secondary_cta'] ) ? $args['secondary_cta'] : null;
	$cta_style    = isset( $args['cta_style'] ) && $args['cta_style'] ? $args['cta_style'] : 'default';
	$cta_size    = isset( $args['cta_size'] ) && $args['cta_size'] ? $args['cta_size'] : 'default';
	$cta_shadow    = ! empty( $args['cta_shadow'] );

?>
<div class="cta-wrap flex cta-style-<?php echo esc_attr( $cta_style ); ?> cta-size-<?php echo esc_attr( $cta_size ); ?><?php echo $cta_shadow ? ' cta-shadow' : ''; ?>">
	<?php if ( $primary_cta ) : ?>
	<a href="<?php echo $primary_cta['url']; ?>" target="<?php echo $primary_cta['target']; ?>" class="cta primary"><?php echo $primary_cta['title']; ?></a>
	<?php endif; ?>
	<?php if ( $secondary_cta ) : ?>
	<a href="<?php echo $secondary_cta['url']; ?>" target="<?php echo $secondary_cta['target']; ?>" class="cta secondary"><?php echo $secondary_cta['title']; ?></a>
	<?php endif; ?>
</div>