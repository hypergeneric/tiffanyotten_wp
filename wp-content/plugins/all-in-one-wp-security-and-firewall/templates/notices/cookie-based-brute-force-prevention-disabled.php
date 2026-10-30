<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<div class="error">
	<h3><?php echo esc_html__('Cookie based brute force login prevention currently disabled', 'all-in-one-wp-security-and-firewall');?></h3>
	<p>
		<?php
		echo sprintf(
			/* Translators: wp-config.php path */
			esc_html__('Cookie based brute force login prevention is currently disabled via the AIOS_DISABLE_COOKIE_BRUTE_FORCE_PREVENTION constant (which is most likely to be defined in your %s)', 'all-in-one-wp-security-and-firewall'),
			esc_html(AIOWPSecurity_Utility_File::get_home_path()) . 'wp-config.php'
		);
		?>
	</p>
</div>