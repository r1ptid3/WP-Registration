<?php
/**
 * Class which describes callback functions for ajax and helper functions.
 *
 * @since      1.0.0
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/public
 */

// Enable strict typing mode.
declare( strict_types = 1 );

/**
 * Ajax callback and some helper functions.
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/public
 */
class R1_Registration_Callbacks {

	/**
	 * Creating new user function
	 *
	 * @since    1.0.0
	 */
	public function register_user_callback() {

		// Checking nonce for security!
		if ( ! wp_verify_nonce( $_POST['nonce_ajax'], 'nonce-ajax-security' ) ) { // phpcs:ignore
			wp_send_json_error( 'Invalid security token sent!' );
		}

		// phpcs:ignore
		parse_str( $_POST['query'], $array );

		// Create WP_Error object.
		$errors = new WP_Error();

		// Get fields from parsed $_POST.
		$user_name             = $array['userFullName'];
		$user_email            = $array['userEmail'];
		$user_password         = $array['userPassword'];
		$user_password_confirm = $array['userPasswordConfirm'];

		// Validate user email & existance.
		$errors = $this->verify_user( $user_email, true );

		// Validate passwords.
		$errors = $this->verify_passwords( $user_password, $user_password_confirm );

		// Create new user. wp_insert_user will sanitize all data by itself.
		if ( ! empty( $user_email ) && ! empty( $user_password ) ) {

			$userdata = array(
				'user_login' => $user_email,
				'user_pass'  => $user_password,
				'user_email' => $user_email,
			);

			$user_id = wp_insert_user( $userdata );

			// Check for errors.
			if ( is_wp_error( $user_id ) ) {

				$errors->add( 'user_error', $user_id->get_error_message() );

			}
		}

		// Show errors and die if any exist.
		$this->error_helper_handler( $errors );

		// Update all optional user meta fields.
		update_user_meta( $user_id, 'full_name', $user_name );

		wp_die();
	}


	/**
	 * Login user function
	 *
	 * @since    1.0.0
	 */
	public function user_login_callback() {

		// Checking nonce for security!
		if ( ! wp_verify_nonce( $_POST['nonce_ajax'], 'nonce-ajax-security' ) ) { // phpcs:ignore
			wp_send_json_error( 'Invalid security token sent!' );
		}

		// phpcs:ignore
		parse_str( $_POST['query'], $array );

		// Create WP_Error object.
		$errors = new WP_Error();

		// Get fields from parsed $_POST.
		$creds                  = array();
		$creds['user_login']    = wp_unslash( $array['email'] );
		$creds['user_password'] = $array['password'];
		$creds['remember']      = false;

		// Verify user.
		$this->verify_user( $creds['user_login'], false );

		// Get user object.
		$user = get_user_by( 'login', $creds['user_login'] );

		// Validate password.
		if ( ! wp_check_password( $creds['user_password'], $user->user_pass, $user->ID ) ) {

			$errors->add( 'invalid_pass', __( 'The password you entered is incorrect', 'r1_registration' ) );

			$this->error_helper_handler( $errors );

		}

		// Sign in account by credentials.
		$user = wp_signon( $creds, true );

		// Check for errors.
		if ( is_wp_error( $user ) ) {

			$errors->add( 'other_error', __( 'Please check login or password', 'r1_registration' ) );

		}

		$this->error_helper_handler( $errors );

		wp_die();
	}


	/**
	 * Lost password function
	 *
	 * @since    1.0.0
	 */
	public function lost_pass_callback() {

		// Checking nonce for security!
		if ( ! wp_verify_nonce( $_POST['nonce_ajax'], 'nonce-ajax-security' ) ) { // phpcs:ignore
			wp_send_json_error( 'Invalid security token sent!' );
		}

		// Load global variables.
		global $wpdb, $wp_hasher;

		// phpcs:ignore
		parse_str( $_POST['query'], $array );

		// Create WP_Error object.
		$errors = new WP_Error();

		// Get fields from parsed $_POST.
		$user_login = $array['user_login'];

		// Validate entered email.
		$this->verify_user( $user_login, false );

		// Get user object.
		$user_data = get_user_by( 'email', trim( $user_login ) );

		// Validate user.
		if ( empty( $user_data ) || ! $user_data ) {
			$errors->add( 'invalid_email', __( 'There is no user registered with that email address.', 'r1_registration' ) );

			$this->error_helper_handler( $errors );
		}

		/**
		 * Fires before errors are returned from a password reset request.
		 *
		 * @param WP_Error $errors A WP_Error object containing any errors generated
		 *                         by using invalid credentials.
		 * @since 4.4.0 Added the `$errors` parameter.
		 *
		 * @since 2.1.0
		 */
		do_action( 'lostpassword_post', $errors );

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key        = get_password_reset_key( $user_data );

		if ( is_wp_error( $key ) ) {
			echo json_encode( $key );
			wp_die();
		}

		// Formating email.
		$message  = __( 'Someone requested that the password be reset for the following account:' ) . "\r\n\r\n";
		$message .= home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
		$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ) . "\r\n\r\n";
		$message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";

		// Replace PAGE_ID with reset page ID.
		$message .= esc_url_raw( home_url( 'reset-password' ) . "/?action=rp&key=$key&login=" . rawurlencode( $user_login ) ) . "\r\n";

