<?php
    if(current_user_can("is_director"))
    {
        if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
        {
            // Variable declaration
            global $current_user;
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        ?>
        	<div class="breadcrumb">
        		<?= CRUMB_DASHBOARD ?>    
        		<?= CRUMB_SEPARATOR ?> 
                <?= CRUMB_CORNER ?>    
                <?= CRUMB_SEPARATOR ?>    
            	<span class="current"><?= __("Press Play Workshop Outlines", "EOT_LMS"); ?></span>     
        	</div>
            <h1 class="article_page_title"><?= __("Press Play Workshop Outlines", "EOT_LMS"); ?></h1>
            <p>
                <?= __("Eager to train your staff, but pressed for time? Look no further. We've started producing workshop outlines that combine your style, and our content with the professional flair of our awesome videos. EOT's own David Malter has debuted an exciting pair of ready-to-use outlines for your on-site staff training. Each Press Play outline contains exercises, timelines, discussion topics, and-of course-EOT videos. Simply follow the outline and you'll have a stellar 75 or 90 minute workshop. Download them by clicking below.", "EOT_LMS"); ?>
            </p>
            <ul>
                <li><a href="/wp-content/uploads/2017/03/PressPlay_Skillful_Discipline_5.30.15.pdf" target="_blank"><?= __("Skillful Discipline", "EOT_LMS"); ?></a></li>
                <li><a href="/wp-content/uploads/2017/03/PressPlay_Helping_Awkward_Children_5.30.15.pdf" target="_blank"><?= __("Helping Awkward Children Fit In", "EOT_LMS"); ?></a></li>
            </ul>
        <?php
        }
        else
        {
            echo __("subscription id not provided!", "EOT_LMS");
        }
    }
    else
    {
        echo __("You do not have the privilege to view this page.", "EOT_LMS");
    }
?>