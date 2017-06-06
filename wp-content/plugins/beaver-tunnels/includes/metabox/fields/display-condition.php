<?php
/**
 * Beaver Tunnels Meta Box Display Condition Field
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rendering the Display Conditions field
 */
class BT_MetaBox_Field_Display_Condition {

	/**
	 * Condition field
	 *
	 * @param  array  $field         The field settings.
	 * @param  string $current_value The current value of the field.
	 *
	 * @return string                The field output
	 */
	static public function render( $field, $current_value ) {

		BT_MetaBox_Field_Display_Condition::enqueue_datetimepicker();
		BT_MetaBox_Field_Display_Condition::enqueue_timepicker();

		ob_start();
		if ( ! is_array( $current_value ) ) {
			$current_value = BT_MetaBox_Field_Display_Condition::default_value();
		}

		$group_id = 0;
		$group_count = count( $current_value ) - 1;
		?>
		<table data-bt-field-id="<?php echo esc_attr( $field['id'] ); ?>" class="bt-condition-table widefat">
			<tbody>
			<tr><th colspan="4"><?php esc_html_e( 'Display this template if', 'beaver-tunnels' ); ?></th></tr>
			<?php foreach ( $current_value as $group ) : ?>
				<?php $rule_id = 0; ?>
				<?php foreach ( $group as $rule ) : ?>
					<?php
					if ( ! isset( $rule['condition'] ) ) {
						$rule['condition'] = '';
					}
					if ( ! isset( $rule['operator'] ) ) {
						$rule['operator'] = '';
					}
					if ( ! isset( $rule['value'] ) ) {
						$rule['value'] = '';
					}

					if ( 'none' === $rule['condition'] ) {
						continue;
					}

					switch ( $rule['condition'] ) :

						case 'before_date':
						case 'after_date':
							BT_MetaBox_Field_Display_Condition::row_date( $field['id'], $rule, $group_id, $rule_id );
							break;

						case 'before_time':
						case 'after_time':
							BT_MetaBox_Field_Display_Condition::row_time( $field['id'], $rule, $group_id, $rule_id );
							break;

						default:
							BT_MetaBox_Field_Display_Condition::row_standard( $field['id'], $rule, $group_id, $rule_id );
							break;

					endswitch;
					$rule_id++;
				endforeach; ?>
				<?php if ( $rule_id > 0 ) : ?>
				<tr data-bt-group="or"><th colspan="4"><?php esc_html_e( 'or', 'beaver-tunnels' ); ?></th></tr>
				<?php endif; ?>
				<?php if ( $rule_id > 0 && $group_id === $group_count ) : ?>
					<tr data-bt-group="<?php echo esc_attr( $group_id ); ?>"><td colspan="4"><button class="button button-secondary bt-condition-button-or"><?php esc_html_e( 'Add new rule', 'beaver-tunnels' ); ?></button></td></tr>
				<?php endif; ?>
				<?php $group_id++; ?>
			<?php endforeach; ?>
			<?php
			if ( 0 === $rule_id ) {
				BT_MetaBox_Field_Display_Condition::row_none( $field['id'], 0, 0 );
				?>
				<tr data-bt-group="or"><th colspan="4"><?php esc_html_e( 'or', 'beaver-tunnels' ); ?></th></tr>
				<tr data-bt-group="<?php echo esc_attr( $group_id ); ?>"><td colspan="4"><button class="button button-secondary bt-condition-button-or"><?php esc_html_e( 'Add new rule', 'beaver-tunnels' ); ?></button></td></tr>
				<?php
			}
			?>
		</tbody>
		</table>
		<?php

		if ( false === true && isset( $field['chosenjs'] ) && true === $field['chosenjs'] ) {

			BT_MetaBox_Field_Display_Condition::enqueue_chosen();
			?>
			<script>
			jQuery( document ).ready(function($) {
				jQuery('table.bt-form-table .bt-condition select').chosen({width: "100%", disable_search: true});
				jQuery('table.bt-form-table .bt-operator select').chosen({width: "100%", disable_search: true});
				jQuery('table.bt-form-table .bt-value select').chosen({width: "100%"});
			});
			</script>
			<?php

		}

		return ob_get_clean();

	}

