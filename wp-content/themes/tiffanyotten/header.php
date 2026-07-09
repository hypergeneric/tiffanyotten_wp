<?php

	$main_cta  = get_field( 'main_cta', 'options' );
	$theme     = tiffanyotten_header_theme();

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1">

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

	<link href="//ajax.googleapis.com" rel="dns-prefetch">

	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

	<header id="top" class="header <?php echo $theme; ?>">
		<div class="container">
			<div class="inner">
				<a class="logo" href="/" aria-label="Go Home"></a>
				<?php get_template_part( 'templates/_partials/main-menu' ); ?>
				<div class="btn-group">
					<?php if( !empty($main_cta)): ?>
						<a id="header-cta" class="desktop cta demo" href="<?php echo $main_cta['url']; ?>" target="<?php echo $main_cta['target']; ?>"><?php echo $main_cta['title']; ?></a>
					<?php endif; ?>
					<button class="navtoggle hamburger hamburger--collapse" type="button">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>
				</div>
			</div>
		</div>
	</header>

	<div class="main">