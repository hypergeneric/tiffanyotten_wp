<?php

	$eyebrow    = isset( $args['eyebrow'] ) ? $args['eyebrow'] : null;
	$eyebrow_style = isset( $args['eyebrow_style'] ) ? $args['eyebrow_style'] : null;
	$eyebrow_class = 'eyebrow' . ( $eyebrow_style === 'chip' ? ' eyebrow-chip' : '' );
	$title    = isset( $args['title'] ) ? $args['title'] : null;
	$title_size    = isset( $args['title_size'] ) ? $args['title_size'] : null;
	$title_style    = isset( $args['title_style'] ) ? $args['title_style'] : null;
	$title_weight    = isset( $args['title_weight'] ) ? $args['title_weight'] : null;
	$title_color    = isset( $args['title_color'] ) ? $args['title_color'] : null;
	$title_class = $title_size;
	if ( $title_style && $title_style !== 'default' ) {
		$title_class = $title_style === 'chip' ? 'eyebrow-chip' : $title_style;
	}
	$title_css = '';
	if ( $title_color ) {
		$title_css .= 'color:' . $title_color . ';';
	}
	if ( $title_weight && $title_weight !== 'default' ) {
		$title_css .= 'font-weight:' . $title_weight . ';';
	}
	$blurb    = isset( $args['blurb'] ) ? $args['blurb'] : null;
	$blurb_size    = isset( $args['blurb_size'] ) ? $args['blurb_size'] : null;
	$blurb_color    = isset( $args['blurb_color'] ) ? $args['blurb_color'] : null;
	$blurb_class = 'p' . ( $blurb_size && $blurb_size !== 'p' ? ' ' . $blurb_size : '' ) . ' body-text';

?>
<?php if ( $eyebrow ) : ?>
	<div class="<?php echo esc_attr( $eyebrow_class ); ?>"><?php echo $eyebrow; ?></div>
<?php endif; ?>
<?php if( $title || $blurb ): ?>
	<div class="heading-content">
		<?php if ( $title ) : ?>
			<<?php echo $title_size; ?> class="<?php echo esc_attr( $title_class ); ?>"<?php if ( $title_css ) : ?> style="<?php echo esc_attr( $title_css ); ?>"<?php endif; ?>><?php echo $title; ?></<?php echo $title_size; ?>>
		<?php endif; ?>
		<?php if ( $blurb ) : ?>
			<div class="<?php echo esc_attr( $blurb_class ); ?>"<?php if ( $blurb_color ) : ?> style="color:<?php echo esc_attr( $blurb_color ); ?>;"<?php endif; ?>><?php echo $blurb; ?></div>
		<?php endif; ?>
	</div>
<?php endif; ?>