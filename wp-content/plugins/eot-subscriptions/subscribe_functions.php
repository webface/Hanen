<?php
/**
  * Functions related to the EOT Subscriptions
**/

function display_subscriptions () 
{
    global $current_user;
    global $wpdb;

    wp_get_current_user ();
    $user_id = $current_user->ID;
    $org_id = get_org_from_user ($user_id);
    if($org_id == "")
    {
        $org_id =  get_indiv_from_user($user_id);
    }
    $org = get_post ($org_id);
    $subscriptions = get_current_subscriptions ($org_id);

    if (empty ($subscriptions)) 
    { 
        if (current_user_can ('is_director') || current_user_can('is_individual')) { 
            // only director or individual can purchase subscriptions so only include the link for them. Not students.
?>
            <p>
                You have no subscriptions associated with this organization. Create a new subscription <a href="<?php bloginfo('url'); ?>/new-subscription/">here</a>.
            </p>
<?php 
        } 
        else 
        { 
?>
            <p>
                There are no subscriptions associated with your organization. Please contact your director so they can create a subscription.
            </p>
<?php 
        }
    } 
    else 
    {
        foreach($subscriptions as $subscription)
        {
            $library_id = $subscription->library_id;
            $library = getLibrary($library_id);

            // make sure user accepted terms of use
            $accepted = accepted_terms($library); // Boolean if user has accepted terms
            if (!$accepted)
            {
               return; // do not continue to display the rest of the dashboard becuase user hasn't accepted the terms yet
            }

            // check if LE or LEL is set up yet
            if (($library_id == LE_ID || $library_id == LEL_ID || $library_id == LE_SP_DC_ID || $library_id == LE_SP_OC_ID || $library_id == LE_SP_PRP_ID) && $subscription->setup)//
            {
                // display dashboard
                display_subscription_dashboard ($subscription);
            }
            elseif ($library_id != LE_ID && $library_id != LEL_ID && $library_id != LE_SP_DC_ID && $library_id != LE_SP_OC_ID && $library_id != LE_SP_PRP_ID) // there is a subscription but its not LE so display the dashboard but no need to customize subscription
            {
                // display the dashboard of library != LE or LEL
                display_subscription_dashboard($subscription);
            }
            if($library_id == SE_ID && !$subscription->setup)
            {
                    $subscription_id = $subscription->ID;
                    $course_name = "Child Welfare and Protection";
                    $course_id = CHILD_WELFARE_COURSE_ID;
                    $course_description = "";
                    $data = compact("user_id", "subscription_id", "course_description"); //course description is ommitted in this case
                    $response = createCourse($course_name, $org_id, $data, 1, $course_id); // create the course and copy the modules from $course_id
                    if (isset($response['status']) && !$response['status']) 
                    {
                        echo "ERROR in display_subscriptions: Couldnt Create Course: $course_name " . $response['message'];
                        error_log("ERROR in display_subscriptions: Couldnt Create Course: $course_name " . $response['message']);
                    }
                    else
                    {
                        $upd = $wpdb->update(TABLE_SUBSCRIPTIONS, 
                                    array( 
                                        'setup' => '1' 
                                    ), 
                                    array( 
                                        'id' => $subscription->ID 
                                    ) 
                                );
                                wp_redirect(site_url('/dashboard'));
                                exit();
                    }
            }
          
            if(($library_id == LE_ID || $library_id == LEL_ID || $library_id == LE_SP_DC_ID || $library_id == LE_SP_OC_ID || $library_id == LE_SP_PRP_ID) && !$subscription->setup) 
            {
                // Its an LE library but hasn't been set up (customized) yet, so display group customization

?>
        <h1><?= $library->name; ?> Group Customization</h1>
        <p>
            Thank you for your subscription to ExpertOnlineTraining.com. To help you get started quickly, we will now generate four staff groups for you, each with their own default courses. We have named these courses: New Staff, Returning Staff, Supervisory Staff and Program Staff, but you can rename them any time. You can also add or take away courses at any time. Each course will come pre-packaged with a suggested bundle of 14 video training modules, each with their own quiz and handout.
        </p>
        <p>
            Most staff can complete an assignment of 14 modules in about 6 hours. That’s a solid day of training, already complete before your staff arrive on site! In the next step, you will be given a chance to review each group’s suggested bundle, take away modules you don’t need and add modules you’d rather include.
        </p>
        <p>
            Before we get started, please tell us about your organization, the youth you serve and the learning goals of your staff. Your replies to these questions will help us customize each course.
        </p>
<?php
                $questions = getQuestions($library_id);
                if(count($questions) > 0)
                {
                    $question_number = 1;
                    echo "<form name='group_customization' method='POST'><fieldset><ol>";
                    foreach($questions as $question)
                    {
                        echo "<li><b>" . $question->question . "</b>";
                        $answers = json_decode($question->answer, true);

                        for($i = 1; $i <= count($answers); $i++)
                        {
                            echo "<div><input type='radio' name='gc_question_" . $question->ID . "_answer' id='gc_question_" . $question->ID . "_answer_" . $i . "' value='" . $i . "'/><label for='gc_question_" . $question->ID . "_answer_" . $i . "'>" . $answers[$i] . "</label></div>";
                        }
                        echo "</li><br>";
                        $question_number++;
                    }
                    echo "</ol><input type='hidden' name='sub_id' value='" . $subscription->ID . "'><input type='hidden' name='lib_id' value='" . $subscription->library_id . "'><input type='submit' name='btn' value='submit' autofocus  onclick='return true;'/></fieldset></form>";                
                }
                else
                {
                    echo "<form name='group_customization' method='POST'><fieldset>";
                    echo "<input type='hidden' name='sub_id' value='" . $subscription->ID . "'><input type='hidden' name='lib_id' value='" . $subscription->library_id . "'><input type='submit' name='btn' value='Continue' autofocus  onclick='return true;'/></fieldset></form>";
                }
            }

        }       

        // check if user submitted answers, if so create the 4 default courses and modify them based on the answers.
        if(isset($_POST['btn']))
        {
            // create 4 default courses for this org
            // then modify them according to the answers
            global $base_courses;
            if ($org_id) // make sure we have an org to add these courses to
            {
                foreach ($base_courses as $course_name => $course_id)
                {
                    // Now create the default courses into the new org.
                    //$response = cloneCourse($LU_course_ID, $org_lrn_upon_id, 'false');
                    $subscription_id = $subscription->ID;
                    $course_description = "";
                    $data = compact("user_id", "subscription_id", "course_description"); //course description is ommitted in this case
                    $response = createCourse($course_name, $org_id, $data, 1, $course_id); // create the course and copy the modules from $course_id
                    if (isset($response['status']) && !$response['status']) 
                    {
                        echo "ERROR in display_subscriptions: Couldnt Create Course: $course_name " . $response['message'];
                        error_log("ERROR in display_subscriptions: Couldnt Create Course: $course_name " . $response['message']);
                    }
                }
            }
            else
            {
                echo "ERROR in display_subscriptions: Couldnt create default courses because no org id found.";
                error_log("ERROR in display_subscriptions: Couldnt create default courses because no org id found.");
                return;               
            }

            // now add/remove specific modules from the above courses based on answers
            global $questionnaire_base_course_id;
            $data = compact ("org_id");
            $lib_id = isset($_REQUEST['lib_id']) ? filter_var($_REQUEST['lib_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
            $courses = getCourses(0,$org_id); // get all the courses in this org.

            // create an associative array of course name to course id.
            foreach ($courses as $course)
            {
                $course_IDs[$course->course_name] = $course->ID;
            }
            
            $modules = getModulesByLibrary($lib_id); // get all the modules in the selected library

            // create an associative array of LU Module IDs.
            foreach ($modules as $module)
            {
                $module_IDs[$module['title']] = $module['ID'];
            }

            // get number of questions for our library id
            
            $query = 'SELECT * FROM ' . TABLE_QUESTIONS . ' WHERE library_id = ' . $lib_id." ORDER BY `order`,`ID`";
            $questions = $wpdb->get_results ($query);
            foreach($questions as $question) 
            {
                // get the answer for this question
                if(isset($_POST['gc_question_' . $question->ID . '_answer']))
                {
                    $answer = filter_var($_POST['gc_question_' . $question->ID . '_answer'], FILTER_SANITIZE_NUMBER_INT);
                    foreach ($questionnaire_base_course_id as $course_name => $course_name_id)
                    {
                        // get all the actions needed for each question for each base course
                        $query = 'SELECT * FROM ' . TABLE_QUESTION_MODIFICATIONS . 
                                ' WHERE library_id = ' . $lib_id .
                                ' AND question = ' . $question->ID . 
                                ' AND answer = ' . $answer .
                                ' AND course_name_id = ' . $course_name_id;
                        $actions = $wpdb->get_results($query, 'ARRAY_A');
                        foreach ($actions as $action) {
                            $video_name = $wpdb->get_var('SELECT name FROM ' . TABLE_VIDEOS . ' WHERE id = ' . $action['video_id']);
                            $module_id = isset($module_IDs[$video_name]) ? $module_IDs[$video_name] : 0;
                            $data = compact("org_id","module_id");

                            if ($action['action'] == 'Add')
                            {
                                // add the module to the course. Dont forget the quiz.
                                //$response = addModule($course_IDs[$course_name], $org_subdomain, $data);
                                $sql = "SELECT * FROM ".TABLE_MODULE_RESOURCES." WHERE module_id = $module_id";
                                
                                $module_resources = $wpdb->get_results($sql);

                                if(isset($module_resources) && (count($module_resources) > 0))
                                {
                                    foreach ($module_resources as $qh) 
                                    {
                                        $wpdb->insert(TABLE_COURSE_MODULE_RESOURCES, 
                                            array(
                                                'course_id' => $course_IDs[$course_name],
                                                'module_id' => $module_id,
                                                'resource_id' => $qh->resource_id,
                                                'type' => $qh->type
                                            )
                                        );

                                        // @TODO check that there are no errors inserting the moudles
                                    }
                                }
                                echo "<p>Trying to add $video_name to " . $course_IDs[$course_name] . "</p>";
                            }
                            elseif ($action['action'] == 'Remove')
                            {
                                // remove the module from the course. Dont forget the quiz.
                                //$response = deleteModule($course_IDs[$course_name], $org_subdomain, $data);
                                $response = $wpdb->delete(TABLE_COURSE_MODULE_RESOURCES, 
                                    array(
                                        'course_id' => $course_IDs[$course_name],
                                        'module_id'=>$module_id
                                    )
                                );
                                echo "<p>Trying to delete $video_name from " . $course_IDs[$course_name] . "</p>";
                                if ($response === false)
                                    echo "ERROR in display subscription: Couldn't remove module from course: " . $wpdb->last_error . "<br>";
                            }
                        }
                    }
                }
            }

            // set subscription set up variable to 1 to indicate it was set up. 
            $sub_id = filter_var($_POST['sub_id'],FILTER_SANITIZE_NUMBER_INT);
            $upd = $wpdb->update(TABLE_SUBSCRIPTIONS, 
                array( 
                    'setup' => '1' 
                ), 
                array( 
                    'id' => $sub_id 
                ) 
            );
            wp_redirect(site_url('/dashboard'));
            exit();
        }
    }
}

function get_current_subscriptions ($org_id) {
    // Check if there user has an organization id
    if($org_id == null){
        echo "Something went wrong. Your account does not have an organization ID. Please contact the administrator."; 
        return;
    }
	global $wpdb;
	$date = date ('Y-m-d');

	$sql = "SELECT * from ".TABLE_SUBSCRIPTIONS." WHERE org_id = $org_id AND start_date <= '$date' AND end_date >= '$date' AND status = 'active' ORDER BY start_date";

	$results = $wpdb->get_results ($sql);

	return $results;
}

/* Create new subscription for an exsisting user or newly registed user. 
 * @param int $user_id - wp user id
 */
function new_subscription ($user_id = 0) {
	global $current_user;
    wp_get_current_user ();

    // check if were passing in a user_id. If so, assign $user to that user's info
    if($user_id > 0)
    {
        $user = get_user_by( 'ID', $user_id );        
    }
    else
    {
        $user = $current_user;
    }

	$org_id = get_org_from_user ($user->ID);
	$org = get_post ($org_id);
?>
	<form id="new-subscription" data-user_id="" action="#">
		<h3>Subscribe</h3>
		<fieldset>
			
            <legend><h2>Subscribe Online using your Credit Card</h2></legend>
			<ol>
                <li>
                    <input type="checkbox" name="le" value="<?= LE_ID ?>" class="library">&nbsp;&nbsp;
                    <label for="chk_le"><span class="heading"><b>Leadership Essentials - Full Pack</b></span></label>
                    <p class="small" style="margin: 9px 0 9px 21px">
                        Our complete library of <b><?= NUM_VIDEOS ?></b> videos, quizzes and resources covering a wide array of summer camp-related topics.
                    </p>
                </li>
                <li>
                    <input type="checkbox" name="le_sp_dc" value="<?= LE_SP_DC_ID ?>" class="library">&nbsp;&nbsp;
                    <label for="chk_le_sp_dc"><span class="heading"><b>Leadership Essentials - Starter Pack - Day Camps</b></span></label>
                    <p class="small" style="margin: 9px 0 9px 21px">
                        Access to <b><?= NUM_VIDEOS_LE_SP_DC ?></b> videos, quizzes and resources covering a wide array of summer camp-related topics specific to day camps.
                    </p>
                </li>

                <li>
                    <input type="checkbox" name="le_sp_oc" value="<?= LE_SP_OC_ID ?>" class="library">&nbsp;&nbsp;
                    <label for="chk_le_sp_oc"><span class="heading"><b>Leadership Essentials - Starter Pack - Overnight Camps</b></span></label>
                    <p class="small" style="margin: 9px 0 9px 21px">
                        Access to <b><?= NUM_VIDEOS_LE_SP_OC ?></b> videos, quizzes and resources covering a wide array of summer camp-related topics specific to overnight camps.
                    </p>
                </li>
                <li>
                    <input type="checkbox" name="le_sp_prp" value="<?= LE_SP_PRP_ID ?>" class="library">&nbsp;&nbsp;
                    <label for="chk_le_sp_prp"><span class="heading"><b>Leadership Essentials - Starter Pack - Park & Rec Programs</b></span></label>
                    <p class="small" style="margin: 9px 0 9px 21px">
                        Access to <b><?= NUM_VIDEOS_LE_SP_PRP ?></b> videos, quizzes and resources covering a wide array of staff and leadership related topics specific to park & rec programs.
                    </p>
                </li>
<!--
                <li>
                    <input type="checkbox" name="ce" value="<?= CE_ID ?>" class="library">&nbsp;&nbsp;
                    <label for="chk_ce"><span class="heading"><b>Clinical Essentials</b></span></label> - <a class="sm" href="#prices_ce">See Pricing</a>
                    <p class="small" style="margin: 9px 0 9px 21px">
                        A library of presentations on clinical topics for Camp Nurses and Doctors.
                    </p>
                </li>
-->
				<li>
					<input type="checkbox" name="se" value="<?= SE_ID ?>" class="library">&nbsp;&nbsp;
					<label for="chk_se"><span class="heading"><b>Child Welfare & Protection</b></span></label>
				</li>            
			</ol>
            <h2>Subscribe with a Different Payment Method</h2>
            <p>
                If you prefer to subscribe and pay by check or credit card over the phone, then you can <b>call us Toll-Free at 877-237-3931.</b>
            </p>
		</fieldset>

		<h3>Staff Accounts</h3>
		<fieldset>
            <h2>Please indicate how many staff accounts you will need:</h2>
            <table class="staff_accounts subscription_confirm Tstandard data" id="le_table">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <b>Leadership Essentials - Complete</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No. of staff accounts:
                        </td>
                        <td>
                            <input type="text" name="le_staff">
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="staff_accounts subscription_confirm Tstandard data" id="le_sp_dc_table">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <b>Leadership Essentials - Starter Pack - Day Camps</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No. of staff accounts:
                        </td>
                        <td>
                            <input type="text" name="le_sp_dc_staff">
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="staff_accounts subscription_confirm Tstandard data" id="le_sp_oc_table">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <b>Leadership Essentials - Starter Pack - Overnight Camps</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No. of staff accounts:
                        </td>
                        <td>
                            <input type="text" name="le_sp_oc_staff">
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="staff_accounts subscription_confirm Tstandard data" id="le_sp_prp_table">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <b>Leadership Essentials - Starter Pack - Parks & Rec Programs</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            No. of staff accounts:
                        </td>
                        <td>
                            <input type="text" name="le_sp_prp_staff">
                        </td>
                    </tr>
                </tbody>
            </table>

<!--
			<table class="staff_accounts subscription_confirm Tstandard data" id="ce_table">
				<tbody>
					<tr>
						<td colspan="2">
							<b>Clinical Essentials</b>
						</td>
					</tr>
					<tr>
						<td>
							No. of staff accounts:<br />
							<span>(first 12 are included)</span>
						</td>
						<td>
							<input type="text" name="ce_staff">
						</td>
					</tr>
				</tbody>
			</table>
-->
			<table class="staff_accounts subscription_confirm Tstandard data" id="se_table">
				<tbody>
					<tr>
						<td colspan="2">
							<b>Child Welfare & Protection</b>
						</td>
					</tr>
					<tr>
						<td>
							No. of staff accounts:<br />
							<span>(first 20 are included)</span>
						</td>
						<td>
							<input type="text" name="se_staff">
						</td>
					</tr>
				</tbody>
			</table>
<!--
			<table class="staff_accounts subscription_confirm Tstandard data">
				<tbody>
					<tr>
						<td>
							Coupon Code:
						</td>
						<td>
							<input type="text" name="coupon">
						</td>
					</tr>
				</tbody>
			</table>
-->
		</fieldset>

		<h3>Total</h3>
		<fieldset>
            <h2>Please verify the information below is correct:</h2>

			<table class="staff_accounts subscription_confirm Tstandard data" id="total_table">
			</table>
            <input type="hidden" name="total_price" id="total" value="0.00" />
		</fieldset>

		<h3>Terms of Use</h3>
		<fieldset>
			<h2>Terms of Use</h2>
            <ol class="terms">
                <li>I understand that I am purchasing a license to use copyrighted works (a library of video training modules, online quizzes, and related print materials) owned by CampSpirit, LLC, and Target Directories Corporation.  This license allows only the use described below as respects the works.  These works are intended for educational use only.  Any commercial use by me or my organization, such as charging a fee to someone in exchange for viewing these modules or uploading the videos to any website, is strictly forbidden.</li>
                <li>I understand that, pursuant to the license, the works may be read or viewed only by me and my employees or volunteers.  No other use is permitted.  During the year of subscription, the works may be viewed an unlimited number of times by me and by each such employee or volunteer.  However, no copies of any kind (other than those required for such viewing on desktops, laptops, portable media players, or other similar devices of mine or those of my employees or volunteers) of any video training modules or quizzes may be made by me or by any of my employees or volunteers.  During the year of subscription, I may duplicate paper copies of print materials (e.g., handouts) for educational use by my employees of volunteers and for no other purpose.  Handouts may be included in staff training manuals during the year of subscription.</li>
                <li>I understand that my license to use to these works (video training modules, online quizzes, and print materials) and my legal right to view them expires on December 31st of the year of purchase.  If I or my employees or volunteers wish to continue viewing the modules, quizzes, or print materials, or have access to the updated online library, I must renew my subscription to the library on or after January 1st of the subsequent year.</li>
                <li>I agree to advise my employees and volunteers that these video training modules are intended for their educational use only and that other use, sale, or distribution is strictly forbidden.  If I become aware of an employee or volunteer who may have violated these terms, (e.g., posting a module on a personal website or a commercial site such as YouTube) then I agree: (a) to immediately direct that employee or volunteer to summarily remove the module; and (b) to immediately notify Target Directories and CampSpirit of that wrongful conduct along with the name and address of that employee or volunteer.  I understand that this material is copyrighted and that copyright infringements may be prosecuted to the full extent of the law.</li>
                <li>I understand that although these modules cover material outlined in certain accreditation standards, such as those provided by the American Camp Association or the Ontario Camp Association, viewing these modules does not constitute compliance with any particular standard.  I understand that these modules, and the quizzes, handouts, and discussion questions that accompany them, are intended to help camps meet their educational goals and that it is a camp director’s sole responsibility to ensure compliance with any and all applicable laws and standards.</li>
                <li>I understand that although the content of some video training modules may discuss abnormal or problematic thoughts, behaviors, and emotions, as well as some forms of psychopathology, there is no expressed or implied psychotherapeutic or other treatment relationship between my camp and its employees, volunteers, and patrons / campers and the owners, employees, and volunteers of Target Directories Corporation and CampSpirit, LLC.  These relationships are best described as educational.  I understand that medical or psychological questions I may have about my employees, volunteers, or patrons / campers are best answered in consultation with a licensed health care professional.</li>
                <li>Although the video training modules and associated print materials are designed to maximize the resources and well-being of an organization’s employees, volunteers, and patrons / campers, neither Target Directories Inc. nor CampSpirit, LLC is a guarantor of results.  Neither Target Directories Inc. nor CampSpirit, LLC, or any of its owners, employees, or volunteers, may be held liable for any camper’s or staff member’s illnesses, injuries, accidents, mental health problems, behavior problems, or lapses in judgment that may occur during or after viewing these video training modules and associated quizzes and print materials.</li>
                <li>I understand that it is my responsibility to preview all of these works (e.g., video training modules, quizzes, and handouts) in order to familiarize myself with the content.  In places where my organization’s policies or procedures differ in important ways from what is recommended in the works, I understand that it is my responsibility to educate my employees and volunteers about these differences and instruct them in my organization’s policies and procedures.</li>
                <li>Our team is so confident that you’ll love training your staff with EOT that we guarantee your satisfaction. If you have any questions or need customer support after activating your subscription, simply contact our team toll-free (877) 237-3931, M-F during the hours of 9am to 5pm EST. We promise to do everything we can to answer your questions and get you up and running. We will also help you strategize the best ways to get the most out of your subscription.</li>
                <li>Because activating your subscription instantly gives you full access to our digital content, we cannot refund your subscription fee after activation. However, we are happy to roll over any unused staff accounts when you renew your subscription. For example, if you purchased 100 staff accounts in 2014 but used only 95 staff accounts, we will credit your 2015 account with 5 staff accounts. Note that unused staff accounts can only be rolled over to your own EOT subscription and only when you renew for the following year. Unused staff accounts are not transferable to other organizations.</li>
                <p class="accept_terms">
                    <input type="checkbox" value="accept" name="terms_of_use" required/> <label><b>I accept the terms of use</b></label>
                </p>
            </ol>
		</fieldset>

		<h3>Payment</h3>
	<fieldset id="new-subscription-p-3" role="tabpanel" aria-labelledby="new-subscription-h-3" class="body current" aria-hidden="false" style="display: block; left: 0px;">
    <?php
            $org_name = apply_filters ('the_title', $org->post_title);
            $full_name = ucwords ($user->user_firstname . " " . $user->user_lastname);
            $address = get_post_meta ($org_id, 'org_address', true);
            $city = get_post_meta ($org_id, 'org_city', true);
            $state = get_post_meta ($org_id, 'org_state', true);
            $country = get_post_meta ($org_id, 'org_country', true);
            $zip = get_post_meta ($org_id, 'org_zip', true);
            $phone = get_post_meta ($org_id, 'org_phone', true);
        ?>
            <h2>Please complete your payment details:</h2>
            <table class="staff_accounts subscription_confirm Tstandard data" id="total_table_payment">
            </table>
            <h2>Billing Address</h2>
            <div class="form-row">
                <label>Organization Name</label>
                <input type="text" name="org_name" value="<?php echo $org_name; ?>" required/>
            </div>
            <div class="form-row">
                <label>Cardholder Name</label>
                <input type="text" name="full_name" value="<?php echo $full_name; ?>" required/>
            </div>
            <div class="form-row">
                <label>Address</label>
                <input type="text" name="address" value="<?php echo $address; ?>" required/>
            </div>
            <div class="form-row">
                <label>City</label>
                <input type="text" name="city" value="<?php echo $city; ?>" required/>
            </div>
            <div class="form-row">
                <label>State/Province</label>
                <input type="text" name="state" value="<?php echo $state; ?>" required/>
            </div>
            <div class="form-row">
                <label>Country</label>
                <input type="text" name="country" value="<?php echo $country; ?>" required/>
            </div>
            <div class="form-row">
                <label>Zip/Postal Code</label>
                <input type="text" name="zip" value="<?php echo $zip; ?>" required/>
            </div>
            <div class="form-row">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $phone; ?>" required/>
            </div>
            <h2>Credit Card</h2>
            <?php 
                $cus_id = get_post_meta($org_id, 'stripe_id', true);
                $cards = get_customer_cards ($cus_id);
            ?>

            <?php if (!empty($cards)) { ?>
                <table cellpadding="5" cellspacing="0" width="90%" class="cc_cards_list">
                    <tr>
                        <td>&nbsp;</td>
                        <td>Type</td>
                        <td>Number</td>
                        <td>Expiration</td>
                        <td>CVC</td>
                    </tr>
                    <?php foreach ($cards as $card) { ?>
                        <tr>
                            <td><input type="radio" name="cc_card" value="<?php echo $card->id; ?>" /></td>
                            <td><?php echo $card->brand; ?></td>
                            <td>**** **** **** <?php echo $card->last4; ?></td>
                            <td><?php echo $card->exp_month; ?> / <?php echo $card->exp_year; ?></td>
                            <td>***</td>
                        </tr>
                    <?php } ?>
                </table>
                <a href="#" id="new_card">Add new Card</a>
            <?php } ?>
                <div id="new_cc_form" <?php if (!empty($cards)) { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?> >
                    <div class="form-row">
                        <label>Card Number</label>
                        <input type="text" size="20" autocomplete="off" name="cc_num" value="" required/>
                    </div>
                    <div class="form-row">
                        <label>CVC</label>
                        <input type="text" size="4" autocomplete="off" name="cc_cvc" value="" required/>
                    </div>
                    <div class="form-row">
                        <label>Expiration</label>
                        <select name="cc_mon" required>
                            <option value="" selected="selected">MM</option>
                            <?php for ($i = 1 ; $i <= 12 ; $i++) { ?>
                                <option value="<?php if ($i < 10) {echo "0";} echo $i; ?>"><?php if ($i < 10) {echo "0";} echo $i; ?></option>
                            <?php } ?>
                        </select>
                        <span> / </span>
                        <select name="cc_yr" required>
                            <option value="" selected="selected">YYYY</option>
                            <?php for ($i = date('Y') ; $i <= (date('Y') + 10) ; $i++) { ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
            </div>
            <?php if ($cus_id) { ?><input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" /><?php } ?>
            <input type="hidden" name="email" value="<?php echo $user->user_email; ?>" />
            <input type="hidden" name="org_id" value="<?php echo $org_id; ?>" />
            <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>" />
            <input type="hidden" name="method" value="Stripe" />

            <p>
                <i class="fa fa-lock"></i> This site uses 256-bit encryption to safeguard your credit card information.
            </p>
        
        </fieldset>
	</form>	


<?php
}

/**
 * Checks to see if array item exists in multi dimentional array
 */

function in_multiarray($elem, $array, $field)
{
    $top = sizeof($array) - 1;
    $bottom = 0;
    while($bottom <= $top)
    {
        if($array[$bottom][$field] == $elem)
            return true;
        else 
            if(is_array($array[$bottom][$field]))
                if(in_multiarray($elem, ($array[$bottom][$field])))
                    return true;

        $bottom++;
    }        
    return false;
}

// show the dashboard box for the subscription
function display_subscription_dashboard ($subscription) 
{
    global $wpdb;
    global $current_user;
    $user_id = $current_user->ID; // Wordpress user ID
    $org_id = get_org_from_user ($user_id); // Organization ID
    $data = compact ("org_id");
//    $staff_accounts = getEotUsers($org_id); // Staff accounts registered in this portal.
    $courses = getCoursesById($org_id, $subscription->ID); // All the courses in the org && subscription.

    // get the library title
    $sql = "SELECT name from ".TABLE_LIBRARY." WHERE ID = ".$subscription->library_id;
    $results = $wpdb->get_results ($sql);
    $library_title = (!empty($results)) ? $results[0]->name : "Unknown Library";
    $staff_credits = $subscription->staff_credits; // Maximum # Staff
    $upgrades = getUpgrades ($subscription->ID, SUBSCRIPTION_START, SUBSCRIPTION_END); // get upgrades from

    // Add upgrade number of staff
    if($upgrades)
    {
        foreach($upgrades as $upgrade)
        {
            $staff_credits += $upgrade->accounts;
        }
    }
   
    // filter out duplicate users who are enrolled in multiple courses
    $learners = array();
    foreach($courses as $course)
    {
        if($users_in_course = getEnrolledUsersInCourse($course['ID']))
        {
            foreach($users_in_course as $user)
            {
                //echo $user['id']."<br>";
                //echo in_multiarray($user['id'], $learners, 'id')."<br>";
                if(!in_multiarray($user['ID'], $learners, 'ID')){
                    array_push($learners, $user);
                }
            }
        }
    }

?>      
    <div class="dashboard_border">
    <h1><?= $library_title ?> <?= SUBSCRIPTION_YEAR ?> <span class="bor_tag">Director Account</span>
    </h1>
    <div class="content_right">
        <div class="clear"></div>
        <div class="menu">
            <a href="?part=view_library&subscription_id=<?= $subscription->ID ?>" onclick="load('load_view_library')">
              <div class="thumbnail">
                  <i class="fa fa-youtube-play" alt="Content"></i>
              </div>
              <div class="para">
                  <h1>View Content</h1>
                  <br/>
                  Watch videos, download handouts, and read articles
              </div>
            </a>
        </div>
        <div class="menu">
          <a href="?part=administration&subscription_id=<?= $subscription->ID ?>" onclick="load('load_administration')">
            <div class="thumbnail">
                <i class="fa fa-cogs" alt="Administration"></i>
            </div>
            <div class="para">
                <h1>Administration</h1>
                <br>
                Create courses and staff accounts
            </div>
          </a>
        </div>
        
        <div class="menu">
          <a href="?part=customer_success&subscription_id=<?= $subscription->ID ?>">
            <div class="thumbnail">
                <i class="fa fa-rocket" alt="Blast Off"></i>
            </div>
            <div class="para">
                <h1>Blast Off</h1>
                <br>
                Quick tips to harness the power of EOT
            </div>
          </a>
        </div>
        <div class="menu">
          <a href="?part=statistics&subscription_id=<?= $subscription->ID ?>" onclick="load('load_statistics')">
            <div class="thumbnail">
                <i class="fa fa-bar-chart-o" alt="Statistics"></i> 
            </div>
            <div class="para">
                <h1>Statistics</h1>
                <br>
                Track your staff's progress
            </div>
          </a>
        </div>
    </div>
    <div class="content_left">
      <table class="tb_border">
        <tbody>
          <tr>
            <td>
              Max. Staff <br>(<a href="?part=upgradesubscription&subscription_id=<?= $subscription->ID ?>">Add More Staff</a>)
            </td>
            <td>
                <?= $staff_credits; ?>
            </td>
          </tr>
          <tr>
            <td class="s1">
                Active Staff
            </td>
            <td class="s2">
                <?= count($learners); ?>                
            </td>
        </tr>
          <tr>
            <td>
              Active Courses
            </td>
            <td>
                <?= count($courses); ?>
            </td>
          </tr>
        </tbody>
      </table>
      <br>

        <div class="dashboard_button">
          <a href="?part=staff_lounge&subscription_id=<?= $subscription->ID ?>" onclick="load('load_staff_lounge')">
            <div class="title" style="padding-top: 5px;">
              <b>Virtual Staff Lounge</b>
              <br>Manage your Forum
            </div>
          </a>
        </div>

        <div class="dashboard_button" style="margin-top:5px; padding-top:5px;">
          <a href="?part=directors_corner&subscription_id=<?= $subscription->ID ?>">
            <div class="title">
              <b>Director's Corner</b>
              <br>Tips and Guides
            </div>
          </a>
        </div>   
    </div>
  </div>
<?php
}

function update_subscription ($subscription_id) {
    global $current_user;
    wp_get_current_user ();
    $result = getSubscriptions($subscription_id);
    // Check if there are a subscription base on the subscription ID
    if($result)
    {
        $subscription = $result; // get the subscription info for this subscription
        $user_id = $subscription->manager_id ? $subscription->manager_id : 0; // The manager's user ID in wp
        $org_id = $subscription->org_id ? $subscription->org_id : 0; // The org id of the subscription
        if($user_id > 0 && $org_id > 0)
        {
            $org = get_post ($org_id);
            $library_id = $subscription->library_id; // The library ID
            $result = getLibraries($library_id);
            // Check if there are a library base on the subscription's library ID
            if($result)
            {
                $library = $result;
                $library_name = $library->name; // Name of the Library.
?>
                <form id="new-subscription" action="#" >
                    <h3>Staff Accounts</h3>
                    <fieldset>
                        <h2>Please indicate how many staff accounts you will need:</h2>
                        <table class="staff_accounts subscription_confirm Tstandard data" id="<?= strtolower($library->tag); ?>_table">
                            <tbody>
                                    <tr>
                                        <td colspan="2">

                                            <b><?= $library_name?> - Complete</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Current No. of staff Accounts
                                        </td>
                                        <td>
                                            <?= $subscription->staff_credits ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            No. of staff accounts to be added:
                                        </td>
                                        <td>
                                            <input type="text" name="<?= strtolower($library->tag); ?>_staff">
                                        </td>
                                    </tr>
                            </tbody>
                            <input type="hidden" name="target" value="upgradeSubscription">
                            <input type="hidden" name="<?= strtolower($library->tag) ?>" value="true">
                        </table>
                        </fieldset>

                        <h3>Payment</h3>
                        <fieldset>
                    <?php
                        $org_name = apply_filters ('the_title', $org->post_title);
                        $full_name = ucwords ($current_user->user_firstname . " " . $current_user->user_lastname);
                        $address = get_post_meta ($org_id, 'org_address', true);
                        $city = get_post_meta ($org_id, 'org_city', true);
                        $state = get_post_meta ($org_id, 'org_state', true);
                        $country = get_post_meta ($org_id, 'org_country', true);
                        $zip = get_post_meta ($org_id, 'org_zip', true);
                        $phone = get_post_meta ($org_id, 'org_phone', true);
                    ?>
                   <h2>Please complete your payment details:</h2>
                        <table id="total_table" class="staff_accounts subscription_confirm Tstandard data">
                        </table>
                        <div class="form-row">
                            <label>Organization Name</label>
                            <input type="text" name="org_name" value="<?php echo $org_name; ?>" required/>
                        </div>
                        <div class="form-row">
                            <label>Cardholder Name</label>
                            <input type="text" name="full_name" value="<?php echo $full_name; ?>" required/>
                        </div>
                        <div class="form-row">
                            <label>Address</label>
                            <input type="text" name="address" value="<?php echo $address; ?>" required/>
                        </div>
                        <div class="form-row">
                            <label>City</label>
                            <input type="text" name="city" value="<?php echo $city; ?>" required/>
                        </div>
                        <div class="form-row">
                            <label>State/Province</label>
                            <input type="text" name="state" value="<?php echo $state; ?>" required/>
                        </div>
                        <div class="form-row">
                            <label>Country</label>
                            <input type="text" name="country" value="<?php echo $country; ?>" required/>
                        </div>
                        <div class="form-row">
                            <label>Zip/Postal Code</label>
                            <input type="text" name="zip" value="<?php echo $zip; ?>" required/>
                        </div>
                        <div class="form-row">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?php echo $phone; ?>" required/>
                        </div>
                        <h2>Credit Card</h2>
                        <?php 
                            $cus_id = get_post_meta($org_id, 'stripe_id', true);
                            $cards = get_customer_cards ($cus_id);
                        ?>

                        <?php if (!empty($cards)) { ?>
                            <table cellpadding="5" cellspacing="0" width="90%" class="cc_cards_list">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Type</td>
                                    <td>Number</td>
                                    <td>Expiration</td>
                                    <td>CVC</td>
                                </tr>
                                <?php foreach ($cards as $card) { ?>
                                    <tr>
                                        <td><input type="radio" name="cc_card" value="<?php echo $card->id; ?>" /></td>
                                        <td><?php echo $card->brand; ?></td>
                                        <td>**** **** **** <?php echo $card->last4; ?></td>
                                        <td><?php echo $card->exp_month; ?> / <?php echo $card->exp_year; ?></td>
                                        <td>***</td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <a href="#" id="new_card">Add new Card</a>
                        <?php } ?>
                            <div id="new_cc_form" <?php if (!empty($cards)) { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?> >
                                <div class="form-row">
                                    <label>Card Number</label>
                                    <input type="text" size="20" autocomplete="off" name="cc_num" value="" required/>
                                </div>
                                <div class="form-row">
                                    <label>CVC</label>
                                    <input type="text" size="4" autocomplete="off" name="cc_cvc" value="" required/>
                                </div>
                                <div class="form-row">
                                    <label>Expiration</label>
                                    <select name="cc_mon" required>
                                        <option value="" selected="selected">MM</option>
                                        <?php for ($i = 1 ; $i <= 12 ; $i++) { ?>
                                            <option value="<?php if ($i < 10) {echo "0";} echo $i; ?>"><?php if ($i < 10) {echo "0";} echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span> / </span>
                                    <select name="cc_yr" required>
                                        <option value="" selected="selected">YYYY</option>
                                        <?php for ($i = date('Y') ; $i <= (date('Y') + 10) ; $i++) { ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                        </div>
                        <?php if ($cus_id) { ?>
                            <p>
                                <i class="fa fa-lock"></i> This site uses 256-bit encryption to safeguard your credit card information.
                            </p>
                            <h2>Others:</h2>
                            <textarea id="discount_notes" name="discount_notes" rows="4" cols="30" style="resize: none;" onclick="this.focus();this.select();"><?= DEFAULT_MESSAGE_FOR_DISCOUNT_NOTES ?></textarea> 
                            <textarea id="other_notes" name="other_notes" rows="4" cols="30" style="resize: none;" onclick="this.focus();this.select();"><?= DEFAULT_MESSAGE_FOR_OTHER_NOTES ?></textarea>
                            <input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" />
                        <?php } ?>
                        <input type="hidden" name="email" value="<?php echo $current_user->user_email; ?>" />
                        <input type="hidden" name="org_id" value="<?php echo $org_id; ?>" />
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                        <input type="hidden" name="subscription_id" value="<?php echo $subscription_id; ?>" />
                        <input type="hidden" name="library_id" value="<?php echo $library_id; ?>" />
                        <input type="hidden" name="method" value="Stripe" />
                        <input type="hidden" name="action" value="upgrade_subscription">
                    </fieldset>
                </form> 
        <?php
            }
            else
            {
                echo "Unable to find the library for the subscription. Please contact the administrator.";
            }
        }
        else
        {
            echo "Could not find the Organization ID or Manager ID. Please update them first. ";
        }
    }
    else
    {
        echo "Unable to find this subscription ID. Please contact the administrator.";
    }
}


/* Allow sales reps to create a new subscription for an exsisting user or newly registed user. 
 * @param int $user_id - wp user id
 */
function sales_rep_new_subscription ($user_id = 0) {

    // make sure we have a user ID to create a subscription for
    if (!$user_id)
    {
        return;
    }

    $user = get_user_by( 'ID', $user_id ); // get the WP user object
    $org_id = get_org_from_user ($user_id);
    $org = get_post ($org_id);
?>

<!--   STYLE   -->
        <style type="text/css">
        .table_header {
            height: 30px;
            margin-top: 25px;
            width: 665px;
        }
        .table_header_list {
            background-color: #C8E0EE;
            border-top: 1px solid #888;
            border-bottom: 1px solid #888;
            font-weight: bold;
            float: left;
            display: block;
            padding: 5px;
            text-align: center;
            margin: 0;
        }
        .table_header_list.library {
            width: 215px;
            border-left: 1px solid #888;
        }
        .table_header_list.amount {
            width: 180px;
            border-left: 1px solid #888;
        }
        .table_header_list.discount {
            width: 125px;
            border-left: 1px solid #888;
        }
        .table_header_list.subtotal {
            width: 100px;
            border-left: 1px solid #888;
            border-right: 1px solid #888;
        }
        .library {
            width: 215px;
            list-style: none outside none;
        }
        .amount {
            width: 180px;
            border-left: 1px solid #FFF;
            list-style: none outside none;
        }
        .discount {
            width: 125px;
            border-left: 1px solid #FFF;
            list-style: none outside none;
        }
        .subtotal {
            width: 100px;
            border-left: 1px solid #FFF;
            list-style: none outside none;
        }
        .library img {
            margin: 2px 5px 2px 2px;
            float: left;
        }
        .calc_topics {
            height: 30px;
            width: 663px;
            border-right: 1px solid #888;
            border-left: 1px solid #888;
        }
        .calc_le {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_lel, .calc_le_sp_dc, .calc_le_sp_oc, .calc_le_sp_prp {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_ss {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
    
        .calc_me, .calc_bc {

            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_bc img {
            margin: 2px 5px 2px 2px;
            float: left;
        }
        .calc_se {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        .calc_topics.le {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.lel, .calc_topics.le_sp_dc, .calc_topics.le_sp_oc, .calc_topics.le_sp_prp  {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.ss {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.se {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        .calc_topics.me {
            border-bottom: 1px solid #888;
            height: 30px;
        }
        
        .calc_ce 
        {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        
        .calc_topics.ce
        {
            border-bottom: 1px solid #888;
            height: 30px;
        }

        .calc_dd 
        {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 20px;
        }
        
        .calc_topics.dd
        {
            border-top: 1px solid #888;
            height: 30px;
        }

        .datadisk {
            width: 542px;
            list-style: none outside none;
        }

        .datadisk img {
            margin: 2px 5px 2px 2px;
            float: left;
        }

        input.small_box {
            text-align: center;
            width: 30px;
        }
        input.medium_box {
            text-align: center;
            width: 40px;
        }
        input.large_box {
            text-align: center;
            width: 60px;
        }
        .expanded_top {
            height: 30px;
            text-align: center;
        }
        .expanded_bot {
            text-align: center;
        }
        .amount_x {
            float: left;
            display: block;
            width: 15px;
            text-align: center;
        }
        .amount_left {
            float: left;
            width: 120px;
            text-align: left;
        }
        .amount_right {
            float: left;
            width: 45px;
            text-align: center;
        }
        #calc_body .amount {
            display: none;
        }
        #calc_body .discount {
            display: none;
        }
        #calc_body .subtotal {
            display: none;
        }
        #calc_total {
            height: 35px;
            width: 663px;
            border: 1px solid #888;
            text-align: right;
            padding-top: 15px;
        }
        #text_total {
            float: left;
            width: 550px;
            text-align: right;
        }
        #sum_total {
            float: left;
            width: 113px;
            text-align: center;
        }
        #submit_calc {
            width: 150px;
            height: 30px;
            float: right;
            margin-top: 20px;
            font-weight: bold;
        }
        #calc_footer {
            width: 663px;
            height: 40px;
        }
        #billing {
            display: none;
        }
        .billing_item {
            height: 50px;
            width: 663px;
            border-right: 1px solid #888;
            border-left: 1px solid #888;
            border-bottom: 1px solid #888;
        }
        .bill_item {
            float: left;
            display: block;
            padding: 5px;
            margin: 0;
            height: 40px;
        }
        #bill_total {
            height: 26px;
            width: 663px;
            border: 1px solid #888;
            text-align: right;
            padding-top: 8px;
        }
        .border_left {
            border-left: 1px solid #888;
        }
        #bill_sum_total {
            float: left;
            width: 113px;
            text-align: center;
        }
        #bill_form label {
            display: block;
            float: left;
            font-weight: bold;
            width: 130px;
        }
        #creditcard_opts img {
            margin-left: 130px;
        }
        #submit_bill {
            width: 150px;
            height: 30px;
            margin: 10px 0;
            font-weight: bold;
        }
        #error_box {
             background-color: #9F0000;
             color: #FFF;
             width: 626px;
             margin: 10px 0;
             padding: 5px 20px;
             display: none;
        }
        #error_box h2 {
             margin: 5px 0;
             padding: 0;
             color: #FFF;
             font-size: 21px;
             text-decoration: underline;
        }
        #bill_response {
            display: none;
        }
        .processing_payment {
            display: none;
        }
        #referred_other {
            display: none;
        }
        </style>


<!--   SCRIPT   -->
        <script type="text/javascript">
        var library, amount, discount, subtotal, topic;
        
        // Our JSON object to store all data
        // Setting the initial prices to zero is important because parseFloat doesn't work on "" values
        var data = {
        "libraries"     :   {   "le"        :   {  "id"         :   <?= LE_ID ?>, 
                                                   "name"       :   "Leadership Essentials", 
                                                   "status"     :   false,
                                                   "data"       :   { "dashboard"   : { "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   },
                                                                      "staff"       : { "number"            :   0,
                                                                                        "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   }               
                                                                    }
                                                },
                                "le_sp_dc"       :   {  "id"         :   <?= LE_SP_DC_ID ?>, 

                                                   "name"       :   "Leadership Essentials - Starter Pack - Day Camps", 
                                                   "status"     :   false, 
                                                   "data"       :   { "dashboard"   : { "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   },
                                                                      "staff"       : { "number"            :   0,
                                                                                        "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   }               
                                                                    }
                                                },
                                "le_sp_oc"       :   {  "id"         :   <?= LE_SP_OC_ID ?>, 

                                                   "name"       :   "Leadership Essentials - Starter Pack - Overnight Camps", 
                                                   "status"     :   false, 
                                                   "data"       :   { "dashboard"   : { "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   },
                                                                      "staff"       : { "number"            :   0,
                                                                                        "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   }               
                                                                    }
                                                },
                                "le_sp_prp"       :   {  "id"         :   <?= LE_SP_PRP_ID ?>, 

                                                   "name"       :   "Leadership Essentials - Starter Pack - Parks & Rec Programs", 
                                                   "status"     :   false, 
                                                   "data"       :   { "dashboard"   : { "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   },
                                                                      "staff"       : { "number"            :   0,
                                                                                        "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   }               
                                                                    }
                                                },
                                "se"       :   {  "id"         :   <?= SE_ID ?>, 

                                                   "name"       :   "Child Welfare & Protection", 
                                                   "status"     :   false, 
                                                   "data"       :   { "dashboard"   : { "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   },
                                                                      "staff"       : { "number"            :   0,
                                                                                        "disc_type"         :   "",
                                                                                        "disc_amount"       :   0,
                                                                                        "total_disc_value"  :   0,
                                                                                        "subtotal"          :   0   }               
                                                                    }
                                                }                                                        
                            },
        "org_id"    :   "<?php echo $org_id; ?>", 
        "total"     :   0,
        "datadisk"  :   {
                            "status"    :   false,
                            "subtotal"  :   0
                        }
        };
        
        jQuery(function($) {
            $(document).ready(function() {
            
                $('#submit_bill').click( function() {
                    var button_ref = $(this);
                    $(button_ref).attr('disabled','disabled');
                    $('#error_box').slideUp();
                    $('.processing_payment').slideDown();
                    $('#sales-rep-new-subscription #loading').show();
                

                    var eot_status = 1;
                    var data = $('#sales-rep-new-subscription').serialize () + "&action=subscribe&currentIndex=4";
                    $.ajax({
                        type: "POST",
                        url: eot.ajax_url,
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            eot_status = response.status;
                            my_response = response;
                            $('#sales-rep-new-subscription #loading').hide();
                            $('.processing_payment').slideUp();
                            if (!eot_status) {
                                $(button_ref).removeAttr('disabled');
                                show_error (my_response.message);
                            }
                            else
                            {
                                // show completed message
                                show_error ("SUCCESS: created subscription!");
                                window.location.replace("?part=admin_view_subscriptions&library_id=" + response.library_id + "&status=upgradeSubscription");
                            }

                            return eot_status;
                        }
                    });
                });
                
                $('input[name=ugroup]').val($("#ugroup_id option:selected").text());
                
                $('#ugroup_id').change(function() {
                    $('input[name=ugroup]').val($("#ugroup_id option:selected").text());
                });
            
                $('#expmonth').focus(function() {
                    if ($(this).val() == 'mm') {
                        $(this).val('');
                    }
                });
                
                $('#expyear').focus(function() {
                    if ($(this).val() == 'yy') {
                        $(this).val('');
                    }
                });
                
                $('#expmonth').blur(function() {
                    if ($(this).val() == '') {
                        $(this).val('mm');
                    }
                });
                
                $('#expyear').blur(function() {
                    if ($(this).val() == '') {
                        $(this).val('yy');
                    }
                });
                
                $("#method").change(function() {
                    if($("#method :selected").val() != "Stripe") {
                        $("#creditcard_opts").fadeOut(500);
                    } else {
                        $("#creditcard_opts").fadeIn(500);
                    }
                });
                
                $("#freferred_by").change(function() {
                    if($("#freferred_by :selected").val() == "other") {
                        $("#referred_other").css("display","inline").fadeIn(500);
                    } else {
                        $("#referred_other").fadeOut(500);
                    }
                });
                
                // A topic is either added or removed
                $('.library img').click(function() {
                    var library = $(this).parent();
                    var amount = $(this).parent().siblings('.amount');
                    var discount = $(this).parent().siblings('.discount');
                    var subtotal = $(this).parent().siblings('.subtotal');
                    
                    var topic = check_topic(library.parent());
                    // If removed, we change the JSON to reflect it and do some style changes
                    if ($(this).attr('src') == '<?= get_template_directory_uri()?>/images/chk_on.png') {
                        $(this).attr('src', '<?= get_template_directory_uri()?>/images/checkbox.png');
                        amount.hide().css('border-left', '1px solid #FFF');
                        discount.hide().css('border-left', '1px solid #FFF');
                        subtotal.hide().css('border-left', '1px solid #FFF');
                        $('.calc_topics.' + topic).css('height','30px');
                        $('.calc_' + topic).css('height','20px');
                        
                        data.libraries[topic].status = false;
                        update_total();
                    } else {
                        // Do they already have a subscription?
                        if ($(this).attr('already-purchased')==='true' && !confirm('This manager already has an active subscription to this library, please confirm you want to continue')) return;
                        if ($(this).attr('has-fla')==='true') alert('The manager has an existing FLA account. Ask if the manager if they would like to upgrade their FLA in order to preserve their current staff groupings. If that is the case, continue with sale but alert the IT dept. to do the update. If they don\'t want to preserve their staff groups, delete their FLA after the sale.');
                        // We check if this is the first time the topic is added and if so, we populate the JSON object with default price values
                        data.libraries[topic].status = true;
                        if (data.libraries[topic].data.dashboard.subtotal == '') {
                            data.libraries[topic].data.dashboard.subtotal = subtotal.children().find('.dash_subtotal').attr('data-full_price');
                            subtotal.children().find('.dash_subtotal').val(data.libraries[topic].data.dashboard.subtotal);
                        }
                        update_total();
                        
                        $(this).attr('src', '<?= get_template_directory_uri()?>/images/chk_on.png');
                        amount.css('border-left', '1px solid #888').show();
                        discount.css('border-left', '1px solid #888').show();
                        subtotal.css('border-left', '1px solid #888').show();
                        $('.calc_topics.' + topic).css('height','60px');
                        $('.calc_' + topic).css('height','50px');
                    }
                    
                });
                
                // A datadisk is either added or removed
                $('.datadisk img').click(function() {
                    //var library = $(this).parent();
                    var subtotal = $(this).parent().siblings('.subtotal');
                    var datadisk_price = "<?= DATA_DRIVE ?>";

                    // If removed, we change the JSON to reflect it and do some style changes
                    if ($(this).attr('src') == '<?= get_template_directory_uri()?>/images/chk_on.png') {
                        $(this).attr('src', '<?= get_template_directory_uri()?>/images/checkbox.png');
                        subtotal.hide().css('border-left', '1px solid #FFF');
                        $('.calc_topics.dd').css('height','30px');
                        $('.calc_dd').css('height','20px');
                        
                        data.datadisk.status = false;
                        $('input[name=subtotal_dd]').attr('value', '0');
                        update_total();
                    } else {
                        // We check if this is the first time the topic is added and if so, we populate the JSON object with default price values
                        data.datadisk.status = true;
                        $('input[name=subtotal_dd]').attr('value',datadisk_price);
                        if (data.datadisk.subtotal == '') {
                            data.datadisk.subtotal = subtotal.children().find('.datadisk_subtotal').attr('data-full_price');
                            subtotal.children().find('.datadisk_subtotal').val(data.datadisk.subtotal);
                        }
                        update_total();
                        
                        $(this).attr('src', '<?= get_template_directory_uri()?>/images/chk_on.png');
                        subtotal.css('border-left', '1px solid #888').show();
                        $('.calc_topics.dd').css('height','60px');
                        $('.calc_dd').css('height','50px');
                    }
                    
                });
                
                // The discount amount changed for dashboard
                $('.dash_discount').change(function() {
                    var topic = check_topic($(this).parents('.calc_topics'));
                    calc_dash_subtotal(topic);
                    $('input[name=subtotal_dash_num_' + topic + ']').val(data.libraries[topic].data.dashboard.subtotal);
                    update_total();
                });
                
                // The discount radio buttons changed for dashboard
                $('.dash_disc').click(function() {
                    var topic = check_topic($(this).parents('.calc_topics'));
                    calc_dash_subtotal(topic);
                    $('input[name=subtotal_dash_num_' + topic + ']').val(data.libraries[topic].data.dashboard.subtotal);
                    update_total();
                });
                
                // Either number of staff changed or amount of discount changed
                $('.num_staff,.staff_discount').change(function() {
                    update_staff_subtotal($(this));
                    update_total();
                });
                
                // Staff discount radio buttons clicked
                $('.staff_disc').click(function() {
                    update_staff_subtotal($(this));
                    update_total();
                });
                
                // Dashboard subtotal value changed manually
                $('.dash_subtotal').change(function() {
                    var topic = check_topic($(this).parents('.calc_topics'));
                    update_dash_disc($(this).val(), topic);
                    $('input[name=disc_dash_num_' + topic + ']').val(data.libraries[topic].data.dashboard.disc_amount);
                    update_total();
                });
                
                // Staff subtotal value changed manually
                $('.staff_subtotal').change(function() {
                    var topic = check_topic($(this).parents('.calc_topics'));
                    update_staff_disc($(this).val(), topic);
                    $('input[name=disc_staff_num_' + topic + ']').val(data.libraries[topic].data.staff.disc_amount);
                    update_total();
                });

                // datadisk subtotal value changed manually
                $('.datadisk_subtotal').change(function() {
                    data.datadisk.subtotal = parseFloat($(this).val()).toFixed(2);
                    $('input[name=subtotal_dd]').attr('value',data.datadisk.subtotal);
                    $('input[name=subtotal_dd]').val(data.datadisk.subtotal);
                    update_total();
                });
                
                // If dashboard subtotal is changed, the discount is recalculated
                function update_dash_disc(subtotal, topic) {
                        var full_dash_price = $('input[name=subtotal_dash_num_' + topic + ']').attr('data-full_price');
                        var disc_type = $('input[name=disc_dash_radio_' + topic + ']:checked').val();
                        var subtotal_dash = $('input[name=subtotal_dash_num_' + topic + ']').val();
                        var disc_amount;
                        if (disc_type == 1) {
                            disc_amount = full_dash_price - subtotal_dash;
                        } else {
                            disc_amount = 100 * (full_dash_price - subtotal_dash)/(full_dash_price);
                        }
                        
                        data.libraries[topic].data.dashboard.disc_type = disc_type;
                        data.libraries[topic].data.dashboard.disc_amount = parseFloat(disc_amount).toFixed(2);
                        data.libraries[topic].data.dashboard.subtotal = parseFloat(subtotal_dash).toFixed(2);
                        data.libraries[topic].data.dashboard.total_disc_value = full_dash_price - data.libraries[topic].data.dashboard.subtotal;
                }
                
                // If staff subtotal is changed, the discount is recalculated
                function update_staff_disc(subtotal, topic) {
                        var num_staff = $('input[name=num_staff_' + topic + ']').val();
                        var full_staff_price;
                        if (topic == "le") {
                            full_staff_price = price_structure_1(num_staff, topic);
                        } else if (topic == "le_sp_dc" || topic == "le_sp_oc" || topic == "le_sp_prp") {
                            full_staff_price = price_structure_2(num_staff, topic);
                        } else {
                            full_staff_price = price_structure_2(num_staff, topic);
                        }
                        var disc_type = $('input[name=disc_staff_radio_' + topic + ']:checked').val();
                        var subtotal_staff = $('input[name=subtotal_staff_num_' + topic + ']').val();
                        var disc_amount;
                        if (disc_type == 1)
                        {
                            if (topic == "se")
                            {
                                if (num_staff < 20)
                                {
                                    disc_amount = 0;
                                } 
                                else
                                {
                                    disc_amount = (((num_staff - 20) * 10) - subtotal_staff)/(num_staff - 20);
                                }

                            } else if (topic == "ce") {
                                if (num_staff < 12)
                                    disc_amount = 0;
                                else
                                    disc_amount = (((num_staff - 12) * 10) - subtotal_staff) / (num_staff - 12);
                            } else {

                                disc_amount = ((num_staff * 12) - subtotal_staff)/num_staff;
                            }
                        } else {
                            disc_amount = 100 * (full_staff_price - subtotal_staff)/(full_staff_price);
                        }
                        
                        data.libraries[topic].data.staff.disc_type = disc_type;
                        data.libraries[topic].data.staff.disc_amount = parseFloat(disc_amount).toFixed(2);
                        data.libraries[topic].data.staff.subtotal = parseFloat(subtotal_staff).toFixed(2);
                        data.libraries[topic].data.staff.total_disc_value = full_staff_price - data.libraries[topic].data.staff.subtotal;
                }//end update staff disc function
                
                // Updates the staff subtotal whenever there is a change in number of staff or discount
                function update_staff_subtotal(element) {
                    var parent_element = element.parents('.calc_topics');
                    var topic = check_topic(parent_element);
                    var num_staff = $('input[name=num_staff_' + topic + ']').val();
                    if (topic == "le") {
                        calc_staff_subtotal_1(num_staff, topic);
                    } else if (topic == "le_sp_dc" || topic == "le_sp_oc" || topic == "le_sp_prp") {
                        calc_staff_subtotal_2(num_staff, topic);
                    } else if (topic == "ss") {
                        calc_staff_subtotal_5(num_staff, topic);
                    } else if (topic == "ce") {
                        calc_staff_subtotal_4(num_staff, topic);    
                    } else if (topic == "se") {
                        calc_staff_subtotal_3(num_staff, topic);    
                    } else {
                        calc_staff_subtotal_3(num_staff, topic);
                    }
                    $('input[name=subtotal_staff_num_' + topic + ']').val(data.libraries[topic].data.staff.subtotal);
                }
                
                // Checks which topic it is
                function check_topic(element) {
                    if (element.hasClass('le')) {
                        return 'le';
                    } else if (element.hasClass('le_sp_dc')) {
                        return 'le_sp_dc';
                    } else if (element.hasClass('le_sp_oc')) {
                        return 'le_sp_oc';
                    } else if (element.hasClass('le_sp_prp')) {
                        return 'le_sp_prp';
                    } else if (element.hasClass('ss')) {
                        return 'ss';
                    } else if (element.hasClass('se')) {
                        return 'se';
                    } else if (element.hasClass('ce')) {
                        return 'ce';
                    } else {
                        return 'le';
                    }
                }
                
                // Calculate dashboard subtotal for Leadership Essentials
                function calc_dash_subtotal(topic) {
                    var subtotal_dash;
                    var full_dash_price = $('input[name=subtotal_dash_num_' + topic + ']').attr('data-full_price');
                    var disc_type = $('input[name=disc_dash_radio_' + topic + ']:checked').val();
                    var disc_amount = $('input[name=disc_dash_num_' + topic + ']').val();
                    if (disc_type == 1 && disc_amount > 0) {
                        subtotal_dash = full_dash_price - disc_amount;
                    } else if (disc_type == 0 && disc_amount > 0) {
                        subtotal_dash = full_dash_price - (full_dash_price * (disc_amount/100));
                    } else {
                        subtotal_dash = full_dash_price;
                    }
                    
                    data.libraries[topic].data.dashboard.disc_type = disc_type;
                    data.libraries[topic].data.dashboard.disc_amount = disc_amount;
                    data.libraries[topic].data.dashboard.subtotal = parseFloat(subtotal_dash).toFixed(2);
                    data.libraries[topic].data.dashboard.total_disc_value = full_dash_price - data.libraries[topic].data.dashboard.subtotal;
                }
                
                // Calculate staff subtotal for Leadership Essentials
                function calc_staff_subtotal_1(num_staff, topic) {
                    var subtotal_staff;
                    var disc_type = $('input[name=disc_staff_radio_' + topic + ']:checked').val();
                    var disc_amount = $('input[name=disc_staff_num_' + topic + ']').val();
                    if (disc_type == 1 && disc_amount > 0) {
                        subtotal_staff = num_staff * (price_structure_1(num_staff)/num_staff - disc_amount);
                    } else if (disc_type == 0 && disc_amount > 0) {
                        subtotal_staff = price_structure_1(num_staff) - (price_structure_1(num_staff) * (disc_amount/100));
                    } else {
                        subtotal_staff = price_structure_1(num_staff);
                    }
                    
                    data.libraries[topic].data.staff.number = num_staff;
                    data.libraries[topic].data.staff.disc_type = disc_type;
                    data.libraries[topic].data.staff.disc_amount = disc_amount;
                    data.libraries[topic].data.staff.subtotal = subtotal_staff.toFixed(2);
                    data.libraries[topic].data.staff.total_disc_value = price_structure_1(num_staff) - data.libraries[topic].data.staff.subtotal;
                }
                
                // Calculate staff subtotal for Leadership Essentials - Limited
                function calc_staff_subtotal_2(num_staff, topic) {
                    var subtotal_staff;
                    var disc_type = $('input[name=disc_staff_radio_' + topic + ']:checked').val();
                    var disc_amount = $('input[name=disc_staff_num_' + topic + ']').val();
                    if (disc_type == 1 && disc_amount > 0) {
                        subtotal_staff = num_staff * (14 - disc_amount);
                    } else if (disc_type == 0 && disc_amount > 0) {
                        subtotal_staff = price_structure_2(num_staff) - (price_structure_2(num_staff) * (disc_amount/100));
                    } else {
                        subtotal_staff = price_structure_2(num_staff);
                    }
                    
                    data.libraries[topic].data.staff.number = num_staff;
                    data.libraries[topic].data.staff.disc_type = disc_type;
                    data.libraries[topic].data.staff.disc_amount = disc_amount;
                    data.libraries[topic].data.staff.subtotal = subtotal_staff.toFixed(2);
                    data.libraries[topic].data.staff.total_disc_value = price_structure_2(num_staff) - data.libraries[topic].data.staff.subtotal;
                }
                
                // Calculate staff subtotal for Safety Essentials - Child welfare and protection
                function calc_staff_subtotal_3(num_staff, topic) {
                    var subtotal_staff;
                    var disc_type = $('input[name=disc_staff_radio_' + topic + ']:checked').val();
                    var disc_amount = $('input[name=disc_staff_num_' + topic + ']').val();
                    if (disc_type == 1 && disc_amount > 0) {
                        if (num_staff <= 20) {
                            subtotal_staff = 0;
                        } else {
                            subtotal_staff = (num_staff - 20) * (10 - disc_amount);
                        }
                    } else if (disc_type == 0 && disc_amount > 0) {
                        subtotal_staff = price_structure_3(num_staff) - (price_structure_3(num_staff) * (disc_amount/100));
                    } else {
                        subtotal_staff = price_structure_3(num_staff);
                    }
                    
                    data.libraries[topic].data.staff.number = num_staff;
                    data.libraries[topic].data.staff.disc_type = disc_type;
                    data.libraries[topic].data.staff.disc_amount = disc_amount;
                    data.libraries[topic].data.staff.subtotal = subtotal_staff.toFixed(2);
                    data.libraries[topic].data.staff.total_disc_value = price_structure_3(num_staff) - data.libraries[topic].data.staff.subtotal;
                }
                
                // Calculate staff subtotal for Clinical Essentials
                function calc_staff_subtotal_4(num_staff, topic)
                {
                    var subtotal_staff;
                    var disc_type = $('input[name=disc_staff_radio_' + topic + ']:checked').val();
                    var disc_amount = $('input[disc_staff_num_' + topic + ']').val();
                    if (disc_type == 1 && disc_amount > 0)
                    {
                        if (num_staff <= 12)
                            subtotal_staff = 0;
                        else
                            subtotal_staff = (num_staff - 12) * (10 - disc_amount); 
                    }
                    else if (disc_type == 0 && disc_amount > 0)
                    {
                        subtotal_staff = price_structure_4(num_staff) - (price_structure_4(num_staff) * (disc_amount / 100));   
                    }
                    else
                    {
                        subtotal_staff = price_structure_4(num_staff);  
                    }
                    
                    data.libraries[topic].data.staff.number = num_staff;
                    data.libraries[topic].data.staff.disc_type = disc_type;
                    data.libraries[topic].data.staff.disc_amount = disc_amount;
                    data.libraries[topic].data.staff.subtotal = subtotal_staff.toFixed(2);
                    data.libraries[topic].data.staff.total_disc_value = price_structure_4(num_staff) - data.libraries[topic].data.staff.subtotal;
                }
                
                // Calculate staff subtotal for Safe Summer
                function calc_staff_subtotal_5(num_staff, topic)
                {
                    var subtotal_staff;
                    var disc_type = $('input[name=disc_staff_radio_' + topic + ']:checked').val();
                    var disc_amount = $('input[disc_staff_num_' + topic + ']').val();
                    if (disc_type == 1 && disc_amount > 0)
                    {
                            subtotal_staff = num_staff * (7 - disc_amount); 
                    }
                    else if (disc_type == 0 && disc_amount > 0)
                    {
                        subtotal_staff = price_structure_5(num_staff) - (price_structure_5(num_staff) * (disc_amount / 100));   
                    }
                    else
                    {
                        subtotal_staff = price_structure_5(num_staff);  
                    }
                    
                    data.libraries[topic].data.staff.number = num_staff;
                    data.libraries[topic].data.staff.disc_type = disc_type;
                    data.libraries[topic].data.staff.disc_amount = disc_amount;
                    data.libraries[topic].data.staff.subtotal = subtotal_staff.toFixed(2);
                    data.libraries[topic].data.staff.total_disc_value = price_structure_5(num_staff) - data.libraries[topic].data.staff.subtotal;
                }
                
                // Leadership Essentials price structure
                function price_structure_1(num_staff) {
                    if (num_staff <= 99) {
                        return num_staff * 14;
                    } else if (num_staff > 99 && num_staff <= 249) {
                        return num_staff * 13;
                    } else {
                        return num_staff * 12;
                    }
                }
                
                // Medical Essentials price structure
                function price_structure_2(num_staff) {
                    if (num_staff <= 99) {
                        return num_staff * 14;
                    } else if (num_staff > 99 && num_staff <= 249) {
                        return num_staff * 13;
                    } else {
                        return num_staff * 12;
                    }
                }
                
                // Safety Essentials price structure
                function price_structure_3(num_staff) {
                    if (num_staff <= 20) {
                        return 0;
                    } else {
                        return (num_staff - 20) * 10;
                    }
                }
                
                // Clinical Essentials price structure
                function price_structure_4(num_staff)
                {
                    if (num_staff <= 12)
                        return 0;
                        
                    return (num_staff - 12) * 10;   
                }
                
                // Safe Summer price structure
                function price_structure_5(num_staff)
                {
                    return num_staff * 7;   
                }
                
                // Updates the total in JSON object and displays it in the total textbox
                function update_total() {
                    data.total = 0;
                    if (data.libraries.le.status) {
                        data.total = (parseFloat(data.total) + parseFloat(data.libraries.le.data.dashboard.subtotal) + parseFloat(data.libraries.le.data.staff.subtotal)).toFixed(2);
                    }
                    if (data.libraries.se.status) {
                        data.total = (parseFloat(data.total) + parseFloat(data.libraries.se.data.dashboard.subtotal) + parseFloat(data.libraries.se.data.staff.subtotal)).toFixed(2);
                    }
                    if (data.libraries.le_sp_dc.status) {
                        data.total = (parseFloat(data.total) + parseFloat(data.libraries.le_sp_dc.data.dashboard.subtotal) + parseFloat(data.libraries.le_sp_dc.data.staff.subtotal)).toFixed(2);
                    }
                    
                    if (data.libraries.le_sp_oc.status) {
                        data.total = (parseFloat(data.total) + parseFloat(data.libraries.le_sp_oc.data.dashboard.subtotal) + parseFloat(data.libraries.le_sp_oc.data.staff.subtotal)).toFixed(2);
                    }
                    
                    if (data.libraries.le_sp_prp.status) {
                        data.total = (parseFloat(data.total) + parseFloat(data.libraries.le_sp_prp.data.dashboard.subtotal) + parseFloat(data.libraries.le_sp_prp.data.staff.subtotal)).toFixed(2);
                    }
                    
                    if (data.datadisk.status) {
                        data.total = (parseFloat(data.total) + parseFloat(data.datadisk.subtotal)).toFixed(2);
                    }

                    $('.total_num').val(data.total);
                }
                
                
                // Long tedious function so putting it at the end
                $('#submit_calc').click(function() {
                    if (data.total >= 0) {
                        var count = 0;
                        if (data.libraries.le.status) {
                            var le_disc_dash, le_disc_staff;
                            count++;
                            
                            if (data.libraries.le.data.dashboard.disc_type == 1 && data.libraries.le.data.dashboard.disc_amount > 0) {
                                le_disc_dash = "$" + data.libraries.le.data.dashboard.disc_amount;
                            } else if (data.libraries.le.data.dashboard.disc_type == 0  && data.libraries.le.data.dashboard.disc_amount > 0) {
                                le_disc_dash = data.libraries.le.data.dashboard.disc_amount + "%";
                            } else {
                                le_disc_dash = '';
                            }
                            
                            if (data.libraries.le.data.staff.disc_type == 1 && data.libraries.le.data.staff.disc_amount > 0) {
                                le_disc_staff = "$" + data.libraries.le.data.staff.disc_amount + " per account";
                            } else if (data.libraries.le.data.staff.disc_type == 0  && data.libraries.le.data.staff.disc_amount > 0) {
                                le_disc_staff = data.libraries.le.data.staff.disc_amount + "% of total price";
                            } else {
                                le_disc_staff = '';
                            }
                            
                            $('.billing_items').append(
                            '<div class="billing_item">' + 
                                '<li class="bill_item library"><strong>Leadership Essentials</strong></li>' + 
                                '<li class="bill_item amount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        '<span class="amount_left">Director Dashboard</span>' + 
                                        '<span class="amount_x">X</span>' + 
                                        '<span class="amount_right">1</span>' + 
                                    '</div>' + 
                                        '<div class="expanded_bot">' + 
                                            '<span class="amount_left">Number of Staff</span>' + 
                                            '<span class="amount_x">X</span>' + 
                                            '<span class="amount_right">' + data.libraries.le.data.staff.number + '</span>' + 
                                        '</div>' + 
                                '</li>' + 
                                '<li class="bill_item discount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        le_disc_dash + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        le_disc_staff + 
                                    '</div>' + 
                                '</li>' + 
                                '<li class="bill_item subtotal border_left">' +
                                    '<div class="expanded_top">' + 
                                        '$' + data.libraries.le.data.dashboard.subtotal + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        '$' + data.libraries.le.data.staff.subtotal + 
                                    '</div>' +
                                '</li>' + 
                            '</div>'+
                            '<input type="hidden" name="le" value="<?= LE_ID ?>">'+
                            '<input type="hidden" name="le_staff" value="' + data.libraries.le.data.staff.number + '">'+
                            '<input type="hidden" name="le_dash_price" value="' + data.libraries.le.data.dashboard.subtotal + '">'+
                            '<input type="hidden" name="le_staff_price" value="' + data.libraries.le.data.staff.subtotal + '">'+
                            '<input type="hidden" name="le_dash_disc" value="' + data.libraries.le.data.dashboard.total_disc_value.toFixed(2) + '">'+
                            '<input type="hidden" name="le_staff_disc" value="' + data.libraries.le.data.staff.total_disc_value.toFixed(2) + '">'
                            );
                        }
                        if (data.libraries.se.status) {
                            var se_disc_dash, se_disc_staff;
                            count++;
                            
                            if (data.libraries.se.data.dashboard.disc_type == 1 && data.libraries.se.data.dashboard.disc_amount > 0) {
                                se_disc_dash = "$" + data.libraries.se.data.dashboard.disc_amount;
                            } else if (data.libraries.se.data.dashboard.disc_type == 0  && data.libraries.se.data.dashboard.disc_amount > 0) {
                                se_disc_dash = data.libraries.se.data.dashboard.disc_amount + "%";
                            } else {
                                se_disc_dash = '';
                            }
                            
                            if (data.libraries.se.data.staff.disc_type == 1 && data.libraries.se.data.staff.disc_amount > 0) {
                                se_disc_staff = "$" + data.libraries.se.data.staff.disc_amount + " per account";
                            } else if (data.libraries.se.data.staff.disc_type == 0  && data.libraries.se.data.staff.disc_amount > 0) {
                                se_disc_staff = data.libraries.se.data.staff.disc_amount + "% of total price";
                            } else {
                                se_disc_staff = '';
                            }
                            
                            $('.billing_items').append(
                            '<div class="billing_item">' + 
                                '<li class="bill_item library"><strong>Child Welfare & Protection</strong></li>' + 
                                '<li class="bill_item amount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        '<span class="amount_left">Director Dashboard</span>' + 
                                        '<span class="amount_x">X</span>' + 
                                        '<span class="amount_right">1</span>' + 
                                    '</div>' + 
                                        '<div class="expanded_bot">' + 
                                            '<span class="amount_left">Number of Staff</span>' + 
                                            '<span class="amount_x">X</span>' + 
                                            '<span class="amount_right">' + data.libraries.se.data.staff.number + '</span>' + 
                                        '</div>' + 
                                '</li>' + 
                                '<li class="bill_item discount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        se_disc_dash + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        se_disc_staff + 
                                    '</div>' + 
                                '</li>' + 
                                '<li class="bill_item subtotal border_left">' +
                                    '<div class="expanded_top">' + 
                                        '$' + data.libraries.se.data.dashboard.subtotal + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        '$' + data.libraries.se.data.staff.subtotal + 
                                    '</div>' +
                                '</li>' + 
                            '</div>'+
                            '<input type="hidden" name="se" value="<?= SE_ID ?>">'+
                            '<input type="hidden" name="se_staff" value="' + data.libraries.se.data.staff.number + '">'+
                            '<input type="hidden" name="se_dash_price" value="' + data.libraries.se.data.dashboard.subtotal + '">'+
                            '<input type="hidden" name="se_staff_price" value="' + data.libraries.se.data.staff.subtotal + '">'+
                            '<input type="hidden" name="se_dash_disc" value="' + data.libraries.se.data.dashboard.total_disc_value.toFixed(2) + '">'+
                            '<input type="hidden" name="se_staff_disc" value="' + data.libraries.se.data.staff.total_disc_value.toFixed(2) + '">'
                            );
                        }                        
                        if (data.libraries.le_sp_dc.status) {
                            var le_sp_dc_disc_dash, le_sp_dc_disc_staff;
                            count++;
                            
                            if (data.libraries.le_sp_dc.data.dashboard.disc_type == 1 && data.libraries.le_sp_dc.data.dashboard.disc_amount > 0) {
                                le_sp_dc_disc_dash = "$" + data.libraries.le_sp_dc.data.dashboard.disc_amount;
                            } else if (data.libraries.le_sp_dc.data.dashboard.disc_type == 0  && data.libraries.le_sp_dc.data.dashboard.disc_amount > 0) {
                                le_sp_dc_disc_dash = data.libraries.le_sp_dc.data.dashboard.disc_amount + "%";
                            } else {
                                le_sp_dc_disc_dash = '';
                            }
                            
                            if (data.libraries.le_sp_dc.data.staff.disc_type == 1 && data.libraries.le_sp_dc.data.staff.disc_amount > 0) {
                                le_sp_dc_disc_staff = "$" + data.libraries.le_sp_dc.data.staff.disc_amount + " per account";
                            } else if (data.libraries.le_sp_dc.data.staff.disc_type == 0  && data.libraries.le_sp_dc.data.staff.disc_amount > 0) {
                                le_sp_dc_disc_staff = data.libraries.le_sp_dc.data.staff.disc_amount + "% of total price";
                            } else {
                                le_sp_dc_disc_staff = '';
                            }
                            
                            $('.billing_items').append(
                            '<div class="billing_item">' + 
                                '<li class="bill_item library"><strong>Leadership Essentials - Starter Pack - Day Camps</strong></li>' + 
                                '<li class="bill_item amount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        '<span class="amount_left">Director Dashboard</span>' + 
                                        '<span class="amount_x">X</span>' + 
                                        '<span class="amount_right">1</span>' + 
                                    '</div>' + 
                                        '<div class="expanded_bot">' + 
                                            '<span class="amount_left">Number of Staff</span>' + 
                                            '<span class="amount_x">X</span>' + 
                                            '<span class="amount_right">' + data.libraries.le_sp_dc.data.staff.number + '</span>' + 
                                        '</div>' + 
                                '</li>' + 
                                '<li class="bill_item discount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        le_sp_dc_disc_dash + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        le_sp_dc_disc_staff + 
                                    '</div>' + 
                                '</li>' + 
                                '<li class="bill_item subtotal border_left">' +
                                    '<div class="expanded_top">' + 
                                        '$' + data.libraries.le_sp_dc.data.dashboard.subtotal + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        '$' + data.libraries.le_sp_dc.data.staff.subtotal + 
                                    '</div>' +
                                '</li>' + 
                            '</div>'+
                            '<input type="hidden" name="le_sp_dc" value="<?= LE_SP_DC_ID ?>">'+
                            '<input type="hidden" name="le_sp_dc_staff" value="' + data.libraries.le_sp_dc.data.staff.number + '">'+
                            '<input type="hidden" name="le_sp_dc_dash_price" value="' + data.libraries.le_sp_dc.data.dashboard.subtotal + '">'+
                            '<input type="hidden" name="le_sp_dc_staff_price" value="' + data.libraries.le_sp_dc.data.staff.subtotal + '">'+
                            '<input type="hidden" name="le_sp_dc_dash_disc" value="' + data.libraries.le_sp_dc.data.dashboard.total_disc_value.toFixed(2) + '">'+
                            '<input type="hidden" name="le_sp_dc_staff_disc" value="' + data.libraries.le_sp_dc.data.staff.total_disc_value.toFixed(2) + '">'
                            );
                        }
                        if (data.libraries.le_sp_oc.status) {
                            var le_sp_oc_disc_dash, le_sp_oc_disc_staff;
                            count++;
                            
                            if (data.libraries.le_sp_oc.data.dashboard.disc_type == 1 && data.libraries.le_sp_oc.data.dashboard.disc_amount > 0) {
                                le_sp_oc_disc_dash = "$" + data.libraries.le_sp_oc.data.dashboard.disc_amount;
                            } else if (data.libraries.le_sp_oc.data.dashboard.disc_type == 0  && data.libraries.le_sp_oc.data.dashboard.disc_amount > 0) {
                                le_sp_oc_disc_dash = data.libraries.le_sp_oc.data.dashboard.disc_amount + "%";
                            } else {
                                le_sp_oc_disc_dash = '';
                            }
                            
                            if (data.libraries.le_sp_oc.data.staff.disc_type == 1 && data.libraries.le_sp_oc.data.staff.disc_amount > 0) {
                                le_sp_oc_disc_staff = "$" + data.libraries.le_sp_oc.data.staff.disc_amount + " per account";
                            } else if (data.libraries.le_sp_oc.data.staff.disc_type == 0  && data.libraries.le_sp_oc.data.staff.disc_amount > 0) {
                                le_sp_oc_disc_staff = data.libraries.le_sp_oc.data.staff.disc_amount + "% of total price";
                            } else {
                                le_sp_oc_disc_staff = '';
                            }
                            
                            $('.billing_items').append(
                            '<div class="billing_item">' + 
                                '<li class="bill_item library"><strong>Leadership Essentials - Starter Pack - Day Camps</strong></li>' + 
                                '<li class="bill_item amount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        '<span class="amount_left">Director Dashboard</span>' + 
                                        '<span class="amount_x">X</span>' + 
                                        '<span class="amount_right">1</span>' + 
                                    '</div>' + 
                                        '<div class="expanded_bot">' + 
                                            '<span class="amount_left">Number of Staff</span>' + 
                                            '<span class="amount_x">X</span>' + 
                                            '<span class="amount_right">' + data.libraries.le_sp_oc.data.staff.number + '</span>' + 
                                        '</div>' + 
                                '</li>' + 
                                '<li class="bill_item discount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        le_sp_oc_disc_dash + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        le_sp_oc_disc_staff + 
                                    '</div>' + 
                                '</li>' + 
                                '<li class="bill_item subtotal border_left">' +
                                    '<div class="expanded_top">' + 
                                        '$' + data.libraries.le_sp_oc.data.dashboard.subtotal + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        '$' + data.libraries.le_sp_oc.data.staff.subtotal + 
                                    '</div>' +
                                '</li>' + 
                            '</div>'+
                            '<input type="hidden" name="le_sp_oc" value="<?= LE_SP_OC_ID ?>">'+
                            '<input type="hidden" name="le_sp_oc_staff" value="' + data.libraries.le_sp_oc.data.staff.number + '">'+
                            '<input type="hidden" name="le_sp_oc_dash_price" value="' + data.libraries.le_sp_oc.data.dashboard.subtotal + '">'+
                            '<input type="hidden" name="le_sp_oc_staff_price" value="' + data.libraries.le_sp_oc.data.staff.subtotal + '">'+
                            '<input type="hidden" name="le_sp_oc_dash_disc" value="' + data.libraries.le_sp_oc.data.dashboard.total_disc_value.toFixed(2) + '">'+
                            '<input type="hidden" name="le_sp_oc_staff_disc" value="' + data.libraries.le_sp_oc.data.staff.total_disc_value.toFixed(2) + '">'
                            );
                        }
                        if (data.libraries.le_sp_prp.status) {
                            var le_sp_prp_disc_dash, le_sp_prp_disc_staff;
                            count++;
                            
                            if (data.libraries.le_sp_prp.data.dashboard.disc_type == 1 && data.libraries.le_sp_prp.data.dashboard.disc_amount > 0) {
                                le_sp_prp_disc_dash = "$" + data.libraries.le_sp_prp.data.dashboard.disc_amount;
                            } else if (data.libraries.le_sp_prp.data.dashboard.disc_type == 0  && data.libraries.le_sp_prp.data.dashboard.disc_amount > 0) {
                                le_sp_prp_disc_dash = data.libraries.le_sp_prp.data.dashboard.disc_amount + "%";
                            } else {
                                le_sp_prp_disc_dash = '';
                            }
                            
                            if (data.libraries.le_sp_prp.data.staff.disc_type == 1 && data.libraries.le_sp_prp.data.staff.disc_amount > 0) {
                                le_sp_prp_disc_staff = "$" + data.libraries.le_sp_prp.data.staff.disc_amount + " per account";
                            } else if (data.libraries.le_sp_prp.data.staff.disc_type == 0  && data.libraries.le_sp_prp.data.staff.disc_amount > 0) {
                                le_sp_prp_disc_staff = data.libraries.le_sp_prp.data.staff.disc_amount + "% of total price";
                            } else {
                                le_sp_prp_disc_staff = '';
                            }
                            
                            $('.billing_items').append(
                            '<div class="billing_item">' + 
                                '<li class="bill_item library"><strong>Leadership Essentials - Starter Pack - Day Camps</strong></li>' + 
                                '<li class="bill_item amount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        '<span class="amount_left">Director Dashboard</span>' + 
                                        '<span class="amount_x">X</span>' + 
                                        '<span class="amount_right">1</span>' + 
                                    '</div>' + 
                                        '<div class="expanded_bot">' + 
                                            '<span class="amount_left">Number of Staff</span>' + 
                                            '<span class="amount_x">X</span>' + 
                                            '<span class="amount_right">' + data.libraries.le_sp_prp.data.staff.number + '</span>' + 
                                        '</div>' + 
                                '</li>' + 
                                '<li class="bill_item discount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        le_sp_prp_disc_dash + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        le_sp_prp_disc_staff + 
                                    '</div>' + 
                                '</li>' + 
                                '<li class="bill_item subtotal border_left">' +
                                    '<div class="expanded_top">' + 
                                        '$' + data.libraries.le_sp_prp.data.dashboard.subtotal + 
                                    '</div>' + 
                                    '<div class="expanded_bot">' + 
                                        '$' + data.libraries.le_sp_prp.data.staff.subtotal + 
                                    '</div>' +
                                '</li>' + 
                            '</div>'+
                            '<input type="hidden" name="le_sp_prp" value="<?= LE_SP_PRP_ID ?>">'+
                            '<input type="hidden" name="le_sp_prp_staff" value="' + data.libraries.le_sp_prp.data.staff.number + '">'+
                            '<input type="hidden" name="le_sp_prp_dash_price" value="' + data.libraries.le_sp_prp.data.dashboard.subtotal + '">'+
                            '<input type="hidden" name="le_sp_prp_staff_price" value="' + data.libraries.le_sp_prp.data.staff.subtotal + '">'+
                            '<input type="hidden" name="le_sp_prp_dash_disc" value="' + data.libraries.le_sp_prp.data.dashboard.total_disc_value.toFixed(2) + '">'+
                            '<input type="hidden" name="le_sp_prp_staff_disc" value="' + data.libraries.le_sp_prp.data.staff.total_disc_value.toFixed(2) + '">'
                            );
                        }

                        if (data.datadisk.status) {
                            count++;
                            
                            $('.billing_items').append(
                            '<div class="billing_item">' + 
                                '<li class="bill_item library"><strong>Data Disk</strong></li>' + 
                                '<li class="bill_item amount border_left">' + 
                                    '<div class="expanded_top">' + 
                                        '<span class="amount_left"></span>' + 
                                        '<span class="amount_x"></span>' + 
                                        '<span class="amount_right"></span>' + 
                                    '</div>' + 
                                '</li>' + 
                                '<li class="bill_item discount border_left">' + 
                                    '<div class="expanded_top"></div>' + 
                                '</li>' + 
                                '<li class="bill_item subtotal border_left">' +
                                    '<div class="expanded_top">' + 
                                        '$' + data.datadisk.subtotal + 
                                    '</div>' + 
                                '</li>' + 
                            '</div>');
                        }
                        
                        if (count == 0) {
                            alert('You must select at least one library to subscribe to.');
                        } else {
                            $('#bill_sum_total').append('<strong>$' + data.total + '</strong>');
                            $('.expanded_top').css('height','22px');
                            $('#calc').fadeOut('slow', function() {
                                $('#billing').fadeIn('slow');
                            });
                            $('#total').val (data.total);
                        }
                    } else {
                        alert('Negative total. Please fix your discounts.');
                    }
                });
    
            });
        });

        </script>
    



<form id="sales-rep-new-subscription" name="sales-rep-new-subscription" action="#" data-user_id="" method="post">

        <div id="calc">
            <h3>Subscription</h3>
            <div class="table_header">
                <li class="table_header_list library">Library</li>
                <li class="table_header_list amount">Amount</li>
                <li class="table_header_list discount">Discount</li>
                <li class="table_header_list subtotal">Subtotal</li>
            </div>
            <div id="calc_body">
                <div class="calc_topics le">
                    <li class="calc_le library"><img src="<?= get_template_directory_uri() . '/images/checkbox.png'?>" already-purchased="false" has-fla="false" />Leadership Essentials</li>
                    <li class="calc_le amount">
                        <div class="expanded_top">
                            <span class="amount_left">Director Dashboard</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right">1</span>
                        </div>
                        <div class="expanded_bot">
                            <span class="amount_left">Number of Staff</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right"><input type="text" name="num_staff_le" class="num_staff small_box" value="0" maxlength="4" /></span>
                        </div>
                    </li>
                    <li class="calc_le discount">
                        <div class="expanded_top">
                            <input type="text" name="disc_dash_num_le" class="dash_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_dash_radio_le" class="dash_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_dash_radio_le" class="dash_disc" />%
                        </div>
                        <div class="expanded_bot">
                            <input type="text" name="disc_staff_num_le" class="staff_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_staff_radio_le" class="staff_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_staff_radio_le" class="staff_disc" />%
                        </div>
                    </li>
                    <li class="calc_le subtotal">
                        <div class="expanded_top">
                            $ <input type="text" name="subtotal_dash_num_le" class="dash_subtotal large_box" value="399.00" data-full_price="399.00" />
                        </div>
                        <div class="expanded_bot">
                            $ <input type="text" name="subtotal_staff_num_le" class="staff_subtotal large_box" value="0.00" />
                        </div>
                    </li>
                </div>

                <div class="calc_topics le_sp_dc">
                    <li class="calc_le_sp_dc library"><img src="<?= get_template_directory_uri() . '/images/checkbox.png'?>" already-purchased="false" has-fla="false" />LE - SP - Day Camps</li>
                    <li class="calc_le_sp_dc amount">
                        <div class="expanded_top">
                            <span class="amount_left">Director Dashboard</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right">1</span>
                        </div>
                        <div class="expanded_bot">
                            <span class="amount_left">Number of Staff</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right"><input type="text" name="num_staff_le_sp_dc" class="num_staff small_box" value="0" maxlength="4" /></span>
                        </div>
                    </li>
                    <li class="calc_le_sp_dc discount">
                        <div class="expanded_top">
                            <input type="text" name="disc_dash_num_le_sp_dc" class="dash_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_dash_radio_le_sp_dc" class="dash_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_dash_radio_le_sp_dc" class="dash_disc" />%
                        </div>
                        <div class="expanded_bot">
                            <input type="text" name="disc_staff_num_le_sp_dc" class="staff_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_staff_radio_le_sp_dc" class="staff_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_staff_radio_le_sp_dc" class="staff_disc" />%
                        </div>
                    </li>
                    <li class="calc_le_sp_dc subtotal">
                        <div class="expanded_top">
                            $ <input type="text" name="subtotal_dash_num_le_sp_dc" class="dash_subtotal large_box" value="199.00" data-full_price="199.00" />
                        </div>
                        <div class="expanded_bot">
                            $ <input type="text" name="subtotal_staff_num_le_sp_dc" class="staff_subtotal large_box" value="0.00" />
                        </div>
                    </li>
                </div>

                <div class="calc_topics le_sp_oc">
                    <li class="calc_le_sp_oc library"><img src="<?= get_template_directory_uri() . '/images/checkbox.png'?>" already-purchased="false" has-fla="false" />LE -SP - Overnight Camps</li>
                    <li class="calc_le_sp_oc amount">
                        <div class="expanded_top">
                            <span class="amount_left">Director Dashboard</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right">1</span>
                        </div>
                        <div class="expanded_bot">
                            <span class="amount_left">Number of Staff</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right"><input type="text" name="num_staff_le_sp_oc" class="num_staff small_box" value="0" maxlength="4" /></span>
                        </div>
                    </li>
                    <li class="calc_le_sp_oc discount">
                        <div class="expanded_top">
                            <input type="text" name="disc_dash_num_le_sp_oc" class="dash_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_dash_radio_le_sp_oc" class="dash_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_dash_radio_le_sp_oc" class="dash_disc" />%
                        </div>
                        <div class="expanded_bot">
                            <input type="text" name="disc_staff_num_le_sp_oc" class="staff_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_staff_radio_le_sp_oc" class="staff_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_staff_radio_le_sp_oc" class="staff_disc" />%
                        </div>
                    </li>
                    <li class="calc_le_sp_oc subtotal">
                        <div class="expanded_top">
                            $ <input type="text" name="subtotal_dash_num_le_sp_oc" class="dash_subtotal large_box" value="199.00" data-full_price="199.00" />
                        </div>
                        <div class="expanded_bot">
                            $ <input type="text" name="subtotal_staff_num_le_sp_oc" class="staff_subtotal large_box" value="0.00" />
                        </div>
                    </li>
                </div>

                <div class="calc_topics le_sp_prp">
                    <li class="calc_le_sp_prp library"><img src="<?= get_template_directory_uri() . '/images/checkbox.png'?>" already-purchased="false" has-fla="false" />LE -SP - Parks & Rec Programs</li>
                    <li class="calc_le_sp_prp amount">
                        <div class="expanded_top">
                            <span class="amount_left">Director Dashboard</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right">1</span>
                        </div>
                        <div class="expanded_bot">
                            <span class="amount_left">Number of Staff</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right"><input type="text" name="num_staff_le_sp_prp" class="num_staff small_box" value="0" maxlength="4" /></span>
                        </div>
                    </li>
                    <li class="calc_le_sp_prp discount">
                        <div class="expanded_top">
                            <input type="text" name="disc_dash_num_le_sp_prp" class="dash_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_dash_radio_le_sp_prp" class="dash_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_dash_radio_le_sp_prp" class="dash_disc" />%
                        </div>
                        <div class="expanded_bot">
                            <input type="text" name="disc_staff_num_le_sp_prp" class="staff_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_staff_radio_le_sp_prp" class="staff_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_staff_radio_le_sp_prp" class="staff_disc" />%
                        </div>
                    </li>
                    <li class="calc_le_sp_prp subtotal">
                        <div class="expanded_top">
                            $ <input type="text" name="subtotal_dash_num_le_sp_prp" class="dash_subtotal large_box" value="199.00" data-full_price="199.00" />
                        </div>
                        <div class="expanded_bot">
                            $ <input type="text" name="subtotal_staff_num_le_sp_prp" class="staff_subtotal large_box" value="0.00" />
                        </div>
                    </li>
                </div>
                <div class="calc_topics se">
                    <li class="calc_se library"><img src="<?= get_template_directory_uri() . '/images/checkbox.png'?>" already-purchased="false" has-fla="false" />Child Welfare & Protection</li>
                    <li class="calc_se amount">
                        <div class="expanded_top">
                            <span class="amount_left">Director Dashboard</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right">1</span>
                        </div>
                        <div class="expanded_bot">
                            <span class="amount_left">Number of Staff</span>
                            <span class="amount_x">X</span>
                            <span class="amount_right"><input type="text" name="num_staff_se" class="num_staff small_box" value="0" maxlength="4" /></span>
                        </div>
                    </li>
                    <li class="calc_se discount">
                        <div class="expanded_top">
                            <input type="text" name="disc_dash_num_se" class="dash_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_dash_radio_se" class="dash_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_dash_radio_se" class="dash_disc" />%
                        </div>
                        <div class="expanded_bot">
                            <input type="text" name="disc_staff_num_se" class="staff_discount medium_box" value="0.00" />
                            <input type="radio" value="1" name="disc_staff_radio_se" class="staff_disc" checked="checked" />$
                            <input type="radio" value="0" name="disc_staff_radio_se" class="staff_disc" />%
                        </div>
                    </li>
                    <li class="calc_se subtotal">
                        <div class="expanded_top">
                            $ <input type="text" name="subtotal_dash_num_se" class="dash_subtotal large_box" value="99.00" data-full_price="99.00" />
                        </div>
                        <div class="expanded_bot">
                            $ <input type="text" name="subtotal_staff_num_se" class="staff_subtotal large_box" value="0.00" />
                        </div>
                    </li>
                </div>
                <div class="calc_topics dd" >
                    <li class="calc_dd datadisk"><img src="<?= get_template_directory_uri() . '/images/checkbox.png'?>" />Data Disk</li>
                    <li class="calc_dd subtotal">
                        <div class="expanded_top">
                            $ <input type="text" name="subtotal_dd" class="datadisk_subtotal large_box" value="0" data-full_price="<?= DATA_DRIVE ?>" />
                        </div>
                    </li>
                </div>

            </div>
            <div id="calc_total">
                <div id="text_total">
                    <strong>TOTAL</strong>
                </div>
                <div id="sum_total">
                    $ <input type="text" name="total_num" class="total_num large_box" value="0.00" readonly="readonly" />
                </div>
            </div>
            <div id="calc_footer">
                <input type="button" value="Next" id="submit_calc" />
            </div>
        </div>
                
        <div id="billing">
            <h3>Billing Information</h3>
            <div class="table_header">
                <li class="table_header_list library">Library</li>
                <li class="table_header_list amount">Amount</li>
                <li class="table_header_list discount">Discount</li>
                <li class="table_header_list subtotal">Subtotal</li>
            </div>
            <div class="billing_items">
                
            </div>  
            <div id="bill_total">
                <div id="text_total">
                    <strong>TOTAL</strong>
                </div>
                <div id="bill_sum_total">
                </div>
            </div>
            
            <div id="error_box">
                <h2>Errors found</h2>
                <p id="error_msgs">
                    Name not specified <br />
                    Phone number not given
                </p>
            </div>
            
    <?php
            $org_name = apply_filters ('the_title', $org->post_title);
            $full_name = ucwords ($user->user_firstname . " " . $user->user_lastname);
            $address = get_post_meta ($org_id, 'org_address', true);
            $city = get_post_meta ($org_id, 'org_city', true);
            $state = get_post_meta ($org_id, 'org_state', true);
            $country = get_post_meta ($org_id, 'org_country', true);
            $zip = get_post_meta ($org_id, 'org_zip', true);
            $phone = get_post_meta ($org_id, 'org_phone', true);
    ?>
            <div id="bill_form">

                <p class="small"><b>All fields required.</b> *</p>

<!--                <form id="sales-rep-new-subscription" name="sales-rep-new-subscription" action="#" data-user_id="" method="post"> -->
                
                    <table class="staff_accounts subscription_confirm Tstandard data" id="total_table_payment">
                    </table>
                    <h2>Billing Address</h2>
                    <div class="form-row">
                        <label>Organization Name</label>
                        <input type="text" name="org_name" value="<?php echo $org_name; ?>" required/>
                    </div>
                    <div class="form-row">
                        <label>Cardholder Name</label>
                        <input type="text" name="full_name" value="<?php echo $full_name; ?>" required/>
                    </div>
                    <div class="form-row">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo $address; ?>" required/>
                    </div>
                    <div class="form-row">
                        <label>City</label>
                        <input type="text" name="city" value="<?php echo $city; ?>" required/>
                    </div>
                    <div class="form-row">
                        <label>State/Province</label>
                        <input type="text" name="state" value="<?php echo $state; ?>" required/>
                    </div>
                    <div class="form-row">
                        <label>Country</label>
                        <input type="text" name="country" value="<?php echo $country; ?>" required/>
                    </div>
                    <div class="form-row">
                        <label>Zip/Postal Code</label>
                        <input type="text" name="zip" value="<?php echo $zip; ?>" required/>
                    </div>
                    <div class="form-row">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo $phone; ?>" required/>
                    </div>

                    <div class="form-row">
                        <label>Send Data disc:</label>
                        <input type="radio" name="send_disc" value="1">Yes &nbsp;&nbsp;
                        <input type="radio" name="send_disc" value="0" checked="checked">No<br />
                    </div>

                    <div class="form-row">
                        <label>Umbrella Group:</label>
                        <select name="ugroup_id" id="ugroup_id">
                            <option value="0" selected="selected">Select Group</option>
<?php
                            $umbrellaGroups = getUmbrellaGroups();

                            foreach ($umbrellaGroups as $group)
                            {
                                echo '<option value="'.$group["org_id"].'">'.$group["camp_name"].'</option>';
                            }
?>                            
                        </select>
                        <input type="hidden" name="ugroup" value="" />
                   </div>

                    <div class="form-row">
                        <label>How did you hear about us?</label><br />
                        <select id="freferred_by" name="referred_by">
                                <option value="google">Google or another search engine</option>
                                <option value="friend">Friend or colleague</option>
                                <option value="presenters">Presenters</option>
                                <option value="conference_workshop">Conference workshop</option>
                                <option value="conference_booth">Conference booth</option>
                                <option value="promotional_video">Promotional video</option>
                                <option value="promotional_postcard">Promotional postcard</option>
                                <option value="eot_facebook_page">EOT’s Facebook page</option>
                                <option value="eot_twitter_page">EOT’s Twitter page</option>
                                <option value="eot_blog">EOT’s Blog on this site</option>
                                <option value="camp_business_magazine">Camp Business magazine</option>
                                <option value="aca_camping_magazine">ACA’s Camping magazine</option>
                                <option value="aca_twitter_feed">ACA’s Twitter feed</option>
                                <option value="aca_email_blast">ACA’s e-mail blasts</option>
                                <option value="aca_facebook_page">ACA’s Facebook page</option>
                                <option value="aca_now_publication">ACA’s Now publication</option>
                                <option value="aca_website_einstitute">ACA’s website or e-institute</option>
                                <option value="aca_conference_promotion">ACA’s conference promotion (code EEP_767)</option>
                                <option value="wecontacted">We contacted you</option>
                                <option value="other">Other</option>
                        </select>
                        <div id="referred_other">
                            <input id="fother" type="text" name="referred_other" size="25" value="" />
                        </div>
                    </div>
                    <div class="form-row">
                        <label>Notes:</label>
                        <textarea name="notes" id="notes"></textarea>
                   </div>
                    <div class="form-row">
                        <label>Payment Method:</label>
                        <select name="method" id ="method">
                            <option value="Stripe">Credit Card</option>
                            <option value="check">Check</option>
                            <option value="free">Free</option>
                        </select><br /><br />
                    </div>

               <div id="creditcard_opts">
                    <h2>Credit Card</h2>
                    <?php 
                        $cus_id = get_post_meta($org_id, 'stripe_id', true);
                        $cards = get_customer_cards ($cus_id);
                    ?>

                    <?php if (!empty($cards)) { ?>
                        <table cellpadding="5" cellspacing="0" width="90%" class="cc_cards_list">
                            <tr>
                                <td>&nbsp;</td>
                                <td>Type</td>
                                <td>Number</td>
                                <td>Expiration</td>
                                <td>CVC</td>
                            </tr>
                            <?php foreach ($cards as $card) { ?>
                                <tr>
                                    <td><input type="radio" name="cc_card" value="<?php echo $card->id; ?>" /></td>
                                    <td><?php echo $card->brand; ?></td>
                                    <td>**** **** **** <?php echo $card->last4; ?></td>
                                    <td><?php echo $card->exp_month; ?> / <?php echo $card->exp_year; ?></td>
                                    <td>***</td>
                                </tr>
                            <?php } ?>
                        </table>
                        <a href="#" id="new_card">Add new Card</a>
                    <?php } ?>
                        <div id="new_cc_form" <?php if (!empty($cards)) { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?> >
                            <div class="form-row">
                                <label>Card Number</label>
                                <input type="text" size="20" autocomplete="off" name="cc_num" value=""/>
                            </div>
                            <div class="form-row">
                                <label>CVC</label>
                                <input type="text" size="4" autocomplete="off" name="cc_cvc" value=""/>
                            </div>
                            <div class="form-row">
                                <label>Expiration</label>
                                <select name="cc_mon">
                                    <option value="" selected="selected">MM</option>
                                    <?php for ($i = 1 ; $i <= 12 ; $i++) { ?>
                                        <option value="<?php if ($i < 10) {echo "0";} echo $i; ?>"><?php if ($i < 10) {echo "0";} echo $i; ?></option>
                                    <?php } ?>
                                </select>
                                <span> / </span>
                                <select name="cc_yr">
                                    <option value="" selected="selected">YYYY</option>
                                    <?php for ($i = date('Y') ; $i <= (date('Y') + 10) ; $i++) { ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                    <?php if ($cus_id) { ?><input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" /><?php } ?>

                    <input type="hidden" name="email" value="<?php echo $user->user_email; ?>" />
                    <input type="hidden" name="org_id" value="<?php echo $org_id; ?>" />
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                    <input type="hidden" name="terms_of_use" value="accept" />
                    <input type="hidden" name="total_price" id="total" value="0.00" />

                    <?php 
                        // if its a sales person, add their rep ID
                        if(current_user_can( "is_sales_rep" ) || current_user_can( "is_sales_manager" ))
                        {
                            global $current_user;
                            echo '<input type="hidden" name="rep_id" value="'. $current_user->ID .'" />';
                        }
                    ?>

                    <p>
                        <i class="fa fa-lock"></i> This site uses 256-bit encryption to safeguard your credit card information.
                    </p>

                </div>
                
                
                <input type="button" value="Make Payment" id="submit_bill" />

        </div>        

    <div class="processing_payment round_msgbox">
        Attempting to charge Credit card and create the subscription... <br />
        <img src="<?= get_template_directory_uri() . '/images/loading.gif'?>" />                         <br />
        If you see this message for more than 15 seconds, please call 877-237-3931 for assistance.  
    </div>
 </div>
    
    <div id="bill_response">
    </div> 

<?php
}

// return an array of all umbrella group post IDs and the camp name
function getUmbrellaGroups()
{
    $uberAdmins = array();
    $args = array(
        'role__in' => array('uber_manager')
    );
    $users = get_users($args);

    foreach ($users as $uberAdmin)
    {

        $org_id = get_user_meta($uberAdmin->ID, 'org_id', true);
        $camp_name = get_the_title($org_id);

        array_push( $uberAdmins, array('org_id' => $org_id, 'camp_name' => $camp_name) );
    }

    return $uberAdmins;
}

/********************************************************************************************************
 * Upgrade Subscription via sales rep or sales administrator
 *******************************************************************************************************/
add_action('wp_ajax_upgradeSubscription', 'upgradeSubscription_callback');
function upgradeSubscription_callback () 
{

    require_once ('stripe_functions.php');

    // Check permissions only sales rep/manager or director can upgrade account.
    if( !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') && !current_user_can('is_director') )
    {
        $result['status'] = false;
        $result['message'] = 'upgradeSubscription_callback Error: Sorry, you do not have permisison to view this page.';
        echo json_encode($result);
        wp_die();   
    }


    if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
    {
        $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
        $ordered_accounts = intval(filter_var($_REQUEST['accounts'],FILTER_SANITIZE_NUMBER_INT));
        $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);
        $rep_id = filter_var($_REQUEST['rep_id'],FILTER_SANITIZE_NUMBER_INT);
        $method = filter_var($_REQUEST['method'],FILTER_SANITIZE_STRING);
        $discount_note = filter_var($_REQUEST['discount_note'],FILTER_SANITIZE_STRING);
        $other_note = filter_var($_REQUEST['other_note'],FILTER_SANITIZE_STRING);
        $trans_id = ''; 

        // Calculates all the staff credits for the camp
        $subscription = getSubscriptions($subscription_id);
        if($subscription) // get the subscription info for this subscription
        {
            $staff_credits = intval($subscription->staff_credits); // The staff credits
            $org_id = $subscription->org_id; // The subscription org ID
            /* 
             * Calculate the accounts available for the camp from wp_upgrade subscription table.
             */
            $upgrades = getUpgrades($subscription_id);
            foreach ($upgrades as $upgrade)
            {
                $staff_credits += intval($upgrade->accounts); 
            }
            $accounts = $staff_credits + $ordered_accounts; // New Staff Credits
            
            if($_REQUEST['price']) // Sales rep has the option of setting a price.
            {
                $price = filter_var($_REQUEST['price'],FILTER_SANITIZE_NUMBER_FLOAT); // Amount sold for the subscription
            } 
            else  // Request coming from director.
            {
                $other_note = 'Self upgrade via Credit Card';
                $price = calculateCost($subscription->library_id, $accounts, $ordered_accounts);
            }

            // Credit card info
            $statement_description = "Expert Online Training: " . $ordered_accounts . " Additional Staff Accounts";

            // check if paying by credit card
            if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'Stripe')
            {

                if (!isset($_REQUEST['cc_card']) && ($_REQUEST['cc_num'] == '' || $_REQUEST['cc_cvc'] == '')) 
                {
                    $result['status'] = false;
                    $result['message'] = 'You must choose a credit card or add a new credit card.'; 
                    echo json_encode($result);
                    wp_die();   
                }
            
                $cc_card = array (
                    "object" => "card",
                    "number" => $_REQUEST['cc_num'],
                    "exp_month" => $_REQUEST['cc_mon'],
                    "exp_year" => $_REQUEST['cc_yr'],
                    "cvc" => $_REQUEST['cc_cvc'],
                    "name" => $_REQUEST['full_name'],
                    "address_line1" => $_REQUEST['address'],
                    "address_city" => $_REQUEST['city'],
                    "address_state" => $_REQUEST['state'],
                    "address_zip" => $_REQUEST['zip'],
                    "address_country" => $_REQUEST['country']
                );

                if (isset($_REQUEST['customer_id'])) 
                {
                    $customer_id = $_REQUEST['customer_id'];
                } 
                else 
                {
                    $customer = create_new_customer ($cc_card, $_REQUEST['email'], $_REQUEST['org_name']); //$customer->{'id'}; 
                    $customer_id = $customer['customer_id'];
                    $card_id = $customer['cc_card'];
                    update_post_meta ($org_id, 'stripe_id', $customer_id);
                }

                if (isset($_REQUEST['cc_card'])) 
                {
                    $card_id = $_REQUEST['cc_card'];
                } 
                else 
                {
                    $card_id = $cc_card;
                }
                
                $trans_id = charge_customer ($price, $customer_id, $card_id, $statement_description); //$charge->{'id'};
            }
            else if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'free')
            {
                $trans_id = 'FREE';
            }
            else if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cheque')
            {
                $trans_id = 'CHEQUE';
            }

            $data = compact('org_id', 'price', 'ordered_accounts', 'user_id', 'method', 'discount_note', 'other_note', 'rep_id', 'trans_id');

            if($trans_id)
            { 
                // Add a row in the upgrade table
                $results = addSubscriptionUpgrade ($subscription_id, $data);
                if(isset($results['success']) && $results['success'])
                {
                    $result['data'] = 'success';
                    $result['status'] = true;
                    $result['library_id'] = $subscription->library_id; // Needed for redirect 
                }
                else
                {
                    $result['status'] = false;
                    $result['message'] = 'upgradeSubscription_callback Error: There was an error adding the upgrade row in WP. ' . $response['errors'];
                }
            }
            else
            {   // This does not need to return json. Stripe echos the return.
                wp_die();
            }
        }
        else
        {
            $result['status'] = false;
            $result['message'] = 'upgradeSubscription_callback ERROR: Unable to find this subscription ID. Please contact the administrator.'; 
        }
    }
    else
    {
        $result['status'] = false;
        $result['message'] = 'upgradeSubscription_callback ERROR: Missing some parameters.'; 
    }
    echo json_encode($result);
    wp_die();   
}

/**
 * check whether the user accepted the terms and condition
 * if not display the terms and conditions
 * @param object $library - the library opject
 * returns true if accepted false otherwise (and echo's the terms)
 */
function accepted_terms($library = NULL)
{
    global $current_user;
    $user_id = $current_user->ID;
    $accepted = get_user_meta($user_id, "accepted_terms", true); // Boolean if user has accepted terms
    
    if ($library == NULL)
    {
        return false; // cant display terms because no library object was provided.
    }
    else if (!$accepted)
    {
        // output the terms
       echo current_user_can('is_student') ? $library->terms_staff : $library->terms_manager;
       echo "<input type='button' class='terms' onclick='acceptTerms()' value='Yes, I accept the terms and conditions'>
            <div id='term_loading'>
                <i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i>
                <span class='sr-only'>Loading...</span>
            </div>
            <script>
           $ = jQuery;
            function acceptTerms()
            {
                $('#term_loading').show(); // Show the loading icon.
                //set up the ajax call parameters
                var data = { action: 'acceptTerms'};
                var url =  ajax_object.ajax_url;

                //ajax call to update the parameter
                 $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: data,
                    success:
                    function(data)
                    {
                        // Request Failed
                       if(data.status == 0)
                       {
                            // Alert the error message and hide the loading icon.
                            alert(data.message);
                            $('#term_loading').hide();
                       }
                       // Request went succesfully. Redirect to tutorial.
                       else if(data.status == 1)
                       {
                            window.location.href = data.location;
                       }
                    }
               });
            }
       </script>";  
    }
    else if ($accepted == 1)
    {
        return true;
    }

    return false; // something went wrong if you got here
}

?>