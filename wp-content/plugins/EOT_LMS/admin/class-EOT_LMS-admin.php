<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://www.expertonlinetraining.com
 * @since      1.0.0
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/admin
 */
/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/admin
 * @author     Your Name <email@example.com>
 */

class EOT_LMS_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $EOT_LMS    The ID of this plugin.
	 */
	private $EOT_LMS;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The snake cased version of plugin ID for making hook tags.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $EOT_LMS    The ID of this plugin.
	 */
	private $snake_cased_EOT_LMS;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $EOT_LMS       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $EOT_LMS, $version ) {

		$this->EOT_LMS = $EOT_LMS;
		$this->version = $version;
		$this->snake_cased_EOT_LMS = $this->sanitize_snake_cased( $EOT_LMS );

	}

	/**
	 * Sanitize a string key.
	 *
	 * Lowercase alphanumeric characters and underscores are allowed.
	 * Uppercase characters will be converted to lowercase.
	 * Dashes characters will be converted to underscores.
	 *
	 * @access   private
	 * @param  string 	$key 	String key
	 * @return string 	     	Sanitized snake cased key
	 */
	private function sanitize_snake_cased( $key ) {

		return str_replace( '-', '_', sanitize_key( $key ) );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'postbox' );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		add_menu_page(
			__( 'EOT LMS', $this->EOT_LMS ),
			__( 'EOT LMS', $this->EOT_LMS ),
			'manage_options',
			$this->EOT_LMS,
			array( $this, 'display_plugin_admin_page' )
			);

		$tabs = EOT_LMS_Settings_Definition::get_tabs();

		foreach ( $tabs as $tab_slug => $tab_title ) {

			add_submenu_page(
				$this->EOT_LMS,
				$tab_title,
				$tab_title,
				'manage_options',
				$this->EOT_LMS . '&tab=' . $tab_slug,
				array( $this, 'display_plugin_admin_page' )
				);
		}

		remove_submenu_page( $this->EOT_LMS, $this->EOT_LMS );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 * @return   array 			Action links
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=' . $this->EOT_LMS ) . '">' . __( 'Settings', $this->EOT_LMS ) . '</a>'
				),
			$links
			);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		$tabs = EOT_LMS_Settings_Definition::get_tabs();

		$default_tab = EOT_LMS_Settings_Definition::get_default_tab_slug();

		$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET[ 'tab' ] : $default_tab;

		include_once( 'partials/' . $this->EOT_LMS . '-admin-display.php' );

	}
}
