<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/admin
 */

// Enable strict typing mode.
declare( strict_types = 1 );

/**
 * The admin-specific functionality of the plugin.
 *
 * An instance of this class should be passed to the run() function
 *
 * Defines the plugin name, version, and enqueue
 * the admin-specific stylesheet and JavaScript.
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/admin
 */
class R1_Registration_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 * @var      string $plugin_name - The ID of this plugin.
	 */
	private string $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 * @var      string $version - The current version of this plugin.
	 */
	private string $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param    string $plugin_name - The name of this plugin.
	 * @param    string $version - The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/r1_registration-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/r1_registration-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * HTML output for custom user meta.
	 *
	 * @since    1.0.0
	 *
	 * @param    object $user - User oject.
	 */
	public function r1_custom_user_profile_fields( object $user ) {
		?>
			<h2><?php esc_html_e( 'Custom Fields', 'r1_registration' ); ?></h2>
			<table class="form-table">
				<tr>
					<th>
						<label for="full_name"><?php esc_html_e( 'Full Name', 'r1_registration' ); ?></label>
					</th>
					<td>
						<input type="text" name="full_name" id="full_name" value="<?php echo esc_attr( get_the_author_meta( 'full_name', $user->ID ) ); ?>" class="regular-text" />
					</td>
				</tr>
			</table>
			<?php wp_nonce_field( 'r1_registration_nonce', '_r1_registration_nonce', false ); ?>
		<?php
	}

	/**
	 * Save custom user meta on backend.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $user_id - User oject.
	 */
	public function r1_update_extra_profile_fields( int $user_id ) {

		// Check for nonce otherwise bail.
		if (
			! isset( $_POST['_r1_registration_nonce'] ) ||
			! wp_verify_nonce( $_POST['_r1_registration_nonce'], 'r1_registration_nonce' ) //phpcs:ignore.
		) {
			return;
		}

		$full_name = ! empty( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';

		if ( current_user_can( 'edit_user', $user_id ) ) {
			update_user_meta( $user_id, 'full_name', $full_name );
		}
	}

}
