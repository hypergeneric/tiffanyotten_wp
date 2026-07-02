<?php

	$filter_by    = isset( $args['filter_by'] )    ? $args['filter_by']    : "Filter by";
	$filter_clear = isset( $args['filter_clear'] ) ? $args['filter_clear'] : "Clear All";
	$search_text  = isset( $args['search_text'] )  ? $args['search_text']  : "Search";
	$post_type    = isset( $args['post_type'] )    ? $args['post_type']    : get_post_type();
	$load_posts   = isset( $args['load_posts'] )   ? $args['load_posts']   : false;

	if ( ! $post_type ) {
		$post_type = 'post';
	}

	$classname =  'tiffanyotten_cpt_' . str_replace( "-", "_", $post_type );
	global $$classname;
	$render_data = $$classname->render();

	$tax_list        = $$classname->getTaxonomies();
	$tax_count       = 0;
	$tax_markup      = '';
	foreach ( $tax_list as $tax ) {
		$select = tiffanyotten_get_taxonomy_select( $tax );
		if ( $select ) {
			$tax_count += 1;
			$tax_markup .= $select;
		}
	}

	$tax_markup = str_replace( '<div class="tax-radio-group" id="tax-radio-event_location">', '<div class="tax-radio-group" id="tax-radio-event_location"><label><input type="radio" name="event_location" value="" checked>All</label>', $tax_markup );

	$sortdir      = isset($_GET['sortdir']) ? sanitize_text_field($_GET['sortdir']) : 'desc';
	$sortdirlabel = $sortdir == 'desc' ? 'Newest' : 'Oldest';

?>
<div id="listing" class="section-wrap block-1 archive-listing <?php echo $post_type; ?>-listing">
	<section class="margin-top-default margin-bottom-default dark">
		<div class="container">
			<div class="inner">
				<div class="loader"><div class="progress"></div></div>
				<div class="title">
					<h2 class="tobias s_48 w_300"><?php echo $$classname->setup['name']; ?></h2>
				</div>
				<div class="top-bar">
					<div class="results">Results: <span><?php echo $load_posts ? '' : $render_data['total']; ?><span></div>
					<div class="" id="sort-results" data-value="<?php echo $sortdir; ?>" data-desc-label="Newest" data-desc-value="desc" data-asc-label="Oldest" data-asc-value="asc"><span><?php echo $sortdirlabel; ?></span></div>
				</div>
				<div class="entries-outer">
					<div class="filters">
						<div class="filter-wrapper">
							<h5 class="h5">Locations</h5>
							<div class="search">
								<div class="icon">
									<img src="<?php echo get_stylesheet_directory_uri() . '/assets/svg/search-icon-grey-alt.svg'; ?>" />
								</div>
								<input id="tax-search" name="search" autocomplete="off" type="text" placeholder="<?php echo $search_text; ?>">
							</div>
							<?php do_action( 'tiffanyotten_listing_filters_before' ); ?>
							<div class="selectors count-<?php echo $tax_count; ?>"><?php echo $tax_markup; ?></div>
							<?php do_action( 'tiffanyotten_listing_filters_after' ); ?>
						</div>
					</div>
					<div class="entries-wrap">
						<div class="entries">
							<?php echo $load_posts ? '' : $render_data['entries']; ?>
						</div>
						<div class="pagination">
							<?php echo $load_posts ? '' : $render_data['pagination']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script>
		document.addEventListener( "DOMContentLoaded", () => {
			const wait_for_listing = setInterval(() => {
				if ( typeof window.Listing === 'undefined' ) {
					return;
				}
				clearInterval( wait_for_listing );
				Listing.start( '<?php echo esc_js( $post_type ); ?>', false );
				<?php if ( $load_posts == true ) { ?>
				Listing.load();
				<?php } ?>
			}, 50 );
		});
	</script>
</div>
