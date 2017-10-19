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
$quiz_id = filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT);
$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
$question_id = filter_var($_REQUEST['question_id'], FILTER_SANITIZE_NUMBER_INT);
$admin_ajax_url = admin_url('admin-ajax.php');
if(!verifyQuiz($quiz_id))
{
    die("This quiz does not belong to you");
}
if(!verifyQuizQuestion($quiz_id, $question_id))
{
    die(__("This question does not belong in your quizzes", "EOT_LMS"));
}
if(isset($_POST['feedback']))
{
    $question_id = filter_var($_REQUEST['question_id'], FILTER_SANITIZE_NUMBER_INT);
    $feedback_correct = preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_REQUEST['feedback_correct']));
    $feedback_incorrect = preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_REQUEST['feedback_incorrect']));
    //$feedback_correct = addslashes($feedback_correct);
    //$feedback_incorrect = addslashes($feedback_incorrect);
    $data = array(
        'feedback_correct' => $feedback_correct,
        'feedback_incorrect' => $feedback_incorrect
    );
    //ddd($feedback_correct, $feedback_incorrect);
    $update_feedback = $eot_quiz->updateFeedback($data, $question_id);
    if($update_feedback)
    {
        $url ='/dashboard?part=manage_quiz_questions&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id; 
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }
    else 
    {
        die('Your feedback could not be saved');
    }
}
else
{
    $quiz_question = $eot_quiz->get_question_by_id($question_id);
    //d($quiz_question);
?>
<div class="bs">
    <div class="panel panel-default">
        <form method="POST" action="#">
            <div class="panel-heading">
              <h3 class="panel-title"><?= $quiz_question['quiz_question']?></h3>
            </div>
            <div class="panel-body">
              <form>
                    <div class="form-group">
                      <label for="feedback_correct">Feedback for correct answer</label>
                      <input type="text" class="form-control" name="feedback_correct" placeholder="Correct Feedback" value='<?= $quiz_question['feedback_correct']?>'>
                    </div>
                    <div class="form-group">
                      <label for="feedback_incorrect">Feedback for incorrect answer</label>
                      <input type="text" class="form-control" name="feedback_incorrect" placeholder="Incorrect Feedback" value='<?= $quiz_question['feedback_incorrect']?>'>
                    </div>
                    <input type='hidden' name='question_id' value="<?= $question_id ?>" />
                    <input type='hidden' name='quiz_id' value="<?= $quiz_id ?>" />
                    <input type='hidden' name='subscription_id' value="<?= $subscription_id ?>" />
                    <input type='hidden' name='feedback' value="true" />
                    <button type="submit" class="btn btn-default">Update Feedback</button>
                  </form>
            </div>
            <div class="panel-footer"><a href="/dashboard/?part=update_quiz_questions&question_id=<?= $question_id?>&quiz_id=<?= $quiz_id?>&subscription_id=<?= $subscription_id ?>" class="btn btn-success pull-right">Take me back to the Question</a><div style="clear:both"></div></div>
        </form>
    </div>
</div>
<?php
}
?>
