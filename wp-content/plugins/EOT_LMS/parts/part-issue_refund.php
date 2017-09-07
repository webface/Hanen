<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>    
    <span class="current">Issue a Refund</span>     
</div>

<?php
$admin_ajax_url = admin_url('admin-ajax.php');
echo '<h1 class="article_page_title">Issue a Refund</h1>';
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "") 
{
    if (isset($_REQUEST['library_id']) && $_REQUEST['library_id'] != "") 
    {
        if (current_user_can("is_sales_rep") || current_user_can("is_sales_manager")) 
        {
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
            $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);
            $subscription = getSubscriptions($subscription_id); // get the subscription info for this subscription
            if (!isset($subscription->ID)) // make sure that there is a subscription
            {
                    wp_die("I couldn't find that subscription ID");
            }
            $subscriptionsTableObj = new stdClass();
            $subscriptionsTableObj->rows = array();
            $subscriptionsTableObj->headers = array(
			'Camp Name' => 'center',
			'Director Name' => 'center',
			'<div ' . hover_text_attr('The amount paid for this subscription.',true) .'>Amount Paid</div>' => 'center',
			'<div ' . hover_text_attr('Refund all or part of this amount',true) .'>Actions</div>' => 'center'
		);
            $org_id = $subscription->org_id;
            $camp_name = get_the_title($org_id);
            $user_id = $subscription->manager_id; // Director's User ID
            $name = get_user_meta ( $user_id, 'first_name', true ) . " " . get_user_meta ( $user_id, 'last_name', true ); // Director's Name
            $amount = $subscription->price;
            $subscriptionsTableObj->rows[]=array(
                $camp_name,
                $name,
                $amount,
                "<a onclick='issueRefund(\"".$subscription->trans_id."\",\"subscription\")'><i class='fa fa-money' aria-hidden='true' ". hover_text_attr('Issue Refund.',true) . "></i></a>"
            );
            echo "<h1>Subscriptions</h1>";
            CreateDataTable($subscriptionsTableObj);
            
            echo "<h1>Upgrades</h1>";
            $upgrades = getUpgrades ($subscription_id);
            //d($upgrades);
            $upgradesTableObj = new stdClass();
            $upgradesTableObj->rows = array();
            $upgradesTableObj->headers = array(
			'Camp Name' => 'center',
			'Type of upgrade' => 'center',
			'<div ' . hover_text_attr('The amount paid for this upgrade.',true) .'>Amount Paid</div>' => 'center',
			'<div ' . hover_text_attr('Refund all or part of this amount',true) .'>Actions</div>' => 'center'
		);
            foreach ($upgrades as $upgrade) {
                $upgradesTableObj->rows[] = array(
                    $camp_name,
                    $upgrade->other_note,
                    $upgrade->price,
                    "<a onclick='issueRefund(\"".$upgrade->trans_id."\",\"upgrade\")'><i class='fa fa-money' aria-hidden='true' ". hover_text_attr('Issue Refund.',true) . "></i></a>"
                );
            }
            CreateDataTable($upgradesTableObj);
            ?>
<script>

    function issueRefund(trans_id,type) 
    {
        //alert('issue refund: '+trans_id);
        //alert('issue refund: '+type);
        $.facebox(function () {

            $.ajax({
                data: {'trans_id': trans_id,'type':type},
                error: function () 
                {
                    $.facebox('There was an error loading the title. Please try again shortly.');
                },
                success: function (data)
                {
                    $.facebox(data);
                },
                type: 'post',
                url: '<?= $admin_ajax_url; ?>?action=get_refund_form'
            });

        });
                $(document).bind('success.refund_camp',
                function (event, data)
                {
                    if(data.success===true)
                    {
                        alert('Refunded');
                    }
                }
       )
    }</script>
<?php
        } 
        else 
        {
            wp_die('You do not have privilege to view this page.');
        }
    } 
    else 
    {
        wp_die('Invalid library id.');
    }
} 
else 
{
    wp_die('Invalid subscription id');
}
?>
