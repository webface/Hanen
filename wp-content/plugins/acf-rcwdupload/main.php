<?php

// UNCOMMENT THIS BLOCK ONLY FOR DEBUG

/* 
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
*/

if (!class_exists('acf_rcwd_clean_exit')){
	
	class acf_rcwd_clean_exit extends RuntimeException{}
	
}

class acf_rcwdup_m{

	function __construct(){

		if(!defined('ACF_RCWDUPLOAD_NAME'))
			define( 'ACF_RCWDUPLOAD_NAME', 'acf-rcwdupload/acf-rcwdupload.php' );
			
		define( 'ACF_RCWDUPLOAD_TITLE', 'Rcwd Upload for Advanced Custom Fields' );
		define( 'ACF_RCWDUPLOAD_UP_TEMP_DIR', WP_CONTENT_DIR.'/acfrcwduploads_temp' );
		define( 'ACF_RCWDUPLOAD_UP_DIR', WP_CONTENT_DIR.'/acfrcwduploads' );
		define( 'ACF_RCWDUPLOAD_UP_TEMP_URL', get_site_url().'/wp-content/acfrcwduploads_temp' );
		define( 'ACF_RCWDUPLOAD_UP_URL', get_site_url().'/wp-content/acfrcwduploads' );	
		define( 'ACF_RCWDUPLOAD_ITEM_ID', '5078456' ); // DEFINE ENVATO ITEM ID

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 11 );
		add_action( 'generate_rewrite_rules', array( 'ACFRcwdCommon', 'generate_rewrite_rules' ) );	
		add_action( 'admin_init', array( 'ACFRcwdCommon', 'flush_rules' ) );	
		add_action( 'init', array( 'ACFRcwdCommon', 'clean_temp' ) );	
		
		if(!ACFRcwdCommon::serial_is_valid()){
			
			add_action( 'admin_notices', array( 'ACFRcwdCommon', 'error_not_authenticated' ) );
			
			return false;
			
		}
		
		if(!wp_mkdir_p(ACF_RCWDUPLOAD_UP_DIR)){
			
			add_action( 'admin_notices', array( 'ACFRcwdCommon', 'error_folder_permissions' ) );
			
			return false;
			
		}else{
			
			wp_mkdir_p(ACF_RCWDUPLOAD_UP_TEMP_DIR);
			
		}

