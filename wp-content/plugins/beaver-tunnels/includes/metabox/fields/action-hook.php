<?php
/**
 * Beaver Tunnels Meta Box Action Hook Field
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rendering the Action Hook field
 */
class BT_MetaBox_Field_Action_Hook {

	/**
	 * Action Hook field
	 *
	 * @param  array  $field         The field settings.
	 * @param  string $current_value The current value of the field.
	 *
	 * @return string                The field output
	 */
	static public function render( $field, $current_value ) {

		if ( ! is_array( $current_value ) ) {
			$current_value = array(
				'action'	=> '',
				'priority'	=> '10',
			);
		}

		$input = '<table class="bt-form-table"><tbody><tr><th>' . __( 'Action', 'beaver-tunnels' ) . '</th><th>' . __( 'Priority', 'beaver-tunnels' ) . '</th></tr><tr><td class="bt-action">';

		$input .= sprintf(
			'<select id="%s" name="%s">',
			$field['id'] . '_action',
			$field['id'] . '[action]'
		);

		$input .= '<option value=""></option>';

		foreach ( $field['options'] as $optgroup ) {

			$input .= sprintf(
				'<optgroup label="%s">',
				$optgroup['title']
			);

			foreach ( $optgroup['options'] as $key => $label ) {

				$input .= sprintf(
					'<option%s value="%s">%s</option>',
					selected( $current_value['action'], $key, false ),
					$key,
					$label
				);

			}

			$input .= '</optgroup>';

		}

		$input .= '</select>';

		$input .= '</td><td class="bt-priority">';

		$input .= sprintf(
			'<input id="%s" name="%s" value="%s" type="number" min="1" max="999999">',
			$field['id'] . '_priority',
			$field['id'] . '[priority]',
			$current_value['priority']
		);

		$input .= '</td></tr></tbody></table>';

		if ( isset( $field['chosenjs'] ) && true === $field['chosenjs'] ) {

			BT_MetaBox_Field_Action_Hook::enqueue_chosen();

			$allow_single_deselect = '';
			if ( isset( $field['chosenjs_single_deselect'] ) && true === $field['chosenjs_single_deselect'] ) {
				$allow_single_deselect = ', allow_single_deselect: true';
			}

			ob_start();
			?>
			<script>
			jQuery( document ).ready(function($) {
				jQuery('#<?php echo esc_html( $field['id'] ); ?>_action').chosen({width: "100%"<?php echo esc_html( $allow_single_deselect ); ?>});
			});
			</script>
			<style>
				#<?php echo esc_html( $field['id'] ); ?>_chosen { width: 100% !important; }
			</style>
			<?php
			$input .= ob_get_clean();

		}

		return $input;

	}

	/**
	 * Chosen jQuery Plugin
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	static private function enqueue_chosen() {
		wp_enqueue_script( 'chosen', BEAVER_TUNNELS_PLUGIN_URL . 'lib/chosen/chosen.jquery.min.js', array( 'jquery' ),'1.5.1' );
		wp_enqueue_style( 'chosen', BEAVER_TUNNELS_PLUGIN_URL . 'lib/chosen/chosen.min.css' );
	}

}
