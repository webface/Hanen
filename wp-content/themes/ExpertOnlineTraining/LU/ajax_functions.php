<?php

// triggers custom_script_process_portal_ajax from ajax.
function ajax_find_wrong_num_modules()
{

    $org_id = (isset($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $id = (isset($_REQUEST['id'])) ? filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $title = (isset($_REQUEST['title'])) ? $_REQUEST['title'] : 0;
    $portal_subdomain = (isset($_REQUEST['subdomain'])) ? $_REQUEST['subdomain'] : 0;

    $result = array("status" => 1, "message" => "");

    if ($org_id == 0)
    {
		$result = array("status" => 0, "message" => "no org_id for portal $id $portal_subdomain $title");
      	echo json_encode($result);
      	die();
    }

    $courses = LU_getCourses($portal_subdomain, 1, compact("org_id"));
	// go through all the courses and find out how many modules in the LE course
	foreach ($courses as $course)
	{
		if ($course['name'] != lrn_upon_LE_Course_TITLE)
		{
			// output number of modules in course
			$num_modules = 0;
			$num_modules = $course['number_of_modules'];

			// get the modules in this course and make sure half are scorm and have are pages
			$modules = LU_getModules($course['id'], $portal_subdomain, compact("org_id"), '');

			// if no modules, make sure its an array so that it doesnt mess up the foreach loop below.
			if (empty($modules))
			{
				$modules = array();
			}

			$num_pages = 0;
			$num_scorm = 0;
			foreach ($modules as $module) 
			{
				if ($module['component_type'] == 'page')
				{
					$num_pages ++;
				}
				elseif ($module['component_type'] == 'scorm')
				{
					$num_scorm ++;
				}
			}			

			if ($num_pages == $num_scorm)
			{
				$result['message'] .= $course['name'] . " (". $course['published_status_id'] .") : $num_pages = $num_scorm <i class=\"fa fa-check\"></i><br>";
			}
			else
			{
				$result['message'] .= $course['name'] . " <b class=\"red\">(". $course['published_status_id'] .")</b> : $num_pages != $num_scorm <i class=\"fa fa-times red\"></i><br>";
			}
		}
	}

    echo json_encode($result);
    die();
    
}

// going to try to create the user
function ajax_processUser()
{
    $user_login = (isset($_REQUEST['user_login'])) ? filter_var($_REQUEST['user_login'], FILTER_SANITIZE_STRING) : 0;
    $user_pass = (isset($_REQUEST['user_pass'])) ? $_REQUEST['user_pass'] : wp_generate_password(12);
    $old_id = (isset($_REQUEST['old_id'])) ? filter_var($_REQUEST['old_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $portal_subdomain = (isset($_REQUEST['subdomain'])) ? $_REQUEST['subdomain'] : 0;
    $user_email = (isset($_REQUEST['user_email'])) ? filter_var($_REQUEST['user_email'], FILTER_SANITIZE_STRING) : 'hagai@targetdirectories.com';
    $role = (isset($_REQUEST['role'])) ? filter_var($_REQUEST['role'], FILTER_SANITIZE_STRING) : 'subscriber';

    $first_name = (isset($_REQUEST['first_name'])) ? filter_var($_REQUEST['first_name'], FILTER_SANITIZE_STRING) : '';
    $last_name = (isset($_REQUEST['last_name'])) ? filter_var($_REQUEST['last_name'], FILTER_SANITIZE_STRING) : '';


    $result = array("status" => 1, "message" => "All Done.");

    // check if user already exists in DB
    $user_exists = username_exists($user_login);
    if ($user_exists)
    {
    	$result['status'] = 0;
    	$result['message'] = "User: $user_login already exists with ID: $user_exists";
    }
    else
    {
    	// user doesnt exist so create him
    	$user_data = array (
    		'user_login' => $user_login,
    		'user_pass' => $user_pass,
    		'user_email' => $user_email,
    		'display_name' => $first_name . ' ' . $last_name,
    		'first_name' => $first_name,
    		'last_name' => $last_name,
    		'role' => $role
    	);

    	// insert user

    	// if user is a manager/student need an org.
    	
    }

    echo json_encode($result);
    die();

}
