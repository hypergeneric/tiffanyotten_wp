<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="proxy-comment">
		<h3 class="hndle"><?php esc_html_e('Proxy comment posting', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<div class="inside">
			<div id="firewall-forbid-proxy-comments-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-forbid-proxy-comments"); ?>
			</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Forbid proxy comment posting', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you want to forbid proxy comment posting.', 'all-in-one-wp-security-and-firewall'), 'aiowps_forbid_proxy_comments', $aiowps_forbid_proxy_comments);

						AIOWPSecurity_Utility_UI::tooltip(array(
							__('This setting will deny any requests that use a proxy server when posting comments.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('By forbidding proxy comments you are in effect eliminating some spam and other proxy requests.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
		</div></div>
