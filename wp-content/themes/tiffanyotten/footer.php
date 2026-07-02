<?php

	$footer_template   = get_field( 'footer_template', 'options' );

	if( have_rows('footer_object', 'options') ):
		while ( have_rows('footer_object', 'options') ) : the_row();
			if( get_row_layout() == 'image' ):
				$content_key = get_sub_field('content_key');
				$content = '<img src="' . get_sub_field('content') . '" />';
			elseif( get_row_layout() == 'wysiwyg' ): 
				$content_key = get_sub_field('content_key');
				$content = get_sub_field('content');
			endif;
			$footer_template   = str_replace( "{".$content_key."}", $content, $footer_template );
		endwhile;
	endif;

	$footer_template   = str_replace( '{YEAR}', date("Y"), $footer_template );

?>
</div>
	<?php echo $footer_template; ?>

	<?php wp_footer(); ?>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<?php tiffanyotten_print_js_components(); ?>
	
</body>
</html>


