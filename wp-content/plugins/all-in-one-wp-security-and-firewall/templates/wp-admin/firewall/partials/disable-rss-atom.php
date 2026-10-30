<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="disable-rss-atom">
			<h3 class="hndle"><?php esc_html_e('Disable WordPress RSS and ATOM feeds', 'all-in-one-wp-security-and-firewall'); ?></h3>
			<div class="inside">
				<div id="firewall-disable-rss-and-atom-badge">
					<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-disable-rss-and-atom"); ?>
				</div>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Disable RSS and ATOM feeds:', 'all-in-one-wp-security-and-firewall'); ?></th>
						<td>
							<div class="aiowps_switch_container">
								<?php
								AIOWPSecurity_Utility_UI::setting_checkbox(__('Enable this if you do not want users using feeds.', 'all-in-one-wp-security-and-firewall') . ' ' .__('RSS and ATOM feeds are used to read content from your site.', 'all-in-one-wp-security-and-firewall'), 'aiowps_disable_rss_and_atom_feeds', '1' == $aiowps_disable_rss_and_atom_feeds);
								
								AIOWPSecurity_Utility_UI::tooltip(array(
									__('Most users will want to share their site content widely, but some may prefer to prevent automated site scraping.', 'all-in-one-wp-security-and-firewall'),
								));
								
								echo '<span style="font-size:13px;vertical-align:middle">';
								printf(
									/* translators: 1: Opening link tag 2: closing link tag */
									esc_html__('For more information, check the %1$sdocumentation%2$s', 'all-in-one-wp-security-and-firewall'),
									'<a target="_blank" href="https://teamupdraft.com/docs/aios/how-to/disabling-rss-and-atom-feeds/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=php-rules-faq&utm_creative_format=text">',
									'</a>',
								);
								echo '<span>';
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
