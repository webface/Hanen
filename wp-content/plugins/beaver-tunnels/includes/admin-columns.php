<?php
/**
 * Beaver Tunnels Admin Columns
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Columns class
 *
 * @since 1.0
 */
class Beaver_Tunnels_Admin_Columns {

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'manage_fl-builder-template_posts_columns', array( $this, 'add_columns_head' ) );
		add_action( 'manage_fl-builder-template_posts_custom_column', array( $this, 'add_columns_content' ), 10, 2 );
	}

	/**
	 * Add the column header
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults Current column headers.
	 */
	public function add_columns_head( $defaults ) {

		$defaults['beaver_tunnels'] = Beaver_Tunnels_White_Label::get_branding();
		return $defaults;

	}

	/**
	 * Add the column header content
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name The slug/id for the column.
	 * @param string $post_id     The post ID.
	 */
	public function add_columns_content( $column_name, $post_id ) {

		if ( 'beaver_tunnels' === $column_name ) {
			$action = get_post_meta( $post_id, '_beavertunnels_action', true );
			if ( is_array( $action ) && isset( $action['action'] ) && strlen( $action['action'] ) > 0 ) {
				echo esc_html( __( 'Action:', 'beaver-tunnels' ) ) . ' ' . esc_html( $action['action'] ) . '<br />' . esc_html( __( 'Priority:', 'beaver-tunnels' ) ) . ' ' . esc_html( $action['priority'] );
			} else {
				esc_html_e( 'Not set', 'beaver-tunnels' );
			}
		}

	}

}
new Beaver_Tunnels_Admin_Columns();
