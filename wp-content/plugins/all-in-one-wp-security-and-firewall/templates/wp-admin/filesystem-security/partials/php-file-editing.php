<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="postbox aio_hidden" data-template="php-file-editing">
	<h3 class="hndle"><?php esc_html_e('Disable PHP file editing', 'all-in-one-wp-security-and-firewall'); ?></h3>
	<div class="inside">
		<div id="filesystem-file-editing-badge">
			<?php
				$aiowps_feature_mgr->output_feature_details_badge("filesystem-file-editing");

				if ($show_disallow_file_edit_warning) {
					echo '<div class="aio_red_box"><p>' . esc_html__('The DISALLOW_FILE_EDIT constant has already been defined, please remove it before enabling this feature.', 'all-in-one-wp-security-and-firewall') . '<br />' . esc_html__('The constant is likely already defined in your wp-config.php file.', 'all-in-one-wp-security-and-firewall') . '</p></div>';
				}
			?>
		</div>
		<div class="aio_blue_box">
		<?php
			echo '<p>'.esc_html__('By default, the WordPress Dashboard allows administrators to edit PHP files, such as plugin and theme files.', 'all-in-one-wp-security-and-firewall').'<br>'.esc_html__('Attackers often use this tool if they are able to log in, because it allows them to execute code.', 'all-in-one-wp-security-and-firewall').'<br>'.esc_html__('This feature disables PHP file editing in the dashboard.', 'all-in-one-wp-security-and-firewall').'</p>';
		?>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Disable ability to edit PHP files', 'all-in-one-wp-security-and-firewall'); ?>:</th>
				<td>
					<div class="aiowps_switch_container">
						<?php AIOWPSecurity_Utility_UI::setting_checkbox(esc_html__('Enable this to remove the ability for people to edit PHP files via the WP dashboard', 'all-in-one-wp-security-and-firewall'), 'aiowps_disable_file_editing', '1' == $aio_wp_security->configs->get_value('aiowps_disable_file_editing')); ?>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
