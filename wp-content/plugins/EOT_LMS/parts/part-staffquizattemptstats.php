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
                $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
                $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
                $fullname = get_user_meta($user_id, 'first_name', true)." ".get_user_meta($user_id, 'last_name', true);
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);// the quiz ID
                $path = WP_PLUGIN_DIR . '/eot_quiz/';
                require $path . 'public/class-eot_quiz_data.php';
                $eot_quiz = new EotQuizData();
                $quiz = $eot_quiz->get_quiz_by_id($quiz_id);
                ?>
                <div class="smoothness">
                                        <h1 class="article_page_title">View <?= $fullname ?>'s Quizzes</h1>
                                        <h3><?= $quiz['name'] ?></h3>
                                        <p>
                                            Times are shown in <b>Pacific Standard Time (PST)</b> <span class="small">(GMT - 8).</span><br />
                                            It is currently <b><?=date('g:ia', time())?></b> on <b><?=date('F j, Y', time())?></b>.
                                        </p>
                                        
                </div>
<?php

                $track_quizzes = getAllQuizAttempts($course_id, $user_id);//All quiz attempts for this course
                $track_quiz_attempts = array();
                foreach ($track_quizzes as $key => $record) { //filter for this quiz
                    if($record['quiz_id'] == $quiz_id)
                    {
                     array_push($track_quiz_attempts, $record); // Save the user ID of the users who failed the quiz.
                      //unset($track_quizzes[$key]); // Delete them from the array.
                    }
                   
                }
d($track_quizzes,  $track_quiz_attempts);
 
                    // Tables that will be displayed in the front end.
                    $quizTableObj = new stdClass();
                    $quizTableObj->rows = array();
                    $quizTableObj->headers = array(
                        'No' => 'left',
                        'Date and Time' => 'center',
                        'Score' => 'center',
                        'Status' => 'center'
                    );

                    // Creating rows for the table
                    $count=0;
                    foreach ($track_quiz_attempts as $quiz_att) 
                    {

                        $count++;
                        $passed = ($quiz_att['passed'] == 1)? "Pass":"Fail";
                        $quizTableObj->rows[] = array(
                            $count,
                            date('F j, Y g:i a', strtotime($quiz_att['date_attempted'])),
                            "<a href='?part=staffquizattemptstats&course_id=$course_id&quiz_id=".$quiz['ID']."&subscription_id=$subscription_id'>".$quiz_att['score']."/".$quiz['num_questions_to_display']."</a>",
                            $passed
                            );
                    }
                    CreateDataTable($quizTableObj); // Print the table in the page
                 
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