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
		$this->defaults = ACFRcwdCommon::get_var('defaults');
	
		// do not delete!
    	parent::__construct();
    	// settings
		$this->settings = array(
		
			'path'      => plugin_dir_path( __FILE__ ),
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
	
	function render_field_settings($field){

		if(ACFRcwdCommon::serial_is_valid()){
			
			$key = $field['name'];
			//$key_ = str_replace( '][', '___', $key ); //replace required to work with field inside a repeater


			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Filesize limit', 'acf-rcwdupload' ),
				'instructions'		=> __( 'Limit the upload size of the files.', 'acf-rcwdupload' ),
				'type'				=> 'text',
				'key'				=> 'filesize',
				'name'				=> 'filesize',
				'prepend'			=> __( 'Size', 'acf-rcwdupload' ),
				'append'			=> 'MB'
				
			));

			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Rename the file', 'acf-rcwdupload' ),
				'instructions'	=> __( 'Change the file name, keeping the original extension.<br />If the repeater is active, a progressive numeric suffix will be added to the filename.', 'acf-rcwdupload' ),
				'type'			=> 'text',
				'key'			=> 'rename',
				'name'			=> 'rename',
				
			));
			
			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Show image preview BEFORE upload', 'acf-rcwdupload' ),
				'type'			=> 'select',
				'key'			=> 'clientpreview',
				'name'			=> 'clientpreview',
				'choices' 		=> array(
				
					'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
					'N'	=> __( 'No', 'acf-rcwdupload' )
					
				)
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Info', 'acf-rcwdupload' ),
				'instructions'		=> __( '0 = proportional resizing.', 'acf-rcwdupload' ),
				'type'				=> 'number',
				'key'				=> 'clientpreview_max_width',
				'name'				=> 'clientpreview_max_width',
				'wrapper'			=> array( 'class' => @$field['clientpreview'] != 'Y' ? 'hidden-by-conditional-logic' : '' ),
				'conditional_logic'	=> array(array(array( 'field' => 'clientpreview','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Width', 'acf-rcwdupload' ),
				'append'			=> 'px'
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Height', 'acf-rcwdupload' ),
				'instructions'		=> __( '0 = proportional resizing.', 'acf-rcwdupload' ),
				'type'				=> 'number',
				'key'				=> 'clientpreview_max_height',
				'name'				=> 'clientpreview_max_height',
				'wrapper'			=> array( 'class' => @$field['clientpreview'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'clientpreview_max_width' ),
				'conditional_logic'	=> array(array(array( 'field' => 'clientpreview','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Height', 'acf-rcwdupload' ),
				'append'			=> 'px'
			
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Crop', 'acf-rcwdupload' ),
				'type'				=> 'select',
				'key'				=> 'clientpreview_crop',
				'name'				=> 'clientpreview_crop',
				'wrapper'			=> array( 'class' => @$field['clientpreview'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'clientpreview_max_width' ),
				'conditional_logic'	=> array(array(array( 'field' => 'clientpreview','operator' => '==', 'value' => '' ))),
				'choices' 			=> array(
				
					''	=> '---'.__( 'Crop', 'acf-rcwdupload' ).' ---',
					'N'	=> __( 'No', 'acf-rcwdupload' ),
					'Y'	=> __( 'Yes', 'acf-rcwdupload' )
					
				)
				
			));

			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Show image preview AFTER upload', 'acf-rcwdupload' ),
				'type'			=> 'select',
				'key'			=> 'preview',
				'name'			=> 'preview',
				'choices' 		=> array(
				
					'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
					'N'	=> __( 'No', 'acf-rcwdupload' )
					
				)
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Info', 'acf-rcwdupload' ),
				'instructions'		=> __( '0 = proportional resizing.', 'acf-rcwdupload' ),
				'type'				=> 'number',
				'key'				=> 'preview_max_width',
				'name'				=> 'preview_max_width',
				'wrapper'			=> array( 'class' => @$field['preview'] != 'Y' ? 'hidden-by-conditional-logic' : '' ),
				'conditional_logic'	=> array(array(array( 'field' => 'preview','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Width', 'acf-rcwdupload' ),
				'append'			=> 'px'
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Height', 'acf-rcwdupload' ),
				'instructions'		=> __( '0 = proportional resizing.', 'acf-rcwdupload' ),
				'type'				=> 'number',
				'key'				=> 'preview_max_height',
				'name'				=> 'preview_max_height',
				'wrapper'			=> array( 'class' => @$field['preview'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'preview_max_width' ),
				'conditional_logic'	=> array(array(array( 'field' => 'preview','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Height', 'acf-rcwdupload' ),
				'append'			=> 'px'
			
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Crop', 'acf-rcwdupload' ),
				'type'				=> 'select',
				'key'				=> 'preview_crop',
				'name'				=> 'preview_crop',
				'wrapper'			=> array( 'class' => @$field['preview'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'preview_max_width' ),
				'conditional_logic'	=> array(array(array( 'field' => 'preview','operator' => '==', 'value' => '' ))),
				'choices' 			=> array(
				
					''	=> '---'.__( 'Crop', 'acf-rcwdupload' ).' ---',
					'N'	=> __( 'No', 'acf-rcwdupload' ),
					'Y'	=> __( 'Yes', 'acf-rcwdupload' )
					
				)
				
			));
			
			get_currentuserinfo();

			if( get_current_user_id() == 1 and $current_user->user_login == 'roberto.c' ){
				
				acf_render_field_setting( $field, array(
				
					'label'			=> __( 'Collection', 'acf-rcwdupload' ),
					'type'			=> 'select',
					'key'			=> 'collection',
					'name'			=> 'collection',
					'choices' 		=> array(
					
						'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
						'N'	=> __( 'No', 'acf-rcwdupload' )
						
					)
					
				));
				
				acf_render_field_setting( $field, array(
				
					'label'			=> __( 'Collection key', 'acf-rcwdupload' ),
					'type'			=> 'text',
					'key'			=> 'collection_key',
					'name'			=> 'collection_key',
					'conditional_logic'	=> array(array(array( 'field' => 'collection','operator' => '==', 'value' => 'Y' ))),
					
				));
							
			}
				
			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Filters', 'acf-rcwdupload' ),
				'instructions'	=> __( 'Enter the file extensions, separated by commas, to enable file uploading.', 'acf-rcwdupload' ),
				'type'			=> 'select',
				'key'			=> 'filters',
				'name'			=> 'filters',
				'choices' 		=> array(
				
					'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
					'N'	=> __( 'No', 'acf-rcwdupload' )
					
				)
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Extensions', 'acf-rcwdupload' ),
				'type'				=> 'text',
				'key'				=> 'filter_images',
				'name'				=> 'filter_images',
				'wrapper'			=> array( 'class' => @$field['filters'] != 'Y' ? 'hidden-by-conditional-logic' : '' ),				
				'conditional_logic'	=> array(array(array( 'field' => 'filters','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Images', 'acf-rcwdupload' )
				
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Docs', 'acf-rcwdupload' ),
				'type'				=> 'text',
				'key'				=> 'filter_docs',
				'name'				=> 'filter_docs',
				'wrapper'			=> array( 'class' => @$field['filters'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'filter_images' ),				
				'conditional_logic'	=> array(array(array( 'field' => 'filters','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Docs', 'acf-rcwdupload' )
				
			));

			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Extensions', 'acf-rcwdupload' ),
				'type'			=> 'text',
				'key'			=> 'filter_compr',
				'name'			=> 'filter_compr',
				'wrapper'			=> array( 'class' => @$field['filters'] != 'Y' ? 'hidden-by-conditional-logic' : '' ),				
				'conditional_logic'	=> array(array(array( 'field' => 'filters','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Compressed files', 'acf-rcwdupload' )
				
			));
						
			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Other files', 'acf-rcwdupload' ),
				'instructions'	=> '',
				'type'			=> 'text',
				'key'			=> 'filter_others',
				'name'			=> 'filter_others',
				'wrapper'			=> array( 'class' => @$field['filters'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'filter_compr' ),				
				'conditional_logic'	=> array(array(array( 'field' => 'filters','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Other files', 'acf-rcwdupload' )
				
			));

			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Resize images', 'acf-rcwdupload' ),
				'instructions'	=> __( 'Enable resizing','acf-rcwdupload' ),
				'type'			=> 'select',
				'key'			=> 'resize',
				'name'			=> 'resize',
				'choices' 		=> array(
				
					'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
					'N'	=> __( 'No', 'acf-rcwdupload' )
					
				)	
							
			));

			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Info', 'acf-rcwdupload' ),
				'instructions'	=> __( '0 = proportional resizing.', 'acf-rcwdupload' ),
				'type'			=> 'text',
				'key'			=> 'max_width',
				'name'			=> 'max_width',
				'wrapper'			=> array( 'class' => @$field['resize'] != 'Y' ? 'hidden-by-conditional-logic' : '' ),
				'conditional_logic'	=> array(array(array( 'field' => 'resize','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'W', 'acf-rcwdupload' ),
				'append'		=> 'px'
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Height', 'acf-rcwdupload' ),
				'instructions'		=> __( '0 = proportional resizing.', 'acf-rcwdupload' ),
				'type'				=> 'text',
				'key'				=> 'max_height',
				'name'				=> 'max_height',
				'wrapper'			=> array( 'class' => @$field['resize'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'max_width' ),
				'conditional_logic'	=> array(array(array( 'field' => 'resize','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'H', 'acf-rcwdupload' ),
				'append'			=> 'px'
			
			));

			if($field['max_quality'] == '')
				$field['max_quality'] = 90;
											
			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Quality', 'acf-rcwdupload' ),
				'type'			=> 'text',
				'key'			=> 'max_quality',
				'name'			=> 'max_quality',
				'wrapper'			=> array( 'class' => @$field['resize'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'max_width' ),
				'conditional_logic'	=> array(array(array( 'field' => 'resize','operator' => '==', 'value' => 'Y' ))),
				'prepend'			=> __( 'Q', 'acf-rcwdupload' ),
				'append'		=> 'px'
				
			));

			acf_render_field_setting( $field, array(
			
				'label'				=> __( 'Crop', 'acf-rcwdupload' ),
				'type'				=> 'select',
				'key'				=> 'crop',
				'name'				=> 'crop',
				'wrapper'			=> array( 'class' => @$field['resize'] != 'Y' ? 'hidden-by-conditional-logic' : '', 'data-append' => 'max_width' ),
				'conditional_logic'	=> array(array(array( 'field' => 'resize','operator' => '==', 'value' => 'Y' ))),
				'choices' 			=> array(
				
					''	=> '---'.__( 'Crop', 'acf-rcwdupload' ).' ---',
					'N'	=> __( 'No', 'acf-rcwdupload' ),
					'Y'	=> __( 'Yes', 'acf-rcwdupload' )
					
				)
				
			));
						
			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Resize the images to clientside', 'acf-rcwdupload' ),
				'instructions'	=> __( 'Images will be automatically resized before the upload step in order to reduce the weight.', 'acf-rcwdupload' ),
				'type'			=> 'select',
				'key'			=> 'csresize',
				'name'			=> 'csresize',
				'wrapper'			=> array( 'class' => @$field['resize'] != 'Y' ? 'hidden-by-conditional-logic' : '' ),
				'conditional_logic'	=> array(array(array( 'field' => 'resize','operator' => '==', 'value' => 'Y' ))),
				'choices' 		=> array(
				
					'N'	=> __( 'No', 'acf-rcwdupload' ),
					'Y'	=> __( 'Yes', 'acf-rcwdupload' )
					
				)	
				
			));
						
			acf_render_field_setting( $field, array(
			
				'label'			=> __( 'Upload the file after selection', 'acf-rcwdupload' ),
				'type'			=> 'select',
				'name'			=> 'autoupload',
				'choices' 		=> array(
				
					'N'	=> __( 'No', 'acf-rcwdupload' ),
					'Y'	=> __( 'Yes', 'acf-rcwdupload' )
					
				)	
				
			));

		}
	
	}

	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add css + javascript to assist your render_field_options() action.
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
						
	function render_field($field){

		acf_rcwdup_m::create_field(array( 'field' => $field, 'v' => 5 ));
		
	}

	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*/
		
	function input_admin_enqueue_scripts(){
		
		acf_rcwdup_m::admin_enqueue_scripts(5);
		
	}

	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add css and javascript to assist your create_field() action.
	*
	*/
	
	function input_admin_head(){
			
		acf_rcwdup_m::admin_head(5);
		
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
			
	function update_value( $value, $post_id, $field ){
		
		return acf_rcwdup_m::update_value(array( 'value' => $value, 'post_id' => $post_id, 'field' => $field ));
		
	}

	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*/
	
	function format_value( $value, $post_id, $field ){
		
		return acf_rcwdup_m::format_value_for_api(array( 'value' => $value, 'post_id' => $post_id, 'field' => $field ));
		
	}
	
	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*/

	function load_field($field){
		
		return $field;
		
	}

	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*/
	
	function update_field( $field ){
		
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