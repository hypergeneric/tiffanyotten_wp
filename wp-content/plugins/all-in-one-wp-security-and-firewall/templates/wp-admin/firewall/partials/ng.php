<?php if (!defined('ABSPATH')) die('Access denied.'); ?>

<div id="aios-6g-firewall-settings-container" class="postbox aio_hidden" data-template="ng">
	<h3 class="hndle"><?php esc_html_e('6G firewall settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="firewall-enable-6g-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-enable-6g"); ?>
		</div>
		<div class="aio_blue_box">
			<p>
			<?php
			printf(
				/* translators: 1: 6G opening link 2: closing link tag 3: 5G opening link 4: Perishable Press opening link */
				esc_html__('This feature allows you to activate the %1$s6G%2$s (or legacy %3$s5G%2$s) firewall security protection rules designed and produced by %4$sPerishable Press%2$s.', 'all-in-one-wp-security-and-firewall'),
				'<a href="http://perishablepress.com/6g/" target="_blank">',
				'</a>',
				'<a href="http://perishablepress.com/5g-blacklist-2013/" target="_blank">',
				'<a href="http://perishablepress.com/" target="_blank">'
			);
			?>
			</p>
			<p>
				<?php esc_html_e('The 6G firewall is an updated and improved version of the 5G firewall that is PHP-based and doesn\'t use a .htaccess file.', 'all-in-one-wp-security-and-firewall'); ?>
				<?php esc_html_e('If you have the 5G firewall active, you might consider activating the 6G firewall instead.', 'all-in-one-wp-security-and-firewall'); ?>			
			</p>
			<p>
				<?php esc_html_e('The 6G firewall is a simple, flexible blacklist that helps reduce the number of malicious URL requests that hit your website.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
			<p>
				<?php esc_html_e('The added advantage of applying the 6G firewall to your site is that it has been tested and confirmed by the people at PerishablePress.com to be an optimal and least disruptive set of security rules for general WP sites.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable 6G firewall protection', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this to apply the recommended 6G firewall protection.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_6g_firewall', '1' == $ng_settings['aiowps_enable_6g_firewall']);
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('This setting will implement the 6G security firewall protection mechanisms on your site which include the following things:', 'all-in-one-wp-security-and-firewall'),
							'',
							__('1) Block forbidden characters commonly used in exploitative attacks.', 'all-in-one-wp-security-and-firewall'),
							__('2) Block malicious encoded URL characters such as the ".css(" string.', 'all-in-one-wp-security-and-firewall'),
							__('3) Guard against the common patterns and specific exploits in the root portion of targeted URLs.', 'all-in-one-wp-security-and-firewall'),
							__('4) Stop attackers from manipulating query strings by disallowing illicit characters.', 'all-in-one-wp-security-and-firewall'),
							__('....and much more.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
		<button type="button" class="button button-link aios-toggle-advanced-options<?php if ($ng_settings['advanced_options_disabled']) echo ' advanced-options-disabled';?>">
				<span class="text">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
					<span class="aios-toggle-advanced-options__text-show"><?php esc_html_e('Show advanced options', 'all-in-one-wp-security-and-firewall'); ?></span>
					<span class="aios-toggle-advanced-options__text-hide"><?php esc_html_e('Hide advanced options', 'all-in-one-wp-security-and-firewall'); ?></span>
				</span>
		</button>

		<div class="aios-advanced-options-panel">
			<?php $aio_wp_security->include_template('wp-admin/firewall/partials/advanced-settings-6g.php', false, array('methods' => $ng_settings['methods'], 'blocked_query' => $ng_settings['blocked_query'], 'blocked_request' => $ng_settings['blocked_request'], 'blocked_referrers' => $ng_settings['blocked_referrers'], 'blocked_agents' => $ng_settings['blocked_agents'], 'block_request_methods' => $ng_settings['block_request_methods'])); ?>
		</div>
	</div>
</div>