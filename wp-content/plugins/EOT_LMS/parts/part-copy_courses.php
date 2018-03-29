<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
    <span class="current">Copy Course</span>     
</div>
<?php

  // Check if the org ID is valid and that the user is an uber/umbrella manager.
  if(current_user_can('is_uber_manager') || current_user_can('is_umbrella_manager'))
  {
    // Variable declaration
    global $current_user;
    $user_id = $current_user->ID; // Wordpress user ID
    $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
    $subscriptions = get_current_subscriptions ($org_id);
    foreach($subscriptions as $subscription)
    {
      $library_id = $subscription->library_id;
      if ($library_id == LE_ID)
      {
        // set the subscription_id and break the loop.
        $_REQUEST['subscription_id'] = $subscription->ID;
        break;
      }
    }
    $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
    $true_subscription = verifyUserAccess(); 
    // set the umbrella group ID. Uber manager's org id, or umbrella manager's umbrella_group_id
    $umbrella_group_id = $org_id;
    if (current_user_can('is_umbrella_manager'))
    {
      $umbrella_group_id = get_user_meta($user_id, 'umbrella_group_id', true);
    }

    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
      if(current_user_can( "is_director" ))
      {
        $admin_ajax_url = admin_url('admin-ajax.php');
?>
    <style type ="text/css">
      .jspHorizontalBar { display: none !important; }     
    </style> 
     <link rel="stylesheet" type="text/css" media="all" href="<?= get_template_directory_uri() . "/css/jquery.jscrollpane.css"?>" /> 
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.mousewheel.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.jscrollpane.min.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.rotate.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.tinymce.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/tinymce/tiny_mce.js'?>"></script> 
      <link href="<?= get_template_directory_uri() . "/css/fileuploader.css"?>" rel="stylesheet" type="text/css" />
      <script src="<?= get_template_directory_uri() . '/js/fileuploader.js'?>" type="text/javascript"></script>
      <!-- Start of left container-->
      <div style="float:left;" id="group_list" class="holder osX" org_id="<?= $org_id ?>"> 
        <div style="width:250px;" class="tableheadboxcontainer">
          <div class="tablehead-tr">
            <div class="tablehead-tl">
              <div style="padding:7px;margin-left:5px;height:20px">
                <h3 style="float:left;" class="tablehead-title"><?= __('Select A Course To Copy','EOT_LMS'); ?></h3><div style="clear:both;"></div>
              </div>
            </div>
          </div>
        </div>
         <div class="jScrollPaneContainer jScrollPaneScrollable" tabindex="0" style="height: 250px;">
          <div id="pane2" class="scroll-pane" style="overflow: hidden; height: 250px; width:250px; padding-right: 0px;">
            <div style="width:100%;">
              <?php 
                /**
                 * Display the name of all courses.
                 * If there's no course, it will display an error.
                 */
                $courses = getCourses(0, $org_id, $subscription_id); // get the courses from their org.
                // Check if there are any courses.
                if($courses)
                {

                  // Lists all the courses that can possibly be clone.
                  foreach($courses as $key => $course) 
                  {
                    $course_id = $course->ID; // Course ID
                    $course_name = $course->course_name; // Course Name
                ?>
                      <div class="group_list_table_row" group_id="<?= $course_id ?>" course-id="<?= $course_id ?>" org-id="<?= $org_id ?>">
                        <div class="group_name"><?= $course_name ?></div>
                        <p class="group_description" style="display:none;">
                          <span class="video_count"></span><?= __('Now select which camps to copy this course to:','EOT_LMS'); ?> 
                        </p>
                      </div>

              <?php
                  }
                }
                else if( isset($courses['status']) && $courses['status'] == 0 )
                {
                  /*
                   * Create an error message.
                   */
                  $error_message = (isset($courses['message'])) ? $courses['message'] : __("Could not find the fault.","EOT_LMS");
                  $error_message .= __(" Please contact the administrator.","EOT_LMS");
                  echo __("There is an error in getting the courses: ","EOT_LMS") . $error_message;
                }
                else if($courses == null)
                {
                  echo '<div style = "width:100%;text-align:center;padding-top:100px;font-size:140%;">'.__("You do not have any courses that can be copied yet.","EOT_LMS").' <br>'.__('Please contact our Customer Success team toll-free at','EOT_LMS').' <br>'.__('(877) 237-3931 M-F 9-5 EST').' <br>'.__('and we will assist you with copying a course.',"EOT_LMS").'</div>';

                }
              ?>                  
            </div>
          </div>
        </div>
        <div class="listing-footercontainer">
          <div class="listing-footer-bl">
            <div class="listing-footer-br">&nbsp;</div>
          </div>
        </div>
        <span>&nbsp;</span> 

      </div>  
      <!-- End of left container -->
      <!-- Right container -->
      <div class="bottom_buttons bottom_buttons_right">
        <a id="camp_option_button" class="btn" data-course-id="" style="position:absolute; bottom:200; display:none">
          Select camps to copy course into
        </a>
      </div>
     <!-- End of right container-->
     <div style="clear: both;"></div>
      <!-- CSS File goes here. -->
       <style type = "text/css">

        div.tablehead-tr {
          background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/target/reports-tr.gif);
          background-position:right top;
          background-repeat:no-repeat;
        }
        div.tablehead-tl {
          background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/target/reports-tl.gif);
          background-position:left top;
          background-repeat:no-repeat;
        }
        div.tableheadboxcontainer {
          background-color:#d7e9f3;
          border-bottom:1px solid white;
          margin:0px;
          /*width:250px;*/
          /*width:100%;*/
        }
        
        .tablehead-title {
          font-size:130%;
          font-weight:bold;
          margin:0;
          padding:0px;
          color:#50646D;
          background-color: #D7E9F3;
        } 

        /*GROUP LIST CSS*/
        p.group_description{
          font-weight:normal;
          font-size:12px;
          /*width:170px;*/
        }
        div.group_list_table_row 
        {
          border-bottom:1px solid white;
          background-color:#e8edff;
          font-weight:bold;
          padding-left:25px;
          margin-right:50px;
          padding-top:5px;
          padding-bottom:5px;
          background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/arrow2.png);
          background-position:5px 5px;
          background-repeat:no-repeat; 
          width:225px;
                   
          position:relative;
        } 
        div.group_list_table_row.active{
          background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/arrow2down.png);
          background-position:5px 5px;
          background-repeat:no-repeat;      
          position:relative;
          background-color:#b0bce7;
        }
        div.group_list_table_row:hover {
          background-color:#b0bce7;
          cursor:pointer
        } 

        div.group_list_delete_row{
          height:15px;
          width:15px;
          float:right;
          /*border:1px solid red;*/
          position:absolute;
          top:5px;
          /*left:215px;*/
       
        }
        div.group_list_edit_row{
          height:15px;
          width:15px;
          float:right;
          /*border:1px solid red;*/
          position:absolute;
          top:5px;
          /*left:215px;*/
        }    
        div.group_list_published_row{
          height:15px;
          width:15px;
          float:right;
          /*border:1px solid red;*/
          position:absolute;
          top:5px;
          /*left:215px;*/
        }    
        
        div.assignment_and_staff_list_delete_row{
          height:15px;
          width:15px;
          float:right;
          border:1px solid red;
          position:absolute;
          top:5px;
          /*left:215px;*/
       
        }
        div.staff_and_assignment_list_row 
        {
          border-bottom:1px solid white;
          background-color:#e8edff;
          font-weight:bold;
          padding-left:10px;
          padding-right:30px;
          margin-right:0px;
          padding-top:5px;
          padding-bottom:5px;
          /*background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/rightarrow.gif);*/
          background-position:5px 5px;
          background-repeat:no-repeat; 
          width:310px;
          position:relative;
        } 
        div.staff_and_assignment_list_row.active
        {
          background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/downarrow.gif);
          background-position:5px 5px;
          background-repeat:no-repeat;      
          position:relative;
          background-color:#b0bce7;
        }
        div.staff_and_assignment_list_row:hover 
        {
          background-color:#b0bce7;
          cursor:pointer
        }
        img.table_header_images
        {
          float:right;
          height:20px;
          margin-left:10px;
        cursor: pointer;
        }
        a.display_options
        {
          clear:both;
          float:right;
          cursor:pointer;
        }
    /*
        div.bottom_buttons
        {
          text-align:center;
          padding: 10px 0px 10px 0px;
        }
    */    
        div.listing-footercontainer
        {
          background-color:#d7e9f3;
          margin-bottom: 15px;
          border: solid 1px white;
        }
        
        div.listing-footer-br {
          background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/target/reports-br.gif);
          background-position:right bottom;
          background-repeat:no-repeat;
         }
        div.listing-footer-bl {
          background-image:url(https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/target/reports-bl.gif);
          background-position:left bottom;
          background-repeat:no-repeat;
        }
      #show_edit_videos_in_group,#show_add_to_group {
        cursor: pointer;
      }
      #file-uploader {
        margin-top: 10px;
      }
      .invite.data {
        font-size: 12px;
      }
      #facebox .invite.data td {
        padding: 3px;
      }
      #facebox #datepicker table.ui-datepicker-calendar td {
        padding: 0;
      }
      #pane2  {
        height: 250px;
      /*  height:250px;*/
      }
      #staff_and_assignment_list_pane
      {
        height:250px;
      }
      input.error
      {
        /*color: #FFFFF;*/
        background: #F39A85;
        border: 2px solid #F33131
      }
    </style> 


    <script type="text/javascript">
      TIME_PER_QUESTION = 30;

      Number.prototype.toHoursMinutes = function () {
        sec_numb    = parseInt(this);
        var hours   = Math.floor(sec_numb / 3600);
        var minutes = Math.floor((sec_numb - (hours * 3600)) / 60);
        var seconds = sec_numb - (hours * 3600) - (minutes * 60);
        if (seconds > 29) minutes++;
        if (hours == 0) return minutes+' min';
        else if (hours == 1) return '1 hour,<br>'+minutes+' min';
        else return hours+' hours,<br>'+minutes+' min';
      }
      function updateCertificateProgress()
      {
        $ = jQuery;
        /*var quiz_electives = $('input:checked[item="quiz"][requiredas="elective"]').length;*/
        var required = $('#certficate_box').attr('required');
        var required_core = $('#certficate_box').attr('required_core');
        var quiz_count = $('input:checked[item="quiz"][owner="eot"]').length
        var quiz_cores = $('input:checked[item="quiz"][requiredas="core"]').length;
        var core_text = '';
        var elec_text = '';
        var cert_title = $('#certficate_box').attr('title');
        var num_more_vids = 0;
        var num_more_elec_vids = 0;

        if(required_core > quiz_cores)
        {
          num_more_vids = required_core - quiz_cores;
          core_text = "<div style='margin:5px 0px;'><div id ='required_video_swatch'></div> Please Select "+num_more_vids+" more core video"+((num_more_vids>1)?"s":"")+" and quiz"+((num_more_vids>1)?"zes":"")+"</div>";
        }
        if(required - required_core > quiz_count - quiz_cores)
        {
          num_more_elec_vids = (required - required_core) - (quiz_count - quiz_cores);
          elec_text = "<div style='margin:5px 0px;'><div id ='required_video_swatch'></div> Please Select "+num_more_elec_vids+" more elective video"+((num_more_vids>1)?"s":"")+" and quiz"+((num_more_vids>1)?"zes":"")+"</div>";
        }    
        
        if(core_text||elec_text)
        {
          $('#certprogress').html("In order for the staff that are assigned to this group to achieve a <strong>"+cert_title+" Certificate</strong>, please do the following:"+core_text+elec_text);  
        }
        else
        {
          $('#certprogress').html("All Staff assigned to this group will be eligible to achieve a "+cert_title+" Certificate");
        }
        //$('.eotprogressbar').attr('percent', Math.floor(Math.random()*101));
        required_elecs = required - required_core;
        countable_elecs = ((quiz_count-quiz_cores)>required_elecs)?required_elecs:(quiz_count-quiz_cores)
        $('.eotprogressbar').attr('percent', ((((quiz_cores+countable_elecs)/required)*100)==0?0.001:(((quiz_cores+countable_elecs)/required)*100)));
        /*setTimeout(function(){reanimateProgressBars()},1);*/
        reanimateProgressBars()


      }
      
      jQuery(function($) {
        $(document).ready(function() {
          $('#pane2').jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});

          fix_icon_position(); // Fix display icons when one course is active then chosed another course to be active.
          $(document).bind('afterReveal.facebox', 
            function() {
              //$('eotprogressbar').eotprogressbar();
              $('.eotprogressbar').eotprogressbar(true);
              setTimeout(function(){animateProgressBars()},1);  
            }
          );
          $('.update_msg_textbox').live('click', function() {
              $("textarea[name='msg']").html($("textarea[name='msg']").attr('value'));
              invite_send_msg_text = $('textarea[name="msg"]').val();    
          });
          
          $('.update_email_textbox').live('click', function() {
              
          });
                
          $('#pane2').jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
          $('a[rel*=facebox]').facebox();
          
          // Do not initialize with "var" because we want these to be global variables
          prevObj = false;
          invite_staff = false;
          invite_send_email = false;
          invite_send_msg = false;
          invite_own = false;
          invite_send_msg_text = false;
          spreadsheet_users = false;
          var group_id, org_id, sub_id, group_name;
          group_name_global = false;
          loading_list = false;
          $(document).bind('close.facebox', 
          function(data,event)
          {
            invite_staff = false;
            invite_send_msg = false;
            invite_send_msg_text = false;
            invite_send_email = false;
            invite_own = false;
            spreadsheet_users = false;
          });       

                
          /******************************************************************************************
          * Binds a live function to the "Back" button on several facebox pages
          * Changes the view by checking if it exists
          *******************************************************************************************/  
          $('.back_fb').live('click', function() {
            var curr_view = $(this).attr('data-curr_view');
            var prev_view = $(this).attr('data-prev_view');
            if (curr_view == "invite_send_msg") {
              invite_send_msg_text = $('textarea[name="msg"]').val();
            } else {
              window[curr_view] = $('.content').html();
            }
            if (prev_view == "main") {
              redirect_to_main();
            } else {
              $('.content').fadeOut(300, function() {
                $('.content').html(window[prev_view]);
                $('.content').fadeIn(300);
              });
            }
          });
              
          // The back button which takes the user back to the send_email view
          $('#back_to_send_msg').live('click', function() {
            group_id = $("#staff_and_assignment_list").attr("group_id");
            org_id = $("#group_list").attr("org-id");
            sub_id = $("#group_list").attr("subscription-id");
            group_name = group_name_global;
            
            if (invite_send_email != false) {
              change_fb_view(invite_send_email);
            } else {
              var url =  'http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=display_form&format=ajax&form_name=use_invitation_email&org_id='+org_id+'&subscription_id='+sub_id+"&group_id="+group_id+"&group_name="+group_name;
              $.ajax({url:url,success:
                function(data)
                {
                  change_fb_view(data);
                }
              });
            }
            return false;
          });
          
          /******************************************************************************************
          * Binds a live function to the "Create Staff" button on the "Add/Remove Staff" facebox view
          *******************************************************************************************/    
          $('#create_staff_fb').live('click', function() {
            group_id = $("#staff_and_assignment_list").attr("group_id");
            org_id = $("#group_list").attr("org_id");
            sub_id = $("#group_list").attr("subscription_id");
            var url =  ajax_object.ajax_url + "?action=getCourseForm&form_name=create_staff_account&org_id="+org_id+"&group_id="+group_id+"&group_name="+group_name;
            prevObj = $('.content').html();
            $('.content').html('<img src="'+ajax_object.template_url+'/images/loading.gif">').html();
            $.ajax({url:url,
            success:
            function(data)
            {
              $('.content').fadeOut(300, function() {
                $(this).html(data).fadeIn();
                $('.negative').click(function() {
                  $('.content').fadeOut(300, function() {
                    $('.content').html(prevObj).fadeIn(300, function() {
                      $('#staff_listing_pane').css({'height':'350px'}).jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
                    });
                  });
                });
              });
            }
            });
            return false;
          });
          
          /******************************************************************************************
          * Binds a function to the success event when the email message is sent to a new staff user
          *******************************************************************************************/ 
          $(document).bind('success.send_message', function (event,data) {
            redirect_to_main();
          });
            
          /******************************************************************************************
          * Handles "Manage Courses" Button which lets the director manage which videos are in
          * the course as well as setting the due date for the assignment
          *******************************************************************************************/
          $('#camp_option_button').click(function() {
            if($("#staff_and_assignment_list").attr("group_id")!="null")
            {
              $("#display_videos_icon").click();
              group_id = $("#group_list").attr("group_id");
              org_id = $("#group_list").attr("org_id");
              sub_id = $("#group_list").attr("subscription_id");
              org_subdomain = $("#group_list").attr("org_subdomain");
              group_name = group_name_global;
              var ajax_url = ajax_object.ajax_url + "?action=getCourseForm&form_name=manage_camp_course&org_id="+org_id+"&course_id="+group_id+"&course_name="+group_name+"&subscription_id="+sub_id+"&course_name="+group_name;  
              jQuery.facebox(
                function()
                {
                  $.ajax({
                    url:ajax_url,
                    success:
                    function(data)
                    {
                      jQuery.facebox(data,'my-groovy-style');
                      //$.facebox(data);
                      //$('#video_listing_pane').css({'height':'550px'}).jScrollPane({contentWidth:'0px',showArrows:true, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
                      /* stops the facebox container to go wider.  */
                      //$('#video_listing_pane').css({'height':'550px'}).jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
                      $('li.video_item')
                      .find("[name*=chk_video_]")
                      .click(
                        function(event)
                        {
                        var obj = $(this)
                        /*********************************************
                        * Toggles class of the video title when checkbox is clicked 
                        *                          
                        *********************************************/                                                    
                        $(document).trigger('updated.assignment_list');
                        obj
                        .parent()
                        .find("span[name=video_title]")
                        .toggleClass(
                          function (index, clas)
                          {
                            return "disabled enabled";
                          })
                        /*********************************************
                        * Ajax to Add/Remove video quiz, and resources from course 
                        *                          
                        *********************************************/
                       /**********************************************************************************
                        * Getting value of the attributes from the courses div in part-manage_courses.php. 
                        * This varables will be used for sending post ajax                     
                        **********************************************************************************/
                        var course_id = obj.attr("course_id"); // the course id
                        var camp_id = obj.attr("camp_id"); // the camp id
                        var org_id_e = obj.attr("org_id"); // org_idsubdomain
                        var course_name = $('form#add_video_group').attr('course_name');// The course name.
                        obj.parent().find("i.fa-spinner").show('slow');
                        var publish_course_after_copy = $('input#chkbox_is_publish_course_after_copy').attr('checked'); // Checkbox for publishing a course after copy?

                        var info_data =
                        {
                            action: 'cloneCourse',
                            course_id: course_id,
                            camp_id: camp_id,
                            org_id: org_id_e,
                            course_name: course_name,
                            publish_course_after_copy: publish_course_after_copy,
                        }

                        $.ajax( {
                          type: "GET",
                          url: ajax_object.ajax_url,
                          data: info_data,
                          dataType: "json",
                          success: function(response)
                          {
                            obj.parent().find("i.fa-spinner").hide('slow');
                            obj.attr('disabled', 'disabled');
                            // Check if they have succesfully cloned the course.
                            if( response.success )
                            {
                              obj.parent().find(".img_check").show('slow');
                            }
                            // The Ajax request failed. Return an error message.
                            else
                            {
                              obj.parent().find(".img_delete").show('slow');
                              obj.parent().find("#clone_error_message").show('slow').text(response.message);
                            }
                          },
                          // If it fails on the other hand.
                          error: function(XMLHttpRequest, textStatus, errorThrown) 
                          {
                            alert( "POST Sent failed: " + textStatus );
                          }
                        });
                        /*********************************************
                        * Set refresh attribute to refresh list when
                        * facebox is closed                         
                        *********************************************/
                        $('#staff_and_assignment_list').attr("refresh",1);
              
                      })
                      .end()
                      .find('input[item = quiz][ type=checkbox]')
                      .click(
                        function()
                        {
                        var obj = $(this)
                        $.getJSON(''+ajax_object.ajax_url+'?action=toggleItemInAssignment&group_id='+obj.attr("group_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id")+'&org_id='+obj.attr("org_id")),
                          function(json)
                          {
                          if(json.action=="added")
                          {
                            obj.parent().removeClass("disabled")
                            obj.parent().addClass("enabled")
                            $(document).trigger('updateAssignmentSummary',[0,1,0]);
                          }
                          else
                          {
                            obj.parent().removeClass("enabled")
                            obj.parent().addClass("disabled")
                            $(document).trigger('updateAssignmentSummary',[0,-1,0]);
                          }
                          updateCertificateProgress(); 
                          }
                        }
                      ) 
                      .end()
                      .find('input[item = resource][ type=checkbox]')
                      .click(
                        function()
                        {
                        var obj = $(this)
                        
                        $.getJSON('http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=toggleItemInAssignment&format=ajax&assignment_id='+obj.attr("assignment_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id"),
                          function(json)
                          {
                          if(json.action=="added")
                          {
                            obj.parent().removeClass("disabled")
                            obj.parent().addClass("enabled")
                            $(document).trigger('updateAssignmentSummary',[0,0,1]);
                          }
                          else
                          {
                            obj.parent().removeClass("enabled")
                            obj.parent().addClass("disabled")
                            $(document).trigger('updateAssignmentSummary',[0,0,-1]);
                          }
                           
                          }
                        )
                        }
                      )
       
                      /*********************************************
                      * Handle Custom Quizzes and Resources
                      *                        
                      *********************************************/
                      $('#custom_quizzes_and_resources')
                      .find('input[item = quiz][ type=checkbox]')
                      .click(
                        function()
                        {
                        var obj = $(this)
                        
                        $.getJSON('http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=toggleItemInAssignment&format=ajax&assignment_id='+obj.attr("assignment_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id"),
                          function(json)
                          {
                          if(json.action=="added")
                          {
                            obj.parent().removeClass("disabled")
                            obj.parent().addClass("enabled")
                            $(document).trigger('updateAssignmentSummary',[0,1,0]);
                          }
                          else
                          {
                            obj.parent().removeClass("enabled")
                            obj.parent().addClass("disabled")
                            $(document).trigger('updateAssignmentSummary',[0,-1,0]);
                          }
                           
                          }
                        )
                        }
                      ) 
                      .end()
                      .find('input[item = resource][ type=checkbox]')
                      .click(
                        function()
                        {
                        var obj = $(this)
                        
                        $.getJSON('http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=toggleItemInAssignment&format=ajax&assignment_id='+obj.attr("assignment_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id"),
                          function(json)
                          {
                          if(json.action=="added")
                          {
                            obj.parent().removeClass("disabled")
                            obj.parent().addClass("enabled")
                            $(document).trigger('updateAssignmentSummary',[0,0,1]);
                          }
                          else
                          {
                            obj.parent().removeClass("enabled")
                            obj.parent().addClass("disabled")
                            $(document).trigger('updateAssignmentSummary',[0,0,-1]);
                          }
                           
                          }
                        )
                        }
                      );
                      $('#video_listing_pane').css({'height':'550px'}).jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
                    }
                  });
                 /******************************************************************
                 * $.facebox = function(data, klass,title,popup_footer, message)
                 * The title and message will pop up on ajax load via facebox.js. 
                 *******************************************************************/
                }, null, "Loading your modules", null, "Please wait while we process your modules...");
              // End of jQuery.facebox(
              }
          });
          
          /****************************************************
          * Handles the click events for each course group row
          *****************************************************/
          $('#pane2').delegate('.group_list_table_row', 'click', function() {
            var course_id = jQuery(this).attr( 'course-id' );
            $("#group_list").attr("group_id", course_id);
            if(group_name_global==$(this).find(".group_name").html())
            {
            $("a#camp_option_button").hide("slow");
            group_name_global = "&lt;NO COURSE SELECTED&gt;";
            $("#staff_and_assignment_list").find(".tablehead-title").fadeTo('fast',0.01,function ()
              {
                $("a#camp_option_button").hide("slow");
                $(this).html("&lt;NO COURSE SELECTED&gt;").fadeTo('fast',1)
              });
            $("#staff_and_assignment_list").find(".scroll-pane-wrapper").html('\
              <div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "height:250px; width: 350px">\
                <div style = "width:100%;">\
                  <div style = "width:100%;text-align:center;padding-top:100px;font-size:140%;">\
                    Select a group...\
                  </div>\
                </div>\
              </div>\
            ');
            $("#staff_and_assignment_list").attr("group_id","null");
            } 
            else 
            {
              $("a#camp_option_button").show("slow");
              $("a#camp_option_button").css('display','inline-block');
              $("a#camp_option_button").attr("data-course-id",$(this).attr( 'course-id' ));
              group_name_global = $(this).find(".group_name").html();
              var shortTitle = group_name_global;
              if (shortTitle.length > 30) 
              {
                shortTitle = jQuery.trim(shortTitle).substring(0, 27).split(" ").slice(0, -1).join(" ") + "...";
              }
            }
            $(this).children("p").slideToggle("fast",
            function(){
              $('#pane2').jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
              //$('#pane2')[0].scrollTo($(this).parent().position().top);
              fix_icon_position();
            }
            );
            $(this).toggleClass("active");
            $(this).siblings().children("p:visible")
            .slideUp("fast",
            function()
            {
              //$('#pane2').jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
              fix_icon_position(); // Fix display icons when one course is active then chosed another course to be active.
            }
            )
            $(this).siblings().children("p:visible").parent().toggleClass("active");
          });
          
          /****************************************************
          * This function is called upon successfull 
          * creation of a new staff group
          *****************************************************/
          $(document).bind('success.create_staff_group',
            function(event,data)
            {
              var element_list = $('#group_list .group_list_table_row');
              var found = false;
              var i=0;
              var div = '<div class = \"group_list_table_row\" group_id = '+data.group_id+' course-id= '+data.group_id+' org-id= '+data.org_id+' subscription-id='+data.subscription_id+' > \
                    <div class=\"group_name\">'+data.group_name+'</div> \
                    <p class = \"group_description\" style=\"display:none;\"><i>'+data.group_desc+'</i></p> \
                    <p class = \"group_description\" style=\"display:none;\"> \
                      <span class="staff_count"> 0 </span> Staff Members<br> \
                      <span class="video_count"> 0 </span> Videos Assigned \
                    </p> \
                    <div class=\"group_list_edit_row\" style=\"left: 215px;\"> \
                      <a href=\"<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=edit_course_group&amp;course_name='+data.group_name+'&amp;org_id='+data.org_id+'&amp;course_id='+data.group_id+'\" class=\"dasdfdasfelete_group\" rel=\"facebox\"> \
                        <i class=\"fa fa-pencil tooltip\" onmouseover=\"Tip(\'Edit course name.\', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')\" onmouseout=\"UnTip()\" aria-hidden=\"true\"></i> \
                      </a> \
                    </div> \
                    <div class=\"group_list_delete_row\" style=\"left: 230px;\"> \
                      <a href=\"<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=delete_course&amp;course_id='+data.group_id+'&amp;course_name='+data.group_name+'&amp;org_id='+data.org_id+'&amp;subscription_id='+data.subscription_id+'\" class=\"dasdfdasfelete_group\" rel=\"facebox\"> \
                        <i class=\"fa fa-trash-o tooltip\" aria-hidden=\"true\" style=\"margin-bottom:2px; color:black\" onmouseover=\"Tip(\'Delete this course.\', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')\" onmouseout=\"UnTip()\"></i> \
                      </a> \
                    </div> \
                    </div>';
              var index = 0;
              for (i=0;i<element_list.length&&!found;i++)
              {
                if ((data.group_name).toLowerCase()<=(element_list.eq(i).children('.group_name').html()).toLowerCase()) {  
                $(div).insertBefore(element_list.eq(i));
                found = true;
                index = i;
                }
                
                if (!found&&i==element_list.length-1) {
                $(div).insertAfter(element_list.eq(i));
                index = i+1;
                }
              }
              if (element_list.length==0) {
                $('#pane2:nth-child(1)').html(div);
              }
              $('#pane2').jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
              fix_icon_position();
              jQuery(document).trigger('close.facebox');
              $('#group_list .group_list_table_row').eq(index).click();
              $('div.group_list_table_row[group_id = '+data.group_id+']').find('a[rel*=facebox]').facebox();
          }); 
        
          /****************************************************
          * This function is called when a successfull edit
          * to a staff group is made
          *****************************************************/
          $(document).bind('success.edit_staff_group', function(event,data) {
            var element_list = $('#group_list .group_list_table_row');            
            var found = false;
            var i=0;
            var div = $('div.group_list_table_row[group_id = '+data.group_id+']');
            var index = 0;
            for(i=0;i<element_list.length&&!found;i++)
            {
              if (!found&&((data.group_name).toLowerCase()<=(element_list.eq(i).children('.group_name').html()).toLowerCase()))
              {  
              found = true;
              index = i;
              if(div.attr("group_id")!=element_list.eq(i).attr("group_id"))
                div.insertBefore(element_list.eq(i));
              }
              
              if(!found&&i==element_list.length-1)
              {
              index = i;
              if(div.attr("group_id")!=element_list.eq(i).attr("group_id"))
                div.insertAfter(element_list.eq(i));
              }
            }
            div
              .find("div.group_name").html(data.group_name).end()
              .find("p.group_description:first").html('<i>'+data.group_desc+'</i>').end();
            
            fix_icon_position();        
            jQuery(document).trigger('close.facebox');
            if ($("#staff_and_assignment_list").attr("group_id") == data.group_id)
            {
              group_name_global = data.group_name;
                      var shortTitle = group_name_global;
                      if (shortTitle.length > 30) {
                        shortTitle = jQuery.trim(shortTitle).substring(0, 27).split(" ").slice(0, -1).join(" ") + "...";
                      }
                      $("#staff_and_assignment_list").find(".tablehead-title").html(shortTitle);
            }
            $('#pane2')[0].scrollTo(div.position().top);
          });
          
          /****************************************************
          * This function is called when a successfull staff
          * group deletion is made
          *****************************************************/
          $(document).bind('success.delete_staff_group', function(event,data) {
            var div = $('div.group_list_table_row[group_id = '+data.group_id+']');
            var element_list_count = $('#group_list .group_list_table_row:visible').length;
            jQuery(document).trigger('close.facebox');
            if(group_name_global==div.find(".group_name").html())
            {             
              $("#staff_and_assignment_list").find(".tablehead-title").fadeTo('fast',0.01,function (){$(this).html("&lt;NO GROUP SELECTED&gt;").fadeTo('fast',1)});
              $("#staff_and_assignment_list").find(".scroll-pane-wrapper").html('\
                <div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "height:250px; width: 350px">\
                 <div style = "width:100%;">\
                <div style = "width:100%;text-align:center;padding-top:100px;font-size:140%;">\
                  Select a group...\
                </div>\
                </div>\
               </div>\
               ');
              $("#staff_and_assignment_list").attr("group_id","null");
            } 
            div.hide("slow",
              function()
              {           
              $('#pane2').jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
              fix_icon_position();
              }
            );
            
            if(element_list_count == 1)
            {
              div.parent().html('<div style = "width:100%;text-align:center;padding-top:100px;font-size:140%;">Create a course...</div>');
            }
          });
          
          /*******************************************************************
          * handles toogle of header icons as well as text below the box
          ********************************************************************/                
          $(".table_header_images").click(
            function (){
            if($("#staff_and_assignment_list").attr("display")!=$(this).attr("name")&&$("#staff_and_assignment_list").attr("group_id")!="null")
            {
              if($(this).attr("name") == "staff_list")
              {
              $("#display_videos_icon").attr("src", ajax_object.template_url + "/images/list_icon.png");
              $("#display_staff_icon").attr("src", ajax_object.template_url + "/images/user_icon_clicked.png");
              $(".display_options").html("Show videos in group..."); 
              }
              else
              {
              $("#display_videos_icon").attr("src", ajax_object.template_url + "/images/list_icon_clicked.png");
              $("#display_staff_icon").attr("src", ajax_object.template_url + "/images/user_icon.png");
              $(".display_options").html("Show staff in group..."); 
              }
              $("#staff_and_assignment_list").attr("display",$(this).attr("name"));
              load_list();
            }
            
            }
          );
          
          $("#display_options").click(
            function ()
            {
              if($("#staff_and_assignment_list").attr("group_id")!="null")
              {
              if($("#staff_and_assignment_list").attr("display") != "staff_list")
              
              {
                $("#display_videos_icon").attr("src", ajax_object.template_url + "/images/list_icon.png");
                $("#display_staff_icon").attr("src", ajax_object.template_url + "/images/user_icon_clicked.png");
                $(".display_options").html("Show videos in group...");
                $("#staff_and_assignment_list").attr("display","staff_list"); 
              }
              else
              {
                $("#display_videos_icon").attr("src", ajax_object.template_url + "/images/list_icon_clicked.png");
                $("#display_staff_icon").attr("src", ajax_object.template_url + "/images/user_icon.png");
                $(".display_options").html("Show staff in group..."); 
                $("#staff_and_assignment_list").attr("display","video_list");
              }
              load_list();
              }
            }
          );
        });
        /****************************************************
        * This function redirects(changes) the facebox
        * content to go back to the add staff to group view
        * It does NOT update from the database so there is 
        * no query.
        *****************************************************/
        function change_fb_view(data) {
          $('.content').fadeOut(300, function() {
            $('.content').html(data).fadeIn(300, function() {
              
            });
          });
        }
        
        /****************************************************
        * This function redirects(changes) the facebox
        * content to go back to the add staff to group view
        *****************************************************/
        function redirect_to_main() {
          var group_id = $("#staff_and_assignment_list").attr("group_id");
          var org_id = $("#group_list").attr("org_id");
          var sub_id = $("#group_list").attr("subscription_id");
          var group_name = group_name_global;
          var url =  ajax_object.ajax_url + "?action=getCourseForm&form_name=add_staff_to_group&org_id="+org_id+"&group_id="+group_id+"&group_name="+group_name+"&org_subdomain="+org_subdomain;
          $.ajax({url:url,success:
            function(data)
            {
              $('.content').fadeOut(300, function() {
                $(this).html(data).fadeIn();
                //resize scrollbar
                $('#staff_listing_pane').css({'height':'350px'}).jScrollPane({});
              });
            }
          });
        }
        
        /****************************************************
        * This function loads the video list
        *****************************************************/
        function load_list()
        {
          var action = "";
          var loading_message = "";
          if($("#staff_and_assignment_list").attr("display")=="staff_list")
          {
            action = "getUsersInCourse";
            loading_message = "Loading your staff accounts";
          }else
          {
            action = "getModules"
            loading_message = "Loading your modules";
          }

          $("#staff_and_assignment_list").find(".scroll-pane-wrapper").html('\
            <div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "height:250px; width: 350px">\
              <div style = "width:100%;">\
                <div style = "width:100%;text-align:center;padding-top:70px;font-size:140%;">\
                    '+loading_message+' <br /> <br /> \
                  <img src="https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/facebox/loading.gif"/>\
                </div>\
              </div>\
            </div>\
          ');

          /**********************************************************************************
           * Getting value of the attributes from the courses div in part-manage_courses.php. 
           * This varables will be used for sending post ajax                     
           **********************************************************************************/
          var link = this;
          var id = $("#staff_and_assignment_list").attr( 'group_id' );      // course ID
          var subdomain = $('.group_list_table_row').attr( 'portal-subdomain' );  // subdomain
          var org_id_e = $('.group_list_table_row').attr( 'org-id' );             // org_id
          var subscription_id = $('.group_list_table_row').attr( 'subscription-id' ); // Subscription ID

          var info_data = 
          {
            action: action,
            course_id: id,
            org_id: org_id_e,
            subscription_id: subscription_id,
          }
          // Send POST to ajax admin_url 
          $.ajax( {
            type: "POST",
            url: ajax_object.ajax_url,
            data: info_data,

            // If we are successful
            success: function(data)
            {
              // Sending post is succesful. However, there is something wrong with sending info to admin-ajax.
              if( data == 0 )
              {
                $("#staff_and_assignment_list").find(".scroll-pane-wrapper").fadeTo('fast',0.1, function()
                   {
                      // For now, redirect to error page. 
                      window.location.replace("?part=error");
                   })
              }
              else
              {
                // Get the message / response. (From here, we are expecting to have a pre-made html divs.)
                var obj = jQuery.parseJSON(data);
                $("#staff_and_assignment_list").find(".scroll-pane-wrapper").fadeTo('fast',0.1, function()
                     {
                        // Inject the defined HTML to this element.
                        $(this).html(obj.message);
                        $('#staff_and_assignment_list_pane')
                        .css({'height':'250px'})
                        .jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
                })

                .fadeTo("slow",1);

                if($('#staff_and_assignment_list').attr("refresh")==1)
                {
                  if(action == "getUsersInCourse")
                    $('.group_list_table_row.active').find("span.staff_count").html(data.staff_count);
                  else
                    $('.group_list_table_row.active').find("span.video_count").html(data.video_count);
                  $('#staff_and_assignment_list').attr("refresh",0);
                }
              }
            },
            // If it fails on the other hand.
            error: function(XMLHttpRequest, textStatus, errorThrown) 
            {
               alert( "POST Sent failed: " + textStatus );
            }
          });
        }
        
        /****************************************************
        * This function does the ajax call and loads the upload
        * spreadsheet view. It also initializes the file upload
        * plugin. This is in a separate function so that it is
        * not written twice.
        *****************************************************/  
        function load_spreadsheet() { 
            org_id = $("#group_list").attr("org_id");
            sub_id = $("#group_list").attr("subscription_id");
            
            var url =  'http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=display_form&format=ajax&form_name=upload_spreadsheet&org_id='+org_id+'&subscription_id='+sub_id;
            $.ajax({url:url,success:
              function(data)
              {
                $('.content').fadeOut(300, function() {
                  $('.content').html(data).fadeIn(300, function() {
                    var uploader = new qq.FileUploader({
                      element: $('#file-uploader')[0],
                      action: 'file_uploader/handleupload.php',
                                        params: { org_id: org_id },
                      multiple: false,
                      onComplete: function(id, filename, responseJSON)
                      {
                        $("input[name='filename']").val("file_uploader/uploads/" + responseJSON.new_filename);
                        var url =  'http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=upload_spreadsheet&format=ajax';
                        $.ajax({url:url,data: $('#upload_form').serialize(),dataType: 'json',type: 'POST', success:
                          function(udata)
                          {
                            if (udata.success) {
                                                        spreadsheet_users = udata.emails;
                              var url =  'http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=display_form&format=ajax&form_name=spreadsheet_send_message&subscription_id='+sub_id;
                              $.ajax({url:url,success:
                                function(data)
                                {
                                  $('.content').fadeOut(300, function() {
                                    $('.content').html(data);
                                    $('.content').fadeIn(300, function() {
                                      $('textarea.tinymce').tinymce({
                                        script_url : 'libraries/tinymce/jscripts/tiny_mce/tiny_mce.js',
                                        mode : "textareas",
                                        theme : "advanced",
                                        entity_encoding : "named", 
                                        entities : "&nbsp;",
                                        theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justrifycenter,justifyright,justifyfull,|,bullist,numlist,|,code",
                                        theme_advanced_buttons2 : "",
                                        theme_advanced_buttons3 : "",
                                        theme_advanced_toolbar_location : "top",
                                        theme_advanced_toolbar_align : "left",
                                        theme_advanced_resizing : true  
                                      });
                                    });
                                  });
                                }
                              });
                            } else {
                              $('.content').fadeOut(300, function() {
                                $('.content').html(udata.data);
                                $('.content').fadeIn(300);
                              });
                            }
                            
                          }
                        });
                      }
                    });
                  });
                });
              }
            });
          }
        
        /****************************************************
        * This function displays a delete confirmation box
        * if the user selects yes then the box staff group
        * is deleted and the Staff group list is update        
        *****************************************************/          
        function delete_group_confirmation(clicked_ele) 
        {
           var answer = confirm('Delete Group? \n\nUser accounts linked to the group will NOT be deleted, but will no longer belong to this Group.');
        
           if (answer){
            var url = $(clicked_ele).attr("href");
            $(clicked_ele).parent().parent().slideUp("slow",function(){$('#pane2').jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16});})
           }
        }
        
        /****************************************************
        * This function repositions the x and pencil icons
        *****************************************************/    
        function fix_icon_position()
        {
          if ($('#pane2').parent().hasClass('jScrollPaneScrollable')) {
            pencil = 200;
            x = 215;
            status = 182;
          } else {
            pencil = 215;
            x = 230;
            status = 198;
          }
          $('div.group_list_published_row').css({'left':status+'px'});
          $('div.group_list_delete_row').css({'left':x+'px'});
          $('div.group_list_edit_row').css({'left':pencil+'px'});
        }
      });
    </script>
<?php
      }
      else
      {
        echo __("Unauthorized!","EOT_LMS");
      }
    }
    else
    {
      echo __("subscription ID does not belong to you","EOT_LMS");
    }
  }
  // Could not find the subscription ID
  else
  {
    echo __("Could not find the subscription ID", "EOT_LMS");
  }
?>
