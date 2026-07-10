(function ($, window, document, undefined) {

	Navigation.start();
	ResponsiveBackground.start();
	window.lazyLoadInstance = new LazyLoad({});

	var scrollPos = 0;
	var ticking = false;

	function checkPageScroll() {
		var top = document.body.getBoundingClientRect().top;
		var $win = jQuery(window);
		$('body').toggleClass( 'scroll-up', top > scrollPos );
		$('body').toggleClass( 'scroll-down', top <= scrollPos );
		scrollPos = top;
		$('body').toggleClass('scrolled', jQuery(document).scrollTop() > 0);
		$('body').toggleClass('bottom', $win.height() + $win.scrollTop() == jQuery(document).height());
		ticking = false;
	}
	window.addEventListener('scroll', function() {
		if ( ! ticking ) {
			ticking = true;
			window.requestAnimationFrame( checkPageScroll );
		}
	}, { passive: true } );
	$(document).ready(checkPageScroll);
	$(window).on("load", checkPageScroll);
	checkPageScroll();

	// anchor tag scroll mechanism
	function fix_anchor_click (e) {
		e.preventDefault();
		var selector = document.querySelector(this.getAttribute('href'));
		if (selector) {
			selector.scrollIntoView({
				behavior: 'smooth'
			});
		}
	}
	if (document.querySelectorAll) {
		var anchors = document.querySelectorAll('a[href^="#"]');
		for (var i = 0; i < anchors.length; i++) {
			var anchor = anchors[i];
			anchor.addEventListener('click', fix_anchor_click);
		}
	}

})(jQuery, window, document);
