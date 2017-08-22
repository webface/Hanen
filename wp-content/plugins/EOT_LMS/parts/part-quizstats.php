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
                $org_id = get_org_from_user ($user_id); // Organization ID
        		$course_id = filter_var($course_id = $_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
		        $course_data = getCourse($course_id); // The course information
		        $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                $quiz_id = filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT);// The quiz ID
                $path = WP_PLUGIN_DIR . '/eot_quiz/';
                require $path . 'public/class-eot_quiz_data.php';
                $eot_quiz = new EotQuizData();
                $quiz = $eot_quiz->get_quiz_data($quiz_id, false);
                $quiz_name = $quiz['quiz']['name'];
                $users = getEotUsers($org_id);
                $users = $users['users'];
                $user_ids = array_column($users, 'ID');
                $user_ids_string = implode(",", $user_ids);
               // d($users);
                
                
?>
		<div class="smoothness">
			<h1 class="article_page_title">Quiz Statistics for "<?= $quiz_name ?>"</h1>
			Here are statistics on the <b><?= $quiz_name ?></b> Quiz.
                        <h2>Question Success Rate</h2>
                        <p>For questions with a low success rate, you may want to go over these topics in greater depth during your on-site training.</p>
<?php 
                            $quiz_questions = $quiz['questions'];
                            $question_ids = array_column($quiz_questions, 'ID');
                            $question_ids_string = implode(",", $question_ids);
                            $question_stats = getQuestionResults($question_ids_string,$user_ids_string, $quiz_id);
                            $quiz_attempts=  array_unique(array_column($question_stats,'attempt_id'));
                            $stats = array();
                            
                            foreach ($question_stats as $stat) 
                            {
                                $stats[$stat['question_id']]= array();
                                
                                foreach($question_stats as $substat)
                                {
                                   if(($stat['question_id'] == $substat['question_id']))
                                   {
                                       array_push($stats[$stat['question_id']], $substat['answer_correct']);
                                   }
                                }
                            }
                            //d($quiz, $question_stats, $stats);
                            // Tables that will be displayed in the front end.
                            $questionTableObj = new stdClass();
                            $questionTableObj->rows = array();
                            $questionTableObj->headers = array(
                                'Question - <a href="#" class="no-ul" onclick="event.stopPropagation();if(jQuery(this).text()==\'Show All Choices\'){jQuery(this).text(\'Hide All Choices\');jQuery(\'.choices\').stop(true,true).removeClass(\'hidden\');}else{jQuery(this).text(\'Show All Choices\');jQuery(\'.choices\').stop(true,true).addClass(\'hidden\');}return false;">Show All Choices</a>' => array("sType"=>"html"),
          '<div '.hover_text_attr("<b>Success Rate:</b> The number of <b>Successful Attempts</b> divided by the <b>Total Number of Attempts</b> for this particular question.",true).'>Success Rate</div>' => 'center',
          '<center><div '.hover_text_attr("<b>Success Rate</b> as a percentage.",true).'>Percentage</div></center>' => 'center'
                            );

                            // Creating rows for the table
                            foreach ($quiz_questions as $question) 
                            {

                                
                                $correct_count = isset($stats[$question['ID']])? array_sum($stats[$question['ID']]) : 0;//Number of passes
                                $num_attempts = isset($stats[$question['ID']]) ? count($stats[$question['ID']]) : 0;//Number of question attempts
                                $questionHTML = "<a href='#' onclick='jQuery(\"#choices_".$question['ID']."\").toggleClass(\"hidden\",1000);return false;'>".$question['quiz_question']."</a><div class='hidden choices' id='choices_".$question['ID']."'><br>";
                                foreach ($question['possibilities'] as $answer) 
                                {
                                    $correct = '';
                                    if ($answer['answer_correct'] == 1) 
                                    {
                                        $correct = '<span class="fa fa-check"></span>';
                                    }
                                    $questionHTML.="<div>".$correct . $answer['answer_text']."</div>"; 
                                } 
                                $questionHTML .= "</div>";
                                $percentage = 0;
                                if($num_attempts > 0)
                                {
                                    $percentage = ($correct_count/$num_attempts)*100;
                                }
                                $questionTableObj->rows[] = array(
                                    $questionHTML,
                                    $correct_count.'/'.$num_attempts,
                                    eotprogressbar('12em', $percentage, true)
                                    );
                            }
                            CreateDataTable($questionTableObj,"100%",10,true,"Stats"); // Print the table in the page
                       
?>
                        
                </div>
<?php                }
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