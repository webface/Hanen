<?php

$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
$eot_quiz = new EotQuizData();

$questionData = array("quiz_question" => preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_POST['question'])));
$answerData = array("answer_text" => preg_replace("/[^a-zA-Z0-9'\"?_\. !&-]+/","",sanitize_text_field($_POST['answer'])));
$question_id = filter_var($_POST['question_id'], FILTER_SANITIZE_NUMBER_INT);
$answer_id = filter_var($_POST['answer_id'], FILTER_SANITIZE_NUMBER_INT);
$updq = $eot_quiz->updateQuestion($questionData, $question_id);
$upda = $eot_quiz->updateAnswer($answerData, $answer_id);

if ($updq && $upda) 
{
    $success = array('message' => 'success','updq'=>$updq,'upda'=>$upda);
    echo json_encode($success);
    exit();
} 
else 
{
    $fail = array('message' => 'fail','updq'=>$updq,'upda'=>$upda);
    echo json_encode($fail);
    exit();
}

