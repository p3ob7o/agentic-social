<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://paolobelcastro.com
 * @since      1.0.0
 *
 * @package    Agentic_Social
 * @subpackage Agentic_Social/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Agentic_Social
 * @subpackage Agentic_Social/admin
 * @author     Paolo Belcastro <paolo@paolobelcastro.com>
 */
class Agentic_Social_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Agentic_Social_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Agentic_Social_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, AGENTIC_SOCIAL_PLUGIN_URL . 'admin/css/agentic-social-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Agentic_Social_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Agentic_Social_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, AGENTIC_SOCIAL_PLUGIN_URL . 'admin/js/agentic-social-admin.js', array( 'jquery' ), $this->version, false );
		
		// Localize script for AJAX operations
		wp_localize_script(
			$this->plugin_name,
			'agentic_social_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'agentic_social_nonce' ),
			)
		);

	}
	
	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		
		// Add main menu page
		add_menu_page(
			__( 'Agentic Social Sharing', 'agentic-social' ),
			__( 'Agentic Social', 'agentic-social' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' ),
			'dashicons-share',
			30
		);
		
		// Add submenu for settings
		add_submenu_page(
			$this->plugin_name,
			__( 'Settings', 'agentic-social' ),
			__( 'Settings', 'agentic-social' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' )
		);
		
		// Add submenu for share history
		add_submenu_page(
			$this->plugin_name,
			__( 'Share History', 'agentic-social' ),
			__( 'Share History', 'agentic-social' ),
			'manage_options',
			$this->plugin_name . '-history',
			array( $this, 'display_share_history_page' )
		);
		
		// Add submenu for LinkedIn settings
		add_submenu_page(
			$this->plugin_name,
			__( 'LinkedIn Settings', 'agentic-social' ),
			__( 'LinkedIn', 'agentic-social' ),
			'manage_options',
			$this->plugin_name . '-linkedin',
			array( $this, 'display_linkedin_settings_page' )
		);
		
	}
	
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 * @param    array    $links    An array of plugin action links.
	 * @return   array              An array of plugin action links.
	 */
	public function add_action_links( $links ) {
		
		$settings_link = array(
			'<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', 'agentic-social' ) . '</a>',
		);
		
		return array_merge( $settings_link, $links );
		
	}
	
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		include_once AGENTIC_SOCIAL_PLUGIN_DIR . 'admin/partials/agentic-social-admin-display.php';
	}
	
	/**
	 * Render the share history page.
	 *
	 * @since    1.0.0
	 */
	public function display_share_history_page() {
		include_once AGENTIC_SOCIAL_PLUGIN_DIR . 'admin/partials/agentic-social-history-display.php';
	}
	
	/**
	 * Render the LinkedIn settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_linkedin_settings_page() {
		include_once AGENTIC_SOCIAL_PLUGIN_DIR . 'admin/partials/agentic-social-linkedin-display.php';
	}
	
	/**
	 * Add meta boxes to post editor.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_boxes() {
		$settings = get_option( 'agentic_social_settings' );
		$post_types = isset( $settings['default_post_types'] ) ? $settings['default_post_types'] : array( 'post' );
		
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'agentic_social_meta_box',
				__( 'Agentic Social Sharing', 'agentic-social' ),
				array( $this, 'render_meta_box' ),
				$post_type,
				'side',
				'high'
			);
		}
	}
	
	/**
	 * Render meta box content.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    The post object.
	 */
	public function render_meta_box( $post ) {
		// Add nonce for security
		wp_nonce_field( 'agentic_social_meta_box', 'agentic_social_meta_box_nonce' );
		
		// Get existing values
		$share_status = get_post_meta( $post->ID, '_agentic_social_share_status', true );
		$linkedin_id = get_post_meta( $post->ID, '_agentic_social_linkedin_id', true );
		$custom_message = get_post_meta( $post->ID, '_agentic_social_custom_message', true );
		
		// Get settings for AI mode check
		$settings = get_option( 'agentic_social_settings', array() );
		$ai_mode = isset( $settings['enable_ai_agent'] ) && $settings['enable_ai_agent'];
		$linkedin_enabled = isset( $settings['linkedin_enabled'] ) && $settings['linkedin_enabled'];
		
		// Get sharing history for this post
		$post_shares = get_post_meta( $post->ID, '_agentic_social_shares', true );
		if ( ! is_array( $post_shares ) ) {
			$post_shares = array();
		}
		?>
		<div class="agentic-social-meta-box<?php echo $ai_mode ? ' agentic-ai-mode' : ''; ?>">
			
			<!-- Share Status -->
			<div class="share-status-section">
				<p>
					<label for="agentic_social_share_status">
						<input type="checkbox" name="agentic_social_share_status" id="agentic_social_share_status" value="1" <?php checked( $share_status, '1' ); ?> />
						<?php esc_html_e( 'Enable social media sharing for this post', 'agentic-social' ); ?>
					</label>
				</p>
			</div>
			
			<!-- Platform Selection -->
			<?php if ( $linkedin_enabled ) : ?>
				<div class="platform-selection">
					<label for="platform_selector">
						<?php esc_html_e( 'Platform:', 'agentic-social' ); ?>
					</label>
					<select name="platform_selector" id="platform_selector" class="platform-selector" <?php echo $ai_mode ? 'data-agentic-action="select-platform"' : ''; ?>>
						<option value="linkedin"><?php esc_html_e( 'LinkedIn', 'agentic-social' ); ?></option>
					</select>
				</div>
			<?php endif; ?>
			
			<!-- Custom Message -->
			<div class="custom-message-section">
				<label for="agentic_social_custom_message">
					<?php esc_html_e( 'Custom Message (optional):', 'agentic-social' ); ?>
				</label>
				<textarea name="agentic_social_custom_message" id="agentic_social_custom_message" rows="3" placeholder="<?php esc_attr_e( 'Leave empty to auto-generate from post content...', 'agentic-social' ); ?>"><?php echo esc_textarea( $custom_message ); ?></textarea>
				<button type="button" class="button generate-summary" data-post-id="<?php echo esc_attr( $post->ID ); ?>" data-platform="linkedin">
					<?php esc_html_e( '✨ Generate Summary', 'agentic-social' ); ?>
				</button>
			</div>
			
			<!-- Sharing History -->
			<?php if ( ! empty( $post_shares ) ) : ?>
				<div class="sharing-history">
					<h4><?php esc_html_e( 'Sharing History', 'agentic-social' ); ?></h4>
					<?php foreach ( $post_shares as $platform => $share_data ) : ?>
						<div class="share-entry">
							<span class="platform-badge platform-<?php echo esc_attr( $platform ); ?>">
								<?php echo esc_html( ucfirst( $platform ) ); ?>
							</span>
							<span class="status-badge status-<?php echo esc_attr( $share_data['status'] === 'completed' ? 'success' : ( $share_data['status'] === 'failed' ? 'error' : 'warning' ) ); ?>">
								<?php echo esc_html( ucfirst( $share_data['status'] ) ); ?>
							</span>
							<span class="share-date">
								<?php echo esc_html( mysql2date( 'M j, Y g:i a', $share_data['timestamp'] ) ); ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			
			<!-- LinkedIn Post ID Display -->
			<?php if ( $linkedin_id ) : ?>
				<div class="linkedin-post-info">
					<p>
						<strong><?php esc_html_e( 'LinkedIn Post ID:', 'agentic-social' ); ?></strong><br>
						<code><?php echo esc_html( $linkedin_id ); ?></code>
					</p>
				</div>
			<?php endif; ?>
			
			<!-- Action Buttons -->
			<div class="action-buttons">
				<button type="button" class="button button-primary" id="agentic-social-share-now" 
				        data-post-id="<?php echo esc_attr( $post->ID ); ?>"
				        <?php echo $ai_mode ? 'data-agentic-action="start-sharing"' : ''; ?>>
					<?php esc_html_e( '🚀 Share Now', 'agentic-social' ); ?>
				</button>
				
				<?php if ( $ai_mode ) : ?>
					<p class="ai-mode-notice">
						<span class="ai-agent-indicator"><?php esc_html_e( '🤖 AI Mode Active', 'agentic-social' ); ?></span><br>
						<em><?php esc_html_e( 'This interface is optimized for AI agent interaction.', 'agentic-social' ); ?></em>
					</p>
				<?php endif; ?>
			</div>
			
			<!-- Quick Preview -->
			<div class="sharing-preview" style="display: none;">
				<h4><?php esc_html_e( 'Sharing Preview', 'agentic-social' ); ?></h4>
				<div class="preview-content">
					<div class="preview-summary"></div>
					<div class="preview-url"><?php echo esc_html( get_permalink( $post->ID ) ); ?></div>
				</div>
			</div>
			
		</div>
		
		<style>
		.agentic-social-meta-box .share-status-section {
			margin-bottom: 15px;
			padding-bottom: 15px;
			border-bottom: 1px solid #e9ecef;
		}
		
		.agentic-social-meta-box .platform-selection {
			margin-bottom: 15px;
		}
		
		.agentic-social-meta-box .platform-selection select {
			width: 100%;
			margin-top: 5px;
		}
		
		.agentic-social-meta-box .custom-message-section {
			margin-bottom: 15px;
		}
		
		.agentic-social-meta-box .custom-message-section textarea {
			margin-top: 5px;
			margin-bottom: 8px;
		}
		
		.agentic-social-meta-box .generate-summary {
			font-size: 12px;
		}
		
		.agentic-social-meta-box .sharing-history {
			margin-bottom: 15px;
			padding: 10px;
			background: #f8f9fa;
			border-radius: 4px;
		}
		
		.agentic-social-meta-box .sharing-history h4 {
			margin: 0 0 10px 0;
			font-size: 13px;
			color: #495057;
		}
		
		.agentic-social-meta-box .share-entry {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-bottom: 5px;
			font-size: 12px;
		}
		
		.agentic-social-meta-box .share-date {
			color: #6c757d;
			font-size: 11px;
		}
		
		.agentic-social-meta-box .linkedin-post-info {
			margin-bottom: 15px;
			padding: 10px;
			background: #e7f3ff;
			border-radius: 4px;
		}
		
		.agentic-social-meta-box .action-buttons {
			text-align: center;
		}
		
		.agentic-social-meta-box .ai-mode-notice {
			margin-top: 10px;
			font-size: 12px;
			text-align: center;
		}
		
		.agentic-social-meta-box .sharing-preview {
			margin-top: 15px;
			padding: 15px;
			background: #f8f9fa;
			border-radius: 4px;
			border: 1px solid #e9ecef;
		}
		
		.agentic-social-meta-box .sharing-preview h4 {
			margin: 0 0 10px 0;
			font-size: 13px;
			color: #495057;
		}
		
		.agentic-social-meta-box .preview-content {
			font-size: 13px;
			line-height: 1.4;
		}
		
		.agentic-social-meta-box .preview-summary {
			margin-bottom: 10px;
			white-space: pre-line;
		}
		
		.agentic-social-meta-box .preview-url {
			color: #0073aa;
			font-family: monospace;
			font-size: 12px;
			word-break: break-all;
		}
		</style>
		<?php
	}
	
	/**
	 * Save meta box data.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    The ID of the post being saved.
	 */
	public function save_post_meta( $post_id ) {
		// Check if nonce is set
		if ( ! isset( $_POST['agentic_social_meta_box_nonce'] ) ) {
			return;
		}
		
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['agentic_social_meta_box_nonce'], 'agentic_social_meta_box' ) ) {
			return;
		}
		
		// Check if this is an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		// Save share status
		$share_status = isset( $_POST['agentic_social_share_status'] ) ? '1' : '0';
		update_post_meta( $post_id, '_agentic_social_share_status', $share_status );
		
		// Save custom message
		if ( isset( $_POST['agentic_social_custom_message'] ) ) {
			$custom_message = sanitize_textarea_field( $_POST['agentic_social_custom_message'] );
			update_post_meta( $post_id, '_agentic_social_custom_message', $custom_message );
		}
	}
	
	/**
	 * Register plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting( 'agentic_social_settings_group', 'agentic_social_settings', array( $this, 'validate_settings' ) );
	}
	
	/**
	 * Validate settings before saving.
	 *
	 * @since    1.0.0
	 * @param    array $input The input settings to validate.
	 * @return   array        The validated settings.
	 */
	public function validate_settings( $input ) {
		$validated = array();
		
		// Boolean settings
		$validated['linkedin_enabled'] = isset( $input['linkedin_enabled'] ) ? 1 : 0;
		$validated['auto_share'] = isset( $input['auto_share'] ) ? 1 : 0;
		$validated['add_link_as_comment'] = isset( $input['add_link_as_comment'] ) ? 1 : 0;
		$validated['enable_ai_agent'] = isset( $input['enable_ai_agent'] ) ? 1 : 0;
		
		// Numeric settings
		$validated['share_delay'] = isset( $input['share_delay'] ) ? absint( $input['share_delay'] ) : 5;
		$validated['share_delay'] = min( max( $validated['share_delay'], 0 ), 60 );
		
		// Array settings
		$validated['default_post_types'] = isset( $input['default_post_types'] ) && is_array( $input['default_post_types'] ) 
			? array_map( 'sanitize_text_field', $input['default_post_types'] ) 
			: array( 'post' );
		
		return $validated;
	}
	
	/**
	 * AJAX handler for generating post summary.
	 *
	 * @since    1.0.0
	 */
	public function ajax_generate_summary() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'agentic_social_nonce' ) ) {
			wp_die( __( 'Security check failed', 'agentic-social' ) );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have permission to perform this action', 'agentic-social' ) );
		}
		
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$platform = isset( $_POST['platform'] ) ? sanitize_text_field( $_POST['platform'] ) : 'linkedin';
		
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Invalid post ID', 'agentic-social' ) );
		}
		
		$summary = Agentic_Social_Content_Processor::generate_summary( $post_id, $platform );
		
		wp_send_json_success( array(
			'summary' => $summary,
			'platform' => $platform,
			'post_id' => $post_id,
		) );
	}
	
	/**
	 * AJAX handler for sharing post.
	 *
	 * @since    1.0.0
	 */
	public function ajax_share_post() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'agentic_social_nonce' ) ) {
			wp_die( __( 'Security check failed', 'agentic-social' ) );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have permission to perform this action', 'agentic-social' ) );
		}
		
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$platform = isset( $_POST['platform'] ) ? sanitize_text_field( $_POST['platform'] ) : 'linkedin';
		
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Invalid post ID', 'agentic-social' ) );
		}
		
		// Get sharing data
		$sharing_data = Agentic_Social_Content_Processor::get_sharing_data( $post_id );
		
		if ( ! $sharing_data ) {
			wp_send_json_error( __( 'Could not generate sharing data', 'agentic-social' ) );
		}
		
		// Log the sharing attempt
		$this->log_share_attempt( $post_id, $platform, 'initiated', $sharing_data );
		
		// Return data for guided workflow
		wp_send_json_success( array(
			'sharing_data' => $sharing_data,
			'workflow_steps' => $this->get_workflow_steps( $platform ),
			'platform' => $platform,
			'post_id' => $post_id,
		) );
	}
	
	/**
	 * AJAX handler for getting share data.
	 *
	 * @since    1.0.0
	 */
	public function ajax_get_share_data() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'agentic_social_nonce' ) ) {
			wp_die( __( 'Security check failed', 'agentic-social' ) );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have permission to perform this action', 'agentic-social' ) );
		}
		
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Invalid post ID', 'agentic-social' ) );
		}
		
		$sharing_data = Agentic_Social_Content_Processor::get_sharing_data( $post_id );
		
		wp_send_json_success( $sharing_data );
	}
	
	/**
	 * AJAX handler for marking share as complete.
	 *
	 * @since    1.0.0
	 */
	public function ajax_mark_complete() {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'agentic_social_nonce' ) ) {
			wp_die( __( 'Security check failed', 'agentic-social' ) );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have permission to perform this action', 'agentic-social' ) );
		}
		
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$platform = isset( $_POST['platform'] ) ? sanitize_text_field( $_POST['platform'] ) : 'linkedin';
		
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Invalid post ID', 'agentic-social' ) );
		}
		
		// Mark as completed
		$this->log_share_attempt( $post_id, $platform, 'completed' );
		
		wp_send_json_success( array(
			'message' => __( 'Share marked as completed', 'agentic-social' ),
			'post_id' => $post_id,
			'platform' => $platform,
		) );
	}
	
	/**
	 * Get workflow steps for a platform.
	 *
	 * @since    1.0.0
	 * @param    string $platform The platform name.
	 * @return   array            The workflow steps.
	 */
	private function get_workflow_steps( $platform ) {
		switch ( $platform ) {
			case 'linkedin':
				return array(
					array(
						'step' => 1,
						'title' => __( 'Open LinkedIn', 'agentic-social' ),
						'description' => __( 'Navigate to LinkedIn in a new tab', 'agentic-social' ),
						'action' => 'open_url',
						'url' => 'https://www.linkedin.com/feed/',
						'ai_selector' => '[data-agentic-action="open-linkedin"]',
					),
					array(
						'step' => 2,
						'title' => __( 'Create New Post', 'agentic-social' ),
						'description' => __( 'Click on "Start a post" or the post creation button', 'agentic-social' ),
						'action' => 'click_element',
						'selector' => '.share-box-feed-entry__trigger, [data-control-name="share_box"]',
						'ai_selector' => '[data-agentic-action="create-post"]',
					),
					array(
						'step' => 3,
						'title' => __( 'Add Post Content', 'agentic-social' ),
						'description' => __( 'Copy and paste the generated summary into the post editor', 'agentic-social' ),
						'action' => 'paste_content',
						'selector' => '.ql-editor, [data-placeholder="What do you want to talk about?"]',
						'ai_selector' => '[data-agentic-action="paste-content"]',
					),
					array(
						'step' => 4,
						'title' => __( 'Publish Post', 'agentic-social' ),
						'description' => __( 'Click the "Post" button to publish', 'agentic-social' ),
						'action' => 'click_element',
						'selector' => '[data-control-name="share.post"]',
						'ai_selector' => '[data-agentic-action="publish-post"]',
					),
					array(
						'step' => 5,
						'title' => __( 'Add Link as Comment', 'agentic-social' ),
						'description' => __( 'Add the original blog post link as the first comment', 'agentic-social' ),
						'action' => 'add_comment',
						'selector' => '.comments-comment-box__form-container textarea',
						'ai_selector' => '[data-agentic-action="add-comment"]',
					),
				);
				
			default:
				return array();
		}
	}
	
	/**
	 * Log share attempt.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id      The post ID.
	 * @param    string $platform     The platform.
	 * @param    string $status       The status.
	 * @param    array  $sharing_data The sharing data.
	 */
	private function log_share_attempt( $post_id, $platform, $status, $sharing_data = array() ) {
		$log_entry = array(
			'post_id' => $post_id,
			'platform' => $platform,
			'status' => $status,
			'timestamp' => current_time( 'mysql' ),
			'sharing_data' => $sharing_data,
		);
		
		// Get existing log
		$share_log = get_option( 'agentic_social_share_log', array() );
		
		// Add new entry
		$share_log[] = $log_entry;
		
		// Keep only last 100 entries
		if ( count( $share_log ) > 100 ) {
			$share_log = array_slice( $share_log, -100 );
		}
		
		// Save log
		update_option( 'agentic_social_share_log', $share_log );
		
		// Also save to post meta for quick access
		$post_shares = get_post_meta( $post_id, '_agentic_social_shares', true );
		if ( ! is_array( $post_shares ) ) {
			$post_shares = array();
		}
		$post_shares[ $platform ] = $log_entry;
		update_post_meta( $post_id, '_agentic_social_shares', $post_shares );
	}

}
