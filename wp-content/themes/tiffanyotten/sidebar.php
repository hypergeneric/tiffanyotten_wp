<?php if ( is_active_sidebar( get_post_type() ) ) : ?>
	<aside id="sidebar" class="widget-area">
		<?php dynamic_sidebar( get_post_type() ); ?>
	</aside>
<?php endif; ?>