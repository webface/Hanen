<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>   
    <?= CRUMB_ADMINISTRATOR ?>   
    <?= CRUMB_SEPARATOR ?>   
    <span class="current">Staff Lounge</span> 
</div>
<h1 class="article_page_title" class="video_title">Staff Lounge</h1>
<?php
// verify this user has access to this portal/subscription/page/view
//$true_subscription = verifyUserAccess();


global $current_user;
$user_id = $current_user->ID; // Wordpress user ID
$org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user($user_id); // Organization ID
$subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID


if ((current_user_can("is_director")) || (current_user_can("is_student"))) 
{
    if (get_post_meta($org_id, 'org_forum_id', true)) 
    {
        echo do_shortcode('[bbp-single-forum id=' . get_post_meta($org_id, 'org_forum_id', true) . ']');
    } 
    else 
    {
        $forum = eot_bbp_create_initial_content();
        $forum_id = $forum['forum_id'];
        update_post_meta($org_id, 'org_forum_id', $forum_id);
        $new_role_forum_role = 'bbp_keymaster';
        bbp_set_user_role($user_id, $new_role_forum_role);
        echo do_shortcode('[bbp-single-forum id=' . $forum_id . ']');
    }
} 
else 
{
    echo "Unauthorized!";
}
?>
