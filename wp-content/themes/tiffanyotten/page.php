<?php

/**
 * The template for displaying all pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bmr
 */

get_header();

	if ( ! post_password_required() ) {
		if ( ! tiffanyotten_has_acf_blocks( get_the_ID() ) ) :
			echo '<section><div class="container"><div class="inner inset"><div class="entry-content">';
			echo '<h1>';
				the_title();
			echo '</h1>';
		endif;
		the_content();
		if ( ! tiffanyotten_has_acf_blocks( get_the_ID() ) ) :
			echo '</div></div></div></section>';
		endif;
	} else {
		echo '<div class="section-wrap page-login-form"><section><div class="container"><div class="inner">';
		echo get_the_password_form();
		echo '</div></div></section></div>';
	}
	
get_footer(); 
