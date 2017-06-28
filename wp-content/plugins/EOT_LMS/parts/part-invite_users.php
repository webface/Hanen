<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <?= CRUMB_ADMINISTRATOR ?>    
    <?= CRUMB_SEPARATOR ?>
    <a href="/dashboard?part=manage_staff_accounts&org_id=<?= $_REQUEST['org_id']?>&subscription_id=<?= $_REQUEST['subscription_id']?>">Manage Staff Accounts</a>
    <?= CRUMB_SEPARATOR ?> 
    <span class="current">Invite Staff To Register</span>     
</div>

<?php
$subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
$org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
global $current_user;

// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess();
$user_id = $current_user->ID;
$email = $current_user->user_email;
$step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 1;//step 4 comes before 3 so 1,2,4,3,5,6 etc
// Check if the subscription ID is valid.
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "") {
    $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    $courses = getCoursesById($org_id, $subscription_id);
    if (isset($true_subscription['status']) && $true_subscription['status']) {
        if (current_user_can("is_director")) {
            switch ($step) {
                case 1:
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
                <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>" class = "" >
                    <div style="height:15px;padding-top:2px;"> 
                        Back
                    </div>
                </a>
                <a href="#" class = "submit_invite" >
                    <div style="height:15px;padding-top:2px;"> 
                        Next
                    </div>
                </a>            
                <div style="clear:both">
                </div>                      
            </div>
            <script>
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
                    $choice=filter_var($_REQUEST['choice'],FILTER_SANITIZE_STRING);
                    $org_id=filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
                    $course_id=filter_var($_REQUEST['course'],FILTER_SANITIZE_NUMBER_INT);
                    $code="%%code%%";
                    ?>
            <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/jquery.tinymce.js' ?>"></script>
            <script type="text/javascript" src="<?= get_template_directory_uri() . '/js/tinymce/tiny_mce.js' ?>"></script>
            <h1 class="article_page_title">Compose Message</h1>
            <form id= "use_invitation_msg"  action="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=5" method="POST"> 
                <table padding=0 class="form">
                    <tr>
                        <td>
                            <div class="fixed_fb_width">
                                <div class="msgboxcontainer_no_width">
                                    <div class="msg-tl">
                                        <div class="msg-tr"> 
                                            <div class="msg-bl">
                                                <div class="msg-br">
                                                    <div class="msgbox">
                                                        <p>This message (below) will be sent to your staff. For your convenience we've written a sample letter that you can customize to your liking. Once you are done, click <strong>Send Invitations</strong>. <br><br>Your message <strong>must</strong> include the following code:<br><strong><?= $code; ?></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr> 
                        <td class="value"> 
                            <textarea class="tinymce" id="composed_message" name="msg" style="margin-left:1px;width: 100%; height: 300px">
                                                    <b>Welcome</b>, <br><br>
                                                   
                                                    <p><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" alt="EOT Logo" style="width: 125px; height: 94px; float: left;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" data-mce-style="width: 150px; height: 113px; float: left;"> 
                                                    
                                    <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> has invited you to join the camp with this code:<br><br></p>

                                                    <strong><?= $code; ?></strong>

                                                    <br><br>
                                                    <b>Got Questions?</b> If you get stuck, call us at <b>877-237-3931</b>! The EOT Customer Success team is on duty M-F from 9-5 ET. As Director of Content, I also welcome your comments and suggestions for new features and video topics.<br><br>

                                                    Enjoy your training!<br><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" alt="Chris's signature" style="width: 100px; height: 55px;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" data-mce-style="width: 100px; height: 55px;"><br>
                                                    Dr. Chris Thurber<br> 
                                                    EOT Co-Founder &amp;<br> 
                                                    Director of Content
                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" name="code" id="org_id" value="<?= $code ?>" />
                            <input type="hidden" name="org_id" id="org_id" value="<?= $_REQUEST['org_id'] ?>" /> 
                            <input type="hidden" name="subscription_id" id="subscription_id" value="<?= $_REQUEST['subscription_id'] ?>" /> 
                            <input type="hidden" name="choice" id="" value=" <?= $_REQUEST['choice'] ?>" />
                            <input type="hidden" name="course_id" id="" value=" <?= $_REQUEST['course'] ?>" />
                            <input type="hidden" name="emails" value="<?= $_REQUEST['emails'] ?>" />
                        </td>
                    </tr>
                </table> 
            </form>

            <br>
            <div class="buttons" >        
                <a href="#" class = "back_btn" >
                    <div style="height:15px;padding-top:2px;"> 
                        Back
                    </div>
                </a>
                <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=5" class = "submit_invite" >
                    <div style="height:15px;padding-top:2px;"> 
                        Send
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
                        $('form').attr('action',"/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=2");
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
                    $emails=isset($_REQUEST['emails'])?$_REQUEST['emails']:'';
                    $use_email=  isset($_REQUEST['use_email'])?$_REQUEST['use_email']:'';
                    //var_dump($use_email);
                    if($use_email==="yes"){
                        echo "<script>var use_email=true;</script>";
                    }else{
                        echo "<script>var use_email=false;</script>";
                    }
                    ?>
            <h1 class="article_page_title">Select Registration Type</h1>
                        <form method="POST" id="choose_recipients" action="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=3"> 
                <p>
                    On registration, enroll staff
                </p>
                <label for="">Enroll in EOT</label>
                <input type="radio" name="choice" id="" value="org"><br>
                <label for="">Enrol in a specific course</label>
                <input type="radio" name="choice" id="" value="course"><br>
                
                <input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
                <input type="hidden" name="emails" id="emails" value="<?= $emails; ?>" />
                <input type="hidden" name="subscription_id" id="subscription_id" value="<?= $subscription_id ?>" />
                <div class='courses' style="display:none">
                    <h3>Choose Course</h3>
                    <?php
                        foreach($courses as $course){
                            echo '<label for="">'.$course['course_name'].'</label>
                <input type="radio" name="course"  value='.$course['ID'].'><br>';
                        }
                    ?>
                </div>
            </form>

            <br>
            <div class="buttons" >        
                <a href="#" class = "go_back" >
                    <div style="height:15px;padding-top:2px;"> 
                        Back
                    </div>
                </a>
                <a href="/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=3" class = "next_btn" >
                    <div style="height:15px;padding-top:2px;"> 
                        Next
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
                       jQuery("#choose_recipients").submit();
                    });
                    $('.next_btn').click(function(e){
                        e.preventDefault();
                        console.log($("input[name='choice']").val());
                        if(!$("input[name='choice']:checked").val()){
                            alert('Please make a selection');
                            return false;
                        }else if($("input[name='choice']:checked").val()=="course"){
                            if(!$("input:radio[name='course']").is(":checked")){
                            alert('Please choose a course');
                            return false;
                            }
                        }
                        if(use_email){
                            $("form").attr("action","/dashboard?part=invite_users&org_id=<?= $org_id ?>&subscription_id=<?= $subscription_id ?>&step=6");
                        }
                            jQuery("#choose_recipients").submit();
                        
                    });
                });
            </script>
            <?php
                    break;
                case 5:
                    //var_dump($_POST);
                    global $wpdb;
                    global $current_user;
                    $code=filter_var($_REQUEST['code'],FILTER_SANITIZE_STRING);
                    $choice=filter_var($_REQUEST['choice'],FILTER_SANITIZE_STRING);
                    $msg=stripslashes($_REQUEST['msg']);
                    $emails=filter_var($_REQUEST['emails'],FILTER_SANITIZE_STRING);
                    $recip=  explode(",", $emails);
                    $org_id=filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
                    $course_id=filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
                    $subscription_id=filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
                    $recipients=array();
                    $errors = array();
  
                    $failed = 0;
                    foreach($recip as $recipient){
                        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) &&(!in_array($recipient,$recipients))) {
                            array_push($recipients, $recipient);
                            if($choice==='org'){
                                $code= wp_hash($current_user->user_email);
                                
                            }else{
                                $code=wp_hash($recipient.$course_id); 
                            }
                            
                            
                        $vars = array(
                                    'code' => $code
                                );

                            /* Replace %%VARIABLE%% using vars*/
                            foreach($vars as $key => $value)
                            {
                              $message = preg_replace('/%%' . $key . '%%/', $value, $msg);
                            }
                            
                            $data = array(
                              'org_id'=>$org_id,
                              'sender_name'=>$current_user->user_firstname." ".$current_user->user_lastname,
                              'sender_email'=>$current_user->user_email,
                              'email'=>$recipient,
                              'subject'=>"An Invitation from Expert Online Training",
                              'message'=>$message
                            );
                            $result = $wpdb->insert(TABLE_PENDING_EMAILS, $data);

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
                            if($choice==="org"){
                                $data2 = array(
                                    'code'=>$code,
                                    'org_id'=>$org_id,
                                    'subscription_id'=>$subscription_id,
                                    'user_email'=>$recipient,
                                    'date'=>current_time('Y-m-d'),
                                    'type'=>'user'
                                );
                            }else{
                                $data2 = array(
                                    'code'=>$code,
                                    'org_id'=>$org_id,
                                    'course_id'=>$course_id,
                                    'subscription_id'=>$subscription_id,
                                    'user_email'=>$recipient,
                                    'date'=>current_time('Y-m-d'),
                                    'type'=>'user'
                                );
                            }
                            $result = $wpdb->insert(TABLE_INVITATIONS, $data2);
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
                        }
                    }
                    //var_dump($recipients);
                    // if failed return false
                    if ($failed)
                    {
                      //return array('status' => 0, 'errors' => $errors);
                    }
                    wp_redirect(site_url('/dashboard?part=invite_users&subscription_id='.$subscription_id.'&org_id='.$org_id.'&process=1&step=7&max='.count($recipients)));
                    exit();
                    break;
                case 6:
                    $choice=filter_var($_REQUEST['choice'],FILTER_SANITIZE_STRING);
                    $org_id=filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
                    $subscription_id=filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
                    $course_id=filter_var($_REQUEST['course'],FILTER_SANITIZE_NUMBER_INT);
                    if($choice==="org"){
                        $code=  wp_hash($email);
                    }else{
                        $code=  wp_hash($email).$course_id;
                    }
                    if($choice==="org"){
                    $data2 = array(
                                    'code'=>$code,
                                    'org_id'=>$org_id,
                                    'subscription_id'=>$subscription_id,
                                    'date'=>current_time('Y-m-d'),
                                    'type'=>'camp'
                                );
                    }else{
                    $data2 = array(
                                    'code'=>$code,
                                    'org_id'=>$org_id,
                                    'course_id'=>$course_id,
                                    'subscription_id'=>$subscription_id,
                                    'date'=>current_time('Y-m-d'),
                                    'type'=>'course'
                                ); 
                    }
                    $result = $wpdb->insert(TABLE_INVITATIONS, $data2);
                    ?>
            <div class="fixed_fb_width">
			<div class="msgboxcontainer_no_width">
			  <div class="msg-tl">
				<div class="msg-tr"> 
				  <div class="msg-bl">
					<div class="msg-br">
						<div class="msgbox">
							<h2>Instructions</h2> For your convenience we've written a sample letter that you can send to your staff. Copy and paste this letter into your e-mail program (you can customize the letter of course) and send it out to your staff. <br><br>It contains the unique registration link at the bottom. Your email <strong>must</strong> include this code:<br>CODE: &nbsp;&nbsp;<strong><?= $code; ?></strong><br><br>We've already filled in some details for you, like <b>Your Name</b> and your <b>Camp Name</b>
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
CODE: &nbsp;&nbsp;<strong><?= $code; ?></strong>
      <br>
      If you run into any technical snags, you can call the toll-free support line: 877-237-3931 during regular business hours. <br>
