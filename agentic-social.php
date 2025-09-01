<?php
/**
 * Plugin Name:       Agentic Social Sharing
 * Plugin URI:        https://paolobelcastro.com/agentic-social
 * Description:       A WordPress plugin to help publishers post their content to social platforms in a semi-automated manner satisfying algorithm requirements. Posting content doesn't happen via API, and links are added as a comment to the post. Every manual interaction is designed in a way that makes its handling automatically by agentic browsers possible. 
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Paolo Belcastro
 * Author URI:        https://paolobelcastro.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       agentic-social
 * Domain Path:       /languages
 *
 * @package Agentic_Social
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AGENTIC_SOCIAL_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'AGENTIC_SOCIAL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'AGENTIC_SOCIAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'AGENTIC_SOCIAL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-agentic-social-activator.php
 */
function activate_agentic_social() {
	require_once AGENTIC_SOCIAL_PLUGIN_DIR . 'includes/class-agentic-social-activator.php';
	Agentic_Social_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-agentic-social-deactivator.php
 */
function deactivate_agentic_social() {
	require_once AGENTIC_SOCIAL_PLUGIN_DIR . 'includes/class-agentic-social-deactivator.php';
	Agentic_Social_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_agentic_social' );
register_deactivation_hook( __FILE__, 'deactivate_agentic_social' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require AGENTIC_SOCIAL_PLUGIN_DIR . 'includes/class-agentic-social.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_agentic_social() {

	$plugin = new Agentic_Social();
	$plugin->run();

}
run_agentic_social();
