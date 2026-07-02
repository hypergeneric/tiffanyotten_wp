<?php

	$login_cta = get_field( 'login_cta', 'options' );
	$login_cta_2 = get_field( 'login_cta_2', 'options' );
	$login_cta_title = get_field( 'login_cta_title', 'options' );
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
					<?php if( !empty($login_cta) || !empty($login_cta_2)): ?>
						<div class="header-login">
							<button id="header-login-target" class="cta cta-alt">
								<?php if(!empty($login_cta_title)) {
									echo $login_cta_title;
								} else {
									echo "Client login";
								} ?>
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="8" viewBox="0 0 14 8" fill="none">
									<path d="M13.5675 1.06754L7.31754 7.31754C7.25949 7.37565 7.19056 7.42175 7.11469 7.4532C7.03881 7.48465 6.95748 7.50084 6.87535 7.50084C6.79321 7.50084 6.71188 7.48465 6.63601 7.4532C6.56014 7.42175 6.49121 7.37565 6.43316 7.31754L0.18316 1.06754C0.0658846 0.95026 0 0.7912 0 0.625347C0 0.459495 0.0658846 0.300435 0.18316 0.18316C0.300435 0.0658843 0.459495 0 0.625347 0C0.7912 0 0.95026 0.0658843 1.06753 0.18316L6.87535 5.99175L12.6832 0.18316C12.7412 0.125091 12.8102 0.0790281 12.886 0.0476015C12.9619 0.0161748 13.0432 0 13.1253 0C13.2075 0 13.2888 0.0161748 13.3647 0.0476015C13.4405 0.0790281 13.5095 0.125091 13.5675 0.18316C13.6256 0.241229 13.6717 0.310167 13.7031 0.386037C13.7345 0.461908 13.7507 0.543226 13.7507 0.625347C13.7507 0.707469 13.7345 0.788787 13.7031 0.864658C13.6717 0.940528 13.6256 1.00947 13.5675 1.06754Z" fill="white" fill-opacity="0.8"/>
								</svg>
							</button>
							<div class="header-login-options hidden">
								<?php if( !empty($login_cta)): ?>
									<a id="login-cta" class="desktop cta cta-alt login" href="<?php echo $login_cta['url']; ?>" target="<?php echo $login_cta['target']; ?>"><?php echo $login_cta['title']; ?></a>
								<?php endif; ?>
								<?php if( !empty($login_cta_2)): ?>
									<a id="login-cta" class="desktop cta cta-alt login" href="<?php echo $login_cta_2['url']; ?>" target="<?php echo $login_cta_2['target']; ?>"><?php echo $login_cta_2['title']; ?></a>
								<?php endif; ?>
							</div>

						</div>
					<?php endif; ?>
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
		<script>
			document.addEventListener( "DOMContentLoaded", () => {
				var navLoginTarget = document.querySelector("#header-login-target");
				var navLoginOptions = document.querySelector(".header-login-options")
				navLoginTarget.addEventListener('click', () => {
					navLoginOptions.classList.toggle('hidden');
					navLoginTarget.classList.toggle('open');
				})

				const targetElement = document.querySelector('.header');
				document.addEventListener('click', (event) => {
					if (!targetElement.contains(event.target) && !navLoginOptions.className.includes('hidden')) {
						navLoginOptions.classList.add('hidden');
						navLoginTarget.classList.remove('open');
					}
				});
			} );
		</script>
	</header>

	<div class="main">