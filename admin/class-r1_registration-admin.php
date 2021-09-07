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
	 * Define array which contains all registration form fields.
	 *
	 * @since    1.1.0
	 *
	 * @access   protected
	 * @var      array $registration_form_fields - The array which contains all registration form fields.
	 */
	private array $registration_form_fields;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param    string $plugin_name - The name of this plugin.
	 * @param    string $version - The version of this plugin.
	 * @param    array $registration_form_fields - registration form fields.
	 */
	public function __construct( string $plugin_name, string $version, array $registration_form_fields ) {

		$this->plugin_name              = $plugin_name;
		$this->version                  = $version;
		$this->registration_form_fields = $registration_form_fields;

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

			<h2><?php esc_html_e( 'Custom Registration Fields', 'r1_registration' ); ?></h2>

				<table class="form-table r1-registration-table">

				<?php

				foreach ( $this->registration_form_fields as $id => $field ) {

					if ( 'email' !== $field['type'] && 'password' !== $field['type'] ) {

						?>

						<tr>
							<th>
								<?php if ( ! empty( $field['label'] ) ) : ?>

								<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>

								<?php endif; ?>
							</th>
							<td>

							<?php

							$output = '';

							// Configure tag.
							switch ( $field['type'] ) {

								case 'select':
									$multiple = true === $field['multiple'] ? 'multiple' : '';
									$selected = '';

									$output .= '<select type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '[]" ' . $multiple . '>';

									if ( ! empty( $field['options'] ) ) {

										foreach ( $field['options'] as $val => $text ) {

											// Check is anything choosen.
											if ( ! empty( get_the_author_meta( $id, $user->ID ) ) ) {

												// Check is option selected.
												if (
													( ! empty( $multiple ) && is_array( get_the_author_meta( $id, $user->ID ) ) && in_array( $val, get_the_author_meta( $id, $user->ID ), true ) ) ||
													( empty( $multiple ) && get_the_author_meta( $id, $user->ID ) === $val )
												) {
													$selected = 'selected';
												} else {
													$selected = '';
												}
											}

											$output .= '<option value="' . $val . '" ' . $selected . '>' . $text . '</option>';

										}
									}

									$output .= '</select>';

									break;

								case 'textarea':
									$output .= '<textarea type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '">' . esc_html( get_the_author_meta( $id, $user->ID ) ) . '</textarea>';

									break;

								case 'radio':
									if ( ! empty( $field['options'] ) ) {
										foreach ( $field['options'] as $val => $text ) {
											$output .= '<div class="radio-item">';

												$checked = get_the_author_meta( $id, $user->ID ) === $val ? 'checked' : '';

												$output .= '<input id="' . $val . '" value="' . $val . '" type="radio" name="' . $id . '" ' . $checked . '>';
												$output .= '<label class="radio-label" for="' . $val . '">' . $text . '</label>';

											$output .= '</div>';
										}
									}

									break;

								case 'checkbox':
									$checked_on  = 'on' === get_the_author_meta( $id, $user->ID ) ? 'checked' : '';
									$checked_off = '' === get_the_author_meta( $id, $user->ID ) ? 'checked' : '';

									$output .= '<input type="hidden" id="' . $id . '_custom" name="' . $id . '" value="off" ' . $checked_off . ' />';
									$output .= '<input type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '" value="on" ' . $checked_on . ' />';

									break;

								default:
									// Default input.
									$output .= '<input class="regular-text" type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '" value="' . get_the_author_meta( $id, $user->ID ) . '" />';

									break;
							}

							// phpcs:ignore
							echo $output;

							?>

							</td>
						</tr>

						<?php
					}
				}

				?>

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
			! current_user_can( 'edit_user', $user_id ) ||
			! isset( $_POST['_r1_registration_nonce'] ) ||
			! wp_verify_nonce( $_POST['_r1_registration_nonce'], 'r1_registration_nonce' ) //phpcs:ignore.
		) {
			return;
		}

		foreach ( $this->registration_form_fields as $id => $field ) {

			switch ( $field['type'] ) {

				case 'select':
					if ( true === $field['multiple'] ) {
						// Update multiple select.
						if ( ! empty( $_POST[ $id ] ) ) {
							update_user_meta( $user_id, $id, $_POST[ $id ] ); //phpcs:ignore.
						}
					} else {
						// Update default select.
						if ( ! empty( $_POST[ $id ][0] ) ) {
							update_user_meta( $user_id, $id, sanitize_text_field( wp_unslash( $_POST[ $id ][0] ) ) );
						}
					}

					break;

				default:
					if ( ! empty( $_POST[ $id ] ) ) {
						update_user_meta( $user_id, $id, sanitize_text_field( wp_unslash( $_POST[ $id ] ) ) );
					}

					break;
			}
		}

	}

}
