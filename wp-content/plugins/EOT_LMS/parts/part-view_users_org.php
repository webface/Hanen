<?php
	// make sure the user has permission to this page
    if( current_user_can("is_sales_manager") )
    {
?>
    	<div class="breadcrumb">
		<?= CRUMB_DASHBOARD ?>    
		<?= CRUMB_SEPARATOR ?>
		<span class="current"> Delete Staff Org ID</span> 
		<h1 class="article_page_title">Delete Staff Org ID</h1>
<?php
    	if( isset($_REQUEST['ID']) )
    	{
    		$user_id =  filter_var($_REQUEST['ID'],FILTER_SANITIZE_NUMBER_INT); // WP ID
    		$user = get_user_by( "ID", $user_id );
			$org_id = get_org_from_user ($user_id); // Organization ID
			$camp_name = get_the_title($org_id);
?>
	<form id="deleteStaffOrgId" method="post" action="">
			<table class="data small">
				<tbody>
					<tr>
						<td class="label">
					  		Email
						</td>
						<td class="value">
					  		<?= $user->user_email ?>              
					  	</td>
					</tr>
					<tr>
						<td class="label">
					  		Organization
						</td>
						<td class="value">
					  		<?= $camp_name ?> (<?= $org_id ?>)
					  	</td>
					</tr>
					<tr>
						<td class="label">
					  		User Role
						</td>
						<td class="value">
					  		<?= implode(", ", $user->roles) ?>
					  	</td>
					</tr>
				</tbody>
		    </table>
		    <input type="submit" value="Delete <?= $user->user_email ?> org ID"> 
	      	<input type="hidden" name="action" value="deleteStaffOrgId">  
	      	<input type="hidden" id="staff_id" name="staff_id" value="<?= $user_id ?>">    
          	<input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
            <?php wp_nonce_field( 'delete-staff_Org_Id' . $org_id ); ?>
		</form>
		<span id="responseMessage"></span>
    <script>
		$ = jQuery;
		// This process the deletion of the staff account. Form sent the ajax request to deleteStaffOrgId_callback.
		$("#deleteStaffOrgId").submit(function(e) {
		load('load_loading');
		$.ajax(
		  {
		    type: "POST",
		    url: ajax_object.ajax_url,
		    data: $("#deleteStaffOrgId").serialize(), // serializes the form'responses elements.
		    success: function(data)
		    {
		      response = jQuery.parseJSON(data);
		      if(response.success == true)
		      {
		        $( "#responseMessage" ).text( "Org ID has been deleted from the user.");
		        window.location.href = eot.dashboard + '/?part=view_users_org'; // Redirect the user
		      }
		      else
		      {
		    	$( "#responseMessage" ).text( response.errors ); 
		    	$(document).trigger('close.facebox'); // Close it on error too. 	
		      }
		    },
		    error: function()
		    {
		      $(document).trigger('close.facebox'); // Close it on error too.
		    }

		  });
		  e.preventDefault(); // avoid to execute the actual submit of the form.
		});
	</script>
<?php

    	}
    	else
    	{
			$args = array(
				'role__not_in' => array("manager", "student"),
			); 
			$users = get_users( $args ); 
			// Tables for users resources.
			$usersOrgTableObj = new stdClass();
			$usersOrgTableObj->rows = array();
		  	$usersOrgTableObj->headers = array(
		  		'ID' => 'center',
				'Email' => '',
				'Role' => '',
				'Organization' => 'center',
				'Action' => 'center'
			);
			foreach ($users as $user) 
			{
				$user_id = $user->ID; // WP User ID
				$org_id = get_org_from_user ($user_id); // Organization ID
				$camp_name = get_the_title($org_id);
			 	$usersOrgTableObj->rows[] = array(
			    	$user_id,
			    	$user->user_email,
			    	implode(",", $user->roles),
			    	"$camp_name - ($org_id)",
			    	'<a href="?part=view_users_org&ID=' . $user_id . '"><i class="fa fa-times" aria-hidden="true"></i></a>'
				);	

			}
			CreateDataTable($usersOrgTableObj); // Print the table in the page
    	}
	}
	else
	{
		wp_die('You do not have access to this display.');
	}
?>