<?php
    if(current_user_can("is_sales_manager"))
    {
        // Variable declaration
        $users = get_users( ); // Users in wordpress

        /*
         * Create table heading for users
         */
        $userTableObj = new stdClass(); 
        $userTableObj->rows = array();
        $userTableObj->headers = array(
    		'Name' => 'quiz-title',
    		'E-mail'=> 'center',
    		'<div ' . hover_text_attr('The date when user first registered.',true) .'>Registered</div>' => 'staff-progress',
    		'Actions'=> 'actions'
    	);
        /* 
         * This add the users who are a student or a camp director.
         * Count the number of enrollments
         */
        foreach($users as $user)
        {
            if(isset($user->allcaps['is_sales_rep'])) // Check the user capability to filter only sales reps
            {
            	$user_id = $user->ID; // Wordpress user ID
            	$name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
            	$email = $user->user_email; // User's Wordpress Email
            	$date_registered = substr($user->user_registered, 0, 10); // User's date registration in wordpress

              // Create a table row.
          		$userTableObj->rows[] = array($name, $email, $date_registered, '<a href="?part=update_sales_rep&user_id=' . $user_id . '" onclick="load(\'load_loading\')"' . hover_text_attr('Update Sales Rep',true) . '><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=deactivate_sales_rep&user_id='.$user_id.'" onclick="load(\'load_loading\')"' . hover_text_attr('Deactivate Sales Rep',true) . '><i class="fa fa-trash" aria-hidden="true"></i></a>');
            }
        }
    ?>
    	<div class="breadcrumb">
    		<?= CRUMB_DASHBOARD ?>    
    		<?= CRUMB_SEPARATOR ?>     
        	<span class="current">Manage Sales Rep</span>     
    	</div>
    	<h1 class="article_page_title">Manage Sales Representatives</h1>
    <?php
        // Display the user's table
    	CreateDataTable($userTableObj);
    ?>
    <img style="margin-right:60em;" class="loader" src="<?= get_template_directory_uri() . "/images/loading.gif"?>" hidden>
    <div class="main-content" data-user="<?= $user_id ?>">
    <h1 class="article_page_title">Create New Sales Rep</h1>
      <form id="create_sales_rep" method="POST" action="">
        <table class="data small">
          <tbody>
              <tr>
                <td class="label right">
                  First Name
                </td>
                <td>
                 <input type="text" name="first_name" size="35">
                </td>
              </tr>
              <tr>
                <td class="label right">
                  Last Name
                </td>
                <td>
                 <input type="text" name="last_name" size="35">
                </td>
              </tr> 
              <tr>
                <td class="label right">
                  Email
                </td>
                <td>
                  <input type="text" name="email" size="35">
                </td>
              </tr>  
              <tr>
                <td class="label right">
                  Password
                </td>
                <td>
                  <input type="text" name="password" size="35">
                </td>
              </tr>
              <tr>
                <td class="right" colspan=2>
                  <input type="hidden" name="action" value="updateCreateSalesRep">
                  <input type="hidden" name="create_user" value="1">
                  <input type="submit" class="create_sales" value="Create User">
                </td>
              </tr>
          </tbody>
        </table>
      </form>
    </div>
        <script>
          $ = jQuery;
          $("#create_sales_rep").submit(function(e) {

            var url =  ajax_object.ajax_url;

            //show loader gif and hide the content
            $('.main-content, .loader').toggle();

            //ajax call to update the fields
            $.ajax({
              type: "POST",
              url: url,
              dataType: 'json',
              data: $("#create_sales_rep").serialize(),
              success: function(data)
              {
                  if (!data['status'])
                  {
                    alert(data['message']);
                  }
                  else
                  {
                    alert("SUCCESS!");
                  }
                  location.reload();
              }
            });
            e.preventDefault(); // avoid to execute the actual submit of the form.
          });
        </script>
    <?php
    }
    else
    {
        echo "You do not have the privilege to view this page.";
    }
?>