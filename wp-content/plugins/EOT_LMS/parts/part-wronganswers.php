<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_MY_LIBRARY ?>    
    <?= CRUMB_SEPARATOR ?>
    <span class="current">Wrong Answers</span>     
</div>
<h1 class="article_page_title">Wrong Answers</h1>

<?php
    // searches an array for key value pair
    function search($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, search($subarray, $key, $value));
            }
        }

        return $results;
    }

    if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] != "")
    {
        if(current_user_can("is_student"))
        {
            global $current_user;
            $user_id = $current_user->ID;
            $quiz_id = filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT);
            $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
            $has_access = verify_student_access($course_id);
            if (!$has_access)
            {
                wp_die("You do not have access to this course");
            }
            $path = WP_PLUGIN_DIR . '/eot_quiz/';
            require $path . 'public/class-eot_quiz_data.php';
            $eot_quiz = new EotQuizData();
            $quiz = $eot_quiz->get_quiz_data($quiz_id, false);
            $quiz_questions = $quiz['questions'];
            $question_ids = array_column($quiz_questions, 'ID');
            $question_ids_string = implode(",", $question_ids);
            $quiz_attempts = getQuizAttempts($quiz_id, $user_id);
?>
            <h2 class="article_page_title">Quiz: <?=$quiz['quiz']['name']?></h2>
<?php
            $count = 1;
            foreach ($quiz_attempts as $attempt) 
            {
                echo "<h3>Attempt $count</h3>";
                if($attempt['passed'] == 1)
                {
?>
                    <div class="bs">
                        <div class="well well-lg">You have passed this quiz, congratulations!</div>
                    </div>
<?php
                }
                else
                {
                    $wrong_answers_in_attempt = array();
                    $results = getQuizResults($attempt['ID']);
                    //d($results);
                    foreach ($results as $result) 
                    {
                        if($result['answer_correct'] == 0)
                        {
                            array_push($wrong_answers_in_attempt, $result);
                        }
                    }

                    $wrong_answers_in_attempt = array_map("unserialize", array_unique(array_map("serialize", $wrong_answers_in_attempt)));
                    //d($wrong_answers_in_attempt);
                    foreach ($wrong_answers_in_attempt as $wa)
                    {
                        $question = search($quiz_questions, 'ID', $wa['question_id']);// search the quiz for the failed question
                        $question = $question[0];
                        $html = "<b>".$question['quiz_question']."</b><br>";
                        $html.="<ul>";
                        foreach ($question['possibilities'] as $answer)//display the failed question for that attempt 
                        {
                            $correct = '';
                            if ($answer['answer_correct'] == 1) 
                            {
                                $correct = ' <span class="fa fa-check"></span> '; // not displaying the correct answer any more.
                            }
                            $chosen_wrong = '';
                            if($answer['ID'] == $wa['answer_id'])
                            {
                               $chosen_wrong = ' <span class="fa fa-times fa-2x wrong"></span> '; 
                            }
                            $html .= "<li>" . $answer['answer_text'] . $chosen_wrong . "</li>";

                        }  
                        $html .= "</ul>";
                        echo $html;
                        //d($question);
                    }
                }
                $count++;

            }
    
        }
		else
		{
			wp_die('You do not have privilege to view this page.');
		}
	}
	else
	{
		wp_die('Invalid course. Please report this to the technical support.');
	}
?>