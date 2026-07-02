<?php

	$type                    = get_post_type();
	$taxonomy                = isset( $args['taxonomy'] ) ? $args['taxonomy'] : 'post_tag';
	$the_date                = get_the_date();
	$first_tag               = get_first_taxonomy_term( get_the_ID(), $taxonomy );
	$event_location          = get_field( 'event_location' );
	$event_date              = get_field( 'event_date' );
	$event_time              = get_field( 'event_time' );

	list( $title, $link, $target, $image, $excerpt ) = tiffanyotten_get_normalized_entry_props( null, "wide-16:9" );
	
?>
<a href="<?php echo esc_url( $link ); ?>" target="<?php echo $target; ?>" class="entry entry-default entry-<?php echo $type; ?> entry-tax-<?php echo $first_tag ? $first_tag->slug : ''; ?>">
	<?php if ( $image ): ?>
		<div class="graphic <?php echo $img_format; ?>">
			<?php if ( $first_tag ) : ?>
				<div class="tax tax-<?php echo esc_attr( $first_tag->slug ); ?>"><?php echo esc_html( $first_tag->name ); ?></div>
			<?php endif; ?>
			<?php echo tiffanyotten_print_img_src( $image ); ?>
		</div>
	<?php endif; ?>
	<div class="meta">
		<?php if ( $event_location ) : ?>
			<div class="event-location"><?php echo $event_location; ?></div>
		<?php endif; ?>
		<div class="date-time">
			<div class="date"><?php echo $event_date; ?></div>
			<?php if ( $event_time ) : ?>
				<div class="time"><?php echo $event_time; ?></div>
			<?php endif; ?>
		</div>
	</div>
	<div class="info">
		<h5 class="h5"><?php echo $title; ?></h5>
		<?php if( $excerpt): ?>
			<p><?php echo $excerpt; ?></p>
		<?php endif; ?>
	</div>
	<button class="cta">Reserve your spot</button>
</a>