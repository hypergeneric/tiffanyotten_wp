(function ($, window, document, undefined) {

CardFlip = ( function() {

	function Constructor() {

		var self = this;
		var bound = false;

		function isInteractive( el, card ) {
			var node = el;
			while ( node && node !== card ) {
				var tag = node.tagName;
				if ( tag === 'A' || tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA' ) {
					return true;
				}
				if ( tag === 'BUTTON' && ! node.hasAttribute( 'data-flip' ) ) {
					return true;
				}
				node = node.parentNode;
			}
			return false;
		}

		function setState( card, flipped ) {
			$( card ).toggleClass( 'is-flipped', flipped );
			var target = card.querySelector( flipped ? '[data-flip="close"]' : '[data-flip="open"]' );
			if ( target && document.activeElement && card.contains( document.activeElement ) ) {
				target.focus();
			}
		}

		function toggle( card ) {
			setState( card, ! $( card ).hasClass( 'is-flipped' ) );
		}

		function closeAll( except ) {
			$( '.entry.has-flip.is-flipped' ).each( function () {
				if ( this !== except ) {
					$( this ).removeClass( 'is-flipped' );
				}
			} );
		}

		self.start = function () {
			if ( bound || ! document.querySelector( '.entry.has-flip' ) ) {
				return;
			}
			bound = true;

			$( document ).on( 'click', '.entry.has-flip', function ( e ) {
				if ( isInteractive( e.target, this ) ) {
					return;
				}
				e.preventDefault();
				closeAll( this );
				toggle( this );
			} );

			$( document ).on( 'keydown', function ( e ) {
				if ( e.key !== 'Escape' && e.keyCode !== 27 ) {
					return;
				}
				var open = document.querySelector( '.entry.has-flip.is-flipped' );
				if ( ! open ) {
					return;
				}
				setState( open, false );
			} );
		};

	}

	return new Constructor();

} )();

})(jQuery, window, document);
