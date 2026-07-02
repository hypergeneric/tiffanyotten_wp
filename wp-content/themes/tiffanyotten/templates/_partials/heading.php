<?php

	$eyebrow    = isset( $args['eyebrow'] ) ? $args['eyebrow'] : null;
	$title    = isset( $args['title'] ) ? $args['title'] : null;
	$title_size    = isset( $args['title_size'] ) ? $args['title_size'] : null;
	$blurb    = isset( $args['blurb'] ) ? $args['blurb'] : null;
	$blurb_size    = isset( $args['blurb_size'] ) ? $args['blurb_size'] : null;

?>
<?php if ( $eyebrow ) : ?>
	<div class="eyebrow"><?php echo $eyebrow; ?></div>
<?php endif; ?>
<?php if( $title || $blurb ): ?>
	<div class="heading-content">
		<?php if ( $title ) : ?>
			<<?php echo $title_size; ?> class="<?php echo $title_size; ?>"><?php echo $title; ?></<?php echo $title_size; ?>>
		<?php endif; ?>
		<?php if ( $blurb ) : ?>
			<div class="p <?php echo $blurb_size; ?> body-text"><?php echo $blurb; ?></div>
		<?php endif; ?>
	</div>
<?php endif; ?>