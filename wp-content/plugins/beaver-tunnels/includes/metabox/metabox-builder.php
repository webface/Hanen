<?php
/**
 * Beaver Tunnels Meta Box Builder
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles the rendering of the metabox
 */
class BT_Meta_Box_Builder {

	/**
	 * ID
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Title
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * Context
	 *
	 * @var string
	 */
	private $context = 'advanced';

	/**
	 * Priority
	 *
	 * @var string
	 */
	private $priority = 'default';

	/**
	 * Screens
	 *
	 * @var array
	 */
	private $screens = array();

	/**
	 * Fields
	 *
	 * @var array
	 */
	private $fields = array();

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 *
	 * @param array $metabox Array of metabox settings.
	 */
	public function __construct( $metabox ) {

		if ( ! is_array( $metabox ) ) {
			return;
		}

		if ( isset( $metabox['id'] ) ) {
			$this->id = $metabox['id'];
		}

		if ( isset( $metabox['title'] ) ) {
			$this->title = $metabox['title'];
		}

		if ( isset( $metabox['context'] ) ) {
			$this->context = $metabox['context'];
		}

		if ( isset( $metabox['priority'] ) ) {
			$this->priority = $metabox['priority'];
		}

		if ( isset( $metabox['screens'] ) ) {
			$this->screens = $metabox['screens'];
		}

		if ( isset( $metabox['fields'] ) ) {
			$this->fields = $metabox['fields'];
		}

		$this->actions();

	}

	/**
	 * Meta Box Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function actions() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Creates a meta box for each screen
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		foreach ( $this->screens as $screen ) {

			add_meta_box(
				$this->id,
				$this->title,
				array( $this, 'add_meta_box_callback' ),
				$screen,
				$this->context,
				$this->priority
			);

		}

	}

	/**
	 * Generates the meta box output
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The post object.
	 */
	public function add_meta_box_callback( $post ) {

		wp_nonce_field( $this->id . '_metabox', $this->id . '_metabox' );
		$this->build_fields( $post );

	}

	/**
	 * Builds the specified fields
	 *
	 * @since 1.0.0
	 *
	 * @param  object $post The post object.
	 *
	 * @return void
	 */
	public function build_fields( $post ) {

		$output = '';
		foreach ( $this->fields as $field ) {

			$label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			if ( isset( $field['desc'] ) ) {
				$label .= sprintf(
					'<p class="description">%s</p>',
					$field['desc']
				);
			}

			$current_value = get_post_meta( $post->ID, $field['id'], true );

			switch ( $field['type'] ) {

				case 'checkbox':
					require_once plugin_dir_path( __FILE__ ) . 'fields/checkbox.php';
					$input = BT_MetaBox_Field_Checkbox::render( $field, $current_value );
					break;

				case 'condition':
					require_once plugin_dir_path( __FILE__ ) . 'fields/display-condition.php';
					$input = BT_MetaBox_Field_Display_Condition::render( $field, $current_value );
					break;

				case 'action_hook':
					require_once plugin_dir_path( __FILE__ ) . 'fields/action-hook.php';
					$input = BT_MetaBox_Field_Action_Hook::render( $field, $current_value );
					break;

			}

			$output .= $this->row_format( $label, $input );

		}

		echo '<div class="bt-loading"><img src="' . esc_attr( BEAVER_TUNNELS_PLUGIN_URL ) . '/assets/img/loading.gif" /></div><table class="bt-form-table widefat" style="table-layout: fixed;"><tbody>' . $output . '</tbody></table>';

	}

	/**
	 * Builds a string of attributes for a field
	 *
	 * @since 1.0.0
	 *
	 * @param  array $field The field attributes.
	 *
	 * @return string        The string of field attributes
	 */
	private function get_attributes( $field ) {

		if ( ! isset( $field['attributes'] ) || ! is_array( $field['attributes'] ) ) {
			return '';
		}

		$attr_string = '';
		foreach ( $field['attributes'] as $key => $value ) {
			$attr_string .= ' ' . $key . '="' . $value . '"';
		}
		return $attr_string;

	}

	/**
	 * Build the row formatting
	 *
	 * @since 1.0.0
	 *
	 * @param  string $label The field label.
	 * @param  string $input The field HTML.
	 *
	 * @return string        The row HTML
	 */
	public function row_format( $label, $input ) {
		return sprintf(
			'<tr><td class="label">%s</th><td>%s</td></tr>',
			$label,
			$input
		);
	}

	/**
	 * Save the field data
	 *
	 * @since 1.0.0
	 *
	 * @param  string $post_id The post ID.
	 *
	 * @return mixed
	 */
	public function save_post( $post_id ) {

		if ( ! isset( $_POST[ $this->id . '_metabox' ] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST[ $this->id . '_metabox' ], $this->id . '_metabox' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		foreach ( $this->fields as $field ) {

			if ( isset( $_POST[ $field['id'] ] ) ) {

				update_post_meta( $post_id, $field['id'], $_POST[ $field['id'] ] );

			} else {

				switch ( $field['type'] ) {

					case 'checkbox':
						update_post_meta( $post_id, $field['id'], '0' );
						break;

				}
			}
		}

	}

}
