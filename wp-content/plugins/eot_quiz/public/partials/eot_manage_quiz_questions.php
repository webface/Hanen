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
$admin_ajax_url = admin_url('admin-ajax.php');
if (isset($_POST['submit'])) 
{
    if ($_POST['type'] == 'choice') 
    {
        if (!isset($_POST['save_question']) || !wp_verify_nonce($_POST['save_question'], 'save_question')) 
        {
            print 'Sorry, your nonce did not verify.';
            exit;
        }
        unset($_POST['submit']);
        $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
        unset($_POST['_wp_http_referer']);
        unset($_POST['quiz_id']);
        unset($_POST['type']);
        unset($_POST['save_question']);

        $fields = array();//the question title
        $questionFields = array();// the question answers

        foreach ($_POST as $key => $value) 
        {
            $arr = explode('_', $key);
            //question
            if(isset($arr[2]))//prevent php from throwing a notice error undefined offset
            {
                
            }
            else
            {
                $arr[2]=NULL;
                
            }
            if ($arr[0] == "question" && $arr[2] != 'answer') 
            {
                $fields[$arr[0] . $arr[1]]['label'] = $value;
                $fields[$arr[0] . $arr[1]]['isRequired'] = 0;
                $fields[$arr[0] . $arr[1]]['type'] = 'radio';
                unset($key);
            } 
            else 
            {
                $questionFields[$key] = $value;
                unset($key);
            }
        }


        foreach ($questionFields as $key => $value) 
        {
            $arr = explode('_', $key);
            if ($arr[0] == "question" && $arr[2] == 'answer') 
            {
                $is_correct = FALSE;
                foreach ($questionFields as $k => $v) {
                    $a = explode('_', $k);

                    if ($a[0] == 'correctquestion' && $a[1] == $arr[1] && $v == $key) 
                    {
                        $is_correct = TRUE;
                    }
                }
                //answer
                $fields[$arr[0] . $arr[1]]['choices'][] = array(
                    'title' => $value,
                    'text' => $value,
                    'is_correct' => $is_correct
                );

                unset($key);
            }
        }
        $data = array(
            'quiz_id' => $quiz_id,
            'quiz_question' =>preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($fields['question0']['label'])),
            'quiz_question_type' => 'radio'
        );
        $question_id = $eot_quiz->addQuestion($data);
        foreach ($fields['question0']['choices'] as $choice) 
        {
            $data = array(
                'question_id' => $question_id,
                'answer_text' =>preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($choice['title'])),
                'answer_correct' => $choice['is_correct']
            );
            $eot_quiz->addAnswer($data);
        }
    } 
    elseif ($_POST['type'] == 'checkbox') 
    {
        if (
                !isset($_POST['save_question']) || !wp_verify_nonce($_POST['save_question'], 'save_question')
        ) {

            print 'Sorry, your nonce did not verify.';
            exit;
        }
        unset($_POST['submit']);
        unset($_POST['save_question']);
        $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
        unset($_POST['quiz_id']);
        unset($_POST['type']);
        unset($_POST['_wp_http_referer']);

        $fields = array();
        $questionFields = array();
        foreach ($_POST as $key => $value) 
        {
            $arr = explode('_', $key);
            //question
            if(isset($arr[2])){//prevent php from throwing a notice error undefined offset
                
            }
            else
            {
                $arr[2]=NULL;
                
            }
            if ($arr[0] == "question" && $arr[2] != 'answer') 
            {
                $fields[$arr[0] . $arr[1]]['label'] = $value;
                $fields[$arr[0] . $arr[1]]['isRequired'] = 0;
                $fields[$arr[0] . $arr[1]]['type'] = 'checkbox';
                unset($key);
            } 
            else 
            {
                $questionFields[$key] = $value;
                unset($key);
            }
        }

        foreach ($questionFields as $key => $value) 
        {
            $arr = explode('_', $key);
            if ($arr[0] == "question" && $arr[2] == 'answer') 
            {
                $is_correct = FALSE;
                foreach ($questionFields as $k => $v) 
                {
                    $a = explode('_', $k);
                    if (($a[0] == 'correctquestion') && ($a[4] == $arr[3]) && ($v == 'on')) 
                    {
                        $is_correct = TRUE;
                    }
                }
                //answer
                $fields[$arr[0] . $arr[1]]['choices'][] = array(
                    'title' => $value,
                    'text' => $value,
                    'is_correct' => $is_correct
                );
                unset($key);
            }
        }
        $data = array(
            'quiz_id' => $quiz_id,
            'quiz_question' =>preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($fields['question0']['label'])),
            'quiz_question_type' => 'checkbox'
        );
        $question_id = $eot_quiz->addQuestion($data);
        foreach ($fields['question0']['choices'] as $choice) 
        {
            $data = array(
                'question_id' => $question_id,
                'answer_text' =>preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($choice['title'])),
                'answer_correct' => $choice['is_correct']
            );
            $eot_quiz->addAnswer($data);
        };
    } 
    elseif ($_POST['type'] == 'text') 
    {
        if (
                !isset($_POST['save_question']) || !wp_verify_nonce($_POST['save_question'], 'save_question')
        ) {

            print 'Sorry, your nonce did not verify.';
            exit;
        }
        unset($_POST['submit']);
        unset($_POST['save_question']);
        $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
        unset($_POST['quiz_id']);
        unset($_POST['type']);
        unset($_POST['_wp_http_referer']);

        $data = array(
            'quiz_id' => $quiz_id,
            'quiz_question' =>preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_POST['question_0'])),
            'quiz_question_type' => 'text'
        );
        $question_id = $eot_quiz->addQuestion($data);
        $data = array(
            'question_id' => $question_id,
            'answer_text' => preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_POST['question_0_answer_0'])),
            'answer_correct' => 1
        );
        $eot_quiz->addAnswer($data);
    }
}
?>
<script>
    $(document).ready(function () {
        $(document).bind('success.add_title',
                function (event, data)
                {
                    //console.log(data);
                    if (data.success === true) 
                    {
                        var settings = {
                            label: data.title,
                            choices: []
                        }
                        $("#createQuestion").show();
                        addQuestion(data.type, settings, count);
                        count++;
                        jQuery(document).trigger('close.facebox');
                    }
                }
        )
    })
    var count = 0;
    function addQuestion(type, settings, num) {
        var html = '<div class="bs panel panel-default" id="quiz_question_' + num + '">' +
                '<div class="bs panel-heading" role="tab" id="heading' + num + '">' +
                '<h4 class="bs panel-title">' +
                '<input type="hidden" name="question_' + num + '" id="question_' + num + '" value=""/>' +
                '<input type="hidden" name="type" id="type" value="' + type + '"/>' +
                '<a role="button" data-toggle="collapse" data-parent="#accordion"' +
                'href="#collapse' + num + '" aria-expanded="false" aria-controls="collapse' + num + '">' +
                'The Question' +
                '</a>' +
                '</h4>' +
                '</div>' +
                '<div id="collapse' + num + '" class="bs panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' + num + '">' +
                '<div class="bs panel-body">' +
                '<span style="color:red" class="editTitle" onclick="editTitle(' + num + ')"><i>Edit Question</i></span><br>' +
                '<strong>Answers</strong><i>&nbsp;Indicate which is correct.</i>' +
                '<button class="bs btn btn-primary pull-right addBtn">Add Answer</button></div' +
                '<div class="bs clearfix"></div>' +
                '<div class="bs panel-content">' +
                '</div>' +
                '&nbsp;&nbsp;<span style="color:red" class="deleteQuestion" onclick="deleteQuestion(' + num + ')"><i>Delete Question</i></span><br>' +
                '</div>' +
                '</div>' +
                ' </div>';
        switch (type) 
        {
            case 'choice':
                $(html).quizQuestionRadio({settings: settings, id: num});
                break;
            case 'checkbox':
                $(html).quizQuestionCheck({settings: settings, id: num});
                break;
            case 'text':
                $(html).quizQuestionText({settings: settings, id: num});
                break;
        }

        $(".createBtn").hide();

    }

    function addNewQuestion(type) 
    {
        var label = prompt("Please enter your question");

        if (label === null || label === false || label === '') 
        { // Canceled
            //break; // Leave this out, if the name is mandatory
        } 
        else 
        {
            var settings = {
                label: label,
                choices: []
            }
            $("#createQuestion").show();
            addQuestion(type, settings, count);
            count++;
        }
    }
    $(document).bind('success.update_title',
            function (event, data)
            {
                if (data.success === true) 
                {
                    $('#heading0 .panel-title a').html(data.title);
                    $('#heading0 #question_0').val(data.title);
                    jQuery(document).trigger('close.facebox');
                }
            }
    )
    function editTitle() 
    {

        $.facebox(function () {

            $.ajax({
                data: {'title': $('#heading0 .panel-title a').html()},
                error: function () 
                {
                    $.facebox('There was an error loading the title. Please try again shortly.');
                },
                success: function (data)
                {
                    $.facebox(data);
                },
                type: 'post',
                url: '<?= $admin_ajax_url; ?>?action=get_quiz_form&form_name=update_title'
            });

        });
    }
    function deleteQuestion(num) {
        $('#quiz_question_' + num).remove();
        $("#createQuestion").hide();
        $(".createBtn").show();
        count = 0;
    }
    function removeAnswer(self, plugin) {
        plugin.removeAnswer(self.attr('data-id'));
    }
    function addAnswer(self, plugin, element, options, count) {
        var entry = plugin.answer('question_' + options.id + '_answer_' + count, '', options.id);
        $(element).find(".panel-content").append(entry);
        $(element).find('.deleteBtn').click(function (e) {
            e.preventDefault();
            removeAnswer($(this), plugin);
            return false;
        });
    }
    function stopRKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode == 13) && (node.type == "text")) {
            return false;
        }
    }

    document.onkeypress = stopRKey;
    $(document).bind('success.delete_answer',
            function (event, data)
            {
                if (data.success === true) {
                    window.location.href = "/dashboard?part=manage_quiz_questions&quiz_id=<?=$quiz_id?>&subscription_id=<?= $subscription_id ?>";
                }
            }
    )


