<?php
/**
 * Beaver Tunnels Scripts
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Scripts class
 */
final class BTScripts {

	/**
	 * Init
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	static public function init() {
		add_action( 'admin_print_scripts-post-new.php', 'BTScripts::enqueue', 11 );
		add_action( 'admin_print_scripts-post.php', 'BTScripts::enqueue', 11 );
		add_action( 'wp_enqueue_scripts', 'BTScripts::enqueue_scripts', 99 );
	}

	/**
	 * Enqueue the scripts
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	static public function enqueue() {

		global $post_type;
		if ( 'fl-builder-template' === $post_type ) {
			wp_enqueue_script( 'bt-template', BEAVER_TUNNELS_PLUGIN_URL . '/assets/js/admin.js' );
			BTScripts::localize();
		}

	}

	/**
	 * Enqueue the scripts
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	static public function enqueue_scripts() {

		if ( ! is_singular( 'fl-builder-template' ) ) {
			return;
		}

		if ( ! FLBuilderModel::is_builder_active() ) {
			return;
		}

		if ( ! isset( $_GET['bt_return'] ) ) {
			return;
		}

		wp_enqueue_script( 'bt-template-builder', BEAVER_TUNNELS_PLUGIN_URL . '/assets/js/template-builder.js', array( 'jquery' ) );
		BTScripts::localize_bt_template_builder();

	}

	/**
	 * Localize the scripts
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	static public function localize() {

		wp_localize_script( 'bt-template', 'bt_vars', array(
			'bt_nonce' => wp_create_nonce( 'bt-nonce' ),
		) );

	}

	/**
	 * Localize the scripts
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	static public function localize_bt_template_builder() {

		if ( ! isset( $_GET['bt_return'] ) ) {
			return;
		}

		wp_localize_script( 'bt-template-builder', 'bt', array(
			'exitUrl' => add_query_arg( 'fl_builder', '', $_GET['bt_return'] ),
		) );

	}

}
BTScripts::init();
