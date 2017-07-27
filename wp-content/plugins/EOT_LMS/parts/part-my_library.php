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
				$user_id = get_current_user_id(); // WP User ID
				$modules_in_course = getModulesInCourse($course_id);
				$categories = getCategoriesByLibrary(1); // Get all the library from the master library (course).            
				$resources_doc = getResourcesInCourse($course_id, "doc");
				$resources_video = getResourcesInCourse($course_id, "video");
				$video_track = getTrack($user_id, 0, "watch_video");
				$modules = array(); // Array of Module objects
				$available_categories = array_unique(array_column($modules_in_course, 'category'));
				// Replace `custom` to no categories.
				$available_categories = array_map(function($v){
			    	return (is_null($v)) ? "Custom" : $v;
			    },$available_categories);
		        if (isset($modules_in_course))
		        { 
					foreach($modules_in_course as $key => $module)
					{
						/* 
						 * This populates the modules array.
						 */
						// if the module does not belong to any of our categories. Skip...
						if( !isset( $categories[$module['category_id']] ) )
						{
						  $new_module = new module( $module['ID'], $module['title'], 'Custom'); // Make a new module in the 'Custom' category
						  array_push($modules, $new_module); // Add the new module to the modules array.

						}
						else
						{
						  $category_name = $categories[$module['category_id']]->name; // The category name of this module.
						  $new_module = new module( $module['ID'], $module['title'], $category_name); // Make a new module object.
						  array_push($modules, $new_module); // Add the new module to the modules array.
						}
					}

					// Get rid of category that does not have any modules
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
				          							echo ($isFinished) ? '/ <a href="?part=quiz&module_id='.$module_id .'">Take Quiz</a>' : ' / Take Quiz' ?> 
				          						<img src="<?= get_template_directory_uri() . "/images/info-sm.gif"?>" title="<b>You must watch the video first (all the way through) before attempting the quiz.</b>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<b>You must watch the video first (all the way through) before attempting the quiz.</b>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()">
				      						</span>
				<?php 
				      						if( isset( $resources_doc ) && count($resources_doc) > 1 )
				      						{
				      							foreach ($resources_doc as $key => $resource) 
				      							{
				      								if($resource['module_id'] == $module->id)
				      								{
					      								$video_id = $resource['video_id'];
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
									echo '</ul>';
		                		}
		                	}
	                	}
	        		}
		        }
		        else
		        {
		        	echo 'There are no modules in this course. Please contact your camp director.'
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