		if ( is_multisite() ) {
			$blogname = $GLOBALS['current_site']->site_name;
		} else {
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}

		$title = sprintf( __( '[%s] Password Reset' ), $blogname );

		/**
		 * Filter the subject of the password reset email.
		 *
		 * @param string $title Default email title.
		 * @param string $user_login The username for the user.
		 * @param WP_User $user_data WP_User object.
		 * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
		 *
		 * @since 2.8.0
		 */
		$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

		/**
		 * Filter the message body of the password reset mail.
		 *
		 * @param string $message Default mail message.
		 * @param string $key The activation key.
		 * @param string $user_login The username for the user.
		 * @param WP_User $user_data WP_User object.
		 * @since 2.8.0
		 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
		 */
		$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

		// Sending email.
		$send_response = wp_mail( $user_email, wp_specialchars_decode( $title ), $message );

		if ( ! $send_response ) {
			$errors->add( 'could_not_sent', __( 'The e-mail could not be sent.', 'r1_registration' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'r1_registration' ), 'message' );
		}

		// Show errors and die if any exist.
		$this->error_helper_handler( $errors );

		wp_die();
	}


	/**
	 * Reset password function
	 *
	 * @since    1.0.0
	 */
	public function reset_pass_callback() {

		// Checking nonce for security!
		if ( ! wp_verify_nonce( $_POST['nonce_ajax'], 'nonce-ajax-security' ) ) { // phpcs:ignore
			wp_send_json_error( 'Invalid security token sent!' );
		}

		// phpcs:ignore
		parse_str( $_POST['query'], $array );

		// Create WP_Error object.
		$errors = new WP_Error();

		// Get fields from parsed $_POST.
		$pass1 = $array['pass1'];
		$pass2 = $array['pass2'];
		$key   = $array['user_key'];
		$login = sanitize_email( $array['user_login'] );

		$user = check_password_reset_key( $key, $login );

		// Validate passwords.
		$this->verify_passwords( $pass1, $pass2 );

		/**
		 * Fires before the password reset procedure is validated.
		 *
		 * @param object $errors WP Error object.
		 * @param WP_User|WP_Error $user WP_User object if the login and reset key match. WP_Error object otherwise.
		 * @since 3.5.0
		 */
		do_action( 'validate_password_reset', $errors, $user );

		// Reset password for a user.
		reset_password( $user, $pass1 );

		// Show errors and die if any exist.
		$this->error_helper_handler( $errors );

		wp_die();
	}


	/**
	 * Check is passwords are equal.
	 *
	 * @param    string $pass1 - Password.
	 * @param    string $pass2 - Password Confirmation.
	 *
	 * @since    1.0.0
	 */
	public function verify_passwords( string $pass1, string $pass2 ) {

		$errors = new WP_Error();

		if ( empty( $pass1 ) || empty( $pass2 ) ) {

			$errors->add( 'password_error', __( 'One or both passwords are empty', 'r1_registration' ) );

		} elseif ( $pass1 !== $pass2 ) {

			$errors->add( 'password_error', __( 'Passwords do not match.', 'r1_registration' ) );

		} elseif ( strlen( $pass1 ) < 6 ) {

			$errors->add( 'password_error', __( 'Password is too short', 'r1_registration' ) );

		}

		// If errors isset.
		if ( ! empty( $errors->errors ) ) {

			$this->error_helper_handler( $errors );

		}

		return $errors;
	}


	/**
	 * Check entered email.
	 *
	 * @param    string $email - Email that will be verified.
	 * @param    bool   $free  - Make sure that email is free.
	 *
	 * @since    1.0.0
	 */
	public function verify_user( string $email, $free = false ) {

		$email = trim( $email );

		$errors = new WP_Error();

		if ( empty( $email ) ) {

			$errors->add( 'user_error', __( 'Email is empty', 'r1_registration' ) );

		} elseif ( ! is_email( $email ) ) {

			$errors->add( 'user_error', __( 'Please enter valid email', 'r1_registration' ) );
		}

		if (
			$free &&
			( username_exists( $email ) || email_exists( $email ) )
		) {

			$errors->add( 'user_error', __( 'This email is already used', 'r1_registration' ) );

		} elseif (
			! $free &&
			( ! username_exists( $email ) && ! email_exists( $email ) )
		) {

			$errors->add( 'user_error', __( 'There is no user registered with that email address.', 'r1_registration' ) );

		}

		// If errors isset.
		if ( ! empty( $errors->errors ) ) {

			$this->error_helper_handler( $errors );

		}

		return $errors;

	}

	/**
	 * Checking nonce for security!
	 *
	 * @param    array $errors - Errors array.
	 *
	 * @since    1.0.0
	 */
	public function error_helper_handler( $errors ) : void {

		if ( $errors->get_error_code() ) {

			$errors_arr = array();

			foreach ( $errors->get_error_codes() as $error ) {
				$errors_arr[ $error ] = $errors->get_error_message( $error );
			}

			// Interrupt the execution and show errors.
			echo wp_json_encode(
				array(
					'status' => 0,
					'errors' => $errors_arr,
				)
			);

			wp_die();

		} else {

			// Complete the execution with success status.
			echo wp_json_encode(
				array(
					'status' => 1,
				)
			);

		}
	}

}
