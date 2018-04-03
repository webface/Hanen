<?php

	$action = isset( $_GET['action'] ) ? filter_var( $_GET['action'], FILTER_SANITIZE_STRING ) : '';

    $headers = array(
        "From: " . get_bloginfo('admin_email'),
        "Content-Type: text/html; charset=UTF-8"
    );

	if ( $action == 'invitation_code' )
	{
		// message all the people who got an invitation code but are not enrolled in a course

		// get all entries from registration form with camp invitation code.
		$form_id = 3;
		$search_criteria['field_filters'][] = array( 'key' => '10', 'operator' => 'isnot', 'value' => '' ); 
		$entries = GFAPI::get_entries( $form_id, $search_criteria );
d($entries);

		// go through each user and check if they are enrolled in a course
		foreach ( $entries as $entry )
		{
			echo 'checking: ' . $entry[2] . '<br>';
			$code = $entry[10];
/*
            $sql = 'SELECT * FROM ' . TABLE_INVITATIONS . ' WHERE code ="'.$code.'"';
if ( SHOW_SQL ) error_log("hagai_debug: get_row-> $sql");
            $current = $wpdb->get_row( $sql, ARRAY_A );//codes should be unique to user
            $subscription_id = isset($current['subscription_id']) ? $current['subscription_id'] : '';
			$user = get_user_by( 'email', $entry[2] );
			$user_id = $user->ID;
			$enrollments = getEnrollmentsByUserId($user_id, "all", $subscription_id);
d($current, $enrollments);

			$enrolled = FALSE;

			// check if user is enrolled
			if ( $enrollments )
			{
				// check if there is a specific course that the user is supposed to be in.
				if ( isset( $current['course_id'] && !empty( $current['course_id'] ) )
				{
					foreach ( $enrollments as $enrollment )
					{
						// check that the user is enrolled in the same course
						if ( $enrollment['course_id'] == $current['course_id'] )
						{
							$enrolled = TRUE;
							break;
						}
					}
				}
			}
			else 
			{
				// user is not enrolled, enroll him then send an email with appology.
				// check if there is a specific course that the user is supposed to be in.
				if ( isset( $current['course_id'] && !empty( $current['course_id'] ) )
				{

				}
			}
*/

            $sql = 'SELECT * FROM ' . TABLE_INVITATIONS . ' WHERE code ="'.$code.'"';
if ( SHOW_SQL ) error_log("after_submission_register: get_row-> $sql");
            $current = $wpdb->get_results( $sql, ARRAY_A ); //codes should be unique to user unless there is no course_id for the user
d($current);
 
            $num_rows = count($current);
            $org_id = 0;
            $subscription_id = 0;
            $course_id = 0;
            $type = '';
            $invitation_id = 0;
            $user_email = 0;

d($num_rows);
            if ( $num_rows == 1 )
            {
error_log("current row: " . json_encode($current));
            	// its a unique code so process accordingly
	            $type = isset($current[0]['type']) ? $current[0]['type'] : '';
	            $subscription_id = isset($current[0]['subscription_id']) ? $current[0]['subscription_id'] : '';
	            $invitation_id = isset($current[0]['ID']) ? $current[0]['ID'] : '';
	            $org_id = isset($current[0]['org_id']) ? $current[0]['org_id'] : '';
	            $course_id = isset($current[0]['course_id']) ? $current[0]['course_id'] : 0;
				$user_email = isset($current[0]['user_email']) ? $current[0]['user_email'] : $entry['2'];
            }
            else if ( $num_rows > 1 )
            {
            	// multiple possibilities so go through each code till we find the right user
            	// type == user in this case but no course ID.
            	foreach ( $current as $invitation )
            	{
            		// check the type of invitation it is.
            		$type = isset($invitation['type']) ? $invitation['type'] : '';
            		if ( $type == 'user' && $invitation['user_email'] == $entry['2'] )
            		{
			            $subscription_id = isset($invitation['subscription_id']) ? $invitation['subscription_id'] : '';
			            $invitation_id = isset($invitation['ID']) ? $invitation['ID'] : '';
			            $org_id = isset($invitation['org_id']) ? $invitation['org_id'] : '';
			            $course_id = isset($invitation['course_id']) ? $invitation['course_id'] : 0;
						$user_email = isset($invitation['user_email']) ? $invitation['user_email'] : $entry['2'];
d($invitation);						
error_log("invitation: " . json_encode($invitation));
            		}
            		else if ( $type == 'org' || $type == 'course' )
            		{
			            $subscription_id = isset($invitation['subscription_id']) ? $invitation['subscription_id'] : '';
			            $invitation_id = isset($invitation['ID']) ? $invitation['ID'] : '';
			            $org_id = isset($invitation['org_id']) ? $invitation['org_id'] : '';
			            $course_id = isset($invitation['course_id']) ? $invitation['course_id'] : 0;
						$user_email = isset($invitation['user_email']) ? $invitation['user_email'] : $entry['2'];
            		}
            	}
            }
            else
            {
            	// code not found. We have a problem.
            }

echo "Invitation Data - type: $type, sub_id: $subscription_id, ID: $invitation_id, org: $org_id, course: $course_id, email: $user_email<br>";

            // check if user is supposed to be enrolled in a course or just the org
			if( $course_id )
            {
            	// make sure user in enrolled in course
				$user = get_user_by( 'email', $entry[2] );
				$user_id = $user->ID;
				$enrollments = getEnrollmentsByUserId($user_id, "all", $subscription_id);
d($enrollments);

				$enrolled = FALSE;

				// check if user is enrolled
				if ( $enrollments )
				{
					foreach ( $enrollments as $enrollment )
					{
						// check that the user is enrolled in the same course
						if ( $enrollment['course_id'] == $course_id )
						{
							$enrolled = TRUE;
							break;
						}
					}
				}
echo "Got course ID: $course_id checking if enrolled: $enrolled<br>";

				if ( !$enrolled )
				{
					// enroll the user if org has enough staff accounts
		            if( org_has_maxed_staff($org_id, $subscription_id) )
		            { // org doesn't have enough staff credits, so put user in pending table
echo "org has maxed its staff: adding to pending table<br><br>";
		                $data=array(
		                    'user_id' => $user_id,
		                    'org_id' => $org_id,
		                    'subscription_id' => $subscription_id,
		                    'course_id' => $course_id,
		                    'user_email' => $user_email
		                );
		                $insert_user_into_pending = $wpdb->insert(TABLE_PENDING_SUBSCRIPTIONS,$data,array('%d','%d','%d','%d','%s'));
		                
		                // if user was successfully added to pending subscriptions table then email the director
		                if($insert_user_into_pending !== FALSE) 
		                {
		                    $sql = "SELECT u.* FROM " . TABLE_USERS . " u LEFT JOIN " . TABLE_SUBSCRIPTIONS . " s ON u.ID = s.manager_id WHERE s.ID = $subscription_id";
if ( SHOW_SQL ) error_log("after_submission_register: get_row-> $sql");
		                    $result = $wpdb->get_row( $sql, ARRAY_A );
		                    
		                    if ( $result )
		                    {
			                    $to = $result['user_email']; // the director
			                    $directors_name = $result['display_name'];
			                    $subject = "Max staff reached on Expert Online Training";
			                    $message = "Dear $direcors_name,<br><br>As per your invitation code, a new user has subscribed on Expert Online Training but your organization has reached the maximum number of staff you can have. You need to add more staff to your subscription in order for this staff member to successfully enroll in your organization.<br><br><a href='https://www.expertonlinetraining.com/dashboard/?part=upgradesubscription&subscription_id=$subscription_id'>Upgrade Now</a><br><br>";
			                    $message .= "User's Name: ".$entry['1.3']." ".$entry['1.6']."<br>";
			                    $message .= "User's email: ".$user_email;
			                    $message .= "<br><br>Regads,<br><br>The Expert Online Training Team";
			                    if(wp_mail($to, $subject, $message, $headers))
			                    {
			                        echo "ERROR: This organization has reached its maximum number of staff that can be registered. Don't sweat, we have contacted the director to increase the number of staff and have recorded your info. We will create your account once the organization upgrades their number of staff accounts and notify you when you can log in.";
			                    }
			                    else
			                    {
			                        echo "ERROR: We were not able to contact your director to inform them that they have reached the maximum number of staff they can add. Please contact us toll free at 1-877-390-2267 or info@expertonlinetraining.com to let us know about this problem";
			                        error_log( "after_submission_register: couldn't contact director to inform them they reached max num of accounts. data = " . json_encode( $data ) );
			                    }
		                    }
		                }
		            }
		            else
		            { // org has enough staff credits
		                update_user_meta($user_id, 'org_id', $org_id);
echo "enrolling user ID: $user_id into course_id: $course_id and sending email<br><br>";
	                    $course = getCourse($course_id);
	                    $course_name = $course['course_name'];
	                    $data = compact("org_id", "course_id", "course_name", "subscription_id");
	                    
	                    $enrolled = enrollUserInCourse($user_email, $data);
	                    if ( isset( $enrolled['status'] ) && !$enrolled['status'] )
	                    {
	                    	//error enrolling student. Send me an email:
	                    	error_log( "after_submission_register: Couldn't enroll user: $user_email data: " . json_encode( $data ) );
	                    	$to = get_option( 'admin_email', DEFAULT_EMAIL );
	                    	$msg = "Tried to auto enroll a user based on the code but couldn't do it.<br><br>after_submission_register: Couldn't enroll user: $user_email data: " . json_encode( $data );
	                    	wp_mail($to, "EOT ERROR: cant enroll $user_email", $msg, $headers);
	                    }
	                    else
	                    {
	                    	// enrolled user. Send them an appology email.
	                    	$to = $user_email;
	                    	$subject = "You have been enrolled in $course_name on expertonlinetraining.com";
	                    	$msg = "Hi " . $entry['1.3'] . ",<br><br>";
	                    	$msg .= "Your camp director originally enrolled you in $course_name on www.expertonlinetraining.com but we neglected to actually add you to that course. Oops...<br><br>";
	                    	$msg .= "We're very sorry for the trouble and frustration this may have caused you and we've now enrolled you into that course.<br><br>";
	                    	$msg .= "If you log back into www.expertonlinetraining.com you should be able to complete the course.<br><br>";
	                    	$msg .= "If you have any more difficulties with our online training, please contact us toll-free at 1-877-390-2267 or info@expertonlinetraining.com from 9 - 5 M - F EDT.<br><br>";
	                    	$msg .= "With appologies<br><br>";
	                    	$msg .= "The Expert Online Training Team";
	                    	wp_mail($to, $subject, $msg, $headers);
	                    }
		            }

		            // if the invitation was to a user specifically, we want to track when the account was created so update the invitations table to include a date_signed_up. NOTE: user may not have been added to the ORG if max staff accounts was reached.
		            if($type == 'user')
		            {
		            	$update=$wpdb->update(
		            		TABLE_INVITATIONS,
		            		array(
		            			'date_signed_up' => current_time('Y-m-d')
		            		),
		            		array(
		            			'ID' => $invitation_id
		            		)
		            	);
		            }
				}

            }
            else
            {
            	// no course id so just make sure user is in org/sub
				$user = get_user_by( 'email', $entry[2] );
				$user_id = $user->ID;
				add_user_in_subscription( $subscription_id, $user_id );
				update_user_meta($user_id, 'org_id', $org_id);
echo 'added user to subscription : ' . $user->user_email . " with ID: $user_id to org: $org_id in sub: $subscription_id<br><br>";
            }
		} // end foreach entry
	} // end action invitation_code
	else
	{
		echo "Nothing to do here";
	}