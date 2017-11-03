<?php
    if(current_user_can("is_director"))
    {
        if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] !== "")
        {
            // Variable declaration
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
?>
        	<div class="breadcrumb">
        		<?= CRUMB_DASHBOARD ?>    
        		<?= CRUMB_SEPARATOR ?> 
                <?= CRUMB_CORNER ?>    
                <?= CRUMB_SEPARATOR ?>    
            	<span class="current"><?= __("Bloopers", "EOT_LMS"); ?></span>     
        	</div>
            <h1 class="article_page_title"><?= __("Bloopers", "EOT_LMS"); ?></h1>
            <p>
                <style type="text/css">
                    iframe.bloopers
                    {
                        height: 360px;
                        width: 640px;    
                    }
                    

                </style>
                <iframe class="bloopers" width="640" height="360" src="https://www.youtube.com/embed/mo43kZftRjM" frameborder="0" allowfullscreen></iframe>
            </p>
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