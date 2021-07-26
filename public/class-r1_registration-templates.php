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
	 * Registration form HTML output.
	 *
	 * @since    1.0.0
	 */
	public function registration_form_output() {

		ob_start();

		?>

		<form id="registrationForm" class="registration" novalidate>

			<div class="input-wrapper">
				<label for="userFullName"><?php esc_html_e( 'ПІБ', 'r1_registration' ); ?></label>
				<input id="userFullName" type="text" name="userFullName"/>
			</div>

			<div class="input-wrapper">
				<label for="userEmail"><?php esc_html_e( 'Email', 'r1_registration' ); ?></label>
				<input id="userEmail" type="email" name="userEmail"/>
			</div>

			<div class="input-wrapper">
				<label for="userPassword"><?php esc_html_e( 'Пароль', 'r1_registration' ); ?></label>
				<input id="userPassword" type="password" name="userPassword"/>
			</div>

			<div class="input-wrapper">
				<label for="userPasswordConfirm"><?php esc_html_e( 'Повторити пароль', 'r1_registration' ); ?></label>
				<input id="userPasswordConfirm" type="password" name="userPasswordConfirm"/>
			</div>

			<ul class="form-errors"></ul>

			<button type="submit" class="button"><?php esc_html_e( 'Відправити', 'r1_registration' ); ?></button>

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
				<label for="email"><?php esc_html_e( 'Email', 'r1_registration' ); ?></label>
				<input id="userEmail" type="email" name="email" />
			</div>

			<div class="input-wrapper">
				<label for="password"><?php esc_html_e( 'Пароль', 'r1_registration' ); ?></label>
				<input id="password" type="password" name="password"/>
			</div>

			<ul class="form-errors"></ul>

			<a href="<?php echo get_site_url() . '/forgot-password'; ?>">
				<?php esc_html_e( 'Забули пароль?', 'r1_registration' ); ?>
			</a>

			<button type="submit" class="button"><?php esc_html_e( 'Увійти', 'r1_registration' ); ?></button>

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
				<label for="user_login"><?php esc_html_e( 'Email', 'r1_registration' ); ?></label>
				<input id="user_login" type="text" name="user_login" required>
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

			<p class="form-success" style="display: none;">Check your email and follow the instructions.</p>

			<a href="<?php echo esc_url( get_site_url() ) . '/login'; ?>">
				<?php esc_html_e( 'Згадали пароль?', 'r1_registration' ); ?>
			</a>

			<button type="submit" class="button"><?php esc_html_e( 'Увійти', 'r1_registration' ); ?></button>

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
				<input type="hidden" name="user_login" id="user_login" value="<?php echo esc_attr( $login ); ?>" autocomplete="off" />

				<div class="input-wrapper">
					<label for="pass1"><?php esc_html_e( 'Пароль', 'r1_registration' ); ?></label>
					<input id="pass1" type="password" name="pass1" autocomplete="off" />
					<a href="javascript:;" class="toggle-password">Show password</a>
				</div>

				<div class="input-wrapper">
					<label for="pass2"><?php esc_html_e( 'Повторіть Пароль', 'r1_registration' ); ?></label>
					<input id="pass2" type="password" name="pass2" autocomplete="off" />
					<a href="javascript:;" class="toggle-password">Show password</a>
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

				<button type="submit" class="button"><?php esc_html_e( 'Змінити', 'r1_registration' ); ?></button>

			</form>

			<?php

			$out = ob_get_clean();

		endif;

		return $out;

	}

}
