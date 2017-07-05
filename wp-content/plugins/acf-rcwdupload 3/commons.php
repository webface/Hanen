<?php
class ACFRcwdCommon{
	
	const ACF_RCWDUPLOAD_ITEM_ID = '5078456';

	static function randomcode($args = array()){
		
		$length		= ( isset($args['length']) and (int)$args['length'] > 0 ) ? (int)$args['length'] : 8;
		$uppercase	= isset($args['uppercase']) ? (bool)$args['uppercase'] 	: true;
		$lowercase	= isset($args['lowercase']) ? (bool)$args['lowercase'] 	: true;
		$number		= isset($args['number']) 	? (bool)$args['number'] 	: true;
		$newpass 	= '';
		
		if( !$uppercase and !$lowercase and !$number )
			$uppercase = $lowercase = $number = true;
			
		for ( $k = 1; $k <= $length; $k++ ){

			if ($k % 3){
				
				if( $uppercase === true or $lowercase === true ){
					
					if( $uppercase === true and $lowercase === true ){
						
						if ( rand( 0, 200 ) <= 100 )
							$newpass .= chr(rand ( 65, 90 ));	// Nella tabella ASCII da 65 a 90 ci sono le lettere dell'alfabeto Maiuscole
						else
							$newpass .= chr(rand( 97, 122 ));	// Nella tabella ASCII da 97 a 122 ci sono le lettere dell'alfabeto Minuscole
							
					}elseif($uppercase === true)
						$newpass .= chr(rand (65,90));
					else
						$newpass .= chr(rand(97,122));

				}else
					if($number === true) $newpass .= rand(0,9);

			}else
				if($number === true)
					$newpass .= rand(0,9);

		}
		
		return $newpass;
		
	}
	
	static function upload_info( $field_key, $foldercode = '', $userid = 0 , $rndfldr_ = '' ){
		
		global $rndfldr;
		
		if($rndfldr_)
			$rndfldr = $rndfldr_;		
		
		$rndfldr 	= !empty($rndfldr) ? $rndfldr : ( isset($_REQUEST["rcwdupload_rndfldr"]) ? $_REQUEST["rcwdupload_rndfldr"] : ACFRcwdCommon::randomcode(array('length' => 15)) );
		$path_temp	= ACF_RCWDUPLOAD_UP_TEMP_DIR.RCWD_DS.$field_key; //.RCWD_DS.$rndfldr;
		$path 		= ACF_RCWDUPLOAD_UP_DIR.RCWD_DS.$field_key;
		$url_temp	= ACF_RCWDUPLOAD_UP_TEMP_URL.'/'.$field_key; //.'/'.$rndfldr;
		$url		= ACF_RCWDUPLOAD_UP_URL.'/'.$field_key;
		$blogurl	= get_bloginfo('url');
		$pfolder	= $foldercode;
		$args		= array(
			
			'field'		=> get_field_object($field_key, false, false, false ),
			'type'		=> '',
			'post_type'	=> ''
			
		);

		if(class_exists('SitePress')){
			
			$pu 		= parse_url($blogurl);
			$blogurl	= $pu['scheme'].'://'.$pu['host'];

		}

		if(substr( $blogurl, -1 ) != '/')
			$blogurl .= '/';
											
		$purl = $blogurl.'acfrcwdup/';

		if(substr( $foldercode, 0, 8 ) == 'options_'){

			$opfolder = substr( $foldercode, 8 );

			if(empty($opfolder))
				$foldercode = 'options_rt';
								
			$pfolder 		= 'o/'.substr( $foldercode, 8 );
			$args['type']	= 'option_page';
			
		}elseif(substr( $foldercode, 0, 4 ) == 'tax_'){
			
			$pfolder 		= 't/'.substr( $foldercode, 4 );
			$args['type']	= 'tax';
			
		}elseif(substr( $foldercode, 0, 5 ) == 'user_'){
			
			$pfolder 		= 'u/'.substr( $foldercode, 5 );
			$args['type']	= 'user';
		
		}elseif(substr( $foldercode, 0, 5 ) == 'post_'){
			
			$pfolder 			= 'p/'.substr( $foldercode, 5 );
			$args['type']		= 'post';
			$args['post_type']	= get_post_type();
			
		}

		$inline		= $purl.'0/'.str_replace( 'field_', rand( 10009, 99999 ).'/', $field_key );
		$attach		= $purl.'1/'.str_replace( 'field_', rand( 10009, 99999 ).'/', $field_key );		
							
		if(!empty($pfolder)){
			
			$path_temp	.= RCWD_DS.$foldercode;
			$path		.= RCWD_DS.$foldercode;
			$url		.= '/'.$foldercode;
			$inline 	.= '/'.$pfolder;
			$attach 	.= '/'.$pfolder;
			
		}

		wp_mkdir_p($path_temp);
		wp_mkdir_p($path);				

		if(!file_exists(ACF_RCWDUPLOAD_UP_TEMP_DIR.RCWD_DS."index.html"))
			self::add_file_index(ACF_RCWDUPLOAD_UP_TEMP_DIR);
	
		if(!file_exists($path_temp.RCWD_DS."index.html"))
			self::add_file_index($path_temp);
			
		if(!file_exists(ACF_RCWDUPLOAD_UP_DIR.RCWD_DS."index.html"))
			self::add_file_index(ACF_RCWDUPLOAD_UP_DIR);
			
		if(!file_exists($path.RCWD_DS."index.html"))
			self::add_file_index($path);

		$path = apply_filters( "acf_rcwdupload_path", $path, $field_key, $foldercode, $args );
		$url  = apply_filters( "acf_rcwdupload_url", $url, $field_key, $foldercode, $args );

		if(substr( $path_temp, -1 ) != RCWD_DS)
			$path_temp .= RCWD_DS;
							
		if(substr( $path, -1 ) != RCWD_DS)
			$path .= RCWD_DS;
			
		if(substr( $url_temp, -1 ) != '/')
			$url_temp .= '/';
							
		if(substr( $url, -1 ) != '/')
			$url .= '/';			

		if(substr( $inline, -1 ) != '/')
			$inline .= '/';

		if(substr( $attach, -1 ) != '/')
			$attach .= '/';					

		$output = array( 'rndfldr' => $rndfldr, 'path_temp' => $path_temp, 'path' => $path, 'url_temp' => $url_temp, 'url' => $url, 'purl' => array( 'inline' => $inline, 'attach' => $attach ) );
		
		return $output;
						
	}

