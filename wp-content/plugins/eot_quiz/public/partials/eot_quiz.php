<div class="quiz-holder" ng-app="quiz" class="bs">

    <div class="slide-container welcome" ng-controller="welcomeCtrl as welcome" ng-hide="welcome.quizmanager.quizActive || welcome.quizmanager.resultsActive">
        <img class="logo" src="{{welcome.quizmanager.quiz_logo}}"/>
        <div class="preload" ng-show="welcome.quizmanager.quizIsLoading">
            <i class="fa fa-spinner fa-pulse fa-3x" aria-hidden="true"></i>
        </div>
        <h1 ng-cloak ng-hide="welcome.quizmanager.quizIsLoading">{{welcome.quizmanager.quiz.quiz.name}}</h1>
        <div class="bs row welcome-message" ng-hide="welcome.quizmanager.quizIsLoading" ng-cloak>

            <div class="bs col-xs-4"><strong>Questions: </strong>{{welcome.quizmanager.quiz.questions.length}}</div>
            <div class="bs col-xs-4"><strong>Time Limit: </strong>{{welcome.quizmanager.quiz.quiz.time_limit}} mins</div>
            <div class="bs col-xs-4"><strong>Passing Grade: </strong>{{welcome.quizmanager.quiz.quiz.passing_score}}/{{welcome.quizmanager.quiz.questions.length}}</div>

        </div>
        <img ng-cloak class="logo startBtn" src="{{welcome.quizmanager.quiz_start_button}}" ng-hide="welcome.quizmanager.quizIsLoading" ng-click="welcome.activateQuiz()"/>
    </div>
    <div class="slide-container quiz"  ng-controller="quizCtrl as quiz" ng-show="quiz.quizmanager.quizActive" ng-cloak>
        <p>{{quiz.quizmanager.counter| formatTimer}}</p>
        <h1>{{quiz.activeQuestion + 1 + ". " + quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question}}</h1>
        <div class="bs progress">
            <div class="bs progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{quiz.percent}}" aria-valuemin="0" aria-valuemax="100" style="width: {{quiz.percent}}%">
                <span class="bs sr-only">{{quiz.percent}}% Complete (success)</span>
            </div>

        </div>
        <div class="row" ng-if="quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type === 'checkbox'">
            <div class="col-xs-12">Check all that apply.</div>
        </div>
        <div class="bs row" ng-if="quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type === 'radio' || quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type === 'checkbox'">
            <!--            <label ng-if="quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type === 'checkbox'">Check all that apply</label><br>-->
            <ul class="answers">
                <li class="answer" ng-repeat="answer in quiz.quizmanager.quiz.questions[quiz.activeQuestion].possibilities"
                    ng-class="{'selected':quiz.quizmanager.quiz.questions[quiz.activeQuestion].possibilities[$index].selected}"
                    ng-click="quiz.selectAnswer($index, quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type)"
                    >{{answer.answer_text}}</li>
            </ul>

        </div>

        <div class="bs form-group" ng-if="quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type === 'text'">
            <input type="text" class="bs form-control" ng-model="quiz.quizmanager.quiz.questions[quiz.activeQuestion].answer"
                   ng-change="quiz.selectAnswer(0, quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type)"
                   />
        </div>
        <div class="bs row">
            <button class="bs btn btn-primary pull-left" ng-hide="quiz.activeQuestion < 1"
                    ng-click="quiz.previousQuestion()"
                    >Previous</button>&nbsp;
            <button class="bs btn btn-primary pull-right" 
                    ng-click="quiz.questionAnswered(quiz.activeQuestion, quiz.quizmanager.quiz.questions[quiz.activeQuestion].quiz_question_type)">
                Continue</button>
            <div class="bs clearfix"></div>
        </div>

    </div>

    <div class="slide-container welcome" ng-controller="resultsCtrl as results" ng-show="results.quizmanager.resultsActive">
        <img class="logo" src="{{results.quizmanager.quiz_logo}}"/>
        <h1 ng-cloak >{{results.quizmanager.quiz.quiz.name}}</h1>
        <div ng-cloak class="preload" ng-show="results.quizmanager.isSaving">
            <i class="fa fa-spinner fa-pulse fa-3x" aria-hidden="true"></i>...saving...
        </div>
        <div class="bs row welcome-message" ng-cloak>

            <div class="bs col-xs-4"><strong>Your Scores: </strong>{{results.quizmanager.score}}</div>
            <div class="bs col-xs-4" ng-cloak><strong>Passing Grade: </strong>{{results.quizmanager.quiz.quiz.passing_score}}/{{results.quizmanager.quiz.quiz.questions}}({{results.quizmanager.perc}}%)</div>
            <div class="bs col-xs-4"><strong>Time Spent: </strong>{{results.quizmanager.counter| formatTimer}}</div>

        </div>
        <h1 ng-cloak >{{results.quizmanager.quiz_message}}</h1>
        <a ng-cloak href="/dashboard" class="exit">Back to Dashboard</a>
<!--        <img ng-cloak class="logo startBtn" src="{{results.quizmanager.quiz_start_button}}" ng-hide="welcome.quizmanager.quizIsLoading" ng-click="welcome.activateQuiz()"/>-->
        <img class="logo" src="{{results.quizmanager.quiz_results_logo}}"/>
    </div>
</div>
