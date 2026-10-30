<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="xmlrpc-pingback-protection">
	<h3 class="hndle"><?php esc_html_e('WordPress XMLRPC and pingback vulnerability protection', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="firewall-pingback-rules-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-pingback-rules"); ?>
		</div>
		<?php $aio_wp_security->include_template('wp-admin/firewall/partials/xmlrpc-warning-notice.php'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Completely block access to XMLRPC', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you are not using the WP XML-RPC functionality and you want to completely block external access to XMLRPC.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_pingback_firewall', $aiowps_enable_pingback_firewall);
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('This setting will disable access to the WordPress xmlrpc.php file which is responsible for the XML-RPC functionality in WordPress.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Hackers can exploit various vulnerabilities in the WordPress XML-RPC API in a number of ways such as:', 'all-in-one-wp-security-and-firewall'),
							'',
							__('1) Denial of Service (DoS) attacks', 'all-in-one-wp-security-and-firewall'),
							__('2) Hacking internal routers.', 'all-in-one-wp-security-and-firewall'),
							__('3) Scanning ports in internal networks to get info from various hosts.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Apart from the security protection benefit, this feature may also help reduce load on your server, particularly if your site currently has a lot of unwanted traffic hitting the XML-RPC API on your installation.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Note: You should only enable this feature if you are not currently using the XML-RPC functionality on your WordPress installation.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Leave this feature disabled and use the feature below if you want pingback protection but you still need XMLRPC.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Disable pingback functionality from XMLRPC', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('If you use Jetpack or WP iOS or other apps which need WP XML-RPC functionality then check this.', 'all-in-one-wp-security-and-firewall') . ' ' . esc_html__('This will enable protection against WordPress pingback vulnerabilities.', 'all-in-one-wp-security-and-firewall'), 'aiowps_disable_xmlrpc_pingback_methods', '1' == $aiowps_disable_xmlrpc_pingback_methods);
						
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('Note: If you use Jetpack or the Wordpress iOS or other apps then you should enable this feature but leave the "Completely Block Access To XMLRPC" checkbox unchecked.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('The feature will still allow XMLRPC functionality on your site but will disable the pingback methods.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('This feature will also remove the "X-Pingback" header if it is present.', 'all-in-one-wp-security-and-firewall'),
						));
						?>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
