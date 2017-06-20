<?php
/********************************************************************************************************
 * Get all exsisting libraries in wp_library base on Library ID
 * @param int $library_id - The library id
 * @return array objects - the library id, name and price.
 *******************************************************************************************************/
function getLibraries ($library_id = 0) 
{
	global $wpdb;
	$sql = "SELECT id, name, price from " . TABLE_LIBRARY;
	$inc_library = ($library_id > 0) ? " where `id` = " . $library_id . " " : "";
	$sql .= $inc_library;
	$results = ($library_id > 0) ? $wpdb->get_row ($sql) : $wpdb->get_results ($sql);
	return $results;
}

/*
 * Get the library based on the subscription id
 * return the Library information
 */
function getLibrary ($library_id = 0) {
    // Check if the library id is valid
    if ($library_id <= 0)
    {
        echo "Invalid library ID";
        return;
    }
    global $wpdb;
    $sql = "SELECT * from ".TABLE_LIBRARY." WHERE id = $library_id";
    $results = $wpdb->get_row ($sql);

    return $results;
}

/********************************************************************************************************
 * Get information from the wp_subscriptions table based on the subscription or library id.
 * @param int $subscription_id - The subscription id
 * @param int $library_id - The library ID
 * @param int $org_id - The organization ID
 * @param boolean active - include subscription that are inactive?
 * @param int $org_id - the org ID
 * @param date $start_date - the date to start the search from
 * @param date $end_date - the date to end the search till
 * @return array objects - subscriptions information for this library
 *******************************************************************************************************/
function getSubscriptions($subscription_id = 0, $library_id = 0, $active = 0, $org_id = 0, $start_date = '0000-00-00', $end_date = '0000-00-00') 
{
  global $wpdb;
  $sql = "SELECT * from " . TABLE_SUBSCRIPTIONS;

  if($subscription_id) 
  {	
  	// looking for a specific subscription
    $sql .=  " where `id` = " . $subscription_id;
  }
  else if($library_id)
  {
  	// looking for all subscriptions for a specific library
      $sql .= " where library_id = " . $library_id;
  }
  else if($org_id)
  {
    // looking for all subscriptions for organization ID
    $sql .= " where org_id = " . $org_id; 
  }
  else if($start_date != "0000-00-00" && $end_date != "0000-00-00")
  {
    $sql .= "  where trans_date >= '" . $start_date . "' AND trans_date <= '" . $end_date . "'";
  }

  $date = current_time('Y-m-d');
  $sql .= ($active) ? " AND status = 'active' AND start_date <= '$date' AND end_date >= '$date'" : "";

  $results = ($subscription_id > 0) ? $wpdb->get_row ($sql) : $wpdb->get_results ($sql);
  return $results;
}

/********************************************************************************************************
 * Get information from the wp_videos table based on the video name
 * @param String $video_name - The name of the video
 * @return array objects - video information
 *******************************************************************************************************/
function getVideo($video_name = "") 
{
  // Check if the library id is valid
  if ($video_name == "")
  {
      echo "Invalid video name";
      return;
  }
  global $wpdb;
  $sql = "SELECT * from " . TABLE_VIDEOS;
  // looking for a specific subscription
  $sql .=  " where `name` = '" . $video_name . "'";
  $results = $wpdb->get_row ($sql);
  return $results;
}

/********************************************************************************************************
 * Update the subscription table
 * @param int $subscription_id - the id of the subscription
 * @param array $data - an array of field values to update
 * @return array objects - video information
 *******************************************************************************************************/
function updateSubscription($subscription_id = 0, $data = array())
{
  extract($data);
  /**  REQUIRED VARIABLES IN $data
   * $library_id
   * $max
   * $transaction_date
   * $start_date
   * $end_date
   * $price
   * $method
   * $notes
   * $rep_id
   */

  global $wpdb;

  $sql = array("library_id" => $library_id,
  "staff_credits" => $max,
  "trans_date" => $transaction_date,
  "start_date" => $start_date,
  "end_date" => $end_date,
  "price" => $price,
  "method" => $method,
  "notes" => $notes,
  "rep_id" => $rep_id);

  $result = $wpdb->update(TABLE_SUBSCRIPTIONS, $sql, array("id" => $subscription_id));

  if($result !== false)
    $result = true;

  return $result;
}

/**
 * Format date into human readable date
 * @param string $date - the date
 */
function dateTimeFormat($date) 
{
    $date = date_create($date);
    return date_format($date, 'F j, Y');
}

/**
 * This creates the text that has a mouse hover message on it.
 *  @param string $msg - The hover message
 *  @param boolean $return = whether to use return or echo.
 *  @return onmouseover element
 */
function hover_text_attr($msg, $return=false) 
{
    $tip = " onmouseover=\"Tip('$msg', FIX, [this, 45, -9], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')\" onmouseout=\"UnTip()\" ";
    if ($return) return $tip;
    else echo $tip;
}


/**
* tableObj:
*  -> rows[][];
*  -> headers[];
*  -> id; (optional)
* rows: array of arrays with just value
*   rows = array( array( 'row 1 col 1', 'row 1 col 2' ), array( 'row 2 col 1', 'row 2 col 2' ) );
* headers: key is name value is class:
*   headers = array( 'col 1 name' => 'col 1 class', 'col 2 name' => 'col 2 class' );
* id: table's html id
* ,"'",'"','$','/','\\','...'
* ,'&#39;','&#34;','&#36;','&#47;','&#92;','xxx&#46;&#46;&#46;'
* filesname: The name of the file.
* download: Boolean, indication to display download button in CSV or Excel.
*/
function CreateDataTable($tableObj, $width="100%", $num_rows = 10, $download = false, $filename = "") 
{
  static $i = 0;
  $i++;
  $id = (isset($tableObj->id) ? $tableObj->$id : "DataTable_$i");
  ?>
  <div class="smoothness">
    <table class="display" id="<?=$id?>" style="width:<?=$width?>"></table>
  </div>
  <script type="text/javascript">
    var dataSet_<?=$id?> = <?=str_replace(array('(',')'), array('&#40;','&#41;'), json_encode($tableObj->rows))?>;
    $ = jQuery;
    (function($){
      $(function(){
        $('#<?=$id?>').dataTable( {
          "aaData": dataSet_<?=$id?>,
          "bJQueryUI": true,
          "sPaginationType": "full_numbers",
          "bAutoWidth": false,
          <?php echo ($download) ? 'dom: \'Bfrtip\',' : ""; ?>
          <?php echo ($download) ? '"buttons": [ {extend: \'csv\',title: \''.$filename.'\'}, {extend: \'excel\',title: \''.$filename.'\'}],' : ""; ?>
          "iDisplayLength": <?=$num_rows?>,
          "aoColumns": [
            <?php
              $columns = array();
              foreach($tableObj->headers as $header => $class) {
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
          }
        } );
      });
    })(jQuery);
  </script>
  <?php
}

/**
 * This creates the progress bar by the percentage specified.
 *  @param int $width - the progress bar's width
 *  @param int $percent - the progress bar's percentage
 *  @param boolean $return - whether to use return or echo.
 *  @return onmouseover element
 */
function eotprogressbar($width, $percent, $return=false) 
{
  $percent = round($percent);
  $bar = '<div class="smoothness">';
  $bar .= '<div style="width:'.$width.';height:1.3em; display:block" class="cell-field text eotprogressbar ui-progressbar ui-widget ui-widget-content ui-corner-all '.($percent < 50 ? "red" : ($percent < 80 ? "yellow" : "green"))."\" percents=\"$percent\" role=\"progressbar\" aria-valuemin=\"0\" aria-valuemax=\"100\" aria-valuenow=\"$percent\">";
  if ($percent) {
    $bar .= '<div class="ui-progressbar-value ui-widget-header ui-corner-left '.(($percent == 100) ? 'ui-corner-right' : null)."\" style=\"display: block; width:$percent%; \">";
    $bar .= "<div style=\"text-align:center;width:$width;position:absolute;text-align:center;\">$percent %</div>";
    $bar .= '</div>';
  }
  $bar .= '</div></div>';
  if ($return) return $bar;
  else echo $bar;
}

/***************************************************************************
 *
 * Format the status code into readable language
 * @param string status - The course status returned in LU
 *
 **************************************************************************/
function formatStatus($status = '')
{
    switch ($status)
    {
        case "in_progress":
            return "In Progress";
        case "not_started":
            return "Not Started";
        case "completed":
            return "Completed";
        case "passed":
            return "Passed";
        case "failed":
            return "Failed";
        case "pending_review":
            return "Pending Review";
        default:
            return "N/A";
    }
}

/**
 *   Generate Helpcenter
 */  
add_action('wp_ajax_getDashboard', 'getDashboard_callback'); //handles actions and triggered when the user is logged in
function getDashboard_callback() 
{
    if( isset ( $_REQUEST['option'] ) && isset ( $_REQUEST['format'] ) && isset ( $_REQUEST['sender'] ) )
    {
        $option = filter_var($_REQUEST['option'],FILTER_SANITIZE_STRING);
        $format = filter_var($_REQUEST['format'],FILTER_SANITIZE_STRING);
        $sender = sanitize_email( $_REQUEST['sender'] );
        
        if(current_user_can( "is_director" ))
        {
          ob_start();
        ?>
          <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
          
          <link rel="stylesheet" href="<?= get_template_directory_uri() . "/css/helpcenter/reset.css" ?>" type="text/css" media="screen" />
          <link rel="stylesheet" href="<?= get_template_directory_uri() . "/css/helpcenter/style.css" ?>" type="text/css" media="screen" />
          <script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() . "/js/jquery.min.js" ?>">
          </script>
          <script type="text/javascript">
          window.anim_finished = true;
          window.anim_speed = 400;
          
          jQuery(function($)
          {   
            $(document).ready(function()
            {           
              //$("#grayArea").height(Math.max($(window).height(), $(document).height(), document.documentElement.clientHeight));
              
              $("span[rel*=facebox]").click(function()
              {
                parent.toggle_help_center();
                parent.open_facebox($(this).attr('dest'));
                return false;
              });
              
              $("#grayArea li a.topic").each(function()
              {
                $(this).click(function()
                {
                  if (window.anim_finished == false)
                    return false;
                  else
                    window.anim_finished = false;
                  
                  $("#grayArea li a.topic").each(function()
                  {
                    if ($(this).hasClass("inactive"))
                    {
                      $(this).slideDown(window.anim_speed, function()
                      {
                        $(this).removeClass("inactive");
                      }).next().slideUp(window.anim_speed);
                      return false;
                    }
                  });
                  
                  $(this).slideUp(window.anim_speed, function()
                  {
                    $(this).addClass("inactive");
                  }).next().slideDown(window.anim_speed);
                  setTimeout(function() { window.anim_finished = true; }, window.anim_speed + 50);
                  return false;
                });
              })
            });
          });
          </script>
          <div id="container">
            <div id="grayArea">
              <h1 id="helpTitle">
                <span class="orange">
                  Help
                </span>
                Center
              </h1>
              <ul>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Watch Some Videos" class="topic">Watch Some Videos <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    This page allows you to view the content you have available for your staff members through your purchased subscription.
                    <br />
                    <br />
                    <span class="link" dest="some_videos" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Pick a Default Course" class="topic">Pick a Default Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to manage the default courses for your staff members.            
                    <br />
                    <br />
                    <span class="link" dest="default_course" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Modify a Default Course" class="topic">Modify a Default Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to modify the default courses for your staff members.
                    <br />
                    <br />
                    <span class="link" dest="modify_default" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Create a New Course" class="topic">Create a New Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to create you own custom course for your staff memebers.            
                    <br />
                    <br />
                    <span class="link" dest="create_course" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Publish a Course" class="topic">Publish a Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Before you can enroll your staff in a course, it must be published.        
                    <br />
                    <br />
                    <span class="link" dest="publish_course" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Modify a Published Course" class="topic">Modify a Published Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to modify a published course.
                    <br />
                    <br />
                    <span class="link" dest="modify_published" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Add Staff Information" class="topic">Add Staff Information <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to add staff and enroll them into courses. 
                    <br />
                    <br />
                    <span class="link" dest="add_staff" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Change Course Enrollment" class="topic">Change Course Enrollment <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to modify user enrollments in specific courses.
                    <br />
                    <br />
                    <span class="link" dest="change_enroll" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Check Statistics" class="topic">Check Statistics <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to get statistics on how your staff is progressing through their assigned courses.
                    <br />
                    <br />
                    <span class="link" dest="check_stats" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Upload Custom Content" class="topic">Upload Custom Content <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to upload your own custom content so that your staff has access to it.
                    <br />
                    <br />
                    <span class="link" dest="upload_custom" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Create a Custom Quiz" class="topic">Create a Custom Quiz <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to create custom quizes for your staff.
                    <br />
                    <br />
                    <span class="link" dest="create_custom_quiz" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
              </ul>
            </div><!--end grayArea-->
          </div><!--end container-->
        <?php
          $html = ob_get_clean();
        }
    }
    echo $html;
    wp_die();
}

/**
 *   Display Help Videos
 */  
add_action('wp_ajax_displayHelp', 'displayHelp_callback');
function displayHelp_callback() 
{
    if( isset ( $_REQUEST['help_name'] ) )
    {
      $help_name = filter_var($_REQUEST['help_name'],FILTER_SANITIZE_STRING);
      if($help_name == "some_videos")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Watch Some Videos</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_1').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_1" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Watch_Some_Videos.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>       
        </div>
        <?php
        $html = ob_get_clean();
      }
      else if($help_name == "default_course")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Pick a Default Course</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_2').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_2" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Pick_a_Default_Course.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>       
        </div>
        <?php
        $html = ob_get_clean();
      }
      else if($help_name == "modify_default")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Modify a Default Course</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_3').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_3" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Modify_a_Default_Course.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>         
        </div>       
        <?php
      }
      else if($help_name == "create_course")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Create a New Course</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_4').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_4" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Create_a_New_Course.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>         
        </div>       
        <?php
      }
      else if($help_name == "publish_course")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Publish a Course</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_5').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_5" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Publish_a_Course.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>         
        </div>       
        <?php
      }
      else if($help_name == "modify_published")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Modify a Published Course</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_6').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_6" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Modify_a_Published_Course.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>         
        </div>       
        <?php
      }
      else if($help_name == "add_staff")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Add Staff Information</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_7').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_7" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Add_Staff_Information.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>         
        </div>       
        <?php
      }
      else if($help_name == "change_enroll")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Change Course Enrollment</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_8').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_8" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Change_Course_Enrollment.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>        
        </div>       
        <?php
      }
      else if($help_name == "check_stats")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Check Statistics</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_9').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_9" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Analyze_Course_Statistics2.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>          
        </div>       
        <?php
      }
      else if($help_name == "upload_custom")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Upload Custom Content</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_10').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_10" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Manage_Custom_Content.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>          
        </div>       
        <?php
      }
      else if($help_name == "create_custom_quiz")
      {
        ob_start();
        ?>
        <div>
          <div class="title">
            <div class="title_h2">Create a Custom Quiz</div>
          </div>
          <div class='buttons'>
            <a onclick="videojs('help_video_11').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
              <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
            </a>
          </div>
        </div>
        <div style="padding: 20px;">
          <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
          <video id="help_video_11" class="video-js vjs-default-skin" preload="auto" width="500" height="300" data-setup='{"controls": true}'>
            <source src="https://eot-output.s3.amazonaws.com/tutorial_Make_a_Custom_Quiz.mp4" type='video/mp4'>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that
              <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>        
          </video>          
        </div>       
        <?php
      }
    }
    echo $html;
    wp_die();
}
/* Create loading screen div template
 * String $id - the ID of this DIV
 * String $title - the title of the loading screen
 * String $message - the message you want to appear to the user. 
 */ 

