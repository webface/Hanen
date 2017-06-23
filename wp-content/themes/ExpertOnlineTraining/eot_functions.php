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
	$inc_library = ($library_id > 0) ? " where `ID` = " . $library_id . " " : "";
	$sql .= $inc_library;
	$results = ($library_id > 0) ? $wpdb->get_row ($sql) : $wpdb->get_results ($sql);
	return $results;
}

/*
 * Get the library based on the library id
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
    $sql = "SELECT * from ".TABLE_LIBRARY." WHERE ID = $library_id";
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
    
    if($start_date != "0000-00-00" && $end_date != "0000-00-00")
    {
      $sql .= " AND date >= '$start_date' AND date <= '$end_date'";
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
function org_has_maxed_staff($org_id = 0, $subscription_id = 0)
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
  
  $response = getEotUsers($org_id); // gets the users for the org
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
      return true; // no more staff credits
  }
  else
  {
      return false; // can add more staff
  }
}

/**
 * Get all the data of a specific course present in the portal
 *  @param int course_id - the course ID
 *
 *  @return course array() - an array of course data
 */
function getCourse($course_id = 0) 
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
    * course_id - the ID of the course to enroll the user into
    */
    if($email == "")
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: invalid user email address.");

    if($course_id == null)
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: no course id supplied.");

    $user = get_user_by('email', $email);
    global $wpdb;

    // Save enrollments to the database.
    $insert = $wpdb->insert(
      TABLE_ENROLLMENTS, 
      array( 
        'course_id' => $course_id, 
        'email' => $email,
        'user_id' => $user->ID,
        'org_id' => $org_id,
        'status' => 'not_started'
      ), 
      array( 
        '%d', 
        '%s', 
        '%d',
        '%d',
        '%s'
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

//get users in an organization
function getEotUsers($org_id = 0, $role = 'student'){
    
    if (!$org_id)
    {
      return array ('status' => 0, 'message' => 'No org id specified');
    }

    $users_info = get_users( 
        array(
            'meta_key' => 'org_id',
            'meta_value' => $org_id,
            'role' => $role
        )
    );

    $learners = array(); // Lists of learners.

    if(!empty($users_info) && count($users_info) > 0)
    {
      foreach ($users_info as $user_info) 
      {
        $user = array();
        $user['first_name'] = get_user_meta ( $user_info->id, "first_name", true);
        $user['last_name'] = get_user_meta ( $user_info->id, "last_name", true);
        $user['email'] = $user_info->user_email;
        $user['id'] = $user_info->ID;
//        $user['user_type'] = 'learner';  // @TODO remove if not used
        array_push($learners, $user);
      }
    }
      return array('status' => 1, 'users' => $learners);
}

 /**
 * Creates a course for the org
 *
 * @param string $course_name - the name of the course
 * @param string $org_id - Organization ID
 * @param array $data - user data
 * @param boolean $copy - to copy the course modules or not
 * @param $copy_course_id - id of course to copy from
 */
function createCourse($course_name = '', $org_id = 0, $data = array(), $copy = 0, $copy_course_id = 0) {
    extract($data);
    /*
     * Variables required in $data
     * user_id - the wordpress/EOT userID 
     * course_due_date - OPTIONAL - the due date of the course
     * course_description - the course description.
     * subscription_id - the subscription id
     */
    global $wpdb;
    if($course_name == "") 
    {
      return array('status' => 0, 'message' => "createCourse error: The course name cannot be blank.");
    }
    else if (!isset($org_id) || $org_id <= 0 || empty($org_id))
    {
      return array('status' => 0, 'message' => "createCourse error: The organization ID cannot be blank.");
    }
    else if (!isset($user_id) || $user_id <= 0 || empty($user_id))
    {
      return array('status' => 0, 'message' => "createCourse error: The Owner ID cannot be blank.");
    }
    else if (!isset($subscription_id) || $subscription_id <= 0 || empty($subscription_id))
    {
      return array('status' => 0, 'message' => "createCourse error: The Subscription ID cannot be blank.");
    }

    
    // filter user input and make sure parameters are included
    $course_name = trim(filter_var($course_name, FILTER_SANITIZE_STRING));
    if(isset($course_description))
    {
        $course_description = trim(filter_var($course_description, FILTER_SANITIZE_STRING));
    }
    else if($copy && $copy_course_id > 0) // get course description from table cause were copying a course
    {
        $copy_course_id = filter_var($copy_course_id, FILTER_SANITIZE_NUMBER_INT);
        $course = $wpdb->get_row("SELECT * FROM ".TABLE_COURSES." WHERE ID = $copy_course_id",OBJECT);
        $course_description = $course->course_description;
    }
    
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    $subscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);
    $course_due_date = (isset($course_due_date)) ? $course_due_date : '1000-01-01 00:00:00';
   
    $insert = $wpdb->insert( 
      TABLE_COURSES, 
      array( 
        'course_name' => $course_name, 
        'course_description' => $course_description,
        'org_id' => $org_id,
        'owner_id' => $user_id,
        'due_date_after_enrollment' => $course_due_date,
        'subscription_id'=>$subscription_id
      ), 
      array( 
        '%s', 
        '%s', 
        '%d',
        '%d',
        '%s',
        '%d' 
      ));

    //checks for errors when create the course
    if( !$insert ) 
    {
      return array('status' => 0, 'message' => "createCourse error:" . $wpdb->last_error);
    }    
    else if( $wpdb->insert_id ) // no errors when inserting
    {
      if(!$copy) // return the newly created course ID
      {
          return array('status' => 1, 'id' => $wpdb->insert_id);
      }
      else if($copy && $copy_course_id > 0) // copy all the modules from the course
      {
        $sql = "INSERT INTO ".TABLE_COURSES_MODULES." (course_id, module_id) SELECT ".$wpdb->insert_id.", m.module_id FROM ".TABLE_COURSES_MODULES." m WHERE m.course_id = $copy_course_id ";
        $result = $wpdb->query($sql);
        if($result !== FALSE) // return the course id of the copied course
        {
          return array('status' => 1, 'id' => $wpdb->insert_id); 
        }
        else // error
        {
          return array('status' => 0, 'message' => "copyCourse error:" . $wpdb->last_error);
        }
      }
    }
    else
    {
      return false;
    }
}

/*
 * Get modules in course
 * @param int $course_id - The course ID
 * @param string $type - Module type. (Page,scorm). Empty $type will return all the module types.
 */
function getModulesInCourse($course_id=0, $type = "page"){
    global $wpdb;
    $course_id  = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT m.* "
                . "FROM " . TABLE_MODULES . " AS m "
                . "LEFT JOIN " . TABLE_COURSES_MODULES . " AS cm ON cm.module_id = m.id "
                . "WHERE cm.course_id = $course_id ";
    if( $type )
    {
      switch ($type)
      {
        case "page":
          $sql .= ' AND component_type = "page"';
          break;
        case "scorm":
          $sql .= ' AND component_type = "scorm"';
          break;
        default:
      }
    }
    $course_modules = $wpdb->get_results($sql, ARRAY_A);
    return $course_modules;
}

//Ajax get modules for a course. Called from part-manage_courses
add_action('wp_ajax_getModules', 'getModules_callback'); // Executes Courses_Modules functions actions only for log in users
function getModules_callback() {
    if( isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['subscription_id'] ))
    {

        // Get the Post ID from the URL
        $course_id          = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain   = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $org_id             = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $subscription_id    = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        $course_status      = ""; //  The course status


        $info_data = array("org_id" => $org_id);


        // check if user has admin/manager permissions
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can ('is_sales_manager') )
        {
            $result['data'] = 'failed';
            $result['message'] = 'LU Error: Sorry, you do not have permisison to view this page. ';
        }
        else
        {
            // Build the response if successful
            // get modules of the course
            $modules=  getModulesInCourse($course_id);
            /*********************************************************************************************
            * Create HTML template and return it back as message. this will return an HTML div set to the 
            * javascript and the javascript will inject it into the HTML page.
            **********************************************************************************************/
            $html = '<div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "width: 350px">';
            $html.= '  <div style = "width:100%;">';

            $num_modules_type_page = 0;
            if( $modules && count($modules) > 0 ) 
            {
                foreach( $modules as $module )
                {   
                    /* 
                     * Include only the courses modules
                     */
                    if($module['component_type'] == "page")
                    {
                        
                        $module_description_text = $module['description_text']; 
                        $module_title     = $module['title'];
                        //$html.= ' <div class = "staff_and_assignment_list_row" onmouseover="Tip(\''.str_replace('"','&quot;',addslashes($module_description_text)).'\', WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\',TITLE,\'Description\')" onmouseout="UnTip()">';
                        $html.= ' <div class = "staff_and_assignment_list_row">';
                        $html.= '  <span class="staff_name" >'.$module_title.'</span>';
                        $html.= ' </div>';
                        $num_modules_type_page++;
                    }
                }
                $html.= '   </div>'; 
                $html.= '</div>';  
                $result['video_count'] = $num_modules_type_page;
                $result['data'] = 'success';
                $result['message'] = $html;
                $result['group_id'] = $course_id; // if not included, when clicking on manage assignment/course, it will not open the dialog box.
                $result['course_status'] = $course_status; // The course status, to determine if the user is allowed to use manage module button.
            }
            else if( count($modules) == 0 )
            {
                $result['video_count'] = 0; // No videos.
                $result['group_id'] = $course_id; // course ID. This is still required on the template so that the user will be able to click the "Manage Courses" button.
                $result['data'] = 'failed';
                $result['message'] = '<p>There are no modules available in this course.</p>';
                $result['course_status'] = $course_status; // The course status, to determine if the user is allowed to use manage module button.
            }
            else 
            {
                $result['data'] = 'failed';
                $result['message'] = 'Error in getting modules for course id: ('. $course_id .')';
            }
        }
    }
    else
    {
        $result['data'] = 'failed';
        $result['message'] = '<p>Unable to process your request</p>';
    }

    echo json_encode($result);
    wp_die();

}
/**
 *  Get the courses present in the org based on a parameter that's passed. Either by course id, all courses in org_id, all courses in subscription_id
 *  @param int $course_id - the course id
 *  @param int $org_id - the course id
 *  @param int $subscription_id - the course id
 *
 *  @return array of objects of the courses
 */
