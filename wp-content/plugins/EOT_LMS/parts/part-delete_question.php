<?php

    $true_subscription = verifyUserAccess();
    if (isset($true_subscription['status']) && $true_subscription['status']) 
    {
        if (current_user_can("is_director")) 
        {
			$path = WP_PLUGIN_DIR . '/eot_quiz/';
			require $path . 'public/class-eot_quiz_data.php';
			$eot_quiz = new EotQuizData();

			$question_id = isset($_REQUEST['question_id']) ? filter_var($_REQUEST['question_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
			$quiz_id = isset($_REQUEST['quiz_id']) ? filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
			$subscription_id = isset($_REQUEST['subscription_id']) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : 0;

			// make sure we got a question ID, quiz ID, and subscription ID
			if (!$question_id || !$quiz_id || !$subscription_id)
				wp_die("ERROR: missing parameters");

			$delete = $eot_quiz->deleteQuestion($question_id);
			if($delete)
			{
			    wp_redirect(home_url('/dashboard?part=manage_quiz_questions&quiz_id='.$quiz_id.'&subscription_id='.$subscription_id));
			    exit();
			}
			else
			{
			    wp_die("ERROR: Couldn't delete the question");
			}
		}
        else 
        {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
        }
    } 
    else 
    {
        echo "subscription ID does not belong to you";
    }
?>