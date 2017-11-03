<?php
    if(current_user_can("is_director"))
    {
        if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
        {
            // Variable declaration
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
            if(isset($_REQUEST['view']) && $_REQUEST['view'] == 1)
            {
?>
                <div class="breadcrumb">
                    <?= CRUMB_DASHBOARD ?>    
                    <?= CRUMB_SEPARATOR ?> 
                    <?= CRUMB_CORNER ?>    
                    <?= CRUMB_SEPARATOR ?>  
                    <?= CRUMB_DEVELOPMENT ?>     
                    <?= CRUMB_SEPARATOR ?>    
                    <span class="current"><?= __("Your Camp's Substance Use Policy", "EOT_LMS"); ?></span>     
                </div>
                <h1 class="article_page_title"><?= __("Design, Implementation & Enforcement", "EOT_LMS"); ?></h1>
                <p>
                    <?= __("This is a 4-part series on developing your substance use policy. Watch the videos in sequence and see how this can help your camp.", "EOT_LMS"); ?>
                </p>
                <h2><?= __("Introduction", "EOT_LMS"); ?></h2>
                <a href="/wp-content/uploads/2017/03/Notes_Substance.pdf" target="_blank"><?= __("Download the PDF Resource", "EOT_LMS"); ?></a>
                <video id="my-video-1-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-1.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        <?= __("To view this video please enable JavaScript, and consider upgrading to a web browser that", "EOT_LMS"); ?>
                        <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __("supports HTML5 video", "EOT_LMS"); ?></a>
                    </p>        
                </video>
                </br>
                <h2><?= __("Policy Design", "EOT_LMS"); ?></h2>
                <video id="my-video-2-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-2.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        <?= __("To view this video please enable JavaScript, and consider upgrading to a web browser that", "EOT_LMS"); ?>
                        <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __("supports HTML5 video", "EOT_LMS"); ?></a>
                    </p>        
                </video>
                </br>
                <h2><?= __("Policy Evaluation", "EOT_LMS"); ?></h2>
                <a href="/wp-content/uploads/2017/03/Notes_Substnc_PrsCns.pdf" target="_blank"><?= __("Download the PDF Resource", "EOT_LMS"); ?></a>
                <video id="my-video-3-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-3.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        <?= __("To view this video please enable JavaScript, and consider upgrading to a web browser that", "EOT_LMS"); ?>
                        <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __("supports HTML5 video", "EOT_LMS"); ?></a>
                    </p>        
                </video>
                </br>
                <h2><?= __("Policy Enforcement", "EOT_LMS"); ?></h2>
                <a href="/wp-content/uploads/2017/03/Notes_Substnc_Enforce.pdf" target="_blank"><?= __("Download the PDF Resource", "EOT_LMS"); ?></a>
                <video id="my-video-4-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-4.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        <?= __("To view this video please enable JavaScript, and consider upgrading to a web browser that", "EOT_LMS"); ?>
                        <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __("supports HTML5 video", "EOT_LMS"); ?></a>
                    </p>        
                </video>
                </br>
                </br>
                <a href="/features/" class="morebutton" target="_blank"><?= __("Learn More", "EOT_LMS"); ?></a>
                <a href="/register/" class="morebutton" target="_blank"><?= __("Subscribe", "EOT_LMS"); ?></a>
                <a href="/contact/" class="morebutton" target="_blank"><?= __("Contact Us", "EOT_LMS"); ?></a>
<?php
            }
            elseif(isset($_REQUEST['view']) && $_REQUEST['view'] == 2)
            {
?>
                <div class="breadcrumb">
                    <?= CRUMB_DASHBOARD ?>    
                    <?= CRUMB_SEPARATOR ?> 
                    <?= CRUMB_CORNER ?>    
                    <?= CRUMB_SEPARATOR ?>  
                    <?= CRUMB_DEVELOPMENT ?>     
                    <?= CRUMB_SEPARATOR ?>    
                    <span class="current"><?= __("Top 10 Tips for Training Terrific Staff", "EOT_LMS"); ?></span>     
                </div>
                <h1 class="article_page_title"><?= __("Top 10 Tips for Training Terrific Staff", "EOT_LMS"); ?></h1>
                <p>
                    <?= __("Utilize these tips to enhance your staff training and develop terrific staff!", "EOT_LMS"); ?><br>
                    <a href="/wp-content/uploads/2017/03/Notes_Training_Week.pdf" target="_blank"><?= __("Download the PDF Resource", "EOT_LMS"); ?></a>
                </p>
                <video id="my-video-1-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/10tips.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        <?= __("To view this video please enable JavaScript, and consider upgrading to a web browser that", "EOT_LMS"); ?>
                        <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __("supports HTML5 video", "EOT_LMS"); ?></a>
                    </p>        
                </video>
<?php
            }
            else
            {
?>
            	<div class="breadcrumb">
            		<?= CRUMB_DASHBOARD ?>    
            		<?= CRUMB_SEPARATOR ?> 
                    <?= CRUMB_CORNER ?>    
                    <?= CRUMB_SEPARATOR ?>    
                	<span class="current"><?= __("Directors' Professional Development", "EOT_LMS"); ?></span>     
            	</div>
                <h1 class="article_page_title"><?= __("Directors' Professional Development", "EOT_LMS"); ?></h1>
                <p>
                    <?= __("Enjoy these free online resources from Dr. Chris Thurber, from online video webinars to online articles.", "EOT_LMS"); ?>
                </p>
                <ul>
                    <li><?= __("View", "EOT_LMS"); ?> <a href="?part=directors_development&subscription_id=<?= $subscription_id ?>&view=1"><?= __("Your Camp's Substance Use Policy", "EOT_LMS"); ?></a> <?= __("Webinar", "EOT_LMS"); ?></li>
                    <li><?= __("View", "EOT_LMS"); ?> <a href="?part=directors_development&subscription_id=<?= $subscription_id ?>&view=2"><?= __("Top Ten Tips for Training Terrific Staff", "EOT_LMS"); ?></a> <?= __("Webinar", "EOT_LMS"); ?></li>
                    <li><?= __("Read", "EOT_LMS"); ?> <a href="http://www.campspirit.com/online-articles/"><?= __("Articles Published by Co-Founder Dr. Chris Thurber", "EOT_LMS"); ?> </a></li>
              </ul>
<?php
            }
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