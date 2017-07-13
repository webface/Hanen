<?php
include_once('main.php');

class acf_field_rcwdupload extends acf_field
{
		
	/*
	*  __construct
	*
	*/
	
	function __construct()
	{
		// vars
		$this->name 	= ACFRcwdCommon::get_var('name');
		$this->label 	= ACFRcwdCommon::get_var('label');
		$this->category = ACFRcwdCommon::get_var('category');
		$this->domain 	= ACFRcwdCommon::get_var('domain');
		
		// do not delete!
    	parent::__construct();
    	// settings
		$this->settings = array(
		
			'path'      => apply_filters('acf/helpers/get_path', __FILE__),
			'dir'     	=> apply_filters('acf/helpers/get_dir', __FILE__),
			'version'	=> ACFRcwdCommon::get_var('version')
			
		);		

		$mofile = $this->settings['path'].'lang'.DIRECTORY_SEPARATOR.$this->domain.'-'.get_locale().'.mo';
		load_textdomain( $this->domain, $mofile );
		
		//add_action('acf/delete_value', array($this, 'rcwd_delete_value'), 4, 2); TO-DO: search a way, when field inside a repeater,  to delete files when field is deleted
		
	}
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*/
	
	function create_options($field){

		acf_rcwdup_m::create_options(array( 'field' => $field, 'v' => 4 ));

	}

	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add css + javascript to assist your create_field_options() action.
	*
	*/

	function field_group_admin_enqueue_scripts(){
		
		acf_rcwdup_m::group_admin_enqueue_scripts();
		
	}

	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add css and javascript to assist your create_field_options() action.
	*
	*/

	function field_group_admin_head(){
	}

	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*/
						
	function create_field($field){

		acf_rcwdup_m::create_field(array( 'field' => $field, 'v' => 4 ));
		
	}

	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*/
		
	function input_admin_enqueue_scripts(){
		
		acf_rcwdup_m::admin_enqueue_scripts(4);
		
	}

	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add css and javascript to assist your create_field() action.
	*
	*/
	
	function input_admin_head(){
			
		acf_rcwdup_m::admin_head(4);
		
	}

	/*
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*/
	
	function load_value( $value, $post_id, $field ){
		return $value;
	}

	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*/
			
	function update_value( $value, $post_id, $field ) {
		
		return acf_rcwdup_m::update_value(array( 'value' => $value, 'post_id' => $post_id, 'field' => $field ));
		
	}

	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*/
	
	function format_value( $value, $post_id, $field ){
		return $value;
	}
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*/
	
	function format_value_for_api( $value, $post_id, $field ){
		
		return acf_rcwdup_m::format_value_for_api(array( 'value' => $value, 'post_id' => $post_id, 'field' => $field ));
		
	}

	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*/

	function load_field( $field ){
		return $field;
	}

	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*/
	
	function update_field( $field, $post_id ){
		return $field;
	}
	
/*	function rcwd_delete_value( $post_id, $key ){
		echo $post_id.'_'.$key.'<br />';
		$value 	= get_option($post_id.'_'.$key);
		$field	= get_option('_'.$post_id.'_'.$key);
		if( $value and $field ){
			$fn = ACF_RCWDUPLOAD_UP_DIR.DIRECTORY_SEPARATOR.$field.DIRECTORY_SEPARATOR.$value;
			if(file_exists($fn)){		
				unlink($fn);
			}
		}		
	}*/
}

new acf_field_rcwdupload();
?>