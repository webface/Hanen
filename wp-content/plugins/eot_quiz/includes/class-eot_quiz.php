<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.expertonlinetraining.com
 * @since      1.0.0
 *
 * @package    Eot_quiz
 * @subpackage Eot_quiz/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Eot_quiz
 * @subpackage Eot_quiz/includes
 * @author     Tommy Adeniyi <tommy@targetdirectories.com>
 */
class Eot_quiz {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Eot_quiz_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

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
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'eot_quiz';
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
     * - Eot_quiz_Loader. Orchestrates the hooks of the plugin.
     * - Eot_quiz_i18n. Defines internationalization functionality.
     * - Eot_quiz_Admin. Defines all hooks for the admin area.
     * - Eot_quiz_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-eot_quiz-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-eot_quiz-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-eot_quiz-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-eot_quiz-public.php';

        $this->loader = new Eot_quiz_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Eot_quiz_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Eot_quiz_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Eot_quiz_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Eot_quiz_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('wp_ajax_update_question', $plugin_public, 'update_question');
        $this->loader->add_action('wp_ajax_nopriv_update_question', $plugin_public, 'update_question');
        $this->loader->add_action('wp_ajax_quiz_data', $plugin_public, 'quiz_data');
        $this->loader->add_action('wp_ajax_nopriv_quiz_data', $plugin_public, 'quiz_data');
        $this->loader->add_action('wp_ajax_get_quiz_form', $plugin_public, 'get_quiz_form');
        $this->loader->add_action('wp_ajax_nopriv_get_quiz_form', $plugin_public, 'get_quiz_form');
        $this->loader->add_action('wp_ajax_delete_quiz', $plugin_public, 'delete_quiz_callback');
        $this->loader->add_action('wp_ajax_nopriv_delete_quiz', $plugin_public, 'delete_quiz_callback');
        $this->loader->add_action('wp_ajax_delete_question', $plugin_public, 'delete_question_callback');
        $this->loader->add_action('wp_ajax_nopriv_delete_question', $plugin_public, 'delete_question_callback');
        $this->loader->add_action('wp_ajax_add_title', $plugin_public, 'add_title_callback');
        $this->loader->add_action('wp_ajax_nopriv_add_title', $plugin_public, 'add_title_callback');
        $this->loader->add_action('wp_ajax_update_title', $plugin_public, 'update_title_callback');
        $this->loader->add_action('wp_ajax_nopriv_update_title', $plugin_public, 'update_title_callback');        
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
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Eot_quiz_Loader    Orchestrates the hooks of the plugin.
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
