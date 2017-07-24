<?php

// HTTP headers for no cache etc
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

global $wpdb;

$classCommon 		= 'ACFRcwdCommon';
$classOrientation 	= 'ACFRcwdOrientation';
$classResize		= 'ACFRcwdResize';

if(!function_exists('rcwd_clone_wp_is_stream')){

	function rcwd_clone_wp_is_stream($path){
		
		$wrappers 		= stream_get_wrappers();
		$wrappers_re 	= '(' . join('|', $wrappers) . ')';
		
		return preg_match( "!^$wrappers_re://!", $path ) === 1;
		
	}

}

if(!function_exists('rcwd_clone_wp_mkdir_p')){

	function rcwd_clone_wp_mkdir_p($target){
		
		$wrapper = null;
	
		if ( rcwd_clone_wp_is_stream( $target ) )
			list( $wrapper, $target ) = explode( '://', $target, 2 );
	
		$target = str_replace( '//', '/', $target );
	
		if ( $wrapper !== null )
			$target = $wrapper . '://' . $target;
	
		$target = rtrim($target, '/');
		
		if ( empty($target) )
			$target = '/';
	
		if ( file_exists( $target ) )
			return @is_dir( $target );
	
		$target_parent = dirname( $target );
		
		while ( '.' != $target_parent && ! is_dir( $target_parent ) )
			$target_parent = dirname( $target_parent );
	
		if ( $stat = @stat( $target_parent ) )
			$dir_perms = $stat['mode'] & 0007777;
		else
			$dir_perms = 0777;
	
		if ( @mkdir( $target, $dir_perms, true ) ) {
	
			if ( $dir_perms != ( $dir_perms & ~umask() ) ) {
				
				$folder_parts = explode( '/', substr( $target, strlen( $target_parent ) + 1 ) );
				
				for ( $i = 1, $c = count( $folder_parts ); $i <= $c; $i++ )
					@chmod( $target_parent . '/' . implode( '/', array_slice( $folder_parts, 0, $i ) ), $dir_perms );
	
			}
	
			return true;
			
		}
	
		return false;
		
	}

}

