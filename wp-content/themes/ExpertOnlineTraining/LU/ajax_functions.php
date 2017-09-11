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
    global $wpdb;

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
        // add new user id into table
        $wpdb->insert ( 'wp_eot_old_data', array('type' => 'USER', 'old_id' => $old_id, 'new_id' => $user_exists));

        //@todo make sure we have a subscription and org for this user is its a director

    	$result['status'] = 0;
    	$result['message'] = "User: $user_login already exists with ID: $user_exists";
    }
    else
    {
        // user doesnt exist so create the user
        // get users first/last name
        if (empty($first_name))
        {
            $first_name = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'first_name'" );
        }
        if (empty($last_name))
        {
            $last_name = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'last_name'" );
        }

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

        $result['message'] = 'trying to create user: ' . json_encode($user_data);

    	// insert new user
        $new_id = wp_insert_user( $user_data );
        if ($new_id)
        {
            // inserted new user, now copy their subscription, upgrades, org, org_meta
            $wpdb->insert ( 'wp_eot_old_data', array('type' => 'USER', 'old_id' => $old_id, 'new_id' => $new_id));

            if ($role = 'manager') // its a director. need to set up their org/sub
            {
                // get old org data, create new org, add it to usermeta
                $old_org_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'org_id'" );
                $org = $wpdb->get_row( "SELECT * FROM wp_eot_posts WHERE ID = $old_org_id", ARRAY_A );
                $org['ID'] = 0; // reset the post ID cause it will be different upon insertion.

                $new_org_id = wp_insert_post ( $org );
                if ($new_org_id)
                {
                    $wpdb->insert ( 'wp_eot_old_data', array('type' => 'ORG', 'old_id' => $old_org_id, 'new_id' => $new_org_id));

                    $old_org_meta = $wpdb->get_results ( "SELECT * FROM wp_eot_postmeta WHERE post_id = $old_org_id" );
                    foreach ($old_org_meta as $old_meta)
                    {
                        update_post_meta( $new_org_id, $old_meta['meta_key'], $old_meta['meta_value'] );
                    }

                    // now insert their subscription
                    $old_sub = $wpdb->get_row( "SELECT * FROM wp_eot_subscriptions WHERE org_id = $old_org_id", ARRAY_A );
                    if ($old_sub)
                    {
                        $old_sub_id = $old_sub['id'];
                        $old_sub['id'] = 0; // clear out the sub id
                        $old_sub['rep_id'] = get_new_id( 'USER', $old_sub['rep_id']);
                        $new_sub_id = $wpdb->insert ( 'wp_subscriptions', $old_sub );

                        if ($new_sub_id)
                        {
                            $wpdb->insert ( 'wp_eot_old_data', array('type' => 'SUB', 'old_id' => $old_sub_id, 'new_id' => $new_sub_id));
                        
                            // check for any upgrades and add them too
                            $upgrades = $wpdb->get_results ( "SELECT * FROM wp_eot_upgrade WHERE subscription_id = $old_sub_id", ARRAY_A);
                            if (!empty($upgrades))
                            {
                                foreach ($upgrades as $upgrade)
                                {
                                    $upgrade['ID'] = 0;
                                    $upgrade['subscription_id'] = $new_sub_id;
                                    $upgrade['user_id'] = get_new_id( 'USER', $upgrade['user_id']);
                                    $upgrade['rep_id'] = get_new_id( 'USER', $upgrade['rep_id']);
                                    $wpdb->insert ( 'wp_upgrade', $upgrade);
                                }
                            }
                        }
                    }
                    else
                    {
                        $result['status'] = 0;
                        $result['message'] = "ERROR: $user_login old_id: $old_id doesnt have a subscription.";
                    }

                }
                // get old subscription data, modify it, insert new subscription

            }
            else if ($role = 'student')
            {
                // add student into new org
            }



        }
        else
        {
            // something went wrong when trying to create new user
            $result['status'] = 0;
            $result['message'] = "ERROR: when trying to insert new user: $user_login old_id: $old_id";
        }
	
    }

    echo json_encode($result);
    die();

}

// get the new id of a given type
function get_new_id( $type = 'USER', $old_id = 0)
{
    if (!$old_id)
        return 0;

    if (!in_array($type, array('USER', 'ORG', 'SUB')))
        return 0;

    global $wpdb;
    $new_id = $wpdb->get_var( "SELECT new_id FROM wp_eot_old_data WHERE type = '$type' AND old_id = $old_id" );
    
    if ($new_id)
        return $new_id;

    return 0;
}
