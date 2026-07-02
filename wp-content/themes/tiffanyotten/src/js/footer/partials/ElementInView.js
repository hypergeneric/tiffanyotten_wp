ElementInView = ( function () {

	function Constructor() {
		/*
		params = {
			elements: [],
			options: {},
			cb: function( el )
		}
		*/ 
		this.start = function ( params ) {

			let elementOptions = {
				rootMargin: params.options.rootMargin,
				threshold: params.options.threshold
			};

			const initializeElement = ( element, observer ) => {
				if ( element[0].isIntersecting ) {
					params.cb( element[0].target );
					elementObserver.unobserve( element[0].target );
				}
			};

			let elementObserver = new IntersectionObserver( initializeElement, elementOptions );
			params.elements.forEach(element => {
				elementObserver.observe( element );
			});

		};

	}

	return new Constructor();

}());

