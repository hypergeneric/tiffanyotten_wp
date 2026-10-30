<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="aio_blue_box">
	<p><?php echo esc_html__('You are successfully connected to TeamUpdraft.com.', 'all-in-one-wp-security-and-firewall') . ' ' . esc_html__('You can now use the features that connect to the TeamUpdraft cloud.', 'all-in-one-wp-security-and-firewall'); ?></p>
</div>
<div class="aio_green_box">
	<p><?php /* translators: %s: Connected email address (as bold HTML). */ echo sprintf(esc_html__('You are connected to TeamUpdraft.com with %s', 'all-in-one-wp-security-and-firewall'), '<strong>' . esc_html($updraft_connected_email) . '</strong>'); ?></p>
</div>
<form action="" method="POST" id="aios-team-updraft-logout-form">
	<?php submit_button(__('Disconnect', 'all-in-one-wp-security-and-firewall'), 'primary', 'aios_team_updraft_logout', false); ?>
</form>
