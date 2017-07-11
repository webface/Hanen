<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <?= CRUMB_ADMINISTRATOR ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current">Manage Courses</span>     
</div>
<?php
  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess(); 

  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
  {
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID

    // Variable declaration
    global $current_user;
    $user_id = $current_user->ID;                  // Wordpress user ID
    $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID

    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
      if(current_user_can( "is_director" ))
      {
        $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
        $data = compact ("org_id", "org_subdomain", "user_id");
        $admin_ajax_url = admin_url('admin-ajax.php');

        // Check if the subscription belongs to this user.
        $subscription = getSubscriptions($subscription_id,0,1); // get the subscription row
        if (isset($subscription))
        {
          if($org_id != $subscription->org_id)
          {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            return;
          }
        }
        else
        {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            return;
          }

?>
      <script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.dataTables.js'?>"></script>
      <script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.eotprogressbar.js'?>"></script>
      <script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.eotdatatables.js'?>"></script>
      <!-- Start of left container-->
      <div style="float:left;" id="group_list" class="holder osX" org_id="<?= $org_id ?>" subscription_id="<?= $subscription_id ?>" org_subdomain="<?= $org_subdomain ?>"> 
        <div style="width:250px;" class="tableheadboxcontainer">
          <div class="tablehead-tr">
            <div class="tablehead-tl">
              <div style="padding:7px;margin-left:5px;height:20px">
                <h3 style="float:left;" class="tablehead-title"> Manage Courses</h3><div style="clear:both;"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="jScrollPaneContainer jScrollPaneScrollable" tabindex="0" style="height: 250px;width:250px;">
          <div id="pane2" class="scroll-pane" style="overflow: hidden; height: 250px; padding-right: 0px;">
            <div style="width:100%;">
              <?php 
                /**
                 * Display the name of all courses.
                 * Display how many modules/videos are there in a course
                 * If there's no course, it will display an error.
                 */
                global $wpdb;
                $courses = getCoursesById($org_id,$subscription_id);
        
                // If status is not set, assuming there is no error.
                if($courses)
                {
                  foreach($courses as $key => $course) 
                  {
                    $course_id = $course['ID'];   // Course ID
                    $course_name = $course['course_name']; // Course Name                  
                    $request_uri = ""; // edit url is not displaying properly because it's looking for this undefine variable. Will use dummy data for now.
                    //$published_status = $course['published_status_id'];
                    // Do not display the cloned leadership essential
                    if($course_name == LE_LIBRARY_TITLE)
                    {
                      continue;
                    }
                ?>
                      <div class="group_list_table_row" group_id="<?= $course_id ?>" course-id="<?= $course_id ?>" portal-subdomain="<?= $org_subdomain ?>" org-id="<?= $org_id ?>" subscription-id="<?= $subscription_id?>">
                        <div class="group_name"><?= $course_name ?></div>
                          <p class="group_description" style="display:none;">
                            <span class="staff_count"></span>
                            <br>
                            <span class="video_count"></span>
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="loading_course_subscription_info" style="display:none"></i>
                          </p>
                          <div class="group_list_edit_row" style="left: 215px;">
                          <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=edit_course_group&amp;course_name=<?= $course_name ?>&amp;org_id=<?= $org_id ?>&amp;course_id=<?= $course_id ?>&amp;portal_subdomain=<?= $org_subdomain ?>" class="dasdfdasfelete_group" rel="facebox">
                              <i class="fa fa-pencil tooltip" onmouseover="Tip('Edit course name.', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()" aria-hidden="true"></i>
                           </a>
                          </div>
                          <div class="group_list_delete_row" style="left: 230px;">
                            <a href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_course&course_id=<?= $course_id ?>&course_name=<?= $course_name ?>&portal_subdomain=<?= $org_subdomain ?>&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>" class="dasdfdasfelete_group" rel="facebox">
                              <i class="fa fa-trash-o tooltip" aria-hidden="true"  title="Delete this course." style="margin-bottom:2px; color:black" onmouseover="Tip('Delete this course.', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                            </a>   
                          </div>
                        </div>
              <?php
                  }
                } 
                else if($courses == null)
                {
                  echo '<div style = "width:100%;text-align:center;padding-top:100px;font-size:140%;">Create a course...</div>';
                }
                else
                {
                  /*
                   * Create an error message.
                   */
                  $error_message = (isset($courses['message'])) ? $courses['message'] : "Could not find the fault.";
                  $error_message .= " Please contact the administrator.";
                  echo "There is an error in getting the courses: " . $error_message;
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
        <div class="bottom_buttons">
          <a class="btn" style="" href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=create_course_group&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id?>" rel="facebox">
            Create Course
          </a>
        </div>
      </div>  
      <!-- End of left container -->
      <!-- Right container -->
      <div  style="float:left; margin-left:25px;width:350px;" id="staff_and_assignment_list" refresh=0 display="video_list" group_id="null" course-status="" class="holder osX"> 
        <div style =  "width:350px;" class = "tableheadboxcontainer">
          <div class = "tablehead-tr">
            <div class = "tablehead-tl">
              <div style = "padding:7px;margin-left:5px;height:20px">
                <H3 style="float:left;"  class = "tablehead-title"> &lt;NO COURSE SELECTED&gt;</H3>
                <img src='<?= get_template_directory_uri() . '/images/list_icon_clicked.png'?>' title='Show videos that have been assigned to this group.' name = "video_list" id = "display_videos_icon" class="table_header_images" style='margin-bottom:2px;' onmouseover="Tip('Show videos that have been assigned to this group.', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()" />
                <img src='<?= get_template_directory_uri() . '/images/user_icon.png'?>' title='Show staff that are in this group.' name = "staff_list" id = "display_staff_icon" class="table_header_images" style='margin-bottom:2px;' onmouseover="Tip('Show staff that are in this group.', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()" />
                <div style="clear:both;" ></div>
              </div>
            </div>
          </div>
        </div>
        <div class="scroll-pane-wrapper">    
          <div  id="staff_and_assignment_list_pane" class="scroll-pane" style="height:250px; width: 350px">
            <div style="width:100%;">
              <div style="width:100%;text-align:center;padding-top:100px;font-size:140%;">
                  Select a course...
              </div>
            </div>
          </div>
        </div>
        <div class="listing-footercontainer">
          <div class="listing-footer-bl">
            <div class="listing-footer-br">&nbsp;</div>
          </div>
        </div>
        <a id="display_options" class="display_options">Show staff in group...</a>
        <div style="clear:both;"></div> 
        <div class="bottom_buttons bottom_buttons_right">
          <a id="show_edit_videos_in_group" class="btn">
            Manage Modules
          </a>
          <a id="show_add_to_group" class="btn">
            Add/Remove Staff
          </a>
        </div>
      </div>
      <br />
      <br />
     <!-- End of right container-->
     <div style="clear: both;"></div>
     <div class="dashboard_border" style="padding: 0; padding-left: 23px; width: 91%;">
        <h1 class="article_page_title">
          <legend>Legend</legend>
        </h1>
       <fieldset style="margin-top: -20px">
<!--          <p>
            <i class="fa fa-eye draft tooltip"></i> The course is in <b>draft</b> mode. You can still make changes.
          </p>
          <p>
            <i class="fa fa-eye published tooltip"></i> The course is <b>published</b>. You can no longer make changes.
          </p>-->
          <p>
            <i class="fa fa-pencil tooltip"></i> <b>Edit</b> the course name. Works for published and unpublished courses.
          </p>
          <p>
            <i class="fa fa-trash tooltip"></i> <b>Delete</b> a course permanently. This action cannot be undone.
          </p>
          <p>
            <img src="<?= get_template_directory_uri() . '/images/user_icon.png'?>" /> Click to see the list of <b>staff</b> who are in a particular course.
          </p>
          <p>
            <img src="<?= get_template_directory_uri() . '/images/list_icon.png'?>" /> Click to see the list of <b>modules</b> in a particular course.
          </p>
       </fieldset>
     </div>
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
       </style>
 
      <link rel="stylesheet" type="text/css" media="all" href="<?= get_template_directory_uri() . "/css/jquery.jscrollpane.css"?>" /> 
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.mousewheel.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.jscrollpane.min.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.rotate.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.tinymce.js'?>"></script>
      <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/tinymce/tiny_mce.js'?>"></script> 
      <link href="<?= get_template_directory_uri() . "/css/fileuploader.css"?>" rel="stylesheet" type="text/css" />
      <script src="<?= get_template_directory_uri() . '/js/fileuploader.js'?>" type="text/javascript"></script>

    <style type ="text/css">
      #pane2  {
        height: 250px;
        max-width: 250px;
        overflow: auto;
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
      .jspHorizontalBar { display: none !important; }
      
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
      function updateTimeToComplete() {
        $ = jQuery;
        
        var video_time_count = 0;
        var video_checked = $('input:checked[name*="chk_video_"]');
        // Count video time.
        $.each(video_checked, function(key, value) 
        {
          video_time_count += parseInt($(this).attr("video_length"));
        });
        var quiz_count = $('input:checked[item="quiz"]').length
        var resource_count = $('input:checked[item="resource"]').length
        var video_count = $('input:checked[name*="chk_video_"]').length
        var avg_video_length = <?= AVG_VIDEO_LENGTH ?>;
        var avg_exam_length = <?= AVG_EXAM_LENGTH ?>;
        
        $('#videoCount').html(video_count);
        $('#quizCount').html(quiz_count);
        $('#resourceCount').html(resource_count);
        total_time = ((video_time_count) + (quiz_count * avg_exam_length)) * 60; // Total time in seconds: Exams will be 10 minutes each while watching 1 video module will be 5 minutes.
        
        $('#timeToComplete').html(total_time.toHoursMinutes());
        //updateCertificateProgress();
      }
      
      jQuery(function($) {
        $(document).ready(function() {
          fix_icon_position(); // Fix display icons when one course is active then chosed another course to be active.
          $(document).bind('afterReveal.facebox', 
            function() {
              //$('eotprogressbar').eotprogressbar();
              $('.eotprogressbar').eotprogressbar(true);
              setTimeout(function(){animateProgressBars()},1);  
            }
          );
                
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
//          $('input[item="quiz"]').live('click',function(){
//              console.log($(this).attr('item_id'));
//          })  
          /******************************************************************************************
          * Binds a live function to the "Create Staff" button on the "Add/Remove Staff" facebox view
          *******************************************************************************************/    
          //$('#create_staff_fb').live('click', function() {
          $(document).on('click','#create_staff_fb',function(){
            group_id = $("#staff_and_assignment_list").attr("group_id");
            org_id = $("#group_list").attr("org_id");
            sub_id = $("#group_list").attr("subscription_id");
            portal_subdomain = $("#group_list").attr("org_subdomain");
            var url =  ajax_object.ajax_url + "?action=getCourseForm&form_name=create_staff_account&org_id="+org_id+"&group_id="+group_id+"&group_name="+group_name+"&portal_subdomain="+portal_subdomain+'&subscription_id='+sub_id;
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
          * Binds a function to the success event of the create_staff_account form
          *******************************************************************************************/               
          $(document).bind('success.create_staff_account', function (event,data) {
          // Msg_send is true when the user thick the "yes, send an e -mail with their login password." when creating a new account.
          if(data.msg_sent)
            {
            jQuery.facebox({ajax: ajax_object.ajax_url + '?action=getCourseForm&form_name=send_message&target=create_account&email='+data.email+'&password='+data.password+'&name='+data.name+'&org_id='+data.org_id+'&last_name='+data.last_name}); 
            }
            else
            {
              jQuery(document).trigger('close.facebox');
            }
          });
          
          /******************************************************************************************
          * Binds a function to the success event when the email message is sent to a new staff user
          *******************************************************************************************/ 
          $(document).bind('success.send_message', function (event,data) {
            redirect_to_main();
          });
          
          /***************************************************************
          * The click event handler for the "Add/Remove Staff" button
          ****************************************************************/
          //$('#show_add_to_group').click(function () {
          $(document).on('click','#show_add_to_group',function(){
            if($("#staff_and_assignment_list").attr("group_id")!="null")
            {
              //this binds the scrollpane reinit to the facebox reveal. Should handle the issue of the scroll bar not showing up.
              var scrollbar_handler = function() {
               $('#staff_listing_pane').css({'height':'350px'}).jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5}); 
              };
              $(document).bind('reveal.facebox', scrollbar_handler);
              
              $("#display_staff_icon").click();
              group_id = $("#staff_and_assignment_list").attr("group_id");
              org_id = $("#group_list").attr("org_id");
              sub_id = $("#group_list").attr("subscription_id");
              org_subdomain = $("#group_list").attr("org_subdomain");
              subscription_id = $("#group_list").attr("subscription_id");
              group_name = group_name_global;
              enrollment_id = 0;
              var url =  ajax_object.ajax_url + "?action=getCourseForm&form_name=add_staff_to_group&org_id="+org_id+"&subscription_id="+subscription_id+"&group_id="+group_id+"&group_name="+encodeURIComponent(group_name)+"&org_subdomain="+org_subdomain;
              jQuery.facebox(
              function(){
                $.ajax({url:url,success: function(data) {
                $.facebox(data);
                setTimeout(function (){$('#staff_listing_pane').jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});},2000);
                $('a[rel=refresh]').click();
                $(document).unbind('reveal.facebox', scrollbar_handler);
                }
                })
                 /******************************************************************
                 * $.facebox = function(data, klass,title,popup_footer, message)
                 * The title and message will pop up on ajax load via facebox.js. 
                 *******************************************************************/
              }, null, "Loading your staff accounts", null, "Please wait while we process your staff accounts...");
            }
            });
            // Manage the add and remove button action.
            //$('.add_remove_btn').live('click', function () {
            $(document).on('click','.add_remove_btn',function(){
              var task = "";
              if ($(this).attr("status")=="add")
              {
              task = "enrollUserInCourse";
              }
              else if($(this).attr("status")=="remove")
              {
              task = "deleteEnrolledUser";
              enrollment_id = $(this).attr("enrollment_id");
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

              $.getJSON( ajax_object.ajax_url + '?action='+task+'&group_id='+group_id+'&email='+encodeURIComponent($(this).attr("email"))+'&org_id='+$(this).attr("org_id")+'&subscription_id='+$(this).attr("subscription_id")+'&user_id='+$(this).attr("user_id")+'&course_name='+encodeURIComponent($(this).attr("course_name"))+'&portal_subdomain='+$(this).attr("portal_subdomain")+'&nonce='+$(this).attr("nonce")+'&enrollment_id='+enrollment_id+'&course_id='+group_id,
                function (json)
                {
                if(json.success)
                {
                  if(task == "enrollUserInCourse")
                  {
                    temp.text( "Remove from course" );
                    temp.attr( "status" , "remove" );
                    temp.attr( "selected" , 1 );
                    temp.attr( "enrollment_id", json.enrollment_id);
                    loading_img.replaceWith(temp);
                    btn.parent().parent().css("background-color","#d7f3ca");
                  }
                  else
                  {
                    temp.text( "Add to course" );
                    temp.attr( "status" , "add" );
                    temp.attr( "selected" , 0 );
                    loading_img.replaceWith(temp); // CHHANGE STATUS MAYBE
                    btn.parent().parent().css("background-color","");
                  }                                
                  $('#staff_and_assignment_list').attr("refresh",1);
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
          * Handles "Manage Courses" Button which lets the director manage which videos are in
          * the course as well as setting the due date for the assignment
          *******************************************************************************************/
          //$('#show_edit_videos_in_group').click(function() { //beginning of statement
          $(document).on('click','#show_edit_videos_in_group',function(){
            if($("#staff_and_assignment_list").attr("group_id")!="null")
            {
              $("#display_videos_icon").click();
              group_id = $("#staff_and_assignment_list").attr("group_id");
              org_id = $("#group_list").attr("org_id");
              sub_id = $("#group_list").attr("subscription_id");
              org_subdomain = $("#group_list").attr("org_subdomain");
              group_name = group_name_global;
              var ajax_url = ajax_object.ajax_url + "?action=getCourseForm&form_name=add_video_group&org_id="+org_id+"&course_id="+group_id+"&course_name="+encodeURIComponent(group_name)+"&portal_subdomain="+org_subdomain+"&subscription_id="+sub_id;  
              jQuery.facebox(
                function()
                {
                  $.ajax({
                    url:ajax_url,
                    success:
                    function(data)
                    {
                      $.facebox(data);
                     
                      updateTimeToComplete();
                  
                  var date_shown, date_stored, due_date_set;
                  var due_date_set = false;
                  $("#datepicker").hide().datepicker(
                  { minDate: 0, 
                    onSelect: function(dateText, inst) {
                            if (!due_date_set) 
                            {
                              due_date_set = true;
                              $('#remove_date').slideDown();
                              $('#certficate_box').animate({top:'+=40'});
                            }
                            date_shown = $.datepicker.formatDate('dd MM, yy', new Date(dateText));
                            date_stored = $.datepicker.formatDate('mm/dd/yy', new Date(dateText));
                            
                            // Make ajax call to update due date for this assignment
                            url = ajax_object.ajax_url;
                            $.post(url, 'action=updateDueDate&course_id='+group_id+'&date='+date_stored+'&org_id='+org_id+'&portal_subdomain='+org_subdomain+'&task=add', function(data){
                              if (data.success == true) {
                                $('.curr_duedate').html("<strong>Due Date:</strong> " + date_shown);
                              } else {
                                alert('Error: ' + data.errors);
                                  }
                                }, 'json');
                              }
                      });
                  
                      $("#datepicker").show();
                  
                  $('#remove_date a').click(function() {
                    // Make ajax call to remove due date for this assignment
                    url = ajax_object.ajax_url;
                    $.post(url, 'action=updateDueDate&course_id='+group_id+'&org_id='+org_id+'&portal_subdomain='+org_subdomain+'&course_id='+group_id+'&task=remove', function(data){
                      if (data.success == true) {
                        $('.curr_duedate').html("<strong>Due Date:</strong> No due date set.");
                        $('#remove_date').slideUp();
                        $('#certficate_box').animate({top:'-=40px'});
                        due_date_set = false;
                      } else {
                        alert('Error: ' + data.errors);
                      }
                    }, 'json');
                  });
                  
                  $('#video_listing_pane').css({'height':'550px'}).jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
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
                        * Shows/Hides the quizzes and resources 
                        *                          
                        *********************************************/
                        obj
                        .parent()
                        .find("div[video_id="+obj.attr("video_id")+"]")
                        .toggle("slow",
                          function()
                          {
                          $('#video_listing_pane').css({'height':'550px'}).jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
                           var item = $(this).find('input[item = quiz][ type=checkbox],input[item = resource][ type=checkbox]')
                          // Disable the module exam if the video module is disabled.
                          // Enable the exam, if the video module is enabled
                          if( obj.is(':checked') )
                          {
                            if(item.is(':not(:checked)'))
                            {
                              item
                              .trigger("click")
                            }

                          }
                          else
                          {
                            if(item.is(':checked'))
                            {
                              item.trigger("click")
                            }
                          } 
                          $(document).trigger('updated.assignment_list');
                          updateCertificateProgress();
                          }
                        )
                    
                        /*********************************************
                        * Ajax to Add/Remove video quiz, and resources from course 
                        *                          
                        *********************************************/
                       /**********************************************************************************
                        * Getting value of the attributes from the courses div in part-manage_courses.php. 
                        * This varables will be used for sending post ajax                     
                        **********************************************************************************/
                        var group_id = obj.attr("group_id"); // the course id
                        var assignment_id = obj.attr("assignment_id"); // assignment id
                        var item_id = obj.attr("item_id"); // the item id
                        var module_id = obj.attr("video_id");//the module id
                        var org_id_e = obj.attr("org_id"); // org_id
                        var subdomain = obj.attr("portal_subdomain"); // portal subdomain

                        var info_data =
                        {
                            action: 'toggleItemInAssignment',
                            group_id: group_id,
                            assignment_id: assignment_id,
                            item_id: item_id,
                            module_id: module_id,
                            item: 'video',
                            org_id: org_id_e,
                            portal_subdomain: subdomain,
                        }

                        $.ajax( {
                          type: "GET",
                          url: ajax_object.ajax_url,
                          data: info_data,
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
                              // We are not expecting any message HTML response here. The script is going to continue below.
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
                        var obj = $(this);
                        console.log('Ive already been clicked');
                        $.getJSON(''+ajax_object.ajax_url+'?action=toggleItemInAssignment&group_id='+obj.attr("group_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id")+'&org_id='+obj.attr("org_id")+'&module_id='+obj.attr("video_id")+'&portal_subdomain='+obj.attr("portal_subdomain"),
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
                        )
                        }
                      ) 
                      .end()
                      .find('input[item = resource][ type=checkbox]')
                      .click(
                        function()
                        {
                        var obj = $(this)
                        
                        $.getJSON(''+ajax_object.ajax_url+'?action=toggleItemInAssignment&group_id='+obj.attr("group_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id")+'&module_id='+obj.attr("video_id")+'&org_id='+obj.attr("org_id")+'&portal_subdomain='+obj.attr("portal_subdomain"),
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
//                      $('#custom_quizzes_and_resources')
//                      .find('input[item = quiz][ type=checkbox]')
//                      .click(
//                        function()
//                        {
//                        var obj = $(this)
//                        console.log('wtf');
//                        $.getJSON('http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=toggleItemInAssignment&format=ajax&assignment_id='+obj.attr("assignment_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id"),
//                          function(json)
//                          {
//                          if(json.action=="added")
//                          {
//                            obj.parent().removeClass("disabled")
//                            obj.parent().addClass("enabled")
//                            $(document).trigger('updateAssignmentSummary',[0,1,0]);
//                          }
//                          else
//                          {
//                            obj.parent().removeClass("enabled")
//                            obj.parent().addClass("disabled")
//                            $(document).trigger('updateAssignmentSummary',[0,-1,0]);
//                          }
//                           
//                          }
//                        )
//                        }
//                      ) 
//                      .end()
//                      .find('input[item = resource][ type=checkbox]')
//                      .click(
//                        function()
//                        {
//                        var obj = $(this)
//                        
//                        $.getJSON('http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task=toggleItemInAssignment&format=ajax&assignment_id='+obj.attr("assignment_id")+'&item='+obj.attr("item")+'&item_id='+obj.attr("item_id"),
//                          function(json)
//                          {
//                          if(json.action=="added")
//                          {
//                            obj.parent().removeClass("disabled")
//                            obj.parent().addClass("enabled")
//                            $(document).trigger('updateAssignmentSummary',[0,0,1]);
//                          }
//                          else
//                          {
//                            obj.parent().removeClass("enabled")
//                            obj.parent().addClass("disabled")
//                            $(document).trigger('updateAssignmentSummary',[0,0,-1]);
//                          }
//                           
//                          }
//                        )
//                        }
//                      ) 
                    }
                  });
                 /******************************************************************
                 * $.facebox = function(data, klass,title,popup_footer, message)
                 * The title and message will pop up on ajax load via facebox.js. 
                 *******************************************************************/
                }, null, "Loading your modules", null, "Please wait while we process your modules...");
              // End of jQuery.facebox(
              }
          });//end of statement
          
          /****************************************************
          * Handles the click events for each course group row
          *****************************************************/
          $('#pane2').delegate('.group_list_table_row', 'click', function() {
            var menu_active_course = $(this); // The selected course.
            $('#display_videos_icon').click(); // If the user switch into another course, switch back to the modules view.
            if(group_name_global==$(this).find(".group_name").html())
            {
            group_name_global = "&lt;NO COURSE SELECTED&gt;";
            $("#staff_and_assignment_list").find(".tablehead-title").fadeTo('fast',0.01,function ()
              {
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
              var task = "";
              if($("#staff_and_assignment_list").attr("display")=="staff_list")
              {
                task = "getModules"
              }
              else
              {
                task = "getusersincourse"
              }

              var baseurl = 'http://www.expertonlinetraining.com/my-dashboard.html?task=do_ajax&ajax_task='+task+'&format=ajax&group_id='; 
              var url = baseurl+$(this).attr("group_id");

              $("#staff_and_assignment_list").find(".tablehead-title").fadeTo('fast',0.01);
              $("#staff_and_assignment_list").find(".scroll-pane-wrapper").html('\
              <div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "height:250px; width: 350px">\
                <div style = "width:100%;">\
                  <div style = "width:100%;text-align:center;padding-top:70px;font-size:140%;">\
                      Loading your modules <br /> <br />\
                      <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="loading_course_subscription_info"></i>\
                  </div>\
                </div>\
              </div>\
              ');
             $("#staff_and_assignment_list").attr("course-status","draft"); // Stops the user from clicking the Add/Remove staff while in published and suddenly chose a draft course.
              group_name_global = $(this).find(".group_name").html();
              var shortTitle = group_name_global;
              if (shortTitle.length > 30) 
              {
                shortTitle = jQuery.trim(shortTitle).substring(0, 27).split(" ").slice(0, -1).join(" ") + "...";
              }
              menu_active_course.find('span.video_count').text(""); // Reload video count.
              menu_active_course.find('#loading_course_subscription_info').show("slow"); // Show loading icon
              /**********************************************************************************
               * Getting value of the attributes from the courses div in part-manage_courses.php. 
               * This varables will be used for sending post ajax                     
               **********************************************************************************/
              var link = this;
              var id   = jQuery( link ).attr( 'course-id' );                  // Course ID
              var subdomain = jQuery( link ).attr( 'portal-subdomain' );      // subdomain
              var org_id_e = jQuery( link ).attr( 'org-id' );                 // org_id
              var subscription_id = jQuery( link ).attr( 'subscription-id' ); // Subscription ID
              var course_status = jQuery(link).attr( 'course-status' );       // The course status
              var info_data = 
              {
                action: 'getModules',
                course_id: id,
                portal_subdomain: subdomain,
                org_id: org_id_e,
                subscription_id: subscription_id,
                course_status: course_status,
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
                            .jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
                    })
                    .fadeTo("slow",1);
                    $("#staff_and_assignment_list").find(".tablehead-title").fadeTo('fast',0.01,function (){$(this).html(shortTitle).fadeTo('fast',1)});
                    /*
                     * When the user chose a course. The group id and course status is changed in #staff_and_assignment_list
                     * this are the attributes the "manage modules" checks everytime it is clicked. 
                     */
                    $("#staff_and_assignment_list").attr("group_id",obj.group_id);
                    $("#staff_and_assignment_list").attr("course-status",obj.course_status);
                    menu_active_course.find('#loading_course_subscription_info').hide(); // Hide the loading icon.
                    menu_active_course.find('span.video_count').text(obj.video_count + " Videos Assigned"); //Update the video count.
                  }
                },
                // If it fails on the other hand.
                error: function(XMLHttpRequest, textStatus, errorThrown) 
                {
                   alert( "POST Sent failed: " + textStatus );
                }
              });
            }
            $(this).children("p").slideToggle("fast",
            function(){
              $('#pane2').jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
             // $('#pane2')[0].scrollTo($(this).parent().position().top);
              fix_icon_position();
            }
            );
            $(this).toggleClass("active");
            $(this).siblings().children("p:visible")
            .slideUp("fast",
            function()
            {
              $('#pane2').jScrollPane({contentWidth:'0px',showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
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
              var div = '<div class = \"group_list_table_row\" group_id = '+data.group_id+' course-id= '+data.group_id+' org-id= '+data.org_id+' subscription-id='+data.subscription_id+' course-status=\"draft\" portal-subdomain='+data.portal_subdomain+' > \
                    <div class=\"group_name\">'+data.group_name+'</div> \
                    <p class = \"group_description\" style=\"display:none;\"><i>'+data.group_desc+'</i></p> \
                    <p class = \"group_description\" style=\"display:none;\"> \
                      <span class="staff_count"> 0 </span> Staff Members<br> \
                      <span class="video_count"> 0 </span> Videos Assigned \
                    </p> \
                    <div class=\"group_list_edit_row\" style=\"left: 215px;\"> \
                      <a href=\"<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=edit_course_group&amp;course_name='+data.group_name+'&amp;org_id='+data.org_id+'&amp;course_id='+data.group_id+'&amp;portal_subdomain='+data.portal_subdomain+'\" class=\"dasdfdasfelete_group\" rel=\"facebox\"> \
                        <i class=\"fa fa-pencil tooltip\" onmouseover=\"Tip(\'Edit course name.\', FIX, [this, 30, -60], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')\" onmouseout=\"UnTip()\" aria-hidden=\"true\"></i> \
                      </a> \
                    </div> \
                    <div class=\"group_list_delete_row\" style=\"left: 230px;\"> \
                      <a href=\"<?= $admin_ajax_url ?>?action=getCourseForm&amp;form_name=delete_course&amp;course_id='+data.group_id+'&amp;course_name='+data.group_name+'&amp;portal_subdomain='+data.portal_subdomain+'&amp;org_id='+data.org_id+'&amp;subscription_id='+data.subscription_id+'\" class=\"dasdfdasfelete_group\" rel=\"facebox\"> \
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
            //$('#pane2')[0].scrollTo(div.position().top);
          });

          /****************************************************
          * This function is called when updating the course 
          * to draft or published
          *****************************************************/
          $(document).bind('success.change_course_status', function(event,data) {     
            jQuery(document).trigger('close.facebox');
            var div = $('div.group_list_table_row[group_id = '+data.group_id+']');
            var element_list = $('#group_list .group_list_table_row');        
            $("#staff_and_assignment_list").attr("course-status","published"); // set course status to published.
            // Change the color of the draft ICON.
            $(element_list).each(function() {
              if( $(this).attr("group_id") == data.course_id)
              {
                if(data.status == "published")
                {
                  $(this).find( ".fa.fa-eye.draft" ).removeAttr('onmouseover');
                  $(this).find( ".fa.fa-eye.draft" ).removeClass( "draft" ).addClass( "published" );
                }
                else
                {
                  $(this).find( ".fa.fa-eye.published" ).removeClass( "published" ).addClass( "draft" );
                }
                return; // Same as continue.
              }
            });
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
                <div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "height:250px; width: 250px">\
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
          
          /*********************************************
          * Count number of  video quiz, and resources that have been checked
          *********************************************/
          $(document).bind('updateAssignmentSummary',updateTimeToComplete); 
          
          /*********************************************
          * Count number of  video quiz, and resources that have been checked
          *********************************************/
          $(document).bind('updated.assignment_list',updateTimeToComplete); 
          $(document).bind('load.staff_video_list',
            function (e, data)
            {
            var task = "";
            var menu_active_course = $('.group_list_table_row.active');
            menu_active_course.find('#loading_course_subscription_info').show("slow"); // Show loading icon

            if($("#staff_and_assignment_list").attr("display")=="staff_list")
            {
              task = "getUsersInCourse"
            }
            else
            {
              task = "getModules"
              menu_active_course.find('span.video_count').text(""); // Reload video count.
            }
            $("#staff_and_assignment_list").find(".scroll-pane-wrapper").html('\
              <div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "height:250px; width: 250px">\
                 <div style = "width:100%;">\
                <div style = "width:100%;text-align:center;padding-top:70px;font-size:140%;">\
                    ' + ( task == "getModules" ? "Loading your modules" : "Loading your staff accounts" ) + ' <br /> <br />\
                      <i class="fa fa-spinner fa-pulse fa-3x fa-fw" \
                      id="loading_course_subscription_info"></i>\
              </div>\
                 </div>\
                 '
            );
           /**********************************************************************************
            * Getting value of the attributes from the courses div in part-manage_courses.php. 
            * This varables will be used for sending post ajax                     
            **********************************************************************************/
            var obj = $(".group_list_table_row");
            var org_id_e = obj.attr("org-id");
            var subdomain = obj.attr("portal-subdomain");
            var subscription_id = obj.attr("subscription-id");
            var course_status = obj.attr("course-status");
            var info_data =
            {
                action: task,
                course_id: data.group_id,
                org_id: org_id_e,
                portal_subdomain: subdomain,
                subscription_id: subscription_id,
                course_status: course_status,
            }
            $.ajax( {
            type: "POST",
            url: ajax_object.ajax_url,
            data: info_data,
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
                $("#staff_and_assignment_list")
                .find(".scroll-pane-wrapper")
                .fadeTo('fast',0.1, function()
                  {
                  $(this).html(obj.message);
                  $('#staff_and_assignment_list_pane')
                    .css({'height':'250px'})
                    .jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5})
                  })
                 .fadeTo("slow",1);
                if($('#staff_and_assignment_list').attr("refresh")==1)
                {
                  if(task == "getModules")
                  {
                    var num_videos = (typeof obj.video_count != 'undefined') ? obj.video_count : 0;
                    menu_active_course.find('#loading_course_subscription_info').hide(); // Hide the loading icon.
                    menu_active_course.find('span.video_count').text(num_videos + " Videos Assigned"); //Update the video count.
                  }
                  else
                  {
                    menu_active_course.find('#loading_course_subscription_info').hide();
                    $('.group_list_table_row.active').find("span.staff_count").text(obj.staff_count + " Staff Members").show();
                  }
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
          });
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
          var url =  ajax_object.ajax_url + "?action=getCourseForm&form_name=add_staff_to_group&org_id="+org_id+"&group_id="+group_id+"&group_name="+encodeURIComponent(group_name)+"&org_subdomain="+org_subdomain;
          $.ajax({url:url,success:
            function(data)
            {
              $('.content').fadeOut(300, function() {
                $(this).html(data).fadeIn();
                //resize scrollbar
                $('#staff_listing_pane').css({'height':'350px'}).jScrollPane({showArrows:true, scrollbarWidth: 15, arrowSize: 16,animateTo:true,animateInterval:50, animateStep:5});
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
            <div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "height:250px; width: 250px">\
              <div style = "width:100%;">\
                <div style = "width:100%;text-align:center;padding-top:70px;font-size:140%;">\
                    '+loading_message+' <br /> <br /> \
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="loading_course_subscription_info"></i>\
                </div>\
              </div>\
            </div>\
          ');

          $('.group_list_table_row.active').find("#loading_course_subscription_info").show();   


          /**********************************************************************************
           * Getting value of the attributes from the courses div in part-manage_courses.php. 
           * This varables will be used for sending post ajax                     
           **********************************************************************************/
          var link = this;
          var id = $("#staff_and_assignment_list").attr( 'group_id' );      // course ID
          var subdomain = $('.group_list_table_row').attr( 'portal-subdomain' );  // subdomain
          var org_id_e = $('.group_list_table_row').attr( 'org-id' );             // org_id
          var subscription_id = $('.group_list_table_row').attr( 'subscription-id' ); // Subscription ID
          var course_status = $('.group_list_table_row').attr( 'course-status' ); // The course status

          var info_data = 
          {
            action: action,
            course_id: id,
            portal_subdomain: subdomain,
            org_id: org_id_e,
            subscription_id: subscription_id,
            course_status: course_status,
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

                // Handle the view when a user click on "Show Staff" or "Show Videos" on top of right column.
                if(action == "getUsersInCourse")
                {
                  $('.group_list_table_row.active').find("#loading_course_subscription_info").hide();
                  $('.group_list_table_row.active').find("span.staff_count").text(obj.staff_count + " Staff Members").show();
                }
                else
                {
                  $('.group_list_table_row.active').find("#loading_course_subscription_info").hide();
                  $('.group_list_table_row.active').find("span.video_count").html(data.video_count);
                }
                if($('#staff_and_assignment_list').attr("refresh")==1)
                {

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