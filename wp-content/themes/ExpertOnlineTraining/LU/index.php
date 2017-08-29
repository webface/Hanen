<?php

	require('../../../../wp-load.php');

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ajax_find_wrong_num_modules')
	{
		ajax_find_wrong_num_modules();
		die();
	}

	require_once(get_template_directory() . '/LU/kint/Kint.class.php');

	// define LearnUpon API keys
	define ('LU_USERNAME', '824ef2d7d93928a069ab');
	define ('LU_PASSWORD', '1623b1d32030b6492c00a343e8546c');

	// base courses (course name => LU course ID)
	$base_courses = array (
		"New Staff" => '128158',
		"Returning Staff" => '128144',
		"Program Staff" => '128145',
		"Supervisory Staff" => '128140'
	);

	// define variables for Leadership Essentials LU course ID
	define ('lrn_upon_LE_Course_ID', 143529);

	// define variables for Leadership Essentials Limited LU course ID
	define ('lrn_upon_LEL_Course_ID', 128143);

	// define variables for Child Welfare & Protection (formarly Safety Essentials) LU course ID
	define ('lrn_upon_SE_Course_ID', 128171);

	// define the LU course ids for the starter packs
	define ('lrn_upon_LE_SP_DC_Course_ID',143886);
	define ('lrn_upon_LE_SP_OC_Course_ID',143887);
	define ('lrn_upon_LE_SP_PRP_Course_ID',143888);

	require_once(get_template_directory() . '/LU/LU_functions.php');

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

Going to get stats for Mid-Atlantic Burn Camp (59824)

<?php

	$portal_subdomain = 'midatlanticburncamp';
//	$portal = getPortals($portal_subdomain);
//	d($portal);

	$data = array();
	$courses = LU_getCourses($portal_subdomain, 1, $data);
	d($courses);











