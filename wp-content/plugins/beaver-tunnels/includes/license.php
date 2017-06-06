<?php
/**
 * Beaver Tunnels License Handler
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels License class
 */
class Beaver_Tunnels_License {

	/**
	 * File
	 *
	 * @var string
	 */
	private $file;

	/**
	 * License
	 *
	 * @var string
	 */
	private $license;

	/**
	 * Item Name
	 *
	 * @var string
	 */
	private $item_name;

	/**
	 * Item ID
	 *
	 * @var string
	 */
	private $item_id;

	/**
	 * Item Shortname
	 *
	 * @var string
	 */
	private $item_shortname;

	/**
	 * Version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Author
	 *
	 * @var string
	 */
	private $author = 'Beaver Tunnels';

	/**
	 * API URL
	 *
	 * @var string
	 */
	private $api_url = 'https://beavertunnels.com/';

	/**
	 * Class construct
	 *
	 * @since 1.0
	 *
	 * @param string $_file      Plugin file.
	 * @param string $_item_id   Item ID.
	 * @param string $_item_name Item Name.
	 * @param string $_version   Version.
	 */
	function __construct( $_file, $_item_id, $_item_name, $_version ) {

		$this->file				= $_file;
		$this->item_name		= $_item_name;
		$this->item_id			= $_item_id;
		$this->version			= $_version;
		$this->license			= trim( $this->get_license_key() );

		$this->includes();
		$this->hooks();
		$this->schedule_license_check();

	}

	/**
	 * Include the EDD Sofitware Licensing updater class
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function includes() {
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			require BEAVER_TUNNELS_PLUGIN_DIR . 'lib/EDD_SL_Plugin_Updater.php';
		}
	}

	/**
	 * Setup our hooks
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function hooks() {

		// Activate the license key when settings are saved.
		add_action( 'admin_init', array( $this, 'fl_activate_license' ) );

		// Deactivate the license key.
		add_action( 'admin_init', array( $this, 'fl_deactivate_license' ) );

		// Register the auto updater.
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

	}

	/**
	 * Get the license key
	 *
	 * @since 1.0
	 *
	 * @return mixed License key or false
	 */
	private function get_license_key() {
		if ( Beaver_Tunnels()->is_network_active() ) {
			$beaver_tunnels = get_site_option( 'beaver_tunnels', array() );
		} else {
			$beaver_tunnels = get_option( 'beaver_tunnels', array() );
		}
		if ( isset( $beaver_tunnels['license_key'] ) ) {
			return $beaver_tunnels['license_key'];
		}
		return false;
	}

	/**
	 * Auto updater
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function auto_updater() {

		if ( Beaver_Tunnels()->is_network_active() ) {
			$license_data = get_site_option( 'beaver_tunnels_license_data' );
		} else {
			$license_data = get_option( 'beaver_tunnels_license_data' );
		}

		if ( is_object( $license_data ) && isset( $license_data->license ) && 'valid' !== $license_data->license ) {
			return;
		}

		$edd_updater = new EDD_SL_Plugin_Updater(
			$this->api_url,
			$this->file,
			array(
				'version'	=> $this->version,
				'license'	=> $this->license,
				'item_name'	=> $this->item_name,
				'author'	=> $this->author,
				'url'		=> home_url(),
			)
		);

	}

	/**
	 * Activate the license
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function fl_activate_license() {

		if ( ! isset( $_POST['fl-ft-bt-nonce'] ) || ! wp_verify_nonce( $_POST['fl-ft-bt-nonce'], 'ft-bt' ) ) {
			return;
		}

		if ( ! isset( $_POST['ft-bt-license-key'] ) || '' === $_POST['ft-bt-license-key'] ) {
			return;
		}

		foreach ( $_POST as $key => $value ) {
			if ( false !== strpos( $key, 'beaver_tunnels_license_deactivate' ) ) {
				return;
			}
		}

		$license_data = get_option( 'beaver_tunnels_license_data' );

		if ( is_object( $license_data ) && isset( $license_data->license ) && 'valid' === $license_data->license ) {
			return;
		}

		$license = sanitize_text_field( $_POST['ft-bt-license-key'] );

		if ( empty( $license ) ) {
			return;
		}

		$body = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url(),
		);

		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'	=> 15,
				'sslverify'	=> false,
				'body'      => $body,
			)
		);

		// Check for errors.
		if ( is_wp_error( $response ) ) {
			return;
		}

		// Make WordPress look for updates.
		set_site_transient( 'update_plugins', null );

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( Beaver_Tunnels()->is_network_active() ) {
			update_site_option( 'beaver_tunnels_license_data', $license_data );
		} else {
			update_option( 'beaver_tunnels_license_data', $license_data );
		}

	}

	/**
	 * Deactivate the license
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function fl_deactivate_license() {

		if ( ! isset( $_POST['fl-ft-bt-nonce'] ) || ! wp_verify_nonce( $_POST['fl-ft-bt-nonce'], 'ft-bt' ) ) {
			return;
		}

		if ( ! isset( $_POST['ft-bt-license-key'] ) || '' === $_POST['ft-bt-license-key'] ) {
			return;
		}

		if ( isset( $_POST['beaver_tunnels_license_deactivate'] ) ) {

			$body = array(
				'edd_action' => 'deactivate_license',
				'license'    => $this->license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url(),
			);

			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'	=> 15,
					'sslverify'	=> false,
					'body'      => $body,
				)
			);

			// Check for errors.
			if ( is_wp_error( $response ) ) {
				return;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( isset( $license_data->success ) && true === $license_data->success ) {
				if ( Beaver_Tunnels()->is_network_active() ) {
					update_site_option( 'beaver_tunnels_license_data', $license_data );
					delete_site_option( 'beaver_tunnels_license_status' );
				} else {
					update_option( 'beaver_tunnels_license_data', $license_data );
					delete_option( 'beaver_tunnels_license_status' );
				}
			}
		}

	}

	/**
	 * Schedule the license check
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function schedule_license_check() {

		if ( Beaver_Tunnels()->is_network_active() ) {
			$beaver_tunnels = get_site_option( 'beaver_tunnels', array() );
		} else {
			$beaver_tunnels = get_option( 'beaver_tunnels', array() );
		}

		if ( ! isset( $beaver_tunnels['license_key'] ) ) {
			return;
		}

		if ( false === wp_next_scheduled( 'beaver_tunnels_license_check' ) ) {
			 wp_schedule_event( time(), 'daily', 'beaver_tunnels_license_check' );
		}
		add_action( 'beaver_tunnels_license_check', array( $this, 'check_license' ) );

	}

	/**
	 * Check the license
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function check_license() {

		if ( Beaver_Tunnels()->is_network_active() ) {
			$beaver_tunnels = get_site_option( 'beaver_tunnels', array() );
		} else {
			$beaver_tunnels = get_option( 'beaver_tunnels', array() );
		}

		if ( ! isset( $beaver_tunnels['license_key'] ) ) {
			return;
		}

		$body = array(
			'edd_action' => 'check_license',
			'license'    => $this->license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url(),
		);

		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'	=> 15,
				'sslverify'	=> false,
				'body'      => $body,
			)
		);

		// Check for errors.
		if ( is_wp_error( $response ) ) {
			return;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( Beaver_Tunnels()->is_network_active() ) {
			update_site_option( 'beaver_tunnels_license_data', $license_data );
		} else {
			update_option( 'beaver_tunnels_license_data', $license_data );
		}

	}

}
