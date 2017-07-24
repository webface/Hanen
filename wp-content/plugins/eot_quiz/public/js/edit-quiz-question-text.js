// Ajax Edit Quiz Question
// An eot_quiz plugin script
// version 1.0, March 31,2017
// by Tommy Adeniyi

(function ($) {

    // here we go!
    $.editQuizQuestionText = function (element, options) {

        // plugin's default options
        // this is private property and is  accessible only from inside the plugin
        var defaults = {
            foo: 'bar',
            // if your plugin is event-driven, you may provide callback capabilities
            // for its events. execute these functions before or after events of your 
            // plugin, so that users may customize those particular events without 
            // changing the plugin's code
            onFoo: function () {
            }

        }
        var ajax_url = edit_quiz_question_text.ajax_url;

        // to avoid confusions, use "plugin" to reference the 
        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('editQuizQuestionText').settings.propertyName from outside the plugin, 
        // where "element" is the element the plugin is attached to;
        plugin.settings = {}

        plugin.answer = function () {
            return '<div class="bs row" style="clear:both" id="">' +
                    '<div class="bs col-xs-12">' +
                    '<label for="answer">Answer</label>' +
                    '<input type="hidden" class="bs form-control" name="answer_id" />' +
                    '<input class="bs form-control" type="text" name="answer"  value=""></div><br>' +
                    '</div>';
        };
        plugin.saveBtn = function () {
            return '<button class="bs btn btn-primary saveBtn">Save</button>&nbsp;<button class="bs btn btn-primary cancelBtn">Cancel</button>';
        }

        var $element = $(element), // reference to the jQuery version of DOM element
                element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.init = function () {

            // the plugin's final properties are the merged default and 
            // user-provided options (if any)
            plugin.settings = $.extend({}, defaults, options);
            $("#editDiv").append(element);
            var entry = plugin.answer();
            $(element).find(".panel-body").append(entry);
            var saveBtn = plugin.saveBtn();
            $(element).find(".panel-footer").append(saveBtn);
            $('input[name="question"]').val(plugin.settings.question.quiz_question);
            $('input[name="question_id"]').val(plugin.settings.question.ID);
            $('input[name="answer"]').val(plugin.settings.answers[0].answer_text);
            $('input[name="answer_id"]').val(plugin.settings.answers[0].ID);
            //console.log(plugin.settings.answers);
            $(element).find(".saveBtn").click(function () {
                plugin.validate_inputs();
            });
            $(element).find(".cancelBtn").click(function () {
                location.reload();
            });

        }

        // public methods
        // these methods can be called like:
        // plugin.methodName(arg1, arg2, ... argn) from inside the plugin or
        // element.data('editQuizQuestionText').publicMethod(arg1, arg2, ... argn) from outside 
        // the plugin, where "element" is the element the plugin is attached to;

        // a public method. for demonstration purposes only - remove it!
        plugin.validate_inputs = function () {
            //console.log('validating text');
            if (($.trim($('input[name="answer"]').val()) == '') || ($.trim($('input[name="question"]').val()) == '')) {
                alert('Fields cannot be empty');
            } else {
                plugin.submitFields();
            }

        }

        plugin.submitFields = function () {
           var data={
                    'action': 'update_question',
                    'type': 'text',
                    'question_id': $('input[name="question_id"]').val(),
                    'answer_id': $('input[name="answer_id"]').val(),
                    'question': $('input[name="question"]').val(),
                    'answer': $('input[name="answer"]').val()
                };
                //console.log(data);
            $.ajax({
                url: ajax_url,
                type: 'POST',
                dataType:'json',
                data: data,
                success: function (data) {
                    //console.log(data.message);
                    if (data.message == "success") {
                        location.reload();
                    } else {
                        alert("There was an error saving your data");
                    }
                },
                error: function (errorThrown) {
                    //console.log(errorThrown);
                    alert("There was an error saving your data");
                }
            });
        }

        // private methods
        // these methods can be called only from inside the plugin like:
        // methodName(arg1, arg2, ... argn)

        // a private method. for demonstration purposes only - remove it!
        var foo_private_method = function () {

            // code goes here

        }

        // fire up the plugin!
        // call the "constructor" method
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.editQuizQuestionText = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('editQuizQuestionText')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.editQuizQuestionText(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('editQuizQuestionText').publicMethod(arg1, arg2, ... argn) or
                // element.data('editQuizQuestionText').settings.propertyName
                $(this).data('editQuizQuestionText', plugin);

            }

        });

    }

})(jQuery);