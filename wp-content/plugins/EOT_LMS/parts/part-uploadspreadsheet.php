<style type="text/css">
	.s-c-x #col1 {
	    overflow: visible !important;
	}
</style>
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
	// verify this user has access to this portal/subscription/page/view
	$true_subscription = verifyUserAccess(); 
	global $current_user;
	$user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID

	// Check if the subscription ID is valid.
	if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
	{
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
		// Variable declaration
		$org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID

                if(isset($true_subscription['status']) && $true_subscription['status'])
		{
			if(!current_user_can( "is_director" ))
			{
				echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.", "EOT_LMS");
				return;
			}
		}
		else
		{
			echo __("subscription ID does not belong to you", "EOT_LMS");
			return;
		}
	}
	// Could not find the subscription ID
	else
	{
		echo __("Could not find the subscription ID", "EOT_LMS");
		return;
	}

	if(isset($_REQUEST['uploadFile']) && $_REQUEST['uploadFile'] == 'true' && isset($_REQUEST['subscription_id']))
	{
		/*****************************************************************
		 * This triggers when a camp director succesfully uploaded a file.
		 *****************************************************************/
		$message = get_field('email_message', 'user_'.$user_id); // The Composed message
	 	$isEmail = get_field('email_user', 'user_'.$user_id); // Boolean indicating whether the user wants to send an email.
      	        $subject = get_field('subject', 'user_'.$user_id); // The Subject
	 	$file = get_field('file_uploadspreadsheet', 'user_'.$user_id);
		$fileLink = $file['url']; // The link to the file
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
    	if( !current_user_can ('is_director') && !current_user_can ('is_administrator') )
        {
         	wp_die(__('Sorry, you do not have permisison to view this page.', "EOT_LMS"));
        }
		// Check if the file exsist and is a text or csv file.
		else if ( pathinfo($fileLink, PATHINFO_EXTENSION) != 'txt' && pathinfo($fileLink, PATHINFO_EXTENSION) != 'csv')
		{
			echo __("The file needs to be a text or csv file. Please follow the instruction carefuly.", "EOT_LMS").'<br><br>';
			echo '<a href="?part=uploadspreadsheet&subscription_id='.$subscription_id.'&user_id='.$user_id.'">'.__('Go back',"EOT_LMS").'</a>';
		}
		else if ( !$fileLink )
		{
			// Check if file exsist.
			echo __('Could not find your file. Please upload your text file again.', "EOT_LMS").'<br><br>';
			echo '<a href="?part=uploadspreadsheet&subscription_id='.$subscription_id.'&user_id='.$user_id.'">'.__('Go back',"EOT_LMS").'</a>';
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
			<h1 class="article_page_title"><?= __('Upload Spreadsheet', 'EOT_LMS') ?></h1>

	        <div class="spreadsheet_upload round_msgbox">
				<?= __('Processing your staff list ...', 'EOT_LMS') ?> <i class="fa fa-spinner fa-pulse fa-2x"></i><br><br>
				<?= __('This page will refresh itself to show you the progress unless there are errors below.', 'EOT_LMS') ?><br><br>
				<?= __('Please inspect the table below to see if any errors exist, if so you will need to fix them, otherwise sit tight and we will create your staff accounts.', 'EOT_LMS') ?><br>
			</div>
			<br/>
			<br/>
			<table class="bordered">
        		<tbody>
    				<tr class="head">
						<td>
							<?= __('Row', 'EOT_LMS') ?>
						</td>
						<td class="name">
							<?= __('First Name', 'EOT_LMS') ?>
						</td>
						<td class="name">
							<?= __('Last Name', 'EOT_LMS') ?>
						</td>
						<td class="email">
							<?= __('E-mail', 'EOT_LMS') ?>
						</td>
						<td class="pw">
							<?= __('Password', 'EOT_LMS') ?>
						</td>
						<td class="course">
							<?= __('Course 1', 'EOT_LMS') ?>
						</td>
						<td class="course">
							<?= __('Course 2', 'EOT_LMS') ?>
						</td>
						<td class="course">
							<?= __('Course 3', 'EOT_LMS') ?>
						</td>
						<td class="course">
							<?= __('Course 4', 'EOT_LMS') ?>
						</td>
					</tr>
				<?php
					$row_counter = 1;
					$has_error = false; // Boolean indication if the process has an error
					$has_user_error = false; // Boolean indicator for individual user error.
					$org_id = get_org_from_user ($user_id); // Organization id
					$portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
					$data = compact("org_id"); // to pass to our functions below
					$courses_in_org = getCoursesByOrgId($org_id); // All the courses in this org
					$course_names = array_column($courses_in_org, 'course_name'); // The titles of the modules from the master library
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
                                        $current_user = get_user_by('ID', $user_id);
					$directorname =	$current_user->display_name; // the directors name
					$campname =	get_the_title($org_id); // the camp name
					//$response = getEotUsers($org_id); // All users in this org
					$response = getUsersInSubscription($subscription_id);

					if (isset($response['status']) && $response['status'] == 1)
					{
						$users = $response['users'];
				        $learners = filterUsers($users, 'learner'); // only the learners
					}
					else
					{
						$users = array();
						$learners = array();
					}
					$num_staff = count($learners); // Number of staff (learners) for this organization
					/********************************************************************** 
					 * This goes to all the staff specificed in the file
					 * Prints their information and error if occured.
					 **********************************************************************/
					foreach($staff_data as $staff)
					{
						$error_message = ''; // Error message initialization
						// Check if the required tabs are used
						if( count($staff) < 5 )
						{
							$error_message .= '<b>-'.__('Missing fields. Make sure you have all the required fields',"EOT_LMS").' <b> <br>';
						}
						$first_name = isset($staff[0]) ? trim($staff[0]) : ''; // User First Name
						$last_name = isset($staff[1]) ? trim($staff[1]) : ''; // User Last Name
                                                $first_name = preg_replace("/[.,*;!{ }@#$%^&()+|?\'\"`\’\”]/", "", $first_name);
                                                $last_name = preg_replace("/[.,*;!{ }@#$%^&()+|?\'\"\’`\”]/", "", $last_name);
						$email = isset($staff[2]) ? trim($staff[2]) : ''; // User e-mail address
						$password = isset($staff[3]) ? trim($staff[3]) : ''; // User password
						$course_1 = isset($staff[4]) ? trim($staff[4]) : ''; // User Course 1
						$course_2 = isset($staff[5]) ? trim($staff[5]) : ''; // User Course 2
						$course_3 = isset($staff[6]) ? trim($staff[6]) : ''; // User Course 3
						$course_4 = isset($staff[7]) ? trim($staff[7]) : ''; // User Course 4
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
						// Check if the camp director specificed more than 1 course
						if( $course_1 == '' && $course_2 == '' && $course_3 == '' && $course_4 == '' )
						{
							$error_message .= '<b>-'.__('You must specify at least 1','EOT_LMS').' <u>'.__('Course','EOT_LMS').'</u> '.__('for each user.'.'EOT_LMS').'</b><br>';
						}
						else
						{	// Check if any of the course exsist on their portals.
							if( !empty($course_1) )
							{
								if(!in_array($course_1, $course_names))
								{
									$error_message .= '- <b>' . $course_1 . ' '.__('is not a valid course.','EOT_LMS').' </b><br/>';
								}
							}
							if( !empty($course_2) )
							{
								if(!in_array($course_2, $course_names))
								{
									$error_message .= '- <b>' . $course_2 . ' '.__('is not a valid course.','EOT_LMS').' </b><br/>';
								}
							}
							if( !empty($course_3) )
							{
								if(!in_array($course_3, $course_names))
								{
									$error_message .= '- <b>' . $course_3 . ' '.__('is not a valid course.','EOT_LMS').' </b><br/>';
								}
							}
							if( !empty($course_4) )
							{
								if(!in_array($course_4, $course_names))
								{
									$error_message .= '- <b>' . $course_4 . ' '.__('is not a valid course.','EOT_LMS').' </b><br/>';
								}
							}
						}
						// Check if this e-mail has already been processed
						if(in_array($email, $email_finish))
							$error_message .= '- <b>'.__('E-mail address (','EOT_LMS') . $email .__( ') is repeated in another row. Is this a duplicate row?','EOT_LMS').'</b>';
						else
							array_push($email_finish, $email);
						// Check if the user password is set && less than 6 characters
						if(!empty($password) && strlen($password) < 5)
						{
							$error_message .= '- <b>'.__('Password must be at least 6 characters in length.','EOT_LMS').'</b>';
						}
						// check if user exists in WP, if he does we dont need to create a new user and dont count it towards his staff
						if (email_exists($email))
						{
							$num_staff--;
						}
						// Check if the user has enough credits to add more staff members
						if( $num_staff >= $staff_credits )
						{
							$error_message .= '- <b>'.__('Unable to add user. You reached the maximum amount of staff you can have.','EOT_LMS').'</b>';
						}
						$num_staff++;
						// check if there is an error message and display it
						if(!empty($error_message))
						{
							$has_error = true;
							error_log("uploadspreadsheet: user had an error, error_message: $error_message");
?>
							<tr>
		                    	<td></td>
			                    <td class="errors" colspan="8">
		                      		<?= $error_message ?>                    
		                      	</td>
		                  	</tr>
<?php
						}
					$row_counter++;
					} // End of foreach


					// no errors. proceed to create accounts.
					if($has_error == false)
					{
						if(is_array($isEmail) && $isEmail[0] = "Yes")
						{
							$isEmail = 1;
						}
						else
						{
							$isEmail = 0;
							$message = '';
							$subject = '';
						}
						//after verification of users, add the required information to the table so it can be processed after
						$result = addPendingUsers($staff_data, $org_id, $message, $subject, $isEmail, $directorname,$subscription_id);

						if($result)
						{
							//url to redirect to (redirects to a page where users are processed in instances of PENDING_USER_LIMIT users)
							$url = get_home_url() .'/dashboard/?part=uploadspreadsheet&org_id=' . $org_id . '&subscription_id=' . $subscription_id. '&user_id=' . $user_id . '&processing=1&max=' . count($staff_data);
?>
							<!-- Redirecting (wp_redirect does not load html if headers have already been sent which is the case here so we have to use javascript)-->
							<script type="text/javascript">
								window.location.href = "<?= $url ?>";
							</script>
<?php
						}
					}
?>
					<tr>
						<td class="errors" colspan="9">
							<strong><?= __('There are errors in your spreadsheet. Please review the errors above and correct them before retrying to upload the spreadsheet. ','EOT_LMS')?></strong>
						</td>
			  		</tr>
				</tbody>
			</table>
			<br>
			<a href="<?= get_home_url() .'/dashboard/?part=uploadspreadsheet&user_id='.$user_id.'subscription_id=' . $subscription_id ?>"><?= __('Please fix your spreadsheet and upload your file again.','EOT_LMS')?> </a>

<?php
		}
	}
	//this part processes users PENDING_USERS_LIMIT each time until it runs out
	else if( isset($_REQUEST['processing']) && isset($_REQUEST['max']) && isset($_REQUEST['subscription_id']) && isset($_REQUEST['org_id']) )
	{
		$processing = filter_var($_REQUEST['processing'],FILTER_SANITIZE_NUMBER_INT);	//the number out of total users we are processing right now
		$max = filter_var($_REQUEST['max'],FILTER_SANITIZE_NUMBER_INT);					//total users being processed from this instance of spreadsheet upload
		$org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);			//org id
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);	//subscription id
		$admin_ajax_url = admin_url('admin-ajax.php');	
