/**
 * Send an action over AJAX. A wrapper around jQuery.ajax. In future, all consumers can be reviewed to simplify some of the options, where there is historical cruft.
 *
 * @param {string}   action   - the action to send
 * @param {*}        data     - data to send
 * @param {Function} callback - will be called with the results
 * @param {object}   options  -further options. Relevant properties include:
 * - [json_parse=true] - whether to JSON parse the results
 * - [alert_on_error=true] - whether to show an alert box if there was a problem (otherwise, suppress it)
 * - [action='aios_ajax'] - what to send as the action parameter on the AJAX request (N.B. action parameter to this function goes as the 'subaction' parameter on the AJAX request)
 * - [nonce=aios_ajax_nonce] - the nonce value to send.
 * - [nonce_key='nonce'] - the key value for the nonce field
 * - [timeout=null] - set a timeout after this number of seconds (or if null, none is set)
 * - [async=true] - control whether the request is asynchronous (almost always wanted) or blocking (would need to have a specific reason)
 * - [type='POST'] - GET or POST
 */
function aios_send_command(action, data, callback, options) {

	var default_options = {
		json_parse: true,
		alert_on_error: true,
		action: 'aios_ajax',
		nonce: aios_data.ajax_nonce,
		nonce_key: 'nonce',
		timeout: null,
		async: true,
		type: 'POST'
	};

	if ('undefined' === typeof options) options = {};

	for (var opt in default_options) {
		if (!options.hasOwnProperty(opt)) { options[opt] = default_options[opt]; }
	}

	var ajax_data = {
		action: options.action,
		subaction: action
	};

	ajax_data[options.nonce_key] = options.nonce;
	ajax_data.data = data;

	var ajax_opts = {
		type: options.type,
		url: ajaxurl,
		data: ajax_data,
		success: function(response, status) {
			if (options.json_parse) {
				try {
					var resp = aios_parse_json(response);
				} catch (e) {
					if ('function' == typeof options.error_callback) {
						return options.error_callback(response, e, 502, resp);
					} else {
						console.log(e);
						console.log(response);
						if (options.alert_on_error) { alert(aios_trans.unexpected_response+' '+response); }
						return;
					}
				}
				if (resp.hasOwnProperty('fatal_error')) {
					if ('function' == typeof options.error_callback) {
						// 500 is internal server error code
						return options.error_callback(response, status, 500, resp);
					} else {
						console.error(resp.fatal_error_message);
						if (options.alert_on_error) { alert(resp.fatal_error_message); }
						return false;
					}
				}
				if ('function' == typeof callback) callback(resp, status, response);
			} else {
				if ('function' == typeof callback) callback(response, status);
			}
		},
		error: function(response, status, error_code) {
			if ('function' == typeof options.error_callback) {
				options.error_callback(response, status, error_code);
			} else {
				console.log("aios_send_command: error: "+status+" ("+error_code+")");
				console.log(response);
			}
		},
		dataType: 'text',
		async: options.async
	};

	if (null != options.timeout) { ajax_opts.timeout = options.timeout; }

	jQuery.ajax(ajax_opts);

}

/**
 * Parse JSON string, including automatically detecting unwanted extra input and skipping it
 *
 * @param {string}  json_mix_str - JSON string which need to parse and convert to object
 * @param {boolean} analyse		 - if true, then the return format will contain information on the parsing, and parsing will skip attempting to JSON.parse() the entire string (will begin with trying to locate the actual JSON)
 *
 * @throws SyntaxError|String (including passing on what JSON.parse may throw) if a parsing error occurs.
 *
 * @returns Mixed parsed JSON object. Will only return if parsing is successful (otherwise, will throw). If analyse is true, then will rather return an object with properties (mixed)parsed, (integer)json_start_pos and (integer)json_end_pos
 */
