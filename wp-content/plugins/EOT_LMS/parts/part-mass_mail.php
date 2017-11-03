<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>   
    <?= CRUMB_ADMINISTRATOR ?>   
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_EMAIL_YOUR_STAFF ?>  
    <?= CRUMB_SEPARATOR ?> 
    <span class="current"><?= __("Mail Staff", "EOT_LMS"); ?></span> 
</div>
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
            if (isset($_REQUEST['processing'])) 
            {
                var_dump("test");
                $processing = filter_var($_REQUEST['processing'], FILTER_SANITIZE_NUMBER_INT); //the number out of total users we are processing right now
                $max = filter_var($_REQUEST['max'], FILTER_SANITIZE_NUMBER_INT);     //total users being processed from this instance of spreadsheet upload
//                $processing_top = ($processing + PENDING_EMAILS_LIMIT - 1 > $max) ? $max : $processing + PENDING_EMAILS_LIMIT - 1;
                $admin_ajax_url = admin_url('admin-ajax.php');
 ?>
                <h1 class="article_page_title"><?= __("Emailing your staff", "EOT_LMS"); ?></h1>

                <div class="spreadsheet_processing round_msgbox">
                    <strong><?= __("Please wait while we send your emails:", "EOT_LMS"); ?> <br>
                        <span class="processing"><?= __("Processing", "EOT_LMS"); ?> <?= $processing ?> <?= __("out of", "EOT_LMS"); ?> <?= $max ?></span> ... </strong> <i class="fa fa-spinner fa-pulse fa-2x"></i><br /><br /><?= __("DO NOT CLOSE THIS WINDOW UNTIL ALL STAFF HAS BEEN EMAILED.", "EOT_LMS"); ?><br><br><?= __("You will be redirected to a success page once the process is complete.", "EOT_LMS"); ?>
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

                                    $('.processing').html("<?= __("Processing", "EOT_LMS"); ?> "+count+" <?= __("out of", "EOT_LMS"); ?> <?= $max ?>");

                                    // check if we finished sending
                                    if (count > <?= $max ?> && overall_status == 1)
                                    {
                                        $('.round_msgbox').html("<?= __("Messages Sent Successfully!", "EOT_LMS"); ?><br><br>" + sent_emails.replace(/,/g, "")); 
                                    }
                                    else if (count > <?= $max ?> && overall_status == 0)
                                    {
                                        $('.round_msgbox').html("<?= __("ERROR: Some emails below did not get sent.", "EOT_LMS"); ?><br><br><?= __("Please contact us for assistance 1-877-390-2267 M-F 9-5 EST.", "EOT_LMS"); ?><br><br><?= __("Error message is: ", "EOT_LMS"); ?>" + result.message + "<br><br>" + sent_emails.replace(/,/g, "")); 
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
