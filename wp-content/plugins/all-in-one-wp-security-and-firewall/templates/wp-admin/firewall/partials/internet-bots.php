<?php if (!defined('ABSPATH')) die('Access denied.'); ?>
<div class="postbox aio_hidden" data-template="internet-bots">
	<h3 class="hndle"><?php esc_html_e('Internet bot settings', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div class="aio_blue_box">
			<p><strong>
			<?php
				printf(
					/* translators: 1: link opening tag. 2: closing link tag */
					esc_html__('%1$sWhat is an Internet Bot%2$s?', 'all-in-one-wp-security-and-firewall'),
					'<a href="http://en.wikipedia.org/wiki/Internet_bot" target="_blank">',
					'</a>'
				);
			?>
			</strong></p>
			<p>
				<?php esc_html_e('A bot is a piece of software which runs on the Internet and performs automatic tasks.', 'all-in-one-wp-security-and-firewall'); ?>
				<?php esc_html_e('For example when Google indexes your pages it uses bots to achieve this task.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
			<p>
				<?php esc_html_e('A lot of bots are legitimate and non-malicious but not all bots are good and often you will find some which try to impersonate legitimate bots such as "Googlebot" but in reality they have nothing to do with Google at all.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
			<p>
				<?php esc_html_e('Although most of the bots out there are relatively harmless sometimes website owners want to have more control over which bots they allow into their site.', 'all-in-one-wp-security-and-firewall'); ?>
			</p>
		</div>
		<?php $aio_wp_security->include_template('wp-admin/firewall/partials/fake-googlebots.php', false, compact('aiowps_block_fake_googlebots')); ?>
		<?php $aio_wp_security->include_template('wp-admin/firewall/partials/blank-ref-and-useragent.php', false, compact('aiowps_ban_post_blank_headers')); ?>
	</div>
</div>
