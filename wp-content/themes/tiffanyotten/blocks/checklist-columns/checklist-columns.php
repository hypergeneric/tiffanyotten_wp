<?php

/**
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

$checklist_title = tiffanyotten_block_value('checklist_title', $args);
$option_1_title = tiffanyotten_block_value('option_1_title', $args);
$option_2_title = tiffanyotten_block_value('option_2_title', $args);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">
			<div class="heading">
				<?php if($checklist_title): ?>
					<h5 class="h5 checklist-title"><?php echo $checklist_title; ?></h5>
				<?php endif; ?>
				<?php if($option_1_title): ?>
					<h5 class="h5 option-title"><?php echo $option_1_title; ?></h5>
				<?php endif; ?>
				<?php if($option_2_title): ?>
					<h5 class="h5 option-title"><?php echo $option_2_title; ?></h5>
				<?php endif; ?>
			</div>
			<?php if (have_rows('entries')) : ?>
				<div class="entries flex">
					<?php while (have_rows('entries')) : the_row();
						$title                     = get_sub_field('title');
						$option_1_response         = get_sub_field('option_1_response');
						$option_1_custom_response  = get_sub_field('option_1_custom_response');
						$option_2_response         = get_sub_field('option_2_response');
						$option_2_custom_response  = get_sub_field('option_2_custom_response');
					?>
						<div class="entry flex flex-v">
							<span class="entry-inner">
								<div class="title">
									<?php if($title): ?>
										<h5 class="h5"><?php echo $title; ?></h5>
									<?php endif; ?>
								</div>
								<div class="option">
									<span class="<?php echo $option_1_response; ?>">
										<?php if($option_1_response === 'custom' && $option_1_custom_response): ?>
											<p class="p"><?php echo $option_1_custom_response; ?></p>
										<?php endif; ?>
									</span>
								</div>
								<div class="option">
									<span class="<?php echo $option_2_response; ?>">
										<?php if($option_2_response === 'custom' && $option_2_custom_response): ?>
											<p class="p"><?php echo $option_2_custom_response; ?></p>
										<?php endif; ?>
									</span>
								</div>
							</span>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
