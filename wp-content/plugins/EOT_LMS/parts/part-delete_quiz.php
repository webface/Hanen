<?php

    $true_subscription = verifyUserAccess();
    global $current_user;
    $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
    if ((isset($true_subscription['status']) && $true_subscription['status']) || current_user_can("is_sales_manager")) 
    {
        if (current_user_can("is_director") || current_user_can("is_sales_manager")) 
        {
			$path = WP_PLUGIN_DIR . '/eot_quiz/';
			require $path . 'public/class-eot_quiz_data.php';
			$eot_quiz = new EotQuizData();

			$quiz_id = isset($_REQUEST['quiz_id']) ? filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
			$subscription_id = isset($_REQUEST['subscription_id']) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : 0;

			// make sure we got a quiz ID, and subscription ID
			if (!$quiz_id || !$subscription_id)
				wp_die(__("ERROR: missing parameters", "EOT_LMS"));

			$delete=$eot_quiz->deleteQuiz($quiz_id);
			if($delete)
			{
			    if($subscription_id == 0)
                            {
                            wp_redirect(home_url('/dashboard?part=manage_quiz_eot&subscription_id='.$subscription_id.'&user_id='.$user_id));
                            }
                            else 
                            {
                            wp_redirect(home_url('/dashboard?part=manage_quiz&subscription_id='.$subscription_id.'&user_id='.$user_id));
                            }
			    exit();
			}
			else
			{
			    wp_die(__("ERROR: Couldn't delete the question", "EOT_LMS"));
			}
		}
        else 
        {
            echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.", "EOT_LMS");
        }
    } 
    else 
    {
        echo __("subscription ID does not belong to you", "EOT_LMS");
    }
?>