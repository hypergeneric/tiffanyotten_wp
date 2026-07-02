<?php

	$args    = isset( $args ) ? $args : null;
	$load_posts   = isset( $args['load_posts'] )   ? $args['load_posts']   : true;
	$post_type                = tiffanyotten_block_value('post_type', $args);

	if ( ! $post_type ) {
		$post_type = 'post';
	}

?>
<?php get_template_part( 'templates/_partials/listing', $post_type, [ 
	'post_type' => $post_type,
	'load_posts' => $load_posts
] ); ?>
