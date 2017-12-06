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
                $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); // The user ID
                $fullname = get_user_meta($user_id, 'first_name', true)." ".get_user_meta($user_id, 'last_name', true);
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);// the quiz ID
                $path = WP_PLUGIN_DIR . '/eot_quiz/';
                require $path . 'public/class-eot_quiz_data.php';
                $eot_quiz = new EotQuizData();
                $quiz = $eot_quiz->get_quiz_by_id($quiz_id);
?>
                <div class="smoothness">
                                        <h1 class="article_page_title"><?= __('View', 'EOT_LMS')?> <?= $fullname ?><?= __("'s Quiz Attempts", 'EOT_LMS')?></h1>
                                        <h3><?= $quiz['name'] ?></h3>
                                        <p>
                                            <?= __('Times are shown in', 'EOT_LMS')?> <b><?= __('Pacific Standard Time (PST)', 'EOT_LMS')?></b> <span class="small"><?= __('(GMT - 8).', 'EOT_LMS')?></span><br />
                                            <?= __('It is currently', 'EOT_LMS')?> <b><?=date('g:ia', time())?></b> <?= __('on', 'EOT_LMS')?> <b><?=date('F j, Y', time())?></b>.
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
//d($track_quizzes,  $track_quiz_attempts);
 
                    // Tables that will be displayed in the front end.
                    $quizTableObj = new stdClass();
                    $quizTableObj->rows = array();
                    $quizTableObj->headers = array(
                        __('No', 'EOT_LMS') => 'left',
                        __('Date and Time', 'EOT_LMS') => 'center',
                        __('Score', 'EOT_LMS') => 'center',
                        __('Status', 'EOT_LMS') => 'center'
                    );

                    // Creating rows for the table
                    $count=0;
                    foreach ($track_quiz_attempts as $quiz_att) 
                    {

                        $count++;
                        $passed = ($quiz_att['passed'] == 1)? __("Pass",'EOT_LMS'):__("Fail",'EOT_LMS');
                        $quizTableObj->rows[] = array(
                            $count,
                            date('F j, Y g:i a', strtotime($quiz_att['date_attempted'])),
                            "<a href='?part=view_quiz_stats&course_id=$course_id&quiz_id=".$quiz['ID']."&attempt_id=".$quiz_att['ID']."&subscription_id=$subscription_id&user_id=$user_id'>".$quiz_att['score']."/".$quiz['num_questions_to_display']."</a>",
                            $passed
                            );
                    }
                    CreateDataTable($quizTableObj,"100%",10,true,__("Stats", 'EOT_LMS')); // Print the table in the page
                 
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