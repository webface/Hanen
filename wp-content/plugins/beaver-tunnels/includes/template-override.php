<?php
/**
 * Beaver Tunnels Template Override
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Template Override class
 */
class Beaver_Tunnels_Template_Override {

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'template_include', array( $this, 'template_include' ), 999 );
	}

	/**
	 * Template include
	 *
	 * @since 1.0
	 *
	 * @param  string $template Path to the template.
	 *
	 * @return [type]           [description]
	 */
	public function template_include( $template ) {

		$beaver_tunnels = get_option( 'beaver_tunnels', array() );
		if ( isset( $beaver_tunnels['disable_template_override'] ) && '1' === $beaver_tunnels['disable_template_override'] ) {
			return $template;
		}

		if ( is_singular( 'fl-builder-template' ) ) {
			return BEAVER_TUNNELS_PLUGIN_DIR . 'templates/content-only.php';
		}

		return $template;

	}

}
new Beaver_Tunnels_Template_Override();
