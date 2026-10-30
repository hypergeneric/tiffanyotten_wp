<?php
if (!defined('ABSPATH')) die('No direct access.');
?>
<div class="postbox">
	<h3 class="hndle"><?php /* translators: %s: TeamUpdraft.com site name (as bold HTML). */ echo sprintf(esc_html__('Connect to %s', 'all-in-one-wp-security-and-firewall'), '<strong>' . esc_html__('TeamUpdraft.com', 'all-in-one-wp-security-and-firewall') . '</strong>'); ?></h3>
	<div class="inside" id="aios_updraft_form_container">
		<?php
		if ('' === $updraft_redirect_token) {
			$aio_wp_security->include_template('wp-admin/settings/partials/updraft-login-form.php', false, array());
		} else {
			$aio_wp_security->include_template('wp-admin/settings/partials/updraft-login-connected.php', false, array(
				'updraft_connected_email' => $updraft_connected_email,
			));
		}
		?>
	</div>
</div>
