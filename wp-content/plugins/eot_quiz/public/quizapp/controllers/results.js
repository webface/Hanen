(function () {

    angular
            .module("quiz")
            .controller("resultsCtrl", ResultsController);

    ResultsController.$inject = ['quizmanager'];

    function ResultsController(quizmanager) 
    {
        var vm = this;

        vm.quizmanager = quizmanager; // Controllers reference to the quiz data from factory

    }


})();