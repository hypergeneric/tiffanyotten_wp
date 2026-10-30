<?php if (!defined('ABSPATH')) die('No direct access.'); ?>

<div id="tfa_trusted_devices_box_inner">

	<p><?php esc_html_e('Trusted devices are devices which have previously logged in with a second factor, belonging to users who have been permitted to mark devices as trusted, and for which the user checked the checkbox on the login form to trust the device.', 'all-in-one-wp-security-and-firewall'); ?></p>

	<?php

	global $current_user;

	$trusted_devices = $this->user_get_trusted_devices($current_user->ID);

	if (empty($trusted_devices)) {
		echo '<em>'.esc_html__('(none)', 'all-in-one-wp-security-and-firewall').'</em>';
	}

	foreach ($trusted_devices as $device_id => $device) {
		
		if (!isset($device['token']) || '' == $device['token']) continue;
		
		$user_agent = empty($device['user_agent']) ? __('(unspecified)', 'all-in-one-wp-security-and-firewall'): $device['user_agent'];
		
		echo '<span class="simbatfa_trusted_device">';
		printf(
			/* Translators: 1: User agent string 2: IP address 3: Date */
			esc_html__('User agent %1$s logged in from IP address %2$s and is trusted until %3$s', 'all-in-one-wp-security-and-firewall'),
			'<strong>'.esc_html($user_agent).'</strong>',
			'<strong><a target="_blank" href="https://ipinfo.io/'.esc_attr($device['ip']).'">'.esc_html($device['ip']).'</a></strong>',
			'<strong>'.esc_html(date_i18n(get_option('time_format').' '.get_option('date_format'), $device['until'])).'</strong>'
		);
		echo ' - <a href="#" class="simbatfa-trust-remove" data-trusted-device-id="'.esc_attr($device_id).'">'.esc_html__('Remove trust', 'all-in-one-wp-security-and-firewall').'</a></span><br>';
		
	}

	?>

</div>
