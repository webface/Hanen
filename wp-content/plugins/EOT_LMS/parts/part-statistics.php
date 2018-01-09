<?php
	// Variable declaration
	global $current_user;
	// verify this user has access to this portal/subscription/page/view
	$true_subscription = verifyUserAccess(); 

	$user_id = (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT) : $current_user->ID; // Wordpress user ID
	$org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID	
	$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
	$courses = getCoursesById($org_id, $subscription_id);// All published courses in the portal.
    //$staff_accounts = getEotUsers($org_id); // Staff accounts registered in this portal.
	$staff_accounts = getUsersInSubscription($subscription_id);
	$num_staff_completed_assignment = 0; // Number of staff who have completed all their assignment
	$num_staff_signed_in = calculate_logged_in($org_id); // Number of staff who signed in at least once.
	$learners = '';
	$completed_user_ids = array();
	$incomplete_user_ids = array();
    $num_videos_watched = calculate_videos_watched($org_id);
    $num_resources_downloaded = calculate_resources_downloaded($org_id);
    $num_quizzes_taken=calculate_quizzes_taken($org_id, $subscription_id);
    //d($staff_accounts,$courses);
	//d($courses,$num_videos_watched,$num_quizzes_taken,$num_resources_downloaded);
    // check if we have staff accounts and filter out everyone other than learners
    if( isset($staff_accounts['status']) && $staff_accounts['status'] )
    {
        $users = (isset($staff_accounts['users'])) ? $staff_accounts['users'] : ''; // All users in the portal
        $learners = filterUsers($users, 'learner'); // only the learners
    }
   /* 
	* This will go through all the courses and enrollments and count completed enrollments.	
	*/
	if(isset($courses['status']) && $courses['status'] == 0)
	{
		echo $courses['message']; // Expect to have error message in getting courses.
	}
	else if (!empty($courses))
	{	
		foreach($courses as $course)
		{
			// Do not include the master library.
//			if($course['course_name'] == LE_LIBRARY_TITLE)
//			{
//				continue;
//			}
			$course_id = $course['ID']; // The course ID
                        
			$enrollment = getEnrollments($course_id, 0, $org_id, ''); // All enrollments in the course
                        //d($enrollment);
			if($enrollment)
			{
				foreach($enrollment as $enroll)
				{
					// check this user's status to see if complete/passed and not already in the completed user array to dedupe.
					if ($enroll['status'] == 'completed' || $enroll['status'] == 'passed')
					{
						// user passed so add them to the completed_user_ids array
						$completed_user_ids[] = $enroll['user_id'];
					}
					else // user failed or didnt start
					{
						$incomplete_user_ids[] = $enroll['user_id'];
					}
				}
			}
		}

		// get only uniques for each array
		$unique_completed_user_ids = array_unique($completed_user_ids);
		$unique_incomplete_user_ids = array_unique($incomplete_user_ids);
		// only count the user who are not in the incomplete array
		$num_staff_completed_assignment = count(array_diff($unique_completed_user_ids, $unique_incomplete_user_ids));
	}
   /* 
	* This will go through all the staff accounts in the portal and count who signed in more than once.
	*/
	foreach($learners as $staff)
	{
//		$sign_in_count = $staff['sign_in_count']; // Staff # of login
//		if($sign_in_count > 0)
//		{
//			$num_staff_signed_in++;
//		}
	}

	// This calculates the percentage for the progressbars.
	$percentage_logged_in = (count($learners) > 0) ? ($num_staff_signed_in / count($learners) * 100) : 0; // The percentage of staff who logged in once.
	$percentage_completed_assignment = (count($learners) > 0) ? ($num_staff_completed_assignment / count($learners) * 100) : 0; // The percentage of staff who completed all their assignments.
