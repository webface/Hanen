<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <?= CRUMB_ADMINISTRATOR ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current"><?= __("Manage Staff Accounts", "EOT_LMS"); ?></span>     
</div>

<?php
  // Variable declaration
  global $current_user;

  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess(); 

  $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // The // Wordpress user ID                  // Wordpress user ID
  $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
  //$user_data = getEotUsers($org_id);
  
  $admin_ajax_url = admin_url('admin-ajax.php');

  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
  {
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    $subscription = getSubscriptions($subscription_id,0,1); // get the current subscription
    $user_data = getUsersInSubscription($subscription_id);
    $users = isset($user_data['users']) ? $user_data['users'] : array();
    $library = getLibrary($subscription->library_id); // The library information base on the user current subscription
    $library_name = $library->name;
    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
      if(current_user_can( "is_director" ))
      {
        echo '<h1 class="article_page_title">' . __("Manage Staff Accounts", "EOT_LMS") . '</h1>';

        if( isset($_REQUEST['status']) && isset($_REQUEST['status']) == 'uploadedspreadsheet' )
        {
?>
          <div class="msgboxcontainer ">  
            <div class="msg-tl">
              <div class="msg-tr"> 
                <div class="msg-bl">
                  <div class="msg-br">
                    <div class="msgbox"><h2><?= __("Spreadsheet Uploaded Successfully!", "EOT_LMS"); ?></h2><?= __("Staff accounts have been created.", "EOT_LMS"); ?>
                    <?php
                      if( isset($_REQUEST['sent']) && $_REQUEST['sent'] == 1 )
                      {
                        echo '<br><b>' . __("Note:", "EOT_LMS") . '</b> '. __("Each user has been sent an e-mail containing their login credentials.", "EOT_LMS");
                      }
                      else
                      {
                        echo '<br><b>' . __("Note:", "EOT_LMS") . '</b> ' . __("Some staff were NOT sent their login credentials. You can use the", "EOT_LMS") . ' <a href="/dashboard/?part=email_staff&subscription_id='.$subscription_id.'">' . __("mass mail", "EOT_LMS") . '</a> ' . __("function to send staff their password.", "EOT_LMS");
                      }

                      if (isset($_REQUEST['import_status']) && !empty($_REQUEST['import_status']))
                      {
                        echo "<p> " . __("Error Report:", "EOT_LMS") . "<br><br>" . $_REQUEST['import_status'] . "<p>";
                      }
                    ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
<?php
        }
?>

<div style="float:left;" id="staff_list" class="holder osX" org_id="<?= $org_id ?>" subscription_id="<?= $subscription_id ?>">      
<?php
          // Create datatable for staff lists.
          $staffTableObj = new stdClass();
          $staffTableObj->rows = array();
          $staffTableObj->headers = array(
            __("Name", "EOT_LMS") => 'name',
            __("E-mail Address", "EOT_LMS") => 'e-mail',
            __("Action", "EOT_LMS") => 'center'
          );
          /*
           *  Check if there are any learners registered in this organization. 
           */
          if( count($users) > 0 )
          {
            foreach($users as $user) 
            { 
              $staff_id = $user['ID'];
              $staff_name = htmlentities( $user['first_name'] . " " . $user['last_name'], ENT_QUOTES, 'UTF-8' );
              $email = $user['email'];

              $staffTableObj->rows[] = array (
                $staff_name, // First and last name
                $email, // The email address
                  '<a href="'. $admin_ajax_url .'?action=getCourseForm&form_name=edit_staff_account&amp;org_id='. $org_id .'&amp;staff_id='.$staff_id.'" rel="facebox">
                    <i class="fa fa-pencil tooltip" aria-hidden="true" style="color:green" onmouseover="Tip(\''. __("Edit the staff account details.", "EOT_LMS") . 
'\', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')" onmouseout="UnTip()"></i></a>&nbsp;&nbsp;&nbsp;
                  <a href="'.$admin_ajax_url.'?action=getCourseForm&form_name=delete_staff_account&amp;org_id='.$org_id.'&amp;staff_id='.$staff_id.'&amp;email='.$email.'&amp;subscription_id='.$subscription_id.'" rel="facebox">
                    <i class="fa fa-trash tooltip" aria-hidden="true" style="color:green" onmouseover="Tip(\'' . __("Delete the staff account.", "EOT_LMS") . '\', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')" onmouseout="UnTip()"></i></a>' // User options
              );
            }
          }
          else
          {
            // No staff. Display message to create a staff account.
            echo '<div id="noStaffAccount" style="width:100%;text-align:center;font-size:140%;">' . __("Create a Staff Account...", "EOT_LMS") . '</div>';
          }
?>
<div style="width:625px;" class="tableheadboxcontainer">
  <div class="tablehead-tr">
    <div class="tablehead-tl">
      <div style="padding:7px;margin-left:5px;height:20px">
        <h3 style="float:left;" class="tablehead-title"> <?= $library_name . " " . SUBSCRIPTION_YEAR ?> <?= __("Staff Listing", "EOT_LMS"); ?></h3><div style="clear:both;"></div>
      </div>  
    </div>
  </div>
</div>


<?php
static $i = 0;
  $i++;
  $id = (isset($staffTableObj->id) ? $staffTableObj->$id : "DataTable_$i");
?>
  <div class="smoothness">
    <table class="display" id="<?=$id?>" style="width:100%"></table>
  </div>
  <script type="text/javascript">
    var dataSet_<?=$id?> = <?=str_replace ( array('(',')'), array('&#40;','&#41;'), json_encode($staffTableObj->rows))?>;
    $ = jQuery;
    (function($){
      $(function(){
        $('#<?=$id?>').dataTable( {
          "iDisplayLength": 25,
          "aaData": dataSet_<?=$id?>,
          "bJQueryUI": true,
          "sPaginationType": "full_numbers",
          "bAutoWidth": false,
          "aoColumns": [
            <?php
              $columns = array();
              foreach($staffTableObj->headers as $header => $class) {
                if (is_array($class)) {
                  $prop = array();
                  $prop[] = '"sTitle": "'.addslashes($header).'"';
                  foreach($class as $field => $val) {
                    $prop[] .= "\"$field\": \"$val\"";
                  }
                  $columns[]= "{ ".implode($prop,',')." }";
                }
                else
                  $columns[]= '{ "sTitle": "'.addslashes($header).'", "sClass": "'.$class.'" }';
              }
              echo implode($columns,',');
            ?>
          ],
          fnInitComplete: function(){
            $.animateProgressBars($('#<?=$id?> .ui-progressbar'));
          },
          // bind actions to the edit, delete and click events for newly created account and modified
          "drawCallback": function( settings ) {
              $('a[rel*=facebox] > i.fa-pencil, a[rel*=facebox] > i.fa-trash').parent().off();
              $('a[rel*=facebox] > i.fa-pencil, a[rel*=facebox] >  i.fa-trash').parent().facebox();
          }
        } );
      });
    })(jQuery);
  </script>
<div class="bottom_buttons">
  <a class="btn" name="create_staff_account" style="" href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=create_staff_account&amp;org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&user_id=<?= $user_id ?>" rel="facebox">
    <?= __("Create Staff Account", "EOT_LMS"); ?>
  </a>

  <a class="btn" style="" href="<?= get_home_url() ?>/dashboard?part=uploadspreadsheet&subscription_id=<?= $subscription_id ?>&user_id=<?= $user_id ?>&org_id=<?= $org_id ?>">
    <?= __("Upload A Spreadsheet", "EOT_LMS"); ?>
  </a>
  <a class="btn" style="" href="<?= get_home_url() ?>/dashboard?part=invite_users&subscription_id=<?= $subscription_id ?>&org_id=<?= $org_id ?>&user_id=<?= $user_id ?>">
    <?= __("Invite Users To Register", "EOT_LMS"); ?>
  </a>
   
  <a class="btn" style="" href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=add_previous_staff_to_subscription&amp;year=<?= SUBSCRIPTION_YEAR ?>&amp;org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>" rel="facebox">
    Manage Previous
  </a> 
    
</div>
</div>

      <link rel="stylesheet" type="text/css" media="all" href="<?= get_template_directory_uri() . "/css/jquery.jscrollpane.css"?>" /> 
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.mousewheel.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.jscrollpane.min.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.rotate.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.tinymce.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/tinymce/tiny_mce.js'?>"></script> 

<script type="text/javascript">
(function ($) 
{ 
  $(function()
  {
      $('a[class="btn"][name="create_staff_account"]').facebox(); // Bind facebox to create_staff_account button.
      $('a[class="btn"][rel="facebox"]').facebox();
      $('#debugger').click(
      function()
      {
        var data = new Object();
        data.name = "zktest";
        data.lastname = "lastname";
        data.email = "email@test.com";
        data.id = 4;
        $(document).trigger('success.create_staff_account',data)
      }
      );
  /******************************************************************************************
  * Binds a function to the success event msg sent
  * 
  *******************************************************************************************/ 
  $(document).bind('success.send_message',
  function (event,data)
  {
    jQuery(document).trigger('close.facebox');
  }
  )
    /******************************************************************************************
      * Binds a function to the success event of the create_staff_account form
      * 
      *******************************************************************************************/               
      $(document).bind('success.create_staff_account',
      function (event,data)
      {
        if(data.msg_sent)
        {
          jQuery.facebox({ajax: ajax_object.ajax_url + '?action=getCourseForm&form_name=send_message&target=create_account&email='+data.email+'&password='+data.password+'&name='+data.name+'&org_id='+data.org_id+'&last_name='+data.last_name}); 
        }
        else
        {
          //close facebox
          if(typeof data.close == "undefined") 
            $(document).trigger('close.facebox')
        }
        $( "#noStaffAccount" ).remove(); // Remove message no staff account
        var staffTable = $('#DataTable_1').DataTable(); // The table for manage staff accounts listing.
        // Create a new row for the newly created user.
        staffTable.row.add([data.name + " " + data.lastname, // First and last name
                  data.email, // Email Address
                  '<a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=edit_staff_account&org_id='+data.org_id+'&staff_id='+data.user_id+'&email='+data.email+'" rel="facebox">\
                    <i class="fa fa-pencil tooltip" aria-hidden="true" style="color:green"></i>\
                  </a>&nbsp;&nbsp;&nbsp;\
                  <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_staff_account&org_id='+data.org_id+'&staff_id='+data.staff_id+'&email='+data.email+'" email="'+data.email+'" rel="facebox">\
                    <i class="fa fa-trash tooltip" aria-hidden="true" style="color:green"></i>\
                  </a>\
                      ' // User options
        ]).draw( false );
      });
    /******************************************************************************************
      * Binds a function to the scroll pane to be executed when scrollpane is init'd and scrollable
      * 
      *******************************************************************************************/        
    $("#staff_list_pane").bind('jsp-added',
      function()
      {
        $('div.staff_list_delete_row').css({'left':'590px'});
        $('div.staff_list_edit_row').css({'left':'575px'});
      }
    );
    /******************************************************************************************
      * Binds a function to the scroll pane to be executed when scrollpane is init'd and not scrollable
      * 
      *******************************************************************************************/        
    $("#staff_list_pane").bind('jsp-removed',
      function()
      {
        $('div.staff_list_delete_row').css({'left':'605px'});
        $('div.staff_list_edit_row').css({'left':'590px'});
      }
    );  
    $("#staff_list_pane").jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
    /******************************************************************************************
      * Binds a function to the onclick event in each clickable area of each staff list row.
      * 
      *******************************************************************************************/  
    $("div.staff_list_clickable_area").click(
      function()
      {
        $(document).trigger('clicked.staff_list_clickable_area');
      }
    )
    //$(document).bind('click.staff_list_clickable_area',
    $(document).bind('clicked.staff_list_clickable_area',
      function()
      {
        //alert ("what do i want this to do hmmmm......");
      }
    );
    /******************************************************************************************
      * Handles staff account deletion 
      * 
      *******************************************************************************************/        
    $(document).bind('success.delete_staff_account',
      function(event,data)
      {
          console.log(data);
        var staffTable = $('#DataTable_1').DataTable();
        staffTable.row( $("td.e-mail:contains('"+data.email+"')").parents('tr') ).remove().draw();
        //close facebox
        $(document).trigger('close.facebox'); 
      }
    );          
    /******************************************************************************************
      * Handles staff account edit
      * 
      *******************************************************************************************/        
    $(document).bind('success.edit_staff_account',
      function(event,data)
      {
        //close facebox
        //console.log(data);
        jQuery(document).trigger('close.facebox');
        
        var staffTable = $('#DataTable_1').DataTable(); // Staff Listing Table
        staffTable.row( $("td.e-mail:contains('"+data.old_email+"')").parents('tr') ).remove().draw(); // Remove the old email

        // Create the row for the updated information.
        staffTable.row.add([data.first_name + " " + data.last_name, // First and last name
                  data.email, // Email Address
                  '<a href="<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=edit_staff_account&amp;org_id='+data.org_id+'&amp;staff_id='+data.user_id+'" email="'+data.email+'" rel="facebox">\
                    <i class="fa fa-pencil tooltip" aria-hidden="true" style="color:green"></i>\
                  </a>&nbsp;&nbsp;&nbsp;\
                  <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_staff_account&amp;org_id='+data.org_id+'&amp;staff_id='+data.user_id+'&amp;email='+data.email+'" email="'+data.email+'" rel="facebox">\
                    <i class="fa fa-trash tooltip" aria-hidden="true" style="color:green"></i>\
                  </a>\
                      ' // User options
        ]).draw( false );
      }
    );
    /******************************************************************************************
      * Handles the addition of an existing staff member to the current subscription
      * 
      *******************************************************************************************/        
    $("#add_previous_staff_to_subscription").click(
      function ()
      {
        setTimeout(
          function (){
            $('#staff_listing_pane').css({'height':'350px'}).jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
            $('.add_to_sub_btn').click(
              function()
              {
                $.getJSON('http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=add_user_to_sub&format=ajax&sub_id=2857&user_id='+$(this).attr("user_id"))
                $(this).fadeOut("slow").closest('div.staff_and_assignment_list_row').css({'background-color':'#D7F3CA'})
                //var data = jQuery.Event('success.create_staff_account');
                var data = new Object();
                data.msg_sent = 0;
                data.id = $(this).attr("user_id");
                data.name = $(this).attr("name");
                data.close = true;
                data.lastname = $(this).attr("lastname");
                data.email = $(this).attr("email");
                alert("created");
                $(document).trigger("success.create_staff_account",data);
              }
            )
          },2500);
      }
    );        
    $("#thetest").click(
      function (){
        alert("hi")
        alert($(".csis").length)
      }
    )
    // Manage the add and remove button action.
            //$('.add_remove_btn').live('click', function () {
            $(document).on('click','.add_remove_btn',function(){
              var task = "";
              //alert($(this).attr("status"));
              if ($(this).attr("status")=="add")
              {
              task = "enrollUserInSubscription";
              }
              else if($(this).attr("status")=="remove")
              {
              task = "deleteEnrolledUserInSubscription";
              //enrollment_id = $(this).attr("enrollment_id");
              }
              if (task!="")
              {
              var loading_img = $('<img />', { 
                class: 'add_remove_btn',
                src: ajax_object.template_url + '/images/loading.gif',
                alt: 'loading...',
                status: 1
              });
              var temp =  $(this);
              $(this).replaceWith(loading_img);
              var btn = $(this);

              $.getJSON( ajax_object.ajax_url + '?action='+task+'&email='+encodeURIComponent($(this).attr("email"))+'&org_id='+$(this).attr("org_id")+'&subscription_id='+$(this).attr("subscription_id")+'&user_id='+$(this).attr("user_id")+'&nonce='+$(this).attr("nonce"),
                function (json)
                {
                if(json.success)
                {
                  if(task == "enrollUserInSubscription")
                  {
                    temp.text( "<?= __("Remove", "EOT_LMS"); ?>" );
                    temp.attr( "status" , "remove" );
                    temp.attr( "selected" , 1 );
                    temp.attr( "insert_id", json.insert_id);
                    loading_img.replaceWith(temp);
                    btn.parent().parent().css("background-color","#d7f3ca");
                    $( "#noStaffAccount" ).remove(); // Remove message no staff account
                    var staffTable = $('#DataTable_1').DataTable(); // The table for manage staff accounts listing.
                    // Create a new row for the newly created user.
                    staffTable.row.add([json.first_name + " " + json.last_name, // First and last name
                              json.email, // Email Address
                              '<a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=edit_staff_account&org_id='+json.org_id+'&staff_id='+json.user_id+'&email='+json.email+'" rel="facebox">\
                                <i class="fa fa-pencil tooltip" aria-hidden="true" style="color:green"></i>\
                              </a>&nbsp;&nbsp;&nbsp;\
                              <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_staff_account&org_id='+json.org_id+'&staff_id='+json.user_id+'&email='+json.email+'" email="'+json.email+'" rel="facebox">\
                                <i class="fa fa-trash tooltip" aria-hidden="true" style="color:green"></i>\
                              </a>\
                                  ' // User options
                    ]).draw( false );
                  }
                  else
                  {
                    temp.text( "<?= __("Add", "EOT_LMS"); ?>" );
                    temp.attr( "status" , "add" );
                    temp.attr( "selected" , 0 );
                    loading_img.replaceWith(temp); // CHHANGE STATUS MAYBE
                    btn.parent().parent().css("background-color","");
                    var staffTable = $('#DataTable_1').DataTable(); // The table for manage staff accounts listing.
                    var email = json.email;
                    staffTable.rows( function ( idx, data, node ) {
                                  return data[1] === email;//column 1
                              } )
                              .remove()
                              .draw();
                  }                                
                  //$('#staff_and_assignment_list').attr("refresh",1);
                }
                else
                {
                  $('.errorbox').text(json.errors);
                  $('.errorbox').show();
                }
                });
              }
            });

            /******************************************************************************************
      * Binds a function to the success event of the create_staff_account form
      * 
      *******************************************************************************************/               
      $(document).bind('success.add_previous_staff_to_subscription',
      function (event,data)
      {
        alert('success');
          //close facebox
          if(typeof data.close == "undefined") 
            $(document).trigger('close.facebox')
        
        $( "#noStaffAccount" ).remove(); // Remove message no staff account
        var staffTable = $('#DataTable_1').DataTable(); // The table for manage staff accounts listing.
        // Create a new row for the newly created user.
        staffTable.row.add([data.name + " " + data.lastname, // First and last name
                  data.email, // Email Address
                  '<a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=edit_staff_account&org_id='+data.org_id+'&staff_id='+data.user_id+'&email='+data.email+'" rel="facebox">\
                    <i class="fa fa-pencil tooltip" aria-hidden="true" style="color:green"></i>\
                  </a>&nbsp;&nbsp;&nbsp;\
                  <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_staff_account&org_id='+data.org_id+'&staff_id='+data.staff_id+'&email='+data.email+'" email="'+data.email+'" rel="facebox">\
                    <i class="fa fa-trash tooltip" aria-hidden="true" style="color:green"></i>\
                  </a>\
                      ' // User options
        ]).draw( false );
      });
      /////////
}
);
})(jQuery);
</script>
<style type ="text/css">
  #staff_list_pane
  {
   height:425px;
  }
input.error
{
  /*color: #FFFFF;*/
  background: #F39A85;
  border: 2px solid #F33131
}
div.staff_list_clickable_area
{
/*border:1px solid pink;*/
width:560px;
}
a {
outline: none;
}
</style> 

<?php
      }
      else
      {
        echo __("Unauthorized!", "EOT_LMS");
      }
    }
    else
    {
      echo __("subscription ID does not belong to you", "EOT_LMS");
    }
  }
  // Could not find the subscription ID
  else
  {
    echo __("Could not find the subscription ID", "EOT_LMS");
  }
?>