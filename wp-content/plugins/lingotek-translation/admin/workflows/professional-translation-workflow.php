<?php

/**
 *   Overrides uploads for Posts, Pages and Terms in order to display a modal and handle redirecting to bridge for payment.
 */
class Lingotek_Professional_Translation_Workflow extends Lingotek_Workflow {

	/**
	 *   Adds the thickbox Wordpress component. Loades the professional-workflow js file that attaches listeners to uploads.
	 *   Sends the workflow id to the js file.
	 *
	 *   @param string $id the workflow id.
	 */
	public function override_events( $id ) {
		add_thickbox();
		wp_enqueue_script( 'lingotek_professional_workflow', LINGOTEK_URL . '/js/workflow/professional-workflow.js' );
		$vars = array(
			'id' => $id,
		);
		wp_localize_script( 'lingotek_professional_workflow', 'workflow_vars', $vars );
	}

	/**
	 *   Writes a modal to the output buffer that contains information about this workflow.
	 *
	 *   @param string $id the workflow id.
	 */
	public function echo_info_modal( $id ) {
		if ( ! $this->info_modal_launched ) {
			$this->info_modal_launched = true;
			$args = array(
				'header' => __( 'Professional Translation Workflow', 'lingotek-translation' ),
				'body' => __( "The Professional Translation Workflow allows you to have any of your content 
							translated professionally. Translations are priced by word count and can be requested from within
							the Wordpress Posts or Pages tab.
							<br><br>In order to use this workflow you must set up a payment method and purchase Word Credits.
							<br><br>Would you like to set up a payment method?", 'lingotek-translation' ),
				'id' => $id,
			);
			$this->_echo_modal( $args );
		}
	}

	/**
	 *   Writes a modal to the output buffer that tells the user how much it is going to cost.
	 *
	 *   @param string $id the workflow id.
	 */
	public function echo_posts_modal( $id ) {
		if ( ! $this->post_modal_launched ) {
			$this->post_modal_launched = true;
			$args = array(
				'header' => __( 'Confirm Document Upload', 'lingotek-translation' ),
				'body' => __( "You have the Professional Translation workflow selected. This means that all 
							translations of this document will cost Word Credits.
						<br><br>Any translations of the uploaded document will be <b>final</b>. A new transaction(s) will be made if this document is 
						edited and re-translations are requested.
							<br><br>Would you like to continue?", 'lingotek-translation' ),
				'id' => $id,
			);
			$this->_echo_modal( $args );
			$this->echo_request_modal( $id );
		}
	}


	/**
	 *   Writes a modal to the output buffer that tells the user how much it is going to cost.
	 *
	 *   @param string $id the workflow id.
	 */
	public function echo_terms_modal( $id ) {
		if ( ! $this->terms_modal_launched ) {
			$this->terms_modal_launched = true;
			$args = array(
				'header' => __( 'Lingotek Terms', 'lingotek-translation' ),
				'body' => __( 'This is an example of a terms modal.', 'lingotek-translation' ),
				'id' => $id,
			);
			$this->_echo_modal( $args );
			$this->echo_request_modal( $id );
		}

	}

	/**
	 *	Writes a modal to the output buffer that is launched when the user clicks on the 'request translation'
	 *	option.
	 *
	 *	@param string $id the workflow id.
	 */
	public function echo_request_modal( $id ) {
		$args = array(
			'header' => __( 'Confirm Request Translation', 'lingotek-translation' ),
			'body' => __( "You have the Professional Translation workflow selected. This document will be translated by a professional
						in the selected language. 
						
						<br><br>The approximated cost to translate this document is 13 Word Credits.
						<br><br>The translation request is <b>final</b> meaning that if the source document is changed then it will cost Word Credits to request re-translation.
						<br><br> Would you like to request this translation?", 'lingotek-translation' ),
			'id' => $id . '-request',
		);
		$this->_echo_modal( $args );
	}
}
