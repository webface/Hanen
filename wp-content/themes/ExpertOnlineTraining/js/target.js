jQuery(document).ready(function ($) {
    // Handes the responsive design menus.
    $('.navbar-toggle').click(function (e) {
      $("ul#menu-main-menu-logged-in").slideToggle( "slow", function() {
      // Animation complete.
      });
    });
    $('#header .menu').click(function (e) {
        $('#mynav').slideToggle ('fast');
        e.preventDefault ();
    });

    //This makes sure the methods of validations are fired on input change
    $.validator.setDefaults({
      onfocusout: function(element) { $(element).valid(); },
      onkeyup: function(element) { $(element).valid(); }
    });

    //This  makes sure the email checking is done in the format of 'example@company.domain'
    $.validator.methods.email = function( value, element ) {
      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return this.optional( element ) || re.test(value);
//      return this.optional( element ) || /[a-z|A-Z|0-9|_-\.]+@[a-z]+\.[a-z]+/.test( value );
    }

    var form = $("#new-account").show();
    
    //This method is added for password strength check
    $.validator.addMethod("strengthCheck",
      function(value, element)
      {
        var password = value;                                               //the password
        var strengthNumber = passwordStrength(password, [], password);      //this is a wordpress function that will check the strength of the password and will output a number from 0-4
        var result = false;                                                 //will be retuned at the end [true or false]
        if(strengthNumber >= 2)                                             //2 means Good which is required for this field
        {
          result = true;                                                    //method reutrns true when password is Good
        }

        //array for strength wording
        var strength = {
          0: "Bad",
          1: "Weak",
          2: "Good",
          3: "Strong",
          4: "Super Strong"
        };
        
        //update the strength wording
        $('#password-strength-text').html("Strength: " + "<strong>" + strength[strengthNumber] + "</strong>"); 

        //update the password strength bar
        $('#password-strength-meter').val(strengthNumber);

        return result;

      });
    

    //This method is added for checking the email against the database for already existing one
    $.validator.addMethod("checkEmail",
      function(value, element)
      {
      var email = value;                                //the email
      var data = { action: 'checkEmail', email: email};  //data for the ajax call
      var url = ajax_object.ajax_url;                   //url for the ajax call
      var result = false;                               //will be retuned at the end [true or false]
      
      //ajax call to checkEmail function in user_function.php file to check the email against the database (returns true if it does not exist in the databse)
      $.ajax({
        type: "POST",
        async: false,
        url: url,
        dataType: "json",
        data: data,
        success:
        function(data)
        {
          if(data == true)
          {
            result = true;                            //the email does not exist in the database
          }
        }
      });

      return result;

    },
    "Email already exists! Try again.");              //the error message to be displayed when this method returns false
    
    //This method is added for checking the portal name for letters only
    $.validator.addMethod("checkPortalRegex",
      function(value, element)
      {
        var portal_name = value;                                                 //the portal name
        var result = false;                                                      //will be retuned at the end [true or false]
        // regex for portal name. letters only

        var re = /^[a-zA-Z]+$/;
        if (re.test(portal_name))
        {
          result = true;
        }
        return result;
      }, 
      "Portal must contain Letters Only! Remove spaces or numbers.");


    //This method is added for checking the portal name against the database for already existing one
    $.validator.addMethod("checkPortal",
      function(value, element)
      {
      var portal_name = value;                                                 //the portal name
      var data = { action: 'checkPortal', portal_name: "eot"+portal_name};      //data for the ajax call
      var url = ajax_object.ajax_url;                                          //url for the ajax call
      var result = false;                                                      //will be retuned at the end [true or false]

      //ajax call to checkPortal function in user_function.php file to check the portal name against the database (returns true if it does not exist in the databse)
      $.ajax({
        type: "POST",
        async: false,
        url: url,
        dataType: "json",
        data: data,
        success:
        function(data)
        {
          if(data == true)
          {
            result = true;                                                    //the portal name does not exist in the database
          }
        }
      });
      return result;

    },
    "Portal name already esists! Try again.");                                //the error message to be displayed when this method returns false
    
    //This method is added for checking the camp name for letters, numbers, spaces, hyphens , underscores, commas and periods
    $.validator.addMethod("checkNameRegex",
      function(value, element)
      {
        var camp_name = value;                                                   //the camp name
        var result = false;                                                      //will be retuned at the end [true or false]
        // regex for camp name. letters only

        var re = /^[a-zA-Z0-9-_ ,.]+$/;
        if (re.test(camp_name))
        {
          result = true;
        }
        return result;
      }, 
      "Camp name can contain letters, numbers, and -_,. only!");

    form.steps({
        headerTag: "h3",
        bodyTag: "fieldset",
        transitionEffect: "slideLeft",
        onInit: function (event, currentIndex) {
            $('#new-account').prepend ('<div id="loading"><span class="spinner">Loading...</span></div>');
        },
        onStepChanging: function (event, currentIndex, newIndex) {
            $('#new-account #loading').show();
            // If the steps is moving backwards
            if (currentIndex > newIndex) {
                return true;
            }
            // If the steps is moving forward to the next step
            if (currentIndex < newIndex) {
                
            }
            
            form.validate().settings.ignore = ":disabled,:hidden";
            $('#new-account #loading').hide();
            return form.valid();
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
            $('#new-account #loading').hide();
        },
        onFinishing: function (event, currentIndex) {
            $('#new-account #loading').show();
            form.validate().settings.ignore = ":disabled,:hidden";
            if (!form.valid()) {
                $('#new-account #loading').hide();
                return form.valid();
            }
            var eot_status = 1;
            var user_id = 0;
            var data = $('#new-account').serialize ();

            $.ajax({
                type: "POST",
                url: eot.ajax_url,
                data: data,
                async: false,
                dataType: "json",
                success: function(response) {
                    eot_status = response.status;
                    my_response = response;
                    user_id = response.user_id; // New wordpress user ID 
                    $("#new-account").attr("data-user_id", user_id);
                    if (!eot_status) {
                        $('#new-account #loading').hide();
                        $('#new-account-message').html(my_response.message);
                        $('#new-account-message').show();
                    }
                },
                error: function(response){
                  $("#new-account").attr("data-status", 'false');
                  $('#new-account #loading').hide();
                  $('#new-account-message').html(my_response.message);
                  $('#new-account-message').show();
                }
            });

            return eot_status;
        },
        onFinished: function (event, currentIndex) {
          // check if there is a salesrep input and redirect accordingly
          var salesrep = $("input[name='salesrep']").attr('value');
          var user_id = $("#new-account").attr("data-user_id"); // Wordpress new user ID
          window.location.href = salesrep ? '?part=admin_create_account&user_id=' + user_id : eot.dashboard;
        },
        labels: 
        {
            cancel: "Cancel",
            current: "current step:",
            pagination: "Pagination",
            finish: "Create Your Account",
            next: "Next",
            previous: "Previous",
            loading: "Loading ..."
        }
    }).validate({
        errorPlacement: function(error, element) {
          if ($(element).attr("name") == "usr_password") {
            return false;                // filter message for password field (custom message displayed with a meter bar in strengthCheck method)
          } else {
            error.insertAfter(element); // default message placement
          }
        },
        //This prevents everything other than the password field to be validated on keyup
        onkeyup: function(element) {
          if ($(element).attr('name') != 'usr_password' && $(element).attr('name') != 'org_name') {
          }
          else{
            $(element).valid();
          }
        },
        rules: {
            email2: {
                equalTo: "#usr_email"
            },
            password2: {
                equalTo: "#usr_password"
            },
            usr_password: {
                strengthCheck: true     //calling strengthCheck method on the password field
            },
            usr_email: {
                checkEmail: true        //calling checkEmail method on the email field
            },
//            org_subdomain: {
//                checkPortalRegex: true,  // check portal is letters only
//                checkPortal: true       //calling checkPortal method on the portal name field
//            },
            org_name: {
                checkNameRegex: true
            }
        }
    });

    //password generation
    $('#password-generate').click(function(){
      var result = "error";                                //result
      var data = { action: 'generatePassword'}  //data for the ajax call
      var url = ajax_object.ajax_url;                   //url for the ajax call

      //ajax call to generate password
      $.ajax({
        type: "POST",
        async: false,
        url: url,
        dataType: "json",
        data: data,
        success:
        function(data)
        {
          result = data;
        }
      });

      
      $('#suggested-password').val(result);
      return false;
    });
});


  /*****************************************************************
   *
   *****************************************************************/  
  function toggle_show(id)
  {
    if(document.getElementById(id).style.display == 'block')
    {
      document.getElementById(id).style.display = 'none'
    }
    else
    {
      document.getElementById(id).style.display = 'block';
    }
  }
  
  /*****************************************************************
   *
   *****************************************************************/  
  function toggle_custom(id)
  {
    if(document.getElementById(id).style.active == '')
    {
      //document.getElementById(id).style... = '';
    }
    else
    {
      //document.getElementById(id).style... = '';
    }
  }
  
  /*****************************************************************
   *
   *****************************************************************/     
  function activate_custom_staff_number(id)
  {
    document.getElementById('custom_staff_number'+id).disabled = false;
    document.getElementById('exactly'+id).style.visibility = 'visible';
    if(id == 5)
    {
      document.subscribe.custom_staff_le.value = '200';
      document.subscribe.custom_staff_le.focus();
      document.subscribe.custom_staff_le.select();
    }
    if(id == 6)
    {
      document.subscribe.custom_staff_me.value = '11';
      document.subscribe.custom_staff_me.focus();
      document.subscribe.custom_staff_me.select();
    }
  }
  
  /*****************************************************************
   *
   *****************************************************************/  
  function deactivate_custom_staff_number(id)
  {
    document.getElementById('custom_staff_number'+id).disabled=true;
    document.getElementById('exactly'+id).style.visibility = 'hidden';
  }
  /*****************************************************************
   * Autofill Suggestions box for create_staff account
   *****************************************************************/ 
		function suggest(inputString, orgid, type){
			   var autofill = "#autofill_"+type;
			   var suggestions = "#autofill_suggestions_"+type;
			   var suggestionsList = "#autofill_suggestionsList_"+type;
			   if(inputString.length == 0) {
			 	   jQuery(function($) { $(suggestions).fadeOut(); });
			   } else {
			 	  jQuery(function($) {
			 		  $(autofill).addClass('autofill_load');
			 		     $.post("autosuggest.php", {queryString: ""+inputString+"", org_id: ""+orgid+"", type: ""+type+""}, function(data){
							  if(data.length >0) {
								var search_text = new RegExp("No suggestions found");
								var no_results = data.search(search_text);
								if (no_results == -1) {
									$(suggestions).fadeIn();
			 					   $(suggestionsList).html(data);
			 					   $(autofill).removeClass('autofill_load');
								} else {
									close_box(type);
									$(autofill).removeClass('autofill_load');
								}
							  }
			 		     });   
			 	  });
			   }
		}

		 function fill(name, lastname, email, type) {
			 jQuery(function($) {
				  $('#autofill_name').val(name);
				  $('#autofill_lastname').val(lastname);
				  $('#autofill_email').val(email);
				  var timeout = "#autofill_suggestions_"+type;
				  $(timeout).fadeOut();
			 });
		 }    
		 
		 function close_box(type) {
			var timeout = "#autofill_suggestions_"+type;
			jQuery(function($) {
				$(timeout).fadeOut();
			});
		 }
         
         jQuery(function($) {
			 $('.autofill_close').live('click', function() {
				$(this).parent().fadeOut();
			 });
		 });  
  
  