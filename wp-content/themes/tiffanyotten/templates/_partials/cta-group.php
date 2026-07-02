<?php

	$primary_cta    = isset( $args['primary_cta'] ) ? $args['primary_cta'] : null;
	$secondary_cta    = isset( $args['secondary_cta'] ) ? $args['secondary_cta'] : null;

?>
<div class="cta-wrap flex">
	<?php if ( $primary_cta ) : ?>
	<a href="<?php echo $primary_cta['url']; ?>" target="<?php echo $primary_cta['target']; ?>" class="cta primary"><?php echo $primary_cta['title']; ?></a>
	<?php endif; ?>
	<?php if ( $secondary_cta ) : ?>
	<a href="<?php echo $secondary_cta['url']; ?>" target="<?php echo $secondary_cta['target']; ?>" class="cta secondary"><?php echo $secondary_cta['title']; ?></a>
	<?php endif; ?>
</div>