function getLoadingDiv($id, $title, $message) 
{
  ?>
  <div style="display:none;" id="<?= $id ?>" rel="facebox">
    <div class="title">
      <div class="title_h2"><?= $title ?></div>
    </div>
    <div id="faceboxMiddle">  
     <p><?= $message ?></p>
    </div>
    <center>
      <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="loading_course_subscription_info"></i>
    </center>
    <br />
  </div>
  <?php
}


/*******************************************************************************
* Module
* Create a module object
* int $id - The module ID
* string $title - The title of the Module
* string $category - The name of the category for the Module
*******************************************************************************/
 
class Module
{
  var $id; // The Module ID
  var $title; // The title of the Module
  var $category; // The category for the Module
  var $type; // Type of module: Course or Exam

  function __construct($id, $title, $category, $type)
  {
    $this->id = $id;
    $this->title = $title;
    $this->category = $category;
    $this->type = $type;
  }
}

/*******************************************************************************
* Enrollment
* Create an enrollment object
* int $id - The Enrollment ID
* int $userid - User ID of the enrolled user
* string $first_name - First name of the enrolled user
* string $last_name - Last name of the enrolled user
* string status - The status of this enrollment
* int $percentage_complete - Percentage complete for the enrollment
* string $email - The email of the user
* int $percentage - The actual score for the user
*******************************************************************************/
class Enrollment
{
  var $id;
  var $userid;
  var $first_name;
  var $last_name;
  var $status;
  var $percentage_complete;
  var $email;
  var $percentage;

  // Constructor
  function __construct($id, $userid, $first_name, $last_name, $status, $percentage_complete, $email, $percentage)
  {
    $this->id = $id;
    $this->userid = $userid;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->status = $status;
    $this->percentage_complete = $percentage_complete;
    $this->email = $email;
    $this->percentage = $percentage;
  }
}


/*********************************************************
 *  Generate a progress bar
 *********************************************************/ 
function eotprogressbar2($percent = 0, $return=false) 
{
  $bar =  '<div id="progressBar">';
  $bar .= '  <div class="smoothness">';
  $bar .= '    <div class="cell-row">';
  $bar .= '      <div class="cell-field text eotprogressbar2 ui-progressbar ui-widget ui-widget-content ui-corner-all" percent="100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100" style="display: block;">';
  $bar .= '       <div class="ui-progressbar-value ui-widget-header ui-corner-left" style="display: block; width: ' . $percent . '%;"></div>';
  $bar .= '    </div>';
  $bar .= '    </div>';
  $bar .= '  </div>';
  $bar .= '</div>';
  if ($return) return $bar;
  else echo $bar;
}

/*********************************************************
 *  Generate a progress bar
 *********************************************************/ 
function getStatus( $status ) 
{
  switch($status)
  {
    case "not_started":
      return "Not Started";
      break;
    case "in_progress":
      return "In Progress";
      break;
    case "completed":
      return "Completed";
      break;
    case "passed":
      return "Passed";
      break;
     case "failed":
      return "Failed";
      break;
    case "pending_review":
      return "Pending Review"; 
      break;     
    default:
      echo "Could not determine your status.";
  }
}

// remove the prefix from the subdomain so we can use it on the EOT domain.
// eg. eotcamp.learnupon.com == camp.expertonlinetraining.com
function getEOTSubdomain ($subdomainName)
{
  return substr($subdomainName, strlen(SUBDOMAIN_PREFIX), strlen($subdomainName));
}

//get questions from the question table
function getQuestions ($library_id = 0)
{
  global $wpdb;

  // make sure a library_id was passed into the function.
  if (!$library_id)
  {
    return null;
  }

  //grab all questions and answers
  $sql = "SELECT * FROM " . TABLE_QUESTIONS . " q WHERE q.library_id = " . $library_id . " ORDER BY q.order, q.id";

  $results = $wpdb->get_results ($sql);

  return $results;

  wp_die();
}

add_action('wp_ajax_updateAnswers', 'updateAnswers_callback');

