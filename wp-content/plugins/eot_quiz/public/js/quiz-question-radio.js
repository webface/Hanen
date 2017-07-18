// Quiz Question
// An eot_quiz plugin script
// version 1.0, March 31,2017
// by Tommy Adeniyi
(function ($) {

    $.quizQuestionRadio = function (element, options) {

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

        };

        // to avoid confusions, use "plugin" to reference the 
        // current instance of the object
        var plugin = this;
        var count = 0;
        var panelContent;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('quizQuestionRadio').settings.propertyName from outside the plugin, 
        // where "element" is the element the plugin is attached to;
        plugin.settings = {};

        var $element = $(element), // reference to the jQuery version of DOM element
                element = element;
        // reference to the actual DOM element
        plugin.answer = function (id, text,num) {
            return '<div class="bs row" style="clear:both" id="' + id + '">' +
                    '<div class="bs col-xs-2"><label class="bs radio-inline control-label"><input type="radio" name="correctquestion_' + num + '" value="'+id+'"/>Correct?</label></div>' +
                    '<div class="bs col-xs-7"><input class="bs form-control" type="text" name="' + id + '"  value="' + text + '"></div>' +
                    '<div class="bs col-xs-3"><button id="delete' + id + '" class="bs btn btn-danger deleteBtn" data-id="' + id + '">Delete</button></div>' +
                    '</div>';
        };

        // the "constructor" method that gets called when the object is created
        plugin.init = function () {

            // the plugin's final properties are the merged default and 
            // user-provided options (if any)
            plugin.settings = $.extend({}, defaults, options);
            //console.log("Plugin init called");
            $element.find(".panel-title a").html('');
            $(element).find(".panel-title a").html(options.settings.label);
            $(element).find(".panel-heading #question_"+options.id).val(options.settings.label);
            //$(element).find(".panel-body").html('Answers');
            //console.log(options.settings);
            $("#accordion").append(element);
            for (var t = count; t < options.settings.choices.length; t++) {
                var entry = plugin.answer('question_' + options.id + '_answer_' + t, options.settings.choices[t].text,options.id);
                $(element).find(".panel-content").append(entry);
                count++;
                //console.log(count);
            }
            $('.deleteBtn').click(function(e){
               e.preventDefault();
               removeAnswer($(this),plugin);
               return false;
            });
            $(element).find('.addBtn').click(function(e){
               e.preventDefault();
               addAnswer($(this),plugin,element,options,count);
               count++;
               return false;
            });
            // code goes here

        }

        // public methods
        // these methods can be called like:
        // plugin.methodName(arg1, arg2, ... argn) from inside the plugin or
        // element.data('quizQuestionRadio').publicMethod(arg1, arg2, ... argn) from outside 
        // the plugin, where "element" is the element the plugin is attached to;

        // a public method. for demonstration purposes only - remove it!
        plugin.addAnswer = function () {

            // code goes here

        }
        plugin.removeAnswer = function (id) {
            //console.log('removing...'+id);
            $('#'+id).remove();

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
    $.fn.quizQuestionRadio = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('quizQuestionRadio')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.quizQuestionRadio(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('quizQuestionRadio').publicMethod(arg1, arg2, ... argn) or
                // element.data('quizQuestionRadio').settings.propertyName
                $(this).data('quizQuestionRadio', plugin);

            }

        });

    }

})(jQuery);