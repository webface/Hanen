<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current">View Invoice</span>     
</div>
<h1 class="article_page_title">View Invoice</h1>
<?php
if(current_user_can( "is_director" ))
{
	// Variable declaration
	global $current_user;
	$user_id = $current_user->ID;	
	$org_id = get_org_from_user ($user_id); // Organization ID
	$subscriptions = getSubscriptions (0, 0, 0, 0, '0000-00-00', '0000-00-00', '0000', $user_id); // Get all the subscriptions with certain start date and end date. 
	$upgrades = getUpgrades (0, '0000-00-00', '0000-00-00', 0, $user_id) ; // get upgrades for this user

	$invoiceInfoTableDownloadObj = new stdClass();
	$invoiceInfoTableDownloadObj->rows = array();
	$invoiceInfoTableDownloadObj->headers = array(
		'Invoice Date' => 'center',
		'Total Amount' => 'center',
		'View Invoice' => 'center',
	);
	
	// Verify if the users has any previous subscriptions or upgrades.
	if($subscriptions || $upgrades)
	{
		// Display all purchased subscriptions.
		foreach($subscriptions as $subscription)
		{
			// Populate invoice info table
			$invoiceInfoTableDownloadObj->rows[] = array(
				$subscription->trans_date, // Transaction Date
				'$' . $subscription->price, // Transaction price.
				'<a href="' . get_home_url() . '/invoice/?type=subscription&subscription_id=' . $subscription->ID . '"><i class="fa fa-file-text-o" aria-hidden="true"></i></a>'
			);
		}
		// Display all purchased upgrades.
		foreach($upgrades as $upgrade)
		{
			$invoiceInfoTableDownloadObj->rows[] = array(
				$upgrade->date, // Transaction Date
				'$' . $upgrade->price, // Transaction price.
				'<a href="' . get_home_url() . '/invoice/?type=upgrade&id=' . $upgrade->ID . '"><i class="fa fa-file-text-o" aria-hidden="true"></i></a>'
			);
		}

		CreateDataTable($invoiceInfoTableDownloadObj); // Print the table in the page	
	}
	else
	{
		echo __("You do not have any previous invoices.","EOT_LMS");
	}
}
else
{
	// Not a director?
    echo __('ERROR: You do not have permisison to view this page.', "EOT_LMS");
}

	