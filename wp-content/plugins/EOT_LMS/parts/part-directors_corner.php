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
            	<span class="current"><?= __("Director's Corner", "EOT_LMS"); ?></span>     
        	</div>
            <div class="component-pad">
                <h1 class="article_page_title"><?= __("Director's Corner", "EOT_LMS"); ?></h1>
                <p>
                    <?= __("The", "EOT_LMS"); ?> <i><?= __("Director's Corner", "EOT_LMS"); ?></i> <?= __("is full of tips to help you make the most out of your subscription as well as other resources that you'll find helpful as a camp director.", "EOT_LMS"); ?>
                </p>
                <ul>
                    <li><a href="/topics-leadership-essentials/"><?= __("Topic Recommendations", "EOT_LMS"); ?></a></li>
                    <li><a href="/getting-the-most-out-of-le/"><?= __("Getting the Most from Your Subscription", "EOT_LMS"); ?></a></li>
                    <li><a href="?part=directors_development&subscription_id=<?= $subscription_id ?>"><?= __("Director's Professional Development", "EOT_LMS"); ?></a></li>
                    <li><a href="/badges/"><?= __("Expert Online Training Badges (Link to Us)", "EOT_LMS"); ?></a></li>
                    <li><a href="?part=bloopers&subscription_id=<?= $subscription_id ?>"><?= __("Check out our Blooper Reels", "EOT_LMS"); ?></a></li>
                    <!--<li><a href="/my-dashboard.html?view=certificatesinfo">About Certificate Courses</a></li>-->
                    <li><a href="?part=press_play&subscription_id=<?= $subscription_id ?>"><?= __("Press Play Workshop Outlines", "EOT_LMS"); ?></a></li>
                </ul>
        
                <div class="highlight_back">
                    <h3><i><font color="black"><?= __("Ready for some in-depth articles on cutting-edge topics?", "EOT_LMS"); ?> </font></i></h3>
                    <font color="black">
                                <?= __("Follow the links to your", "EOT_LMS"); ?> <b><?= __("FREE", "EOT_LMS"); ?></b> <?= __("subscriptions", "EOT_LMS"); ?> 
                                <?= __("to", "EOT_LMS"); ?> <a href="https://northstarpubs.typeform.com/to/AhDq9N"><?= __("Camp Business", "EOT_LMS"); ?></a>
                                <?= __("and", "EOT_LMS"); ?> <a href="https://northstarpubs.typeform.com/to/NQx1Lx"><?= __("Parks and Rec Business", "EOT_LMS"); ?></a>.<br>
                                <?= __("Your digital subscriptions include access to fantastic articles by EOT faculty members Dr. Chris Thurber, Dr. Zach Mural, Scott Arizala and others. Enjoy!", "EOT_LMS"); ?><br><br>
                                <div>
                                    <a class="aMagazine" style="margin-left:21%;" href="http://www.campbusiness.com/" target="_blank">
                                        <img style="box-shadow: 5px 5px 5px #591720;" src="/wp-content/uploads/2017/03/campBusiness.jpg" width="144" height="192" alt="Camp Business Magazine"></a>
                                    <a class="aMagazine" style=" margin:7%;" href="http://www.parksandrecbusiness.com/" target="_blank">
                                        <img style="box-shadow: 5px 5px 5px #02330E;" src="/wp-content/uploads/2017/03/prbMagazine.jpg" width="144" height="192" alt="Parks and Rec Business Magazine"></a>
                                </div>
                    </font>
                </div>
                <font color="black">
                    <br>
                    <p>
                        <?= __("At ExpertOnlineTraining.com, we have a global vision to elevate the relationship quality between young people and their professional adult caregivers. To do this, we seek to magnify our collective leadership strengths through innovative educational materials.  By coaching caregivers to reach their highest potential, we refract our greatest human strengths outward into our world.", "EOT_LMS"); ?>
                    </p>
                </font>
            </div>
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