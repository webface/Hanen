<?php
  // Variable declaration
  global $current_user;

  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess(); 

  $user_id = $current_user->ID;                  // Wordpress user ID
  $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
  $data = compact ("org_id", "user_id");
  $users = array(); // will store the users of our org
  $users = getEotUsers($org_id);
  $admin_ajax_url = admin_url('admin-ajax.php');

  if( isset($_REQUEST['status']) && isset($_REQUEST['status']) == 'uploadedspreadsheet' )
  {
?>
  <div class="msgboxcontainer ">  
    <div class="msg-tl">
      <div class="msg-tr"> 
        <div class="msg-bl">
          <div class="msg-br">
            <div class="msgbox"><h2>Spreadsheet Uploaded Successfully!</h2>Staff accounts have been created.
            <?php
              if( isset($_REQUEST['sent']) && $_REQUEST['sent'] == 1 )
              {
                echo '<br><b>Note:</b> Each user has been sent an e-mail containing their login credentials.';
              }
              else
              {
                $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                echo '<br><b>Note:</b> Some staff were NOT sent their login credentials. You can use the <a href="/dashboard/?part=email_staff&subscription_id='.$subscription_id.'">mass mail</a> function to send staff their password.';
              }

              if (isset($_REQUEST['import_status']) && !empty($_REQUEST['import_status']))
              {
                echo "<p> Error Report:<br><br>" . $_REQUEST['import_status'] . "<p>";
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

<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <?= CRUMB_ADMINISTRATOR ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current">Manage Staff Accounts</span>     
</div>
<?php
  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
  {
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    $subscription = getSubscriptions($subscription_id,0,1); // get the current subscription
    $library = getLibrary($subscription->library_id); // The library information base on the user current subscription
    $library_name = $library->name;
    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
      if(current_user_can( "is_director" ))
      {
?>
<h1 class="article_page_title">Manage Staff Accounts</h1>
<div style="float:left;" id="staff_list" class="holder osX" org_id="<?= $org_id ?>" subscription_id="">      
<?php
  // Create datatable for staff lists.
  $staffTableObj = new stdClass();
  $staffTableObj->rows = array();
  $staffTableObj->headers = array(
    'Name' => 'name',
    'E-mail Address' => 'e-mail',
    'Action' => 'center'
  );
?>
<br>
<?php 
      /*
       *  Check if there are any learners registered in this organization. 
       */
      if( count($users) > 0 )
      {
        foreach($users as $user) 
        { 
          $staff_id = $user['id'];
          $email = $user['email'];
          $staffTableObj->rows[] = array($user['first_name'] . " " . $user['last_name'], // First and last name
                                          $email, // The email address
                                          '<a href="'. $admin_ajax_url .'?action=getCourseForm&form_name=edit_staff_account&amp;org_id='. $org_id .'&amp;staff_id='.$staff_id.'" rel="facebox">
                                              <i class="fa fa-pencil tooltip" aria-hidden="true" style="color:green" onmouseover="Tip(\'Edit the staff account details.\', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')" onmouseout="UnTip()"></i>
                                            </a>&nbsp;&nbsp;&nbsp;
                                            <a href="'.$admin_ajax_url.'?action=getCourseForm&form_name=delete_staff_account&amp;org_id='.$org_id.'&amp;staff_id='.$staff_id.'&amp;email='.$email.'" rel="facebox">
                                              <i class="fa fa-trash tooltip" aria-hidden="true" style="color:green" onmouseover="Tip(\'Delete the staff account.\', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')" onmouseout="UnTip()"></i>
                                            </a>' // User options
                                        );
?> 

<?php 
        }
      }
      else
      {
        // No staff. Display message to create a staff account.
        echo '<div id="noStaffAccount" style="width:100%;text-align:center;font-size:140%;">Create a Staff Account...</div>';
      }
?>
<div style="width:625px;" class="tableheadboxcontainer">
  <div class="tablehead-tr">
    <div class="tablehead-tl">
      <div style="padding:7px;margin-left:5px;height:20px">
        <h3 style="float:left;" class="tablehead-title"> <?= $library_name . " " . SUBSCRIPTION_YEAR ?> Staff Listing</h3><div style="clear:both;"></div>
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
    var dataSet_<?=$id?> = <?=str_replace(array('(',')'), array('&#40;','&#41;'), json_encode($staffTableObj->rows))?>;
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
  <a class="btn" name="create_staff_account" style="" href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=create_staff_account&amp;org_id=<?= $org_id ?>&subscription_id=<?= $_REQUEST['subscription_id']?>" rel="facebox">
    Create Staff Account
  </a>

  <a class="btn" style="" href="<?= get_home_url() ?>/dashboard/?part=uploadspreadsheet&subscription_id=<?= $_REQUEST['subscription_id']?>">
    Upload A Spreadsheet
  </a>
  <a class="btn" style="" href="<?= get_home_url() ?>/dashboard/?part=invite_users&subscription_id=<?= $_REQUEST['subscription_id']?>&org_id=<?= $org_id ?>">
    Invite Users To Register
  </a>
  <!-- 
  <a class="btn" style="" href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=add_previous_staff_to_subscription&amp;year=<?= SUBSCRIPTION_YEAR ?>&amp;org_id=<?= $org_id ?>" rel="facebox">
    Manage Previous
  </a> 
  -->  
</div>
</div>
<base href="http://www.expertonlinetraining.com/my-dashboard.html" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow" />
<meta name="keywords" content="camp, staff, training, summer, online, courses, learning, lessons, videos, expert" />
<meta name="description" content="Summer Camp Staff Training from Expert Online Training provides a summer camp staff training solution with online videos, auto-marked quizzes, activity tracking and expert content to prepare your staff to meet any challenges. These are great summer camp training ideas for counselors." />
<meta name="generator" content="ExpertOnlineTraining" />
<title>Manage Staff Accounts</title>
<link href="https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<script type="text/javascript" src="<?= get_template_directory_uri() . "/js/jquery.mousewheel.js"?>"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() . "/js/jScrollPane.js"?>"></script> 
<link rel="stylesheet" type="text/css" media="all" href="<?= get_template_directory_uri() . "/css/jScrollPane.css"?>" /> 
<script type="text/javascript" src="<?= get_template_directory_uri() . "/js/jquery.rotate.js"?>"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() . "/js/jquery.tinymce.js"?>"></script>
<script type="text/javascript" src="<?= get_template_directory_uri() . '/js/tinymce/tiny_mce.js'?>"></script> 
<script type="text/javascript">

(function ($) 
{ 
  $(function()
  {
      $('a[class="btn"][name="create_staff_account"]').facebox(); // Bind facebox to create_staff_account button.
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
                  '<a href="<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=edit_staff_account&amp;org_id='+data.org_id+'&amp;staff_id='+data.user_id+"&email="'+data.email+'" rel="facebox">\
                    <i class="fa fa-pencil tooltip" aria-hidden="true" style="color:green"></i>\
                  </a>&nbsp;&nbsp;&nbsp;\
                  <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_staff_account&amp;org_id='+data.org_id+'&amp;staff_id='+data.staff_id+'&amp;email='+data.email+'" email="'+data.email+'" rel="facebox">\
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
        var staffTable = $('#DataTable_1').DataTable();
        staffTable.row( $("td.e-mail:contains('"+data.email+"')").parents('tr') ).remove().draw();
        //close facebox
        $(document).trigger('close.facebox') 
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
        console.log(data);
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
        echo "Unauthorized!";
      }
    }
    else
    {
      echo "subscription ID does not belong to you";
    }
  }
  // Could not find the subscription ID
  else
  {
    echo "Could not find the subscription ID";
  }
?>