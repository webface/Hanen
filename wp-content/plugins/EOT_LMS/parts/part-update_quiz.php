<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <?= CRUMB_ADMINISTRATOR ?>    
    <?= CRUMB_SEPARATOR ?>
    <?=  CRUMB_QUIZ ?> 
    <?= CRUMB_SEPARATOR ?>
    <span class="current">Update Quizzes</span>     
</div>
<h1 class="article_page_title">Update Quiz</h1>
<?php

    $true_subscription = verifyUserAccess();
    // Check if the subscription ID is valid.
    if ((isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")||current_user_can("is_sales_manager")) 
    {
        if ((isset($true_subscription['status']) && $true_subscription['status']) || current_user_can("is_sales_manager")) 
        {
            //d(current_user_can("is_sales_manager"));
            if (current_user_can("is_director") || current_user_can("is_sales_manager")) 
            {
                
                echo do_shortcode('[eot_quiz_admin action="update_quiz"]');
            } 
            else 
            {
                echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.","EOT_LMS");
            }
        }
        else 
        {
            echo __("subscription ID does not belong to you", "EOT_LMS");
        }
    } 
    else 
    {
        echo __("Could not find the subscription ID", "EOT_LMS");
    }
?>