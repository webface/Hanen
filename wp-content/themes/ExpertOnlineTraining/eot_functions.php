<?php
/********************************************************************************************************
 * Get all exsisting libraries in wp_library base on Library ID
 * @param int $library_id - The library id
 * @return array objects - the library id, name and price.
 *******************************************************************************************************/
function getLibraries ($library_id = 0) 
{
	global $wpdb;
	$sql = "SELECT ID, name, price from " . TABLE_LIBRARY;
	$inc_library = ($library_id > 0) ? " where `ID` = " . $library_id . " " : "";
	$sql .= $inc_library;
	$results = ($library_id > 0) ? $wpdb->get_row ($sql) : $wpdb->get_results ($sql);
	return $results;
}

/*
 * Get the library based on the library id
 * return the Library information
 */
function getLibrary ($library_id = 0) 
{
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
function getSubscriptions($subscription_id = 0, $library_id = 0, $active = 0, $org_id = 0, $start_date = '0000-00-00', $end_date = '0000-00-00', $year_end_date = '0000') 
{
  global $wpdb;
  $sql = "SELECT * from " . TABLE_SUBSCRIPTIONS;

  if($subscription_id) 
  {	
  	// looking for a specific subscription
    $sql .=  " WHERE `ID` = " . $subscription_id;
  }
  else if($library_id)
  {
  	// looking for all subscriptions for a specific library
      $sql .= " WHERE library_id = " . $library_id;
  }
  else if($org_id)
  {
    // looking for all subscriptions for organization ID
    $sql .= " WHERE org_id = " . $org_id; 
  }
  else if($start_date != "0000-00-00" && $end_date != "0000-00-00")
  {
    $sql .= "  WHERE trans_date >= '" . $start_date . "' AND trans_date <= '" . $end_date . "'";
  }
  
  $sql .= ($year_end_date != "0000") ? " AND YEAR(end_date) = $year_end_date" : "";

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
          <?php echo ($download) ? 'dom: \'Blfrtip\',' : ""; ?>
          <?php echo ($download) ? '"buttons": [ {extend: \'csv\',title: \''.$filename.'\',messageTop:\'Expert Online Training\'}, {extend: \'excel\',title: \''.$filename.'\',messageTop:\'Expert Online Training\'}],' : ""; ?>
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
        //listen for paged event and re-initialize facebox.
        $('#<?=$id?>').on( 'page.dt', function () {
            setTimeout(function(){$('a[rel*=facebox]').facebox();},500);
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
 *   Display Help Videos
 */  
add_action('wp_ajax_displayHelp', 'displayHelp_callback');
function displayHelp_callback() 
{
    if( isset ( $_REQUEST['video_id'] ) && $_REQUEST['video_id'] > 0)
    {
      $video = getHelpVideoById(filter_var($_REQUEST['video_id'], FILTER_SANITIZE_NUMBER_INT));
      ob_start();
?>
      <div>
        <div class="title">
          <div class="title_h2"><?= $video->title; ?></div>
        </div>
        <div class='buttons'>
          <a onclick="videojs('help_video_<?= $video->ID; ?>').pause(); jQuery(document).trigger('close.facebox');" class='negative' style='margin-top: -30px; margin-right: 0px; padding-top: 6px; padding-right: 7px;'>
            <img src='<?php echo get_template_directory_uri() . '/images/cross.png' ?>' alt='' style='margin: 0px !important;'/>
          </a>
        </div>
      </div>
      <div style="padding: 20px;">
        <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js"></script>
        <video id="help_video_<?= $video->ID; ?>" class="video-js vjs-default-skin" preload="auto" width="640" height="480" data-setup='{"controls": true}'>
          <source src="https://eot-output.s3.amazonaws.com/<?= $video->video_filename; ?>" type='video/mp4'>
          <p class="vjs-no-js">
            To view this video please enable JavaScript, and consider upgrading to a web browser that
            <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
          </p>        
        </video>       
      </div>
<?php
      $html = ob_get_clean();
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

  function __construct($id, $title, $category)
  {
    $this->id = $id;
    $this->title = $title;
    $this->category = $category;
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
  $question_number = filter_var($_REQUEST['question_number'], FILTER_SANITIZE_NUMBER_INT);

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
  $question_number = filter_var($_REQUEST['question_number'], FILTER_SANITIZE_NUMBER_INT);

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
  $video_id = filter_var($_REQUEST['video_id'], FILTER_SANITIZE_NUMBER_INT);               //video id
  $question_number = filter_var($_REQUEST['question_number'], FILTER_SANITIZE_NUMBER_INT); //question id
  $answer_number = filter_var($_REQUEST['answer_number'], FILTER_SANITIZE_NUMBER_INT);     //answer number
  $course_name_id = filter_var($_REQUEST['course_name_id'], FILTER_SANITIZE_NUMBER_INT);   //course id
  $library_id = filter_var($_REQUEST['library_id'], FILTER_SANITIZE_NUMBER_INT);           //library id
  $change = filter_var($_REQUEST['change'], FILTER_SANITIZE_STRING);                       //this variable contains 'Add' or 'Remove'

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
  $question_id = filter_var($_REQUEST['question_id'], FILTER_SANITIZE_NUMBER_INT);         //question id
  $library_id = filter_var($_REQUEST['library_id'], FILTER_SANITIZE_NUMBER_INT);           //library id

  global $wpdb;

  $sql = "DELETE FROM " . TABLE_QUESTIONS . " WHERE id = " . $question_id . " AND library_id = " . $library_id;
  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

add_action('wp_ajax_addQuestion', 'addQuestion_callback');

//this function adds questions to the questionnaire
function addQuestion_callback()
{
  $library_id = filter_var($_REQUEST['library_id'], FILTER_SANITIZE_NUMBER_INT);           //library id
  $question = $_REQUEST['question'];                                                      //question
  $answer = $_REQUEST['answer'];                                                          //answer

  global $wpdb;

  $sql = "INSERT INTO " . TABLE_QUESTIONS . " (library_id, question, answer) VALUES (" . $library_id . ", \"" . $question . "\", \"" . $answer . "\")";
  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

/********************************************************************************************************
 * Filter out and return an array of user types
 * @param ARRAY $users - an array of associative arrays of users (got from JSON decode)
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
 * @param ARRAY $users - an array of associative arrays of users (got from JSON decode)
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
        $user_info['id'] = $user['ID'];
        global $wpdb;
        $sign_in_count = $wpdb->get_var("SELECT COUNT(*) FROM " . TABLE_TRACK . " WHERE type = 'login' AND user_id = ".$user['ID']);
        $user_info['sign_in_count'] = $sign_in_count;

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
  $meta_key = filter_var($_REQUEST['meta_key'], FILTER_SANITIZE_STRING);
  $meta_value = filter_var($_REQUEST['meta_value'], FILTER_SANITIZE_STRING);
  $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);
  $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
  $type = filter_var($_REQUEST['type'], FILTER_SANITIZE_STRING);

  if ($type == "post_meta")
  {
    update_post_meta($org_id, $meta_key, $meta_value);
  }
  else if ($type == "user")
  {
    update_user_meta($user_id, $meta_key, $meta_value);
  }
  else if($type == "post" && $meta_key == 'post_title')
  {
    $posts_info = array(
      'ID'           => $org_id,
      'post_title'   => $meta_value
    );
    wp_update_post( $posts_info );
  }
  wp_die();
}

add_action('wp_ajax_switchUser', 'switchUser_callback');

//this function switches the user
function switchUser_callback()
{
  $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);          //user id

  switch_to_user($user_id);

  wp_die();
}

add_action('wp_ajax_updateSubscriptionSettings', 'updateSubscriptionSettings_callback');

//This function updates the subscription fields
function updateSubscriptionSettings_callback()
{
  global $wpdb;
  
  $id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
  $field = filter_var($_REQUEST['field'], FILTER_SANITIZE_STRING);
  $value = filter_var($_REQUEST['value'], FILTER_SANITIZE_STRING);

  $sql = "UPDATE " . TABLE_SUBSCRIPTIONS . ' SET ' . $field . ' = "' . $value . '" WHERE id = ' . $id;
  $wpdb->query ($wpdb->prepare ($sql));

  wp_die();
}

add_action('wp_ajax_createSubdomainAll', 'createSubdomainAll_callback');

//This function creates the subdomain on learnupon and links to the user
function createSubdomainAll_callback()
{
  
  $org_subdomain = SUBDOMAIN_PREFIX.filter_var($_REQUEST['subdomain'], FILTER_SANITIZE_STRING);   //subdomain
  $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);                          //org id
  $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);                        //user id
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
  $original_subdomain = filter_var($_REQUEST['original_subdomain'], FILTER_SANITIZE_STRING);     //original subdomain
  $subdomain = filter_var($_REQUEST['subdomain'], FILTER_SANITIZE_STRING);                       //new subdomain
  $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);                       //user id
  $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);                         //org id
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
  $new_subdomain = filter_var($_REQUEST['new_subdomain'], FILTER_SANITIZE_STRING);               //new subdomain
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
  $first_name = isset($_REQUEST['first_name']) ? filter_var($_REQUEST['first_name'], FILTER_SANITIZE_STRING) : 'John';
  $last_name = isset($_REQUEST['last_name']) ? filter_var($_REQUEST['last_name'], FILTER_SANITIZE_STRING) : 'Doe';
  $email = isset($_REQUEST['email']) ? sanitize_email($_REQUEST['email']) : 'johndoe@expertonlinetraining.com';
  $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
  $create_user = isset($_REQUEST['create_user']) ? filter_var($_REQUEST['create_user'], FILTER_SANITIZE_NUMBER_INT) : 1; // int: 1 to create user, 0 or null to update

  $new_user = array(
    'user_login' => $email,
    'user_email' => $email,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'role' => 'salesrep'
  );

  // if we have a password, include it in the user data
  if($password)
  {
    $new_user['user_pass'] = $password;
  }

  // check if we need to create the user or update
  if($create_user)
  {
    $user_id = wp_insert_user ($new_user);
  }
  else
  {
    if (isset($_REQUEST['user_id']))
    {
      $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);
      $new_user['ID'] = $user_id;
      $new_user['display_name'] = $first_name . " " . $last_name;
      $user_id = wp_update_user ($new_user);
    }
    else
    {
      $result['status'] = 0;
      $result['message'] = "updateCreateSalesRep ERROR: No user id provided.";
    }
  }

  // check for errors
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
    'Youth Development and Play' => 2,
    'Mental Health and Behavior' => 3,
    'Physical and Emotional Safety' => 4,
    'Supervision' => 5,
    'Creative Literacy' => 6
  );

  // make sure there is a value for a category even it its not in our array above
  //$order[$a] = (!isset($order[$a])) ? 100 : $order[$a];
  //$order[$b] = (!isset($order[$b])) ? 100 : $order[$b];

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
// returns an array with status boolean true/false
function verifyUserAccess ()
{

  global $current_user;
  global $wpdb;

  // get the subscription ID if exists
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
  { 
    $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
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
      $sql = "SELECT * FROM " . TABLE_SUBSCRIPTIONS . " WHERE ID = " . $subscription_id;
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
      $sql = "SELECT * FROM " . TABLE_SUBSCRIPTIONS . " WHERE ID = " . $subscription_id;
      $results = $wpdb->get_row ($sql);
      if ($results)
      {
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
  else if (current_user_can("is_student"))
  {
    $user_id = $current_user->ID;
    // make sure a subscription ID was passed in and this user has access to it.
    if ($subscription_id)
    {
      $sql = "SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE user_id = $user_id AND subscription_id = $subscription_id";
      $results = $wpdb->get_row ($sql);
      if ($results)
      {
        return array ( 'status' => 1 );
      }
      else
      {
        return array( 'status' => 0, 'message' => 'User not in this subscription.' );
      }
    }
    else
    {
      return array( 'status' => 0, 'message' => 'ERROR: student without a subscription ID' );
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
    $subscription_id=filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
    $target = filter_var($_REQUEST['target'], FILTER_SANITIZE_STRING); // Target. User selected option.
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

    if($subscription_id <= 0 && $start_date != "0000-00-00" && $end_date != "0000-00-00")
    {
      $sql .= " WHERE date >= '$start_date' AND date <= '$end_date'";
    }
    else if($start_date != "0000-00-00" && $end_date != "0000-00-00")
    {
      $sql .= " AND date >= '$start_date' AND date <= '$end_date'";
    }

    $results = $wpdb->get_results ($sql);
    return $results;
}

/**
 * Get certificates for a specific user by user ID alone, or either a certificate ID or course id
 * @param int $user_id = the WP User ID
 * @param int $certificate_id = Ther certificate ID
 * @param int $course_id = the course ID
 */ 
function getCertificates($user_id = 0, $certificate_id = 0, $course_id = 0) 
{
  global $wpdb;
  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
  $certificate_id = filter_var($certificate_id, FILTER_SANITIZE_NUMBER_INT);
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

  // make sure we get a user ID at least
  if( $user_id == 0 )
  {
    return array();
  }

  $sql = "SELECT * FROM " . TABLE_CERTIFICATES . " WHERE user_id = " . $user_id;
  if ($certificate_id > 0)
  {
    $sql .= " AND ID = " . $certificate_id;
  }
  else if($course_id > 0)
  {
    $sql .= " AND course_id = " . $course_id;
  }
  $result = ($certificate_id == 0 && $course_id == 0) ? $wpdb->get_results ($sql, ARRAY_A) : $wpdb->get_row ($sql, ARRAY_A);
  return $result;
}

/******************************************
 * Insert the certificate data into the certificate table
 * $user_id = the WP User ID
 * $data = an array with cerfiticate data
 ******************************************/ 
function setCertificate( $user_id = 0, $data = array() )
{
  extract($data);
  /*
   * Variables required in $data
   * org_id - The organization ID
   * course_id - The Learnupon Course ID
   * course_name - The course name
   * filename - Certificate file name
   * status - conferred / pending
   */
 
  if($user_id <= 0)
  {
    return false;
  }

  global $wpdb;
  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
  $today = current_time('Y-m-d');
  $sql = "INSERT INTO " . TABLE_CERTIFICATES . " (user_id, org_id, course_id, course_name, filename, date_created, status) 
          VALUES ($user_id, $org_id, $course_id, '$course_name', '$filename', '$today', '$status')";
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
 * $course_id = the course id
 ******************************************/ 
function getCertificatesSyllabus($user_id = 0, $course_id = 0) 
{
  global $wpdb;
  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

  // check that we got a user id
  if( $user_id == 0 )
  {
    return array();
  }

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
function addPendingUsers ($staff_data = array(), $org_id = 0, $message = '', $subject = '', $isEmail = 0, $directorname = '', $subscription_id = 0)
{  
  global $wpdb;
  $remove_chars = array("'", '"');
  $ubscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);
  foreach ($staff_data as $staff)
  {
    $first_name = str_replace($remove_chars, "", trim($staff[0]));
    $last_name = str_replace($remove_chars, "", trim($staff[1]));
    $email = trim($staff[2]);
    $password = trim($staff[3]);
    $courses = json_encode(array(trim($staff[4]), trim($staff[5]), trim($staff[6]), trim($staff[7])));
    $variables = json_encode(compact("first_name", "last_name", "directorname"));
    $sql = "INSERT INTO " . TABLE_PENDING_USERS . " (org_id, variables, email, password, courses, subject, message, isEmail, subscription_id) 
    VALUES 
    ($org_id, '$variables', '$email', '$password', '$courses', '".addslashes($subject)."', '" . addslashes($message) . "', $isEmail, $subscription_id)";
    $result = $wpdb->query ($sql);
    if(!$result)
    {
      return false;
    }
  }

  return true;
}

// triggers mass mailing from ajax.
add_action('wp_ajax_mass_register_ajax', 'mass_register_ajax_callback');
function mass_register_ajax_callback()
{
    $org_id = (isset($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $result = processUsers(PENDING_USERS_LIMIT, $org_id);

    if ($org_id == 0)
    {
      return $result; 
    }

    echo json_encode($result);
    wp_die();
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
    $sql = "SELECT * FROM " . TABLE_PENDING_EMAILS . " WHERE time < DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY ID asc limit " . $limit;
  }
  else   //means this function is being called from mass email page and we should target the specific org_id
  {
    $sql = "SELECT * FROM " . TABLE_PENDING_EMAILS . " WHERE org_id = " . $org_id . " ORDER BY ID asc limit " . $limit;
  }

  $recipients = $wpdb->get_results($sql, ARRAY_A);
  $results = sendMail('massmail', $recipients, $data);

  if(isset($results['status']) && $results['status'])
  {
    foreach($recipients as $recipient)
    {
      $sql = "DELETE FROM " . TABLE_PENDING_EMAILS . " WHERE ID = " . $recipient['ID'];
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
      $sql = "DELETE FROM " . TABLE_PENDING_EMAILS . " WHERE ID = " . $recipient['ID'];
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

// @TODO fix function when merging with Tommy's version
//processes the firt PENDING_USERS_LIMIT users from the temperory table
function processUsers ($limit = PENDING_USERS_LIMIT, $org_id = 0)
{
  global $wpdb;

  // check if we're calling the function from cron or a user. If cron then org_id = 0 and we should look for records older than PENDING_USERS_CRON_TIME_LIMIT hours ago 
  if($org_id == 0)
  {
    $sql = "SELECT * FROM " . TABLE_PENDING_USERS . " WHERE time < DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY ID ASC LIMIT " . $limit;
  }
  else   //means this function is being called from spreadsheet upload page and we should target the specific org_id
  {
    $sql = "SELECT * FROM " . TABLE_PENDING_USERS . " WHERE org_id = " . $org_id . " ORDER BY ID ASC LIMIT " . $limit;
  }
  $staff_data = $wpdb->get_results($sql);
  /****************************************************************
   * This process the savings of the user accounts 
   * into WP User Database 
   ****************************************************************/
  $recepients = array(); // List of recepients
  $emailError = '';
  $org_id = 0;        // the staff's org id
  $isEmail = 0;             //boolean indicating whether we should email the users or not
  //$subscription_id;     //subscription id
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
    $subscription_id = $staff->subscription_id;//subscription ID
    $message = stripslashes($staff->message);
    $isEmail = $staff->isEmail; //boolean to see if email should be sent
    $subject = stripslashes($staff->subject); //subject of the email
    $data = compact("org_id", "first_name", "last_name", "email", "password");
    $courses = array_filter(json_decode($staff->courses)); // the courses to enroll the user into. array filter removes empty values. json decode turns the json object into its original data type (array in this case)
    $subscription_id = $staff->subscription_id; // the subscription id
    
    // check if user exists in WP, if yes make sure they are in the same org. 
    if ( email_exists($email) )
    {
      $staff_id = get_user_by('email', $email)->ID;
      if ( get_user_meta($staff_id, 'org_id', true) == $org_id )
      {
        $result = createWpUser($data, 'student'); // Create WP user
        if (isset($result['success']) && $result['success'])
        {
          // enroll user in courses 
          $result2 = enrollUserInCourses($courses, $org_id, $email, $subscription_id);
          if (isset($result2['status']) && !$result2['status'])
          {
            // ERROR in enrolling user
            $has_error = true;
            $has_user_error = true;
//              echo "<p>ERROR: Could not enroll $email into one or more courses. ".$result2['message']."</p>";
            $import_status .= "$email - ERROR: User exists in WP. But couldnt enroll into course: ".$result2['message']."<br>";
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
      // if user doesnt exist in WP, create user in WP 
      $result = createWpUser($data,'student'); // Create WP and LU user

      if (isset($result['success']) && $result['success'])
      {
        // enroll user in courses
        $result2 = enrollUserInCourses($courses, $org_id, $email, $subscription_id);
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
          '%%name%%' => $name,
          '%%email%%' => $email,
          '%%your_name%%' => $directorname,
          '%%logininfo%%' => $loginInfo,
          '%%directorname%%'  =>  $directorname,
          '%%campname%%'  =>  $campname,
          '%%numvideos%%' =>  NUM_VIDEOS,
      );

      /* Replace %%VARIABLE%% using vars*/
      $message = str_replace(array_keys($vars) , $vars , $message);

      $recepient = array (
          'name' => $name,
          'email' => $email,
          'message' => $message,
          'subject' => $subject
      );
      array_push($recepients, $recepient);
    }

    $sql = "DELETE FROM " . TABLE_PENDING_USERS . " WHERE ID = " . $staff->ID;
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
  $course = $wpdb->get_row("SELECT * FROM " . TABLE_COURSES . " WHERE ID = $course_id", ARRAY_A);
  return $course;
}

/**
 * Enroll the user into the course base on email address and course name
 *
 * @param string $email - e-amil of the user
 * @param array $data - user data
 **/
function enrollUserInCourse($email = '', $data = array()) 
{
    extract($data);
    /*
    * Variables required in $data
    * org_id - the organization ID
    * course_id - the ID of the course to enroll the user into
    * subscription_id - the subscription id
    */
    if($email == "")
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: invalid user email address.");

    if($course_id == null)
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: no course id supplied.");

    if($org_id == null)
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: no organization id supplied.");

    if($subscription_id == null)
        return array('status' => 0, 'message' => "ERROR in enrollUserInCourse: no subscription id supplied.");

    $user = get_user_by('email', $email);
    global $wpdb;

    // Save enrollments to the database.
    $insert = $wpdb->insert(
      TABLE_ENROLLMENTS, 
      array( 
        'course_id' => $course_id, 
        'subscription_id' => $subscription_id,
        'email' => $email,
        'user_id' => $user->ID,
        'org_id' => $org_id,
        'status' => 'not_started'
      ), 
      array( 
        '%d', 
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
function getEotUsers($org_id = 0, $role = 'student')
{
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
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
        $user['first_name'] = get_user_meta ( $user_info->ID, "first_name", true);
        $user['last_name'] = get_user_meta ( $user_info->ID, "last_name", true);
        $user['email'] = $user_info->user_email;
        $user['ID'] = $user_info->ID;
        $user['user_type'] = 'learner';  // @TODO remove if not used(used in manage_staff_accounts line 90)
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
        $sql = "INSERT INTO ".TABLE_COURSE_MODULE_RESOURCES." (course_id, module_id, resource_id, type) SELECT ".$wpdb->insert_id.", m.module_id,m.resource_id,m.type FROM ".TABLE_COURSE_MODULE_RESOURCES." m WHERE m.course_id = $copy_course_id ";
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
      return array('status' => 0, 'message' => "createCourse error: Something went wrong!");
    }
}

/*
 * Get modules in course
 * @param int $course_id - The course ID
 */
function getModulesInCourse($course_id = 0){
    global $wpdb;
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT DISTINCT m.*, c.name AS category "
                . "FROM " . TABLE_MODULES . " AS m "
                . "LEFT JOIN " . TABLE_COURSE_MODULE_RESOURCES . " AS cmr ON cmr.module_id = m.id "
                . "LEFT JOIN " . TABLE_CATEGORIES . " AS c ON m.category_id = c.id "            
                . "WHERE cmr.course_id = $course_id";

    $course_modules = $wpdb->get_results($sql, ARRAY_A);
    //error_log(json_encode($course_modules));
    return $course_modules;
}

//Ajax get modules for a course. Called from part-manage_courses
add_action('wp_ajax_getModules', 'getModules_callback'); // Executes Courses_Modules functions actions only for logged in users
function getModules_callback() 
{
    if( isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['subscription_id'] ))
    {

        // Get the Post ID from the URL
        $course_id          = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id             = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $subscription_id    = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        $course_status      = ""; //  The course status

        $info_data = compact("org_id");

        // check if user has admin/manager permissions
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can ('is_sales_manager') )
        {
            $result['data'] = 'failed';
            $result['message'] = 'Error: Sorry, you do not have permisison to view this page. ';
        }
        else
        {
            // Build the response if successful
            // get modules of the course
            $modules = getModulesInCourse($course_id);
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
                    $module_description_text = $module['description_text']; 
                    $module_title = $module['title'];
                    //$html.= ' <div class = "staff_and_assignment_list_row" onmouseover="Tip(\''.str_replace('"','&quot;',addslashes($module_description_text)).'\', WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\',TITLE,\'Description\')" onmouseout="UnTip()">';
                    $html.= ' <div class = "staff_and_assignment_list_row">';
                    $html.= '  <span class="staff_name" >'.$module_title.'</span>';
                    $html.= ' </div>';
                    $num_modules_type_page++;
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
    $courses = $wpdb->get_results("SELECT * FROM " . TABLE_COURSES . " WHERE ID = $course_id", OBJECT_K); 
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
 * Get the modules in a library order them alphabetically
 * @global type $wpdb
 * @param type $library_id
 * @return type
 */
function getModulesByLibrary($library_id = 0)
{
  global $wpdb;
  $library_id = filter_var($library_id, FILTER_SANITIZE_NUMBER_INT);
  $modules = $wpdb->get_results("SELECT m.*, c.name AS category "
          . "FROM " . TABLE_MODULES . " m "
          . "LEFT JOIN " . TABLE_CATEGORIES . " c "
          . "ON m.category_id = c.id "
          . "JOIN ". TABLE_LIBRARY_MODULES. " lm "
          . "ON lm.library_id = $library_id AND lm.module_id = m.id "
          //. "WHERE m.library_id = $library_id "
          . "ORDER BY m.title" , ARRAY_A);
  return $modules;  
}

/**
 * Get the categories in a library
 * @global type $wpdb
 * @param type $library_id - the library id
 * @return type Array Categories
 */
function getCategoriesByLibrary($library_id = 0){
    global $wpdb;
    $library_id = filter_var($library_id, FILTER_SANITIZE_NUMBER_INT);
    $categories = $wpdb->get_results("SELECT * FROM " . TABLE_CATEGORIES . " c WHERE c.library_id = $library_id ORDER BY c.order ASC");
    return $categories;
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

/*
 * Get Courses by org_id
 * @param $org_id - the org id
 */
function getCoursesByOrgId($org_id = 0){
    global $wpdb;
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    $courses = $wpdb->get_results("SELECT * FROM " . TABLE_COURSES . " WHERE org_id = $org_id", ARRAY_A);
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
 *  Get all the enrollments status by course ID / user ID / subscription ID
 *  @param int $course_ID - the course ID
 *  @param int $user_id - the user ID
 *  @param int $subscription_id - the subscription ID
 *  @param boolean $completed - to return completed courses or not.
 *
 *  @return json encoded list of enrollments
 */
/* NOT USED SINCE I MADE A BETTER FUNCTION BELOW
function getEnrollments($course_id = 0, $user_id = 0, $subscription_id = 0, $completed = true) 
{
  global $wpdb;
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
  $subscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);
  if($course_id == 0 && $user_id == 0 && $subscription_id == 0)
  {
    echo "Invalid library ID";
    return;
  }
  if($course_id > 0)
  {
    $sql = "SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE course_id = $course_id";
  }
  else if($user_id > 0)
  {
      $sql = "SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE (user_id = $user_id)"; // All the completed or passed enrollments of the user.
  }
  else if($subscription_id > 0)
  {
      $sql = "SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE (subscription_id = $subscription_id)"; // All the completed or passed enrollments of the user.
  }
  if($completed)
  {
    $sql .= " and (status = 'completed ' or status = 'passed')";
  }
  $enrollments = $wpdb->get_results($sql, ARRAY_A);
  return $enrollments;
}
*/

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

/**
 * Get the video resources by module ids
 *  @param string module_ids - the module ids separated by comma.
 *
 *  @return array() - an array of VideoResources data
 */
function getVideoResourcesInModules($module_ids = '')
{
    if (empty($module_ids))
      return NULL;

    global $wpdb;
    $module_ids = filter_var($module_ids, FILTER_SANITIZE_STRING);
    $resources = $wpdb->get_results("SELECT DISTINCT mr.module_id, v.* FROM ". TABLE_MODULE_RESOURCES ." mr LEFT JOIN " . TABLE_VIDEOS . " v "
            . "ON mr.resource_id = v.ID "
            . "WHERE mr.module_id IN (" . $module_ids . ") AND mr.type = 'video' ORDER BY mr.order ASC", ARRAY_A);

/*
    $resources = $wpdb->get_results(
      "SELECT DISTINCT mr.module_id, v.*, t.result, t.video_time, t.ID as track_id, r.url 
       FROM " . TABLE_MODULE_RESOURCES . " mr LEFT JOIN " . TABLE_VIDEOS . " v " . "ON mr.resource_id = v.ID 
       LEFT JOIN " . TABLE_RESOURCES . " r ON r.ID = mr.resource_id 
       LEFT JOIN " . TABLE_TRACK. " t ON t.user_id = $user_id AND t.video_id = v.ID" . " 
       WHERE mr.module_id IN (" . $module_ids . ") AND mr.type = 'video' 
       ORDER BY mr.order ASC", OBJECT_K);
*/       
    return $resources;
}

/**
 * Get the quiz resources by module ids
 *  @param string module_ids - the module ids separated by comma.
 *
 *  @return array() - an array of QuizResources data
 */
function getQuizResourcesInModules($module_ids = '')
{
    if (empty($module_ids))
      return NULL;

    global $wpdb;
    $module_ids = filter_var($module_ids, FILTER_SANITIZE_STRING);
    $resources = $wpdb->get_results("SELECT DISTINCT mr.module_id, q.* FROM " . TABLE_MODULE_RESOURCES . " mr LEFT JOIN " . TABLE_QUIZ . " q "
            . "ON mr.resource_id = q.ID "
            . "WHERE mr.module_id IN (" . $module_ids . ") AND mr.type = 'exam' ORDER BY mr.order ASC", ARRAY_A);
    return $resources;
}

/**
 * Get all the course handouts by modules ids
 *  @param string module_ids - the module ids separated by comma.
 *
 *  @return array() - an array of quiz handouts data
 */
function getHandouts($module_ids = '')
{
    if (empty($module_ids))
      return NULL;

    global $wpdb;
    $module_ids = filter_var($module_ids, FILTER_SANITIZE_STRING);
    $handouts = $wpdb->get_results( "SELECT * FROM ". TABLE_RESOURCES ." WHERE module_id IN ($module_ids)", ARRAY_A );
    return $handouts;
}

/**
 * get the quizzes in a course
 * @param $course_id - the course ID
 * @return array of quizzes in course
 */
function getQuizzesInCourse($course_id = 0)
{
    global $wpdb;
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT q.* "
                . "FROM " . TABLE_QUIZ . " AS q "
                . "LEFT JOIN " . TABLE_COURSE_MODULE_RESOURCES . " AS cq ON cq.resource_id = q.ID "
                . "WHERE cq.course_id = $course_id AND cq.type = 'exam'";
    $course_quizzes = $wpdb->get_results($sql, ARRAY_A);
    return $course_quizzes;
}

/**
 * get the resources in a course
 * @param $course_id - the course ID
 * @param $type - the type of resource
 * @return array of resources in course
 */
function getResourcesInCourse($course_id = 0, $type = '')
{
    global $wpdb;
    // make sure there is a type or else return empty array
    if ($type == '' )
    {
      return array();
    }
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    switch($type)
    {
        case 'exam':
            $table = TABLE_QUIZ;
            break;
        case 'video':
            $table = TABLE_VIDEOS;
            break;
        case 'doc':
            $table = TABLE_RESOURCES;
            break;
        case 'link':
            $table = TABLE_RESOURCES;
            break;
        case 'custom_video':
            $table = TABLE_RESOURCES;
            break;
        default:
            $table = TABLE_RESOURCES;
    }
    $sql = "SELECT r.*, cmr.module_id as mid "
                . "FROM " . $table . " AS r "
                . "LEFT JOIN " . TABLE_COURSE_MODULE_RESOURCES . " AS cmr ON cmr.resource_id = r.ID "
                . "WHERE cmr.course_id = $course_id AND cmr.type = '$type'";
    $course_resources = $wpdb->get_results($sql, ARRAY_A);
    return $course_resources;
}

/**
 * Get Modules in Org
 * @global type $wpdb
 * @param type $org_id - the org ID
 * @return array of modules in org
 * 
 */
function getModules($org_id = 0) 
{
    global $wpdb;
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    $modules=$wpdb->get_results("SELECT * FROM " . TABLE_MODULES. " WHERE org_id = $org_id" , ARRAY_A);
    return $modules;
}

/**
 * Get the category by ID, or by library id
 * @param $id - Category ID
 * @param $library_id - The library ID
 * @return array objects - an object arrays of category data
 */
function getLibraryCategory($id = 0, $library_id = 0)
{
  // check we got the right parameters
  if (!$id && !$library_id)
    return NULL;

  // sanitize input
  $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
  $library_id = filter_var($library_id, FILTER_SANITIZE_NUMBER_INT);

  global $wpdb;
  $sql = "SELECT ID, name FROM " . TABLE_CATEGORIES;

  if($id > 0)
  {
    $sql .= " WHERE ID = $id";
  }
  else if ($library_id > 0)
  {
    $sql .= " WHERE library_id = $library_id ORDER BY `order`";
  }

  $categories = $wpdb->get_results( $sql, OBJECT_K ); // returns an array of objects with the key as the ID
  return $categories;
}

/**
 * Get the help videos for the specified view and user role.
 * @param string $part_name - The view name.
 * @param string $role - The role name.
 * @return arrays of views data
 */
function getHelpForView($part_name = "", $role = "")
{
  // check we got the right parameters
  if ( empty($part_name) || empty($role) )
    return null;

  global $wpdb;
  $sql = "SELECT * FROM " . TABLE_HELP_TOPICS . " ht LEFT JOIN " . TABLE_HELP_TOPICS_FOR_VIEW . " htv ON ht.ID = htv.topic_id WHERE htv.part_name = '$part_name' AND htv.role='$role' ORDER BY htv.order";
  
  $views = $wpdb->get_results( $sql, ARRAY_A ); // returns an array of view information.
  return $views;
}

/**
 * Get the help video data by ID
 * @param int $id - The help topic ID.
 * @return object of video data
 */
function getHelpVideoById($id = 0)
{
  // check we got the right parameters
  if ( !$id )
    return null;

  global $wpdb;
  $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
  $sql = "SELECT * FROM " . TABLE_HELP_TOPICS . " WHERE ID = $id";
  
  $video = $wpdb->get_row( $sql, OBJECT ); // returns an object with the video data.
  return $video;
}

/**
 * Get Handout Resources in a course's modules
 * @global type $wpdb
 * @param type $module_ids - string of comma seperated module ids
 * @return array handouts
 * 
 */
function getHandoutResourcesInModules($module_ids = '')
{
    if (empty($module_ids))
      return NULL;

    global $wpdb;
    $module_ids = filter_var($module_ids, FILTER_SANITIZE_STRING);
    //$handouts = $wpdb->get_results("SELECT r.* FROM " . TABLE_RESOURCES . " r WHERE r.module_id IN (" . $module_ids . ")" , ARRAY_A);
    $handouts = $wpdb->get_results("SELECT DISTINCT mr.module_id as mod_id, r.* FROM " . TABLE_MODULE_RESOURCES . " mr LEFT JOIN " .TABLE_RESOURCES . " r on mr.resource_id = r.ID WHERE mr.module_id IN (" . $module_ids . ") AND mr.type NOT IN ('video','exam')",ARRAY_A);
    return $handouts;
}

/**
 * Get Quiz Resources in a single module
 * @global type $wpdb
 * @param type $module_id
 * @return array quizzes
 * 
 */
function getQuizResourcesInModule($module_id = 0)
{

    if (!$module_id)
      return NULL;

    global $wpdb;
    $module_id = filter_var($module_id, FILTER_SANITIZE_NUMBER_INT);
    $resources=$wpdb->get_results("SELECT DISTINCT mr.module_id, q.* FROM ". TABLE_MODULE_RESOURCES ." mr LEFT JOIN " . TABLE_QUIZ . " q "
            . "ON mr.resource_id = q.ID "
            . "WHERE mr.module_id = $module_id AND mr.type = 'exam' ORDER BY mr.order ASC", ARRAY_A);
    return $resources;
}

/**
 *  Handles the add and remove of the module in a course
 *  @param int $course_ID - the course ID
 *  @param array $data - user/camp data 
 *
 *  @return json encoded list of modules
 */
function toggleVideoInAssignment($course_id = 0, $data = array()) 
{
  extract($data);
  /*
   * Variables required in $data
   * org_id - the organization ID
   * module_id - the module ID
   * video_id - the video ID in videos table
   */
  global $wpdb;
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
  $modules = $wpdb->get_results("SELECT m.* "
                . "FROM " . TABLE_MODULES . " AS m "
                . "LEFT JOIN " . TABLE_COURSE_MODULE_RESOURCES . " AS cmr ON cmr.module_id = m.ID "
                . "WHERE cmr.course_id = $course_id AND cmr.type = 'video'", ARRAY_A); // All the modules registered in the course.
  $course_module_ids = array_column($modules, 'ID'); // Modules IDS

  // Check if the module id is in the course
  if(in_array($module_id, $course_module_ids))
  {
      $result = $wpdb->delete(TABLE_COURSE_MODULE_RESOURCES, array('course_id' => $course_id, 'module_id' => $module_id, 'resource_id' => $video_id, 'type' => 'video'));
            if ($result === false) 
            {
                return false;
            } 
            else 
            {
                return true;
            }
  }
  else
  {
    $result = $wpdb->insert(TABLE_COURSE_MODULE_RESOURCES, array('course_id' => $course_id, 'module_id' => $module_id, 'resource_id' => $video_id, 'type' => 'video'));
            if ($result === false) 
            {
                return false;
            } 
            else 
            {
                return true;
            }
  }
}

/**
 *  Handles the add and remove of a quiz in a course
 *  @param int $course_ID - the course ID
 *  @param array $data - user/camp data 
 *
 *  @return json encoded list of modules
 */
function toggleQuizInAssignment($course_id = 0, $data = array()) 
{
  extract($data);
  /*
   * Variables required in $data
   * org_id - the organization ID
   * module_id - the module ID
   * quiz_id - the quiz id
   */
  global $wpdb;
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
  $quizzes=$wpdb->get_results("SELECT q.* "
                . "FROM " . TABLE_QUIZ . " AS q "
                . "LEFT JOIN " . TABLE_COURSE_MODULE_RESOURCES . " AS cmr ON cmr.resource_id = q.ID "
                . "WHERE cmr.course_id = $course_id AND cmr.type = 'exam'", ARRAY_A); // All the modules registered in the course.
  $course_quizzes_ids = array_column($quizzes, 'ID'); // Modules IDS

  // Check if the module id is in the course
  if(in_array($quiz_id, $course_quizzes_ids))
  {
      $result = $wpdb->delete(TABLE_COURSE_MODULE_RESOURCES, array('course_id' => $course_id, 'resource_id' => $quiz_id,'module_id' => $module_id,'type' => 'exam'));
            if ($result === false) 
            {
                return false;
            } 
            else 
            {
                return true;
            }
  }
  else
  {
    $result = $wpdb->insert(TABLE_COURSE_MODULE_RESOURCES, array('course_id' => $course_id, 'resource_id' => $quiz_id,'module_id' => $module_id,'type' => 'exam'));
            if ($result === false) 
            {
                return false;
            } 
            else 
            {
                return true;
            }
  }
}
/**
 * Handles the add and remove of custom modules in a course
 * @param $course_id - the course ID
 * returns true or false
 */
function toggleModuleInAssignment($course_id = 0, $data = array())
{
  extract($data);
  /*
   * Variables required in $data
   * module_id - the module ID
   */
  global $wpdb;

  // make sure we have a valid course
  if (!$course_id || !$module_id)
    return false;

  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
  $module_id = filter_var($module_id, FILTER_SANITIZE_NUMBER_INT);


  $module_resources = $wpdb->get_results("SELECT * FROM " . TABLE_COURSE_MODULE_RESOURCES . " WHERE module_id = $module_id AND course_id = $course_id", ARRAY_A);
  if(count($module_resources) > 0)
  {
      $resources = $wpdb->get_results("SELECT * FROM " . TABLE_MODULE_RESOURCES . " WHERE module_id = $module_id", ARRAY_A);
      $result = true;
      foreach($resources as $resource)
      {    
          $delete= $wpdb->delete(TABLE_COURSE_MODULE_RESOURCES, array(
              'resource_id'=>$resource['resource_id'],
              'module_id'=>$resource['module_id'],
              'type'=>$resource['type']
          ));
          if(!$delete)
          {
              $result = false;
          }
      }
      
  }
  else
  {
      $sql = "INSERT INTO " . TABLE_COURSE_MODULE_RESOURCES . " (course_id, module_id, resource_id, type) SELECT $course_id, module_id, resource_id, type FROM " . TABLE_MODULE_RESOURCES . " WHERE module_id = $module_id";
      $result = $wpdb->query($sql);
  }

  // check if we successfully added/deleted module and return appropraite result.
  if ($result === false) 
  {
      return false;
  } 
  else 
  {
      return true;
  }
}

/**
 *  Handles the add and remove of a resource in a course
 *  @param int $course_ID - the course ID
 *  @param array $data - user/camp data to
 *
 *  @return json encoded list of modules
 */
function toggleResourceInAssignment($course_id = 0, $data = array()) 
{
  extract($data);
  /*
   * Variables required in $data
   * org_id - the organization ID
   * module_id - the module ID
   * resource_id - the resource ID 
   */
  global $wpdb;
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
  $resources=$wpdb->get_results("SELECT r.* "
                . "FROM " . TABLE_RESOURCES . " AS r "
                . "LEFT JOIN " . TABLE_COURSE_MODULE_RESOURCES . " AS cmr ON cmr.resource_id = r.ID "
                . "WHERE cmr.course_id = $course_id AND cmr.type = 'doc'", ARRAY_A); // All the modules registered in the course.
  $course_resource_ids = array_column($resources, 'ID'); // Modules IDS

  // Check if the module id is in the course
  if(in_array($resource_id, $course_resource_ids))
  {
      $result = $wpdb->delete(TABLE_COURSE_MODULE_RESOURCES, array('course_id' => $course_id, 'resource_id' => $resource_id,'module_id' => $module_id,'type' => 'doc'));
            if ($result === false) 
            {
                return false;
            } 
            else 
            {
                return true;
            }
  }
  else
  {
    $result = $wpdb->insert(TABLE_COURSE_MODULE_RESOURCES, array('course_id' => $course_id, 'resource_id' => $resource_id,'module_id' => $module_id,'type' => 'doc'));
            if ($result === false) 
            {
                return false;
            } 
            else 
            {
                return true;
            }
  }
}

/********************************************************************************************************
 * Toogle on and off module in a course
 *******************************************************************************************************/
add_action('wp_ajax_toggleItemInAssignment', 'toggleItemInAssignment_callback'); 
function toggleItemInAssignment_callback() 
{

    if( isset ( $_REQUEST['group_id'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['item'] ) && isset ( $_REQUEST['item_id'] ) )
    {
        $course_id          = filter_var($_REQUEST['group_id'], FILTER_SANITIZE_NUMBER_INT);
        $item               = filter_var($_REQUEST['item'], FILTER_SANITIZE_STRING);
        $item_id            = filter_var($_REQUEST['item_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id             = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $module_id          = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
        
        switch($item){
            case "video":
                $info_data          = array("org_id" => $org_id, "video_id" => $item_id, 'module_id' => $module_id);
                $response = toggleVideoInAssignment($course_id, $info_data);
                break;
            case "quiz":
                $info_data          = array("org_id" => $org_id, "quiz_id" => $item_id, 'module_id' => $module_id);
                $response =  toggleQuizInAssignment($course_id, $info_data);
                break;
            case "resource":
                $info_data          = array("org_id" => $org_id, "resource_id" => $item_id, 'module_id' => $module_id);
                $response = toggleResourceInAssignment($course_id, $info_data);
                break;
            case "module":
                $info_data          = array("org_id" => $org_id, "resource_id" => $item_id, 'module_id' => $module_id);
                $response = toggleModuleInAssignment($course_id, $info_data);
                break;
            
        }
        echo json_encode($response);
    }
    wp_die();
}

/********************************************************************************************************
 * This processed the creation of a course.
 * @param int $org_id - Organization ID
 * @param int $user_id - User ID from wordpress
 * @param string $course_name - Name of the course
 * @param string $course_description - description of the course
 *******************************************************************************************************/
add_action('wp_ajax_createCourse', 'createCourse_callback'); 
function createCourse_callback ( ) 
{
    if( isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['name'] ) && isset ( $_REQUEST['user_id'] ) && isset ( $_REQUEST['subscription_id'] ) )
    {
        // This form is generated in getCourseForm function from this file.
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID

        $chars = array("'",'"',"?","’","”","&quot;",'\"',"\'",'\\');
        $course_name = str_replace($chars, "", trim($_REQUEST['name']));
        $course_name = filter_var($course_name, FILTER_SANITIZE_STRING);
        $course_description = (isset($_REQUEST['desc'])) ? filter_var($_REQUEST['desc'], FILTER_SANITIZE_STRING) : "";

        // Check permissions
        if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'create-course_' . $org_id ) ) 
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'createCourse_callback error: Sorry, your nonce did not verify.';
            echo json_encode($result);
            wp_die();
        }
        else if($course_name == "")
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'createCourse_callback error: Please Enter the <b>Name</b> of the course.';
            echo json_encode($result);
            wp_die();
        }

        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
        {
            $result['display_errors'] = 'success';
            $result['errors'] = 'createCourse_callback error: Sorry, you do not have permisison to view this page. ';
        }
        else 
        {
            $course_due_date = ""; //for future use
            $data = compact( "org_id", "user_id", "course_due_date", "course_description", "subscription_id");
            // Add the course
            $response = createCourse($course_name, $org_id, $data);
            if($response['status'] == 0)
            {
                // Build the response if create course failed to execute.
                $result['display_errors'] = true;
                $result['data'] = 'failed';
                $result['errors'] = "Response Message: " . $response['message'];
            }
            elseif ($response['status'] == 1) 
            {
                // Build the response if successful
                $result['data'] = 'success';
                $result['org_id'] = $org_id;
                $result['message'] = 'Course has been created';
                $result['success'] = true;
                $result['group_name'] = $course_name;
                $result['group_id'] = $response['id'];
                $result['group_desc'] = $course_description;
                $result['subscription_id'] = $subscription_id;
            }
            else
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "ERROR: Could not create the course name.";
            }
        }
    }
    else
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = 'createCourse_callback Error: Missing some parameters.';
    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * Updating a course name/description
 *******************************************************************************************************/
add_action('wp_ajax_updateCourse', 'updateCourse_callback'); 
function updateCourse_callback ( ) 
{
    if( isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['name'] ) )
    {
        // This form is generated in getCourseForm function with $form_name = edit_course_group from this file.
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $course_id = filter_var($_REQUEST['group_id'], FILTER_SANITIZE_NUMBER_INT);
        $chars = array("'",'"',"?","’","”","&quot;",'\"',"\'",'\\');
        $course_name = str_replace($chars, "", trim($_REQUEST['name']));
        $course_name = filter_var($course_name, FILTER_SANITIZE_STRING);
        $course_description = (isset($_REQUEST['desc'])) ? filter_var($_REQUEST['desc'], FILTER_SANITIZE_STRING) : "";

        if($course_name == ""){
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "Course name cannot be blank";
        }
        // Check permissions
        else if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'edit-course_' . $org_id ) ) 
        {
            $result['display_errors'] = 'Failed';
            $result['success'] = false;
            $result['errors'] = 'edit course error: Sorry, your nonce did not verify.';
        }
        else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
        {
            $result['display_errors'] = 'Failed';
            $result['success'] = false;
            $result['errors'] = 'edit course error: Sorry, you do not have permisison to view this page. ';
        }
        else 
        {
            $data = compact( "org_id", "course_name", "course_description");
            // Edit the course
            global $wpdb;
            $response = $wpdb->update(TABLE_COURSES, $data, array('ID' => $course_id));
            if ($response === FALSE)
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "Response Message: " . $wpdb->last_error;
            }
            else 
            {
                // Build the response if successful
                $result['data'] = 'success';
                $result['org_id'] = $org_id;
                $result['message'] = 'Course has been updated';
                $result['success'] = true;
                $result['group_name'] = $course_name;
                $result['group_id'] = $course_id;
                $result['group_desc'] = $course_description;
            }
        }
    }
    else
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = 'updateCourse_callback Error: Missing some parameters.';
    }
    echo json_encode($result);
    wp_die();
}

/**
 *   Ajax call for a deleting a course.
 */  
add_action('wp_ajax_deleteCourse', 'deleteCourse_callback'); //handles actions and triggered when the user is logged in

function deleteCourse_callback() 
{
    if( isset ( $_REQUEST['group_id'] ) && isset ( $_REQUEST['org_id'] ) )
    {

      // Get the Post ID from the URL
      $course_id          = filter_var($_REQUEST['group_id'], FILTER_SANITIZE_NUMBER_INT);
      $org_id             = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);

      $data = compact("org_id");

      // Check permissions
      if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'delete-course_' . $course_id ) ) 
      {
          $result['display_errors'] = 'failed';
          $result['success'] = false;
          $result['errors'] = 'deleteCourse_callback error: Sorry, your nonce did not verify.';
      }
      else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
      {
          $result['display_errors'] = 'failed';
          $result['success'] = false;
          $result['errors'] = 'deleteCourse_callback Error: Sorry, you do not have permisison to view this page.';
      }
      else 
      {
        global $wpdb;
        $response = $wpdb->delete(TABLE_COURSES, array('ID' => $course_id));
        if ($response === FALSE)
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Response Message: " . $wpdb->last_error;
        }
        else 
        {
            // Build the response if successful
            $result['data'] = 'success';
            $result['message'] = 'Course has been deleted';
            $result['group_id'] = $course_id;
            $result['success'] = true;
        }
      }
      echo json_encode( $result );
    }
    wp_die();
}

/********************************************************************************************************
 * get a list of users enrolled in a course
 *******************************************************************************************************/
add_action('wp_ajax_getUsersInCourse', 'getUsersInCourse_callback'); 
function getUsersInCourse_callback() 
{
    if( isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['org_id'] ) )
    {

        // Get the Post ID from the URL
        $course_id          = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id             = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);

        // check if user has admin/manager permissions
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['data'] = 'failed';
            $result['message'] = 'Error: Sorry, you do not have permisison to view this page. ';
        }
        else
        {
            global $wpdb;
            // Get the enrollments who are enrolled in the course.
//            $enrollments = $wpdb->get_results("SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE course_id = $course_id", ARRAY_A);
            $enrollments = getEnrollments($course_id);

            $users = array(); // Lists of users who are enrolled in the course.

            $user_data = getEotUsers($org_id); // get the students in this org
            $user_ids_in_camp = array(); // Lists of all user ids in the camp.
            if( isset($user_data['users']) )
            {
              $user_ids_in_camp = array_column($user_data['users'], 'ID');
            }
            if($enrollments && count($enrollments) > 0)
            {
              foreach ($enrollments as $enrollment) 
              {
                if(in_array($enrollment['user_id'], $user_ids_in_camp)) // check that only students are being displayed. @todo make this more efficient in the future. Use SQL instead of in_array.
                {
                  $user['first_name'] = get_user_meta ( $enrollment['user_id'], "first_name", true);
                  $user['last_name'] = get_user_meta ( $enrollment['user_id'], "last_name", true);
                  array_push($users, $user);
                }
              }
            }

            /*********************************************************************************************
            * Create HTML template and return it back as message. this will return an HTML div set to the 
            * javascript and the javascript will inject it into the HTML page.
            **********************************************************************************************/
            $html = '<div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "width: 350px">';
            $html .= '  <div style = "width:100%;">';
            if( $users && count($users) > 0 ) 
            {
                usort($users, "sort_first_name"); // sort the users by first name
                foreach( $users as $user )
                {
                    $html .= '<div class ="staff_and_assignment_list_row">';
                    $html .= '<span class="staff_name">' . $user['first_name'] . ' ' . $user['last_name']  . '</span>';
                    $html .= '</div>';
                }
                $html .= '   </div>'; 
                $html .= '</div>';  

                $result['staff_count'] = count($users);
                $result['data'] = 'success';
                $result['message'] = $html;
                $result['group_id'] = $course_id; // if not included, when clicking on manage assignment/course, it will not open the dialog box.
            }
            else if( count($users) == 0 )
            {
                $result['staff_count'] = 0;
                $result['data'] = 'failed';
                $result['message'] = '<p>There are no users enrolled in this course.</p>';
            }
            else 
            {
                $result['data'] = 'failed';
                $result['message'] = 'Error in getting enrolled users for course id: ('. $course_id .')';
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
 *   Manage the creation of a staff account 
 */  
add_action('wp_ajax_createUser', 'createUser_callback'); //handles actions and triggered when the user is logged in

function createUser_callback() 
{
    if( isset ( $_REQUEST['name'] ) && isset ( $_REQUEST['lastname'] ) && isset ( $_REQUEST['email'] ) && isset ( $_REQUEST['pw'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['subscription_id'] ))
    {
        //$first_name = filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING); // User's first name
        //$last_name = filter_var($_REQUEST['lastname'], FILTER_SANITIZE_STRING); // User's last name
        
        $chars=array("'",'"',"?","’","”","&quot;",'\"',"\'",'\\');
        $first_name = str_replace($chars, "", trim($_REQUEST['name']));
        $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
        
        $last_name = str_replace($chars, "",trim($_REQUEST['lastname']));
        $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);
        
        $email = sanitize_email( $_REQUEST['email']); // User's e-mail address
        $password = $_REQUEST['pw']; // User's password
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT); // The course ID
        $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        $data = compact("org_id", "first_name", "last_name", "email", "password", "course_id", "subscription_id");
        $send_mail = ( isset($_REQUEST['send_mail']) && filter_var($_REQUEST['send_mail'], FILTER_SANITIZE_NUMBER_INT) == 1 ) ? TRUE : FALSE;

        // Check permissions
        if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'create-staff_' . $org_id ) ) 
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'create staff account error: Sorry, your nonce did not verify.';
            echo json_encode( $result );
            wp_die();
        }
        if($first_name == ""){
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'create staff account error: Please Enter a first name';
            echo json_encode( $result );
            wp_die();           
        }
        if($last_name == ""){
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'create staff account error: Please Enter a last name';
            echo json_encode( $result );
            wp_die();           
        }        

        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'Failed';
            $result['success'] = false;
            $result['errors'] = 'create staff account error: Sorry, you do not have permisison to view this page. ';
        }
        else 
        {
            // check that the user doesnt exist in WP
            if ( email_exists($email) == false )
            {
                    $result['success'] = true;
                    $result['msg_sent'] = $send_mail;
                    $result['name'] = $first_name;
                    $result['lastname'] = $last_name;
                    $result['org_id'] = $org_id;
                    $result['email'] = $email;
                    $result['password'] = $password;

                    // create the user in WP (student)
                    $userdata = array (
                        'user_login' => $email,
                        'user_pass' => $password,
                        'role' => 'student',
                        'user_email' => $email,
                        'first_name' => $first_name,
                        'last_name' => $last_name
                    );
                    $WP_user_id = wp_insert_user ($userdata);

                    // check if we successfully inserted the user
                    if ( ! is_wp_error( $WP_user_id ) ) 
                    {
                      $result['user_id'] = $WP_user_id;
                      // Newly created WP user needs some meta data values added
                      update_user_meta ( $WP_user_id, 'org_id', $org_id );
                      update_user_meta ( $WP_user_id, 'accepted_terms', '0');
 
                      // Create enrollment
                      if($course_id)
                      {
                          // Adding the course name in the $data
                          $response = enrollUserInCourse($email, $data);    
                          if($response['status'] == 1)
                          {
                              // success message is set above.
                          }
                          else
                          {
                              $result['success'] = false;
                              $result['display_errors'] = true;
                              $result['errors'] = "CreateUser_callback Error: " . $response['message'];
                          }
                      }
                      else // user created successfully, but no course ID so can't enroll
                      {
                          $result['success'] = false;
                          $result['display_errors'] = true;
                          $result['errors'] = "createUser_callback Error: Created user but could not enroll in course because couldn't find the course name.";
                      }   
                    }
                    else
                    {
                      // error, couldnt insert the user
                      $result['display_errors'] = 'Failed';
                      $result['success'] = false;
                      $result['errors'] = 'create staff account error: Sorry, we couldnt create the user. ';
                    }
            }
            else 
            {
                $result['success'] = false;
                $result['display_errors'] = true;
                $result['errors'] = 'Wordpress error: User already exsists.';
            } 
        }
        // This variable will return to part-manage_staff_accounts.php $(document).bind('success.create_staff_account). Line 865
        echo json_encode( $result );
    }
    wp_die();
}

/**
 * Create Wordpress User for upload spreadsheet page
 * @param array $data - user data
 * @param string $role - the user role
 */

function createWpUser($data = array(), $role = 'student')
{
   /*******************************************************
   * org_id - The organization ID
   * first_name - User First Name
   * last_name - User Last Name
   * email - User Email
   * password - User Password
   * @return json encoded list of completion or errors
   ********************************************************/    
  extract($data);

  // Check if all parameters are available.
  if( $org_id != "" && $first_name != "" &&  $last_name != "" &&  $email != "" && $password != "" )
  {
    $chars=array("'",'"',"?","’","”","&quot;",'\"',"\'",'\\');
    $first_name = str_replace($chars, "", trim($first_name));
    $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
    
    $last_name = str_replace($chars, "",trim($last_name));
    $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);
    
    $email = sanitize_email($email); // User's e-mail address
    $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);

    // check that the user doesnt exist in WP
    if ( email_exists($email) == false )
    {  
      // create the user in WP 
      $userdata = array (
          'user_login' => $email,
          'user_pass' => $password,
          'role' => $role,
          'user_email' => $email,
          'first_name' => $first_name,
          'last_name' => $last_name
      );
      
      $WP_user_id = wp_insert_user ($userdata);
        
      // check if we successfully inserted the user
      if ( ! is_wp_error( $WP_user_id ) )
      {
        // Newly created WP user needs some meta data values added
        update_user_meta ( $WP_user_id, 'org_id', $org_id );
        update_user_meta ( $WP_user_id, 'accepted_terms', '0');
        $result['success'] = true;
        $result['name'] = $first_name;
        $result['lastname'] = $last_name;
        $result['org_id'] = $org_id;
        $result['email'] = $email;
        $result['password'] = $password;
        $result['user_id'] = $WP_user_id; // LU User ID
      }
      else
      {
        // error, couldnt insert the user
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'createWpUser error: Sorry, we couldnt create the user. ';
        $result['message'] = 'createWpUser error: Sorry, we couldnt create the user. ';
      }
    }
    else
    {
      // email already exists  
      $result['success'] = false;
      $result['message']='User already exists';
    }

  }
  return $result;
}


/**
 *  enroll a user in courses
 *  @param array $courses - an array of course names to enroll the user in
 *  @param int $org_id - the org id
 *  @param string $email - the user's email
 *  @param int $subscription_id - the subscription id
 *  @return result array with succes/failiure
 */  
function enrollUserInCourses($courses = array(), $org_id = 0, $email = '', $subscription_id = 0)
{

  // make sure we have the data we need.
  if ($email == '' || !$org_id)
  {
    return array('status' => 0, 'message' => 'Error: Invalid arguments supplied to enrollUserInCourses');
  }
  $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
  $subscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);
  $allcourses =  getCoursesById($org_id, $subscription_id);
  $allcourses_names = array_column($allcourses, 'course_name');
  $result['message'] = ''; // will contain the success or failure messages and email statuses.
  $result['status'] = 1; // assume success unless we fail below
  // go through each course and enroll the user if course exists in all courses
  
  foreach ($courses as $course_name)
  {
    if(in_array($course_name, $allcourses_names))// see if course exists in courses for this org
    {
      foreach($allcourses as $course)
      {
        if($course['course_name'] == $course_name)// get the matching course_id
        {
          $course_id = $course['ID'];  
        }
      }
      
      $data = compact('org_id', 'course_name', 'course_id', 'subscription_id');

      if(user_is_enrolled($email, $course_id))
      {
        $result['status'] = 0;
        $result['message'] .= "enrollUserInCourses Error: $email is already enrolled in " . $course_name . "<br>\n";
        return $result;
      }
      else
      {
        $response = enrollUserInCourse($email, $data);
        if(isset($response['status']) && !$response['status']) // failed to enroll staff in course
        {
          $result['status'] = 0;
          $result['message'] .= "enrollUserInCourses Error: Couldn't enroll $email in $course_name : " . $response['message'] . "<br>\n";
        }
      }
    }
    else
    {
      $result['status'] = 0;
      $result['message'] .= "enrollUserInCourses ERROR: couldn't find the course: $course_name <br>\n";
    }
  }
  return $result;
}

/********************************************************************************************************
 * send e-mail base on type
 *******************************************************************************************************/
add_action('wp_ajax_sendMail', 'sendMail_callback'); 
function sendMail_callback() 
{
    $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
    $target = filter_var($_REQUEST['target'], FILTER_SANITIZE_STRING);

    // Check permissions
    if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'wp_ajax_sendMail error: Sorry, you do not have permisison to view this page. ';
    }
    else if( $target == "create_account" )
    {
        $message = stripslashes($_REQUEST['composed_message']); // Remove backward slash from GET. This fixed the problem for sending message with colored fonts.
        $subject = filter_var($_REQUEST['subject'], FILTER_SANITIZE_STRING);
        $name = filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING);
        $email = sanitize_email($_REQUEST['email']);

        $recepients = array(); // List of recepients

        $recepient = array (
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'subject' => $subject
        );

        array_push($recepients, $recepient);
        $data = compact( "org_id" );

        $response = sendMail( $target, $recepients, $data );

        if($response['status'] == "0")
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Error in sendMail_callback: " . $response['message'];
        }
        else if($response['status'] == "1")
        {
            $result['display_errors'] = false;
            $result['success'] = true;
        }
        else
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "ERROR in sendMail_callback: Could not email the user";
        }
    }

    echo json_encode($result);
    wp_die();
}

