<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://www.expertonlinetraining.com
 * @since      1.0.0
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    EOT_LMS
 * @subpackage EOT_LMS/includes
 * @author     Your Name <email@example.com>
 */
class EOT_LMS {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      EOT_LMS_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $EOT_LMS    The string used to uniquely identify this plugin.
	 */
	protected $EOT_LMS;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->EOT_LMS = 'EOT_LMS';
		$this->version = '1.0.0';

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
	 * - EOT_LMS_Variables. Defines constants for use throughout the plugin.
	 * - EOT_LMS_Loader. Orchestrates the hooks of the plugin.
	 * - EOT_LMS_i18n. Defines internationalization functionality.
	 * - EOT_LMS_Admin. Defines all hooks for the dashboard.
	 * - EOT_LMS_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining the variables.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-EOT_LMS-variables.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-EOT_LMS-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-EOT_LMS-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-EOT_LMS-admin.php';

		/**
		 * The class responsible for defining the page templates.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-EOT_LMS-page-templater.php';

		/**
		 * Gamajo_Template_Loader Class enables get_template_part() to be used in plugins.
		 * https://github.com/GaryJones/Gamajo-Template-Loader
		 * https://pippinsplugins.com/template-file-loaders-plugins/
		 */
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gamajo-template-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-EOT_LMS-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-EOT_LMS-option.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/class-EOT_LMS-callback-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/class-EOT_LMS-meta-box.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/class-EOT_LMS-sanitization-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/class-EOT_LMS-settings-definition.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/class-EOT_LMS-settings.php';

		$this->loader = new EOT_LMS_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the EOT_LMS_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new EOT_LMS_i18n();
		$plugin_i18n->set_domain( $this->get_EOT_LMS() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new EOT_LMS_Admin( $this->get_EOT_LMS(), $this->get_version() );

		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add the options page and menu item.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->EOT_LMS . '.php' );
		$this->loader->add_action( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

		// Built the option page
		$settings_callback = new EOT_LMS_Callback_Helper( $this->EOT_LMS );
		$settings_sanitization = new EOT_LMS_Sanitization_Helper( $this->EOT_LMS );
		$plugin_settings = new EOT_LMS_Settings( $this->get_EOT_LMS(), $settings_callback, $settings_sanitization);
		$this->loader->add_action( 'admin_init' , $plugin_settings, 'register_settings' );

		$plugin_meta_box = new EOT_LMS_Meta_Box( $this->get_EOT_LMS() );
		$this->loader->add_action( 'load-toplevel_page_' . $this->get_EOT_LMS() , $plugin_meta_box, 'add_meta_boxes' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new EOT_LMS_Public( $this->get_EOT_LMS(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_filter( 'init', $plugin_public, 'custom_rewrite_endpoint', 10, 1 );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'custom_query_vars', 10, 1 );	
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
	 * @return    string    The name of the plugin.
	 */
	public function get_EOT_LMS() {
		return $this->EOT_LMS;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    EOT_LMS_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
