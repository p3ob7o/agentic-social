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
	 * Handle post publish event.
	 *
	 * @since    1.0.1
	 * @param    string  $new_status New post status.
	 * @param    string  $old_status Old post status.
	 * @param    WP_Post $post       Post object.
	 */
	public function handle_post_publish( $new_status, $old_status, $post ) {
		// Only trigger on publish (not updates)
		if ( $new_status !== 'publish' || $old_status === 'publish' ) {
			return;
		}
		
		// Only proceed for standard posts
		if ( 'post' !== $post->post_type ) {
			return;
		}
		
		// Mark this post as just published
		update_post_meta( $post->ID, '_agentic_social_just_published', time() );
		
		// Set a flag in user meta to show overlay on next page load
		update_user_meta( get_current_user_id(), 'agentic_social_show_overlay', $post->ID );
	}
	
	/**
	 * Add publish overlay to admin footer.
	 *
	 * @since    1.0.1
	 */
	public function add_publish_overlay() {
		// Only show on edit screens
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->base, array( 'post', 'page' ), true ) ) {
			return;
		}
		
		// Check if we should show the overlay - either from user meta or URL parameter
		$post_id = get_user_meta( get_current_user_id(), 'agentic_social_show_overlay', true );
		
		// Also check if we're on a post that was just published (within last 2 minutes)
		if ( ! $post_id && isset( $_GET['post'] ) ) {
			$current_post_id = absint( $_GET['post'] );
			$published_time = get_post_meta( $current_post_id, '_agentic_social_just_published', true );
			
			if ( $published_time && ( time() - $published_time ) < 120 ) {
				$post_id = $current_post_id;
				// Clean up the meta
				delete_post_meta( $current_post_id, '_agentic_social_just_published' );
			}
		}
		
		// Also check URL parameter for message=1 which indicates post was just published
		if ( ! $post_id && isset( $_GET['message'] ) && $_GET['message'] == '1' && isset( $_GET['post'] ) ) {
			$current_post_id = absint( $_GET['post'] );
			$post = get_post( $current_post_id );
			
			// Check if this is a recently published post (within last 5 minutes)
			if ( $post && $post->post_status === 'publish' ) {
				$post_date = strtotime( $post->post_date );
				if ( ( time() - $post_date ) < 300 ) {
					$post_id = $current_post_id;
				}
			}
		}
		
		if ( ! $post_id ) {
			return;
		}
		
		// Clear the user meta flag
		delete_user_meta( get_current_user_id(), 'agentic_social_show_overlay' );
		
		// Get post data
		$post = get_post( $post_id );
		if ( ! $post ) {
			return;
		}
		
		// Only for standard posts
		if ( 'post' !== $post->post_type ) {
			return;
		}
		
		$ai_mode = false;
		
		// Generate summary immediately
		$sharing_data = Agentic_Social_Content_Processor::get_sharing_data( $post_id );
		
		?>
		<div id="agentic-social-publish-overlay" class="agentic-publish-overlay" style="display: none;">
			<div class="overlay-backdrop"></div>
			<div class="overlay-content">
				<div class="overlay-header">
					<h2><?php esc_html_e( '🎉 Post Published Successfully!', 'agentic-social' ); ?></h2>
					<p><?php esc_html_e( 'Share your content on LinkedIn to maximize reach', 'agentic-social' ); ?></p>
					<button class="overlay-close" aria-label="<?php esc_attr_e( 'Close', 'agentic-social' ); ?>">&times;</button>
				</div>
				
				<div class="overlay-body">
					<div class="sharing-section">
						<div class="post-info">
							<h3><?php echo esc_html( $post->post_title ); ?></h3>
							<p class="post-url"><?php echo esc_html( get_permalink( $post_id ) ); ?></p>
						</div>
						
						<div class="summary-section">
							<h4><?php esc_html_e( 'Generated LinkedIn Post', 'agentic-social' ); ?></h4>
							<div class="summary-container">
								<textarea id="linkedin-summary" rows="8" readonly><?php echo esc_textarea( $sharing_data['linkedin_summary'] ); ?></textarea>
								<div class="summary-actions">
									<button type="button" class="button copy-summary"><?php esc_html_e( '📋 Copy Text', 'agentic-social' ); ?></button>
									<a href="https://www.linkedin.com/feed/?shareActive=true" target="_blank" class="button button-primary"><?php esc_html_e( '🚀 Open LinkedIn New Post', 'agentic-social' ); ?></a>
								</div>
							</div>
						</div>
						
						<div class="comment-section">
							<h4><?php esc_html_e( 'First Comment (Link to your post)', 'agentic-social' ); ?></h4>
							<div class="summary-container">
								<textarea id="linkedin-comment" rows="4" readonly><?php echo esc_textarea( get_permalink( $post_id ) ); ?></textarea>
								<div class="summary-actions">
									<button type="button" class="button copy-link" data-url="<?php echo esc_attr( get_permalink( $post_id ) ); ?>"><?php esc_html_e( '📋 Copy Link', 'agentic-social' ); ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="overlay-footer">
					<button class="button skip-sharing"><?php esc_html_e( 'Close', 'agentic-social' ); ?></button>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Show overlay immediately
			setTimeout(function() {
				$('#agentic-social-publish-overlay').fadeIn(300);
				$('body').addClass('agentic-overlay-open');
				$(window).scrollTop(0);
			}, 500); // Small delay to ensure page is fully loaded
		});
		</script>
		
		<style>
		.agentic-overlay-open {
			overflow: hidden;
		}
		
		.agentic-publish-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 999999;
			background: rgba(0, 0, 0, 0.8);
			backdrop-filter: blur(5px);
		}
		
		.overlay-backdrop {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		
		.overlay-content {
			position: relative;
			max-width: 1200px;
			width: 95%;
			height: 95%;
			margin: 2.5% auto;
			background: white;
			border-radius: 12px;
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
			display: flex;
			flex-direction: column;
			overflow: hidden;
		}
		
		.overlay-header {
			background: linear-gradient(135deg, #0077b5, #005885);
			color: white;
			padding: 20px 30px;
			position: relative;
		}
		
		.overlay-header h2 {
			margin: 0 0 5px 0;
			font-size: 24px;
		}
		
		.overlay-header p {
			margin: 0;
			opacity: 0.9;
		}
		
		.overlay-close {
			position: absolute;
			top: 20px;
			right: 20px;
			background: none;
			border: none;
			color: white;
			font-size: 28px;
			cursor: pointer;
			width: 40px;
			height: 40px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: background-color 0.2s;
		}
		
		.overlay-close:hover {
			background-color: rgba(255, 255, 255, 0.2);
		}
		
		.overlay-body {
			flex: 1;
			padding: 30px;
			overflow-y: auto;
		}
		
		.post-info {
			margin-bottom: 25px;
			padding: 15px;
			background: #f8f9fa;
			border-radius: 8px;
			border-left: 4px solid #0077b5;
		}
		
		.post-info h3 {
			margin: 0 0 10px 0;
			color: #333;
		}
		
		.post-url {
			margin: 0;
			color: #666;
			font-family: monospace;
			font-size: 14px;
			word-break: break-all;
		}
		
		.summary-section {
			margin-bottom: 30px;
		}
		
		.summary-section h4 {
			margin: 0 0 15px 0;
			color: #333;
			font-size: 18px;
		}
		
		.summary-container textarea {
			width: 100%;
			padding: 15px;
			border: 2px solid #e1e5e9;
			border-radius: 8px;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
			font-size: 14px;
			line-height: 1.5;
			resize: vertical;
			min-height: 120px;
		}
		
		.summary-actions {
			margin-top: 10px;
			display: flex;
			gap: 10px;
		}
		
		.linkedin-section {
			margin-bottom: 30px;
		}
		
		.option-tabs {
			display: flex;
			gap: 5px;
			margin-bottom: 15px;
		}
		
		.tab-button {
			padding: 10px 20px;
			background: #f1f3f4;
			border: none;
			border-radius: 6px;
			cursor: pointer;
			font-weight: 500;
			transition: all 0.2s;
		}
		
		.tab-button.active {
			background: #0077b5;
			color: white;
		}
		
		.tab-content {
			display: none;
		}
		
		.tab-content.active {
			display: block;
		}
		
		.iframe-container {
			position: relative;
			width: 100%;
			height: 500px;
			border: 2px solid #e1e5e9;
			border-radius: 8px;
			overflow: hidden;
		}
		
		.iframe-container iframe {
			width: 100%;
			height: 100%;
		}
		
		.iframe-overlay {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(255, 255, 255, 0.95);
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			gap: 15px;
		}
		
		.iframe-overlay.hidden {
			display: none;
		}
		
		.newwindow-actions {
			text-align: center;
			padding: 40px;
			background: #f8f9fa;
			border-radius: 8px;
		}
		
		.ai-automation-section {
			margin-bottom: 30px;
			padding: 20px;
			background: linear-gradient(135deg, #f0f8ff, #e6f3ff);
			border-radius: 8px;
			border: 1px solid #b8daff;
		}
		
		.ai-automation-section h4 {
			margin: 0 0 15px 0;
			color: #0c5460;
		}
		
		.automation-controls {
			text-align: center;
		}
		
		.step-progress {
			margin-top: 20px;
		}
		
		.step {
			display: flex;
			align-items: center;
			gap: 15px;
			padding: 10px;
			margin-bottom: 5px;
			background: white;
			border-radius: 6px;
		}
		
		.step-number {
			width: 30px;
			height: 30px;
			background: #e9ecef;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
			font-size: 14px;
		}
		
		.step.active .step-number {
			background: #ffc107;
			color: #212529;
		}
		
		.step.completed .step-number {
			background: #28a745;
			color: white;
		}
		
		.step-text {
			flex: 1;
		}
		
		.step-status {
			font-size: 18px;
		}
		
		.manual-instructions {
			background: #fff3cd;
			padding: 20px;
			border-radius: 8px;
			border: 1px solid #ffeaa7;
		}
		
		.manual-instructions h4 {
			margin: 0 0 15px 0;
			color: #856404;
		}
		
		.manual-instructions ol {
			margin: 0;
			padding-left: 20px;
		}
		
		.manual-instructions li {
			margin-bottom: 8px;
			line-height: 1.5;
		}
		
		.post-link-for-comment {
			display: inline-block;
			background: #f8f9fa;
			padding: 4px 8px;
			border-radius: 4px;
			font-family: monospace;
			font-size: 12px;
			margin: 0 5px;
		}
		
		.overlay-footer {
			background: #f8f9fa;
			padding: 20px 30px;
			border-top: 1px solid #e9ecef;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		
		@media (max-width: 768px) {
			.overlay-content {
				width: 100%;
				height: 100%;
				margin: 0;
				border-radius: 0;
			}
			
			.overlay-header, .overlay-body, .overlay-footer {
				padding: 15px 20px;
			}
			
			.iframe-container {
				height: 300px;
			}
			
			.overlay-footer {
				flex-direction: column;
				gap: 10px;
			}
		}
		</style>
		<?php
	}
	

	
	/**
	 * Add admin bar button for manual trigger.
	 *
	 * @since    1.0.2
	 * @param    WP_Admin_Bar $wp_admin_bar The admin bar object.
	 */
	public function add_admin_bar_button( $wp_admin_bar ) {
		// Only show on single post edit screens
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->base, array( 'post', 'page' ), true ) ) {
			return;
		}
		
		// Only show if we have a post ID
		if ( ! isset( $_GET['post'] ) ) {
			return;
		}
		
		$post_id = absint( $_GET['post'] );
		$post = get_post( $post_id );
		
		if ( ! $post || $post->post_status !== 'publish' ) {
			return;
		}
		
		// Check settings
		$settings = get_option( 'agentic_social_settings', array() );
		if ( ! isset( $settings['linkedin_enabled'] ) || ! $settings['linkedin_enabled'] ) {
			return;
		}
		
		// Add the button
		$wp_admin_bar->add_node( array(
			'id'    => 'agentic-social-share',
			'title' => '🚀 ' . __( 'Share on LinkedIn', 'agentic-social' ),
			'href'  => '#',
			'meta'  => array(
				'onclick' => 'agenticSocialShowOverlay(' . $post_id . '); return false;',
				'title'   => __( 'Open Agentic Social sharing overlay', 'agentic-social' ),
			),
		) );
	}
	
	/**
	 * Register plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		// Register the main settings
		register_setting( 
			'agentic_social_settings_group', 
			'agentic_social_settings', 
			array( 
				'sanitize_callback' => array( $this, 'validate_settings' ),
				'default' => array()
			) 
		);
		
		// Add settings section
		add_settings_section(
			'agentic_social_general_section',
			__( 'General Settings', 'agentic-social' ),
			array( $this, 'general_section_callback' ),
			'agentic_social_settings_group'
		);
		
		// Add individual settings fields
		add_settings_field(
			'linkedin_enabled',
			__( 'Enable LinkedIn Sharing', 'agentic-social' ),
			array( $this, 'linkedin_enabled_callback' ),
			'agentic_social_settings_group',
			'agentic_social_general_section'
		);
		
		add_settings_field(
			'auto_share',
			__( 'Auto Share', 'agentic-social' ),
			array( $this, 'auto_share_callback' ),
			'agentic_social_settings_group',
			'agentic_social_general_section'
		);
		
		add_settings_field(
			'share_delay',
			__( 'Share Delay (minutes)', 'agentic-social' ),
			array( $this, 'share_delay_callback' ),
			'agentic_social_settings_group',
			'agentic_social_general_section'
		);
		
		add_settings_field(
			'default_post_types',
			__( 'Post Types', 'agentic-social' ),
			array( $this, 'post_types_callback' ),
			'agentic_social_settings_group',
			'agentic_social_general_section'
		);
		
		add_settings_field(
			'add_link_as_comment',
			__( 'Add Link as Comment', 'agentic-social' ),
			array( $this, 'add_link_as_comment_callback' ),
			'agentic_social_settings_group',
			'agentic_social_general_section'
		);
		
		add_settings_field(
			'enable_ai_agent',
			__( 'Enable AI Agent Mode', 'agentic-social' ),
			array( $this, 'enable_ai_agent_callback' ),
			'agentic_social_settings_group',
			'agentic_social_general_section'
		);
	}
	
	/**
	 * General section callback.
	 *
	 * @since    1.0.0
	 */
	public function general_section_callback() {
		echo '<p>' . esc_html__( 'Configure the general settings for Agentic Social sharing.', 'agentic-social' ) . '</p>';
	}
	
	/**
	 * LinkedIn enabled field callback.
	 *
	 * @since    1.0.0
	 */
	public function linkedin_enabled_callback() {
		$settings = get_option( 'agentic_social_settings', array() );
		$value = isset( $settings['linkedin_enabled'] ) ? $settings['linkedin_enabled'] : false;
		?>
		<input type="checkbox" id="linkedin_enabled" name="agentic_social_settings[linkedin_enabled]" value="1" <?php checked( $value, true ); ?> />
		<p class="description"><?php esc_html_e( 'Enable sharing posts to LinkedIn.', 'agentic-social' ); ?></p>
		<?php
	}
	
	/**
	 * Auto share field callback.
	 *
	 * @since    1.0.0
	 */
	public function auto_share_callback() {
		$settings = get_option( 'agentic_social_settings', array() );
		$value = isset( $settings['auto_share'] ) ? $settings['auto_share'] : false;
		?>
		<input type="checkbox" id="auto_share" name="agentic_social_settings[auto_share]" value="1" <?php checked( $value, true ); ?> />
		<p class="description"><?php esc_html_e( 'Automatically share new posts when published.', 'agentic-social' ); ?></p>
		<?php
	}
	
	/**
	 * Share delay field callback.
	 *
	 * @since    1.0.0
	 */
	public function share_delay_callback() {
		$settings = get_option( 'agentic_social_settings', array() );
		$value = isset( $settings['share_delay'] ) ? $settings['share_delay'] : 5;
		?>
		<input type="number" id="share_delay" name="agentic_social_settings[share_delay]" value="<?php echo esc_attr( $value ); ?>" min="0" max="60" />
		<p class="description"><?php esc_html_e( 'Delay in minutes before sharing after publishing.', 'agentic-social' ); ?></p>
		<?php
	}
	
	/**
	 * Post types field callback.
	 *
	 * @since    1.0.0
	 */
	public function post_types_callback() {
		$settings = get_option( 'agentic_social_settings', array() );
		$selected_types = isset( $settings['default_post_types'] ) ? $settings['default_post_types'] : array( 'post' );
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		
		foreach ( $post_types as $post_type ) {
			if ( 'attachment' === $post_type->name ) {
				continue;
			}
			?>
			<label style="display: block; margin-bottom: 5px;">
				<input type="checkbox" name="agentic_social_settings[default_post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $selected_types, true ) ); ?> />
				<?php echo esc_html( $post_type->label ); ?>
			</label>
			<?php
		}
		?>
		<p class="description"><?php esc_html_e( 'Select which post types can be shared.', 'agentic-social' ); ?></p>
		<?php
	}
	
	/**
	 * Add link as comment field callback.
	 *
	 * @since    1.0.0
	 */
	public function add_link_as_comment_callback() {
		$settings = get_option( 'agentic_social_settings', array() );
		$value = isset( $settings['add_link_as_comment'] ) ? $settings['add_link_as_comment'] : true;
		?>
		<input type="checkbox" id="add_link_as_comment" name="agentic_social_settings[add_link_as_comment]" value="1" <?php checked( $value, true ); ?> />
		<p class="description"><?php esc_html_e( 'Add post links as comments instead of in the main post (better for platform algorithms).', 'agentic-social' ); ?></p>
		<?php
	}
	
	/**
	 * Enable AI agent field callback.
	 *
	 * @since    1.0.0
	 */
	public function enable_ai_agent_callback() {
		$settings = get_option( 'agentic_social_settings', array() );
		$value = isset( $settings['enable_ai_agent'] ) ? $settings['enable_ai_agent'] : false;
		?>
		<input type="checkbox" id="enable_ai_agent" name="agentic_social_settings[enable_ai_agent]" value="1" <?php checked( $value, true ); ?> />
		<p class="description"><?php esc_html_e( 'Design interactions for AI agentic browsers like Perplexity\'s Comet.', 'agentic-social' ); ?></p>
		<?php
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
	 * AJAX handler for getting overlay HTML.
	 *
	 * @since    1.0.2
	 */
	public function ajax_get_overlay_html() {
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
		
		// Set the flag to show overlay
		update_user_meta( get_current_user_id(), 'agentic_social_show_overlay', $post_id );
		
		// Return success and reload page
		wp_send_json_success( array(
			'message' => __( 'Overlay will be shown on page reload', 'agentic-social' ),
			'reload' => true,
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