function updateAnswers_callback()
{
  //grab the question number and new answer
  $new_answer = $_REQUEST['new_answer'];
  $question_number = filter_var($_REQUEST['question_number'],FILTER_SANITIZE_NUMBER_INT);

  global $wpdb;

  //update statement to update the answer
  $sql = "UPDATE " . TABLE_QUESTIONS . " SET answer=\"" . $new_answer . "\" WHERE id=" . $question_number;

  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

add_action('wp_ajax_updateQuestion', 'updateQuestion_callback');

function updateQuestion_callback()
{
  //grab the question number and new question
  $new_question = $_REQUEST['new_question'];
  $question_number = filter_var($_REQUEST['question_number'],FILTER_SANITIZE_NUMBER_INT);

  global $wpdb;

  //update statement to update the question
  $sql = "UPDATE " . TABLE_QUESTIONS . " SET question=\"" . $new_question . "\" WHERE id=" . $question_number;

  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

function grabVideos()
{
  global $wpdb;

  //grab the list of all videos
  $sql = "SELECT id, name, `desc` FROM " . TABLE_VIDEOS . " ORDER BY name";

  $results = $wpdb->get_results ($sql);

  return $results;

  wp_die();
}


function grabConditions($question_number = 0, $answer_number = 0, $course_name_id = 0)
{
  global $wpdb;

  // check to make sure we have valid inputs
  if (!$question_number || !$answer_number || !$course_name_id)
  {
    return null;
  }

  //grab the conditions based on question id, answer number and course_name_id
  $sql = "SELECT name FROM " . TABLE_VIDEOS . " as videos INNER JOIN " . TABLE_QUESTION_MODIFICATIONS . " as conditions ON videos.id = conditions.video_id WHERE conditions.question = " . $question_number . " AND conditions.answer = " . $answer_number . " AND conditions.course_name_id = " . $course_name_id;

  $results = $wpdb->get_results ($sql, ARRAY_N);

  return $results;

  wp_die();
}

add_action('wp_ajax_changeCondition', 'changeCondition_callback');

//this function is designed to reflect changes to the questionnaire conditions into the database (add and remove)
function changeCondition_callback()
{
  $video_id = filter_var($_REQUEST['video_id'],FILTER_SANITIZE_NUMBER_INT);               //video id
  $question_number = filter_var($_REQUEST['question_number'],FILTER_SANITIZE_NUMBER_INT); //question id
  $answer_number = filter_var($_REQUEST['answer_number'],FILTER_SANITIZE_NUMBER_INT);     //answer number
  $course_name_id = filter_var($_REQUEST['course_name_id'],FILTER_SANITIZE_NUMBER_INT);   //course id
  $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
  $change = filter_var($_REQUEST['change'],FILTER_SANITIZE_STRING);                       //this variable contains 'Add' or 'Remove'

  global $wpdb;

  //This statement will grab any existing Add or Remove based on video id, question id and answer number
  $sql = "SELECT * FROM " . TABLE_QUESTION_MODIFICATIONS . " WHERE video_id = " . $video_id . " AND question = " . $question_number . " AND answer = " . $answer_number . " AND course_name_id = " . $course_name_id;

  $results = $wpdb->get_results ($sql, ARRAY_N);

  //if no Add or Remove statements already exist
  if(empty($results))
  {
    //insert a new Add or Remove statement
    $sql = "INSERT INTO " . TABLE_QUESTION_MODIFICATIONS . " (library_id, question, answer, course_name_id, action, video_id) VALUES (" . $library_id . ", " . $question_number . ", " . $answer_number . ", " . $course_name_id . ", \"" . $change . "\", " . $video_id . ")";
    $wpdb->query ($wpdb->prepare ($sql));
  }
  //Otherwise remove the already existing Add or Remove statement
  else
  {
    $sql = "DELETE FROM " . TABLE_QUESTION_MODIFICATIONS . " WHERE video_id = " . $video_id . " AND question = " . $question_number . " AND answer = " . $answer_number;
    $wpdb->query ($wpdb->prepare ($sql));
  }

  wp_die();
}

add_action('wp_ajax_deleteQuestion', 'deleteQuestion_callback');

//this function deletes questions from the questionnaire
function deleteQuestion_callback()
{
  $question_id = filter_var($_REQUEST['question_id'],FILTER_SANITIZE_NUMBER_INT);         //question id
  $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id

  global $wpdb;

  $sql = "DELETE FROM " . TABLE_QUESTIONS . " WHERE id = " . $question_id . " AND library_id = " . $library_id;
  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

add_action('wp_ajax_addQuestion', 'addQuestion_callback');

//this function adds questions to the questionnaire
function addQuestion_callback()
{
  $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
  $question = $_REQUEST['question'];                                                      //question
  $answer = $_REQUEST['answer'];                                                          //answer

  global $wpdb;

  $sql = "INSERT INTO " . TABLE_QUESTIONS . " (library_id, question, answer) VALUES (" . $library_id . ", \"" . $question . "\", \"" . $answer . "\")";
  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

/********************************************************************************************************
 * Filter out and return an array of user types
 * @param ARRAY $users - an array of associative arrays of users (got from JOSN decode)
 * @param STRING $type - the user type you want (learner, admin, instructor)
 * @param bool $sort - whether we want to sort the results by first name
 * @return array of associative arrays - lists of users of specified type
 *******************************************************************************************************/
function filterUsers ($users = '', $type = 'learner', $sort = 0)
{
  $staff_accounts = array(); // Staff accounts

  // check that users in not NULL
  if ($users == '')
    return $staff_accounts;

  foreach($users as $user)
  {
      if($user['user_type'] == $type)
      {
        // We have to capitalized the first letter of their first and last name so we can properly sort them by name. 
        $user['first_name'] = ucfirst(strtolower($user['first_name'])); // Make the first letter of their first name capital.
        $user['last_name'] = ucfirst(strtolower($user['last_name'])); // Make the first letter of their last name capital.
        array_push($staff_accounts, $user);
      }
  }
  // check if we want to sort the array and if so sort it by sort_field
  if ($sort)
  {
    uasort($staff_accounts, 'sort_array_by_first_name'); // Sort the users by their first name.
  }
  return $staff_accounts;
}

/********************************************************************************************************
 * Filter out and return an array of user types
 * @param ARRAY $users - an array of associative arrays of users (got from JOSN decode)
 * @param STRING $type - the user type you want (learner, admin, instructor)
 * @param bool $sort - whether we want to sort the results by first name
 * @return array of associative arrays - lists of users of specified type
 *******************************************************************************************************/
function filterUsersMassMail ($users = '', $type = 'learner', $sort = 0)
{
  $staff_accounts = array(); // Staff accounts

  // check that users in not NULL
  if ($users == '')
    return $staff_accounts;

  foreach($users as $user)
  {
      if($user['user_type'] == $type)
      {
        // We have to capitalized the first letter of their first and last name so we can properly sort them by name. 
        $user_info['first_name'] = ucfirst(strtolower($user['first_name'])); // Make the first letter of their first name capital.
        $user_info['last_name'] = ucfirst(strtolower($user['last_name'])); // Make the first letter of their last name capital.
        $user_info['email'] = $user['email'];
        $user_info['id'] = $user['id'];
        $user_info['sign_in_count'] = $user['sign_in_count'];

        array_push($staff_accounts, $user_info);
      }
  }
  // check if we want to sort the array and if so sort it by sort_field
  if ($sort)
  {
    uasort($staff_accounts, 'sort_array_by_first_name'); // Sort the users by their first name.
  }
  return $staff_accounts;
}


//grabs video duration and wordpress id of presentor and returns it in an array with video titles as keys
function videoTimes ()
{
  global $wpdb;

  $sql = "SELECT  name, id, presenter_id, secs, `desc` FROM " . TABLE_VIDEOS;

  $results = $wpdb->get_results ($sql, OBJECT_K);

  return $results;
}

add_action('wp_ajax_updateCustomSettings', 'updateCustomSettings_callback');

//This function updates the custom fields
function updateCustomSettings_callback()
{
  $meta_key = filter_var($_REQUEST['meta_key'],FILTER_SANITIZE_STRING);
  $meta_value = filter_var($_REQUEST['meta_value'],FILTER_SANITIZE_STRING);
  $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);
  $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
  $type = filter_var($_REQUEST['type'],FILTER_SANITIZE_STRING);

  if ($type == "post")
  {
    update_post_meta($org_id, $meta_key, $meta_value);
  }
  else if ($type == "user")
  {
    update_user_meta($user_id, $meta_key, $meta_value);
  }
  wp_die();
}

add_action('wp_ajax_switchUser', 'switchUser_callback');

//this function switches the user
function switchUser_callback()
{
  $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);          //user id

  switch_to_user($user_id);

  wp_die();
}

add_action('wp_ajax_updateSubscriptionSettings', 'updateSubscriptionSettings_callback');

//This function updates the subscription fields
function updateSubscriptionSettings_callback()
{
  global $wpdb;
  
  $id = filter_var($_REQUEST['id'],FILTER_SANITIZE_NUMBER_INT);
  $field = filter_var($_REQUEST['field'],FILTER_SANITIZE_STRING);
  $value = filter_var($_REQUEST['value'],FILTER_SANITIZE_STRING);

  $sql = "UPDATE " . TABLE_SUBSCRIPTIONS . ' SET ' . $field . ' = "' . $value . '" WHERE id = ' . $id;
  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

add_action('wp_ajax_createSubdomainAll', 'createSubdomainAll_callback');

//This function creates the subdomain on learnupon and links to the user
function createSubdomainAll_callback()
{
  
  $org_subdomain = SUBDOMAIN_PREFIX.filter_var($_REQUEST['subdomain'],FILTER_SANITIZE_STRING);   //subdomain
  $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);                          //org id
  $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);                        //user id
  $first_name = get_user_meta ($user_id, 'first_name', true);                                    //first name
  $last_name = get_user_meta ($user_id, 'last_name', true);                                      //last name
  $email = get_user_by('id', $user_id)->user_login;                                              //email
  $password = wp_generate_password();                                                            //generate password for learnupon
  $org_name = get_the_title($org_id);                                                            //org name

  //link subdomain to org id
  update_post_meta ($org_id, 'org_subdomain', $org_subdomain);

  //create subdomain on learnupon
  $data = compact ("org_id", "org_name", "org_subdomain", "user_id", "first_name", "last_name", "email", "password");
  $result = communicate_with_learnupon ('create_account', $data);

  if (isset($result['status']) && $result['status'])
  {
    // yey ... success
  }
  else if (isset($result['status']) && !$result['status'])
  {
    // error
    echo json_encode($result);
    wp_die();
  }
  else
  {
    // some other error
    $result['status'] = 0;
    $result['message'] = 'ERROR in createSubdomainAll callback: Something went terribly wrong!';
    echo json_encode($result);
    wp_die();
  }

  $result = setCnameRecord($org_subdomain);
  if (isset($result['status']) && $result['status'])
  {
    // yey ... success
  }
  else if (isset($result['status']) && !$result['status'])
  {
    // error
    echo json_encode($result);
    wp_die();
  }
  else
  {
    // some other error
    $result['status'] = 0;
    $result['message'] = 'ERROR in createSubdomainAll callback: Something went terribly wrong!';
    echo json_encode($result);
    wp_die();
  }
  wp_die();
}

//grabs learn upon information from api logs
function getLrnuponFromLogs($org_subdomain, $first_name, $last_name)
{
  global $wpdb;

  $lrn_upon_info = new stdClass();      //object to be returned

  //find learnupon id
  $sql = "SELECT response FROM " . TABLE_API_LOGS . " WHERE endpoint = 'https://eot.learnupon.com/api/v1/portals' AND payload LIKE '%\"subdomain\":\"" . $org_subdomain . "\"%'";

  $result = $wpdb->get_row ($sql);

  if(isset(json_decode($result->response)->id))
  {
    $lrn_upon_info->lrn_upon_id = json_decode($result->response)->id;
  }
  else
  {
    $lrn_upon_info->lrn_upon_id = "Not found";
  }

  //find learnupon api user and pass
  $sql = "SELECT response FROM " . TABLE_API_LOGS . " WHERE endpoint = 'https://" . $org_subdomain . ".learnupon.com/api/v1/portals/" . $lrn_upon_info->lrn_upon_id . "/generate_keys'";

  $result = $wpdb->get_row ($sql);
  
  if(isset(json_decode($result->response)->portal[0]) && isset(json_decode($result->response)->portal[0]->username))
  {
    $lrn_upon_info->lrn_upon_api_usr =  json_decode($result->response)->portal[0]->username;
  }
  else
  {
    $lrn_upon_info->lrn_upon_api_usr = "Not found";
  }  

  if(isset(json_decode($result->response)->portal[0]) && isset(json_decode($result->response)->portal[0]->password))
  {
    $lrn_upon_info->lrn_upon_api_pass =  json_decode($result->response)->portal[0]->password;
  }
  else
  {
    $lrn_upon_info->lrn_upon_api_pass = "Not found";
  }

  //find learnupon user id
  $sql = "SELECT response FROM " . TABLE_API_LOGS . " WHERE endpoint = 'https://" . $org_subdomain . ".learnupon.com/api/v1/users' and payload LIKE '%\"last_name\":\"" . $last_name . "\",\"first_name\":\"" . $first_name . "\"%'";

  $result = $wpdb->get_row ($sql);

  if(isset(json_decode($result->response)->id))
  {
    $lrn_upon_info->staff_id = json_decode($result->response)->id;
  }
  else
  {
    $lrn_upon_info->staff_id = "Not found";
  }

  //return object
  return $lrn_upon_info;
}

add_action('wp_ajax_updateDomainName', 'updateDomainName_callback');

//This function updates the domain name
function updateDomainName_callback()
{
  $original_subdomain = filter_var($_REQUEST['original_subdomain'],FILTER_SANITIZE_STRING);     //original subdomain
  $subdomain = filter_var($_REQUEST['subdomain'],FILTER_SANITIZE_STRING);                       //new subdomain
  $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);                       //user id
  $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);                         //org id
  $org_name = get_the_title($org_id);                                                           //org name

  $cloudflare_id = listCnameRecords($original_subdomain);
  if (isset($cloudflare_id[0]['id']))
  {
    // we got the CNAME id
    $subdomain_id = $cloudflare_id[0]['id'];
  }
  else if (isset($cloudflare_id['status']) && !$cloudflare_id['status'])
  {
    // got an error
    echo json_encode($cloudflare_id);
    wp_die();
  }
  else
  {
    // some other error
    $result['status'] = 0;
    $result['message'] = 'ERROR in updateDomainName callback: Something went terribly wrong!';
    echo json_encode($result);
    wp_die();
  }

  $portal = getPortalByTitle(DEFAULT_SUBDOMAIN, $org_name);
  if (isset($portal['status']) && !$portal['status'])
  {
    // error getting the portal ID
    echo json_encode($portal);
    wp_die();
  }
  else if (empty($portal))
  {
    $result['status'] = 0;
    $result['message'] = 'ERROR: Couldn\'t find the subdomain in LU';
    echo json_encode($result);
    wp_die();
  }
  else if (isset($portal[0]['id']))
  {
    $portal_id = $portal[0]['id'];
  }
  else {
    // some other error
    $result['status'] = 0;
    $result['message'] = 'ERROR in updateDomainName callback: Something went terribly wrong!';
    echo json_encode($result);
    wp_die();
  }

  //update Cname record on cloudflare
  $update_subdomain = updateCnameSubdomain($subdomain_id, $subdomain);
  if (isset($update_subdomain['status']) && $update_subdomain['status'])
  {
    // yey ... success
  }
  else if (isset($update_subdomain['status']) && !$update_subdomain['status'])
  {
    // error
    echo json_encode($update_subdomain);
    wp_die();
  }
  else
  {
    // some other error
    $result['status'] = 0;
    $result['message'] = 'ERROR in updateDomainName callback: Something went terribly wrong!';
    echo json_encode($result);
    wp_die();
  }

  //update portal name on learnupon
  $update_portal_subdomain = updatePortalSubdomain($original_subdomain, $portal_id, $subdomain, $org_id);
  if (isset($update_portal_subdomain['status']) && $update_portal_subdomain['status'])
  {
    // yey ... success
  }
  else if (isset($update_portal_subdomain['status']) && !$update_portal_subdomain['status'])
  {
    // error
    echo json_encode($update_portal_subdomain);
    wp_die();
  }
  else
  {
    // some other error
    $result['status'] = 0;
    $result['message'] = 'ERROR in updateDomainName callback: Something went terribly wrong!';
    echo json_encode($result);
    wp_die();
  }

  //update portal name in our database
  update_post_meta($org_id, 'org_subdomain', $subdomain);

  echo json_encode(array('status' => 1));
  wp_die();
}

