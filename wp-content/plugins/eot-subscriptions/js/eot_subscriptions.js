var my_response = "";
var eot_status = 1;

jQuery(document).on('change', '.cc_cards_list input[type="radio"]', function () {
    //alert('changed');
    if (jQuery(this).attr('checked') == "checked") { //reset credit card form when a previous credit card is chosen
        jQuery('#new_cc_form input').val ('');
        jQuery('#new_cc_form select').val ('');
        jQuery('#new_cc_form').slideUp();
        if (jQuery('#new_card').is(":hidden")) {
            var new_height = jQuery('.content.clearfix').height() - 150;
            jQuery('.content.clearfix').css("height", new_height+"px");
            jQuery('#new_card').css("display", "block");
        }
    }
}).ready(function ($) {
    var form = $("#new-subscription").show();

    function iframeResizePipe()
    {
        // What's the page height?
        var height = document.body.scrollHeight;

        // Going to 'pipe' the data to the parent through the helpframe..
        var pipe = document.getElementById('new-subscription');

        // Cachebuster a precaution here to stop browser caching interfering
        pipe.src = 'http://www.foo.com/helper.html?height='+height+'&cacheb='+Math.random();
        console.log("entered function");

    }

    form.steps({
        headerTag: "h3",
        bodyTag: "fieldset",
        transitionEffect: "slideLeft",
        onInit: function (event, currentIndex) {
            $('#new-subscription').prepend ('<div id="loading"><span class="spinner">Loading...</span></div>');
        },
        onStepChanging: function (event, currentIndex, newIndex) {
            var eot_status = 1;
            my_response = null;
            // If the steps is moving backwards
            if (currentIndex > newIndex) {
                return true;
            }
            // If the steps is moving forward to the next step
            if (currentIndex < newIndex) {
                form.validate().settings.ignore = ":disabled,:hidden";
                if (!form.valid()) {
                    return form.valid();
                }
                show_loading();
                var data = $('#new-subscription').serialize () + "&action=subscribe&currentIndex="+currentIndex+"&newIndex="+newIndex;

                form.find(".body:eq(" + newIndex + ") label.error").remove();
                form.find(".body:eq(" + newIndex + ") .error").removeClass("error");

                $.ajax({
                    type: "POST",
                    url: eot.ajax_url,
                    data: data,
                    async: false, 
                    dataType: "json",
                    success: function(response) {
                        eot_status = response.status;
                        my_response = response;
                        if (!eot_status) {
                            show_error (my_response.message);
                            hide_loading();
                            return false;
                        }
                    }
                });

            }
            return eot_status;
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
            if (priorIndex < currentIndex) {
                switch(currentIndex) {
                    case 1:
                        $('#new-subscription .library').each (function () {
                            var name = $(this).attr('name');
                            if ($(this).attr('checked') == "checked") {
                                $('.staff_accounts#'+name+"_table").show();
                            } else {
                                $('.staff_accounts#'+name+"_table").hide();
                            }
                        });
                        break;
                    case 2:
                        var table_info;
                        $('#total_table .row').remove();
                        jQuery.each (my_response.message, function () {
                            var label = this.label;
                            var price = "$ " + this.price;
                            var name = this.name;
                            table_info += "<tr><td class='left label'>"+label+"</td><td class='field right'>"+price+"<input type='hidden' name='"+name+"' value='"+this.price+"' /></td></tr>";
                        });
                        table_info += "<tr><td class='label'><b>Total</b></td><td id='total_price'>$ <b>" + my_response.total_price +"</b></td></tr>";
                        $('#total').val (my_response.total_price);
                        $('#total_table').html("");
                        $('#total_table').prepend (table_info);
                        $('#total_table_payment').html("");
                        $('#total_table_payment').prepend( table_info );
                        break;
                    case 3:
                        break;
                    case 4:
                        break;

                }
            }
            hide_loading();
        },
        onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ":disabled,:hidden";
            if (!form.valid()) {
                return form.valid();
            }
            show_loading();
            var eot_status = 1;
            var data = $('#new-subscription').serialize () + "&action=subscribe&currentIndex="+currentIndex;

            $.ajax({
                type: "POST",
                url: eot.ajax_url,
                data: data, 
                async: false,
                dataType: "json",
                success: function(response) {
                    eot_status = response.status;
                    my_response = response;
                }
            });
            
            if (!eot_status) {
                show_error (my_response.message);
            }
            hide_loading();
            return eot_status;
        },
        onFinished: function (event, currentIndex) {
            window.location.href = eot.dashboard;
        },
        labels: {
            cancel: "Cancel",
            current: "current step:",
            pagination: "Pagination",
            finish: "Complete Payment",
            next: "Next",
            previous: "Previous",
            loading: "Loading ..."
        }
    }).validate({
        errorPlacement: function errorPlacement(error, element) { element.before(error); },
        rules: {
            confirm: {
                equalTo: "#password-2"
            }
        }
    });

    $('#new_card').click (function (e) {
        $('#new_cc_form').slideToggle();
        $('.cc_cards_list input[type="radio"]').removeAttr('checked');
        var new_height = $('.content.clearfix').height() + 150;
        $('.content.clearfix').css("height", new_height+"px");
        $('#new_card').css("display", "none");
        e.preventDefault ();
    });
});

function show_error (message) {
    alert (message);
}

function show_loading() {
    jQuery('#new-subscription #loading').show();
}
function hide_loading() {
    jQuery('#new-subscription #loading').hide();
}