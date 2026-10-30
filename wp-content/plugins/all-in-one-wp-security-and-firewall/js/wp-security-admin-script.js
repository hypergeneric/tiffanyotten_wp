jQuery(function($) {
	//Add Generic Admin Dashboard JS Code in this file

	//Media Uploader - start
	jQuery("#aiowps_restore_htaccess_form").on('submit', function(e) {
		e.preventDefault();
		var form = jQuery(this);

		aios_read_restore_file(this, 'htaccess', function() {
			aios_submit_form(form, 'perform_restore_htaccess_file', true, aios_trans.processing, null, function(response) {
				form[0].reset();
			})
		});
	});

	jQuery("#aiowps_restore_wp_config_form").on('submit', function(e) {
		e.preventDefault();
		var form = jQuery(this);

		aios_read_restore_file(this, 'wp_config', function() {
			aios_submit_form(form, 'perform_restore_wp_config_file', true, aios_trans.processing, null, function(response) {
				form[0].reset();
			})
		});
	});

	jQuery("#aiowps_restore_settings_form").on('submit', function(e) {
		e.preventDefault();

		aios_read_restore_file(this, 'import_settings', function() {
			var import_contents = JSON.parse(jQuery('#aiowps_import_settings_file_contents').val());
			if ("1" != jQuery("#aios_import_settings_confirm").val() && "undefined" != typeof import_contents.sitedetails && "undefined" != typeof import_contents.sitedetails.site_url && new URL(import_contents.sitedetails.site_url).host !== window.location.host) {
				// Admin email
				jQuery("#import_settings_details").removeClass("aio_hidden");
				
				if ("undefined" != typeof import_contents.sitedetails.admin_email) {
					jQuery("#aios_import_settings_email").val(import_contents.sitedetails.admin_email.toString());
				}
				
				// Cookie based brute force on then secret word otherwise renamed login page to confirm
				if ("undefined" != typeof import_contents.firewall && "undefined" != typeof import_contents.firewall.aios_enable_brute_force_attack_prevention && "1" == import_contents.firewall.aios_enable_brute_force_attack_prevention.toString()) {
					jQuery("#site_details_cookie_based_brute_force").removeClass("aio_hidden");
					if ("undefined" != typeof import_contents.firewall.aios_brute_force_secret_word) {
						jQuery("#aios_import_settings_brute_force_secret_word").val(import_contents.firewall.aios_brute_force_secret_word.toString());
					}
				}
				
				if ("undefined" != typeof import_contents.general && "undefined" != typeof import_contents.general.aiowps_enable_rename_login_page && "1" == import_contents.general.aiowps_enable_rename_login_page.toString()) {
					jQuery("#site_details_renamed_login_page").removeClass("aio_hidden");
					if ("undefined" != typeof import_contents.general.aiowps_login_page_slug) {
						jQuery("#aios_import_settings_renamed_login_page").val(import_contents.general.aiowps_login_page_slug.toString());
					}
				}
				
				// Captcha type based reCaptcha or turnstile site key and secret key.
				if ("undefined" != typeof import_contents.general && "undefined" != typeof import_contents.general.aiowps_default_captcha) {
					if ("google-recaptcha-v2" == import_contents.general.aiowps_default_captcha) {
						jQuery("#site_details_captcha_keys").removeClass("aio_hidden");
						jQuery("#captcha_type").html(jQuery("#captcha_type").attr("attr_recaptcha"));
						if ("undefined" != typeof import_contents.general.aiowps_recaptcha_site_key) {
							jQuery("#aios_import_settings_site_key").val(import_contents.general.aiowps_recaptcha_site_key.toString());
						}
						if ("undefined" != typeof import_contents.general.aiowps_recaptcha_secret_key) {
							jQuery("#aios_import_settings_secret_key").val(import_contents.general.aiowps_recaptcha_secret_key.toString());
						}
					} else if ("cloudflare-turnstile" == import_contents.general.aiowps_default_captcha) {
						jQuery("#site_details_captcha_keys").removeClass("aio_hidden");
						jQuery("#captcha_type").html(jQuery("#captcha_type").attr("attr_turnstile"));
						if ("undefined" != typeof import_contents.general.aiowps_turnstile_site_key) {
							jQuery("#aios_import_settings_site_key").val(import_contents.general.aiowps_turnstile_site_key.toString());
						}
						if ("undefined" != typeof import_contents.general.aiowps_turnstile_secret_key) {
							jQuery("#aios_import_settings_secret_key").val(import_contents.general.aiowps_turnstile_secret_key.toString());
						}
					}
				}
				return false;
			}
			perform_restore_settings();
		})
	});
	
	/**
	 * Performs restore settings for import file contents
	 */
	function perform_restore_settings() {
		var form = jQuery("#aiowps_restore_settings_form");
		aios_submit_form(form, 'perform_restore_aiowps_settings', true, aios_trans.processing, null, function(response) {
			form[0].reset();
			jQuery("#aios_import_settings_confirm").val("0");
			jQuery("#import_settings_details").addClass("aio_hidden");
			jQuery("#site_details_cookie_based_brute_force").addClass("aio_hidden");
			jQuery("#site_details_renamed_login_page").addClass("aio_hidden");
			jQuery("#site_details_captcha_keys").addClass("aio_hidden");
			if (response.hasOwnProperty('redirect_url')) {
				// Redirect to the URL
				window.location.href = response.extra_args.redirect_url;
			}
		})
	}

	jQuery("#aios_import_settings_confirm_button").on('click', function(e) {
		jQuery("#aios_import_settings_confirm").val("1");
		perform_restore_settings();
	});

	jQuery('.aios-tooltip').tooltip({
		show: 200,
		hide: 150,
		position: {
			my: 'center bottom-10',
			at: 'center top'
		}
	});

	/**
	 * Reads the contents of a selected file and submits the form after populating a hidden input with the file contents.
	 *
	 * @param {HTMLFormElement} form - The form element to submit after reading the file contents.
	 * @param {string} 			file - The type of file to read ('htaccess', 'wp_config', 'import_settings').
	 * @param {Function} [callback] - Optional. A callback function to execute after reading the file contents.
	 */
	function aios_read_restore_file(form, file, callback) {
		var aios_import_file_input = document.getElementById('aiowps_' + file + '_file');
		if (0 == aios_import_file_input.files.length) {
			alert(aios_trans.no_import_file);
			return;
		}
		var aios_import_file_file = aios_import_file_input.files[0];
		var aios_import_file_reader = new FileReader();
		aios_import_file_reader.onload = function() {
			jQuery('#aiowps_' + file + '_file_contents').val(this.result);
			if ('function' === typeof callback) {
				// If callback is provided, execute it
				callback();
			} else {
				// If callback is not provided, submit the form
				form.submit();
			}
		};
		aios_import_file_reader.readAsText(aios_import_file_file);
	}
	//End of Media Uploader

	/**
	 * This function uses javascript to retrieve a query arg from the current page URL
	 *
	 * @param {string} name - The name of the query parameter to retrieve.
	 * @returns {string|null} The value of the query parameter, or null if the parameter does not exist.
	 */
	function getParameterByName(name) {
		var url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}

	// Start of brute force attack prevention toggle handling
	jQuery('input[name=aiowps_enable_brute_force_attack_prevention]').on('click', function() {
		jQuery('input[name=aiowps_brute_force_secret_word]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_cookie_based_brute_force_redirect_url]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_brute_force_attack_prevention_pw_protected_exception]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_brute_force_attack_prevention_ajax_exception]').prop('disabled', !jQuery(this).prop('checked'));
	});
	// End of brute force attack prevention toggle handling

	// Start of CAPTCHA handling
	jQuery('.wrap').on('change', '#aiowps_default_captcha', function() {
		var selected_captcha = jQuery(this).val();
		jQuery('.captcha_settings').hide();
		jQuery('#aios-'+ selected_captcha).show();

		if ('none' === selected_captcha) {
			jQuery('#aios-captcha-options').hide();
		} else {
			jQuery('#aios-captcha-options').show();
		}
	});
	// End of CAPTCHA handling

	/**
	 * Take a backup with UpdraftPlus if possible.
	 *
	 * @param {String}   file_entities
	 *
	 * @return void
	 */
	function take_a_backup_with_updraftplus(file_entities) {
		// Set default for file_entities to empty string
		if ('undefined' == typeof file_entities) file_entities = '';
		var exclude_files = file_entities ? 0 : 1;

		if ('function' === typeof updraft_backupnow_inpage_go) {
			updraft_backupnow_inpage_go(function() {
				// Close the backup dialogue.
				jQuery('#updraft-backupnow-inpage-modal').dialog('close');
			}, file_entities, 'autobackup', 0, exclude_files, 0);
		}
	}

	if (jQuery('#aios-manual-db-backup-now').length) {
		jQuery('#aios-manual-db-backup-now').on('click', function(e) {
			e.preventDefault();
			take_a_backup_with_updraftplus();
		});
	}

	// Hide 2FA premium section (advertisements) for free.
	if (jQuery('.tfa-premium').length && 0 == jQuery('#tfa_trusted_for').length) {
		jQuery('.tfa-premium').parent().find('hr').first().hide();
		jQuery('.tfa-premium').hide();
	}

	// Start of trash spam comments toggle handling
	jQuery('input[name=aiowps_enable_trash_spam_comments]').on('click', function() {
		jQuery('input[name=aiowps_trash_spam_comments_after_days]').prop('disabled', !jQuery(this).prop('checked'));
	});
	// End of trash spam comments toggle handling

	/**
	 * Copies text to the clipboard using the deprecated document.execCommand method.
	 *
	 * @param {string} text - The text to be copied to the clipboard.
	 */
	function deprecated_copy(text) {
		var $temp = jQuery('<input>');
		jQuery('body').append($temp);
		$temp.val(text).select();
		if (document.execCommand('copy')) {
			alert(aios_trans.copied);
		}
		$temp.remove();
	}

	// Start of copy-to-clipboard click handling
	jQuery('.copy-to-clipboard').on('click', function(event) {
		if (navigator.clipboard) {
			navigator.clipboard.writeText(event.target.value).then(function() {
					alert(aios_trans.copied);
				}, function() {
				deprecated_copy(event.target.value);
			});
		} else {
			deprecated_copy(event.target.value);
		}
	});
	// End of copy-to-clipboard click handling

	// Start audit log list table handling
	var audit_log_table_id = '#audit-log-list-table #tables-filter',
		audit_log_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a, #search-submit, .action, .aios-per-page',
		audit_log_bulk_action_selector = '#bulk-action-selector-top, #bulk-action-selector-bottom',
		audit_log_table_tab = 'render_audit_log_tab',
		audit_log_filter_event = '.audit-filter-event',
		audit_log_search = '#search_audit_events-search-input',
		audit_log_level = '.audit-filter-level';
	aios_detect_table_action(audit_log_table_id, audit_log_elements, audit_log_bulk_action_selector, audit_log_table_tab, audit_log_filter_event, audit_log_search, audit_log_level);
	// End of audit log list table handling

	// Start locked ip list table handling
	var locked_ip_table_id = '#locked-ip-list-table #tables-filter',
		locked_ip_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a, #search-submit, .action',
		locked_ip_bulk_action_selector = '#bulk-action-selector-top, #bulk-action-selector-bottom',
		locked_ip_table_tab = 'render_locked_ip_tab';
	aios_detect_table_action(locked_ip_table_id, locked_ip_elements, locked_ip_bulk_action_selector, locked_ip_table_tab, '', '', '');
	// End of locked ip list table handling

	// Start permanent block ip list table handling
	var permanent_blocked_ip_table_id = '#permanent-ip-list-table #tables-filter',
		permanent_blocked_ip_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a, #search-submit, .action',
		permanent_blocked_ip_bulk_action_selector = '#bulk-action-selector-top, #bulk-action-selector-bottom',
		permanent_blocked_ip_table_tab = 'render_permanent_block_list_tab',
		permanent_blocked_ip_search = '#search_permanent_block-search-input';
		permanent_blocked_ip_reason = '.permanent-block-filter-reason';
	aios_detect_table_action(permanent_blocked_ip_table_id, permanent_blocked_ip_elements, permanent_blocked_ip_bulk_action_selector, permanent_blocked_ip_table_tab, '', permanent_blocked_ip_search, '', permanent_blocked_ip_reason);
	// End of  permanent block ip list table handling

	// Start 404 log list table handling
	var log_404_table_id = '#404-log-list-table #tables-filter',
		log_404_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a, #search-submit, .action',
		log_404_bulk_action_selector = '#bulk-action-selector-top, #bulk-action-selector-bottom',
		log_404_table_tab = 'render_404_log_tab',
		log_404_search = '#search_404_events-search-input';
	aios_detect_table_action(log_404_table_id, log_404_elements, log_404_bulk_action_selector, log_404_table_tab, '', log_404_search, '');
	// End of 404 log list table handling

	// Start of debug log list table handling
	var debug_log_table_id = '#debug-list-table',
		debug_log_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a',
		debug_log_table_tab = 'render_debug_log_tab';
	aios_detect_table_action(debug_log_table_id, debug_log_elements, '', debug_log_table_tab);
	// End of debug log list table handling

	// Start of logged in user table handling
	var logged_in_users_table_id = '#aios-logged-in-users-table #tables-filter',
		logged_in_users_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a, .action',
		logged_in_users_bulk_action_selector = '#bulk-action-selector-top, #bulk-action-selector-bottom',
		logged_in_users_table_tab = 'render_logged_in_users_tab';
	aios_detect_table_action(logged_in_users_table_id, logged_in_users_elements, logged_in_users_bulk_action_selector, logged_in_users_table_tab, '', '', '');
	// End of logged in user table handling

	// Start of manual approval (registered users) table handling
	var manual_approval_table_id = '#aios-manual-approval-table #tables-filter',
		manual_approval_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a, #search-submit, .action',
		manual_approval_bulk_action_selector = '#bulk-action-selector-top, #bulk-action-selector-bottom',
		manual_approval_table_tab = 'render_manual_approval_tab',
		manual_approval_search = '#search_user_registration-search-input';
	aios_detect_table_action(manual_approval_table_id, manual_approval_elements, manual_approval_bulk_action_selector, manual_approval_table_tab, '', manual_approval_search, '');
	// End of manual approval table handling

	// Start of spammer ip list table handling
	var spammer_ip_table_id = '#aios-spammer-list-table #tables-filter',
		spammer_ip_elements = '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a, #search-submit, .action',
		spammer_ip_bulk_action_selector = '#bulk-action-selector-top, #bulk-action-selector-bottom',
		spammer_ip_table_tab = 'render_comment_spammer_ip_table';
	aios_detect_table_action(spammer_ip_table_id, spammer_ip_elements, spammer_ip_bulk_action_selector, spammer_ip_table_tab, '', '', '');
	// End of spammer ip list table handling

	// Start of database table prefix handling
	jQuery('#aiowps_enable_random_prefix').on('click', function() {
		jQuery('#aiowps_new_manual_db_prefix').prop('disabled', jQuery(this).prop('checked'));
	});

	jQuery('#aiowps_new_manual_db_prefix').on('input', function() {
		if (jQuery(this).prop('value')) {
			jQuery('#aiowps_enable_random_prefix').prop('disabled', true);
		} else {
			jQuery('#aiowps_enable_random_prefix').prop('disabled', false);
		}
	});
	// End of database table prefix handling

	// Start of dashboard menu ajaxify
	jQuery("#locked-ip-list-table").on('click', '.aios-unlock-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'unlock_ip', {ip: element.data('ip')}, aios_trans.unlocking, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(locked_ip_table_id, '', locked_ip_bulk_action_selector, locked_ip_table_tab, '', '', '', '', false);
			});
		}
	});

	jQuery("#locked-ip-list-table").on('click', '.aios-delete-locked-ip-record', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element,'delete_locked_ip_record', {id: element.data('id')}, aios_trans.deleting, null, function (response) {
				if ('success' === response.status) aios_detect_table_action(locked_ip_table_id, '', locked_ip_bulk_action_selector, locked_ip_table_tab, '', '', '', '', false);
			});
		}
	});

	jQuery("#permanent-ip-list-table").on('click', '.aios-unblock-permanent-ip', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'blocked_ip_list_unblock_ip', {id: element.data('id')}, aios_trans.unlocking, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(permanent_blocked_ip_table_id, '', permanent_blocked_ip_bulk_action_selector, permanent_blocked_ip_table_tab, '', permanent_blocked_ip_search, '', permanent_blocked_ip_reason, false);
			});
		}
	});

	jQuery("#permanent-ip-list-table").on('click', '.aios-unblock-filtered-permanent-ip', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'blocked_ip_list_unblock_ip', {id: element.data('id')}, aios_trans.unlocking, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(permanent_blocked_ip_table_id, '', permanent_blocked_ip_bulk_action_selector, permanent_blocked_ip_table_tab, '', permanent_blocked_ip_search, '', permanent_blocked_ip_reason, false);
			});
		}
	});

	jQuery('#audit-log-list-table').on('click', '.aios-delete-audit-log', function(e) {
		e.preventDefault();
		var element = jQuery(this);

		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'delete_audit_log', {id: element.data('id')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(audit_log_table_id, '', audit_log_bulk_action_selector, audit_log_table_tab, audit_log_filter_event, audit_log_search, audit_log_level, '', false);
			})
		}
	});

	jQuery('#audit-log-list-table').on('click', '.aios-unlock-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'unlock_ip', {ip: element.data('ip')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(audit_log_table_id, '', audit_log_bulk_action_selector, audit_log_table_tab, audit_log_filter_event, audit_log_search, audit_log_level, '', false);
			})
		}
	});

	jQuery('#audit-log-list-table').on('click', '.aios-unblacklist-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'unblacklist_ip', {ip: element.data('ip')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(audit_log_table_id, '', audit_log_bulk_action_selector, audit_log_table_tab, audit_log_filter_event, audit_log_search, audit_log_level, '', false);
			})
		}
	});

	jQuery('#audit-log-list-table').on('click', '.aios-lock-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element,'lock_ip', {ip: element.data('ip'), lock_reason: 'audit-log'}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(audit_log_table_id, '', audit_log_bulk_action_selector, audit_log_table_tab, audit_log_filter_event, audit_log_search, audit_log_level, '', false);
			})
		}
	});

	jQuery('#audit-log-list-table').on('click', '.aios-blacklist-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element,'blacklist_ip', {ip: element.data('ip')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(audit_log_table_id, '', audit_log_bulk_action_selector, audit_log_table_tab, audit_log_filter_event, audit_log_search, audit_log_level, '', false);
			})
		}
	});

	jQuery('#audit-log-list-table').on('click', '#aiowps_export_audit_event_logs_to_csv', function(e) {
		e.preventDefault();

		aios_submit_form(jQuery(this), 'export_audit_logs', true, aios_trans.processing, null, function(response) {
			if (false === response.success) return;
			aios_download_csv_file(response.data, response.title);
		});
	});

	jQuery('#aios-export-404-csv-form').on('submit', function(e) {
		e.preventDefault();

		aios_submit_form(jQuery(this), 'export_404_detection_logs', true, aios_trans.processing, null, function (response) {
			if (false === response.success) return;
			aios_download_csv_file(response.data, response.title);
		});
	});

	jQuery('#aios-clear-debug-logs').on('click', '.aios-clear-debug-logs', function(e) {
		e.preventDefault();
		if (confirm(jQuery(this).data('message'))) {
			aios_submit_form(jQuery(this), 'clear_debug_logs', {}, aios_trans.deleting, null, function(response) {
				if ("success" === response.status) aios_update_list_table(debug_log_table_tab, {});
			});
		}
	});
	// End of dashboard menu ajaxify

	//Start of Spam prevention ajaxify
	jQuery('#aios-spam-prevention-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_comment_spam_prevention');
	})

	jQuery('#aios-auto-spam-block-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_auto_block_spam_ip');
	})

	jQuery('#aios-spam-ip-search-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_ip_spam_search', true, aios_trans.processing, null, function(response) {
			var targetOffset = jQuery('#aios-spammer-list-table').offset().top;
			jQuery('html, body').animate({ scrollTop: targetOffset }, 'slow');
			if ("success" === response.status) {
				aios_detect_table_action(spammer_ip_table_id, '', spammer_ip_bulk_action_selector, spammer_ip_table_tab, '', '', '', '', false);
			}
		})
	})

	jQuery('#aios-spammer-list-table').on('click', '.aios-block-author-ip', function(e) {
		e.preventDefault();
		var ip = jQuery(this).data('ip');
		var data = {
			'ip': ip
		}

		if (confirm(jQuery(this).data('message'))) {
			aios_submit_form(jQuery(this), 'perform_block_spam_ip', data, aios_trans.processing, null, function(response) {
				if ("success" === response.status) {
					aios_detect_table_action(spammer_ip_table_id, '', spammer_ip_bulk_action_selector, spammer_ip_table_tab, '', '', '', '', false);
					jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			});
		}
	})
	//End of spam prevention ajaxify

	// Start of settings menu ajaxify
	jQuery('#aiowpsec-disable-all-features-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_disable_all_features', true, aios_trans.disabling);
	});

	jQuery('#aiowpsec-disable-all-firewall-rules-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_disable_all_firewall_rules', true, aios_trans.disabling);
	});

	jQuery('#aiowpsec-reset-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_reset_all_settings', true, aios_trans.processing);
	});

	jQuery('#aiowps_enable_debug').on('change', function(e) {
		e.preventDefault();
		var aiowps_enable_debug = jQuery(this).is(':checked') ? '1' : '';
		var data = {
			'aiowps_enable_debug': aiowps_enable_debug
		};

		aios_submit_form(jQuery(this), 'perform_save_debug_settings', data, aios_trans.processing);
	});

	jQuery('#aiowpsec-save-htaccess-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_backup_htaccess_file', true, aios_trans.processing, null, function(response) {
			if ('success' === response.status) {
				aios_download_txt_file(response.extra_args.data, response.extra_args.title);
			}
		});
	});

	jQuery('#aiowpsec-delete-plugin-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_delete_plugin_settings');
	});

	jQuery('#aiowpsec-remove-wp-meta-info-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_remove_wp_version_info_settings');
	});

	jQuery('#aiowpsec-ip-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_ip_settings');
	});

	jQuery('#aiowpsec-save-wp-config-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_wp_config', {}, aios_trans.saving, null, function(response) {
			aios_download_txt_file(response.extra_args.data, response.extra_args.title);
		});
	});

	jQuery('#aiowpsec-export-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_export_aios_settings', {}, aios_trans.exporting, null, function(response) {
			aios_download_txt_file(response.extra_args.data, response.extra_args.title);
		});
	});
	// End of settings menu ajaxify
	
	// Updraft login form handling

	// Handles the response shared by the login and registration requests: reveals the
	// two-factor field when required, or recovers from an incorrect password.
	function handle_updraft_post(response) {
		let ea = response.extra_args || {};

		if ('incorrect_password' === ea.code) {
			// Return to the credential step: show the credential fields, hide and clear the
			// two-factor field (a stale code would otherwise be re-sent on the next attempt
			// and rejected before the server can re-prompt), and restore the button label.
			jQuery('.aiowps_updraft_credential_row').show();
			jQuery('#aiowps_updraft_two_factor_row').hide();
			jQuery('#aiowps_updraft_two_factor_code').val('');
			jQuery('#aios_updraft_login_submit').val(aios_trans.connect);
			jQuery('#aiowps_updraft_password').val('').focus();
		}

		if (ea.tfa_enabled) {
			// The account has two-factor authentication enabled; hide the credential
			// fields and reveal the code field so the user can resubmit the form.
			jQuery('.aiowps_updraft_credential_row').hide();
			jQuery('#aiowps_updraft_two_factor_row').show();
			jQuery('#aiowps_updraft_two_factor_code').focus();
			jQuery('#aios_updraft_login_submit').val(aios_trans.verify);

			// Suppress the error modal — the 2FA field reveal is the only feedback needed.
			delete response.message;
			delete response.status;
		}

		// On 'authenticated'/'registered' success the server has already swapped the
		// form container via response.content, so there is nothing to do here.
	}

	jQuery(document).on('submit', '#aios-team-updraft-login-form', function(e) {
		e.preventDefault();

		let $form = jQuery(this),
			awaiting_2fa = jQuery('#aiowps_updraft_two_factor_row').is(':visible'),
			terms = jQuery('#aiowps_updraft_terms').is(':checked');

		// The terms checkbox is hidden during the two-factor step, so only enforce it
		// on the initial credentials submission.
		if (!awaiting_2fa && !terms) {
			alert(aios_trans.accept_terms);
			return;
		}

		let	data = {
			'email': jQuery('#aiowps_updraft_email').val(),
			'password': jQuery('#aiowps_updraft_password').val(),
			'terms': terms
		};

		// Only send the two-factor code when we are actually on the 2FA step; otherwise a
		// stale code could short-circuit the server into 2FA verification on a fresh login.
		if (awaiting_2fa) {
			data.two_factor_code = jQuery('#aiowps_updraft_two_factor_code').val();
		}

		aios_submit_form($form, 'perform_updraft_login', data, aios_trans.processing, null, handle_updraft_post);
	});

	jQuery(document).on('submit', '#aios-team-updraft-logout-form', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_updraft_disconnect', true, aios_trans.processing);
	});

	// Start of filesystem menu ajaxify
	jQuery('#aios-file-permissions-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_fix_permissions', true, aios_trans.processing);
	})

	jQuery('#aiowps_delete_default_wp_files').on('click', function(e) {
		e.preventDefault();
		var button = jQuery(this);
		aios_submit_form(jQuery(this), 'perform_delete_default_wp_files', {'aios-delete_default_wp_files': 1}, aios_trans.deleting, function() {
			button.prop('disabled', true);
			},
			function(response) {
				button.prop('disabled', false);
		});
	})

	jQuery('#aios-file-protection-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_file_protection_settings');
	})

	jQuery('#aios-host-system-logs-form').on('submit', function(e) {
		e.preventDefault();

		aios_submit_form(jQuery(this), 'perform_host_system_logs', true, aios_trans.processing, function() {
			jQuery('#aios-host-system-logs-results').html('');
			jQuery('#aiowps_activejobs_table').html('<p><span class="aiowps_spinner spinner">'+ aios_escape_html(aios_trans.processing) + '</span></p>');
			jQuery('#aiowps_activejobs_table .aiowps_spinner').addClass('visible');
			},
			function(response) {
				var loading_span = jQuery('#aiowps_activejobs_table');
				loading_span.hide();
		});
	})

	jQuery('#aios-frame-display-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_frame_display_prevent');
	})

	jQuery('#aios-copy-protection-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this),'perform_save_copy_protection');
	})
	// End of filesystem menu ajaxify

	// Start of firewall menu ajaxify
	jQuery('#aios-php-firewall-settings-form').on('submit', function(e) {
		e.preventDefault();
		if ('perform_save_rest_api_settings' === jQuery(this).data('command')) {
			aios_submit_form(jQuery(this), 'perform_save_rest_api_settings');
			return;
		}
		aios_submit_form(jQuery(this), 'perform_php_firewall_settings', true, aios_trans.saving, null, function(response) {
			if ("success" === response.status) {
				jQuery('.aio_orange_box').remove();
				if (jQuery('#aiowps_enable_6g_firewall').prop('checked')) {
					jQuery('.aios-toggle-advanced-options').removeClass('advanced-options-disabled').trigger('click');
					jQuery('.aios-advanced-options-panel .aiowps_more_info_body').hide();
				} else {
					jQuery('.aios-toggle-advanced-options').addClass('advanced-options-disabled');
					jQuery('.button.button-link.aios-toggle-advanced-options').removeClass('opened');
				}
				check_input();
				jQuery('#post-body h2:first').after(response.extra_args.xmlprc_warning);
			}
		});
	});

	jQuery('#aios-htaccess-firewall-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this),'perform_htaccess_firewall_settings');
	});

	jQuery("#aios-blacklist-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this),'perform_save_blacklist_settings');
	});

	jQuery("#aios-firewall-allowlist-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this),'perform_firewall_allowlist');
	});

	jQuery("#aios-5g-firewall-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_5g_settings')
	});

	jQuery('#aiowps-firewall-status-container').on('submit', "#aiowpsec-firewall-setup-form", function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_setup_firewall', true, aios_trans.setting_up_firewall, null, function(response) {
			if (response.extra_args && response.extra_args.info_box) {
				jQuery("#aios-firewall-setup-notice").remove();
				jQuery('#wpbody-content .wrap h2:first').after(response.extra_args.info_box);
			}
		});
	});

	jQuery('#aiowps-firewall-status-container').on('submit', "#aiowps-firewall-downgrade-form", function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_downgrade_firewall', true, aios_trans.downgrading_firewall, null, function(response) {
			if (response.extra_args && response.extra_args.info_box) {
				jQuery("#aios-firewall-installed-notice").remove();
				jQuery('#wpbody-content .wrap h2:first').after(response.extra_args.info_box);
			}
		});
	});

	jQuery('#aios-upgrade-unsafe-http-calls-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_upgrade_unsafe_http_calls_settings');
	});
	// End of firewall menu ajaxify

	// start of user security menu ajax
	jQuery('#aios-user-accounts-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_user_account_settings');
	});

	jQuery('#aios-change-admin-username-button').on('click', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this).closest('form'), 'perform_change_admin_username', true, aios_trans.saving, null, function(response) {
			if (response.extra_args && true === response.extra_args.logout_user) {
				setTimeout(function() {
					// Check if a logout URL is present in the response
					if (response.extra_args.logout_url) {
						// Redirect to the logout URL
						window.location.href = response.extra_args.logout_url;
					} else {
						// If no logout URL is provided, reload the current page
						location.reload();
					}
				}, 3000);
			}
		});
	});

	jQuery('#aios-user-login-lockdown-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_login_lockout_settings');
	});

	jQuery('#aios-user-login-lockout-whitelist-settings-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_login_lockout_whitelist_settings');
	});

	jQuery('#aios-force-user-logout-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_force_logout_settings', true, aios_trans.saving, null, function(response) {
			if (response.extra_args && true === response.extra_args.logout_user) {
				setTimeout(function() {
					// Check if a logout URL is present in the response
					if (response.extra_args.logout_url) {
						// Redirect to the logout URL
						window.location.href = response.extra_args.logout_url;
					} else {
						// If no logout URL is provided, reload the current page
						location.reload();
					}
				}, 3000);
			}
		});
	});

	jQuery('#aios-http-authentication-settings-form').on('submit', function(e) {
		e.preventDefault();
		if (typeof tinyMCE !== 'undefined') {
			tinyMCE.triggerSave();
		}
		aios_submit_form(jQuery(this), 'perform_save_http_authentication_settings');
	});

	jQuery('#aios-disable-application-password-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_disable_application_password');
	});

	jQuery('#aios-enable-salt-postfix-form').submit(function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this),'perform_add_salt_postfix', true, aios_trans.saving, null,function() {
			setTimeout(function() {
				location.reload();
			}, 3000);
		});
	});

	jQuery('#aios-logged-in-users-table').on('click', '.aios-force-logout-user', function(e) {
		e.preventDefault();
		let user_id = jQuery(this).data('user-id'), data = {
			logged_in_id: user_id
		};
		if (confirm(jQuery(this).data('message'))) {
			aios_submit_form(jQuery(this), 'perform_force_user_logout', data, aios_trans.processing, null, function(response) {
				if ('success' === response.status) {
					aios_detect_table_action(logged_in_users_table_id, '', logged_in_users_bulk_action_selector, logged_in_users_table_tab, '', '', '', '', false);
				}
			});
		}
	});

	jQuery("#aios-refresh-logged-in-user-list-form").on('submit', function(e) {
		e.preventDefault();
		aios_block_ui(aios_trans.refreshing);
		var submitButton = jQuery(this).find(':submit');
		submitButton.prop('disabled', true);
		aios_update_list_table(logged_in_users_table_tab, {}, function() {
			jQuery.unblockUI();
			submitButton.prop('disabled', false);
		});
	});

	jQuery('#aios-manually-approve-registrations-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_manual_approval_settings');
	});

	jQuery("#aios-manual-approval-table").on('click', '.aios-approve-user-acct', function(e) {
		e.preventDefault();
		let user_id = jQuery(this).data('id'), data = {
			user_id: user_id,
			action: 'approve_acct'
		};
		if (confirm(jQuery(this).data('message'))) {
			aios_submit_form(jQuery(this), 'perform_manual_approval_item_action', data, aios_trans.processing, null, function(response) {
				if ('success' === response.status) {
					aios_detect_table_action(manual_approval_table_id, '', manual_approval_bulk_action_selector, manual_approval_table_tab, '', manual_approval_search, '', '', false);
				}
			});
		}
	});

	jQuery("#aios-manual-approval-table").on('click', '.aios-delete-user-acct', function(e) {
		e.preventDefault();
		let user_id = jQuery(this).data('id'), data = {
			user_id: user_id,
			action: 'delete_acct'
		};
		if (confirm(jQuery(this).data('message'))) {
			aios_submit_form(jQuery(this), 'perform_manual_approval_item_action', data, aios_trans.processing, null, function(response) {
				if ('success' === response.status) {
					aios_detect_table_action(manual_approval_table_id, '', manual_approval_bulk_action_selector, manual_approval_table_tab, '', manual_approval_search, '', '', false);
				}
			});
		}
	});

	jQuery("#aios-manual-approval-table").on('click', '.aios-block-ip', function(e) {
		e.preventDefault();
		let ip_address = jQuery(this).data('ip'), data = {
			ip_address: ip_address,
			action: 'block_ip'
		};
		if (confirm(jQuery(this).data('message'))) {
			aios_submit_form(jQuery(this),'perform_manual_approval_item_action', data, aios_trans.blocking, null, function(response) {
				if ('success' === response.status) {
					aios_detect_table_action(manual_approval_table_id, '', manual_approval_bulk_action_selector, manual_approval_table_tab, '', manual_approval_search, '', '', false);
				}
			})
		}
	});

	jQuery("#aios-refresh-manual-approval-list-form").on('submit', function(e) {
		e.preventDefault();
		aios_block_ui(aios_trans.refreshing);
		var submitButton = jQuery(this).find(':submit');
		submitButton.prop('disabled', true);
		aios_update_list_table(manual_approval_table_tab, {}, function(response) {
			// Add the search box after the list refresh when it is not already displayed.
			if (response.search_box && !jQuery('#tables-filter .search-box').length) jQuery('#tables-filter').prepend(response.search_box);
			jQuery.unblockUI();
			submitButton.prop('disabled', false);
		});
	});

	// end of user security menu ajax

	// start of tools menu ajaxify
	jQuery("#aiowpsec-whois-lookup-form").on('submit', function(e) {
		e.preventDefault();

		jQuery('#aios-who-is-lookup-result-container').html('');

		aios_submit_form(jQuery(this), 'perform_whois_lookup', true, aios_trans.processing, null, function() {
			var targetOffset = jQuery('#aios-who-is-lookup-result-container').offset().top;
			jQuery('html, body').animate({ scrollTop: targetOffset }, 'slow');
		});
	});

	jQuery("#aiowpsec-site-lockout-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_general_visitor_lockout', true, aios_trans.saving, function() {
			var editor = tinyMCE.get('aiowps_site_lockout_msg_editor_content');
			if (editor) {
				editor.save();
			}
		});
	});

	jQuery('#maintenance_mode_status').on('click', '#aiowps_site_lockout', function(e) {
		aios_block_ui(aios_trans.saving);

		var enabled = jQuery(this).is(':checked');
		var data = {};

		if (enabled) {
			data.aiowps_site_lockout = '1';
		}

		aios_send_command('perform_general_visitor_lockout_dashboard_widget', data, function(response) {
			aios_show_ajax_response_message(response);
			if ('success' === response.status) {
				if (enabled) {
					jQuery("#aiowpsec-dashboard-maintenance-mode-status-message").text(aios_trans.maintenance_mode_enabled);
				} else {
					jQuery("#aiowpsec-dashboard-maintenance-mode-status-message").text(aios_trans.maintenance_mode_disabled);
				}
			}
		});
	});

	jQuery("#aiowpsec-save-custom-rules-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_store_custom_htaccess_settings');
	});
	// end  of tools menu ajaxify
	jQuery('#aiowpsec-scheduled-fcd-scan-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_save_file_detection_change_settings');
	});

	// Start of file scan handling
	jQuery('.aiowps_next_scheduled_scan_wrapper').on('click', '.aiowps_view_last_fcd_results', view_scan_results_handler);
	jQuery('#aiowps_fcds_change_detected').on('click', '.aiowps_view_last_fcd_results', view_scan_results_handler);

	/**
	 * This function handles the view last scan result event
	 *
	 * @param {*} e - the event
	 */
	function view_scan_results_handler(e) {
		e.preventDefault();

		jQuery('#aiowps_previous_scan_wrapper').html('');

		var reset_change_detected = jQuery(this).data('reset_change_detected') ? 1 : 0;

		aios_submit_form(jQuery(this), 'get_last_scan_results', { reset_change_detected: reset_change_detected}, aios_trans.processing, null, function(response) {
			if (reset_change_detected) jQuery('#aiowps_fcds_change_detected').remove();
			var targetOffset = jQuery('#aiowps_previous_scan_wrapper').offset().top;
			jQuery('html, body').animate({ scrollTop: targetOffset }, 'slow');
		})
	}

	jQuery('#aiowps_manual_fcd_scan').on('click', function(e) {
		e.preventDefault();

		jQuery('#aiowps_manual_fcd_scan').prop('disabled', true);
		// Insert spinner loader
		aiowps_show_spinner('#aiowps_activejobs');
		jQuery('#aiowps_previous_scan_wrapper').html('')

		aios_send_command('initiate_file_scan', {}, function(response) {
			if ('success' === response.status) {
				jQuery('#aiowps_cancel_fcd_scan').show().data('task-id', response.extra_args.task_id);
			}
			handle_file_scan_response(response);
		});
	});

	jQuery('#aiowps_cancel_fcd_scan').on('click', function(e) {
		e.preventDefault();

		if (confirm(jQuery(this).data('message'))) {
			aios_submit_form(jQuery(this), 'cancel_file_scan', {task_id: jQuery(this).data('task-id')}, aios_trans.processing);
		}
	});

	/**
	 * Handles a response from the file scan task.
	 *
	 * @param {Object} response
	 *
	 * @returns {void}
	 */
	function handle_file_scan_response(response) {
		if ('success' === response.status) {
			aiowps_show_spinner('#aiowps_manual_fcd_scan')

			let stage = response.extra_args.current_stage,
				text = response.message,
				percent = 10,
				stop_task = false;

			switch (stage) {
				case 'starting':
					percent = 20;
					break;
				case 'fetching':
					percent = 30;
					break;
				case 'downloading_checksums':
					percent = 40;
					break;
				case 'scanning':
					let number_of_scanned_files = response.extra_args.number_of_scanned_files,
						total_file_count = response.extra_args.total_files;

					if (total_file_count > 0) {
						let file_scan_percentage = (number_of_scanned_files / total_file_count) * 100;
						percent = 50 + (file_scan_percentage * 0.3);
						if (percent > 80) percent = 80; // cap at 80
						text = text + ': ' + number_of_scanned_files + '/' + total_file_count + ' (' + file_scan_percentage.toFixed(2) + '%)';
					} else {
						percent = 50;
					}
					break;
				case 'comparing':
					let number_of_compared_files = response.extra_args.number_of_compared_files,
						total_kept_files = response.extra_args.total_kept_files;

					if (total_kept_files > 0) {
						let compare_percentage = (number_of_compared_files / total_kept_files) * 100;
						percent = 80 + (compare_percentage * 0.1);
						if (percent > 90) percent = 90; // cap at 90
						text = text + ': ' + number_of_compared_files + '/' + total_kept_files + ' (' + compare_percentage.toFixed(2) + '%)';
					} else {
						percent = 80;
					}
					break;
				case 'saving':
					percent = 90;
					break;
				case 'completed':
					percent = 100;
					stop_task = true;
					break;
				case 'cancelled':
					percent = 100;
					stop_task = true;
					break;
			}

			jQuery('#aiowps_activejobs_table').html('<div class="aios-loading-bar"><div class="aios-loading-progress"></div><div class="aios-progress-text">' + aios_escape_html(text) + '...</div></div>');
			var progress = jQuery('.aios-loading-progress');
			progress.css('width', percent + '%');

			if (stop_task) {
				setTimeout(function() {
					jQuery('.aios-loading-bar').fadeOut();
					if (response.hasOwnProperty('content')) {
						jQuery.each(response.content, function(key, value) {
							aios_update_content('#' + key, value);
						});
					}
					reset_scan_page_ui();
					var scan_status = response.extra_args.scan_status;
					var view_link = '<a href="#" class="aiowps_view_last_fcd_results">' + aios_escape_html(aios_trans.scan_result_view_link) + '</a>';
					var result_text = '';
					if ('initial_scan' === scan_status) {
						result_text = aios_escape_html(aios_trans.scan_result_initial) + ' ' + view_link;
					} else if ('changes_detected' === scan_status) {
						result_text = aios_escape_html(aios_trans.scan_result_changes) + ' ' + view_link;
					} else if ('no_changes' === scan_status) {
						result_text = aios_escape_html(aios_trans.scan_result_no_changes);
					}
					if (result_text) jQuery('#aiowps_activejobs_table').append('<p>' + result_text + '</p>');
				}, 3000)
			} else {
				setTimeout(function () {
					aios_send_command('get_file_scan_update', {task_id: response.extra_args.task_id}, handle_file_scan_response);
				}, 10000)
			}
		} else {
			reset_scan_page_ui();

			if ('error' === response.status) {
				aios_show_success_modal({
					message: response.extra_args?.error_message ? response.message + ': ' + response.extra_args.error_message : response.message,
					icon: 'no-alt',
					classes: 'warning'
				}, false);
			}
		}
	}

	function reset_scan_page_ui() {
		jQuery('#aiowps_manual_fcd_scan').prop('disabled', false);
		aiowps_hide_spinner('#aiowps_activejobs');
		aiowps_hide_spinner('#aiowps_manual_fcd_scan');
		jQuery('#aiowps_activejobs_table').find('.aios-loading-bar').remove();
		jQuery('#aiowps_manual_fcd_scan').text(aios_trans.scan_now);
		jQuery('#aiowps_cancel_fcd_scan').hide();
	}

	/**
	 * Resumes the periodic update-handle loop if a file scan task is currently running.
	 *
	 * @returns {void}
	 */
	function check_if_running_file_scan() {
		if (!window.location.href.includes('admin.php?page=aiowpsec_filescan')) {
			return;
		}

		jQuery('#aiowps_manual_fcd_scan').prop('disabled', true);
		aiowps_show_spinner('#aiowps_activejobs');

		aios_send_command('check_running_task', {type: 'file_scan'}, function (response) {
			if ('success' === response.status) {
				jQuery('#aiowps_cancel_fcd_scan').show().data('task-id', response.extra_args.task_id);
				aios_send_command('get_file_scan_update', {task_id: response.extra_args.task_id}, handle_file_scan_response);
				aiowps_show_spinner('#aiowps_manual_fcd_scan');
			} else {
				jQuery('#aiowps_manual_fcd_scan').prop('disabled', false);
				aiowps_hide_spinner('#aiowps_activejobs');
			}
		});
	}

	check_if_running_file_scan();
	// End of file scan handling

	// Start of brute force ajax
	jQuery('#aios-rename-login-page-form').on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_rename_login_page', true, aios_trans.saving, null,function(response) {
			if ("error" === response.status) {
				jQuery('#aiowps_enable_rename_login_page').prop('checked', false);
				return;
			}

			if (response.extra_args && response.extra_args.logout_url) {
				jQuery('#wp-admin-bar-logout a').attr('href', response.extra_args.logout_url);
			}
		})
	});

	jQuery("#aios-cookie-based-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_cookie_based_brute_force_prevention');
	});

	jQuery("#aios-perform-cookie-test").on('click', function(e) {
		e.preventDefault();
		var cookieButton = jQuery(this);
		cookieButton.prop('disabled', true);

		aios_submit_form(jQuery(this), 'perform_cookie_test', {}, aios_trans.processing, null,function(response) {
			cookieButton.prop('disabled', false);
			if ("success" === response.status) {
				var submitButton = jQuery('#aios-cookie-based-settings-form').find(':submit');
				submitButton.prop('disabled', false);
			}
		})
	});

	jQuery("#aios-login-whitelist-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_login_whitelist_settings');
	});

	jQuery("#aios-honeypot-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_honeypot_settings');
	});

	jQuery("#aios-captcha-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_captcha_settings');
	});

	jQuery("#aios-404-detection-settings-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_404_settings', true, aios_trans.saving, null, function(response) {
			if ("success" === response.status) {
				jQuery('.aios-404-detection-container').toggleClass('aio_hidden', !jQuery('#aiowps_enable_404_IP_lockout').is(':checked'));
			}
		});
	});

	jQuery("#aios-delete-404-form").on('submit', function(e) {
		e.preventDefault();
		aios_submit_form(jQuery(this), 'perform_delete_all_404_event_records', true, aios_trans.deleting, null, function(response) {
			if ("success" === response.status) jQuery('#404-log-list-table').load(' #404-log-list-table >  *', function() {
				jQuery(".aiowps_more_info_body").hide();
			});
		});
	});

	jQuery('#404-log-list-table').on('click', '.aios-unlock-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'unlock_ip', {ip: element.data('ip')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(log_404_table_id, '', log_404_bulk_action_selector, log_404_table_tab, '', log_404_search, '', '', false);
			})
		}
	});

	jQuery('#404-log-list-table').on('click', '.aios-unblacklist-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'unblacklist_ip', {ip: element.data('ip')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(log_404_table_id, '', log_404_bulk_action_selector, log_404_table_tab, '', log_404_search, '', '', false);
			})
		}
	});

	jQuery('#404-log-list-table').on('click', '.aios-lock-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'lock_ip', {ip: element.data('ip'), lock_reason: '404', username: element.data('username')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(log_404_table_id, '', log_404_bulk_action_selector, log_404_table_tab, '', log_404_search, '', '', false);
			})
		}
	});

	jQuery('#404-log-list-table').on('click', '.aios-blacklist-ip-button', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'blacklist_ip', {ip: element.data('ip')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(log_404_table_id, '', log_404_bulk_action_selector, log_404_table_tab, '', log_404_search, '', '', false);
			})
		}
	});

	jQuery('#404-log-list-table').on('click', '.aios-delete-404-log', function(e) {
		e.preventDefault();
		var element = jQuery(this);
		if (confirm(element.data('message'))) {
			aios_submit_form(element, 'delete_404_log', {id: element.data('id')}, aios_trans.processing, null, function(response) {
				if ('success' === response.status) aios_detect_table_action(log_404_table_id, '', log_404_bulk_action_selector, log_404_table_tab, '', log_404_search, '', '', false);
			})
		}
	});
	// End of brute force ajax

	// Start of login whitelist suggests both IPv4 and IPv6
	if (jQuery('#aios_user_ip_maybe_also').length) {
		var selector = '#aios-ipify-ip-address';
		var ipfield = '#aios_user_ip_maybe_also';
		var getting_text = jQuery(ipfield).attr('getting_text');
		var ip_maybe = jQuery(ipfield).attr('ip_maybe');
		if ('v6' == ip_maybe) {
			var url = 'https://api64.ipify.org/?format=json';
		} else {
			var url = 'https://api.ipify.org/?format=json';
		}
		jQuery(selector).html(getting_text);
		jQuery.ajax({
			type: 'GET',
			dataType: 'json',
			url: url,
			success: function(response, status) {
				if (response.hasOwnProperty('ip') && response.ip != jQuery('#aiowps_user_ip').val()) {
					jQuery(ipfield).val(response.ip);
					jQuery(ipfield).removeClass('aio_hidden');
				} else {
					console.log(response);
				}
				jQuery(selector).html('');
			},
			error: function(response, status, error_code) {
				console.log(response);
				jQuery(selector).html('');
			}
		});
	}
	// End of login whitelist suggests both IPv4 and IPv6

	// Click the 'show/hide advanced options' button
	jQuery('button.button-link.aios-toggle-advanced-options').on('click', function() {
		if (!jQuery(this).hasClass('advanced-options-disabled')) {
			jQuery(this).toggleClass('opened');
		}
	});

	// Start of the new UI settings
	var initial_values = {};
	jQuery('.aiowps-actions').hide();

	/**
	 * Set active tab from URL
	 *
	 * @param {string} subtab
	 *
	 * @returns {void}
	 */
	function set_active_tab_from_url() {
		const url_params = new URLSearchParams(window.location.search);
		const subtab = url_params.get('subtab');
		if (subtab) {
			jQuery('.aiowps-rules li').removeClass('aiowps-active');
			jQuery(`.aiowps-rules li[data-template="${subtab}"]`).addClass('aiowps-active');
			jQuery('.aiowps-settings .postbox[data-template]').hide();
			jQuery(`.aiowps-settings .postbox[data-template="${subtab}"]`).show();
		} else {
			jQuery('.aiowps-settings .postbox[data-template]:first').show();
		}
	}

	/**
	 * Store initial values of the settings
	 *
	 * @returns {void}
	 */
	function store_values() {
		jQuery('.aiowps-settings :input').each(function() {
			if (jQuery(this).hasClass('aiowps-actions-ignore')) {
				return;
			}
			if (jQuery(this).is(':checkbox')) {
				initial_values[jQuery(this).attr('name')] = jQuery(this).is(':checked');
			} else {
				initial_values[jQuery(this).attr('name')] = jQuery(this).val();
			}
		});
	}

	// Store initial values on page load
	store_values();

	// Add change event listener to all inputs
	function check_input() {
		jQuery('.aiowps-settings :input').on('change', function() {
			var all_inputs_back_to_original = true;
			jQuery('.aiowps-settings :input').each(function() {
				var input_name = jQuery(this).attr('name');
				if (jQuery(this).hasClass('aiowps-actions-ignore')) {
					return true;
				}
				if (jQuery(this).is(':checkbox')) {
					if (jQuery(this).is(':checked') !== initial_values[input_name]) {
						all_inputs_back_to_original = false;
						return false;
					}
				} else {
					if (jQuery(this).val() !== initial_values[input_name]) {
						all_inputs_back_to_original = false;
						return false;
					}
				}
			});

			if (all_inputs_back_to_original) {
				jQuery('.aiowps-actions').hide();
			} else {
				jQuery('.aiowps-actions').show();
			}
		});
	}
	check_input();
	// Add click event listener to the button
	jQuery('.aiowps-actions :input').on('click', function() {
		// Hide the actions div
		jQuery('.aiowps-actions').hide();

		// Re-store the values
		store_values();
	});

	/**
	 * Initiates the download of a text file with the provided data and title.
	 *
	 * @param {string} data - The text data to be included in the file.
	 * @param {string} title - The name of the file to be downloaded.
	 */
	function aios_download_txt_file(data, title) {

		// Create a Blob containing the text data
		let blob = new Blob([data], {type: 'text/plain'});
		aios_download_file(blob, title);
	}

	/**
	 * Initiates the download of a CSV file with the provided data and title.
	 *
	 * @param {string} data - The CSV data to be included in the file.
	 * @param {string} title - The name of the file to be downloaded.
	 */
	function aios_download_csv_file(data, title) {

		// Create a Blob containing the CSV data
		let blob = new Blob([data], {type: 'text/csv'});
		aios_download_file(blob, title);
	}

	/**
	 * Triggers the download of a file using the provided Blob and filename.
	 *
	 * This function creates a temporary URL for the given Blob, then creates
	 * and triggers a download of the file with the specified title. After the
	 * download is initiated, it cleans up by removing the temporary element
	 * and revoking the Blob URL.
	 *
	 * @param {Blob} blob - The Blob object containing the file data to be downloaded.
	 * @param {string} title - The name of the file to be downloaded (including the file extension).
	 */
	function aios_download_file(blob, title) {

		// Create a temporary URL to the Blob
		let url = window.URL.createObjectURL(blob);

		// Create a temporary <a> element to trigger the download
		let a = document.createElement('a');
		a.href = url;
		a.download = title;
		document.body.appendChild(a);
		a.click();

		// Cleanup: remove the temporary <a> element and revoke the Blob URL
		document.body.removeChild(a);
		window.URL.revokeObjectURL(url);
	}

	// Add click event listener to rules
	jQuery('.aiowps-rules li').on('click', function() {
		jQuery('.aiowps-rules li').removeClass('aiowps-active');
		jQuery(this).addClass('aiowps-active');
		var template = jQuery(this).data('template');
		jQuery('.aiowps-settings .postbox[data-template]').hide();
		jQuery('.aiowps-settings .postbox[data-template]').each(function() {
			if (jQuery(this).data('template') === template) {
				jQuery(this).show();
				return false;
			}
		});
		const url_params = new URLSearchParams(window.location.search);
		const subtab_param = 'subtab=' + template;

		if (url_params.has('subtab')) {
			// If subtab parameter already exists, replace its value
			url_params.set('subtab', template);
		} else {
			// If subtab parameter doesn't exist
			if ("" === url_params.toString()) {
				// If there are no existing parameters, append subtab with '?'
				window.history.replaceState({}, '', window.location.pathname + '?' + subtab_param);
				return;
			} else {
				// If there are existing parameters
				const query_params = Array.from(url_params.entries());
				const new_params = query_params.map(param => param.join('=')).join('&');
				window.history.replaceState({}, '', window.location.pathname + '?' + new_params + '&' + subtab_param);
				return;
			}
		}

		window.history.replaceState({}, '', window.location.pathname + '?' + url_params.toString());
	});

	// Search for rules
	jQuery('.aiowps-search').on('keydown', function(event) {
		if ('Enter' === event.key) {
			event.preventDefault();
		}
	});

	jQuery('.aiowps-search').on('keyup', function(event) {
		if ('Enter' === event.key) {
			event.preventDefault();
		}
		var value = jQuery(this).val().toLowerCase();
		jQuery('.aiowps-rules li').each(function() {
			var template_value = jQuery(this).data('template').toLowerCase();
			var span_text = jQuery(this).find('span').text().toLowerCase();
			var th_match = false;
			jQuery('.form-table th').each(function() {
				var th_text = jQuery(this).text().toLowerCase();
				if (th_text.indexOf(value) > -1) {
					var template = jQuery(this).closest('.postbox').data('template');
					if (template === template_value) {
						th_match = true;
						return false;
					}
				}
			});
			if (template_value.indexOf(value) > -1 || span_text.indexOf(value) > -1 || th_match) {
				jQuery(this).show();
			} else {
				jQuery(this).hide();
			}
		});
	});

	jQuery('#aiowps-rule-search .clear-search').on('click', function() {
		jQuery('.aiowps-search').val('');
		jQuery('.aiowps-search').trigger('keyup');
	});

	set_active_tab_from_url();
	// End of the new UI settings

	function aiowps_show_spinner(element){
		jQuery(element + '_spinner').show();
	}

	function aiowps_hide_spinner(element) {
		jQuery(element + '_spinner').hide();
	}
	
	//toggle xmlrpc warning
	jQuery('#aiowps_enable_pingback_firewall').change(function() {
		if (jQuery(this).is(':checked')) {
			// When the checkbox is checked, remove the 'aio_hidden' class
			jQuery('.xmlrpc_warning_box').removeClass('aio_hidden');
		} else {
			// Optionally, if unchecked, you can add the class back (if needed)
			jQuery('.xmlrpc_warning_box').addClass('aio_hidden');
		}
	});

	// Start of WP REST API toggle whitelist setting handling
	jQuery('input[name=aiowps_disallow_unauthorized_rest_requests]').on('click', function() {
		jQuery('.aios-rest-white-list-options-panel').toggleClass('hidden');
	});
	// End of WP REST API toggle whitelist setting handling

	// Start of the copy report button handling
	jQuery('#copy-report').on('click', function(event) {
		var text = jQuery('#report-textarea').val();
		if (navigator.clipboard) {
			navigator.clipboard.writeText(text).then(function() {
				alert(aios_trans.copied);
				}, function() {
				deprecated_copy(text);
			});
		} else {
			deprecated_copy(text);
		}
	});
	// End of the copy report button handling

	// Start of the send report button handling
	jQuery('#send-report').on('click', function(e) {
		e.preventDefault();
		var report_email = jQuery('#report_email').val();
		var report_sections = jQuery('#report_sections').val();
		jQuery('#report-response').html('<p><span class="aiowps_spinner spinner">'+ aios_escape_html(aios_trans.processing) + '</span></p>');
		jQuery('#report-response .aiowps_spinner').addClass('visible');

		aios_send_command('send_report_email', {'report_email': report_email, 'report_sections': report_sections}, function(resp) {
			if (resp.hasOwnProperty('message')) {
				alert(resp.message);
			}
		});
	});
	// End of the send report button handling

	// Start of custom selector handling
	jQuery('.aios-selector').each(function () {
		const $selector = jQuery(this);
		const selector = {
			$selected: $selector.find('.aios-selector-selected'),
			$items_container: $selector.find('.aios-selector-items')
		};

		selector.$selected.val(selector.$selected.attr('value'));

		selector.$selected.on('click', function (e) {
			e.stopPropagation();
			selector.$items_container.toggle();
		});

		selector.$items_container.each(function () {
			jQuery(this).on('click', function (e) {
				e.stopPropagation(); // This is to prevent the dropdown from disappearing when clicking locked premium items.
			});
		}).find('div:not(.aios-selector-premium-item)').each(function () {
			const $selector_item = jQuery(this);

			$selector_item.val($selector_item.attr('value'));

			$selector_item.on('click', function (e) {
				e.stopPropagation();
				selector.$selected.find('.aios-selector-selected-title').text($selector_item.text());
				selector.$selected.val($selector_item.val());
				selector.$items_container.hide();

				if (selector.$selected.is('#bulk-action-selector-top')) {
					jQuery('#bulk-action-selector-bottom').find('.aios-selector-selected-title').text($selector_item.text());
					jQuery('#bulk-action-selector-bottom').val($selector_item.val());
				} else if (selector.$selected.is('#bulk-action-selector-bottom')) {
					jQuery('#bulk-action-selector-top').find('.aios-selector-selected-title').text($selector_item.text());
					jQuery('#bulk-action-selector-top').val($selector_item.val());
				}
			});
		});
	});

	jQuery(document).on('click', function () {
		jQuery('.aios-selector-items').hide();
	});
	// End of custom selector handling
});
