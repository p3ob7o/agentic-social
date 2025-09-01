<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://paolobelcastro.com
 * @since      1.0.0
 *
 * @package    Agentic_Social
 * @subpackage Agentic_Social/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Get current settings
$settings = get_option( 'agentic_social_settings' );
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<form method="post" action="options.php">
		<?php
		settings_fields( 'agentic_social_settings_group' );
		do_settings_sections( 'agentic_social_settings_group' );
		?>
		
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="linkedin_enabled"><?php esc_html_e( 'Enable LinkedIn Sharing', 'agentic-social' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="linkedin_enabled" name="agentic_social_settings[linkedin_enabled]" value="1" <?php checked( isset( $settings['linkedin_enabled'] ) ? $settings['linkedin_enabled'] : false, true ); ?> />
						<p class="description"><?php esc_html_e( 'Enable sharing posts to LinkedIn.', 'agentic-social' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="auto_share"><?php esc_html_e( 'Auto Share', 'agentic-social' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="auto_share" name="agentic_social_settings[auto_share]" value="1" <?php checked( isset( $settings['auto_share'] ) ? $settings['auto_share'] : false, true ); ?> />
						<p class="description"><?php esc_html_e( 'Automatically share new posts when published.', 'agentic-social' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="share_delay"><?php esc_html_e( 'Share Delay (minutes)', 'agentic-social' ); ?></label>
					</th>
					<td>
						<input type="number" id="share_delay" name="agentic_social_settings[share_delay]" value="<?php echo esc_attr( isset( $settings['share_delay'] ) ? $settings['share_delay'] : 5 ); ?>" min="0" max="60" />
						<p class="description"><?php esc_html_e( 'Delay in minutes before sharing after publishing.', 'agentic-social' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label><?php esc_html_e( 'Post Types', 'agentic-social' ); ?></label>
					</th>
					<td>
						<?php
						$post_types = get_post_types( array( 'public' => true ), 'objects' );
						$selected_types = isset( $settings['default_post_types'] ) ? $settings['default_post_types'] : array( 'post' );
						
						foreach ( $post_types as $post_type ) :
							if ( 'attachment' === $post_type->name ) {
								continue;
							}
							?>
							<label style="display: block; margin-bottom: 5px;">
								<input type="checkbox" name="agentic_social_settings[default_post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $selected_types, true ) ); ?> />
								<?php echo esc_html( $post_type->label ); ?>
							</label>
						<?php endforeach; ?>
						<p class="description"><?php esc_html_e( 'Select which post types can be shared.', 'agentic-social' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="add_link_as_comment"><?php esc_html_e( 'Add Link as Comment', 'agentic-social' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="add_link_as_comment" name="agentic_social_settings[add_link_as_comment]" value="1" <?php checked( isset( $settings['add_link_as_comment'] ) ? $settings['add_link_as_comment'] : true, true ); ?> />
						<p class="description"><?php esc_html_e( 'Add post links as comments instead of in the main post (better for platform algorithms).', 'agentic-social' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="enable_ai_agent"><?php esc_html_e( 'Enable AI Agent Mode', 'agentic-social' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="enable_ai_agent" name="agentic_social_settings[enable_ai_agent]" value="1" <?php checked( isset( $settings['enable_ai_agent'] ) ? $settings['enable_ai_agent'] : false, true ); ?> />
						<p class="description"><?php esc_html_e( 'Design interactions for AI agentic browsers like Perplexity\'s Comet.', 'agentic-social' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php submit_button(); ?>
	</form>
</div>
