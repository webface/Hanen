<?php
/**
 * Template Name: View Invoice
 */
if( isset($_REQUEST['type']) &&  (isset($_REQUEST['id']) || isset($_REQUEST['subscription_id'])) )
{
    // Only director can see invoice.
    if( current_user_can ('is_director') )
    {
        $subscription_id = isset( $_REQUEST['subscription_id'] ) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : 0; // Subscription ID
        $upgrade_id = isset( $_REQUEST['id'] ) ? filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT) : 0; // Upgrade ID
        $type = filter_var($_REQUEST['type'], FILTER_SANITIZE_STRING); // Invoice type: subscription or upgrade
        if($type == "upgrade" || $type == "subscription")
        {
            $upgrade = ($type == "upgrade" && $upgrade_id > 0) ? getUpgrades(0,0,0,$upgrade_id) : null;
            $subscription = ($type == "subscription") ? getSubscriptions($subscription_id) : null;
            if($upgrade)
            {
                $order_info['id'] = $upgrade_id; // Transaction date
                $order_info['trans_date'] = $upgrade->date; // Transaction date
                $order_info['price'] = $upgrade->price; // Upgrade Price
                $order_info['accounts'] = $upgrade->accounts; // Upgrade number accounts
                $order_info['user_id'] = $upgrade->user_id; // WP User ID
                $order_info['method'] = $upgrade->method; // Method of payment
                $order_info['rep_id'] = $upgrade->rep_id; // The REP ID
                $order_info['trans_id'] = $upgrade->trans_id; // Transaction ID
            }
            else if($subscription)
            {
                $order_info['id'] = $subscription_id; // Transaction date
                $order_info['user_id'] = $subscription->manager_id;
                $order_info['method'] = $subscription->method; // Method of payment
                $order_info['trans_date'] = $subscription->trans_date; // Trans date.
                $order_info['price'] = $subscription->price; // Overall Price
                $order_info['trans_id'] = $subscription->trans_id; // Transaction ID
            }
            if( $subscription || $upgrade ) 
            {
                global $current_user;
                $user_id = $current_user->ID; // Wordpress user ID
                $org_id = get_org_from_user ($user_id);
                $first_name = get_user_meta($user_id, "first_name", true);  // First name
                $last_name  = get_user_meta($user_id, "last_name", true);   // Last name
                $address = get_post_meta ($org_id, 'org_address', true);
                $city = get_post_meta ($org_id, 'org_city', true);
                $state = get_post_meta ($org_id, 'org_state', true);
                $country = get_post_meta ($org_id, 'org_country', true);
                $zip = get_post_meta ($org_id, 'org_zip', true);
                $full_name = $first_name . " " . $last_name;    // Full name of user in wordpress db wp_users
                if( $order_info['user_id'] == $user_id ) // Verify the user access to the subscription
                {
?>
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="utf-8" />
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <title>Expert Online Training - EOT<?= ucfirst($type) ?>#<?= $order_info['id'] ?></title>
                        <link href="<?php echo get_template_directory_uri(); ?>/css/target.css" rel="stylesheet">
                        <link href="<?php echo get_template_directory_uri(); ?>/css/bootstrap.css" rel="stylesheet">
                    </head>
                    <body>
                        <div class="container-fluid invoice-container">
                            <div class="row">
                                <div class="col-xs-7">
                                    <p>
                                        <img src="<?= get_template_directory_uri() . '/images/EOT_logo_2015_rectangle.png' ?>" height="" width="327" title="FranTech Solutions" id="banner" />
                                    </p>
                                    <h3>EOT<?= ucfirst($type) ?>#<?=$order_info['id']?></h3>
                                </div>
                                <div class="col-xs-5 text-center">
                                    <div class="invoice-status">
                                        <span class="paid">Paid</span>
                                    </div>   
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-6 pull-sm-right text-right-sm">
                                    <strong>Pay To:</strong>
                                    <address class="small-text">
                                        Dr. Chris Turber<br />
                                        3635 Craigmillar Ave.<br />
                                        Victoria, BC<br />
                                        V8P 3H2<br />
                                        Canada
                                    </address>
                                </div>
                                <div class="col-xs-6" id="invoice_to_container" style="text-align: right;">
                                    <strong>Invoiced To:</strong>
                                    <address class="small-text">
                                        <?= $full_name ?><br />
                                        <?= $address ?><br />
                                        <?= $city ?>, <?= $state ?>, <?= $zip ?><br />
                                        <?= $country ?>
                                    </address>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <strong>Payment Method:</strong>
                                    <br>
                                    <span class="small-text">
                                        <?= ucfirst($order_info['method']) ?>
                                    </span>
                                    <br />
                                    <br />
                                </div>
                                <div class="col-xs-6 text-right-sm" style="text-align: right;">
                                    <strong>Transaction Date:</strong>
                                    <address class="small-text">
                                        <?=str_replace('-', '/', $order_info['trans_date']) ?>
                                    </address>
                                </div>
                            </div>
                            <br />
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <strong>Invoice Items</strong>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-condensed">
                                            <thead>
                                                <tr>
                                                    <td>
                                                        <strong>Description</strong>
                                                    </td>
                                                    <td width="20%" class="text-center">
                                                        <strong>Amount</strong>
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <?php
                                                    if($upgrade)
                                                    {
                                                        echo "<td>";
                                                        echo    "Additional staff: " . $order_info['accounts'] . "x";
                                                        echo "<br />";
                                                        echo "</td>";
                                                        echo '<td class="text-center">';
                                                        echo    "$" . $order_info['price'] . " USD";
                                                        echo "</td>";
                                                    }
                                                    else if($subscription)
                                                    {
                                                        echo "<td>";
                                                        echo    "Dashboard: (" . str_replace('-', '/', $subscription->start_date) . " - " . str_replace('-', '/', $subscription->end_date) . ")";
                                                        echo "<br />";
                                                        if( $subscription->staff_credits > 0 )
                                                        {
                                                            echo "Number of staff: " . $subscription->staff_credits . "x";
                                                            echo "<br />";
                                                        }
                                                        if( $subscription->data_disk_price > 0)
                                                        {
                                                            echo "Data Disc: Included";
                                                            echo "<br />";
                                                        }
                                                        if( $subscription->dash_discount > 0 )
                                                        {
                                                            echo "Dashboard discount";
                                                            echo "<br />";
                                                        }
                                                        if( $subscription->staff_discount > 0)
                                                        {
                                                            echo "Staff discount";
                                                            echo "<br />";
                                                        }
                                                        echo "</td>";
                                                        echo '<td class="text-center">';
                                                        echo "$" . $subscription->dash_price . " USD";
                                                        if( $subscription->staff_credits > 0 )
                                                        {
                                                            echo "<br />";
                                                            echo "$" . $subscription->staff_price . " USD";
                                                        }
                                                        if( $subscription->data_disk_price > 0 )
                                                        {
                                                            echo "<br />";
                                                            echo "$" . $subscription->data_disk_price . " USD";
                                                        }
                                                       if( $subscription->dash_discount > 0 )
                                                        {
                                                            echo "<br />";
                                                            echo "-$" . $subscription->data_disk_price . " USD";
                                                        }
                                                        if( $subscription->staff_discount > 0)
                                                        {
                                                            echo "<br />";
                                                            echo "-$" . $subscription->staff_discount . " USD";
                                                        }
                                                        echo "</td>";
                                                    } 
                                                ?>                                                 
                                                </tr>
                                                <tr>
                                                    <td class="total-row text-right">
                                                        <strong>Total</strong>
                                                    </td>
                                                    <td class="total-row text-center">$<?= $order_info['price'] ?> USD</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>   
                            <div class="transactions-container small-text">
                                <div class="table-responsive">
                                    <table class="table table-condensed">
                                        <thead>
                                            <tr>
                                                <td class="text-center">
                                                    <strong>Transaction Date</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>Gateway</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>Transaction ID</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>Amount</strong>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center"><?=str_replace('-', '/', $order_info['trans_date']) ?></td>
                                                <td class="text-center"><?= ucfirst($order_info['method']) ?></td>
                                                <td class="text-center"><?= $order_info['trans_id'] ?></td>
                                                <td class="text-center">$<?= $order_info['price'] ?> USD</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="pull-right btn-group btn-group-sm hidden-print">
                                <a href="javascript:window.print()" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
                            </div>
                        </div>
                        <p class="text-center hidden-print"><a href="../dashboard/?part=view_invoice">&laquo; Back to Invoice Page</a></a></p>

                    </body>
                    </html>
<?php
                }
                else
                {
                    // The Subscription has a different WP ID.
                    wp_die("ERROR: This " . ucfirst($type) . " does not belong to you.");
                }
            }
            else
            {   // Unable to find subscription or upgrade.
                wp_die('ERROR: Could not find the subscription or upgrade.');
            }
        }
        else
        {   // Invalid type.
            wp_die('ERROR: Invalid type.');
        }
    }
    else
    {
        // Not a director?
        wp_die('ERROR: You do not have permisison to view this page.');
    }
}
else
{
    // Invalid request. No request for user ID or Course ID 
    wp_die('ERROR: invalid type and id');
}

?>