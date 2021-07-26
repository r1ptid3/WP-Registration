<?php
/**
 * The file that defines the core plugin class
 *
 * @since      1.0.0
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/includes
 */

// Enable strict typing mode.
declare( strict_types = 1 );

/**
 * The core plugin class.
 *
 * @since      1.0.0
 *
 * @package    R1_Registration
 * @subpackage R1_Registration/includes
 */
class R1_Registration {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   protected
	 * @var      object $loader - Maintains and registers all hooks for the plugin.
	 */
	protected object $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   protected
	 * @var      string $plugin_name - The string used to uniquely identify this plugin.
	 */
	protected string $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   protected
	 * @var      string $version - The current version of the plugin.
	 */
	protected string $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'R1_REGISTRATION_VERSION' ) ) {
			$this->version = R1_REGISTRATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'r1_registration';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - R1_Registration_Loader. Orchestrates the hooks of the plugin.
	 * - R1_Registration_i18n. Defines internationalization functionality.
	 * - R1_Registration_Admin. Defines all hooks for the admin area.
	 * - R1_Registration_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-r1_registration-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-r1_registration-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-r1_registration-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-r1_registration-public.php';

		/**
		 * The class responsible for output html templates for forms.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-r1_registration-templates.php';

		/**
		 * The class responsible for callback ajax functions.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-r1_registration-callbacks.php';

		$this->loader = new R1_Registration_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the R1_Registration_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new R1_Registration_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new R1_Registration_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Hooks near the bottom of profile page (if current user).
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'r1_custom_user_profile_fields' );

		// Hooks near the bottom of the profile page (if not current user).
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'r1_custom_user_profile_fields' );

		// Hook is used to save custom fields that have been added to the WordPress profile page (if current user).
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'r1_update_extra_profile_fields' );

		// Hook is used to save custom fields that have been added to the WordPress profile page (if not current user).
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'r1_update_extra_profile_fields' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public    = new R1_Registration_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_callbacks = new R1_Registration_Callbacks();
		$plugin_templates = new R1_Registration_Templates();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Callback hooks.
		$this->loader->add_action( 'wp_ajax_register_user', $plugin_callbacks, 'register_user_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_register_user', $plugin_callbacks, 'register_user_callback' );

		$this->loader->add_action( 'wp_ajax_login_form', $plugin_callbacks, 'user_login_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_login_form', $plugin_callbacks, 'user_login_callback' );

		$this->loader->add_action( 'wp_ajax_lost_pass', $plugin_callbacks, 'lost_pass_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_lost_pass', $plugin_callbacks, 'lost_pass_callback' );

		$this->loader->add_action( 'wp_ajax_nopriv_reset_pass', $plugin_callbacks, 'reset_pass_callback' );
		$this->loader->add_action( 'wp_ajax_reset_pass', $plugin_callbacks, 'reset_pass_callback' );

		// Shortcode hooks.
		$this->loader->add_shortcode( 'r1_registration-form', $plugin_templates, 'registration_form_output' );
		$this->loader->add_shortcode( 'r1_login-form', $plugin_templates, 'login_form_output' );
		$this->loader->add_shortcode( 'r1_forgot-pass-form', $plugin_templates, 'forgot_pass_form_output' );
		$this->loader->add_shortcode( 'r1_reset-pass-form', $plugin_templates, 'reset_pass_form_output' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 *
	 * @return    string - The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return    R1_Registration_Loader - Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return    string - The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
