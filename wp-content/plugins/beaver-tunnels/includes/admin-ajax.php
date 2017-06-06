<?php
/**
 * Admin Ajax functions
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Ajax class
 *
 * @package Beaver_Tunnels
 * @since 2.0
 */
final class BTAdminAjax {

	/**
	 * Initialize the actions
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	static public function init() {
		add_action( 'wp_ajax_bt_get_and_condition_row', 'BTAdminAjax::get_and_condition_row' );
		add_action( 'wp_ajax_bt_row_condition_changed', 'BTAdminAjax::row_condition_changed' );
	}

	/**
	 * Triggered when a condition is changed in the Display Conditions field.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	static public function row_condition_changed() {

		if ( ! isset( $_POST['bt_nonce'] ) || ! wp_verify_nonce( $_POST['bt_nonce'], 'bt-nonce' ) ) {
			die( 'Insufficient Permissions' );
		}

		if ( ! isset( $_POST['bt_condition'] ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['bt_group'] ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['bt_field'] ) ) {
			wp_die();
		}

		require_once plugin_dir_path( __FILE__ ) . 'metabox/fields/display-condition.php';

		$rule_id = uniqid();

		switch ( $_POST['bt_condition'] ) {

			case 'before_date':
			case 'after_date':
				$rule = array(
					'condition'	=> $_POST['bt_condition'],
					'operator'	=> '==',
					'value'		=> '',
				);
				BT_MetaBox_Field_Display_Condition::row_date( $_POST['bt_field'], $rule, $_POST['bt_group'], $rule_id );
				break;

			case 'before_time':
			case 'after_time':
				$rule = array(
					'condition'	=> $_POST['bt_condition'],
					'operator'	=> '==',
					'value'		=> '',
				);
				BT_MetaBox_Field_Display_Condition::row_time( $_POST['bt_field'], $rule, $_POST['bt_group'], $rule_id );
				break;

			case 'none':
				$rule = array(
					'condition'	=> 'none',
					'operator'	=> '==',
					'value'		=> '',
				);
				BT_MetaBox_Field_Display_Condition::row_none( $_POST['bt_field'], $rule, $_POST['bt_group'], $rule_id );
				break;

			default:
				$rule = array(
					'condition'	=> $_POST['bt_condition'],
					'operator'	=> '==',
					'value'		=> '',
				);
				BT_MetaBox_Field_Display_Condition::row_standard( $_POST['bt_field'], $rule, $_POST['bt_group'], $rule_id );
				break;

		}

		wp_die();

	}

	/**
	 * Triggered when a new Display Conditions row is added
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	static public function get_and_condition_row() {

		if ( ! isset( $_POST['bt_nonce'] ) || ! wp_verify_nonce( $_POST['bt_nonce'], 'bt-nonce' ) ) {
			die( 'Insufficient Permissions' );
		}

		if ( ! isset( $_POST['bt_group'] ) ) {
			wp_die();
		}

		if ( ! isset( $_POST['bt_field'] ) ) {
			wp_die();
		}

		require_once plugin_dir_path( __FILE__ ) . 'metabox/fields/display-condition.php';

		$rule_id = uniqid();

		BT_MetaBox_Field_Display_Condition::row_none( $_POST['bt_field'], $_POST['bt_group'], $rule_id );
		wp_die();

	}

}
BTAdminAjax::init();