function getCourses($course_id = 0, $org_id = 0, $subscription_id = 0) {
  global $wpdb;

  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
  $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
  $subscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);

  if($course_id > 0)
  {
    $courses = $wpdb->get_results("SELECT * FROM " . TABLE_COURSES . " WHERE course_id = $course_id", OBJECT_K); 
  }
  else if($org_id > -1) // org_id == 0 belongs to EOT
  {
    $courses = $wpdb->get_results("SELECT * FROM " . TABLE_COURSES . " WHERE org_id = $org_id", OBJECT_K); 
  }
  else if($subscription_id > 0)
  {
    $courses = $wpdb->get_results("SELECT * FROM " . TABLE_COURSES . " WHERE subscription_id = $subscription_id", OBJECT_K); 
  }
  else
  {
    return array('status' => 0, 'message' => "ERROR in getCourses: Invalid parameters");
  }
  return $courses;
}

/**
 * Get the modules in a library
 * @global type $wpdb
 * @param type $library_id
 * @return type
 */
function getModulesByLibrary($library_id = 0)
{
  global $wpdb;
  $library_id = filter_var($library_id, FILTER_SANITIZE_NUMBER_INT);
  $modules=$wpdb->get_results("SELECT * FROM " . TABLE_MODULES. " WHERE library_id = $library_id" , ARRAY_A);
  return $modules;  
}
/**
 * Get courses based on org and subscription ids
 * @global type $wpdb
 * @param type $org_id
 * @param type $subscription_id
 * @return type
 */
function getCoursesById($org_id = 0, $subscription_id = 0)
{
    global $wpdb;
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    $subscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);
    $courses = $wpdb->get_results("SELECT * FROM " . TABLE_COURSES . " WHERE org_id = $org_id AND subscription_id = $subscription_id", ARRAY_A);
    return $courses;
}

/**
 * Get users in a specific course
 * @global type $wpdb
 * @param type $course_id
 * @return array
 */
function getEotUsersInCourse($course_id = 0){
    global $wpdb;
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    // Get the enrollments who are enrolled in the course.
    $enrollments = $wpdb->get_results("SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE course_id = $course_id", ARRAY_A);
    $users = array(); // Lists of users who are enrolled in the course.
    if($enrollments && count($enrollments) > 0)
    {
      foreach ($enrollments as $enrollment) 
      {
        $user['first_name'] = get_user_meta ( $enrollment['user_id'], "first_name", true);
        $user['last_name'] = get_user_meta ( $enrollment['user_id'], "last_name", true);
        $user['id']= $enrollment['user_id'];
//        $user['user_type'] = 'learner'; // @TODO remove if not used
        array_push($users, $user);
      }
    }
    return $users;
}

/**
 * Get enrolled users in a specific course
 * @param int $course_id - the course ID
 * @return array of enrolled users or NULL if none exist
 */
function getEnrolledUsersInCourse($course_id = 0)
{
    global $wpdb;
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

    // Get the enrollments who are enrolled in the course.
    $enrollments = $wpdb->get_results("SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE course_id = $course_id", ARRAY_A);

    if($enrollments && count($enrollments) > 0)
    {
      return $enrollments;
    }
    return NULL;
}

/********************************************************************************************************
 * Create HTML form and return it back as message. this will return an HTML div set to the 
 * javascript and the javascript will inject it into the HTML page.
 * The submit and cancel buttons are handled by javascript in this HTML (part-manage_courses.php for now)  
 *******************************************************************************************************/
