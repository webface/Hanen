
<?php
	// Variable declaration
	global $current_user;

	// verify this user has access to this portal/subscription/page/view
	$true_subscription = verifyUserAccess(); 

	$user_id = $current_user->ID;							     // Wordpress user ID
    $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
	$image = get_field( 'dashboard_logo', 'user_' . $org_id ); // Advance custom fields. Field for the dashboard_logo
	
?>
	<div class="breadcrumb">
		<?= CRUMB_DASHBOARD ?>    
		<?= CRUMB_SEPARATOR ?>     
		<?= CRUMB_ADMINISTRATOR ?>    
		<?= CRUMB_SEPARATOR ?>    
	    <span class="current"><?= __("Manage Logo", "EOT_LMS"); ?></span>     
	</div>
<?php

	// Check if the subscription ID is valid.
	if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "")
	{
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID

		if(isset($true_subscription['status']) && $true_subscription['status'])
		{
	        if(current_user_can( "is_director" ))
	        {
				the_content(); 
				acf_form(
					array(
						'field_groups' => array(ACF_MANAGE_LOGO), // The POST ID for dashboard logo
						'post_id' => 'user_'.$org_id,
						'return' => '?part=manage_logo&updated=true&org_id='.$org_id.'&subscription_id=' . $subscription_id,
						'updated_message' => __(__("Dashboard logo updated.", "EOT_LMS"), 'acf'),
					)
				);
      		}
			else
			{
				echo __("Unauthorized!", "EOT_LMS");
			}
		}
		else
		{
			echo __("subscription ID does not belong to you", "EOT_LMS");
		}
	}
	// Could not find the subscription ID
	else
	{
		echo __("Could not find the subscription ID", "EOT_LMS");
	}
?>

