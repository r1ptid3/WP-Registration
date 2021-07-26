<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/includes
 */

// Enable strict typing mode.
declare( strict_types = 1 );

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    R1_Registration
 * @subpackage R1_Registration/includes
 */
class R1_Registration_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain(): void {

		load_plugin_textdomain(
			'r1_registration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
