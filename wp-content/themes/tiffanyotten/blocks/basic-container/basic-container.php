<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

$allowed_blocks    = tiffanyotten_get_registered_acf_block_names();

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<InnerBlocks
		allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>"
		templateLock="false"
	/>
</section>
