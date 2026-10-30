<?php if (!defined('ABSPATH')) die('No direct access.');

do_action('aiowps_before_updraft_connect_section');

$aio_wp_security->include_template('wp-admin/settings/partials/updraft-connect.php', false, array(
	'updraft_redirect_token'  => $updraft_redirect_token,
	'updraft_connected_email' => $updraft_connected_email,
));
