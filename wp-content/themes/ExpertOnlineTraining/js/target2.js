
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
  
  