(function ($, window, document, undefined) {

FormPopup = ( function () {

	function Constructor() {

		var dialog = null;
		var lastFocus = null;
		var scrollLockY = 0;
		var hash = '#form-popup';

		function normalizeHash( value ) {
			if ( ! value || typeof value !== 'string' ) {
				return '#form-popup';
			}
			value = value.trim();
			if ( ! value ) {
				return '#form-popup';
			}
			if ( value.charAt( 0 ) !== '#' ) {
				value = '#' + value;
			}
			return value;
		}

		function resolveHash() {
			if ( dialog && dialog.getAttribute( 'data-form-hash' ) ) {
				return normalizeHash( dialog.getAttribute( 'data-form-hash' ) );
			}
			if ( typeof window.tiffanyottenFormPopup !== 'undefined' && window.tiffanyottenFormPopup.hash ) {
				return normalizeHash( window.tiffanyottenFormPopup.hash );
			}
			return '#form-popup';
		}

		function isFormTrigger( href ) {
			if ( ! href ) {
				return false;
			}
			var current = resolveHash();
			return href === current || href.indexOf( current + '?' ) === 0 || href.indexOf( current + '&' ) === 0;
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
			if ( ! dialog || typeof dialog.showModal !== 'function' ) {
				return;
			}
			if ( dialog.open ) {
				return;
			}
			lastFocus = document.activeElement;
			lockScroll();
			dialog.showModal();
			var closeBtn = dialog.querySelector( '.form-popup__close' );
			if ( closeBtn ) {
				closeBtn.focus();
			}
		};

		this.close = function () {
			if ( ! dialog || ! dialog.open ) {
				return;
			}
			dialog.close();
		};

		this.start = function () {
			dialog = document.getElementById( 'form-popup' );
			if ( ! dialog ) {
				return;
			}

			var self = this;
			hash = resolveHash();

			dialog.addEventListener( 'close', function () {
				unlockScroll();
				if ( window.location.hash === hash ) {
					history.replaceState( null, '', window.location.pathname + window.location.search );
				}
				if ( lastFocus && typeof lastFocus.focus === 'function' ) {
					lastFocus.focus();
				}
			} );

			dialog.addEventListener( 'click', function ( e ) {
				if ( e.target === dialog ) {
					self.close();
				}
			} );

			$( document ).on( 'click', 'a[href^="#"], .js-form-popup', function ( e ) {
				var href = $( this ).attr( 'href' );
				var isClassTrigger = $( this ).hasClass( 'js-form-popup' );
				if ( ! isClassTrigger && ! isFormTrigger( href ) ) {
					return;
				}
				e.preventDefault();
				self.open();
			} );

			$( dialog ).on( 'click', '.form-popup__close', function ( e ) {
				e.preventDefault();
				self.close();
			} );

			if ( window.location.hash === hash ) {
				self.open();
			}

			window.addEventListener( 'hashchange', function () {
				hash = resolveHash();
				if ( window.location.hash === hash ) {
					self.open();
				}
			} );
		};

		this.isFormHash = function ( href ) {
			return isFormTrigger( href );
		};

		this.getHash = function () {
			return resolveHash();
		};

	}

	return new Constructor();

}() );

})(jQuery, window, document);
