<?php

$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
$eot_quiz = new EotQuizData();
global $current_user;
$user_id = $current_user->ID; // Wordpress user ID

//Required for angular because data array $_POST variables are screwy
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_POST = json_decode(file_get_contents('php://input'), true);

$quiz_id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
switch ($_REQUEST['part']) 
{
    case 'get_quiz':
        
        $quiz = $eot_quiz->get_quiz_data($quiz_id);
        echo json_encode($quiz);
        exit();
        break;
    case 'save_quiz':

        $data = array(
            'quiz_id' => $_POST['ID'],
            'user_id' => $user_id,
            'score' => $_POST['score'],
            'percentage' => $_POST['percentage'],
            'completed' => $_POST['completed'] === true ? 1 : 0,
            'passed' => $_POST['passed'] === true ? 1 : 0,
            'total_time' => $_POST['time_spent'],
            'date_attempted' => current_time('Y-m-d H:i:s'),
        );
        $attempt_id = $eot_quiz->add_quiz_attempt($data);

        $questions = $_POST['questions'];
        //var_dump($questions);
        foreach ($questions as $question) 
        {
            switch ($question['quiz_question_type']) 
            {
                case 'radio':
                    $data = array(
                        'quiz_id' => $_POST['ID'],
                        'user_id' => $user_id,
                        'question_id' => $question['ID'],
                        'answer' => 1
                    );
                    foreach ($question['possibilities'] as $answer) 
                    {
                        if ($answer['selected'] === true) {
                            $data['answer_id'] = $answer['ID'];
                            $attempt_id = $eot_quiz->add_quiz_result($data);
                        }
                    }

                    break;
                case 'checkbox':
                    foreach ($question['possibilities'] as $answer) 
                    {
                        $data = array(
                            'quiz_id' => $_POST['ID'],
                            'user_id' => $user_id,
                            'question_id' => $question['ID'],
                            'answer_id' => $answer['ID'],
                        );
                        if ($answer['selected'] === true) {
                            $data['answer'] = $answer['selected'] === true ? 1 : 0;
                            $attempt_id = $eot_quiz->add_quiz_result($data);
                        }
                    }
                    break;
                case 'text':
                    $data = array(
                        'quiz_id' => $_POST['ID'],
                        'user_id' => $user_id,
                        'question_id' => $question['ID'],
                        'answer' => $question['answer']
                    );
                    $attempt_id = $eot_quiz->add_quiz_result($data);
                    break;
            }
        }
        echo json_encode(array('message' => 'success', 'req' => $attempt_id));
        exit();
        break;
}