/**
 * Processed the sending of the message based on target
 *
 * @param string $target - The target action to be taken, eg. create_user, mass mail, etc..
 * @param array $receipients - an array of associative arrays which contain the receiptients information (name/email/message/subject)
 * @param array $data - any additional info we need such as org_id
 */
function sendMail ( $target = '', $recipients = '', $data = array()) 
{
    extract($data);
    /*
     * Variables required in $data
     * org_id - The organization ID
     */
    
    $current_user = wp_get_current_user();
    $sender_email = $current_user->user_email;
    $sender_name = $current_user->user_firstname . " " . $current_user->user_lastname;

    // check that a target is defined.
    if( $target != null && $target != "" )
    {
        // check for at least 1 receipient
        if (count( $recipients ) > 0)
        {
            // we have at least 1 receipient. Check what to do next.
            if( $target == "create_account" )
            {  
                // can expect only 1 recipient, send email and return the response
                return massMail($sender_email, $sender_name, $recipients);
            }
            else if($target == "NewSubscription" || $target == "NewAccount")
            {
                return massMail(get_bloginfo( 'admin_email' ),'Expert Online Training', $recipients);
            }
            else if($target == "cloudflare_error")
            {
                return massMail(get_bloginfo( 'admin_email' ), 'Couldflare error', $recipients);
            }
            else if($target == "massmail" )
            {
              return massMail($sender_email, $sender_name, $recipients);
            }
        }
        else
        {
            // no receipients
            return array('status' => 0, 'message' => "sendMail error: no recipients.");
        }
    }
    else 
    {
        // No target defined. Return error.
        return array('status' => 0, 'message' => "sendMail error: invalid target.");
    }
}