    static function add_file_index($dir){
		
        if(!is_dir($dir))
            return;

        if(!($od = opendir($dir)))
            return;

        set_error_handler(create_function("", "return 0;"), E_ALL);

        if($f = fopen( $dir."/index.html", 'w' ))
            fclose($f);

        restore_error_handler();

        while((false !== $file = readdir($od)))
           if(is_dir("$dir/$file") and $file != '.' and $file != '..' )
               self::add_file_index("$dir/$file");

        closedir($od);
		
    }
		
	static function get_var($var){
		
		switch($var){
			case 'version'	:
				return '1.1.6';
				break;
			case 'name' 	: 
				return 'rcwdupload';
				break;	
			case 'label' 	: 
				return __( 'Rcwd Upload', 'acf-rcwdupload' );
				break;
			case 'category' : 
				return __( 'Content', 'acf-rcwdupload' );
				break;
			case 'domain' 	: 
				return 'acf-rcwdupload';
			case 'defaults':
				return array(
			
					'post_id'					=> 0,
					'rename'					=> '',
					'clientpreview'				=> 'N',
					'clientpreview_max_width'	=> 150,
					'clientpreview_max_height'	=> 150,					
					'clientpreview_crop'    	=> 'N',
					'preview'	 				=> 'N',
					'preview_max_width'			=> 150,
					'preview_max_height'		=> 150,
					'preview_quality'			=> 90,
					'preview_crop'				=> 'N',					
					'collection'				=> 'N',
					'collection_key'			=> '',
					'filters'    				=> 'Y',
					'filter_images'				=> 'jpg,gif,png',
					'filter_docs'   			=> 'pdf,doc,docx,xml,txt,xls,xlsx',
					'filter_compr'				=> 'zip,rar,ace',
					'filter_others'				=> '',
					'resize'					=> 'N',
					'resize_max_width'			=> '0',
					'resize_max_height'			=> '0',
					'resize_max_quality'		=> '90',					
					'resize_crop'				=> 'N',
					'max_width'					=> '0',
					'max_height'				=> '0',
					'max_quality'				=> '90',
					'csresize'					=> 'N',
					'autoupload'				=> 'N',
					'chunks'					=> 0,
					'chunks_max_retries'		=> 3,
					'sizelimit'					=> '',
					'sizelimit_type'			=> 'kb'					
									
				);
														
		}
		return false;
				
	}

    static function get_dir($file){
		
        $dir 	= trailingslashit(dirname($file));
        $count 	= 0;
        
        // sanitize for Win32 installs
        $dir = str_replace('\\' ,'/', $dir); 
        
        // if file is in plugins folder
        $wp_plugin_dir 	= str_replace('\\' ,'/', WP_PLUGIN_DIR); 
        $dir 			= str_replace($wp_plugin_dir, plugins_url(), $dir, $count);
        
        if( $count < 1 ){
	        // if file is in wp-content folder
	        $wp_content_dir = str_replace('\\' ,'/', WP_CONTENT_DIR); 
	        $dir 			= str_replace($wp_content_dir, content_url(), $dir, $count);
        }
        
        if( $count < 1 ){
	        // if file is in ??? folder
	        $wp_dir = str_replace('\\' ,'/', ABSPATH); 
	        $dir 	= str_replace($wp_dir, site_url('/'), $dir);
        }

        return $dir;
		
    }
	
