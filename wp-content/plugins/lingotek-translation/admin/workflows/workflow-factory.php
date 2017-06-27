<?php

/**
 *   WorkflowFactory
 *
 *   Returns an instance of a Workflow object.
 */
class Lingotek_Workflow_Factory {

	const BASE_WORKFLOW = 'Lingotek_Workflow';

	/**
	 *   Constructor. Loads the base workflow class and the base workflow js file.
	 */
	public function __construct() {
		self::class_load( self::BASE_WORKFLOW );
		wp_enqueue_script( 'lingotek_workflow_namespace', LINGOTEK_URL . '/js/workflow/workflow.js' );
	}
	/**
	 *   An associative array that maps a workflowId to a Workflow object.
	 *
	 *   @var array
	 */
	private static $map = array(
		'professional-translation' => 'Lingotek_Professional_Translation_Workflow',
	);

	/**
	 *   Checks the map to see if a specialized workflow exists. A BasicWorkflow is returned
	 *   if a specialized workflow type does not exist.
	 *
	 *   @param string $workflow_id the Id of the given workflow.
	 */
	public static function get_workflow_instance( $workflow_id ) {
		if ( ! isset( self::$map[ $workflow_id ] ) )
		{
			return new Lingotek_Workflow();
		}
		else
		{
			self::class_load( self::$map[ $workflow_id ] );
			return new self::$map[ $workflow_id ]();
		}
	}

	/**
	 *   Echos the information modals describing various details about the workflow.
	 */
	public static function echo_info_modals() {
		add_thickbox();
		foreach ( self::$map as $id => $class ) {
			$workflow = new $class();
			$workflow->echo_info_modal( $id );
		}
	}

	/**
	 *   Lazyloads classes as they are needed.
	 *
	 *   @param string $class class name to be loaded.
	 */
	private static function class_load( $class ) {
		if ( ! isset( $class ) ) { return; }
		if ( ! class_exists( $class ) ) { require_once( LINGOTEK_WORKFLOWS . '/' . Lingotek::convert_class_to_file( $class ) . '.php' ); }
	}
}
