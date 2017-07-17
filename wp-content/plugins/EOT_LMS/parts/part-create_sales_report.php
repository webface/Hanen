<?php
	// make sure the user has permission to this page
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {

		if( !isset( $_REQUEST['start_date']) || !isset($_REQUEST['end_date'] ) )
		{
			wp_die('Invalid start date or end date.');
		}
?>


<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <span class="current">Create Sales Report</span>     
</div>
<h1 class="article_page_title">Download Sales Report</h1>
<form>
	<table>
		<input type="hidden" name="part" value="create_sales_report">
		<tr>
			<th>
				Start Date:(yy-mm-dd)
			</th>
			<th>
				&nbsp;<input type="text" name="start_date" id="start_date" class="date-picker" size="10" value="<?= $_REQUEST['start_date']?>">
			</th>
		</tr>
		<tr colspan="3">
			<td>
				End Date:(yy-mm-dd)
			</td>
			<td>
				&nbsp;<input type="text" name="end_date" id="end_date" class="date-picker" size="10" value="<?= $_REQUEST['end_date'] ?>">
			</td>
			<td>
				&nbsp;&nbsp;<input type="submit" value="Generate Sales Report" id="generateSalesReport" style="">
			</td>
		</tr>
		<tr>
			<td>
				<select id="quickSelection">
					<option value="" selected>Quick Selections</option>
					<option value="this_month">This Month</option>
					<option value="last_month">Last Month</option>
					<option value="this_week">This Week</option>
					<option value="last_week">Last Week</option>
					<option value="last_7_days">Last 7 Days</option>
					<option value="last_30_days">Last 30 Days</option>
					<option value="this_season">This season</option>
				</select>
			</td>
		</tr>
	</table>
</form>
<br />
<?php 
		$start_date = filter_var($_REQUEST['start_date'],FILTER_SANITIZE_STRING);
		$end_date   = filter_var($_REQUEST['end_date'],FILTER_SANITIZE_STRING);
		$subscriptions = getSubscriptions(0, 0, 0, 0, $start_date, $end_date); // Get all the subscriptions with certain start date and end date.
		$libraries = getLibraries(); // get all the libraries
		$library_names = array();
		// need to create associative array of library names
		if ($libraries)
		{
			foreach ($libraries as $key => $lib_obj)
			{
				$library_names[$lib_obj->id] = $lib_obj->name;
			}
		}

		// Tables for download. This will not be displayed on the front end.
		$subscriptionsTableDownloadObj = new stdClass();
		$subscriptionsTableDownloadObj->rows = array();
	  	$subscriptionsTableDownloadObj->headers = array(
			'Date' => 'center',
			'Camp Name' => 'center',
			'Gross' => 'center',
			'Stripe Fee' => 'center',
			'Dashboard Price' => 'center',
			'Dashboard Discount' => 'center',
			'Staff Price' => 'center',
			'Staff Discount' => 'center',
			'Data Disk Price' => 'center',
			'Contact' => 'center',
			'Email' => 'center',
			'Phone' => 'center',
			'Staff' => 'center',
			'Library' => 'center',
			'Sales Rep' => 'center',
			'Commisions' => 'center',
			'Type of Sale' => 'center',
			'Payment Method' => 'center',
		);

		// Tables that will be displayed in the front end.
		$subscriptionsTableObj = new stdClass();
		$subscriptionsTableObj->rows = array();
	  	$subscriptionsTableObj->headers = array(
			'Date' => 'center',
			'Camp Name' => 'center',
			'Total Amount' => 'center',
			'Sales Rep' => 'center',
			'Commisions' => 'center',
			'Sales Type' => 'center',
		);

	   	// Required to download table for excell download file. This table will not be displayed
	  	foreach($subscriptions as $subscription)
	  	{
	  		$stripe_charge = ($subscription->method == "stripe") ? "$" . number_format($subscription->price * 0.029 + 0.3, 2, ".", "") : "";
	  		$customer_info = get_userdata($subscription->manager_id); // Camp owner Info from WP
	  		$customer_name = ($customer_info) ? $customer_info->first_name . " " . $customer_info->last_name: 'Could not find the manager.'; // REP first and last name
	  		$customer_email = ( $customer_info ) ? $customer_info->user_email : 'Could not find the e-mail';
	  		$rep_info = get_userdata($subscription->rep_id); // Rep Info from WP
	  		$rep_name = ($rep_info) ? $rep_info->first_name . " " . $rep_info->last_name : ''; // REP first and last name
		 
	  		// Adding attributes to the object. To avoid redundancy for using get_post_meta and get_post
	  		$subscription->customer_name = $customer_name;
	  		$subscription->customer_email = $customer_email;
	  		$subscription->rep_name = $rep_name;
	  		$subscription->org_phone = get_post_meta( $subscription->org_id, 'phone', true );
	  		$subscription->camp_name = get_the_title($subscription->org_id);
	  		$camps_name[$subscription->ID] = $subscription->camp_name; // Associative array of subscription id and camp name. Subscription id is the key.

	  		// Populate subscription table
		 	$subscriptionsTableObj->rows[] = array(
				$subscription->trans_date, // Transaction Date
				$subscription->camp_name, // The name of the camp,
				'$' . $subscription->price, // Transaction price.
				$rep_name, // REP first and last name
				($rep_info && $subscription->method != "free") ? '$' . ($subscription->price * COMMISION_PERCENT_NEW) : "",
				'Subscription'
			);

			// Populate subscription download table
		 	$subscriptionsTableDownloadObj->rows[] = array(
		    	$subscription->trans_date,
			    $subscription->camp_name,
			    "$" . number_format($subscription->price, 2, ".", ""),
			    $stripe_charge,
			    "$" . $subscription->dash_price,
			    "$" . $subscription->dash_discount,
			    "$" . $subscription->staff_price,
			    "$" . $subscription->staff_discount,
			    "$" . $subscription->data_disk_price,
			    $customer_name,
			    $customer_email,
			    $subscription->org_phone,
			    $subscription->staff_credits,
			    $library_names[$subscription->library_id],
			    $rep_name,
		     	($rep_info && $subscription->method != "free") ? "$" . ($subscription->price * COMMISION_PERCENT_NEW) : "",
			    'Subscription Sale',
			    $subscription->method,
			);	
	}
    $upgrades = getUpgrades(0, $start_date, $end_date); // All the upgrades for this date range.
    if($upgrades)
    {
    	// Add the subscription upgrades details as well
        foreach($upgrades as $upgrade)
        {
        	// Make associative array
        	$upgrades_array[$upgrade->id] = $upgrade; // Associative array of upgrades.
        	$upgrade_rep_info = get_userdata($upgrade->rep_id); // Upgrade Rep Info from WP
        	$upgrade_rep_name = ($upgrade_rep_info) ? $upgrade_rep_info->first_name . " " . $upgrade_rep_info->last_name : ''; // REP first and last name
			$upgrade->org_phone = get_post_meta( $upgrade->org_id, 'phone', true );
			$stripe_charge = ($upgrade->method == "stripe") ? "$" . number_format($upgrade->price * 0.029 + 0.3, 2, '.', '') : "";
			// Populate subscription download table for upgrades
		 	$subscriptionsTableDownloadObj->rows[] = array(
		    	$upgrade->date,
			    $camps_name[$upgrade->subscription_id],
			    '$' . number_format($upgrade->price, 2, '.', ''),
			    $stripe_charge,
			    "",
			    "",
			    "",
			    "",
			    "",
			    $customer_name,
			    $customer_email,
			    $upgrade->org_phone,
			    $upgrade->accounts,
			    $library_names[$subscription->library_id],
			    $upgrade_rep_name,
			    ($upgrade_rep_info && $upgrade->method != "free") ? '$' . ($upgrade->price * COMMISION_PERCENT_NEW)  : "",
			    'Upgrade Sale',
			    $upgrade->method,
			);	

        	// Populate the subscription table for upgrades
			$subscriptionsTableObj->rows[] = array(
				$upgrade->date, // Transaction Date
				$subscription->camp_name, // The name of the camp,
				'$' . number_format($upgrade->price, 2, '.', ''), // Transaction price.
				$upgrade_rep_name, // REP first and last name
				($upgrade_rep_info) ? '$' . number_format(($upgrade->price * COMMISION_PERCENT_NEW), 2, '.', '') : '', // Commision for the upgrade.
				'Upgrade'
			);
        }
    }
 ?>
</table>
<?php
CreateDataTable($subscriptionsTableObj); // Print the table in the page
echo 'Download:';
CreateDataTable($subscriptionsTableDownloadObj, "100%", 25, true, "EOTSalesReport-" . $start_date . "To" . $end_date);
?>

<script>
// Quick selections
$( document ).on('change', '#quickSelection', function() 
{
	var dateObj = new Date();
	// Quick date Selections.
	switch( $(this).val() )
	{
		case "this_month":
			var lastDay = new Date(dateObj.getFullYear(), dateObj.getMonth() +1, 0);
			var last_day_of_month = lastDay.getUTCDate();
			first_date = dateObj.getUTCFullYear() + "-" + (dateObj.getUTCMonth() + 1) + "-01";
			last_date = dateObj.getUTCFullYear() + "-" + (dateObj.getUTCMonth() + 1) + "-" + last_day_of_month;
			break;
		case "last_month":
			dateObj.setDate(1);
			dateObj.setMonth(dateObj.getMonth()-1);
			first_date = dateObj.getUTCFullYear()  + "-" + (dateObj.getUTCMonth() + 1) + "-01";
			var lastDay = new Date(dateObj.getFullYear(), dateObj.getMonth() +1, 0);
			last_date = dateObj.getUTCFullYear()  + "-" + (dateObj.getUTCMonth() + 1) + "-" + lastDay.getUTCDate();
			break;
		case "this_week":
			first_date = dateObj.getUTCFullYear() + "-" + (dateObj.getUTCMonth() + 1) + "-" + (dateObj.getDate() - dateObj.getDay() +1);
			last_date = dateObj.getUTCFullYear() + "-" + (dateObj.getUTCMonth() + 1) + "-" + (dateObj.getDate() - dateObj.getDay()+7);
			break;
		case "last_week":
			dateObj.setDate(dateObj.getDate() - 7);
			first_date = dateObj.getUTCFullYear() + "-" + (dateObj.getUTCMonth() + 1) + "-" + (dateObj.getDate() - dateObj.getDay() +1);
			last_date = dateObj.getUTCFullYear()  + "-" + (dateObj.getUTCMonth() + 1) + "-" + (dateObj.getDate() - dateObj.getDay()+7);
			break;
		case "last_7_days":
			last_date = dateObj.getUTCFullYear()  + "-" + (dateObj.getUTCMonth() + 1) + "-" + dateObj.getDate();
			dateObj.setDate(dateObj.getDate() - 7);
			first_date = dateObj.getUTCFullYear() + "-" + (dateObj.getUTCMonth() + 1) + "-" + (dateObj.getDate());
			break;
		case "last_30_days":
			last_date = dateObj.getUTCFullYear()  + "-" + (dateObj.getUTCMonth() + 1) + "-" + dateObj.getDate();
			dateObj.setDate(dateObj.getDate() - 30);
			first_date = dateObj.getUTCFullYear() + "-" + (dateObj.getUTCMonth() + 1) + "-" + (dateObj.getDate());
			break;
		case "this_season":
			last_date = '<?= SUBSCRIPTION_END ?>'
			first_date = '<?= SUBSCRIPTION_START ?>';
			break;
		default:
			break;
	}
	$('#start_date').val(first_date); // Inject the selected date in the form.
	$('#end_date').val(last_date); // Inject the selected date in the form.
	$('#generateSalesReport').click(); // Click the Generate Sales Report.
})
</script>
<br />
<br/>
<script language="javascript" type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script language="javascript" type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script language="javascript" type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script language="javascript" type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script language="javascript" type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/vfs_fonts.js"></script>
<script language="javascript" type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script language="javascript" type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
<script>
	$ = jQuery;
	$('.date-picker').datepicker(
	{ 
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});
	$( "#generateSalesReport" ).click(function(e) {
	});
	$(document).ready(function () {
		$('#DataTable_2, #DataTable_2_filter, #DataTable_2_paginate, #DataTable_2_info').hide();
	})
</script>
<?php
	}
?>
<style>
	.buttons-html5 {
		padding-right:10px;
	}
</style>