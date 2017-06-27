<?php
  // Variable declaration
  global $current_user;

  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess(); 

  $user_id = $current_user->ID;                  // Wordpress user ID
  $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
  $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
  $data = compact ("org_id", "user_id");
  $users = array(); // will store the users of our portal
  //$response = getUsers($org_subdomain, $data);
  $response=  getEotUsers($org_id);

  if (isset($response['status']) && $response['status'])
  {
    $users = $response['users'];
  }

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
                echo '<br><b>Note:</b> Staff were NOT sent their login credentials. You will need to inform them of their password yourself.';
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

    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
      if(current_user_can( "is_director" ))
      {
?>
<h1 class="article_page_title">Manage Staff Accounts</h1>
<div style="float:left;" id="staff_list" class="holder osX" org_id="<?= $org_id ?>" subscription_id="">      
<div style="width:625px;" class="tableheadboxcontainer">
  <div class="tablehead-tr">
    <div class="tablehead-tl">
      <div style="padding:7px;margin-left:5px;height:20px">
        <h3 style="float:left;" class="tablehead-title"> Leadership Essentials <?= SUBSCRIPTION_YEAR ?> Staff Listing</h3><div style="clear:both;"></div>
      </div>  
    </div>
  </div>
</div>
<div class="jScrollPaneContainer jScrollPaneScrollable" tabindex="0" style="height: 425px; width: 625px;">
  <div id="staff_list_pane" class="scroll-pane" style="width: 610px; overflow: visible; height: auto; padding-right: 0px; position: absolute; top: -575.884px;">
    <div id="staff_list_wrapper" style="width:100%;">   
      <?php 
        $user_learner = 0; // Number of learners.
        /*
         *  Check if there are any learners registered in this organization. 
         */
        foreach($users as $user) 
        {
          if($user['user_type'] == 'learner')
            $user_learner++;
            continue;
        }
        /* 
         * Check if there are any learners.
         */
        if($user_learner > 0)
        {
          foreach($users as $user) 
          {
            if($user['user_type'] == 'admin')
              continue;
            
            $staff_id = $user['id'];
            $email = $user['email'];
        ?> 

            <div class="staff_list_table_row" user_id="<?= $staff_id ?>">
              <div class="staff_list_clickable_area">
                <div>
                  <span class="staff_name"><?= $user['first_name'] ?> <?= $user['last_name'] ?> </span>/ <span class="staff_email"><?= $user['email'] ?></span>               
                </div>         
              </div>
              <div class="staff_list_edit_row" style="left: 575px;">
                <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=edit_staff_account&amp;org_id=<?= $org_id ?>&amp;staff_id=<?= $staff_id ?>&amp;portal_subdomain=<?= $org_subdomain ?>" rel="facebox">
                  <img <img src="<?= get_template_directory_uri() . "/images/icon-edit.gif"?>" title="" class="tooltip" style="margin-bottom:2px;" onmouseover="Tip('Edit the staff account details.', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()">
                </a>
              </div>
              <div class="staff_list_delete_row" style="left: 590px;">
                <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_staff_account&amp;org_id=<?= $org_id ?>&amp;staff_id=<?= $staff_id ?>&amp;portal_subdomain=<?= $org_subdomain ?>&amp;email=<?= $email ?>" rel="facebox">
                        <img src="<?= get_template_directory_uri() . "/images/delete.gif"?>" title="" class="tooltip" style="margin-bottom:2px;" onmouseover="Tip('Delete the staff account.', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()">
                      </a>
                </div>
              </div>

        <?php 
          }
        }
        else
        {
          echo '<div id="noStaffAccount" style="width:100%;text-align:center;padding-top:100px;font-size:140%;">Create a Staff Account...</div>';
        }
      ?>
    </div>
  </div>
  <div class="jScrollCap jScrollCapTop" style="height: 0px;">
  </div>
  <div class="jScrollPaneTrack" style="width: 15px; height: 393px; top: 16px;">
    <div class="jScrollPaneDrag" style="width: 15px; height: 104.718px; top: 141.756px;">
      <div class="jScrollPaneDragTop" style="width: 15px;">
        
      </div>
      <div class="jScrollPaneDragBottom" style="width: 15px;">
          
      </div>
    </div>
  </div>
  <div class="jScrollCap jScrollCapBottom" style="height: 0px;">
  </div>
  <a href="javascript:;" class="jScrollArrowUp" tabindex="-1" style="width: 15px; top: 0px;">Scroll up</a>
  <a href="javascript:;" class="jScrollArrowDown" tabindex="-1" style="width: 15px; bottom: 0px;">Scroll down</a>
</div>
<div class="listing-footercontainer">
  <div class="listing-footer-bl">
    <div class="listing-footer-br">&nbsp;
    </div>
  </div>
</div>
<div class="bottom_buttons">
  <a class="btn" style="" href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=create_staff_account&amp;org_id=<?= $org_id ?>&amp;portal_subdomain=<?= $org_subdomain ?>&subscription_id=<?= $subscription_id?>" rel="facebox">
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


<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.min.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery-ui.min.js'?>"></script>
<link type="text/css" href="<?= get_template_directory_uri() . "/css/jqueryui/jquery-ui-1.8.22.custom.css"?>" rel="stylesheet" />
<link type="text/css" href="<?= get_template_directory_uri() . "/css/jqueryui/style.css"?>" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.dataTables.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.eotprogressbar.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.eotdatatables.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/target2.js'?>"></script>
<link href="<?= get_template_directory_uri() . "/css/facebox.css"?>" media="screen" rel="stylesheet" type="text/css"/>
<script src="<?= get_template_directory_uri() . '/js/facebox.js'?>" type="text/javascript"></script>
<script src="<?= get_template_directory_uri() . '/js/flowplayer.min.js'?>"></script>

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
      $('a[rel*=facebox]').facebox();
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
        $( "#noStaffAccount" ).remove();
        //add row to the current list in the right spot
        var element_list = $('#staff_list .staff_list_table_row');
        var found = false;
        var i=0;              
        var index = 0;
        // Create a new DIV for the newly created user.
        var div = '<div class="staff_list_table_row" user_id="'+data.user_id+'">\
            <div class="staff_list_clickable_area">\
              <div>\
                <span class="staff_name">'+data.name+' '+data.lastname+' </span>/ <span class="staff_email">'+data.email+'</span>\
              </div>\
          </div>\
          <div class="staff_list_edit_row" style="left: 590px;">\
              <a href="<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=edit_staff_account&amp;org_id='+data.org_id+'&amp;staff_id='+data.user_id+'&amp;portal_subdomain='+data.portal_subdomain+'" rel="facebox">\
                <img src="<?= get_template_directory_uri() . "/images/icon-edit.gif"?>" title="" class="tooltip" style="margin-bottom:2px;" onmouseover="Tip("Edit the staff account details.", FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, "#E5E9ED", BORDERCOLOR, "#A1B0C7", PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, "#F1F3F5")" onmouseout="UnTip()">\
              </a>\
          </div>\
          <div class="staff_list_delete_row" style="left: 605px;">\
              <a href="<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=delete_staff_account&amp;org_id='+data.org_id+'&amp;staff_id='+data.user_id+'&amp;portal_subdomain='+data.portal_subdomain+'&amp;email='+data.email+'" rel="facebox">\
                    <img src="<?= get_template_directory_uri() . "/images/delete.gif"?>" title="" class="tooltip" style="margin-bottom:2px;" onmouseover="Tip("Delete the staff account.", FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, "#E5E9ED", BORDERCOLOR, "#A1B0C7", PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, "#F1F3F5")" onmouseout="UnTip()">\
                </a>\
          </div>\
        </div>';
        for(i=0;i<element_list.length&&!found;i++)
        {
          if ((data.name+' '+data.lastname).toLowerCase()<=(element_list.eq(i).find('span.staff_name').html()).toLowerCase())
          {  
        $(div).insertBefore(element_list.eq(i));
            found = true;
            index = i;
          }
          //if we've reached the last element on the list and still not found, append it on the end
          if(!found&&i==element_list.length-1)
          {
            $(div).insertAfter(element_list.eq(i));
            index = i+1;
          }
        }
        //if there are no staff accounts created yet
        if(element_list.length==0)
        {
          //$('#staff_list:nth-child(1)').html(div);
          $('#staff_list_wrapper').html(div);
        }
        //bind actions to the edit, delete and click events
        $('div.staff_list_table_row[user_id = '+data.user_id+']')
          .find('a[rel*=facebox]').facebox().end()
          .find('.staff_list_clickable_area').click(
            function()
            {
              $(document).trigger('clicked.staff_list_clickable_area');
            }
          );
        $('div.staff_list_table_row[user_id = '+data.user_id+']')
        //resize scrollbar
        $("#staff_list_pane").jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
        //scroll to the new created staff
        $("#staff_list_pane")[0].scrollTo($('#staff_list .staff_list_table_row').eq(index).position().top);
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
        //close facebox
        $(document).trigger('close.facebox') 
        //Hide the deleted row
        $('.staff_list_table_row[user_id = '+data.user_id+']')
        .hide('slow', 
          function()
          {
            $("#staff_list_pane").jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
            $(this).remove(); // Remove the DIV
          }
        )
        var element_list = $('#staff_list .staff_list_table_row');  
        //if there are no staff accounts display a Create A Staff Account Message
        if(element_list.length==1)
        {
          $('#staff_list_wrapper').html('<div id="noStaffAccount" style="width:100%;text-align:center;padding-top:100px;font-size:140%;">Create a Staff Account...</div>');
        }
      }
    );          
    /******************************************************************************************
      * Handles staff account edit
      * 
      *******************************************************************************************/        
    $(document).bind('success.edit_staff_account',
      function(event,data)
      {
          console.log(data);
        //close facebox
        jQuery(document).trigger('close.facebox');
        location.reload();
        return;
        var element_list = $('#staff_list .staff_list_table_row');            
        var found = false;
        var i=0;
        var div = $('div.staff_list_table_row[user_id = '+data.staff_id+']');
        var index = 0;
        //compare new name to names in the list and move it to new location
        for(i=0;i<element_list.length&&!found;i++)
        {
          if (!found&&((data.name+' '+data.lastname).toLowerCase()<=(element_list.eq(i).find('.staff_name').html()).toLowerCase()))
          {  
            found = true;
            index = i;
            if(div.attr("user_id")!=element_list.eq(i).attr("user_id"))
              div.insertBefore(element_list.eq(i));
          }
          if(!found&&i==element_list.length-1)
          {
            index = i;
            if(div.attr("user_id")!=element_list.eq(i).attr("user_id"))
              div.insertAfter(element_list.eq(i));
          }
        }
        div.find(".staff_name").html(data.name+' '+data.lastname).end()
        if ($("#staff_and_assignment_list").attr("user_id") == data.staff_id)
        {
          $("#staff_and_assignment_list").find(".tablehead-title").html(data.name+' '+data.lastname);
        }
        div.find(".staff_email").html(data.staff_email);
        //scroll to new location
        $('#staff_list_pane')[0].scrollTo(div.position().top);
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