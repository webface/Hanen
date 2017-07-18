<?php

$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
$eot_quiz = new EotQuizData();

$delete=$eot_quiz->deleteQuestion($_REQUEST['question_id']);
if($delete){
    wp_redirect(home_url('/dashboard?part=manage_quiz_questions&quiz_id='.$_REQUEST['quiz_id'].'&subscription_id='.$_REQUEST['subscription_id']));
    exit();
}else{
    die();
}

