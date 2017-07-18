
<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <span><a href="?part=administration&amp;subscription_id=<?= $_REQUEST['subscription_id']; ?>" onclick="load('load_administration')">Administration</a></span>    
    <?= CRUMB_SEPARATOR ?>
    <span><a href="?part=manage_quiz&amp;subscription_id=<?= $_REQUEST['subscription_id']; ?>" onclick="load('load_quiz')">Manage Quizzes</a></span>    
    <?= CRUMB_SEPARATOR ?>
    <span class="current">Update Quiz Questions</span>     
</div>
<h1 class="article_page_title">Update Quiz</h1>
<?php
if(current_user_can( "is_director" )){
    
?>
<?php
echo do_shortcode( '[eot_quiz_admin action="update_quiz_questions"]' );
?>
<?php 
}
else
        {
            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            return;
          }

?>