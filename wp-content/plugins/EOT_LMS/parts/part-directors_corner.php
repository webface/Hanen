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
            	<span class="current">Director's Corner</span>     
        	</div>
            <div class="component-pad">
                <h1 class="article_page_title">Director's Corner</h1>
                <p>
                    The <i>Director's Corner</i> is full of tips to help you make the most out of your subscription as well as other resources that you'll find helpful as a camp director.
                </p>
                <ul>
                    <li><a href="/topics-leadership-essentials/">Topic Recommendations</a></li>
                    <li><a href="/getting-the-most-out-of-le/">Getting the Most from Your Subscription</a></li>
                    <li><a href="?part=directors_development&subscription_id=<?= $subscription_id ?>">Director's Professional Development</a></li>
                    <li><a href="/badges/">Expert Online Training Badges (Link to Us)</a></li>
                    <li><a href="?part=bloopers&subscription_id=<?= $subscription_id ?>">Check out our Blooper Reels</a></li>
                    <!--<li><a href="/my-dashboard.html?view=certificatesinfo">About Certificate Courses</a></li>-->
                    <li><a href="?part=press_play&subscription_id=<?= $subscription_id ?>">Press Play Workshop Outlines</a></li>
                </ul>
        
                <div class="highlight_back">
                    <h3><i><font color="black">Ready for some in-depth articles on cutting-edge topics? </font></i></h3>
                    <font color="black">
                                Follow the links to your <b>FREE</b> subscriptions 
                                to <a href="https://northstarpubs.typeform.com/to/AhDq9N">Camp Business</a>
                                and <a href="https://northstarpubs.typeform.com/to/NQx1Lx">Parks and Rec Business</a>.<br>
                                Your digital subscriptions include access to fantastic articles by EOT faculty 
                                members Dr. Chris Thurber, Dr. Zach Mural, Scott Arizala and others. Enjoy!<br><br>
                                <div>
                                    <a class="aMagazine" style="float; margin-left:21%;" href="http://www.campbusiness.com/" target="_blank">
                                        <img style="box-shadow: 5px 5px 5px #591720;" src="/wp-content/uploads/2017/03/campBusiness.jpg" width="144" height="192" alt="Camp Business Magazine"></a>
                                    <a class="aMagazine" style=" margin:7%;" href="http://www.parksandrecbusiness.com/" target="_blank">
                                        <img style="box-shadow: 5px 5px 5px #02330E;" src="/wp-content/uploads/2017/03/prbMagazine.jpg" width="144" height="192" alt="Parks and Rec Business Magazine"></a>
                                </div>
                    </font>
                </div>
                <font color="black">
                    <br>
                    <p>
                        At ExpertOnlineTraining.com, we have a global vision to elevate the relationship quality between young people and their professional adult caregivers. To do this, we seek to magnify our collective leadership strengths through innovative educational materials.  By coaching caregivers to reach their highest potential, we refract our greatest human strengths outward into our world.
                    </p>
                </font>
            </div>
        <?php
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