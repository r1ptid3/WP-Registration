<?php
/**
 * Starter r1ptid3's WP Plugin
 *
 * @since             1.0.0
 * @package           R1_Registration
 *
 * @wordpress-plugin
 * Plugin Name:       R1 Registration Plugin
 * Description:       Starter registration WordPress Plugin
 * Version:           1.0.0
 * Author:            r1ptid3
 * Author URI:        https://github.com/r1ptid3
 * License:           GNU General Public License v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:       r1_registration
 * Domain Path:       /languages
 */

// Enable strict typing mode.
declare( strict_types = 1 );

// Disable direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Currently plugin version.
define( 'R1_REGISTRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 *
 * @return  void
 */
function activate_r1_registration(): void {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-r1_registration-activator.php';
	R1_Registration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * @return  void
 */
function deactivate_r1_registration(): void {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-r1_registration-deactivator.php';
	R1_Registration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_r1_registration' );
register_deactivation_hook( __FILE__, 'deactivate_r1_registration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-r1_registration.php';


/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 *
 * @return  void
 */
function run_r1_registration(): void {

	$plugin = new R1_Registration();
	$plugin->run();

}
run_r1_registration();
