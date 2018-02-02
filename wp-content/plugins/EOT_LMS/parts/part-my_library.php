<?php
if (isset($_REQUEST['course_id']) && $_REQUEST['course_id'] != "") 
{
    $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
    
    if (current_user_can("is_student")) 
    {
        global $current_user;
        $user_id         = $current_user->ID;
        $admin_ajax_url  = admin_url('admin-ajax.php');
        $subscription_id = getSubscriptionIdByUser($user_id);
        
        // verify this user has access to this course
        $has_access = verify_student_access($course_id);
        if (!$has_access) 
        {
            wp_die(__("You do not have access to this course", "EOT_LMS"));
        }
        
        // check course status - update in necessary
        $enrollment_id = isset($_REQUEST['enrollment_id']) ? filter_var($_REQUEST['enrollment_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
        $enrollment_status = getEnrollmentStatus($enrollment_id);
        //d($enrollment_status);
        if ($enrollment_status == 'not_started') 
        {
            // output the javascript to update enrollment status to in_progress
?>
 			<script type='text/javascript'>
      			$(document).ready(function() 
      			{			
					var url =  ajax_object.ajax_url + "?action=updateEnrollmentStatus&enrollment_id=<?= $enrollment_id ?>&status=in_progress";
					$.ajax({
						url:url
		            });
      			});
  			</script>	
<?php
        }
        $course_info = getCourse($course_id); // the course info, name, desc, etc...
        if ($course_info) 
        {
?>
			<div class="breadcrumb">
				<?= CRUMB_DASHBOARD ?>    
				<?= CRUMB_SEPARATOR ?>  
			  	<span class="current"><?= $course_info['course_name'] ?></span> 
			</div>
	      	<h1 class="article_page_title"></h1>
			<h2><?= __("Course", "EOT_LMS"); ?> "<u><?= $course_info['course_name'] ?></u>"</h2> 	
			<p><?= __("Here are the videos, exams and downloadable handouts available to you in this module.", "EOT_LMS"); ?></p>
<?php
            // Get all the modules in this course by the course ID
            $user_id           = get_current_user_id(); // WP User ID
            $org_id            = get_org_from_user($user_id);
            $modules_in_course = getModulesInCourse($course_id);
            $subscription      = getSubscriptionByCourse($course_id); // get the subscription data
            $library_id        = isset($subscription['library_id']) ? $subscription['library_id'] : 0; // the library ID
            $continue_learning = get_post_meta($org_id, 'continue_learning', true);
            
            if ($continue_learning && $enrollment_status == 'completed') //director has set users to continue learning and user has completed their enrollment
            {
                $modules_in_course = getModulesByLibrary($library_id);
                $resources_doc     = getResourcesInLibrary($library_id, "doc");
                $resources_video   = getResourcesInLibrary($library_id, "video");
                $resources_exam    = getResourcesInLibrary($library_id, "exam");
            } 
            else 
            {
                $modules_in_course = getModulesInCourse($course_id);
                $resources_doc     = getResourcesInCourse($course_id, "doc");
                $resources_video   = getResourcesInCourse($course_id, "video");
                $resources_exam    = getResourcesInCourse($course_id, "exam");
            }
            
            $finished_module_quizzes      = array();
        
            
            $exams = array();
            foreach ($resources_exam as $exam) 
            {
                if (isset($exams[$exam['mid']])) 
                {
                    array_push($exams[$exam['mid']], array(
                        'ID' => $exam['ID'],
                        'name' => $exam['name']
                    ));
                } 
                else 
                {
                    $exams[$exam['mid']] = array();
                    array_push($exams[$exam['mid']], array(
                        'ID' => $exam['ID'],
                        'name' => $exam['name']
                    ));
                }
            }
            
            $video_track          = getTrack($user_id, 0, "watch_video");

            $modules_in_portal_ids_string = array();
            // Display all the modules in the course. Including custom modules.
            if (isset($modules_in_course)) 
            {                
                foreach ($modules_in_course as $key => $module) 
                {
                    $module_id    = $module['ID']; // The module ID
                    $module_title = $module['title']; // The module name.
                    // Custom modules
                    if( $module['org_id'] > 0 )
                    {
                        $videos_custom_modules     = getVideoResourcesInModules($module['ID']);
                        $resources_custom_modules = getHandoutResourcesInModules($module['ID']);
                        $quizzes_custom_modules    = getQuizResourcesInModules($module['ID']);
                        
                        ?>
                        <ul class="tree">
                            <li class="tree_video">
                                <i class="fa fa-square-o" aria-hidden="true"></i>
                                &nbsp;<b><?= $module_title ?></b>
                        <?php
                        if( $resources_custom_modules )
                        {
                            foreach ($resources_custom_modules as $resource_module) 
                            {
                                $type = $resource_module['type'];
                                $name = $resource_module['name'];
                                if($type == 'link')
                                {
                                    $icon   = "fa-link";
                                    $action = __("Visit Url", "EOT_LMS");
                                    $url    = $resource_module['url'];
                                }
                                else if($type == 'doc')
                                {
                                    $icon   = "fa-sticky-note-o";
                                    $action = __("Download File", "EOT_LMS");
                                    $url    = "/download-file?module_id=$module_id&course_id=$course_id&resource_id=" . $resource_module['ID'];
                                }
                                else if($type == 'custom_video')
                                {
                                    $icon   = "fa-play";
                                    $action = __("Watch Video", "EOT_LMS");
                                    $url    = "?part=view_custom&course_id=$course_id&module_id=$module_id&video_id=" . $resource_module['ID'];
                                }
?>                          
                                        <ul class="inner nobullet">
                                            <li>
                                               <a href="<?= $url ?>"><i class="fa <?= $icon; ?>" aria-hidden="true"></i></a> <?= $name ?> - <span class="small"><a href="<?= $url ?>"><?= $action; ?></a></span>
                                            </li>
                                        </ul>
<?php
                            }
                        }
                        if($quizzes_custom_modules)
                        {
                            foreach ($quizzes_custom_modules as $quiz_module) 
                            {
                                $exam_id    = $quiz_module['ID'];
                                $exam_title = $quiz_module['name'];
                                $icon       = "fa-question-circle-o";
                                $url        = "?part=quiz&module_id=$module_id&quiz_id=$exam_id&subscription_id=$subscription_id&course_id=$course_id&enrollment_id=$enrollment_id";
?>
                                <ul class="inner nobullet">
                                    <li><a href="<?= $url ?>"><i class="fa <?= $icon; ?>" aria-hidden="true"></i></a> <?= $quiz_module['name'] ?> - <span class="small"><a href="<?= $url ?>"><?= __("Take Quiz", "EOT_LMS"); ?></a></span></li>
                                </ul>
<?php
                            }
                        }
                echo        '</li>';
                echo    '</ul>';

                        continue;
                    }
                    echo '<ul class="tree">';
?>
                    <li class="tree_video">
                        <a href="?part=view&course_id=<?= $course_id ?>&module_id=<?= $module_id ?>">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </a> 
                        <b><?= $module_title ?></b> 
                        <span class="small"> - 
<?php
                    if ($continue_learning && $enrollment_status == 'completed') 
                    {
?>
                        <a href="?part=view_continued&course_id=<?= $course_id ?>&module_id=<?= $module_id ?>&enrollment_id=<?= $enrollment_id ?>">
                            <?= __("Watch Video", "EOT_LMS"); ?>
                        </a>
<?php
                    } 
                    else 
                    {
?>
                        <a href="?part=view&course_id=<?= $course_id ?>&module_id=<?= $module_id ?>&enrollment_id=<?= $enrollment_id ?>">
                            <?= __("Watch Video", "EOT_LMS"); ?>
                        </a>
<?php
                    }
                    foreach ($resources_video as $key => $video) 
                    {
                        // Get the video ID
                        if ($video['module_id'] == $module_id) 
                        {
                            $video_id   = $video['ID'];
                            $track_key  = myKey($video_track, 'video_id', $video_id);
                            $isFinished = ($track_key && $video_track[$track_key]->result == 1 && $video_track[$track_key]->repeat == 0) ? true : false;
                            unset($resources_video[$key]); // Unset the key.
                        }
                    }
                    // display link to the quiz if the video has been watched.
                    if ($isFinished) 
                    {
                        if (isset($exams[$module_id])) 
                        {
                            $exam_data = $exams[$module_id];
                            $quiz_id   = $exam_data[0]['ID'];
                            echo '/ <a href="?part=quiz&module_id=' . $module_id . '&quiz_id=' . $quiz_id . '&subscription_id=' . $subscription_id . '&course_id=' . $course_id . '&enrollment_id=' . $enrollment_id . '">' . __("Take Quiz", "EOT_LMS") . '</a>';
                            array_push($finished_module_quizzes, $quiz_id); //store that the module for this quiz was completed
                            
                        }
                    } 
                    else 
                    {
                        if (isset($exams[$module_id])) 
                        {
?>                                                                                                  
                                / Take Quiz
                            &nbsp; <i class="fa fa-question-circle" title="<b><?= __("You must watch the video first (all the way through) before attempting the quiz.", "EOT_LMS"); ?></b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b><?= __("You must watch the video first (all the way through) before attempting the quiz.", "EOT_LMS"); ?></b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
<?php
                        }
                    }
                    echo '</span>';
                    if (isset($resources_doc) && count($resources_doc) > 1) 
                    {
                        foreach ($resources_doc as $key => $resource) 
                        {
                            if ($resource['module_id'] == $module['ID']) 
                            {
                                $video_id = $resource['video_id'];
    ?>                              
                                    <ul class="inner nobullet">
                                        <li><a href="/download-file?module_id=<?= $module_id ?>&course_id=<?= $course_id ?>&resource_id=<?= $resource['ID'] ?>"><i class="fa fa-sticky-note-o" aria-hidden="true"></i></a> <?= $module_title ?> - <span class="small"><a href="/download-file?module_id=<?= $module_id ?>&resource_id=<?= $resource['ID'] ?>&course_id=<?= $course_id ?>"><?= __("Download Handout (PDF)", "EOT_LMS"); ?></a></span></li>
                                    </ul>
    <?php
                            }
                            unset($resource);
                        }
                    }
?>
                    </li>                    
<?php
            echo '</ul>';
                } //end for each
            } 
            else 
            {
                echo __("There are no modules in this course. Please contact your camp director.", "EOT_LMS");
            }
            
            $quizzes_in_course   = getQuizzesInCourse($course_id);
            $quiz_ids            = array_column($quizzes_in_course, 'ID');
            $quiz_ids_string     = implode(',', $quiz_ids);
            $passed_quizzes      = getPassedQuizzes($quiz_ids_string, $user_id);
            $passed_quiz_ids     = array_column($passed_quizzes, 'ID');
            $quiz_attempts       = getAllQuizAttempts($course_id, $user_id);
            //d($quizzes_in_course,$quiz_attempts,$passed_quizzes,$finished_module_quizzes);
            $track_passed        = array();
            $track_quiz_attempts = array();
            foreach ($quiz_attempts as $key => $record) 
            {
                if ($record['passed'] == 1) 
                {
                    array_push($track_passed, $record['quiz_id']); // Save the quiz ID of the passed quiz.
                    //unset($track_quizzes[$key]); // Delete them from the array.
                }
                array_push($track_quiz_attempts, $record['quiz_id']);
            }
            $passed_users  = array_count_values($track_passed);
            $attempt_count = array_count_values($track_quiz_attempts);
?>
                                    <h1 class="article_page_title"><?= __("Quiz Summary", "EOT_LMS"); ?></h1>
                                    <div class="bss">
                                    <table class="table table-striped table-bordered" border="1">
                                        <thead>
                                        <th><b><?= __("Quiz Title", "EOT_LMS"); ?></b></th>
                                        <th align="center"><b><?= __("Attempts", "EOT_LMS"); ?></b>&nbsp;<i class="fa fa-question-circle" title="<b><?= __("This shows the number of times you attempted the quiz.", "EOT_LMS"); ?></b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b><?= __("This shows the number of times you attempted the quiz.", "EOT_LMS"); ?></b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></th>
                                        <th><b>Status</b>&nbsp;<i class="fa fa-question-circle" title="<b><?= __("Whether you passed or failed the quiz.", "EOT_LMS"); ?></b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b><?= __("Whether you passed or failed the quiz.", "EOT_LMS"); ?></b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></th>
                                        <th><b>Quiz</b></th>
                                        </thead>
                                        <tbody>
                                            <?php
            foreach ($quizzes_in_course as $quiz) 
            {
                $passed   = isset($passed_users[$quiz['ID']]) ? 'Passed' : 'Incomplete'; //Number of passes
                $attempts = isset($attempt_count[$quiz['ID']]) ? $attempt_count[$quiz['ID']] : 0; //Number of quiz attempts
                if (!isset($passed_users[$quiz['ID']]) && $attempts > 0) //they must have failed that quiz
                {
                    $passed = "Failed";
                }
?>
                                            <tr>
                                                <td><?= $quiz['name'] . ($passed != 'Incomplete' ? '<br> <a href="/dashboard?part=wronganswers&quiz_id=' . $quiz['ID'] . '&course_id=' . $course_id . '">' . __("See Wrong Answers", "EOT_LMS") . '</a>' : '') ?></td>
                                                <td align="center"><?= $attempts ?></td>
                                                <td><?= $passed ?></td>
                                                <td>
                                                    <?php
                $action = __("Take Quiz", "EOT_LMS") . ' &nbsp;<i class="fa fa-question-circle" title="" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip(\'<b>' . __("You must watch the video first (all the way through) before attempting the quiz.", "EOT_LMS") . '</b>\', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')" onmouseout="UnTip()"></i>';
                if (in_array($quiz['ID'], $finished_module_quizzes) && $quiz['org_id'] == 0) 
                {
                    $action = '<a href="?part=quiz&quiz_id=' . $quiz['ID'] . '&subscription_id=' . $subscription_id . '&course_id=' . $course_id . '&enrollment_id=' . $enrollment_id . '">Take Quiz</a>';
                } 
                elseif ($quiz['org_id'] != 0) 
                {
                    $action = '<a href="?part=quiz&quiz_id=' . $quiz['ID'] . '&subscription_id=' . $subscription_id . '&course_id=' . $course_id . '&enrollment_id=' . $enrollment_id . '">' . __("Take Quiz", "EOT_LMS") . '</a>';
                }
                echo $action;
?>
                                                </td>
                                            </tr>
                                            <?php
            }
            $percentage = (count($quizzes_in_course) > 0) ? (count($passed_users) / count($quizzes_in_course)) * 100 : 0;
?>
                                            <tr>
                                                <td><b><?= __("Completed Quizzes", "EOT_LMS"); ?></b></td>
                                            	<td align="center"><b><?= count($passed_users) ?></b></td>
                                            <td colspan="2"><?= eotprogressbar('12em', $percentage, true) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                    <?php
            
        } //end if course info
        elseif ($course_info && $enrollment_status == "completed") 
        {
            
        } 
        else 
        {
            wp_die(__("Could not find the course. Please report this to the technical support.", "EOT_LMS"));
        }
    } 
    else 
    {
        wp_die(__("You do not have privilege to view this page.", "EOT_LMS"));
    }
} 
else 
{
    wp_die(__("Invalid course. Please report this to the technical support.", "EOT_LMS"));
}

/**
 * check the track results for the ID of the video
 * @param $array - the track result
 * @param $field - the field you're looking for. ie. video_id
 * @param $value - the video ID
 */
function myKey($array, $field, $value)
{
    foreach ($array as $key => $item) 
    {
        if ($item->$field == $value)
            return $key;
    }
    return false;
}
?>