add_action('wp_ajax_getCourseForm', 'getCourseForm_callback'); 
function getCourseForm_callback ( ) {
    if(isset($_REQUEST['org_id']) && isset($_REQUEST['form_name']) )
    {
        global $current_user;
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $form_name = filter_var($_REQUEST['form_name'],FILTER_SANITIZE_STRING);
        $user_id = $current_user->ID;
        error_log($form_name);
        if($form_name == "create_course_group")
        {
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Create a Course</div>
            </div>
            <div class="middle">
                <form id= "create_staff_group" frm_name="create_staff_group" frm_action="createCourse" rel="submit_form" hasError=0> 
                    <table padding=0 class="form"> 
                        <tr> 
                            <td class="label"> 
                                <label for="field_name">Name:</label> 
                            <td class="value"> 
                                <input  type="text" name="name" id="field_name" size="35" /><span class="asterisk">*</span> 
                            </td>
                        </tr> 
                        <tr >
                            <TD></TD>
                            <td class="value">
                                <span class="fyi">eg. First-Year Staff, Returning Staff, etc.</span>
                            </td>
                        </tr> 
                        <!--
                        <tr> 
                            <td class="label"> 
                                <label for="field_desc">Description:</label> 
                            </td> 
                            <td class="value"> 
                                <input type="text" name="desc" id="field_desc" size="35" />  
                            </td> 
                        </tr>
                        <tr >
                        <TD></TD>
                            <td class="value">
                                <span class="fyi">(for your own information)</span>
                            </td>
                        </tr>              
                        <tr> 
                        <td class="label"> 
                        </td> 
                        -->
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                <input type="hidden" name="user_id" value="<?= $user_id ?>" /> 
                                <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>" /> 
                                <?php wp_nonce_field( 'create-course_' . $org_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                    <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                        <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt="Close"/>
                        Cancel
                    </a>
                        <a active = '0' acton = "create_staff_group" rel = "submit_button" class="positive">
                        <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt="Save"/> 
                        Save
                    </a>
                </div>
            </div>

            <?php
            $html = ob_get_clean();
        }

        else if($form_name == "create_uber_camp_director")
        {
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $data = compact("org_id");
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Add a Camp Director</div>
            </div>
            <div class="middle">
                <form id= "edit_staff_group" frm_name="create_uber_camp_director" frm_action="createUberCampDirector" rel="submit_form" hasError=0> 
                    <table style="border-spacing: 10px; border-collapse: separate;">   
                        <tr> 
                          <td class="label"> 
                            <label for="field_camp_name">Camp Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="camp_name" id="field_camp_name" size="20" value=""/>
                          </td>
                        </tr>  
                        <tr> 
                          <td class="label"> 
                            <label for="field_first_name">First Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="first_name" id="field_first_name" size="20" value=""/>
                          </td>
                        </tr>
                        <tr> 
                          <td class="label"> 
                            <label for="field_last_name">Last Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="last_name" id="field_last_name" size="20" value=""/>
                          </td>
                        </tr>  
                        <tr> 
                          <td class="label"> 
                            <label for="field_password">Email:</label> 
                          <td class="value"> 
                            <input  type="email" name="email" id="field_email" size="20" value=""/>
                          </td>
                        </tr>  
                        <tr> 
                          <td class="label"> 
                            <label for="field_password">Password:</label> 
                          <td class="value"> 
                            <input  type="password" name="password" id="field_password" size="20" value=""/>
                          </td>
                        </tr>
                        <tr> 
                            <td class="label"> 
                            </td> 
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                 <?php wp_nonce_field( 'create-uber_camp_director_' . $org_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "create_uber_camp_director" rel = "submit_button" class="positive">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Save
                  </a>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
        }
        else if($form_name == "edit_course_group")
        {
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
            $data = compact("org_id");
            //$course_data = getCourse($portal_subdomain, $course_id, $data); // all the settings for the specified course
            global $wpdb;
        $course_data = getCourse($course_id);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Edit Course</div>
            </div>
            <div class="middle">
                <form id= "edit_staff_group" frm_name="edit_staff_group" frm_action="updateCourse" rel="submit_form" hasError=0> 
                    <table padding=0 class="form"> 
                        <tr> 
                          <td class="label"> 
                            <label for="field_name">Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="name" id="field_name" size="35" value="<?= $course_data['course_name'] ?>"/><span class="asterisk">*</span> 
                          </td>
                        </tr> 
                        <tr >
                            <TD></TD>
                            <td class="value">
                            <span class="fyi">eg. First-Year Staff, Returning Staff, etc.</span>
                            </td>
                        </tr> 
                        <tr> 
                        <!--
                          <td class="label"> 
                            <label for="field_desc">Description:</label> 
                          </td>
                          <td class="value"> 
                            <input type="text" name="desc" id="field_desc" size="35" value=""/>  
                          </td> 
                        </tr>
                        <tr >
                            <TD></TD>
                            <td class="value">
                                <span class="fyi">(for your own information)</span>
                            </td>
                        </tr>   
                        -->           
                        <tr> 
                            <td class="label"> 
                            </td> 
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                <input type="hidden" name="group_id" value="<?= $course_id ?>" />
                                <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                                <?php wp_nonce_field( 'edit-course_' . $org_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "edit_staff_group" rel = "submit_button" class="positive">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Save
                  </a>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
        }

        else if($form_name == "delete_course")
        {
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Delete <?=$course_name?> Course</div>
            </div>
            <div class="middle">
                <form id= "delete_staff_group" frm_name="delete_staff_group" frm_action="deleteCourse" rel="submit_form" hasError=0> 
                    <table padding=0 class="form"> 
                        <tr >
                            <TD></TD>
                            <td class="value">
                            <span class="fyi">Staff accounts enrolled in this course will NOT be deleted, but staff will no longer be able to access this course or its contents and any progress they made so far will be lost.</span>
                            </td>
                        </tr> 
                        <tr> 
                            <td class="label"> 
                            </td> 
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                <input type="hidden" name="group_id" value="<?= $course_id ?>" />
                                <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                                <?php wp_nonce_field( 'delete-course_' . $course_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="deleting_course" style="display:none"></i>
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "delete_staff_group" rel = "submit_button" class="positive" onclick="jQuery('#deleting_course').show();">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Delete
                  </a>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
        }
        else if($form_name == "manage_camp_course")
        {
          $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
          $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT); // The organization ID
          $portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
          $umbrellaCamps = (current_user_can('is_umbrella_manager')) ? getUmbrellaCamps($org_id, 'regional_umbrella_group_id') : getUmbrellaCamps($org_id); // Lists of umbrella camps
          $camps = array(); // Lists of camps
          $data = compact("org_id");
          $course_name = filter_var($_REQUEST['course_name'], FILTER_SANITIZE_STRING); // the name of the course
          
          if ($umbrellaCamps->have_posts())
          { 
            // Get all umbrella camps, and add them into camps array.
            while ( $umbrellaCamps->have_posts() ) 
            {
              $umbrellaCamps->the_post(); 
              $camp['id'] = get_the_ID(); // The org ID
              $camp['name'] = get_the_title(); // The camp name
              array_push($camps, $camp);
            }
          }


        ?>

             <div style="position:absolute;right:-290px;top:0px;">
              <div class="popup"> 
                <table> 
                  <tbody>
                    <tr> 
                      <td class="tl"/><td class="b"/><td class="tr"/> 
                    </tr>
                    <tr> 
                      <td class="b"/> 
                      <td class="body"> 
                        <div class="content">  
                          <table class="assign_summary data" style = "width:260px;margin:0px;padding:5px;">
                            <tr  class="head">
                              <td style ="padding:5px;" colspan="2">
                                Publish Option
                              </td>
                            </tr>
                            <tr>
                              <td style ="padding:2px 5px 2px 5px;">Do you want to Publish this course after the copy?<br>(Published courses can not be modified by the individual camp. Draft courses will need to be published by each individual camp.)</td>
                              <td style="padding:2px 5px 2px 5px;font-size:16px;font-weight:bold;text-align:center;"  id="timeToComplete" >
                                <input type="checkbox" name="chkbox_is_publish_course_after_copy" id="chkbox_is_publish_course_after_copy" value='' /> 
                              </td>
                            </tr>
                          </table>         
                        </div> 
                      </td> 
                      <td class="b"/> 
                    </tr>
                    <tr> 
                      <td class="bl"/>
                      <td class="b"/>
                      <td class="br"/>
                    </tr>
                  </tbody> 
                </table> 
              </div> 
            </div>
            <div class="title">
              <div class="title_h2"><?= $course_name; ?></div>
            </div>
            <div class="middle" style ="padding:0px;clear:both;">  
              <div id="video_listing" display="video_list" group_id="null" class="holder osX">
                <div id="video_listing_pane" class="scroll-pane" style="padding:0px 0px 0px 10px;width: 600px">
                  <form name = "add_video_group" id = "add_video_group">
                    <ul class="tree organizeassignment">
                      <h3 class="library_topic">Select which camps to copy this course into:</h3>
              <?php
                      // Display all uber camps and a clone checkbox beside it for cloning functionality.
                      foreach($camps as $camp)
                      {
              ?>
                        <li class="video_item" camp_id="<?= $camp['id']?>">
                          <input collection="add_remove_from_group" org_id="<?= $org_id ?>" portal_subdomain="<?= DEFAULT_SUBDOMAIN ?>" id="chk_video_<?= $camp['id']?>" name="chk_video_<?= $camp['id']?>" type="checkbox" value="1" camp_id="<?= $camp['id']?>" course_id="<?= $course_id ?>" "/> 
                          <label for="<?= $camp['id'] ?>">
                              <span name="video_title">
                                <b>Camp</b> - <span class="vtitle"><?= $camp['name'] ?></span>
                              </span>
                          </label>
                          <img style="margin-right: 0; display:none" class="loader" id="img_loading" src="<?= get_template_directory_uri() . "/images/loading.gif"?>">
                          <img style="margin-right: 0; display:none" class="loader" id="img_check" src="<?= get_template_directory_uri() . "/images/checkmark.gif"?>">
                          <img style="margin-right: 0; display:none" class="loader" id="img_delete" src="<?= get_template_directory_uri() . "/images/delete.gif"?>">
                          <span id="clone_error_message" style="margin-right: 0; display:none"></span>
                        </li> 
              <?php
                      }
              ?> 
                    </ul>
                  </form>
                </div>
              </div>
            </div>      
            <div class="popup_footer" style="background-color:#FFF; padding:15px 15px 5px 15px;">
              <div class="buttons" >
                <a active='0' acton="add_video_group" rel="done_button" >
                  Done
                </a>
      <!--
                <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="unselect_all_button" >
                  Remove All
                </a> 
                <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="select_all_button" >
                  Add All
                </a>
      -->
                <div style="clear:both"></div>                      
              </div>
              <div style="margin-top:5px;margin-bottom: -10px;">
              </div>
            </div>

        <?php
            $html = ob_get_clean();
        }
        else if($form_name == "add_video_group")
        {
            global $wpdb;
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $data = array( "org_id" => $org_id ); // to pass to our functions above
            $course_modules = getModulesInCourse($course_id); // all the modules in the specified course
            $course_quizzes=  getQuizzesInCourse($course_id);
            $course_resources=getResourcesInCourse($course_id);
            //var_dump($course_resources);
            $course_modules_titles = array_column($course_modules, 'title'); // only the titles of the modules in the specified course
            $course_quizzes_titles = array_column($course_quizzes, 'name');
            $course_resources_titles = array_column($course_resources, 'name');
            //var_dump($course_quizzes_titles);
            $modules_in_portal = getModules($org_id);// all the modules in this portal
            //var_dump($modules_in_portal);
            $user_modules_titles = array_column($modules_in_portal, 'title'); // only the titles of the modules from the user library (course).
            $master_course = getCourseByName(lrn_upon_LE_Course_TITLE); // Get the master course. Cloned LE
            $master_course_id = $master_course['id']; // Master library ID
            $master_modules = getModulesByLibrary(1);// Get all the modules from the master library (course).            
            $master_modules_titles = array_column($master_modules, 'title'); // only the titles of the modules from the master library (course).
            $master_module_ids=array_column($master_modules, 'id');
            $modules_in_portal_ids = array_column($modules_in_portal, 'id');
            $all_module_ids=  array_merge($master_module_ids, $modules_in_portal_ids);
            $all_module_ids_string=implode(',',$all_module_ids);
            $exams=array();  
            $resources=getQuizResourcesInModules($all_module_ids_string);
            //var_dump($resources);
            foreach($resources as $resource){
                if(isset($exams[$resource['module_id']]))
                {
                    array_push($exams[$resource['module_id']], array('id'=>$resource['id'],'name'=>$resource['name']));
                }else{
                $exams[$resource['module_id']]=array();
                array_push($exams[$resource['module_id']], array('id'=>$resource['id'],'name'=>$resource['name']));
                }
            }
            $handouts=array();
            $handout_resources=  getHandoutResourcesInCourseModules($all_module_ids_string);
            //var_dump($handout_resources);
            foreach($handout_resources as $handout){
                if(isset($handouts[$handout['module_id']]))
                {
                    array_push($handouts[$handout['module_id']], array('id'=>$handout['id'],'name'=>$handout['name']));
                }else{
                $handouts[$handout['module_id']]=array();
                array_push($handouts[$handout['module_id']], array('id'=>$handout['id'],'name'=>$handout['name']));
                }
            }
            //$course_data = getCourse($portal_subdomain, $course_id, $data); // all the settings for the specified course
            $course_data=getCourse($course_id);
            $due_date =$course_data['due_date_after_enrollment']!==NULL? date('m/d/Y',  strtotime($course_data['due_date_after_enrollment'])):NULL; // the due date of the specified course
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); //  The subscription ID
            $videoCount = count($course_modules_titles);
            $quizCount = count($course_modules_titles);
            ob_start();
        ?>

       <div style="position:absolute;right:-290px;top:0px;">
        <div class="popup"> 
          <table> 
            <tbody>
              <tr> 
                <td class="tl"/><td class="b"/><td class="tr"/> 
              </tr>
              <tr> 
                <td class="b"/> 
                <td class="body"> 
                  <div class="content">  
                    <table class="assign_summary data" style = "width:260px;margin:0px;padding:5px;">
                      <tr  class="head">
                        <td style ="padding:5px;" colspan="2">
                          Assignment Summary
                        </td>
                      </tr>
                      <tr>
                        <td style ="padding:2px 5px 2px 5px;">Videos</td>
                        <td style ="text-align:center;padding:2px 5px 2px 5px;" id="videoCount"><?= count($course_modules_titles) ?></td>
                      </tr>
                      <tr>
                        <td style ="padding:2px 5px 2px 5px;">Quizzes</td>
                        <td style ="text-align:center;padding:2px 5px 2px 5px;" id="quizCount"><?= count($course_modules_titles) ?></td>
                      </tr>
                      <tr>
                        <td style ="padding:2px 5px 2px 5px;">Estimated time to complete&nbsp;</td>
                        <td style="padding:2px 5px 2px 5px;font-size:16px;font-weight:bold;text-align:center;"  id="timeToComplete" ><?= round((MINUTES_PER_VIDEO*$videoCount + MINUTES_PER_QUIZ*$quizCount)/60,1) ?> hours</td>
                      </tr>
 
                    </table>         
                  </div> 
                </td> 
                <td class="b"/> 
              </tr>
              <tr> 
                <td class="bl"/>
                <td class="b"/>
                <td class="br"/>
              </tr>
            </tbody> 
          </table> 
        </div> 
      </div>
    <div style="position:absolute;right:-290px;top:150px;">
        <div class="popup"> 
          <table> 
            <tbody>
              <tr> 
                <td class="tl"/><td class="b"/><td class="tr"/> 
              </tr>
              <tr> 
                <td class="b"/> 
                <td class="body"> 
                  <div class="content">  
                    <table class="assign_summary data" style = "width:260px;margin:0px;padding:5px;">
                      <tr  class="head">
                        <td style ="padding:5px;" colspan="2">
                          Assignment Due Date
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:12px;font-size:12px;">
                          <center>
                            <?php
                              if ($due_date === NULL) {
                            ?>
                                <p class='curr_duedate'><strong>Due Date:</strong> No due date set.</p>
                                <div id="datepicker"></div>
                                <div id="remove_date" class="buttons" style="display:none;padding-top:10px;">
                                  <a class="negative" style="margin-right:50px;">
                                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                                      Remove Due Date
                                  </a>
                                </div>
                            <?php
                              } 
                              else 
                              {
                                echo "<p class='curr_duedate'><strong>Due Date:</strong> " . date('j F, Y', strtotime($due_date));
                            ?>
                                <div id="datepicker"></div>
                                <div id="remove_date" class="buttons" style="padding-top:10px;">
                                  <a class="negative" style="margin-right:50px;">
                                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                                    Remove Due Date
                                  </a>
                                </div>
                            <?php
                              }
                            ?>
                          </center>
                        </td>
                      </tr>
                    </table>         
                  </div> 
                </td> 
                <td class="b"/> 
              </tr>
              <tr> 
                <td class="bl"/>
                <td class="b"/>
                <td class="br"/>
              </tr>
            </tbody> 
          </table> 
        </div> 
      </div>
      <div class="title">
        <div class="title_h2"><?= $course_name ?></div>
      </div>
      <div class="middle" style ="padding:0px;clear:both;">  
        <div id="video_listing" display="video_list" group_id="null" class="holder osX">
          <div id="video_listing_pane" class="scroll-pane" style="padding:0px 0px 0px 10px;width: 600px">
            <form name = "add_video_group" id = "add_video_group">
              <ul class="tree organizeassignment">
                <?php 
                    $videos = $wpdb->get_results("SELECT name, secs FROM " . TABLE_VIDEOS, OBJECT_K); // All videos name and their time in seconds.
                    $subscription = getSubscriptions($subscription_id,0,1); // get the current subscription
                    $library = getLibrary ($subscription->library_id); // The library information base on the user current subscription
                   // var_dump($library);
                    // Check if the library exsist
                    if( isset($library) )
                    {
                        $library_tag = $library->tag; // The library Tag for this subscription
                        $modules = array(); // Array of Module objects
                        $categories = array(); // Array of the name of the categories
                        foreach($master_modules as $key => $module)
                        {
                            /* 
                             * This populates the modules array.
                             */
                            $pieces = explode(" ", $module['tags']); // Expected result is [String category Library Codes] : The library code could be more than one.
                            $category_name = $pieces[0]; // The category for this module
                            /*
                             * Create a module object and category if the module belongs to this subscription.
                             * Otherwise, skip them.
                             */
                            if($category_name) // Check if there are tags set in LU
                            {
                               /*
                                * Include only the modules that are in the same library tag
                                */
                            array_shift($pieces); // Remove the category in this array and left with the library codes.
                                if (in_array($library_tag, $pieces)) 
                                {   
                                    //echo "inarray<br>";
                                    $new_module = new module( $module['id'], $module['title'], $category_name, $module['component_type']); // Make a new module.
                                    array_push($modules, $new_module); // Add the new module to the modules array.
                                    /*
                                     * Populate the category name array if the category name is not yet in the array.
                                     */
                                    if(!in_array($category_name, $categories))
                                    {
                                        array_push($categories, $category_name);
                                    }
                                }
                            }
                        }
                        usort($categories, "category_sort"); // Sort the categories based on the function below.
                        /*  
                         * Display the category and display its modules
                         */
                        foreach($categories as $category)
                        {
                            $category_name = str_replace("_", " ", $category);// The category name. Replace Coma with spaces.
                        ?>
                            <h3 class="library_topic"><?= $category_name ?></h3>
                        <?php
                            /************************************************************************************************
                            * Print modules that are in the same category its in.
                            *************************************************************************************************/
                            foreach( $modules as $key => $module )
                            {
                                 
                                if($module->type == "page") 
                                { // Print the course modules.
                                    
                                    if ( $module->category == $category )
                                    {
                                        $video_active = 0; // variable to indicate whether module is currently in the portal course
                                        $video_class = 'disabled'; // variable to indicate whther module is currently in the portal course
                                        $module_id = $module->id; // The module ID 
                                        echo '<li class="video_item" video_id="' . $module_id . '">';
                                            if(in_array($module->title, $course_modules_titles))
                                            {   
                                                $video_active = '1';
                                                $video_class = 'enabled';
                                                // if module is in course, need to get it's module id from course, not master course.
                                                foreach ($course_modules as $key2 => $value)
                                                {
                                                  if ($value['title'] == $module->title)
                                                  {
                                                    $module_id =  $value['id']; // overwrite the module id originally from master modules with the module id from this specific course
                                                    break;
                                                  }
                                                }
                                            }
                                            $module_time = ($videos[$module->title]) ? $videos[$module->title]->secs/60 : lrn_upon_Module_Video_Length; // The module time, divided by 60 to convert them in minutes.
                                    ?>
                                            <input collection="add_remove_from_group" video_length="<?= $module_time ?>" org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" group_id=<?= $course_id ?> assignment_id="<?= $course_id ?>" video_id="<?= $module_id ?>" id="chk_video_<?= $module_id ?>" name="chk_video_<?= $module_id ?>" type="checkbox" value="1" <?=($video_active)?' checked="checked"':'';?> /> 
                                            <label for="chk_video_<?= $module_id ?>">
                                                <span name="video_title" class="<?=$video_class?> video_title">
                                                  <b>Video</b> - <span class="vtitle"><?= $module->title ?></span>
                                                </span>
                                            </label>
                                            <div video_id=<?= $module_id ?> class="<?=$video_class?> item" <?=(!$video_active)?' org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" style="display:none"':'';?> >
                                            <?php
                                               
                                            ?>
                                                <?php
                                               
                                                /* 
                                                 * Check if there is a an exam for this module
                                                 * The exam checkbox input will not be shown, if there are no exam uploaded in LU.
                                                 * Find the ID of this exam in the modules array.
                                                 */
                                                //var_dump($exams[$module->id]);
                                                if($exams[$module->id])
                                                {
                                                    foreach($exams[$module->id] as $exam){
                                                            $exam_id = $exam['id']; 
                                                   
                                            ?>
                                                    <input item="quiz" quiz_length="<?= lrn_upon_Quiz_Length ?>" group_id="<?= $course_id ?>" <?= $exam_id ? ' item_id="' . $exam_id . '" name="chk_defaultquiz_'.$exam_id.'" id="chk_defaultquiz_' .$exam_id . ' "':'';?> type="checkbox"   group_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" <?= in_array($exam['name'], $course_quizzes_titles) ? ' checked="checked"':''; $exam_id = 0; // Reset Exam ID?> /> 
                                                    <label for="chk_defaultquiz_<?= $module_id ?>">
                                                      <i>Exam</i> (<?= $exam['name'] ?>) 
                                                    </label><br>
                                            <?php
                                                 }
                                                }
                                                /* 
                                                 * Check if there is a handout for this module
                                                 * The resource checkbox input will not be shown, if there are no resources.
                                                 * Find the ID of this exam in the modules array.
                                                 */
                                                //var_dump($exams[$module->id]);
                                                if($handouts[$module->id])
                                                {
                                                    foreach($handouts[$module->id] as $handout){
                                                            $handout_id = $handout['id']; 
                                                   
                                            ?>
                                                    <input item="resource" quiz_length="<?= lrn_upon_Quiz_Length ?>" group_id="<?= $course_id ?>" <?= $handout_id ? ' item_id="' . $handout_id . '" name="chk_defaultresource_'.$handout_id.'" id="chk_defaultresource_' .$handout_id . ' "':'';?> type="checkbox"   assignment_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" <?= in_array($handout['name'], $course_resources_titles) ? ' checked="checked"':''; $handout_id = 0; // Reset Exam ID?> /> 
                                                    <label for="chk_defaultresource_<?= $handout_id ?>">
                                                      <i>Resource</i> (<?= $handout['name'] ?>) 
                                                    </label><br>
                                            <?php
                                                 }
                                                }      
                                            ?>
                                            </div>
                                        </li> 
                                    <?php
                                        //$resources =  getResourcesInModule($module->id);
                                        //var_dump($resources);
                                        unset( $modules[$key] ); // remove this module in the modules array
                                    }
                                }
                            }
                            // End of Modules foreach
                        }
                        // End of Category foreach
                    }
                    else
                    {
                        // Error in getting the library for this subscription ID
                        echo "Invalid library ID.";
                    }
                ?>
              </ul>

              <div id="custom_quizzes_and_resources">
                <h2 class="library_topic">Your Custom Modules</h2>
                <ul class="tree organizeassignment">
                  <?php
                    foreach($modules_in_portal as $key => $module) // go thourh all the modules in our portal
                    {
//                        if($module['component_type'] == "page" || $module['component_type'] == 'exam')
//                        {
                            $module_active = 0; // variable to indicate whether module is currently in the portal course
                            $module_class = 'disabled'; // variable to indicate whther module is currently in the portal course
                            // check if portal module exists in master course
                            // if it does, do not display it as a custom module
                            if(!in_array($module['title'], $master_modules_titles)) 
                            {
                                // check if the module is in this specific course. if it is, then enable it, otherwise its default disabled.
                                if(in_array($module['title'], $course_modules_titles))
                                {
                                    $module_active = '1';
                                    $module_class = 'enabled';
                                }

                                if ($module['component_type'] == "exam")
                                {
                                  // displpay the module title but disable the checkbox and if clicked alert a message
?>
                                  <li class="video_item" video_id="<?= $module['id'] ?>" >
                                  <input collection="add_remove_from_group" org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" group_id=<?= $course_id ?> video_length="<?= lrn_upon_Module_Video_Length ?>" assignment_id="<?= $course_id ?>" video_id="<?= $module['id'] ?>" id="chk_video_<?= $module['id'] ?>" name="chk_video_<?= $module['id'] ?>" type="checkbox" value="1" <?=($module_active)?' checked="checked"':'';?> /> 
                                  <label for="chk_video_<?= $module['id'] ?>">
                                  <span name="video_title" class="<?=$module_class?> video_title">
<?php
                                }
                                else
                                {
                                  // show the input checkbox as ususal
?>
                                  <li class="video_item" video_id="<?= $module['id'] ?>" >
                                  <input collection="add_remove_from_group" org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" group_id=<?= $course_id ?> video_length="<?= lrn_upon_Module_Video_Length ?>" assignment_id="<?= $course_id ?>" video_id="<?= $module['id'] ?>" id="chk_video_<?= $module['id'] ?>" name="chk_video_<?= $module['id'] ?>" type="checkbox" value="1" <?=($module_active)?' checked="checked"':'';?> /> 
                                  <label for="chk_video_<?= $module['id'] ?>">
                                  <span name="video_title" class="<?=$module_class?> video_title">
<?php                                  
                                }
?>


                                  <span class="vtitle"><?= $module['title'] ?></span>
                                  </span><br>
                                      <?php
                                if($exams[$module['id']])
                                                {
                                                    foreach($exams[$module['id']] as $exam){
                                                            $exam_id = $exam['id']; 
                                                   
                                            ?>
                                                    <input item="quiz" quiz_length="<?= lrn_upon_Quiz_Length ?>" group_id="<?= $course_id ?>" <?= $exam_id ? ' item_id="' . $exam_id . '" name="chk_defaultquiz_'.$exam_id.'" id="chk_defaultquiz_' .$exam_id . ' "':'';?> type="checkbox"   assignment_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" <?= in_array($exam['name'], $course_quizzes_titles) ? ' checked="checked"':''; $exam_id = 0; // Reset Exam ID?> /> 
                                                    <label for="chk_defaultquiz_<?= $module_id ?>">
                                                      <i>Exam</i> (<?= $exam['name'] ?>) 
                                                    </label><br>
                                            <?php
                                                 }
                                                }
                                                
?>
                                </label>
                                </li>
                      <?php
                            }
//                        }
                    }
                  ?>
                </ul>
              </div>
            </form>
          </div>
        </div>
      </div>      
      <div class="popup_footer" style="background-color:#FFF; padding:15px 15px 5px 15px;">
        <div class="buttons" >
          <a active='0' acton="add_video_group" rel="done_button" >
            Done
          </a>
          <!--
          <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="unselect_all_button" >
            Remove All
          </a> 
          <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="select_all_button" >
            Add All
          </a>
          -->
<!--
          <a href="my-dashboard.html?view=managedocuments&" active='0' acton="add_video_group" rel="loading" >
            Upload Resource
          </a>
          <a href ="my-dashboard.html?view=managequizzes&org_id=" active='0' acton="add_video_group" rel="loading" >
            Create Custom Quiz
          </a> 
-->                              
          <div style="clear:both"></div>                      
        </div>
        <div style="margin-top:5px;margin-bottom: -10px;">
        </div>
      </div>

        <?php
            $html = ob_get_clean();
        }
        else if($form_name == "edit_staff_account")
        {
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $staff_id = filter_var($_REQUEST['staff_id'],FILTER_SANITIZE_NUMBER_INT);
            $data = array( "org_id" => $org_id );
//            $response = getUser($portal_subdomain, $staff_id, $data);
//            if ($response['status'] == 1)
//            {
//              $user = $response['user'];
//            }
            $theuser =  get_user_by('id', $staff_id);
            //var_dump($theuser);
            $user['email']=$theuser->user_email;
            $user['first_name']= get_user_meta($staff_id, 'first_name', true);
            $user['last_name']= get_user_meta($staff_id, 'last_name', true);
            //var_dump($user);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Edit Staff Account</div>
            </div>
            <div class="middle">
                <form id= "edit_staff_account" frm_name="edit_staff_account" frm_action="updateUser" rel="submit_form" hasError=0> 
                Change your Staffs user details. Once they log in, you will not be able <br /> to edit nor delete their accounts.<br />
                <span class="asterisk">*</span> Required fields        
                <br /><br />
                <table class="Tstandard">
                  <tr>
                    <td class="label">
                      First Name:<span class="asterisk">*</span>     
                    </td>
                    <td class="field">
                      <input type="text" name="name" value="<?= $user['first_name'] ?>" size="30" />
                    </td>
                  </tr>
                  <tr>
                    <td class="label">
                      Last Name:<span class="asterisk">*</span>     
                    </td>
                    <td class="field">
                      <input type="text" name="lastname" value="<?= $user['last_name'] ?>" size="30" />
                    </td>
                  </tr>
                  <tr>
                    <td class="label">
                      E-mail:<span class="asterisk">*</span>        
                    </td>
                    <td class="field">
                      <input type="text" name="email" value="<?= $user['email'] ?>" size="35" />
                    </td>
                  </tr>
                  <tr class="spacer">
                  </tr>
                  <tr>
                    <td class="label">
                      Change Password:<span class="asterisk"></span>      
                    </td>
                    <td class="field">
                      <input type="text" name="pw" value="" size="18" /> <span class="small">(not hidden)</span>
                    </td>
                  </tr>
                  <tr class="spacer">
                  </tr>
                  <tr>
                    <td></td>
                    <td class="field">
                      <input type="hidden" name="subscription_id" value="" />
                      <input type="hidden" name="org_id" value="<?= $org_id ?>" />
                      <input type="hidden" name="staff_id" value="<?= $staff_id ?>" />
                      <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                      <input type="hidden" name="old_email" value="<?= $user['email'] ?>" />
                      <?php wp_nonce_field( 'update-staff_' . $staff_id ); ?>
                    </td>
                  </tr>
                </table>        
              </form>
              <br /><br />    
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                  <img src="<?php bloginfo('template_directory'); ?>/images/cross.png" alt=""/>
                    Cancel
                </a>
                <a active = "0" acton = "edit_staff_account" rel = "submit_button" class="positive">
                  <img src="<?php bloginfo('template_directory'); ?>/images/tick.png" alt=""/> 
                  Update
                </a>        
              </div>
            </div>
        <?php
            $html = ob_get_clean();
        }
        else if($form_name == "create_staff_account")
        {   
            $subscription_id=filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);            
            $data = array( "org_id" => $org_id );
            $courses_in_portal = getCoursesById($org_id,$subscription_id); // get all the published courses in the portal
            $course_id = 0; // The course ID
            if(isset($_REQUEST['group_id']))
            {
                $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_STRING);
            }
            if(!org_has_maxed_staff($org_id, $subscription_id) ){
                                    ob_start();
        ?>
                    <div class="title">
                        <div class="title_h2">Unable to Create Staff</div>
                    </div>
                    <div class="middle" style ="font-size:11px;clear:both;">
                        <div class="fixed_fb_width">
                            <div class="msgboxcontainer_no_width">
                                <div class="msg-tl">
                                    <div class="msg-tr"> 
                                        <div class="msg-bl">
                                        <div class="msg-br">
                                            <div class="msgbox">
                                                <p>You have reached the maximum number of staff you can have. To add more staff, you need first to upgrade your subscription</p>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="popup_footer" style = "background-color:#FFF; padding:15px 15px 5px 15px;">
                        <div class="buttons" >        
                                <a onclick="jQuery(document).trigger('close.facebox');">
                                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                                </a>
                                      
                            <div style="clear:both">
                            </div>                      
                        </div>
                    </div>
        <?php
        $html = ob_get_clean();
            }else{
            ob_start();
        ?>
            <div class="title">
              <div class="title_h2">Create Staff Account</div>
            </div>
            <div class="middle">
              <div class="fixed_fb_width">
                <form id="create_staff_account" frm_name="create_staff_account" frm_action="createUser" rel="submit_form" hasError=0> 
                  <br />
                  <br />
                  <span class="asterisk">*</span> Required fields            
                  <br />
                  <br />
                  <table id="create_staff_table" class="Tstandard" style="font-size:12px;">
                    <tr>
                      <td class="label">
                        Camp, School or Youth Program:
                      </td>
                      <td class="field">
                        <?= get_the_title($org_id); ?>                
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        Enroll in course:
                      </td>
                      <td class="field">
                        <select name="course_id">                          
                          <?php
                            foreach ($courses_in_portal as $key => $course)
                            {
                                // If course ID is found, set the dropdown selection to this course.
                                if($course_id > 0)
                                {
                                    if($course['id'] == $course_id)
                                    {
                                        echo "<option name='course_id' value='" . $course['id'] . "' selected>" . $course['course_name'] . "</option>";
                                    }
                                    else
                                    {
                                        echo "<option name='course_id' value='" . $course['id'] . "'>" . $course['course_name'] . "</option>";
                                    }
                                }
                                // There's no course to be selected.
                                else
                                {
                                    echo "<option name='course_id' value='" . $course['id'] . "'>" . $course['course_name'] . "</option>";
                                }               
                            }
                          ?>
                        </select>
                        <img src="<?= get_template_directory_uri() . "/images/info.gif" ?>" title="You must enroll a user directly into a <b>course</b>. All users must be in enrolled in at least 1 course to gain access to the content.<br /><br />To create or edit courses, go to:<br><i>Administration &gt; Manage Courses</i>" class="tooltip" style="margin-bottom: -9px" onmouseover="Tip('You must enroll a user directly into a <b>course</b>. All users must be in enrolled in at least 1 course to gain access to the content.<br /><br />To create or edit courses, go to:<br><i>Administration &gt; Manage Courses</i>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()" />                
                      </td>
                    </tr>
                    <tr class="spacer">
                    </tr>
                    <tr>
                      <td class="label">
                        First Name: <span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                            <div id="autofill_suggest">
                                <input type="text" name="name" value="" size="30" required />
                            </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        Last Name:<span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                        <div id="autofill_suggest">
                            <input type="text" name="lastname" value="" size="30" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        E-mail:<span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                          <div id="autofill_suggest">
                             <input class="email_large" type="text" name="email" value="" size="40" />
                          </div>            
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        Password:<span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                        <input type="text" name="pw" value="" size="18" />
                      </td>
                    </tr>
                    <tr class="spacer">
                    </tr>
                    <tr>
                      <td></td>
                      <td>
                            <span class="asterisk">*</span> 
                            <input id="send" type="checkbox" name="send_mail" value="1" checked="checked" /> 
                            <label for="send">Yes, send an e-mail with their login password.</label>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td class="field">
                        <input type="hidden" name="org_id" value="<?= $org_id ?>" />
                        <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                        <?php wp_nonce_field( 'create-staff_' . $org_id ); ?>
                      </td>
                    </tr>
                  </table>
                </form>
                <br /><br />    
              </div>   
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                  <img src="<?php bloginfo('template_directory'); ?>/images/cross.png" alt=""/>
                    Cancel
                </a>
                <a active="0" acton="create_staff_account" rel="submit_button" class="positive">
                  <img src="<?php bloginfo('template_directory'); ?>/images/tick.png" alt=""/> 
                  Create
                </a>        
              </div>
            </div>
        <?php
            $html = ob_get_clean();
            }
        }
        else if($form_name == "send_message")
        {        
            $name = filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING);
            $password = $_REQUEST['password'];    
            $email = sanitize_email( $_REQUEST['email'] );
            $target = filter_var($_REQUEST['target'],FILTER_SANITIZE_STRING);
            $data = array( "org_id" => $org_id );
            global $current_user;
            wp_get_current_user();
        ?>
            <div class="title">
              <div class="title_h2">
                Edit Email Message
              </div>
            </div>
            <div class="middle" style ="font-size:12px;width:600px;margin:10px;padding:0px;clear:both;">
                <form frm_name="send_message" id="send_message" frm_action="sendMail" rel="submit_form" hasError=0>
                    <table>
                      <tr>
                        <td class="label" width="100px">
                          From
                        </td>
                        <td class="value">
                          <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?><span class="small">(<?= $current_user->user_email ?>)</span>
                        </td>
                      </tr>
                      <tr>
                        <td class="label">
                          Subject
                        </td>
                        <td class="value">
                          <input type="text" name="subject" value="Your account on ExpertOnlineTraining.com (Leadership Training)" size="60" />
                        </td>
                      </tr>
                      <tr>
                        <td class="label vtop">Message</td>
                        <td class="value">
                          <textarea class="tinymce" id="composed_message" name="message" style="margin-left:1px;width: 525px; height: 300px">
                            <b>Welcome</b>, <?= $name ?>!<br><br>
                            <b>Congratulations!</b> You are now a member of Expert Online Training (EOT), the world’s best virtual classroom for youth development professionals. 
                            By using EOT now, before your job starts at <?= get_the_title($org_id); ?>, you will turbocharge your leadership skills, boost your self-confidence, 
                            and get even more out of <?= get_the_title($org_id); ?>’s on-site training.<br><br>

                            <p><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" alt="EOT Logo" style="width: 125px; height: 94px; float: left;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" data-mce-style="width: 150px; height: 113px; float: left;"> 
                            <br><b>Take EOT with you.</b> We know you are busy, so our new website is mobile-friendly. You can now watch EOT videos and take your quizzes on any smartphone, tablet, or laptop with a WiFi connection. 
                            Imagine learning more about behavior management, leadership, supervision, games, and safety while you sit in a café, library, or student lounge!</p><br><br>

                            <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> just created an account for you with these login credentials:<br><br>

                            E-mail / username: <?= $email ?><br>
                            Password: <?= $password ?><br><br>

                            To watch EOT’s intro video and log in, <a href="https://www.expertonlinetraining.com" target="_blank" data-mce-href="https://www.expertonlinetraining.com">click here</a>.<br><br>

                            <b>When is it due?</b> Directors usually require staff to complete their online learning assignment before arriving on-site. 
                            If you have not yet received a due-date for your assignment, check with <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> to get one. 
                            As you move through your course, <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> will have access to an electronic dashboard that allows them to track your progress and quiz scores.<br><br>

                            <b>Got Questions?</b> If you get stuck, watch our online help videos or call us at <b>877-237-3931</b>! The EOT Customer Success team is on duty M-F from 9-5 ET. As Director of Content, I also welcome your comments and suggestions for new features and video topics.<br><br>

                            Enjoy your training!<br><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" alt="Chris's signature" style="width: 100px; height: 55px;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" data-mce-style="width: 100px; height: 55px;"><br>
                            Dr. Chris Thurber<br> 
                            EOT Co-Founder &amp;<br> 
                            Director of Content
                          </textarea>
                          <br /><br />
                            <input type="hidden" name="email" value="<?= $email ?>" />
                            <input type="hidden" name="org_id" value="<?= $org_id ?>" />
                            <input type="hidden" name="target" value="<?= $target ?>" />
                            <input type="hidden" name="name" value="<?= $name ?>" />
                        </td>
                      </tr>
                    </table>
                </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');">
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a active='0' acton="send_message" rel="submit_button" >
                  <div style="height:15px;padding-top:2px;"> Send Message</div>
                </a>
              </div>
            </div>      
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "add_staff_to_group")
        {        
            $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['group_name'],FILTER_SANITIZE_STRING);
            $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT); 
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); 
            $users_info = get_users( array('meta_key' => 'org_id',
                                           'meta_value' => $org_id,
                                           'role' => 'student'
            ));
            $learners = array(); // Lists of learners.
            if( count($users_info) > 0 )
            {
              if($users_info && count($users_info) > 0)
              {
                foreach ($users_info as $user_info) 
                {
                  $user['first_name'] = get_user_meta ( $user_info->id, "first_name", true);
                  $user['last_name'] = get_user_meta ( $user_info->id, "last_name", true);
                  $user['email'] = $user_info->user_email;
                  $user['id'] = $user_info->ID;
                  array_push($learners, $user);
                }
              }
              usort($learners, "sort_first_name"); // sort learners by first name.
              global $wpdb;
              $enrollments = $wpdb->get_results("SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE course_id = $course_id", ARRAY_A); // Lists of enrollments base on course ID
                $user_ids_in_course = array_column($enrollments, 'user_id'); // Lists of Ids that are enrolled in the specified course.
                ?>
                <div class="title">
                    <div class="title_h2"><?= $course_name ?></div>
                </div>
                <div class="middle" style="padding:0px;clear:both;">
                    <div class="fixed_fb_width">
                        <form id="add_staff_to_group" frm_name="add_staff_to_group" rel="submit_form" hasError=0> 
                            <div  id="staff_listing" display="staff_list" group_id="null" class="holder osX">
                                <div  id="staff_listing_pane" class="scroll-pane" style="width: 600px">  
                                    <div style="width:100%;">
                                        <div class="errorbox" style="display:none;"></div>
                                        <?php 
                                            foreach($learners as $user)
                                            {
                                              $name = $user['first_name'] . " " . $user['last_name']; // Learner's First and last name.
                                              $email = $user['email']; // Learner's email.
                                              $user_id = $user['id']; // Learner's user ID
                                              $nonce = wp_create_nonce ('add/deleteEnrollment-userEmail_' . $email);
                                              if(in_array($user_id, $user_ids_in_course))
                                              {      
                                        ?>
                                                <div class="staff_and_assignment_list_row" style="width:600px;padding:7px 155px 7px 5px;background-color:#D7F3CA" >  
                                                    <span class="staff_name" style="font-size:12px;"><?= $name ?></span> / 
                                                    <span class="staff_name" style="font-size:12px;"><?= $email ?></span>
                                                    <div style="width:140px;text-align:center;float:right;padding-right:35px;">
                                                        <a selected=1 class="add_remove_btn" collection="add_remove_from_group" group_id="<?= $course_id ?>" email="<?= $email ?>" status="remove" org_id="<?= $org_id ?>" subscription_id="<?= $subscription_id ?>" enrollment_id="<?= $enrollment_id ?>" course_name="<?= $course_name ?>" portal_subdomain="<?= $org_subdomain ?>" nonce="<?= $nonce ?>" user_id="<?= $user_id ?>" >
                                                            Remove from course
                                                        </a>
                                                    </div>
                                                    <div style="clear:both;">
                                                    </div> 
                                                </div> 

                                            <?php
                                                }
                                                else 
                                                {
                                            ?>
                                                <div class="staff_and_assignment_list_row" style="width:600px;padding:7px 155px 7px 5px;" >  
                                                    <span class="staff_name" style="font-size:12px;"><?= $name ?></span> / 
                                                    <span class="staff_emai" style="font-size:12px;"><?= $email ?></span>
                                                    <div style="width:140px;text-align:center;float:right;padding-right:35px;">
                                                        <a selected=1 class="add_remove_btn" collection="add_remove_from_group" group_id="<?= $course_id ?>" email="<?= $email ?>" status="add" org_id="<?= $org_id ?>" subscription_id="<?= $subscription_id ?>" course_name="<?= $course_name ?>" portal_subdomain="<?= $org_subdomain ?>" nonce="<?= $nonce ?>" user_id="<?= $user_id ?>">
                                                            Add to course
                                                        </a>
                                                    </div>
                                                    <div style="clear:both;">
                                                    </div> 
                                                </div> 
                                        <?php
                                                }
                                            }                      
                                        ?>

                                    </div>
                                </div>
                            </div>     
                        </form>
                    </div>
                </div>      
                <div class="popup_footer" style="background-color:#FFF; padding:15px 15px 5px 15px;">
                    <div class="buttons" style="padding-right:20px;">
                        <a active='0' acton="add_staff_to_group" rel="done_button" >
                            <!--<img src="/images/tick.png" alt=""/>--> 
                            Done
                        </a>
                        <a active='0' acton="add_staff_to_group" collection="add_remove_from_group" rel="unselect_all_button" >
                            <!--<img src="/images/selectall.png" alt=""/>-->  
                            Remove All
                        </a> 
                        <a active='0' acton="add_staff_to_group" collection="add_remove_from_group" rel="select_all_button" >
                            <!--<img src="/images/icon-user.gif" alt=""/> --> 
                            Add All
                        </a>
  <!--
                        <a active='0' acton="add_staff_to_group" href="#" id="invite_staff_fb">
                            Invite To Register
                        </a>          
                        <a active='0' acton="add_staff_to_group" id="upload_spreadsheet_fb">
                            Upload Spreadsheet
                        </a>
  -->
                        <a active='0' acton="add_staff_to_group" href='#' id="create_staff_fb">
                            <!--<img src="/images/icon-user.gif" alt=""/> --> 
                            Create Staff
                        </a>
<!--                        <a active='0' acton="add_staff_to_group" href='#' id="invite_staff_fb">
                            <img src="/images/icon-user.gif" alt=""/>  
                            Invite To Register
                        </a>-->
                        <div style="clear:both"></div>                      
                    </div>
                </div>
                <?php
            }
            else
            {
                ob_start();
        ?>
        <div class="title" style="width:320px">
            <div class="title_h2">No Staff Accounts</div>
        </div>
        <div class="middle">
            Please go to Administration > Manage Staff Accounts to add staff. 
        </div>      
        <div class="popup_footer">
            <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" class="positive">
                     <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/>
                    Ok
                </a>

            </div>
        </div>


        <?php
        $html = ob_get_clean();
        echo $html;
            }
        ?>

        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "change_course_status_form")
        {        
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $status = filter_var($_REQUEST['status'],FILTER_SANITIZE_STRING);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Publish Course?</div>
            </div>
            <div class="middle">
                <form id= "change_course_status" frm_name="change_course_status" frm_action="changeCourseStatus" rel="submit_form" hasError=0> 
                    <input type="radio" name="status" value="draft" <?php echo ( $status == "draft" ) ? 'checked' : ' '; ?> >No<br>
                    <input type="radio" name="status" value="published" <?php echo ( $status == "published" ) ? 'checked' : ' '; ?> >Yes<br>

                    <p><b>Note:</b> Once a course is published, you can no longer add/remove modules to this course. If you still need to finalize your modules, please do so prior to publishing a course.</p>

                    <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                    <input type="hidden" name="group_id" value="<?= $course_id ?>" />
                    <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                    <?php wp_nonce_field( 'change-status-org_id_' . $org_id ); ?>
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "change_course_status" rel = "submit_button" class="positive">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Save
                  </a>
                </div>
            </div>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "invite_staff_register")
        {        
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
            $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);

            // Check if the user has enough credits to add more staff members
            //if(org_has_maxed_staff($org_id, $subscription_id) )
            if(2>3)
            {
                    ob_start();
        ?>
                    <div class="title">
                        <div class="title_h2">Unable to Invite Staff</div>
                    </div>
                    <div class="middle" style ="font-size:11px;clear:both;">
                        <div class="fixed_fb_width">
                            <div class="msgboxcontainer_no_width">
                                <div class="msg-tl">
                                    <div class="msg-tr"> 
                                        <div class="msg-bl">
                                        <div class="msg-br">
                                            <div class="msgbox">
                                                <p>You have reached the maximum number of staff you can have. To add more staff, you need first to upgrade your subscription</p>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="popup_footer" style = "background-color:#FFF; padding:15px 15px 5px 15px;">
                        <div class="buttons" >        
                            <a class="back_fb" data-curr_view="invite_staff" data-prev_view="main">
                                <div style="height:15px;padding-top:2px;"> 
                                    Back
                                </div>
                            </a>
                                      
                            <div style="clear:both">
                            </div>                      
                        </div>
                    </div>
        <?php
        $html = ob_get_clean();
            }else{
                            ob_start();
        ?>
                    <div class="title">
                        <div class="title_h2">Invite Staff to Register</div>
                    </div>
                    <div class="middle" style ="font-size:11px;clear:both;">
                        <div class="fixed_fb_width">
                            <div class="msgboxcontainer_no_width">
                                <div class="msg-tl">
                                    <div class="msg-tr"> 
                                        <div class="msg-bl">
                                        <div class="msg-br">
                                            <div class="msgbox">
                                                <p>Staff will receive an e-mail with a hyperlink (containing a unique code) that lets them register
                                        and be automatically placed in the <b>Group 1</b> Group.</p>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h2>Choose an Invitation Method:</h2>
                            <ol>
                                <li>
                                    <h3>Use our Invitation Sender</h3>
                                    <ul>
                                        <li>
                                            Just copy-and-paste the staff e-mail addresses and we'll send out the invitation e-mail.
                                        </li>
                                        <li>
                                            You can <b>Track</b> those who haven't registered yet by viewing the <b>Invite List Report</b>.
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <h3>Use your own E-mail (Hotmail, GMail, etc.)</h3>
                                    <ul>
                                        <li>
                                            We give you the hyperlink to copy-and-paste into an e-mail and you send it yourself.
                                        </li>
                                        <li>
                                            You <b><u>cannot</u> Track</b> those who haven't registered yet.
                                        </li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="popup_footer" style = "background-color:#FFF; padding:15px 15px 5px 15px;">
                        <div class="buttons" >        
                            <a class="back_fb" data-curr_view="invite_staff" data-prev_view="main">
                                <div style="height:15px;padding-top:2px;"> 
                                    Back
                                </div>
                            </a>
                            <a class = "use_own_email" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Use your own Email
                                </div>
                            </a>
                            <a class = "use_invitation_email" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Use our Invitation Sender
                                </div>
                            </a>            
                            <div style="clear:both">
                            </div>                      
                        </div>
                    </div>
        <?php
        $html = ob_get_clean();
            }

        }
        else if($form_name == "use_invitation_email")
        {        
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $status = filter_var($_REQUEST['status'],FILTER_SANITIZE_STRING);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">
                    Email Addresses
                </div>
            </div>
            <div class="middle" style ="font-size:11px;clear:both;">
                <div class="fixed_fb_width">
                    <div class="msgboxcontainer_no_width">
                        <div class="msg-tl">
                            <div class="msg-tr"> 
                                <div class="msg-bl">
                                    <div class="msg-br">
                                        <div class="msgbox">
                                            <p>
                                                Paste a list of e-mail addresses in the field below.
                                            </p>
                                            <div class="small">
                                                <b>Format of E-mail Addresses:</b>
                                                 The format should be one of the following:
                                                <ul>
                                                    <li>
                                                        comma-separated list, or
                                                    </li>
                                                    <li>
                                                        one e-mail address per line (no comma), or
                                                    </li>
                                                    <li>
                                                        a combination of commas and one-per-line
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="" method="post" id="invite_emails">
                        <textarea name="emails" style="width: 600px; height: 300px;">
john@sample.com, jane@email.com
OR
john@sample.com
jane@email.com
                        </textarea>
                    </form>
                </div>
            </div>
            <div class="popup_footer" style = "background-color:#FFF; padding:15px 15px 5px 15px;">
                <div class="buttons" >        
                    <a class="back_fb update_email_textbox" data-curr_view="invite_send_email" data-prev_view="invite_staff">
                        <div style="height:15px;padding-top:2px;"> 
                            Back
                        </div>
                    </a>
                    <a class = "use_invitation_msg" >
                        <div style="height:15px;padding-top:2px;"> 
                            Next
                        </div>
                    </a>
                    <div style="clear:both">
                    </div>                      
                </div>
            </div>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "delete_staff_account")
        {        
            $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
            $staff_id = filter_var($_REQUEST['staff_id'],FILTER_SANITIZE_NUMBER_INT);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $email =  sanitize_email( $_REQUEST['email'] );
            ob_start();
        ?>
            <div class="title">
              <div class="title_h2">Delete Staff Account</div>
            </div>
            <div class="middle">
              <form id= "delete_staff_account" frm_name="delete_staff_account" frm_action="deleteStaffAccount" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                  <tr> 
                    <td class="value"> 
                      Are you sure that you want to delete this staff account? 
                      <input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
                      <input type="hidden" name="email" id="email" value="<?= $email ?>" /> 
                      <input type="hidden" name="staff_id" id="staff_id" value=" <?= $staff_id ?>" />
                      <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                        <?php wp_nonce_field( 'delete-staff_id-org_id_' . $org_id ); ?>
                    </td>
                  </tr> 
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" >
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a active = '0' acton = "delete_staff_account" rel = "submit_button" >
                  <div style="height:15px;padding-top:2px;"> Delete</div>
                </a>
              </div>
            </div>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "edit_questionnaire_conditions")
        {
            $question_number = filter_var($_REQUEST['question_number'],FILTER_SANITIZE_NUMBER_INT); //question id
            $answer_number = filter_var($_REQUEST['answer_number'],FILTER_SANITIZE_NUMBER_INT);     //answer number
            $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
            $all_videos = grabVideos();                                                             //list of all exisiting videos

            //List of all courses
            $courses = getCourses(0,0);

            //This variable will contain all course ids based on their name
            $course_ids = array();

            //this loop will put the course ids based on name into course_ids
            foreach ($courses as $course)
            {
                $course_ids[$course->course_name] = $course->id;
            }
            //var_dump($course_ids);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Conditions</div>
            </div>
            <div>
            <img style="margin-right:60em;" class="loader" src="<?= get_template_directory_uri() . "/images/loading.gif"?>" hidden>
            </div>
            <!-- question id, answer number and library id are stored here using a data structure for consistency -->
            <div class="middle" data-question="<?= $question_number ?>" data-answer="<?= $answer_number ?>" data-library="<?= $library_id ?>">
                <!-- Following 6 lines set up the tabs -->
                <ul class="tab">
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'new-staff')">New Staff</a></li>
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'returning-staff')">Returning Staff</a></li>
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'program-staff')">Program Staff</a></li>
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'supervisory-staff')">Supervisory Staff</a></li>
                </ul>
                <div id= "new-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="1">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 1);
                                //var_dump($all_conditions);
                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of New Staff course
                                $new_staff_modules = getModulesByLibrary($course_ids['New Staff']);
                                //var_dump($new_staff_modules);
                                //this variable will contain the modules for New Staff course in a desired format
                                $new_staff_videos = array();

                                //this loop helps create new_staff_videos
                                foreach ($new_staff_modules as $module) {
                                    array_push($new_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $new_staff_videos), array_diff($new_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <div id= "returning-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" frm_action="editQuestionnaireConditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="2">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 2);

                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of Returning Staff course
                                $returning_staff_modules = getModulesByLibrary($course_ids['Returning Staff']);

                                //this variable will contain the modules for Returning Staff course in a desired format
                                $returning_staff_videos = array();

                                //this loop helps create returning_staff_videos
                                foreach ($returning_staff_modules as $module) {
                                    array_push($returning_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $returning_staff_videos), array_diff($returning_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <div id= "program-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" frm_action="editQuestionnaireConditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="3">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 3);

                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of Program Staff course
                                $program_staff_modules = getModulesByLibrary($course_ids['Program Staff']);

                                //this variable will contain the modules for Program Staff course in a desired format
                                $program_staff_videos = array();

                                //this loop helps create program_staff_videos
                                foreach ($program_staff_modules as $module) {
                                    array_push($program_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $program_staff_videos), array_diff($program_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <div id= "supervisory-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" frm_action="editQuestionnaireConditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="4">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 4);

                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of Supervisory Staff course
                                $supervisory_staff_modules = getModulesByLibrary($course_ids['Supervisory Staff']);

                                //this variable will contain the modules for Supervisory Staff course in a desired format
                                $supervisory_staff_videos = array();

                                //this loop helps create supervisory_staff_videos
                                foreach ($supervisory_staff_modules as $module) {
                                    array_push($supervisory_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $supervisory_staff_videos), array_diff($supervisory_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
            </div>
            </img>
            <div class="popup_footer">
                <div class="buttons">
                  <a onclick="jQuery(document).trigger('close.facebox');" class="positive">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/>
                      Done
                  </a>
                </div>
            </div>
            <script>

                //This function deals with the tab functionality
                function openCity(evt, cityName) {
                    // Declare all variables
                    var i, tabcontent, tablinks;

                    // Get all elements with class="tabcontent" and hide them
                    tabcontent = document.getElementsByClassName("tabcontent");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = "none";
                    }

                    // Get all elements with class="tablinks" and remove the class "active"
                    tablinks = document.getElementsByClassName("tablinks");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].className = tablinks[i].className.replace(" active", "");
                    }

                    // Show the current tab, and add an "active" class to the link that opened the tab
                    document.getElementById(cityName).style.display = "block";
                    evt.currentTarget.className += " active";
                }

                //This function will trigger when a radio button is checked
                $('input:radio').change(function(){
                    var video_id = $(this).parent().attr('data-id');                                                //video id
                    var change = $(this).parent().attr('data-change');                                              //this variable contains "Add" or "Remove"
                    var course_name_id = $(this).parent().parent().parent().parent().attr('data-course-name-id');   //course_name_id
                    var question_number = $('.middle').attr('data-question');                                       //question id
                    var answer_number = $('.middle').attr('data-answer');                                           //answer number
                    var library_id = $('.middle').attr('data-library');                                             //library id

                    //data is used for the ajax call
                    //contains variable needed by the PHP function to make changes to the database
                    var data = {
                        action: 'changeCondition',
                        video_id: video_id,
                        question_number: question_number,
                        answer_number: answer_number,
                        course_name_id: course_name_id,
                        library_id: library_id,
                        change: change
                    };

                    //url for ajax call
                    var url =  ajax_object.ajax_url;

                    //hide radio buttons and show loading gif
                    var tempScrollTop = $(window).scrollTop();
                    $('.middle, .loader').toggle();

                    //ajax call to change conditions in database
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'json',
                        data: data,
                        success:
                        function(data)
                        {
                            //show radio buttons and hide loading gif
                            $('.middle, .loader').toggle();
                            $(window).scrollTop(tempScrollTop);
                        },
                        error:
                        function(data)
                        {
                            alert("Something went wrong");
                        }
                    });
                });
            </script>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "delete_question_questionnaire")
        {
            $question_number = filter_var($_REQUEST['question_number'],FILTER_SANITIZE_NUMBER_INT); //question id
            $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
            ob_start();
        ?>
            <div class="title">
              <div class="title_h2">Delete Question</div>
            </div>
            <div class="middle">
              <form id= "delete_question" frm_name="delete_question" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                  <tr> 
                    <td class="value"> 
                      Are you sure that you want to delete this question? 
                    </td>
                  </tr> 
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" >
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a rel = "submit_button" >
                  <div class = "delete-question" data-question="<?= $question_number ?>" data-library="<?= $library_id ?>" style="height:15px;padding-top:2px;"> Delete</div>
                </a>
              </div>
            </div>
            <script>
                $('div.delete-question').click(function()
                {
                    var question_id = $(this).attr('data-question');        //question id
                    var library_id = $(this).attr('data-library');          //library id

                    var data = {
                        action: 'deleteQuestion',
                        question_id: question_id,
                        library_id: library_id
                    };

                    //url for ajax call
                    var url =  ajax_object.ajax_url;

                    //ajax call to delete question in database
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'json',
                        data: data,
                        success:
                        function(data)
                        {
                            jQuery(document).trigger('close.facebox');
                            location.reload();
                        },
                        error:
                        function(data)
                        {
                            alert("Something went wrong");
                        }
                    });
                });
            </script>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "add_question_questionnaire")
        {
            $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
            ob_start();
        ?>
            <div class="title">
              <div class="title_h2">Add Question</div>
            </div>
            <div class="middle">
              <form id= "add_question" frm_name="add_question" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                  <tr> 
                    <td class="value"> 
                      Question: <input id="question" type="text">
                    </td>
                  </tr>
                    <td>
                        <br>
                    </td>
                  <tr>
                  </tr>
                    <td>
                        <br>
                    </td>
                  <tr>
                    <td>
                        Answers:
                    </td>
                  </tr>
                  </tr>
                    <td>
                        <br>
                    </td>
                  <tr>
                  <tr>
                    <td>
                        <div><i class="fa fa-minus-square minus" aria-hidden="true"></i>   <i class="fa fa-plus-square plus" aria-hidden="true"></i>   <input class="answer" type="text"></div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                        <div><i class="fa fa-minus-square minus" aria-hidden="true"></i>   <i class="fa fa-plus-square plus" aria-hidden="true"></i>   <input class="answer" type="text"></div>
                    </td>
                  </tr>
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" >
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a rel = "submit_button" >
                  <div class = "add-question" data-library="<?= $library_id ?>" style="height:15px;padding-top:2px;"> Add</div>
                </a>
              </div>
            </div>
            <script>

                $('div.add-question').click(function()
                {
                    var library_id = $(this).attr('data-library');      //library id
                    var question = $('input#question').val();           //question text

                    //gather all answers
                    var all_answers_array = $('table.form').find('.answer').map(function() { return $(this).val(); }).get();
                    var all_answers_object = {};

                    //This loop will make an object so that all answers can be converted to JSON
                    for(var i = 1; i<=all_answers_array.length;i++)
                    {
                        all_answers_object[i.toString()] = all_answers_array[i-1];
                    }

                    //this will convert the object to JSON string
                    var answer = JSON.stringify(all_answers_object);

                    var data = {
                        action: 'addQuestion',
                        library_id: library_id,
                        question: question,
                        answer: answer
                    };

                    //url for ajax call
                    var url =  ajax_object.ajax_url;

                    //ajax call to add question in database
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'json',
                        data: data,
                        success:
                        function(data)
                        {
                            jQuery(document).trigger('close.facebox');
                            location.reload();
                        },
                        error:
                        function(data)
                        {
                            alert("Something went wrong");
                        }
                    });
                });

                $('i.plus').live("click", function()
                {
                    $(this).parent().parent().parent().after('<tr><td><div><i class="fa fa-minus-square minus" aria-hidden="true"></i>   <i class="fa fa-plus-square plus" aria-hidden="true"></i>   <input class="answer" type="text"></div></td></tr>');
                });

                $('i.minus').live("click", function()
                {   
                    var $answers=$('input.answer');
                    //console.log($answers.length);
                    if($answers.length>1){
                        $(this).parent().parent().parent().remove();
                    }
                });
            </script>
        <?php
        $html = ob_get_clean();
        }else if($form_name == "use_invitation_msg")
        {
            global $current_user;
            get_currentuserinfo();
            $user_email = $current_user->user_email;
            $org_id =  filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
            $subscription_id =  filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
            $group_id =  filter_var($_REQUEST['group_id'], FILTER_SANITIZE_NUMBER_INT);
            $group_name =  filter_var($_REQUEST['group_name'], FILTER_SANITIZE_STRING);
            
            
            ob_start();
            ?>
                        <div class="title">
              <div class="title_h2">Invite Staff To Register</div>
            </div>
            <div class="middle">
              <form id= "use_invitation_msg" frm_name="use_invitation_msg" frm_action="sendInvitationMsg" rel="submit_form" hasError=0> 
                <table padding=0 class="form">
                    <tr>
                        <td>
                            <div class="fixed_fb_width">
                            <div class="msgboxcontainer_no_width">
                                <div class="msg-tl">
                                      <div class="msg-tr"> 
                                            <div class="msg-bl">
                                                  <div class="msg-br">
                                                          <div class="msgbox">
                                                                  <p>This message (below) will be sent to your staff. For your convenience we've written a sample letter that you can customize to your liking. Once you are done, click <strong>Send Invitations</strong>. <br><br>Your message <strong>must</strong> include the following code:<br>http://expertonlinetraining.net/register.html?key=EME4YG</p>
                                                          </div>
                                                </div>
                                          </div>
                                         </div>
                                </div>
		           </div>
                            </div>
                        </td>
                    </tr>
                  <tr> 
                    <td class="value"> 
                      <textarea class="tinymce" id="composed_message" name="msg" style="margin-left:1px;width: 100%; height: 300px">
                            <b>Welcome</b>, <br><br>
                           
                            <p><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" alt="EOT Logo" style="width: 125px; height: 94px; float: left;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" data-mce-style="width: 150px; height: 113px; float: left;"> 
                            
                            <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> has invited you to join the camp with this code:<br><br>

                            <strong><?= wp_hash($user_email);?></strong>

                            <br><br>
                            <b>Got Questions?</b> If you get stuck, call us at <b>877-237-3931</b>! The EOT Customer Success team is on duty M-F from 9-5 ET. As Director of Content, I also welcome your comments and suggestions for new features and video topics.<br><br>

                            Enjoy your training!<br><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" alt="Chris's signature" style="width: 100px; height: 55px;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" data-mce-style="width: 100px; height: 55px;"><br>
                            Dr. Chris Thurber<br> 
                            EOT Co-Founder &amp;<br> 
                            Director of Content
                          </textarea>
                    </td>
                  </tr>
                  <tr>
                      <td>
                            <input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
                            <input type="hidden" name="subscription_id" id="subscription_id" value="<?= $subscription_id ?>" /> 
                            <input type="hidden" name="group_id" id="group_id" value=" <?= $group_id ?>" />
                            <input type="hidden" name="group_name" value="<?= $group_name ?>" />
                      </td>
                  </tr>
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                  <a class="back_fb update_email_textbox" data-curr_view="invite_send_msg" data-prev_view="invite_send_email">
                        <div style="height:15px;padding-top:2px;"> 
                            Back
                        </div>
                    </a>
                <a class="send_invitations" >
                  <div style="height:15px;padding-top:2px;"> Send Invitation</div>
                </a>
              </div>
            </div>
            <?php
            $html = ob_get_clean();
        }else if($form_name == "use_own_email")
        {
            global $current_user;
            get_currentuserinfo();
            $user_email = $current_user->user_email;
            $org_id =  filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
            $subscription_id =  filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
            $group_id =  filter_var($_REQUEST['group_id'], FILTER_SANITIZE_NUMBER_INT);
            $group_name =  filter_var($_REQUEST['group_name'], FILTER_SANITIZE_STRING);
            
            
            ob_start();
            ?>
                        <div class="title">
              <div class="title_h2">Use your own email account to send message</div>
            </div>
            <div class="middle">
              <form id= "use_invitation_msg" frm_name="use_own_email" frm_action="sendInvitationMsg" rel="submit_form" hasError=0> 
                <table padding=0 class="form">
                    <tr>
                        <td>
                            <div class="fixed_fb_width">
                            <div class="msgboxcontainer_no_width">
                                <div class="msg-tl">
                                      <div class="msg-tr"> 
                                            <div class="msg-bl">
                                                  <div class="msg-br">
                                                          <div class="msgbox">
                                                                  <p>For your convenience we've written a sample letter that you can customize to your liking. Once you are done, click <strong>Send Invitations</strong>. <br><br>Your message <strong>must</strong> include the following code:<br>http://expertonlinetraining.net/register.html?key=EME4YG</p>
                                                          </div>
                                                </div>
                                          </div>
                                         </div>
                                </div>
		           </div>
                            </div>
                        </td>
                    </tr>
                  <tr> 
                    <td class="value"> 
                      <div class="" id="composed_message" name="message" style="margin-left:1px;height: 300px;max-width:600px">
                            <b>Welcome</b>, <br><br>
                           
                            <p><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" alt="EOT Logo" style="width: 125px; height: 94px; float: left;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" data-mce-style="width: 150px; height: 113px; float: left;"> 
                            
                            <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> has invited you to join the camp with this code:<br><br>

                            
                            <b>Got Questions?</b> If you get stuck, call us at <b>877-237-3931</b>! The EOT Customer Success team is on duty M-F from 9-5 ET. As Director of Content, I also welcome your comments and suggestions for new features and video topics.<br><br>

                            Enjoy your training!<br><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" alt="Chris's signature" style="width: 100px; height: 55px;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" data-mce-style="width: 100px; height: 55px;"><br>
                            Dr. Chris Thurber<br> 
                            EOT Co-Founder &amp;<br> 
                            Director of Content
                          </div>
                    </td>
                  </tr>
                  <tr>
                      <td>
                            <input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
                            <input type="hidden" name="subscription_id" id="subscription_id" value="<?= $subscription_id ?>" /> 
                            <input type="hidden" name="group_id" id="group_id" value=" <?= $group_id ?>" />
                            <input type="hidden" name="group_name" value="<?= $group_name ?>" />
                      </td>
                  </tr>
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                  <a class="back_fb update_email_textbox" data-curr_view="use_own_email" data-prev_view="invite_staff">
                        <div style="height:15px;padding-top:2px;"> 
                            Back
                        </div>
                    </a>
                <a onclick="jQuery(document).trigger('close.facebox');" class="positive">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/>
                      Done
                  </a>
              </div>
            </div>
            <?php
            $html = ob_get_clean();
        }
        else
        {
            $html = "<p>Invalid form.</p>";
        }
    }
    else
    {   
        $error_path = get_site_url() . "/subscribe/dashboard/?part=error";
        // Someone is fooling our system. Redirect to error page.
        header("Location: " . $error_path . "");
        die();
    }

    echo $html;

    wp_die();
}
