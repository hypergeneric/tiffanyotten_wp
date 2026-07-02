<?php

	$post_type       = get_post_type();
	$title           = get_the_title();
	$author_id       = get_the_author_meta( 'ID' );
	$show_author     = get_field( "show_author" );
	$override_author = get_field( "override_author" );
	$author_name     = $override_author ? get_field( "author_name" ) : get_the_author_meta( "display_name", $author_id );
	$reading_time    = reading_time( get_the_content() );
	$hero_image      = tiffanyotten_get_featured_image();
	$hero_video      = get_field("featured_video");
	$hero_video_url  = get_field("featured_video_url");

	if ( $hero_video_url ) {
		$hero_video = $hero_video_url;
	}

	$category = false;
	switch ( $post_type ) {
		case 'post':
			$category = 'category';
			break;
	}
	$first_term = $category ? get_first_taxonomy_term( get_the_ID(), $category ) : false;
	$sidebar_id = is_active_sidebar( $post_type . '-single' ) ? $post_type . '-single' : 'post-single';

?>

<section id="single-head-<?php echo get_the_ID(); ?>" class="page-head default dark single spacing-bottom-none">
	<div class="container">
		<div class="inner">
			<div class="heading">
				<?php get_template_part( 'templates/_partials/breadcrumb', get_post_type() ); ?>
				<div class="meta body-text">
					<?php if ( $first_term ) : ?>
						<span class="archivo s_16 w_400"><?php echo $first_term->name; ?></span>
						<span class="archivo s_16 w_400">|</span>
					<?php endif; ?>
					<span class="archivo s_16 w_400"><?php echo $reading_time; ?></span>
				</div>
				<h1 class="tobias s_72 w_300"><?php echo $title; ?></h1>
			</div>
			<div class="graphic">
				<?php if ( $hero_video ) : ?>
					<?php echo tiffanyotten_print_video_src( $hero_video, $hero_image, ['controls'] ); ?>
				<?php else : ?>
					<?php echo tiffanyotten_print_img_src( $hero_image ); ?>
				<?php endif; ?>
			</div>
			<div class="lower">
				<div class="meta-groups">
					<?php if ($show_author) : ?>
						<div class="meta-group">
							<span class="body-text archivo s_16 w_400">Written By</span>
							<span class="archivo s_16 w_400"><?php echo $author_name; ?></span>
						</div>
					<?php endif; ?>
					<div class="meta-group">
						<span class="body-text archivo s_16 w_400">Published On</span>
						<span class="archivo s_16 w_400"><?php echo get_the_date(); ?></span>
					</div>
				</div>
				<?php get_template_part( 'templates/_partials/share', get_post_type() ); ?>
			</div>
		</div>
	</div>
</section>

<section id="single-body-<?php echo get_the_ID(); ?>" class="single-body">
	<div class="container">
		<div class="inner">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<div class="sidebar">
				<?php if ( is_active_sidebar( $sidebar_id ) ) : dynamic_sidebar( $sidebar_id ); endif; ?>
			</div>
		</div>
	</div>
</section>