add_action('wp_ajax_updateCloudflareSubdomain', 'updateCloudflareSubdomain_callback');

//This function updates the subddomain name on cloudflare only
function updateCloudflareSubdomain_callback()
{
  $new_subdomain = filter_var($_REQUEST['new_subdomain'],FILTER_SANITIZE_STRING);               //new subdomain
  $subdomain_id = $_REQUEST['subdomain_id'];             //subdomain id

  //update Cname record on cloudflare
  $update_subdomain = updateCnameSubdomain($subdomain_id, $new_subdomain);

  if (isset($update_subdomain['status']) && $update_subdomain['status'])
  {
    // yey ... success
  }
  else if (isset($update_subdomain['status']) && !$update_subdomain['status'])
  {
    // error
    echo json_encode($update_subdomain);
    wp_die();
  }
  else
  {
    // some other error
    $result['status'] = 0;
    $result['message'] = 'ERROR in updateCloudflareSubdomain callback: Something went terribly wrong!';
    echo json_encode($result);
    wp_die();
  }

  echo json_encode(array('status' => 1));
  wp_die();
}

//This function returns a LU link by using Sqsso key of the user to log in
//if enrollment_id is given, it will redirect to the course
function redirectToLU($enrollment_id = 0)
{
  // Variable declaration
  global $current_user;
  $user_id = $current_user->ID; // Wordpress user ID
  $email = $current_user->user_email; // Wordpres e-mail address
  $org_id = get_org_from_user ($user_id); // Organization ID
  $portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
  $sqsso_key = get_post_meta ($org_id, 'lrn_upon_sqsso_key', true); // the SQSSO key for this portal
   /* 
    * This process the creation of the URL for the SQSSO. Redirect them to the specified course in learnupon.
  */
  $date = new DateTime();
  $timestamp = $date->getTimestamp();
  $message = "USER=" . $email . "&TS=" . $timestamp . "&KEY=" . $sqsso_key; // the Secret KEY

  $link = "https://" . $portal_subdomain . "." . LRN_UPON_URL . "/sqsso?"; // SQSSO Link

  if($enrollment_id)
  {
    $link .= "SSOUserName=" . $email . "&Email=" . $email . "&TS=" . $timestamp . "&SSOToken=" . md5( $message ) . "&redirect_uri=/enrollments/{$enrollment_id}"; // Parameters send to SQSSO
  }
  else
  {
    $link .= "SSOUserName=" . $email . "&Email=" . $email . "&TS=" . $timestamp . "&SSOToken=" . md5( $message ); // Parameters send to SQSSO
  }

  return $link;
}

//This function puts the information of the uploaded resource into the database
function uploadResource($video_name = '', $name = '', $order = 10, $url = '')
{
  global $wpdb;

  $video_id = getVideoId($video_name);

  if(!$video_id)
  {
    return "ERROR: couldn't get the video ID.";
  }

  $sql = "INSERT INTO " . TABLE_RESOURCES . " (video_id, name, `order`, `date`, url) VALUES (" . $video_id . ", '" . $name . "', " . $order . ", NOW(), '" . $url . "')";
  $result = $wpdb->query ($sql);

  if(!$result)
  {
    return "ERROR: couldn't insert resource into DB (query).";
  }

  return "Successfully uploaded!";
}

//This function grabs the resources previously uploaded for a ceratin video. Video Id is from the wp_videos table
function getResources($video_id = 0)
{
  global $wpdb;

  $sql = "SELECT * FROM " . TABLE_RESOURCES . " WHERE video_id = " . $video_id . " AND active = 1 ORDER BY `order`";
  $results = $wpdb->get_results($sql);

  return $results;
}

//This function finds the video_id from wp_videos table based on the video name
function getVideoId($video_name = '')
{
  global $wpdb;

  $sql = "SELECT id FROM " . TABLE_VIDEOS . " WHERE name = '" . $video_name . "'";
  $video_id = $wpdb->get_row($sql);

  if(!$video_id)
  {
    return NULL;
  }

  return $video_id->id;
}

add_action('wp_ajax_updateCreateSalesRep', 'updateCreateSalesRep_callback');

//this function updates or creates a sales rep (if doesn't exist)
function updateCreateSalesRep_callback()
{
  $first_name = filter_var($_REQUEST['first_name'],FILTER_SANITIZE_STRING);
  $last_name = filter_var($_REQUEST['last_name'],FILTER_SANITIZE_STRING);
  $email = filter_var($_REQUEST['email'],FILTER_SANITIZE_STRING);
  $password = $_REQUEST['password'];
  $create_user = filter_var($_REQUEST['create_user'],FILTER_SANITIZE_NUMBER_INT); // int: 1 to create user, 0 or null to update

  $new_user = array(
    'user_login' => $email,
    'user_email' => $email,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'role' => 'salesrep'
  );

  if($password)
  {
    $new_user['user_pass'] = $password;
  }

  if($create_user)
  {
    $user_id = wp_insert_user ($new_user);
  }
  else
  {
    $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);
    $new_user['ID'] = $user_id;
    $user_id = wp_update_user ($new_user);
  }

  if (is_wp_error($user_id))
  {
    $result['status'] = 0;
    $result['message'] = $user_id->get_error_message();
  }
  else
  {
   $result['status'] = 1;
  }

  echo json_encode($result);
  wp_die();
}

// sort category order
function category_sort($a, $b)
{
  $order = array (
    'Leadership' => 1,
    'Youth_Development_and_Play' => 2,
    'Mental_Health_and_Behavior' => 3,
    'Physical_and_Emotional_Safety' => 4,
    'Supervision' => 5,
    'Creative_Literacy' => 6
  );

  // make sure there is a value for a category even it its not in our array above
  $order[$a] = (!isset($order[$a])) ? 100 : $order[$a];
  $order[$b] = (!isset($order[$b])) ? 100 : $order[$b];

  if ($order[$a] == $order[$b])
  {
    return 0;
  }

  return ($order[$a] < $order[$b]) ? -1 : 1;
}

// sort by first name
function sort_first_name ($a, $b)
{
  if (strtolower($a['first_name']) == strtolower($b['first_name'])) return 0;
  return strtolower($a['first_name']) < strtolower($b['first_name']) ? -1 : 1;
}

// function to display the uber manager's dashboard interface
function display_uber_manager_dashboard()
{
  global $current_user;
  global $wpdb;

  wp_get_current_user ();
  $user_id = $current_user->ID;
  $org_id = get_org_from_user ($user_id);
  $org = get_post ($org_id);
  $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
  $org_lrn_upon_id = get_post_meta ($org_id, 'lrn_upon_id', true); // the LU portal ID
  $data = compact ('org_id');

  echo '<h1>Uber Manager Administration</h1>';

  echo "<p>The Uber Administration panel is where you'll manage your organization's content & staff.</p>"
?>
  <div class="row">
    <div class="col">
      <a href="?part=copy_courses&amp;org_id=<?= $org_id ?>&user_id=<?= $user_id; ?>" onclick="load('load_courses')">
        <i class="fa fa-files-o fa-3x" aria-hidden="true"></i>
      </a>
    </div>
    <div class="col">
      <a href="?part=copy_courses&amp;org_id=<?= $org_id ?>&user_id=<?= $user_id; ?>" onclick="load('load_courses')">Copy Courses</a>
      <br>
      Copy your custom courses into any of your organization's camps
    </div>
  </div>
  <br>
<?php
  // Display a table of current Camps/managers/stats
  $umbrellaCamps = getUmbrellaCamps($org_id); // Lists of umbrella camps

  if ($umbrellaCamps->have_posts())
  { 

    /*
     * Create table heading
     */
    $userTableObj = new stdClass(); 
    $userTableObj->rows = array();
    $userTableObj->headers = array(
      'Camp Name' => 'left',
      'Director'=> 'left',
      'Actions'=> 'center'
    );

    // Get all umbrella camps, and add them into camps array.
    while ( $umbrellaCamps->have_posts() ) 
    {
      $umbrellaCamps->the_post(); 
      $org_id = get_the_ID(); // The org ID
      $camp_name = get_the_title(); // The camp name
      $args = array (
        'role__in' => array('manager'),
        'meta_key' => 'org_id',
        'meta_value' => $org_id,
        'number' => 1
      );
      $director = get_users($args); // get the user name associated with this org
      $director_name = 'John Doe';
      if ($director)
      {
        $director_name = $director[0]->first_name . " " . $director[0]->last_name;
        $user_id = $director[0]->ID;
      }

      // Create a table row.
      $userTableObj->rows[] = array($camp_name, $director_name, '<a href="/dashboard/?part=statistics&org_id='.$org_id.'&user_id='.$user_id.'" onclick="load(\'load_loading\')"><i class="fa fa-line-chart" aria-hidden="true"></i>Stats</a>&nbsp;&nbsp;&nbsp;<a href="/dashboard/?part=administration&org_id='.$org_id.'&user_id='.$user_id.'"><i class="fa fa-share" aria-hidden="true"></i>Admin</a>');

    }

    // Display the user's table
    CreateDataTable($userTableObj);
    echo '<div class="row">&nbsp;</div>';

  }
}