/**
 * Worker function that sends the email to the specified receipients
 *
 * @param string $sender_email - The email of the sender
 * @param string $sender_name - The name of the sender
 * @param array $recipients - an array of associative arrays with each receipient and all the email info eg. name, email, subject, message
 */
function massMail ( $sender_email = '', $sender_name = '', $recipients = array()) 
{
    $status = 1; // status of the send, success or failure.

    // check that we have a name/email for the from field. ie. the sender.
    if( $sender_email != null && $sender_email != "" && $sender_name != null && $sender_name != "")
    {

        // using mandrill, can only send emails from our domain. So must set the from email manually.
        $headers = array(
            "From: $sender_name <".get_bloginfo('admin_email').">",
            "Reply-To: $sender_name <$sender_email>",
            "Content-Type: text/html; charset=UTF-8"
            );

        // we have the sender info now send them email(s) 
        foreach ($recipients as $recipient)
        {
            if(!wp_mail( $recipient['email'], $recipient['subject'], $recipient['message'], $headers ))
            {
              $status = 0;
              error_log('massmail error: to: ' . $recipient['email'] . ' num reciepients: ' . count($recipients));
            }
        }       
    }
    else
    {
        return array('status' => 0, 'message' => "massMail error: invalid sender name/email.");
    }


    // Check if the mesage sends.
    if ( $status )
    {
        return array('status' => 1, 'message' => 'Your message was sent succesfully.');
    } 
    else 
    {
        return array('status' => 0, 'message' => 'massMail error: Your messsage failed to send.');
    }
}