if( isset($_GET['info']) and isset($_REQUEST["name"]) ){
	
	$info = json_decode( base64_decode(substr( $_GET['info'], 3 )), true );

	define( 'ACF_RCWDUPLOAD_UP_TEMP_DIR', $info['temp_path'] );
	
	$pst		= @$_GET['post'];
	$fkey 		= @$info['fkey'];
	$randomcode		= $info['randomcode'];
	$overw 		= @(bool)$info['overw'];
	//$pst		= @$_GET['pst'];
	//$foldercode	= @$_GET['foldercode'];
	//$targetDir 	= ACF_RCWDUPLOAD_UP_TEMP_DIR.DIRECTORY_SEPARATOR.$fkey;
	$targetDir		= $info['path_temp'];
	
	//if($foldercode != ''){
		
	//$targetDir .= DIRECTORY_SEPARATOR.$foldercode;

	rcwd_clone_wp_mkdir_p($targetDir);
		
	//}

	$targetDir 			.= DIRECTORY_SEPARATOR;
	$renamed			= 0;
	$cleanupTargetDir 	= true; // Remove old files
	$maxFileAge 		= 5 * 3600; // Temp file age in seconds
	
	// 5 minutes execution time
	@set_time_limit(5 * 60);
	
	// Uncomment this one to fake upload time
	// usleep(5000);
	
	// Get parameters
	$chunk 			= isset($_REQUEST["chunk"]) 	? intval($_REQUEST["chunk"]) 	: 0;
	$chunks 		= isset($_REQUEST["chunks"]) 	? intval($_REQUEST["chunks"]) 	: 0;
	$fileName 		= isset($_REQUEST["name"]) 		? $_REQUEST["name"] 			: '';
	
	// Clean the fileName for security reasons
	$fileName 		= preg_replace('/[^\w\._]+/', '_', $fileName);
	$base_name 		= basename($fileName);
	$file_pathinfo 	= pathinfo($fileName);	
	$file_ext		= strtolower($file_pathinfo['extension']);	
	$filenameonly 	= substr( $fileName, 0, strrpos($fileName, '.') );
	
	// Make sure the fileName is unique but only if chunking is disabled
	if ( !$overw and $chunks < 2 and file_exists($targetDir.$fileName)){
		
		$ext 		= strrpos( $fileName, '.' );
		$fileName_a = $filenameonly;
		$fileName_b = substr( $fileName, $ext );
		$count 		= 1;
		
		while (file_exists($targetDir.$fileName_a . '_' . $count . $fileName_b))
			$count++;
	
		$fileName 	= $fileName_a . '_' . $count . $fileName_b;
		$renamed	= 1;
	}
	
	$filePath = $targetDir . $fileName;
	
	// Create target dir
	if (!file_exists($targetDir))
		@mkdir($targetDir);
	
	// Remove old temp files	
	if ($cleanupTargetDir){
		
		if (is_dir($targetDir) && ($dir = opendir($targetDir))){
			
			while (($file = readdir($dir)) !== false){
				
				$tmpfilePath = $targetDir.$file;
	
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part"))
					@unlink($tmpfilePath);
	
			}
			
			closedir($dir);
			
		}else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	
	}	
	
	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
	
	if (isset($_SERVER["CONTENT_TYPE"]))
		$contentType = $_SERVER["CONTENT_TYPE"];
	
	// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
	if (strpos($contentType, "multipart") !== false){
		
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])){
			// Open temp file
			$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			
			if($out){
				// Read binary input stream and append it to temp file
				$in = @fopen($_FILES['file']['tmp_name'], "rb");
	
				if($in){
					
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
						
				}else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					
				@fclose($in);
				@fclose($out);
				@unlink($_FILES['file']['tmp_name']);
				
			}else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
				
		}else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			
	}else{
		// Open temp file
		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		
		if($out){
			// Read binary input stream and append it to temp file
			$in = @fopen("php://input", "rb");
	
			if($in){
				
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
					
			}else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	
			@fclose($in);
			@fclose($out);
			
		}else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			
	}
	
	// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off 
		rename( "{$filePath}.part", $filePath );
		
		if(in_array( $file_ext, array( 'gif', 'jpg', 'jpeg', 'png' ) )){
			
			if(empty($info['resize_qs']))
				$info['resize_qs'] = array(
				
					'quality'	=> 100,
					'width'		=> 0,
					'height'	=> 0,
					'crop'		=> false
					
				);
							
			$quality 	= isset($info['resize_qs']['quality']) 	? (int)$info['resize_qs']['quality'] 	: 100;
			$maxwidth 	= isset($info['resize_qs']['width']) 	? (int)$info['resize_qs']['width'] 		: 0;
			$maxheight 	= isset($info['resize_qs']['height']) 	? (int)$info['resize_qs']['height'] 	: 0;
			$crop 		= isset($info['resize_qs']['crop']) 	? (bool)$info['resize_qs']['crop'] 		: false;
			$orw 		= isset($_REQUEST["orw"]) 				? (int)$_REQUEST["orw"]					: 0;
			$orh 		= isset($_REQUEST["orh"]) 				? (int)$_REQUEST["orh"]					: 0;
			
			switch($file_ext){
				
				case 'gif':
				
					$fullsize = imagecreatefromgif($targetDir.$fileName);
					
					break;
					
				case 'jpg':
				case 'jpeg':
				
					$fullsize = imagecreatefromjpeg($targetDir.$fileName);
					
					break;
					
				case 'png':
				
					$fullsize = imagecreatefrompng($targetDir.$fileName);
					
			}

			$fullsize_width		= imagesx($fullsize);
			$fullsize_height	= imagesy($fullsize);
			$orientation		= $classOrientation::detect($targetDir.$fileName);

/*			if( ( $maxwidth == 0 and $maxheight == 0  ) or ( ($maxwidth >= $fullsize_width) and ($maxheight >= $fullsize_height) ) ){
								
				if( $orw > 0 and $orw > 0 and ( ($orw > $fullsize_width) or ($orh > $fullsize_height) ) ){
					
					$newimage = imagecreatetruecolor( $orw, $orh );
					
					imagealphablending( $newimage, false );
					imagesavealpha( $newimage, true ); 
					imagecopyresampled( $newimage, $fullsize, 0, 0, 0, 0, $orw, $orh, $fullsize_width, $fullsize_height );
					
					$fullsize = $newimage;				
				}
				
			}*/
			
			if($orientation > 1)
				$fullsize = $classOrientation::fix( $fullsize, $orientation );
							
			if( $maxwidth > 0 or $maxheight > 0 ){

				if( $maxwidth == 0 or $maxheight == 0 )
					$crop = false;
												
				if( ( $maxwidth < $fullsize_width and $maxwidth > 0 ) or ( $maxheight < $fullsize_height and $maxheight > 0 ) ){	
			
					if($crop === true){
						
						if($maxwidth > 0) 
							$crop_width = $maxwidth;
																										 
						if($maxheight > 0)
							$crop_height = $maxheight;		
												
					}
					
					if( $maxwidth == 0 or $maxwidth == '' or !is_numeric($maxwidth) ){
						
						$maxwidth	= floor( $fullsize_width / ( $fullsize_height / $maxheight ) );
						$crop		= false;
						
					}elseif( $maxwidth > $fullsize_width ){
						
						$maxwidth 	= $fullsize_width;
						$crop_width = $maxwidth;
						
					}
					
					if( $maxheight == 0 or $maxheight == '' or !is_numeric($maxheight) ){
						
						$maxheight	= floor( $fullsize_height / ( $fullsize_width / $maxwidth ) );
						$crop		= false;
						
					}elseif($maxheight > $fullsize_width){
						
						$maxheight		= $fullsize_height;
						$crop_height 	= $maxheight;
						
					}
		
					$ratio = $fullsize_width / $fullsize_height;
		
					// compute the unspecified dimension
					if ($maxwidth < 1)
						$maxwidth = $maxheight * $ratio;
					elseif($maxheight < 1)
						$maxheight = $maxwidth/$ratio;
					
					$new_ratio 		= $maxwidth/$maxheight;
					$widthRatio		= $maxwidth/$fullsize_width;
					$heightRatio 	= $maxheight/$fullsize_height;
					
					if(!$crop){
						
						// keep original ratio
						if ( $new_ratio != $ratio and abs($new_ratio - $ratio) > 1 ){
							
							if ($widthRatio > $heightRatio)
								$maxwidth = $maxheight * $ratio;
							else
								$maxheight = $maxwidth / $ratio; 
							
						}
						$src_x = 0;
						$src_y = 0;
	
						$newimage = imagecreatetruecolor( $maxwidth, $maxheight );
						
						imagealphablending( $newimage, false );
						imagesavealpha( $newimage, true ); 
						imagecopyresampled( $newimage, $fullsize, 0, 0, $src_x, $src_y, $maxwidth, $maxheight, $fullsize_width, $fullsize_height );
						
						switch($file_ext){
							
							case 'gif':
							
								imagegif( $newimage, $targetDir.$fileName, $quality );
								
								break;
								
							case 'jpg':
							case 'jpeg':
							
								imagejpeg( $newimage, $targetDir.$fileName, $quality );
								
								break;
								
							case 'png':
							
								imagepng( $newimage, $targetDir.$fileName, round(abs(( $quality - 100 ) / 11.111111 )) );
								
								break;
								
						}
											
					}else{

						$resizeObj = new $classResize($targetDir.$fileName);
						
						$resizeObj->resizeImage( $crop_width, $crop_height, 'crop' );
						$resizeObj->saveImage( $targetDir.$fileName, $quality, $orientation );
										
					}
					
				}else{
					
					if($orientation > 1){
											
						$newimage = $fullsize;
							
						switch($file_ext){
							
							case 'gif':
							
								imagegif( $newimage, $targetDir.$fileName, $quality );
								
								break;
								
							case 'jpg':
							case 'jpeg':
							
								imagejpeg( $newimage, $targetDir.$fileName, $quality );
								
								break;
								
							case 'png':
							
								imagepng( $newimage, $targetDir.$fileName, round(abs(($quality - 100) / 11.111111 )) );
								
								break;
								
						}
								
					}	
					
				}
	
			}else{
				
				if($orientation > 0){
										
					$newimage = $fullsize;
						
					switch($file_ext){
						
						case 'gif':
						
							imagegif( $newimage, $targetDir.$fileName, $quality );
							
							break;
							
						case 'jpg':
						case 'jpeg':
						
							imagejpeg( $newimage, $targetDir.$fileName, $quality );
							
							break;
							
						case 'png':
						
							imagepng( $newimage, $targetDir.$fileName, round(abs(($quality - 100) / 11.111111 )) );
							
							break;
							
					}
							
				}				
				
			}
									
		}
		
	}

	if(empty($orientation))
		$orientation = '""';
			
		//die('{"jsonrpc" : "2.0", "renamed" : '.$renamed.', "filename" : "'.$fileName.'", "size" : "'.@filesize($targetDir.$fileName).'", "o" : '.$orientation.', "base64" : "'.$classCommon::randomcode(array( 'length' => 3, 'uppercase' => false )).base64_encode($targetDir.$fileName).'"}');
		die('{"jsonrpc" : "2.0", "renamed" : '.$renamed.', "filename" : "'.$fileName.'", "size" : "'.@filesize($targetDir.$fileName).'", "o" : '.$orientation.', "base64" : "'.$randomcode.base64_encode($targetDir.$fileName).'"}');
	
}

