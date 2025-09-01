<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://paolobelcastro.com
 * @since      1.0.0
 *
 * @package    Agentic_Social
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Clean up plugin data
 *
 * This function removes all data created by the plugin:
 * - Options
 * - Database tables (if any)
 * - Scheduled events
 * - Transients
 * - User meta
 * - Post meta
 */
function agentic_social_uninstall() {
	global $wpdb;

	// Remove plugin options
	delete_option( 'agentic_social_settings' );
	delete_option( 'agentic_social_version' );
	delete_option( 'agentic_social_linkedin_settings' );
	
	// Remove any transients
	delete_transient( 'agentic_social_cache' );
	
	// Clear any scheduled hooks
	wp_clear_scheduled_hook( 'agentic_social_daily_sync' );
	
	// For multisite installations
	if ( is_multisite() ) {
		// Get all blogs in the network and delete plugin data for each one
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			
			// Remove plugin options for this blog
			delete_option( 'agentic_social_settings' );
			delete_option( 'agentic_social_version' );
			delete_option( 'agentic_social_linkedin_settings' );
			
			// Remove transients for this blog
			delete_transient( 'agentic_social_cache' );
			
			// Clear scheduled hooks for this blog
			wp_clear_scheduled_hook( 'agentic_social_daily_sync' );
			
			restore_current_blog();
		}
	}
	
	// Remove any custom post meta created by the plugin
	$wpdb->delete(
		$wpdb->postmeta,
		array( 'meta_key' => '_agentic_social_share_status' ),
		array( '%s' )
	);
	
	$wpdb->delete(
		$wpdb->postmeta,
		array( 'meta_key' => '_agentic_social_linkedin_id' ),
		array( '%s' )
	);
	
	// Flush rewrite rules
	flush_rewrite_rules();
}

agentic_social_uninstall();
