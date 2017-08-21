<?php
	if( isset($_REQUEST['module_id']) && isset($_REQUEST['course_id']))
	{
    	$module_id = filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT); // The module ID
    	$course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
        $subscription = getSubscriptionByCourse($course_id);
        $subscription_id = $subscription['ID'];
    	// make sure the user has access to this course
		$has_access = verify_student_access($course_id);
		if($has_access) 
		{
			// Check if the module belongs to the course.
			if(verify_module_in_course($module_id, $course_id, $type = 'video'))
			{
				// get the module data
				$module = getModule($module_id);
?>
<div class="breadcrumb">
	<?= CRUMB_DASHBOARD ?>    
	<?= CRUMB_SEPARATOR ?>  
	<?= CRUMB_MY_LIBRARY ?>    
	<?= CRUMB_SEPARATOR ?>    
	  <span class="current"><?= $module['title'] ?></span> 
</div>
<?php

				$user_id = get_current_user_id(); // WP User ID
				$subLanguage = isset($_REQUEST['subLang']) ? filter_var($_REQUEST['subLang'],FILTER_SANITIZE_STRING) : null; // Video Language
		   		$resolution = isset($_REQUEST['res']) ? filter_var($_REQUEST['res'],FILTER_SANITIZE_STRING) : null; // Video resolution
				$module_video_resources = getResourcesInModuleInCourse($course_id, $module_id, $type = 'video'); // get the video resources in this module
                                $resources_exam = getResourcesInCourse($course_id, "exam");

                                
                                $exams = array();
                                foreach($resources_exam as $exam){
                                    if(isset($exams[$exam['mid']]))
                                    {
                                        array_push($exams[$exam['mid']], array('ID'=>$exam['ID'],'name'=>$exam['name']));
                                    }else{
                                    $exams[$exam['mid']]=array();
                                    array_push($exams[$exam['mid']], array('ID'=>$exam['ID'],'name'=>$exam['name']));
                                    }
                                }
				if( isset( $module_video_resources ) )
				{
					foreach ($module_video_resources as $key => $video)
					{
						$video_name = $video['name'];
						$video_id = $video['ID']; // The video ID
						$video_record = getTrack($user_id, $video_id);
						$track_id = ( count($video_record) > 0) ? $video_record['ID'] : 0;

						//turn seconds into minutes.seconds
		              	$duration_in_seconds = $video['secs'];
		              	$minutes = floor($duration_in_seconds / 60);
		              	$seconds = $duration_in_seconds - (60 * $minutes);

		      			// Check if the user has not watched the video yet.
		              	$videoWatchStatus = ( isset($video_record['result'] ) && $video_record['result'] == 1) ? true : false;
		              	//  Get the time where they last watched the video.
		              	$video_last_time  = (isset($video_record['video_time']) && $video_record['video_time'] > 0) ? $video_record['video_time'] : 0;
		              	echo '<h1 class="video_title">' . $video_name . '</h1>';
                                //d($video);

?>
				<b>Length: <?= " (" . $minutes . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . ") "; ?></b>
				<br>
				<br>
                <div id='player_<?= $video_id; ?>' style='width:665px;height:388px'>
                    <video id="my-video" user-id="<?=$user_id?>" track-id="<?=$track_id?>" class="video-js vjs-default-skin" controls preload="auto" width="665" height="388" poster="<?php echo bloginfo('template_directory'); ?>/images/eot_logo.png" data-setup='{"controls": true}'>
                        <track kind="captions" src="https://eot-output.s3.amazonaws.com/<?= $video['video_name'] ?>_en.vtt" srclang="en" label="English" default>
                        <track kind="captions" src="https://eot-output.s3.amazonaws.com/<?= $video['video_name'] ?>_es.vtt" srclang="es" label="Spanish">
                        <track kind="captions" src="https://eot-output.s3.amazonaws.com/<?= $video['video_name'] ?>_ma.vtt" srclang="man" label="Mandarin">

<?php 
                        // Check if we are showing by language or resolution.
                        if($subLanguage)
                        {
?>
                            <source src="https://eot-output.s3.amazonaws.com/<?= $subLanguage ? $video_record['spanish'] : $video['shortname_medium'] ?>.mp4" type='video/mp4'>4
                            <p class="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a web browser that
                            <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                            </p>
<?php
                        }
                        else
                        {
                            // Manage changing video resolution
                            if($resolution == null || $resolution == "high")
                            {
                                $video_name = $video['shortname'];
                            }
                            else if($resolution == "medium")
                            {
                                $video_name = $video['shortname_medium'];
                            }
                            else if($resolution == "low")
                            {
                                $video_name = $video['shortname_low'];
                            } 
?>

                            <source src="https://eot-output.s3.amazonaws.com/<?= $video_name ?>.mp4#t=<?= $video_last_time ?>" type='video/mp4'>
                            <p class="vjs-no-js">
        	                    To view this video please enable JavaScript, and consider upgrading to a web browser that
            	                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		                    </p>        
<?php
                        }
?>
                        
                    </video>
                </div>
                <h3>Description</h3>
				<p>
          			<?= $video['desc']; ?>       
      			</p>
      			<br>
<?php
                    if(isset($exams[$module_id]))
                    {
                        $exam_data = $exams[$module_id];
                        $quiz_id = $exam_data[0]['ID'];

?>
      			<center>
      				<div id="quiz" style="display:none">
						<a class="btn" style="" href="?part=quiz&module_id=<?= $module_id ?>&quiz_id=<?= $quiz_id?>&subscription_id=<?= $subscription_id?>&course_id=<?= $course_id?>">
	    					Take Quiz
  						</a>
						</div>
						<div id="noQuiz" style="display:none">
							<p>Note* You have to <b>finish</b> <b>watching</b> the <b>video</b> to be able to take the quiz.</p>
						</div>
					</center>     			
      			<center>
<?php
                    }
?>
      			<script type='text/javascript'>
      			$(document).ready(function() 
      			{
<?php 						// Check if the user has not watched the video yet.
					if( $videoWatchStatus )
					{
						echo '$("#quiz").show();'; // Show Take quiz button.
					}
					else
					{
						echo '$("#noQuiz").show();'; // Show div message to finish watching the video.
					}
?>			
      			});
      			// Update the video status to finish.
      		   	$('video').on("ended", function() {
	            	$("#noQuiz").hide();
  		   			var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&track_id="+$(this).attr("track-id")+"&status=finish&type=watch_video";
					$.ajax({
					url:url,
		            success:
		            function(data)
		            {
		            	$("#quiz").show(); // Show Take quiz button.
		            }
		            });
      		   	});
      		   	// Update the video time.
      		   	$("video").on("pause", function (e) {
      		   		var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&time=" + e.target.currentTime + "&track_id="+$(this).attr("track-id")+"&video_id=<?=$video_id?>&status=pause&type=watch_video";
					$.ajax({url:url,
		            success:
		            function(data)
		            {
		            	// Don't need to do anything.
		            }
		            });
				});
				// Update video for when they first started playing the video.
	  		   	$("video").on("play", function (e) {
	  		   		// Handle new watch video.
	  		   		if( $("video").attr('track-id') == 0 )
	  		   		{
						var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&status=started&video_id=<?=$video_id?>&type=watch_video";
						$.ajax({url:url,
			            success:
			            function(data)
			            {
			            	var result = JSON.parse(data);
			            	$("video").attr('track-id', result.track_id); // New track ID record.
			            },
			            });
					  	$( this ).off( e ); // Triggers once only.
	  		   		}
				});
      			</script>
<?php
					}
				}
      			

			}
			else
			{ // Error, module does not belong to the course.
				echo 'Error: This module does not belong to this course.';
			}
		}
		else
		{
			echo "Error: You do not have access to this course";
		}
	}
	else
	{ // Incorrect parameters.
		echo "Sorry but you have an invalid request. Please contact the site administrator.";
	}
?>