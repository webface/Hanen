<?php
$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
global $current_user;
$user_id = $current_user->ID; // Wordpress user ID
$org_id = get_org_from_user($user_id);
$quiz_id=filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
$subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
$eot_quiz = new EotQuizData();

if (isset($_POST['submit'])) 
{

    $data = array(
        'name' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_POST['quizName'])),
        'description' => filter_var($_POST['quizDescription']),
        'org_id' => $org_id,
        'user_id' => $user_id,
        'num_attempts'=>$_POST['quizAttempts'],
        'passing_score'=>$_POST['passing_score'],
        'time_limit'=>'00:'.$_POST['quizTimeText'].':00',
        //'date_created'=>current_time('Y-m-d')
    );


    $quiz_updated = $eot_quiz->updateQuiz($data,$quiz_id);
    if($quiz_updated)
    {
        wp_redirect(home_url('dashboard?part=manage_quiz&subscription_id='.$subscription_id));
        exit();
    }
}
?>
<?php
if(current_user_can( "is_director" ))
{
    
?>
<?php
$quiz=$eot_quiz->get_quiz_by_id($quiz_id);
?>
<h3><?php echo ($quiz['name']);?></h3>

<div class="bs well" style="padding:10px">
    <form action="/dashboard?part=update_quiz" method="POST">
        <div class="bs form-group">
            <label for="quizName">Quiz Name*</label>
            <input type="text" class="bs form-control" id="quizName" name="quizName" placeholder="Quiz Name" value="<?php echo ($quiz['name']);?>">
        </div>
        <div class="bs form-group">
            <label for="quizDescription">Quiz Description*</label>
            <textarea class="bs form-control" rows="3" id="quizDescription" name="quizDescription"><?= ($quiz['description']);?></textarea>
        </div>
        <div class="bs row">
            <div class="bs col-xs-6">
                <div class="bs form-group">
                    <label for="quizAttempts">Number of attempts allowed*<em>Leave 0 for unlimited</em></label>
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
                    <label for="quizTime">Time limit for the quiz*<em>(in minutes)</em></label>
                    <input
                        id="quizTime"
                        type="range"
                        min="1"                    
                        max="60"                  
                        step="1"                   
                        value="<?= date('i', strtotime($quiz['time_limit']))?>"                 
                        data-orientation="horizontal" 
                        onclick='quizTimeText.value=this.value'
                        >
                    <input type="text" name="quizTimeText" value='<?= date('i', strtotime($quiz['time_limit']));?>' readonly>
                </div>
            </div>
        </div>
        <div class="bs row">
            <div class="bs col-xs-6"><label for="passing_score">Passing Score../<?= $quiz['questions'];?> questions</label>
                <input type="text" name="passing_score" class="bs form-control" value="<?= ($quiz['passing_score']) ? $quiz['passing_score']: 0; ?>"/>
            </div>
            <div class="bs col-xs-6"></div>
        </div>
        <input type="hidden" name="quiz_id" value="<?= $quiz_id; ?>">
        <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>">
        <input type="submit" class="bs btn btn-primary pull-right" name="submit" value="Update">
        <span class="clearfix"></span>
    </form>
</div>

<script>
    $(document).ready(function () {

        $('form').submit(function () {
            var errors = false;
            if ($.trim($('#quizName').val()) == '') 
            {
                errors = true;
            }
            if ($.trim($('#quizDescription').val()) == '') 
            {
                errors = true;
            }
            if (!errors) 
            {
                return true;
            } 
            else 
            {
                alert("Name and Description fields are mandatory");
                return false;
            }
        })
    })
</script>

<?php 
}
else
        {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            return;
          }

?>

