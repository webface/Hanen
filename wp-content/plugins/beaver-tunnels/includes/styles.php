<?php
/**
 * Beaver Tunnels Styles
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Styles class
 */
final class BTStyles {

	/**
	 * Init
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	static public function init() {
		add_action( 'admin_print_scripts-post-new.php', 'BTStyles::enqueue', 11 );
		add_action( 'admin_print_scripts-post.php', 'BTStyles::enqueue', 11 );
	}

	/**
	 * Enqueue the styles
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	static public function enqueue() {

		global $post_type;
		if ( 'fl-builder-template' === $post_type ) {
			wp_enqueue_style( 'bt-template', BEAVER_TUNNELS_PLUGIN_URL . '/assets/css/admin.css' );
		}

	}

}
BTStyles::init();
