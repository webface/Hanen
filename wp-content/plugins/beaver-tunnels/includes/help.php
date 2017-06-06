<?php
/**
 * Beaver Tunnels Help
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Help class
 *
 * @since 2.0
 */
class BT_Help {

	/**
	 * Class constructor
	 *
	 * @since 2.0
	 */
	public function __construct() {
		add_action( 'load-post.php', array( $this, 'template_help' ) );
		add_action( 'load-post-new.php', array( $this, 'template_help' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'pointer_load' ) );
		add_filter( 'bt_admin_pointers_plugins', array( $this, 'pointer_register' ) );
	}

	/**
	 * Add the help tab to the screen
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function template_help() {

		$screen = get_current_screen();

		if ( 'fl-builder-template' !== $screen->id ) {
			return;
		}

		$screen->add_help_tab( array(
			'id'		=> 'ft-bt',
			'title'		=> Beaver_Tunnels_White_Label::get_branding(),
			'content'	=> sprintf( '<p><strong>%s</strong> — %s</p><p><strong>%s</strong> — %s</p><p><strong>%s</strong> — %s</p>',
			 	__( 'Action', 'beaver-tunnels' ),
				__( 'This is the action hook in the theme or plugin where you would like to attach the template. The Priority field is used to determine the order in which the actions are attached. The lower the number, the earlier the action happens.', 'beaver-tunnels' ),
				__( 'Global', 'beaver-tunnels' ),
				__( 'This will display the template on every page of your site as long as the Display Conditions are also valid.', 'beaver-tunnels' ),
				__( 'Display Conditions', 'beaver-tunnels' ),
				__( 'Here, you can build complex conditions that have to be valid in order for your template to be displayed. If the only condition is &quot;is not equal to&quot;, then the template will not be displayed. The exception to this is if the option to display globally has been checked.', 'beaver-tunnels' )
			),
		) );

	}

	/**
	 * Load the pointers
	 *
	 * @since 2.0.2
	 *
	 * @param  string $hook_suffix Hook suffix.
	 *
	 * @return [type]              [description]
	 */
	public function pointer_load( $hook_suffix ) {

		// Get the screen ID.
		$screen = get_current_screen();
		$screen_id = $screen->id;

		// Get pointers for this screen.
		$pointers = apply_filters( 'bt_admin_pointers_' . $screen_id, array() );

		// No pointers? Then we stop.
		if ( ! $pointers || ! is_array( $pointers ) ) {
			return;
		}

		// Get dismissed pointers.
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$valid_pointers = array();

		// Check pointers and remove dismissed ones.
		foreach ( $pointers as $pointer_id => $pointer ) {

	    	// Sanity check.
			if ( in_array( $pointer_id, $dismissed, true ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) ) {
				continue;
			}

			$pointer['pointer_id'] = $pointer_id;

			// Add the pointer to $valid_pointers array.
			$valid_pointers['pointers'][] = $pointer;
		}

		// No valid pointers? Stop here.
		if ( empty( $valid_pointers ) ) {
			return;
		}

		// Add pointers style to queue.
		wp_enqueue_style( 'wp-pointer' );

		wp_enqueue_script( 'bt-pointers', BEAVER_TUNNELS_PLUGIN_URL . '/assets/js/pointers.js', array( 'wp-pointer' ) );

		// Add pointer options to script.
		wp_localize_script( 'bt-pointers', 'bt_pointer', $valid_pointers );

	}

	/**
	 * Register the pointer
	 *
	 * @since 2.0.2
	 *
	 * @param  array $pointer Pointers.
	 *
	 * @return array
	 */
	public function pointer_register( $pointer ) {

		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$pointer['bt_fresh_install'] = array(
			'target' => '#menu-posts-fl-builder-template',
			'options' => array(
				'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
					Beaver_Tunnels_White_Label::get_branding(),
					__( 'Display conditions are available when editing a saved template, row, or module from the Builder menu.', 'beaver-tunnels' )
				),
				'position' => array( 'edge' => 'top', 'align' => 'top' ),
			),
		);
		return $pointer;
	}

}
new BT_Help;
