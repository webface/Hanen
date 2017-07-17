<div class="breadcrumb">
	<?= CRUMB_DASHBOARD ?>    
	<?= CRUMB_SEPARATOR ?>
    <?= CRUMB_USERSLISTS ?>  
    <?= CRUMB_SEPARATOR ?>
	<span class="current">Delete User</span>     
</div>
<h1 class="article_page_title">Delete User</h1>
<?php
if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "" && (current_user_can("is_sales_rep") || current_user_can("is_sales_manager")))
{ 
    // Variable declaration
    global $current_user;
    $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); // Wordpress User ID
    $user_wordpress = get_user_by( 'id', $user_id ); // User info in wordpress
    if($user_wordpress)
    {
      $staff_id = $user_wordpress->ID;
      $org_id = get_org_from_user ($user_id); // Organization ID
      $name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
      $camp_name = get_the_title($org_id);
	    $email = $user_wordpress->user_email; // User's Wordpress Email
	    $date_registered = substr($user_wordpress->user_registered, 0, 10); // User's date registration in wordpress
?>
	Are you sure you want to delete the selected staff account?
		<form id="deleteStaffAccount" method="post" action="">
			<table class="data small">
				<tbody>
					<tr>
						<td class="label">
					  		Name
						</td>
						<td class="value">
					  		<?= $name ?>              
					  	</td>
					</tr>
					<tr>
					<td class="label">
				  		E-mail
					</td>
					<td class="value">
				  		<a href="mailto:<?= $email ?>"><?= $email ?></a>
					</td>
					</tr>
					<tr>
						<td class="label">
					  		Registered
						</td>
						<td class="value">
							<?= $date_registered ?>              
					  	</td>
					</tr>
					<tr>
						<td class="label">
					  		Camp(s)
						</td>
            <td class="value">
              <?= $camp_name ?>
            </td>
					</tr>
				</tbody>
		    </table>
		    <input type="submit" value="Delete <?= $name ?>"> 
	      	<input type="hidden" name="action" value="deleteStaffAccount">  
	      	<input type="hidden" id="<?= $user_id ?>" name="<?= $user_id ?>" value="<?= $user_id ?>">    
          	<input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
          	<input type="hidden" name="email" id="email" value="<?= $email ?>" /> 
          	<input type="hidden" name="staff_id" id="staff_id" value=" <?= $staff_id ?>" />
            <?php wp_nonce_field( 'delete-staff_id-org_id_' . $org_id ); ?>
		</form>
      	<span id="responseMessage"></span>
      <script>
      $ = jQuery;
      // This process the deletion of the staff account. Form sent the ajax request to deleteStaffACcount_callback.
      $("#deleteStaffAccount").submit(function(e) {
      load('load_loading');
      $.ajax(
          {
            type: "POST",
            url: ajax_object.ajax_url,
            data: $("#deleteStaffAccount").serialize(), // serializes the form'responses elements.
            success: function(data)
            {
              response = jQuery.parseJSON(data);
              if(response.success == true)
                $( "#responseMessage" ).text( response.message);
              else if ( response.success == false)
                $( "#responseMessage" ).text (response.errors);
              else
                $( "#responseMessage" ).text( 'Could not find the error in confirmdeleteuser file. Please contact the administrator' );
              window.location.href = eot.dashboard + '/?part=user_list'; // Redirect the user

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
    	// Could not find the user in wordpress
    	echo "Could not find the user in WP";
    }
}
else
{
	// Invalid parameters, could not find the user_id
	echo "Could not find the user id";
}
?>