// function to display the umbrella manager's dashboard interface
function display_umbrella_manager_dashboard()
{
  global $current_user;
  global $wpdb;

  wp_get_current_user ();
  $user_id = $current_user->ID;
  $org_id = get_org_from_user ($user_id);
  $org = get_post ($org_id);
  $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
  $org_lrn_upon_id = get_post_meta ($org_id, 'lrn_upon_id', true); // the LU portal ID
  $data = compact ('org_id');

  echo '<h1>Umbrella Manager Administration</h1>';

  echo "<p>The Umbrella Administration panel is where you'll manage your organization's content & staff.</p>"
?>
  <div class="row">
    <div class="col">
      <a href="?part=copy_courses" onclick="load('load_courses')">
        <i class="fa fa-files-o fa-3x" aria-hidden="true"></i>
      </a>
    </div>
    <div class="col">
      <a href="?part=copy_courses" onclick="load('load_courses')">Copy Courses</a>
      <br>
      Copy your custom courses into any of your organization's camps
    </div>
  </div>
  <br>
<?php
  // Display a table of current Camps/managers/stats
  $umbrellaCamps = getUmbrellaCamps($org_id, 'regional_umbrella_group_id'); // Lists of regional umbrella camps

  if ($umbrellaCamps->have_posts())
  { 

    /*
     * Create table heading
     */
    $userTableObj = new stdClass(); 
    $userTableObj->rows = array();
    $userTableObj->headers = array(
      'Camp Name' => 'left',
      'Director'=> 'left',
      'Actions'=> 'center'
    );

    // Get all umbrella camps, and add them into camps array.
    while ( $umbrellaCamps->have_posts() ) 
    {
      $umbrellaCamps->the_post(); 
      $org_id = get_the_ID(); // The org ID
      $camp_name = get_the_title(); // The camp name
      $args = array (
        'role__in' => array('manager'),
        'meta_key' => 'org_id',
        'meta_value' => $org_id,
        'number' => 1
      );
      $director = get_users($args); // get the user name associated with this org
      $director_name = 'John Doe';
      if ($director)
      {
        $director_name = $director[0]->first_name . " " . $director[0]->last_name;
        $user_id = $director[0]->ID;
      }

      // Create a table row.
      $userTableObj->rows[] = array($camp_name, $director_name, '<a href="/dashboard/?part=statistics&org_id='.$org_id.'&user_id='.$user_id.'" onclick="load(\'load_loading\')"><i class="fa fa-line-chart" aria-hidden="true"></i>Stats</a>&nbsp;&nbsp;&nbsp;<a href="/dashboard/?part=administration&org_id='.$org_id.'&user_id='.$user_id.'"><i class="fa fa-share" aria-hidden="true"></i>Admin</a>');

    }

    // Display the user's table
    CreateDataTable($userTableObj);
    echo '<div class="row">&nbsp;</div>';

  }
}


// retrieve a list of all the umbrella camps for this org.
// @param $key string - the type of meta key we are looking for
function getUmbrellaCamps($org_id = 0, $key = 'umbrella_group_id')
{
  // check that we got an org id
  if(!$org_id)
  {
    return;
  }

  // verify that $key is only umbrella_group_id or regional_umbrella_group_id
  $valid_keys = array (
    'umbrella_group_id',
    'regional_umbrella_group_id'
  ); 
  if (!in_array($key, $valid_keys))
  {
    return;
  } 

  // get a list of orgs which have umbrella_group_id ($key) == org_id
  $args = array(
    'post_type' => 'org',
    'nopaging' => true,
    'meta_query' => array (
      array (
        'key' => $key,
        'value' => $org_id,
        'compare' => '='
      )
    )
  );
  $query = new WP_Query($args);

//  echo $query->request; // this shows the query if you ever need to see the SQL.

  return $query;
}

// verify whether or not this user is allowed to modify this page/subscription/view
// returns boolean true/false
function verifyUserAccess ()
{

  global $current_user;
  global $wpdb;

  // get the subscription ID if exists
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
  { 
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
  }
  else
  {
    $subscription_id = 0; // no subscription ID provided
  }


  // check if were dealing with an uber admin or a director
  if (current_user_can("is_uber_manager"))
  {
    // check that this uber admin has access to this portal
    if ($subscription_id)
    {
      // we have a subscription ID so just check that this uber admin is authorized to modify this subscription/org
      $sql = "SELECT * FROM " . TABLE_SUBSCRIPTIONS . " WHERE id = " . $subscription_id;
      $results = $wpdb->get_row ($sql);
      if ($results)
      {
        $manager_id = $results->manager_id;
        $org_id = $results->org_id;  

        // if its our own subscription, then we can modify, otherwise we need to check umbrella org below
        if ($current_user->ID == $manager_id)
        {
          return array ( 'status' => 1 );
        }

      }
      else
      {
        return array( 'status' => 0, 'message' => 'couldn\'t find the subscription' );
      }

    }
    else
    {
      // we dont have a subscription so we need to get org_id and user_id->manager_id
      if (isset($_REQUEST['org_id']) && isset($_REQUEST['user_id']))
      {
        // verify that the current uber/umbrella manager has access to this user
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $manager_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);
      }
      else
      {
        return array( 'status' => 0, 'message' => 'we didn\'t get an org_id and user_id' );
      }
    }

    // now using the org_id and manager_id see if this uber admin is allowed to access this org.
    // get the uber admin's org id (post id)
    $args = array(
      'post_type' => 'org',
      'author' => $current_user->ID
    );
    $posts = get_posts($args);
    if ($posts)
    {
      $umbrella_group_id = $posts[0]->ID;
    }
    else
    {
      return array( 'status' => 0, 'message' => 'couldn\'t get the uber admin\'s org_id' );
    }

    // get the user meta for the manager so we can compare the umbrella_group_id
    $user_meta = get_user_meta($manager_id);

    if ($user_meta)
    {
      if (isset($user_meta['umbrella_group_id']) && $user_meta['umbrella_group_id'][0] == $umbrella_group_id && $user_meta['org_id'][0] == $org_id)
      {
        // all good, proceed to get subscriptions and edit this org.
        $date = current_time('Y-m-d');
        $sql = "SELECT * FROM " . TABLE_SUBSCRIPTIONS . " WHERE ";
        $sql .= "org_id = ".$org_id." AND manager_id = ".$manager_id." AND library_id = ".LE_ID." AND status = 'active' AND start_date <= '$date' AND end_date >= '$date'";
        $results = $wpdb->get_row ($sql);
        if ($results)
        {
          // set the subscription ID and org ID
          $_REQUEST['subscription_id'] = $results->id;
          $_REQUEST['org_id'] = $org_id;

          return array ( 'status' => 1 );
        }
        else
        {
          return array( 'status' => 0, 'message' => 'couldn\'t get the subscription ID' );
        }
      }
      else
      {
        return array( 'status' => 0, 'message' => 'couldn\'t confirm this uber user is this user\'s uber admin.' );
      }
    }
    else
    {
      return array( 'status' => 0, 'message' => 'couldn\'t get the manager\'s meta info' );
    }


  }
  else if (current_user_can("is_umbrella_manager"))
  {
    // check that this umbrella manager has access to this portal
    if ($subscription_id)
    {
      // we have a subscription ID so just check that this umbrella admin is authorized to modify this subscription/org
      $sql = "SELECT * FROM " . TABLE_SUBSCRIPTIONS . " WHERE id = " . $subscription_id;
      $results = $wpdb->get_row ($sql);
      if ($results)
      {
        $manager_id = $results->manager_id;
        $org_id = $results->org_id;  

        // if its our own subscription, then we can modify, otherwise we need to check umbrella org below
        if ($current_user->ID == $manager_id)
        {
          return array ( 'status' => 1 );
        }

      }
      else
      {
        return array( 'status' => 0, 'message' => 'couldn\'t find the subscription' );
      }

    }
    else
    {
      // we dont have a subscription so we need to get org_id and user_id->manager_id
      if (isset($_REQUEST['org_id']) && isset($_REQUEST['user_id']))
      {
        // verify that the current uber/umbrella manager has access to this user
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $manager_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);
      }
      else
      {
        return array( 'status' => 0, 'message' => 'we didn\'t get an org_id and user_id' );
      }
    }

    // now using the org_id and manager_id see if this umbrella admin is allowed to access this org.
    // get the umbrella admin's org id (post id)
    $args = array(
      'post_type' => 'org',
      'author' => $current_user->ID
    );
    $posts = get_posts($args);
    if ($posts)
    {
      $regional_umbrella_group_id = $posts[0]->ID;
    }
    else
    {
      return array( 'status' => 0, 'message' => 'couldn\'t get the uber admin\'s org_id' );
    }

    // get the user meta for the manager so we can compare the regional_umbrella_group_id
    $user_meta = get_user_meta($manager_id);

    if ($user_meta)
    {
      if (isset($user_meta['regional_umbrella_group_id']) && $user_meta['regional_umbrella_group_id'][0] == $regional_umbrella_group_id && $user_meta['org_id'][0] == $org_id)
      {
        // all good, proceed to get subscriptions and edit this org.
        $date = current_time('Y-m-d');
        $sql = "SELECT * FROM " . TABLE_SUBSCRIPTIONS . " WHERE ";
        $sql .= "org_id = ".$org_id." AND manager_id = ".$manager_id." AND library_id = ".LE_ID." AND status = 'active' AND start_date <= '$date' AND end_date >= '$date'";
        $results = $wpdb->get_row ($sql);
        if ($results)
        {
          // set the subscription ID and org ID
          $_REQUEST['subscription_id'] = $results->id;
          $_REQUEST['org_id'] = $org_id;

          return array ( 'status' => 1 );
        }
        else
        {
          return array( 'status' => 0, 'message' => 'couldn\'t get the subscription ID' );
        }
      }
      else
      {
        return array( 'status' => 0, 'message' => 'couldn\'t confirm this uber user is this user\'s uber admin.' );
      }
    }
    else
    {
      return array( 'status' => 0, 'message' => 'couldn\'t get the manager\'s meta info' );
    }


  }
  else if (current_user_can("is_director"))
  {
    // make sure a subscription ID was passed in and this user has access to it.
    if ($subscription_id)
    {
      $sql = "SELECT * FROM " . TABLE_SUBSCRIPTIONS . " WHERE id = " . $subscription_id;
      $results = $wpdb->get_row ($sql);
      if ($results){
        // verify that the current user is the director for this subscription
        $manager_id = $results->manager_id;
        $user_id = $current_user->ID; // Wordpress user ID
        if ($user_id == $manager_id)
        {
          return array ( 'status' => 1 );
        }
        else
        {
          return array( 'status' => 0, 'message' => 'not the same user' );
        }
      }
      else
      {
        return array( 'status' => 0, 'message' => 'something went wrong and we couldn\'t find this subscription' );
      }
    }
    else
    {
      return array( 'status' => 0, 'message' => 'a camp director without a subscription ID' );
    }
  }

  return array( 'status' => 0, 'message' => 'You do not have the authority to modify this page.' ); // if got here then they have no authority to modify
}

/********************************************************************************************************
 * Assign a camp to an umbrella manager
 *******************************************************************************************************/
add_action('wp_ajax_assignCampUmbrellaManager', 'assignCampUmbrellaManager_callback'); 
function assignCampUmbrellaManager_callback() 
{
    if( isset ( $_REQUEST['user_id'] ) && isset ( $_REQUEST['regional_umbrella_group_id'] )  && isset ( $_REQUEST['org_id'] ) )
    {
      $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);
      $regional_umbrella_group_id = filter_var($_REQUEST['regional_umbrella_group_id'], FILTER_SANITIZE_NUMBER_INT);
      $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);

      if(!update_user_meta($user_id, 'regional_umbrella_group_id', $regional_umbrella_group_id))
      {
        add_user_meta($user_id, 'regional_umbrella_group_id', $regional_umbrella_group_id);
      }

      if(!update_post_meta($org_id, 'regional_umbrella_group_id', $regional_umbrella_group_id))
      {
        add_post_meta($org_id, 'regional_umbrella_group_id', $regional_umbrella_group_id);
      }

