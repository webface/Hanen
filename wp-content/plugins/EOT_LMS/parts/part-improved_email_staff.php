<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>   
    <?= CRUMB_ADMINISTRATOR ?>   
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_EMAIL_YOUR_STAFF ?>  
    <?= CRUMB_SEPARATOR ?> 
     <span class="current">Mail Staff</span> 
</div>

<?php
// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess(); 

if (isset($_REQUEST['target']) && isset($_REQUEST['subscription_id']))
{
	$subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // the subscription ID
   	$target = filter_var ( $_REQUEST['target'], FILTER_SANITIZE_STRING );

    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
		if(current_user_can( "is_director" ))
		{

			// Message is sent. It is processed in my_pre_save_post_sendMassMail function in eot_functions.php
			if( isset($_REQUEST['sent']) )
			{
				echo '<h1 class="article_page_title">' . __("Message Sent", "EOT_LMS") . '</h1>';
				echo '<p>' . __("Your message has been succesfully sent. The subject and body template has been saved for the next time you want to email your staff.", "EOT_LMS") . '</p>';
			}
			else
			{
				// Display the ACF form.
				global $current_user;
				$user_id = (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] > 0) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT)  : $current_user->ID; // Wordpress user ID // $_REQUEST['user_id'] is verified in verifyUserAccess().
				$wp_user = get_user_by( 'ID', $user_id ); // WP User
        $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
		  	$portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
		  	$first_name = get_user_meta($user_id, "first_name", true);	// First name
		  	$last_name  = get_user_meta($user_id, "last_name", true);	// Last name
				$full_name = $first_name . " " . $last_name;	// Full name of user in WP
				$email_address = $wp_user->user_email;// Email address in wordpress db wp_users 
		  	$data = compact ("org_id");
		  	$courses = getCourses(0,$org_id, 0); // All the courses that are published cause draft courses cant have staff enrolled.
        //$response = getEotUsers($org_id); // Lists of users in the org
        $response = getUsersInSubscription($subscription_id);
				$users = array(); // Lists of users
				$incomplete_statuses = array ('not_started', 'in_progress', 'failed'); // the statuses that an incomplete user has
				$complete_statuses = array ('completed', 'passed'); // the statuses that an incomplete user has
                $complete_users_email = array(); // Lists of email address that has been processed.
		  		$courses_with_num_enrollments = array(); // Courses information in associative array. which includes the ID, Name, and the number of enrollments.
                $c = isset($_REQUEST['recipient']) ? filter_var($_REQUEST['recipient'], FILTER_SANITIZE_NUMBER_INT) : 0;
				// make sure we have some users in this portal
			    if($response['status'] == 1)
			    {
			      	if($courses) // check that there are published courses
				{
				  		/*
				  		 * Filter users base on their selected option. 
				  		 */
                                        if($target == "select-staff" || $target == "all")
                                        {
                                                $users = filterUsersMassMail($response['users']); // filter out and return an array of learner user types.
                                        }
                                        else if($target == "incomplete")
                                        {
				        	// Get all users who has incomplete enrollments.
				        	foreach($courses as $course)
				        	{
					      		$enrollments = getEnrollments($course->ID, 0,0, false); // All enrollments in the courses 
								if( $enrollments )
								{
							        foreach ($enrollments as $enrollment) 
							        {
						        		// Avoid duplicate users
						        		if(!in_array($enrollment['email'], $complete_users_email) 
						        			&& in_array($enrollment['status'], $incomplete_statuses))
						        		{
											$user = array (
												'id' => $enrollment['user_id'],
												'first_name' => get_user_meta($enrollment['user_id'], 'first_name', true),
                                                                                                'last_name' => get_user_meta($enrollment['user_id'], 'last_name', true),
												'email' => $enrollment['email']
											);
											array_push($users, $user);
						        			array_push($complete_users_email, $enrollment['email']);	
								        }
							        }
						    	}
				        	}
				        }
				        else if($target == "completed")
				        {
				        	$incomplete_users = array(); // Users with incomplete course.
				        	$completed_users = array(); // Users with complete course.
				        	// Get all users who has incomplete enrollments.
				        	foreach($courses as $course)
				        	{
					      		$enrollments = getEnrollments($course->ID, 0,0, false); // All enrollments in the courses 
                                                        if( $enrollments )
                                                        {
									// Combine course and enrollments.
							        foreach ($enrollments as $enrollment) 
							        {
										// Lists of users who haven't completed their enrollment.
							        	if(in_array($enrollment['status'], $incomplete_statuses))
									{
                                                                                $user = array (
                                                                                        'id' => $enrollment['user_id'],
                                                                                        'first_name' => get_user_meta($enrollment['user_id'], 'first_name', true),
                                                                                        'last_name' => get_user_meta($enrollment['user_id'], 'last_name', true),
                                                                                        'email' => $enrollment['email']
                                                                                );
                                                                                array_push($incomplete_users, $user);
									}
									else if (in_array($enrollment['status'], $complete_statuses))
									{
                                                                                $user = array (
                                                                                        'id' => $enrollment['user_id'],
                                                                                        'first_name' => get_user_meta($enrollment['user_id'], 'first_name', true),
                                                                                        'last_name' => get_user_meta($enrollment['user_id'], 'last_name', true),
                                                                                        'email' => $enrollment['email']
                                                                                );
                                                                                array_push($completed_users, $user);
									}
							        }
						    	}
				        	}
					        // Make sure that all of their enrollments are completed.
					        foreach ($completed_users as $completed_user) 
					        {
					        	// make sure the user does not have any courses that are incomplete, and its not a duplicate that we already added into users
					        	if( !in_array($completed_user, $incomplete_users) && !in_array($completed_user, $users) )
					        	{
									array_push($users, $completed_user);
					        	}
					        }		        	
				        }
				        else if($target == "nologin")
				        {
							$filtered_users = filterUsersMassMail($response['users']); // filter out and return an array of learner user types.
				        	foreach ($filtered_users as $user) 
				        	{
				        		if($user['sign_in_count'] == 0)
				        		{
				        			array_push($users, $user);
				        		}
				        	}
				        }
				        else if($target == "staff-passwords")
				        {
							$users = filterUsersMassMail($response['users']); // filter out and return an array of learner user types.
				        }
				        else if ($target == "all-course" || $target == 'course-passwords')
				        {
					  		foreach ($courses as $course) 
					  		{
					  			$users_in_course = array();
					  			// Get enrollments for each course.
						 		$enrollments = getEnrollments($course->ID, 0,0, false); // All enrollments in the courses 
						 		if($enrollments)
						 		{
							 		$course_info['id'] = $course->ID; // The course ID
						 			$course_info['name'] = $course->course_name; // The course name
						 			$course_info['num_enrollments'] = count($enrollments); // Number of enrollments in the course.
						 			foreach ($enrollments as $enrollment) 
							        {
										$user = array (
											'id' => $enrollment['user_id'],
											'first_name' => get_user_meta($enrollment['user_id'], 'first_name', true),
											'last_name' => get_user_meta($enrollment['user_id'], 'last_name', true),
											'email' => $enrollment['email']
										);
										array_push($users_in_course, $user);
							        }
						 			$course_info['users'] = $users_in_course;
						 			array_push($courses_with_num_enrollments, $course_info);
						 		}
					  		}		        	
				        }
				  	}
					else if($courses == null)
				    {
				   		// No course.
				      	echo __("You do not have any published courses. You must publish a course in order to be able to enroll staff into said course.", "EOT_LMS");
				    }
				    else
				    {
				    	// Could not find the fault
				     	$error_message =  (isset($courses['message'])) ? $courses['message'] : __("Could not find the fault.", "EOT_LMS");
				      	$error_message .= "<br/> " . __("Please contact the administrator.", "EOT_LMS");
				   		echo "<br/>There is an error in getting the courses: <br/>" . $error_message;
				   	}
			    }
			    else if($response['status'] == 0)
			    {
			    	// Error message in getUsers(); ie. no users
			        echo $response['message'];
			    }	
		?>
									
		<script type="text/javascript">
		/**
		 * Keep track of which user and group ids the user wants to send to
		 */             
		var ids = new Array();
		var target = "<?= $_REQUEST['target']?>";
		/**
		 * On document ready
		 */             
		jQuery( function($){   
		  $('a[rel*=facebox]').facebox(); // Enable facebox
		  // Add a loading facebox before sending a message.
		  $('input[value="Send Message"]').on("click", function() {
		  	load('load_email_send');
		  });
		  // Initial load. Hide Divs for displaying message later.
		  $('.error').hide();     
		  $('.hidden').hide();
		  $('.staff-list').hide();
		  $('.email-form').hide();
		  $('.email-message-sent').hide();


		  if (target=="all") {
		    $('div.groups').hide(); 
		    $('div.staff').css("width","100%").show();   
		    $('#message_staff_members').show();   
		  }
		  else if (target=="all-course") {
		    $('div.groups').css("width","100%").show(); 
		    $('div.staff').hide();   
		    $('#message_staff_members').show();
		  }
		  else if (target=="nologin") {
		    $('div.groups').hide();
		    $('div.staff').css("width","100%").show();  
		    $('#message_yet_to_login').show();       
		  }
		  else if (target=="incomplete") {
		    $('div.groups').hide();
		    $('div.staff').css("width","100%").show();  
		    $('#message_incomplete_complete').show();       
		  }
		  else if (target=="completed") {
		    $('div.groups').hide();
		    $('div.staff').css("width","100%").show();    
		    $('#message_incomplete_complete').show();     
		  }
		  else if (target=="staff-passwords") {
		    $('div.groups').hide(); 
		    $('div.staff').css("width","100%").show();         
		    $('#message_staff_password').show();   
		  }
		  else if (target=="course-passwords") {
		    $('div.staff').hide(); 
		    $('div.groups').css("width","100%").show();                  
		    $('#message_group_password').show();   
		  }
		  else if (target=="select-staff") { 
		    $('div.groups').hide(); 
		    $('div.staff').css("width","100%").show();   
		    $('#message_staff_members').show();   
		  }

		  /**
		   * User has chosen recipients, go to compose mail section
		   */                 
		  $('#goto-email-form').bind('click', function() {
		    var staffSelected = $('input:checked.staff');
		    var groupsSelected = $('input:checked.groups');
		    var userids = new Array();
		    // No staff or course selected. Show an error message.
		    if (staffSelected.length==0 && groupsSelected.length==0) {
		      $('.error').show();
		      $('.error').focus();
		      return;
		    }
		    // Show div for lists of staff
		    else if (staffSelected.length==0) {
		      $('#staff-box').hide();
		      $('#group-box').css('width','100%').show();
		    }
		    // Show div for lists of course
		    else if (groupsSelected.length==0) {
		      $('#group-box').hide();
		      $('#staff-box').css('width','100%').show();
		    }
		    // Inject the selected staff ids into ACF Form
		    var users_info = new Array();
		    if (staffSelected.length!=0) {
		    	for (var i=0; i<staffSelected.length; i++) {
					ids[i] = staffSelected[i].value; // add the staff ids into ids array
					$('#staff-'+ids[i]).show(); // display the selected staff
		    		users_info.push(staffSelected[i].getAttribute('user')); // all all the json encoded user info into users_info array
		    	}
		    	$('<input type="text" />').attr({
		    		style: "display:none",
		    		id: "users_info",
		    		value: "[" + users_info + "]",
				    name: "users_info"
				}).appendTo("form.acf-form");
		    }

//		    var users_info = new Array();
			var groupBox = $('#group-box ul');
		    // Inject the selected course ids into ACF Form, and display the recipients.
		    if (groupsSelected.length!=0) {
			    // Display the staff of the selected course
			    for (var i=0; i<groupsSelected.length; i++) {
			      	var users = JSON.parse(groupsSelected[i].getAttribute('users'));
					// Goes to each users of selected course. Filtered unique users.
			      	for(var x=0; x<users.length; x++) {
						if ($.inArray(users[x].id,userids)==-1) {
							users_info.push(JSON.stringify(users[x]));
							groupBox.append('<li>'+users[x].first_name+' '+users[x].last_name+' ('+users[x].email+')');
							userids.push(users[x].id);
						}
			    	}
			    }
		    	$('<input type="text" />').attr({
		    		style: "display:none",
		    		id: "users_info",
		    		value: "[" + users_info + "]",
				    name: "users_info"
				}).appendTo("form.acf-form");
		    }
		    $('.email-select-users').hide();
		    $('.email-form').slideDown(300);  
		  });

		  	// Check all the checkbox
			$("#select_all_btn").click(function() {
				$('input[type="checkbox"]').prop("checked", true);
			});

			// Un-Check all the checkbox
			$("#diselect_all_btn").click(function() {
				$('input[type="checkbox"]').removeAttr("checked");
			});

			/**
		 	 * User wants an example of using the tags
		     */
			$('#view-example').bind('click', function() {
				$(this).html($('#tag-example').html()).css({"color":"inherit","text-decoration":"none"});  
			});                       
		});  
		</script>
		    <!--hidden field containing the tag example-->
		  	<section class="hidden" id="tag-example">
			    <?= __("When an email is sent, the message is sent to each recipient one at a time, and the following phrases are changed to the individual recipient's information one at a time:", "EOT_LMS"); ?>
			    <ul>
					<li>%%name%% <?= __("is replaced by each staff's name", "EOT_LMS"); ?></li>        
					<li>%%email%% <?= __("is replaced with each staff's email (what they use to login with)", "EOT_LMS"); ?></li>
					<li>%%password%% <?= __("is replaced by each staff's new password link", "EOT_LMS"); ?></li>
					<li>%%your_name%% <?= __("is replaced by your name", "EOT_LMS"); ?></li>
					<li>%%logininfo%% <?= __("is replaced by the login info(username/password) of the staff member who the email is sent to.", "EOT_LMS"); ?></li>
					<li>%%directorname%% <?= __("is replaced with your name.", "EOT_LMS"); ?></li>
					<li>%%campname%% - <?= __("is replaced with your camp's name.", "EOT_LMS"); ?></li>
					<li>%%numvideos%% - <?= __("is replaced with the number of videos in our library.", "EOT_LMS"); ?></li>
			    </ul>
			    <strong><?= __("For example,", "EOT_LMS"); ?></strong><br />
			    <?= __("If you were sending an email to Joe and Sally which you wanted to turn out like this:", "EOT_LMS"); ?><br><br>
			    <?= __("Message to Joe", "EOT_LMS"); ?>
			    <div style="width:120px;border-width:2px;border-style:dashed;border-color:grey;padding:7px;background-color:#99FFDD;">
			      <?= __("Dear Joe,", "EOT_LMS"); ?><br>
			      <?= __("Have a fun summer!", "EOT_LMS"); ?>
			    </div><br>      
			    <?= __("Message to Sally", "EOT_LMS"); ?>
			    <div style="width:120px;border-width:2px;border-style:dashed;border-color:grey;padding:7px;background-color:#99FFDD;">
			      <?= __("Dear Sally,", "EOT_LMS"); ?><br>
			      <?= __("Have a fun summer!", "EOT_LMS"); ?>
			    </div><br>  
			    <?= __("You would", "EOT_LMS"); ?> <strong><?= __("enter it like this:", "EOT_LMS"); ?></strong>
			    <div style="width:120px;border-width:2px;border-style:dashed;border-color:grey;padding:7px;background-color:#99FFDD;">
			      <?= __("Dear", "EOT_LMS"); ?> %%name%%,<br>
			      <?= __("Have a fun summer!", "EOT_LMS"); ?>
		    	</div>
		  	</section>
			<!--first step of email process: selecting recipients-->
			<div class="email-select-users">
			<h1 class="article_page_title"><?= __("Mail Staff", "EOT_LMS"); ?></h1>          
			<div class="msgboxcontainer">
			    <div class="msg-tl">
			      <div class="msg-tr"> 
			        <div class="msg-bl">
			          <div class="msg-br">
			            <div class='msgbox'>


		<?php 	
							// check if we have an error and if so display error message.
							if ($target == "all-course" || $target == 'course-passwords')
							{
								// check for existing courses
								if ( count($courses_with_num_enrollments) <= 0 )
								{
									switch ($target)
									{
										case 'all-course':
											echo __("ERROR: It appears that you do not have any users enrolled in published courses. Please publish your course first, then enroll your staff in this course to be able to email them.", "EOT_LMS");
											break;
										case 'course-passwords':
											echo __("ERROR: It appears that you do not have any users enrolled in published courses. Please publish your course first, then enroll your staff in this course to be able to email them.", "EOT_LMS");
											break;
										default:
											echo __("ERROR: there was a problem with your request. Please go back and try again: ", "EOT_LMS") . CRUMB_EMAIL_YOUR_STAFF;
									}	
									return;						
								}
								else
								{
				            		echo __("You have selected to send this email to <b> All Staff</b> of the following Course(s): ", "EOT_LMS");
								}
							}
							else
							{
								// check for existing users
								if( count($users) <= 0 )
								{
									switch ($target)
									{
										case 'all':
											echo __("ERROR: It appears that you do not have any staff. Please add staff members first before you can email them.", "EOT_LMS");
											break;
										case 'select-staff':
											echo __("ERROR: It appears that you do not have any staff. Please add staff members first before you can email them.", "EOT_LMS");
											break;
										case 'incomplete':
											echo __("ERROR: None of your staff have incomplete courses.", "EOT_LMS");
											break;
										case 'completed':
											echo __("ERROR: None of your staff have completed all of their courses.", "EOT_LMS");
											break;
										case 'nologin':
											echo __("ERROR: All of your staff have logged in previously.", "EOT_LMS");
											break;
										case 'staff-passwords':
											echo __("ERROR: It appears that you do not have any staff. Please add staff members first before you can email them.", "EOT_LMS");
											break;
										default:
											echo __("ERROR: there was a problem with your request. Please go back and try again: ", "EOT_LMS") .CRUMB_EMAIL_YOUR_STAFF;
									}
									return;
								}
								else
								{
				            		echo __("Select the", "EOT_LMS") . ' <b>' . __("staff members", "EOT_LMS") . '</b> ' . __("that you would like this message to go to.", "EOT_LMS");
								}
							}
		?>

		            	</div> 
		              </div>
			        </div>
			      </div>
			    </div>
			</div> 
			<div class="error" style="display:none">      
				<div class="errorboxcontainer" >
				    <div class="error-tl">
				      <div class="error-tr"> 
				        <div class="error-bl">
				          <div class="error-br">
				            <div class='errorbox'><?= __("Please select staff to mail to", "EOT_LMS"); ?></div>              </div>
				        </div>
				      </div>
				    </div>
			  	</div>
			  </div>
			<a id="select_all_btn" class="btn" style="margin-left: 33px;">
				<?= __("Select All", "EOT_LMS"); ?>
			</a>
			<a id="diselect_all_btn" class="btn" style="margin-left: 10px">
				<?= __("Diselect All", "EOT_LMS"); ?>
			</a>
			<br>
			<form action="" onsubmit="return false" >
			  <div style="display:block;padding:3px;overflow:auto;">
			    <div class="staff" style="display:none;width:40%;float:left;">
			      <div style="font-weight:bold;border-style:solid;border-bottom:1px solid gray;width:175px;margin-left:15px;padding-left:20px; display:none"><?= __("Staff", "EOT_LMS"); ?></div>
			      <ol>
		<?php
					if($target == "select-staff" || $target == "all" || $target == "staff-passwords" || $target == "nologin" || $target == "completed" || $target == "incomplete")
					{
						// Display users in checkbox.		      	 
		                foreach($users as $user)
		                {
		            		$name = $user['first_name'] . " " . $user['last_name']; // LU User full name
			                $user_id = $user['id']; // LU User ID
			                $email = $user['email'];

			                // remove single quotes before json encoding it below
			                $user['first_name'] = str_replace("'", "", $user['first_name']);
			                $user['last_name'] = str_replace("'", "", $user['last_name']);
		?>
					      	<li>
				      			<input type="checkbox"   
			      					class="staff" 
			      					name="id" 
			      					value="<?= $user_id ?>"
			      					user='<?= json_encode($user); ?>'
			      					<?= ($target == "all" || $target == "incomplete" || $target == "completed" || $target == "nologin" || $c == $user_id) ? 'checked' : '' ?> /> <?= $name ?> (<?= $email ?>)
				      		</li>
		<?php
		                }
					}
		?>        
		  			</ol>
			    </div>
			    <div class="groups" style="display:none;width:60%;float:right;">
			      <div style="font-weight:bold;border-style:solid;border-bottom:1px solid gray;width:175px;margin-left:15px;padding-left:20px; display:none"><?= __("Staff Groups", "EOT_LMS"); ?></div>
		      		<ol>
<?php
					if($target == "all-course" || $target == "course-passwords")
					{
		      			foreach ($courses_with_num_enrollments as $key => $course) 
		      			{
		      				$users_escaped = array(); // user names without single quotes
		      				// check for single quotes in users list
		      				if (isset($course['users']) && count($course['users']) > 0)
		      				{
				                // get rid of all the single quotes from user names
				                foreach ($course['users'] as $key2 => $user2)
				                {
									$user2['first_name'] = str_replace("'", "", $user2['first_name']);
									$user2['last_name'] = str_replace("'", "", $user2['last_name']);
									$users_escaped[] = $user2;
				            	}
		      				}
?>
		      				<li>
		      					<input type="checkbox" 
		      						class="groups" 
		      						name="group_id" 
		      						id="g_<?= $course['id']?>" 
		      						value="<?= $course['id']?>" 
		      						users='<?= json_encode($users_escaped); ?>'
		      						<?= ($target == "all" || $c == $course['id']) ? 'checked' : '' ?>>
		      						<label for="g_<?= $course['id']?>"><?= $course['name']?><span class="small"><b>(<?= $course['num_enrollments'] ?> Staff)</b></span></label>
		  					</li>
<?php
		      			}
		      		}
?>
			 			          
						</ol>
			    </div>
			  </div>
			  	<input type="submit" value="Next" id="goto-email-form" class="btn" style="margin-left: 33px">
			</form>
			</div>
			<!--second step of email process: composing the email-->   
			<div class="email-form" style="display:none">
				<h1 class="article_page_title"><?= __("Compose Message", "EOT_LMS"); ?></h1>    <p>
				  <?= __("Compose your e-mail message to your staff.", "EOT_LMS"); ?>
				</p>

				<table class="">
					<tr>
		        <div class="msgboxcontainer " >
					<div class="msg-tl">
					  <div class="msg-tr"> 
					    <div class="msg-bl">
					      	<div class="msg-br">
					        	<div class='msgbox'><?= __("To personalize emails to your staff by including their name, email, or password or a combination of those, place the phrases %%name%%, %%email%%, %%password%%, %%campname%%, %%directorname%%, and %%your_name%% where you would like that information to appear in the body.", "EOT_LMS"); ?><br /><br /><span id="view-example" style="text-decoration:underline;color:gray"><?= __("Click Here to Get More Info", "EOT_LMS"); ?></span></div>              
				        	</div>
					    </div>
					  </div>
					</div>
					</div>
					      </tr>
					<tr>
					  <td class="label">
					    <?= __("From", "EOT_LMS"); ?>
					  </td>
					  <td class="value">
					    <?= $full_name ?> <span class="small"><?= $email_address ?></span>
					  </td>
					</tr>
					<tr><td><br /></td></tr>
				</table>


		<?php
				// Display the compose message form	
				the_content();

				echo '<div id="message_staff_members" style="display:none">';
					acf_form(
					array(
								'field_groups' => array(ACF_COMPOSE_MESSAGE_STAFF_MEMBERS), // The post id for ACF compose message staff members
								'post_id' => 'user_' . $user_id,
								'return' => '?part=improved_email_staff&sent=true&target=improved_email_staff&subscription_id=' . $subscription_id . "&user_id=" . $user_id,
								'updated_message' => __(__("We are sending your message.", "EOT_LMS"), 'acf'),
								'submit_value' => __(__("Send Message", "EOT_LMS"), 'acf'),
					));
				echo '</div>';
				echo '<div id="message_incomplete_complete" style="display:none">';
					acf_form(
					array(
								'field_groups' => array(ACF_COMPOSE_MESSAGE_INCOMPLETE_COMPLETE), // The post id for ACF compose message Incomplete and complete
								'post_id' => 'user_' . $user_id,
								'return' => '?part=improved_email_staff&sent=true&target=improved_email_staff&subscription_id=' . $subscription_id . "&user_id=" . $user_id,
								'updated_message' => __(__("We are sending your message.", "EOT_LMS"), 'acf'),
								'submit_value' => __(__("Send Message", "EOT_LMS"), 'acf'),
					));
				echo '</div>';
				echo '<div id="message_yet_to_login" style="display:none">';
					acf_form(
					array(
								'field_groups' => array(ACF_COMPOSE_MESSAGE_YET_TO_LOGIN), // The post id for ACF compose yet to login
								'post_id' => 'user_' . $user_id,
								'return' => '?part=improved_email_staff&sent=true&target=improved_email_staff&subscription_id=' . $subscription_id . "&user_id=" . $user_id,
								'updated_message' => __(__("We are sending your message.", "EOT_LMS"), 'acf'),
								'submit_value' => __(__("Send Message", "EOT_LMS"), 'acf'),
					));
				echo '</div>';
				echo '<div id="message_staff_password" style="display:none">';
					acf_form(
					array(
								'field_groups' => array(ACF_COMPOSE_MESSAGE_STAFF_PASSWORD), // The post id for ACF compose message staff members
								'post_id' => 'user_' . $user_id,
								'return' => '?part=improved_email_staff&sent=true&target=improved_email_staff&subscription_id=' . $subscription_id . "&user_id=" . $user_id,
								'updated_message' => __(__("We are sending your message.", "EOT_LMS"), 'acf'),
								'submit_value' => __(__("Send Message", "EOT_LMS"), 'acf'),
					));
				echo '</div>';
				echo '<div id="message_group_password" style="display:none">';
					acf_form(
					array(
								'field_groups' => array(ACF_COMPOSE_MESSAGE_COURSE_PASSWORD), // The post id for ACF compose messagegroups password
								'post_id' => 'user_' . $user_id,
								'return' => '?part=improved_email_staff&sent=true&type=individual&target=improved_email_staff&subscription_id=' . $subscription_id . "&user_id=" . $user_id,
								'updated_message' => __(__("We are sending your message.", "EOT_LMS"), 'acf'),
								'submit_value' => __(__("Send Message", "EOT_LMS"), 'acf'),
					));
				echo '</div>';
			?>

				<h1 class="article_page_title">Recipients</h1>    
					<div style="display:inline;padding:3px;overflow:auto;">
						<div class="staff" id="staff-box" style="display:none;width:50%;float:left;">
				    		<div style="font-weight:bold;border-style:solid;border-bottom:1px solid gray;width:575px;margin-left:15px;padding-left:17px;">The following staff members will receive this email</div>
				    			<ul class="notop">
		<?php
						// Display only the users and skip the admins.		      	 
			            foreach($users as $user)
			            {
			                // we dont want to display super admins in the user list so skip the loop if its an admin
			        		$name = $user['first_name'] . " " . $user['last_name']; // LU User full name
			                $user_id = $user['id']; // LU User ID
			                $email = $user['email'];
		?>				
							<li class="staff-list" id="staff-<?= $user_id ?>" ><?= $name ?> (<?= $email ?>)
							</li>
		<?php
		                }
		?>
			    	</ul>      
			    	</div>
					<div class="groups" id="group-box" style="display:none;width:50%;float:right;">
					<div style="font-weight:bold;border-style:solid;border-bottom:1px solid gray;width:575px;margin-left:15px;padding-left:17px;">The following staff members will receive this email</div>
					<br>
					<ul class="notop">
					</div>
				</div>
			</div>
		  	<!--third step of email process: email sent confirmation-->
		  	<div class="email-message-sent" style="display:none">
		    	<h1 class="article_page_title">Message Sent</h1>       
		  	</div>
		<?php
			} // end else
		}
	    else
	    {
	        echo "Unauthorized!";
	    }
	}
	else
	{
	    echo "subscription ID does not belong to you";
	}
}
// Invalid request parameters.
else
{
	echo "Error: Invalid parameters.";
}
?>
