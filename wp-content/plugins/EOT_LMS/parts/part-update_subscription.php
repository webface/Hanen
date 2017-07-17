<?php

	// check user's role for permission to access this display
	if (!current_user_can('is_sales_manager'))
	{
		wp_die('You do not have access to this display.');
	}

if( isset($_REQUEST['subscription_id']) )
{
	$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);

	if(isset($_REQUEST['action'])
		&& $_REQUEST['action'] == "updateSubscription"
		&& isset($_REQUEST['library_id'])
		&& isset($_REQUEST['max'])
		&& isset($_REQUEST['transaction_date'])
		&& isset($_REQUEST['start_date'])
		&& isset($_REQUEST['end_date'])
		&& isset($_REQUEST['price'])
		&& isset($_REQUEST['method'])
		&& isset($_REQUEST['rep_id']) )
    {
    	$library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);	//Library ID
    	$max = filter_var($_REQUEST['max'],FILTER_SANITIZE_NUMBER_INT);	//Max Staff
    	$transaction_date = filter_var($_REQUEST['transaction_date'],FILTER_SANITIZE_STRING);	//Transaction Date
    	$start_date = filter_var($_REQUEST['start_date'],FILTER_SANITIZE_STRING);	//Start Date
    	$end_date = filter_var($_REQUEST['end_date'],FILTER_SANITIZE_STRING);	//End Date
    	$price = filter_var($_REQUEST['price'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);	//Price
    	$method = filter_var($_REQUEST['method'],FILTER_SANITIZE_STRING);	//Method of payment
    	$rep_id = filter_var($_REQUEST['rep_id'],FILTER_SANITIZE_NUMBER_INT);	//Representative ID
    	$notes = filter_var($_REQUEST['notes'],FILTER_SANITIZE_STRING);	//Notes
    	$data = compact('library_id', 'max', 'transaction_date', 'start_date', 'end_date', 'price', 'method', 'rep_id', 'notes');

    	$result = updateSubscription($subscription_id, $data);	//update subscription result (T/F)

    	if($result)
    	{
    		$message = "The subscription has been updated.";

    	}
    	else
    	{
    		$message = "Update failed.";
    	}
    }
    else
    {
    	$message = "Missing parameters.";
    }

	$subscription = getSubscriptions($subscription_id,0,1); // get the current subscription
    $manager_id = $subscription->manager_id; // The manager id of the subscription
    $user = get_user_by('id', $manager_id); // WP User information for the camp director
	$args = array('role__in' => array('salesrep', 'sales_manager')); // Arguments to get only sales rep
	$salesreps = get_users($args); // All sales rep.
?>
	<div class="breadcrumb">
	<?= CRUMB_DASHBOARD ?>    
	<?= CRUMB_SEPARATOR ?>
	 <?= CRUMB_SUBSCRIPTION_DETAILS ?> 
	<?= CRUMB_SEPARATOR ?> 
	<span class="current">Update Subscription</span> 
	<h1 class="article_page_title">Update Subscription</h1>

<?php 
	if (isset($message) && isset($_REQUEST['action']))
	{
?>
    <div class="round_msgbox">
        <?= $message ?><br />
    </div>		
<?php
	} 
?>		

	<form action="" method="post">
		<table class="data small">
	  		<tbody>
				<tr>
					<td class="label">
					  ID
					</td>
					<td class="value">
						<?= $subscription_id ?>            
				  	</td>
				</tr>
				<tr>
					<td class="label">
				  		TXN ID
					</td>
					<td class="value">
				  		<?= $subscription->trans_id ?>                
				  	</td>
				</tr>
				<tr>
					<td class="label">
				  		Org ID
					</td>
					<td class="value">
	                  	<?= $subscription->org_id ?> / <?= get_post($subscription->org_id)->post_title; ?>          
		          	</td>
				</tr>
				<tr>
					<td class="label">
				  		Director
					</td>
					<td class="value">
						<?= $user ? $user->first_name . " " . $user->last_name . '/ <a href="mailto:' . $user->user_email .' "> ' . $user->user_email . ' </a>' : "N/a" ?>
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Library ID
					</td>
					<td class="value">
				  		<input name="library_id" type="text" size="3" value="<?= $subscription->library_id ?>">
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Max. Staff
					</td>
					<td class="value">
				  		<input name="max" type="text" size="3" value="<?= $subscription->staff_credits ?>" readonly>
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Transaction Date
					</td>
					<td class="value">
				  		<input id="transaction_date" name="transaction_date" type="text" size="10" value="<?= $subscription->trans_date ?>">
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Start Date
					</td>
					<td class="value">
				  		<input id="start_date" name="start_date" type="text" size="10" value="<?= $subscription->start_date ?>">
					</td>
				</tr>
				<tr>
					<td class="label">
				  		End Date
					</td>
					<td class="value">
						<input id="end_date" name="end_date" type="text" size="10" value="<?= $subscription->end_date ?>">
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Price
					</td>
					<td class="value">
				  		<input name="price" type="text" size="7" value="<?= $subscription->price ?>">
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Payment Method:
					</td>
					<td class="value">
					  	<select name="method">
		          			<option value="stripe"<?php echo ($subscription->method == 'stripe') ? ' selected' : '' ?>>stripe</option>
		          			<option value="check" <?php echo ($subscription->method == 'check') ? ' selected' : '' ?>>cheque</option>
		          			<option value="free" <?php echo ($subscription->method == 'free') ? ' selected' : '' ?>>free</option>
				        </select>
					</td>
				</tr>
				<tr>
					<td class="label">
						Umbrella Group:
					</td>
					<td class="value">
						<input name="umbrella_group" type="text" size="25" value="">
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Notes
					</td>
					<td class="value">
				  		<textarea name="notes" rows="4" cols="40"><?= $subscription->notes ?></textarea>
					</td>
				</tr>
				<tr>
					<td class="label">
				  		Sales Rep
					</td>
					<td class="value">
						<select name="rep_id">
							<option value="0" >None</option>
<?php
							// Goes to each sales rep, prints their name and select the current rep for this subscription 
							foreach ($salesreps as $rep) 
							{
?>
								<option value="<?= $rep->ID ?>"<?php echo ($rep->ID == $subscription->rep_id) ? 'selected' : '' ?>><?= $rep->display_name ?></option>
<?php
							}
?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="label">
					</td>
					<td class="value">
			  			<input type="hidden" name="subscription_id" value="<?= $subscription->ID ?>">
				  		<input type="hidden" name="action" value="updateSubscription">  
				  		<input type="submit" value="Update Subscription">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
<?php
}
?>
	<script>
		$ = jQuery;
		/* 
		*  Added date menu for the Transaction Date, Starting Date and the End Date.
		*/
		$(function() 
		{
			$( "#start_date, #transaction_date, #end_date" ).datepicker
			({
				dateFormat: 'yy-mm-dd',
			});
		});
	</script>