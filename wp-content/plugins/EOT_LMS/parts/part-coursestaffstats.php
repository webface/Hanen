<?php
// enqueue required javascripts
wp_enqueue_script('datatables-buttons', get_template_directory_uri() . '/js/dataTables.buttons.min.js', array('datatables-js'), '1.2.4', true);
wp_enqueue_script('buttons-flash', get_template_directory_uri() . '/js/buttons.flash.min.js', array(), '1.2.4', true);
wp_enqueue_script('jszip', get_template_directory_uri() . '/js/jszip.min.js', array(), '2.5.0', true);
wp_enqueue_script('vfs-fonts', get_template_directory_uri() . '/js/vfs_fonts.js', array(), '0.1.24', true);
wp_enqueue_script('buttons-html5', get_template_directory_uri() . '/js/buttons.html5.min.js', array(), '1.2.4', true);
wp_enqueue_script('buttons-print', get_template_directory_uri() . '/js/buttons.print.min.js', array(), '1.2.4', true);
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
                // Variable declaration
                global $current_user;
                $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
                $org_id = get_org_from_user($user_id); // Organization ID
                $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT); // The course ID
                $course_data = getCourse($course_id); // The course information
                $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                $quizzes_in_course = getQuizzesInCourse($course_id);
                $num_quizzes_in_course = count($quizzes_in_course);
                $track_records = getAllTrack($org_id); // All track records.
                $track_quizzes = getAllQuizAttempts($course_id); //All quiz attempts for this course
                $track_watchVideo = array();
                $track_login = array();

                // Create sub arrays from all the track records
                foreach ($track_records as $key => $record) 
                {
                    if ($record['type'] == "watch_video") 
                    {
                        array_push($track_watchVideo, $record['user_id']); // Save the user ID of the users who watch the video.
                        //unset($track_records[$key]); // Delete them from the array.
                    }
                    if ($record['type'] == "watch_custom_video") 
                    {
                        array_push($track_watchVideo, $record['user_id']); // Save the user ID of the users who watch the custom video.
                        //unset($track_records[$key]); // Delete them from the array.
                    }
                    if ($record['type'] == "login") 
                    {
                        array_push($track_login, $record['user_id']); // Save the user ID of the users who logged in.
                        //unset($track_records[$key]); // Delete them from the array.
                    }
                    // Separate another type into another array of its own.
                }
                $trackFailed = array();
                $trackPassed = array();
                $quizPassed = array();//needed to verify and remove quizzes passed more than once
                $track_quiz_attempts = array();
                foreach ($track_quizzes as $key => $record) 
                {
                    if ($record['passed'] == 0) 
                    {
                        array_push($trackFailed, $record['user_id']); // Save the user ID of the users who failed the quiz.
                        //unset($track_quizzes[$key]); // Delete them from the array.
                    }
                    if ($record['passed'] == 1 && (!isset($quizPassed[$record['quiz_id']]) || ($quizPassed[$record['quiz_id']] != $record['user_id'])))//make sure the quiz has not been already passed 
                    {
                        $quizPassed[$record['quiz_id']] = $record['user_id'];
                        array_push($trackPassed, $record['user_id']); // Save the user ID of the users who failed the quiz.
                        //unset($track_quizzes[$key]); // Delete them from the array.
                    }
                    array_push($track_quiz_attempts, $record['user_id']); // Save the user ID of the users who failed the quiz. 
                }

                $failed_users = array_count_values($trackFailed);
                $passed_users = array_count_values($trackPassed);
                $attempt_count = array_count_values($track_quiz_attempts);
                $login_users = array_count_values($track_login); // Return the user ID and the key/times the user has logged in.
                $watched_users = array_count_values($track_watchVideo); // Return the user ID and the key/times the user has watch the module.

                if (isset($course_data['status']) && $course_data['status'] == 0) 
                {
                    // error received from getCourse
                    wp_die($course_data['message'], 'Error');
                } 
                else 
                {
                    if (isset($course_data['ID'])) 
                    {

                        $course_name = $course_data['course_name'];
                    }
                }
                $calculated_num_completed = 0;
                $enrollments = getEnrollments($course_id, 0, 0, false); // Get all failed/passed enrollments in the course.
                $total_number_of_staff = count($enrollments); // The total number of staff enrolled in the course.
                $calculated_percentage_completed = 0; // the percentage of completed staff

                // This calculates the percentage for the progressbars.
                if ($total_number_of_staff > 0)
                { 
                    $quizTableObj = new stdClass();
                    $quizTableObj->rows = array();
                    $quizTableObj->headers = array(
                        __('Name', 'EOT_LMS') => 'quiz-title',
                        __('Email', 'EOT_LMS') => 'center',
                        __('Passed', 'EOT_LMS') => 'center',
                        __('Failed', 'EOT_LMS') => 'center',
                        __('Logins', 'EOT_LMS') => 'center',
                        //__('Views', 'EOT_LMS') => 'center',
                        '<div ' . hover_text_attr(__('The enrollment status in this course. This can be the following statuses: Not started, in progress, completed. For completed courses a completion date is shown in the format: Y-M-D.', 'EOT_LMS'), true) . '>'.__('Status', 'EOT_LMS').'</div>' => 'center',
                        '<div ' . hover_text_attr(__('This is a representation of the number of modules completed by the Staff member as. A percentage of the total number of modules in the course.', 'EOT_LMS'), true) . '>'.__('Progress', 'EOT_LMS').'</div>' => 'staff-progress',
                        __('Actions', 'EOT_LMS') => 'center'
                    );
                    /*
                     * This goes through all the enrollments and display a table 
                     * with Name, Status and the Progress of each staff in the course
                     */
                    foreach ($enrollments as $enrollment) 
                    {
                        $name = get_user_meta($enrollment['user_id'], "first_name", true) . " " . get_user_meta($enrollment['user_id'], "last_name", true);
                        $user_info = get_userdata($enrollment['user_id']);
                        $login_count = isset($login_users[$enrollment['user_id']]) ? $login_users[$enrollment['user_id']] : 0; // Number of times the user has log in to our system.
                        $view_count = isset($watched_users[$enrollment['user_id']]) ? $watched_users[$enrollment['user_id']] : 0; // Number of times the user has watch the module.
                        $fail_count = isset($failed_users[$enrollment['user_id']]) ? $failed_users[$enrollment['user_id']] : 0; // Number of times they failed
                        $passed_count = isset($passed_users[$enrollment['user_id']]) ? $passed_users[$enrollment['user_id']] : 0; //Number of passes
                        $attempts = isset($attempt_count[$enrollment['user_id']]) ? $attempt_count[$enrollment['user_id']] : 0; //Number of quiz attempts
                        $percentage = eotprogressbar('8em', 0, true);

                        if ($attempts > 0) 
                        {
                            $percentage = eotprogressbar('8em', (($passed_count / $num_quizzes_in_course) * 100), true);
                        }

                        $status = displayStatus($passed_count, $num_quizzes_in_course, $attempts, $view_count);
                        if ($status == 'Completed')
                        {   // Add completion date
                            $lastCompletedDate = getLatestQuizCompletionDate($enrollment['user_id'], $course_id);
                            if ($lastCompletedDate && isset($lastCompletedDate->max_date))
                            {
                                $date = date_format( date_create($lastCompletedDate->max_date), "Y-m-d" );
                                $status = $status . " $date";
                            }
                            $calculated_num_completed++;
                        }

                        $quizTableObj->rows[] = array(
                            $name,
                            $user_info->user_email,
                            "<a href='/dashboard?part=staffquizstats&stats_user_id=" . $enrollment['user_id'] . "&course_id=$course_id&subscription_id=$subscription_id&user_id=$user_id'>" . $passed_count . '/' . $num_quizzes_in_course . "</a>",
                            "<a href='/dashboard?part=staffquizstats&stats_user_id=" . $enrollment['user_id'] . "&course_id=$course_id&subscription_id=$subscription_id&user_id=$user_id'>" . $fail_count . "</a>",
                            "<a href='/dashboard?part=loginrecordstats&stats_user_id=" . $enrollment['user_id'] . "&course_id=$course_id&subscription_id=$subscription_id&user_id=$user_id'>" . $login_count . "</a>",
                            //"<a href='/dashboard?part=videowatchstats&stats_user_id=" . $enrollment['user_id'] . "&course_id=$course_id&subscription_id=$subscription_id&user_id=$user_id'>" . $view_count . "</a>",
                            $status,
                            $percentage,
                            "<a href='?part=improved_email_staff&subscription_id=".$subscription_id."&target=select-staff&recipient=".$enrollment['user_id']."' target='_blank'><i class='fa fa-envelope tooltip' title=\"Send an email to $name\" style=\"margin-bottom: -2px\" onmouseover=\"Tip('Send an email to $name', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')\" onmouseout=\"UnTip()\"></i></a>"
                        );
                    }

                    $calculated_percentage_completed = (($calculated_num_completed / $total_number_of_staff) * 100); 
                }


