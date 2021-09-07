(function( $ ) {
	'use strict';

	window.addEventListener( "load", function () {
	    r1_registrationForm();
	    r1_loginForm();
	    r1_forgotPasswordForm();
	    r1_resetPasswordForm();
	    r1_togglePassword();
	});


	/**
	 * Registration form processing
	 *
	 * @since    1.0.0
	 */
	function r1_registrationForm () {

		if ( $('#registrationForm').length > 0 ) {

			let errors = $('#registrationForm .form-errors');

			$('#registrationForm').on('submit', function (e) {
				
				e.preventDefault();

				// Clear all errors before new iteration
				errors.empty();

				// Forming data for the POST query
				let data = {
					action: 'register_user',
					nonce_ajax: wp_ajax.nonce,
					query: $(this).serialize(),
				};

				$.ajax({
					url: wp_ajax.ajax_url,
					method: 'POST',
					data: data,
					success: function ( data ) {

						let response = JSON.parse(data);

						// TODO: remove debug information
						console.log(response);

						if ( 1 === response.status ) {

							// TODO: Do success code
							alert( 'You successfully registered!' );
							window.location.reload();

						} else if ( 0 === response.status ) {

							// Show errors
							for ( const [key, value] of Object.entries( response.errors ) ) {
								errors.show().append('<li>' + value + '</li>');

								r1_hightlightField(key);
							}

						}

					},
					error: function (error) {
						console.log( 'error', error );
					}
				});

				return false;
			});

		}
	}


	/**
	 * Login form processing
	 *
	 * @since    1.0.0
	 */
	function r1_loginForm () {

		if ( $('#loginForm').length > 0 ) {

			let errors = $('#loginForm .form-errors');

			$('#loginForm').on('submit', function (e) {

	        	e.preventDefault();

	        	// Clear all errors before new iteration
				errors.empty();

				// Forming data for the POST query
				let data = {
					action: 'login_form',
					nonce_ajax: wp_ajax.nonce,
					query: $(this).serialize(),
				};

				$.ajax({
				    url: wp_ajax.ajax_url,
				    method: 'POST',
				    data: data,
				    success: function ( data ) {

				        let response = JSON.parse(data);

				        // TODO: remove debug information
						console.log(response);

				        if ( 1 === response.status ) {

				        	// TODO: Do success code. Reload page to set user's cookies!
				        	alert( 'You successfully logged in!' );
				            window.location.reload();

				        } else if ( 0 === response.status ) {

				            // console.log(response.errors, );
				            for ( const [key, value] of Object.entries(response.errors) ) {
				            	errors.show().append('<li>' + value + '</li>');

				            	r1_hightlightField(key);
				            }

				        }

				    },
				    error: function (error) {
				        console.log( 'error', error );
				    }
				});

				return false;
			});

		}

	}


	/**
	 * Forgot password form processing
	 *
	 * @since    1.0.0
	 */
	function r1_forgotPasswordForm () {

		if ( $('#forgotPasswordForm').length > 0 ) {

			let errors         = $('#forgotPasswordForm .form-errors');
			let successMessage = $('#forgotPasswordForm .form-success');

			$("#forgotPasswordForm").on('submit', function (e) {

				e.preventDefault();

				// Clear all errors before new iteration
				errors.empty();
				successMessage.hide();

				// Forming data for the POST query
				let data = {
					action: 'lost_pass',
					nonce_ajax: wp_ajax.nonce,
					query: $(this).serialize(),
				};

				$.ajax({
					url: wp_ajax.ajax_url,
					method: 'POST',
					data: data,
					success: function (data) {

						console.log(data);

						let response = JSON.parse(data);

						// TODO: remove debug information
						console.log(response);

						if ( 1 === response.status ) {
							
							// TODO: Do success code
							successMessage.show();
							return;

						} else if ( 0 === response.status ) {

							// Show errors
							for ( const [key, value] of Object.entries( response.errors ) ) {
								errors.show().append('<li>' + value + '</li>');
							}

						}

					},
					error: function (error) {
						console.log( 'error', error );
					}
				});

				return false;
			});

		}
	}


	/**
	 * Reset password form processing
	 *
	 * @since    1.0.0
	 */
	function r1_resetPasswordForm () {

		if ( $('#resetPasswordForm').length > 0 ) {

			let errors = $('#resetPasswordForm .form-errors');

			$("#resetPasswordForm").on('submit', function (e) {

				e.preventDefault();

				// Clear all errors before new iteration
				errors.empty();

				// Forming data for the POST query
				let data = {
					action: 'reset_pass',
					nonce_ajax: wp_ajax.nonce,
					query: $(this).serialize(),
				};

				$.ajax({
					url: wp_ajax.ajax_url,
					method: 'POST',
					data: data,
					success: function (data) {

						let response = JSON.parse(data);

						// TODO: remove debug information
						console.log(response);

						if ( 1 === response.status ) {

							// TODO: Do success code
							alert( 'Your password has been changed!' );
							return;

						}

						if ( 0 === response.status ) {

							// Show errors
							for ( const [key, value] of Object.entries( response.errors ) ) {
								errors.show().append('<li>' + value + '</li>');
							}

						}

					},
					error: function (error) {
						console.log( 'error', error );
					}
				});

				return false;

			});

		}
	}


	/**
	 * Toggle password fields visibility
	 *
	 * @since    1.0.0
	 */
	function r1_togglePassword () {

		$('.toggle-password').on("click", function() {

			let this_is = $(this);

			this_is.toggleClass('active');

			if ( this_is.hasClass('active') ) {
				this_is.closest('.input-wrapper').find('input').attr('type', 'text');
			} else {
				this_is.closest('.input-wrapper').find('input').attr('type', 'password');
			}

		});

	}


	/**
	 * Hightlight field with errors.
	 *
	 * @since    1.0.0
	 */
	function r1_hightlightField ( key ) {

		console.log(key);

		key = key.substring( 0, key.length - 6 );

		let parent = $('#'+key).parent();

		if ( 'user_passwords' === key ) {
			parent = $('#user_password, #user_password_confirm').parent();
		}

		console.log(key);
		console.log(parent);

		parent.addClass('has-error');

	}


})( jQuery );
