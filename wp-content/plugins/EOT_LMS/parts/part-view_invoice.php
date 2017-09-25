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
	$subscriptions = getSubscriptions (0, 0, 0, 0, '0000-00-00', '0000-00-00', $user_id); // Get all the subscriptions with certain start date and end date.
	$upgrades = getUpgrades (0, '0000-00-00', '0000-00-00', 0, $user_id) ; // get upgrades from

	// Tables for download. This will not be displayed on the front end.
	$invoiceInfoTableDownloadObj = new stdClass();
	$invoiceInfoTableDownloadObj->rows = array();
	$invoiceInfoTableDownloadObj->headers = array(
		'Invoice Date' => 'center',
		'Total Amount' => 'center',
		'Sales Rep' => 'center',
		'Description' => 'center',
	);
	// Verify if the users has any previous subscriptions or upgrades.
	if($subscriptions || $upgrades)
	{
		// Display all purchased subscriptions.
		foreach($subscriptions as $subscription)
		{
			// Populate invoice info table
			$rep_info = get_userdata($subscription->rep_id); // Rep Info from WP
			$rep_name = ($rep_info) ? $rep_info->first_name . " " . $rep_info->last_name : ''; // REP first and last name
			$library = getlibrary($subscription->library_id);
			$library_title = isset( $library->name ) ? $library->name : "Unknown Library";
			$invoiceInfoTableDownloadObj->rows[] = array(
				$subscription->trans_date, // Transaction Date
				'$' . $subscription->price, // Transaction price.
				$rep_name, // REP first and last name
				'<a href="../invoice/?type=subscription&subscription_id=' . $subscription->ID . '">EOTSubscription#' . $subscription->ID . '</a>'
			);
		}
		// Display all purchased upgrades.
		foreach($upgrades as $upgrade)
		{
			$upgradeInfoTableDownloadObj->rows[] = array(
			$upgrade->date, // Transaction Date
			'$' . $upgrade->price, // Transaction price.
			$upgrade->accounts, // REP first and last name
			$upgrade->method
			);
			// Populate invoice info table
			$rep_info_upgrade = get_userdata($upgrade->rep_id); // Rep Info from WP
			$rep_name_upgrade = ($rep_info_upgrade) ? $rep_info_upgrade->first_name . " " . $rep_info_upgrade->last_name : ''; // REP first and last name
			$invoiceInfoTableDownloadObj->rows[] = array(
			$upgrade->date, // Transaction Date
			'$' . $upgrade->price, // Transaction price.
			$rep_name_upgrade, // REP first and last name
			'<a href="../invoice/?type=upgrade&id=' . $upgrade->ID . '">EOTUpgrade#' . $upgrade->ID . '</a>'
			);
		}

		CreateDataTable($invoiceInfoTableDownloadObj); // Print the table in the page	
	}
	else
	{
		echo "You do not have any previous invoice.";
	}
}
else
{
	// Not a director?
    echo 'ERROR: You do not have permisison to view this page.';
}

	