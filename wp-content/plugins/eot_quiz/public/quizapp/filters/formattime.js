/*
 * IIFE to avoid global namespace pollution and keep code safe.
 */
(function () {


    angular
            .module("quiz")
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



})();
