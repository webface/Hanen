<?php
/**
 * Beaver Tunnels Upgrade
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Upgrade class
 */
class Beaver_Tunnels_Upgrade {

	/**
	 * Construct
	 *
	 * @since 1.2.0
	 */
	function __construct() {
		add_action( 'wp', array( $this, 'upgrade_check' ) );
	}

	/**
	 * Triggers the upgrade functions
	 *
	 * @since 1.2.0
	 */
	public function upgrade_check() {

		// Get the database vesrion.
		$beaver_tunnels_version = get_option( 'beaver_tunnels_version' );

		// If it's empty, assign it a version number.
		if ( ! $beaver_tunnels_version ) {
			// 1.2.0 is the first version to use this option, so we add the previous version
			$beaver_tunnels_version = '1.1.1';
		}

		// Check if we've already run this.
		if ( version_compare( $beaver_tunnels_version, BEAVER_TUNNELS_VERSION, '=' ) ) {
			return;
		}

		// Database is before 1.2.0.
		if ( version_compare( $beaver_tunnels_version, '1.2.0', '<' ) ) {
			$this->v1_2_0_upgrade();
		}

		// During beta testing, 2.0 was 1.3.
		if ( '1.3.0' === $beaver_tunnels_version ) {
			$beaver_tunnels_version = '2.0.0';
		}

		// Database is before 2.0.0.
		if ( version_compare( $beaver_tunnels_version, '2.0.0', '<' ) ) {
			$this->v2_0_0_upgrade();
		}

		update_option( 'beaver_tunnels_version', BEAVER_TUNNELS_VERSION );

	}

	/**
	 * Upgrade routine for Beaver Tunnels 1.2.0
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	private function v1_2_0_upgrade() {

		// Makes sure the function is defined before trying to use it.
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		if ( ! is_plugin_active_for_network( plugin_basename( BEAVER_TUNNELS_PLUGIN_FILE ) ) ) {
			return;
		}

		$blog_list = get_blog_list( 0, 'all' );
		foreach ( $blog_list as $blog ) {

			switch_to_blog( $blog['blog_id'] );
			$beaver_tunnels = get_option( 'beaver_tunnels', array() );
			if ( isset( $beaver_tunnels['license_key'] ) ) {
				$beaver_tunnels_license_data = get_option( 'beaver_tunnels_license_data' );
				update_site_option( 'beaver_tunnels', $beaver_tunnels );
				update_site_option( 'beaver_tunnels_license_data', $beaver_tunnels_license_data );
				delete_option( 'beaver_tunnels_license_data' );
				restore_current_blog();
				break;
			}
			restore_current_blog();

		}

	}

	/**
	 * Upgrade routine for Beaver Tunnels 2.0
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	private function v2_0_0_upgrade() {

		require_once( BEAVER_TUNNELS_PLUGIN_DIR . 'includes/upgrades/v2-0-0-upgrade.php' );

	}

}
new Beaver_Tunnels_Upgrade();
