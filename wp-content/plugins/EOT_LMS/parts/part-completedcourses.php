<?php
// Check if the subscription ID is valid.
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0) 
{
	$true_subscription = verifyUserAccess();
    if (isset($true_subscription['status']) && $true_subscription['status']) 
    {
        if (current_user_can("is_director")) 
        {
		  	// Variable declaration
			global $current_user;
			$user_id = $current_user->ID; // Wordpress user ID
			$org_id = get_org_from_user ($user_id); // Organization ID
		 	$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // Subscription ID
		 	$courses = getCourses(0, $org_id, $subscription_id); // All the courses in the organization.
			$num_staff_completed_courses = 0; // Number of staff who have completed all their courses
			//$response = getEotUsers($org_id); // gets the users for the org
			$response = getUsersInSubscription($subscription_id);
			if ($response['status'] == 1)
			{
				$staff_accounts = $response['users'];
				$learners = filterUsers($staff_accounts, 'learner'); // only the learners
			}
			else
			{
				$users = array();
				$learners = array();
			}
		   	$num_staff_incomplete_course = 0; // # of staff with incomplete course.

			if(count($learners) < 1)
			{
				echo __("You do not have any staff accounts", "EOT_LMS");
			}
			else if($learners == null)
			{
				echo __("There's something wrong. Please contact the administrator.", "EOT_LMS");
			}
			else
			{
				$quizTableObj = new stdClass();
				$quizTableObj->rows = array();
		        $quizTableObj->headers = array(
					'Name' => 'quiz-title',
					'<div ' . hover_text_attr(__("The number of <b>Courses completed</b>.", "EOT_LMS"),true) .'>' . __("Courses Completed", "EOT_LMS") . '</div>' => 'center',
					'<div ' . hover_text_attr(__("The <b>Status</b> of the user:<ul><li><b>Complete.</b> User has successfully passed all exams.</li><li><b>In Progress.</b> The user has logged in and started viewing videos but has not completed all of the exams yet.</li><li><b>Not Started.</b> The user has not logged in yet.</li></ul>", "EOT_LMS"), true) .'>' . __("Status", "EOT_LMS") . '</div>' => 'center',
					'<div ' . hover_text_attr(__("This is a representation of the number of modules completed by the Staff member as a percentage of the total number of modules in the course.", "EOT_LMS"), true) .'>' . __("Progress", "EOT_LMS") . '</div>' => 'staff-progress'
				);
				$learners_user_info = array();
				$enrollments = getEnrollments(0, 0, $org_id, false, $subscription_id); // All enrollments in the organization in the sub.
//d($learners, $enrollments);
/*
				foreach ($learners as $learner)
				{
					$user_id = $learner['ID']; // User ID
					$name = $learner['first_name'] . " " . $learner['last_name']; // User first and last name
					$num_enrollments = 0;  // User number of enrollments
					$num_complete_enrollment = 0; // User number of completed enrollments
					$combined_percentage = 0; // keeps track of percentage complete for all courses the user is enrolled in.
					foreach ($enrollments as $enrollment) 
					{
						if( $enrollment['user_id'] == $user_id )
						{	
							$num_enrollments++;
							$status = formatStatus($enrollment['status']); // The status of the enrollment
							// Check if this enrollment is completed
							if($status == "Completed" || $status == "Passed")						
							{
								$num_complete_enrollment++;
								$combined_percentage += 100;
							}
							else if($status == "Failed")
							{
								$status = 'In Progress';
								$combined_percentage += 0;
							}
							else 
							{
								$combined_percentage += 0;
							}
						}
					}
					$overallpercent = $num_enrollments ? ($combined_percentage/($num_enrollments*100))*100 : 0; // The overall percentage for all courses
					
					// This determines if all the courses has been completed.
					if($num_enrollments == $num_complete_enrollment)
					{
						$num_staff_completed_courses++;
					}
					else
					{
						// Count how many staff with incomplete course.
						$num_staff_incomplete_course++;
					}
					
					// make sure that status doesn't show not started if the user has some progress
					if ($status == "Not Started" && $overallpercent > 0)
					{
						$status = "In Progress";
					}
					
					$percentage = eotprogressbar('8em', $overallpercent, true);
					$quizTableObj->rows[] = array($name, $num_complete_enrollment . " / " . $num_enrollments, ($num_enrollments == $num_complete_enrollment ? __("Complete", "EOT_LMS") : __("$status", "EOT_LMS")), $percentage);
					$finished_user[] = $enrollment['email']; // Add the email to the finished user.
				}
*/		

				$incomplete_users = array(); // Users with incomplete course.
				$completed_users = array(); // Users with complete course. 
				$user_num_enrolled = array(); // an associative array $user_id => num enrolled courses
				$user_num_passed = array(); // an associative array $user_id => num passed courses

				// go through each course to get the quizzes in that course
				foreach ($courses as $course) 
				{
					$quizzes = getQuizzesInCourse($course->ID);
					$enrolled_users = array(); // the enrolled users in this course

					if ( $enrollments )
					{
						// check if the user is currently enrolled in this course
						foreach ( $enrollments as $enrollment )
						{
							if( $enrollment['course_id'] == $course->ID )
							{
								array_push( $enrolled_users, $enrollment['user_id'] );
							}
						}
					}

					// check if the enrolled users in this course passed it or not.
					if ( $enrolled_users )
					{
//d($course->ID, $enrolled_users);
						foreach ($enrolled_users as $user_id)
						{
							$track_quiz_attempts = array();
							$trackPassed = array();
							$quizPassed = array(); // needed to verify and remove quizzes passed more than once  
							$track_quizzes = getAllQuizAttempts($course->ID, $user_id, $quizzes); // All quiz attempts for this course for this user

							foreach ($track_quizzes as $key => $record)
							{
								if($record['passed'] == 1 && (!isset($quizPassed[$record['quiz_id']]) || $quizPassed[$record['quiz_id']] != $record['user_id'])) //make sure the quiz has not been recorded as passed previously
								{
									$quizPassed[$record['quiz_id']] = $record['user_id'];
									array_push($trackPassed, $record['user_id']); // Save the user ID of the users who failed the quiz.
								}
								array_push($track_quiz_attempts, $record['user_id']); // Save the user ID of the users who failed the quiz. 	
							}

//d($trackPassed, $track_quiz_attempts);
							// check if they passed the quiz
							if ( in_array( $user_id, $trackPassed ) && !in_array( $user_id, $incomplete_users ) )
							{
								array_push( $completed_users, $user_id );

								// if passed, then add to user_num_passed array.
								if ( isset($user_num_passed[$user_id] ) )
								{
									$user_num_passed[$user_id] += 1;
								} 
								else
								{
									$user_num_passed[$user_id] = 1;
								}
							}
							else
							{
								if ( !in_array( $user_id, $incomplete_users ) )
								{
									array_push( $incomplete_users, $user_id );	
								} 
							}

							// regardless of pass/fail. add user to enrolled array
							if ( isset( $user_num_enrolled[$user_id] ) )
							{
								$user_num_enrolled[$user_id] += 1;
							}
							else
							{
								$user_num_enrolled[$user_id] = 1;
							}
						}
					}
				}
//d($completed_users, $incomplete_users, $user_num_enrolled, $user_num_passed);		


				// dedupe complete and incomplete users
				$completed_users = array_unique( $completed_users );
				$incomplete_users = array_unique( $incomplete_users );

				// remove user ids from complete if they have any incomplete courses
				$complete_users = array_diff( $completed_users, $incomplete_users );

				$num_staff_completed_courses = count( $complete_users );
				$num_staff_incomplete_course = count( $incomplete_users );

				// go through each learner and add them to the table
				foreach ($learners as $learner)
				{
					$user_id = $learner['ID']; // User ID
					$name = $learner['first_name'] . " " . $learner['last_name']; // User first and last name
					$num_complete_enrollment = isset( $user_num_passed[$user_id] ) ? $user_num_passed[$user_id] : 0;
					$num_enrollments = isset( $user_num_enrolled[$user_id] ) ? $user_num_enrolled[$user_id] : 0;
					if ( $num_enrollments == 0 )
					{
						$status = __("Not Enrolled", "EOT_LMS");
					}
					else if ( $num_enrollments == $num_complete_enrollment )
					{
						$status = __("Complete", "EOT_LMS");
					}
					else
					{
						$status = __("In Progress", "EOT_LMS");
					}

					$overallpercent = $num_enrollments ? ($num_complete_enrollment/$num_enrollments)*100 : 0;
					$percentage = eotprogressbar('8em', $overallpercent, true);
					$quizTableObj->rows[] = array(
						$name, 
						$num_complete_enrollment . " / " . $num_enrollments, 
						$status, 
						$percentage
					);
				}
			}
?>
		<div class="breadcrumb">
			<?= CRUMB_DASHBOARD ?>              
			<?= CRUMB_SEPARATOR ?>  
			<?= CRUMB_STATISTICS ?>  
			<?= CRUMB_SEPARATOR ?>        
		    <span class="current"><?= __("Complete and Incomplete Statistics", "EOT_LMS") ?></span>   
		</div>
		<div class="smoothness">
			<h1 class="article_page_title"><?= __("Staff Statistics", "EOT_LMS") ?></h1>
			<?= __("Here are statistics on the staff who have <b>complete and incomplete courses.</b>", "EOT_LMS") ?>

			<h2><?= __("Summary", "EOT_LMS") ?></h2>

			<div class="smoothness">
				<div class="cell-row middle-row">
				    <div class="cell-caption">
				    	<?= __("The total number of <b>Staff With Complete Courses", "EOT_LMS") ?></b>
				    </div>
				    <div class="cell-field number">
				    	<b><?= $num_staff_completed_courses; ?></b>
					</div>
				</div>
				<div class="cell-row">
					<div class="cell-caption">
						<?= __("The total number of <b>Staff With Incomplete Courses", "EOT_LMS") ?></b>
					</div>
					<div class="cell-field number">
						<b><?= $num_staff_incomplete_course; ?></b>
					</div>
				</div>
			</div>
			<h2><?= __("Staff Statistics", "EOT_LMS") ?></h2>
<?php 
				CreateDataTable($quizTableObj, "100%", 25);
?>
		</div>
<?php
        }
        else 
        {
            echo __("Unauthorized!", "EOT_LMS");
        }
    }
    else 
    {
        echo __("subscription ID does not belong to you", "EOT_LMS");
    }
}
// Could not find the subscription ID
else 
{
	echo __("Could not find the subscription ID", "EOT_LMS");
}
?>