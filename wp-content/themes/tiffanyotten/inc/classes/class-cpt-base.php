<?php

class tiffanyottenCptBase {

	var $setup       = [];
	var $tax_objects = [];
	var $taxonomies  = [];
	var $request     = [];
	var $pagination  = -1;
	var $total       = 0;
	var $default_tax = "category";

	public function register() {
		// Register taxonomies
		$this->taxonomies = []; // Initialize to avoid duplicate entries
		foreach ( $this->tax_objects as $tax ) {
			$this->taxonomies[] = $tax['slug'];
			if ( $tax['slug'] == 'category' || $tax['slug'] == 'post_tag' ) {
				continue; // skip built-in taxonomies
			}
			$args = array(
				'labels' => [
					"name" => __( $tax['name'], "tiffanyotten" ),
					"singular_name" => __( $tax['singular'], "tiffanyotten" ),
				],
				'hierarchical' => $tax['hierarchical'],
				'public' => true,
				'show_ui' => true,
				'show_in_rest' => true,
				'show_admin_column' => true,
				'rewrite' => [ 
					'slug' => $this->setup['archive'] . '/' . $tax['slug'], 
					'with_front' => false,
				],
			);
			register_taxonomy( $tax['slug'], [ $this->setup['slug'] ], $args );
		}

		if ( $this->setup['slug'] == 'post' ) {
			return;
		}

		// Register the post type
		$args = [
			'label'  => esc_html__( $this->setup['name'], 'tiffanyotten' ),
			'labels' => [
				'menu_name'          => esc_html__( $this->setup['name'], 'tiffanyotten' ),
				'name_admin_bar'     => esc_html__( $this->setup['singular'], 'tiffanyotten' ),
				'add_new'            => esc_html__( 'Add ' . $this->setup['singular'], 'tiffanyotten' ),
				'add_new_item'       => esc_html__( 'Add new ' . $this->setup['singular'], 'tiffanyotten' ),
				'new_item'           => esc_html__( 'New ' . $this->setup['singular'], 'tiffanyotten' ),
				'edit_item'          => esc_html__( 'Edit ' . $this->setup['singular'], 'tiffanyotten' ),
				'view_item'          => esc_html__( 'View ' . $this->setup['singular'], 'tiffanyotten' ),
				'update_item'        => esc_html__( 'Update ' . $this->setup['singular'], 'tiffanyotten' ),
				'all_items'          => esc_html__( 'All ' . $this->setup['name'], 'tiffanyotten' ),
				'search_items'       => esc_html__( 'Search ' . $this->setup['name'], 'tiffanyotten' ),
				'parent_item_colon'  => esc_html__( 'Parent ' . $this->setup['singular'], 'tiffanyotten' ),
				'not_found'          => esc_html__( 'No ' . $this->setup['name']. ' found', 'tiffanyotten' ),
				'not_found_in_trash' => esc_html__( 'No ' .$this->setup['name']. ' found in Trash', 'tiffanyotten' ),
				'name'               => esc_html__( $this->setup['name'], 'tiffanyotten' ),
				'singular_name'      => esc_html__( $this->setup['singular'], 'tiffanyotten' ),
			],
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
			'hierarchical'        => $this->setup['hierarchical'],
			'has_archive'         => $this->setup['archive'],
			'query_var'           => true,
			'can_export'          => true,
			'show_in_menu'        => true,
			'supports'            => $this->setup['supports'],
			'rewrite'             => [ 'slug' => $this->setup['slug'], 'with_front' => false ],
			'taxonomies'          => $this->taxonomies,
		];
		register_post_type( $this->setup['slug'], $args );

	}

