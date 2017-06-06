<?php
/**
 * Beaver Tunnels Shortcodes
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Shortcodes class
 */
class Beaver_Tunnels_Shortcodes {

	/**
	 * The Post
	 *
	 * @var object
	 */
	public $post;

	/**
	 * The WP_Query
	 *
	 * @var object
	 */
	public $wp_query;

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_shortcode( 'beaver_tunnels', array( $this, 'beaver_tunnels_shortcode' ) );

		if ( is_admin() ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		add_action( 'wp', array( $this, 'set_post' ) );

	}

	/**
	 * Set the post
	 *
	 * @since 1.0
	 */
	public function set_post() {

		global $post;
		global $wp_query;

		$this->wp_query	= $wp_query;
		$this->post		= get_queried_object();

	}

	/**
	 * Process the [beaver_tunnels] shortcode
	 *
	 * @since 1.0
	 *
	 * @param  array  $atts Shortcode attributes.
	 * @param  string $content	Shortcode content.
	 *
	 * @return string
	 */
	public function beaver_tunnels_shortcode( $atts = array(), $content = '' ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( 0 === strlen( trim( $content ) ) ) {
			return;
		}

		global $wp_query;
		global $post;

		$wp_query_backup	= $wp_query;
		$post_backup		= $post;

		$post		= $this->post;
		$wp_query	= $this->wp_query;

		$content = do_shortcode( $content );

		$wp_query	= $wp_query_backup;
		$post		= $post_backup;

		return $content;

	}

}
new Beaver_Tunnels_Shortcodes();
