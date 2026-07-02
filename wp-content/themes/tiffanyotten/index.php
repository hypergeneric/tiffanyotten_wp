<?php

/**
 * The template for displaying archives
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bmr
 */

get_header();

	if ( ! post_password_required() ) {
		get_template_part( 'templates/archive/archive', get_post_type() );
	} else {
		echo '<div class="section-wrap page-login-form"><section><div class="container"><div class="inner">';
		echo get_the_password_form();
		echo '</div></div></section></div>';
	}

get_footer();
