<?php

$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
$eot_quiz = new EotQuizData();
global $current_user, $wpdb;
$user_id = $current_user->ID; // Wordpress user ID

//Required for angular because data array $_POST variables are screwy
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_POST = json_decode(file_get_contents('php://input'), true);
//error_log(json_encode($_POST));
switch ($_REQUEST['part']) 
{
    case 'get_quiz':
        $quiz_id = filter_var($_REQUEST['ID'], FILTER_SANITIZE_NUMBER_INT);
        $quiz = $eot_quiz->get_quiz_data($quiz_id,true);
        echo json_encode($quiz);
        exit();
        break;
    case 'save_quiz':
        $course_id = filter_var($_POST['course_id'],FILTER_SANITIZE_NUMBER_INT);
        $quiz_id = filter_var($_POST['ID'], FILTER_SANITIZE_NUMBER_INT);
        $data = array(
            'quiz_id' => $quiz_id,
            'user_id' => $user_id,
            'score' => $_POST['score'],
            'percentage' => $_POST['percentage'],
            'completed' => $_POST['completed'] === true ? 1 : 0,
            'passed' => $_POST['passed'] === true ? 1 : 0,
            'total_time' => $_POST['time_spent'],
            'date_attempted' => current_time('Y-m-d H:i:s'),
        );
        $main_attempt_id = $eot_quiz->add_quiz_attempt($data);
        
        $enrollments = getEnrollments(0, $user_id);
        $enrolled_courses = array_column($enrollments,'course_id');
        $quizzes_in_course = getQuizzesInCourse($course_id);

        $passed = 0;
        foreach ($quizzes_in_course as $required) 
        {
            $iPassed = $wpdb->get_row("SELECT passed FROM ".TABLE_QUIZ_ATTEMPTS. " WHERE quiz_id = ".$required['ID']." AND user_id = $user_id AND passed = 1", ARRAY_A);
            //error_log("The course name is: ".$required['name']);
            if($iPassed)
            {
                $passed++;
                //error_log("I passed: ".$course_id);
            }
        }
        if($passed >= count($quizzes_in_course))
        {
            $wpdb->update(TABLE_ENROLLMENTS, 
                    array(
                        'status' => 'completed'
                        ),
                    array(
                        'course_id' => $course_id,
                        'user_id' => $user_id
                    )
                    );
        }
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
                        'attempt_id' => $main_attempt_id
                        
                    );
                    foreach ($question['possibilities'] as $answer) 
                    {
                        if ($answer['selected'] === true) {
                            $data['answer_id'] = $answer['ID'];
                            $data['answer'] = $answer['selected'] === true ? 1 : 0;
                            $attempt_id = $eot_quiz->add_quiz_result($data);
                       }
                    }
                    unset($data['answer']);
                    unset($data['answer_id']);
                    $data['answer_correct'] = $question['correct'];
                    $q_result = $wpdb->insert(TABLE_QUIZ_QUESTION_RESULT, $data);
                    break;
                case 'checkbox':
                    foreach ($question['possibilities'] as $answer) 
                    {
                        $data = array(
                            'quiz_id' => $_POST['ID'],
                            'user_id' => $user_id,
                            'question_id' => $question['ID'],
                            'answer_id' => $answer['ID'],
                            'attempt_id' => $main_attempt_id
                        );
                        if ($answer['selected'] === true) {
                            $data['answer'] = $answer['selected'] === true ? 1 : 0;
                            $attempt_id = $eot_quiz->add_quiz_result($data);
                        }
                    }
                    $data = array(
                            'quiz_id' => $_POST['ID'],
                            'user_id' => $user_id,
                            'question_id' => $question['ID'],
                            'attempt_id' => $main_attempt_id,
                            'answer_correct' => $question['correct']
                    );
                    $q_result = $wpdb->insert(TABLE_QUIZ_QUESTION_RESULT, $data);
                    break;
                case 'text':
                    $data = array(
                        'quiz_id' => $_POST['ID'],
                        'user_id' => $user_id,
                        'question_id' => $question['ID'],
                        'answer' => $question['answer'],
                        'attempt_id' => $main_attempt_id
                    );
                    foreach ($question['possibilities'] as $answer) 
                    {
                        $data['answer_id'] = $answer['ID'];
                    }
                    $attempt_id = $eot_quiz->add_quiz_result($data);
                    unset($data['answer']);
                    unset($data['answer_id']);
                    $data['answer_correct'] = $question['correct'];
                    $q_result = $wpdb->insert(TABLE_QUIZ_QUESTION_RESULT, $data);
                    break;
            }
        }
        echo json_encode(array('message' => 'success', 'req' => $attempt_id));
        exit();
        break;
}
