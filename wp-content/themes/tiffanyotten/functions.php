<?php

include( get_template_directory() . '/inc/classes/class-config.php' );
include( get_template_directory() . '/inc/classes/class-themer.php' );
include( get_template_directory() . '/inc/functions.php' );
include( get_template_directory() . '/inc/default.php' );
include( get_template_directory() . '/inc/scripts.php' );
include( get_template_directory() . '/inc/acf.php' );
include( get_template_directory() . '/inc/cpt.php' );
include( get_template_directory() . '/inc/menus.php' );
include( get_template_directory() . '/inc/blocks.php' );
include( get_template_directory() . '/inc/shortcodes.php' );
include( get_template_directory() . '/inc/walkers.php' );
include( get_template_directory() . '/inc/media.php' );
include( get_template_directory() . '/inc/typography.php' );
include( get_template_directory() . '/inc/editor.php' );

function my_custom_mime_types($mimes) {
    $mimes['otf'] = 'font/otf';
    $mimes['ttf'] = 'font/ttf';
    return $mimes;
}
add_filter('upload_mimes', 'my_custom_mime_types');