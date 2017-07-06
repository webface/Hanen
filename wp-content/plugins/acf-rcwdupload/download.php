<?php

if( isset($_GET['acfrcwduplddwnlod']) and isset($_GET['file']) ){
	$type				= filter_var( $_GET['type'], FILTER_SANITIZE_STRING );
	$value				= filter_var( $_GET['value'], FILTER_SANITIZE_STRING );
	$acfrcwduplddwnlod	= filter_var( $_GET['acfrcwduplddwnlod'], FILTER_SANITIZE_STRING );
	$file_name			= filter_var( $_GET['file'], FILTER_SANITIZE_URL );
	$folder				= '';
	
	switch($type){
		case 'o':
			$folder = 'options_'.$value.DIRECTORY_SEPARATOR;
			break;	
		case 't':
			$folder = 'tax_'.$value.DIRECTORY_SEPARATOR;
			break;
		case 'u':
			$folder = 'user_'.$value.DIRECTORY_SEPARATOR;
			break;
		case 'p':
			$folder = 'post_'.$value.DIRECTORY_SEPARATOR;
			break;									
	}
	
	$file_path = ACF_RCWDUPLOAD_UP_DIR.DIRECTORY_SEPARATOR.'field_'.$acfrcwduplddwnlod.DIRECTORY_SEPARATOR.$folder.$file_name;

	if (!file_exists($file_path)) {
		_e( 'File not found :(', 'acf-rcwdupload' );
	}else{
		$mode	= (int)@$_GET['mode'];
		$cdisp 	= 'inline';

		if($mode == 1){
			$cdisp = 'attachment';
		}
							
		$length		= filesize($file_path);
		$file_ext 	= strtolower(substr( strrchr( $file_name, "." ), 1 ));		

		switch($file_ext) {
			case "pdf"	: $file_type = "application/pdf"; 				break;
			case "exe"	: $file_type = "application/octet-stream"; 		break;
			case "zip"	: $file_type = "application/zip"; 				break;
			case "doc"	: $file_type = "application/msword"; 			break;
			case "xls"	: $file_type = "application/vnd.ms-excel"; 		break;
			case "ppt"	: $file_type = "application/vnd.ms-powerpoint"; break;
			case "gif"	: $file_type = "image/gif"; 					break;
			case "png"	: $file_type = "image/png";		 				break;
			case "jpeg"	: $file_type = "image/jpg"; 					break;
			case "jpg"	: $file_type = "image/jpg"; 					break;
			case "mp3"	: $file_type = "application/mpeg"; 				break;
			case "wav"	: $file_type = "application/x-wav"; 			break;
			case "mpeg"	: $file_type = "application/mpeg"; 				break;
			case "mpg"	: $file_type = "application/mpeg"; 				break;
			case "mpe"	: $file_type = "application/mpeg"; 				break;
			case "mov"	: $file_type = "application/quicktime"; 		break;
			case "avi"	: $file_type = "application/x-msvideo"; 		break;
	
			case "php"	:
			case "htm"	:
			case "html"	: die(__( 'Sorry, file extension not allowed.', 'acf-rcwdupload' )); break;
	
			default: $file_type = "application/force-download";
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		//header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: $file_type");  
		header("Content-Disposition: $cdisp; filename= ".$file_name);
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$length);
		readfile($file_path);
					
													
	}
}else{
	_e( 'File not found :(', 'acf-rcwdupload' );	
}
?>