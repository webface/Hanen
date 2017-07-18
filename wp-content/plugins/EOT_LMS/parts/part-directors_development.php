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
                    <span class="current">Your Camp's Substance Use Policy</span>     
                </div>
                <h1 class="article_page_title">Design, Implementation & Enforcement</h1>
                <p>
                    This is a 4-part series on developing your substance use policy. Watch the videos in sequence and see how this can help your camp.
                </p>
                <h2>Introduction</h2>
                <a href="/wp-content/uploads/2017/03/Notes_Substance.pdf" target="_blank">Download the PDF Resource</a>
                <video id="my-video-1-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-1.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>        
                </video>
                </br>
                <h2>Policy Design</h2>
                <video id="my-video-2-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-2.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>        
                </video>
                </br>
                <h2>Policy Evaluation</h2>
                <a href="/wp-content/uploads/2017/03/Notes_Substnc_PrsCns.pdf" target="_blank">Download the PDF Resource</a>
                <video id="my-video-3-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-3.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>        
                </video>
                </br>
                <h2>Policy Enforcement</h2>
                <a href="/wp-content/uploads/2017/03/Notes_Substnc_Enforce.pdf" target="_blank">Download the PDF Resource</a>
                <video id="my-video-4-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/substance-4.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>        
                </video>
                </br>
                </br>
                <a href="/features/" class="morebutton" target="_blank">Learn More</a>
                <a href="/register/" class="morebutton" target="_blank">Subscribe</a>
                <a href="/contact/" class="morebutton" target="_blank">Contact Us</a>
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
                    <span class="current">Top 10 Tips for Training Terrific Staff</span>     
                </div>
                <h1 class="article_page_title">Top 10 Tips for Training Terrific Staff</h1>
                <p>
                    Utilize these tips to enhance your staff training and develop terrific staff!<br>
                    <a href="/wp-content/uploads/2017/03/Notes_Training_Week.pdf" target="_blank">Download the PDF Resource</a>
                </p>
                <video id="my-video-1-big" class="video-js vjs-default-skin" preload="auto" width="650" height="365" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/10/Chris-thumbnail.png" data-setup='{"controls": true}'>
                    <source src="https://eot-output.s3.amazonaws.com/10tips.mp4" type='video/mp4'>
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
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
                	<span class="current">Directors' Professional Development</span>     
            	</div>
                <h1 class="article_page_title">Directors' Professional Development</h1>
                <p>
                    Enjoy these free online resources from Dr. Chris Thurber, from online video webinars to online articles.
                </p>
                <ul>
                    <li>View <a href="?part=directors_development&subscription_id=<?= $subscription_id ?>&view=1">Your Camp's Substance Use Policy</a> Webinar</li>
                    <li>View <a href="?part=directors_development&subscription_id=<?= $subscription_id ?>&view=2">Top Ten Tips for Training Terrific Staff</a> Webinar</li>
                    <li>Read <a href="http://www.campspirit.com/online-articles/">Articles Published by Co-Founder Dr. Chris Thurber </a></li>
              </ul>
<?php
            }
        }
        else
        {
            echo "subscription id not provided!";
        }
    }
    else
    {
        echo "You do not have the privilege to view this page.";
    }
?>