//      echo json_encode(array('status' => 0, 'message' => "user: $user_id regional_umbrella_group_id: $regional_umbrella_group_id"));
     echo json_encode(array('status' => 1));
    }
    else
    {
      echo json_encode(array('status' => 0, 'message' => 'Failed to assign camp to umbrella manager...'));
    }
  wp_die();
}

/* 
 * This triggers when any acf form is submitted.
 */
//add_action('wp_ajax_my_acf_save_post', 'my_acf_save_post');
add_action('acf/save_post' , 'my_acf_save_post', 20);
function my_acf_save_post( $post_id ) 
{
  // handles mail submission.
  if( isset($_REQUEST['part']) && $_REQUEST['part'] == FILE_IMPROVED_EMAIL_STAFF && isset($_REQUEST['subscription_id']) && isset($_REQUEST['target']))
  {
      // Variable declaration
    global $current_user;
    $user_id = $current_user->ID; // Wordpress user ID
    $sender_name = $current_user->user_firstname . " " . $current_user->user_lastname; // Recepient sender's name
    $sender_email = $current_user->user_email; // Recepient sender's name
    $org_id = get_org_from_user ($user_id); // Organization ID
    $data = compact("org_id", "sender_name", "sender_email");
    $portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
    $subscription_id=filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
    $target = filter_var($_REQUEST['target'],FILTER_SANITIZE_STRING); // Target. User selected option.
    $user_email_finish = array(); // Lists of email addresses that we already finish sending mail to
    $recepients = array(); // List of recepients
    $users = json_decode(stripslashes( html_entity_decode($_REQUEST['users_info'])) ); // Get the users information
error_log("users_info: " . json_encode($users));
    $campname = get_the_title($org_id); // the camp name
    $directorname = $current_user->display_name; // the directors name

    // Select the fields used for composing the e-mail message. The subject and composed message. 
    if($target == "all" || $target == "select-staff" || $target == "all-course")
    {
      // Get the subject and message template created by the user.
      $subject_template = get_field( "txt_subject_staff_members", $post_id ); // The e-mail subject
      $message_template = get_field( "wysiwyg_compose_message_staff_members", $post_id ); // The e-mail message
    }
    else if($target == "incomplete" || $target == "completed")
    {
      // Get the subject and message template created by the user.
      $subject_template = get_field( "txt_subject_incomplete_complete", $post_id ); // The e-mail subject
      $message_template = get_field( "wysiwyg_compose_message_incomplete_complete", $post_id ); // The e-mail message
    }
    else if($target == "nologin")
    {
      // Get the subject and message template created by the user.
      $subject_template = get_field( "txt_subject_yet_to_login", $post_id ); // The e-mail subject
      $message_template = get_field( "wysiwyg_compose_message_yet_to_login", $post_id ); // The e-mail message
    }
    else if($target == "staff-passwords")
    {
      // Get the subject and message template created by the user.
      $subject_template = get_field( "txt_subject_staff_passwrd", $post_id ); // The e-mail subject
      $message_template = get_field( "wysiwyg_compose_message_staff_passwrd", $post_id ); // The e-mail message
    }
    else if($target == "course-passwords")
    {
      // Get the subject and message template created by the user.
      $subject_template = get_field( "txt_subject_group_passwrd", $post_id ); // The e-mail subject
      $message_template = get_field( "wysiwyg_compose_message_group_passwrd", $post_id ); // The e-mail message
    }

    // Goes to each selected user. Compose and send the message.
    foreach($users as $user)
    {
      $name = $user->first_name . " " . $user->last_name; // User's full name
      $email = $user->email; // User's e-mail address
    
      $vars = array(
          'name' => $name,
          'email' => $email,
          'your_name' =>  $sender_name,
          'campname' => $campname,
          'directorname' => $directorname,
          'numvideos' =>  NUM_VIDEOS,
      );

      // check if the message contains the %%password%% key. If so, need to generate password link. Otherwise, no need.
      if(preg_match('/%%password%%/', $message_template) || preg_match('/%%logininfo%%/', $message_template))
      {
        // Generate a link to set a new password
        $user_data = get_user_by( 'email',  $email ); 
        $key = get_password_reset_key( $user_data );
        $set_new_password_link = '<a href="'.network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($email), 'login').'">Reset your password</a>';
        $vars['password'] = $set_new_password_link;
        $vars['logininfo'] = "Username: $email<br>Password: $set_new_password_link";
      }

      // Need to add extra breakspace, cause on the ACF Wysiwyg Editor uses <p></p> without <br> when adding a line break or <enter>
//      $message = str_replace("</p>","</p><br/>",$message_template); 
      /* Replace %%VARIABLE%% using vars*/
      $message = $message_template; // reset the message to what the director wrote before inserting variables for specific user.
      foreach($vars as $key => $value)
      {
        $message = preg_replace('/%%' . $key . '%%/', $value, $message);
      }
      // Recepient information
      $recepient = array (
          'email' => $email,
          'message' => $message,
          'subject' => $subject_template
      );
      array_push($recepients, $recepient);
    }

    // add emails to pending emails table
    $result = addPendingEmails($org_id,$sender_name,$sender_email,$recepients);

    if(is_array($result))
    {
      // display the errors we got
    }
    else
    {
      wp_redirect(site_url('/dashboard?part=mass_mail&subscription_id='.$subscription_id."&org_id=".$org_id."&processing=1&max=".count($recepients)));
      exit;

    }

    // now send an email to all the users.
    //sendMail( 'massmail', $recepients, $data ); // Send email message to the recepients.
    
  }
}

/****************
 * Adds email recipient and data to the temp emails table
 */
function addPendingEmails($org_id = 0, $sender_name = '', $sender_email = '', $recepients = array())
{
  global $wpdb;
  $remove_chars = array("'", '"');
  $errors = array();
  
  $failed = 0;

  foreach ($recepients as $recepient)
  {
    $data=array(
          'org_id'=>$org_id,
          'sender_name'=>$sender_name,
          'sender_email'=>$sender_email,
          'email'=>$recepient['email'],
          'subject'=>$recepient['subject'],
          'message'=>$recepient['message']
    );
    $result = $wpdb->insert(TABLE_PENDING_EMAILS, $data);

    // check if there was an error inserting the email into the DB
    if (!$result)
    {
      $failed = 1;
      array_push($errors, array 
        (
          'email' => $recepient['email'],
          'error_message' => $wpdb->print_error
        )
      );
    }
  }

  // if failed return false
  if ($failed)
  {
    return array('status' => 0, 'errors' => $errors);
  }
  return true;
}

// Sort associative array by the field parameter ie. first_name using uasort. Example usage: uasort($users, 'sort_array_by_first_name');
function sort_array_by_first_name($a = '', $b = '')
{
  return strnatcmp($a['first_name'], $b['first_name']);
}

/*******************************************************************************
* Add Subscription Upgrade in wordpress database
* int $subscription_id - The Subscription ID
* int $user_id - User's wordpress ID
* @param array $data - Subscription upgrade information 
*********************************************************************************/
function addSubscriptionUpgrade ($subscription_id = 0, $data = array())
{
  extract($data);
  /**
    * Variables required in $data
    * int $org_id - the organization id
    * float $price - the amount we charged
    * int $ordered_accounts - The number of accounts to upgrade
    * int $user_id - Wordpress User ID
    * string $method - Method of payment. Stripe or Free
    * string $discount_note - The discount notes
    * string $other_note - Other notes
    * int $rep_id - The rep ID who made the sale.
    * string $trans_id - the transaction ID from stripe
   **/

  // check to make sure we have valid inputs
  if ($subscription_id <= 0 || $price < 0 || $ordered_accounts <= 0 || $user_id <= 0 || empty($method))
  {
    $result['success'] = false;
    $result['errors'] = 'addSubscriptionUpgrade error: Couldn\'t add row to WP upgrades because of missing parameters';
  }
  $rep_id =  ( $rep_id ? $rep_id : 0);
  global $wpdb;

  $date = current_time('Y-m-d'); // Date
  // SQL Query
//  $sql = "INSERT INTO " . TABLE_UPGRADE_SUBSCRIPTION . " (date, subscription_id, price, accounts, user_id, method, discount_note, other_note, rep_id) VALUES (\"" . $date . "\" , " . $subscription_id . ", \"" . $price . "\", \"" . $ordered_accounts . "\", \"" . $user_id . "\", \"" . $method . "\", \"" . $discount_note . "\", \"" . $other_note . "\", \"" . $rep_id . "\")";
  $sql = "INSERT INTO " . TABLE_UPGRADE_SUBSCRIPTION . " (date, org_id, subscription_id, price, accounts, user_id, method, discount_note, other_note, rep_id, trans_id) VALUES ('$date', $org_id, $subscription_id, $price, $ordered_accounts, $user_id, '$method', '$discount_note', '$other_note', $rep_id, '$trans_id')";

  if( $wpdb->query ($wpdb->prepare ($sql)) )
  {
    $result['data'] = 'success';
    $result['success'] = true;
  }
  else
  {
    $result['success'] = false;
    $result['errors'] = 'addSubscriptionUpgrade error: ' . $wpdb->print_error();
  }
  return $result;
}

//This function updates the subscription
function updateSubscriptionSettings($id = 0, $field = '', $value = 0)
{
  global $wpdb;
  
  if( $id > 0 || empty($field) )
  {
    return null;

  }
  $sql = "UPDATE " . TABLE_SUBSCRIPTIONS . ' SET ' . $field . ' = "' . $value . '" WHERE id = ' . $id;

  if( $wpdb->query ($wpdb->prepare ($sql)) )
  {
    $result['data'] = 'success';
    $result['success'] = true;
  }
  else
  {
    $result['display_errors'] = 'failed';
    $result['success'] = false;
    $result['errors'] = $wpdb->print_error();
  }
}

/*
 * Get the upgrades this subscription year based on the subscription id
 * return the upgrade subscription information
 */
function getUpgrades ($subscription_id = 0, $start_date = '0000-00-00', $end_date = '0000-00-00') 
{
    global $wpdb;
    $sql = "SELECT * from " . TABLE_UPGRADE_SUBSCRIPTION;
    if ($subscription_id > 0)
    {
        $sql .= " WHERE subscription_id = $subscription_id";
    }
    else if($start_date != "0000-00-00" && $end_date != "0000-00-00")
    {
      $sql .= "  WHERE date >= '$start_date' AND date <= '$end_date'";
    }
    $results = $wpdb->get_results ($sql);
    return $results;
}

/******************************************
 * Get certificates by ID
 * $user_id = the WP User ID
 * $certificate_id = Ther certificate ID
 * $course_id = the course ID
 ******************************************/ 
function getCertificates($user_id = 0, $certificate_id = 0, $course_id = 0) 
{
  if( $user_id == 0 )
  {
    return false;
  }
  global $wpdb;
  $sql = "SELECT * FROM " . TABLE_CERTIFICATES . " WHERE user_id = " . $user_id;
  if ($certificate_id > 0)
  {
    $sql .= " AND id = " . $certificate_id;
  }
  else if($course_id > 0)
  {
    $sql .= " AND course_id = " . $course_id;
  }
  $result = ($certificate_id == 0) ? $wpdb->get_results ($sql, ARRAY_A) : $wpdb->get_row ($sql, ARRAY_A);
  return $result;
}

