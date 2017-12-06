<?php
	if( isset($_REQUEST['module_id']) && isset($_REQUEST['course_id'])&& isset($_REQUEST['video_id']))
	{
            $module_id = filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT); // The module ID
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
            $video_id = filter_var($_REQUEST['video_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
            $video = get_custom_video($video_id);
            $user_id = get_current_user_id(); // WP User ID
            $video_record = getTrack($user_id, $video_id);
            $track_id = ( count($video_record) > 0) ? $video_record['ID'] : 0;
            $subscription = getSubscriptionByCourse($course_id);
            $subscription_id = $subscription['ID'];
            // make sure the user has access to this course
		$has_access = verify_student_access($course_id);
		if($has_access) 
		{
			// Check if the module belongs to the course.
//			if(verify_module_in_course($module_id, $course_id, $type = 'video'))
//			{
				// get the module data
				$module = getModule($module_id);
                $resources_docs = array_merge(getResourcesInCourse($course_id, "doc"),getResourcesInCourse($course_id, "link"));
                $my_resources = array();
                
                foreach ($resources_docs as $resource) 
                { 
                	// get the resources for this module
                    if($resource['mid'] == $module_id)
                    {
                        array_push($my_resources, $resource);
                    }
                }
                $resources_exam = getResourcesInCourse($course_id, "exam");
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
?>
<div class="breadcrumb">
	<?= CRUMB_DASHBOARD ?>    
	<?= CRUMB_SEPARATOR ?>  
	<?= CRUMB_MY_LIBRARY ?>    
	<?= CRUMB_SEPARATOR ?>    
	  <span class="current"><?= $module['title'] ?></span> 
</div>
<?php

				
                echo '<h1 class="video_title">' . $video['name'] . '</h1>';

?>

                <div id='player_<?= $video_id; ?>' style='width:665px;height:388px'>
                    <video id="my-video" user-id="<?=$user_id?>" track-id="<?=$track_id?>" class="video-js vjs-default-skin" controls preload="auto" width="665" height="388" poster="<?php echo bloginfo('template_directory'); ?>/images/eot_logo.png" data-setup='{"controls": true}'>

                        <source src="<?= $video['url']?>" type='video/mp4'>
                            <p class="vjs-no-js">
        	                    To view this video please enable JavaScript, and consider upgrading to a web browser that
            	                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		                    </p>        

                    </video>
                </div>

               <script type='text/javascript'>
                   $(document).ready(function(){
                        var agent = navigator.userAgent;
                        var isIphone = ((agent.indexOf('iPhone') != -1) || (agent.indexOf('iPod') != -1)|| (agent.indexOf('iPad') != -1)) ;
                        if (isIphone) 
                        {
                            var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&track_id="+$('#my-video').attr("track-id")+"&status=finish&type=watch_video";
                            $.ajax({
                                url:url,
                                success:
                                function(data)
                                {
                                    // custom video so dont have to watch to take quiz.
                                }
                            });
                        }
                   });

      			// Update the video status to finish.
      		   	$('video').on("ended", function() {
	            	
  		   			var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&track_id="+$(this).attr("track-id")+"&status=finish&type=watch_custom_video";
					$.ajax({
					url:url,
		            success:
		            function(data)
		            {
		            	
		            }
		            });
      		   	});
      		   	// Update the video time.
      		   	$("video").on("pause", function (e) {
      		   		var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&time=" + e.target.currentTime + "&track_id="+$(this).attr("track-id")+"&video_id=<?=$video_id?>&status=pause&type=watch_custom_video";
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
                                    console.log('play');
	  		   		// Handle new watch video.
	  		   		if( $("video").attr('track-id') == 0 )
	  		   		{
						var url =  ajax_object.ajax_url + "?action=updateVideoProgress&user_id=<?= $user_id ?>&module_id=<?= $module_id?>&course_id=<?= $course_id?>&status=started&video_id=<?=$video_id?>&type=watch_custom_video";
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
                                    $url = "/download-file?module_id=$module_id&course_id=$course_id&resource_id=".$resource['ID'];
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
                            if(isset($exams[$module_id]))
                            {
                                echo '<h3>Quiz</h3>';
                                echo '<ul class="inner nobullet">';
                                foreach($exams[$module_id] as $exam)
                                {
                                    ?>
                                        <li><i class="fa fa-question-circle-o"></i><?= $exam['name']?> <a href="?part=quiz&module_id=<?= $module_id ?>&quiz_id=<?= $exam['ID']?>&subscription_id=<?= $subscription_id?>&course_id=<?= $course_id?>">Take Quiz</a></li>
                                        <?php
                                    
                                }
                                echo '</ul>';
                            }
			}
			else
			{ // Error, module does not belong to the course.
				echo 'Error: This module does not belong to this course.';
			}
//		}
//		else
//		{
//			echo "Error: You do not have access to this course";
//		}
	}
	else
	{ // Incorrect parameters.
		echo "Sorry but you have an invalid request. Please contact the site administrator.";
	}
?>