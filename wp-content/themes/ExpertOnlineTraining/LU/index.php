<?php

require('../../../../wp-load.php');

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ajax_find_wrong_num_modules')
	{
		ajax_find_wrong_num_modules();
		die();
	}

require_once(get_template_directory() . '/LU/kint/Kint.class.php');

$process = 0; // boolean whether to delete and clone or just debug
$num_portals_to_process = 5; // the number of portals to process.
$admin_ajax_url = admin_url('admin-ajax.php');

	// make sure were alowed to use this script
	$cid = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : 0;
	if ($cid != 'zxasqw12~')
	{
		exit;
	}


?>
<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://use.fontawesome.com/27982da7ee.js"></script>
<style type="text/css" src="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"></style>
<style type="text/css">
	.red {
		color: red;
	}
</style>
</head>
<body>
<div class="processing"></div>
<p>&nbsp;</p>
<div class="ajax_response"></div>



