# WP-Registration

WordPress starter plugin for custom ajax registration

Below will be described all important steps to customize plugin.

# Usage

### Shortcodes:
- Registration form shortcode [r1_registration-form]
- Login form shortcode [r1_login-form]
- Forgot password form shortcode [r1_forgot-pass-form]
- Reset password form shortcode [r1_reset-pass-form]

### Customize registration form:
- To customize registration form change <code>registration_form_fields</code> variable in <code>./includes/class-r1_registration.php</code> file

### Tips:
- Do not change <code>Required field keys</code> below.
- If <code>error_msg</code> not setted up for required fields, error message will be next <code>[Label] is required</code>.

# Customize Registration Form

### Required fields:
- 'user_email'
- 'user_password'
- 'user_password_confirm'
  
### Available field types:
- 'text'
- 'select'
- 'textarea'
- 'radio'
- 'checkbox'

### Custom registration form example
```php
'user_full_name' => array(
	'type'        => 'text',
	'label'       => esc_html__( 'Full Name', 'r1_registration' ),
	'placeholder' => esc_html__( 'John Doe', 'r1_registration' ),
	'required'    => true,
	'error_msg'   => esc_html__( 'Full Name is required', 'r1_registration' ),
),
// Required field!
'user_email' => array(
	'type'        => 'email',
	'label'       => esc_html__( 'Email', 'r1_registration' ),
	'placeholder' => esc_html__( 'Email', 'r1_registration' ),
	'required'    => true,
),
// Required field!
'user_password' => array(
	'type'        => 'password',
	'label'       => esc_html__( 'Password', 'r1_registration' ),
	'placeholder' => esc_html__( 'Password', 'r1_registration' ),
	'required'    => true,
),
// Required field!
'user_password_confirm' => array(
	'type'            => 'password',
	'label'           => esc_html__( 'Confirm Password', 'r1_registration' ),
	'placeholder'     => esc_html__( 'Confirm Password', 'r1_registration' ),
	'required'        => true,
	'is_confirmation' => true,
),
'user_city' => array(
	'type'      => 'select',
	'label'     => esc_html__( 'City', 'r1_registration' ),
	'multiple'  => false,
	'required'  => true,
	'error_msg' => esc_html__( 'Please, select your city', 'r1_registration' ),
	'options'   => array(
		''          => '',
		'texas'     => 'Texas',
		'florida'   => 'Florida',
		'baltimore' => 'Baltimore',
		'chicago'   => 'Chicago',
	),
),
'user_hobbies' => array(
	'type'     => 'select',
	'label'    => esc_html__( 'Hobbies', 'r1_registration' ),
	'multiple' => true,
	'required' => false,
	'options'  => array(
		''           => '',
		'tennis'     => 'Tennis',
		'baseball'   => 'Baseball',
		'basketball' => 'Basketball',
		'football'   => 'Football',
	),
),
'user_short_bio' => array(
	'type'        => 'textarea',
	'label'       => esc_html__( 'Short Bio', 'r1_registration' ),
	'placeholder' => esc_html__( 'Some information about you', 'r1_registration' ),
	'required'    => false,
),
'user_gender' => array(
	'type'      => 'radio',
	'label'     => esc_html__( 'Gender', 'r1_registration' ),
	'required'  => true,
	'error_msg' => esc_html__( 'Please select your gender', 'r1_registration' ),
	'options'   => array(
		'male'   => 'Male',
		'female' => 'Female',
	),
),
'user_privacy' => array(
	'type'      => 'checkbox',
	'label'     => esc_html__( 'Privacy Policy', 'r1_registration' ),
	'required'  => true,
	'error_msg' => esc_html__( 'Accepting Privacy Policy is required', 'r1_registration' ),
),
'user_messaging' => array(
	'type'      => 'checkbox',
	'label'     => esc_html__( 'Receive advertising emails', 'r1_registration' ),
	'required'  => false,
),
```

# Structure
Plugin structure based on "wppb.me" boilerplate and written used OOP.

# Admin
Admin part extended with code that shows and saves user custom meta fields on "Dashboard -> Users" page.

# Public
Public part has 3 classes with different responsibility and javascript file which managing all ajax requests.

# Errors
All form errors are handled via PHP in public/class-r1_registration-callbacks.php

# Common tasks
- All form templates are located in public/class-r1_registration-templates.php
- To create custom meta fields add them as input field into templates file and then if you want to show them on admin side add processing code into admin/class-r1_registration-admin.php
- To add new verification conditions go to public/class-r1_registration-callbacks.php and change necessary form.
- You can change events on form request success or error into public/js/r1_registration-public.js
