<?php
/**
 * Class which provides HTML templates for shortcodes.
 *
 * @since      1.0.0
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/public
 */

// Enable strict typing mode.
declare( strict_types = 1 );

/**
 * The public-facing functionality which provides HTML templates for shortcodes.
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/public
 */
class R1_Registration_Templates {

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
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.1.0
	 *
	 * @param    array $registration_form_fields - registration form fields.
	 */
	public function __construct( array $registration_form_fields ) {
		$this->registration_form_fields = $registration_form_fields;
	}

	/**
	 * Registration form HTML output.
	 *
	 * @since    1.0.0
	 */
	public function registration_form_output() {

		ob_start();

		?>

		<form id="registrationForm" class="registration" novalidate>

			<?php $this->create_registration_form_html(); ?>

			<ul class="form-errors"></ul>

			<button type="submit" class="button"><?php esc_html_e( 'Register', 'r1_registration' ); ?></button>

		</form>

		<?php

		$out = ob_get_clean();

		return $out;

	}

	/**
	 * Login form HTML output.
	 *
	 * @since    1.0.0
	 */
	public function login_form_output() {

		ob_start();

		?>

		<form id="loginForm" novalidate>

			<div class="input-wrapper">
				<label for="user_email"><?php esc_html_e( 'Email', 'r1_registration' ); ?></label>
				<input id="user_email" type="email" name="user_email" />
			</div>

			<div class="input-wrapper">
				<label for="user_password"><?php esc_html_e( 'Password', 'r1_registration' ); ?></label>
				<input id="user_password" type="password" name="user_password"/>
				<a href="javascript:;" class="toggle-password"></a>
			</div>

			<ul class="form-errors"></ul>

			<a href="<?php echo get_site_url() . '/forgot-password'; ?>">
				<?php esc_html_e( 'Forgot Password?', 'r1_registration' ); ?>
			</a>

			<button type="submit" class="button"><?php esc_html_e( 'Log in', 'r1_registration' ); ?></button>

		</form>

		<?php

		$out = ob_get_clean();

		return $out;

	}

	/**
	 * Forgot password form HTML output.
	 *
	 * @since    1.0.0
	 */
	public function forgot_pass_form_output() {

		ob_start();

		?>

		<form id="forgotPasswordForm" novalidate>

			<div class="input-wrapper">
				<label for="user_email"><?php esc_html_e( 'Email', 'r1_registration' ); ?></label>
				<input id="user_email" type="text" name="user_email" required>
			</div>

			<?php

				/**
				 * Fires inside the lostpassword <form> tags, before the hidden fields.
				 *
				 * @since 2.1.0
				 */
				do_action( 'lostpassword_form' );

			?>

			<ul class="form-errors"></ul>

			<p class="form-success" style="display: none;"><?php esc_html_e( 'Check your email and follow the instructions.', 'r1_registration' ) ?></p>

			<a href="<?php echo esc_url( get_site_url() ) . '/login'; ?>">
				<?php esc_html_e( 'Remember Password?', 'r1_registration' ); ?>
			</a>

			<button type="submit" class="button"><?php esc_html_e( 'Send', 'r1_registration' ); ?></button>

		</form>

		<?php

		$out = ob_get_clean();

		return $out;

	}

	/**
	 * Reset password form HTML output.
	 *
	 * @since    1.0.0
	 */
	public function reset_pass_form_output() {

		$temp_errors = new WP_Error();

		$key   = '';
		$login = '';
		$user  = '';

		if ( ! empty( $_GET['key'] ) && ! empty( $_GET['login'] ) ) {

			$key   = sanitize_text_field( wp_unslash( $_GET['key'] ) );
			$login = sanitize_text_field( wp_unslash( $_GET['login'] ) );

		}

		$user = check_password_reset_key( $key, $login );

		if ( is_wp_error( $user ) ) :

			if ( $user->get_error_code() === 'expired_key' ) {

				$temp_errors->add( 'expiredkey', __( 'Sorry, that key has expired. Please try again.', 'r1_registration' ) );

			} else {

				$temp_errors->add( 'invalidkey', __( 'Sorry, that key does not appear to be valid.', 'r1_registration' ) );

			}

			$out = '<p class="reset-failure">' . $temp_errors->get_error_message( $temp_errors->get_error_code() ) . '</p>';

		else :

			ob_start();

			?>

			<form id="resetPasswordForm" method="post" autocomplete="off" novalidate>

				<input type="hidden" name="user_key" id="user_key" value="<?php echo esc_attr( $key ); ?>" autocomplete="off" />
				<input type="hidden" name="user_email" id="user_email" value="<?php echo esc_attr( $login ); ?>" autocomplete="off" />

				<div class="input-wrapper">
					<label for="user_password"><?php esc_html_e( 'Password', 'r1_registration' ); ?></label>
					<input id="user_password" type="password" name="user_password" autocomplete="off" />
					<a href="javascript:;" class="toggle-password"></a>
				</div>

				<div class="input-wrapper">
					<label for="user_password_confirm"><?php esc_html_e( 'Confirm Password', 'r1_registration' ); ?></label>
					<input id="user_password_confirm" type="password" name="user_password_confirm" autocomplete="off" />
					<a href="javascript:;" class="toggle-password"></a>
				</div>

				<?php
					/**
					 * Fires following the 'Strength indicator' meter in the user password reset form.
					 *
					 * @param WP_User $user User object of the user whose password is being reset.
					 * @since 3.9.0
					 */
					do_action( 'resetpass_form', $user );
				?>

				<button type="submit" class="button"><?php esc_html_e( 'Change Password', 'r1_registration' ); ?></button>

			</form>

			<?php

			$out = ob_get_clean();

		endif;

		return $out;

	}

	/**
	 * Function which forming registration form HTML.
	 *
	 * @since    1.1.0
	 */
	public function create_registration_form_html() {

		$out = '';

		foreach ( $this->registration_form_fields as $id => $field ) {

			$out .= '<div class="input-wrapper ' . $field['type'] . '">';

			if ( ! empty( $field['label'] ) ) {
				$out .= '<label for="' . $id . '">' . $field['label'] . '</label>';
			}

			$placeholder = ! empty( $field['placeholder'] ) ? 'placeholder="' . $field['placeholder'] . '"' : '';

			// Configure tag.
			switch ( $field['type'] ) {

				case 'select':

					if ( true === $field['multiple'] ) {
						$out .= '<select type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '[]" multiple>';
					} else {
						$out .= '<select type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '">';
					}

					if ( ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $val => $text ) {
							$out .= '<option value="' . $val . '">' . $text . '</option>';
						}
					}

					$out .= '</select>';

					break;

				case 'textarea':
					$out .= '<textarea type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '" ' . $placeholder . '></textarea>';
					break;

				case 'radio':
					if ( ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $val => $text ) {
							$out .= '<div class="radio-item">';

								$out .= '<input id="' . $val . '" value="' . $val . '" type="radio" name="' . $id . '">';
								$out .= '<label class="radio-label" for="' . $val . '">' . $text . '</label>';

							$out .= '</div>';
						}
					}

					break;

				case 'password':
					$out .= '<input type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '" ' . $placeholder . ' >';
					$out .= '<a href="javascript:;" class="toggle-password"></a>';

					break;

				default:
					// Default input.
					$out .= '<input type="' . $field['type'] . '" id="' . $id . '" name="' . $id . '" ' . $placeholder . ' />';

					break;
			}

			$out .= '</div>';

		}

		echo $out;

	}

}
