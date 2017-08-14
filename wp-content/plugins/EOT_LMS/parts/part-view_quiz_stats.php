<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>         
    <?= CRUMB_STATISTICS ?>    
    <?= CRUMB_SEPARATOR ?>
    <span class="current">View Quiz Statistics</span>     
</div>

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
    if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "") 
    {
        if (isset($true_subscription['status']) && $true_subscription['status']) 
        {
            if (current_user_can("is_director")) 
            {
                $path = WP_PLUGIN_DIR . '/eot_quiz/';
                require $path . 'public/class-eot_quiz_data.php';
                global $current_user;
                $user_id = $current_user->ID; // Wordpress user ID
                $org_id = get_org_from_user($user_id);
                $eot_quiz = new EotQuizData();
                $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
                $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
                $attempt_id = filter_var($_REQUEST['attempt_id'],FILTER_SANITIZE_NUMBER_INT);
                $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); // The user ID
                $fullname = get_user_meta($user_id, 'first_name', true)." ".get_user_meta($user_id, 'last_name', true);
                $quiz_data = $eot_quiz->get_quiz_data($quiz_id,true);
                $quiz_result = getQuizResults($attempt_id);
                //d($quiz_data, $quiz_result);
                $correct_questions = array();
                $selected_answers = array();
                foreach($quiz_result as $record)
                {
                    if($record['question_correct']==1)
                    {
                        array_push($correct_questions, $record['question_id']);
                    }
                    if($record['answer']==1)
                    {
                        array_push($selected_answers, $record['answer_id']);
                    }
                }
                $correct_questions = array_unique($correct_questions);
                $selected_answers = array_unique($selected_answers);
            ?>
                <h1 class="article_page_title">View Quiz Stats for <?=$fullname;?></h1>                
                <h3><?= $quiz_data['quiz']['name']; ?></h3>
                <p><?= $quiz_data['quiz']['description']; ?><p>
                <span><strong>Time Limit: </strong><?= $quiz_data['quiz']['time_limit']; ?> minutes</span><br>
                <span><strong>Passing Score: </strong><?= $quiz_data['quiz']['passing_score']; ?> /<?= $quiz_data['quiz']['questions']; ?></span><br>
            <?php 
                foreach ($quiz_data['questions'] as $question) 
                {
                    $correct = in_array($question['ID'], $correct_questions)? "<span style='color:green'>Correct</span>":"<span style='color:red'>Incorrect</span>";
            ?>
                <hr>
                <h4>Question: <?= $question['quiz_question']; ?></h4>
                <?= $correct; ?>
                <p>Answers</p>
                <ul>
            <?php
                    foreach ($question['possibilities'] as $answer) 
                    {
                        $correct = '';
                        $user_selected = in_array($answer['ID'], $selected_answers)? '<span style="font-style:italic;color:orange;padding:2px;background:black">Chosen</span>' :'';
                        if ($answer['answer_correct'] == 1) 
                        {
                            $correct = '<span class="fa fa-check"></span>';
                        }
            ?>
                        <li><?= $correct . $answer['answer_text'].$user_selected ?></li>
            <?php   
                    } 
            ?>
                </ul>
            <?php
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
    } 
    else 
    {
        echo "Could not find the subscription ID";
    }
?>