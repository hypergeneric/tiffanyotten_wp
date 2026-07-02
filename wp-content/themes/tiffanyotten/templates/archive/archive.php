<?php

	$type = get_post_type();
	$post_type_obj = get_post_type_object( $type );

?>

<?php tiffanyotten_render_block( 'basic-heading', $type, [
	'eyebrow'          => $post_type_obj->labels->name,
	'title_size'       => 'h1',
	'title'            => get_the_archive_title(),
	'blurb'            => get_the_archive_description(),
	'blurb_size'       => 'large',
] ); ?>

<?php tiffanyotten_render_block( 'listing', $type, [
	'post_type'        => $post_type,
	'load_posts'       => false,
] ); ?>
