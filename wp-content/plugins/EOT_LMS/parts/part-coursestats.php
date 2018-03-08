<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>         
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_STATISTICS ?>          
    <?= CRUMB_SEPARATOR ?>  
    <span class="current"><?= __("Course Stats", "EOT_LMS"); ?></span>     
</div>
<?php

// enqueue required javascripts
wp_enqueue_script('datatables-buttons', get_template_directory_uri() . '/js/dataTables.buttons.min.js', array('datatables-js'), '1.2.4', true);
wp_enqueue_script('buttons-flash', get_template_directory_uri() . '/js/buttons.flash.min.js', array(), '1.2.4', true);
wp_enqueue_script('jszip', get_template_directory_uri() . '/js/jszip.min.js', array(), '2.5.0', true);
wp_enqueue_script('vfs-fonts', get_template_directory_uri() . '/js/vfs_fonts.js', array(), '0.1.24', true);
wp_enqueue_script('buttons-html5', get_template_directory_uri() . '/js/buttons.html5.min.js', array(), '1.2.4', true);
wp_enqueue_script('buttons-print', get_template_directory_uri() . '/js/buttons.print.min.js', array(), '1.2.4', true);

global $current_user;
$user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
$page_title = __("Stats", "EOT_LMS");

// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess();

// Check if the subscription ID is valid.
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0) 
{
    if (isset($true_subscription['status']) && $true_subscription['status']) 
    {
        if (current_user_can("is_director")) 
        {
            if (isset($_REQUEST['course_id']) && $_REQUEST['course_id'] > 0) 
            {
                $org_id = get_org_from_user($user_id); // Organization ID
                $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT); // The course ID
                $course_data = getCourse($course_id); // The course information
                $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                if (isset($course_data['status']) && $course_data['status'] == 0) 
                {
                    // error received from getCourse
                    wp_die($course_data['message']);
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
                $total_number_failed = 0;
                $enrollments = getEnrollments($course_id, 0, 0, false); // Get all failed/passed enrollments in the course.
                $quizzes_in_course = getQuizzesInCourse($course_id);
                $track_quizzes = array();
                if($quizzes_in_course)
                {
                    $track_quizzes = getAllQuizAttempts($course_id); //All quiz attempts for this course
                }
                $num_quizzes_in_course = count($quizzes_in_course);
                $videos = getResourcesInCourse($course_id, 'video');
                $custom_videos = getResourcesInCourse($course_id, 'custom_video');
                $all_videos = array_merge($videos, $custom_videos);
                $track_records = getAllTrack($org_id); // All track records.
                $track_watchVideo = array();
                $track_login = array();
                $track_watch_customVideo = array();
                foreach ($track_records as $key => $record) 
                {
                    if ($record['type'] == "watch_video") 
                    {
                        array_push($track_watchVideo, $record['video_id']); // Save the ID of the video.
                        //unset($track_records[$key]); // Delete them from the array.
                    }
                    if ($record['type'] == "watch_custom_video") 
                    {
                        array_push($track_watch_customVideo, $record['video_id']); // Save the ID of the video.
                        //unset($track_records[$key]); // Delete them from the array.
                    }
                }
                $trackPassed = array(); // Track passed for each users
                $trackedQuizPassed = array(); // Track passed for each quizzes.
                $track_quiz_attempts = array();
                $quizPassed = array();//needed to verify and remove quizzes passed more than once
                foreach ($track_quizzes as $key => $record) 
                {
                    //make sure the quiz has not been already passed 
                    if ($record['passed'] == 1 )
                    {
                        // Save the user ID of the users who failed the quiz.
                        if((!isset($quizPassed[$record['quiz_id']]) || ($quizPassed[$record['quiz_id']] != $record['user_id'])))
                        {
                            $quizPassed[$record['quiz_id']] = $record['user_id'];
                            array_push($trackPassed, $record['user_id']); 
                        }
                        array_push($trackedQuizPassed, $record['quiz_id']);
                    }

                    array_push($track_quiz_attempts, $record['quiz_id']); // Save the user ID of the users who failed the quiz. 
                }
                $passed_users = array_count_values($trackPassed);
                $passed_quiz = array_count_values($trackedQuizPassed);
                $attempt_count = array_count_values($track_quiz_attempts);
                $login_users = array_count_values($track_login); // Return the user ID and the key/times the user has logged in.
                $watched_users = array_count_values($track_watchVideo); // Return the user ID and the key/times the user has watch the module.
                $calculated_num_completed = 0;
                /*
                 * This goes through all the enrollments to calculate $calculated_num_completed
                 * copied the same function from coursestaffstats.php. Just deleted unnecessary code.
                 */
                foreach ($enrollments as $enrollment) 
                {
                    
                    $view_count = isset($watched_users[$enrollment['user_id']]) ? $watched_users[$enrollment['user_id']] : 0; // Number of times the user has watch the module.
                    $passed_count = isset($passed_users[$enrollment['user_id']]) ? $passed_users[$enrollment['user_id']] : 0; //Number of passes
                    $attempts = isset($attempt_count[$enrollment['user_id']]) ? $attempt_count[$enrollment['user_id']] : 0; //Number of quiz attempts
                    $percentage = eotprogressbar('8em', 0, true);
                    $status = displayStatus($passed_count, $num_quizzes_in_course, $attempts, $view_count);
                    if ($status == 'Completed')
                    {   
                        $calculated_num_completed++;
                    }
                }

/*
                foreach ($enrollments as $enrollment) 
                {

                    $passed_count = isset($passed_users[$enrollment['user_id']]) ? $passed_users[$enrollment['user_id']] : 0; //Number of passes

                    if ($passed_count == $num_quizzes_in_course) 
                    {
                        $calculated_num_completed++; // people who passed also completed the course.
                    } 
                    else if ($status == "failed") 
                    {
                        $total_number_failed++;
                    }
                }
*/
                $total_number_of_staff = count($enrollments); // The total number of staff enrolled in the course.
                // This calculates the percentage for the progressbars.
                if ($total_number_of_staff > 0) 
                { 
                    // Can't be divided by 0
                    $calculated_percentage_completed = (($calculated_num_completed / $total_number_of_staff) * 100);
                } 
                else 
                {
                    $calculated_percentage_completed = 0;
                }
?>
                <div class="smoothness">
                    <h1 class="article_page_title"><?= __("Course Statistics for", "EOT_LMS"); ?> "<?= $course_name ?>"</h1>
                    <?= __("Here are statistics on the", "EOT_LMS"); ?> <b><?= $course_name ?></b> <?= __("Modules", "EOT_LMS"); ?>.
                    <h2><?= __("Summary", "EOT_LMS"); ?></h2>
                    <div class="cell-row middle-row">
                        <div class="cell-caption">
                            <i class="fa fa-question-circle" title="<?= __("The total number of staff (in Staff Groups) who have been assigned this Course.", "EOT_LMS"); ?>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<?= __('The total number of staff (in Staff Groups) who have been assigned this Course.', 'EOT_LMS'); ?>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i> <?= __('Staff given this ', 'EOT_LMS')?><b><?= __('Course', 'EOT_LMS')?></b>
                        </div>
                        <div class="cell-field number">
                            <b><?= $total_number_of_staff ?></b>
                        </div>
                    </div>
                    <div class="cell-row">
                        <div class="cell-caption">
                            <i class="fa fa-question-circle" title="" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<?= __('The total number of staff who have passed all the required modules in this Course.', 'EOT_LMS'); ?>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i> <?= __('Staff who have ', 'EOT_LMS')?><b><?= __('Completed', 'EOT_LMS')?></b> <?= __(' this Course', 'EOT_LMS')?>
                        </div>
                        <div class="cell-field number">
                            <b><?= $calculated_num_completed ?></b>
                        </div>
                <?= eotprogressbar('12em', $calculated_percentage_completed, true); ?>
                    </div>
                    <h2><?= __("Quiz Success Rate", "EOT_LMS"); ?></h2>
                    <p><?= __("For quizzes with a low success rate, you may want to go over these topics in greater depth during your on-site training.", "EOT_LMS"); ?></p>
<?php
                    // Tables that will be displayed in the front end.
                    $quizTableObj = new stdClass();
                    $quizTableObj->rows = array();
                    $quizTableObj->headers = array(
                        __('Quiz Name', 'EOT_LMS') => 'left',
                        __('Success Rate', 'EOT_LMS') => 'center',
                        __('Percentage', 'EOT_LMS') => 'center'
                    );

                    // Creating rows for the table
                    foreach ($quizzes_in_course as $quiz) 
                    {

                        //$time_limit = date('i', strtotime($quiz['time_limit']));
                        $passed_count = isset($passed_quiz[$quiz['ID']]) ? $passed_quiz[$quiz['ID']] : 0; //Number of passes
                        $attempts = isset($attempt_count[$quiz['ID']]) ? $attempt_count[$quiz['ID']] : 0; //Number of quiz attempts
                        $percentage = $attempts > 0 ? (($passed_count / $attempts) * 100) : 0;

                        $quizTableObj->rows[] = array(
                            ' <span>' . stripslashes($quiz['name']) . '</span>',
                            "<a href='?part=quizstats&course_id=$course_id&quiz_id=" . $quiz['ID'] . "&subscription_id=$subscription_id&user_id=$user_id'>" . $passed_count . '/' . $attempts . "</a>",
                            eotprogressbar('12em', $percentage, true)
                        );
                    }
                    CreateDataTable($quizTableObj, "100%", 10, true, "Stats"); // Print the table in the page
                //}
?>
                    <h2><?= __("Video Views", "EOT_LMS"); ?></h2>
<?php
 
                    $views = array_count_values($track_watchVideo);
                    $custom_views = array_count_values($track_watch_customVideo);

                    $videosTableObj = new stdClass();
                    $videosTableObj->rows = array();
                    $videosTableObj->headers = array(
                        __('Video Title', 'EOT_LMS') => 'left',
                        __('Views', 'EOT_LMS') => 'center'
                    );
                    // Creating rows for the table
                    foreach ($all_videos as $video) {

                        if (isset($video['type']) && $video['type'] == 'custom_video') 
                        {
                            $view_count = isset($custom_views[$video['ID']]) ? $custom_views[$video['ID']] : 0; //Number of video views 
                            $custom = 1;
                        } 
                        else 
                        {
                            $view_count = isset($views[$video['ID']]) ? $views[$video['ID']] : 0; //Number of video views
                            $custom = 0;
                        }

                        $videosTableObj->rows[] = array(
                            ' <span>' . stripslashes($video['name']) . '</span>',
                            "<a href='?part=videostats&course_id=$course_id&video_id=" . $video['ID'] . "&custom=$custom&subscription_id=$subscription_id&user_id=$user_id'>" . $view_count . "</a>"
                        );
                    }
                    CreateDataTable($videosTableObj, "100%", 10, true, "Stats"); // Print the table in the page
?>
                    <h2><?= __("Resource Views", "EOT_LMS"); ?></h2>
<?php
                    $resources = getResourcesInCourse($course_id, 'doc');
                    //d($resources);
                    $track_download = array();
                    foreach ($track_records as $key => $record) 
                    {
                        if ($record['type'] == "download_resource") 
                        {
                            array_push($track_download, $record['resource_id']); // Save the ID of the video.
                            //unset($track_records[$key]); // Delete them from the array.
                        }
                    }
                    $downloads = array_count_values($track_download);
                    $resourceTableObj = new stdClass();
                    $resourceTableObj->rows = array();
                    $resourceTableObj->headers = array(
                        __('Name', 'EOT_LMS') => 'left',
                        __('Downloads', 'EOT_LMS') => 'center'
                    );
                    // Creating rows for the table
                    foreach ($resources as $resource) {


                        $download_count = isset($downloads[$resource['ID']]) ? $downloads[$resource['ID']] : 0; //Number of video views


                        $resourceTableObj->rows[] = array(
                            ' <span>' . stripslashes($resource['name']) . '</span>',
                            "<a href='?part=resourcestats&course_id=$course_id&resource_id=" . $resource['ID'] . "&subscription_id=$subscription_id&user_id=$user_id'>" . $download_count . "</a>"
                        );
                    }
                    CreateDataTable($resourceTableObj, "100%", 10, true, "Stats"); // Print the table in the page
?>
                </div>
<?php
                } 
                else 
                {
                    echo __("You dont have a valid course ID", "EOT_LMS");
                }
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