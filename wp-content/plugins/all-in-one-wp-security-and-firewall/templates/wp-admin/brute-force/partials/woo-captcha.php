<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="woo-captcha">
	<h3 class="hndle"><?php esc_html_e('WooCommerce forms CAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="woo-login-captcha-badge">
		<?php
		//Display security info badge
		global $aiowps_feature_mgr;
		$aiowps_feature_mgr->output_feature_details_badge("woo-login-captcha");
		?>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable CAPTCHA on WooCommerce login form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(
							__('Enable this if you want to insert CAPTCHA on a WooCommerce login form.', 'all-in-one-wp-security-and-firewall'),
							'aiowps_enable_woo_login_captcha',
							'1' == $aiowps_enable_woo_login_captcha
						);
						?>
					</div>
				</td>
			</tr>
		</table>
		<hr>
		<div id="woo-lostpassword-captcha-badge">
		<?php
		$aiowps_feature_mgr->output_feature_details_badge("woo-lostpassword-captcha");
		?>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable CAPTCHA on WooCommerce lost password form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(
							__('Enable this if you want to insert CAPTCHA on a WooCommerce lost password form.', 'all-in-one-wp-security-and-firewall'),
							'aiowps_enable_woo_lostpassword_captcha',
							'1' == $aiowps_enable_woo_lostpassword_captcha
						);
						?>
					</div>
				</td>
			</tr>
		</table>
		<hr>
		<div id="woo-register-captcha-badge">
		<?php
		$aiowps_feature_mgr->output_feature_details_badge("woo-register-captcha");
		?>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable CAPTCHA on WooCommerce registration form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(
							__('Enable this if you want to insert CAPTCHA on a WooCommerce registration form.', 'all-in-one-wp-security-and-firewall'),
							'aiowps_enable_woo_register_captcha',
							'1' == $aiowps_enable_woo_register_captcha
						);
						?>
					</div>
				</td>
			</tr>
		</table>
		<hr>
		<div id="woo-checkout-captcha-badge">
		<?php
		$aiowps_feature_mgr->output_feature_details_badge("woo-checkout-captcha");
		?>
		</div>
		<table class="form-table">
			<div class="<?php echo $is_enabled_guest_checkout ? "aio_blue_box" : "aio_red_box"; ?>">
				<p>
					<?php
					if (!$is_enabled_guest_checkout) {
						$checkout_checkbox_attributes = array('disabled' => 'disabled');
						esc_html_e('Guest checkout is not enabled in your WooCommerce settings.', 'all-in-one-wp-security-and-firewall');
						echo ' ';
						esc_html_e('Therefore, the setting below is not relevant.', 'all-in-one-wp-security-and-firewall');
					} else {
						$checkout_checkbox_attributes = array();
						esc_html_e('Guest checkout allows a customer to place an order without an account or being logged in.', 'all-in-one-wp-security-and-firewall');
					}
					?>
				</p>
			</div>
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable CAPTCHA on the WooCommerce checkout page', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(
							__('Enable this if you want to insert a CAPTCHA on the WooCommerce checkout page when a guest places an order.', 'all-in-one-wp-security-and-firewall'),
							'aiowps_enable_woo_checkout_captcha',
							'1' == $aiowps_enable_woo_checkout_captcha,
							$checkout_checkbox_attributes
						);
						?>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>