function aios_parse_json(json_mix_str, analyse) {

	analyse = ('undefined' === typeof analyse) ? false : true;

	// Just try it - i.e. the 'default' case where things work (which can include extra whitespace/line-feeds, and simple strings, etc.).
	if (!analyse) {
		try {
			var result = JSON.parse(json_mix_str);
			return result;
		} catch (e) {
			console.log('AIOS: Exception when trying to parse JSON (1) - will attempt to fix/re-parse based upon first/last curly brackets');
			console.log(json_mix_str);
		}
	}

	var json_start_pos = json_mix_str.indexOf('{');
	var json_last_pos = json_mix_str.lastIndexOf('}');

	// Case where some php notice may be added after or before json string
	if (json_start_pos > -1 && json_last_pos > -1) {
		var json_str = json_mix_str.slice(json_start_pos, json_last_pos + 1);
		try {
			var parsed = JSON.parse(json_str);
			if (!analyse) { console.log('AIOS: JSON re-parse successful'); }
			return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: json_last_pos + 1 } : parsed;
		} catch (e) {
			console.log('AIOS: Exception when trying to parse JSON (2) - will attempt to fix/re-parse based upon bracket counting');

			var cursor = json_start_pos;
			var open_count = 0;
			var last_character = '';
			var inside_string = false;

			// Don't mistake this for a real JSON parser. Its aim is to improve the odds in real-world cases seen, not to arrive at universal perfection.
			while ((open_count > 0 || cursor == json_start_pos) && cursor <= json_last_pos) {

				var current_character = json_mix_str.charAt(cursor);

				if (!inside_string && '{' == current_character) {
					open_count++;
				} else if (!inside_string && '}' == current_character) {
					open_count--;
				} else if ('"' == current_character && '\\' != last_character) {
					inside_string = inside_string ? false : true;
				}

				last_character = current_character;
				cursor++;
			}
			console.log("Started at cursor="+json_start_pos+", ended at cursor="+cursor+" with result following:");
			console.log(json_mix_str.substring(json_start_pos, cursor));

			try {
				var parsed = JSON.parse(json_mix_str.substring(json_start_pos, cursor));
				console.log('AIOS: JSON re-parse successful');
				return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: cursor } : parsed;
			} catch (e) {
				// Throw it again, so that our function works just like JSON.parse() in its behaviour.
				throw e;
			}
		}
	}

	throw "AIOS: could not parse the JSON";

}

/**
 * Updates the content of an HTML element identified by its ID with the provided badge text.
 *
 * @param {Array} badges - An array of objects representing badges to update.
 * @param {string} badges.id - The ID of the HTML element to update.
 * @param {string} badges.html - The HTML content to set for the element.
 * @returns {void}
 */
function aios_update_badge(badges) {
	badges.forEach(function(badge) {
		aios_update_content(badge.id, badge.html);
	});
}

/**
 * Update the content of an element with the specified HTML.
 *
 * @param {string} id - The ID of the element to update.
 * @param {string} html - The HTML content to set for the element.
 * @returns {void}
 */
function aios_update_content(id, html) {
	jQuery(id).html(html);
}

/**
 * Escapes a string for safe HTML output.
 *
 * @param {string} str - The string to escape.
 *
 * @returns {string} The escaped HTML string.
 */
function aios_escape_html(str) {
	const div = document.createElement('div');
	div.textContent = str;
	return div.innerHTML;
}

/**
 * Function to block the UI and display a loading message.
 * Uses jQuery blockUI plugin.
 *
 * @param {string} message - A string to be shown when function is called
 *
 * @returns {void}
 */
function aios_block_ui(message = aios_trans.saving) {
	jQuery.blockUI({
		css: {
			width: '500px',
			border: 'none',
			'border-radius': '10px',
			left: 'calc(50% - 250px)',
			top: 'calc(50% - 150px)',
			padding: '20px'
		},
		message: '<div style="margin: 8px; font-size:150%;" class="aios_saving_popup"><img src="' + aios_trans.logo + '" height="80" width="80" style="padding-bottom:10px;"><br>' + message + '</div>'
	});
}

/**
 * Display a success modal with optional message and icon.
 *
 * @param {Object|string} args - Configuration object or message string.
 * @param {boolean} close_popup - Optional. If true, the popup will close automatically after 2 seconds. Default is true.
 *
 * @returns {void}
 */
