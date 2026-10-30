<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="other-plugins">
	<h3 class="hndle"><?php esc_html_e('Other forms CAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<?php if (AIOWPSecurity_Utility::is_buddypress_plugin_active()) { ?>
	<div class="inside">
		<div id="bp-register-captcha-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("bp-register-captcha"); ?>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Enable CAPTCHA on BuddyPress registration form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
					<?php
						AIOWPSecurity_Utility_UI::setting_checkbox(
							sprintf(
								/* translators: %s: BuddyPress (Plugin name) */
								esc_html__('Enable this if you want to insert a CAPTCHA field on the %s registration forms.', 'all-in-one-wp-security-and-firewall'),
								'BuddyPress'
							),
							'aiowps_enable_bp_register_captcha',
							'1' == $aiowps_enable_bp_register_captcha
						);
					?>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php } ?>
	<?php if (AIOWPSecurity_Utility::is_bbpress_plugin_active()) { ?>
		<div class="inside">
			<div id="bbp-new-topic-captcha-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("bbp-new-topic-captcha"); ?>
			</div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Enable CAPTCHA on bbPress new topic form', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
						<?php
							AIOWPSecurity_Utility_UI::setting_checkbox(
								sprintf(
									/* translators: %s: bbPress (Plugin name) */
									esc_html__('Enable this if you want to insert a CAPTCHA field on the %s new topic forms.', 'all-in-one-wp-security-and-firewall'),
									'bbPress'
								),
								'aiowps_enable_bbp_new_topic_captcha',
								'1' == $aiowps_enable_bbp_new_topic_captcha
							);
						?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>
	<?php if (AIOWPSecurity_Utility::is_contact_form_7_plugin_active()) { ?>
		<div class="inside">
			<div id="contact-form-7-captcha-badge">
				<?php $aiowps_feature_mgr->output_feature_details_badge("contact-form-7-captcha"); ?>
			</div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
					<?php
						printf(
							/* translators: %s: Contact Form 7 (Plugin name) */
							esc_html__('Enable CAPTCHA on %s', 'all-in-one-wp-security-and-firewall'),
							'Contact Form 7'
						);
					?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php
							AIOWPSecurity_Utility_UI::setting_checkbox(
								sprintf(
									/* translators: %s: Contact Form 7 (Plugin name) */
									esc_html__('Enable this if you want to insert a CAPTCHA field on %s forms.', 'all-in-one-wp-security-and-firewall'),
									'Contact Form 7'
								),
								'aiowps_enable_contact_form_7_captcha',
								'1' == $aiowps_enable_contact_form_7_captcha
							);
							AIOWPSecurity_Utility_UI::tooltip(array(
								sprintf(
									/* translators: %s: AIOS (Plugin name) */
									__('%s will automatically try to insert a CAPTCHA field before the form\'s submit button', 'all-in-one-wp-security-and-firewall'),
									'AIOS'
								),
								'',
								sprintf(
									/* translators: %s: Contact Form 7 (Plugin name) */
									__('For the exact placement of the CAPTCHA you can use the following shortcode in your %s template', 'all-in-one-wp-security-and-firewall'),
									'Contact Form 7'
								),
								'['.esc_html(AIOWPSEC_CAPTCHA_SHORTCODE).']',
								'',
								sprintf(
									/* translators: 1: Contact Form 7 (Plugin name) 2: 5.0 */
									__('This feature requires %1$s version %2$s or greater', 'all-in-one-wp-security-and-firewall'),
									'Contact Form 7',
									'5.0'
								),
								'',
								sprintf(
									/* translators: 1: Contact Form 7 (Plugin name) 2: 5.6 */
									__('The validation message will be displayed only when using %1$s version %2$s or greater', 'all-in-one-wp-security-and-firewall'),
									'Contact Form 7',
									'5.6'
								),
							));
							?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>
</div>
