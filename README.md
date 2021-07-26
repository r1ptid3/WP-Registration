# WP-Registration

WordPress starter plugin for custom ajax registration

Below will be described all important steps to customize plugin.

# Usage
- Registration form shortcode [r1_registration-form]
- Login form shortcode [r1-login-form]
- Forgot password form shortcode [r1_forgot-pass-form]
- Reset password form shortcode [r1_reset-pass-form]

# Structure
Plugin structure based on "wppb.me" boilerplate and written used OOP

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