/**
 *   Updating user info.
 */  
add_action('wp_ajax_updateUser', 'updateUser_callback'); //handles actions and triggered when the user is logged in
function updateUser_callback() 
{
    if( isset ( $_REQUEST['name'] ) && isset ( $_REQUEST['lastname'] ) && isset($_REQUEST['email']) && isset ( $_REQUEST['old_email'] ))
    {
        $chars=array("'",'"',"?","’","”","&quot;",'\"',"\'",'\\');
        $first_name = str_replace($chars, "", trim($_REQUEST['name']));
        $first_name  = filter_var($first_name, FILTER_SANITIZE_STRING);
        
        $last_name = str_replace($chars, "", trim($_REQUEST['lastname']));
        $last_name  = filter_var($last_name, FILTER_SANITIZE_STRING);
        
        $email = sanitize_email( $_REQUEST['email'] );
        $old_email = sanitize_email( $_REQUEST['old_email'] );
        $user_id = filter_var($_REQUEST['staff_id'], FILTER_SANITIZE_STRING);
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $password = isset($_REQUEST['pw']) ? $_REQUEST['pw'] : '';
        $new_user = array();
        $original_id = $user_id;

        //make sure we preserve the role
        $accepted_roles = array('manager', 'student', 'uber_manager', 'umbrella_manager');
        $role = (isset($_REQUEST['role']) && in_array($_REQUEST['role'], $accepted_roles)) ? $_REQUEST['role'] : 'student';

        // Check permissions
        if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'update-staff_' . $user_id ) ) 
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'updateUser_callback error: Sorry, your nonce did not verify.';
        }
        else if($first_name == ""){
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'updateuser_callback Error: Please Enter a first name';
        }
        else if($last_name == ""){
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'updateuser_callback Error: Please Enter a last name';
        }
        else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'updateuser_callback Error: Sorry, you do not have permisison to view this page.';
        }
        else 
        {
                // check if user exists
                $user_id = get_user_by( 'email', $old_email ); // The user in WP
                // update or insert new user in WP
                $userdata = array (
                    'user_login' => $email,
                    'ID' => $user_id->ID,
                    'user_email' => $email,
                    'user_pass' => $password,
                    'role' => $role,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $first_name . " " . $last_name,
                );
                if ($user_id) 
                {
                    // check if email was updated because if it was, we need to update the user_login field.
                    if ($email != $old_email)
                    {
                        global $wpdb;
                        // cant use wp_insert_user because we need to updtate login as well and that function wont do it.
                        if ( $wpdb->update( $wpdb->users, array( 'user_login' => $email ), array( 'ID' => $user_id->ID ) ) )
                        {
                            // success
                            $result['success'] = true;
                            $result['message'] = 'User account information has been successfully updated.';
                            $result['staff_id']=$user_id->ID;
                            $result['staff_email']=$email;
                            $result['email']=$email;
                            $result['old_email']= $old_email;
                            $result['first_name']=$first_name;
                            $result['last_name']=$last_name;
                        }
                        else
                        {
                            //failed
                            $result['display_errors'] = 'failed';
                            $result['success'] = false;
                            $result['errors'] = 'updateUser_callback Error: Could not update WP user.';
                        }
                    }
                    // dont change their password unless they added a new password
                    if (!$password)
                    {
                        unset($userdata['user_pass']);
                    }

                    // remove the user_login because the update function below cant do it.
                    unset($userdata['user_login']);

                    // update the user into WP
                    $WP_user_id = wp_update_user ($userdata);
                    // success
                    $result['success'] = true;
                    $result['message'] = 'User account information has been successfully updated.';
                    $result['staff_id'] = $WP_user_id;
                    $result['old_email']= $old_email;
                    $result['staff_email']=$email;
                    $result['email']=$email;
                    $result['first_name']=$first_name;
                    $result['last_name']=$last_name;
                }
                else
                {
                    // user doesnt exist to create WP User
                    // insert the user into WP
                    $WP_user_id = wp_insert_user ($userdata);

                    // Newly created WP user needs some meta data values added
                    update_user_meta ( $WP_user_id, 'org_id', $org_id );
                    update_user_meta ( $WP_user_id, 'accepted_terms', '0');

                    // assume we are successful for now... check later.
                    $result['success'] = true;
                    $result['message'] = 'User account information has been successfully updated3.';
                    $result['staff_id']=$WP_user_id;
                    $result['old_email']= $old_email;
                    $result['staff_email']=$email;
                    $result['name']=$first_name;
                    $result['lastname']=$last_name;
                }   

                // check if we need to update the org name
                if ($org_id && isset($_REQUEST['camp_name']) && $_REQUEST['camp_name'] != "" && isset($_REQUEST['old_camp_name']) && $_REQUEST['camp_name'] != $_REQUEST['old_camp_name'] && isset($_REQUEST['user_id']))
                {
                  $this_user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);

                  // verify the org ID belongs to this user
                  if ($org_id == get_user_meta($this_user_id, 'org_id', true))
                  {
                    // update the camp name
                    $camp_name = filter_var($_REQUEST['camp_name'], FILTER_SANITIZE_STRING);
                    $args = array (
                      'ID' => $org_id,
                      'post_title' => $camp_name
                    );
                    wp_update_post( $args );
                  }
                }



/* @TODO remove if not using later on
            // check that previous attempts to update user did not fail. If not, check if we need to update portal name (camp name)
            if (isset($result['success']) && $result['success'])
            {
              // check if we need to update portal name
              if (isset($_REQUEST['portal_id']) && isset($_REQUEST['old_camp_name']) && isset($_REQUEST['camp_name']) && $_REQUEST['old_camp_name'] != $_REQUEST['camp_name'])
              {
                $camp_name = filter_var($_REQUEST['camp_name'], FILTER_SANITIZE_STRING);
                $portal_id = filter_var($_REQUEST['portal_id'], FILTER_SANITIZE_NUMBER_INT); // LU Portal ID
                // update the portal name on LU
                $portal_data = array('title' => $camp_name);

                  // Success in updating the portal on LU, now update camp name in WP
                  $post_data = array (
                    'ID'          => $org_id,
                    'post_title'  => $camp_name,
                    );
                  
                  $update_post = wp_update_post($post_data, true);

                  // check if there was an error updating WP camp name. If so, undo LU changes
                  if (is_wp_error($update_post))
                  {
                    $errors = $update_post->get_error_messages();
                    foreach ($errors as $error) {
                      write_log($error);
                    }

                    // undo changes in LU
                    $portal_data = array('title' => $_REQUEST['old_camp_name']);
                    $response = updatePortal($portal_id, $org_subdomain, $portal_data);
                  }

              }
            }
*/            
        }
    }
    else
    {
        $result['display_errors'] = 'failed';
        $result['success'] = false;
        $result['errors'] = 'updateUser_callback Error: Missing some parameters.';
    }

    echo json_encode( $result );
    wp_die();
}

/********************************************************************************************************
 * Delete a staff account.
 *******************************************************************************************************/
add_action('wp_ajax_deleteStaffAccount', 'deleteStaffAccount_callback');
function deleteStaffAccount_callback () 
{
    global $wpdb;
    if( isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['staff_id'] ) && isset ( $_REQUEST['email'] ) )
    {
        // This form is generated in getCourseForm function with $form_name = change_course_status_form from this file.
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT); // The Org ID
        $staff_id = filter_var($_REQUEST['staff_id'], FILTER_SANITIZE_NUMBER_INT); // The staff account ID
        $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The staff account ID
        $email = sanitize_email( $_REQUEST['email'] ); // wordpress e-mail address
        $data = compact("org_id");

        // Check permissions
        if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'delete-staff_id-org_id_' . $org_id ) ) 
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'deleteStaffAccount_callback error: Sorry, your nonce did not verify.';
        }
        else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'deleteStaffAccount_callback Error: Sorry, you do not have permisison to view this page.';
        }
        else
        {
                $watched_video = $wpdb->get_results("SELECT * FROM ". TABLE_TRACK ." "
                        . "WHERE user_id = $staff_id "
                        . "AND org_id = $org_id "
                        . "AND type IN('watch_video','watch_custom_video') "
                        . "AND date BETWEEN '".SUBSCRIPTION_START."' AND '".SUBSCRIPTION_END."'", ARRAY_A);
                if(count($watched_video)>0)
                {
                    $result['display_errors'] = 'failed';
                    $result['success'] = false;
                    $result['errors'] = 'deleteStaffAccount_callback Error: Sorry, this user has already started the course.';
                    echo json_encode($result);
                    wp_die();
                }
                // Delete the staff account from LU
                $user = get_user_by( 'ID', $staff_id ); // The user in WP
                if($user)
                {
                    // Delete the account in WP
                    if (wp_delete_user( $user->ID ))
                    {
                        // Build the response if successful
                        $deleted_enrollment = $wpdb->delete(TABLE_ENROLLMENTS, array('user_id'=>$user->ID)); // must delete their enrollment as well.
                        $result['data'] = 'success';
                        $result['user_id'] = $staff_id;
                        $result['success'] = true;
                        $result['email'] = $email;
                    } 
                    else
                    {
                        $result['display_errors'] = 'failed';
                        $result['success'] = false;
                        $result['errors'] = 'deleteStaffAccount_callback ERROR: Could not delete the WP user account.';
                    }
                }
                else
                {   
                    $result['display_errors'] = 'failed';
                    $result['success'] = false;
                    $result['errors'] = 'deleteStaffAccount_callback ERROR: Could not find the user account.';
                }
        }
    }
    else
    {
        $result['display_errors'] = 'failed';
        $result['success'] = false;
        $result['errors'] = 'deleteStaffAccount_callback ERROR: Missing some parameters.';
    }
    echo json_encode($result);
    wp_die();
}

/**
 * 
 * @global type $wpdb
 * @param type $email - the email of the user
 * @param type $course_id - the course ID
 * @return boolean
 * 
 */
function user_is_enrolled($email = '', $course_id = 0)
{
    global $wpdb;
    $email = filter_var($email , FILTER_SANITIZE_EMAIL);
    $course_id = filter_var($course_id , FILTER_SANITIZE_NUMBER_INT);
    $enrollment = $wpdb->get_row("SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE email = '$email' AND course_id = $course_id", ARRAY_N);
    if ( null !== $enrollment ) {
        // do something with the link 
        return true;
      } else {
        // no link found
        return false;
      }
}

/********************************************************************************************************
 * Create enrollment for the user
 *******************************************************************************************************/
add_action('wp_ajax_enrollUserInCourse', 'enrollUserInCourse_callback'); 
function enrollUserInCourse_callback () 
{

    // This form is generated in getCourseForm function with $form_name = add_staff_to_group from this file.
    $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT); // The organization ID
    $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); // The organization ID
    $email  = filter_var($_REQUEST['email'],FILTER_SANITIZE_STRING); // The Email Address of the user
    $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT); // The Course ID
    $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING); // The course Name
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_STRING); // The subscription id

    // Check permissions
    if(wp_verify_nonce( $_REQUEST['nonce'], 'process-userEmail_' . $email ) )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'enrollUserInCourse_callback error: Sorry, your nonce did not verify.';
    }
    else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'enrollUserInCourse_callback error: Sorry, you do not have permisison to view this page.';
    }
    else 
    {
        $data = compact( "org_id", "course_name");
        global $wpdb;
        // Save enrollments to the database.
        $insert = $wpdb->insert(
          TABLE_ENROLLMENTS, 
          array( 
            'course_id' => $course_id, 
            'subscription_id' => $subscription_id,
            'email' => $email,
            'user_id' => $user_id,
            'org_id' => $org_id
          ), 
          array( 
            '%d', 
            '%d',
            '%s', 
            '%d',
            '%d' 
        ));

        // Didn't save. return an error.
        if ($insert === FALSE)
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Response Message: " . $wpdb->last_error;
        }
        else
        {
          // Build the response if successful
          $result['message'] = 'User has been enrolled.';
          $result['success'] = true;
          $result['enrollment_id'] = $wpdb->insert_id;
        }

    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * Delete the enrollment for the user
 *******************************************************************************************************/
