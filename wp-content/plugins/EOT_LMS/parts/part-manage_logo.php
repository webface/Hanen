
<?php
	// Variable declaration
	global $current_user;

	// verify this user has access to this portal/subscription/page/view
	$true_subscription = verifyUserAccess(); 

	$user_id = $current_user->ID;							     // Wordpress user ID
    $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
	$org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
  	$data = compact ("org_id", "org_subdomain", "user_id");
  	$admin_ajax_url = admin_url('admin-ajax.php');
	$image = get_field( 'dashboard_logo', 'user_' . $org_id ); // Advance custom fields. Field for the dashboard_logo
	
?>
	<div class="breadcrumb">
		<?= CRUMB_DASHBOARD ?>    
		<?= CRUMB_SEPARATOR ?>     
		<?= CRUMB_ADMINISTRATOR ?>    
		<?= CRUMB_SEPARATOR ?>    
	    <span class="current">Manage Logo</span>     
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
						'field_groups' => array(11815), // The POST ID for dashboard logo
						'post_id' => 'user_'.$org_id,
						'return' => '?part=manage_logo&updated=true&subscription_id=' . $subscription_id,
						'updated_message' => __("Dashboard logo updated.", 'acf'),
					)
				);

				if(isset($_REQUEST['updated']) && $_REQUEST['updated'] == true)
				{
?>
<script>
	$ = jQuery;
	var link = $( ".acf-image-image" ).attr("src");
		var ajax_url = ajax_object.ajax_url;  
	var info_data = 
	{
		action: 'updatePortal',
		logo_image_url: '<?php echo $image['sizes']['medium_large']; ?>',
		portal_subdomain: "<?= $org_subdomain ?>",
		org_id: "<?= $org_id ?>",
	}
	/* Sends request to updatePortal in learnupon function to update the img url.
	 */
	$.ajax({
		type: "POST",
		data: info_data,
		url: ajax_url,
	   	success: function(data)
	    {
	      // Sending post is succesful. However, there is something wrong with sending info to admin-ajax.
	      if( data == 0 )
	      {
	        $("#staff_and_assignment_list").find(".scroll-pane-wrapper").fadeTo('fast',0.1, function()
	        {
	        // For now, redirect to error page. 
	        window.location.replace("?part=error");
	        })
	      }
	      else
	      {
	      	var obj = jQuery.parseJSON(data);
	      	if(obj.message)
	      	{
	        	// Display error message.
	        	$("#message p").text(obj.message);
	      	}
	      }
	    },
		// If it fails on the other hand.
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			alert( "POST Sent failed: " + textStatus );
		}
	});	
</script>

<?php
				}
      		}
			else
			{
				echo "Unauthorized!";
			}
		}
		else
		{
			echo "subscription ID does not belong to you";
		}
	}
	// Could not find the subscription ID
	else
	{
	echo "Could not find the subscription ID";
	}
?>

