<?php
	$post_id            = get_the_ID();
?>
<div class="meta-share">
	<div class="icons">
		<?php
			$title_raw = get_post( $post_id )->post_title;
			$link = 'http://www.facebook.com/sharer.php?u=' . get_the_permalink();
		?>
		<a class="facebook" rel="noopener noreferrer" href="<?php echo esc_url( $link ); ?>" target="_blank">
			<?php echo tiffanyotten_print_img_src( get_template_directory_uri() . '/assets/svg/brand-facebook.svg', null, null, true ); ?>
		</a>
		<?php
			$link = 'https://twitter.com/intent/tweet?url=' . get_the_permalink() . '&text=' . rawurlencode( $title_raw );
		?>
		<a class="twitter" rel="noopener noreferrer" href="<?php echo esc_url( $link ); ?>" target="_blank">
			<?php echo tiffanyotten_print_img_src( get_template_directory_uri() . '/assets/svg/brand-twitter.svg', null, null, true ); ?>
		</a>
		<?php
			$link = 'https://www.linkedin.com/shareArticle?mini=true&url=' . get_the_permalink();
		?>
		<a class="linkedin" rel="noopener noreferrer" href="<?php echo esc_url( $link ); ?>" target="_blank">
			<?php echo tiffanyotten_print_img_src( get_template_directory_uri() . '/assets/svg/brand-linkedin.svg', null, null, true ); ?>
		</a>
		<?php
			$link = 'mailto:?subject=' . rawurlencode( $title_raw ) . '&body=' . rawurlencode( get_the_permalink() );
		?>
		<a class="mail" rel="noopener noreferrer" href="<?php echo esc_url( $link ); ?>" target="_blank">
			<?php echo tiffanyotten_print_img_src( get_template_directory_uri() . '/assets/svg/mail.svg', null, null, true ); ?>
		</a>
	</div>
</div>