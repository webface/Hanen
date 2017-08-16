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
                $video_id = filter_var($_REQUEST['video_id'], FILTER_SANITIZE_NUMBER_INT);// The video ID
                $user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);// The user ID
                $fullname = get_user_meta($user_id, 'first_name', true)." ".get_user_meta($user_id, 'last_name', true);
                $custom = filter_var($_REQUEST['custom'], FILTER_SANITIZE_NUMBER_INT);
                if($custom == 0)
                {
                $video = getVideoById($video_id,false);
                $stats= getTrack($user_id, $video_id,'watch_video');
                }
                else 
                {
                $video = getVideoById($video_id, true);
                $stats= getTrack($user_id, $video_id, 'watch_custom_video');
                }
                
               //d($stats,$video_id,$video);
?>
                <div class="smoothness">
                                        <h1 class="article_page_title">Video Viewing Record for "<?= $fullname ?>"</h1>
                                        <h2><?= $video['name'] ?></h2>
                                        <a class="btn btn-primary watch_video" onclick="watchVideo();" style="float:right">Watch Video</a>
                                        <p>
                                            Here is a table showing when these users have viewed <b><?=$video['name']?></b>.<br />
                                            Times are shown in <b>Pacific Standard Time (PST)</b> <span class="small">(GMT - 8).</span><br />
                                            It is currently <b><?=date('g:ia', time())?></b> on <b><?=date('F j, Y', time())?></b>.
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
                    $.facebox('There was an error loading the title. Please try again shortly.');
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
          '<div>No</div>' => 'left',
          '<center><div>Views</div></center>' => 'left'
                            );

                               $usersTableObj->rows[] = array(
                                    1,
                                    date('F j, Y g:i a', strtotime($stats['date']))
                                    ); 
                                

                            CreateDataTable($usersTableObj,"100%",10,true,"Stats"); // Print the table in the page
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