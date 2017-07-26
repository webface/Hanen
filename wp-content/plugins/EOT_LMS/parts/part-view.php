<?php
	if( isset($_REQUEST['module_id']) && isset($_REQUEST['course_id']) 
		&& isset($_REQUEST['courses_modules'])&& isset($_REQUEST['video_resources']))
	{
		$user_id = get_current_user_id(); // WP User ID
    	$module_id = filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT); // The module ID
    	$course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
		$enrollments = getEnrollmentsByUserId($user_id); // Enrollments of this user.
		if($enrollments) 
		{
			$allowed_courses_ids = array_column($enrollments, "course_id"); // Lists of courses the user is enrolled to.
		   	// Verify if the user is enrolled to the course.
			if(in_array($course_id, $allowed_courses_ids))
			{
				$subLanguage = isset($_REQUEST['subLang']) ? filter_var($_REQUEST['subLang'],FILTER_SANITIZE_STRING) : null; // Video Language
		   		$resolution = isset($_REQUEST['res']) ? filter_var($_REQUEST['res'],FILTER_SANITIZE_STRING) : null; // Video resolution
				$course_modules = json_decode( base64_decode ( filter_var( $_REQUEST['courses_modules'],FILTER_SANITIZE_STRING ) ),true ); // The course modules in array
				$course_modules_ids = array_column($course_modules, 'module_id'); // The course modules ids
				// Check if the module belongs to the course.
				if(in_array($module_id, $course_modules_ids))
				{
					$videos_resources_in_module =  json_decode ( base64_decode( filter_var($_REQUEST['video_resources'],FILTER_SANITIZE_STRING ) ), true ) ; // All the modules.
					$video_resource = $videos_resources_in_module[$module_id];
					if( isset( $video_resource) )
					{
						// Staff only have access to videos that are in the courses they are enrolled in.
						$module_name = $video_resource['name'];
						$video_id = $video_resource['ID']; // The video ID
						$video_record = getTrack($user_id, $video_id);
						$track_id = ((isset($video_record['ID'])) && $video_record['ID'] > 0) ? $video_record['ID'] : 0;
						//turn seconds into minutes.seconds
		              	$duration_in_seconds = $video_resource['secs'];
		              	$minutes = floor($duration_in_seconds / 60);
		              	$seconds = $duration_in_seconds - (60 * $minutes);
              			
              			// Check if the user has not watched the video yet. Get the time where they last watched the video.
		              	$videoWatch = ( isset($video_record['result'] ) && $video_record['result'] == 1) ? true : false;
		              	$video_last_time  = (isset($video_record['video_time']) && $video_record['video_time'] > 0) ? $video_record['video_time'] : 0;
?>
						<div class="breadcrumb">
							<?= CRUMB_DASHBOARD ?>    
							<?= CRUMB_SEPARATOR ?>  
							<?= CRUMB_MY_LIBRARY ?>    
							<?= CRUMB_SEPARATOR ?>    
							  <span class="current"><?= $module_name ?></span> 
						</div>
						<h1 class="video_title"><?= $module_name ?></h1>
						<b>Length: <?= " (" . $minutes . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . ") "; ?></b>
						<br>
						<br>
		                <div id='player123' style='width:665px;height:388px'>
		                    <video id="my-video" user-id="<?=$user_id?>" track-id="<?=$track_id?>" class="video-js vjs-default-skin" controls preload="auto" width="665" height="388" poster="<?php echo bloginfo('template_directory'); ?>/images/eot_logo.png" data-setup='{"controls": true}'>
		                        <?php 
		                        // Check if we are showing by language or resolution.
		                        if($subLanguage)
		                        {
		                        ?>
		                            <source src="https://eot-output.s3.amazonaws.com/<?= $subLanguage ? $video_record['spanish'] : $video_resource['shortname_medium'] ?>.mp4" type='video/mp4'>4
		                            <p class="vjs-no-js">
		                            To view this video please enable JavaScript, and consider upgrading to a web browser that
		                            <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		                            </p>
		                        <?php
		                        }
		                        else
		                        {
		                            // Manage changing video resolution
		                        ?>
		                            <?php
		                            if($resolution == null || $resolution == "high")
		                            {
		                                $video_name = $video_resource['shortname'];
		                            }
		                            else if($resolution == "medium")
		                            {
		                                $video_name = $video_resource['shortname_medium'];
		                            }
		                            else if($resolution == "low")
		                            {
		                                $video_name = $video_resource['shortname_low'];
		                            } 
		                            ?>

		                            <source src="rtmp://awscdn.expertonlinetraining.com/cfx/st/&mp4:<?= $video_name ?>.mp4#t=<?= $video_last_time ?>" type='rtmp/mp4'>
		                            <source src="http://awscdn2.expertonlinetraining.com/<?= $video_name ?>.mp4#t=<?= $video_last_time ?>" type='video/mp4'>
		<!--
		                            <source src="https://eot-output.s3.amazonaws.com/<?= $video_name ?>.mp4" type='video/mp4'>4
		-->
		                            <p class="vjs-no-js">
		                            To view this video please enable JavaScript, and consider upgrading to a web browser that
		                            <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		                            </p>        
		                        <?php
		                        }?>
		                    </video>
		                </div>
		                <h3>Description</h3>
						<p>
		          			<?= $video_resource['desc']; ?>       
		      			</p>
		      			<br>
		      			<center>
		      				<div id="quiz" style="display:none">
								<a class="btn" style="" href="?part=quiz&module_id=<?= $module_id ?> " rel="facebox">
			    					Take Quiz
		  						</a>
								</div>
								<div id="noQuiz" style="display:none">
									<p>Note* You have to <b>finish</b> <b>watching</b> the <b>video</b> to be able to take the quiz.</p>
								</div>
							</center>     			
		      			<center>  	
		      			<script type='text/javascript'>
		      			$(document).ready(function() {
		      				<?php
		      				// Check if the user has not watched the video yet.
							if( $videoWatch )
							{
								echo '$("#quiz").show();'; // Show Take quiz button.
							}
							else
							{
								echo '$("#noQuiz").show();'; // Show message to finished the video..
							}
							?>			
		      			});
		      			// Process when the video is finished
		      		   	$('video').on("ended", function() {
			            	$("#noQuiz").hide();
		  		   			var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&track_id="+$(this).attr("track-id")+"&status=finish";
							$.ajax({
							url:url,
				            success:
				            function(data)
				            {
				            	$("#quiz").show(); // Show Take quiz button.
				            }
				            });
		      		   	});
		      		   	// On video pause
		      		   	$("video").on("pause", function (e) {
		      		   		var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&time=" + e.target.currentTime + "&track_id="+$(this).attr("track-id")+"&video_id=<?=$video_id?>&status=pause";
							$.ajax({url:url,
				            success:
				            function(data)
				            {
				            	// Don't need to do anything.
				            }
				            });
						});
						// On video play
			  		   	$("video").on("play", function (e) {
			  		   		// Handle new watch video.
			  		   		if( $("video").attr('track-id') == 0 )
			  		   		{
								var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&status=started&video_id=<?=$video_id?>";
								$.ajax({url:url,
					            success:
					            function(data)
					            {
					            	var result = JSON.parse(data);
					            	$("video").attr('track-id', result.track_id);
					            },
					            });
							  	$( this ).off( e ); // Triggers once only.
			  		   		}
						});
		      			</script>
	<?php
		      		}
		      		else
		      		{
		      			echo 'Error: Could not find the module. Please contact the administrator.';
		      		}
				}
				else
				{ // Error, module does not belong to the course.
					echo 'Error: This module does not belong to this course.';
				}
			}
			else
			{ // Error, no privilege to view this course..
				echo "Error: You are not enrolled in this course.";
			}
		}
		else
		{
			echo "Error: You don't have any enrollments. Please contact your camp director.";
		}
	}
	else
	{ // Incorrect parameters.
		echo "Sorry but you have an invalid request. Please contact the site administrator.";
	}
?>