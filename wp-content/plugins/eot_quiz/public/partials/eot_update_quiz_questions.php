<?php
$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
global $current_user;
$user_id = $current_user->ID; // Wordpress user ID
$org_id = get_org_from_user($user_id);
$eot_quiz = new EotQuizData();
$question_id = filter_var($_REQUEST['question_id'] , FILTER_SANITIZE_NUMBER_INT);
$quiz_id = filter_var($_REQUEST['quiz_id'] , FILTER_SANITIZE_NUMBER_INT);
$subscription_id = filter_var($_REQUEST['subscription_id'] , FILTER_SANITIZE_NUMBER_INT);
$question = $eot_quiz->get_question_by_id($question_id);
?>
<?php
if(current_user_can( "is_director" ))
{
    
?>
<h3><?php echo $question['quiz_question'] ?></h3>
<span class="note"><a href="/dashboard?part=manage_quiz_questions&quiz_id=<?= $quiz_id ?>&subscription_id=<?=$subscription_id ?>" onclick="load('load_quiz')"><em>Click here to return to quiz</em></a></span>
<?php
$answers = $eot_quiz->get_question_answers($question_id);
if ($answers) 
{
    // Tables that will be displayed in the front end.
    $answersTableObj = new stdClass();
    $answersTableObj->rows = array();
    $answersTableObj->headers = array(
        'Answer' => 'left',
        'Correct' => 'center',
    );

    // Creating rows for the table
    foreach ($answers as $answer) 
    {
        $answersTableObj->rows[] = array(
            '<span>' . stripslashes($answer['answer_text']) . '</span>', // Transaction Date
            ($answer['answer_correct']) ? '<span class="fa fa-check"></span>' : '',
        );
    }
    CreateDataTable($answersTableObj); // Print the table in the page"
}
echo "<button class='bs btn btn-primary pull-right editBtn' onclick='editQuestion(\"" . $question['quiz_question_type'] . "\")'>Edit</button>";

?>
<div id="editDiv"></div>
<script>
    $(document).ready(function () {

        $('.delete').bootstrap_confirm_delete( );
    });
    var question =<?= json_encode($question); ?>;
    var answers =<?= json_encode($answers); ?>;
    var html = '<div class="bs panel panel-default">' +
            '<div class="bs panel-heading">' +
            '<div class="bs form-group"><label for="question">Question</label>' +
            '<input type="text" class="bs form-control" name="question" />' +
            '<input type="hidden" class="bs form-control" name="question_id" />' +
            '</div>' +
            '</div>' +
            '<div class="bs panel-body">' +
            '</div>' +
            '<div class="bs panel-footer"></div>' +
            '</div>';
    var html_form = '<form id="UpdateForm"><div class="bs panel panel-default">' +
            '<div class="bs panel-heading">' +
            '<div class="bs form-group"><label for="question">Question</label>' +
            '<input type="text" class="bs form-control" name="question" />' +
            '<input type="hidden" class="bs form-control" name="question_id" />' +
            '</div>' +
            '</div>' +
            '<div class="bs panel-body">' +
            '</div>' +
            '<div class="bs panel-footer">' +
            '<span><em>Remember to Update when youre done</em><span><br>' +
            '<a class="bs btn btn-primary updateBtn">Update</a>&nbsp;' +
            '<a class="bs btn btn-primary cancelBtn">Cancel</a>' +
            '<a class="bs btn btn-primary addAnswerBtn pull-right">New Answer</a>' +
            '</div></form>' +
            '</div>';
    function editQuestion(type) 
    {
        //console.log("Editing: " + type);
        $('.dataTables_wrapper').hide();
        $('.editBtn').hide();
        $('.note').hide();
        switch (type) 
        {
            case 'radio':
                $(html_form).editQuizQuestionRadio({question: question, answers: answers});
                break;
            case 'checkbox':
                $(html_form).editQuizQuestionCheckbox({question: question, answers: answers});
                break;
            case 'text':
                $(html).editQuizQuestionText({question: question, answers: answers});
                break;
        }
    }
</script>
<?php 
}
else
        {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            return;
          }

?>