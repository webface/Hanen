<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <?= CRUMB_ADMINISTRATOR ?>    
    <?= CRUMB_SEPARATOR ?>    
    <span class="current">Manage EOT Quizzes</span>     
</div>
<h1 class="article_page_title">Manage EOT Quizzes</h1>
<?php

            if (current_user_can("is_sales_manager")) 
            {
                echo do_shortcode('[eot_quiz_admin action="manage_eot_quiz"]');
            } 
            else 
            {
                echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
            }
?>