(function () {

    angular
            .module("quiz")
            .controller("quizCtrl", QuizController);


    QuizController.$inject = ['quizmanager'];

    function QuizController(quizmanager) 
    {

        var vm = this;

        vm.quizmanager = quizmanager; // Attaching the quizmanager object to the view model
        vm.questionAnswered = questionAnswered; // also a named function defined below
        vm.previousQuestion = previousQuestion;// also a named function defined below
        vm.selectAnswer = selectAnswer; // also a named function
        vm.finaliseAnswers = finaliseAnswers; //also a named function
        vm.activeQuestion = 0; // currently active question in the quiz
        vm.percent = 0;
        vm.calculatePercent = calculatePercent;// also a named function defined below
        vm.isPrevious = false;


        var numQuestionsAnswered = 0;

        function questionAnswered() 
        {
            var questionsLength = quizmanager.quiz.questions.length;

            if (vm.isPrevious) 
            {
                vm.activeQuestion++;
                if (numQuestionsAnswered == vm.activeQuestion) 
                {
                    vm.isPrevious = false;
                } 
                else 
                {
                    vm.isPrevious = true;
                }


            } 
            else 
            {

                if (quizmanager.quiz.questions[vm.activeQuestion].selected) 
                {
                    numQuestionsAnswered += 1;
                    //console.log('Question has been answered: ' + numQuestionsAnswered);
                    if (numQuestionsAnswered >= questionsLength) 
                    {
                        //console.log("Questions Length: " + questionsLength);
                        //console.log("Questions Answered: " + numQuestionsAnswered);
                        vm.finaliseAnswers();
                        quizmanager.stop();
                    }
                    vm.activeQuestion++;
                    vm.isPrevious = false;

                }
            }
            //console.log("Active Question: " + vm.activeQuestion);
            //console.log("Questions Answered: " + numQuestionsAnswered);
            vm.calculatePercent(vm.activeQuestion, questionsLength);
            quizmanager.numQuestionsAnswered = numQuestionsAnswered + 1;

        }
        function previousQuestion() 
        {
            var questionsLength = quizmanager.quiz.questions.length;
            if (vm.activeQuestion > 0) 
            {
                vm.activeQuestion--;
                vm.isPrevious = true;
                vm.calculatePercent(vm.activeQuestion, questionsLength);
            }
            //console.log("Active Question: " + vm.activeQuestion);
            //console.log("Questions Answered: " + numQuestionsAnswered);
        }
        function calculatePercent(x, y) 
        {
            vm.percent = Math.round((x / y) * 100);
        }

        function selectAnswer(index, type) 
        {
            quizmanager.selectAnswer(index, type, vm.activeQuestion)
        }
        function finaliseAnswers() 
        {
            //console.log('quiz is complete!: ' + quizmanager.numQuestionsAnswered);
            quizmanager.stop();
            quizmanager.markQuiz();
            quizmanager.changeState("quiz", false);
            quizmanager.changeState("results", true);
        }

    }


})();