	function registerSidebar() {
		register_sidebar( [
			'id'            => $this->setup['slug'] . '-single',
			'name'          => __( $this->setup['name'] . ' Sidebar', 'tiffanyotten' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="">',
			'after_title'   => '</h5>',
		] );
	}

	public function addSettingsPage() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page( [
				'page_title' 	=> $this->setup['singular'] . ' Settings',
				'menu_title' 	=> 'Settings',
				'menu_slug' 	=> $this->setup['slug'] . '_settings',
				'parent_slug'	=> 'edit.php?post_type=' . $this->setup['slug']
			] );
		}
	}

	function getTaxonomies() {
		return $this->taxonomies;
	}

	function isOwnTax() {
		return count( $this->taxonomies ) == 0 ? false : is_tax( $this->taxonomies );
	}

	function isOwnSingle() {
		return is_singular( $this->setup['slug'] );
	}

	function isOwnArchive() {
		return is_post_type_archive( $this->setup['slug'] ) || $this->isOwnTax();
	}

	public function modifyPreGetPosts( $query ) {
		if ( ! is_admin() && $query->is_main_query() && $this->isOwnArchive() ) {
			$query->set( 'posts_per_page', $this->pagination );
			if ( $this->pagination != -1 ) {
				$query->set( 'paged', get_query_var('page') );
			}
			// Default tax_query setup
			$tax_query = [ 'relation' => 'AND' ];
			foreach ( $this->taxonomies as $taxonomy ) {
				if ( isset( $_GET[ $taxonomy ] ) && ! empty( $_GET[ $taxonomy ] ) ) {
					$tax_query[] = [
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $_GET[ $taxonomy ] ),
					];
				}
			}
			// Set tax_query if there are filters
			if ( count( $tax_query ) > 1 ) {
				$query->set( 'tax_query', $tax_query );
			}
			// Check for 'sortdir' parameter and apply sorting if present
			$sortdir = isset($_GET['sortdir']) ? sanitize_text_field($_GET['sortdir']) : null;
			if ($sortdir) {
				$query->set('orderby', 'date'); // Default ordering by date
				$query->set('order', strtoupper($sortdir) === 'ASC' ? 'ASC' : 'DESC'); // Validate and set sort direction
			}
		}
	}

	public function redirectTaxArchives() {
		if ( $this->isOwnTax() ) {
			$current_term = get_queried_object();
			$taxonomy     = $current_term->taxonomy;
			$term_slug    = $current_term->slug;
			if ( ! isset( $_GET[ $taxonomy ] ) ) {
				$redirect_url = add_query_arg( [ $taxonomy => $term_slug ], get_post_type_archive_link( $this->setup['slug'] ) );
				wp_redirect( $redirect_url, 301 );
				exit;
			}
		}
	}

	public function registerRestApi() {
		register_rest_route( 'custom/v2', '/' . $this->setup['slug'], [
			'methods'  => 'POST',
			'callback' => [ $this, 'getPostsData' ],
			'permission_callback' => '__return_true'
		] );
	}

	public function getAvailableYears() {
		global $wpdb;

		// Query to get distinct years with posts
		$results = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT YEAR(post_date) 
				FROM $wpdb->posts 
				WHERE post_type = %s 
				AND post_status = 'publish' 
				ORDER BY post_date DESC",
				$this->setup['slug']
			)
		);

		return $results ? $results : [];
	}

	function getRequestParam( $request, $param, $default = '' ) {
		if ( $request instanceof WP_REST_Request ) {
			$value = $request->get_param( $param );
			// If the value is null or an empty string, return the default
			return ( isset( $value ) && $value !== '' ) ? $value : $default;
		}
		return $default;
	}

	function buildBaseArgs($args = []) {
		// Start the WP query args
		$args['post_type']      = $this->setup['slug'];
		$args['posts_per_page'] = $this->pagination;

		// Check if sortdir exists in the request and apply sorting
		$sortdir = $this->getRequestParam($this->request, 'sortdir', null);
		if ($sortdir) {
			$args['orderby'] = 'date'; // Default ordering by date
			$args['order']   = strtoupper($sortdir) === 'ASC' ? 'ASC' : 'DESC'; // Validate sort direction
		}

		return $args;
	}

	function buildTaxQuery( $args=[] ) {
		// create the taxonomy array
		$tax_query = [ 'relation' => 'AND' ];
		// Loop through the taxonomies array and build the tax_query
		foreach ( $this->taxonomies as $taxonomy ) {
			$value = $this->getRequestParam( $this->request, $taxonomy );
			if ( !empty( $value ) ) {
				$tax_query[] = [
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $value
				];
			}
		}
		// add it to the query
		if ( ! empty( $tax_query ) && count( $tax_query ) > 1 ) {
			$args['tax_query'] = $tax_query;
		}
		return $args;
	}

	function buildSearchQuery( $args=[] ) {
		$search = $this->getRequestParam( $this->request, 'search' );
		// Add search parameter if present
		if ( $search != '' ) {
			$args['s'] = $search;
		}
		return $args;
	}

	function buildPaginationQuery( $args=[] ) {
		if ( $this->pagination != -1 ) {
			$current_page = $this->getRequestParam( $this->request, 'page', get_query_var('page') );
			$args['paged'] = $current_page; // Add paging
		}
		return $args;
	}

	function renderPagination( $args=false ) {
		if ( $this->pagination == -1 ) {
			return '';
		}
		$current_page = $this->getRequestParam( $this->request, 'page', get_query_var('page') );
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$main_query = new WP_Query( $args );
			$total = $main_query->max_num_pages;
		} else {
			global $wp_query;
			$total = $wp_query->max_num_pages;
		}
		$base_url = remove_query_arg( 'page', get_post_type_archive_link( $this->setup['slug'] ) );
		$pagination = paginate_links( [
			'base'      => add_query_arg( 'page', '%#%', $base_url ),
			'format'    => '',
			'current'   => max( 1, $current_page ),
			'total'     => $total,
			'prev_text' => '<span class="pagination-arrows">«</span> Previous',
			'next_text' => 'Next <span class="pagination-arrows">»</span>',
			'type'      => 'plain',
		] );
		return $pagination;
	}

	public function renderPosts( $args ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$main_query = new WP_Query( $args );
			if ( $main_query->have_posts() ) : ?>
				<?php $i = 0; while ( $main_query->have_posts() ) : $main_query->the_post(); ?>
					<?php get_template_part( 'templates/_partials/entry', get_post_type(), [
						'taxonomy' => count( $this->taxonomies ) > 0 ? $this->taxonomies[0] : null
					] ); ?>
				<?php $i += 1; endwhile; ?>
			<?php endif;
			$this->total = $main_query->found_posts;
		} else {
			if ( have_posts() ) : ?>
				<?php $i = 0; while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'templates/_partials/entry', get_post_type(), [
						'taxonomy' => count( $this->taxonomies ) > 0 ? $this->taxonomies[0] : null
					] ); ?>
				<?php $i += 1; endwhile; ?>
			<?php endif;
			global $wp_query; 
			$this->total = $wp_query->found_posts;
		}
	}

	function getPostsData( $request ) {

		$this->request = $request;

		$args = $this->buildBaseArgs();
		$args = $this->buildTaxQuery( $args );
		$args = $this->buildSearchQuery( $args );
		$args = $this->buildPaginationQuery( $args );

		$response      = '';
		$tt_no_results = "No results found!";

		ob_start();
		$this->renderPosts( $args );
		$response .= ob_get_clean();

		if ( $response == '' ) {
			$response = $tt_no_results;
		}

		if ( $this->request === true ) {
			return $response;
		}

		return new WP_REST_Response( [
			'entries'    => $response,
			'pagination' => $this->renderPagination( $args ),
			'total' => $this->total,
		], 200 );

	}

	function render() {

		return [
			'entries'    => $this->getPostsData( true ),
			'pagination' => $this->renderPagination(),
			'total'      => $this->total,
		];

	}

	function __construct() {
		add_action( 'init', [ $this, 'register' ], 1 );
		add_action( 'init', [ $this, 'addSettingsPage' ] );
		add_action( 'widgets_init', [ $this, 'registerSidebar' ] );
		add_action( 'pre_get_posts', [ $this, 'modifyPreGetPosts' ] );
		add_action( 'template_redirect', [ $this, 'redirectTaxArchives' ] );
		add_action( 'rest_api_init', [ $this, 'registerRestApi' ] );
		add_filter( 'wpseo_next_rel_link', '__return_false' );
		add_filter( 'wpseo_prev_rel_link', '__return_false' );
		add_filter( 'wpseo_canonical', function( $canonical ) {
			if ( $this->isOwnArchive() && is_paged() ) {
				return false;
			}
			return $canonical;
		} );
		add_filter('redirect_canonical', function ( $redirect_url ) {
			if ( $this->isOwnArchive() && get_query_var('page') ) {
				return false;
			}
			return $redirect_url;
		});
	}
}