</script>
<?php
$quiz = $eot_quiz->get_quiz_by_id($quiz_id);
$quiz_id = $quiz['ID'];
?>
<h3><?= $quiz['name']; ?></h3>
<?php $questions = $eot_quiz->get_quiz_questions($quiz_id); ?>


<?php if ($questions) { ?>
    <?php
    // Tables that will be displayed in the front end.
    $questionsTableObj = new stdClass();
    $questionsTableObj->rows = array();
    $questionsTableObj->headers = array(
        'Question' => 'left',
        'Type' => 'center',
        'Num Answers' => 'center',
        'Actions' => 'center',
        'Delete' => 'center',
    );

    // Creating rows for the table
    foreach ($questions as $question) {
        $questionsTableObj->rows[] = array(
            '<span>' . stripslashes($question['quiz_question']) . '</span>', // Transaction Date
            $question['quiz_question_type'],
            $question['answers'],
            '<a href="/dashboard?part=update_quiz_questions&question_id=' . $question['ID'] . '&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id . '" onclick="load(\'load_edit_quiz\')">Edit question & answers</a>', // The name of the camp,
            '<a href="' . $admin_ajax_url . '?action=get_quiz_form&form_name=delete_question&question_id=' . $question['ID'] . '&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id . '" data-type="Question" class="delete" rel="facebox"><i class="fa fa-trash" aria-hidden="true"></i></a>',);
    }
    CreateDataTable($questionsTableObj); // Print the table in the page
    echo "<a class='bs btn btn-primary  createBtn' href='" . $admin_ajax_url . "?action=get_quiz_form&form_name=add_title&type=choice' rel='facebox'>Add Choice Question&nbsp;<span class='fa fa-circle-o'></span></a>";
    echo "&nbsp;<a class='bs btn btn-primary  createBtn' href='" . $admin_ajax_url . "?action=get_quiz_form&form_name=add_title&type=checkbox' rel='facebox'>Add Checkbox Question&nbsp;<span class='fa fa-check-square-o'></span></a>";
    echo "&nbsp;<a class='bs btn btn-primary  createBtn' href='" . $admin_ajax_url . "?action=get_quiz_form&form_name=add_title&type=text' rel='facebox'>Add Text Question&nbsp;<span class='fa fa-file-text-o'></span></a>";
    ?>
    <?php
} else {
    echo "<div class='bs'><div class='well well-lg'>No Questions yet!</div></div><a class='bs btn btn-primary  createBtn' href='" . $admin_ajax_url . "?action=get_quiz_form&form_name=add_title&type=choice' rel='facebox'>Add Choice Question&nbsp;<span class='fa fa-circle-o'></span></a>";
    echo "&nbsp;<a class='bs btn btn-primary  createBtn' href='" . $admin_ajax_url . "?action=get_quiz_form&form_name=add_title&type=checkbox' rel='facebox'>Add Checkbox Question&nbsp;<span class='fa fa-check-square-o'></span></a>";
    echo "&nbsp;<a class='bs btn btn-primary  createBtn' href='" . $admin_ajax_url . "?action=get_quiz_form&form_name=add_title&type=text' rel='facebox'>Add Text Question&nbsp;<span class='fa fa-file-text-o'></span></a>";
}
?>


<div id="createQuestion" style="display:none">
    <h3>Create Question</h3>
    <p>Click the question name to create and edit answers before saving.</p>
    <form id="SaveQuestion" action="/dashboard?part=manage_quiz_questions&quiz_id=<?= $quiz_id; ?>&subscription_id=<?= $subscription_id ?>" method="POST">
        <?php wp_nonce_field('save_question', 'save_question'); ?>
        <div class="bs panel-group" id="accordion" role="tablist" aria-multiselectable="true"></div>
        <input type="hidden" name="quiz_id" value="<?= $quiz_id; ?>"/>
        <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>"/>
        <input type="submit" class="bs btn btn-primary pull-right" name="submit" value="Save Question">
        <span class="clearfix"></span>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('a[rel*=facebox]').facebox();
        $('#SaveQuestion').submit(function () {
            if ($('#SaveQuestion #type').val() == "choice") {
                //console.log('creating a multiple choice question');
                if (!$("input[name='correctquestion_0']:checked").val()) {
                    alert('You didnt select a correct answer!');
                    return false;
                }

            }

        });
        $(document).bind('success.delete_question',
                function (event, data)
                {
                    if (data.success === true) {
                        location.reload();
                    }
                }
        )

    });


</script>




