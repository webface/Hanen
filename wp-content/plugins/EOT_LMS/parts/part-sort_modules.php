<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <?= CRUMB_ADMINISTRATOR ?>    
  <?= CRUMB_SEPARATOR ?>    
  <?= CRUMB_COURSES ?>
  <?= CRUMB_SEPARATOR ?>
    <span class="current"><?= __("Sort Modules", "EOT_LMS"); ?></span>     
</div>

<?php
  // Variable declaration
  global $current_user;

  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess(); 

  $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // The // Wordpress user ID                  // Wordpress user ID
  $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
  

  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
  {
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
      if(current_user_can( "is_director" ))
      {
        // Check if the subscription belongs to this user.
        $subscription = getSubscriptions($subscription_id,0,1); // get the subscription row
        if (isset($subscription))
        {
          if($org_id != $subscription->org_id)
          {
            echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.", "EOT_LMS");
            return;
          }
        }
        else
        {
            echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.", "EOT_LMS");
            return;
        }
        if( isset($_REQUEST['course_id']) )
        {
          $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID 
          $course_info = getCourse($course_id);
          echo '<h1 class="article_page_title">' . __("Sort Modules for ", "EOT_LMS") . $course_info['course_name'] . ' </h1>';
          echo'<ol id="sortable" style="margin-left:20px">';
          $modules = getModulesInCourse($course_id);
          foreach ($modules as $module) 
          {
?>
            <li class="ui-state-default" id="<?= $module['ID'] ?>" module-title="<?= $module['title']?>">
              <span class="ui-icon ui-icon-arrowthick-2-n-s">
              </span><?= $module['title']?>
            </li>
<?php
        }
?>
          </ol>
          <br>
          <br>
          <a href="?part=sort_modules&org_id=<?=$org_id?>&subscription_id=<?=$subscription_id?>" class="btn"><?= __("Choose another course", "EOT_LMS") ?></a>
          <style>
            #sortable { margin: 0; padding: 0; }
            #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
            #sortable li span { position: absolute; margin-left: -1.3em; }
          </style>
          <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script>
          $( function() {
            $( "#sortable" ).sortable({
                axis: 'y',
                update: function (event, ui) 
                {
                  var modules = [];
                  $(this).children('li').each(function(index, element) 
                  {
                      modules.push($(this).attr('id'));
                  });    
                  load('load_loading');
                  $.ajax(
                  {
                    url: ajax_object.ajax_url + "?action=updateModulesOrder&course_id=<?=$course_id?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {modules:modules},
                    success: function (data) {
                        console.log(data.message);
                        if (data.message == "success") 
                        {
                            $(document).trigger('close.facebox');
                        } 
                        else 
                        {
                            alert("<?= __("There was an error updating the module order", "EOT_LMS"); ?>: " + data.message);
                        }
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                        alert("<?= __("There was an error saving your data", "EOT_LMS"); ?>");
                    }
                  });        
                }
            });
            $( "#sortable" ).disableSelection();
          } );
          </script>
          <?php
        }
        else
        {
          echo '<h1 class="article_page_title">' . __("Sort Modules", "EOT_LMS") .' </h1>';
          echo __("Please begin by selecting a course.", "EOT_LMS");
          // Create datatable for staff lists.
          $courseTableObj = new stdClass();
          $courseTableObj->rows = array();
          $courseTableObj->headers = array(
            __("Course Name", "EOT_LMS") => 'name',
          );
          global $wpdb;
          $courses = getCoursesById($org_id,$subscription_id);
          foreach ($courses as $course) 
          {
            $courseTableObj->rows[] = array (
              '<a href="?part=sort_modules&org_id='.$org_id.'&subscription_id='.$subscription_id.'&user_id='.$user_id.'&course_id='.$course['ID'] .'" onclick="load("load_courses")">' . $course['course_name'] . '</a>'
            );
          }
          CreateDataTable($courseTableObj);
        }
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