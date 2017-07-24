<?php

$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
$eot_quiz = new EotQuizData();

switch ($_POST['part']) 
{
    case 'delete_answer':
        $answer_id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
        $upda = $eot_quiz->deleteAnswer($answer_id);
        if ($upda) 
        {
            $success = array('message' => 'success', 'upda' => $upda);
            echo json_encode($success);
            exit();
        } 
        else 
        {
            $fail = array('message' => 'fail', 'upda' => $upda);
            echo json_encode($fail);
            exit();
        }
        exit();
        break;
    case 'add_answer':
        $data = array(
            'question_id' => filter_var($_POST['question_id'],FILTER_SANITIZE_NUMBER_INT),
            'answer_text' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_POST['title']))
        );
        $adda = $eot_quiz->addAnswer($data);
        if ($adda > 0) 
        {
            $success = array('message' => 'success', 'ID' => $adda);
            echo json_encode($success);
            exit();
        } 
        else 
        {
            $fail = array('message' => 'fail', 'upda' => $upda);
            echo json_encode($fail);
            exit();
        }
        exit();
        break;
    case 'save_question_answers':
        $updq = $eot_quiz->updateQuestion(
                array(
                    'quiz_question' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_POST['question_text']))
                    ), 
                filter_var($_POST['question_id'],FILTER_SANITIZE_NUMBER_INT));
        $answers = $_POST['answers'];
        $toupdate = count($answers);

        foreach ($answers as $answer) 
        {
               //var_dump($answer['answer_text']); 
               //exit();
            $data = array(
                'answer_text' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($answer['answer_text'])),
                'answer_correct' => $answer['answer_correct']
            );
         
            $updd = $eot_quiz->updateAnswer($data, $answer['answer_id']);
            if ($updd) {
                $toupdate=$toupdate-1;
            }
        }
        if ($updq && ($toupdate == 0)) 
        {
            $success = array('message' => 'success', 'ref' => count($answers),'toupdate'=>$toupdate);
            echo json_encode($success);
            exit();
        } 
        else 
        {
            $fail = array('message' => 'fail','toupdate'=>$toupdate);
            echo json_encode($fail);
            exit();
        }
        exit();
        break;
}



