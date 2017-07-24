<?php

$path = WP_PLUGIN_DIR . '/eot_quiz/';
require $path . 'public/class-eot_quiz_data.php';
$eot_quiz = new EotQuizData();

$delete=$eot_quiz->deleteQuiz($_REQUEST['quiz_id']);
if($delete){
    wp_redirect(home_url('/dashboard?part=manage_quiz&subscription_id='.$_REQUEST['subscription_id']));
    exit();
}else{
    die();
}

