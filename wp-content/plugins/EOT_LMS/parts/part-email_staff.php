<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>   
    <?= CRUMB_ADMINISTRATOR ?>   
    <?= CRUMB_SEPARATOR ?>   
     <span class="current"><?= __("E-mail Your Staff", "EOT_LMS"); ?></span> 
</div>
<h1 class="article_page_title" class="video_title"><?= __("E-mail Your Staff", "EOT_LMS"); ?></h1>

<?php
    // verify this user has access to this portal/subscription/page/view
    $true_subscription = verifyUserAccess(); 

    if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
    {
        global $current_user;
        $user_id = $current_user->ID; // Wordpress user ID
        $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
        $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID

        if(isset($true_subscription['status']) && $true_subscription['status'])
        {
            if(current_user_can( "is_director" ))
            {

?>
                <p>
                    <?= __("Want to get in touch with all your staff? Just those in a particular course? Just the ones who are lagging? Simply click a link below to initiate an email to any subset of your staff.", "EOT_LMS"); ?>
                </p>
                <ul>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=all" onclick="load('load_email')">All staff enrolled in EOT</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>All:</b> Sends an email to all staff in your organization who have an EOT learner account.<br /><br /><b>NOTE:</b> If you have multiple subscriptions, this message will go to staff in both subscriptions.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li> 
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=all-course" onclick="load('load_email')">Staff in a Specific Course</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Select Course:</b> Sends an email to all staff from a specific course or multiple courses.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>                                                       
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=select-staff" onclick="load('load_email')">Individual Staff Members</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Select Staff:</b> Sends and email to individual staff members.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <br> 
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=incomplete" onclick="load('load_email')">Incomplete</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Incomplete:</b> Sends and email to staff who have not finished all the courses that they are enrolled in.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=completed" onclick="load('load_email')">Completed</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Completed:</b> Sends and email to staff who have finished all of the courses that they are enrolled in.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=nologin" onclick="load('load_email')">Yet to Login</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Yet to Login:</b> Sends and email to staff who still have not logged on to EOT.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <br>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=course-passwords" onclick="load('load_email')">Send Passwords to Staff in Specific Courses</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Select Courses:</b> Will let you select a specific courses to send those staff members their passwords, then you will be able to customize the message they recieve.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=staff-passwords" onclick="load('load_email')">Send Passwords to Specific Staff Members</a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b><?= __("Select Staff", "EOT_LMS"); ?>:</b> <?= __("Will let you select a specific staff members you want to recieve their passwords, then you will be able to customize the message they recieve", "EOT_LMS"); ?>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>      
                    <br>
              </ul>
 <?php
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
    else
    {
        echo __("Sorry but you have an invalid subscription. Please contact the site administrator.", "EOT_LMS");
    }
?>
