<div class="breadcrumb">
<?= CRUMB_DASHBOARD ?>    
<?= CRUMB_SEPARATOR ?>
<span class="current"> Create Subscription</span> 

<h1 class="article_page_title">Create Subscription</h1>
<?php
	if(current_user_can( "is_sales_rep" ) || current_user_can( "is_sales_manager" ))
	{
		if(isset($_REQUEST['user_id']))
		{
			$user_id =  filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); // The newly created wordpress account id
			$wp_user = get_user_by( 'ID', $user_id ); // WP User
			// Check if the wordpress user exsist.
			if($wp_user)
			{	
				// verify that the user is created
				echo '<div class="round_msgbox ">You have created the account! You may now add the subscription.</div>';
				sales_rep_new_subscription($user_id);	// Create new subscription for this user
			}
			else
			{	// WP ERROR. User not found.
				wp_die('Invalid user.');
			}
		}
		else
		{
			display_register_account_form (); // Create an account before creating a new subscription.
		}
	}
	else
	{
		wp_die('You do not have access to this display.');
	}
?>