function aios_show_success_modal(args, close_popup = true) {
	if ('string' == typeof args) {
		args = {
			message: args
		};
	}
	var data = jQuery.extend(
		{
			icon: 'yes',
			close: aios_trans.close,
			message: '',
			classes: 'success'
		},
		args
	);

	var closeButtonHTML = '';
	if (!close_popup) {
		closeButtonHTML = '<button class="button aios-close-overlay"><span class="dashicons dashicons-no-alt"></span>' + data.close + '</button>';
	}

	jQuery.blockUI({
		css: {
			width: '500px',
			border: 'none',
			'border-radius': '10px',
			left: 'calc(50% - 250px)',
			top: 'calc(50% - 150px)',
			cursor: 'default'
		},
		onOverlayClick: jQuery.unblockUI,
		message: '<div class="aios_success_popup ' + data.classes + '"><span class="dashicons dashicons-' + data.icon + '"></span><div class="aios_success_popup--message">' + data.message + '</div>' + closeButtonHTML + '</div>'
	});

	// click the show more for AJAX info
	jQuery('.aios_success_popup').on('click', '#aios_ajax_showmoreoptions', function(e) {
		e.preventDefault();
		let more_options = jQuery('#aios_ajax_moreoptions');
		more_options.toggle();
		// Toggle text between "Show more" and "Hide"
		let new_text = more_options.is(':visible') ? aios_trans.hide_info : aios_trans.show_info;
		jQuery(this).text(new_text);
	});

	// close success popup
	jQuery('.blockUI .aios-close-overlay').on('click', function() {
		jQuery.unblockUI();
	});

	if (close_popup) {
		setTimeout(function() {
			jQuery.unblockUI();
		}, 2000);
	}
}

/**
 * Submits a form using AJAX and handles the response.
 *
 * @param {jQuery} form - The jQuery object representing the form element.
 * @param {string} action - The action to perform when submitting the form.
 * @param {boolean|Object} [use_data=true] - Indicates whether to include form data in the AJAX request.
 * @param {string} [block_ui_message="Saving..."] - The message to display while blocking UI during AJAX request.
 * @param {Function} [pre_ajax_callback] - Optional callback function to execute before the AJAX request.
 * @param {Function} [post_ajax_callback] - Optional callback function to execute after the AJAX request.
 * @param {Function} [error_callback] - Optional callback function to execute if an error occurs during the AJAX request.
 */
function aios_submit_form(form, action, use_data = true, block_ui_message = aios_trans.saving, pre_ajax_callback, post_ajax_callback, error_callback) {
	aios_block_ui(block_ui_message);
	var submitButton = form.find(':submit');
	submitButton.prop('disabled', true);
	var data = {};

	if ('function' === typeof pre_ajax_callback) {
		pre_ajax_callback();
	}

	if ('object' === typeof use_data) {
		data = use_data; // Use custom data object
	} else if (use_data) {
		var dataArray = form.serializeArray();
		var dataLength = dataArray.length;
		for (var i = 0; i < dataLength; i++) {
			data[dataArray[i].name] = dataArray[i].value;
		}
	}

	if (typeof error_callback !== 'function') {
		error_callback = function() {
			jQuery.unblockUI();
		};
	} else {
		// Save reference to the original callback
		var original_error_callback = error_callback;

		error_callback = function() {
			original_error_callback();
			jQuery.unblockUI();
		};
	}

	let options = {
		error_callback: error_callback
	}

	aios_send_command(action, data, function(response) {
		aios_handle_ajax_update(response, post_ajax_callback);
		submitButton.prop('disabled', false)
	}, options);
}

/**
 * Handle AJAX response and update UI elements accordingly.
 * If a callback function is provided, it will be executed.
 *
 * @param {Object} response - The AJAX response object.
 * @param {Function} [callback] - Optional callback function to execute.
 *
 * @returns {void}
 */
