<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <?= CRUMB_ADMINISTRATOR ?>    
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_MANAGE_STAFF_ACCOUNTS ?>
    <?= CRUMB_SEPARATOR ?> 
    <span class="current">Invite Staff To Register</span>     
</div>

<?php
    global $current_user;
    // verify this user has access to this portal/subscription/page/view
    $true_subscription = verifyUserAccess();
    $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = $current_user->ID;
    $directors_email = $current_user->user_email; // the director's email
    $step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 1; //step 4 comes before 3 so 1,2,4,3,5,6 etc
    
    // Check if the subscription ID is valid.
    if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] !== "") 
    {
        $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        $courses = getCoursesById($org_id, $subscription_id);
        if (isset($true_subscription['status']) && $true_subscription['status']) 
        {
            if (current_user_can("is_director")) 
            {
                switch ($step) 
                {
                    case 1:
                        // the options page
?>
                        <h1 class="article_page_title">Invite Staff To Register</h1>
                        <div class="msgboxcontainer_no_width">
                            <div class="msg-tl">
                                <div class="msg-tr"> 
                                    <div class="msg-bl">
                                        <div class="msg-br">
                                            <div class="msgbox">
                                                <p>Staff will receive an e-mail with a hyperlink (containing a unique code) that lets them register
                                                    and be automatically placed in the Camp or Course.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h2>Choose an Invitation Method:</h2>
                        <ol>
                            <li>
                                <h3>Use our Invitation Sender</h3>
                                <ul>
                                    <li>
                                        Just copy-and-paste the staff e-mail addresses and we'll send out the invitation e-mail.
                                    </li>
                                    <li>
                                        You can <b>Track</b> those who haven't registered yet by viewing the <b>Invite List Report</b>.
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <h3>Use your own E-mail (Hotmail, GMail, etc.)</h3>
                                <ul>
                                    <li>
                                        We give you the hyperlink to copy-and-paste into an e-mail and you send it yourself.
                                    </li>
                                    <li>
                                        You <b><u>cannot</u> Track</b> those who haven't registered yet.
                                    </li>
                                </ul>
                            </li>
                        </ol>
                        <div class="buttons" >        
                            <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=4&use_email=yes" class = "use_own_email" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Use your own Email
                                </div>
                            </a>
                            <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=2&use_email=no" class = "use_invitation_email" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Use our Invitation Sender
                                </div>
                            </a>            
                            <div style="clear:both">
                            </div>                      
                        </div>
<?php
                    break;

                    case 2:
                        // our invitation sender. User will be asked to input a comma seperated list of emails
?>
                        <h1 class="article_page_title">Staff Emails</h1>
                        <div class="msgboxcontainer_no_width">
                            <div class="msg-tl">
                                <div class="msg-tr"> 
                                    <div class="msg-bl">
                                        <div class="msg-br">
                                            <div class="msgbox">
                                                <p>
                                                    Paste a list of e-mail addresses in the field below.
                                                </p>
                                                <div class="small">
                                                    <b>Format of E-mail Addresses:</b>
                                                    The format should be one of the following:
                                                    <ul>
                                                        <li>
                                                            comma-separated list, or
                                                        </li>
                                                        <li>
                                                            one e-mail address per line (no comma), or
                                                        </li>
                                                        <li>
                                                            a combination of commas and one-per-line
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form action="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=4" method="post" id="">
                            <?php if (isset($_REQUEST['emails'])) { ?>
                                <textarea name="emails" style="width: 100%; height: 300px;"><?= $_REQUEST['emails'] ?></textarea>
                            <?php } else { ?>
                                <textarea name="emails" style="width: 100%; height: 300px;">john@sample.com, jane@email.com
OR
john@sample.com
jane@email.com
                                </textarea>

                            <?php } ?>
                        </form>
                        <br>
                        <div class="buttons" >        
                            <a href="#" class = "submit_invite" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Next
                                </div>
                            </a>            
                            <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>" class = "" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Back
                                </div>
                            </a>
                            <div style="clear:both">
                            </div>                      
                        </div>
                        <script>
                            // joins all the emails by commas
                            $(document).ready(function () {
                                $('.submit_invite').click(function (e) {
                                    e.preventDefault();
                                    $("textarea[name='emails']").html($("textarea[name='emails']").attr('value'));
                                    var myarr = $("textarea[name='emails']").html().split(/,|\s/);
                                    var clean_array = new Array();
                                    var counter = 0;
                                    for (my in myarr) {
                                        if (myarr[my].length > 0) {
                                            clean_array[counter] = $.trim(myarr[my]);
                                            counter++;
                                        }
                                    }
                                    var final_string = clean_array.join(',');
                                    $("textarea[name='emails']").val(final_string);
                                    $('form').submit();
                                });
                            });
                        </script>
<?php
                    break;

                    case 3:
                        // displays the tinymce editor with generated code
                        $choice = isset($_REQUEST['choice']) ? filter_var($_REQUEST['choice'], FILTER_SANITIZE_STRING) : '';
                        $subscription_id = $_REQUEST['subscription_id'];
                        $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
                        $course_id = isset($_REQUEST['course_id']) ? filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT) : '';
                        $emails = isset($_REQUEST['emails']) ? $_REQUEST['emails'] : '';
                        $camp_name = get_the_title($org_id);
                        $code = "%%code%%";
?>
                        <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.tinymce.js' ?>"></script>
                        <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/tinymce/tiny_mce.js' ?>"></script>
                        <h1 class="article_page_title">Compose Message</h1>
                        <div class="msgboxcontainer_no_width">
                            <div class="msg-tl">
                                <div class="msg-tr"> 
                                    <div class="msg-bl">
                                        <div class="msg-br">
                                            <div class="msgbox">
                                                <p>The following message will be sent to your staff. For your convenience we've written a sample letter that you can customize to your liking. Once you are done, click <strong>Send</strong>. <br><br>Your message <strong>MUST</strong> include the following text which will substitute a unique sign up link for each staff member:<br><strong><?= $code; ?></strong> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form id= "use_invitation_msg"  action="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=5" method="POST"> 
                            <table padding="0" class="form">
                                <tr> 
                                    <td class="value"> 
                                        <textarea class="tinymce" id="composed_message" name="msg" style="margin-left:1px;width: 100%; height: 300px">
                                            <p><b>Congratulations!</b> <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> of <?= $camp_name; ?> is inviting you to join  Expert Online Training (EOT), the world’s best virtual classroom for youth development professionals. By using EOT now, before your job starts at <?= $camp_name; ?>, you will turbocharge your leadership skills, boost your self-confidence, and get even more out of <?= $camp_name; ?>’s on-site training. </p>

                                            <div style="height: 94px;"><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" alt="EOT Logo" style="width: 125px; height: 94px; float: left;"> <b>Take EOT with you.</b> We know you are busy, so our new website is mobile-friendly. You can now watch EOT videos and take your quizzes on any smartphone, tablet, or laptop with a WiFi connection. Imagine learning more about behavior management, leadership, supervision, games, and safety while you sit in a café, library, or student lounge!</div>

                                            <p>You must create your account by following this link:</p>

                                            <p><?= $code; ?></p>

                                            <p>To watch EOT’s intro video, <a href="https://www.expertonlinetraining.com" target="_blank">click here</a>.</p>

                                            <p><b>When is it due?</b> Directors usually require staff to complete their online learning assignment before arriving on-site. If you have not yet received a due-date for your assignment, check with <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> to get one. As you move through your course, <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> will have access to an electronic dashboard that allows them to track your progress and quiz scores.</p>

                                            <p><b>Got Questions?</b> If you get stuck, watch our online help videos or call us at <b>877-390-2267</b>! The EOT Customer Success team is on duty M-F from 9-5 ET. As Director of Content, I also welcome your comments and suggestions for new features and video topics.</p>

                                            <p>Enjoy your training!<br><br>
                                            <img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" alt="Chris's signature" style="width: 100px; height: 55px;"><br><br>
                                            Dr. Chris Thurber<br>
                                            EOT Co-Founder &<br>
                                            Director of Content</p>
                                        </textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" name="code" id="org_id" value="<?= $code ?>" />
                                        <input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
                                        <input type="hidden" name="subscription_id" id="subscription_id" value="<?= $subscription_id ?>" /> 
                                        <input type="hidden" name="choice" id="" value="<?= $choice ?>" />
                                        <input type="hidden" name="course_id" id="" value=" <?= $course_id ?>" />
                                        <input type="hidden" name="emails" value="<?= $emails ?>" />
                                    </td>
                                </tr>
                            </table> 
                        </form>

                        <br>
                        <div class="buttons" >        
                            <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=5" class = "submit_invite" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Send
                                </div>
                            </a>            
                            <a href="#" class = "back_btn" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Back
                                </div>
                            </a>
                            <div style="clear:both">
                            </div>                      
                        </div>
                        <script>
                            $(document).ready(function () {
                                tinymce.init({selector: 'textarea',
                                    script_url: 'libraries/tinymce/jscripts/tiny_mce/tiny_mce.js',
                                    mode: "textareas",
                                    theme: "advanced",
                                    entity_encoding: "named",
                                    entities: "&nbsp;",
                                    theme_advanced_buttons1: "bold,italic,underline,|,justifyleft,justrifycenter,justifyright,justifyfull,|,bullist,numlist,|,code",
                                    theme_advanced_buttons2: "",
                                    theme_advanced_buttons3: "",
                                    theme_advanced_toolbar_location: "top",
                                    theme_advanced_toolbar_align: "left",
                                    theme_advanced_resizing: true
                                });
                                $('.back_btn').click(function(e){
                                    e.preventDefault();
                                    $('form').attr('action',"/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=4");
                                    $('form').submit();
                                });
                                $('.submit_invite').click(function(e){
                                    e.preventDefault();
                                    $('form').submit();
                                });
                            });
                        </script>
<?php
                    break;
                    
                    case 4:
                        // user will select what happens after the staff member signs up. either enroll in camp or in a specific course.
                        $emails = isset($_REQUEST['emails']) ? $_REQUEST['emails'] : '';
                        $use_email = isset($_REQUEST['use_email']) ? $_REQUEST['use_email'] : '';
                        if($use_email == "yes")
                        {
                            echo "<script>var use_email=true;</script>";
                        }
                        else
                        {
                            echo "<script>var use_email=false;</script>";
                        }
?>
                        <h1 class="article_page_title">Select Registration Type</h1>
                        <div class="msgboxcontainer_no_width">
                            <div class="msg-tl">
                                <div class="msg-tr"> 
                                    <div class="msg-bl">
                                        <div class="msg-br">
                                            <div class="msgbox">
                                                <p>When a staff member signs up, please select whether or not you would like to enroll these staff members into a specific course or simply add them to your camp and enroll them into specific courses later on.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" id="choose_enrollment" action="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=3"> 
                            <input type="radio" name="choice" id="" value="org">
                            <label for="">Just add these staff members to my camp</label><br>
                            <input type="radio" name="choice" id="" value="course">
                            <label for="">I would like to enroll these staff members into a specific course</label><br>
                            
                            <input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
                            <input type="hidden" name="emails" id="emails" value="<?= $emails; ?>" />
                            <input type="hidden" name="subscription_id" id="subscription_id" value="<?= $subscription_id ?>" />
                            <div class='courses' style="display:none">
                                <h3>Course(s)</h3>
<?php
                                foreach($courses as $course)
                                {
                                    echo '<input type="radio" name="course_id"  value='.$course['ID'].'><label for="">'.$course['course_name'].'</label><br>';
                                }
?>
                            </div>
                        </form>
                        <br>
                        <div class="buttons" >        
                            <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=3" class = "next_btn" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Next
                                </div>
                            </a>            
                            <a href="#" class = "go_back" >
                                <div style="height:15px;padding-top:2px;"> 
                                    Back
                                </div>
                            </a>
                            <div style="clear:both">
                            </div>                      
                        </div>
                        <script>
                            $(document).ready(function(){
                                $("input[name='choice']").click(function(){
                                    if($(this).val()==="course"){
                                        $('.courses').show();
                                    }else{
                                        $('.courses').hide();
                                    }
                                });
                                $(".go_back").click(function(e){
                                    console.log("go back");
                                    e.preventDefault();
                                    $("form").attr("action","/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=2");
                                   jQuery("#choose_enrollment").submit();
                                });
                                $('.next_btn').click(function(e){
                                    e.preventDefault();
                                    console.log($("input[name='choice']").val());
                                    if(!$("input[name='choice']:checked").val()){
                                        alert('Please make a selection');
                                        return false;
                                    }else if($("input[name='choice']:checked").val()=="course"){
                                        if(!$("input:radio[name='course_id']").is(":checked")){
                                        alert('Please choose a course');
                                        return false;
                                        }
                                    }
                                    if(use_email){
                                        $("form").attr("action","/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=6");
                                    }
                                        jQuery("#choose_enrollment").submit();
                                    
                                });
                            });
                        </script>
<?php
                    break;
             
                    case 5:
                        // process the recipients when using the invitation sender
                        global $wpdb;
                        global $current_user;
//                        $code = isset($_REQUEST['code']) ? filter_var($_REQUEST['code'], FILTER_SANITIZE_STRING) : '';
                        $choice = isset($_REQUEST['choice']) ? filter_var($_REQUEST['choice'], FILTER_SANITIZE_STRING) : '';
                        $msg = isset($_REQUEST['msg']) ? stripslashes($_REQUEST['msg']) : '';
                        $emails = isset($_REQUEST['emails']) ? filter_var($_REQUEST['emails'], FILTER_SANITIZE_STRING) : '';
                        $recip = explode(",", $emails);
                        $org_id = isset($_REQUEST['org_id']) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : '';
                        $course_id = isset($_REQUEST['course_id']) ? filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT) : '';
                        $subscription_id = isset($_REQUEST['subscription_id']) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : '';
                        $recipients = array();
                        $errors = array();
      
                        $failed = 0;
                        foreach($recip as $recipient)
                        {
                            $recipient = filter_var($recipient, FILTER_VALIDATE_EMAIL);
                            if ($recipient && !in_array($recipient,$recipients)) 
                            {
                                array_push($recipients, $recipient);
                                if($choice == 'org')
                                {
                                    $code = wp_hash($current_user->user_email);
                                }
                                else
                                {
                                    $code = wp_hash($recipient.$course_id); 
                                }
                                
                                // add URL to the code
                                $code_url = site_url('/register/?type=Student&code='.$code);

                                $vars = array(
                                    'code' => $code_url
                                );

                                /* Replace %%VARIABLE%% using vars*/
                                foreach($vars as $key => $value)
                                {
                                  $message = preg_replace('/%%' . $key . '%%/', $value, $msg);
                                }
                                
                                $data = array(
                                  'org_id'=>$org_id,
                                  'sender_name'=>$current_user->user_firstname." ".$current_user->user_lastname,
                                  'sender_email'=>$directors_email,
                                  'email'=>$recipient,
                                  'subject'=>"Your account on ExpertOnlineTraining.com (Camp Training)",
                                  'message'=>$message
                                );

                                $query = "SELECT ID FROM " . TABLE_PENDING_EMAILS . " WHERE org_id = $org_id AND email = '$recipient'";

                                // check that the email doesn't exist in the table before you add it
                                $email_exists = $wpdb->get_row($query, ARRAY_A);

                                $result = true; // assume email either exists in pending emails table or will be inserted below.
                                if (!$email_exists)
                                {
                                    $result = $wpdb->insert(TABLE_PENDING_EMAILS, $data);
                                }

                                // check if there was an error inserting the email into the DB
                                if (!$result)
                                {
                                  $failed = 1;
                                  array_push($errors, array 
                                    (
                                      'email' => $recepient,
                                      'error_message' => $wpdb->print_error
                                    )
                                  );
                                }
                                else // successfully added user to pending emails or email existed previously now add the code.
                                {
                                    if($choice == "course")
                                    {
                                        $data2 = array(
                                            'code'=>$code,
                                            'org_id'=>$org_id,
                                            'subscription_id'=>$subscription_id,
                                            'course_id'=>$course_id,
                                            'user_email'=>$recipient,
                                            'date'=>current_time('Y-m-d H:i:s'),
                                            'type'=>'user'
                                        );
                                        $query = "SELECT ID FROM " . TABLE_INVITATIONS . " WHERE code = '$code' AND org_id = $org_id AND subscription_id = $subscription_id AND course_id = $course_id AND type = 'user' AND user_email = '$recipient'";
                                    }
                                    else
                                    {
                                        $data2 = array(
                                            'code'=>$code,
                                            'org_id'=>$org_id,
                                            'subscription_id'=>$subscription_id,
                                            'user_email'=>$recipient,
                                            'date'=>current_time('Y-m-d H:i:s'),
                                            'type'=>'user'
                                        );
                                        $query = "SELECT ID FROM " . TABLE_INVITATIONS . " WHERE code = '$code' AND org_id = $org_id AND subscription_id = $subscription_id AND type = 'user' AND user_email = '$recipient'";
                                    }
                                    // check if the code exists, if not add it
                                    $code_exists = $wpdb->get_row($query, ARRAY_A);
                                    $result = true; // assume code exists or we will add it below.
                                    if (!$code_exists)
                                    {
                                        $result = $wpdb->insert(TABLE_INVITATIONS, $data2);
                                    }

                                    if (!$result)
                                    {
                                        $failed = 1;
                                        array_push($errors, array 
                                            (
                                                'email' => $recepient,
                                                'error_message' => $wpdb->print_error
                                            )
                                        );
                                        error_log("Couldn't insert code into invitations table for $recipient: " . $wpdb->print_error);
                                    
                                        // since we failed to add the code, need to remove the user from pending email
                                        $wpdb->delete(TABLE_PENDING_EMAILS, array
                                            (
                                                'org_id' => $org_id,
                                                'email' => $recipient
                                            )
                                        );
                                    }
                                }
                            }
                        }
                        
                        $redirect_url = '/dashboard?part=invite_users&subscription_id='.$subscription_id.'&org_id='.$org_id.'&process=1&step=7&max='.count($recipients);
                        // if failed return false
                        if ($failed)
                        {
                          //return array('status' => 0, 'errors' => $errors);
                            $redirect_url .= '&errors=' . json_encode($errors);
                        }
                        wp_redirect(site_url($redirect_url));
                        exit();
                        break;
 
                    case 6:
                        // director wants to use his own email so displays a sample message with the code.
                        $choice = isset($_REQUEST['choice']) ? filter_var($_REQUEST['choice'], FILTER_SANITIZE_STRING) : '';
                        $org_id = isset($_REQUEST['org_id']) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : '';
                        $subscription_id = isset($_REQUEST['subscription_id']) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : '';
                        $course_id = isset($_REQUEST['course_id']) ? filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT) : '';

                        if($choice == "org")
                        {
                            $code = wp_hash($directors_email);
                            $data2 = array(
                                            'code'=>$code,
                                            'org_id'=>$org_id,
                                            'subscription_id'=>$subscription_id,
                                            'date'=>current_time('Y-m-d H:i:s'),
                                            'type'=>'org'
                                        );
                            $query = 'SELECT ID FROM ' . TABLE_INVITATIONS . ' WHERE code = "' . $code .'" AND type = "org"' . 'AND org_id = ' . $org_id;
                        }
                        else
                        {
                            $code = wp_hash($directors_email.$course_id);
                            $data2 = array(
                                            'code'=>$code,
                                            'org_id'=>$org_id,
                                            'course_id'=>$course_id,
                                            'subscription_id'=>$subscription_id,
                                            'date'=>current_time('Y-m-d H:i:s'),
                                            'type'=>'course'
                                        ); 
                            $query = 'SELECT ID FROM ' . TABLE_INVITATIONS . ' WHERE code = "' . $code .'" AND type = "course" AND course_id = ' . $course_id . ' AND org_id = ' . $org_id;
                        }

                        //before insert the code, check that it doesn't exist
                        $code_exists = $wpdb->get_row($query, ARRAY_A);
                        if (!$code_exists)
                        {
                           $result = $wpdb->insert(TABLE_INVITATIONS, $data2);
                        }
?>
                        <div class="fixed_fb_width">
                			<div class="msgboxcontainer_no_width">
                			  <div class="msg-tl">
                				<div class="msg-tr"> 
                				  <div class="msg-bl">
                					<div class="msg-br">
                						<div class="msgbox">
                							<h2>Instructions</h2> For your convenience we've written a sample letter that you can send to your staff. Copy and paste this letter into your e-mail program (you can customize the letter of course) and send it out to your staff. <br><br>It contains the unique registration link at the bottom. Your email <strong>MUST</strong> include this code to link your staff to your camp:<br>CODE: <strong><?= $code; ?></strong><br><br>We've already filled in some details for you, like <b>Your Name</b> and your <b>Camp Name</b>
                						</div>
                				  </div>
                				</div>
                			  </div>
            			  </div>
            		  </div>
<p>
    Dear Staff,<br>
    <br>
    Summer is right around the corner! Before you know it, the campers will be arriving at <?= get_the_title($org_id)?>, full of energy, enthusiasm, and youthful exuberance.<br>
    <br>
    Before you arrive for our on-site training, I'd like you to watch a set of short training videos and take the accompanying quizzes. I'll be monitoring your progress along the way. This combination of online and on-site training is engaging, relevant, and essential for your work with children this summer.<br>
    <br>
    Follow the link below to register your account and start the training. If you already have an account, still click the link as it will add you to the appropriate grouping.<br>
    <br>
    CODE: <a href="<?= site_url('/register/?type=Student&code='.$code); ?>"><strong><?= $code; ?></strong></a><br>
    <br>
    If the link above does not work, try to copy/paste this URL into your browser: <?= site_url('/register/?type=Student&code='.$code); ?><br>
    <br>
    If you run into any technical snags, you can call the toll-free support line: 877-390-2267 during regular business hours. <br>
    <br>
    Sincerely,<br>
    <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?><br>
    Camp Director
</p>
    		 </div>
<?php
                    break;

                    case 7:
                        // sending out the invitations
                        $processing = isset($_REQUEST['processing']) ? filter_var($_REQUEST['processing'], FILTER_SANITIZE_NUMBER_INT) : 0; //the number out of total users we are processing right now
                        $max = filter_var($_REQUEST['max'], FILTER_SANITIZE_NUMBER_INT);     //total users being processed from this instance of spreadsheet upload
                        $admin_ajax_url = admin_url('admin-ajax.php');

                        // check if we had errors adding users to tables or code to table:
                        $errors = isset($_REQUEST['errors']) ? json_decode($_REQUEST['errors']) : '';

?>
                        <h1 class="article_page_title">Sending Out Your Invitations</h1>
<?php
                        if ($errors)
                        {
                            echo '<div class="errors round_msgbox">';
                            echo '  There were some errors when we tried to process the following users:<br>';
                            foreach ($errors as $key => $value) {
                                echo $value['email'] . ': ' . $value['error_message'] . '<br>';
                            }
                            echo '  <br>The above users DID NOT get an email invitation to join EOT. You will have to fix the errors and try them again seperatly.';
                            echo '</div>';
                        }
?>
                        <div class="spreadsheet_processing round_msgbox">
                            <strong>Please wait while we send your emails: <br>
                                <span class="processing">Processing 1 out of <?= $max ?></span> ... </strong> <i class="fa fa-spinner fa-pulse fa-2x"></i><br /><br />DO NOT CLOSE THIS WINDOW UNTIL ALL STAFF HAS BEEN EMAILED.<br><br>A report showing which emails were sent and which were not will be displayed once the process is complete.
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
                                        sent_emails += result.sent_emails;
                                        count += <?= PENDING_EMAILS_LIMIT ?>;

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
                                            $('.round_msgbox').html("ERROR: Some emails below did not get sent.<br><br>Please contact us for assistance 1-877-390-2267 M-F 9-5 EST.<br><br>" + sent_emails.replace(/,/g, "")); 
                                        }
                                        else
                                        {
                                            sendMail();
                                        }
                                    }
                                });
                            }
                        </script>
<?php
                            break;
                    default:
                        break;
                }
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
    // Could not find the subscription ID
    else 
    {
        echo "Could not find the subscription ID";
    }
?>

