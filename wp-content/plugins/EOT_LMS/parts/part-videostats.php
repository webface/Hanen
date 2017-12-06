<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>         
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_STATISTICS ?>          
    <?= CRUMB_SEPARATOR ?>  
    <span class="current">Video Stats</span>     
</div>
<?php
// enqueue required javascripts
wp_enqueue_script('datatables-buttons', get_template_directory_uri() . '/js/dataTables.buttons.min.js', array('datatables-js'), '1.2.4', true);
wp_enqueue_script('buttons-flash', get_template_directory_uri() . '/js/buttons.flash.min.js', array(), '1.2.4', true);
wp_enqueue_script('jszip', get_template_directory_uri() . '/js/jszip.min.js', array(), '2.5.0', true);
wp_enqueue_script('vfs-fonts', get_template_directory_uri() . '/js/vfs_fonts.js', array(), '0.1.24', true);
wp_enqueue_script('buttons-html5', get_template_directory_uri() . '/js/buttons.html5.min.js', array(), '1.2.4', true);
wp_enqueue_script('buttons-print', get_template_directory_uri() . '/js/buttons.print.min.js', array(), '1.2.4', true);
global $current_user;
$user_id = $current_user->ID;
$page_title = "Stats";
$admin_ajax_url = admin_url('admin-ajax.php');
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
                //d($users,$video,$video_stats);
?>
                <div class="smoothness">
                                        <h1 class="article_page_title"><?= __('Video Statistics for' ,'EOT_LMS')?>"<?= $course_name ?>"</h1>
                                        <h2><?= $video['name'] ?></h2>
                                        <a class="btn btn-primary watch_video" onclick="watchVideo();" style="float:right"><?= __('Watch Video','EOT_LMS')?></a>
                                        <p>
                                            <?= __('Here is a table showing when these users have viewed ','EOT_LMS')?><b><?=$video['name']?></b>.<br />
                                            <?= __('Times are shown in ','EOT_LMS')?><b><?= __('Pacific Standard Time (PST)','EOT_LMS')?></b> <span class="small"><?= __('(GMT - 8).','EOT_LMS')?></span><br />
                                            <?= __('It is currently ','EOT_LMS')?><b><?=date('g:ia', time())?></b><?= __(' on ','EOT_LMS')?><b><?=date('F j, Y', time())?></b>.
                                        </p>
                                        
                </div>
<script>
function watchVideo() 
    {

        $.facebox(function () {

            $.ajax({
                data: {'video_id': <?= $video_id?>,'custom':<?= $custom?>},
                error: function () 
                {
                    $.facebox(<?= __('There was an error loading the title. Please try again shortly.', 'EOT_LMS')?>);
                },
                success: function (data)
                {
                    $.facebox(data);
                },
                type: 'post',
                url: '<?= $admin_ajax_url; ?>?action=get_video_form&form_name=watch_video'
            });

        });
    }
    </script>
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
                            CreateDataTable($usersTableObj,"100%",10,true,"Stats"); // Print the table in the page
                }
            else 
            {
                echo __("You dont have a valid course ID",'EOT_LMS');
            }
        } 
        else 
        {
            echo __("Unauthorized!",'EOT_LMS');
        }
    } 
    else 
    {
        echo __("subscription ID does not belong to you",'EOT_LMS');
    }
}
// Could not find the subscription ID
else
{
    echo __("Could not find the subscription ID",'EOT_LMS');
}
?>