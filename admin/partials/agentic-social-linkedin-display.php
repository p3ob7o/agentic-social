<?php
/**
 * Provide a admin area view for LinkedIn settings
 *
 * This file is used to markup the LinkedIn settings page of the plugin.
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
$linkedin_settings = get_option( 'agentic_social_linkedin_settings', array() );

// Handle form submission
if ( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'agentic_social_linkedin_settings' ) ) {
	$linkedin_settings = array(
		'profile_url' => sanitize_url( $_POST['profile_url'] ),
		'default_hashtags' => sanitize_textarea_field( $_POST['default_hashtags'] ),
		'include_author_tag' => isset( $_POST['include_author_tag'] ) ? 1 : 0,
		'add_cta' => isset( $_POST['add_cta'] ) ? 1 : 0,
		'custom_cta' => sanitize_text_field( $_POST['custom_cta'] ),
		'post_timing' => sanitize_text_field( $_POST['post_timing'] ),
		'content_template' => sanitize_textarea_field( $_POST['content_template'] ),
	);
	
	update_option( 'agentic_social_linkedin_settings', $linkedin_settings );
	
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'LinkedIn settings saved successfully!', 'agentic-social' ) . '</p></div>';
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<div class="agentic-social-linkedin-header">
		<div class="platform-info">
			<img src="<?php echo esc_url( AGENTIC_SOCIAL_PLUGIN_URL . 'admin/images/linkedin-logo.png' ); ?>" 
			     alt="LinkedIn" style="width: 40px; height: 40px; vertical-align: middle;" 
			     onerror="this.style.display='none'">
			<h2 style="display: inline-block; margin-left: 10px; vertical-align: middle;">
				<?php esc_html_e( 'LinkedIn Integration', 'agentic-social' ); ?>
			</h2>
		</div>
		<p class="description">
			<?php esc_html_e( 'Configure how your content is shared on LinkedIn. This plugin uses a semi-automated approach that works with AI agents while respecting platform algorithms.', 'agentic-social' ); ?>
		</p>
	</div>
	
	<?php if ( ! isset( $settings['linkedin_enabled'] ) || ! $settings['linkedin_enabled'] ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php 
				printf(
					/* translators: %s: Settings page URL */
					__( 'LinkedIn sharing is currently disabled. <a href="%s">Enable it in the main settings</a> to use these options.', 'agentic-social' ),
					esc_url( admin_url( 'admin.php?page=agentic-social' ) )
				);
				?>
			</p>
		</div>
	<?php endif; ?>
	
	<form method="post" action="">
		<?php wp_nonce_field( 'agentic_social_linkedin_settings' ); ?>
		
		<div class="agentic-social-settings-sections">
			
			<!-- Profile Settings -->
			<div class="postbox">
				<div class="postbox-header">
					<h2 class="hndle"><?php esc_html_e( 'Profile Settings', 'agentic-social' ); ?></h2>
				</div>
				<div class="inside">
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row">
									<label for="profile_url"><?php esc_html_e( 'Your LinkedIn Profile URL', 'agentic-social' ); ?></label>
								</th>
								<td>
									<input type="url" id="profile_url" name="profile_url" 
									       value="<?php echo esc_attr( isset( $linkedin_settings['profile_url'] ) ? $linkedin_settings['profile_url'] : '' ); ?>" 
									       class="regular-text" placeholder="https://www.linkedin.com/in/yourprofile" />
									<p class="description">
										<?php esc_html_e( 'Used for author attribution and profile linking.', 'agentic-social' ); ?>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<!-- Content Settings -->
			<div class="postbox">
				<div class="postbox-header">
					<h2 class="hndle"><?php esc_html_e( 'Content Settings', 'agentic-social' ); ?></h2>
				</div>
				<div class="inside">
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row">
									<label for="content_template"><?php esc_html_e( 'Content Template', 'agentic-social' ); ?></label>
								</th>
								<td>
									<textarea id="content_template" name="content_template" rows="5" class="large-text"><?php echo esc_textarea( isset( $linkedin_settings['content_template'] ) ? $linkedin_settings['content_template'] : '{summary}

{hashtags}

{cta}' ); ?></textarea>
									<p class="description">
										<?php esc_html_e( 'Template for LinkedIn posts. Use {summary}, {title}, {url}, {hashtags}, {cta}, {author} as placeholders.', 'agentic-social' ); ?>
									</p>
								</td>
							</tr>
							
							<tr>
								<th scope="row">
									<label for="default_hashtags"><?php esc_html_e( 'Default Hashtags', 'agentic-social' ); ?></label>
								</th>
								<td>
									<textarea id="default_hashtags" name="default_hashtags" rows="3" class="large-text" placeholder="#wordpress #blogging #content"><?php echo esc_textarea( isset( $linkedin_settings['default_hashtags'] ) ? $linkedin_settings['default_hashtags'] : '' ); ?></textarea>
									<p class="description">
										<?php esc_html_e( 'Default hashtags to include with posts. One per line or space-separated.', 'agentic-social' ); ?>
									</p>
								</td>
							</tr>
							
							<tr>
								<th scope="row">
									<label for="include_author_tag"><?php esc_html_e( 'Include Author Attribution', 'agentic-social' ); ?></label>
								</th>
								<td>
									<input type="checkbox" id="include_author_tag" name="include_author_tag" value="1" 
									       <?php checked( isset( $linkedin_settings['include_author_tag'] ) ? $linkedin_settings['include_author_tag'] : false, true ); ?> />
									<p class="description">
										<?php esc_html_e( 'Include "By [Author Name]" in the post content.', 'agentic-social' ); ?>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<!-- Call-to-Action Settings -->
			<div class="postbox">
				<div class="postbox-header">
					<h2 class="hndle"><?php esc_html_e( 'Call-to-Action Settings', 'agentic-social' ); ?></h2>
				</div>
				<div class="inside">
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row">
									<label for="add_cta"><?php esc_html_e( 'Add Call-to-Action', 'agentic-social' ); ?></label>
								</th>
								<td>
									<input type="checkbox" id="add_cta" name="add_cta" value="1" 
									       <?php checked( isset( $linkedin_settings['add_cta'] ) ? $linkedin_settings['add_cta'] : true, true ); ?> />
									<p class="description">
										<?php esc_html_e( 'Add an engaging call-to-action to encourage interaction.', 'agentic-social' ); ?>
									</p>
								</td>
							</tr>
							
							<tr>
								<th scope="row">
									<label for="custom_cta"><?php esc_html_e( 'Custom Call-to-Action', 'agentic-social' ); ?></label>
								</th>
								<td>
									<input type="text" id="custom_cta" name="custom_cta" 
									       value="<?php echo esc_attr( isset( $linkedin_settings['custom_cta'] ) ? $linkedin_settings['custom_cta'] : '' ); ?>" 
									       class="large-text" placeholder="What are your thoughts on this?" />
									<p class="description">
										<?php esc_html_e( 'Custom CTA text. Leave empty to use auto-generated CTAs.', 'agentic-social' ); ?>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<!-- Timing Settings -->
			<div class="postbox">
				<div class="postbox-header">
					<h2 class="hndle"><?php esc_html_e( 'Posting Behavior', 'agentic-social' ); ?></h2>
				</div>
				<div class="inside">
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row">
									<label for="post_timing"><?php esc_html_e( 'Optimal Posting Time', 'agentic-social' ); ?></label>
								</th>
								<td>
									<select id="post_timing" name="post_timing">
										<option value="immediate" <?php selected( isset( $linkedin_settings['post_timing'] ) ? $linkedin_settings['post_timing'] : 'immediate', 'immediate' ); ?>>
											<?php esc_html_e( 'Share immediately', 'agentic-social' ); ?>
										</option>
										<option value="business_hours" <?php selected( isset( $linkedin_settings['post_timing'] ) ? $linkedin_settings['post_timing'] : '', 'business_hours' ); ?>>
											<?php esc_html_e( 'Business hours (9 AM - 5 PM)', 'agentic-social' ); ?>
										</option>
										<option value="peak_hours" <?php selected( isset( $linkedin_settings['post_timing'] ) ? $linkedin_settings['post_timing'] : '', 'peak_hours' ); ?>>
											<?php esc_html_e( 'Peak engagement (8-10 AM, 12-1 PM, 5-6 PM)', 'agentic-social' ); ?>
										</option>
									</select>
									<p class="description">
										<?php esc_html_e( 'When to suggest posting for optimal engagement. Note: This is for guidance only as posting is manual.', 'agentic-social' ); ?>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<!-- AI Agent Settings -->
			<?php if ( isset( $settings['enable_ai_agent'] ) && $settings['enable_ai_agent'] ) : ?>
				<div class="postbox">
					<div class="postbox-header">
						<h2 class="hndle"><?php esc_html_e( 'AI Agent Configuration', 'agentic-social' ); ?></h2>
					</div>
					<div class="inside">
						<p class="description">
							<?php esc_html_e( 'AI Agent Mode is enabled. The plugin will include additional data attributes and structured information to help AI agents navigate LinkedIn automatically.', 'agentic-social' ); ?>
						</p>
						
						<div class="ai-agent-info">
							<h4><?php esc_html_e( 'AI Agent Workflow Steps:', 'agentic-social' ); ?></h4>
							<ol>
								<li><?php esc_html_e( 'Navigate to LinkedIn feed', 'agentic-social' ); ?></li>
								<li><?php esc_html_e( 'Click "Start a post" button', 'agentic-social' ); ?></li>
								<li><?php esc_html_e( 'Paste generated content', 'agentic-social' ); ?></li>
								<li><?php esc_html_e( 'Click "Post" to publish', 'agentic-social' ); ?></li>
								<li><?php esc_html_e( 'Add original link as first comment', 'agentic-social' ); ?></li>
							</ol>
						</div>
						
						<div class="ai-agent-selectors">
							<h4><?php esc_html_e( 'Key Selectors for AI Agents:', 'agentic-social' ); ?></h4>
							<ul>
								<li><code>[data-agentic-action="open-linkedin"]</code> - Open LinkedIn</li>
								<li><code>[data-agentic-action="create-post"]</code> - Create new post</li>
								<li><code>[data-agentic-action="paste-content"]</code> - Paste content</li>
								<li><code>[data-agentic-action="publish-post"]</code> - Publish post</li>
								<li><code>[data-agentic-action="add-comment"]</code> - Add comment with link</li>
							</ul>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
		</div>
		
		<div class="submit-section">
			<?php submit_button( __( 'Save LinkedIn Settings', 'agentic-social' ), 'primary', 'submit' ); ?>
		</div>
	</form>
	
	<!-- Test Section -->
	<div class="postbox">
		<div class="postbox-header">
			<h2 class="hndle"><?php esc_html_e( 'Test LinkedIn Integration', 'agentic-social' ); ?></h2>
		</div>
		<div class="inside">
			<p><?php esc_html_e( 'Test your LinkedIn settings with a sample post:', 'agentic-social' ); ?></p>
			
			<div class="test-controls">
				<select id="test-post-select">
					<option value=""><?php esc_html_e( 'Select a post to test...', 'agentic-social' ); ?></option>
					<?php
					$recent_posts = get_posts( array(
						'numberposts' => 10,
						'post_status' => 'publish',
					) );
					
					foreach ( $recent_posts as $post ) :
						?>
						<option value="<?php echo esc_attr( $post->ID ); ?>">
							<?php echo esc_html( $post->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				
				<button type="button" id="test-linkedin-share" class="button">
					<?php esc_html_e( 'Generate Test Content', 'agentic-social' ); ?>
				</button>
			</div>
			
			<div id="test-results" style="display: none; margin-top: 20px;">
				<h4><?php esc_html_e( 'Generated Content Preview:', 'agentic-social' ); ?></h4>
				<div id="test-content" style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; white-space: pre-line;"></div>
				
				<div style="margin-top: 15px;">
					<button type="button" id="copy-test-content" class="button">
						<?php esc_html_e( 'Copy to Clipboard', 'agentic-social' ); ?>
					</button>
					<a href="https://www.linkedin.com/feed/" target="_blank" class="button button-primary" 
					   data-agentic-action="open-linkedin">
						<?php esc_html_e( 'Open LinkedIn', 'agentic-social' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.agentic-social-linkedin-header {
	background: #f8f9fa;
	padding: 20px;
	border-radius: 4px;
	margin-bottom: 20px;
}

.platform-info {
	display: flex;
	align-items: center;
	margin-bottom: 10px;
}

.agentic-social-settings-sections .postbox {
	margin-bottom: 20px;
}

.ai-agent-info, .ai-agent-selectors {
	background: #f0f8ff;
	padding: 15px;
	border-radius: 4px;
	margin: 15px 0;
}

.ai-agent-info ol, .ai-agent-selectors ul {
	margin: 10px 0;
	padding-left: 20px;
}

.ai-agent-selectors code {
	background: #333;
	color: #fff;
	padding: 2px 6px;
	border-radius: 3px;
	font-family: monospace;
}

.test-controls {
	display: flex;
	gap: 10px;
	align-items: center;
	margin: 15px 0;
}

.test-controls select {
	min-width: 200px;
}

.submit-section {
	background: #f9f9f9;
	padding: 20px;
	border-radius: 4px;
	margin: 20px 0;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#test-linkedin-share').on('click', function() {
		var postId = $('#test-post-select').val();
		var button = $(this);
		
		if (!postId) {
			alert('<?php echo esc_js( __( 'Please select a post to test.', 'agentic-social' ) ); ?>');
			return;
		}
		
		button.prop('disabled', true).text('<?php echo esc_js( __( 'Generating...', 'agentic-social' ) ); ?>');
		
		$.post(ajaxurl, {
			action: 'agentic_social_generate_summary',
			post_id: postId,
			platform: 'linkedin',
			nonce: '<?php echo wp_create_nonce( 'agentic_social_nonce' ); ?>'
		}, function(response) {
			button.prop('disabled', false).text('<?php echo esc_js( __( 'Generate Test Content', 'agentic-social' ) ); ?>');
			
			if (response.success) {
				$('#test-content').text(response.data.summary);
				$('#test-results').show();
			} else {
				alert('<?php echo esc_js( __( 'Failed to generate content. Please try again.', 'agentic-social' ) ); ?>');
			}
		});
	});
	
	$('#copy-test-content').on('click', function() {
		var content = $('#test-content').text();
		navigator.clipboard.writeText(content).then(function() {
			var button = $('#copy-test-content');
			var originalText = button.text();
			button.text('<?php echo esc_js( __( 'Copied!', 'agentic-social' ) ); ?>');
			setTimeout(function() {
				button.text(originalText);
			}, 2000);
		});
	});
});
</script>
