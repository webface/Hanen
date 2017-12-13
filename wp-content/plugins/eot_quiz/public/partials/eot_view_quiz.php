<?php
    /* Form $_POST based on Gravity Forms API
     * Contains more functionaility than needed for this page
     * Components only used once in application
     */
    $path = WP_PLUGIN_DIR . '/eot_quiz/';
    require $path . 'public/class-eot_quiz_data.php';
    global $current_user;
    $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
    $org_id = get_org_from_user($user_id);
    $eot_quiz = new EotQuizData();
    $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
    $quiz_data = $eot_quiz->get_quiz_data($quiz_id, false);
?>
    <h3><?= $quiz_data['quiz']['name']; ?></h3>
    <p><?= $quiz_data['quiz']['description']; ?><p>

    <span><strong><?= __('Time Limit:', 'EOT_LMS')?> </strong><?= $quiz_data['quiz']['time_limit']; ?> <?= __('minutes', 'EOT_LMS')?></span><br>
    <span><strong><?= __('Passing Score:', 'EOT_LMS')?> </strong><?= $quiz_data['quiz']['passing_score']; ?> /<?= $quiz_data['quiz']['questions']; ?></span><br>
    <?= '<a  href="?part=update_quiz&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id .'&user_id=' . $user_id . '">'.__('Edit quiz details', 'EOT_LMS').'</a>'; ?>

<?php 
    foreach ($quiz_data['questions'] as $question) 
    { 
?>
    <hr>
    <h4><?= __('Question:', 'EOT_LMS')?> <?= $question['quiz_question']; ?></h4>
    <p><?= __('Answers', 'EOT_LMS')?></p>
    <ul>
<?php
        foreach ($question['possibilities'] as $answer) 
        {
            $correct = '';
            if ($answer['answer_correct'] == 1) 
            {
                $correct = '<span class="fa fa-check"></span>';
            }
?>
            <li><?= $correct . $answer['answer_text'] ?></li>
<?php   
        } 
?>
    </ul>
<?php
    echo '<a href="/dashboard?part=update_quiz_questions&question_id=' . $question['ID'] . '&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id. '&user_id=' . $user_id . '" onclick="load(\'load_edit_quiz\')">'.__('Edit question & answers', 'EOT_LMS').'</a>';
    }
?>
