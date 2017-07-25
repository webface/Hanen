<?php

/**
 * Description of class-eot_quiz
 *
 * @author tommy
 */
class EotQuizData 
{

    function __construct() 
    {
        
    }

    public function addQuiz($data = array()) 
    {
        global $wpdb;
        global $current_user;
        $user_id = $current_user->ID; // Wordpress user ID
        $result = $wpdb->insert(TABLE_QUIZ, $data);
        $lastid = $wpdb->insert_id;
        return $result;
    }

    public function updateQuiz($data = array(), $id = 0) 
    {
        global $wpdb;
        $result = $wpdb->update(TABLE_QUIZ, $data, array('ID' => $id));
        if ($result === false) 
        {
            return false;
        } 
        else
        {
            return true;
        }
    }

    public function getQuizzes($org_id = 0, $user_id = 0) 
    {
        global $wpdb;
        $quizzes = $wpdb->get_results(""
                . "SELECT q.ID, q.name,q.time_limit,q.passing_score,COUNT(qa.quiz_question) AS questions "
                . "FROM " . TABLE_QUIZ . " as q "
                . "LEFT JOIN " . TABLE_QUIZ_QUESTION . " as qa ON q.ID=qa.quiz_id "
                . "WHERE q.org_id = $org_id and q.user_id= $user_id "
                . "GROUP BY q.ID "
                . "ORDER BY q.ID ASC", ARRAY_A);
        return $quizzes;
    }

    public function get_quiz_by_id($id = 0) 
    {
        global $wpdb;
        $quiz = $wpdb->get_row("SELECT q.*, "
                . "COUNT(qa.quiz_question) AS questions "
                . "FROM " . TABLE_QUIZ . " AS q "
                . "JOIN " . TABLE_QUIZ_QUESTION . " as qa ON q.ID = qa.quiz_id "
                . "WHERE q.ID = $id", ARRAY_A);
        return $quiz;
    }

    public function get_quiz_data($id = 0) 
    {
        $quiz_data = array();
        $quiz = $this->get_quiz_by_id($id);
        $quiz['time_limit'] = date('i', strtotime($quiz['time_limit']));
        $quiz_data['quiz'] = $quiz;
        $questions = $this->get_quiz_questions($id);
        $myquestions = array();
        foreach ($questions as $question) 
        {
            $question['possibilities'] = $this->get_question_answers($question['ID']);
            $myquestions[] = $question;
        }
        shuffle($myquestions);
        $quiz_data['questions'] = $myquestions;
        return $quiz_data;
    }

    public function deleteQuiz($id = 0) 
    {
        global $wpdb;
        $del = $wpdb->delete(TABLE_QUIZ_QUESTION, array('quiz_id' => $id));
        $del = $wpdb->delete(TABLE_QUIZ, array('ID' => $id));

        return $del;
    }

    public function addQuestion($data = array()) 
    {
        global $wpdb;
        $result = $wpdb->insert(TABLE_QUIZ_QUESTION, $data);
        $lastid = $wpdb->insert_id;
        return $lastid;
    }

    public function updateQuestion($data = array(), $id = 0)
    {
        global $wpdb;
        $result = $wpdb->update(TABLE_QUIZ_QUESTION, $data, array('ID' => $id));
        if ($result === false) 
        {
            return false;
        } 
        else 
        {
            return true;
        }
    }

    public function get_question_by_id($id = 0)
    {
        global $wpdb;
        $question = $wpdb->get_row("SELECT * FROM " . TABLE_QUIZ_QUESTION . " WHERE ID = $id", ARRAY_A);
        return $question;
    }

    public function get_quiz_questions($quiz_id = 0)
    {
        global $wpdb;
        $questions = $wpdb->get_results("SELECT q.ID,q.quiz_question,q.quiz_question_type, "
                . "COUNT(qa.answer_text) AS answers "
                . "FROM " . TABLE_QUIZ_QUESTION . " AS q "
                . "LEFT JOIN " . TABLE_QUIZ_ANSWER . " AS qa ON q.ID=qa.question_id "
                . "WHERE q.quiz_id = $quiz_id "
                . "GROUP BY q.ID "
                . "ORDER BY q.ID ASC", ARRAY_A);
        return $questions;
    }

    public function deleteQuestion($id = 0) 
    {
        global $wpdb;
        $del = $wpdb->delete(TABLE_QUIZ_ANSWER, array('question_id' => $id));
        $del = $wpdb->delete(TABLE_QUIZ_QUESTION, array('ID' => $id));

        return $del;
    }

    public function addAnswer($data = array()) 
    {
        global $wpdb;
        $result = $wpdb->insert(TABLE_QUIZ_ANSWER, $data);
        $lastid = $wpdb->insert_id;
        return $lastid;
    }

    public function updateAnswer($data = array(), $id = 0) 
    {
        global $wpdb;
        $result = $wpdb->update(TABLE_QUIZ_ANSWER, $data, array('ID' => $id));
        if ($result === false) 
        {
            return false;
        } 
        else 
        {
            return true;
        }
    }

    public function deleteAnswer($id = 0) 
    {
        global $wpdb;
        $result = $wpdb->delete(TABLE_QUIZ_ANSWER, array('ID' => $id));

        if ($result === false) 
        {
            return false;
        } 
        else 
        {
            return true;
        }
    }

    public function get_question_answers($question_id = 0) 
    {
        global $wpdb;
        $answers = $wpdb->get_results("SELECT * FROM " . TABLE_QUIZ_ANSWER . " WHERE question_id = $question_id", ARRAY_A);
        return $answers;
    }
    public function add_quiz_attempt($data = array()) 
    {
        global $wpdb;
        $result = $wpdb->insert(TABLE_QUIZ_ATTEMPT, $data);
        $lastid = $wpdb->insert_id;
        return $lastid;
    }
    public function add_quiz_result($data = array()) 
    {
        global $wpdb;
        $result = $wpdb->insert(TABLE_QUIZ_RESULT, $data);
        $lastid = $wpdb->insert_id;
        return $lastid;
    }

}
