<?php
/**
 * Template Name: Renewals
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();
global $wpdb;
$id =isset($_REQUEST['id'])? filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT):0;
$key = isset($_REQUEST['key'])?filter_var($_REQUEST['key'], FILTER_SANITIZE_STRING):'';
if($id == 0 || $key =="")
{
    die("Invalid Credentials");
}
$user_data = get_user_by('ID', $id);
$email = $user_data->user_email;
d($user_data);
$subscriptions = $wpdb->get_results("SELECT * FROM ". TABLE_SUBSCRIPTIONS . " WHERE manager_id = $id",OBJECT_K);
d($subscriptions);
foreach($subscriptions as $subscription)
{
    //d(($email.$subscription->ID));
    if(wp_hash($email.$subscription->ID)== $key)
    {
        $the_subscription = $subscription;
    }
}
if(isset($the_subscription))
{
    ?>
<div class="col-md-9 content-area" id="main-column">
    <main id="main" class="site-main" role="main">
        <h1 class="article-title">Renew Subscription</h1>
        
    </main>
</div>
<div style="clear:both"></div>

<?php
    
}
 else
{
?>
<div class="bs">
    <div class="well well-sm">Sorry we couldn't find your subscription. Please contact the Administrator.</div>
</div>
<?php
}
?>
<?php get_sidebar('left'); ?>

<?php get_sidebar('right'); ?> 
<?php get_footer(); ?> 