function aios_handle_ajax_update(response, callback) {

	// update contents on the page
	if (response.hasOwnProperty('content')) {
		jQuery.each(response.content, function(key, value) {
			aios_update_content('#' + key, value);
		});
	}

	// update fields with new values if changed
	if (response.hasOwnProperty('values')) {
		jQuery.each(response.values, function(key, value) {
			jQuery('#' + key).val(value);
		});
	}

	// update badges
	if (response.hasOwnProperty('badges')) {
		aios_update_badge(response.badges);
	}

	if ('function' === typeof callback) {
		callback(response);
	}

	aios_show_ajax_response_message(response);
}

/**
 * Displays an AJAX response message in a modal or unblocks the UI.
 *
 * This function processes the response from an AJAX request and displays
 * the message or info in a modal. If there is an error in the response,
 * it shows a warning modal. If there are additional informational messages,
 * it provides a toggle to display them. If no messages are present, it unblocks the UI.
 *
 * @param {Object} response - The response object returned from the AJAX call.
 * @param {string} [response.message] - The main message to be displayed in the modal.
 * @param {string} [response.status] - The status of the response ('error' indicates a warning).
 * @param {Array<string>} [response.info] - Additional information messages to display.
 */
function aios_show_ajax_response_message(response) {
	var update_message = (response.hasOwnProperty('message') && response.message.length > 0) || (response.hasOwnProperty('info') && response.info.length > 0);

	if (update_message) {
		var messageContainer = jQuery('<div></div>');
		var close_popup = true;

		// display single message
		if (response.hasOwnProperty('message')) {
			messageContainer.append(response.message);
			messageContainer.append('<br>');
		}
		
		if (response.hasOwnProperty('extra_args') && response.extra_args.hasOwnProperty('link')) {
			var link = response.extra_args.link;
			// Only accept the structured {url, text} shape, and only http(s) URLs.
			if (link && 'string' === typeof link.url && /^https?:\/\//i.test(link.url)) {
				var link_anchor = jQuery('<a>', {target: '_blank', rel: 'noopener noreferrer'})
					.attr('href', link.url)
					.text('string' === typeof link.text && link.text.length > 0 ? link.text : link.url);
				messageContainer.append(link_anchor);
				messageContainer.append('<br>');
			}
		}

		if (response.hasOwnProperty('info') && response.info.length > 0) {
			close_popup = false;
			// info toggle
			let toggle = jQuery('<span>' + aios_trans.show_notices + ' (<a href="#" id="aios_ajax_showmoreoptions">' + aios_trans.show_info + '</a>)</span>');
			toggle.appendTo(messageContainer);


			let infoContainer = jQuery('<div id="aios_ajax_moreoptions" class="aiowps_more_info_body" style="display:none;"></div>');
			response.info.forEach(function(info) {
				infoContainer.append(`<span class="aios-modal-info">${info}</span>`, '<br>');
			});

			infoContainer.appendTo(messageContainer);
		}


		if ('error' === response.status) {
			aios_show_success_modal({
				message: messageContainer.html(),
				icon: 'no-alt',
				classes: 'warning'
			}, false);
		} else {
			aios_show_success_modal(messageContainer.html(), close_popup);
		}
	} else {
		setTimeout(function() {
			jQuery.unblockUI();
		}, 1500);
	}
}

// Start of list table handling
/**
 * Attaches event handlers to specified table elements for detecting and performing actions,
 * then updates the table based on the current state or the clicked element.
 *
 * @param {string} id - The ID selector of the table element (e.g., '#my-table').
 * @param {string} elements - The elements inside the table to attach event handlers to (e.g., 'input, select').
 * @param {string} action_selector - The selector for the bulk action dropdown element (e.g., '#bulk-action-selector-top').
 * @param {string} tab - The tab or section to update after performing an action (e.g., 'posts', 'comments').
 * @param {string} [filter_event_selector=''] - The selector for filtering by event type (optional).
 * @param {string} [search_selector=''] - The selector for the search input element (optional).
 * @param {string} [filter_level_selector=''] - The selector for filtering by level (optional).
 * @param {string} [filter_reason_selector=''] - The selector for filtering by reason (optional).
 * @param {boolean} [detect=true] - Whether to detect actions based on the clicked element or to perform an action immediately.
 *
 * @returns {void}
 */
