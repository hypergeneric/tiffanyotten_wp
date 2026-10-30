<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="postbox" id="aios-auto-spam-block-container">
	<h3 class="hndle"><?php esc_html_e('Auto block spammer IPs', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="auto-block-spam-ips-badge">
			<?php $aiowps_feature_mgr->output_feature_details_badge("auto-block-spam-ips"); ?>
		</div>
		<?php
		if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_autoblock_spam_ip') && '1' != $aio_wp_security->configs->get_value('aiowps_enable_spambot_detecting')) {
		?>
		<div class="aio_orange_box" id="message">
			<p><strong>
				<?php
				printf(
					/* translators: 1: Opening link tag. 2: closing link tag */
					esc_html__('This feature has detected that %1$sspam comment detection%2$s is not active.', 'all-in-one-wp-security-and-firewall'),
					'<a href="admin.php?page='.esc_attr(AIOWPSEC_SPAM_MENU_SLUG).'&tab=comment-spam">',
					'</a>'
				);
				echo ' ';
				esc_html_e('It is highly recommended that you activate to make the most of this feature.', 'all-in-one-wp-security-and-firewall');
				?>
			</strong></p>
		</div>
		<?php }	?>
		<form action="" id="aios-auto-spam-block-form" method="POST">
			<div class="aio_blue_box">
				<?php
				echo '<p>'.esc_html__('This feature allows you to automatically and permanently block IP addresses which have exceeded a certain number of spam comments.', 'all-in-one-wp-security-and-firewall').'</p>'.'<p>'.esc_html__('Comments are considered spam if the "Spam comment detection" feature is enabled or an administrator manually marks a comment as "spam" from the WordPress Comments menu.', 'all-in-one-wp-security-and-firewall').'</p>';
				?>
			</div>
			<div id="aios-blocked-comments-output">
				<?php if (!empty($min_block_comments)) { ?>
					<div class="aio_yellow_box">
					<?php if (empty($total_results_count)) { ?>
						<p><strong><?php esc_html_e('You currently have no IP addresses permanently blocked due to spam.', 'all-in-one-wp-security-and-firewall'); ?></strong></p>
					<?php
					} else {
					?>
						<p><strong>
							<?php esc_html_e('Spammer IPs added to permanent block list today:', 'all-in-one-wp-security-and-firewall'); ?>
							<?php echo esc_html($todays_blocked_count); ?>
						</strong></p>
						<hr>
						<p><strong>
							<?php esc_html_e('All time total:', 'all-in-one-wp-security-and-firewall'); ?>
							<?php echo esc_html($total_results_count); ?>
						</strong></p>
						<p>
							<a class="button" href="admin.php?page=<?php echo esc_attr(AIOWPSEC_MAIN_MENU_SLUG); ?>&tab=permanent-block" target="_blank">
							<?php esc_html_e('View blocked IPs', 'all-in-one-wp-security-and-firewall'); ?>
							</a>
						</p>
					<?php } ?>
					</div>
				<?php }	?>
			</div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Enable auto block of spam comment IPs', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
						<div class="aiowps_switch_container">
							<?php AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this if you want this plugin to automatically block IP addresses which submit spam comments.', 'all-in-one-wp-security-and-firewall'), 'aiowps_enable_autoblock_spam_ip', '1' == $comment_spam_data['aiowps_enable_autoblock_spam_ip']); ?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="aiowps_spam_ip_min_comments_block"><?php esc_html_e('Minimum number of spam comments', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
					<td><input id="aiowps_spam_ip_min_comments_block" type="number" min="1" name="aiowps_spam_ip_min_comments_block" value="<?php echo esc_attr($comment_spam_data['aiowps_spam_ip_min_comments_block']); ?>" />
						<span class="description"><?php esc_html_e('Specify the minimum number of spam comments for an IP address before it is permanently blocked.', 'all-in-one-wp-security-and-firewall'); ?></span>
						<?php
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('Example 1: Setting this value to "1" will block ALL IP addresses which were used to submit at least one spam comment.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Example 2: Setting this value to "5" will block only those IP addresses which were used to submit 5 spam comments or more on your site.', 'all-in-one-wp-security-and-firewall'),
						), 5);
						?>
					</td>
				</tr>
				<!-- <tr valign="top"> -->
				<!-- <th scope="row"> --><?php //esc_html_e('Run now', 'all-in-one-wp-security-and-firewall'); ?><!--:</th>-->
				<!-- <td><input type="submit" name="aiowps_auto_spam_block_run" value=" --><?php //esc_html_e('Run spam IP blocking now', 'all-in-one-wp-security-and-firewall'); ?><!--" class="button-secondary" />-->
				<!-- <span class="description">--><?php //esc_html_e('This feature normally runs automatically whenever a comment is submitted but you can run it manually by clicking this button. (useful for older comments)', 'all-in-one-wp-security-and-firewall');?><!--</span>-->
				<!-- </td> -->
				<!-- </tr> -->
			</table>
			<input type="submit" name="aiowps_auto_spam_block" value="<?php esc_html_e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
		</form>
	</div>
