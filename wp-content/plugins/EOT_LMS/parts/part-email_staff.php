<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>   
    <?= CRUMB_ADMINISTRATOR ?>   
    <?= CRUMB_SEPARATOR ?>   
     <span class="current"><?= __("E-mail Your Staff", "EOT_LMS"); ?></span> 
</div>
<h1 class="article_page_title" class="video_title"><?= __("E-mail Your Staff", "EOT_LMS"); ?></h1>

<?php
global $current_user;
if ($current_user->ID == 348 || $current_user->ID == 18)
{
}
else
{
    // temporary error message
    echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">';
    echo '
        <div class="alert alert-danger" role="alert">
            Notice: 2018-03-05 Please excuse this interuption to our service but we are experiencing technical difficulties with the mass mail option. It will be fixed shortly. <br><br>Please try again in a couple of hours.<br><br>If you would like to contact use you may call us M-F 9-5 at 1-877-390-2267 or email: <a href="mailto:info@expertonlinetraining.com">info@expertonlinetraining.com</a>
        </div>
    ';
    return;
}

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
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=all" onclick="load('load_email')"><?= __('All staff enrolled in EOT', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>All:</b> Sends an email to all staff in your organization who have an EOT learner account.<br /><br /><b>NOTE:</b> If you have multiple subscriptions, this message will go to staff in both subscriptions.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li> 
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=all-course" onclick="load('load_email')"><?= __('Staff in a Specific Course', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Select Course:</b> Sends an email to all staff from a specific course or multiple courses.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>                                                       
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=select-staff" onclick="load('load_email')"><?= __('Individual Staff Members', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Select Staff:</b> Sends and email to individual staff members.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <br> 
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=incomplete" onclick="load('load_email')"><?= __('Incomplete', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Incomplete:</b> Sends and email to staff who have not finished all the courses that they are enrolled in.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=completed" onclick="load('load_email')"><?= __('Completed', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Completed:</b> Sends and email to staff who have finished all of the courses that they are enrolled in.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=nologin" onclick="load('load_email')"><?= __('Yet to Login', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Yet to Login:</b> Sends and email to staff who still have not logged on to EOT.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <br>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=course-passwords" onclick="load('load_email')"><?= __('Send Passwords to Staff in Specific Courses', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b>Select Courses:</b> Will let you select a specific courses to send those staff members their passwords, then you will be able to customize the message they recieve.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
                    </li>
                    <li>
                        <a href="?part=improved_email_staff&subscription_id=<?= $subscription_id ?>&target=staff-passwords" onclick="load('load_email')"><?= __('Send Passwords to Specific Staff Members', 'EOT_LMS')?></a> <i class="fa fa-question-circle fa-lg tooltip" aria-hidden="true" title="" style="margin-bottom: -2px" onmouseover="Tip('<b><?= __("Select Staff", "EOT_LMS"); ?>:</b> <?= __("Will let you select a specific staff members you want to recieve their passwords, then you will be able to customize the message they recieve", "EOT_LMS"); ?>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"></i>
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
