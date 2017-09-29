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


    $result = array("status" => 1, "message" => "");

    // check if user already exists in DB
    $user_exists = username_exists($user_login);
    if ($user_exists)
    {
        $result['message'] .= "User: $user_login already exists with ID: $user_exists. ";

        // add new user id into table
        if (!$wpdb->get_row( "SELECT * FROM wp_eot_old_data WHERE type = 'USER' AND old_id = $old_id", ARRAY_A ))
        {
            $wpdb->insert ( 'wp_eot_old_data', array('type' => 'USER', 'old_id' => $old_id, 'new_id' => $user_exists));
            $result['message'] .= "Added old id to table. ";
        }

        //make sure we have a subscription and org for this user if its a director
        if ($role == 'manager')
        {
            $has_org = get_user_meta( $user_exists, 'org_id', true );
            if (!$has_org)
            {
                $result['message'] .= "Director doesnt have org yet. ";
                // doesnt have an org yet. Check if he had one before.
                $old_org_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'org_id'" );
                if ($old_org_id)
                {
                    $result['message'] .= "but found old org: $old_org_id. ";
                    // it had an old org so insert the old data
                    $org = $wpdb->get_row( "SELECT * FROM wp_eot_posts WHERE ID = $old_org_id", ARRAY_A );
                    //$org['ID'] = 0; // reset the post ID cause it will be different upon insertion.
                    unset($org['ID']);
                    $new_org_id = wp_insert_post ( $org );
                    if ($new_org_id)
                    {
                        $result['message'] .= "Created new org: $new_org_id. ";
                        update_user_meta($user_exists, 'org_id', $new_org_id);
                        $wpdb->insert ( 'wp_eot_old_data', array('type' => 'ORG', 'old_id' => $old_org_id, 'new_id' => $new_org_id));

                        $old_org_meta = $wpdb->get_results ( "SELECT * FROM wp_eot_postmeta WHERE post_id = $old_org_id",ARRAY_A );
                        foreach ($old_org_meta as $old_meta)
                        {
                            update_post_meta( $new_org_id, $old_meta['meta_key'], $old_meta['meta_value'] );
                        }
                    }
                }
            }
            else
            {
                $result['message'] .= "Director does have org: $has_org. ";
                // has an org, make sure he has a subscription
                $has_subscription = $wpdb->get_row( "SELECT * FROM wp_subscriptions WHERE org_id = $has_org", ARRAY_A );
                if ($has_subscription)
                {
                    $result['message'] .= "Does have subscription: ". $has_subscription['ID'] . ". ";
                    // has a subscription, check if there are any upgrades
                    $has_upgrades = $wpdb->get_results( "SELECT * FROM wp_upgrade WHERE org_id = $has_org", ARRAY_A );
                    if(!$has_upgrades)
                    {
                        $result['message'] .= "Doesn't have upgrades. ";
                        // no upgrades, check if there were in the old db
                        $upgrades = $wpdb->get_results ( "SELECT * FROM wp_eot_upgrade WHERE subscription_id = $has_org", ARRAY_A);
                        if (!empty($upgrades))
                        {
                            $result['message'] .= "Did have upgrades, so adding them.";
                            foreach ($upgrades as $upgrade)
                            {
                                unset($upgrade['id']);
                                //$upgrade['ID'] = 0;
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
                    $result['message'] .= "Doesn't have a subscription, checking if had one. ";
                    // no subscription. Check if he had one.
                    $old_sub = $wpdb->get_row( "SELECT * FROM wp_eot_subscriptions WHERE org_id = $has_org", ARRAY_A );
                    if ($old_sub)
                    {
                        $old_sub_id = $old_sub['id'];
                        $result['message'] .= "Had old subscription: $old_sub_id. ";
                        $old_sub['id'] = 0; // clear out the sub id
                        $old_sub['rep_id'] = get_new_id( 'USER', $old_sub['rep_id']);
                        $old_sub['manager_id'] = get_new_id( 'USER', $old_sub['manager_id']);
                        $old_sub['org_id'] = get_new_id( 'ORG', $old_sub['org_id']);
                        $new_sub_id = $wpdb->insert ( 'wp_subscriptions', $old_sub );

                        if ($new_sub_id)
                        {
                            $result['message'] .= "New subscription id: $new_sub_id. ";
                            $wpdb->insert ( 'wp_eot_old_data', array('type' => 'SUB', 'old_id' => $old_sub_id, 'new_id' => $new_sub_id));
                        
                            // check for any upgrades and add them too
                            $upgrades = $wpdb->get_results ( "SELECT * FROM wp_eot_upgrade WHERE subscription_id = $old_sub_id", ARRAY_A);
                            if (!empty($upgrades))
                            {
                                $result['message'] .= "Had old upgrades, adding... ";
                                foreach ($upgrades as $upgrade)
                                {
                                    unset($upgrade['id']);
                                    $upgrade['ID'] = 0;
                                    $upgrade['subscription_id'] = $new_sub_id;
                                    $upgrade['user_id'] = get_new_id( 'USER', $upgrade['user_id']);
                                    $upgrade['rep_id'] = get_new_id( 'USER', $upgrade['rep_id']);
                                    $wpdb->insert ( 'wp_upgrade', $upgrade);
                                }
                            }
                        }
                    }
                }
            }
        } // end if manager role
        else if ($role == 'student')
        {
            $result['message'] .= "Student. ";
            // student exists, make sure he's assigned to an org
            $has_org = get_user_meta( $user_exists, 'org_id', true );
            if(!$has_org)
            {
                $result['message'] .= "No org yet. ";
                // isn't assigned to an org yet so find out what his old org is.
                $old_org_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'org_id'" );

                // get the new org id
                $new_org_id = get_new_id( 'ORG', $old_org_id );

                // insert new org
                update_user_meta( $user_exists, 'org_id', $new_org_id );
                $result['message'] .= "Old org: $old_org_id New org: new_org_id. ";
            }

            // add the lrn_upon_id
            if (!get_user_meta( $user_exists, 'lrn_upon_id', true ))
            {
                $lrn_upon_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'lrn_upon_id'" );
                update_user_meta( $user_exists, 'lrn_upon_id', $lrn_upon_id );
                $result['message'] .= "Added lrn_upon_id: $lrn_upon_id. ";
            }

            // add the portal
            if (!get_user_meta( $user_exists, 'portal', true ))
            {
                $portal = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'portal'" );
                update_user_meta( $user_exists, 'portal', $portal );
                $result['message'] .= "Added portal: $portal";
            }
        }
        $result['message'] .= "All Done.<br>";
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

        $result['message'] .= "Creating new user: " . json_encode($user_data) . "<br>";

    	// insert new user
        $new_id = wp_insert_user( $user_data );
        if (! is_wp_error( $new_id ))
        {
            $result['message'] .= "New ID: $new_id. ";
            // inserted new user, now copy their subscription, upgrades, org, org_meta
            $wpdb->insert ( 'wp_eot_old_data', array('type' => 'USER', 'old_id' => $old_id, 'new_id' => $new_id));
            if ($role == 'manager') // its a director. need to set up their org/sub
            {
                $result['message'] .= "Director. ";
                // get old org data, create new org, add it to usermeta
                $old_org_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'org_id'" );
                if($old_org_id)
                {
                    $result['message'] .= "Had old org: $old_org_id. ";
                    $org = $wpdb->get_row( "SELECT * FROM wp_eot_posts WHERE ID = $old_org_id", ARRAY_A );
                    unset($org['ID']); // reset the post ID cause it will be different upon insertion.

                    $new_org_id = wp_insert_post ( $org );
                    if ($new_org_id)
                    {
                        $result['message'] .= "New org: $new_org_id. ";
                        update_user_meta($new_id, 'org_id', $new_org_id);
                        $wpdb->insert ( 'wp_eot_old_data', array('type' => 'ORG', 'old_id' => $old_org_id, 'new_id' => $new_org_id));

                        $old_org_meta = $wpdb->get_results ( "SELECT * FROM wp_eot_postmeta WHERE post_id = $old_org_id", ARRAY_A);
                        foreach ($old_org_meta as $old_meta)
                        {
                            update_post_meta( $new_org_id, $old_meta['meta_key'], $old_meta['meta_value'] );
                        }

                        // now insert their subscription
                        $old_sub = $wpdb->get_row( "SELECT * FROM wp_eot_subscriptions WHERE org_id = $old_org_id", ARRAY_A );
                        if ($old_sub)
                        {
                            $result['message'] .= "Old subscription: $old_sub. ";
                            $old_sub_id = $old_sub['id'];
                            unset($old_sub['id']); // clear out the sub id
                            $old_sub['rep_id'] = get_new_id( 'USER', $old_sub['rep_id']);
                            $old_sub['manager_id'] = get_new_id( 'USER', $old_sub['manager_id']);
                            $old_sub['org_id'] = get_new_id( 'ORG', $old_sub['org_id']);
                            $old_sub['setup'] = 1;
                            $new_sub = $wpdb->insert ( 'wp_subscriptions', $old_sub );
                            $new_sub_id = $wpdb->insert_id;
                            if ($new_sub_id)
                            {
                                $result['message'] .= "New subscription: $new_sub_id. ";
                                $wpdb->insert ( 'wp_eot_old_data', array('type' => 'SUB', 'old_id' => $old_sub_id, 'new_id' => $new_sub_id));

                                // check for any upgrades and add them too
                                $upgrades = $wpdb->get_results ( "SELECT * FROM wp_eot_upgrade WHERE subscription_id = $old_sub_id", ARRAY_A);
                                if (!empty($upgrades))
                                {
                                    $result['message'] .= "Inserting upgrades. ";
                                    foreach ($upgrades as $upgrade)
                                    {
                                        unset($upgrade['id']);
                                        //$upgrade['ID'] = 0;
                                        $upgrade['subscription_id'] = $new_sub_id;
                                        $upgrade['user_id'] = get_new_id( 'USER', $upgrade['user_id']);
                                        $upgrade['rep_id'] = get_new_id( 'USER', $upgrade['rep_id']);
                                        $wpdb->insert ( 'wp_upgrade', $upgrade);
                                    }
                                }
                            }
                            else
                            {
                               $result['status'] = 0;
                               $result['message'] .= "<span style='color:red'>ERROR: </span> could'nt add the subscription. "; 
                            }
                        }
                        else
                        {
                            $result['status'] = 0;
                            $result['message'] .= "<span style='color:red'>ERROR: </span> $user_login old_id: $old_id doesnt have a subscription.";
                        }

                    }
                }

            }
            else if ($role == 'student')
            {
                $result['message'] .= "Student. ";
                // add student into new org
                $has_org = get_user_meta( $new_id, 'org_id', true );
                if(!$has_org)
                {
                    // isn't assigned to an org yet so find out what his old org is.
                    $old_org_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'org_id'" );

                    // get the new org id
                    $new_org_id = get_new_id( 'ORG', $old_org_id );

                    // insert new org
                    update_user_meta( $new_id, 'org_id', $new_org_id );
                    $result['message'] .= "Old org: $old_org_id New org: $new_org_id. ";
                }

                // add the lrn_upon_id
                if (!get_user_meta( $new_id, 'lrn_upon_id', true ))
                {
                    $lrn_upon_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'lrn_upon_id'" );
                    update_user_meta( $new_id, 'lrn_upon_id', $lrn_upon_id );
                    $result['message'] .= "lrn_upon_id: $lrn_upon_id. ";
                }

                // add the portal
                if (!get_user_meta( $new_id, 'portal', true ))
                {
                    $portal = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'portal'" );
                    update_user_meta( $new_id, 'portal', $portal );
                    $result['message'] .= "portal: $portal. ";
                }

            }
        }
        else
        {
            // something went wrong when trying to create new user
            $result['status'] = 0;
            $result['message'] .= "<span style='color:red'>ERROR:</span> when trying to insert new user: $user_login old_id: $old_id ";
        }
	   
       $result['message'] .= "All Done. ";
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

//return camp subdomain from camp id from LU data
function getCampFromUserId($user_id)
{
    global $wpdb;
    include(get_template_directory() . '/LU/data.php');

    foreach ($LU_data as $key => $value) {
        if($value['user_id']==$user_id)
        {
            return $key;
        }
        
    }
}

function ajax_processStats()
{
    global $wpdb;
    require_once ('data.php');
    include(get_template_directory() . '/LU/data.php');
    //include(get_template_directory() . '/LU/LU_functions.php');
    $old_id = (isset($_REQUEST['old_id'])) ? filter_var($_REQUEST['old_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $result = array("status" => 1, "message" => "");   

	//echo "Going to get stats for Mid-Atlantic Burn Camp (59824)";
	$portal_subdomain = getCampFromUserId($old_id);
	//	$portal = getPortals($portal_subdomain);
    //d($portal_subdomain);
    if($portal_subdomain)
    {
        $result['message'] .= "Got portal: $portal_subdomain. ";
        $org_id = $LU_data[$portal_subdomain]['org_id'];
        $data = compact("org_id");
        $old_subscription_id = $wpdb->get_var("SELECT ID from wp_eot_subscriptions where org_id = ".$org_id);

        // check if there is an old subscription, otherwise dont need to get any courses
        if ($old_subscription_id)
        {
            $courses = LU_getCourses($portal_subdomain, 1, $data);
            // error_log(json_encode($courses));

            if($courses && count($courses) > 0)
            {
                $result['message'] .= "Processing courses... ";

                // go through each course and get the enrolled users / modules
                foreach ($courses as $course)
                {
                    $result['message'] .= "Course: " . $course['name'] . " ";
                    // get modules

                    $new_course = $wpdb->insert(TABLE_COURSES, array(
                        'course_name'=> esc_sql($course['name']),
                        'course_description'=> esc_sql($course['description_html']),
                        'subscription_id'=> get_new_id( 'SUB', $old_subscription_id ),
                        'org_id' => get_new_id( 'ORG', $org_id ),
                        'owner_id' => get_new_id( 'USER', $LU_data[$portal_subdomain]['user_id'] )
                    ));
                    $course_id = $wpdb->insert_id; // the new course id
                        
                    // make sure we inserted the new course properly    
                    if ($course_id)
                    {
                        $result['message'] .= "Course ID: $course_id. ";
                        $modules = LU_getModules($course['id'], $portal_subdomain, $data, 'page');//get modules using the old course id

                        // if no modules, make sure its an array so that it doesnt mess up the foreach loop below.
                        if (empty($modules))
                        {
                            $result['message'] .= "No modules found in course. ";
                            $modules = array();
                        }
                        else 
                        {
                            $result['message'] .= "processing " . count($modules) . " modules: ";
                            foreach ($modules as $module) {
                                $result['message'] .= $module['title'] . ", ";
                                $module_id = $wpdb->get_var("SELECT ID FROM ".TABLE_MODULES." WHERE title = '".$module['title']."' AND org_id = 0");
                                if($module_id)
                                {
                                    $mr = getResourcesInModule($module_id);
                                    if($mr && count($mr) > 0)
                                    {
                                        foreach($mr as $resource)
                                        {
                                            $wpdb->insert(TABLE_COURSE_MODULE_RESOURCES, array(
                                                'course_id' => $course_id,
                                                'module_id' => $module_id,
                                                'resource_id' => $resource['ID'],
                                                'type' => $resource['type']
                                            ));
                                        }
                                    }
                                }
                            }
                        }

                        $result['message'] .= "Published: " . $course['published_status_id'] . " Num Enrolled: " . $course['num_enrolled'];
                        // check if published, to know if we need to get enrolled users
                        if ($course['published_status_id'] == 'published' && $course['num_enrolled'] > 0)
                        {
                            $result['message'] .= "Enrolling users: ";
                            // get enrolled users
                            $enrolled_users = LU_getEnrollment($course['id'], $portal_subdomain, $data);
                            if($enrolled_users && count($enrolled_users) > 0)
                            {
                                foreach ($enrolled_users as $user) {
                                    $user_id = email_exists($user['email']);
                                    if($user_id)
                                    {
                                        $subscription = getSubscriptionByCourse($course_id);//gets the new subscription id
                                        $enroll = $wpdb->insert(TABLE_ENROLLMENTS,array(
                                            'course_id' => $course_id,
                                            'user_id'=> $user_id,
                                            'org_id' => get_new_id( 'ORG', $org_id ),
                                            'subscription_id' => $subscription['ID'],
                                            'email' => $user['email'],
                                            'date_enrolled' =>$user['date_enrolled'],
                                            'status' =>$user['status']

                                        ));
                                        $result['message'] .= $user['email'] . ", ";
                                    }
                                    else
                                    {
                                        $result['message'] .= "ERROR: " . $user['email'] . "doesn't exist. ";
                                    }
                                }
                            }
        //d($enrolled_users);
                        }

                    }
                }    
            }
            $result['message'] .= "All Done.<br>";
        }
        else
        {
            $result['status'] = 0;
            $result['message'] .= "ERROR: no old subscription ID.";
        }
    }
    else
    {
       // something went wrong when trying to create enrollments
      $result['status'] = 0;
      $result['message'] .= "ERROR: portal does not exist"; 
    }
    echo json_encode($result);
    die();
}