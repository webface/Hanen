<?php
/**
 * This file handles all the functions involved with a user
 * @author Patrick Roy
**/

/* registration process for the new account with director and organization information only when user not logged in (nopriv) */
add_action( 'wp_ajax_nopriv_register_acc', 'eot_register_new_user' );
/* registration process for the new account with their newly creating account information for logged in users (Sales manager and Sales representative) */
add_action( 'wp_ajax_register_acc', 'eot_register_new_user' );

/* removes the color scheme for the profile page */
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
/* removes some of the fields on the edit user page that are not needed */
add_action( 'admin_head', 'eot_profile_start' );
/* removes some of the fields on the edit user page that are not needed */
add_action( 'admin_footer', 'eot_profile_end' );
/* after user clicks activation email sets up user as director/student and processes their invitation */
add_action( 'gform_user_registered', 'after_submission_register', 10, 4 );
/**
 * Displays the new registration form on FRONT-END which registers the director and creates a new organization
 * Outputs the form in a 2-step format using the 'jquery-steps' plugin
**/
function display_register_account_form () { 
	global $states, $countries;
	if(isset($_GET['type'])) { $type = $_GET['type']; }
	?>
	<p id="new-account-message"></p>
	<form id="new-account" data-user_id="" data-status="" action="#">
		<h3>Director Info Tab</h3>
		<fieldset>
			<table cellpadding="5" cellspacing="0">
				<tr>
					<td class="label">
						First Name:<span class="asterisk">*</span>      
					</td>
					<td class="field">
						<input type="text" name="usr_firstname" value="" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						Last Name:<span class="asterisk">*</span>      
					</td>
					<td class="field">
						<input type="text" name="usr_lastname" value="" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						E-mail:<span class="asterisk">*</span>
						<br><br><span style="visibility: hidden;">space</span>
					</td>
					<td class="field">
						<input type="email" name="usr_email" value="" id="usr_email" required>
						<br><span>Example: email@org.domain</span>
					</td>
				</tr>
				<tr>
					<td class="label">
						Confirm E-mail:<span class="asterisk">*</span>      
					</td>
					<td class="field">
						<input type="email" name="email2" value="" id="email2" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						Password:<span class="asterisk">*</span>  
						<br><br><br><br><span style="visibility: hidden;">space</span>    
					</td>
					<td class="field">
						<input type="password" name="usr_password" value="" id="usr_password" required>
						<span>Minimum password strength required: Good</span><br>
						<span>Tips: Min 8 characters. Do not use names or words from the dictionary.</span><br>
						<!-- Password strength indicator -->
						<div class="meter-wrapper"><meter max="4" id="password-strength-meter"></meter></div>
						<p id="password-strength-text"></p>
					</td>
				</tr>
				<tr>
					<td class="label">
						Confirm Password:<span class="asterisk">*</span>  
						<br><br><span style="visibility: hidden;">space</span>    
					</td>
					<td class="field">
						<input type="password" name="password2" value="" id="password2" required>
						<br><span>Suggested password: <input readonly id="suggested-password" style="display:inline-block;"> <a href="#" id="password-generate">Generate Password</a></span>
					</td>
				</tr>
			</table>
		</fieldset>
		<h3>Organization Info Tab</h3>
		<fieldset>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td class="label">
						Camp, School or Youth Program Name:<span class="asterisk">*</span>
					</td>
					<td class="field">
						<input type="text" name="org_name" value="" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						Camp Portal Name (eg. supercamp):<span class="asterisk">*</span><br><span style="font-weight: normal;">* Letters Only</span>
					</td>
					<td class="field">
						<input type="text" name="org_subdomain" value="" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						Office Phone:<span class="asterisk">*</span>      
					</td>
					<td class="field">
						<input type="tel" name="org_phone" value="" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						Website:<span class="asterisk">*</span><br><span style="font-weight: normal;">* http:// is required</span>
					</td>
					<td class="field">
						<input type="url" name="org_url" value="http://" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						Office Address:<span class="asterisk">*</span>
					</td>
					<td class="field">
						<input type="text" name="org_address" value="" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						City:<span class="asterisk">*</span>
					</td>
					<td class="field">
						<input type="text" name="org_city" value="" required>
					</td>
				</tr>
				<tr>
					<td class="label">
						State/Province:<span class="asterisk">*</span>
					</td>
					<td class="field">
						<select name="org_state" required>
							<option value="">-- State/Province --</option>
							<?php foreach ($states as $label) { ?>
								<option value="<?php echo $label; ?>"><?php echo $label; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="label">
						Country:<span class="asterisk">*</span>
					</td>
					<td class="field">
						<select name="org_country" required>
							<option value="">-- Country --</option>
							<?php foreach ($countries as $label) { ?>
								<option value="<?php echo $label; ?>"><?php echo $label; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="label">
						Zip Code:<span class="asterisk">*</span>
					</td>
					<td class="field">
						<input type="text" name="org_zip" value="" required>
					</td>
				</tr>
			</table>
		</fieldset>
		<input type="text" name="new_account_body" value="" class="honeypot" />
		<input type="hidden" value="register_acc" name="action" />
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("register_acc_nonce"); ?>" />
		<input type="hidden" value="manager" name="usr_role" />
		<?php
			if(current_user_can( "is_sales_rep" ) || current_user_can( "is_sales_manager" ))
			{
				echo '<input type="hidden" name="salesrep" value="1" id="salesrep" />';
			}
		?>
	</form>
<?php
}

function after_submission_register($user_id, $feed, $entry, $user_pass){
    global $wpdb;
    
    $email=$entry['2'];
    $campname=$entry['5'];
    $website=$entry['8'];
    $phone=$entry['7'];
    $address1=$entry['9.1'];
    $address2=$entry['9.2'];
    $city=$entry['9.3'];
    $state=$entry['9.4'];
    $country=$entry['9.6'];
    $zip=$entry['9.5'];
        
    if($entry['4']==="Camp Director"){

        $new_org = array(
				'post_title' => $entry['5'],
				'post_author' => $user_id,
				'post_type' => 'org',
				'post_status' => 'publish',
				'post_content' => ''
			);

		$org_id = wp_insert_post ($new_org);

		if (is_wp_error($org_id)) 
		{ /* if error creating new organization, output to director on screen */
			echo $org_id->get_error_message();
			error_log("ERROR: couldn't create org in after_submission_register: " . $org_id->get_error_message());
		} 
		else
		{
            update_user_meta($user_id, 'org_id', $org_id);
            update_post_meta($org_id, 'org_address', $address1." ".$address2);
            update_post_meta($org_id, 'org_url', $website);
            update_post_meta($org_id, 'org_phone', $phone);
            update_post_meta($org_id, 'org_city', $city);
            update_post_meta($org_id, 'org_state', $state);
            update_post_meta($org_id, 'org_country', $country);
            update_post_meta($org_id, 'org_zip', $zip);
        }
    }
    else
    { //is student
        $code = $entry['10'];
        if($code == "") 
        { 
        	// no code meaning its an individual registrant
            $new_indiv = array(
				'post_title' => $entry['1.3']." ".$entry['1.6'],
				'post_author' => $user_id,
				'post_type' => 'indiv',
				'post_status' => 'publish',
				'post_content' => ''
			);
        
            wp_update_user( array( 'ID' => $user_id, 'role' => 'individual' ) );
			$indiv_id = wp_insert_post ($new_indiv);
		
			if (is_wp_error($indiv_id)) 
			{ /* if error creating new individual, output to user on screen */
				echo $indiv_id->get_error_message();
				error_log("ERROR: couldn't create indiv in after_submission_register: " . $indiv_id->get_error_message());
			} 
			else
			{
                update_user_meta($user_id, 'indiv_id', $indiv_id);
                update_post_meta($indiv_id, 'user_email', $email);
                update_post_meta($indiv_id, 'indiv_address', $address1." ".$address2);
                update_post_meta($indiv_id, 'indiv_url', $website);
                update_post_meta($indiv_id, 'indiv_phone', $phone);
                update_post_meta($indiv_id, 'indiv_city', $city);
                update_post_meta($indiv_id, 'indiv_state', $state);
                update_post_meta($indiv_id, 'indiv_country', $country);
                update_post_meta($indiv_id, 'indiv_zip', $zip);
            }
        }
        else
        { // code exists so its a student being invited to an org
            $sql = 'SELECT * FROM ' . TABLE_INVITATIONS . ' WHERE code ="'.$code.'"';
            $current = $wpdb->get_row($sql,ARRAY_A);//codes should be unique to user
            $subscription_id = isset($current['subscription_id']) ? $current['subscription_id'] : '';

            if(org_has_maxed_staff($current['org_id'], $subscription_id))
            { // org doesn't have enough staff credits, so put user in pending table
                $data=array(
                    'user_id' => $user_id,
                    'org_id' => $current['org_id'],
                    'subscription_id' => $subscription_id,
                    'course_id' => $current['course_id'],
                    'user_email' => $email
                );
                $insert_user_into_pending = $wpdb->insert(TABLE_PENDING_SUBSCRIPTIONS,$data,array('%d','%d','%d','%d','%s'));
                
                // if user was successfully added to pending subscriptions table then email the director
                if($insert_user_into_pending !== FALSE) 
                {
                    $sql = "SELECT users.user_email FROM ".TABLE_USERS." as users LEFT JOIN ".TABLE_SUBSCRIPTIONS." as subscr on users.id = subscr.manager_id WHERE subscr.id =".$subscription_id;
                    $result = $wpdb->get_row($sql,ARRAY_A);
                    $to = $result['user_email']; // the director
                    $subject = "Max staff reached on Expert Online Training";
                    $message = "A new user has subscribed on Expert Online Training but your organization has reached the maximum number of staff you can have. You need to upgrade your subscription to add more staff<br><br><a href='#'>Upgrade Now</a><br><br>";
                    $message .= "User's Name: ".$entry['1.3']." ".$entry['1.6']."<br>";
                    $message .= "User's email: ".$email;
                    if(wp_mail($to, $subject, $message))
                    {
                        echo "ERROR: This organization has reached its maximum number of staff that can be registered. Don't sweat, we have contacted the director to increase the number of staff and have recorded your info. We will create your account once the organization upgrades their number of staff accounts and notify you when you can log in.";
                    }
                }
            }
            else
            { // org has enough staff credits
                add_user_meta($user_id, 'org_id', $current['org_id']);
                if($current['course_id'] != '0')
                {
                    $course = getCourse($current['course_id']);
                    $org_id = $current['org_id'];
                    $course_id = $current['course_id'];
                    $course_name = $course['course_name'];
                    $data = compact("org_id", "course_id", "course_name", "subscription_id");

                    if($email == $current['user_email'])
                    {
                        enrollUserInCourse($email, $data);
                    }
                }
            }

            // if the invitation was to a user specifically, we want to track when the account was created so update the invitations table to include a date_signed_up. NOTE: user may not have been added to the ORG if max staff accounts was reached.
            if($current['type'] == 'user')
            {
            	$update=$wpdb->update(TABLE_INVITATIONS,array('date_signed_up'=>current_time('Y-m-d')),array('code'=>$code));
            }
        }
    }
}
/**
 * Registers the new account given the information passed through the form
 * Creates new 'inactive' manager account on LearnUpon
 * Creates a new 'portal' for the organization
 * Added the new manager to the portal of the organization
 * Stores USERID and ORGID to link with LearnUpon
 * @return json object containing message for jQuery post
 */
function eot_register_new_user() {
	global $eot_login_url;

	/* checking to ensure the request is coming from our site using wordpress */
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "register_acc_nonce")) {
		echo json_encode(array('status' => 0, 'message' => 'No naughty business please'));
		exit;
	}

	/* check the honeypot to check for bot */
	if (isset ($_REQUEST['new_account_body']) && $_REQUEST['new_account_body'] != "") {
		echo json_encode(array('status' => 0, 'message' => 'No naughty business please'));
		exit;
	}
	$break = "<br />";
	/* gather all the information needed for creating new director */
	$email = filter_var($_REQUEST['usr_email'], FILTER_SANITIZE_EMAIL);
	$password = $_REQUEST['usr_password'];
	$first_name = filter_var($_REQUEST['usr_firstname'],FILTER_SANITIZE_STRING);
	$last_name = filter_var($_REQUEST['usr_lastname'],FILTER_SANITIZE_STRING);
	$role = filter_var($_REQUEST['usr_role'],FILTER_SANITIZE_STRING);
	$org_name = filter_var($_REQUEST['org_name'],FILTER_SANITIZE_STRING);

	if (username_exists ($email)) { /* if username already exists, tell director and ask to sign in */
		$result['status'] = 0;
		$result['message'] = 'Username already exists. Please try to <a href="'.$eot_login_url.'">log in</a>';
	} else { /* otherwise, go ahead and create a new director */
		$new_user = array(
			'user_login' => $email,
			'user_pass' => $password,
			'display_name' => ucwords ($first_name) . " " . ucwords ($last_name),
			'user_email' => $email,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'role' => $role
		);
		$user_id = wp_insert_user ($new_user);
		if (is_wp_error($user_id)) { /* if director has error, output to front-end */
			$result['status'] = 0;
			$result['message'] = $user_id->get_error_message();
		} 
		else 
		{ 
			/* now director has been created, go ahead and add a new organization with the post_author variable being the new director id */
			$new_org = array(
				'post_title' => $org_name,
				'post_author' => $user_id,
				'post_type' => 'org',
				'post_status' => 'publish',
				'post_content' => ''
			);

			$org_id = wp_insert_post ($new_org);
			if (is_wp_error($org_id)) { /* if error creating new organization, output to director on screen */
				$result['type'] = "error";
				$result['html'] = $org_id->get_error_message();
			} else { /* if organization created, collect data and store as post_meta values excluding certain key values */
				foreach ($_POST as $key => $data) {
					if ($key == "usr_email" || $key == "usr_password" || $key == "usr_firstname" || $key == "usr_lastname" || $key == "email2" || $key == "password2" || $key == "org_name" || $key == "action" || $key == "nonce" || $key == "new_account_body") {continue;}

					if ($key == "org_subdomain") { /* if key is subdomain, store the data and ensure the subdomain is only alpha and lowercase */
						$data = SUBDOMAIN_PREFIX . preg_replace("/[^a-z]/", '', strtolower ($data));
						$org_subdomain = $data;
					}

					update_post_meta ($org_id, $key, $data);
				}

				/* set the organization id to the director to link together */
				update_user_meta ($user_id, 'org_id', $org_id);
				
				/* 
				 * Sign in the user if they are guests, otherwise, if they are sales rep or sales manager, we do not have to sign in the user.
				 */
				if(!current_user_can( "is_sales_rep" ) && !current_user_can( "is_sales_manager" ))
				{
					// get the credentials from the director email and log director into account 
					$creds['user_login'] = sanitize_user ($email, true);
					$creds['user_password'] = $password;
					$creds['remember'] = false;
					$user = wp_signon ($creds, false);

					if (is_wp_error ($user)) { // if error, notify director there was an error logging in
					$result['status'] = 0;
					$result['message'] = $user->get_error_message ();
					} 
					else 
					{ 
						// if no error logging in, communicate the data with LearnUpon to create new account 
						$data = compact ("org_id", "org_name", "org_subdomain", "user_id", "first_name", "last_name", "email", "password");
						$result = communicate_with_learnupon ('create_account', $data);
					}
				}
				else
				{
					// Communicate the data with LearnUpon to create new account 
					$data = compact ("org_id", "org_name", "org_subdomain", "user_id", "first_name", "last_name", "email", "password");
					$result = communicate_with_learnupon ('create_account', $data);
					$result['user_id'] = $user_id; // WP User ID. Required to determined which user_id to re-direct the director to subscribe the new user. This is required in target.js in eot-subscription plugin
				}

				// Generate a link to set a new password
				$user = get_user_by( 'email',  $email ); 
				$key = get_password_reset_key( $user );
				$set_new_password_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($email), 'login');

				// Send e-mail message
				$vars = array(
					'eot_link' => get_site_url(),
					'first_name' => $first_name, 
					'last_name' => $last_name,
					'email' => $email,
					'set_new_password_link' => $set_new_password_link, 
					'organization_name' => $org_name // The name of the camp
				);
				$fileLocation = get_template_directory_uri() . '/emailTemplates/NewAccount.txt'; // Template message
				$body = wp_remote_fopen($fileLocation, "r");
				$subject = 'Your New Account Credentials on EOT';
				/* Replace %%VARIABLE%% using vars*/
				foreach($vars as $key => $var)
				{
					$body = preg_replace('/%%' . $key . '%%/', $var, $body);
				}
				$recepients = array(); // List of recepients
				// Recepient information
		        $recepient = array (
		            'name' => $first_name . " " . $last_name,
		            'email' => $email,
		            'message' => $body,
		            'subject' => $subject
		        );
		        array_push($recepients, $recepient);
		        // send the e-mail
				$response = sendMail( 'NewAccount', $recepients, $data );
			}
		}
	}
	/* check to ensure the communication with this function is legit, otherwise return to referer */
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$result = json_encode($result);
		echo $result;
	} else {
		header("Location: ".$_SERVER["HTTP_REFERER"]);
	}

	die();
}

