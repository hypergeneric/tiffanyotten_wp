<?php

	$base_term  = get_post_type_object( get_post_type() )->labels->name;
	switch ( get_post_type() ) {
		case 'post':
			$base_term = 'Blogs';
			break;
	}

?>
<div class="breadcrumb">
	<a href="<?php echo get_post_type_archive_link( get_post_type() ); ?>"><span></span>Back to <?php echo $base_term; ?></a>
</div>
