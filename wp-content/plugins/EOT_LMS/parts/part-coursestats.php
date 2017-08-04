<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>         
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_STATISTICS ?>          
    <?= CRUMB_SEPARATOR ?>  
    <span class="current">Course Stats</span>     
</div>
<?php
global $current_user;
$user_id = $current_user->ID;
$page_title = "Stats";
// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess();
// Check if the subscription ID is valid.
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0) 
{

    if (isset($true_subscription['status']) && $true_subscription['status']) 
    {
        if (current_user_can("is_director"))
        {
          
            if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] > 0)
            {
                $org_id = get_org_from_user ($user_id); // Organization ID
	 	$data = array( "org_id" => $org_id ); // to pass to our functions above
		$course_id = filter_var($course_id = $_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
		$course_data = getCourse($course_id); // The course information
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                if (isset($course_data['status']) && $course_data['status'] == 0)
		{
			// error received from getCourse
			wp_die($course_data['message'],'Error');
		}
		else 
		{
			if (isset($course_data['ID']))
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
		}  else {
                        $calculated_percentage_completed = 0;
                }
?>
		<div class="smoothness">
			<h1 class="article_page_title">Course Statistics for "<?= $course_name ?>"</h1>
			Here are statistics on the <b><?= $course_name ?></b> Modules.
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
                        <h2>Quiz Success Rate</h2>
                        <p>For quizzes with a low success rate, you may want to go over these topics in greater depth during your on-site training.</p>
<?php 
                        $quizzes = getQuizzesInCourse($course_id);
                        $track_quizzes = getAllQuizAttempts($course_id);//All quiz attempts for this course
                        $track_passed = array();
                        $track_quiz_attempts = array();
                        foreach ($track_quizzes as $key => $record) {
                            if($record['passed'] == 1)
                            {
                              array_push($track_passed, $record['quiz_id']); // Save the user ID of the users who failed the quiz.
                              //unset($track_quizzes[$key]); // Delete them from the array.
                            }
                           array_push($track_quiz_attempts, $record['quiz_id']);
                        }
                        $passed_users = array_count_values($track_passed);
                        $attempt_count = array_count_values($track_quiz_attempts);
//d($track_quizzes, $passed_users, $attempt_count);
                        if ($quizzes) 
                        {  
                            // Tables that will be displayed in the front end.
                            $quizTableObj = new stdClass();
                            $quizTableObj->rows = array();
                            $quizTableObj->headers = array(
                                'Quiz Name' => 'left',
                                'Success Rate' => 'center',
                                'Percentage' => 'center'
                            );

                            // Creating rows for the table
                            foreach ($quizzes as $quiz) 
                            {

                                $time_limit = date('i', strtotime($quiz['time_limit']));
                                $passed_count = isset($passed_users[$quiz['ID']])? $passed_users[$quiz['ID']] : 0;//Number of passes
                                $attempts = isset($attempt_count[$quiz['ID']]) ? $attempt_count[$quiz['ID']] : 0;//Number of quiz attempts
                                $percentage = $attempts>0?(($passed_count/$attempts)*100):0;

                                $quizTableObj->rows[] = array(
                                    ' <span>' . stripslashes($quiz['name']) . '</span>',
                                    $passed_count.'/'.$attempts,
                                    eotprogressbar('12em', $percentage, true)
                                    );
                            }
                            CreateDataTable($quizTableObj); // Print the table in the page
                        }
?>
                        <h2>Video Views</h2>
<?php 
                        $videos =  getResourcesInCourse($course_id, 'video');
                        $custom_videos = getResourcesInCourse($course_id, 'custom_video');
                        $all_videos = array_merge($videos, $custom_videos);
                        $track_records = getAllTrack($org_id); // All track records.
//d($videos,$custom_videos,$all_videos,$track_records);
                        $track_watchVideo = array();
                        foreach ($track_records as $key => $record) {
                            if($record['type'] == "watch_video")
                            {
                                    array_push($track_watchVideo, $record['video_id']); // Save the ID of the video.
                                    //unset($track_records[$key]); // Delete them from the array.
                            }
                            if($record['type'] == "watch_custom_video")
                            {
                                    array_push($track_watchVideo, $record['video_id']); // Save the ID of the video.
                                    //unset($track_records[$key]); // Delete them from the array.
                            }
                        }
                        $views = array_count_values($track_watchVideo);
                        
                        $videosTableObj = new stdClass();
                            $videosTableObj->rows = array();
                            $videosTableObj->headers = array(
                                'Video Title' => 'left',
                                'Views' => 'center'
                            );
                        // Creating rows for the table
                            foreach ($all_videos as $video) 
                            {

                                
                                $view_count = isset($views[$video['ID']]) ? $views[$video['ID']] : 0;//Number of video views
                                

                                $videosTableObj->rows[] = array(
                                    ' <span>' . stripslashes($video['name']) . '</span>',
                                    $view_count
                                    );
                            }
                         CreateDataTable($videosTableObj); // Print the table in the page
?>
                         <h2>Resource Views</h2>
<?php
                        $resources = array_merge(getResourcesInCourse($course_id, 'doc'));
                        d($resources);
                        $track_download = array();
                        foreach ($track_records as $key => $record) {
                            if($record['type'] == "download_resource")
                            {
                                    array_push($track_download, $record['resource_id']); // Save the ID of the video.
                                    //unset($track_records[$key]); // Delete them from the array.
                            }
                        }
                        $downloads = array_count_values($track_download);
                        $resourceTableObj = new stdClass();
                            $resourceTableObj->rows = array();
                            $resourceTableObj->headers = array(
                                'Name' => 'left',
                                'Downloads' => 'center'
                            );
                        // Creating rows for the table
                            foreach ($resources as $resource) 
                            {

                                
                                $download_count = isset($downloads[$resource['ID']]) ? $downloads[$resource['ID']] : 0;//Number of video views
                                

                                $resourceTableObj->rows[] = array(
                                    ' <span>' . stripslashes($resource['name']) . '</span>',
                                    $download_count
                                    );
                            }
                         CreateDataTable($resourceTableObj); // Print the table in the page
?>
                </div>
<?php                }
            else 
            {
                echo "You dont have a valid course ID";
            }
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
?>