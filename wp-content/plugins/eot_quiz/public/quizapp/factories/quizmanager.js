/*
 * IIFE to avoid global namespace pollution and keep code safe.
 */
(function () {

    /*
     * creating a factory called quizMetrics and attaching that to the 
     * turtleFacts module. 
     *
     * This factories job is to hold all the data the pertains to the quiz. 
     * This could be:
     *          -the questions themselves. What kind of question it is(text or 
     *              image)
     *          -Whether the current question has been answered or is still 
     *              blank. 
     *          -Hold data to show if quiz is active, results are active or 
     *              neither
     *          -Method to change the state of the quiz and results (active or
     *              inactive)
     *          -Hold what the actual correct answers are
     *          -Method to mark the answers
     *          -hold how many correct answers the user gave
     *          
     */
    angular
            .module("quiz")
            .factory("quizmanager", QuizManager)
            .filter('formatTimer', function () {
                return function (input)
                {
                    function z(n) {
                        return (n < 10 ? '0' : '') + n;
                    }
                    var seconds = input % 60;
                    var minutes = Math.floor(input / 60);
                    var hours = Math.floor(minutes / 60);
                    return (z(hours) + ':' + z(minutes) + ':' + z(seconds));
                };
            });

    /*
     * dependency injection as seen in all the controllers. See comments 
     * there for a deeper explaination of dependency injection
     */
    QuizManager.$inject = ['$http', '$timeout', '$httpParamSerializer', '$filter'];

    /*
     * function definition for the factory
     */
    function QuizManager($http, $timeout, $httpParamSerializer, $filter)
    {

        /*
         * quizObj is an object that will hold all of the above mentioned 
         * properties and methods and will be the return value of the 
         * factory
         *
         * As per pattern used in the controllers, the methods will 
         * reference named functions that are at the bottom of this function
         */
        var quizObj = {
            quiz: [],
            quiz_logo: quizapp.logo,
            quiz_results_logo: quizapp.results_logo,
            quiz_start_button: quizapp.startBtn,
            quiz_preloader: quizapp.preloader,
            quizIsLoading: true,
            quizActive: false,
            course_id:course_id,
            enrollment_id:enrollment_id,
            resultsActive: false,
            passed: false,
            numQuestionsAnswered: 0,
            numQuestions:0,
            completed: false,
            changeState: changeState, // changeState is a named function below
            calculatePerc: calculatePerc,
            score: 0,
            markQuiz: markQuiz, // markQuiz is a named function below
            perc: 0,
            quiz_message: '',
            loadQuiz: loadQuiz, //loadQuiz is a named function below
            selectAnswer: selectAnswer, //selectAnswer is a named function below
            saveQuiz: saveQuiz,
            counter: 0,
            timerRunning: false,
            isSaving: false,
            countdown: countdown,
            stop: stop,
            teste: teste
                    // all questions answered

        };
        return quizObj;
        var stopped;
        /*
         * 
         * Get The Quiz
         *
         * 
         */
        function loadQuiz()
        {
            var data = {
                ID: quiz_id,
                action: 'quiz_data',
                part: 'get_quiz'
            };
            //$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
            $http({
                url: quizapp.ajax_url,
                method: "POST",
                params: data
            })
                    .then(function (response) {
//console.log('success');
//console.log(response.data);
                        quizObj.quiz = response.data;
                        quizObj.quizIsLoading = false;
                        quizObj.numQuestions = quizObj.quiz.questions.length;
//console.log('success:'+quizObj.numQuestions);
                    },
                            function (response)
                            { // optional
                                // failed
                                //console.log(response);
                            });
        }

        function selectAnswer(index, type, activeQuestion)
        {
            switch (type)
            {
                case 'radio':

                    for (var i = 0; i < quizObj.quiz.questions[activeQuestion].possibilities.length; i++) {
                        quizObj.quiz.questions[activeQuestion].possibilities[i].selected = false;
                    }
                    quizObj.quiz.questions[activeQuestion].possibilities[index].selected = true;
                    quizObj.quiz.questions[activeQuestion].selected = true;
                    //console.log(quizObj.quiz.questions[activeQuestion].possibilities[index]);
                    break;
                case 'checkbox':
                    //console.log(quizObj.quiz.questions[activeQuestion].possibilities[index].selected);
                    if (quizObj.quiz.questions[activeQuestion].possibilities[index].selected === true) {
                        quizObj.quiz.questions[activeQuestion].possibilities[index].selected = false;
                    }
                    else
                    {
                        quizObj.quiz.questions[activeQuestion].possibilities[index].selected = true

                    }
                    quizObj.quiz.questions[activeQuestion].selected = false;
                    for (var i = 0; i < quizObj.quiz.questions[activeQuestion].possibilities.length; i++)
                    {
                        if (quizObj.quiz.questions[activeQuestion].possibilities[i].selected === true)
                        {
                            quizObj.quiz.questions[activeQuestion].selected = true;
                        }
                        ;
                    }
                    // console.log(quizObj.quiz.questions[activeQuestion].possibilities[index].selected)
                    break;
                case 'text':
                    quizObj.quiz.questions[activeQuestion].possibilities[index].selected = true;
                    quizObj.quiz.questions[activeQuestion].selected = true;
                    //console.log(quizObj.quiz.questions[activeQuestion].possibilities[index].answer);
                    break;
            }

        }


        /*
         * Function to change the state of either the quiz or the results.
         *
         * It accepts two arguments, one is which metric to change (quiz or
         * results) and the other is what to change the state too.
         */
        function changeState(metric, state)
        {
            if (metric === "quiz")
            {
                quizObj.quizActive = state;
            }
            else if (metric === "results")
            {
                quizObj.resultsActive = state;
            }
            else
            {
                return false;
            }
        }

        /*
         * When called, the markQuiz method will loop through all the users
         * answers and compare them to the know correct answers to each
         * question. The total number of correct answers by the user is 
         * calculated and saved in the numCorrect property of the quizObj 
         * object
         */
        function markQuiz()
        {
            //console.log(quizObj.quiz);
            quizObj.stop();
            for (var i = 0; i < quizObj.quiz.questions.length; i++)
            {
                if (quizObj.quiz.questions[i].quiz_question_type == 'radio')
                {
                    var correct = false
                    for (var j = 0; j < quizObj.quiz.questions[i].possibilities.length; j++)
                    {
                        if ((quizObj.quiz.questions[i].possibilities[j].selected === true) && (quizObj.quiz.questions[i].possibilities[j].answer_correct === "1"))
                        {
                            quizObj.score += 1;
                            correct = true
                            //console.log('answer_correct_radio');
                        }

                    }
                    if (correct)
                    {
                        quizObj.quiz.questions[i].correct = 1;
                        //console.log('answer_correct_checkbox');
                    }
                    else
                    {
                        quizObj.quiz.questions[i].correct = 0;
                    }
                }
                else if (quizObj.quiz.questions[i].quiz_question_type == 'checkbox')
                {
                    var correct = 0;
                    for (var j = 0; j < quizObj.quiz.questions[i].possibilities.length; j++)
                    {
                        if ((quizObj.quiz.questions[i].possibilities[j].selected === true) && (quizObj.quiz.questions[i].possibilities[j].answer_correct === "1"))
                        {
                            correct += 1;
                            //console.log('correct check');
                        }
                        if (((quizObj.quiz.questions[i].possibilities[j].selected === null) || (quizObj.quiz.questions[i].possibilities[j].selected === undefined) || (quizObj.quiz.questions[i].possibilities[j].selected === false)) && (quizObj.quiz.questions[i].possibilities[j].answer_correct === "0"))
                        {
                            correct += 1;
                            //console.log('correct check not selected');
                        }
                        if (correct >= quizObj.quiz.questions[i].answers)
                        {
                            quizObj.score += 1;
                            quizObj.quiz.questions[i].correct = 1;
                            //console.log('answer_correct_checkbox');
                        }
                        else
                        {
                            quizObj.quiz.questions[i].correct = 0;
                        }
                    }

                }
                else
                {
                    if (quizObj.quiz.questions[i].answer !== undefined)
                    {
                        if (quizObj.quiz.questions[i].answer.toLowerCase() === quizObj.quiz.questions[i].possibilities[0].answer_text.toLowerCase())
                        {
                            quizObj.score += 1;
                            quizObj.quiz.questions[i].correct = 1;
                            //console.log('answer_correct_text');
                        }
                        else
                        {
                            quizObj.quiz.questions[i].correct = 0;
                        }
                    }
                }
            }
            quizObj.calculatePerc();

        }
        function saveQuiz()
        {
            quizObj.isSaving = true;
            var data = {
                ID: quiz_id,
                course_id:course_id,
                action: 'quiz_data',
                part: 'save_quiz',
                score: quizObj.score,
                passed: quizObj.passed,
                completed: quizObj.completed,
                percentage: quizObj.perc,
                time_spent: $filter('formatTimer')(quizObj.counter),
                questions: quizObj.quiz.questions
            };
            //console.log(data);
            $http({
                url: quizapp.ajax_url + '?action=quiz_data&part=save_quiz',
                method: "POST",
                data: data
            })
                    .then(function (response) {
                        //console.log(response.data);

                        quizObj.isSaving = false;

                    },
                            function (response)
                            { // optional
                                // failed
                                //console.log(response);
                            });
        }
        function countdown()
        {
            if (quizObj.counter >= (quizObj.quiz.quiz.time_limit * 60))
            {
                quizObj.quizActive = false;
                quizObj.resultsActive = true;
                quizObj.stop();
                quizObj.markQuiz();
                return;
            }
            quizObj.timerRunning = true;
            stopped = $timeout(function () {
                //console.log($scope.counter);
                quizObj.counter++;
                quizObj.countdown();
            }, 1000);
        }


        function stop()
        {
            quizObj.timerRunning = false;
            $timeout.cancel(stopped);
        }

        function calculatePerc()
        {
            quizObj.perc = Math.round(quizObj.score / quizObj.quiz.questions.length * 100);
            if (quizObj.score >= quizObj.quiz.quiz.passing_score)
            {
                quizObj.quiz_message = "You have passed the Quiz!"
                quizObj.passed = true;
            }
            else
            {
                quizObj.quiz_message = "You have failed the Quiz!"
                quizObj.passed = false;
            }
            if (quizObj.numQuestionsAnswered >= quizObj.quiz.questions.length)
            {
                quizObj.completed = true;
            }
            quizObj.saveQuiz();
        }

        function teste()
        {
            //console.log(quizObj.counter);
        }

    }

})();