add_action('wp_ajax_deleteEnrolledUser', 'deleteEnrolledUser_callback'); 
function deleteEnrolledUser_callback () 
{

    // This form is generated in getCourseForm function with $form_name = add_staff_to_group from this file.
    $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
    $email  = filter_var($_REQUEST['email'],FILTER_SANITIZE_STRING);
    $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
    $enrollment_id = filter_var($_REQUEST['enrollment_id'],FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);

    // Check permissions
    if(wp_verify_nonce( $_REQUEST['nonce'], 'process-userEmail_' . $email ) )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'deleteEnrolledUser_callback Error: Sorry, your nonce did not verify.';
    }
    else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'deleteEnrolledUser_callback Error: Sorry, you do not have permisison to view this page.';
    }
    else 
    {
      global $wpdb;
      // Delete the record from our database.
      $response = $wpdb->delete(TABLE_ENROLLMENTS, // Table to delete the data from
                                array('course_id' => $course_id, 
                                      'user_id' => $user_id));
      // Something went wrong. Display error message.
      if ($response === FALSE)
      {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Response Message: " . $wpdb->last_error;
      }
      else 
      {
          // Build the response if successful
          $result['data'] = 'success';
          $result['message'] = 'the enrollment has been deleted';
          $result['group_id'] = $course_id;
          $result['success'] = true;
      }
    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * Update due date for the course.
 *******************************************************************************************************/
add_action('wp_ajax_updateDueDate', 'updateDueDate_callback'); 
function updateDueDate_callback() 
{
    if( isset ( $_REQUEST['action'] ) && isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['task'] ) && isset ( $_REQUEST['org_id'] ) )
    {
        if($_REQUEST['task'] == "remove" || $_REQUEST['task'] == "add")
        {
            $course_id                   = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
            $org_id                      = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT); // The org id
            $task                        = filter_var($_REQUEST['task'],FILTER_SANITIZE_STRING); // The task
            
            // check if user has admin/manager permissions
            if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
            {
                $result['display_errors'] = 'Failed';
                $result['success'] = false;
                $result['errors'] = 'updateDueDate_callback Error: Sorry, you do not have permisison to view this page.';
            }
            else
            {
                // This sets the due date
                if( $task == "remove" )
                {
                    $due_date_after_enrollment = "1000-01-01 00:00:00"; // Remove the date.
                }
                else if( $task == "add" )
                {
                    $due_date_after_enrollment = filter_var($_REQUEST['date'],FILTER_SANITIZE_STRING); // The due date 
                }

                $data = compact( "org_id", "due_date_after_enrollment");
                // Edit the course
                //$response = updateCourse($course_id, $portal_subdomain, $data);
                global $wpdb;
                $response=$wpdb->update(TABLE_COURSES, array('due_date_after_enrollment'=>date("Y-m-d H:i:s", strtotime($due_date_after_enrollment))), array('ID' => $course_id));
                if ($response === false)
                {
                    $result['display_errors'] = true;
                    $result['success'] = false;
                    $result['errors'] = "updateDueDate_callback ERROR: ". $response['message'];

                }
                else
                {
                    // Build the response if successful
                    $result['success'] = true;
                }
                
            }
        }
        else
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "updateDueDate_callback ERROR: invalid task.";
        }
    }
    else
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = "updateDueDate_callback ERROR: Missing parameters";
    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * get Amazon Web Services S3 Credentials
 *******************************************************************************************************/
add_action('wp_ajax_getAwsCredentials', 'getAwsCredentials_callback'); 
function getAwsCredentials_callback ( ) 
{
    if(current_user_can("is_director"))
    {
        $credentials = array(
            'accessKeyId'=> AWS_ACCESS_KEY_ID,
            'secretAccessKey'=> AWS_SECRET_ACCESS_KEY
        );

        echo json_encode($credentials);
        wp_die();
    }
    wp_die();
}

/****
 * gets AWS user uploads from uploads table
 * @param org_id - the organization id
 * @param user_id - the id of the user
 */
function getUserUploads($org_id = 0, $user_id = 0)
{
    global $wpdb;
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    $uploads = $wpdb->get_results("SELECT * FROM " . TABLE_RESOURCES . " WHERE org_id = $org_id AND owner_id = $user_id", ARRAY_A);
    return $uploads;
}

/*
 * Save Data to database for AWS uploads
 */

add_action( 'wp_ajax_upload_file', 'upload_file_callback' );
function upload_file_callback() 
{
  $filetype = filter_var($_REQUEST['type'], FILTER_SANITIZE_STRING);
  $fta =  explode('/', $filetype); // filetype has slashes in it ie. text/csv or image/jpg
  $type = $fta[0]; // get the first part of the filetype
  $thetype = '';
  switch ($type) 
  {
      case 'video':
          $thetype = 'custom_video';
          break;
      case 'image':
          $thetype = 'doc';
          break;
      default:
          $thetype = 'doc';
          break;
  }
	
  // insert the file data into resources table
  global $wpdb;
  $data = array
  (
      'owner_id' => filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT),
      'org_id' => filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT),
      'file_key' => $_REQUEST['key'],
      'url' => filter_var($_REQUEST['url'],FILTER_SANITIZE_URL),
      'type' => $thetype,
      'name' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']))
  );
  $insert = $wpdb->insert(TABLE_RESOURCES, $data);
  if($insert != FALSE)
  {
      echo json_encode(array('message' => 'success', 'id' => $wpdb->insert_id));
  }
  else
  {
      echo json_encode(array('message' => 'fail'));
  }
        
	wp_die();
}

/*
 * Rename the title of the User Uploaded File
 */
add_action( 'wp_ajax_update_file_title', 'update_file_title_callback' );
function update_file_title_callback()
{
    $title = preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']));
    $id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
    if($title == "")
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = "Your title is invalid";
    }
    else
    {
      global $wpdb;
      $data = array('name' => $title);
      $update = $wpdb->update(TABLE_RESOURCES, $data, array('ID' => $id));
      if ($update == false) {
          $result['success'] = false;
          $result['display_errors'] = false;
      } 
      else 
      {
          $result['success'] = true;
          $result['display_errors'] = false;
      }
    }
    echo json_encode($result);
    wp_die();
}

/*
 * Save the URL link into the resources table  when a user uploads a link
 */

add_action( 'wp_ajax_save_url', 'save_url_callback' );
function save_url_callback() 
{
	global $wpdb;
  $data=array
  (
      'owner_id' => filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT),
      'org_id' => filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT),
      'file_key' => filter_var($_REQUEST['key'], FILTER_SANITIZE_STRING),
      'url' => filter_var($_REQUEST['url'], FILTER_SANITIZE_URL),
      'type' => 'link',
      'name' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']))
  );
  $insert = $wpdb->insert(TABLE_RESOURCES, $data);
  if($insert != FALSE)
  {
      echo json_encode(array('message' => 'success', 'id' => $wpdb->insert_id));
  }
  else
  {
      echo json_encode(array('message' => 'fail'));
  }
 	wp_die();
}

/*
 * Delete user uploaded AWS file
 */
add_action( 'wp_ajax_aws_delete_file', 'aws_delete_file_callback' );
function aws_delete_file_callback()
{
    $path = WP_PLUGIN_DIR . '/EOT_LMS/';
    require $path . 'includes/aws/aws-autoloader.php';
    // Instantiate the S3 client with your AWS credentials
    $deleted = true; // assume true unless we have a non URL resource in which case try to delete it first from S3.
    

    if($_REQUEST['key'] != 'url')
    {
      $s3Client = new Aws\S3\S3Client(array(
        'version' => 'latest',
        'region' => AWS_REGION,
        'credentials' => array(
            'key' => AWS_ACCESS_KEY_ID,
            'secret' => AWS_SECRET_ACCESS_KEY,
        )
      ));

      $bucket = AWS_S3_USER_RESOURCES;
      $keyname = filter_var($_REQUEST['key'], FILTER_SANITIZE_STRING);

      $res = $s3Client->deleteObject(
        array(
          'Bucket' => $bucket,
          'Key'    => $keyname
        )
      );
    
      $confirm = $s3Client->doesObjectExist($bucket, $keyname);
      if($confirm)
      {
        $deleted = false;
      }
      else
      {
        $deleted = true;
      }
    }
    
    // remove from resources table if it was successfully deleted from S3 or if its a URL
    if($deleted)
    {
        global $wpdb;
        $id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
        $del = $wpdb->delete(TABLE_RESOURCES, array('ID' => $id));

        if ($del == false)
        {
          $result['display_errors'] = true;
          $result['success'] = false;
          $result['errors'] = "Your file cannot be deleted";
        } 
        else 
        {
          $result['success'] = true;
          $result['display_errors'] = false;
        }
    }
    else
    {
      $result['display_errors'] = true;
      $result['success'] = false;
      $result['errors'] = "Your file cannot be deleted";
    }
    echo json_encode($result);
    wp_die();
}

/*
 * Rename User Uploaded File
 */
add_action( 'wp_ajax_get_upload_form', 'get_upload_form_callback' );
function get_upload_form_callback()
{
    switch ($_REQUEST['form_name']) 
    {
      case 'update_title':
        $title = filter_var($_REQUEST['title'], FILTER_SANITIZE_STRING);
        $id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
        ob_start();
?>
        <div class="title" style="width:320px">
            <div class="title_h2">Type in or update your title</div>
        </div>
        <div class="middle">
            <span class="errorbox"></span>
            <form id= "add_title" frm_name="update_file_title" frm_action="update_file_title" rel="submit_form" hasError=0> 
                        <div class=" bs form_group">
                            <input class="bs form-control" type="text" name="title" value="<?=$title;?>" /> 
                            <input type="hidden" name="id" value="<?= $id ?>"
                            <?php wp_nonce_field('update-file-title_' . $id); ?>
                        </div> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
          <div class="buttons">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="update_file_title" style="display:none"></i>
            <a onclick="jQuery(document).trigger('close.facebox');" >
              <div style="height:15px;padding-top:2px;"> Cancel</div>
            </a>
            <a active = '0' acton = "update_file_title" rel = "submit_button" onclick="jQuery('#update_file_title').show();">
              <div style="height:15px;padding-top:2px;"> Submit</div>
            </a>
          </div>
      </div>
<?php
        $html = ob_get_clean();
        echo $html;
        wp_die();
        break;
      
      case 'delete_file':
        $key = filter_var($_REQUEST['key'], FILTER_SANITIZE_STRING);
        $id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
        ob_start();
?>
        <div class="title">
            <div class="title_h2">Delete This File?</div>
        </div>
        <div class="middle">
            <form id= "delete_quiz" frm_name="aws_delete_file" frm_action="aws_delete_file" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                    <tr>
                        <td class="value">
                            <p>Are you sure you want to delete this file?</p>
                            <input type="hidden" name="key" value="<?= $key ?>" /> 
                            <input type="hidden" name="id" value="<?= $id ?>" />
                            <?php wp_nonce_field('aws-delete-file_' . $key); ?>
                        </td> 
                    </tr> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
            <div class="buttons">
              <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="aws_delete_file" style="display:none"></i>
              <a onclick="jQuery(document).trigger('close.facebox');" >
                <div style="height:15px;padding-top:2px;"> Cancel</div>
              </a>
              <a active = '0' acton = "aws_delete_file" rel = "submit_button" onclick="jQuery('#aws_delete_file').show();">
                <div style="height:15px;padding-top:2px;"> Yes</div>
              </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        wp_die();
        break;
      default:
        break;
    }
}

/**
 * Facebox html code for various custom module actions such as update title, etc...
 */
add_action('wp_ajax_get_module_form','get_module_form_callback');
function get_module_form_callback()
{
    switch ($_REQUEST['form_name']) 
    {
      case 'update_title':
        $title = preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']));
        $module_id = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        ob_start();
?>
        <div class="title" style="width:320px">
            <div class="title_h2">Type in or update your title</div>
        </div>
        <div class="middle">
            <span class="errorbox"></span>
            <form id= "update_module_title" frm_name="update_module_title" frm_action="update_module_title" rel="submit_form" hasError=0> 
                        <div class=" bs form_group">
                            <input class="bs form-control" type="text" name="title" value="<?=$title;?>" /> 
                            <input type="hidden" name="module_id" value="<?= $module_id?>"/>
                            <input type="hidden" name="org_id" value="<?= $org_id?>"/>
                            <?php wp_nonce_field('update-module-title_' . $module_id); ?>
                        </div> 
                </table> 
            </form>
        </div>      
         <div class="popup_footer">
            <div class="buttons">
              <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="update_module_title" style="display:none"></i>
              <a onclick="jQuery(document).trigger('close.facebox');" >
                <div style="height:15px;padding-top:2px;"> Cancel</div>
              </a>
              <a active = '0' acton = "update_module_title" rel = "submit_button" onclick="jQuery('#update_module_title').show();">
                <div style="height:15px;padding-top:2px;"> Submit</div>
              </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        wp_die();
        break;
      
      case 'delete_module':
        $module_id = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        ob_start();
?>
        <div class="title">
            <div class="title_h2">Delete This Module?</div>
        </div>
        <div class="middle">
            <form id= "delete_quiz" frm_name="delete_module" frm_action="delete_module" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                    <tr>
                        <td class="value">
                            <p>If there are users assigned to this module, all records of the results will be lost</p>
                            <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                            <input type="hidden" name="module_id" value="<?= $module_id ?>" /> 
                            <?php wp_nonce_field('delete-module_' . $module_id); ?>
                        </td> 
                    </tr> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
            <div class="buttons">
              <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="delete_module" style="display:none"></i>
              <a onclick="jQuery(document).trigger('close.facebox');" >
                <div style="height:15px;padding-top:2px;"> Cancel</div>
              </a>
              <a active = '0' acton = "delete_module" rel = "submit_button" onclick="jQuery('#delete_module').show();">
                <div style="height:15px;padding-top:2px;"> Yes</div>
              </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        wp_die();
        break;
      
      case 'delete_resource':
        $resource_id = filter_var($_REQUEST['resource_id'], FILTER_SANITIZE_NUMBER_INT);
        $module_id = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        ob_start();
?>
        <div class="title">
            <div class="title_h2">Delete This Module Resource?</div>
        </div>
        <div class="middle">
            <form id= "delete_quiz" frm_name="delete_resource" frm_action="delete_resource" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                    <tr>
                        <td class="value">
                            <p>If there are users assigned to this module resource, all records of the results will be lost</p>
                            <input type="hidden" name="module_id" value="<?= $module_id ?>" /> 
                            <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                            <input type="hidden" name="resource_id" value="<?= $resource_id ?>" /> 
                            <?php wp_nonce_field('delete-resource_' . $resource_id); ?>
                        </td> 
                    </tr> 
                </table> 
            </form>
        </div>      
        <div class="popup_footer">
            <div class="buttons">
              <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="delete_resource" style="display:none"></i>
              <a onclick="jQuery(document).trigger('close.facebox');" >
                <div style="height:15px;padding-top:2px;"> Cancel</div>
              </a>
              <a active = '0' acton = "delete_resource" rel = "submit_button" onclick="jQuery('#delete_resource').show();">
                <div style="height:15px;padding-top:2px;"> Yes</div>
              </a>
            </div>
        </div>
<?php
        $html = ob_get_clean();
        echo $html;
        wp_die();
        break;
    }
}

/**
 * Delete the entire module
 * requires org_id to verify user's access to this module
 */
add_action('wp_ajax_delete_module','delete_module_callback');
function delete_module_callback()
{
        global $wpdb;
        $module_id = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);

        // Check permissions
        if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'delete-module_' . $module_id ) ) 
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'delete module error: Sorry, your nonce did not verify.';
            echo json_encode( $result );
            wp_die();
        }

        $del = $wpdb->delete(TABLE_MODULES, array('ID' => $module_id, 'org_id' => $org_id));
        
        if($del)
        {
            $wpdb->delete(TABLE_MODULE_RESOURCES, array('module_id' => $module_id));
            echo json_encode(array('success' => 'true'));
        }
        else
        {
           echo json_encode(array('success' => 'false')); 
        }
        wp_die();
}

/**
 * Delete a resource from an individual module
 * requires org id & module id to make sure module belongs to this user/org
 */
add_action('wp_ajax_delete_resource','delete_resource_callback');
function delete_resource_callback()
{
        global $wpdb;
        $resource_id = filter_var($_REQUEST['resource_id'], FILTER_SANITIZE_NUMBER_INT);
        $module_id = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        error_log("Module id is $module_id. Org id is $org_id and resource_id is $resource_id");
        // Check permissions
        if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'delete-resource_' . $resource_id ) ) 
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = 'delete resource error: Sorry, your nonce did not verify.';
            echo json_encode( $result );
            wp_die();
        }

        // verify that the user/org ownes this module/reources
        $query = "SELECT ID FROM " . TABLE_MODULES . " WHERE ID = $module_id AND org_id = $org_id";
        $verified = $wpdb->get_row($query, ARRAY_A);
        if ($verified)
        {
          $del = $wpdb->delete(TABLE_MODULE_RESOURCES, array('resource_id' => $resource_id, 'module_id' => $module_id));
          if($del)
          {
              $courses = $wpdb->get_results("SELECT * FROM ".TABLE_COURSE_MODULE_RESOURCES. " WHERE module_id = $module_id AND resource_id = $resource_id",ARRAY_A);
              foreach ($courses as $course) {
                  $del = $wpdb->delete(TABLE_COURSE_MODULE_RESOURCES,array(
                      'course_id'=>$course['course_id'],
                      'module_id' => $module_id,
                      'resource_id'=> $resource_id
                  ));
              }
              echo json_encode(array('success' => 'true'));
          }
          else
          {
             echo json_encode(array('success' => 'false', "errors" => "There was an error deleting the resource.")); 
          }
        }
        else
        {
          echo json_encode(array('success' => 'false', "errors" => "There was an error deleting the resource, you do not appear to own this resource."));
        }
        wp_die();
}

/**
 * Update the module title
 */
add_action('wp_ajax_update_module_title','update_module_title_callback');
function update_module_title_callback()
{
    global $wpdb;
    $title = preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['title']));
    $module_id = filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT);
    $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);

    // Check permissions
    if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'update-module-title_' . $module_id ) ) 
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = 'update module title error: Sorry, your nonce did not verify.';
        echo json_encode( $result );
        wp_die();
    }

    if ($title == "") 
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = "Your module title is invalid";
    } 
    else 
    {
        $update = $wpdb->update(TABLE_MODULES, array('title' => $title), array('ID' => $module_id, 'org_id' => $org_id));
        if($update===false)
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "There was an error changing your title";
        }
        else
        {
            $result['success'] = true;
            $result['display_errors'] = false;
            $result['title'] = $title;
        }
    }
    echo json_encode($result);
    wp_die();
}

/**
 * get all the resources in a module. 
 * Returns an associative array with the resource data
 */