/**
 * Checks if the user currently has an organization attached to its account
 * Checks user_meta given user_id for the key 'org_id' which links the orgnization id
 * @return true/false
**/
function user_has_organization ($user_id) {
	if (!get_userdata ($user_id)) 
		return false;

	if (get_user_meta($user_id, 'org_id', true)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Removes some of the options in the admin area for the User Profile
**/
function eot_remove_personal_options ($buffer) {
	$titles = array('#<h3>About Yourself</h3>#','#<h3>About the user</h3>#');
	$buffer = preg_replace($titles,'<h3>Password</h3>',$buffer,1);
	$biotable = '#<h3>Password</h3>.+?<table.+?/tr>#s';
	$buffer = preg_replace($biotable,'<h3>Password</h3> <table class="form-table">',$buffer,1);
	$buffer = preg_replace( '#<h3>Personal Options</h3>.+?/table>#s', '', $buffer, 1 );
	return $buffer;
}

/**
 * Starts the user options removal
**/
function eot_profile_start () {
	ob_start( 'eot_remove_personal_options' );
}

/**
 * Ends the user options removal
**/
function eot_profile_end () {
	ob_end_flush();
}

//since the register page is displayed when the user is not logged in, nopriv is used to let the ajax call still go through
add_action('wp_ajax_nopriv_checkEmail', 'checkEmail_callback');
add_action('wp_ajax_checkEmail', 'checkEmail_callback');

//checks if the email already exists in the database and returns true if it does not
function checkEmail_callback()
{
	//email
	$email = filter_var($_REQUEST['email'],FILTER_SANITIZE_STRING);

	$result = get_user_by('login', $email);
	//if the email was not found return true otherwise false
	if($result)
	{
		echo false;
	}
	else
	{
		echo true;
	}

	wp_die();
}


//since the register page is displayed when the user is not logged in, nopriv is used to let the ajax call still go through
add_action('wp_ajax_nopriv_checkPortal', 'checkPortal_callback');
add_action('wp_ajax_checkPortal', 'checkPortal_callback');

//checks if the protal name already exists in the database and returns true if it does not
function checkPortal_callback()
{
	//portal name
	$portal_name = filter_var($_REQUEST['portal_name'],FILTER_SANITIZE_STRING);

	global $wpdb;

	$sql = "SELECT * from " . TABLE_POSTMETA . " WHERE meta_key = 'org_subdomain' AND meta_value = '" . $portal_name . "'";
	$result = $wpdb->get_results ($sql);
	
	//if the portal name was not found return true otherwise false
	if(empty($result))
	{
		echo true;
	}
	else
	{
		echo false;
	}

	wp_die();
}

add_action('wp_ajax_nopriv_generatePassword', 'generatePassword_callback');
add_action('wp_ajax_generatePassword', 'generatePassword_callback');

//generates a password
function generatePassword_callback()
{

	echo json_encode(wp_generate_password());

	wp_die();
}