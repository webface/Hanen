<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_MY_LIBRARY ?>    
    <?= CRUMB_SEPARATOR ?>
    <span class="current"><?= __("Quizzes", "EOT_LMS"); ?></span>     
</div>
<h1 class="article_page_title"><?= __("Take the quiz", "EOT_LMS"); ?></h1>
<?php

    $quiz_id = isset($_REQUEST['quiz_id']) ? filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $course_id = isset($_REQUEST['course_id']) ? filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $subscription_id = isset($_REQUEST['subscription_id']) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $enrollment_id = isset($_REQUEST['enrollment_id'])? filter_var($_REQUEST['enrollment_id'], FILTER_SANITIZE_NUMBER_INT) : 0;

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
                echo __("This quiz is not part of your course", "EOT_LMS");
            }
        } 
        else 
        {
            echo __("the course ID does not belong to you", "EOT_LMS");
        }
    }
    else
    {
        echo __("subscription ID does not belong to you", "EOT_LMS");
    }
?>
