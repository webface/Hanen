<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.expertonlinetraining.com
 * @since      1.0.0
 *
 * @package    Eot_quiz
 * @subpackage Eot_quiz/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Eot_quiz
 * @subpackage Eot_quiz/public
 * @author     Tommy Adeniyi <tommy@targetdirectories.com>
 * ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
 */
class Eot_quiz_Public 
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) 
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() 
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Eot_quiz_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Eot_quiz_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        //wp_enqueue_style('slider', plugin_dir_url(__FILE__) . 'css/rangeslider.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/eot_quiz-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() 
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Eot_quiz_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Eot_quiz_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        //wp_enqueue_script('slider', plugin_dir_url(__FILE__) . 'js/rangeslider.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script('bootstrap_confirm_delete', plugin_dir_url(__FILE__) . 'js/bootstrap-confirm-delete.js', array('jquery'), $this->version, false);
        wp_enqueue_script('quiz_question_radio', plugin_dir_url(__FILE__) . 'js/quiz-question-radio.js', array('jquery'), $this->version, false);
        wp_enqueue_script('quiz_question_check', plugin_dir_url(__FILE__) . 'js/quiz-question-check.js', array('jquery'), $this->version, false);
        wp_enqueue_script('quiz_question_text', plugin_dir_url(__FILE__) . 'js/quiz-question-text.js', array('jquery'), $this->version, false);
        wp_enqueue_script('edit_quiz_question_text', plugin_dir_url(__FILE__) . 'js/edit-quiz-question-text.js', array('jquery'), $this->version, false);
        wp_enqueue_script('edit_quiz_question_radio', plugin_dir_url(__FILE__) . 'js/edit-quiz-question-radio.js', array('jquery'), $this->version, false);
        wp_enqueue_script('edit_quiz_question_checkbox', plugin_dir_url(__FILE__) . 'js/edit-quiz-question-checkbox.js', array('jquery'), $this->version, false);
        wp_localize_script('edit_quiz_question_checkbox', 'edit_quiz_question_checkbox', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_localize_script('edit_quiz_question_text', 'edit_quiz_question_text', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_localize_script('edit_quiz_question_radio', 'edit_quiz_question_radio', array('ajax_url' => admin_url('admin-ajax.php')));

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/eot_quiz-public.js', array('jquery'), $this->version, false);
        wp_enqueue_script('angular', plugin_dir_url(__FILE__) . 'js/angular.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('angular-route', plugin_dir_url(__FILE__) . 'js/angular-route.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('quizapp', plugin_dir_url(__FILE__) . 'quizapp/app.js', array('jquery'), $this->version, true);
        wp_enqueue_script('quizmanager', plugin_dir_url(__FILE__) . 'quizapp/factories/quizmanager.js', array('jquery'), $this->version, true);
        wp_enqueue_script('quiztimeformat', plugin_dir_url(__FILE__) . 'quizapp/filters/formattime.js', array('jquery'), $this->version, true);
        wp_enqueue_script('quizctrl', plugin_dir_url(__FILE__) . 'quizapp/controllers/quiz.js', array('jquery'), $this->version, true);
        wp_enqueue_script('quizresultsctrl', plugin_dir_url(__FILE__) . 'quizapp/controllers/results.js', array('jquery'), $this->version, true);
        wp_enqueue_script('quizwelcomectrl', plugin_dir_url(__FILE__) . 'quizapp/controllers/welcome.js', array('jquery'), $this->version, true);
        wp_localize_script('quizapp', 'quizapp', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'partials' => plugin_dir_url(__FILE__) . 'quizapp/templates/',
            'logo' => plugin_dir_url(__FILE__) . 'quizapp/eot_logo.png',
            'results_logo' => plugin_dir_url(__FILE__) . 'images/EOT_results_logo.jpg',
            'startBtn' => plugin_dir_url(__FILE__) . 'quizapp/eot_start_button.png',
            'preloader' => plugin_dir_url(__FILE__) . 'quizapp/preloader.gif'
                )
        );
    }

    public function register_shortcodes() 
    {
        add_shortcode('eot_quiz_admin', array($this, 'frontend_quiz_admin'));
        add_shortcode('eot_quiz_display', array($this, 'frontend_quiz'));
    }

    //Shortcode function to display front end quiz admin
    public function frontend_quiz_admin($atts) 
    {
        $a = shortcode_atts(array(
            'action' => 'manage_quiz',
            'attr_2' => 'attribute 2 default',
                // ...etc
                ), $atts);
        switch ($a['action']) 
        {
            case 'manage_quiz':
                require_once plugin_dir_path(__FILE__) . 'partials/eot_manage_quiz.php';
                break;
            case 'view_quiz':
                require_once plugin_dir_path(__FILE__) . 'partials/eot_view_quiz.php';
                break;
            case 'view_core_quiz':
                require_once plugin_dir_path(__FILE__) . 'partials/eot_view_core_quiz.php';
                break;
            case 'update_quiz':
                require_once plugin_dir_path(__FILE__) . 'partials/eot_update_quiz.php';
                break;
            case 'manage_quiz_questions':
                require_once plugin_dir_path(__FILE__) . 'partials/eot_manage_quiz_questions.php';
                break;
            case 'update_quiz_questions':
                require_once plugin_dir_path(__FILE__) . 'partials/eot_update_quiz_questions.php';
                break;
        }
    }

    //Shortcode function to display front end quiz
    public function frontend_quiz($atts) 
    {
        $a = shortcode_atts(array(
            'action' => 'view_quiz',
            'id' => 0,
                // ...etc
                ), $atts);
        require_once plugin_dir_path(__FILE__) . 'partials/eot_quiz.php';
    }

    //Ajax function to save question
    //ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
    public function update_question_callback() 
    {
        switch ($_POST['type']) {
            case 'radio':
                require_once plugin_dir_path(__FILE__) . 'partials/ajax_update_radio.php';
                break;
            case 'checkbox'://uses same ajax script for processing
                require_once plugin_dir_path(__FILE__) . 'partials/ajax_update_radio.php';
                break;
            case 'text':
                require_once plugin_dir_path(__FILE__) . 'partials/ajax_update_text.php';
                break;
        }
        exit();
    }

    //Ajax function to manage quiz
    //ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
    public function quiz_data_callback() 
    {

        require_once plugin_dir_path(__FILE__) . 'partials/ajax_quiz_manager.php';
    }

    //Ajax function to get quiz admin forms(facebox)
    //ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
    public function get_quiz_form_callback() 
    {
        require_once plugin_dir_path(__FILE__) . 'partials/ajax_get_quiz_form.php';
    }

    //Ajax function to delete quiz
    //ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
    public function delete_quiz_callback() 
    {
        $path = WP_PLUGIN_DIR . '/eot_quiz/';
        require $path . 'public/class-eot_quiz_data.php';
        $eot_quiz = new EotQuizData();
        $quiz_id = filter_var($_REQUEST['quiz_id'],FILTER_SANITIZE_NUMBER_INT);
        if ( ! wp_verify_nonce('delete-quiz_'.$quiz_id ) ) 
        {

            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Your nonce did not verify."; 

        }
        $delete = $eot_quiz->deleteQuiz($quiz_id);
        if ($delete) {
            $result['success'] = true;
        } else {
            $result['success'] = false;
        }
        echo json_encode($result);
        exit();
    }
    //Ajax function to delete question
    //ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
    public function delete_question_callback() 
    {
        $path = WP_PLUGIN_DIR . '/eot_quiz/';
        require $path . 'public/class-eot_quiz_data.php';
        $eot_quiz = new EotQuizData();
        $question_id = filter_var($_REQUEST['question_id'],FILTER_SANITIZE_NUMBER_INT);
        if ( ! wp_verify_nonce('delete-question_'.$question_id ) ) 
        {

            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Your nonce did not verify."; 

        }
        $delete = $eot_quiz->deleteQuestion($question_id);
        if ($delete) {
            $result['success'] = true;
        } else {
            $result['success'] = false;
        }
        echo json_encode($result);
        exit();
    }
    //Ajax function to add question title
    //ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
    public function add_title_callback() 
    {
        $title = preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']));
        $type = filter_var($_REQUEST['type'], FILTER_SANITIZE_STRING);
        if ( ! wp_verify_nonce('add-title_'.$type ) ) 
        {

            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Your nonce did not verify."; 

        }
        if ($title === "") {

            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Your question is invalid";
        } else {
            $result['success'] = true;
            $result['display_errors'] = false;
            $result['title'] = $title;
            $result['type'] = $type;
        }
        echo json_encode($result);
        exit();
    }
    //Ajax function to update title
    //ACTIONS ARE ADDED IN INCLUDES/CLASS.EOT_QUIZ.PHP
    public function update_title_callback() 
    {
        $title = preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']));
        if ( ! wp_verify_nonce('update-title' ) ) 
        {

            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Your nonce did not verify."; 

        }
        if ($title === "") {

            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Your question is invalid";
        } else {
            $result['success'] = true;
            $result['display_errors'] = false;
            $result['title'] = $title;
        }
        echo json_encode($result);
        exit();
    }

}
