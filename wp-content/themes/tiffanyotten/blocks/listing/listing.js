(function ($, window, document) {

	Listing = (function () {

		function Constructor() {

			let post_type;
			let use_params = true;
			let debounce;
			let currentPage = 1; // Track the current page for pagination

			// Function to fetch posts based on filters and pagination
			function get_posts() {
				const data = { page: currentPage }; // Include page in data
				const params = new URLSearchParams();

				$(".archive-listing").addClass('loading');

				$('.filter-wrapper select, #tax-search, .filter-wrapper input[type="radio"]:checked').each(function () {
					const $element = $(this);
					const name = $element.attr('name');
					if (name) {
						const type = $element.attr('type');
						let value = $element.val();
						if (type === 'checkbox') {
							value = $element.is(':checked');
						}
						data[name] = value;
					}
				});
				$('.filter-wrapper select, .filter-wrapper input[type="radio"]:checked').each(function () {
					const $element = $(this);
					const name = $element.attr('name');
					if (name) {
						let value = $element.val();
						data[name] = value;
						if (value) {
							params.set(name, value);
						} else {
							params.delete(name);
						}
					}
				});

				if ( use_params ) {

					// update the sort direction
					var sortdir = $('#sort-results').data( 'value' );
					data.sortdir = sortdir;
					params.set('sortdir', sortdir);

					// Update the URL with query parameters
					if (currentPage > 1) {
						params.set('page', currentPage); // Add current page to URL only if > 1
					} else {
						params.delete('page'); // Remove 'page' parameter if it's 1
					}
					const queryString = params.toString();
					const newUrl = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
					history.pushState({}, '', newUrl);

				}

				// AJAX request with pagination
				$.ajax({
					method: 'POST',
					url: `${ajax_object.rest_url}/${post_type}`,
					data: data,
					success: function (response) {
						$(".archive-listing").removeClass('loading');
						$(".archive-listing .results span ").html(response.total); // Use response structure
						$(".archive-listing .entries").html(response.entries); // Use response structure
						$(".archive-listing .pagination").html(response.pagination); // Update pagination links
						window.lazyLoadInstance.update();
					}
				});
			}

			// Function to reset pagination and trigger get_posts on filter change
			function applyFilters() {
				currentPage = 1; // Reset to the first page
				get_posts();
			}

			// Function to sync filters based on URL parameters without resetting pagination
			function sync_filters_from_url() {
				const params = new URLSearchParams(window.location.search);
				currentPage = params.get('page') ? parseInt(params.get('page')) : 1; // Update currentPage from URL
				$('.filter-wrapper select, .filter-wrapper input').each(function () {
					const $element = $(this);
					const name = $element.attr('name');
					if (name) {
						if ($element.attr('type') === 'checkbox' || $element.attr('type') === 'radio') {
							$element.prop('checked', params.get(name) === 'true');
						} else {
							$element.val(params.get(name)).trigger('change.select2');
						}
					}
				});
				const sortdir = params.get('sortdir') ? params.get('sortdir') : 'desc';
				if ( sortdir == 'desc' || sortdir == 'asc' ) {
					$('#sort-results').data( 'value', $( this ).data( sortdir + 'Value' ) );
					$('#sort-results').attr( 'data-value', $( this ).data( sortdir + 'Value' ) );
					$('#sort-results').find( 'span' ).text( $( this ).data( sortdir + 'Label' ) );
				}
				get_posts(); // Trigger the post update based on the current URL state
			}

			// Tooltip hover/click functionality
			function hover_toggle(e) {
				const $this = $(this);
				const title = $this.attr('title');
				const type = e.type;
				const offset = $this.offset();
				const xOffset = e.pageX - offset.left;
				const yOffset = e.pageY - offset.top;
				if (type === 'mouseenter' || (type === 'click' && !$this.find('.title').length)) {
					$this.data('tipText', title).removeAttr('title');
					$this.append(`<span class="title">${title}</span>`);
					$this.find('.title').css({ top: yOffset + 'px', left: xOffset + 'px' });
				} else if (type === 'mouseleave' || (type === 'click' && $this.find('.title').length)) {
					$this.attr('title', $this.data('tipText'));
					$this.find('.title').remove();
				} else if (type === 'mousemove') {
					$this.find('.title').css({ top: yOffset + 'px', left: xOffset + 'px' });
				}
			}

			// Function to handle pagination link clicks
			function handlePaginationClick(e) {
				e.preventDefault();
				const page = $(this).attr('href').match(/page=(\d+)/); // Extract page number from URL
				currentPage = page ? parseInt(page[1]) : 1; // Update the current page
				get_posts(); // Fetch posts for the selected page
				document.getElementById( 'listing' ).scrollIntoView( {
					behavior: 'smooth'
				} );
			}

			// Start function to initialize the listing and filters
			this.start = function ( str, bool ) {
				post_type = str;
				use_params = (typeof bool === 'undefined') ? true : bool;

				// tooltips
				$(".tooltip:not([title=''])").on({
					click: hover_toggle,
					mouseenter: hover_toggle,
					mouseleave: hover_toggle,
					mousemove: hover_toggle
				});

				// Initialize select2 with placeholder and clear option
				$('select.select').prepend('<option selected=""></option>').select2({
					placeholder: "Choose",
					allowClear: true,
					width: '100%'
				}).each(function () {
					// Trigger a change to set select2 to recognize the pre-selected option if any
					$(this).val($(this).find('option[selected="selected"]').val()).trigger('change.select2');
				}).on("select2:clear", function () {
					applyFilters();
					$(this).on("select2:opening.cancelOpen", function (evt) {
						evt.preventDefault();
						$(this).off("select2:opening.cancelOpen");
					});
				});

				// Event listeners for filters
				$('.filter-wrapper select').on('select2:select', applyFilters);
				$('.filter-wrapper input:not(#tax-search)').on('change', applyFilters);

				// Debounced search input
				$('#tax-search').on('input', function () {
					clearTimeout(debounce);
					debounce = setTimeout(applyFilters, 1000);
				});

				// Clear filters button
				$('#sort-results').on('click', function () {
					var state = $( this ).data( 'value' );
					if ( state == 'desc' ) {
						$( this ).data( 'value', $( this ).data( 'ascValue' ) );
						$( this ).attr( 'data-value', $( this ).data( 'ascValue' ) );
						$( this ).find( 'span' ).text( $( this ).data( 'ascLabel' ) );
					} else {
						$( this ).data( 'value', $( this ).data( 'descValue' ) );
						$( this ).attr( 'data-value', $( this ).data( 'descValue' ) );
						$( this ).find( 'span' ).text( $( this ).data( 'descLabel' ) );
					}
					applyFilters();
				});

				// Clear filters button
				$('#filter-clear').on('click', function () {
					$('.filter-wrapper select').val(null).trigger('change');
					$(".filter-wrapper input").prop('checked', false).val('');
					$('#sort-results').data( 'value', $('#sort-results').data( 'descValue' ) );
					$('#sort-results').attr( 'data-value', $('#sort-results').data( 'descValue' ) );
					$('#sort-results').find( 'span' ).text( $('#sort-results').data( 'descLabel' ) );
					applyFilters();
				});

				// Toggle filter panel
				$('#filter-toggle').on('click', function () {
					$( this ).toggleClass( 'active' );
					$( this ).next().slideToggle( function(){ 
						var state = $( this ).is(':hidden') ? 'hidden' : 'visible';
					});
				});
				if ( $(window).width() < 767 ) {
					$('#filter-toggle').toggleClass( 'active' );
					$('#filter-toggle').next().slideToggle( 0 );
				}

				if ( use_params ) {
					// Pagination click event
					const initialParams = new URLSearchParams(window.location.search);
					currentPage = initialParams.get('page') ? parseInt(initialParams.get('page')) : 1;
					$(document).on('click', '.pagination a', handlePaginationClick);

					// Listen for popstate event to handle back/forward browser navigation
					window.addEventListener('popstate', sync_filters_from_url);
				}
			};

			this.load = function () {
				get_posts();
			};
		}

		return new Constructor();

	})();

})(jQuery, window, document);
