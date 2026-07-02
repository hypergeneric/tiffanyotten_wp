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
						$number  = $index;
						$letter = !empty($title) ? $title[0] : NULL;
						$index++;
					?>
						<div class="entry">
							<span class="entry-inner">
								<?php if ($image) { ?>
									<?php echo tiffanyotten_print_img_src($image); ?>
								<?php } ?>
								<div class="entry-left">
									<p class="p body-text card-number">0<?php echo $number; ?></p>
									<?php if ($letter) { ?>
										<h1 class="h1 expand-title"><?php echo $letter; ?></h1>
									<?php } ?>
								</div>
								<div class="entry-text">
									<?php if ($title) { ?>
										<h3 class="h3 full-width-card-title"><?php echo $title; ?></h3>
									<?php } ?>
									<?php if ($blurb) { ?>
										<?php echo $blurb; ?>
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
			const cardTitles = document.getElementsByClassName('full-width-card-title');
			for(let i = 0; i < cardTitles.length; i++) {
				const allWords = cardTitles[i].innerText.split(' ')
				if(allWords) {
					const firstWord = allWords.shift();
					const remainingSentence = allWords.join(' ');
					const newHtml = '<span class="highlight-title">' + firstWord + '</span> ' + remainingSentence;
					console.log(newHtml)
					cardTitles[i].innerHTML = newHtml;
					console.log(cardTitles[i].innerHTML)
				}
			}
		} );
	</script>
</section>