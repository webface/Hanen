<?php

/**
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/admin/settings
 * @author     Your Name <email@example.com>
 */

class EOT_LMS_Meta_Box {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $EOT_LMS    The ID of this plugin.
	 */
	private $EOT_LMS;

	/**
	 * The snake cased version of plugin ID for making hook tags.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $EOT_LMS    The ID of this plugin.
	 */
	private $snake_cased_EOT_LMS;

	/**
	 * The araay of settings tabs
	 *
	 * @since 	1.0.0
	 * @access  private
	 * @var   	array 		$options_tabs 	The araay of settings tabs
	 */
	private $options_tabs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $EOT_LMS       The name of this plugin.
	 */
	public function __construct( $EOT_LMS ) {

		$this->EOT_LMS = $EOT_LMS;
		$this->snake_cased_EOT_LMS = $this->sanitize_snake_cased( $EOT_LMS );
		$this->options_tabs = EOT_LMS_Settings_Definition::get_tabs();
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
	 * Register the meta boxes on settings page.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_boxes() {

		foreach ( $this->options_tabs as $tab_id => $tab_name ) {

			add_meta_box(
					$tab_id,							// Meta box ID
					$tab_name,							// Meta box Title
					array( $this, 'render_meta_box' ),	// Callback defining the plugin's innards
					$this->snake_cased_EOT_LMS . '_settings_' . $tab_id, // Screen to which to add the meta box
					'normal'							// Context
					);

			} // end foreach
	}

	/**
	 * Print the meta box on settings page.
	 *
	 * @since     1.0.0
	 */
	public function render_meta_box( $active_tab ) {

		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'partials/' . $this->EOT_LMS . '-meta-box-display.php' );
	}
}