		add_action( 'init', array( 'ACFRcwdCommon', 'handle_upload_request' ) );				
		add_action( 'acf/input/form_data', array( $this, 'acf_input_form_data' ), 10, 1 );					
				
	}

	function admin_menu(){
		
		add_submenu_page( 'edit.php?post_type=acf', __( 'Rcwd Upload settings', 'acf-rcwdupload' ), __( 'Rcwd Upload settings', 'acf-rcwdupload' ), 'manage_options', ACFRcwdCommon::get_var('domain'), array( $this, 'settings' ) );		
		add_submenu_page( 'edit.php?post_type=acf-field-group', __( 'Rcwd Upload settings', 'acf-rcwdupload' ), __( 'Rcwd Upload settings', 'acf-rcwdupload' ), 'manage_options', ACFRcwdCommon::get_var('domain'), array( $this, 'settings' ) );		
	}
	
	function settings(){
		
		include_once('envato/check.php');
		
	}

	static function admin_head($v){

		if(ACFRcwdCommon::serial_is_valid()){
			
			global $pagenow;	
	
			$pst = 0;
			
			if(is_admin()){
				
				if( isset($_GET['page']) and substr( $_GET['page'], 0, 11 ) == 'acf-options' ){
					
					$pst 		= 'options';
					$foldercode = 'options_'.substr( $_GET['page'], 12 );

					$opfolder = substr( $_GET['page'], 12 );

					if(empty($opfolder))
						$foldercode = 'options_rt';
												
				}
		
				if( $pagenow == 'edit-tags.php' and isset($_GET['taxonomy']) ){
					
					$foldercode = 'tax_'.$_GET['taxonomy'];
		
					if( isset($_GET['action']) and $_GET['action'] == "edit" ){
						
						$_GET['tag_ID']	= filter_var( $_GET['tag_ID'], FILTER_SANITIZE_NUMBER_INT );
						$foldercode 	.= '_'.$_GET['tag_ID'];
						
					}
								
				}elseif(in_array( $pagenow, array( 'profile.php', 'user-new.php', 'user-edit.php' ) )){
					
					global $user_id;
					
					if(is_wp_error($user_id))
						return false;
						
					$foldercode = 'user_'.$user_id;
					
				}elseif( in_array( $pagenow, array( 'post.php', 'post-new.php', 'media.php' ) ) ){
					
					global $post;
					
					$pst 		= 'post';
					$foldercode = 'post_'.$post->ID;	
						
				}
				
			
			}else{
				
				if( is_page() or is_single() ){
					
					global $post, $rcwd_custom_post_id;

					$pst = 'post';

					if( !empty($rcwd_custom_post_id) and (int)$rcwd_custom_post_id > 0)
						$foldercode = 'post_'.(int)$rcwd_custom_post_id;
					else
						$foldercode = 'post_'.$post->ID;	
																						
				}
				
			}
?>
			<script type="text/javascript">
				acf_rcwdupload_url					= '<?php echo site_url().'/wp-content/plugins/acf-rcwdupload/upload.php?a=a&post='.$pst.'&foldercode='.$foldercode ?>';
                acf_rcwdupload_flash_swf_url 		= '<?php echo ACFRcwdCommon::get_dir(__FILE__) ?>js/jquery/plupload/Moxie.swf';
                acf_rcwdupload_silverlight_xap_url 	= '<?php echo ACFRcwdCommon::get_dir(__FILE__) ?>js/jquery/plupload/Moxie.xap';
							
				jQuery(function($){
					
					//$(".rcwdacflupload-tabs").tabs();
				
				});				
            </script>
<?php		

		}
	
	}

	function acf_input_form_data($args){

		global $acf_rcwdupload_5_post_id;

		$acf_rcwdupload_5_post_id = $args['post_id'];
		
	}
		
	static function group_admin_enqueue_scripts(){
		
		if(ACFRcwdCommon::serial_is_valid()){
			
			wp_enqueue_style( 'acf-'.ACFRcwdCommon::get_var('name').'-style', ACFRcwdCommon::get_dir(__FILE__).'css/style-group.css');

		}
		
	}
	
	static function admin_enqueue_scripts($v){

		if(ACFRcwdCommon::serial_is_valid()){
		
			//wp_deregister_script('plupload');
			wp_enqueue_script( 'rcwdupload-plupload', ACFRcwdCommon::get_dir(__FILE__).'js/jquery/plupload/plupload.full.min.js', array('jquery'), '2.1.8' );
			wp_enqueue_script( 'rcwd-impromptu', ACFRcwdCommon::get_dir(__FILE__).'js/jquery/impromptu/jquery-impromptu.min.js', array('jquery'), '5.2.5' );
			wp_enqueue_style( 'rcwd-impromptu', ACFRcwdCommon::get_dir(__FILE__).'js/jquery/impromptu/jquery-impromptu.min.css');
			
			wp_enqueue_script( 'acf-'.ACFRcwdCommon::get_var('name').'-scripts', ACFRcwdCommon::get_dir(__FILE__).'js/scripts.js');
			
			//wp_enqueue_script('jquery-ui-dialog'); 
			//wp_enqueue_style('wp-jquery-ui-dialog');		
			//wp_enqueue_style( 'acf-'.ACFRcwdCommon::get_var('name').'-jqueryui', ACFRcwdCommon::get_dir(__FILE__).'css/jquery-ui-custom.min.css');
			
			wp_enqueue_style( 'acf-'.ACFRcwdCommon::get_var('name').'-style', ACFRcwdCommon::get_dir(__FILE__).'css/style.css');
			wp_enqueue_style( 'acf-'.ACFRcwdCommon::get_var('name').'-jquerytabs', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css');

			$wpversion = get_bloginfo('version');
			
			if(version_compare( $wpversion, '3.8', '>='))
				wp_enqueue_style( 'acf-'.ACFRcwdCommon::get_var('name').'-style-2', ACFRcwdCommon::get_dir(__FILE__).'css/style-2.css');
			
			$i18n = array(	'err' 					=> 'We have a problem...',
							'err600' 				=> sprintf( __( 'Sorry, the file size must not be greater than %s kb. Please try another file.', 'acf-rcwdupload' ), round( (int)wp_max_upload_size() / 1024 ) ),
							'err601' 				=> __( 'Sorry, the file extension is not permitted. Please try another file.', 'acf-rcwdupload' ),
							'errqueue' 				=> __( 'It seems that some files need to be uploaded. Please check the upload fields.', 'acf-rcwdupload' ),
							'processing'			=> __( 'wait...', 'acf-rcwdupload' ),							
							'thumburl'				=> AcfRcwdCommon::get_dir(__FILE__).'includes/PHPThumb/thumb.php',
							'flash_swf_url'			=> AcfRcwdCommon::get_dir(__FILE__).'js/jquery/plupload/Moxie.swf',
							'silverlight_xap_url'	=> AcfRcwdCommon::get_dir(__FILE__).'js/jquery/plupload/Moxie.xap',							
							
			);
			wp_localize_script( 'acf-'.ACFRcwdCommon::get_var('name').'-scripts', 'acfrcwdi18n', $i18n );
			
			if($v == 3){
				
				self::group_admin_enqueue_scripts();
				
			}

		}		
				
	}
	
	static function create_options($args){
		
		if(ACFRcwdCommon::serial_is_valid()){
			
			$v			= $args['v'];
			$field		= $args['field'];
			$key 		= $field['name'];
			$key_		= str_replace( '][', '___', $key ); //replace required to work with field inside a repeater
			$defaults 	= ACFRcwdCommon::get_var('defaults');

			$field = array_merge( $defaults, $field );	
										
			if(file_exists(ACF_RCWDUPLOAD_UP_DIR)){
?>
                <tr class="field_option field_option_<?php echo ACFRcwdCommon::get_var('name'); ?>">
                    <td class="label">
                        <label><?php _e( 'Show image preview BEFORE upload', 'acf-rcwdupload' ); ?></label>
                    </td>
                    <td>
<?php
						$f_arr = array(	'type' 		=> 'select',
										'name'    	=> 'fields['.$key.'][clientpreview]',
										'value'  	=> $field['clientpreview'],
										'layout'  	=> 'horizontal',
                                        'class'		=>  ACFRcwdCommon::get_var('name').'_group_clientpreview',
										'choices' 	=> array(
										
											'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
											'N'	=> __( 'No', 'acf-rcwdupload' )
											
						) );
																
                        if($v == 4)
                            do_action( 'acf/create_field', $f_arr );
                        else
                            $this->parent->create_field($f_arr);
?>
					</td>
				</tr>
                <tr>
                	<td class="label">
                        <label><?php _e( 'Show image preview AFTER upload', 'acf-rcwdupload' ); ?></label>
                    </td>
                    <td>
<?php
						$f_arr = array(	'type' 		=> 'select',
										'name'    	=> 'fields['.$key.'][preview]',
										'value'  	=> $field['preview'],
										'layout'  	=> 'horizontal',
                                        'class'		=>  ACFRcwdCommon::get_var('name').'_group_preview',
										'choices' 	=> array(
										
											'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
											'N'	=> __( 'No', 'acf-rcwdupload' )
											
						) );
																
                        if($v == 4)
                            do_action( 'acf/create_field', $f_arr );
                        else
                            $this->parent->create_field($f_arr);

?>                    
                    </td>
                </tr>
                <tr class="field_option field_option_<?php echo ACFRcwdCommon::get_var('name'); ?>">
                    <td class="label">
                        <script type='text/javascript'>
                            jQuery(document).ready(function($){
                
                                // Show/hide filter options ______________________________________________________
                                
                                    if($('.<?php echo ACFRcwdCommon::get_var('name') ?>_group_filters_<?php echo $key_ ?>').val() == 'Y')
                                        $('#<?php echo ACFRcwdCommon::get_var('name') ?>_group_foptions_<?php echo $key_ ?>').fadeTo( 300, 1 );
                
                                    $('.<?php echo ACFRcwdCommon::get_var('name') ?>_group_filters_<?php echo $key_ ?>').on("change", function(event){
										
                        				if($(this).val() == 'Y')
                                			$('#<?php echo ACFRcwdCommon::get_var('name') ?>_group_foptions_<?php echo $key_ ?>').fadeTo( 300, 1 );
                                 		else
                                            $('#<?php echo ACFRcwdCommon::get_var('name') ?>_group_foptions_<?php echo $key_ ?>').fadeTo( 300, 0, function(){
												
                                                $(this).css( 'display', 'none' );
												
                                            });
										
                                    });	
                                                                        
                                // Show/hide resize options ______________________________________________________
                                
                                    if($('.<?php echo ACFRcwdCommon::get_var('name') ?>_group_resize_<?php echo $key_ ?>').val() == 'Y')
                                        $('#<?php echo ACFRcwdCommon::get_var('name') ?>_group_maxdims_<?php echo $key_ ?>').fadeTo( 300, 1 );
                    
                                    $('.<?php echo ACFRcwdCommon::get_var('name') ?>_group_resize_<?php echo $key_ ?>').on("change", function(event){
										
                                        if($(this).val() == 'Y')
                                            $('#<?php echo ACFRcwdCommon::get_var('name') ?>_group_maxdims_<?php echo $key_ ?>').fadeTo( 300, 1 );
                                        else
                                            $('#<?php echo ACFRcwdCommon::get_var('name') ?>_group_maxdims_<?php echo $key_ ?>').fadeTo( 300, 0, function(){
												
                                                $(this).css( 'display', 'none' );
												
                                            });

                                    });	
                                                    
                            });		
                        </script>                
                        <label><?php _e( 'Filters', 'acf-rcwdupload' ); ?></label>
                    </td>
                    <td>
                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Filters the files', 'acf-rcwdupload' ); ?></label>
<?php
						$f_arr = array(	'type' 		=> 'select',
										'name'    	=> 'fields['.$key.'][filters]',
										'value'  	=> $field['filters'],
										'layout'  	=> 'horizontal',
										'class'   	=> ACFRcwdCommon::get_var('name')."_group_filters ".ACFRcwdCommon::get_var('name')."_group_filters_{$key_}",
										'choices' 	=> array(	'Y'	=> __( 'Yes', 'acf-rcwdupload' ),
																'N'	=> __( 'No', 'acf-rcwdupload' ) ) );
																	
                        if($v == 4)
                            do_action( 'acf/create_field', $f_arr );
                        else
                            $this->parent->create_field($f_arr);
?>
                        <table class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_foptions" id="<?php echo ACFRcwdCommon::get_var('name') ?>_group_foptions_<?php echo $key_ ?>" style="width:100%">
                            <tbody>
                                <tr>
                                    <td style="padding-left:0;">
                                        <p class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_description"><?php _e( 'Enter the file extensions, separated by commas, to enable file uploading.', 'acf-rcwdupload' ); ?></a></p>
                                    </td>
                                </tr>
                                <tr>                        
                                    <td style="padding-left:0;">
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Images', 'acf-rcwdupload' ); ?></label>
<?php 
										$f_arr = array(	'type'	=>	'text',
														'name'	=>	'fields['.$key.'][filter_images]',
														'value'	=>	strtolower($field['filter_images']) );
														                      
                                        if($v == 4)
                                            do_action( 'acf/create_field', $f_arr );
                                        else
                                            $this->parent->create_field($f_arr);
?>	
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left:0;">
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Documents','acf-rcwdupload' ); ?></label>
<?php 
										$f_arr = array(	'type'	=>	'text',
														'name'	=>	'fields['.$key.'][filter_docs]',
														'value'	=>	strtolower($field['filter_docs']) );
															    
                                        if($v == 4)
                                            do_action( 'acf/create_field', $f_arr );
                                        else
                                            $this->parent->create_field($f_arr);
?>	
                                    </td>
                                </tr>    
                                <tr>
                                    <td style="padding-left:0;">
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Compressed files', 'acf-rcwdupload' ); ?></label>
<?php 
										$f_arr = array(	'type'	=>	'text',
														'name'	=>	'fields['.$key.'][filter_compr]',
														'value'	=>	strtolower($field['filter_compr']) );
															    
                                        if($v == 4)
                                            do_action( 'acf/create_field', $f_arr );
                                        else
                                            $this->parent->create_field($f_arr);
?>	
                                    </td>
                                </tr> 
                                <tr>
                                    <td style="padding-left:0;">
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Other files', 'acf-rcwdupload' ); ?></label>
<?php 
										$f_arr = array(	'type'	=>	'text',
														'name'	=>	'fields['.$key.'][filter_others]',
														'value'	=>	strtolower($field['filter_others']) );
															   
                                        if($v == 4)
                                            do_action( 'acf/create_field', $f_arr );
                                        else
                                            $this->parent->create_field($f_arr);
?>	
                                    </td>
                                </tr>                                                                   
                            </tbody>
                        </table> 
                    </td>
                </tr>
                <tr class="field_option field_option_<?php echo ACFRcwdCommon::get_var('name'); ?>">
                    <td class="label">
                        <label><?php _e( 'Resize images', 'acf-rcwdupload' ); ?></label>
                    </td>
                    <td>
                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Enable resizing','acf-rcwdupload' ); ?></label>
<?php
                        if($v == 4){
                            $f_arr = array(	'type' 		=> 'select',
                                            'name'    	=> 'fields['.$key.'][resize]',
                                            'value'  	=> $field['resize'],
                                            'layout'  	=> 'horizontal',
                                            'class'   	=> ACFRcwdCommon::get_var('name')."_group_resize ".ACFRcwdCommon::get_var('name')."_group_resize_{$key_}",
                                            'choices' 	=> array(	'N'	=> __( 'No', 'acf-rcwdupload' ),
                                                                    'Y'	=> __( 'Yes', 'acf-rcwdupload' ) )
                                          );
                            do_action( 'acf/create_field', $f_arr );
                        }else{
                            $this->parent->create_field($f_arr);
                        }
?>            
                        <table class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_maxdims" id="<?php echo ACFRcwdCommon::get_var('name') ?>_group_maxdims_<?php echo $key_ ?>">
                            <tbody>
                                <tr>            
                                    <td style="padding-left:0;">
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Width', 'acf-rcwdupload' ); ?></label>
<?php 
                                        if($v == 4){
                                            $f_arr = array(	'type'	=>	'text',
                                                            'name'	=>	'fields['.$key.'][max_width]',
                                                            'value'	=>	(int)$field['max_width'],
                                                            'class'	=>  ACFRcwdCommon::get_var('name').'_group_maxdim'
                                                          );
                                            do_action( 'acf/create_field', $f_arr );
                                        }else{
                                            $this->parent->create_field($f_arr);
                                        }
?>					
                                    </td>
                                    <td>
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Height', 'acf-rcwdupload' ); ?></label>
<?php 
                                        if($v == 4){
                                            $f_arr = array(	'type'	=>	'text',
                                                            'name'	=>	'fields['.$key.'][max_height]',
                                                            'value'	=>	(int)$field['max_height'],
                                                            'class'	=>  ACFRcwdCommon::get_var('name').'_group_maxdim'
                                                          );
                                            do_action( 'acf/create_field', $f_arr );
                                        }else{
                                            $this->parent->create_field($f_arr);
                                        }
?>
                                    </td>
                                    <td>
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Quality', 'acf-rcwdupload' ); ?></label>
<?php 
                                        if($field['max_quality'] == '')
                                            $field['max_quality'] = 90;
    
                                        if($v == 4){
                                            $f_arr = array(	'type'	=>	'text',
                                                            'name'	=>	'fields['.$key.'][max_quality]',
                                                            'value'	=>	(int)$field['max_quality'],
                                                            'class'	=>  ACFRcwdCommon::get_var('name').'_group_maxdim'
                                                          );
                                            do_action( 'acf/create_field', $f_arr );
                                        }else{
                                            $this->parent->create_field($f_arr);
                                        }
?>
                                    </td>                            
                                </tr>
                                <tr>
                                    <td colspan="3" scope="row"><p class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_description"><?php _e( '0 = proportional resizing.','acf-rcwdupload' ); ?></a></p></td>
                                </tr> 
                                <tr>
                                    <td colspan="3" scope="row">
                                        <label class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_label"><?php _e( 'Resize the images to clientside', 'acf-rcwdupload' ); ?></label>
<?php
                                            if($v == 4){
                                                $f_arr = array(	'type' 		=> 'select',
                                                                'name'    	=> 'fields['.$key.'][csresize]',
                                                                'value'  	=> $field['csresize'],
                                                                'layout'  	=> 'horizontal',
                                                                'class'		=>  ACFRcwdCommon::get_var('name').'_group_csresize',
                                                                'choices' 	=> array(	'N'	=> __( 'No', 'acf-rcwdupload' ),
                                                                                        'Y'	=> __( 'Yes', 'acf-rcwdupload' ) )
                                                              );
                                                do_action( 'acf/create_field', $f_arr );
                                            }else{
                                                $this->parent->create_field($f_arr);
                                            }
?>
                                    </td>                        
                                </tr>
                                <tr>
                                    <td colspan="3" scope="row"><p class="<?php echo ACFRcwdCommon::get_var('name') ?>_group_description"><?php _e( 'Images will be automatically resized before the upload step in order to reduce the weight.', 'acf-rcwdupload' ); ?></a></p></td>
                                </tr>                                                 
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr class="field_option field_option_<?php echo ACFRcwdCommon::get_var('name'); ?>">
                    <td class="label">
                        <label><?php _e( 'Upload the file after selection', 'acf-rcwdupload' ); ?></label>
                    </td>
                    <td>
<?php
						$f_arr = array(	'type' 		=> 'select',
										'name'    	=> 'fields['.$key.'][autoupload]',
										'value'  	=> $field['autoupload'],
										'layout'  	=> 'horizontal',
                                        'class'		=>  ACFRcwdCommon::get_var('name').'_group_autoupload',
										'choices' 	=> array(	'N'	=> __( 'No', 'acf-rcwdupload' ),
																'Y'	=> __( 'Yes', 'acf-rcwdupload' ) ) );
																
                        if($v == 4)
                            do_action( 'acf/create_field', $f_arr );
                        else
                            $this->parent->create_field($f_arr);
?>
                        
					</td>
				</tr>                   
<?php
            }else{
?>
                <tr class="field_option field_option_<?php echo ACFRcwdCommon::get_var('name'); ?>">
                    <td class="label">
                    </td>
                    <td>
<?php			
                        self::error_folder_permissions(false);	
?>            
                    </td>
                </tr>
<?php			
            }
				
		}else{

?>
            <tr class="field_option field_option_<?php echo ACFRcwdCommon::get_var('name'); ?>">
                <td class="label">
                </td>
                <td>
<?php			
                   self::error_not_authenticated(false);	
?>            
                </td>
            </tr>
<?php
		}
		
	}
	
	static function create_field($args){		

		if(ACFRcwdCommon::serial_is_valid()){

			global $pagenow;	
		
			$v				= $args['v'];
			$field			= $args['field'];
			$value			= '';
			$defaults		= ACFRcwdCommon::get_var('defaults');
			$rndfldr		= '';
			$field 			= array_merge( $defaults, $field );

			if( is_array($args['field']) and is_array($args['field']['value']) )
				$value = $field['value']['file'];
	
			$rename						= sanitize_file_name($field['rename']);
			$maxfiles 					= 9999;
			$max_file_size				= round( (int)wp_max_upload_size() / 1024 ).'kb'; // This string can be in the following formats 100b, 10kb, 10mb, 1gb
			$multiselection				= true;
			$overwrite					= false;
			$clientpreview 				= $field['clientpreview'];
			$clientpreview_max_width	= $field['clientpreview_max_width'];
			$clientpreview_max_height 	= $field['clientpreview_max_height'];
			$clientpreview_crop 		= $field['clientpreview_crop'];
			$preview 					= $field['preview'];
			$preview_max_width			= $field['preview_max_width'];
			$preview_max_height 		= $field['preview_max_height'];
			$preview_quality 			= $field['preview_quality'];			
			$preview_crop 				= $field['preview_crop'];			
			$autoupload 				= $field['autoupload'];		
			$sizelimit 					= $field['sizelimit'];
			$sizelimit_type				= $field['sizelimit_type'];			
			$collection 				= $field['collection'];		
			$collection_key 			= $field['collection_key'];		
			$is_hidden 					= !defined('RCWD_ACFUPLOAD_DEBUG') ? 'hidden' : 'text';	
			$fieldlinked 				= false;

			if( $preview == 'Y' and !class_exists('PhpThumb') )
				require_once 'includes'.DIRECTORY_SEPARATOR.'PHPThumb'.DIRECTORY_SEPARATOR.'ThumbLib.inc.php';		

			if((int)$sizelimit == 0)
				if($sizelimit_type == 'kb')
					$sizelimit = round( (int)wp_max_upload_size() / 1024 );
				else
					$sizelimit = round( (int)wp_max_upload_size() / 1024 / 1024 );
				
			if($sizelimit_type == 'kb')
				$max_file_size	= $sizelimit.'KB';
			else
				$max_file_size	= $sizelimit.'MB';
	
			$chunks = filter_var( apply_filters( 'acf_rcwdupload_chunks', $field['chunks'], $field ), FILTER_SANITIZE_STRING );

			// resize settings ___________________________________________
			
				$doresize 		= $field['resize'];
				$resize			= '';
				$resize_or		= '';
				$csresize		= '';
				$csresize_or	= '';
				
				if($doresize == 'Y'){
		
					// serverside resize settings
					
						$resize = array('quality' => (int)$field['max_quality']);
						
						if((int)$field['max_width'] > 0)
							$resize['width'] = $field['max_width'];
							
						if((int)$field['max_height'] > 0)
							$resize['height'] = $field['max_height'];	

						if($field['crop'] == 'Y')	
							$resize['crop'] = true;	
												
						$resize_or 	= $resize;	
						$resize 	= json_encode($resize);				
				
					// clientside resize settings
				
						$do_csresize	= $field['csresize'];
						$csresize		= '';
						
						if($do_csresize == 'Y'){
							
							$csresize = array('quality' => 100);
							
							if((int)$field['max_width'] > 0)
								$csresize['width'] = $field['max_width'];
								
							if((int)$field['max_height'] > 0)
								$csresize['height'] = $field['max_height'];		
								
							$csresize_or	= $csresize;	
							$csresize 		= json_encode($csresize);	
								
						}
		
				}
			
			// ___________________________________________________________
			
			$dofilters 	= $field['filters'];
			$upfilters 	= array();	

			if($dofilters == 'Y'){
								
				$filter_images 	= preg_replace('/\s+/', '', strtolower($field['filter_images']));
				$filter_docs 	= preg_replace('/\s+/', '', strtolower($field['filter_docs']));
				$filter_compr 	= preg_replace('/\s+/', '', strtolower($field['filter_compr']));
				$filter_others 	= preg_replace('/\s+/', '', strtolower($field['filter_others']));
				
				if( $filter_images != '')
					$upfilters[] = array( 'title' => __( 'Image files', 'acf-rcwdupload' ), 'extensions' => $filter_images );
					
				if( $filter_docs != '')
					$upfilters[] = array( 'title' => __( 'Document files', 'acf-rcwdupload' ), 'extensions' => $filter_docs );
					
				if( $filter_compr != '')
					$upfilters[] = array( 'title' => __( 'Compressed files', 'acf-rcwdupload' ), 'extensions' => $filter_compr );
					
				if( $filter_others != '')
					$upfilters[] = array( 'title' => __( 'Various files', 'acf-rcwdupload' ), 'extensions' => $filter_others );
										
				if(count($upfilters) > 0)
					$upfilters = json_encode($upfilters);
				else
					$upfilters = '';
					
			}
					
			if(is_admin()){
				
				if($pagenow == 'admin-ajax.php'){
					
					if($_POST['page_type'] == 'taxonomy'){
						
						$foldercode = 'tax_'.$_POST['option_name'];
				
					}elseif($_POST['page_type'] == 'user'){
				
						$foldercode = $_POST['option_name'];
						
					}
					
				}else{
					
					if( isset($_GET['page']) and substr( $_GET['page'], 0, 11 ) == 'acf-options' ){
												
						$foldercode = 'options_'.substr( $_GET['page'], 12 );

						$opfolder = substr( $_GET['page'], 12 );
	
						if(empty($opfolder))
							$foldercode = 'options_rt';
													
					}

					if( $pagenow == 'edit-tags.php' and isset($_GET['taxonomy']) ){
						
						$foldercode = 'tax_'.$_GET['taxonomy'];
			
						if( isset($_GET['action']) and $_GET['action'] == "edit" ){
							
							$_GET['tag_ID']	= filter_var( $_GET['tag_ID'], FILTER_SANITIZE_NUMBER_INT );
							$foldercode 	.= '_'.$_GET['tag_ID'];
							
						}
									
					}
								
					if( in_array( $pagenow, array( 'post.php', 'post-new.php', 'media.php' ) ) ){
						
						global $post;
						
						if(class_exists('SitePress')){
							
							$lang = rtrim( strip_tags( isset($_GET[ 'lang' ]) ? $_GET[ 'lang' ] : '' ), '/' );
							
							if(!empty($lang)){
								
								global $sitepress, $iclTranslationManagement;
								
								if (isset($iclTranslationManagement)){
								
									$fname = $field['_name'];
									
									if( !empty($fname) and isset($iclTranslationManagement->settings['custom_fields_translation'][$fname]) ){
										
										$trid 			= $sitepress->get_element_trid( $post->ID, 'post_'.get_post_type($post->ID) );
										$cft			= (int)$iclTranslationManagement->settings['custom_fields_translation'][$fname];
										$translations	= $iclTranslationManagement->get_element_translations( $trid, 'post_'.get_post_type($post->ID) );
										$post_lng		= $sitepress->get_language_for_element($post->ID, 'post_'.get_post_type($post->ID));
										$trid_lng		= $sitepress->get_language_for_element($trid, 'post_'.get_post_type($post->ID));
										$foldercode 	= 'post_'.$trid;
										
										if( $cft == 1 and !empty($trid_lng) and $post_lng != $trid_lng){
											
											$field 			= get_field_object( $fname, $trid );
											$fieldlinked	= true;
											
										}else
											$foldercode = 'post_'.$post->ID;
									
									}else
										$foldercode = 'post_'.$post->ID;

								}else
									$foldercode = 'post_'.$post->ID;
								
							}else
								$foldercode = 'post_'.$post->ID;
						
						}else
							$foldercode = 'post_'.$post->ID;
							
					}
					
				}

				$foldercode_temp = $foldercode;
			
			}else{
			
				if( is_page() or is_single() ){
					
					global $post, $rcwd_custom_post_id, $acf_rcwdupload_5_post_id;
					
					$pst = 'post';

					if( function_exists('acf_get_setting') and version_compare( acf_get_setting('version'), "5", ">=" ) ){

						if( !empty($acf_rcwdupload_5_post_id) and (int)$acf_rcwdupload_5_post_id > 0)
							$foldercode = 'post_'.(int)$acf_rcwdupload_5_post_id;
						else
							$foldercode = 'post_'.$post->ID;

						$foldercode_temp = 'post_'.$post->ID;
												
					}else{

						if( !empty($rcwd_custom_post_id) and (int)$rcwd_custom_post_id > 0)
							$foldercode = 'post_'.(int)$rcwd_custom_post_id;
						else
							$foldercode = 'post_'.$post->ID;

						$foldercode_temp = 'post_'.$post->ID;
												
					}
					
				}
				
			}

			if($value != '')
				$nf_txt = __( 'To change with:', 'acf-rcwdupload' );
			else
				$nf_txt = __( 'File:', 'acf-rcwdupload' );
				
			$frc = (bool)apply_filters( 'acf_rcwdupload_thumb_force_http', false, $field );

			if($frc)
				$frc = 1;
			else
				$frc = 0;

			$upload_info 	= ACFRcwdCommon::upload_info( $field['key'], $foldercode, 0, $rndfldr );
			$path_temp		= $upload_info['path_temp'];
			$path			= $upload_info['path'];
			$url_temp		= $upload_info['url_temp'];
			$url			= $upload_info['url'];
			$rndfldr		= $upload_info['rndfldr'];
			$randomcode		= ACFRcwdCommon::randomcode(array( 'length' => 3, 'uppercase' => false ));
			$datainfo 		= strrev($randomcode).base64_encode(json_encode(array(
			
				'temp_path'		=> $path_temp,
				'path_temp'		=> $path_temp,
				'overw' 		=> $overwrite,
				'fkey' 			=> $field['key'],
				'resize_qs'		=> $resize_or,
				'randomcode'	=> $randomcode
				
			)));
			
					
													
?>
			<div id="<?php echo $field['id']?>-acf_rcwdupload" class="acf_rcwdupload">
			 
				<div id="<?php echo $field['id']?>-container" class="rcwdplupload-container" data-fid="<?php echo $field['id']?>" data-fkey="<?php echo $field['key'] ?>" data-maxf="<?php echo $maxfiles ?>" data-clientpreview='<?php echo $clientpreview ?>' data-clientpreview_max_width='<?php echo $clientpreview_max_width ?>' data-clientpreview_max_height='<?php echo $clientpreview_max_height ?>' data-clientpreview_crop='<?php echo $clientpreview_crop ?>' data-preview='<?php echo $preview ?>' data-preview_max_width='<?php echo $preview_max_width ?>' data-preview_max_height='<?php echo $preview_max_height ?>' data-preview_crop='<?php echo $preview_crop ?>' data-upflrs='<?php echo $upfilters ?>' data-maxfsize="<?php echo $max_file_size ?>" data-overw="<?php echo $overwrite ?>" data-resize='<?php echo $resize ?>' data-csresize='<?php echo $csresize ?>' data-msel='<?php echo $multiselection ?>' data-nftxt="<?php echo $nf_txt ?>" data-autoupload='<?php echo $autoupload ?>' data-collection='<?php echo $collection ?>' data-folder='<?php echo $foldercode_temp ?>' data-rename='<?php echo $rename ?>' data-chunks='<?php echo $chunks ?>' ddata-frc='<?php echo $frc ?>' data-info='<?php echo $datainfo ?>'>
<?php
					if(!$fieldlinked){
?>                
						<span id="<?php echo $field['id']?>-ddbox" class="rcwdplupload-ddbox"><span class="rcwdplupload-ddbox-inner"><?php _e( 'Drag your file here', 'acf-rcwdupload' ) ?></span></span>
<?php
					}else
						echo '<p>'.__( 'NOTE: You can change this file only in the original post.', 'acf-rcwdupload' ).'</p>';	
						
					if( ( $value != '' and file_exists($path.$value) ) or $field['collection'] == 'Y' ){
						
						if( ( $value != '' and file_exists($path.$value) ) ){
							
							$file 			= $url.$value;
							$fsize			= size_format(filesize($path.$value));
							$flnm_pathinfo 	= pathinfo($path.$value);
							$flnm_ext		= strtolower($flnm_pathinfo['extension']);	
						
						}else{
							
							$file 		= $url;
							$fsize		= '';							
							$value		= '';	
							$flnm_ext	= '';						
							
						}
?>
						<div id="<?php echo $field['id']?>-current" class="rcwdplupload-current" <?php if(empty($value)) echo 'style="display:none"'?> >
<?php
							if(!$fieldlinked){
?>                        
                            	<a id="<?php echo $field['id']?>-removecf" class="rcwdplupload-removecf <?php echo( ($v == 5) ? 'acf-icon -cancel dark' : 'acf-button-delete ir' ) ?>" href="#"><?php echo( ($v == 5) ? '' : __( 'Remove', 'acf-rcwdupload' ) ) ?></a>              
<?php
							}
?>                                          
							<div id="<?php echo $field['id']?>-current-file" class="rcwdplupload-current-file"><strong><?php _e( 'Current file:', 'acf-rcwdupload' ) ?></strong> <a href="<?php echo $file ?>" target="_blank"><?php echo $value; ?></a></div>
							<div id="<?php echo $field['id']?>-current-size" class="rcwdplupload-current-size"><strong><?php _e( 'Size:', 'acf-rcwdupload' ) ?></strong> <span><?php echo $fsize ?></span></div>
<?php
							if( $preview == 'Y' and in_array( strtolower($flnm_ext), array( 'jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'svg' ) ) ){
								
								if(strtolower($flnm_ext) == 'svg')
									$imgtag = '<img src="'.$file.'" alt="" style="max-width:100px">';
								else
									$imgtag = '<img src="'.AcfRcwdCommon::get_dir(__FILE__).'includes/PHPThumb/thumb.php?f='.AcfRcwdCommon::randomcode(array( 'length' => 3, 'uppercase' => false )).base64_encode($file).'&w='.$preview_max_width.'&h='.$preview_max_height.'&c='.$preview_crop.'&t=0&frc='.$frc.'" alt="">';
?>                                    
								<div style="margin:5px 0 0 0" class="acfrcwdplupload-current-preview">
									<?php echo $imgtag ?>
								</div>
<?php
							}
?>                            
						</div>
<?php
					}
					
					if($fieldlinked){
						
						echo '<br>';
							
						return;
						
					}
?>  
					<div id="<?php echo $field['id']?>-temp" class="rcwdplupload-temp">
						<a id="<?php echo $field['id']?>-removetf" class="rcwdplupload-removetf <?php echo( ($v == 5) ? 'acf-icon -cancel dark' : 'acf-button-delete ir' ) ?>" href="#"><?php echo( ($v == 5) ? '' : __( 'Remove', 'acf-rcwdupload' ) ) ?></a>
						<div id="<?php echo $field['id']?>-temp-file" class="rcwdplupload-temp-file"><strong><span class="rcwdplupload-temp-file-txt"></span> <a href="<?php echo $url_temp ?>" target="_blank"></a></strong></div>
						<div id="<?php echo $field['id']?>-temp-size" class="rcwdplupload-temp-size"><strong><?php _e( 'Size:', 'acf-rcwdupload' ) ?></strong> <span></span></div>
					</div>
<?php
					if($field['collection'] == 'Y'){
						
						$thefolder = isset($field['value']['folder']) ? $field['value']['folder'] : $foldercode;
?>                    
                        <div id="rcwdacflupload-<?php echo $field['id']?>-tabs" class="rcwdacflupload-tabs">
                            <ul>
                                <li><a href="#rcwdacflupload-<?php echo $field['id']?>-tab-1"><?php _e( 'Upload a file', 'acf-rcwdupload' ) ?></a></li>
                                <li><a href="#rcwdacflupload-<?php echo $field['id']?>-tab-2"><?php _e( 'Uploaded files', 'acf-rcwdupload' ) ?></a></li>
                            </ul>   
    
							<div id="rcwdacflupload-<?php echo $field['id']?>-tab-1">
								<input class="acf-file-folder" type="<?php echo $is_hidden ?>" id="<?php echo $field['id']; ?>-folder" name="<?php echo $field['key']?>-folder[]" value="<?php echo $thefolder; ?>" autocomplete=off />
<?php
					}
?>     
					<input class="acf-file-value" type="<?php echo $is_hidden ?>" id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo $value; ?>" autocomplete=off />
					<!--<input class="rcwdplupload-cf" type="<?php echo $is_hidden ?>"" name="<?php echo $field['key']?>-cf[]" value="<?php echo $value; ?>" />-->                      
					
					<div id="<?php echo $field['id']?>-filelist" class="rcwdplupload-filelist">
						<div class="rcwdplupload-filewrapper">
							<span class="rcwdplupload-filename">...</span><span id="<?php echo $field['id']?>-remove" class="rcwdplupload-remove"></span>
						</div>
					</div>                
					<a id="<?php echo $field['id']?>-pickfiles" class="button rcwdplupload-pickfiles" href="#"><?php _e( 'Browse', 'acf-rcwdupload' ) ?></a>
					<a id="<?php echo $field['id']?>-uploadfiles" class="button button-primary rcwdplupload-uploadfiles" href="#"><?php _e( 'Upload', 'acf-rcwdupload' ) ?></a>
					<div class="rcwdplupload-clear"></div>
                    <span id="clientpreview-<?php echo $field['id'] ?>" class="rcwdplupload-clientpreview"></span>
					<span id="<?php echo $field['id']?>-filesize" class="rcwdplupload-filesize"><strong><?php _e( 'Size:', 'acf-rcwdupload' ) ?></strong><span></span></span>
<?php
/*					if($field['collection'] == 'Y'){
?>
                            </div>
                            <div id="rcwdacflupload-<?php echo $field['id']?>-tab-2">
                                <div id="rcwdacflupload-<?php echo $field['id']?>-folder-files-wrapper" class="rcwdacflupload-folder-files-wrapper">
<?php
						$fieldfolder 		= ACF_RCWDUPLOAD_UP_DIR.DIRECTORY_SEPARATOR.$field['key'].DIRECTORY_SEPARATOR;
						$fieldsubfolders	= glob( $fieldfolder.'/*', GLOB_ONLYDIR );
						$fimages			= array();
						$fothers			= array();
						
						if(count($fieldsubfolders) > 0){
							
							foreach($fieldsubfolders as $fieldsubfolder){
								
								$folder_images = glob( $fieldsubfolder.DIRECTORY_SEPARATOR.'*.{jpg,png,gif}', GLOB_BRACE );
								
								foreach($folder_images as $filename){
									
									$arrk			= sanitize_title(basename(basename($filename)));
									$selected		= ( $value != '' and $value == basename($filename) and basename($fieldsubfolder) == $thefolder) ? 'rcwdacflupload-files-selected' : '';
									$fsize			= size_format(filesize($filename));
									$fimages[$arrk]	= array( basename($filename), basename($fieldsubfolder), ACF_RCWDUPLOAD_UP_URL.'/'.$field['key'].'/'.basename($fieldsubfolder).'/'.basename($filename), $selected, $fsize );
									
								}
						
								$folder_others = glob( $fieldsubfolder.DIRECTORY_SEPARATOR.'*' );
								
								foreach($folder_others as $filename){
									
									if(!in_array( rcwd_get_file_extension($filename), array( 'jpg', 'png', 'gif' ) )){
							
										$arrk			= sanitize_title(basename(basename($filename)));
										$selected		= ( $value != '' and $value == basename($filename) and basename($fieldsubfolder) == $thefolder) ? 'rcwdacflupload-files-selected' : '';
										$fsize			= size_format(filesize($filename));
										$fothers[$arrk]	= array( basename($filename), basename($fieldsubfolder), ACF_RCWDUPLOAD_UP_URL.'/'.$field['key'].'/'.basename($fieldsubfolder).'/'.basename($filename), $selected, $fsize );
									
									}
									
								}
										
							}
							
							if(count($fimages))
								 foreach($fimages as $filename)	
									echo '<div class="rcwdacflupload-folder-files rcwdacflupload-folder-images '.$filename[3].'" data-filefolder="'.$filename[1].'" data-filename="'.$filename[0].'" data-size="'.$filename[4].'"><div><img src="'.$filename[2].'" alt=""  /></div><span>x</span></div>';

							if(count($fothers))
								 foreach($fothers as $filename)	
									echo '<div class="rcwdacflupload-folder-files rcwdacflupload-folder-others '.$filename[3].'" data-filefolder="'.$filename[1].'" data-filename="'.$filename[0].'" data-size="'.$filename[4].'"><div>'.$filename[0].'</div><span>x</span></div>';
									
						}
?>  
                                    <div class="rcwdplupload-clear"></div>
                                </div>
                            
                            </div>				   
                        </div>
<?php
					}*/
?>                          				   
				</div>  
			</div>
<?php
		
		}
		
	}

	static function update_value($args){

		if(ACFRcwdCommon::serial_is_valid()){

			global $pagenow;	

			$value 			= trim($args['value']);
			$post_id 		= $args['post_id'];
			$field 			= $args['field'];
			$rptfieldkey 	= '';
			$ftodelete		= false;
			$defaults 		= ACFRcwdCommon::get_var('defaults');
			$field 			= array_merge( $defaults, $field );	
					
			if( isset($_POST[$field['key'].'_rptfieldkey']) and $_POST[$field['key'].'_rptfieldkey'] != '' )
				$rptfieldkey = $_POST[$field['key'].'_rptfieldkey'];		
	
			if(is_admin()){

				if( isset($_GET['page']) and substr( $_GET['page'], 0, 11 ) == 'acf-options' ){
					
					$foldercode = $foldercode_temp = 'options_'.substr( $_GET['page'], 12 );
					$opfolder 	= substr( $_GET['page'], 12 );
	
					if(empty($opfolder))
						$foldercode = $foldercode_temp = $foldercode = 'options_rt';
												
				}
		
				if( $pagenow == 'edit-tags.php' ){
					
					$foldercode = $foldercode_temp = 'tax_'.$_POST['taxonomy'].'_'.$_POST['tag_ID'];
								
				}elseif(in_array( $pagenow, array( 'profile.php', 'user-new.php', 'user-edit.php' ) )){
					
					global $user_id;
					
					$foldercode = $foldercode_temp = 'user_'.$_POST['user_id'];
					
				}elseif( in_array( $pagenow, array( 'post.php', 'post-new.php', 'media.php' ) ) ){
					
					global $post;
					
					$pst 		= 'post';
					$foldercode = $foldercode_temp = 'post_'.$post->ID;	
						
				}
			
			}else{
				
				if( is_page() or is_single() ){

					global $post, $rcwd_custom_post_id, $acf_rcwdupload_5_post_id;

					$pst = 'post';

					if( !empty($rcwd_custom_post_id) and (int)$rcwd_custom_post_id > 0)
						$post_id = (int)$rcwd_custom_post_id;
					else
						$post_id = $post->ID;

					if( function_exists('acf_get_setting') and version_compare( acf_get_setting('version'), "5", ">=" ) ){

						$foldercode = 'post_'.$args['post_id'];
						$post_id	= $args['post_id'];
						
						if( !empty($acf_rcwdupload_5_post_id) and (int)$acf_rcwdupload_5_post_id > 0){
							
							$foldercode_temp = 'post_'.$acf_rcwdupload_5_post_id;
						
						}else{

							$foldercode_temp = 'post_'.$post->ID;
														
						}
						
					}else{
				
						if( isset($_POST) and isset($_POST['post_id']) and $_POST['post_id'] == 'new'){ // COMPATIBILITY WITH ACF FRONTEND FORM
	
							$foldercode 		= 'post_'.$post_id;
							$foldercode_temp 	= 'post_'.$post_id;
							
						}else
							$foldercode_temp = 'post_'.$post_id;
						
					}

				}
				
			}

			if(!empty($value)){

				$upload_info	= ACFRcwdCommon::upload_info( $field['key'], $foldercode );
				$fnt			= $upload_info['path_temp'].$value;
				$f				= $upload_info['path'];
				$fn				= $f.$value;
				$ok				= false;
				
				if(file_exists($fnt)){
					
					$ok = true;
					
					wp_mkdir_p($f);
					
					rename( $fnt, $fn ); // move file from temp folder to field folder
						
					if($rptfieldkey != '')
						$ftodelete = self::ftd_from_repeater( $rptfieldkey, $field );
					else{
						
						if(is_numeric($post_id)){

							$ftodelete = get_post_meta( $post_id, $field['name'], true );

							if( $ftodelete and $ftodelete == $value )
								$ftodelete = false;

						}elseif( strpos($post_id, 'user_') !== false ){
							
							$post_id 	= str_replace('user_', '', $post_id);
							$ftodelete 	= get_user_meta( $post_id, $field['name'], true );
							
							if( $ftodelete and $ftodelete == $value )
								$ftodelete = false;
									
						}else{
							$ftodelete = get_option($post_id.'_'.$field['name']);
							
							if( $ftodelete and $ftodelete == $value )
								$ftodelete = false;

						}					
					}

					do_action( 'acf_rcwdupload_file_uploaded_after_update', $fn, $field, $post_id, $current );
					
				}elseif(!file_exists($fn))				
					$value = '';
				
				if( $ftodelete and $field['collection'] == 'Y' )
					$ftodelete = false;
								
			}else{
				
				if($rptfieldkey != '')
					$ftodelete = self::ftd_from_repeater( $rptfieldkey, $field );
				else{
					
					if( is_numeric($post_id) )
						$ftodelete = get_post_meta( $post_id, $field['name'], true );
					
					elseif( strpos($post_id, 'user_') !== false ){
						
						$post_id 	= str_replace('user_', '', $post_id);
						$ftodelete 	= get_user_meta( $post_id, $field['name'], true );	
								
					}else
						$ftodelete = get_option($post_id.'_'.$field['name']);

				}
				
				do_action( 'acf_rcwdupload_file_deleted_after_update', $ftodelete, $field, $post_id );

			}

			if( is_array($ftodelete) and isset($ftodelete['file']) )
				$ftodelete = $ftodelete['file'];

			if( $ftodelete and ( ( $ok === true and $ftodelete != $value ) or $ok === false ) )
				if(file_exists($upload_info['path'].$ftodelete))
					unlink($upload_info['path'].$ftodelete);
	
			if($value != '')
				$value = array( 'file' => $value, 'folder' => $foldercode );
					
		}
		
		return $value;
		
	}

	static function ftd_from_repeater( $rptfieldkey, $field ){
		
		$fid_rep		= str_replace( 'acf-field-', '', $field['id'] ); 
		$fname_split	= explode( '_rcwd_bg_image', $field['name'] ); 
		$fname_split	= explode( '_', $fname_split[0] ); 
		$forder			= end($fname_split);
		$fields			= $_POST['fields'][$rptfieldkey];	
		$count 			= 0;
		$ftodelete		= false;
		$cf 			= $_POST[$field['key'].'-cf'];
		
		foreach( $fields as $key => $value_ ){
			if($count == $forder){
				if($value_[$field['key']] != $cf[$count]){
					$ftodelete = $cf[$count];
					foreach( $fields as $key => $value_ ){
						if($value_[$field['key']] == $cf[$count]){
							$ftodelete = false;	
							break;
						}
					}
					break;
				}
			}
			$count++;
		}	
		return $ftodelete;	
			
	}

	static function format_value_for_api($args){
		
		if(ACFRcwdCommon::serial_is_valid()){
		
			$post_id 		= $args['post_id'];
			$field 			= $args['field'];
			$value 			= $args['value'];
			
			if(is_array($value)){
				
				$file 			= $value['file'];	
				$upload_info	= ACFRcwdCommon::upload_info( $field['key'], @$value['folder'] );

				$value 			= array(
				
					'file'	=> $file,
					'path'	=> $upload_info['path'].$file,
					'url' 	=> $upload_info['url'].$file,
					'size' 	=> size_format(filesize($upload_info['path'].$file)),
					'purl' 	=> array(
					
						'inline' => $upload_info['purl']['inline'].$file,
						'attach' => $upload_info['purl']['attach'].$file
										
					));	
	
				$filext = strtolower(substr( strrchr( $file, "." ), 1 ));
				
				switch($filext){
					
					case 'gif'	:
					case 'png'	:
					case 'jpeg'	:
					case 'jpg'	:
					
						list( $width, $height, $type, $attr ) 	= getimagesize($upload_info['path'].$file);
						$value['width'] 						= $width;
						$value['height'] 						= $height;
						
				}
																	
				return $value;
				
			}

		}
			
		return false;
					
	}
	
}

$acf_rcwdup_m = new acf_rcwdup_m();
?>