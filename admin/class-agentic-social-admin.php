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
		
		?>
		<div class="agentic-social-meta-box">
			<p>
				<label for="agentic_social_share_status">
					<input type="checkbox" name="agentic_social_share_status" id="agentic_social_share_status" value="1" <?php checked( $share_status, '1' ); ?> />
					<?php esc_html_e( 'Share this post on social media', 'agentic-social' ); ?>
				</label>
			</p>
			
			<?php if ( $linkedin_id ) : ?>
				<p>
					<strong><?php esc_html_e( 'LinkedIn Post ID:', 'agentic-social' ); ?></strong><br>
					<code><?php echo esc_html( $linkedin_id ); ?></code>
				</p>
			<?php endif; ?>
			
			<p>
				<label for="agentic_social_custom_message">
					<?php esc_html_e( 'Custom Message (optional):', 'agentic-social' ); ?>
				</label><br>
				<textarea name="agentic_social_custom_message" id="agentic_social_custom_message" rows="3" style="width: 100%;"><?php echo esc_textarea( $custom_message ); ?></textarea>
			</p>
			
			<p>
				<button type="button" class="button" id="agentic-social-share-now">
					<?php esc_html_e( 'Share Now', 'agentic-social' ); ?>
				</button>
			</p>
		</div>
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

}
