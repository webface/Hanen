(function () {

    angular
            .module("quiz")
            .controller("welcomeCtrl", WelcomeController);

    WelcomeController.$inject = ['quizmanager'];

    function WelcomeController(quizmanager) 
    {
        var vm = this;

        vm.quizmanager = quizmanager; // Controllers reference to the quiz data from factory

        vm.activateQuiz = activateQuiz; // reference to named function below
        /*
         * STARTING POINT OF APPLICATION. All the other views are hidden
         */
        quizmanager.loadQuiz();

        function activateQuiz() 
        {

            quizmanager.changeState("quiz", true);
            quizmanager.countdown();
        }
    }


})();


