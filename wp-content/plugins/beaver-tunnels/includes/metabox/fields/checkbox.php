<?php
/**
 * Beaver Tunnels Meta Box Checkbox Field
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rendering the Checkbox field
 */
class BT_MetaBox_Field_Checkbox {

	/**
	 * Checkbox field
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $field         The field settings.
	 * @param  string $current_value The current value of the field.
	 *
	 * @return string                The field output
	 */
	static public function render( $field, $current_value ) {

		$input = sprintf(
			'<input %s id="%s" name="%s" type="checkbox" value="1">',
			'1' === $current_value ? 'checked' : '',
			$field['id'],
			$field['id']
		);

		if ( isset( $field['field_desc'] ) ) {
			$input .= '<label for="' . $field['id'] . '">' . $field['field_desc'] . '</label>';
		}

		return '<table class="bt-form-table"><tbody><tr><td>' . $input . '</td></tr></tbody></table>';

	}

}
