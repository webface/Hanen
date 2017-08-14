<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>         
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_STATISTICS ?>          
    <?= CRUMB_SEPARATOR ?>  
    <span class="current">Video Stats</span>     
</div>
<?php
global $current_user;
$user_id = $current_user->ID;
$page_title = "Stats";
// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess();
// Check if the subscription ID is valid.
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0) 
{

    if (isset($true_subscription['status']) && $true_subscription['status']) 
    {
        if (current_user_can("is_director"))
        {
          
            if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] > 0)
            {
                $org_id = get_org_from_user ($user_id); // Organization ID
	 	$data = array( "org_id" => $org_id ); // to pass to our functions above
		$course_id = filter_var($course_id = $_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
		$course_data = getCourse($course_id); // The course information
		$course_name = $course_data['course_name'];
                $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
                $custom = filter_var($_REQUEST['custom'], FILTER_SANITIZE_NUMBER_INT);
                $video_id = filter_var($_REQUEST['video_id'], FILTER_SANITIZE_NUMBER_INT);// The video ID
                if($custom == 0)
                {
                $video = getVideoById($video_id,false);
                $video_stats = getVideoStats($video_id, $org_id,false);
                }
                else 
                {
                $video = getVideoById($video_id, true); 
                $video_stats = getVideoStats($video_id, $org_id, true);
                }
                $users = getEotUsers($org_id);
                $users = $users['users'];
                $user_ids = array_column($users, 'ID');
                $user_ids_string = implode(",", $user_ids);
                //$video_stats = getVideoStats($video_id, $org_id);
                d($users,$video,$video_stats);
?>
                <div class="smoothness">
                                        <h1 class="article_page_title">Video Statistics for "<?= $course_name ?>"</h1>
                                        <h2><?= $video['name'] ?></h2>
                                        <p>
                                            Here is a table showing when these users have viewed <b><?=$video['name']?></b>.<br />
                                            Times are shown in <b>Pacific Standard Time (PST)</b> <span class="small">(GMT - 8).</span><br />
                                            It is currently <b><?=date('g:ia', time())?></b> on <b><?=date('F j, Y', time())?></b>.
                                        </p>
                                        
                </div>
<?php
                // Tables that will be displayed in the front end.
                            $usersTableObj = new stdClass();
                            $usersTableObj->rows = array();
                            $usersTableObj->headers = array(
          '<div>Name</div>' => 'left',
          '<center><div>Views</div></center>' => 'center'
                            );
                            
                            foreach ($video_stats as $stat) 
                            {   
                               $custom = 0;
                               if($stat['type'] == 'watch_custom_video')
                               {
                                   $custom = 1;
                               }
                               $usersTableObj->rows[] = array(
                                    $stat['display_name'],
                                    "<a href='?part=videostatsview&course_id=$course_id&custom=$custom&video_id=".$video_id."&user_id=".$stat['user_id']."&subscription_id=$subscription_id'>1</a>"
                                    ); 
                                
                            }
                            CreateDataTable($usersTableObj); // Print the table in the page
                }
            else 
            {
                echo "You dont have a valid course ID";
            }
        } 
        else 
        {
            echo "Unauthorized!";
        }
    } 
    else 
    {
        echo "subscription ID does not belong to you";
    }
}
// Could not find the subscription ID
else
{
    echo "Could not find the subscription ID";
}
?>