<?php
  if(isset($_REQUEST['id']) && $_REQUEST['id'] != "" && current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
  {
      // Variable declaration
      $user_id = filter_var($_REQUEST['id'],FILTER_SANITIZE_NUMBER_INT); // Wordpress User ID
      $org_id = get_org_from_user ($user_id); // Organization ID
      $user = get_user_by( 'id', $user_id ); // User's Wordpress ID
      $first_name = get_user_meta ($user_id, 'first_name', true);
      $last_name = get_user_meta ($user_id, 'last_name', true);   
      $name = $first_name . " " . $last_name; // User's First and Last name in wordpress
      $org_name = get_the_title($org_id);
      // set the role
      if (in_array('manager', $user->roles))
      {
        $role = 'manager';
      }
      else if (in_array('uber_manager', $user->roles))
      {
        $role = 'uber_manager';
      }
      else if (in_array('umbrella_manager', $user->roles))
      {
        $role = 'umbrella_manager';
      }
      else
      {
        $role = 'student';
      }
  ?>
  	<div class="breadcrumb">
  		<?= CRUMB_DASHBOARD ?>    
  		<?= CRUMB_SEPARATOR ?>
      <?= CRUMB_USERSLISTS ?>  
      <?= CRUMB_SEPARATOR ?>
     	<span class="current">Update User</span>     
  	</div>
  	<h1 class="article_page_title">Update User <b><?= $name ?></b></h1>
      <form id="updateProfile" method="post" action="">
          <table class="data small">
            <tbody>
                <tr>
                  <td class="label right">
                    First Name
                  </td>
                  <td>
                   <input type="text" name="name" id="name" size="35" value="<?= $first_name ?>">
                  </td>
                </tr>
                <tr>
                  <td class="label right">
                    Last Name
                  </td>
                  <td>
                   <input type="text" name="lastname" id="lastname" size="35" value="<?= $last_name ?>">
                  </td>
                </tr> 
                <tr>
                  <td class="label right">
                    Camp Name
                  </td>
                  <td>
                    <input type="text" name="camp_name" id="camp_name" value="<?= $org_name ?>" size="35">
                  </td>
                </tr>  
                <tr>
                  <td class="label right">
                    New Password
                  </td>
                  <td>
                    <input type="text" name="pw" id="pw" size="35">
                  </td>
                </tr>       
                <tr>
                  <td class="label right">
                    Email
                  </td>
                  <td>
                    <input type="text" name="email" id="email" size="35" value="<?= $user->user_email; ?>">
                  </td>
                </tr>
                <tr>
                  <td colspan="2" align="right">
                      <input type="submit" value="Save Changes">     
                      <input type="hidden" name="action" value="updateUser">  
                      <input type="hidden" id="user_id" name="user_id" value="<?= $user_id ?>">        
                      <input type="hidden" id="old_camp_name" name="old_camp_name" value="<?= $org_name ?>">        
                      <input type="hidden" id="old_email" name="old_email" value="<?= $user->user_email ?>">
                      <input type="hidden" id="staff_id" name="staff_id" value="<?= $user_id ?>">    
                      <input type="hidden" id="org_id" name="org_id" value="<?= $org_id ?>">   
                      <input type="hidden" id="role" name="role" value="<?= $role ?>">   
                      <?php wp_nonce_field( 'update-staff_' . $user_id ); ?> 
                  </td>               
                </tr>   
            </tbody>
          </table>
      </form>
      <span id="responseMessage"></span>
      <script>
      $ = jQuery;
      // This sends the ajax request when the form is submitted to update the user profile.
      $("#updateProfile").submit(function(e) {
      load('load_loading');
      $.ajax(
          {
            type: "POST",
            url: ajax_object.ajax_url,
            data: $("#updateProfile").serialize(), // serializes the form'responses elements.
            success: function(data)
            {
              response = jQuery.parseJSON(data);
              if(response.success == true)
              {
                $( "#responseMessage" ).text( response.message);
                if(response.old_email != "") // Update old email value.
                {
                  $( "#old_email" ).val(response.email);
                }
              }
              else if ( response.success == false)
                $( "#responseMessage" ).text (response.errors);
              else
                $( "#responseMessage" ).text( 'Could not find the error in updateUser file. Please contact the administrator' );
              $(document).trigger('close.facebox');
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
?>