<?php
    $path = WP_PLUGIN_DIR . '/eot_quiz/';
    require $path . 'public/class-eot_quiz_data.php';
    global $current_user;
    $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
    $org_id = get_org_from_user($user_id);
    $eot_quiz = new EotQuizData();
    $question_id = filter_var($_REQUEST['question_id'] , FILTER_SANITIZE_NUMBER_INT);
    $quiz_id = filter_var($_REQUEST['quiz_id'] , FILTER_SANITIZE_NUMBER_INT);
    $subscription_id = filter_var($_REQUEST['subscription_id'] , FILTER_SANITIZE_NUMBER_INT);
    $question = $eot_quiz->get_question_by_id($question_id);
if(!verifyQuiz($quiz_id))
{
    die(__("This quiz does not belong to you", 'EOT_LMS'));
}
if(!verifyQuizQuestion($quiz_id, $question_id))
{
    die(__("This quiz question does not belong to you", 'EOT_LMS'));
}
    if(current_user_can( "is_director" ) || current_user_can("is_sales_manager"))
    {
    
?>
        <h3><?php echo $question['quiz_question'] ?></h3>
        <span class="note">
            <a href="/dashboard?part=manage_quiz_questions&quiz_id=<?= $quiz_id ?>&subscription_id=<?=$subscription_id ?>&user_id=<?=$user_id ?>" onclick="load('load_quiz')"><em><?= __('Click here to return to quiz', 'EOT_LMS')?></em></a>
        </span>
<?php
        $answers = $eot_quiz->get_question_answers($question_id);
        if ($answers) 
        {
            // Tables that will be displayed in the front end.
            $answersTableObj = new stdClass();
            $answersTableObj->rows = array();
            $answersTableObj->headers = array(
                __('Answer', 'EOT_LMS') => 'left',
                __('Correct', 'EOT_LMS') => 'center',
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
        echo "<br><br><span class='bs done-btn'><a href='?part=manage_quiz&subscription_id=".$subscription_id."&user_id=".$user_id."' class='btn btn-success'>".__("I'm done for now", "EOT_LMS")."&nbsp;&nbsp;<i class='fa fa-exclamation'></i></a></span>";
        echo "<button class='bs btn btn-primary pull-right editBtn' onclick='editQuestion(\"" . $question['quiz_question_type'] . "\")'>".__('Edit', 'EOT_LMS')."</button>";
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
                    '<div class="bs form-group"><label for="question"><?= __('Question', 'EOT_LMS')?></label>' +
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
                    '<div class="bs form-group"><label for="question"><?= __('Question', 'EOT_LMS')?></label>' +
                    '<input type="text" class="bs form-control" name="question" />' +
                    '<input type="hidden" class="bs form-control" name="question_id" />' +
                    '</div>' +
                    '</div>' +
                    '<div class="bs panel-body">' +
                    '</div>' +
                    '<div class="bs panel-footer">' +
                    '<span><em><?= __('Remember to Update when youre done', 'EOT_LMS')?></em><span><br>' +
                    '<a class="bs btn btn-primary updateBtn"><?= __('Update', 'EOT_LMS')?></a>&nbsp;' +
                    '<a class="bs btn btn-primary cancelBtn"><?= __('Cancel', 'EOT_LMS')?></a>' +
                    '<a class="bs btn btn-primary addAnswerBtn pull-right"><?= __('New Answer', 'EOT_LMS')?></a>' +
                    '</div></form>' +
                    '</div>';
            function editQuestion(type) 
            {
                //console.log("Editing: " + type);
                $('.dataTables_wrapper, .done-btn').hide();
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
        echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.", 'EOT_LMS');
        return;
      }
?>