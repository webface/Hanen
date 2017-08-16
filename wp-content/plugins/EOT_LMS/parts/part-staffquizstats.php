<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>         
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_STATISTICS ?>          
    <?= CRUMB_SEPARATOR ?>  
    <span class="current">Course Stats</span>     
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
                $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
                $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
                $fullname = get_user_meta($user_id, 'first_name', true)." ".get_user_meta($user_id, 'last_name', true);
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                ?>
                <div class="smoothness">
                                        <h1 class="article_page_title">View <?= $fullname ?>'s Quizzes</h1>
                                        
                </div>
<?php
                $quizzes = getQuizzesInCourse($course_id);
                $track_quizzes = getAllQuizAttempts($course_id, $user_id);//All quiz attempts for this course
                $track_passed = array();
                $track_quiz_attempts = array();
                foreach ($track_quizzes as $key => $record) 
                {
                    if($record['passed'] == 1)
                    {
                      array_push($track_passed, $record['quiz_id']); // Save the user ID of the users who passed the quiz.
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
                        'Attempts' => 'center',
                        'Status' => 'center'
                    );

                    // Creating rows for the table
                    foreach ($quizzes as $quiz) 
                    {

                        ///$time_limit = date('i', strtotime($quiz['time_limit']));
                        $passed = isset($passed_users[$quiz['ID']])? 'Passed' : 'Incomplete';//Number of passes
                        $attempts = isset($attempt_count[$quiz['ID']]) ? $attempt_count[$quiz['ID']] : 0;//Number of quiz attempts
                        if(!isset($passed_users[$quiz['ID']]) && $attempts > 0)//they must have failed that quiz
                        {
                            $passed = "Failed";
                        }
                        //$percentage = $attempts>0?(($passed_count/$attempts)*100):0;

                        $quizTableObj->rows[] = array(
                            ' <span>' . stripslashes($quiz['name']) . '</span>',
                            "<a href='?part=staffquizattemptstats&course_id=$course_id&user_id=$user_id&quiz_id=".$quiz['ID']."&subscription_id=$subscription_id'>".$attempts."</a>",
                            $passed
                            );
                    }
                    CreateDataTable($quizTableObj,"100%",10,true,"Stats"); // Print the table in the page
                        }
            }
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