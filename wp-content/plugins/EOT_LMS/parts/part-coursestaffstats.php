<?php 
	if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] > 0)
	{
	  	// Variable declaration
		global $current_user;
		$user_id = $current_user->ID; // Wordpress user ID
		$org_id = get_org_from_user ($user_id); // Organization ID
	 	$data = array( "org_id" => $org_id ); // to pass to our functions above
	 	$org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
		$course_id = filter_var($course_id = $_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
		$course_data = getCourse($course_id); // The course information
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
		$track_records = getTrack("all", $org_id); // All track records.
		$track_watchVideo = array();
		// Goes to each track records. Separating all other types except the login from the track_records array.
		foreach ($track_records as $key => $record) 
		{
			if($record['type'] == "watch_video")
			{
				array_push($track_watchVideo, $record['user_id']); // Save the user ID of the users who watch the video.
				unset($track_records[$key]); // Delete them from the array.
			}
			// Separate another type into another array of its own.
		}
		$login_users = array_column($track_records, 'user_id'); // Assuming the track records have users with the type login.
		$login_users = array_count_values($login_users); // Return the user ID and the key/times the user has logged in.
		$watched_users = array_count_values($track_watchVideo); // Return the user ID and the key/times the user has watch the module.

		if (isset($course_data['status']) && $course_data['status'] == 0)
		{
			// error received from getCourse
			wp_die($course_data['message'],'Error');
		}
		else 
		{
			if (isset($course_data['id']))
			{

				$course_name = $course_data['course_name'];
				$total_number_complete = $course_data['num_completed']; // The total number of staff who have completed the course.
				$total_number_not_started = $course_data['num_not_started']; // The total number of staff who haven't started yet
				$total_number_in_progress = $course_data['num_in_progress']; // The total number of staff who are in progress
				$total_number_passed = $course_data['num_passed']; // The total number of staff who have passed the course
			}
		}
		$calculated_num_completed = 0;
		$total_number_failed = 0;
        $enrollments =  getEnrollments($course_id, 0, 0, false); // Get all failed/passed enrollments in the course.
        foreach ($enrollments as $enrollment) 
        {
        	$status = $enrollment['status'];
        	if($status == "completed" || $status == "passed")
        	{
        		$calculated_num_completed++; // people who passed also completed the course.
        	}
        	else if($status == "failed")
        	{
        		$total_number_failed++;
        	}
        }
        $total_number_of_staff =  count($enrollments); // The total number of staff enrolled in the course.
		// Variable initialisation 
		$percentage_completed = 0; // The percentage of staff who logged in once.
		$percentage_not_started = 0;  // The percentage of staff who logged in once.
		$percentage_number_in_progress = 0; // The percentage of staff who are in progress.
		$percentage_number_passed = 0; // The percentage of staff who passed in this course.
		$percentage_number_failed = 0; // The percentage of staff who failed in this course.

		// This calculates the percentage for the progressbars.
		if($total_number_of_staff > 0) // Can't be divided by 0.
		{
			$percentage_completed = (($total_number_complete / $total_number_of_staff) * 100); 
			$percentage_not_started = (($total_number_not_started / $total_number_of_staff) * 100); 
			$percentage_number_in_progress = (($total_number_in_progress / $total_number_of_staff) * 100); 
			$percentage_number_passed = (($total_number_passed / $total_number_of_staff) * 100); 
			$percentage_number_failed = (($total_number_failed / $total_number_of_staff) * 100); 

			$calculated_percentage_completed = (($calculated_num_completed / $total_number_of_staff) * 100);
		}
?>
		<div class="breadcrumb">
		    <?= CRUMB_DASHBOARD ?>              
		    <?= CRUMB_SEPARATOR ?>  
		   	<?= CRUMB_STATISTICS ?>  
		   	<?= CRUMB_SEPARATOR ?>  
	   	    <b>Staff statistics for "<?= $course_name ?>"</b>
		</div>
		<div class="smoothness">
			<h1 class="article_page_title">Course Statistics for "<?= $course_name ?>"</h1>
			Here are statistics on the staff taking the <b><?= $course_name ?></b> Course.
			<h2>Summary</h2>
			<div class="cell-row middle-row">
				<div class="cell-caption">
					<img src="<?= get_template_directory_uri() . "/images/info-sm.gif"?>" title="The total number of staff (in Staff Groups) who have been assigned this Course." class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('The total number of staff (in Staff Groups) who have been assigned this Course.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"> Staff given this <b>Course</b>
				</div>
				<div class="cell-field number">
					<b><?= $total_number_of_staff ?></b>
				</div>
			</div>
			<div class="cell-row">
				<div class="cell-caption">
					<img src="<?= get_template_directory_uri() . "/images/info-sm.gif"?>" title="" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('The total number of staff who have passed all the required modules in this Course.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"> Staff who have <b>Completed</b> this Course
				</div>
				<div class="cell-field number">
					<b><?= $calculated_num_completed ?></b>
				</div>
				<?= eotprogressbar('12em', $calculated_percentage_completed, true); ?>
			</div>
			<h2>Staff Statistics</h2>
        		<?php
        			if($enrollments)
        			{
        				$quizTableObj = new stdClass();
        				$quizTableObj->rows = array();
				        $quizTableObj->headers = array(
      						'Name' => 'quiz-title',
                                            'Passed'=>'center',
                                            'Failed'=>'center',
                                            'Logins'=>'center',
                                            'Views'=>'center',
          					'<div ' . hover_text_attr('The enrollment status in this course. This can be the following statuses: Not started, in progress, completed, passed, failed or pending review',true) .'>Status</div>' => 'center',
          					'<div ' . hover_text_attr('This is a representation of the number of modules completed by the Staff member as. A percentage of the total number of modules in the course.',true) .'>Progress</div>' => 'staff-progress'
    					);
	        		   /* 
	        			* This goes through all the enrollments and display a table 
	        			* with Name, Status and the Progress of each staff in the course
	        			*/
        				foreach($enrollments as $enrollment)
        				{
							$name = get_user_meta ( $enrollment['user_id'], "first_name", true) . " " . get_user_meta ( $enrollment['user_id'], "last_name", true);
							$percentage_complete = (isset($enrollment['percentage'])) ? $enrollment['percentage'] : $enrollment['percentage_complete']; // Check if percentage exsist for student who failed a course. Otherwise show percentage complete.
							$status = formatStatus($enrollment['status']);
							$login_count = ($login_users[$enrollment['user_id']]) ? $login_users[$enrollment['user_id']] : 0; // Number of times the user has log in to our system.
							$view_count = ($watched_users[$enrollment['user_id']]) ? $watched_users[$enrollment['user_id']] : 0; // Number of times the user has watch the module.
							if($status == "Failed")
							{
								$status = 'In Progress';
							}
							else if($status == "Completed" || $status == "Passed")
							{
								$percentage_complete = 100;
							}
							$percentage = eotprogressbar('8em', $percentage_complete, true);
	        				$quizTableObj->rows[] = array($name,'0/0',0,$login_count, $view_count, $status, $percentage);
        				}
        				CreateDataTable($quizTableObj);
        			}
        			else
        			{
        				?>
        					There are no staff registered in this course.
        				<?php
        			}
        		?>
		</div>

<?php
	}
	else
	{
?>
	<div class="errorboxcontainer">
        <div class="error-tl">
          <div class="error-tr"> 
	            <div class="error-bl">
	              	<div class="error-br">
	                	<div class="errorbox">You do not have access to these Statistics.</div>             
	     	 		</div>
	            </div>
          </div>
        </div>
  	</div>
<?php
	}
?>