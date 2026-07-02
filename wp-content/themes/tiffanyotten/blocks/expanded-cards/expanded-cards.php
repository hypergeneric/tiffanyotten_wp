<?php

$args    = isset( $args ) ? $args : null;
$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
$context = isset( $context ) && is_array( $context ) ? $context : [];
$index = 1;

list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

?>
<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>">
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if (have_rows('entries')) : ?>
				<div class="entries flex">
					<?php while (have_rows('entries')) : the_row();
						$image   = get_sub_field('image');
						$title   = get_sub_field('title');
						$blurb   = get_sub_field('blurb');
						$linkage = get_sub_field('linkage');
						$tag     = $linkage ? 'a' : 'div';
						$number  = $index;
						$letter = !empty($title) ? $title[0] : NULL;
						$index++;
					?>
						<div class="entry expand-entry<?php if($number === 1) echo ' entry--expanded'; ?>">
							<span class="entry-inner">
								<?php if ($image) { ?>
									<?php echo tiffanyotten_print_img_src($image); ?>
								<?php } ?>
								<button class="entry-button"></button>
								<div class="entry-bg-default"></div>
								<div class="entry-bg-hover"></div>
								<div class="entry-text-fade"></div>
								<div class="entry-text">
									<?php if ($title) { ?>
										<h1 class="h1"><?php echo $title; ?></h1>
									<?php } ?>
									<?php if ($blurb) { ?>
										<p class="p body-text"><?php echo $blurb; ?></p>
									<?php } ?>
									<?php if ( $linkage ) : ?>
										<span class="cta primary"><?php echo $linkage['title']; ?></span>
									<?php endif; ?>
								</div>
								<div class="entry-text-closed">
									<p class="p body-text">0<?php echo $number; ?></p>
									<?php if ($letter) { ?>
										<h3 class="h3 expand-title"><?php echo $letter; ?></h3>
									<?php } ?>
								</div>
							</span>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
	<script>
		document.addEventListener( "DOMContentLoaded", () => {
			const buttons = document.getElementsByClassName('entry-button');
			const cards = document.getElementsByClassName('expand-entry');
			for(let i = 0; i < buttons.length; i++) {
				const button = buttons[i];
				button.addEventListener('click', (e) => {
					const parentCard = e.target.parentElement.parentElement;
					if(parentCard.className.includes('expanded')) {
						return;
					}

					for(let j = 0; j < cards.length; j++) {
						cards[j].classList.remove('entry--expanded');
					}

					parentCard.classList.add('entry--expanded');
				});
			}		
		} );
	</script>
</section>