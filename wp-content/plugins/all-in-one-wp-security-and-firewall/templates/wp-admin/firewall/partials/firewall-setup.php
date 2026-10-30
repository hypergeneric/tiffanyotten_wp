<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Firewall setup', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div class="aio_blue_box">
			<p>
				<?php esc_html_e('This option allows you to set up or downgrade the firewall.', 'all-in-one-wp-security-and-firewall'); ?><br>
				<?php esc_html_e('We recommend you set up the firewall for greater protection, but if for whatever reason you wish to downgrade the firewall, then you can do so here.', 'all-in-one-wp-security-and-firewall'); ?><br>
			</p>
		</div>
		<table class="form-table">
			<tr valign="row">
				<th scope="row"><?php esc_html_e('Firewall', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div id="aiowps-firewall-status-container" style="display: inline">
						<?php AIOWPSecurity_Utility_Firewall::is_firewall_setup() ? $aio_wp_security->include_template('wp-admin/firewall/partials/firewall-downgrade-button.php') : $aio_wp_security->include_template('wp-admin/firewall/partials/firewall-set-up-button.php'); ?>
					</div>
					<?php
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('Set up firewall:', 'all-in-one-wp-security-and-firewall'),
							__('This will attempt to set up the firewall in order to give you the highest level of protection it has to offer.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Downgrade firewall', 'all-in-one-wp-security-and-firewall'),
							__('This will undo the changes performed by the set-up mechanism.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('The firewall will still be active if it is downgraded or not set up, but you will have reduced protection.', 'all-in-one-wp-security-and-firewall'),
						), 6);
					?>
				</td>
			</tr>
		</table>
	</div>
</div>