?>
		<h1 class="article_page_title"><?= __('Upload Staff Spreadsheet','EOT_LMS')?></h1>

        <div class="spreadsheet_processing round_msgbox">
			<strong><?= __('Please wait while we create your staff accounts:','EOT_LMS')?> <br>
            <span  class="processing">Processing <?= $processing ?> out of <?= $max ?> ...</span> </strong> <i class="fa fa-spinner fa-pulse fa-2x"></i><br /><br /><?= __('DO NOT CLOSE THIS WINDOW UNTIL ALL STAFF ACCOUNTS HAVE BEEN CREATED.','EOT_LMS')?><br><br><?= __('You will be redirected to a success page once the import is complete.','EOT_LMS')?>
		</div>
            <div id="insert_form" style="display:none;"></div>
                <script>
                var count = 1;
                var max = <?=$max?>;
                var sent_emails = '';
                var overall_status = 1;

                $(document).ready(function () {
                    sendMail();
                });

                function sendMail() 
                {
                    $.ajax({
                        url: "<?= $admin_ajax_url ?>?action=mass_register_ajax&org_id=<?= $org_id ?>", 
                        success: function (result) 
                        {
                            //console.log(result);
                            result = JSON.parse(result);
                            //console.log(result);

                                sent_emails += result.import_status;
                                count += <?= PENDING_USERS_LIMIT ?>;

                                // check if there was a problem
                                if (result.status == false)
                                    {
                                        overall_status = 0;
                                    }

                                    $('.processing').html("Processing "+count+" out of <?= $max ?>");

                                    // check if we finished sending
                                    if (count > <?= $max ?> && overall_status == 1)
                                    {
                                            <?php
                                        $url = get_home_url() .'/dashboard/?part=manage_staff_accounts&status=uploadedspreadsheet&org_id='. $org_id . '&subscription_id=' . $subscription_id .'&user_id=' . $user_id . '&sent=1';
                                        ?>
                                        $('#insert_form').html('<form action="<?= $url; ?>" name="redirect" method="post" style="display:none;"><input type="text" name="import_status" value="'+sent_emails+'" /></form>');

                                            document.forms['redirect'].submit(); 
                                    }
                                    else if (count > <?= $max ?> && overall_status == 0)
                                    {
                                        $('.round_msgbox').html("<?= __('ERROR: Some accounts below did not get created.','EOT_LMS')?><br><br><?= __('Please contact us for assistance 1-877-390-2267 M-F 9-5 EST.','EOT_LMS')?><br><br><?= __('Error message is:','EOT_LMS')?> " + result.message + "<br><br>" + sent_emails.replace(/,/g, "")); 
                                    }
                                    else
                                    {
                                        sendMail();
                                    }

                        }});
                }
            </script>
