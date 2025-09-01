<?php
/**
 * Fired during plugin activation
 *
 * @link       https://paolobelcastro.com
 * @since      1.0.0
 *
 * @package    Agentic_Social
 * @subpackage Agentic_Social/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Agentic_Social
 * @subpackage Agentic_Social/includes
 * @author     Paolo Belcastro <paolo@paolobelcastro.com>
 */
class Agentic_Social_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Check WordPress version
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
			wp_die( esc_html__( 'This plugin requires WordPress version 5.8 or higher.', 'agentic-social' ) );
		}
		
		// Check PHP version
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			wp_die( esc_html__( 'This plugin requires PHP version 7.4 or higher.', 'agentic-social' ) );
		}
		
		// Create default options
		$default_settings = array(
			'linkedin_enabled'     => false,
			'auto_share'          => false,
			'share_delay'         => 5,
			'default_post_types'  => array( 'post' ),
			'add_link_as_comment' => true,
			'enable_ai_agent'     => false,
		);
		
		// Add default settings if they don't exist
		if ( false === get_option( 'agentic_social_settings' ) ) {
			add_option( 'agentic_social_settings', $default_settings );
		}
		
		// Store plugin version
		add_option( 'agentic_social_version', AGENTIC_SOCIAL_VERSION );
		
		// Create database tables if needed (placeholder for future use)
		self::create_tables();
		
		// Schedule cron events
		if ( ! wp_next_scheduled( 'agentic_social_daily_sync' ) ) {
			wp_schedule_event( time(), 'daily', 'agentic_social_daily_sync' );
		}
		
		// Flush rewrite rules
		flush_rewrite_rules();
	}
	
	/**
	 * Create custom database tables
	 *
	 * @since    1.0.0
	 */
	private static function create_tables() {
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		// Table for storing share history
		$table_name = $wpdb->prefix . 'agentic_social_shares';
		
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			post_id bigint(20) NOT NULL,
			platform varchar(50) NOT NULL,
			share_status varchar(20) NOT NULL,
			share_url text,
			share_id varchar(255),
			share_date datetime DEFAULT CURRENT_TIMESTAMP,
			error_message text,
			PRIMARY KEY (id),
			KEY post_id (post_id),
			KEY platform (platform),
			KEY share_status (share_status)
		) $charset_collate;";
		
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

}