function getResourcesInModule($module_id = 0)
{
    global $wpdb;
    $module_id = filter_var($module_id,FILTER_SANITIZE_NUMBER_INT);
    $resources = $wpdb->get_results("SELECT * FROM " . TABLE_MODULE_RESOURCES . " AS mr WHERE mr.module_id = " . $module_id . " ORDER BY mr.order ASC", ARRAY_A);
    $clean_results = array();
    foreach($resources as $resource)
    {
      if($resource['type'] == 'exam')
      {
        $thequiz = $wpdb->get_row("SELECT * FROM " . TABLE_QUIZ . " WHERE ID = " . $resource['resource_id'], ARRAY_A);
        if($thequiz)
        {
          array_push($clean_results, 
            array(
              'name' => $thequiz['name'],
              'ID' => $thequiz['ID'],
              'resource_id' => $resource['ID'],
              'type' => 'exam',
              'order' => $resource['order']
            )
          );
        }
      }
      else if($resource['type'] == 'video')
      {
        $thevideo = $wpdb->get_row("SELECT * FROM " . TABLE_VIDEOS . " WHERE ID = " . $resource['resource_id'], ARRAY_A);
        if($thevideo)
        {
          array_push($clean_results, 
            array(
              'name' => $thevideo['name'],
              'ID' => $thevideo['ID'],
              'desc' => $thevideo['desc'],
              'video_name' => $thevideo['video_name'],
              'secs' => $thevideo['secs'],
              'shortname' => $thevideo['shortname'],
              'shortname_medium' => $thevideo['shortname_medium'],
              'shortname_low' => $thevideo['shortname_low'],
              'spanish' => $thevideo['spanish'],
              'resource_id' => $resource['ID'],
              'type' => 'video',
              'order' => $resource['order']
            )
          );
        }
      }
      else
      {
        $theresource = $wpdb->get_row("SELECT * FROM " . TABLE_RESOURCES . " WHERE ID = " . $resource['resource_id'], ARRAY_A);
        if($theresource)
        {
          array_push($clean_results, 
            array(
              'name' => $theresource['name'],
              'ID' => $theresource['ID'],
              'url' => $theresource['url'],     
              'resource_id' => $resource['ID'],
              'type' => $resource['type'],
              'order' => $resource['order']));      
        } 
      }
    }
    return $clean_results;
}

/**
 * return all the module data
 * @param int $module_id - the module ID
 * returns an associative array of all table fields 
 */
function getModule($module_id = 0)
{
    global $wpdb;
    $module_id = filter_var($module_id, FILTER_SANITIZE_NUMBER_INT);
    $module = $wpdb->get_row("SELECT * FROM " . TABLE_MODULES. " WHERE ID = $module_id", ARRAY_A);
    return $module;
}

/**
 * Adds a resource into a module
 */
add_action('wp_ajax_add_resource_to_module','add_resource_to_module_callback');
function add_resource_to_module_callback()
{
    global $wpdb;
    $module_id = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
    $resource_id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
    $order = filter_var($_REQUEST['order'], FILTER_SANITIZE_NUMBER_INT);
    $type = filter_var($_REQUEST['type'], FILTER_SANITIZE_STRING);
    $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
    $thetype = ''; // will contain the type of resource to add
    if($type == "quiz")
    {
      $thetype = "exam";
      // make sure this quiz is owned by the user/org
      $query = "SELECT ID FROM " . TABLE_QUIZ . " WHERE ID = $resource_id AND org_id = $org_id";
      $verified = $wpdb->get_row($query, ARRAY_A);
      if (!$verified)
      {
        $thetype = ''; // quiz doesn't belong to this user/org. Set type to NULL so it dies below.
      } 
    }
    else
    {
      $theresource = $wpdb->get_row("SELECT * FROM " . TABLE_RESOURCES . " WHERE ID = $resource_id AND org_id = $org_id", ARRAY_A);
      $thetype = isset($theresource['type']) ? $theresource['type'] : '';
    }
 
    // make sure we got a resource type.
    if($thetype == '')
    {
      echo json_encode(array('message' => 'Error in add_resource_to_module: coulnt find the resource type'));
      wp_die();
    }

    // the resource data for module_resources table
    $data = array(
        'module_id' => $module_id,
        'resource_id' => $resource_id,
        'type' => $thetype,
        'order' => $order
    );
    
    // check if the resource already exists in the module.
    $existing = $wpdb->get_row("SELECT * FROM " . TABLE_MODULE_RESOURCES . " WHERE module_id = " . $module_id . " AND resource_id = " . $resource_id . " AND type = '" . $thetype . "'",ARRAY_A);
    if($existing)
    {
        echo json_encode(array('message'=>'You have already added this resource to this module.'));
    }
    else
    {
        $update = $wpdb->insert(TABLE_MODULE_RESOURCES, $data);
        if($update)
        {
            $courses_in = $wpdb->get_results("SELECT * FROM ".TABLE_COURSE_MODULE_RESOURCES. " WHERE module_id = $module_id", ARRAY_A);
            if($courses_in)
            {
                $course_ids = array_unique(array_column($courses_in, 'course_id'));
            }
            foreach ($course_ids as $course_id) {
                $existing = $wpdb->get_row("SELECT * FROM ".TABLE_COURSE_MODULE_RESOURCES. " WHERE course_id = $course_id AND module_id = $module_id AND resource_id = $resource_id and type = '$thetype'");
                if(!$existing)
                {
                        $data = array(
                            'course_id' => $course_id,
                            'module_id' => $module_id,
                            'resource_id' => $resource_id,
                            'type' => $thetype,
                            'order' => $order
                        );
                        $insert = $wpdb->insert(TABLE_COURSE_MODULE_RESOURCES,$data);
                }
                
            }
            
            error_log(json_encode($courses_in));
            echo json_encode(array('message'=>'success'));
        }
        else
        {
            echo json_encode(array('message'=>'fail'));
        }
    }
    wp_die();
}

/********************************************************************************************************
 * Create HTML form and return it back as message. this will return an HTML div set to the 
 * javascript and the javascript will inject it into the HTML page.
 * The submit and cancel buttons are handled by javascript in this HTML (part-manage_courses.php for now)  
 *******************************************************************************************************/
add_action('wp_ajax_getCourseForm', 'getCourseForm_callback'); 
function getCourseForm_callback ( ) 
{
    if(isset($_REQUEST['org_id']) && isset($_REQUEST['form_name']) )
    {
        global $current_user;
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
        $form_name = filter_var($_REQUEST['form_name'], FILTER_SANITIZE_STRING);
        $user_id = $current_user->ID;
        if($form_name == "create_course_group")
        {
            $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
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
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'], FILTER_SANITIZE_STRING);
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
            $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
//            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'], FILTER_SANITIZE_STRING);
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
                        
                          <td class="label"> 
                            <label for="field_desc">Description:</label> 
                          </td>
                          <td class="value"> 
                            <input type="text" name="desc" id="field_desc" size="35" value="<?= $course_data['course_description'] ?>"/>  
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
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                <input type="hidden" name="group_id" value="<?= $course_id ?>" />
<!--                                <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />-->
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
            $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'], FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'], FILTER_SANITIZE_STRING);
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
          $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT); // The course ID
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
                  <form name="add_video_group" id="add_video_group">
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
            $course_name = filter_var($_REQUEST['course_name'], FILTER_SANITIZE_STRING);
            $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
            $subscription_id = getSubscriptionFromCourse($course_id);
            $library_id = getLibraryFromSubscription($subscription_id);
            $data = array( "org_id" => $org_id ); // to pass to our functions above
            
            $course_videos = array_merge(getResourcesInCourse($course_id,'video'),getResourcesInCourse($course_id,'custom_video')) ; // all the module videos in the specified course
            $course_quizzes = getResourcesInCourse($course_id,'exam');
            $course_handouts = array_merge(getResourcesInCourse($course_id,'doc'),getResourcesInCourse($course_id,'link'));
            $course_handouts_module_ids = array_column($course_handouts,'mid');
            $course_videos_titles = array_column($course_videos, 'name'); // only the titles of the modules in the specified course
            $course_quizzes_titles = array_column($course_quizzes, 'name');
            $course_handouts_ids = array_column($course_handouts, 'ID');
            $modules_in_portal = getModules($org_id);// all the custom modules in this portal
            $user_modules_titles = array_column($modules_in_portal, 'title'); // only the titles of the modules from the user library.
            
            $categories = getCategoriesByLibrary($library_id);
            
            $master_modules = getModulesByLibrary($library_id);// Get all the modules from the current library.            
            $master_modules_titles = array_column($master_modules, 'title'); // only the titles of the modules from the current library.
            $master_module_ids = array_column($master_modules, 'ID');
            $modules_in_portal_ids = array_column($modules_in_portal, 'ID');
            $all_module_ids = array_merge($master_module_ids, $modules_in_portal_ids);
            $all_module_ids_string = implode(',',$all_module_ids);
            
            $videos_in_course = array();
            $vids = getVideoResourcesInModules($all_module_ids_string);
            foreach($vids as $video)
            {
              if(isset($videos_in_course[$video['module_id']]))
              {
                array_push($videos_in_course[$video['module_id']], array('ID'=>$video['ID'],'name'=>$video['name']));
              }
              else
              {
                $videos_in_course[$video['module_id']]=array();
                array_push($videos_in_course[$video['module_id']], array('ID'=>$video['ID'],'name'=>$video['name']));
              }
            }
            $exams = array();
            
            $resources=getQuizResourcesInModules($all_module_ids_string);
            foreach($resources as $resource)
            {
              if(isset($exams[$resource['module_id']]))
              {
                array_push($exams[$resource['module_id']], array('ID'=>$resource['ID'],'name'=>$resource['name']));
              }
              else
              {
                $exams[$resource['module_id']]=array();
                array_push($exams[$resource['module_id']], array('ID'=>$resource['ID'],'name'=>$resource['name']));
              }
            }
            $handouts = array();
            $handout_resources = getHandoutResourcesInModules($all_module_ids_string);
            
            foreach($handout_resources as $handout)
            {
                if(isset($handouts[$handout['mod_id']]))
                {
                    array_push($handouts[$handout['mod_id']], array('ID'=>$handout['ID'],'name'=>$handout['name']));
                }else{
                $handouts[$handout['mod_id']]=array();
                array_push($handouts[$handout['mod_id']], array('ID'=>$handout['ID'],'name'=>$handout['name']));
                }
            }
//d($exams,$handouts,$videos_in_course,$modules_in_portal,$handout_resources,$course_handouts_ids);
            $course_data=getCourse($course_id);// all the settings for the specified course
            $due_date =$course_data['due_date_after_enrollment']!==NULL? date('m/d/Y',  strtotime($course_data['due_date_after_enrollment'])):NULL; // the due date of the specified course
            $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); //  The subscription ID
            $videoCount = count($course_videos);
            $quizCount = count($course_quizzes);
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
                        <td style ="text-align:center;padding:2px 5px 2px 5px;" id="videoCount"><?= $videoCount ?></td>
                      </tr>
                      <tr>
                        <td style ="padding:2px 5px 2px 5px;">Quizzes</td>
                        <td style ="text-align:center;padding:2px 5px 2px 5px;" id="quizCount"><?= $quizCount ?></td>
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
                        //$categories = array(); // Array of the name of the categories
                        foreach($master_modules as $key => $module)
                        {
                            /* 
                             * This populates the modules array.
                             */
                                    $new_module = new module( $module['ID'], $module['title'], $module['category']); // Make a new module.
                                    array_push($modules, $new_module); // Add the new module to the modules array.

                        }
                        //usort($categories, "category_sort"); // Sort the categories based on the function below.
                        /*  
                         * Display the category and display its modules
                         */
// d($modules,$exams,$handouts);
                        foreach($categories as $category)
                        {
                            $category_name = $category->name;// The category name. Replace Coma with spaces.
                            echo "<h3 class='library_topic'>$category_name</h3>";
                            /************************************************************************************************
                            * Print modules that are in the same category its in.
                            *************************************************************************************************/

                          foreach( $modules as $key => $module )
                          {
                            if ( $module->category == $category_name )
                            {
                                $video_active = 0; // variable to indicate whether module is currently in the portal course
                                $video_class = 'disabled'; // variable to indicate whther module is currently in the portal course
                                $module_id = $module->id; // The module ID 
                                echo '<li class="video_item" video_id="' .$module_id . '">';
                                    if(in_array($module->title, $course_videos_titles))
                                    {   
                                        $video_active = '1';
                                        $video_class = 'enabled';
                                    }
                                    $module_time = (isset($videos[$module->title])) ? $videos[$module->title]->secs/60 : DEFAULT_MODULE_VIDEO_LENGTH; // The module time, divided by 60 to convert them in minutes.
                                      if(isset($videos_in_course[$module_id]))
                                      {
                              // echo "Theres a vid";
                                    foreach($videos_in_course[$module_id] as $video){
                                            $vid_id = $video['ID'];
?>
                                    <input collection="add_remove_from_group" video_length="<?= $module_time ?>" org_id="<?= $org_id ?>" item_id="<?= $vid_id ?>" group_id="<?= $course_id ?>" assignment_id="<?= $module_id ?>" video_id="<?= $module_id ?>" id="chk_video_<?= $module_id ?>" name="chk_video_<?= $module_id ?>" type="checkbox" value="1" <?=($video_active)?' checked="checked"':'';?> /> 
                                    <label for="chk_video_<?= $module_id ?>">
                                        <span name="video_title" class="<?=$video_class?> video_title">
                                          <b>Video</b> - <span class="vtitle"><?= $module->title ?></span>
                                        </span>
                                    </label>
<?php
                                            }
                                        }
?>
                                    <div video_id=<?= $module_id ?> class="<?=$video_class?> item" <?=(!$video_active)?' org_id=" <?= $org_id ?>" style="display:none"':'';?> >
                                    
<?php
                                       
                                        /* 
                                         * Check if there is a an exam for this module
                                         * The exam checkbox input will not be shown, if there are no exam uploaded in LU.
                                         * Find the ID of this exam in the exams array.
                                         */
                                        if(isset($exams[$module_id]))
                                        {
                                            foreach($exams[$module_id] as $exam)
                                            {
                                                    $exam_id = $exam['ID']; 
                                           
?>
                                            <input item="quiz" quiz_length="<?= DEFAULT_QUIZ_LENGTH ?>" video_id="<?= $module_id ?>" assignment_id="<?= $module_id ?>" group_id="<?= $course_id ?>" <?= $exam_id ? ' item_id="' . $exam_id . '" name="chk_defaultquiz_'.$exam_id.'" id="chk_defaultquiz_' .$exam_id . ' "':'';?> type="checkbox"   group_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" <?= in_array($exam['name'], $course_quizzes_titles) ? ' checked="checked"':''; $exam_id = 0; // Reset Exam ID?> /> 
                                            <label for="chk_defaultquiz_<?= $module_id ?>">
                                              <i>Exam</i> (<?= $exam['name'] ?>) 
                                            </label><br>
<?php
                                         }
                                        }
                                        /* 
                                         * Check if there is a handout for this module
                                         * The resource checkbox input will not be shown, if there are no resources.
                                         * Find the ID of this exam in the handouts array.
                                         */
                                        if(isset($handouts[$module_id]))
                                        {
                                            foreach($handouts[$module_id] as $handout){
                                                    $handout_id = $handout['ID']; 
                                           
?>
                                            <input item="resource" quiz_length="<?= DEFAULT_QUIZ_LENGTH ?>" assignment_id="<?= $module_id ?>" video_id="<?= $module_id ?>" group_id="<?= $course_id ?>" <?= $handout_id ? ' item_id="' . $handout_id . '" name="chk_defaultresource_'.$handout_id.'" id="chk_defaultresource_' .$handout_id . ' "':'';?> type="checkbox"   assignment_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" <?= in_array($handout['ID'], $course_handouts_ids) ? ' checked="checked"':''; $handout_id = 0; // Reset Exam ID?> /> 
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
                                unset( $modules[$key] ); // remove this module in the modules array
                            }

                          } // End of Modules foreach
                            
                        } // End of Category foreach
                        
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
                            if(!in_array($module['ID'], $master_module_ids)) 
                            {
                                // check if the module is in this specific course. if it is, then enable it, otherwise its default disabled.
                                if(in_array($module['ID'], $course_handouts_module_ids))
                                {
                                    $module_active = '1';
                                    $module_class = 'enabled';
                                }

                                  // show the input checkbox as ususal
?>
                                  <li class="video_item" video_id="<?= $module['ID'] ?>" >
                                  <input collection="add_remove_from_group" item="module" org_id=" <?= $org_id ?>" group_id=<?= $course_id ?> video_length="<?= DEFAULT_MODULE_VIDEO_LENGTH ?>" assignment_id="<?= $course_id ?>" video_id="<?= $module['ID'] ?>" item_id="<?= $module['ID']?>" id="chk_module_<?= $module['ID'] ?>" name="chk_module_<?= $module['ID'] ?>" type="checkbox" value="1" <?=($module_active)?' checked="checked"':'';?> /> 
                                  <label for="chk_module_<?= $module['ID'] ?>">
                                  <span name="video_title" class="<?=$module_class?> video_title">
<?php                                  
                                
?>


                                  <span class="vtitle"><?= $module['title'] ?></span>
                                  </span><br>
<?php
                                if(isset($exams[$module['ID']]))
                                {
                                                    foreach($exams[$module['ID']] as $exam)
                                                    {
                                                            $exam_id = $exam['ID']; 
                                                   
?>
<!--                                                    <input item="quiz" quiz_length="<?= DEFAULT_QUIZ_LENGTH ?>" group_id="<?= $course_id ?>" <?= $exam_id ? ' item_id="' . $exam_id . '" name="chk_defaultquiz_'.$exam_id.'" id="chk_defaultquiz_' .$exam_id . ' "':'';?> type="checkbox"   assignment_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" <?= in_array($exam['name'], $course_quizzes_titles) ? ' checked="checked"':''; $exam_id = 0; // Reset Exam ID?> /> -->
                                                    <label for="chk_defaultquiz_<?= $module_id ?>">
                                                      <i>Exam</i> (<?= $exam['name'] ?>) 
                                                    </label><br>
<?php
                                                    }
                                }
                                                                        /* 
                                         * Check if there is a handout for this module
                                         * The resource checkbox input will not be shown, if there are no resources.
                                         * Find the ID of this exam in the handouts array.
                                         */
                                        if(isset($handouts[$module['ID']]))
                                        {
                                            foreach($handouts[$module['ID']] as $handout){
                                                    $handout_id = $handout['ID'];
                                                    
                                           
?>
<!--                                            <input item="resource" quiz_length="<?= DEFAULT_QUIZ_LENGTH ?>" assignment_id="<?= $module['ID'] ?>" video_id="<?= $module['ID'] ?>" group_id="<?= $course_id ?>" <?= $handout_id ? ' item_id="' . $handout_id . '" name="chk_defaultresource_'.$handout_id.'" id="chk_defaultresource_' .$handout_id . ' "':'';?> type="checkbox"   assignment_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" <?= in_array($handout_id, $course_handouts_ids) ? ' checked="checked"':''; //$handout_id = 0; // Reset Exam ID?> /> -->
                                            <label for="chk_defaultresource_<?= $handout_id ?>">
                                              <i>Resource</i> (<?=$handout['name'] ?>) 
                                            </label><br>
<?php
                                            }
                                        }                 
?>
                                </label>
                                </li>
<?php
                            }//end if(!in_array)
//                        }
                    }//end for each
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
            $staff_id = filter_var($_REQUEST['staff_id'], FILTER_SANITIZE_NUMBER_INT);
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
            $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
            $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
            $data = array( "org_id" => $org_id );
            $courses_in_portal = getCoursesById($org_id,$subscription_id); // get all the published courses in the portal
            $course_id = 0; // The course ID
            if(isset($_REQUEST['group_id']))
            {
                $course_id = filter_var($_REQUEST['group_id'], FILTER_SANITIZE_STRING);
            }
            if(org_has_maxed_staff($org_id, $subscription_id) ){
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
                                    if($course['ID'] == $course_id)
                                    {
                                        echo "<option name='course_id' value='" . $course['ID'] . "' selected>" . $course['course_name'] . "</option>";
                                    }
                                    else
                                    {
                                        echo "<option name='course_id' value='" . $course['ID'] . "'>" . $course['course_name'] . "</option>";
                                    }
                                }
                                // There's no course to be selected.
                                else
                                {
                                    echo "<option name='course_id' value='" . $course['ID'] . "'>" . $course['course_name'] . "</option>";
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
                        <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>" />
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
            $name = filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING);
            $password = $_REQUEST['password'];    
            $email = sanitize_email( $_REQUEST['email'] );
            $target = filter_var($_REQUEST['target'], FILTER_SANITIZE_STRING);
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
            $course_id = filter_var($_REQUEST['group_id'], FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['group_name'], FILTER_SANITIZE_STRING);
            $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT); 
            $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); 
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
                  $user['first_name'] = get_user_meta ( $user_info->ID, "first_name", true);
                  $user['last_name'] = get_user_meta ( $user_info->ID, "last_name", true);
                  $user['email'] = $user_info->user_email;
                  $user['ID'] = $user_info->ID;
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
                                              $user_id = $user['ID']; // Learner's user ID
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
                                                        <a selected=1 class="add_remove_btn" collection="add_remove_from_group" group_id="<?= $course_id ?>" email="<?= $email ?>" status="add" org_id="<?= $org_id ?>" subscription_id="<?= $subscription_id ?>" course_name="<?= $course_name ?>" nonce="<?= $nonce ?>" user_id="<?= $user_id ?>">
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
                $html = ob_get_clean();
                echo $html;
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
            $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'], FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'], FILTER_SANITIZE_STRING);
            $status = filter_var($_REQUEST['status'], FILTER_SANITIZE_STRING);
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
            $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
            $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);

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
            $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'], FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'], FILTER_SANITIZE_STRING);
            $status = filter_var($_REQUEST['status'], FILTER_SANITIZE_STRING);
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
            $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
            $staff_id = filter_var($_REQUEST['staff_id'], FILTER_SANITIZE_NUMBER_INT);
            $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
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
                      <input type="hidden" name="subscription_id" id="subscription_id" value=" <?= $subscription_id ?>" />
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
            $question_number = filter_var($_REQUEST['question_number'], FILTER_SANITIZE_NUMBER_INT); //question id
            $answer_number = filter_var($_REQUEST['answer_number'], FILTER_SANITIZE_NUMBER_INT);     //answer number
            $library_id = filter_var($_REQUEST['library_id'], FILTER_SANITIZE_NUMBER_INT);           //library id
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
            $question_number = filter_var($_REQUEST['question_number'], FILTER_SANITIZE_NUMBER_INT); //question id
            $library_id = filter_var($_REQUEST['library_id'], FILTER_SANITIZE_NUMBER_INT);           //library id
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
            $library_id = filter_var($_REQUEST['library_id'], FILTER_SANITIZE_NUMBER_INT);           //library id
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

