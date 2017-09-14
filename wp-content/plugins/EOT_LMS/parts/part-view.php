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
			if(verify_module_in_course($module_id, $course_id, 'video'))
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
				$module_video_resources = getResourcesInModuleInCourse($course_id, $module_id, 'video'); // get the video resources in this module
                $resources_exam = getResourcesInCourse($course_id, "exam");
                $resources_docs = array_merge(getResourcesInCourse($course_id, "doc"),getResourcesInCourse($course_id, "link"));
                $my_resources = array();
                
                foreach ($resources_docs as $resource) {// get the resources for this module
                    if($resource['mid'] == $module_id)
                    {
                        array_push($my_resources, $resource);
                    }
                }
//d($resources_docs,$my_resources);
                
                $exams = array();
                foreach($resources_exam as $exam){
                    if(isset($exams[$exam['mid']]))
                    {
                        array_push($exams[$exam['mid']], array('ID'=>$exam['ID'],'name'=>$exam['name']));
                    }
                    else
                    {
                    	$exams[$exam['mid']] = array();
                    	array_push($exams[$exam['mid']], array('ID'=>$exam['ID'],'name'=>$exam['name']));
                    }
                }
                               // d($module_video_resources);
				if( isset( $module_video_resources ) )
				{
					foreach ($module_video_resources as $key => $video)
					{
						$video_name = $video['name'];
						$video_id = $video['ID']; // The video ID
						$video_record = getTrack($user_id, $video_id);
						$track_id = ( count($video_record) > 0) ? $video_record['ID'] : 0;
//d($track_id,$video_id);
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
                            <source src="https://eot-output.s3.amazonaws.com/<?= $subLanguage ? $video_record['spanish'] : $video['shortname_medium'] ?>.mp4" type='video/mp4'>
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

                            <source src="https://eot-output.s3.amazonaws.com/<?= $video_name ?>.mp4" type='video/mp4'>
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
                        echo '<h3>Quiz</h3>';
                        $exam_data = $exams[$module_id];
                        $quiz_id = $exam_data[0]['ID'];

?>
      			<center>
      				<div id="quiz" style="display:none">
                                    <span class="loadingQuiz" style="display:none"><i class="fa fa-spinner fa-pulse fa-2x"></i>loading quiz</span>
                                    <a class="btn takeQuiz" style="display:none" href="?part=quiz&module_id=<?= $module_id ?>&quiz_id=<?= $quiz_id?>&subscription_id=<?= $subscription_id?>&course_id=<?= $course_id?>">
                                    Take Quiz
                                    </a>
                                </div>
                                <div id="noQuiz" style="display:none">
                                        <p>Note* You have to <b>finish</b> <b>watching</b> the <b>video</b> to be able to take the quiz.</p>
                                </div>
                        </center>     			
<?php
                    }
                    echo '<h3>Resources</h3>';
                    echo '<ul class="inner nobullet">';
                    foreach($my_resources as $resource)
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
		                    
		                        <li><a href="<?= $url ?>"><i class="fa <?= $icon; ?>" aria-hidden="true"></i></a> <?= $resource['name'] ?> - <span class="small"><a href="<?= $url ?>"><?= $action;?></a></span></li>
		                    
<?php
                        }
                        echo '</ul>';
?>
  			<script type='text/javascript'>
                var video_ended = false;
      			$(document).ready(function() 
      			{
<?php 						// Check if the user has not watched the video yet.
//					if( $videoWatchStatus )
//					{
//						echo '$("#quiz").show();'; // Show Take quiz button.
//                                                echo '$(".loadingQuiz").hide();'; // Show Take quiz button.
//                                                echo '$(".takeQuiz").show();';
//					}
//					else
//					{
						echo '$("#noQuiz").show();'; // Show div message to finish watching the video.
//					}
?>			
      			});
                        // Update the video status to finish.
                        $( 'video' ).on('timeupdate',function(event){

                                // Save object in case you want to manipulate it more without calling the DOM
                                $this = $(this);
                                
                                if( this.currentTime > ( this.duration - 30 ) ) {
                                    console.log(this.currentTime);
                                        if(!video_ended)
                                        {
                                                video_ended=true;                            
                                                $("#noQuiz").hide();
                                                $("#quiz").show();
                                                $(".loadingQuiz").show();
                                                $(".takeQuiz").hide();
                                                var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&track_id="+$(this).attr("track-id")+"&status=finish&type=watch_video";
                                                $.ajax({
                                                url:url,
                                            success:
                                            function(data)
                                            {
                                                $(".loadingQuiz").hide(); // Show Take quiz button.
                                                $(".takeQuiz").show();
                                            }
                                            });
                                            
                                    }
                                }

                            });
      			// Update the video status to finish.
//      		   	$('video').on("ended", function() {
//	            	$("#noQuiz").hide();
//  		   			var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&track_id="+$(this).attr("track-id")+"&status=finish&type=watch_video";
//					$.ajax({
//					url:url,
//		            success:
//		            function(data)
//		            {
//		            	$("#quiz").show(); // Show Take quiz button.
//		            }
//		            });
//      		   	});
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