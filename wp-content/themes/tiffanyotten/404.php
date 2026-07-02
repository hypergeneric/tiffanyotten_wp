<?php get_header(); ?>
<?php tiffanyotten_render_block( 'basic-heading', null, [
	'title_size'     => 'h1',
	'title'          => get_field( 'the_404_page_main_heading', 'options' ),
	'blurb'          => get_field( 'the_404_page_copy', 'options' ),
	'layout'         => 'left',
	'padding_bottom' => 'none',
] ); ?>
<?php tiffanyotten_render_block( 'inline-cta-group', null, [
	'padding_top'    => 'none',
	'alignment'      => 'left',
	'primary_cta'    => get_field( 'the_404_page_cta', 'options' ),
] ); ?>
<?php get_footer(); ?>