/**
 * Get enrolled users in a specific course
 * @param int $user_id - the user ID
 * @return array of enrollements for this user or NULL if none exist
 */
function getEnrollmentsByUserId($user_id = 0, $status = "all")
{
    global $wpdb;
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT * FROM " . TABLE_ENROLLMENTS . " WHERE user_id = $user_id";

    // Modify SQL Query for searching by status.
    if($status == "not_started")
    {
      $sql .= " WHERE status == 'not_started'";
    }
    // Get the enrollments who are enrolled in the course.
    $enrollments = $wpdb->get_results($sql, ARRAY_A);

    if($enrollments && count($enrollments) > 0)
    {
      return $enrollments;
    }
    return NULL;
}

/**
 * Get course modules by the course id
 * @param type $course_id - the course ID
 * @return array of modules
 * 
 */
function getCourseModules($course_id = 0) 
{
    global $wpdb;
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    $modules = $wpdb->get_results("SELECT DISTINCT course_id, module_id FROM " . TABLE_COURSE_MODULE_RESOURCES . " WHERE course_id = $course_id" , ARRAY_A);
    return $modules;
}

/**
 * Get the track records
 * @param int $user_id - WP User ID
 * @param in $video_id - The Video ID
 * @param string $type - Type of track
 * 
 */
function getTrack($user_id = 0, $video_id = 0, $type = "all")
{
  // Check if the user ID is valid.  
  if($user_id <= 0)
  {
    return false;
  }

  $type = filter_var($type, FILTER_SANITIZE_STRING); // Type of track
  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
  $video_id = filter_var($video_id, FILTER_SANITIZE_NUMBER_INT);

  
  global $wpdb;
  $sql = "SELECT * FROM " . TABLE_TRACK . " WHERE user_id = $user_id";
  if($video_id > 0)
  {
    $sql .= " AND video_id = $video_id";
  }

  $types = array('download_resource','video_started','watch_video','watch_custom_video','watch_slide','login','failed_login','failed_coupon','massmail','download_video','generate_report','delete_subscription','quiz_taken','certificate_conferred'); // Types of track.
  // Check if the type is a valid option.
  if( in_array($type, $types)  )
  {
    $sql .= " AND type = '$type'";
  }
  // Watch video needed a key value pair array. It has its own sql statement.
  if( $type == "watch_video" || $type == "video_started" )
  {
    $sql = "SELECT video_id, t.* FROM " . TABLE_TRACK . " as t WHERE user_id = $user_id and type = '$type'";
    $results = ($video_id > 0) ? (array) $wpdb->get_row($sql, OBJECT) : $wpdb->get_results($sql, OBJECT_K);
  }
  else
  {
    $results = ($video_id > 0) ? (array) $wpdb->get_row($sql, OBJECT) : $wpdb->get_results($sql);
  }
  return $results;
}

add_action('wp_ajax_updateVideoProgress', 'updateVideoProgress_callback');
/*
 * Update the video progress.
 */
function updateVideoProgress_callback()
{
  $user_id = isset($_REQUEST['user_id']) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT) : 0; // WP User ID
  $module_id = isset($_REQUEST['module_id']) ? filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT) : 0; // Module ID
  $video_id = isset($_REQUEST['video_id']) ? filter_var($_REQUEST['video_id'],FILTER_SANITIZE_NUMBER_INT) : 0; // Video ID
  $type = isset($_REQUEST['type']) ? filter_var($_REQUEST['type'], FILTER_SANITIZE_STRING) : 0;//the track type
  $sql = '';
  $query_result = 0; // defaults for variables in case the if statement below doesn't resolve.

  // Validate the user
  if($user_id == get_current_user_id())
  {
    global $wpdb;
    // Video just started.
    if( isset($_REQUEST['status']) && $_REQUEST['status'] == "started" )
    {
      $org_id = get_user_meta($user_id, "org_id", true);
//      $sql = "INSERT INTO " . TABLE_TRACK . " (type, user_id, org_id, date, video_id, module_id, video_time) VALUES ('watch_video', $user_id, $org_id, NOW(), $video_id, $module_id, 1)";
      $query_result = $wpdb->insert(
        TABLE_TRACK,
        array (
          'type' => $type,
          'user_id' => $user_id,
          'org_id' => $org_id,
          'date' => date('Y-m-d H:i:s'),
          'video_id' => $video_id,
          'module_id' => $module_id,
          'video_time' => '1'
        )
      );
    }
    else if( isset($_REQUEST['track_id']) && $_REQUEST['track_id'] )
    {
      $track_id = filter_var($_REQUEST['track_id'],FILTER_SANITIZE_NUMBER_INT); // Track ID
      // Update video time.
      if(isset($_REQUEST['status']) && $_REQUEST['status'] == "pause" && isset($_REQUEST['time']) && $_REQUEST['time'] > 0 )
      {
        $time = filter_var($_REQUEST['time'],FILTER_SANITIZE_STRING); // The time when the video stopped.
        //update the time for table video status.
//        $sql = "UPDATE " . TABLE_TRACK . " SET video_time=\"" . $time . "\" WHERE ID = $track_id AND user_id = $user_id";
        $query_result = $wpdb->update(
          TABLE_TRACK,
          array (
            'video_time' => $time
          ),
          array (
            'ID' => $track_id,
            'user_id' => $user_id
          )
        );
      }
      // Update video result 1. Indicating the video has been finished watching.
      else if( isset($_REQUEST['status']) && $_REQUEST['status'] == "finish" )
      {
        //update the time for table video status.
//        $sql = "UPDATE " . TABLE_TRACK . " SET result=1 WHERE ID = $track_id AND user_id = $user_id";
        $query_result = $wpdb->update(
          TABLE_TRACK,
          array (
            'result' => '1',
            'repeat' => 0
          ),
          array (
            'ID' => $track_id,
            'user_id' => $user_id
          )
        );
      }
    }

    // New Record. Success.
//    $query_result = $wpdb->query ($wpdb->prepare ($sql));
    if( $query_result )
    {
      $result['data'] = 'success';
      $result['success'] = true;
      $result['track_id'] = $wpdb->insert_id;
    }
    else
    {
      $result['display_errors'] = true;
      $result['success'] = false;
      $result['errors'] = 'updateVideoProgress_callback error: Failed to update the database. Please contact the administrator.';
    }
  }
  else
  {
    //return an error message
    $result['display_errors'] = true;
    $result['success'] = false;
    $result['errors'] = "updateVideoProgress_callback error: You do not have the privilege to modify other user's video progress.";
  }
  echo json_encode( $result );
  wp_die();
}

/**
 * Calculate the percentage completion for a given course
 * @param int $user_id - the user id
 * @param int $course_id - the course id
 * returns an int between 0-100 representing the percentage completion 
 */
function calc_course_completion($user_id = 0, $course_id = 0)
{
  if (!$user_id || !$course_id)
    return 0;

  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

  // get quizzes in course
  $quizzes = getQuizzesInCourse($course_id);
  $num_quizzes = count($quizzes);
  if ($num_quizzes == 0)
    return 0; // cant divide by 0

  $quiz_ids = implode(',', array_column($quizzes, 'ID')); // a comma seperated list of quiz ids in this course

  // check how many quizzes the user passed
  global $wpdb;
  $query = "SELECT * FROM " . TABLE_QUIZ_ATTEMPTS . " WHERE quiz_id IN ($quiz_ids) AND user_id = $user_id AND passed = 1";
  $amount_passed = $wpdb->get_results($query, ARRAY_A);
  $quizzes_passed = array_column($amount_passed,'quiz_id');
  $uniques= array_count_values($quizzes_passed);
  $num_passed = count($uniques);
  // calculate %
  if ($num_passed == 0)
    return 0; 

  $percentage_complete = intval($num_passed/$num_quizzes*100);
  return $percentage_complete;
}

/**
 * check whether the current user is enrolled in the course ID.
 * @param int $course_id - the course id
 * return true if the current user is inrolled in this course
 */
function verify_student_access($course_id = 0)
{
  if (!$course_id)
    return false;

  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

  global $current_user, $wpdb;
  $user_id = $current_user->ID;

  $query = "SELECT ID FROM " . TABLE_ENROLLMENTS . " WHERE course_id = $course_id AND user_id = $user_id";
  //error_log($query);
  $enrolled = $wpdb->get_var($query);

  if($enrolled)
  {
    return true;
  }
  return false;
}

/**
 * check whether the module is in the specified course
 * @param int $module_id - the module id
 * @param int $course_id - the course id
 * return true if the module is in the course.
 */
function verify_module_in_course($module_id = 0, $course_id = 0, $type = '')
{
  if (!$course_id || !$module_id)
    return false;

  $module_id = filter_var($module_id, FILTER_SANITIZE_NUMBER_INT);
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

  global $wpdb;
  $query = "SELECT ID FROM " . TABLE_COURSE_MODULE_RESOURCES . " WHERE course_id = $course_id AND module_id = $module_id";
  $exists = $wpdb->get_var($query);

  if($exists)
  {
    return true;
  }
  return false;
}

/**
 * get all the subscription info for a given course
 * @param int $course_id - the course ID
 * return an array of all the subscription info for a given course
 */
function getSubscriptionByCourse($course_id = 0)
{
  if (!$course_id)
    return false;

  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

  global $wpdb;

  $query = "SELECT s.* FROM " . TABLE_SUBSCRIPTIONS . " s LEFT JOIN " . TABLE_COURSES . " c ON s.ID = c.subscription_id WHERE c.ID = $course_id";
  $subscription = $wpdb->get_row($query, ARRAY_A);
  return $subscription;
}

/**
 * get the resources in a specific module in a course
 * @param $course_id - the course ID
 * @param $module_id - the module ID
 * @param $type - the type of resource
 * @return array of resources in course
 */
function getResourcesInModuleInCourse($course_id = 0, $module_id = 0, $type = '')
{
    global $wpdb;
    // make sure there is a type or else return empty array
    if ($type == '' || !$course_id || !$module_id)
      return array();

    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    $module_id = filter_var($module_id, FILTER_SANITIZE_NUMBER_INT);

    switch($type){
        case 'exam':
            $table = TABLE_QUIZ;
            break;
        case 'video':
            $table = TABLE_VIDEOS;
            break;
        case 'doc':
            $table = TABLE_RESOURCES;
            break;
    }
    $sql = "SELECT r.* "
                . "FROM " . $table . " AS r "
                . "LEFT JOIN " . TABLE_COURSE_MODULE_RESOURCES . " AS cmr ON cmr.resource_id = r.ID "
                . "WHERE cmr.course_id = $course_id AND cmr.type = '$type' AND cmr.module_id = $module_id";
    $course_module_resources = $wpdb->get_results($sql, ARRAY_A);
    return $course_module_resources;
}

/**
 * Get enrollement status by enrollment ID or by course/user id
 * @param int $enrollment_id - the enrollment ID
 * @param int $user_id - the user ID
 * @param int $course_id - the course ID
 * @return string of the enrollement status or NULL if none exist
 */
function getEnrollmentStatus($enrollment_id = 0, $user_id = 0, $course_id = 0)
{
    global $wpdb;
    $enrollment_id = filter_var($enrollment_id, FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);

    if($enrollment_id)
    {
      $sql = "SELECT status FROM " . TABLE_ENROLLMENTS . " WHERE ID = $enrollment_id";
    }
    elseif ($user_id && $course_id)
    {
      $sql = "SELECT status FROM " . TABLE_ENROLLMENTS . " WHERE course_id = $course_id AND user_id = $user_id";
    }
    else
    {
      return NULL;
    }

    // Get the enrollments who are enrolled in the course.
    $enrollment_status = $wpdb->get_var($sql);

    if($enrollment_status)
    {
      return $enrollment_status;
    }
    return NULL;
}

/**
 * update the enrollment status
 * @param int $enrollment_id - the enrollment ID
 * @return true if successful, false otherwise
 */
