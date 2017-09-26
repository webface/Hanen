<?php
$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
global $current_user;
$user_id = $current_user->ID; // Wordpress user ID
$org_id = get_org_from_user($user_id);
$eot_quiz = new EotQuizData();
$subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
$admin_ajax_url = admin_url('admin-ajax.php');
//$str = "{}A man and the man's dog walk into\"!@#$%^&*()_- a bar?";
//$title = preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($str));
//echo $title."<br>";
if (isset($_POST['submit'])) 
{
    if (!isset($_POST['submit_quiz']) || !wp_verify_nonce($_POST['submit_quiz'], 'submit_quiz')) 
    {
        print 'Sorry, your nonce did not verify.';
        exit;
    }

    $quiz_name = preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_POST['quizName']));
    $data = array(
        'name' => $quiz_name,
        'description' => filter_var($_POST['quizDescription'],FILTER_SANITIZE_STRING),
        'org_id' => $org_id,
        'user_id' => $user_id,
        'num_attempts' => $_POST['quizAttempts'],
        'time_limit' => '00:' . $_POST['quizTimeText'] . ':00',
        'date_created' => current_time('Y-m-d')
    );


    $quiz_id = $eot_quiz->addQuiz($data);
    $url ='/dashboard?part=manage_quiz_questions&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id; 
    ob_start();
    header('Location: '.$url);
    ob_end_flush();
    die();
}
?>

<h3>My Quizzes</h3>
<span><span class="fa fa-exclamation"></span><em>Edit Quiz Details after creating questions to set the passing score and time limit</em></span><br>
<?php
$quizzes = $eot_quiz->getQuizzes($org_id, $user_id);
?>


<?php 
if ($quizzes) 
{  
    // Tables that will be displayed in the front end.
    $quizTableObj = new stdClass();
    $quizTableObj->rows = array();
    $quizTableObj->headers = array(
        'Quiz Name' => 'left',
        '# Questions' => 'center',
        'Displayed Questions' => 'center',
        'Passing Score' => 'center',
        'Time Limit' => 'center',
        'Questions' => 'center',
        'Actions' => 'center'
    );

    // Creating rows for the table
    foreach ($quizzes as $quiz) 
    {

        $time_limit = date('i', strtotime($quiz['time_limit']));

        $quizTableObj->rows[] = array(
            '<span>' . stripslashes($quiz['name']) . '</span>',
            $quiz['questions'],
            $quiz['num_questions_to_display'],
            (($quiz['passing_score']) ? $quiz['passing_score'] : 0) . " of " . $quiz['num_questions_to_display'],
            $time_limit . " minutes",
            '<a href="/dashboard?part=manage_quiz_questions&quiz_id=' . $quiz['ID'] . '&subscription_id=' . $subscription_id . '" onclick="load(\'load_edit_quiz\')">Edit Questions</a>',
            '<a  href="/dashboard?part=update_quiz&quiz_id=' . $quiz['ID'] . '&subscription_id=' . $subscription_id . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>
            &nbsp;&nbsp&nbsp;
            <a  href="/dashboard?part=view_quiz&quiz_id=' . $quiz['ID'] . '&subscription_id=' . $subscription_id . '"><i class="fa fa-eye" aria-hidden="true"></i></a>
            &nbsp;&nbsp&nbsp;
            <a href="' . $admin_ajax_url . '?action=get_quiz_form&form_name=delete_quiz&quiz_id=' . $quiz['ID'] . '&subscription_id=' . $subscription_id . '" data-type="Quiz" class="delete" rel="facebox"><i class="fa fa-trash" aria-hidden="true"></i></a>'
        );
    }
    CreateDataTable($quizTableObj); // Print the table in the page
    ?>
    <?php
} 
else 
{
    echo "<div class='bs'><div class='well well-lg'>No Quizzes yet!</div></div>";
}
?>



<h3>Create Quiz</h3>
<span><em>Create a new quiz below</em></span><br>

<div class="bs well" style="padding:10px">
    <form action="/dashboard?part=manage_quiz" method="POST">
        <?php wp_nonce_field('submit_quiz', 'submit_quiz'); ?>
        <div class="bs form-group">
            <label for="quizName">Quiz Name*</label>
            <input type="text" class="bs form-control" id="quizName" name="quizName" placeholder="Quiz Name">
        </div>
        <div class="bs form-group">
            <label for="quizDescription">Quiz Description*</label>
            <textarea class="bs form-control" rows="3" id="quizDescription" name="quizDescription"></textarea>
        </div>
        <div class="bs row">
            <div class="bs col-xs-6">
                <div class="bs form-group">
                    <label for="quizAttempts">Number of attempts allowed*<em>Leave 0 for unlimited</em></label>
                    <select class="bs form-control"  id="quizAttempts" name="quizAttempts">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>
            <div class="bs col-xs-6">
                <div class="bs form-group">
                    <label for="quizTime">Time limit for the quiz*<em>(in minutes)</em></label>
                    <select class="bs form-control"  id="quizTime" name="quizTimeText">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                        <option value=30>30</option>
                        <option value=45>45</option>
                        <option value="60">60</option>
                    </select>
                </div>
            </div>
        </div>
        <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>">
        <input type="submit" class="bs btn btn-primary pull-right" name="submit" value="Submit">
        <span class="clearfix"></span>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('a[rel*=facebox]').facebox();
        $('form').submit(function () 
        {
            var errors = false;
            if ($.trim($('#quizName').val()) == '') 
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

        $(document).bind('success.delete_quiz',
                function (event, data)
                {
                    if(data.success===true)
                    {
                        window.location.href = "/dashboard?part=manage_quiz&subscription_id=<?= $subscription_id ?>";
                    }
                }
       )
    });
</script>