	function generate_rewrite_rules($wp_rewrite){
		
		$new_non_wp_rules 			= array('acfrcwdup/([^/\.]*)/[^/\.]+/([^/\.]+)/([^/]+)/([^/]+)/([^/]+)(\.[^.]*$|$)?$' => '/?mode=$1&acfrcwduplddwnlod=$2&type=$3&value=$4&file=$5' );
		$wp_rewrite->non_wp_rules 	= $new_non_wp_rules + $wp_rewrite->non_wp_rules;
		
	}

	static function flush_rules(){
		
		global $wp_rewrite;

		$rulexists = get_option('acf_rcwdupload_rulexists');
		
		if(!$rulexists){
			
			$wp_rewrite->flush_rules();
			
			$rulexists = update_option( 'acf_rcwdupload_rulexists', 1 );	
				
		}
				
	}

	static function error_folder_permissions( $isnotice = true, $return = false ){
		
		$msg = sprintf( __( 'Sorry, %1s for ACF cannot create the following folder under wp-content: %2s. Please check your folder permissions or the add-on will not save your files. Thanks...', 'acf-rcwdupload' ), self::get_var('label'), '<strong>acfrcwduploads</strong>' );
		if( is_string($isnotice) and $isnotice == '') 
			$isnotice = true;
		if($isnotice){
			$msg = '<div id="message" class="error"><p>'.$msg.'</p></div>';
		}
		if(!$return)
			echo $msg;
		else
			return $msg;
			
	}	

	static function RemoveEmptySubFolders($path){
		
		$empty = true;
		
		foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
			$empty &= is_dir($file) and self::RemoveEmptySubFolders($file);
			
		return $empty and @rmdir($path);
	  
	}
	
	static function is_dir_empty($dir){
	
		if (!is_readable($dir))
			return NULL; 
			
		if(is_dir($dir)){
			
			$handle = @opendir($dir);
			
			while( false !== ($entry = readdir($handle)) )
				if ($entry != "." and $entry != ".." )
					return false;
				
		}
				
		return true;
	
	}
		
	static function remove_file($dir){ 
	
		global $acfrcwd2brem_dirs, $acfrcwd2brem_files;
	
		if(is_dir($dir)){ 
		
			$objects = @scandir($dir); 
			
			foreach($objects as $object){ 
			
				if( $object != "." and $object != ".." ){ 
					
					$sdfhko = $dir."/".$object;
					
					if(!empty($sdfhko)){
										
						if (is_dir($dir."/".$object))
							self::remove_file($dir."/".$object);
						else{
							
							if(file_exists($dir."/".$object)){
								
								$filemtime = filemtime($dir."/".$object);
								
								if( time() - $filemtime > 86400 )
									$acfrcwd2brem_files[] = $dir."/".$object;
									//@unlink($dir."/".$object); 
								
							}
								
						}
					
					}
					
				} 
				
			}
			
			if( is_dir($dir) and self::is_dir_empty($dir) )
				$acfrcwd2brem_dirs[] = $dir;
				//@rmdir($dir); 
			
		} 
	
	}
	
	static function clean_temp(){

		global $acfrcwd2brem_dirs, $acfrcwd2brem_files;

		$clean = get_option('acf_rcwdupload_clean_temp');
		
		if( !$clean or time() - $clean > 86400 ){
			
			if (is_dir(ACF_RCWDUPLOAD_UP_TEMP_DIR)){

				$folders = scandir(ACF_RCWDUPLOAD_UP_TEMP_DIR);
			
				foreach($folders as $folder)
					if( !empty($folder) and $folder!= '.' and $folder != '..' and $folder != 'index.html' and file_exists(ACF_RCWDUPLOAD_UP_TEMP_DIR.RCWD_DS.$folder) )					
						self::remove_file(ACF_RCWDUPLOAD_UP_TEMP_DIR.RCWD_DS.$folder);
			
			}
			
			$clean = update_option( 'acf_rcwdupload_clean_temp', time() );	

			if(!empty($acfrcwd2brem_files))
				foreach($acfrcwd2brem_files as $file)
					if(file_exists($file))
						@unlink($file);
						
			if(!empty($acfrcwd2brem_dirs))
				foreach($acfrcwd2brem_dirs as $dir)
					self::RemoveEmptySubFolders($dir);
								
		}
		
	}