?>
                <div class="breadcrumb">
                <?= CRUMB_DASHBOARD ?>              
                <?= CRUMB_SEPARATOR ?>  
                <?= CRUMB_STATISTICS ?>  
                <?= CRUMB_SEPARATOR ?>  
                    <b><?= __("Staff statistics for", "EOT_LMS"); ?> "<?= $course_name ?>"</b>
                </div>
                <div class="smoothness">
                    <h1 class="article_page_title"><?= __("Staff statistics for", "EOT_LMS") ?> "<?= $course_name ?>"</h1>
                    <?= __("Here are statistics on the staff taking the", "EOT_LMS") ?> <b><?= $course_name ?></b> <?= __("Course", "EOT_LMS") ?>.
                    <h2><?= __("Summary", "EOT_LMS") ?>&nbsp;&nbsp;<span class="bs"><a href="?part=improved_email_staff&subscription_id=<?= $subscription_id?>&target=all-course&recipient=<?= $course_id?>" class="btn btn-primary" target='_blank'><i class="fa fa-envelope"></i>&nbsp;<?= __("Email Staff", "EOT_LMS") ?></a></span></h2>

                    <div class="cell-row middle-row">
                        <div class="cell-caption">
                            <img src="<?= get_template_directory_uri() . "/images/info-sm.gif" ?>" title="<?= __("The total number of staff (in Staff Groups) who have been assigned this Course.", "EOT_LMS"); ?>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<?= __("The total number of staff (in Staff Groups) who have been assigned this Course.", "EOT_LMS"); ?>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"> <?= __("Staff given this", "EOT_LMS"); ?> <b><?= __("Course", "EOT_LMS"); ?></b>
                        </div>
                        <div class="cell-field number">
                            <b><?= $total_number_of_staff ?></b>
                        </div>
                    </div>
                    <div class="cell-row">
                        <div class="cell-caption">
                            <img src="<?= get_template_directory_uri() . "/images/info-sm.gif" ?>" title="" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<?= __("The total number of staff who have passed all the required modules in this Course.", "EOT_LMS"); ?>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"> <?= __("Staff who have", "EOT_LMS"); ?> <b><?= __("Completed", "EOT_LMS"); ?></b> <?= __("this Course", "EOT_LMS"); ?>
                        </div>
                        <div class="cell-field number">
                            <b><?= $calculated_num_completed ?></b>
                        </div>
                <?= eotprogressbar('12em', $calculated_percentage_completed, true); ?>
                    </div>
                    <h2><?= __("Staff Statistics", "EOT_LMS"); ?></h2>
<?php
                if ($enrollments) 
                {
                    CreateDataTable($quizTableObj, "100%", 25, true, "Stats");
                } 
                else 
                {
?>
                    <?= __("There are no staff registered in this course.", "EOT_LMS"); ?>
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
                                    <div class="errorbox"><?= __("You do not have access to these Statistics.", "EOT_LMS"); ?></div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php
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