class ACFRcwdResize{
	
	private $image, $width, $height, $imageResized;

	function __construct($fileName){
		
		$this->image 	= $this->openImage($fileName);
		$this->width  	= imagesx($this->image);
		$this->height 	= imagesy($this->image);
		
	}

	private function openImage($file){
		
		$extension = strtolower(strrchr($file, '.'));

		switch($extension){
			
			case '.jpg':
			case '.jpeg':
			
				$img = @imagecreatefromjpeg($file);
				
				break;
				
			case '.gif':
			
				$img = @imagecreatefromgif($file);
				
				break;
				
			case '.png':
			
				$img = @imagecreatefrompng($file);
				
				break;
				
			default:
			
				$img = false;
				
				break;
				
		}
		
		return $img;
		
	}

	public function resizeImage( $newWidth, $newHeight, $option = "auto" ){
		
		$optionArray 		= $this->getDimensions( $newWidth, $newHeight, $option );	// *** Get optimal width and height - based on $option
		$optimalWidth  		= $optionArray['optimalWidth'];
		$optimalHeight		= $optionArray['optimalHeight'];
		$this->imageResized = imagecreatetruecolor( $optimalWidth, $optimalHeight );	// *** Resample - create image canvas of x, y size
		
		imagecopyresampled( $this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height );

		if ( $option == 'crop' and ( $newWidth < $optimalWidth or $newHeight < $optimalHeight ) )
			$this->crop( $optimalWidth, $optimalHeight, $newWidth, $newHeight );

	}

