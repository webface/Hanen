<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <span class="current">Quizzes</span>     
</div>
<h1 class="article_page_title">Take the quiz</h1>
<!--DEFINE QUIZ ID-->
<script>quiz_id=1;</script>
<?php
//if(current_user_can( "is_student" )){
    
?>
<?php
//load quiz
echo do_shortcode( '[eot_quiz_display action="quiz"]' );
?>
<?php 
//}
//else
//        {
//            echo "ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.";
//            return;
//          }

?>
