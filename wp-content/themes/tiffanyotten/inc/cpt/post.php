<?php

class tiffanyottenCptPost extends tiffanyottenCptBase {

	var $pagination = 6;

	var $setup = [
		'hierarchical' => false, 
		'slug' => 'post', 
		'archive' => 'blog', 
		'name' => 'Blogs', 
		'singular' => 'Blog', 
	];

	var $tax_objects = [ 
		[
			'slug' => 'category',
			'name' => 'Categories', 
			'singular' => 'Category', 
			'hierarchical' => true
		]
	];

	public function addSettingsPage() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page( [
				'page_title' 	=> 'Posts - Settings',
				'menu_title' 	=> 'Settings',
				'menu_slug' 	=> 'posts_settings',
				'parent_slug'	=> 'edit.php',
			] );
		}
	}

	function isOwnTax() {
		return is_tax( $this->taxonomies ) || is_category();
	}

	function isOwnArchive() {
		return is_home() || is_tax( $this->taxonomies );
	}

	function __construct() {
		parent::__construct();
	}
}

// Instantiate the class to register taxonomies and post type
$tiffanyotten_cpt_post = new tiffanyottenCptPost();

add_action( 'init', function() {
	unregister_taxonomy_for_object_type( 'post_tag', 'post' );
}, 11 );