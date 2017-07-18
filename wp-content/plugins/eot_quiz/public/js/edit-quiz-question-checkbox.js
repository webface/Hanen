(function ($) {

    // here we go!
    $.editQuizQuestionCheckbox = function (element, options) {

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
        var ajax_url = edit_quiz_question_checkbox.ajax_url;
        // to avoid confusions, use "plugin" to reference the 
        // current instance of the object
        var plugin = this;
        var count = 1;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('editQuizQuestionCheckbox').settings.propertyName from outside the plugin, 
        // where "element" is the element the plugin is attached to;
        plugin.settings = {}

        plugin.answer = function (obj) {
            var checked = (obj.answer_correct == 1) ? 'checked' : '';
            return '<div class="bs row answer" style="clear:both" id="answer_' + obj.ID + '">' +
                    '<div class="bs col-xs-2"><label class="bs radio-inline control-label"><input type="checkbox" name="correct_answer" value="' + obj.ID + '" ' + checked + '/>Correct?</label></div>' +
                    '<div class="bs col-xs-7"><input class="bs form-control" type="text" name=""  value="' + obj.answer_text + '"></div>' +
                    '<div class="bs col-xs-3"><a id="" class="bs btn btn-danger deleteBtn" data-id="' + obj.ID + '">Delete</a></div>' +
                    '</div>';
        };

        var $element = $(element), // reference to the jQuery version of DOM element
                element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.init = function () {
            //console.log('plugin init called');
            // the plugin's final properties are the merged default and 
            // user-provided options (if any)
            plugin.settings = $.extend({}, defaults, options);
            $("#editDiv").append(element);

            $('input[name="question"]').val(plugin.settings.question.quiz_question);
            $('input[name="question_id"]').val(plugin.settings.question.ID);
            // code goes here
            //console.log(plugin.settings.answers);
            for (i = 0; i < plugin.settings.answers.length; i++) {
                var entry = plugin.answer(plugin.settings.answers[i]);
                $(element).find(".panel-body").append(entry);
            }
            $(element).find(".deleteBtn").click(function (e) {
                e.preventDefault();
                plugin.delete_answer($(this));
            });
            $(element).find(".cancelBtn").click(function (e) {
                e.preventDefault();
                location.reload();
            });
            $(element).find(".updateBtn").click(function (e) {
                e.preventDefault();
                plugin.validate();
            });
            $(element).find(".addAnswerBtn").click(function () {
                plugin.add_answer();
            });


        }

        // public methods
        // these methods can be called like:
        // plugin.methodName(arg1, arg2, ... argn) from inside the plugin or
        // element.data('editQuizQuestionCheckbox').publicMethod(arg1, arg2, ... argn) from outside 
        // the plugin, where "element" is the element the plugin is attached to;

        // a public method. for demonstration purposes only - remove it!
        plugin.delete_answer = function (elem) {
            //console.log('deleting answer');
            var data = {
                'action': 'update_question',
                'type': 'checkbox',
                'part': 'delete_answer',
                'id': elem.attr('data-id')
            };
            //console.log(data);
            $.ajax({
                url: ajax_url,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (data) {
                    //console.log(data);
                    if (data.message == "success") {
                        $("#answer_" + elem.attr('data-id')).remove();
                    } else {
                        alert("There was an error deleting your data");
                    }
                },
                error: function (errorThrown) {
                    //console.log(errorThrown);
                    alert("There was an error deleting your data");
                }
            });



        }
        plugin.add_answer = function () {
            //console.log('adding answer');
            var label = prompt("Please enter your answer");

            if (label === null || label === false || label === '') { // Canceled
                //break; // Leave this out, if the name is mandatory
            } else {
                var data = {
                    'action': 'update_question',
                    'type': 'checkbox',
                    'question_id': plugin.settings.question.ID,
                    'part': 'add_answer',
                    'title': label
                };
                //console.log(data);
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        //console.log(data);
                        if (data.message == "success") {
                            //console.log('answer added');
                            var obj = {
                                'id': data.ID,
                                'answer_text': label,
                                'answer_correct': 0
                            }
                            var entry = plugin.answer(obj);
                            $(element).find(".panel-body").append(entry);
                            //console.log($(" #answer_" + data.ID + ".deleteBtn"));
                            $("#answer_" + data.ID + " .deleteBtn").click(function (e) {
                                e.preventDefault();
                                //console.log('this should delete');
                                plugin.delete_answer($(this));
                            });
                        } else {
                            alert("There was an error adding your answer");
                        }
                    },
                    error: function (errorThrown) {
                        //console.log(errorThrown);
                        alert("There was an error adding your answer");
                    }
                });
            }

        }
        plugin.save_question = function () {

        }

        plugin.validate = function () {
            //console.log('validate called');
            var valid = true;
            $(element).find("input[type='text']").each(function () {
                //console.log($(this).val());
                if ($.trim($(this).val()) == "") {
                    alert("Fields Cannot be empty");
                    valid = false;
                }
            });
            if (valid) {
                var data = {
                    'action': 'update_question',
                    'type': 'checkbox',
                    'part': 'save_question_answers',
                    'question_id': plugin.settings.question.ID,
                    'question_text': $("input[name='question']").val(),
                    'answers': []
                };
                $(element).find(".answer").each(function () {
                    var obj = {
                        "answer_id": $(this).find('.deleteBtn').attr('data-id'),
                        "answer_text": $(this).find('input[type="text"]').val(),
                        "answer_correct":($(this).find('input[type="checkbox"]').is(':checked'))?1:0
                    }
                    data.answers.push(obj);
                });
                //console.log(data);
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        //console.log(data);
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
    $.fn.editQuizQuestionCheckbox = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('editQuizQuestionCheckbox')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.editQuizQuestionCheckbox(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('editQuizQuestionCheckbox').publicMethod(arg1, arg2, ... argn) or
                // element.data('editQuizQuestionCheckbox').settings.propertyName
                $(this).data('editQuizQuestionCheckbox', plugin);

            }

        });

    }

})(jQuery);