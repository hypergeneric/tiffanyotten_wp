<?php
	$args    = isset( $args ) ? $args : null;
	$block   = isset( $block ) ? $block : (isset($args['block']) ? $args['block'] : null);
	$context = isset( $context ) && is_array( $context ) ? $context : [];

	list($blockid, $blockslug) = tiffanyotten_get_block_meta($block, [], $args, $context);

	$heading             = tiffanyotten_heading_args( $args );
	$entries 				 = tiffanyotten_block_value('entries', $args);
?>

<section id="<?php echo esc_attr($blockid); ?>" class="<?php echo esc_attr($blockslug); ?>"> 
	<?php print_background_markup( $args ); ?>
	<div class="container">
		<div class="inner">

			<?php if ( $heading['eyebrow'] || $heading['title'] || $heading['blurb'] ) : ?>
				<div class="heading">
					<?php get_template_part( 'templates/_partials/heading', null, $heading ); ?>
				</div>
			<?php endif; ?>


			<div class="faqs__list">
				<?php if($entries): ?>
					<?php foreach($entries as $faq): ?>
						<div class="faqs__faq">
							<button class="faqs__faqTarget">
								<p class="p large"><?php echo esc_html($faq['title']); ?></p>
							</button>
							<div class="faqs__faqInfo">
								<?php echo $faq['body']; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

		</div>
	</div>

</section>


<script>
const faqElements = document.getElementsByClassName('faqs__faqTarget');
if(faqElements && faqElements.length) {
    // Open the first item by default
    faqElements[0].parentElement.classList.toggle('open');

    for(let i = 0; i < faqElements.length; i++) {
        faqElements[i].addEventListener('click', (e) => {
            e.target.parentElement.classList.toggle('open');
        })
    }
}
</script>
