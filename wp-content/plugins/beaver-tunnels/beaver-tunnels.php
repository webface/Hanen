<?php
/**
 * Plugin Name: Beaver Tunnels
 * Plugin URI: http://beavertunnels.com/
 * Description: Allows you to assign Beaver Builder Templates to action hooks from popular themes and plugins.
 * Version: 2.1.5
 * Author: FireTree Design, LLC <info@firetreedesign.com>
 * Author URI: https://firetreedesign.com/
 * Text Domain: beaver-tunnels
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BEAVER_TUNNELS_VERSION', '2.1.5' );

/**
 * Beaver Hooks class
 */
class Beaver_Tunnels {

	/**
	 * Beaver_Tunnels instance
	 *
	 * @var Beaver_Tunnels The one true Beaver_Tunnels
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main Beaver_Tunnels Instance
	 *
	 * Insures that only one instance of Beaver_Tunnels exists in memory at any one time.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses Beaver_Tunnels::includes() Include the required files
	 * @see Beaver_Tunnels()
	 * @return The one true Beaver_Tunnels
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Beaver_Tunnels ) ) {

			self::$instance = new Beaver_Tunnels;

			if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {

				add_action( 'admin_notices', array( 'Beaver_Tunnels', 'below_php_version_notice' ) );
				return self::$instance;

			}

			self::$instance->setup_constants();
			self::$instance->standalone_includes();
			self::$instance->license();

			if ( class_exists( 'FLBuilder' ) ) {
				self::$instance->includes();
			}
		}
		return self::$instance;

	}

	/**
	 * Checks if the plugin is network active.
	 *
	 * @return boolean Result
	 */
	public function is_network_active() {

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			return false;
		}

		if ( is_plugin_active_for_network( plugin_basename( BEAVER_TUNNELS_PLUGIN_FILE ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin File.
		if ( ! defined( 'BEAVER_TUNNELS_PLUGIN_FILE' ) ) {
			define( 'BEAVER_TUNNELS_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Folder Path.
		if ( ! defined( 'BEAVER_TUNNELS_PLUGIN_DIR' ) ) {
			define( 'BEAVER_TUNNELS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'BEAVER_TUNNELS_PLUGIN_URL' ) ) {
			define( 'BEAVER_TUNNELS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/registered-actions.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/admin-columns.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/option-values.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/metabox.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/help.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/output.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/admin-bar-menu.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/visual-guides.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/template-override.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/shortcodes.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/scripts.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/styles.php';
		require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/admin-ajax.php';

	}

	/**
	 * Include standalone files
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	private function standalone_includes() {
		 require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/license.php';
		 require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/settings.php';
		 require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/white-label.php';
		 require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/upgrades.php';
	}

	/**
	 * Initialize the license
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function license() {
		$license = new Beaver_Tunnels_License( __FILE__, 'beaver-tunnels', 'Beaver Tunnels', BEAVER_TUNNELS_VERSION );
	}

	 /**
	  * Notice when an old version of PHP is used
	  *
	  * @since 2.0
	  *
	  * @return void
	  */
	public static function below_php_version_notice() {
		echo '<div class="error"><p>' . esc_html( __( 'Your version of PHP is below the minimum version of PHP required by Beaver Tunnels. Please contact your host and request that your version be upgraded to 5.3 or later.', 'beaver-tunnels' ) ) . '</p></div>';
	}

}

/**
 * Initialize the Beaver_Tunnels class
 *
 * @since 1.0.0
 *
 * @return object Instance of Beaver_Tunnels
 */
function beaver_tunnels() {
	return Beaver_Tunnels::instance();
}
add_action( 'plugins_loaded', 'beaver_tunnels' );
