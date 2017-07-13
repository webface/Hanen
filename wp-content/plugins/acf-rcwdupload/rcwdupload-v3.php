<?php
include_once('main.php');

class acf_field_rcwdupload extends acf_field{

	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- This function is called when the field class is initalized on each page.
	*	- Here you can add filters / actions and setup any other functionality for your field
	*
	*-------------------------------------------------------------------------------------*/

	public function __construct($parent){

    	parent::__construct($parent);

    	$this->name 	= ACFRcwdCommon::get_var('name');
		$this->title 	= ACFRcwdCommon::get_var('label');
		$this->category = ACFRcwdCommon::get_var('category');
		$this->domain 	= ACFRcwdCommon::get_var('domain');
		$this->version 	= ACFRcwdCommon::get_var('version');

		add_action( 'admin_print_scripts', array( &$this, 'admin_print_scripts' ), 12, 0 );
		add_action( 'admin_print_styles',  array( &$this, 'admin_print_styles' ),  12, 0 );

		$mofile = trailingslashit(dirname(__FILE__)).'lang'.DIRECTORY_SEPARATOR.$this->domain.'-'.get_locale().'.mo';
		load_textdomain( $this->domain, $mofile );		
		
	}

	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*	- this function is called in the admin_head of the edit screen where your field
	*	is created. Use this function to create css and javascript to assist your
	*	create_field() function.
	*
	*-------------------------------------------------------------------------------------*/

	public function admin_head(){
		
		acf_rcwdup_m::admin_head();
		
	}

	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*	- this function is called in the admin_print_scripts / admin_print_styles where
	*	your field is created. Use this function to register css and javascript to assist
	*	your create_field() function.
	*
	*-------------------------------------------------------------------------------------*/

	public function admin_print_styles(){
	}

	public function admin_print_scripts(){
		
		acf_rcwdup_m::admin_enqueue_scripts(3);
		
	}

	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- this function is called from core/field_meta_box.php to create extra options
	*	for your field
	*
	*-------------------------------------------------------------------------------------*/
	
	public function create_options($key, $field){
		
		acf_rcwdup_m::create_options(array( 'key' => $key, 'field' => $field, 'v' => 3 ));
		
	}

	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- this function is called on edit screens to produce the html for this field
	*
	*-------------------------------------------------------------------------------------*/
	
	public function create_field($field){

		acf_rcwdup_m::create_field(array( 'field' => $field, 'v' => 3 ));
		
	}

	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*	- this function is called when saving a post object that your field is assigned to.
	*	the function will pass through the 3 parameters for you to use.
	*
	*-------------------------------------------------------------------------------------*/

	public function update_value($post_id, $field, $value){
		
		$value = acf_rcwdup_m::update_value(array( 'value' => $value, 'post_id' => $post_id, 'field' => $field ));
		
		parent::update_value($post_id, $field, $value);
		
	}	

	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc).
	*	This function is useful if your field needs to format the returned value
	*
	*-------------------------------------------------------------------------------------*/
	
	public function get_value_for_api($post_id, $field){
		
		$value = $this->get_value($post_id, $field);
		
		return acf_rcwdup_m::format_value_for_api(array( 'value' => $value, 'post_id' => $post_id, 'field' => $field ));

	}	
}
?>