(function(){

    /*
     * Declaration of main angular module for this appllication.
     *
     * It is named quiz and has no dependencies (hence the 
     * empty array as the second argument)
     */
    angular
        .module("quiz", []);

})();


////var quiz = angular.module('quiz', ['ngRoute']);
//quiz.config(function ($routeProvider, $locationProvider) {
//    $routeProvider
//            .when('/', {
//                templateUrl: quizapp.partials + 'main.html',
//                controller: 'Main'
//            })
//});
//quiz.controller('Main', function ($scope, $http, $routeParams) {
//    $scope.logo = quizapp.logo;
//    var data = {
//        id: quiz_id,
//        action: 'quiz_data',
//        part: 'get_quiz'
//    };
//    //$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
//    $http({
//        url: quizapp.ajax_url,
//        method: "POST",
//        params: data
//    })
//            .then(function (response) {
//                console.log('success');
//                console.log(response.data);
//            },
//                    function (response) { // optional
//                        // failed
//                        console.log(response);
//                    });
//    console.log($scope.logo);
//});


