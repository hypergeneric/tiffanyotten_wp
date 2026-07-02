<?php

	$hero_clipping  = isset( $args['hero_clipping'] ) ? $args['hero_clipping'] : null;
	$hero_image     = isset( $args['hero_image'] ) ? $args['hero_image'] : null;
	$hero_video     = isset( $args['hero_video'] ) ? $args['hero_video'] : null;
	$hero_video_url = isset( $args['hero_video_url'] ) ? $args['hero_video_url'] : null;

	if ( $hero_video_url ) {
		$hero_video = $hero_video_url;
	}

?>
<div class="graphic clip-<?php echo $hero_clipping; ?>">
	<?php if ($hero_video) : ?>
		<?php echo tiffanyotten_print_video_src($hero_video, $hero_image, ['controls']); ?>
	<?php else : ?>
		<?php echo tiffanyotten_print_img_src($hero_image); ?>
	<?php endif; ?>
</div>