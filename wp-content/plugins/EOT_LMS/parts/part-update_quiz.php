
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
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "") {
    if (isset($true_subscription['status']) && $true_subscription['status']) {
        if (current_user_can("is_director")) {
            ?>
            <?php
            echo do_shortcode('[eot_quiz_admin action="update_quiz"]');
            ?>
            <?php
        } else {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            return;
        }
    } else {
        echo "subscription ID does not belong to you";
        return;
    }
} else {
    echo "Could not find the subscription ID";
    return;
}
?>