add_action('wp_ajax_updateEnrollmentStatus', 'updateEnrollmentStatus_callback');
function updateEnrollmentStatus_callback()
{
    global $wpdb;
    $enrollment_id = isset($_REQUEST['enrollment_id']) ? filter_var($_REQUEST['enrollment_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $status = isset($_REQUEST['status']) ? filter_var($_REQUEST['status'], FILTER_SANITIZE_STRING) : '';

    if($enrollment_id && $status != '')
    {
        $update_status = $wpdb->update(
        TABLE_ENROLLMENTS,
        array (
          'status' => $status
        ),
        array (
          'ID' => $enrollment_id
        )
      );
    } 
    wp_die();
}

/**
 *  Get all the enrollments by course ID / user ID / org ID
 *  @param int $course_ID - the course ID
 *  @param int $user_id - the user ID
 *  @param int $org_id - the organization ID
 *  @param string $status - the status
 *  @return array of enrollments
 */
function getEnrollments($course_id = 0, $user_id = 0, $org_id = 0, $status = '') 
{
  global $wpdb;
  $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
  $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
  $status = filter_var($status, FILTER_SANITIZE_STRING);

  // make sure we have at least 1 paramater
  if (!$course_id && !$user_id && !$org_id) 
  {
    return array('status' => 0, 'message' => "ERROR in getEnrollments: Invalid course id, user id or org id");
  }

  // build the SQL statement
  $sql = "SELECT * FROM " . TABLE_ENROLLMENTS . " e ";

  // make sure were only looking for student user types
  $sql .= "LEFT JOIN " . TABLE_USERMETA . " um ON e.user_id = um.user_id ";
  $sql .= "WHERE um.meta_key = 'wp_capabilities' AND um.meta_value LIKE '%student%' ";

  if ($course_id > 0)
  {
    $sql .= "AND e.course_id = $course_id ";
  }
  
  if ($user_id > 0 && $course_id > 0)
  {
    $sql .= "AND e.user_id = $user_id ";
  }
  else if ($user_id > 0 )
  {
    $sql .= "AND e.user_id = $user_id ";
  }

  if (($user_id > 0 || $course_id > 0) && $org_id > 0)
  {
    $sql .= "AND e.org_id = $org_id ";
  }
  else if ($org_id > 0)
  {
    $sql .= "AND e.org_id = $org_id ";
  }

  if ($status != '')
  {
    $sql .= "AND e.status = '$status' ";
  }

  $enrollments = $wpdb->get_results($sql, ARRAY_A);
  return $enrollments;
}

/******************************************
 * Get certificates by user ids
 * @param array $user_ids - Lists of WP user ID.
 * @param string $type - Type of certificate. (Image or Syllabus.)
 * @param date $start_date - the date to start the search from
 * @param date $end_date - the date to end the search till
 * returns an array of certificates or empty array
 ******************************************/ 
function getCertificatesByUserIds( $user_ids = array(), $type = 'image', $start_date = '0000-00-00', $end_date = '0000-00-00' )
{
  global $wpdb;
  $type = filter_var($type, FILTER_SANITIZE_STRING);

  // make sure we have some users to look for
  if( count($user_ids) == 0 )
  {
    return array();
  }

  $list_user_ids = implode(', ', $user_ids);

  // depending on the type you're looking for select the cert or syllabus table
  switch( $type )
  {
    case "image":
      $table = TABLE_CERTIFICATES;
      break;
    case "syllabus":
      $table = TABLE_CERTIFICATES_SYLLABUS;
      break;
  }
  $sql = "SELECT * FROM " . $table . " WHERE user_id IN ($list_user_ids)";
  if( $start_date != "0000-00-00" && $end_date != "0000-00-00")
  {
    $sql .= "  AND date_created >= '" . $start_date . "' AND date_created <= '" . $end_date . "'";
  }
  $result = $wpdb->get_results ($sql, ARRAY_A);
  return $result;
}

/******************************************
 * Verify the certificate by the user id and org id.
 * 1 Scenario: If the student no longer registered to the camp and requested a copy from a director. We need to validate this request from the certificate table.
 * @param int $student_user_id - Student WP ID
 * @param int $director_org_id - Director org ID.
 ******************************************/ 
function verifyCertificate($student_user_id = 0, $director_org_id = 0)
{
  if( !$user_id || !$org_id )
  {
    return false;
  }
  global $wpdb;
  $student_user_id = filter_var($student_user_id, FILTER_SANITIZE_NUMBER_INT);
  $director_org_id = filter_var($director_org_id, FILTER_SANITIZE_NUMBER_INT);

  $sql = "SELECT * FROM " . TABLE_CERTIFICATES . " WHERE user_id = $student_user_id AND org_id = $director_org_id";
  $results = $wpdb->get_row ($sql);
  return $results;
}

add_action('wp_ajax_acceptTerms', 'acceptTerms_callback');
// Updates the user field "accepted_terms" and gives a redirect location to view tutorial
function acceptTerms_callback()
{ 
  // check if user has student/director permissions
  if( !current_user_can ('is_director') && !current_user_can ('is_student') )
  {
    $status = array('status' => 0,
                    'data' => 'failed', 
                    'message' => 'Error: Sorry, you do not have permisison to view this page.');
  }
  else
  {
    // Update the accepted terms of the user.
    global $current_user;
    $user_id = $current_user->ID;
    $response = update_user_meta($user_id, "accepted_terms", 1);
    $location = get_home_url() . "/dashboard/?tutorial=1";
    $status = array('status' => 1,
                    'response' => $response, 
                    'location' => $location);
  }
  echo json_encode($status);
  wp_die();
}

/*for students only
 * get subscription id from enrollments
 * @param: $user_id - the user ID
 */
function getSubscriptionIdByUser($user_id = 0)
{
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $enrollment = $wpdb->get_row("SELECT * FROM ". TABLE_ENROLLMENTS ." WHERE user_id = $user_id",ARRAY_A);
    //error_log("SELECT * FROM ". TABLE_ENROLLMENTS ." WHERE user_id = $user_id AND subscription_id != NULL");
    return $enrollment['subscription_id'];
}


/**
 * stats function to calculate videos watched
 * @param $org_id - ID of the org
 */
function calculate_videos_watched($org_id = 0)
{
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $num_videos_watched = $wpdb->get_row("SELECT COUNT(ID) as count FROM ". TABLE_TRACK." WHERE org_id = $org_id AND result = 1 AND type = 'watch_video'", ARRAY_A);
    return $num_videos_watched['count'];
}


/**
 * stats function calculate number of quizzes watched
 * @param $org_id - ID of the org
 */
function calculate_quizzes_taken($org_id = 0, $subscription_id = 0)
{
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    $subscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);
    if ($org_id == 0 || $subscription_id == 0) 
    {
        return 0;
    }
    global $wpdb;
    $quizzes_in_course = $wpdb->get_results("SELECT DISTINCT cmr.resource_id,c.* FROM ".TABLE_COURSE_MODULE_RESOURCES." as cmr LEFT JOIN ". TABLE_COURSES . " as c ON c.ID = cmr.course_id WHERE c.org_id = $org_id AND c.subscription_id = $subscription_id AND cmr.type = 'exam'",ARRAY_A);
    $quizzes_ids = array_column($quizzes_in_course, 'resource_id');
    $quiz_ids_string = implode(',',$quizzes_ids);
    $users_in_org = getEotUsers($org_id);
    $users_in_org = isset($users_in_org['users']) ? $users_in_org['users'] : array();
    if(count($users_in_org) == 0)
    {
      return 0;
    }
    $user_ids = array_column($users_in_org, 'ID');
    $user_ids_string = implode(',',$user_ids);
    $attempts = $wpdb->get_row("SELECT COUNT(ID) as count FROM ". TABLE_QUIZ_ATTEMPTS ." WHERE quiz_id IN(".$quiz_ids_string.") AND  user_id IN(".$user_ids_string.")",ARRAY_A);
    return $attempts['count'];
}

/**
 * stats function calculate the number of staff logged in
 * @param $org_id - the org ID
 */
function calculate_logged_in($org_id = 0)
{
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    if ($org_id == 0) 
    {
        return 0;
    }
    global $wpdb;
    $num_logged_in = $wpdb->get_row("SELECT COUNT(DISTINCT user_id) as count FROM ". TABLE_TRACK ." WHERE org_id = $org_id and type = 'login'",ARRAY_A);
    return $num_logged_in['count'];
}

/**
 * get custom video from resources table
 * @param $video_id - the ID of the video resource
 */
function get_custom_video($video_id = 0)
{
    $video_id = filter_var($video_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $custom_video = $wpdb->get_row("SELECT * FROM ". TABLE_RESOURCES . " WHERE ID = $video_id",ARRAY_A);
    return $custom_video;
}

/**
 * stats function calculate_resources_downloaded
 * @param $org_id - the org ID
 */
function calculate_resources_downloaded($org_id = 0)
{
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    if ($org_id == 0) 
    {
        return 0;
    }
    global $wpdb;
    $num_downloaded = $wpdb->get_row("SELECT COUNT(user_id) as count FROM ". TABLE_TRACK ." WHERE org_id = $org_id and type = 'download_resource'",ARRAY_A);
    return $num_downloaded['count'];
}

/**
 * stats function get all track for an org
 * @param - $org_id //the ID of the org
 */
function getAllTrack($org_id = 0)
{
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    if ($org_id == 0) 
    {
        return array();
    }
    global $wpdb;
    $allTrack = $wpdb->get_results("SELECT * FROM ". TABLE_TRACK ." WHERE org_id = $org_id", ARRAY_A);
    return $allTrack;
}

/**
 * stats function get quiz attempts
 * @param type $course_id - the course ID
 * @param type $user_id - the ID of the user
 */
function getAllQuizAttempts($course_id = 0, $user_id = 0)
{
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    if ($course_id == 0) 
    {
        return array();
    }
    global $wpdb;
    $quizzes = getQuizzesInCourse($course_id);
    if(empty($quizzes))
    {
        return array();
    }
    $quiz_ids = array_column($quizzes, 'ID');
    $quiz_ids_string = implode(',', $quiz_ids);
    $sql = "SELECT DISTINCT(quiz_id), ID, user_id, passed, completed, score, date_attempted ";
    $sql.= "FROM ". TABLE_QUIZ_ATTEMPTS . " ";
    $sql.= "WHERE quiz_id IN(".$quiz_ids_string.") ";
    $sql.= "AND course_id = $course_id ";
    $sql.= "AND date_attempted BETWEEN '". SUBSCRIPTION_START ."' AND '". SUBSCRIPTION_END ."'";
    if($user_id>0)
    {
        $sql.= " AND user_id = $user_id";
    }
    $attempts = $wpdb->get_results($sql, ARRAY_A);
    return $attempts;
}

/**
 * stats function get question results
 * @param: $question_ids - comma seperated question IDs
 */
function getQuestionResults($question_ids = 0,$user_ids = 0, $quiz_id = 0)
{
    if($user_ids == 0 || $user_ids == "")
    {
        return array();
    }
    $quiz_id = filter_var($quiz_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $results = $wpdb->get_results("SELECT qr.* FROM ". TABLE_QUIZ_QUESTION_RESULT." qr WHERE qr.quiz_id = $quiz_id AND qr.question_id IN (".$question_ids.") AND qr.user_id IN (".$user_ids.")", ARRAY_A);
    return $results;
}

/*
 * get video by id
 * @param - video_id - the ID of the video
 * $param - custom - whether it is a custom video
 */
function getVideoById($video_id = 0, $custom = false)
{
    $video_id = filter_var($video_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    if($custom)
    {
        $video = $wpdb->get_row("SELECT * FROM ". TABLE_RESOURCES. " WHERE ID = $video_id AND type = 'custom_video'", ARRAY_A);
    }
    else 
    {
        $video = $wpdb->get_row("SELECT * FROM ". TABLE_VIDEOS. " WHERE ID = $video_id", ARRAY_A);
    }
    
    return $video;
}

/**
 * stats function get video stats
 * @param type $video_id - the ID of the video
 * @param type $org_id - the ID of the org
 * @param type $custom - boolean indicating if its a custom video
 */
function getVideoStats($video_id = 0, $org_id = 0, $custom = false)
{
    $video_id = filter_var($video_id, FILTER_SANITIZE_NUMBER_INT);
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $sql="SELECT u.display_name, t.* FROM ";
    $sql.= TABLE_USERS ." as u LEFT JOIN ";
    $sql.=TABLE_TRACK." as t ON t.user_id = u.ID ";
    $sql.= "WHERE t.org_id = $org_id ";
    $sql.= "AND t.video_id = $video_id ";
    if($custom)
    {
        $sql.= "AND t.type = 'watch_custom_video' ";
    }
    else 
    {
        $sql.= "AND t.type = 'watch_video' ";
    }
    $sql.= "AND t.result = 1";
    $stats = $wpdb->get_results($sql,ARRAY_A);

    return $stats;
}

/**
 * get resource by id
 * @param type $resource_id - the ID of the resource
 * 
 */
function getResourceById($resource_id = 0)
{
    $resource_id = filter_var($resource_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $resource = $wpdb->get_row("SELECT * FROM ". TABLE_RESOURCES . " WHERE ID = $resource_id", ARRAY_A);
    return $resource;
}

/**
 * stats function get video stats
 * @param type $video_id - the ID of the video
 * @param type $org_id - the ID of the org
 */
function getResourceStats($resource_id = 0, $org_id = 0)
{
    $resource_id = filter_var($resource_id, FILTER_SANITIZE_NUMBER_INT);
    $org_id = filter_var($org_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $stats = $wpdb->get_results("SELECT u.display_name, t.* FROM "
            . TABLE_USERS ." as u LEFT JOIN "
            .TABLE_TRACK." as t ON t.user_id = u.ID "
            . "WHERE t.org_id = $org_id "
            . "AND t.resource_id = $resource_id "
            . "AND t.type = 'download_resource'",ARRAY_A);
    return $stats;
}

/**
 * track function get date when resource was viewed
 * @param type $user_id
 * @param type $resource_id
 * 
 */
function trackResource($user_id = 0, $resource_id =0)
{
    $resource_id = filter_var($resource_id, FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    $result = $wpdb->get_row("SELECT * FROM ". TABLE_TRACK . " WHERE user_id = $user_id AND resource_id = $resource_id", ARRAY_A);
    return $result;
}

/*
 * stats function get quiz results for an individual
 * @param $attempt_id - the attempt ID of the quiz attempt
 */
function getQuizResults($attempt_id = 0)
{
    $attempt_id = filter_var($attempt_id, FILTER_SANITIZE_NUMBER_INT);
    if($attempt_id == 0)
    {
        return NULL;
    }
    global $wpdb;
    $results = $wpdb->get_results("SELECT qr.*,qa.answer_correct,qqr.answer_correct as question_correct "
            . "FROM ". TABLE_QUIZ_RESULT ." qr "
            . "LEFT JOIN ".TABLE_QUIZ_ANSWER." qa ON qr.answer_id = qa.ID "
            . "LEFT JOIN ".TABLE_QUIZ_QUESTION_RESULT." qqr ON qr.attempt_id = qqr.attempt_id AND qr.question_id = qqr.question_id "
            . "WHERE qr.attempt_id = $attempt_id",ARRAY_A);
    return $results;
}

/**
 *   Display Videos In Statistics
 */  
add_action('wp_ajax_get_video_form', 'get_video_form_callback');
function get_video_form_callback()
{
    $video_id = filter_var($_REQUEST['video_id'], FILTER_SANITIZE_NUMBER_INT);
    $custom = filter_var($_REQUEST['custom'], FILTER_SANITIZE_NUMBER_INT);
    if($custom == 1){
        $video = get_custom_video($video_id);
        $video_file = $video['url'];
    }
    else 
    {
       $video = getVideoById($video_id);
       $video_file = "https://eot-output.s3.amazonaws.com/".$video['shortname'].".mp4";
    }
    //d($video);
    $title = $video['name'];
        ob_start();
?>
  <div id="watch_video">
            <div class="title" style="width:665px">
            <div class="title_h2"><?= $title;?></div>
        </div>
      

                <div id='player' style='width:665px;height:388px'>
                    <video id="my-video" class="video-js vjs-default-skin" controls preload="auto" width="665" height="388" poster="<?php echo bloginfo('template_directory'); ?>/images/eot_logo.png" data-setup='{"controls": true}'>

                        <source src="<?= $video_file?>" type='video/mp4'>
                            <p class="vjs-no-js">
        	                    To view this video please enable JavaScript, and consider upgrading to a web browser that
            	                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                            </p>        

                    </video>
                </div>

        
             
        <div class="popup_footer">
            <div class="buttons">
                <a onclick="videojs('my-video').dispose();jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/cross.png" alt="Close"/>
                    Close
                </a>
            </div>
        </div>
  </div>
<?php
        $html = ob_get_clean();
        echo $html;
    wp_die();
}

/**
 * 
 * @param type $quiz_ids_string - the quiz IDs
 * 
 */
function getPassedQuizzes($quiz_ids_string,$user_id = 0)
{
    global $wpdb;
    $sql = "SELECT * FROM ".TABLE_QUIZ_ATTEMPTS. " WHERE quiz_id IN($quiz_ids_string) AND user_id = $user_id ";
    $sql.= "AND date_attempted BETWEEN '". SUBSCRIPTION_START ."' AND '". SUBSCRIPTION_END ."' AND passed = 1";
    $passed_quizzes = $wpdb->get_results($sql, ARRAY_A);
    return $passed_quizzes;
}

/**
 * 
 * @param type $quiz_id - the ID of the quiz
 * @param type $user_id - the ID of the user
 * 
 */
function getQuizAttempts($quiz_id = 0, $user_id = 0)
{
    
    global $wpdb;
    $quiz_id = filter_var($quiz_id, FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    if($quiz_id == 0 || $user_id == 0)
    {
        return NULL;
    }
    $attempts = $wpdb->get_results("SELECT * FROM ". TABLE_QUIZ_ATTEMPTS ." WHERE quiz_id = $quiz_id AND user_id = $user_id", ARRAY_A);
    return $attempts;
}

/**
 * 
 * @param type $user_id - the user ID
 * 
 */
function calculate_progress($user_id = 0, $course_id = 0)
{
    global $wpdb;
    $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    if($user_id == 0 || $course_id == 0)
    {
        return 0;
    }
    $quizzes_in_course = getQuizzesInCourse($course_id);

    $passed = 0;
    foreach ($quizzes_in_course as $required) 
    {
        $iPassed = $wpdb->get_row("SELECT passed FROM ".TABLE_QUIZ_ATTEMPTS. " WHERE quiz_id = ".$required['ID']." AND user_id = $user_id AND passed = 1", ARRAY_A);
        if($iPassed)
        {
            $passed++;
        }
    }
    $percentage = $passed/count($quizzes_in_course)*100;
    return $percentage;
}

/**
 * 
 * @param type $quiz_id - the quiz ID
 * @param type $course_id - the course ID
 * 
 */
function verifyQuizInCourse($quiz_id = 0 , $course_id = 0)
{
    $quiz_id = filter_var($quiz_id, FILTER_SANITIZE_NUMBER_INT);
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    if($course_id == 0 || $quiz_id == 0)
    {
        return false;
    }
    $in_course = $wpdb->get_row("SELECT * FROM ". TABLE_COURSE_MODULE_RESOURCES ." WHERE resource_id = $quiz_id AND course_id = $course_id AND type = 'exam'", ARRAY_A);

    if($in_course)
    {
        return true;
    }
    else 
    {
       return false; 
    }
}

/**
 * get subscription id based on course id
 * @param type $course_id - the course ID
 * 
 */
function getSubscriptionFromCourse($course_id = 0)
{
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    if($course_id == 0)
    {
        return 0;
    }
    $course = $wpdb->get_row("SELECT * FROM ". TABLE_COURSES . " WHERE ID = $course_id", OBJECT);
    return isset($course->subscription_id) ? $course->subscription_id : 0;
}


/**
 * get library id from subscription
 * @param type $subscription_id
 * 
 */
function getLibraryFromSubscription($subscription_id)
{
    $subscription_id = filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT);
    global $wpdb;
    if($subscription_id == 0)
    {
        return 0;
    }
    $subscription = $wpdb->get_row("SELECT * FROM ". TABLE_SUBSCRIPTIONS . " WHERE ID = $subscription_id", OBJECT);
    return isset($subscription->library_id) ? $subscription->library_id : 0;
}


/**
 * get all the resources in a module
 * @param type $course_id
 * @param type $resource_id
 * @param type $type
 * 
 */
function getOtherResourcesInModule($course_id = 0, $resource_id = 0, $type = "")
{
    $course_id = filter_var($course_id, FILTER_SANITIZE_NUMBER_INT);
    $resource_id = filter_var($resource_id, FILTER_SANITIZE_NUMBER_INT);
    $type = filter_var($type, FILTER_SANITIZE_STRING);
    global $wpdb;
    if($course_id == 0 || $resource_id == 0 || $type == "")
    {
        return array();
    }
    
    // course module resources
    $cmr = $wpdb->get_row("SELECT * FROM ". TABLE_COURSE_MODULE_RESOURCES . " "
            . "WHERE course_id = $course_id "
            . "AND resource_id = $resource_id "
            . "AND type = '$type'", OBJECT);
    
    if (isset($cmr->module_id))
    {
      $other_resources = $wpdb->get_results("SELECT * FROM ". TABLE_COURSE_MODULE_RESOURCES ." WHERE course_id = $course_id AND module_id =".$cmr->module_id, ARRAY_A);
        
      return (!empty($other_resources)) ? $other_resources : array();

    }
        
    return array();
}

/********************************************************************************************************
 * Delete ORG ID
 *******************************************************************************************************/
add_action('wp_ajax_deleteStaffOrgId', 'deleteStaffOrgId_callback');
function deleteStaffOrgId_callback () 
{
    global $wpdb;
    if( isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['staff_id'] ) )
    {
        // This form is generated in getCourseForm function with $form_name = change_course_status_form from this file.
        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT); // The Org ID
        $staff_id = filter_var($_REQUEST['staff_id'], FILTER_SANITIZE_NUMBER_INT); // The staff account ID
        // Check permissions
        if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'delete-staff_Org_Id' . $org_id ) ) 
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'deleteStaffOrgId_callback error: Sorry, your nonce did not verify.';
        }
        else if( !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'deleteStaffOrgId_callback Error: Sorry, you do not have permisison to view this page.';
        }
        else
        {
//          $result = update_user_meta( $staff_id, "org_id", "");
          $deleted = delete_user_meta( $staff_id, "org_id", $org_id);
          $result['data'] = 'success';
          $result['user_id'] = $staff_id;
          $result['success'] = $deleted; // Return true if deleted
          $result['email'] = $email;
        }
    }
    else
    {
        $result['display_errors'] = 'failed';
        $result['success'] = false;
        $result['errors'] = 'deleteStaffOrgId_callback ERROR: Missing some parameters.';
    }
    echo json_encode($result);
    wp_die();
}

/**
 * Verify quiz belongs to current user, or current user is sales manager
 * @global type $current_user
 * @param type $quiz_id
 * @return boolean
 * 
 */
function verifyQuiz($quiz_id = 0)
{
    global $wpdb;
    $quiz_id = filter_var($quiz_id, FILTER_SANITIZE_NUMBER_INT);
    
    if($quiz_id == 0)
    {
        return false;
    }
    global $current_user;
    if(current_user_can("is_sales_manager"))
    {
        return true;
    }
    $quiz_user = $wpdb->get_var("SELECT user_id FROM ". TABLE_QUIZ. " WHERE ID = $quiz_id");
    if($quiz_user == $current_user->ID)
    {
        return true;
    }
    else 
    {
        return false;
    }
}

/**
 * Verify if a question is part of a quiz
 * @param type $quiz_id
 * @param type $question_id
 * 
 */
function verifyQuizQuestion($quiz_id = 0, $question_id = 0)
{
    global $wpdb;
    $quiz_id = filter_var($quiz_id, FILTER_SANITIZE_NUMBER_INT);
    $question_id = filter_var($question_id, FILTER_SANITIZE_NUMBER_INT);
    if($quiz_id == 0 || $question_id == 0)
    {
        return false;
    }
    $question_quiz_id = $wpdb->get_var("SELECT quiz_id FROM ". TABLE_QUIZ_QUESTION . " WHERE ID = $question_id");
    if($question_quiz_id == $quiz_id)
    {
        return true;
    }
    else 
     {
        return false;
    }
}