	/**
	 * Default values
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	static public function default_value() {
		return array(
			'group_0' => array(
				'rule_0' => array(
					'condition'	=> 'none',
					'operator'	=> '==',
					'value'		=> '',
				),
			),
		);
	}

	/**
	 * Render row - None
	 *
	 * @since 2.0
	 *
	 * @param  string $field_id Field ID.
	 * @param  string $group_id Group ID.
	 * @param  string $rule_id  Rule ID.
	 *
	 * @return void
	 */
	static public function row_none( $field_id, $group_id, $rule_id ) {
		?>
		<tr data-bt-group="<?php echo esc_attr( $group_id ); ?>" data-bt-rule="<?php echo esc_attr( $rule_id ); ?>">
			<td class="bt-condition">
				<select id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-condition" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][condition]">
					<?php echo BTOptionValues::get_condition_options( 'none' ); ?>
				</select>
			</td>
			<td class="bt-operator">
				<select disabled="disabled" id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-operator" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][operator]">
					<?php echo BTOptionValues::get_operator_options( '==' ); ?>
				</select>
			</td>
			<td class="bt-value">
				<select disabled="disabled" id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-value" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][value]">
					<?php echo BTOptionValues::get_options( 'none', '' ); ?>
				</select>
			</td>
			<td class="bt-and">
				<button class="button button-secondary bt-condition-button-and"><?php esc_html_e( 'and', 'beaver-tunnels' ); ?></button>
			</td>
			<td class="bt-remove">
				<button class="button button-secondary bt-condition-button-remove"><span class="dashicons dashicons-no-alt"></span></button>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render row - Standard
	 *
	 * @since 2.0
	 *
	 * @param  string $field_id Field ID.
	 * @param  array  $rule     Rule.
	 * @param  string $group_id Group ID.
	 * @param  string $rule_id  Rule ID.
	 *
	 * @return void
	 */
	static public function row_standard( $field_id, $rule, $group_id, $rule_id ) {
		?>
		<tr data-bt-group="<?php echo esc_attr( $group_id ); ?>" data-bt-rule="<?php echo esc_attr( $rule_id ); ?>">
			<td class="bt-condition">
				<select id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-condition" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][condition]">
					<?php echo BTOptionValues::get_condition_options( $rule['condition'] ); ?>
				</select>
			</td>
			<td class="bt-operator">
				<select id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-operator" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][operator]">
					<?php echo BTOptionValues::get_operator_options( $rule['operator'] ); ?>
				</select>
			</td>
			<td class="bt-value">
				<select id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-value" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][value]">
					<?php echo BTOptionValues::get_options( $rule['condition'], $rule['value'] ); ?>
				</select>
			</td>
			<td class="bt-and">
				<button class="button button-secondary bt-condition-button-and"><?php esc_html_e( 'and', 'beaver-tunnels' ); ?></button>
			</td>
			<td class="bt-remove">
				<button class="button button-secondary bt-condition-button-remove"><span class="dashicons dashicons-no-alt"></span></button>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render row - Date
	 *
	 * @since 2.0
	 *
	 * @param  string $field_id Field ID.
	 * @param  array  $rule     Rule.
	 * @param  string $group_id Group ID.
	 * @param  string $rule_id  Rule ID.
	 *
	 * @return void
	 */
	static public function row_date( $field_id, $rule, $group_id, $rule_id ) {
		?>
		<tr data-bt-group="<?php echo esc_attr( $group_id ); ?>" data-bt-rule="<?php echo esc_attr( $rule_id ); ?>" class="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>">
			<td class="bt-condition">
				<select id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-condition" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][condition]">
					<?php echo BTOptionValues::get_condition_options( $rule['condition'] ); ?>
				</select>
				<input type="hidden" id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-operator" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][operator]" value="<?php echo esc_attr( $rule['operator'] ); ?>">
			</td>
			<td class="bt-value daterange daterange--single" colspan="2">
				<input type="text" id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-value" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][value]" value="<?php echo esc_attr( $rule['value'] ); ?>">
			</td>
			<td class="bt-and">
				<button class="button button-secondary bt-condition-button-and"><?php esc_html_e( 'and', 'beaver-tunnels' ); ?></button>
			</td>
			<td class="bt-remove">
				<button class="button button-secondary bt-condition-button-remove"><span class="dashicons dashicons-no-alt"></span></button>
			</td>
		</tr>
		<script>
		jQuery( document ).ready(function($) {
			jQuery('#<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-value').datetimepicker({
				format: 'Y-m-d H:i',
				step: 15
			});
		});
		</script>
		<?php
	}

	/**
	 * Render row - Time
	 *
	 * @since 2.0
	 *
	 * @param  string $field_id Field ID.
	 * @param  array  $rule     Rule.
	 * @param  string $group_id Group ID.
	 * @param  string $rule_id  Rule ID.
	 *
	 * @return void
	 */
	static public function row_time( $field_id, $rule, $group_id, $rule_id ) {
		?>
		<tr data-bt-group="<?php echo esc_attr( $group_id ); ?>" data-bt-rule="<?php echo esc_attr( $rule_id ); ?>" class="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>">
			<td class="bt-condition">
				<select id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-condition" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][condition]">
					<?php echo BTOptionValues::get_condition_options( $rule['condition'] ); ?>
				</select>
				<input type="hidden" id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-operator" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][operator]" value="<?php echo esc_attr( $rule['operator'] ); ?>">
			</td>
			<td class="bt-value" colspan="2">
				<input type="text" id="<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-value" name="<?php echo esc_attr( $field_id ); ?>[group_<?php echo esc_attr( $group_id ); ?>][rule_<?php echo esc_attr( $rule_id ); ?>][value]" value="<?php echo esc_attr( $rule['value'] ); ?>">
			</td>
			<td class="bt-and">
				<button class="button button-secondary bt-condition-button-and"><?php esc_html_e( 'and', 'beaver-tunnels' ); ?></button>
			</td>
			<td class="bt-remove">
				<button class="button button-secondary bt-condition-button-remove"><span class="dashicons dashicons-no-alt"></span></button>
			</td>
		</tr>
		<script>
		jQuery( document ).ready(function($) {
			jQuery('#<?php echo esc_attr( $field_id ); ?>-group_<?php echo esc_attr( $group_id ); ?>-rule_<?php echo esc_attr( $rule_id ); ?>-value').wickedpicker({
				now: '<?php echo esc_attr( (string) date( 'G:i', strtotime( str_replace( ' ', '', $rule['value'] ) ) ) ); ?>',
				title: '<?php esc_attr_e( 'Choose a time', 'beaver-tunnels' ); ?>'
			});
		});
		</script>
		<?php
	}

	/**
	 * Chosen jQuery Plugin
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	static private function enqueue_chosen() {
		wp_enqueue_script( 'chosen', BEAVER_TUNNELS_PLUGIN_URL . 'lib/chosen/chosen.jquery.min.js', array( 'jquery' ), '1.5.1' );
		wp_enqueue_style( 'chosen', BEAVER_TUNNELS_PLUGIN_URL . 'lib/chosen/chosen.min.css' );
	}

	/**
	 * DateTimePicker jQuery Plugin
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	static private function enqueue_datetimepicker() {
		wp_enqueue_script( 'jquery-datetimepicker', BEAVER_TUNNELS_PLUGIN_URL . 'lib/datetimepicker/jquery.datetimepicker.full.min.js', array( 'jquery' ), '2.5.4' );
		wp_enqueue_style( 'jquery-datetimepicker', BEAVER_TUNNELS_PLUGIN_URL . 'lib/datetimepicker/jquery.datetimepicker.min.css', array(), '2.5.4' );
	}

	/**
	 * Timepicker jQuery Plugin
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	static private function enqueue_timepicker() {
		wp_enqueue_script( 'wickedpicker', BEAVER_TUNNELS_PLUGIN_URL . 'lib/wickedpicker/wickedpicker.min.js', array( 'jquery' ), '0.4.1' );
		wp_enqueue_style( 'wickedpicker', BEAVER_TUNNELS_PLUGIN_URL . 'lib/wickedpicker/wickedpicker.min.css' );
	}

}