/******************************************
 * Save a certificate
 * $user_id = the WP User ID
 * $status = The status of this certificate. Etiher, Deferred or pending. 
 ******************************************/ 
function setCertificate( $user_id = 0, $data = array() )
{
  extract($data);
  /*
   * Variables required in $data
   * course_id - The Learnupon Course ID
   * course_name - The course name
   * filename - Certificate file name
   * status - conferred / pending
   * user_id - The wordpress user id of the user.
   * date_enrolled - enrolled date.
   */
  if($user_id <= 0)
  {
    return false;
  }
  global $wpdb;
  $date =  date('Y-m-d h:i:s'); // Current day and time.
  $sql =  "INSERT INTO " . TABLE_CERTIFICATES . " (user_id, course_id, course_name, filename, datecreated, date_enrolled, status) 
          VALUES ($user_id, $course_id, '$course_name', '$filename', '$date', '$date_enrolled', '$status')";
  $result = $wpdb->query ($sql);
}

/******************************************
 * Save a certificate
 * $user_id = the WP User ID
 * $status = The status of this certificate. Etiher, Deferred or pending. 
 ******************************************/ 
function setCertificateSyllabus( $user_id = 0, $data = array() )
{
  extract($data);
  /*
   * Variables required in $data
   * course_id - The Course ID
   * course_name - The course name
   * module_titles - All modules title in JSON Format
   */
  if($user_id <= 0)
  {
    return false;
  }
  global $wpdb;
  $sql =  "INSERT INTO " . TABLE_CERTIFICATES_SYLLABUS . " (user_id, course_id, course_name, modules) 
          VALUES ($user_id, $course_id, '$course_name' , '$module_titles')";
  $result = $wpdb->query ($sql);
}

/******************************************
 * Get certificates Syllabus by user id
 * $user_id = the WP User ID
 ******************************************/ 
function getCertificatesSyllabus($user_id = 0, $course_id = 0) 
{
  if( $user_id <= 0 )
  {
    return false;
  }
  global $wpdb;
  $sql = "SELECT * FROM " . TABLE_CERTIFICATES_SYLLABUS . " WHERE user_id = " . $user_id;
  if($course_id > 0)
  {
    $sql .= " AND course_id = " . $course_id;
  }
  $result = ($course_id > 0) ? $wpdb->get_row ($sql, ARRAY_A) : $wpdb->get_results ($sql, ARRAY_A);
  return $result;
}

// This function gets the index position of a line based on the character limit
function get_line($words_array, $index, $char_limit) 
{
  $line = '';
  while ( array_key_exists($index, $words_array) && strlen($line . " " . $words_array[$index]) < $char_limit ) 
  {
    $line = $line." " . $words_array[$index];
    $index++;
  }
  $response_obj = new stdClass();
  $response_obj->line = trim($line);
  $response_obj->index = $index;
  return $response_obj;

}

// This function calculates the character limit based on the width and the max width character (W)
function calc_char_limit($maxwidth, $im, $draw ) 
{
  $fontInfo = $im->queryFontMetrics($draw, "W");
  return ( ($maxwidth/($fontInfo['textWidth'] -  8)));
}

// This function calculates the ending index position for each line and returns it in an array
function calc_line_text($words_array, $chars_per_line) 
{
  $text = array();  // Stores the text in lines in array format
  $index = 0; // Stores the current index position we are at in the words_array array
  $line_count = 0;
  while ( count($words_array) > ($index+1) ) 
  {
    $line_count++;
    $response = get_line($words_array, $index, $chars_per_line);
    $text[$line_count] = $response->line;
    $index =  $response->index;     
  }
  
  // If total number of line(meaning count($text)) is greater than 4
  // then we don't want to display them as it will be jumbled up
  // Max number of lines should be 4
  if ($line_count > 4) 
  {
    return false;
  } 
  else 
  {
    return $text;
  }
}

//adds users to a temperory table to process later
function addPendingUsers ($staff_data = array(), $org_id = 0, $message = '', $subject = '', $isEmail = 0, $directorname = '')
{  
  global $wpdb;
  $remove_chars = array("'", '"');

  foreach ($staff_data as $staff)
  {
    $first_name = str_replace($remove_chars, "", trim($staff[0]));
    $last_name = str_replace($remove_chars, "", trim($staff[1]));
    $email = trim($staff[2]);
    $password = trim($staff[3]);
    $courses = json_encode(array(trim($staff[4]), trim($staff[5]), trim($staff[6]), trim($staff[7])));
    $variables = json_encode(compact("first_name", "last_name", "directorname"));
    $sql = "INSERT INTO " . TABLE_PENDING_USERS . " (org_id, variables, email, password, courses, subject, message, isEmail) 
    VALUES 
    ($org_id, '$variables', '$email', '$password', '$courses', '".addslashes($subject)."', '" . addslashes($message) . "', $isEmail)";
    $result = $wpdb->query ($sql);
//error_log("addPendingUsers: added user into DB: $sql");
    if(!$result)
    {
      return false;
    }
  }

  return true;
}

