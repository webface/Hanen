<?php
$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
global $current_user;


$quiz_id=filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
$subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
if($subscription_id == 0)// in case its an eot quiz
{
    $org_id = 0;
    $user_id = 0; // Wordpress user ID
}
else
{
    $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
    $org_id = get_org_from_user($user_id);

}
if(!verifyQuiz($quiz_id))
{
    die("This quiz does not belong to you");
}
$eot_quiz = new EotQuizData();

if (isset($_POST['submit'])) 
{

    // Process quiz time.
    $quizTime = convertMinutesToTime($_POST['quizTimeText']);
    $data = array(
        'name' => preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_POST['quizName'])),
        'description' => filter_var($_POST['quizDescription']),
        'org_id' => $org_id,
        'user_id' => $user_id,
        'num_attempts'=>$_POST['quizAttempts'],
        'passing_score'=>$_POST['passing_score'],
        'time_limit'=> $quizTime,
        'num_questions_to_display'=>$_POST['num_questions_to_display']
    );


    $quiz_updated = $eot_quiz->updateQuiz($data,$quiz_id);
    if($quiz_updated)
    {
        if($subscription_id == 0)// in case its an eot quiz
        {
            wp_redirect(home_url('dashboard?part=manage_quiz_eot&subscription_id='.$subscription_id.'&user_id='.$user_id));
        }
        else
        {
        wp_redirect(home_url('dashboard?part=manage_quiz&subscription_id='.$subscription_id.'&user_id='.$user_id));
        }
        exit();
    }
}
?>
<?php
if(current_user_can( "is_director" ) || current_user_can("is_sales_manager"))
{
    $quiz = $eot_quiz->get_quiz_by_id($quiz_id);

    if (isset($quiz['num_questions_to_display']) && empty($quiz['num_questions_to_display']))
    {
        $quiz['num_questions_to_display'] = isset($quiz['questions']) ? $quiz['questions'] : 0;
    }
?>
<h3><?php echo ($quiz['name']);?></h3>

<div class="bs well" style="padding:10px">
    <form action="/dashboard?part=update_quiz" method="POST">
        <div class="bs form-group">
            <label for="quizName"><?= __('Quiz Name *', 'EOT_LMS')?></label>
            <input type="text" class="bs form-control" id="quizName" name="quizName" placeholder="Quiz Name" value="<?php echo esc_attr($quiz['name']);?>">
        </div>
        <div class="bs form-group">
            <label for="quizDescription"><?= __('Quiz Description', 'EOT_LMS')?></label>
            <textarea class="bs form-control" rows="3" id="quizDescription" name="quizDescription"><?= esc_attr($quiz['description']);?></textarea>
        </div>
        <div class="bs row">
            <div class="bs col-xs-6">
                <div class="bs form-group">
                    <label for="quizAttempts"><?= __('Number of attempts allowed *', 'EOT_LMS')?><br><em><?= __('Leave 0 for unlimited', 'EOT_LMS')?></em></label>
                    <select class="bs form-control"  id="quizAttempts" name="quizAttempts">
                        <option value="0" <?= ($quiz['num_attempts']==0) ?  'selected': ''; ?>>0</option>
                        <option value="1" <?= ($quiz['num_attempts']==1) ?  'selected': ''; ?>>1</option>
                        <option value="2" <?= ($quiz['num_attempts']==2) ?  'selected': ''; ?>>2</option>
                        <option value="3" <?= ($quiz['num_attempts']==3) ?  'selected': ''; ?>>3</option>
                        <option value="4" <?= ($quiz['num_attempts']==4) ?  'selected': ''; ?>>4</option>
                        <option value="5" <?= ($quiz['num_attempts']==5) ?  'selected': ''; ?>>5</option>
                        <option value="6" <?= ($quiz['num_attempts']==6) ?  'selected': ''; ?>>6</option>
                        <option value="7" <?= ($quiz['num_attempts']==7) ?  'selected': ''; ?>>7</option>
                        <option value="8" <?= ($quiz['num_attempts']==8) ?  'selected': ''; ?>>8</option>
                        <option value="9" <?= ($quiz['num_attempts']==9) ?  'selected': ''; ?>>9</option>
                        <option value="10" <?= ($quiz['num_attempts']==10) ?  'selected': ''; ?>>10</option>
                    </select>
                </div>
            </div>
            <div class="bs col-xs-6">
                <div class="bs form-group">
                    <label for="quizTime"><?= __('Time limit for the quiz *', 'EOT_LMS')?><br><em><?= __('(in minutes)', 'EOT_LMS')?></em></label>
                    <select class="bs form-control"  id="quizTime" name="quizTimeText">
                        <option value="5" <?= (date('i', strtotime($quiz['time_limit']))==5) ?  'selected': ''; ?>>5</option>
                        <option value="10" <?= (date('i', strtotime($quiz['time_limit']))==10) ?  'selected': ''; ?>>10</option>
                        <option value="15" <?= (date('i', strtotime($quiz['time_limit']))==15) ?  'selected': ''; ?>>15</option>
                        <option value="20" <?= (date('i', strtotime($quiz['time_limit']))==20) ?  'selected': ''; ?>>20</option>
                        <option value="25" <?= (date('i', strtotime($quiz['time_limit']))==25) ?  'selected': ''; ?>>25</option>
                        <option value=30 <?= (date('i', strtotime($quiz['time_limit']))==30) ?  'selected': ''; ?>>30</option>
                        <option value=45 <?= (date('i', strtotime($quiz['time_limit']))==45) ?  'selected': ''; ?>>45</option>
                        <option value="60" <?= (date('i', strtotime($quiz['time_limit']))==60) ?  'selected': ''; ?>>60</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="bs row">
            <div class="bs col-xs-6"><label for="num_questions_to_display"><?= __('Number of Questions to display', 'EOT_LMS')?> <span class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="You have a total of <?= $quiz['questions']?> question(s) in this quiz but you do not have to display all of them. You can select to display only a subset of the questions. We will randomly display that number of questions to your staff."></span></label>
                <input type="text" id="num_questions_to_display" name="num_questions_to_display" class="bs form-control" value="<?= ($quiz['num_questions_to_display']) ? $quiz['num_questions_to_display']: 0; ?>"/>
            </div>
            <div class="bs col-xs-6"><label for="passing_score"><?= __('Passing Score', 'EOT_LMS')?> <span class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="Please enter the number of questions a staff member has to answer correctly in order to pass this quiz. Please note that you should set the number of questions based on the number of displayed questions. Eg. If your quiz has 15 questions but you're only displaying 10, a passing requirement of 80% would be 8."></span></label>
                <input type="text" id="passing_score" name="passing_score" class="bs form-control" value="<?= ($quiz['passing_score']) ? $quiz['passing_score']: 0; ?>"/>
            </div>
        </div>
        <div class="bs row">
            <div class="bs col-xs-6">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id; ?>">
            <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            </div>
            <div class="bs col-xs-6">
            <input type="submit" class="bs btn btn-primary pull-right" name="submit" value="Update">
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {

        $('form').submit(function () {
            if ($.trim($('#quizName').val()) == '') 
            {
                alert(<?= __("Quiz Name is mandatory!", 'EOT_LMS')?>);
                return false;
            }
            
            if ($('#passing_score').val() > $('#num_questions_to_display').val())
            {
                alert(<?= __("Can't have a passing score that's more than the number of questions you are displaying!", 'EOT_LMS')?>);
                return false;
            }

            return true;
        });

        $('[data-toggle="tooltip"]').tooltip(); 

    })
</script>

<?php 
}
else
        {
            echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.",'EOT_LMS');
            return;
          }

?>

