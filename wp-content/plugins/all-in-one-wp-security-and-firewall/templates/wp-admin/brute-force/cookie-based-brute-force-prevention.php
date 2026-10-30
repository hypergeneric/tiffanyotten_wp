<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<h2><?php esc_html_e('Brute force prevention firewall settings', 'all-in-one-wp-security-and-firewall'); ?></h2>
<div class="aio_blue_box">
	<p>
		<?php esc_html_e('A Brute Force Attack is when a hacker tries many combinations of usernames and passwords until they succeed in guessing the right combination.', 'all-in-one-wp-security-and-firewall'); ?>
		<br>
		<?php esc_html_e('Due to the fact that at any one time there may be many concurrent login attempts occurring on your site via malicious automated robots, this also has a negative impact on your server\'s memory and performance.', 'all-in-one-wp-security-and-firewall'); ?>
		<br>
		<?php esc_html_e('The features in this tab will stop the majority of brute force login attacks thus providing even better protection for your WP login page.', 'all-in-one-wp-security-and-firewall'); ?>
	</p>
</div>
<div class="aio_yellow_box">
	<p><a href="https://teamupdraft.com/docs/aios/how-to/protecting-your-website-against-brute-force-attacks/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=tutorial-on-brute-force-prevention&utm_creative_format=text#how-to-use-cookie-based-brute-force-login-attack-prevention-feature" target="_blank">
	<?php esc_html_e('Read our tutorial on how to use the cookie-based brute force prevention feature', 'all-in-one-wp-security-and-firewall'); ?></a></p>	
</div>
<?php

	if (defined('AIOS_DISABLE_COOKIE_BRUTE_FORCE_PREVENTION') && AIOS_DISABLE_COOKIE_BRUTE_FORCE_PREVENTION) {
		$aio_wp_security->include_template('notices/cookie-based-brute-force-prevention-disabled.php');
	}
