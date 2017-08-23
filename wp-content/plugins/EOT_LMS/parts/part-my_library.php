<?php
	if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] != "")
	{
		$course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);

		if(current_user_can("is_student"))
	    {
            global $current_user;
            $user_id = $current_user->ID;
            $admin_ajax_url = admin_url('admin-ajax.php');
            $subscription_id = getSubscriptionIdByUser($user_id);

			// verify this user has access to this course
	    	$has_access = verify_student_access($course_id);
			if (!$has_access)
			{
				wp_die("You do not have access to this course");
			}

			// check course status - update in necessary
			$enrollment_id = isset($_REQUEST['enrollment_id']) ? filter_var($_REQUEST['enrollment_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
			$enrollment_status = getEnrollmentStatus($enrollment_id);
			if($enrollment_status == 'not_started')
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
	
			if($course_info)
			{
?>
				<div class="breadcrumb">
					<?= CRUMB_DASHBOARD ?>    
					<?= CRUMB_SEPARATOR ?>  
				  	<span class="current"><?= $course_info['course_name'] ?></span> 
				</div>
		      	<h1 class="article_page_title"></h1>
				<h2>Course "<u><?= $course_info['course_name'] ?></u>"</h2> 	
				<p>Here are the videos, exams and downloadable handouts available to you in this module.</p>
<?php
		       	// Get all the modules in this course by the course ID
				$user_id = get_current_user_id(); // WP User ID
                $org_id = get_org_from_user($user_id);
				$modules_in_course = getModulesInCourse($course_id);
                $modules_in_portal = getModules($org_id);// all the custom modules in this portal
                $modules_in_portal_ids = array_column($modules_in_portal, 'ID');
                $modules_in_portal_ids_string = implode(',',$modules_in_portal_ids);
                $videos_in_custom_modules = getVideoResourcesInModules($modules_in_portal_ids_string);
                $quizzes_in_custom_modules=getQuizResourcesInModules($modules_in_portal_ids_string);
                $resources_in_custom_modules =  getHandoutResourcesInModules($modules_in_portal_ids_string);
				$subscription = getSubscriptionByCourse($course_id); // get the subscription data
				$library_id = isset($subscription['library_id']) ? $subscription['library_id'] : 0; // the library ID
				$categories = getCategoriesByLibrary($library_id); // Get all the library from the master library (course).   
				$resources_doc = getResourcesInCourse($course_id, "doc");
				$resources_video = getResourcesInCourse($course_id, "video");
	            $resources_exam = getResourcesInCourse($course_id, "exam");
	            $finished_module_quizzes = array();
	            //d($modules_in_portal,$videos_in_custom_modules,$quizzes_in_custom_modules,$resources_in_custom_modules);
	            
	            $exams = array();
	            foreach($resources_exam as $exam){
	                if(isset($exams[$exam['mid']]))
	                {
	                    array_push($exams[$exam['mid']], array('ID'=>$exam['ID'],'name'=>$exam['name']));
	                }
	                else
	                {
	                	$exams[$exam['mid']]=array();
	                	array_push($exams[$exam['mid']], array('ID'=>$exam['ID'],'name'=>$exam['name']));
	                }
	            }

				$video_track = getTrack($user_id, 0, "watch_video");
				$modules = array(); // Array of Module objects
				$available_categories = array_unique(array_column($modules_in_course, 'category'));

				// if there are any null categories, set them to `custom`.
				$available_categories = array_map(function($v){
			    	return (is_null($v)) ? "Custom" : $v;
			    },$available_categories);

			    // if there are modules in this course, create module objects
		        if (isset($modules_in_course))
		        { 
					foreach($modules_in_course as $key => $module)
					{
						/* 
						 * This populates the modules array.
						 */
						$category_name = in_array($module['category'], $available_categories) ? $module['category'] : 'Custom'; // The category name for this module.
						$new_module = new module( $module['ID'], $module['title'], $category_name); // Make a new module object.
						array_push($modules, $new_module); // Add the new module to the modules array.
					}

			        // Display library and the modules inside it.
                            foreach($categories as $category)
                            {
                                    $category_name = $category->name;
                                    // Check if the category has any modules. Otherwise skip it.
                                    if(in_array($category_name, $available_categories))
                                    {
		        			echo '<h3 class="library_topic">'.$category_name.'</h3>';
			              	foreach( $modules as $key => $module )
		          		{
			                	if ( $module->category == $category_name )
		                		{
		                			echo '<ul class="tree">';
									$module_id = $module->id; // The module ID
									$module_title = $module->title; // The module name.
?>
				                  		<li class="tree_video">
				          					<a href="?part=view&course_id=<?= $course_id ?>&module_id=<?=$module_id?>">
				          						<i class="fa fa-play" aria-hidden="true"></i>
				      						</a> 
				      						<b><?= $module_title ?></b> 
				      						<span class="small"> - 
				          						<a href="?part=view&course_id=<?=$course_id?>&module_id=<?=$module_id?>">
				          							Watch Video
				          						</a> 
<?php 
				          							foreach ($resources_video as $key => $video) 
				          							{
				          								// Get the video ID
				          								if($video['module_id'] == $module_id)
				          								{
				          									$video_id = $video['ID'];
				          									$isFinished = (isset($video_track[$video_id]) && $video_track[$video_id]->result == 1) ? true : false;
				          									unset($resources_video[$key]); // Unset the key.
				          								}
				          							}
				          							// display link to the quiz if the video has been watched.
				          							if($isFinished)
				          							{
                                                        if(isset($exams[$module_id]))
                                                        {
                                                            $exam_data = $exams[$module_id];
                                                            $quiz_id = $exam_data[0]['ID'];
															echo '/ <a href="?part=quiz&module_id='.$module_id .'&quiz_id='.$quiz_id.'&subscription_id='.$subscription_id.'&course_id='.$course_id.'">Take Quiz</a>';
															array_push($finished_module_quizzes,$quiz_id); //store that the module for this quiz was completed
			
                                                        }
                                                    }
				          							else
				          							{
                                                        if(isset($exams[$module_id]))
                                                        {
?>                                                                                                  
				          									/ Take Quiz
															&nbsp; <img src="<?= get_template_directory_uri() . "/images/info-sm.gif"?>" title="<b>You must watch the video first (all the way through) before attempting the quiz.</b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b>You must watch the video first (all the way through) before attempting the quiz.</b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()">
<?php
                                                        }
                                                    }

				      				echo	'</span>';
		      						if( isset( $resources_doc ) && count($resources_doc) > 1 )
		      						{
		      							foreach ($resources_doc as $key => $resource) 
		      							{
		      								if($resource['module_id'] == $module->id)
		      								{
			      								$video_id = $resource['video_id'];
?>								
			              						<ul class="inner nobullet">
	                                                <li><a href="/dashboard?part=download&module_id=<?=$module_id?>&course_id=<?=$course_id?>&resource_id=<?=$resource['ID']?>"><i class="fa fa-sticky-note-o" aria-hidden="true"></i></a> <?= $module_title ?> - <span class="small"><a href="/dashboard?part=download&module_id=<?=$module_id?>&resource_id=<?=$resource['ID']?>">Download Handout (PDF)</a></span></li>
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
		                		}//end if
		                	}//end for each
                                    }//end if
	        		}//end for each
		        }//end if
                        
		        else
		        {
		        	echo 'There are no modules in this course. Please contact your camp director.';
		        }

                echo '<h3 class="library_topic">Custom Modules</h3>';
                $rcm = array();
                if(count($resources_in_custom_modules) > 0)
                {
                    foreach($resources_in_custom_modules as $resource)
                    {
                      if(isset($rcm[$resource['mod_id']]))
                      {
                        array_push($rcm[$resource['mod_id']], array('ID'=>$resource['ID'],'name'=>$resource['name'],'type'=>$resource['type'],'url'=>$resource['url']));
                      }
                      else
                      {
                        $rcm[$resource['mod_id']]=array();
                        array_push($rcm[$resource['mod_id']], array('ID'=>$resource['ID'],'name'=>$resource['name'],'type'=>$resource['type'],'url'=>$resource['url']));
                      }
                    }
                }
                $exams = array();
                if(count($quizzes_in_custom_modules) > 0)
                {
                    foreach($quizzes_in_custom_modules as $resource)
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
                }

                //d($exams);
                foreach($modules_in_portal as $module)
                {
                    echo '<ul class="tree">';
                    $module_id = $module['ID']; // The module ID
    				$module_title = $module['title']; // The module name.
?>
                    <li class="tree_video">
                        <i class="fa fa-square-o" aria-hidden="true"></i>
                            <b><?= $module_title ?></b>
<?php
                    if(isset($rcm[$module_id]))
                    {
                        foreach($rcm[$module_id] as $resource)
                        {
                            switch ($resource['type']) 
                            {
                                case 'link':
                                    $icon = "fa-link";
                                    $url = $resource['url'];
                                    $action = 'Visit Url';
                                    break;
                                case 'doc':
                                    $icon = "fa-sticky-note-o";
                                    $url = "/dashboard?part=download&module_id=$module_id&course_id=$course_id&resource_id=".$resource['ID'];
                                    $action = 'Download File';
                                    break;
                                case 'custom_video':
                                    $icon = "fa-play";
                                    $url = "?part=view_custom&course_id=$course_id&module_id=$module_id&video_id=".$resource['ID'];
                                    $action = 'Watch Video';
                                    break;
                                default:
                                    $icon = "fa-sticky-note-o";
                            }
                            
?>
		                    <ul class="inner nobullet">
		                        <li><a href="<?= $url ?>"><i class="fa <?= $icon; ?>" aria-hidden="true"></i></a> <?= $resource['name'] ?> - <span class="small"><a href="<?= $url ?>"><?= $action;?></a></span></li>
		                    </ul>
<?php
                        }
                    
                    }

                    if(isset($exams[$module_id]))
                    {
                        foreach ($exams[$module_id] as $exam) 
                        {
                            $exam_id = $exam['ID'];
                            $exam_title = $exam['name'];
                            $icon = "fa-question-circle-o";
                            $url = "?part=quiz&module_id=$module_id&quiz_id=$exam_id&subscription_id=$subscription_id&course_id=$course_id";
?>
		                    <ul class="inner nobullet">
		                        <li><a href="<?= $url ?>"><i class="fa <?= $icon; ?>" aria-hidden="true"></i></a> <?= $exam_title ?> - <span class="small"><a href="<?= $url ?>">Take Quiz</a></span></li>
		                    </ul>
<?php
                        }
                    }
                                            
?>
                        </li>

                        <script>
                            function downloadResource(resource_id,module_id)
                            {
                                var url =  ajax_object.ajax_url  + "?action=trackAndDownload&user_id=<?= $user_id ?>&module_id="+module_id+"&resource_id="+resource_id;
                                $.ajax({
                                url:url,
                                success:
                                    function(data)
                                    {
                                    }
                                });
                            }
	                        $(document).ready(function(){});
                        </script>
<?php
                	echo '</ul>';
				}
                                    $quizzes_in_course = getQuizzesInCourse($course_id);
                                    $quiz_ids = array_column($quizzes_in_course, 'ID');
                                    $quiz_ids_string = implode(',', $quiz_ids);
                                    $passed_quizzes = getPassedQuizzes($quiz_ids_string,$user_id);
                                    $passed_quiz_ids = array_column($passed_quizzes, 'ID');
                                    $quiz_attempts = getAllQuizAttempts($course_id, $user_id);
                                    //d($quizzes_in_course,$quiz_attempts,$passed_quizzes,$finished_module_quizzes);
                                    $track_passed = array();
                                    $track_quiz_attempts = array();
                                    foreach ($quiz_attempts as $key => $record) 
                                    {
                                        if($record['passed'] == 1)
                                        {
                                          array_push($track_passed, $record['quiz_id']); // Save the quiz ID of the passed quiz.
                                          //unset($track_quizzes[$key]); // Delete them from the array.
                                        }
                                       array_push($track_quiz_attempts, $record['quiz_id']);
                                    }
                                    $passed_users = array_count_values($track_passed);
                                    $attempt_count = array_count_values($track_quiz_attempts);
?>
                                    <h1 class="article_page_title">Quiz Summary</h1>
                                    <div class="bss">
                                    <table class="table table-striped table-bordered" border="1">
                                        <thead>
                                        <th><b>Quiz Title</b></th>
                                        <th align="center"><b>Attempts</b>&nbsp;<img src="<?= get_template_directory_uri() . "/images/info-sm.gif"?>" title="<b>This shows the number of times you attempted the quiz.</b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b>This shows the number of times you attempted the quiz.</b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></th>
                                        <th><b>Status</b>&nbsp;<img src="<?= get_template_directory_uri() . "/images/info-sm.gif"?>" title="<b>Whether you passed or failed the quiz.</b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b>Whether you passed or failed the quiz.</b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></th>
                                        <th><b>Quiz</b></th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($quizzes_in_course as $quiz) 
                                            { 
                                                $passed = isset($passed_users[$quiz['ID']])? 'Passed' : 'Incomplete';//Number of passes
                                                $attempts = isset($attempt_count[$quiz['ID']]) ? $attempt_count[$quiz['ID']] : 0;//Number of quiz attempts
                                                if(!isset($passed_users[$quiz['ID']]) && $attempts > 0)//they must have failed that quiz
                                                {
                                                    $passed = "Failed";
                                                }
                                            ?>
                                            <tr>
                                                <td><?= $quiz['name'].($passed != 'Incomplete'?'<br> <a href="/dashboard?part=wronganswers&quiz_id='.$quiz['ID'].'&user_id='.$user_id.'&course_id='.$course_id.'">See Wrong Answers</a>':'')?></td>
                                                <td align="center"><?= $attempts ?></td>
                                                <td><?= $passed ?></td>
                                                <td>
                                                    <?php
                                                    $action = 'Take Quiz&nbsp;<img src="'.get_template_directory_uri() . '/images/info-sm.gif" title="" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip(\'<b>You must watch the video first (all the way through) before attempting the quiz.</b>\', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\')" onmouseout="UnTip()">';
                                                    if(in_array($quiz['ID'], $finished_module_quizzes) && $quiz['org_id']==0)
                                                    {
                                                       $action = '<a href="?part=quiz&quiz_id='.$quiz['ID'].'&subscription_id='.$subscription_id.'&course_id='.$course_id.'">Take Quiz</a>';
                                                    }
                                                    elseif($quiz['org_id']!=0)
                                                    {
                                                       $action = '<a href="?part=quiz&quiz_id='.$quiz['ID'].'&subscription_id='.$subscription_id.'&course_id='.$course_id.'">Take Quiz</a>';  
                                                    }
                                                    echo $action;
                                                            ?>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                            $percentage = (count($passed_users)/count($quizzes_in_course))*100;
                                            ?>
                                            <tr>
                                                <td><b>Completed Quizzes</b></td>
                                            <td align="center"><?= count($passed_users)?></td>
                                            <td colspan="2"><?= eotprogressbar('12em', $percentage, true)?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                    <?php
                                
			}//end if course info
			else
			{
				wp_die('Could not find the course. Please report this to the technical support.');
			}
		}
		else
		{
			wp_die('You do not have privilege to view this page.');
		}
	}
	else
	{
		wp_die('Invalid course. Please report this to the technical support.');
	}
?>
