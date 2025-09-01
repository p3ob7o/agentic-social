<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://paolobelcastro.com
 * @since      1.0.0
 *
 * @package    Agentic_Social
 * @subpackage Agentic_Social/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Agentic_Social
 * @subpackage Agentic_Social/includes
 * @author     Paolo Belcastro <paolo@paolobelcastro.com>
 */
class Agentic_Social_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Clear scheduled cron events
		wp_clear_scheduled_hook( 'agentic_social_daily_sync' );
		
		// Clear any transients
		delete_transient( 'agentic_social_cache' );
		
		// Flush rewrite rules
		flush_rewrite_rules();
	}

}