//triggers mass mailing from ajax.
add_action('wp_ajax_mass_mail_ajax', 'mass_mail_ajax_callback');
function mass_mail_ajax_callback()
{

    $org_id = (isset($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $result = processEmails(PENDING_EMAILS_LIMIT, $org_id);

    if ($org_id == 0)
    {
      return $result;
    }

    echo json_encode($result);
    wp_die();
    
}

//this function is called by a cron job every hour to process left over emails (PENDING_EMAILS_CRON_TIME_LIMIT hours or older)
function processEmailsCron()
{
  global $wpdb;

  $sql = "SELECT count(*) as count FROM " . TABLE_PENDING_EMAILS . " WHERE time < DATE_SUB(NOW(), INTERVAL ".PENDING_EMAILS_CRON_TIME_LIMIT." HOUR) ORDER BY id asc";
  $result = $wpdb->get_row($sql)->count;

  error_log("ProccessEmailsCron started");

  if($result > 0)
  {
    for($i = 0; $i < $result; $i += PENDING_EMAILS_CRON_LIMIT)
    {
      $result = processEmails(PENDING_EMAILS_CRON_LIMIT);
      if(!$result['status'])
      {
        break;
      }
    }
  }

  error_log("ProccessEmailsCron finished");

}

// processes the firt PENDING_EMAILS_LIMIT emails from the temperory table
function processEmails ($limit = PENDING_EMAILS_LIMIT, $org_id = 0)
{
  global $wpdb;
  $data=compact('org_id');
  $sent_emails = array();

  // check if we're calling the function from cron or a user. If cron then org_id = 0 and we should look for records older than PENDING_USERS_CRON_TIME_LIMIT hours ago 
  if($org_id == 0)
  {
    $sql = "SELECT * FROM " . TABLE_PENDING_EMAILS . " WHERE time < DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY id asc limit " . $limit;
  }
  else   //means this function is being called from mass email page and we should target the specific org_id
  {
    $sql = "SELECT * FROM " . TABLE_PENDING_EMAILS . " WHERE org_id = " . $org_id . " ORDER BY id asc limit " . $limit;
  }

  $recipients = $wpdb->get_results($sql, ARRAY_A);
  $results = sendMail('massmail', $recipients, $data);

  if(isset($results['status']) && $results['status'])
  {
    foreach($recipients as $recipient)
    {
      $sql = "DELETE FROM " . TABLE_PENDING_EMAILS . " WHERE id = " . $recipient['id'];
      $wpdb->query ($sql);

      // create a list of successfully sent emails
      array_push($sent_emails, '<i class="fa fa-check" aria-hidden="true"></i>' . $recipient['email'] . '<br>');
    }
    $results['sent_emails'] = $sent_emails;
  }
  else if(isset($results['status']) && !$results['status'])
  {
    foreach($recipients as $recipient)
    {
      $sql = "DELETE FROM " . TABLE_PENDING_EMAILS . " WHERE id = " . $recipient['id'];
      $wpdb->query ($sql);

      // create a list of successfully sent emails
      array_push($sent_emails, '<br><i class="fa fa-times" aria-hidden="true"></i><b>' . $recipient['email'] . '</b><br><br>');
    }
    $results['sent_emails'] = $sent_emails;
  }
  return $results;
}

//this function is called by a cron job every hour to process left over users from spreadsheet (PENDING_USERS_CRON_TIME_LIMIT hours or older)
function processUsersCron()
{
  global $wpdb;

  $sql = "SELECT count(*) as count FROM " . TABLE_PENDING_USERS . " WHERE time < DATE_SUB(NOW(), INTERVAL ".PENDING_USERS_CRON_TIME_LIMIT." HOUR) ORDER BY id asc";
  $result = $wpdb->get_row($sql)->count;

  if($result>0)
  {
    for($i=0;$i<$result;$i+=PENDING_USERS_CRON_LIMIT)
    {
      $result = processUsers(PENDING_USERS_CRON_LIMIT);
      if(!$result['status'])
      {
        break;
      }
    }
  }
}

//processes the firt PENDING_USERS_LIMIT users from the temperory table
function processUsers ($limit = PENDING_USERS_LIMIT, $org_id = 0)
{
  global $wpdb;

  // check if we're calling the function from cron or a user. If cron then org_id = 0 and we should look for records older than PENDING_USERS_CRON_TIME_LIMIT hours ago 
  if($org_id == 0)
  {
    $sql = "SELECT * FROM " . TABLE_PENDING_USERS . " WHERE time < DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY id asc limit " . $limit;
  }
  else   //means this function is being called from spreadsheet upload page and we should target the specific org_id
  {
    $sql = "SELECT * FROM " . TABLE_PENDING_USERS . " WHERE org_id = " . $org_id . " ORDER BY id asc limit " . $limit;
  }
  $staff_data = $wpdb->get_results($sql);

  /****************************************************************
   * This process the savings of the user accounts 
   * into WP User Database and create accounts in LU
   ****************************************************************/
  $recepients = array(); // List of recepients
  $emailError = '';
  $org_id = 0;        // the staff's org id
  $isEmail;             //boolean indicating whether we should email the users or not
  $subscription_id;     //subscription id
  $has_error = false;   // Boolean indication if the process has an error
  $has_user_error = false; // Boolean indicator for individual user error.
  $import_status = ''; // will contain a report of import statuses ie. the errors.
  foreach($staff_data as $staff)
  {
    $has_user_error = false;
    // Create New Account
    $variables = json_decode($staff->variables, true);          //json decode turn json object to its original datatype (array) and 'true' makes sure the returned data is an associative array
    extract($variables); //first_name, last_name, directorname
    $email = $staff->email; // User e-mail Address
    $password = ($staff->password == '') ? wp_generate_password() : $staff->password; // User Password, generate one if the director didnt include one.
    $org_id = $staff->org_id; // staff org id
    $message = stripslashes($staff->message);
    $isEmail = $staff->isEmail; //boolean to see if email should be sent
    $subject = stripslashes($staff->subject); //subject of the email
    $portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
    $data = compact("org_id", "first_name", "last_name", "email", "password");
    $courses = array_filter(json_decode($staff->courses)); // the courses to enroll the user into. array filter removes empty values. json decode turns the json object into its original data type (array in this case)
    
    // check if user exists in WP, if yes make sure they are in the same org. 
    if ( email_exists($email) )
    {
      $staff_id = get_user_by('email', $email)->ID;
      if ( get_user_meta($staff_id,'org_id', true) == $org_id )
      {
        // If in same org check if LU user exists by getting user meta data. 
        if(get_user_meta($staff_id, 'lrn_upon_id', true))
        {
          // If yes, enroll into courses.
          $result2 = enrollUserInCourses($portal_subdomain, $courses, $org_id, $email);
          if (isset($result2['status']) && !$result2['status'])
          {
            // ERROR in enrolling user
            $has_error = true;
            $has_user_error = true;
//            echo "<p>ERROR: Could not enroll $email into one or more courses. ".$result2['message']."</p>";
            $import_status .= "$email - ERROR: User exists in WPLU but couldnt enroll into course: ".$result2['message']."<br>";
          }
          else
          {
            // success
            $import_status .= "$email - SUCCESS: enrolled in course<br>";
          }
        }
        else
        {
          // if user doesnt exist in LU, create LU user and link to WP user.
          $result = createWpLuUser($portal_subdomain, $data, false, true, 'student'); // Create LU user
          if (isset($result['success']) && $result['success'])
          {
            // enroll user in courses 
            $result2 = enrollUserInCourses($portal_subdomain, $courses, $org_id, $email);
            if (isset($result2['status']) && !$result2['status'])
            {
              // ERROR in enrolling user
              $has_error = true;
              $has_user_error = true;
//              echo "<p>ERROR: Could not enroll $email into one or more courses. ".$result2['message']."</p>";
              $import_status .= "$email - ERROR: User exists in WP. Created in LU but couldnt enroll into course: ".$result2['message']."<br>";
            }
            else
            {
              // success
              $import_status .= "$email - SUCCESS: enrolled in course<br>";
            }
          }
          else
          {
            // ERROR in creating user
            $has_error = true;
            $has_user_error = true;
//            echo "<p>ERROR: Could not create user: $email ".$result['message']."</p>";
            $import_status .= "$email - ERROR: Could not create user: ".$result['message']."<br>";
          }
        }
      }
      else
      {
        // ERROR: WP user exists but in a different org.
        $has_error = true;
        $has_user_error = true;
//        echo "<p>ERROR: This user, $email, already exists but is assigned to a different organization.</p>";
        $import_status .= "$email - ERROR: This user, already exists but is assigned to a different camp.<br>";
      }
    }
    else
    {
      // if user doesnt exist in WP, create user in WP and LU
      $result = createWpLuUser($portal_subdomain, $data, true, true, 'student'); // Create WP and LU user

      if (isset($result['success']) && $result['success'])
      {
        // enroll user in courses
        $result2 = enrollUserInCourses($portal_subdomain, $courses, $org_id, $email);
        if (isset($result2['status']) && !$result2['status'])
        {
          // ERROR in enrolling user
          $has_error = true;
          $has_user_error = true;
//          echo "<p>ERROR: Could not enroll $email into one or more courses. ".$result2['message']."</p>";
          $import_status .= "$email - ERROR: Cerated user in WPLU but couldnt enroll into course: ".$result2['message']."<br>";
        }
        else
        {
          // it succeeded
          $import_status .= "$email - SUCCESS: enrolled in course<br>";
        }
      }
      else
      {
        // ERROR in creating user
        $has_error = true;
        $has_user_error = true;
//        echo "<p>ERROR: Could not create user: $email ".$result['message']."</p>";
        $import_status .= "$email - ERROR: User didnt exist but could not create user: ".$result['message']."<br>";
      }
    }
    
    /************************************************************
    * Check if the camp director wants to send the users an email 
    * compose and create recepient with subject and message
    *************************************************************/
    if($isEmail != 0 && !$has_user_error) 
    {
      $loginInfo = 'Username: ' . $email . '<br/>'; // Login Information
      $loginInfo .= 'Password: ' . $password;
      $name = $first_name . ' ' . $last_name;
      $campname = get_the_title($org_id); // the camp name
      $vars = array(
          'name' => $name,
          'email' => $email,
          'your_name' => $directorname,
          'logininfo' => $loginInfo,
          'directorname'  =>  $directorname,
          'campname'  =>  $campname,
          'numvideos' =>  NUM_VIDEOS,
      );

      /* Replace %%VARIABLE%% using vars*/
      foreach($vars as $key => $value)
      {
        $message = preg_replace('/%%' . $key . '%%/', $value, $message);
      }

      $recepient = array (
          'name' => $name,
          'email' => $email,
          'message' => $message,
          'subject' => $subject
      );
      array_push($recepients, $recepient);
    }

    $sql = "DELETE FROM " . TABLE_PENDING_USERS . " WHERE id = " . $staff->id;
    $wpdb->query ($sql);
  } // End of foreach loop

  $sent = 0; // Initialize whether the emails were sent or not
  if($isEmail != 0)
  {
    $sent = 1;
    // Send the email message to all recepients
    $target = "create_account";
    $response = sendMail( $target, $recepients, $data );
    // Check for error.
    if(isset($response['status']) && $response['status'] == 0)
    {
      $emailError .= "Error: " . $response['message'];
      $sent = 0;
    }
  }
  $final_result['status'] = false;

  // check if there were any errors, if so dont redirect with success message.
  if( isset($emailError) && !empty($emailError) )
  {
//    echo "<p>$emailError</p>";
    $import_status .= "Email " . $emailError . "<br>";
    // continue with the rest of the user creation.
    $final_result['status'] = true;
    $final_result['org_id'] = $org_id;
    $final_result['sent'] = 0;
    $final_result['import_status'] = $import_status;
    return $final_result;
  }
  else if (!$has_error) // no email error or other errors
  {
    //need these variables for redirection
    $final_result['status'] = true;
    $final_result['org_id'] = $org_id;
    $final_result['sent'] = $sent;
    $final_result['import_status'] = $import_status;
    return $final_result;
  }
  else
  {
//    echo "<p><strong>There were a few errors while processing your request. All errors have been displayed above. Users without errors have been created. Users with errors have not and you will need to create them manually.</strong></p>";
    $import_status .= "Email Error: Users without errors have been created. Users with errors have not and you will need to create them manually.<br>";
    // continue with the rest of the user creation.
    $final_result['status'] = true;
    $final_result['org_id'] = $org_id;
    $final_result['sent'] = 0;
    $final_result['import_status'] = $import_status;
    return $final_result; 
  }
}

//Create a new forum with no topics and no replies
function eot_bbp_create_initial_content( $args = array() ) 
{

	// Parse arguments against default values
	$r = bbp_parse_args( $args, array(
		'forum_parent'  => 0,
		'forum_status'  => 'publish',
		'forum_title'   => __( 'Forum', 'bbpress' ),
		'forum_content' => __( 'Forum chit-chat', 'bbpress' ),
	), 'create_initial_content' );

	// Create the initial forum
	$forum_id = bbp_insert_forum( array(
		'post_parent'  => $r['forum_parent'],
		'post_status'  => $r['forum_status'],
		'post_title'   => $r['forum_title'],
		'post_content' => $r['forum_content']
	) );

	return array('forum_id' => $forum_id);
}

//Customize bbpress breadcrumbs
function mycustom_bb_breadcrumb_options() 
{
  $args['before']  = '<div class="bbp-breadcrumb"><p><span class="bbp-breadcrumb-text"><a href="/dashboard">My Dashboard</a></span><span class="bbp-breadcrumb-sep"> › </span>';
	// Home - default = true
	$args['include_home']    = false;
	// Forum root - default = true
	$args['include_root']    = false;
	// Current - default = true
	$args['include_current'] = true;

	return $args;
}

add_filter('bbp_before_get_breadcrumb_parse_args', 'mycustom_bb_breadcrumb_options' );

//Prevent users from accesssing other organization's forums
function default_bbpress_to_org()
{
  if(is_user_logged_in() && is_bbpress() )
  { 
    $current_forum_id = bbp_get_forum_id();
    global $current_user;
    $user_id = $current_user->ID;
    $org_id = get_org_from_user($user_id);
    if($current_forum_id != get_post_meta($org_id, "org_forum_id", true)){
      wp_redirect(home_url() . "/dashboard/");
      exit();
    }
    else
    {
      //echo $current_forum_id == get_post_meta($org_id, "org_forum_id", true);
    }
  }
  elseif(is_bbpress())
  {
    wp_redirect(home_url());
    exit();
  }
}

add_action( 'wp', 'default_bbpress_to_org' );
add_filter( 'bbp_get_author_link', 'remove_author_links', 10, 2);
add_filter( 'bbp_get_reply_author_link', 'remove_author_links', 10, 2);
add_filter( 'bbp_get_topic_author_link', 'remove_author_links', 10, 2);

//Remove BB press Author Links
function remove_author_links($author_link, $args) 
{
	$author_link = preg_replace(array('{<a[^>]*>}','{}'), array(" "), $author_link);
	return $author_link;
}

/******************************
 * Boolean if(org_has_maxed_staff())
 */
function org_has_maxed_staff($org_id,$subscription_id)
{
            $subscription = getSubscriptions($subscription_id,0,1); // Subscription details
            $staff_credits = $subscription->staff_credits; // The staff credits
            // Add upgrade number of staff
            $upgrades = getUpgrades ($subscription_id);
            if($upgrades)
            {
                foreach($upgrades as $upgrade)
                {
                    $staff_credits += $upgrade->accounts;
                }
            }
            $response = getEotUsers($org_id);
            if ($response['status'] == 1)
            {
            $users = $response['users'];
            $learners = filterUsers($users, 'learner'); // only the learners
            }
            else
            {
                    $users = array();
                    $learners = array();
            }
            $num_staff = count($learners); // Number of staff for this organization
            // Check if the user has enough credits to add more staff members
            if( $num_staff >= $staff_credits )
            {
                return true;
            }else{
                return false;
            }
}

/**
 * Get all the data of a specific course present in the portal
 *  @param int course_id - the LU course ID
 *
 *  @return course array()
 */
function getCourse($course_id) 
{
global $wpdb; 
$course=$wpdb->get_row("SELECT * FROM " . TABLE_COURSES . " WHERE id = $course_id", ARRAY_A);
return $course;
}

/**
 * Enroll the user into the course base on email address and course name
 *
 * @param string $email - e-amil of the user
 * @param string $portal_subdomain - The subdomain name of the portal
 * @param array $data - user data
 **/
function enrollUserInCourse($email = '', $portal_subdomain = DEFAULT_SUBDOMAIN, $data) 
{
    extract($data);
    /*
    * Variables required in $data
    * org_id - the organization ID
    * course_name - name of the course the user will be enrolled to
     * course_id
    */
   //error_log(Json_encode($data));
    if($email == "")
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: invalid user email address.");

    if($course_id == null)
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: no course name supplied.");
        $user=  get_user_by('email', $email);
        global $wpdb;
        // Save enrollments to the database.
        $insert = $wpdb->insert(
          TABLE_ENROLLMENTS, 
          array( 
            'course_id' => $course_id, 
            'email' => $email,
            'org_id' => $org_id,
            'status' => 'not_started',
            'user_id' => $user->ID
          ), 
          array( 
            '%d', 
            '%s', 
            '%d',
            '%d',
            '%d'
        ));
    

    //checks for errors when creating enrollment
    if($insert===FALSE) 
    {
        return array('status' => 0, 'message' => "Error in enrollUserInCourse");
    }    
    else
    {
        return array('status' => 1);
    } 
}
