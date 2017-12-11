<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>         
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_STATISTICS ?>          
    <?= CRUMB_SEPARATOR ?>  
    <span class="current">Course Stats</span>     
</div>
<?php
// enqueue required javascripts
wp_enqueue_script('datatables-buttons', get_template_directory_uri() . '/js/dataTables.buttons.min.js', array('datatables-js'), '1.2.4', true);
wp_enqueue_script('buttons-flash', get_template_directory_uri() . '/js/buttons.flash.min.js', array(), '1.2.4', true);
wp_enqueue_script('jszip', get_template_directory_uri() . '/js/jszip.min.js', array(), '2.5.0', true);
wp_enqueue_script('vfs-fonts', get_template_directory_uri() . '/js/vfs_fonts.js', array(), '0.1.24', true);
wp_enqueue_script('buttons-html5', get_template_directory_uri() . '/js/buttons.html5.min.js', array(), '1.2.4', true);
wp_enqueue_script('buttons-print', get_template_directory_uri() . '/js/buttons.print.min.js', array(), '1.2.4', true);
// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess();
global $current_user;
$user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
// Check if the subscription ID is valid.
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0) 
{

    if (isset($true_subscription['status']) && $true_subscription['status']) 
    {
        if (current_user_can("is_director"))
        {
          
            if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] > 0)
            {
              $course_id = filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT);
              $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
              $suser_id = filter_var($_REQUEST['stats_user_id'], FILTER_SANITIZE_NUMBER_INT);
              $fullname = get_user_meta($suser_id, 'first_name', true)." ".get_user_meta($suser_id, 'last_name', true);
              
?>
                <div class="smoothness">
                                        <h1 class="article_page_title">Videos and Resources Viewed</h1>
                                        <p>
                                            See which videos, resources and links <b><?= $fullname ?></b> has viewed.
                                        </p>
                                        <h3>Video Views</h3>
                                        <p>
                                           Here is a table showing the number of times this user has watched each video. 
                                        </p>
                </div>                        
               
<?php                
                        $videos =  getResourcesInCourse($course_id, 'video');
                        $custom_videos = getResourcesInCourse($course_id, 'custom_video');
                        $all_videos = array_merge($videos, $custom_videos);
                        $track_videos = array_merge(getTrack($suser_id, 0, 'watch_video'),getTrack($suser_id, 0, 'watch_custom_video'));
                        //d($all_videos,$track_videos);
                        $watched_videos = array_column($track_videos,'video_id');
                        $views = array_count_values($watched_videos);
                        $videosTableObj = new stdClass();
                            $videosTableObj->rows = array();
                            $videosTableObj->headers = array(
                                'Video Title' => 'left',
                                'Views' => 'center'
                            );
                        // Creating rows for the table
                            foreach ($all_videos as $video) 
                            {

                                if(isset($video['type']) && $video['type']=='custom_video')
                                {
                                   $custom = 1;
                                }
                                else 
                                {
                                    $custom = 0;
                                }
                                $view_count = isset($views[$video['ID']]) ? $views[$video['ID']] : 0;//Number of video views
                                

                                $videosTableObj->rows[] = array(
                                    ' <span>' . stripslashes($video['name']) . '</span>',
                                    "<a href='?part=videostatsview&course_id=$course_id&custom=$custom&stats_user_id=$suser_id&user_id=$user_id&video_id=".$video['ID']."&subscription_id=$subscription_id'>".$view_count."</a>"
                                    );
                            }
                         CreateDataTable($videosTableObj,"100%",10,true,"Stats"); // Print the table in the page
            
?>
                         <h2>Document, Link, and Resource File Views</h2>
                         <p>
                             Here is a table showing the number of times this user has downloaded each resource.
                         </p>
<?php
                        $resources = getResourcesInCourse($course_id, 'doc');
                        $track_download = getTrack($user_id, 0, 'download_resource');
                        $downloaded_resources = array_column($track_download, 'resource_id');
                        $downloads = array_count_values($downloaded_resources);
                        //d($track_download);
                        $resourceTableObj = new stdClass();
                            $resourceTableObj->rows = array();
                            $resourceTableObj->headers = array(
                                'Name' => 'left',
                                'Downloads' => 'center'
                            );
                        // Creating rows for the table
                            foreach ($resources as $resource) 
                            {

                                
                                $download_count = isset($downloads[$resource['ID']]) ? $downloads[$resource['ID']] : 0;//Number of video views
                                

                                $resourceTableObj->rows[] = array(
                                    ' <span>' . stripslashes($resource['name']) . '</span>',
                                    "<a href='?part=resourcestatsview&user_id=$user_id&course_id=$course_id&resource_id=".$resource['ID']."&subscription_id=$subscription_id'>".$download_count."</a>"
                                    );
                            }
                         CreateDataTable($resourceTableObj,"100%",10,true,"Stats"); // Print the table in the page
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