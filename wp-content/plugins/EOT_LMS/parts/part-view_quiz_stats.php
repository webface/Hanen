<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>         
    <?= CRUMB_STATISTICS ?>    
    <?= CRUMB_SEPARATOR ?>
    <span class="current"><?= __("View Quiz Statistics", "EOT_LMS"); ?></span>     
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
                $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
                $org_id = get_org_from_user($user_id);
                $eot_quiz = new EotQuizData();
                $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
                $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
                if(!verifyStatsUser())
                {
                    die(__('You dont have permission to view this user\'s stats','EOT_LMS'));
                }
                $attempt_id = filter_var($_REQUEST['attempt_id'],FILTER_SANITIZE_NUMBER_INT);
                $suser_id = filter_var($_REQUEST['stats_user_id'],FILTER_SANITIZE_NUMBER_INT); // The user ID
                $fullname = get_user_meta($suser_id, 'first_name', true)." ".get_user_meta($suser_id, 'last_name', true);
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
                <h1 class="article_page_title"><?= __("View Quiz Stats for", "EOT_LMS"); ?> <?=$fullname;?></h1>                
                <h3><?= $quiz_data['quiz']['name']; ?></h3>
                <p><?= $quiz_data['quiz']['description']; ?><p>
                <span><strong><?= __("Time Limit:", "EOT_LMS"); ?> </strong><?= $quiz_data['quiz']['time_limit']; ?> <?= __("minutes", "EOT_LMS"); ?></span><br>
                <span><strong><?= __("Passing Score:", "EOT_LMS"); ?> </strong><?= $quiz_data['quiz']['passing_score']; ?> /<?= $quiz_data['quiz']['num_questions_to_display']; ?></span><br>
            <?php 
                foreach ($quiz_data['questions'] as $question) 
                {
                    $correct = in_array($question['ID'], $correct_questions)? "<span style='color:green'>" . __("Correct", "EOT_LMS") . "</span>":"<span style='color:red'>" . __("Incorrect", "EOT_LMS") . "</span>";
            ?>
                <hr>
                <h4><?= __("Question:", "EOT_LMS"); ?> <?= $question['quiz_question']; ?></h4>
                <?= $correct; ?>
                <p><?= __("Answers", "EOT_LMS"); ?></p>
                <ul>
            <?php
                    foreach ($question['possibilities'] as $answer) 
                    {
                        $correct = '';
                        $user_selected = in_array($answer['ID'], $selected_answers)? '<span style="font-style:italic;color:orange;padding:2px;background:black">' . __("Chosen", "EOT_LMS") . '</span>' :'';
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
                echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.", "EOT_LMS");
            }
        } 
        else 
        {
            echo __("subscription ID does not belong to you", "EOT_LMS");
        }
    } 
    else 
    {
        echo __("Could not find the subscription ID", "EOT_LMS");
    }
?>