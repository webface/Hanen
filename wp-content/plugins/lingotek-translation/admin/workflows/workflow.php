<?php

/**
 *   The base class that all specialized workflows can override.
 */
class Lingotek_Workflow {

	/**
	 *   If a modal has already been written to the output buffer then we don't
	 *   want to write it again.
	 *
	 *   @var boolean
	 */
	protected $info_modal_launched = false;

	/**
	 *   If a modal has already been written to the output buffer then we don't
	 *   want to write it again.
	 *
	 *   @var boolean
	 */
	protected $post_modal_launched = false;

	/**
	 *   If a modal has already been written to the output buffer then we don't
	 *   want to write it again.
	 *
	 *   @var boolean
	 */
	protected $terms_modal_launched = false;

	/**
	 *   This method is called when the Posts or Pages pages are loaded. The overriding class may choose to
	 *   load a custom js file to handle events or do any sort of custom 'prep work'.
	 *
	 *   @param string $id the workflow id.
	 */
	public function override_events( $id ) {}

	/**
	 *   Writes a modal to the output buffer. This is called when the Translation > Settings > Defaults pages is loaded.
	 *   The modal should contain information about the workflow.
	 *   NOTE: The workflow-defaults.js file is already loaded and set up to launch this modal. The workflow_id embedded in the
	 *   html is used to identify the modal and show it to the user.
	 *
	 *   @param string $id the workflow id.
	 */
	public function echo_info_modal( $id ) {}

	/**
	 *   This method is called when the Posts or Pages columns are being rendered.
	 *
	 *   @param string $id the workflow id.
	 */
	public function echo_posts_modal( $id ) {}

	/**
	 *   This method is called when the Terms table is being rendered.
	 *
	 *   @param string $id the workflow id.
	 */
	public function echo_terms_modal( $id ) {}

	/**
	 *   This method writes the modal that should be displayed when
	 *   the request icon is clicked.
	 *
	 *   @param string $id the workflow id.
	 */
	public function echo_request_modal( $id ) {}

	/**
	 *   This method acts as a template for building the modals. The arguments passed
	 *   are inserted into the html string and then echo'd. If any extra html elements
	 *   need to be added at a later date, they must be added to the $allowed_html array so
	 *   that they are not stripped away during the wp_kses() call.
	 *
	 *   @param array $args the arguments to populate the modal.
	 */
	protected function _echo_modal( $args ) {
		/**
		*   This allows us to use the 'display' CSS attribute. WP
		*   blacklists it by default.
		*/
		add_filter( 'safe_style_css', array(&$this, 'add_modal_styles'));

		$allowed_html = array(
			'div' => array(
				'id' => array(),
				'style' => array(),
			),
			'h2' => array(
				'style' => array(),
			),
			'br' => array(),
			'b' => array(),
			'p' => array(
				'style' => array(),
			),
			'a' => array(
				'id' => array(),
				'href' => array(),
				'style' => array(),
				'class' => array()
			),
		);
		$id = isset( $args['id'] ) ? '-' . $args['id'] : '';
		echo wp_kses( "<div id='modal-window-id" . esc_attr( $id ) . "' style='display:none; height:100%;' >
                    <h2 style='text-align:center; font-size:large;'>" . esc_html( $args['header'] ) . "</h2>
                    <p style='text-align:center; font-size:110%;'>" . wp_kses( $args['body'], $allowed_html ) . "</p>
                    <br>
                    <a id='yes" . esc_attr( $id ) . "' class='lingotek-color dashicons' href='#' style='position:absolute; float:left; bottom:30px;'>Continue</a>
					<div style='float:right; padding-right:55px;'>
                    <a id='no" . esc_attr( $id ) . "' class='lingotek-color dashicons' href='#' style='position:absolute; float:right; bottom:30px;'>Cancel</a> 
					</div>
					</div>", $allowed_html);
	}

	public function add_modal_styles()
	{
		$styles = array();
		$styles[] = 'display';
		$styles[] = 'position';
		$styles[] = 'bottom';
		return $styles;
	}
}