	static function handle_upload_request(){
		
		if (isset($_GET['acfrcwdupload'])){
			
			$upload_file =  implode( DIRECTORY_SEPARATOR, array( trailingslashit(dirname(__FILE__)), 'upload.php' ) );

			if(!empty($upload_file))
				require_once($upload_file);
			else
				die('Something gone wrong :(');	
							
			die();
			//throw new acf_rcwd_clean_exit();
			
		}
		
		if (isset($_GET['acfrcwduplddwnlod'])){
			
			require_once(implode( DIRECTORY_SEPARATOR, array( trailingslashit(dirname(__FILE__)), 'download.php' ) ));
			
			die();
			//throw new acf_rcwd_clean_exit();
			
		}
		
	}	

	static function serial_is_valid($remotecheck = false){
	
		$check 				= get_option( 'acf_rcwdupload_verifypurchase' );
		$check['item_id'] 	= self::ACF_RCWDUPLOAD_ITEM_ID;
		$docheck 			= true;

		if( isset($check['username']) and isset($check['purchased']) and $check['purchased'] === true and isset($check['lastcheck']) )
			$docheck = false;
		elseif(!$remotecheck)
			return false;		

		if($docheck){
			
			$check['website'] = $_SERVER['HTTP_HOST'];
						
			$checkit 		= self::checkit();
			$remote_post	= $checkit[0];
			$state			= $checkit[1];

			if($state)
				$remote_post = unserialize($remote_post['body']);
			else
				if( !isset($check['purchased']) or !$check['purchased']  ){		
				
					update_option( 'acf_rcwdupload_verifypurchase', $check );
					
					return false;
					
				}
				
			if( ( $remote_post !== false or $remote_post == '' ) and ( !isset($remote_post['verify-purchase']) or !isset($remote_post['verify-purchase']['item_id']) or $remote_post['verify-purchase']['item_id'] != $check['item_id'] or !isset($remote_post['verify-purchase']['buyer']) or strtolower($remote_post['verify-purchase']['buyer']) != strtolower($check['username']) ) ){

				unset($check['purchased']);
				unset($check['lastcheck']);
				
				update_option( 'acf_rcwdupload_verifypurchase', $check );
				
				return false;
				
			}

			$check['purchased'] = true;
			$check['lastcheck'] = time();
				
			update_option( 'acf_rcwdupload_verifypurchase', $check );
		
		}
			
		return true;	
			
	}
		
	static function error_not_authenticated( $isnotice = true, $return = false ){

		if( !isset($_GET['page']) or ( isset($_GET['page']) and $_GET['page'] != 'acf-rcwdupload' ) ){
	
			if( function_exists('acf_get_setting') and version_compare( acf_get_setting('version'), "5", ">=" ) )
				$page = 'edit.php?post_type=acf-field-group&page=acf-rcwdupload';
			else
				$page = 'edit.php?post_type=acf&page=acf-rcwdupload';

			$msg = sprintf( __( 'Please fill the correct data in <a href="%1s">this page</a>  before to use %2s for ACF. Thanks...', 'acf-rcwdupload' ), $page, self::get_var('label') );
			
			if( is_string($isnotice) and $isnotice == '') 
				$isnotice = true;
				
			if($isnotice)
				$msg = '<div id="message" class="error"><p>'.$msg.'</p></div>';

			if(!$return)
				echo $msg;
			else
				return $msg;
			
		}
			
	}

	static function checkit($args = array()){

		$check 				= get_option( 'acf_rcwdupload_verifypurchase' );
		$check['item_id'] 	= self::ACF_RCWDUPLOAD_ITEM_ID;
		$dcheck				= isset($args['dcheck']) ? (bool)$args['dcheck'] : false;
		
		if (stripos( get_option('siteurl'), 'https://' ) === 0)
			$_SERVER['HTTPS'] = 'on';

		$api_url 	= 'http://www.cantarano.com';
		$api_surl 	= 'https://rcwd.gdev.it';
		$state		= false;
		
		if (is_ssl())
			$api_url = 'https://rcwd.gdev.it';
		
		$check['website'] 	= $_SERVER['HTTP_HOST'];
		$a					= array(

			'sslverify' => false,
			'timeout' 	=> 15,
			'body' 		=> $check

		);

		$remote_post = wp_remote_post( $api_url.'/envato/api/', $a );

		if( ( !is_wp_error($remote_post) and $remote_post['response']['code'] == 200 ) and $remote_post !== false ){
			
			$state = true;
	
		}elseif($api_url != $api_surl){
			
			$remote_post = wp_remote_post( $api_surl.'/envato/api/', $a );

			if ( ( !is_wp_error($remote_post) and $remote_post['response']['code'] == 200 ) and $remote_post !== false )
				$state = true;
				
		}
		
		return array( $remote_post, $state );
							
	}
								
}
?>