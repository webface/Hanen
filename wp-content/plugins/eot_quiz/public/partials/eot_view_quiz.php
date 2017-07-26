<?php
    /* Form $_POST based on Gravity Forms API
     * Contains more functionaility than needed for this page
     * Components only used once in application
     */
    $path = WP_PLUGIN_DIR . '/eot_quiz/';
    require $path . 'public/class-eot_quiz_data.php';
    global $current_user;
    $user_id = $current_user->ID; // Wordpress user ID
    $org_id = get_org_from_user($user_id);
    $eot_quiz = new EotQuizData();
    $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
    $quiz_data = $eot_quiz->get_quiz_data($quiz_id);
?>
    <h3><?= $quiz_data['quiz']['name']; ?></h3>
    <p><?= $quiz_data['quiz']['description']; ?><p>
    <span><strong>Time Limit: </strong><?= $quiz_data['quiz']['time_limit']; ?> minutes</span><br>
    <span><strong>Passing Score: </strong><?= $quiz_data['quiz']['passing_score']; ?> /<?= $quiz_data['quiz']['questions']; ?></span><br>
    <?= '<a  href="/dashboard?part=update_quiz&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id . '">Edit quiz details</a>'; ?>
<?php 
    foreach ($quiz_data['questions'] as $question) 
    { 
?>
    <hr>
    <h4>Question: <?= $question['quiz_question']; ?></h4>
    <p>Answers</p>
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
    echo '<a href="/dashboard?part=update_quiz_questions&question_id=' . $question['ID'] . '&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id . '" onclick="load(\'load_edit_quiz\')">Edit question & answers</a>';
    }
?>