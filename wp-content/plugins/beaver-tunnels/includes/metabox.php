<?php
/**
 * Beaver Tunnels Metabox
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once BEAVER_TUNNELS_PLUGIN_DIR . 'includes/metabox/metabox-builder.php';

/**
 * Beaver Tunnels Metabox class
 */
class BT_Meta_Meta_Box {

	/**
	 * Prefix
	 *
	 * @var string
	 */
	private $prefix = '_beavertunnels_';

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'setup' ), 20 );
	}

	/**
	 * Setup the meta box
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		new BT_Meta_Box_Builder( array(
			'id'       => 'beaver_tunnels',
			'title'    => Beaver_Tunnels_White_Label::get_branding(),
			'context'  => 'normal',
			'priority' => 'high',
			'screens'  => array(
				'fl-builder-template',
			),
			'fields' => array(
				array(
					'id'                       => $this->prefix . 'action',
					'label'                    => '<span class="dashicons dashicons-editor-help"></span>' . __( 'Action', 'beaver-tunnels' ),
					'desc'                     => __( 'Where would you like to attach this template?', 'beaver-tunnels' ),
					'type'                     => 'action_hook',
					'chosenjs'                 => true,
					'chosenjs_single_deselect' => true,
					'default'                  => array(
						'action'	=> '',
						'priority'	=> '10',
					),
					'options'                  => $this->get_action_options(),
				),
				array(
					'id'         => $this->prefix . 'all_pages',
					'label'      => '<span class="dashicons dashicons-editor-help"></span>' . __( 'Global', 'beaver-tunnels' ),
					'desc'       => __( 'As long as the Display Conditions are also valid.', 'beaver-tunnels' ),
					'type'       => 'checkbox',
					'field_desc' => __( 'Display globally on all pages', 'beaver-tunnels' ),
				),
				array(
					'id'       => $this->prefix . 'conditions',
					'label'    => '<span class="dashicons dashicons-editor-help"></span>' . __( 'Display Conditions', 'beaver-tunnels' ),
					'desc'     => __( 'Please choose the conditions for displaying this template.' ),
					'type'     => 'condition',
					'default'  => 'any',
					'chosenjs' => true,
				),
			),
		) );

	}

	/**
	 * Retrive the array of action hooks that are available
	 *
	 * @since 1.0.0
	 *
	 * @return array Available action hooks
	 */
	private function get_action_options() {

		$actions = apply_filters( 'beaver_tunnels', array() );

		$actions_array = array();
		if ( ! empty( $actions ) ) {

			foreach ( $actions as $platform ) {

				$platform_actions = array();
				if ( ! empty( $platform['actions'] ) && is_array( $platform['actions'] ) ) {

					foreach ( $platform['actions'] as $action ) {
						$platform_actions[ $action ] = $action;
					}
				}

				$actions_array[] = array(
					'title' => $platform['title'],
					'options' => $platform_actions,
				);

			}
		}

		return $actions_array;

	}

}
new BT_Meta_Meta_Box;
