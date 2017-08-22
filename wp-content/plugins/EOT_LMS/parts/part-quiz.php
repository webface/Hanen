<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <span class="current">Quizzes</span>     
</div>
<h1 class="article_page_title">Take the quiz</h1>
<?php
	$quiz_id = isset($_REQUEST['quiz_id']) ? filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $course_id = isset($_REQUEST['course_id']) ? filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
	$subscription_id = isset($_REQUEST['subscription_id']) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : 0;

	// make sure we got a question ID, quiz ID, and subscription ID
	if (!$quiz_id || !$subscription_id)
		die();

	//$true_subscription = verifyUserAccess();

	if (verify_student_access($course_id)) 
    {
		echo '<script>var quiz_id = ' . $quiz_id . '; var course_id = ' . $course_id . ';</script>'; // set the quiz ID in JS

		//load quiz
		echo do_shortcode( '[eot_quiz_display action="view_quiz" id="' . $quiz_id . '"]' );
    } 
    else 
    {
        echo "subscription ID does not belong to you";
    }
?>