?>
<?php
  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
  {

      if(isset($true_subscription['status']) && $true_subscription['status'])
      {
        if(current_user_can( "is_director" ))
        {
			if(isset($_REQUEST['forward']) && $_REQUEST['forward'] == 1)
			{
				$page_title = __("Detailed Stats Tutorial", "EOT_LMS");
?>
				<div class="breadcrumb">
				 	<?= CRUMB_DASHBOARD ?>         
				  	<?= CRUMB_SEPARATOR ?>
				  	<?= CRUMB_STATISTICS ?>          
				  	<?= CRUMB_SEPARATOR ?>  
					<span class="current"><?= __("Detailed Stats", "EOT_LMS") ?></span>     
				</div>
				<div class="smoothness">
					<h1 class="article_page_title"><?= $page_title; ?></h1>
		            <script type="text/javascript" src="https://vjs.zencdn.net/5.8.8/video.js?ver=5.8.8"></script>
		            <div id='tutorial_video'>
		                <video id="my-video" class="video-js vjs-default-skin" preload="auto" width="650" height="366" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/11/Tutorial-thumbnail-e1478882719523.png" data-setup='{"controls": true}'>
		                    <source src="https://<?= AWS_S3_BUCKET ?>.s3.amazonaws.com/tutorial_Analyze_Course_Statistics2.mp4" type='video/mp4'>
		                    <p class="vjs-no-js">
		                        <?= __("To view this video please enable JavaScript, and consider upgrading to a web browser that", "EOT_LMS") ?>
		                        <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __("supports HTML5 video", "EOT_LMS") ?></a>
		                    </p>        
		                </video>
		            </div>
		            <br><br><br><br>
		            <a href="?part=statistics&subscription_id=<?= $subscription_id ?>&forward=2" class="statsbutton" target="_blank"><?= __("View Detailed Stats Now!", "EOT_LMS") ?></a>
<?php
			}
			else if(isset($_REQUEST['forward']) && $_REQUEST['forward'] == 2)
			{
				$link = redirectToLU();
				wp_redirect( $link );
				exit;
			}
			else
			{
				$page_title = __("View Statistics", "EOT_LMS") . ' ' . SUBSCRIPTION_YEAR;
				if (current_user_can("is_uber_manager") || current_user_can("is_umbrella_manager"))
				{
					$page_title .= ' - ' . get_the_title($org_id);
				}
?>
		<div class="breadcrumb">
		 	<?= CRUMB_DASHBOARD ?>         
		  	<?= CRUMB_SEPARATOR ?>       
			<span class="current"><?= __("View Statistics", "EOT_LMS") ?> <?= SUBSCRIPTION_YEAR ?></span>     
		</div>
		<div class="smoothness">
			<h1 class="article_page_title"><?= $page_title; ?></h1>
			<div class="msgboxcontainer width-100">
				<div class="msg-tl">
					<div class="msg-tr"> 
						<div class="msg-bl">
							<div class="msg-br">
								<div class="msgbox"><?= __("Here is a summary of your entire staff’s progress, as well as a listing of the courses you have published.", "EOT_LMS") ?> <br><br><?= __("To see individual staff members’ progress in a course, click the “Course Stats” link next to the course title. And to see details of every learner’s performance in all courses, or to download an Excel spreadsheet of course history, click on the green “Detailed Stats” button below.", "EOT_LMS") ?>
								</div>              
							</div>
						</div>
					</div>
				</div>
			</div>
			<h2><?= __("Summary", "EOT_LMS") ?></h2>
			<div class="cell-row">
				<div class="cell-caption">
					<?= __("Staff who have", "EOT_LMS") ?> <b><?= __("completed all their courses", "EOT_LMS") ?></b>
				</div>
				<div class="cell-field number">
					<b>
						<a href="./?part=completedcourses&subscription_id=<?= $subscription_id ?>"><?= $num_staff_completed_assignment ?></a>
					</b>
				</div>
			  	<?= eotprogressbar('12em', $percentage_completed_assignment, true); ?>
			</div>
			<div class="cell-row">
		        <div class="cell-caption">
		        	<b><?= __("Total Staff Accounts", "EOT_LMS") ?></b> <?= __("in this Camp, School or Youth Program", "EOT_LMS") ?>
		    	</div>
		        <div class="cell-field number">
		        	<b><?= count($learners) ?></b>
		        </div>
		  	</div>
		  	<div class="cell-row">
			        <div class="cell-caption">
			        	<?= __("Staff who have", "EOT_LMS") ?> <b><?= __("Logged In", "EOT_LMS") ?></b> <?= __("at least once", "EOT_LMS") ?>
			    	</div>
			        <div class="cell-field number">
			        	<b><?= $num_staff_signed_in ?></b>
			    	</div>
			  	<?= eotprogressbar('12em', $percentage_logged_in, true); ?>
		  	</div>
		  	<div class="cell-row">
			        <div class="cell-caption">
			        	<?= __("Number of", "EOT_LMS") ?> <b><?= __("Videos Watched", "EOT_LMS") ?></b>
			    	</div>
			        <div class="cell-field number">
			        	<b><?= $num_videos_watched ?></b>
			    	</div>
			  	
		  	</div>                        
		  	<div class="cell-row">
			        <div class="cell-caption">
			        	<?= __("Number of", "EOT_LMS") ?> <b><?= __("Quizzes Taken", "EOT_LMS") ?></b>
			    	</div>
			        <div class="cell-field number">
                                    <b><?= $num_quizzes_taken ?></b>
			    	</div>
			  	
		  	</div>
		  	<div class="cell-row">
			        <div class="cell-caption">
			        	<?= __("Number of", "EOT_LMS") ?> <b><?= __("Certificates Conferred", "EOT_LMS") ?></b>
			    	</div>
			        <div class="cell-field number">
			        	<b><?= 0 ?></b>
			    	</div>
			  	
		  	</div>
		  	<div class="cell-row">
			        <div class="cell-caption">
			        	<?= __("Number", "EOT_LMS") ?> of <b><?= __("Resources Downloaded", "EOT_LMS") ?></b>
			    	</div>
			        <div class="cell-field number">
                                    <b><?= $num_resources_downloaded ?></b>
			    	</div>
			  	
		  	</div>                        

<!--  	<div class="cell-row">
		<a class="stats-button" href="">Export Assignment Summary to Excel</a>
	</div>-->
	
			<h2><?= __("Courses", "EOT_LMS") ?></h2>
            <div class="bs">
            <table class="table table-striped table-bordered">            
				<tbody>
<?php
				if (!empty($courses))
				{
					foreach($courses as $key => $course) 
					{
						$course_name = $course['course_name']; // The course name
			        	// Do not display the cloned leadership essential
//				        if($course_name == LE_LIBRARY_TITLE)
//				        {
//				          continue;
//				        }
?>
						<tr>
							<td><?= $course_name ?></td>
							<td><a href="?part=coursestats&course_id=<?= $course['ID'] ?>&subscription_id=<?= $subscription_id ?>&user_id=<?= $user_id ?>"><?= __("Course Stats", "EOT_LMS") ?></a></td>
                            <td><a href="?part=coursestaffstats&course_id=<?= $course['ID'] ?>&subscription_id=<?= $subscription_id ?>&user_id=<?= $user_id ?>"><?= __("Individual Stats", "EOT_LMS") ?></a></td>
							<!--<td><a href="#">Download Excel Report</a></td>-->
                        </tr>
<?php
					}
				}
				else
				{
					echo '<h3>' . __("You do not have any published courses at this time", "EOT_LMS") . '</h3>';
				}
	
?>
				</tbody>
			</table>
            </div>
<?php
			}
	    }
        else
        {
          echo __("Unauthorized!", "EOT_LMS");
        }
      }
	  else
	  {
	    echo __("subscription ID does not belong to you", "EOT_LMS");
	  }
  }
  // Could not find the subscription ID
  else
  {
    echo __("Could not find the subscription ID", "EOT_LMS");
  }

?>
</div>