function aios_detect_table_action(id, elements, action_selector, tab, filter_event_selector = '', search_selector = '', filter_level_selector = '', filter_reason_selector = '', detect = true) {

	if (true === detect) {
		jQuery(id).on('click change', elements, function(e) {
			if (jQuery(this).is('.aios-per-page') && e.type === 'click') {
				return;
			}
			// Check if the event's default action is prevented by the confirm message cancel
			if (!e.isDefaultPrevented()) {
				e.preventDefault();
				perform_action.call(this); // Ensure the context (this) is correct
			}
		});

		// Handle Enter key navigation for the current page pagination input.
		jQuery(id).on('keydown', '.current-page', function(e) {
			if ('Enter' === e.key) {
				e.preventDefault();
				perform_action.call(this);
			}
		});
	} else {
		perform_action.call(jQuery(id).get(0)); // Use the first element from the selector to maintain context
	}

	/**
	 * Perform the action based on the clicked element or the current sorting state.
	 *
	 * @returns {void}
	 */
	function perform_action() {
		aios_block_ui(aios_trans.processing);
		var checked_values = jQuery('th.check-column input[type="checkbox"]:checked').map(function() {
			return jQuery(this).val();
		}).get();

		var event_filter = jQuery(filter_event_selector).val();

		var top_selector = jQuery('#bulk-action-selector-top').val(),
		bottom_selector = jQuery('#bulk-action-selector-bottom').val(),
		search = jQuery(search_selector).val(),
		action;

		if ('-1' !== top_selector) {
			action = top_selector;
		} else if ('-1' !== bottom_selector) {
			action = bottom_selector;
		} else {
			action = '-1';
		}

		var data = {
			'paged': jQuery(this).is('.aios-per-page') ? 1 : aios_get_page_number(jQuery(this)),
			'order': aios_get_order(jQuery(this)),
			'orderby': aios_get_order_by(jQuery(this)),
			's': search || '',
			'level-filter': jQuery(filter_level_selector).val() || '-1',
			'event-filter': event_filter || '-1',
			'per-page': jQuery('.aios-per-page').val() || 100,
			'reason-filter': jQuery(filter_reason_selector).val() || '-1',
			'items': checked_values,
			'bulk_apply': jQuery(this).is('.bulkactions .action'),
			'action': action
		};

		aios_update_list_table(tab, data);

		jQuery(action_selector).val('-1');
	}
}

/**
 * Get the column ID based on the clicked element or the current sorting state.
 *
 * @param {jQuery} element - The jQuery object representing the clicked element.
 *
 * @returns {string} The ID of the column.
 */
function aios_get_order_by(element) {
	if (element.is('.manage-column.sortable a')) return element.closest('th').attr('id');
	else if (0 < jQuery('.sorted').length) return jQuery('.sorted').attr('id');
	else return element.closest('th').attr('id');
}

/**
 * Get the order (ascending or descending) based on the clicked element or the current sorting state.
 *
 * @param {jQuery} element - The jQuery object representing the clicked element.
 *
 * @returns {string} The order ('asc' for ascending, 'desc' for descending).
 */
function aios_get_order(element) {
	if (element.is('.manage-column.sortable a') || element.is('.manage-column.sorted a')) {
		var url = element.attr('href'),
		orderMatch = url.match(/[?&]order=([^&]+)/);
		return orderMatch[1];
	} else if (0 < jQuery('.sorted').length) {
		return  jQuery('.sorted').hasClass('asc') ? 'asc' : 'desc';
	} else {
		return element.closest('th').hasClass('asc') ? 'asc' : 'desc';
	}
}

/**
 * Get the page number based on the clicked element.
 *
 * @param {jQuery} element - The jQuery object representing the clicked element.
 *
 * @returns {number|string} The page number. If the clicked element represents a specific page, returns that page number. If not, returns the current page number.
 */