</div>
<div class="postbox" id="aios-spam-ip-search-container">
	<h3 class="hndle"><?php esc_html_e('List spammer IP addresses', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div class="aio_blue_box">
			<?php
			echo '<p>'.esc_html__('This section displays a list of the IP addresses of the people or bots who have left spam comments on your site.', 'all-in-one-wp-security-and-firewall').'<br>'.esc_html__('This information can be handy for identifying the most persistent IP addresses or ranges used by spammers.', 'all-in-one-wp-security-and-firewall').'<br>'.esc_html__('By inspecting the IP address data coming from spammers you will be in a better position to determine which addresses or address ranges you should block by adding them to the permanent block list.', 'all-in-one-wp-security-and-firewall').'<br>'.esc_html__('To add one or more of the IP addresses displayed in the table below to your blacklist, simply press the "Block" link for the individual row or select more than one address using the checkboxes and then choose the "block" option from the Bulk Actions dropdown list and press the "Apply" button.', 'all-in-one-wp-security-and-firewall').'</p>';
			?>
		</div>
		<form action="" id="aios-spam-ip-search-form" method="POST">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="aiowps_spam_ip_min_comments"><?php esc_html_e('Minimum number of spam comments per IP', 'all-in-one-wp-security-and-firewall'); ?>:</label></th>
					<td>
						<input id="aiowps_spam_ip_min_comments" type="number" min="1" name="aiowps_spam_ip_min_comments" value="<?php echo esc_attr($aio_wp_security->configs->get_value('aiowps_spam_ip_min_comments')); ?>" />
						<span class="description"><?php esc_html_e('This field allows you to list only those IP addresses which have been used to post X or more spam comments.', 'all-in-one-wp-security-and-firewall'); ?></span>
						<?php
						AIOWPSecurity_Utility_UI::tooltip(array(
							__('Example 1: Setting this value to "1" will list ALL IP addresses which were used to submit at least 1 spam comment.', 'all-in-one-wp-security-and-firewall'),
							'',
							__('Example 2: Setting this value to "5" will list only those IP addresses which were used to submit 5 spam comments or more on your site.', 'all-in-one-wp-security-and-firewall'),
						), 5);
						?>
					</td>
				</tr>
			</table>
			<input type="submit" name="aiowps_ip_spam_comment_search" value="<?php esc_html_e('Find IP addresses', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
		</form>
	</div>
</div>
<div class="postbox" id="aios-spammer-list-table">
	<h3 class="hndle"><?php esc_html_e('Spammer IP address results', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<?php
		// Fetch, prepare, sort, and filter our data...
		$spammer_ip_list->prepare_items();
		// echo "put table of locked entries here";
		?>
		<form id="tables-filter" method="post">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo esc_attr($page); ?>" />
			<input type="hidden" name="tab" value="<?php echo esc_attr($tab); ?>" />
			<!-- Now we can render the completed list table -->
			<?php $spammer_ip_list->display(); ?>
		</form>
	</div>
</div>