<br>
      Sincerely,<br>
      <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?><br>
      Camp Director		  </p>
		 </div>
            <?php
                    break;
                case 7:
                $processing =  isset($_REQUEST['processing'])? filter_var($_REQUEST['processing'], FILTER_SANITIZE_NUMBER_INT):0; //the number out of total users we are processing right now
                $max = filter_var($_REQUEST['max'], FILTER_SANITIZE_NUMBER_INT);     //total users being processed from this instance of spreadsheet upload
                $admin_ajax_url = admin_url('admin-ajax.php');
                 ?>
                <h1 class="article_page_title">Sending Out Your Invitations</h1>

                <div class="spreadsheet_processing round_msgbox">
                    <strong>Please wait while we send your emails: <br>
                        <span class="processing">Processing 1 out of <?= $max ?></span> ... </strong> <i class="fa fa-spinner fa-pulse fa-2x"></i><br /><br />DO NOT CLOSE THIS WINDOW UNTIL ALL STAFF HAS BEEN EMAILED.<br><br>You will be redirected to a success page once the process is complete.
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
                                        $('.round_msgbox').html("ERROR: Some emails below did not get sent.<br><br>Please contact us for assistance 1-877-239-3931 M-F 9-5 EST.<br><br>" + sent_emails.replace(/,/g, "")); 
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
                    break;
                default:
                    break;
            }
        } else {
            echo "Unauthorized!";
        }
    } else {
        echo "subscription ID does not belong to you";
    }
}
// Could not find the subscription ID
else {
    echo "Could not find the subscription ID";
}
?>

