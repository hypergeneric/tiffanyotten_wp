<?php

$root_node      = get_field( 'root_node', 'options' );
$main_cta  = get_field( 'main_cta', 'options' );
$current_page   = get_request_uri();

?>
<div id="primary" class="menu">
	<?php foreach ( $root_node as $menu_item ) {
		$linkage           = $menu_item['linkage'];
		$blurb             = $menu_item['blurb'];
		$sub_linkage       = $menu_item['sub_linkage'];
		$sub_nodes         = $menu_item['sub_nodes'];
		$promo_image       = $menu_item['promo_image'];
		$promo_link        = $menu_item['promo_link'];
		$active            = is_active_page( $linkage['url'], $current_page );
		if ( ! $active ) {
			if ( ! empty($sub_nodes) ) {
				foreach ( $sub_nodes as $sub_node ) {
					if ( ! empty($sub_node['linkage']) ) {
						$active = is_active_page( $sub_node['linkage']['url'], $current_page );
						if ( $active ) {
							break;
						}
					}
				}
			}
		}
	?>
	<div class="navitem menu-root-item <?php echo $active ? 'active' : ''; ?> <?php echo empty( $sub_nodes ) ? 'single-link' : ''; ?>">
		<a href="<?php echo $linkage['url']; ?>" target="<?php echo $linkage['target']; ?>"><?php echo $linkage['title']; ?></a>
		<?php if ( ! empty( $sub_nodes ) ) : ?>
		<div class="mega-wrap">
			<div class="mega <?php echo ! empty( $promo_link ) ? 'has-promo' : ''; ?>">
				<div class="mega-inner">
					<div class="details">
						<p class="tobias s_24 w_500"><?php echo $linkage['title']; ?></p>
						<p class="blurb tobias s_14"><?php echo $blurb; ?></p>
						<?php if ( $sub_linkage ) : ?>
						<a class="cta-button" href="<?php echo $sub_linkage['url']; ?>" target="<?php echo $sub_linkage['target']; ?>"><?php echo $sub_linkage['title']; ?></a>
						<?php endif; ?>
					</div>
					<div class="menu">
						<div class="menu-items">
							<?php $open = false; foreach ( $sub_nodes as $sub_node ) :
								$linkage         = $sub_node['linkage'];
								if ( ! $linkage ) {
									continue;
								}
								$active          = is_active_page( $linkage['url'], $current_page );
							?>
								<a class="navitem <?php echo $active ? 'active' : ''; ?>" href="<?php echo $linkage['url']; ?>" target="<?php echo $linkage['target']; ?>">
									<?php echo $linkage['title']; ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
					<?php if ( ! empty( $promo_link ) && ! empty( $promo_image ) ) : ?>
					<div class="promo">
						<a class="link" href="<?php echo $promo_link['url']; ?>" target="<?php echo $promo_link['target']; ?>" title="<?php echo $promo_link['title']; ?>">
							<?php echo tiffanyotten_print_img_src( $promo_image ); ?>
						</a>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php } ?>
	<div class="utility">
		<?php if( !empty($main_cta)): ?>
			<a id="header-cta" class="mobile cta demo" href="<?php echo $main_cta['url']; ?>" target="<?php echo $main_cta['target']; ?>"><?php echo $main_cta['title']; ?></a>
		<?php endif; ?>
	</div>
</div>