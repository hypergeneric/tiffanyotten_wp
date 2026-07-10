(function ($, window, document, undefined) {

Navigation = ( function() {

	function Constructor() {

		var state = 'closed';
		var interval;
		var scrollLockY = 0;
		var self = this;

		function resetHeight () {
			$(':root').css( '--megaheight', 0 );
		}

		function lockScroll () {
			scrollLockY = window.pageYOffset;
			$( 'html' ).addClass( 'noscroll' ).css( 'top', -scrollLockY + 'px' );
		}

		function unlockScroll () {
			$( 'html' ).removeClass( 'noscroll' ).css( 'top', '' );
			window.scrollTo( 0, scrollLockY );
		}

		this.open = function () {
			if ( state == 'open' ) {
				return;
			}
			state = 'open';
			$( '.navtoggle' ).addClass( 'is-active' );
			$( 'body' ).addClass( 'nav-open' );
			$( 'body' ).removeClass( 'nav-closed' );
			lockScroll();
		};

		this.close = function () {
			$( '.navtoggle' ).removeClass( 'is-active' );
			if ( state == 'open' ) {
				state = 'closed';
				$( 'body' ).removeClass( 'nav-open' );
				$( 'body' ).addClass( 'nav-closed' );
				unlockScroll();
			}
		};

		this.start = function () {

			$( '.navtoggle' ).click( function () {
				if ( state == 'open' ) {
					self.close();
				} else {
					self.open();
				}
			} );

			$( '#primary' ).on( 'click', 'a', function () {
				if ( state !== 'open' ) {
					return;
				}
				var href = $( this ).attr( 'href' ) || '';
				self.close();
				if ( href.indexOf( '#' ) === 0 ) {
					var target = document.querySelector( href );
					if ( target ) {
						target.scrollIntoView( { behavior: 'smooth' } );
					}
				}
			} );

			var menurootitem   = $( '.menu-root-item:not( .single-link ) > a' );
			menurootitem.click( function ( e ) {
				if ( $( ".navtoggle" ).is( ":visible" ) ) {
					$( this ).parent().toggleClass( 'is-active' );
					$( this ).parent().find( '.mega-wrap' ).slideToggle();
					return false;
				}
			} );
			menurootitem.hover( function() {
				clearTimeout( interval );
				var mega = $(this).next().outerHeight();
				$(':root').css( '--megaheight', mega );
			}, function() {
				interval = setTimeout( resetHeight, 300 );
			} );

		};

	}
	return new Constructor();

}() );

})(jQuery, window, document);
