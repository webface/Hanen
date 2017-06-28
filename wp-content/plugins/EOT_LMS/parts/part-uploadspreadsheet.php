		<div class="breadcrumb">
		  <?= CRUMB_DASHBOARD ?>    
		  <?= CRUMB_SEPARATOR ?>     
		  <?= CRUMB_ADMINISTRATOR ?>    
		  <?= CRUMB_SEPARATOR ?>    
		  <?= CRUMB_MANAGE_STAFF_ACCOUNTS ?>
		  <?= CRUMB_SEPARATOR ?>
		    <span class="current">Upload Spreadsheet</span>     
		</div>

<?php

if(isset($_REQUEST['uploadFile']) && $_REQUEST['uploadFile'] == true && isset($_REQUEST['subscription_id']))
	{
		// Ignore user aborts and allow the script
		// to run for 5 minutes (300 seconds)
		ignore_user_abort(true);
		set_time_limit(300);

    	        global $current_user;
		$user_id = $current_user->ID; // Wordpress account user id

		/*****************************************************************
		 * This triggers when a camp director succesfully uploaded a file.
		 *****************************************************************/
		$message = get_field('email_message', 'user_'.$user_id); // The Composed message
	 	$isEmail = get_field('email_user', 'user_'.$user_id); // Boolean indicating whether the user wants to send an email.
	 	$file = get_field('file_uploadspreadsheet', 'user_'.$user_id);
		$fileLink = $file['url']; // The link to the file
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
                if( !current_user_can ('is_director') && !current_user_can ('is_administrator') )
                {
                        wp_die('Sorry, you do not have permisison to view this page.');
                }
		// Check if the file exsist and is a text or csv file.
		else if ( pathinfo($fileLink, PATHINFO_EXTENSION) != 'txt' && pathinfo($fileLink, PATHINFO_EXTENSION) != 'csv')
		{
			echo 'The file needs to be a text or csv file. Please follow the instruction carefuly.<br><br>';
			echo '<a href="?part=uploadspreadsheet&subscription_id='.$subscription_id.'">Go back</a>';
		}
		else if ( !$fileLink )
		{
			// Check if file exsist.
			echo 'Could not find your file. Please upload your text file again.<br><br>';
			echo '<a href="?part=uploadspreadsheet&subscription_id='.$subscription_id.'">Go back</a>';
		}
		else
		{
			// Parse the text file and store them in the $staff_data array.
			$delimiter = (pathinfo($fileLink, PATHINFO_EXTENSION) == 'txt') ? "\t" : ",";
			$fp = fopen($fileLink , 'r');
			$staff_data = array();
			while ( !feof($fp) )
			{
			    $line = fgets($fp, 2048);
			    $data = str_getcsv($line, $delimiter);
			    if(isset($data[2]) && strlen($data[2]) > 0) // check that there is actually an email present and if so push it into the array.
		    	{
		    		array_push ( $staff_data , $data );
		    	}
			}
			array_shift($staff_data); // Delete first element of the array. Delete the headers.
			fclose($fp);
		?>
			<h1 class="article_page_title">Process Report</h1>
			Here is the process report. It could contain error details.
			<br/>
			<br/>
			<table class="bordered">
        		<tbody>
    				<tr class="head">
						<td>
							Row
						</td>
						<td class="name">
							First Name
						</td>
						<td class="name">
							Last Name
						</td>
						<td class="email">
							E-mail
						</td>
						<td class="pw">
							Password
						</td>
						<td class="course">
							Course 1
						</td>
						<td class="course">
							Course 2
						</td>
						<td class="course">
							Course 3
						</td>
						<td class="course">
							Course 4
						</td>
					</tr>
				<?php
					$row_counter = 1;
					$has_error = false; // Boolean indication if the process has an error
					$has_user_error = false; // Boolean indicator for individual user error.
					$org_id = get_org_from_user ($user_id); // Organization id
					$portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
					$data = array( "org_id" => $org_id ); // to pass to our functions below
					$courses_in_portal = getCoursesByOrgId($org_id); // All the published courses in the portal
					$course_names = array_column($courses_in_portal, 'course_name'); // The titles of the modules from the master library
					$email_finish = array(); // Array of email that has been proceseed or finished
					$subscription = getSubscriptions($subscription_id,0,1); // Subscription details
					$staff_credits = $subscription->staff_credits; // The staff credits

                                        // Add upgrade number of staff
                                        $upgrades = getUpgrades ($subscription_id);
                                        if($upgrades)
                                        {
                                            foreach($upgrades as $upgrade)
                                            {
                                                $staff_credits += $upgrade->accounts;
                                            }
                                        }

					$response = getEotUsers($org_id); // All users in this portal
					$directorname =	$current_user->display_name; // the directors name
					$campname =	get_the_title($org_id); // the camp name

					if ($response['status'] == 1)
					{
						$users = $response['users'];
				        $learners = filterUsers($users, 'learner'); // only the learners
					}
					else
					{
						$users = array();
						$learners = array();
					}
					$num_staff = count($learners); // Number of staff for this organization
					/********************************************************************** 
					 * This goes to all the staff specificed in the file
					 * Prints their information and error if occured.
					 **********************************************************************/
                                      //var_dump($staff_data);
					foreach($staff_data as $staff)
					{
						$error_message = ''; // Error message initialization
						// Check if the required tabs are used
						if( count($staff) < 5 )
						{
							$error_message .= '- Missing fields. Make sure you have all the required fields <br>';
						}
						$first_name = isset($staff[0]) ? trim($staff[0]) : ''; // User First Name
						$last_name = isset($staff[1]) ? trim($staff[1]) : ''; // User Last Name
						$email = isset($staff[2]) ? trim($staff[2]) : ''; // User e-mail address
						$password = isset($staff[3]) ? trim($staff[3]) : ''; // User password
						$course_1 = isset($staff[4]) ? trim($staff[4]) : ''; // User Course 1
						$course_2 = isset($staff[5]) ? trim($staff[5]) : ''; // User Course 2
						$course_3 = isset($staff[6]) ? trim($staff[6]) : ''; // User Course 3
						$course_4 = isset($staff[7]) ? trim($staff[7]) : ''; // User Course 4
                                                $first_name = preg_replace("/[.,*;!{ }@#$%^&()+|?\'\"\’\”]/", "", $first_name);
                                                $last_name = preg_replace("/[.,*;!{ }@#$%^&()+|?\'\"\’\”]/", "", $last_name);
				?>
						<tr>
							<td class="center">
								<i><?= $row_counter ?></i>
							</td>
							<td class="name">
								<?= $first_name ?>                    
						  	</td>
							<td class="name">
						  		<?= $last_name ?>                     
						  	</td>
							<td class="email">
								<?= $email ?>                  
						  	</td>
							<td class="pw">
								<?= $password ?>                 
						  	</td>
							<td class="course">
						  		<?= $course_1 ?>                   
						  	</td>
							<td class="course">
						  		<?= $course_2 ?>                  
						  	</td>
							<td class="course">
						  		<?= $course_3 ?>                    
						  	</td>
							<td class="course">
						  		<?= $course_4 ?>                   
						  	</td>
	              		</tr>
				<?php
//                                                if($email===""){
//                                                        $error_message .= '- <b>Email</b> email is missing.<br/>';
//                                                }                                
						// Check if the camp director specificed more than 1 course
						if( $course_1 == '' && $course_2 == '' && $course_3 == '' && $course_4 == '' )
						{
							$error_message .= '- You must specify at least 1 <u><b>Course</b></u> for each user.<br>';
						}
						else
						{	// Check if any of the course exsist on their portals.
							if( !empty($course_1) )
							{
								if(!in_array($course_1, $course_names))
								{
									$error_message .= '- <b>' . $course_1 . '</b> is either not a valid course or is not published yet. <br/>';
								}
							}
							if( !empty($course_2) )
							{
								if(!in_array($course_2, $course_names))
								{
									$error_message .= '- <b>' . $course_2 . '</b> is either not a valid course or is not published yet. <br/>';
								}
							}
							if( !empty($course_3) )
							{
								if(!in_array($course_3, $course_names))
								{
									$error_message .= '- <b>' . $course_3 . '</b> is either not a valid course or is not published yet. <br/>';
								}
							}
							if( !empty($course_4) )
							{
								if(!in_array($course_4, $course_names))
								{
									$error_message .= '- <b>' . $course_4 . '</b> is either not a valid course or is not published yet. <br/>';
								}
							}
						}
                                                                                                //Check if email is empty

						// Check if this e-mail has already been processed
						if(in_array($email, $email_finish))
							$error_message .= '- <b>E-mail address</b> (' . $email . ') is repeated in another row. Is this a duplicate row?<br/>';
						else
							array_push($email_finish, $email);
						// Check if the user password is set && less than 6 characters
						if(!empty($password) && strlen($password) < 5)
						{
							$error_message .= '- <b>Password</b> must be at least 6 characters in length.<br/>';
						}

						// Check if the user has enough credits to add more staff members
						if( $num_staff >= $staff_credits )
						{
							$error_message .= '- Unable to add user. You reached the maximum amount of staff you can have.<br/>';
						}
						$num_staff++;
						// check if there is an error message and display it
						if($error_message)
						{
							$has_error = true;
				?>
							<tr>
		                    	<td></td>
			                    <td class="errors" colspan="12">
		                      		<?= $error_message ?>                    
		                      	</td>
		                  	</tr>
              	<?php
						}
					$row_counter++;
					} // End of foreach
                                        //var_dump($staff_data);
					// no errors. proceed to create accounts.
					//if($has_error == false)
					//{
						/****************************************************************
						 * This process the savings of the user accounts 
						 * into WP User Database and create accounts in LU
						 ****************************************************************/
				        $recepients = array(); // List of recepients
				        $emailError = '';
						foreach($staff_data as $staff)
						{
							$has_user_error = false;
							// Create New Account
							$first_name = $staff[0]; // User first name
							$last_name = $staff[1]; // User last name
							$email = $staff[2]; // User e-mail Address
							$password = ($staff[3] == '') ? wp_generate_password() : $staff[3]; // User Password, generate one if the director didnt include one.
							$data = compact("org_id", "first_name", "last_name", "email", "password");
							$courses = array(); // the courses to enroll the user into
							for ($i = 4; $i < 8; $i++)
							{
								if (!empty($staff[$i]))
								{
									array_push($courses, $staff[$i]);
								}	
							}
							
							// check if user exists in WP, if yes make sure they are in the same org. 
							if ( email_exists($email) )
							{
                                                            
								$staff_id = get_user_by('email', $email)->ID;
								if ( get_user_meta($staff_id,'org_id', true) === $org_id )
								{
									
											// enroll user in courses	
											$result2 = enrollUserInCourses($portal_subdomain, $courses, $org_id, $email,$subscription_id);
											if (isset($result2['status']) && !$result2['status'])
											{
												// ERROR in enrolling user
												$has_error = true;
												$has_user_error = true;
												echo "<p>ERROR: Could not enroll $email into one or more courses. ".$result2['message']."</p>";
											}
									
								}
								else
								{
									// ERROR: WP user exists but in a different org.
									$has_error = true;
									$has_user_error = true;
									echo "<p>ERROR: This user, $email, already exists but is assigned to a different organization.</p>";
								}

							}
							else
							{
								// if user doesnt exist in WP, create user in WP and LU
								$result = createWpLuUser($portal_subdomain, $data, true, false, 'student'); // Create WP and LU user

								if (isset($result['success']) && $result['success'])
								{
                                                                        /************************************************************
                                                                        * Check if the camp director wants to send the users an email 
                                                                        * compose and create recepient with subject and message
                                                                        *************************************************************/
                                                                        if($isEmail != '') 
                                                                        {

                                                                                $loginInfo = 'Username: ' . $email . '<br/>'; // Login Information
                                                                                $loginInfo .= 'Password: ' . $password;
                                                                                $subject = get_field('subject', 'user_'.$user_id); // The Subject
                                                                                $name = $first_name . ' ' . $last_name;
                                                                                $message = get_field('email_message', 'user_'.$user_id); // File upload e-mail message
                                                                                        $vars = array(
                                                                                                'name' => $name,
                                                                                                'logininfo' => $loginInfo,
                                                                                                'directorname'	=>	$directorname,
                                                                                                'campname'	=>	$campname,
                                                                                                'numvideos'	=>	NUM_VIDEOS,
                                                                                );
                                                                                // Need to add extra breakspace, cause on the ACF Wysiwyg Editor uses <p></p> without <br> when adding a line break or <enter>
                                                                                $message = str_replace("</p>","</p><br/>",$message); 
                                                                                /* Replace %%VARIABLE%% using vars*/
                                                                                foreach($vars as $key => $value)
                                                                                {
                                                                                        $message = preg_replace('/%%' . $key . '%%/', $value, $message);
                                                                                }
                                                                        $recepient = array (
                                                                            'name' => $name,
                                                                            'email' => $email,
                                                                            'message' => $message,
                                                                            'subject' => $subject
                                                                        );
                                                                        array_push($recepients, $recepient);

                                                                        }
                                                                        // enroll user in courses
									$result2 = enrollUserInCourses($portal_subdomain, $courses, $org_id, $email,$subscription_id);
									if (isset($result2['status']) && !$result2['status'])
									{
										// ERROR in enrolling user
										$has_error = true;
										$has_user_error = true;
										echo "<p>ERROR: Could not enroll $email into one or more courses. ".$result2['message']."</p>";
									}
								}
								else
								{
									// ERROR in creating user
									$has_error = true;
									$has_user_error = true;
									echo "<p>ERROR: Could not create user: $email ".$result['message']."</p>";
								}
							}
							

                                                        
             
                                                           
						} // End of foreach loop
                                                if($isEmail != ''){
                                                    $response=  addPendingEmails($org_id, $directorname, $current_user->user_email, $recepients);

                                                }

                                                if($has_error){
                                                    echo "Please fix the errors and upload your spreadsheet again";
                                                }
					//}
				?>

				</tbody>
			</table>
			<br>
			<a href="<?= get_home_url() .'/dashboard/?part=uploadspreadsheet&subscription_id=' . $subscription_id ?>">Return to Upload. </a>

		<?php
                //echo "Recipients: ".count($recipients);
                if($isEmail != '' && count($recepients)>0){
                $processing = 1; //the number out of total users we are processing right now
                $max = count($recepients);     //total users being processed from this instance of spreadsheet upload
//                $processing_top = ($processing + PENDING_EMAILS_LIMIT - 1 > $max) ? $max : $processing + PENDING_EMAILS_LIMIT - 1;
                $admin_ajax_url = admin_url('admin-ajax.php');
 ?>
                <h3 class="article_page_title">Emailing your staff</h3>

                <div class="spreadsheet_processing round_msgbox">
                    <strong>Please wait while we send your emails: <br>
                        <span class="processing">Processing <?= $processing ?> out of <?= $max ?></span> ... </strong> <i class="fa fa-spinner fa-pulse fa-2x"></i><br /><br />DO NOT CLOSE THIS WINDOW UNTIL ALL STAFF HAS BEEN EMAILED.<br><br>You will be redirected to a success page once the process is complete.
                </div>
                <script>
                    var count = 1;
                    var max = <?=$max?>;
                    var sent_emails = '';
                    var overall_status = 1;

                    $(document).ready(function () {
                        sendMail();
                    });

                    function sendMail() {
                        $.ajax({
                            url: "<?= $admin_ajax_url ?>?action=mass_mail_ajax&org_id=<?= $org_id ?>", 
                            success: function (result) 
                            {
                                result = JSON.parse(result);
//                                if(result.status == 1)
//                                {
                                    sent_emails += result.sent_emails;
                                    count += <?= PENDING_EMAILS_LIMIT ?>;
/*
                                    // calculate the next amount of emails to process
                                    if (count + <?= PENDING_EMAILS_LIMIT ?> -1 > <?= $max ?>)
                                    {
                                        var processing_top = <?= $max ?>;
                                    }
                                    else
                                    {
                                        var processing_top = count + <?= PENDING_EMAILS_LIMIT ?> -1;
                                    }
*/
                                    // check if there was a problem
                                    if (result.status == 0)
                                    {
                                        overall_status = 0;
                                    }

                                    $('.processing').html("Processing "+count+" out of <?= $max ?>");

                                    // check if we finished sending
                                    if (count > <?= $max ?> && overall_status == 1)
                                    {
                                        $('.round_msgbox').html("Users Added Successfully!<br><br>" + sent_emails.replace(/,/g, "")); 
                                    }
                                    else if (count > <?= $max ?> && overall_status == 0)
                                    {
                                        $('.round_msgbox').html("ERROR: Some emails below did not get sent.<br><br>Please contact us for assistance 1-877-239-3931 M-F 9-5 EST.<br><br>Error message is: " + result.message + "<br><br>" + sent_emails.replace(/,/g, "")); 
                                    }
                                    else
                                    {
                                        sendMail();
                                    }
//                                }
//                                else if(result.status == 0)
//                                {
//                                    $('.round_msgbox').html(result.message);
//                                }
                            }});
                    }
                </script>
<?php                                    
            ?>
                                                                    
            <?php
                                                   
                                                }
		}
	}
        else if( isset($_REQUEST['processing']) && isset($_REQUEST['max']) && isset($_REQUEST['subscription_id']) && isset($_REQUEST['org_id']) ){
                            $processing = filter_var($_REQUEST['processing'], FILTER_SANITIZE_NUMBER_INT); //the number out of total users we are processing right now
                $max = filter_var($_REQUEST['max'], FILTER_SANITIZE_NUMBER_INT);     //total users being processed from this instance of spreadsheet upload
//                $processing_top = ($processing + PENDING_EMAILS_LIMIT - 1 > $max) ? $max : $processing + PENDING_EMAILS_LIMIT - 1;
                $admin_ajax_url = admin_url('admin-ajax.php');
 ?>
                <h1 class="article_page_title">Emailing your staff</h1>

                <div class="spreadsheet_processing round_msgbox">
                    <strong>Please wait while we send your emails: <br>
                        <span class="processing">Processing <?= $processing ?> out of <?= $max ?></span> ... </strong> <i class="fa fa-spinner fa-pulse fa-2x"></i><br /><br />DO NOT CLOSE THIS WINDOW UNTIL ALL STAFF HAS BEEN EMAILED.<br><br>You will be redirected to a success page once the process is complete.
                </div>
                <script>
                    var count = 1;
                    var max = <?=$max?>;
                    var sent_emails = '';
                    var overall_status = 1;

                    $(document).ready(function () {
                        sendMail();
                    });

                    function sendMail() {
                        $.ajax({
                            url: "<?= $admin_ajax_url ?>?action=mass_mail_ajax&org_id=<?= $org_id ?>", 
                            success: function (result) 
                            {
                                result = JSON.parse(result);
//                                if(result.status == 1)
//                                {
                                    sent_emails += result.sent_emails;
                                    count += <?= PENDING_EMAILS_LIMIT ?>;
/*
                                    // calculate the next amount of emails to process
                                    if (count + <?= PENDING_EMAILS_LIMIT ?> -1 > <?= $max ?>)
                                    {
                                        var processing_top = <?= $max ?>;
                                    }
                                    else
                                    {
                                        var processing_top = count + <?= PENDING_EMAILS_LIMIT ?> -1;
                                    }
*/
                                    // check if there was a problem
                                    if (result.status == 0)
                                    {
                                        overall_status = 0;
                                    }

                                    $('.processing').html("Processing "+count+" out of <?= $max ?>");

                                    // check if we finished sending
                                    if (count > <?= $max ?> && overall_status == 1)
                                    {
                                        $('.round_msgbox').html("Messages Sent Successfully!<br><br>" + sent_emails.replace(/,/g, "")); 
                                    }
                                    else if (count > <?= $max ?> && overall_status == 0)
                                    {
                                        $('.round_msgbox').html("ERROR: Some emails below did not get sent.<br><br>Please contact us for assistance 1-877-239-3931 M-F 9-5 EST.<br><br>Error message is: " + result.message + "<br><br>" + sent_emails.replace(/,/g, "")); 
                                    }
                                    else
                                    {
                                        sendMail();
                                    }
//                                }
//                                else if(result.status == 0)
//                                {
//                                    $('.round_msgbox').html(result.message);
//                                }
                            }});
                    }
                </script>
<?php
        }
	else if( isset($_REQUEST['subscription_id']) )
	{
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
		$subscription = getSubscriptions($subscription_id,0,1); // Subscription details
		global $current_user;
		$user_id = $current_user->ID; // Wordpress account user id
		if( !isset($subscription) || $subscription->manager_id != $user_id)
		{
			wp_die('You do not have privilege for this subscription.');
		}
		?>
		<h1 class="article_page_title">Upload Staff Spreadsheet</h1>
		<p>
      	You can <a href="<?= get_template_directory_uri() . '/templates/template-staff.xlsx' ?>">Download our Excel Template</a> and fill out your staff details, then upload it back to create your staff accounts. Make sure to follow these instructions carefully:
    	</p>
		<ol>
			<li>
				<h2>Download the Excel Template</h2>
				<a href="<?= get_template_directory_uri() . '/templates/template-staff.xlsx' ?>">Download the Excel Template (XLSX file)</a>. You will use this to fill in your staff data.
				<br><br>
			</li>
			<li>
		        <h2>Populate the Spreadsheet</h2>
		        Enter your staff details using <b>Microsoft Excel</b> <i>(or a compatible application)</i> according to these guidelines:
		        <div class="sm">
			        <ul class="nested">
			          	<li>
			      			<b>First Name <span class="red">(required)</span></b>
			          	</li>
						<li>
							<b>Last Name <span class="red">(required)</span></b>
						</li>
						<li>
							<b>E-mail <span class="red">(required)</span></b>
						</li>
						<li>
							<b>Password</b> - if left blank, a password will be generated (and will be included in a welcome e-mail automatically sent) <span class="red">If included, it must be a minimum of 6 characters.</span>
						</li>
						<li>
				          	<b>Course <i>1, 2, 3, 4</i> <span class="red">(minimum 1 course)</span></b> - Enter the <b>Name</b> of the Course you want this staff member to join. It must <u>exactly match</u> the name of the Course, so it is recommended that you copy-and-paste the name to avoid errors.
				            <ul class="nested">
								<li>
									You must put each user in <u>at least 1 Course</u> (in any of the 4 slots)
								</li>
								<li>
									You can put each user into <u>up to 4 Courses</u> (there are 4 slots in the template)
								</li>
								<li>
									You don't have to use the field <b>Course 1</b> (you can put a Course into <b>Course 2</b> and leave <b>Course 1</b> blank and it will still work fine. Our system is smart that way).
								</li>
				            </ul>
			          	</li>
			        </ul> 
		        </div>
		        <div style="margin:1em;">
					<div class="errorboxcontainer">
						<div class="error-tl">
							<div class="error-tr"> 
								<div class="error-bl">
									<div class="error-br">
										<div class="errorbox">
											<center>Do not delete, move, or add columns!</center>
										</div>              
									</div>
								</div>
							</div>
						</div>
					</div>
		      	</div>
		        <img src="<?= get_template_directory_uri() ?>/images/spreadsheet-example.png" alt="Spreadsheet Example">
		        <br><br>
	      	</li>
	      	<li>
		        <h2>Save as Tab-Delimited Text File</h2>
		        	Once finished, click <b>Save As...</b> and save it as a <b>Tab-Delimited Text File</b> <img src="<?= get_template_directory_uri() ?>/images/info-sm.gif" title="<div class=\'sm\'><b>FYI:</b> This will actually make a text file version of the spreadsheet that will:<ul class=nested><li>not have any style formatting (ie: bold, italic, underline), and</li><li>each value will be separated by a tab</li></ul>This makes it easier for us to process the data.</div>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<div class=\'sm\'><b>FYI:</b> This will actually make a text file version of the spreadsheet that will:<ul class=nested><li>not have any style formatting (ie: bold, italic, underline), and</li><li>each value will be separated by a tab</li></ul>This makes it easier for us to process the data.</div>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"><br>
		        <br>
		        <br>
		        <img src="<?= get_template_directory_uri() ?>/images/save-as-tab-delim.png" alt="Save as Tab-Delimited Text File">
		        <br><br>
		        <div style="margin:1em;">
					<div class="errorboxcontainer">
						<div class="error-tl">
							<div class="error-tr"> 
								<div class="error-bl">
									<div class="error-br">
										<div class="errorbox">
											<center>NOTE: if you are using a mac and can not save as a tab delimited file, you may save as a comma seperated values (.CSV) file. Make sure you do not have any commas in any of the table cells!</center>
										</div>              
									</div>
								</div>
							</div>
						</div>
					</div>
		      	</div>
	      	</li>
	      	<li>
		        <h2>Upload the .TXT or .CSV file</h2>
		    <?php
				$options = array(
						'post_id' => 'user_' . $user_id,
						'field_groups' => array(ACF_UPLOAD_SPREADSHEET),
						'return' => '?part=uploadspreadsheet&uploadFile=true&subscription_id='.$subscription_id,
						'submit_value' => __("Upload Spreadsheet", 'acf'),
				);
			 	acf_form( $options ); 
		    ?>
		        <br>
		        <div class="batch_upload round_msgbox">
        			Please be patient while we create your user accounts ... <br />
        			<img src="<?= get_template_directory_uri() . '/images/loading.gif'?>" /><br />
        			If you see this message for more than 60 seconds, please call 877-237-3931 for assistance.  
    			</div>
    			<script type="text/javascript">
    				jQuery(function($) {
			            $(document).ready(function() {
			            
			                $('#post input[type="submit"]').click( function() {
			                    $('.batch_upload').slideDown();
			                });
			            });
			        });
			    </script>
		        <br>
		        <div class="sm">
			        <b>PLEASE NOTE</b>
			        <ol>
						<li>
							<b>Errors:</b><br>If there are any errors in the spreadsheet, it will give you a list of errors to fix. You must fix these errors, re-save as Tab-Delimited Text File, then upload the file again. No accounts will be created until the entire spreadsheet is valid.
							<br><br>
						</li>
						<li>
							<b>Existing Users:</b> <br>If a user already exists and you specify a <b>Course</b> to join (which they are not currently in), they will be added to that course. If the user exists and you <i>don't</i> specify any extra courses to join (that they are not in yet), it will be considered an erroneous duplicate, and you will need to remove this person from the spreadsheet.
								<br><br></li>
						<li>
							<b>Help!</b><br>
							If you're having trouble, just give us a call at <b>877-237-3931</b> and we'll help you. You can even send your spreadsheet to us to get it checked.

						</li>
			        </ol>
		        </div>
	      	</li>
    	</ol>
		<?php
	}
?>
