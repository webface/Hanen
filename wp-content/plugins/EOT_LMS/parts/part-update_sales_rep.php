<?php
  if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "" && current_user_can("is_sales_manager"))
  {
      // Variable declaration
      $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT); 	// Wordpress User ID
      $first_name = get_user_meta ($user_id, 'first_name', true);				//first name
      $last_name = get_user_meta ($user_id, 'last_name', true);					//last name
      $user = get_user_by( 'id', $user_id ); 									// User's Wordpress information
      $email = $user->user_email;												//email

?>
  	<div class="breadcrumb">
  		<?= CRUMB_DASHBOARD ?>    
  		<?= CRUMB_SEPARATOR ?>
          <?= CRUMB_MANAGESALESREP ?>  
          <?= CRUMB_SEPARATOR ?>
      	<span class="current">Update Sales Rep</span>     
  	</div>
    <img style="margin-right:60em;" class="loader" src="<?= get_template_directory_uri() . "/images/loading.gif"?>" hidden>
    <div class="main-content">
  	<h1 class="article_page_title">Update Sales Representative Information</h1>
      <form id="update_sales_rep" method="POST" action="">
        <table class="data small">
          <tbody>
              <tr>
                <td class="label right">
                  First Name
                </td>
                <td>
                 <input type="text" name="first_name" size="35" value="<?= $first_name ?>">
                </td>
              </tr>
              <tr>
                <td class="label right">
                  Last Name
                </td>
                <td>
                 <input type="text" name="last_name" size="35" value="<?= $last_name ?>">
                </td>
              </tr> 
              <tr>
                <td class="label right">
                  Email
                </td>
                <td>
                  <input type="text" name="email" id="email" value="<?= $email ?>" size="35">
                </td>
              </tr>  
              <tr>
                <td class="label right">
                  New Password
                </td>
                <td>
                  <input type="text" name="password" size="35">
                </td>
              </tr>
              <tr>
                <td class="right" colspan=2>
              	<input type="submit" class="update_sales" value="Submit Changes">
                <input type="hidden" name="action" value="updateCreateSalesRep">
                <input type="hidden" name="create_user" value="0">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                </td>
              </tr>
          </tbody>
        </table>
      </form>
    </div>
    <script>
      $ = jQuery;

      $("#update_sales_rep").submit(function(e) {

	    //set up the ajax call parameters
	    var url =  ajax_object.ajax_url;

	    //show loader gif and hide the content
	    $('.main-content, .loader').toggle();

	    //ajax call to update the fields
	    $.ajax({
	      type: "POST",
	      url: url,
	      dataType: 'json',
	      data: $("#update_sales_rep").serialize(),
	      success:
	      function(data)
	      {
            if (!data['status'])
            {
              alert(data['message']);
              location.reload();
            }
            else
            {
              alert("SUCCESS!");
              window.location.href = '/dashboard/?part=manage_sales_rep';
            }
	      }
	    });

      e.preventDefault(); // avoid to execute the actual submit of the form.
      
      });
    </script>
<?php
  }
?>