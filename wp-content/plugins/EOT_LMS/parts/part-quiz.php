<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_MY_LIBRARY ?>    
    <?= CRUMB_SEPARATOR ?>
    <span class="current">Quizzes</span>     
</div>
<h1 class="article_page_title">Take the quiz</h1>
<?php
    global $wpdb, $current_user;
    $user_id = $current_user->ID;
    $quiz_id = isset($_REQUEST['quiz_id']) ? filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $course_id = isset($_REQUEST['course_id']) ? filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $subscription_id = isset($_REQUEST['subscription_id']) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $enrollment_id = isset($_REQUEST['enrollment_id'])? filter_var($_REQUEST['enrollment_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $other_resources = getOtherResourcesInModule($course_id, $quiz_id, 'exam');
    foreach ($other_resources as $resource) {
        if($resource['type']== "video")
        {
            $video_id = $resource['resource_id'];
            $module_id = $resource['module_id'];
        }
    }
    if(isset($video_id))
    {
        $track = $wpdb->get_row("SELECT * FROM ". TABLE_TRACK. " WHERE user_id = $user_id and type = 'watch_video' AND video_id = $video_id AND module_id = $module_id",OBJECT);
        if($track && $track->repeat == 1)
        {
            echo "<div class='bs'><div class='well'><p>You have failed the quiz! You must rewatch the video to attempt it again</p></div></div>";
            echo "<a href='?part=my_library&course_id=".$course_id."&enrollment_id=".$enrollment_id."'>Back to Courses</a>";
            exit();
        }
    }
    // make sure we got a question ID, quiz ID, and subscription ID
    if (!$quiz_id || !$subscription_id)
            die();

    $true_subscription = verifyUserAccess();
    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
        if (verify_student_access($course_id)) 
        {
            if(verifyQuizInCourse($quiz_id, $course_id))
            {
                echo '<script>var quiz_id = ' . $quiz_id . '; var course_id = ' . $course_id . '; var enrollment_id = ' . $enrollment_id . ';</script>'; // set the quiz ID in JS

                //load quiz
                echo do_shortcode( '[eot_quiz_display action="view_quiz" id="' . $quiz_id . '"]' );
            }
            else
            {
                echo "This quiz is not part of your course";
            }
        } 
        else 
        {
            echo "the course ID does not belong to you";
        }
    }
    else
    {
        echo "subscription ID does not belong to you";
    }
?>
