(function ($, window, document, undefined) {

	Navigation.start();
	ResponsiveBackground.start();
	window.lazyLoadInstance = new LazyLoad({});

	var scrollPos = 0;
	window.addEventListener('scroll', function() {
		if ( ( document.body.getBoundingClientRect() ).top > scrollPos) {
			$('body').removeClass( "scroll-down" );
			$('body').addClass( "scroll-up" );
		} else {
			$('body').addClass( "scroll-down" );
			$('body').removeClass( "scroll-up" );
		}	
		scrollPos = ( document.body.getBoundingClientRect() ).top;
	} );

	// listen for scroll events
	function checkPageScroll() {
		var $win = jQuery(window);
		$('body').toggleClass('scrolled', jQuery(document).scrollTop() > 0);
		$('body').toggleClass('bottom', $win.height() + $win.scrollTop() == jQuery(document).height());
	}
	$(document).scroll(checkPageScroll);
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