function aios_get_page_number(element) {
	if (element.hasClass('first-page'))
		return '1';
	else if (element.hasClass('last-page'))
		return aios_find_page_number(jQuery('.last-page'));
	else if (element.hasClass('prev-page'))
		return aios_find_page_number(jQuery('.prev-page'));
	else if (element.hasClass('next-page'))
		return aios_find_page_number(jQuery('.next-page'));
	else return jQuery('.current-page').val();
}

/**
 * Finds and returns the page number from the href attribute of the provided element.
 *
 * @param {jQuery} element - The jQuery object representing the element containing the href attribute.
 *
 * @returns {number} The page number extracted from the href attribute.
 */
function aios_find_page_number(element) {
	return parseInt(element.attr('href').split('paged=')[1]);
}

/**
 * Update the list table with data from an AJAX call.
 * If the current page number exceeds the total pages available,
 * make a new request for the last available page.
 *
 * @param {string} table - The table tab.
 * @param {object} data - The data object containing table parameters.
 * @param {Function} callback - Optional function called after the AJAX request.
 */
function aios_update_list_table(table, data, callback) {
	aios_send_command(table, data, function(response) {
		// Check if the requested page number exceeds the total pages available
		if (data.paged > response.total_pages && 0 < response.total_pages) {
			// Update data to request the last available page
			data.paged = response.total_pages;
			// Make another AJAX call to get the data for the last page
			aios_send_command(table, data, function(response) {
				if ('function' === typeof callback) callback(response);
				aios_render_table(response);
			}, { // phpcs:ignore PEAR.Functions.FunctionCallSignature.Indent -- Seems to be a false positive.
				error_callback: function(response) {
					aios_handle_ajax_error(response);
					if ('function' === typeof callback) callback(response);
				}
			});
		} else {
			// Proceed to render the table with the current response
			if ('function' === typeof callback) callback(response);
			aios_render_table(response);
		}
	}, { // phpcs:ignore PEAR.Functions.FunctionCallSignature.Indent -- Seems to be a false positive.
		error_callback: function(response) {
			aios_handle_ajax_error(response);
			if ('function' === typeof callback) callback(response);
		}
	});
}
// End of list table handling

/**
 * Render the table elements based on the AJAX response.
 *
 * @param {object} response - The response object from the AJAX call.
 */
function aios_render_table(response) {
	// Add the requested rows
	if (response.rows.length) jQuery('#the-list').html(response.rows);
	// Update column headers for sorting
	if (response.column_headers.length) jQuery('thead tr, tfoot tr').html(response.column_headers);
	// Replace the whole tablenav (bulk actions, pagination, per-page selector, and any extra controls)
	// so bulk actions correctly appear or disappear when a table changes between empty and populated
	// via AJAX, instead of updating individual elements that may not exist.
	if (response.tablenav && response.tablenav.top.length) jQuery('.tablenav.top').replaceWith(response.tablenav.top);
	if (response.tablenav && response.tablenav.bottom.length) jQuery('.tablenav.bottom').replaceWith(response.tablenav.bottom);
	// Refresh the Permanent Block List reason filter controls after AJAX table updates.
	if (typeof response.reason_filter !== 'undefined') jQuery('.permanent-block-filter-controls').replaceWith(response.reason_filter);
	// Update the search box visibility.
	if (typeof response.show_search_box !== 'undefined') jQuery('#tables-filter .search-box').toggle(response.show_search_box);

	// Add/Remove the message
	if (response.message && response.message.length) {
		aios_show_ajax_response_message(response);
	} else {
		setTimeout(function() {
			jQuery.unblockUI();
		}, 3000);
	}
}

/**
 * Handle errors from the AJAX call.
 *
 * @param {object} response - The response object from the AJAX call.
 * @param {string} status - The status of the AJAX call.
 * @param {string} error_code - The error code from the AJAX call.
 * @param {object} resp - The response object containing detailed error information.
 */
function aios_handle_ajax_error(response, status, error_code, resp) {
	if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
		console.error(resp.fatal_error_message);
		alert(resp.fatal_error_message);
	} else {
		var error_message = "aios_send_command: error: " + status + " (" + error_code + ")";
		console.log(error_message);
		alert(error_message);
	}
	jQuery.unblockUI();
}