	private function getDimensions( $newWidth, $newHeight, $option ){

	   switch ($option){
			
			case 'exact':
			
				$optimalWidth 	= $newWidth;
				$optimalHeight	= $newHeight;
				
				break;
				
			case 'portrait':
			
				$optimalWidth 	= $this->getSizeByFixedHeight($newHeight);
				$optimalHeight	= $newHeight;
				
				break;
				
			case 'landscape':
			
				$optimalWidth 	= $newWidth;
				$optimalHeight	= $this->getSizeByFixedWidth($newWidth);
				
				break;
				
			case 'auto':
			
				$optionArray 	= $this->getSizeByAuto($newWidth, $newHeight);
				$optimalWidth 	= $optionArray['optimalWidth'];
				$optimalHeight 	= $optionArray['optimalHeight'];
				
				break;
				
			case 'crop':

/*				$optionArray 	= $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth 	= $optionArray['optimalWidth'];
				$optimalHeight 	= $optionArray['optimalHeight']; */
				$optimalWidth 	= $newWidth;
				$optimalHeight	= $this->getSizeByFixedWidth($newWidth);
				
				break;			
				
		}
		
		return array( 'optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight );
		
	}

	## --------------------------------------------------------

	private function getSizeByFixedHeight($newHeight){
		
		$ratio 		= $this->width / $this->height;
		$newWidth 	= $newHeight * $ratio;
		
		return $newWidth;
		
	}

	private function getSizeByFixedWidth($newWidth){
		
		$ratio 		= $this->height / $this->width;
		$newHeight 	= $newWidth * $ratio;
		
		return $newHeight;
		
	}

	private function getSizeByAuto( $newWidth, $newHeight ){
		
		if ($this->height < $this->width){	// *** Image to be resized is wider (landscape)
		
			$optimalWidth 	= $newWidth;
			$optimalHeight	= $this->getSizeByFixedWidth($newWidth);
			
		}elseif($this->height > $this->width){	// *** Image to be resized is taller (portrait)
		
			$optimalWidth 	= $this->getSizeByFixedHeight($newHeight);
			$optimalHeight	= $newHeight;
			
		}else{	// *** Image to be resizerd is a square
		
			if ($newHeight < $newWidth){
				
				$optimalWidth 	= $newWidth;
				$optimalHeight	= $this->getSizeByFixedWidth($newWidth);
				
			}elseif($newHeight > $newWidth){
				
				$optimalWidth 	= $this->getSizeByFixedHeight($newHeight);
				$optimalHeight	= $newHeight;
				
			}else{
				
				// *** Sqaure being resized to a square
				$optimalWidth 	= $newWidth;
				$optimalHeight	= $newHeight;
				
			}
			
		}

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	## --------------------------------------------------------

	private function getOptimalCrop( $newWidth, $newHeight ){

		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width /  $newWidth;

		if ($heightRatio < $widthRatio)
			$optimalRatio = $heightRatio;
		else
			$optimalRatio = $widthRatio;

		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width  / $optimalRatio;

		return array( 'optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight );
		
	}

	## --------------------------------------------------------

	private function crop( $optimalWidth, $optimalHeight, $newWidth, $newHeight ){
		
		// *** Find center - this will be used for the crop
		$cropStartX 		= ( $optimalWidth / 2) - ( $newWidth /2 );
		$cropStartY 		= ( $optimalHeight/ 2) - ( $newHeight/2 );
		$crop 				= $this->imageResized;
		$this->imageResized = imagecreatetruecolor( $newWidth, $newHeight );	// *** Now crop from center to exact requested size
		
		imagecopyresampled( $this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight );
		
	}

	## --------------------------------------------------------

	public function saveImage( $savePath, $imageQuality = "100", $orientation = 0 ){
		
		// *** Get extension
		$extension = strrchr($savePath, '.');
		$extension = strtolower($extension);

		switch($extension){
			
			case '.jpg':
			case '.jpeg':
			
				if ( imagetypes() and IMG_JPG )
					imagejpeg( $this->imageResized, $savePath, $imageQuality );
					
				break;

			case '.gif':
			
				if ( imagetypes() and IMG_GIF )
					imagegif( $this->imageResized, $savePath );

				break;

			case '.png':
			
				$scaleQuality 		= round(($imageQuality/100) * 9);	// *** Scale quality from 0-100 to 0-9
				$invertScaleQuality = 9 - $scaleQuality;				// *** Invert quality setting as 0 is best, not 9

				if ( imagetypes() and IMG_PNG )
					 imagepng( $this->imageResized, $savePath, $invertScaleQuality );

				break;

			default:

				break;
				
		}

		imagedestroy($this->imageResized);
		
	}

}

class ACFRcwdOrientation{
		
	static function detect($file){
		
		if(function_exists('exif_read_data')){
	
			$file_pathinfo 	= pathinfo($file);	
			$file_ext 		= strtolower($file_pathinfo['extension']);
				
			if( $file_ext == 'jpg' or $file_ext == 'jpeg' ){
				
				$exif 			= @exif_read_data($file);
				$orientation 	= isset($exif['Orientation']) ? (int)$exif['Orientation'] : 0;
				
				if($orientation == 0){
					
					$exif			= @exif_read_data( $file, 'IFD0' );
					$orientation 	= isset($exif['IFD0']['Orientation']) ? (int)$exif['IFD0']['Orientation'] : 0;
				
				}
			
			}else
				return 0;
	
		}else
			return 0;
			
		return $orientation;
		
	}
	
	static function fix( $newimage, $orientation = 0 ){
		
		switch((int)$orientation){
			
			case 3:
			
				$newimage = imagerotate( $newimage, 180, 0 );
				break;
				
			case 6:
			
				$newimage = imagerotate( $newimage, -90, 0 );
				break;
				
			case 8:
			
				$newimage = imagerotate( $newimage, 90, 0 );
				break;
				
		}	
	
		return $newimage;
		
	}	

}
		
die('Hello :)');
?>