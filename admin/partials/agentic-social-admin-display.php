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
		submit_button();
		?>
	</form>
</div>
