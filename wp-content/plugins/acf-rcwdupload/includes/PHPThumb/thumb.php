<?php
require_once 'ThumbLib.inc.php';

if( isset($_GET['f']) and isset($_GET['w']) and isset($_GET['h']) and isset($_GET['c']) ){

	$f 		= urldecode(base64_decode(substr( $_GET['f'], 3 )));
	$t 		= isset($_GET['t']) 	? (bool)$_GET['t'] 		: false;
	$frc	= isset($_GET['frc'])	? (bool)$_GET['frc'] 	: false;
	$w 		= (int)$_GET['w'];
	$h 		= (int)$_GET['h'];
	$c 		= filter_var( $_GET['c'], FILTER_SANITIZE_STRING );
	
	if($frc)
		$f = str_replace( 'https:', 'http:' , $f );
		
	$thumb = PhpThumbFactory::create($f);
	
	if($c == 'Y')
		$thumb->crop( 0, 0, $w, $h )->show();
	else
		$thumb->resize( $w, $h )->show();
}
?>