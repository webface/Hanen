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
			$response = getEotUsers($org_id); // gets the users for the org
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
				?>
					You do not have any staff accounts
				<?php
			}
			else if($learners == null)
			{
				?>
					There's something wrong. Please contact the administrator.
				<?php
			}
			else
			{
				$quizTableObj = new stdClass();
				$quizTableObj->rows = array();
		        $quizTableObj->headers = array(
					'Name' => 'quiz-title',
					'<div ' . hover_text_attr('The number of <b>Courses completed</b>.',true) .'>Courses Completed</div>' => 'center',
					'<div ' . hover_text_attr('The <b>Status</b> of the user:<ul><li><b>Complete.</b> User has successfully passed all exams.</li><li><b>In Progress.</b> The user has logged in and started viewing videos but has not completed all of the exams yet.</li><li><b>Not Started.</b> The user has not logged in yet.</li></ul>',true) .'>Status</div>' => 'center',
					'<div ' . hover_text_attr('This is a representation of the number of modules completed by the Staff member as a percentage of the total number of modules in the course.',true) .'>Progress</div>' => 'staff-progress'
				);
				$learners_user_info = array();
				$enrollments = getEnrollments(0, 0, $org_id); // All enrollments in the organization.
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
							$percentage_complete = $enrollment['percentage_complete'];
							if($status == "Completed" || $status == "Passed")						
							{
								$num_complete_enrollment++;
								$combined_percentage += 100;
							}
							else if($status == "Failed")
							{
								$status = 'In Progress';
								$combined_percentage += $percentage_complete;
							}
							else 
							{
								$combined_percentage += $percentage_complete;
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
					$quizTableObj->rows[] = array($name, $num_complete_enrollment . " / " . $num_enrollments, ($num_enrollments == $num_complete_enrollment ? "Complete" : $status), $percentage);
					$finished_user[] = $enrollment['email']; // Add the email to the finished user.
				}
			}
		?>
		<div class="breadcrumb">
			<?= CRUMB_DASHBOARD ?>              
			<?= CRUMB_SEPARATOR ?>  
			<?= CRUMB_STATISTICS ?>  
			<?= CRUMB_SEPARATOR ?>        
		    <span class="current">Complete and Incomplete Statistics</span>   
		</div>
		<div class="smoothness">
			<h1 class="article_page_title">Staff Statistics</h1>
			Here are statistics on the staff who have <b>complete and incomplete courses.</b>

			<h2>Summary</h2>

			<div class="smoothness">
				<div class="cell-row middle-row">
				    <div class="cell-caption">
				    	The total number of <b>Staff With Complete Courses</b>
				    </div>
				    <div class="cell-field number">
				    	<b><?= $num_staff_completed_courses; ?></b>
					</div>
				</div>
				<div class="cell-row">
					<div class="cell-caption">
						The total number of <b>Staff With Incomplete Courses</b>
					</div>
					<div class="cell-field number">
						<b><?= $num_staff_incomplete_course; ?></b>
					</div>
				</div>
			</div>
			<h2>Staff Statistics</h2>
<?= 
				CreateDataTable($quizTableObj, "100%", 25);
?>
		</div>
<?php
        }
        else 
        {
            echo "Unauthorized!";
        }
    }
    else 
    {
        echo "subscription ID does not belong to you";
    }
}
// Could not find the subscription ID
else 
{
	echo "Could not find the subscription ID";
}