<?php
	}
	else if( isset($_REQUEST['subscription_id']) )
	{
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
		$subscription = getSubscriptions($subscription_id, 0, 1); // get active Subscription details
                //d($subscription);
		if( !isset($subscription) || $subscription->manager_id != $user_id)
		{
			wp_die('You do not have privilege for this subscription.');
		}
		?>
		<h1 class="article_page_title"><?= __('Upload Staff Spreadsheet', 'EOT_LMS')?></h1>
		<p>
      	You can <a href="<?= get_template_directory_uri() . '/templates/template-staff.xlsx' ?>"><?= __('Download our Excel Template', 'EOT_LMS')?></a> <?= __('and fill out your staff details, then upload it back to create your staff accounts. Make sure to follow these instructions carefully:', 'EOT_LMS')?>
    	</p>
		<ol>
			<li>
				<h2><?= __('Download the Excel Template', 'EOT_LMS')?></h2>
				<a href="<?= get_template_directory_uri() . '/templates/template-staff.xlsx' ?>"><?= __('Download the Excel Template (XLSX file)', 'EOT_LMS')?></a>. <?= __('You will use this to fill in your staff data.', 'EOT_LMS')?>
				<br><br>
			</li>
			<li>
		        <h2><?= __('Populate the Spreadsheet', 'EOT_LMS')?></h2>
		        <?= __('Enter your staff details using', 'EOT_LMS')?> <b><?= __('Microsoft Excel', 'EOT_LMS')?></b> <i><?= __('(or a compatible application)', 'EOT_LMS')?></i> <?= __(' according to these guidelines:', 'EOT_LMS')?>
		        <div class="sm">
			        <ul class="nested">
			          	<li>
			      			<b><?= __('First Name', 'EOT_LMS')?> <span class="red"><?= __('(required)', 'EOT_LMS')?></span></b>
			          	</li>
						<li>
							<b><?= __('Last Name', 'EOT_LMS')?> <span class="red"><?= __('(required)', 'EOT_LMS')?></span></b>
						</li>
						<li>
							<b><?= __('E-mail ', 'EOT_LMS')?><span class="red"><?= __('(required)', 'EOT_LMS')?></span></b>
						</li>
						<li>
							<b><?= __('Password', 'EOT_LMS')?></b> <?= __('- if left blank, a password will be generated (and will be included in a welcome e-mail automatically sent) ', 'EOT_LMS')?><span class="red"><?= __('If included, it must be a minimum of 6 characters', 'EOT_LMS')?>.</span>
						</li>
						<li>
				          	<b><?= __('Course', 'EOT_LMS')?> <i><?= __('1, 2, 3, 4', 'EOT_LMS')?></i> <span class="red"><?= __('(minimum 1 course)', 'EOT_LMS')?></span></b><?= __(' - Enter the ', 'EOT_LMS')?><b><?= __('Name', 'EOT_LMS')?></b> <?= __('of the Course you want this staff member to join. It must ', 'EOT_LMS')?><u><?= __('exactly match', 'EOT_LMS')?></u> <?= __('the name of the Course, so it is recommended that you copy-and-paste the name to avoid errors.', 'EOT_LMS')?>
				            <ul class="nested">
								<li>
									<?= __('You must put each user in ', 'EOT_LMS')?><u><?= __('at least 1 Cours', 'EOT_LMS')?>e</u> <?= __('(in any of the 4 slots)', 'EOT_LMS')?>
								</li>
								<li>
									<?= __('You can put each user into ', 'EOT_LMS')?><u><?= __('up to 4 Courses', 'EOT_LMS')?></u> <?= __('(there are 4 slots in the template)', 'EOT_LMS')?>
								</li>
								<li>
									<?= __("You don't have to use the field ", 'EOT_LMS')?><b><?= __('Course 1', 'EOT_LMS')?></b> <?= __('(you can put a Course into ', 'EOT_LMS')?> <b><?= __('Course 2 ', 'EOT_LMS')?></b> <?= __('and leave ', 'EOT_LMS')?> <b><?= __('Course 1 ', 'EOT_LMS')?></b> <?= __('blank and it will still work fine. Our system is smart that way).', 'EOT_LMS')?>
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
											<center><?= __('Do not delete, move, or add columns', 'EOT_LMS')?>!</center>
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
		        <h2><?= __('Save as Tab-Delimited Text File', 'EOT_LMS')?></h2>
		        	<?= __('Once finished, click', 'EOT_LMS')?> <b><?= __('Save As...', 'EOT_LMS')?></b> <?= __('and save it as a', 'EOT_LMS')?> <b><?= __('Tab-Delimited Text File', 'EOT_LMS')?></b> <img src="<?= get_template_directory_uri() ?>/images/info-sm.gif" title="<div class=\'sm\'><b>FYI:</b> <?= __('This will actually make a text file version of the spreadsheet that will: ', 'EOT_LMS')?><ul class=nested><li><?= __('not have any style formatting (ie: bold, italic, underline), and', 'EOT_LMS')?></li><li><?= __('each value will be separated by a tab', 'EOT_LMS')?></li></ul><?= __('This makes it easier for us to process the data.', 'EOT_LMS')?></div>" class="tooltip" style="margin-bottom: -2px" onmouseover="Tip('<div class=\'sm\'><b>FYI:</b> <?= __('This will actually make a text file version of the spreadsheet that will: ', 'EOT_LMS')?><ul class=nested><li><?= __('not have any style formatting (ie: bold, italic, underline), and', 'EOT_LMS')?></li><li><?= __('each value will be separated by a tab', 'EOT_LMS')?></li></ul><?= __('This makes it easier for us to process the data.', 'EOT_LMS')?></div>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"><br>
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
											<center><?= __('NOTE: if you are using a mac and can not save as a tab delimited file, you may save as a comma seperated values (.CSV) file. Make sure you do not have any commas in any of the table cells!', 'EOT_LMS')?></center>
										</div>              
									</div>
								</div>
							</div>
						</div>
					</div>
		      	</div>
	      	</li>
	      	<li>
		        <h2><?= __('Upload the .TXT or .CSV file', 'EOT_LMS')?></h2>
		    <?php
				$options = array(
						'post_id' => 'user_' . $user_id,
						'field_groups' => array(ACF_UPLOAD_SPREADSHEET),
						'return' => '?part=uploadspreadsheet&uploadFile=true&subscription_id='.$subscription_id.'&user_id='.$user_id,
						'submit_value' => __("Upload Spreadsheet", 'EOT_LMS'),
				);
			 	acf_form( $options ); 
		    ?>
		        <br>
		        <div class="batch_upload round_msgbox">
        			<?= __('Please be patient while we create your user accounts ... ', 'EOT_LMS')?><br />
        			<i class="fa fa-spinner fa-pulse fa-2x"></i> <br />
        			<?= __('If you see this message for more than 60 seconds, please call 877-390-2267 for assistance.', 'EOT_LMS')?>  
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
			        <b><?= __('PLEASE NOTE', 'EOT_LMS')?></b>
			        <ol>
						<li>
							<b><?= __('Errors:', 'EOT_LMS')?></b><br><?= __('If there are any errors in the spreadsheet, it will give you a list of errors to fix. You must fix these errors, re-save as Tab-Delimited Text File, then upload the file again. No accounts will be created until the entire spreadsheet is valid.', 'EOT_LMS')?>
							<br><br>
						</li>
						<li>
							<b><?= __('Existing Users:', 'EOT_LMS')?></b> <br><?= __('If a user already exists and you specify a', 'EOT_LMS')?> <b><?= __('Course', 'EOT_LMS')?></b> <?= __('to join (which they are not currently in), they will be added to that course. If the user exists and you', 'EOT_LMS')?>  <i><?= __("don't", 'EOT_LMS')?></i> <?= __('specify any extra courses to join (that they are not in yet), it will be considered an erroneous duplicate, and you will need to remove this person from the spreadsheet.', 'EOT_LMS')?>
								<br><br></li>
						<li>
							<b><?= __('Help!', 'EOT_LMS')?></b><br>
							<?= __("If you're having trouble, just give us a call at ", 'EOT_LMS')?><b><?= __('877-390-2267', 'EOT_LMS')?></b> <?= __("and we'll help you. You can even send your spreadsheet to us to get it checked.", 'EOT_LMS')?>

						</li>
			        </ol>
		        </div>
	      	</li>
    	</ol>
		<?php
	}
?>
