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
                $resource_id = filter_var($_REQUEST['resource_id'], FILTER_SANITIZE_NUMBER_INT);// The video ID
                $resource = getResourceById($resource_id);
                //$users = getEotUsers($org_id);
                //$users = $users['users'];
                //$user_ids = array_column($users, 'ID');
                //$user_ids_string = implode(",", $user_ids);
                $resource_stats = getResourceStats($resource_id, $org_id);
                $video_stats = array();
                d($resource,$resource_stats);
?>
                <div class="smoothness">
                                        <h1 class="article_page_title">Resource Viewing Record for "<?= $course_name ?>"</h1>
                                        <h2><?= $resource['name'] ?></h2>
                                        <p>
                                            Here is a table showing when these users have viewed <b><?=$resource['name']?></b>.<br />
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
                            
                            foreach ($resource_stats as $stat) 
                            {
                               $usersTableObj->rows[] = array(
                                    $stat['display_name'],
                                    "<a href='?part=resourcestatsview&course_id=$course_id&resource_id=".$stat['resource_id']."&user_id=".$stat['user_id']."&subscription_id=$subscription_id'>1</a>"
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