?>
<div id="aios-brute-force-info-box"></div>
<div class="postbox">
	<h3 class="hndle"><?php esc_html_e('Cookie based brute force login prevention', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="firewall-enable-brute-force-attack-prevention-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("firewall-enable-brute-force-attack-prevention"); ?>
		</div>
		<form action="" id="aios-cookie-based-settings-form">
			<div class="aio_orange_box">
				<p>
				<?php /* translators: %s: Notes link. */ ?>
				<?php echo esc_html__('This feature can lock you out of admin if it doesn\'t work correctly on your site.', 'all-in-one-wp-security-and-firewall').' ' . sprintf(esc_html__('Before activating this feature, please read the following %s.', 'all-in-one-wp-security-and-firewall'), '<a href="https://teamupdraft.com/docs/aios/how-to/protecting-your-website-against-brute-force-attacks/?utm_source=aios-plugin&utm_medium=referral&utm_campaign=paac&utm_content=you-must-read-this-to-activate-brute-force-login-prevention-feature&utm_creative_format=text#using-the-intermediate-and-advanced-features-in-aios" target="_blank">' . esc_html__('message', 'all-in-one-wp-security-and-firewall') . '</a>'); ?>
				</p>
			</div>
			<div id="cookie-test-result-div">
			</div>
			<div id="aios-perform-cookie-test-div">
				<?php
					$cookie_test_value = $aiowps_cookie_test_success;

					$disable_brute_force_feature_input = true;
					// If the cookie test is successful or if the feature is already enabled then go ahead as normal
					if ('1' == $cookie_test_value || '1' == $aiowps_enable_brute_force_attack_prevention) {
						$disable_brute_force_feature_input = false;
					} else {
						$aio_wp_security->include_template('wp-admin/brute-force/partials/cookie-test-container.php', false);
					}
					$disable_brute_force_sub_fields = !$aiowps_enable_brute_force_attack_prevention;
				?>
			</div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Enable brute force attack prevention', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php
							AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you want to protect your login page from a brute force attack.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_brute_force_attack_prevention', $aiowps_enable_brute_force_attack_prevention);
							
							AIOWPSecurity_Utility_UI::tooltip(array(
								__('This feature will deny access to your WordPress login page for all people except those who have a special cookie in their browser.', 'all-in-one-wp-security-and-firewall'),
								'',
								__('To use this feature do the following:', 'all-in-one-wp-security-and-firewall'),
								'',
								__('1) Enable the checkbox.', 'all-in-one-wp-security-and-firewall'),
								__('2) Enter a secret word consisting of alphanumeric characters which will be difficult to guess.', 'all-in-one-wp-security-and-firewall') . ' ' .
								__('The secret word will be useful whenever you need to know the special URL which you will use to access the login page (see point below).', 'all-in-one-wp-security-and-firewall'),
								__('3) You will then be provided with a special login URL.', 'all-in-one-wp-security-and-firewall') . ' ' .
								__('You will need to use this URL to login to your WordPress site instead of the usual login URL.', 'all-in-one-wp-security-and-firewall'),
								'',
								__('Note: The system will deposit a special cookie in your browser which will allow you access to the WordPress administration login page.', 'all-in-one-wp-security-and-firewall'),
								'',
								__('Any person trying to access your login page who does not have the special cookie in their browser will be automatically blocked.', 'all-in-one-wp-security-and-firewall'),
							));
							?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="aiowps_brute_force_secret_word"><?php esc_html_e('Secret word', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
					<td><input id="aiowps_brute_force_secret_word" type="text" size="40" name="aiowps_brute_force_secret_word" value="<?php echo esc_attr($aiowps_brute_force_secret_word); ?>"<?php disabled($disable_brute_force_sub_fields); ?>>
					<span class="description"><?php echo esc_html__('Choose a secret word consisting of alphanumeric characters which you can use to access your special URL.', 'all-in-one-wp-security-and-firewall') . ' ' . esc_html__('You are highly encouraged to choose a word which will be difficult to guess.', 'all-in-one-wp-security-and-firewall'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="aiowps_cookie_based_brute_force_redirect_url"><?php esc_html_e('Re-direct URL', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
					<td>
						<input id="aiowps_cookie_based_brute_force_redirect_url" type="text" size="40" name="aiowps_cookie_based_brute_force_redirect_url" value="<?php echo esc_attr($aiowps_cookie_based_brute_force_redirect_url); ?>" <?php disabled($disable_brute_force_sub_fields); ?> />
						<span class="description">
							<?php
							esc_html_e('Specify a URL to redirect a hacker to when they try to access your WordPress login page.', 'all-in-one-wp-security-and-firewall');
							?>
						</span>
						<?php
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('The URL specified here can be any site\'s URL and does not have to be your own.', 'all-in-one-wp-security-and-firewall'),
							__('This field will default to: http://127.0.0.1 if you do not enter a value.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Useful Tip:', 'all-in-one-wp-security-and-firewall'),
							__('It\'s a good idea to not redirect attempted brute force login attempts to your site because it increases the load on your server.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Redirecting a hacker or malicious bot back to "http://127.0.0.1" is ideal because it deflects them back to their own local host and puts the load on their server instead of yours.', 'all-in-one-wp-security-and-firewall'),
						), 6);
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e('My site has posts or pages which are password protected', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php
							AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you are using the native WordPress password protection feature for some or all of your blog posts or pages.', 'all-in-one-wp-security-and-firewall'), 'aiowps_brute_force_attack_prevention_pw_protected_exception', '1' == $aiowps_brute_force_attack_prevention_pw_protected_exception);
							
							AIOWPSecurity_Utility_UI::tooltip(array(
								__('In the cases where you are protecting some of your posts or pages using the in-built WordPress password protection feature, a few extra lines of directives and exceptions need to be added so that people trying to access pages are not automatically blocked.', 'all-in-one-wp-security-and-firewall'),
								'',
								__('By enabling this checkbox, the plugin will add the necessary rules and exceptions so that people trying to access these pages are not automatically blocked.', 'all-in-one-wp-security-and-firewall'),
								'',
								__('Helpful Tip:', 'all-in-one-wp-security-and-firewall'),
								__('If you do not use the WordPress password protection feature for your posts or pages then it is highly recommended that you leave this checkbox disabled.', 'all-in-one-wp-security-and-firewall'),
							));
							?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e('My site has a theme or plugins which use AJAX', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php
							AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if your site uses AJAX functionality.', 'all-in-one-wp-security-and-firewall'), 'aiowps_brute_force_attack_prevention_ajax_exception', '1' == $aiowps_brute_force_attack_prevention_ajax_exception);
							
							AIOWPSecurity_Utility_UI::tooltip(array(
								__('In the cases where your WordPress installation has a theme or plugin that uses AJAX, a few extra lines of directives and exceptions need to be added to prevent AJAX requests from being automatically blocked by the brute force prevention feature.', 'all-in-one-wp-security-and-firewall'),
								'',
								__('By enabling this checkbox, the plugin will add the necessary rules and exceptions so that AJAX operations will work as expected.', 'all-in-one-wp-security-and-firewall'),
							));
							?>
						</div>
					</td>
				</tr>
			</table>
			<?php
				$other_attributes = $disable_brute_force_feature_input ? array('disabled' => 'disabled') : array();
				submit_button(esc_html__('Save feature settings', 'all-in-one-wp-security-and-firewall'), 'primary', 'aiowps_apply_cookie_based_bruteforce_firewall', false, $other_attributes);
			?>
		</form>
	</div>
</div>