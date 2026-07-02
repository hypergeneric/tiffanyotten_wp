(function ($, window, document, undefined) {

Navigation = ( function() {

	function Constructor() {

		var state = 'closed';
		var interval;

		function resetHeight () {
			$(':root').css( '--megaheight', 0 );
		}

		this.close = function () {
			$( '.navtoggle' ).removeClass( 'is-active' );
			if ( state == 'open' ) {
				state = 'closed';
				$( 'body' ).removeClass( 'nav-open' );
				$( 'body' ).addClass( 'nav-closed' );
			}
		};

		this.start = function () {

			$( '.navtoggle' ).click( function () {
				$( this ).toggleClass( 'is-active' );
				if ( state == 'open' ) {
					state = 'closed';
					$( 'body' ).removeClass( 'nav-open' );
					$( 'body' ).addClass( 'nav-closed' );
				} else {
					state = 'open';
					$( 'body' ).addClass( 'nav-open' );
					$( 'body' ).removeClass( 'nav-closed' );
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
