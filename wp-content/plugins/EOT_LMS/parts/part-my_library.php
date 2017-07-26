<?php
	if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] != "")
	{
		$course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);

		if(current_user_can("is_student"))
	    {

			// verify this user has access to this course
	    	$has_access = verify_student_access($course_id);
			if (!$has_access)
			{
				wp_die("You do not have access to this course");
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
				$course_modules = getCourseModules($course_id);
				$resources = getResourcesInCourse($course_id, "doc");
				$modules_ids = implode(',', array_column($course_modules, 'module_id'));
				if($course_modules)
				{
					$user_id = get_current_user_id(); // WP User ID
					$videos_resources_in_module = getVideoResourcesInModules($modules_ids, $user_id);
					echo '<ul class="tree">';
					?>
					<form id="moduleInfoForm" action="?part=view" method="post">
						<input type="hidden" name="courses_modules" value="<?= base64_encode(json_encode($course_modules))?>">
						<input type="hidden" name="video_resources" value="<?= base64_encode(json_encode($videos_resources_in_module)) ?>">
					</form>
					<?php
					// Display link to watch the module, and link to the quiz if they finished watching the video.
					foreach ($course_modules as $module) 
					{
						$module_id = $module['module_id']; // The module ID
						// CHeck if the video resources exist for this module. Otherwise skip.
						if( !isset( $videos_resources_in_module[$module_id] ) )
						{
							continue;
						}
						$module_title = $videos_resources_in_module[$module_id]->name; // The module name.
					?>
                  		<li class="tree_video">
          					<a href="?part=view&course_id=<?= $course_id ?>&module_id=<?=$module_id?>">
          						<i class="fa fa-play" aria-hidden="true"></i>
      						</a> 
      						<b><?= $module_title ?></b> 
      						<span class="small"> - 
          						<a class="watchVideo" module-id="<?=$module_id?>" course-id="course_id=<?= $course_id ?>" href="">
          							Watch Video
          						</a> 
          						<?php 
          							// display link to the quiz if the video has been watched.
          							echo ( $videos_resources_in_module[$module_id]->result == 1 ) ? '/ <a href="?part=quiz&module_id='.$module_id .'">Take Quiz</a>' : '' ?> 
          						<img src="<?= get_template_directory_uri() . "/images/info-sm.gif"?>" title="<b>You must watch the video first (all the way through) before attempting the quiz.</b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b>You must watch the video first (all the way through) before attempting the quiz.</b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()">
      						</span>
<?php 
      						if( isset( $resources ) && count($resources) > 1 )
      						{
      							foreach ($resources as $key => $resource) 
      							{
      								if($resource['module_id'] == $module['module_id'])
      								{
?>								
	              						<ul class="inner nobullet">
	                                        <li><a href="<?= $resource['url'] ?>"><i class="fa fa-sticky-note-o" aria-hidden="true"></i></a> <?= $module_title ?> - <span class="small"><a href="<?= $resource['url']?>">Download Handout (PDF)</a></span></li>
                                      	</ul>
<?php 
      								}
      								unset($resource);
      							}
							}
?>
    					</li> 	                 
					<?php
					}
					echo '</ul>';
					?>
					<h2>Your Custom Quizzes</h2>

					<h1 class="article_page_title">Quiz Summary</h1>
					<script type="text/javascript">
						$(document).ready(function() {
							// Handles the link for Watch Video. It is done via form submit so we can transfer the modules to the next page.
							$('.watchVideo').click(function(e) {
								e.preventDefault();
								var course_id = e.target.getAttribute('course-id'); // Course ID
								var module_id = e.target.getAttribute('module-id'); // Module ID
								$('#moduleInfoForm').attr('action', "?part=view&"+course_id+"&module_id="+module_id+"");
								form = $('#moduleInfoForm').submit();
							})
							var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?=$course_id?>&status=progress";
							$.ajax({
							url:url,
				            success:
				            function(data)
				            {
				            	$("#quiz").show(); // Show Take quiz button.
				            }
				            });
						});
					</script>
					<?php
				}
				else if(count($course_modules) == 0) // User has no modules
				{
				 	echo '<b>' . $course_info['course_name'] . '</b>: There are no modules in this course. Please contact your camp director.';
				}
			}
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
