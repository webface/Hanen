<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <span class="current">View Subscriptions</span>     
</div>
<h1 class="article_page_title">View Subscriptions</h1>
<?php 
  if( isset($_REQUEST['status']) && $_REQUEST['status'] == 'upgradeSubscription' )
  {
?>
  <div class="msgboxcontainer ">  
    <div class="msg-tl">
      <div class="msg-tr"> 
        <div class="msg-bl">
          <div class="msg-br">
            <div class="msgbox"><h2>Subscription Upgraded Successfully!</h2>You have succesfully upgraded the account.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
  }
  if( isset($_REQUEST['status']) && $_REQUEST['status'] == 'chargeUser' )
  {
?>
  <div class="msgboxcontainer ">  
    <div class="msg-tl">
      <div class="msg-tr"> 
        <div class="msg-bl">
          <div class="msg-br">
            <div class="msgbox"><h2>Payment Successful!</h2>You have succesfully charged the user.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
  }	// check user's role for permission to access this display
	if (!current_user_can('is_sales_rep') && !current_user_can('is_sales_manager'))
	{
		wp_die('You do not have access to this display.');
	}

  	if( isset($_REQUEST['library_id']) && $_REQUEST['library_id'] > 0 )
  	{
		$library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);
		$library = getLibraries($library_id); // The library information
		$library_name = $library->name; // Name of the Library.
		$subscriptions = getSubscriptions(0, $library_id); // All the subscriptions in this library
		$total_amount_paid = 0; // Total amount paid for the subscription
		$total_num_staff = 0; // Total # of staff for the subscription
		// Initialize the table and create the headers
		$subscriptionsTableObj = new stdClass();
		$subscriptionsTableObj->rows = array();
        $subscriptionsTableObj->headers = array(
			'Camp Name' => 'center',
			'Director Name' => 'center',
			'<div ' . hover_text_attr('The total number of staff in this camp.',true) .'>Number of Staff</div>' => 'center',
			'<div ' . hover_text_attr('The total amount paid in this camp.',true) .'>Amount Paid</div>' => 'center',
			'<div ' . hover_text_attr('See more information about this camp or upgrade and disable this camp.',true) .'>Actions</div>' => 'center'
		);

		// Tables for download. This will not be displayed on the front end.
		$subscriptionsTableDownloadObj = new stdClass();
		$subscriptionsTableDownloadObj->rows = array();
	  	$subscriptionsTableDownloadObj->headers = array(
			'Camp Name' => 'center',
			'Director Name' => 'center',
			'<div ' . hover_text_attr('The total number of staff in this camp.',true) .'>Number of Staff</div>' => 'center',
			'<div ' . hover_text_attr('The total amount paid in this camp.',true) .'>Amount Paid</div>' => 'center'
		);

		// This go through all subscriptions, and add the data into the table row.
                //d($subscriptions);
		foreach($subscriptions as $subscription)
		{
			$user_id = $subscription->manager_id; // Director's User ID
			$user_info = get_userdata($user_id); // Director's User INFO
			$org_id = $subscription->org_id; // Subscription org ID 
			$subscription_id = $subscription->ID; // The subscription ID
			$camp_name = get_the_title($org_id); // The name of the camp
			$num_staff = $subscription->staff_credits; // # of staff in the subscription
			$price = $subscription->price; // The price sold for the subscription
			$upgrades = getUpgrades ($subscription_id);
			// Add upgrade costs, and upgrade number of staff
                        //d($upgrades);
			if($upgrades)
			{
				foreach($upgrades as $upgrade)
				{
					$num_staff += $upgrade->accounts;
					$price += $upgrade->price;
				}
			}
			$name = get_user_meta ( $user_id, 'first_name', true ) . " " . get_user_meta ( $user_id, 'last_name', true ); // Director's Name
			$total_amount_paid += $price;
			$total_num_staff += $num_staff;
			// Create thata rows, and add them into the table.
			$subscriptionsTableObj->rows[] = array($camp_name, 
													$user_info ? "<a href=mailto:" . $user_info->user_email . ">" . $name . "</a>" : "Can't find the user", 
													$num_staff, 
													" $" . number_format($price, 2, ".", ""),
													"<a href='./?part=admin_subscription_details&library_id=".$library_id."&subscription_id=".$subscription_id."'><i class='fa fa-info' aria-hidden='true' ". hover_text_attr('More information<br> for this camp.',true) . "></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='./?part=admin_create_account&user_id=".$user_id."&renewal=true'><i class='fa fa-vcard-o' aria-hidden='true' ". hover_text_attr('Renew<br> this camp.',true) . "></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='./?part=charge_user&library_id=".$library_id."&subscription_id=".$subscription_id."'><i class='fa fa-cart-plus' aria-hidden='true' ". hover_text_attr('Charge this camp.',true) . "></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='./?part=add_upgrade&subscription_id=".$subscription_id."&library_id=".$library_id."'><i class='fa fa-usd' aria-hidden='true' ". hover_text_attr('Upgrade.',true) . "></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa fa-times-circle' aria-hidden='true' ". hover_text_attr('Disable.',true) . "></i>&nbsp;&nbsp;&nbsp;&nbsp;<a href='./?part=issue_refund&library_id=".$library_id."&subscription_id=".$subscription_id."'><i class='fa fa-money' aria-hidden='true' ". hover_text_attr('Issue Refund.',true) . "></i></a>");
			$subscriptionsTableDownloadObj->rows[] = array($camp_name, 
										$user_info ? "<a href=mailto:" . $user_info->user_email . ">" . $name . "</a>" : "Can't find the user", 
										$num_staff, 
										" $" . number_format($price, 2, ".", ""));
		}
?>
  		<h2><?= $library_name ?></h2>
<table class="data">
	<tbody>
		<tr class="head">
			<td>
			  Totals
			</td>
			<td class="right">
			  Total # of Staff
			</td>
			<td class="right">
			  Total Payments
			</td>
		</tr>
		<tr>
			<td class="label">
			</td>
			<td class="value right" style="padding-left: 15px;">    
				<?= $total_num_staff ?>        
		  	</td>
			<td class="right" style="padding-left: 15px;">  
				$<?= number_format($total_amount_paid, 2, ".", "") ?>  
		  	</td>
		</tr>
	</tbody>
</table>
<?php
		CreateDataTable($subscriptionsTableObj);
		echo 'Download:';
		CreateDataTable($subscriptionsTableDownloadObj, "100%", 10, true, "EOTSubscription");
  	}
?>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() ?>/js/jquery.dataTables.min.js"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() ?>/js/dataTables.buttons.min.js"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() ?>/js/buttons.flash.min.js"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() ?>/js/jszip.min.js"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() ?>/js/vfs_fonts.js"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() ?>/js/buttons.html5.min.js"></script>
<script language="javascript" type="text/javascript" src="<?= get_template_directory_uri() ?>/js/buttons.print.min.js"></script>
<script>
	$(document).ready(function () {
		$('#DataTable_2, #DataTable_2_filter, #DataTable_2_paginate, #DataTable_2_info, #DataTable_2_length').hide();
	})
</script>
<style>
	.buttons-html5 {
		padding-right:10px;
	}
</style>


