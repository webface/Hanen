<?php 
  global $current_user;

  /* 
  * User Info
  */
  $user_id    = $current_user->ID;                                // Wordpress account user ID
  $user_email = $current_user->user_email;                        // Wordpress account email address
  $first_name = get_user_meta($user_id, "first_name", true);      // First name
  $last_name  = get_user_meta($user_id, "last_name", true);       // Last name
  $org_id = get_org_from_user($user_id);
  $admin_ajax_url = admin_url('admin-ajax.php');// Org ID
?>
<h1 class="article_page_title">My Account</h1>
<?php
  // Process when user update the profile.
  if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submitted'] == true)
  {
    $new_email = $_POST['email']; // New E-mail
    // Update first and last name.
    $new_first_name = isset( $_REQUEST['first_name'] ) ? filter_var($_REQUEST['first_name'],FILTER_SANITIZE_STRING) : "";
    $new_last_name = isset( $_REQUEST['last_name'] ) ? filter_var($_REQUEST['last_name'],FILTER_SANITIZE_STRING) : "";
    $new_user_password = isset( $_POST['pw1'] ) ? $_POST['pw1'] : "";
    $user_info =  array(
                    'ID' => $user_id,
                    'user_email' => $new_email,
                    'first_name' => $new_first_name,
                    'last_name' => $new_last_name,
                    'user_pass' => $new_user_password
                  );
    // Disregard not used data in the array.
    if($first_name == $new_first_name)
    {
      unset($user_info['first_name']);
    }
    if($last_name == $new_last_name)
    {
      unset($user_info['last_name']);
    }
    if ($last_name != $new_last_name || $first_name != $new_first_name)
    {
      // they changed the name so change the sidplay name
      $user_info['display_name'] = $new_first_name . ' ' . $new_last_name;
    }
    if($user_email == $new_email)
    {
      unset($user_info['user_email']);
    }
    else
    {
      // email changed so update user nickname and nicename
      $user_info['user_nicename'] = $new_email;
      $user_info['nickname'] = $new_email;
    }
    if($new_user_password == "")
    {
      unset($user_info["user_pass"]);
    }
    // Update user info. This does the check if the e-mail already exist.
    $update_user = wp_update_user( $user_info );
    if( !is_wp_error( $update_user ) )
    {
      // Update user login manually, as it won't update using wp_update_user
      if( $user_email != $new_email )
      {
        global $wpdb;
        $wpdb->update( $wpdb->users, array( 'user_login' => $new_email), array( 'ID' => $user_id) );

        // email address changed. Need to auto login new user
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
      }
      displaySuccess("Your account information has been updated."); 
      // Update display in front end.

?>    <script>
        $(document).ready(function () 
        {
          $("input#first_name").val("<?= $new_first_name; ?>");
          $("input#last_name").val("<?= $new_last_name; ?>");
          $('#email').val("<?= $new_email; ?>");
          $('#accountAreaTop h1').html("Hi  <?= $new_first_name; ?> <?= $new_last_name; ?>");
        })
      </script>
<?php
    }
    else
    {
      // Error in updating user account.
      displayError ($update_user->get_error_message()); // This will also catch errors when the e-mail already exist.
?>
        <script>
          $(document).ready(function () 
          {
            $("input#first_name").val("<?= $first_name; ?>");
            $("input#last_name").val("<?= $last_name; ?>");
            $('#email').val("<?= $user_email; ?>");
            $('#accountAreaTop h1').html("Hi  <?= $first_name; ?> <?= $last_name; ?>");
          })
        </script>
      <?php
    } 
  }

  // Function to display the error message on top of the form.
  function displayError( $message ) 
  {
?>
    <div class="bs">
      <div class="alert alert-danger">
        <strong>Error:</strong> <?= $message ?>
      </div>
    </div>
<?php
  }
  // Function to display the error message on top of the form.
  function displaySuccess( $message ) 
  {
?>
    <div class="bs">
      <div class="alert alert-success">
        <strong>Success!</strong> <?= $message ?>
      </div>
    </div>
<?php
  }
?>


Change your account details here.
<span class="asterisk">*</span> Required fields<br /><br />
<form id="updateProfile" method="post" action="">
  <table class="Tstandard">
    <tr>
      <td class="label">  
        E-mail:<span class="asterisk">*</span>
      </td>
      <td class="field">
        <input type="text" name="email" id="email" value="" size="30" />
      </td>      
    </tr>
    <tr>
      <td class="label">
        First Name:<span class="asterisk">*</span>
      </td>
      <td class="field">
        <input type="text" name="first_name" id="first_name" value="" size="30" />
      </td>
    </tr>
    <tr>
      <td class="label">
        Last Name:<span class="asterisk">*</span>      
      </td>
      <td class="field">
        <input type="text" name="last_name" id="last_name" value="" size="30" />
      </td>
    </tr>
    <tr class="spacer"></tr>
    <tr>
      <td class="label">
        Password:<br><br><br><br><br>
      </td>
      <td class="field">
        <input type="password" oninput="strengthCheck();" name="pw1" id="pw1" value="" size="18" /><br>
        <span>Minimum password strength required: Good</span><br>
        <span>Tips: Min 8 characters. Do not use names or words from the dictionary.</span><br>
        <!-- Password strength indicator -->
        <div class="meter-wrapper"><meter max="4" id="password-strength-meter"></meter></div>
        <p id="password-strength-text">Strength: <b>Bad</b></p>
      </td>
    </tr>
    <tr>
      <td class="label">
        Confirm Password:
      </td>
      <td class="field">
        <input type="password" name="pw2" id="pw2" onkeyup="checkPasswordMatch();" value="" size="18" /> <span id="passwordConfirmation"></span>
      </td>
    </tr>
    <tr>
      <td>
      </td>
      <td class="field">
        <br><br>Need help getting a secure password? Click the generate password link below for some suggestions.<br>
        <a href="#" id="password-generate">Generate Password: &nbsp;&nbsp;</a> <input readonly id="suggested-password" size="18" style="display:inline-block;"> 
      </td>
    </tr>
  </table>
  <input type="hidden" name="action" value="contact_form">
  <input type="hidden" name="submitted" value="1">
  <input type="submit" value="Update Profile">
</form>

<a name="profile-picture" id="profile_picture"></a>
<h1 class="article_page_title" ><a name="subscription_settings">Profile Picture</a></h1>
<?php echo do_shortcode( '[avatar_upload]' ); ?>


<h1 class="article_page_title" ><a name="subscription_settings">Subscription Settings</a></h1>
<script>
 function save_checkbox(org_id) {
    $( "#subscription_settings_result" ).html("");
    $.post( '<?= $admin_ajax_url?>?action=continue_education' , { checked : $('#subscription_settings').is(":checked"), org_id : org_id }, 
       function( response ) {
         //alert(response);
         $( "#subscription_settings_result" ).html( response );
       }
    );
 }
</script>
 
<div id="subscription_settings_result"></div>  <!-- div to hold results from ajax -->
 
<input id="subscription_settings" type="checkbox" value="" checked="<?= get_post_meta($org_id, 'continue_learning', true)?>" onclick="save_checkbox(<?php echo $org_id ?>);";/>&nbsp;&nbsp;Allow staff to continue training after they have completed assigned modules?  
<script>
  $ = jQuery;

  $(document).ready(function() {
<?php 
      if( !isset($_POST['submitted']) )
      {
?>
        // On page load. This populates the fields for email, name and last name.
        $('#email').val("<?= $user_email; ?>");
        $('#first_name').val("<?= $first_name; ?>");
        $('#last_name').val("<?= $last_name; ?>");
        $('#accountAreaTop h1').html("Hi  <?= $first_name; ?> <?= $last_name; ?>");
<?php
      }
?>
      
     
  });

  /* Stops submitting the form if there's an error. */
  $('#updateProfile').submit(function() 
  {
    var password = $("#pw1").val();
    var confirmPassword = $("#pw2").val();
    if(password)
    {
      if (password != "" && confirmPassword == "")
      {
        $("#passwordConfirmation").html("You can't leave this empty");
        return false;
      }
      if (password != confirmPassword)
      {
        alert("Passwords need to match.");
        return false;
      }

      var strengthNumber = passwordStrength(password, [], password);      //this is a wordpress function that will check the strength of the password and will output a number from 0-4
      if(strengthNumber < 2)                                             //2 means Good which is required for this field
      {
        alert("Password needs to be at least Good.");
        return false;                                                    //method reutrns true when password is Good
      }
    }
  });

   /* 
    * This checks if the both password are the same. On KEYUP.
    */
  function checkPasswordMatch() {
      var password = $("#pw1").val();
      var confirmPassword = $("#pw2").val();

      if (password != confirmPassword)
          $("#passwordConfirmation").html("Passwords do not match!");
      else
          $("#passwordConfirmation").html("Passwords match.");
  }

  //Password strength check
  function strengthCheck() 
  {
    var password = $("#pw1").val();                                    //the password
    var strengthNumber = passwordStrength(password, [], password);      //this is a wordpress function that will check the strength of the password and will output a number from 0-4
    if(strengthNumber >= 2)                                             //2 means Good which is required for this field
    {
      result = true;                                                    //method reutrns true when password is strong
    }
    // If strengthNumber goes negative. It will be treated as bad password.
    if(strengthNumber < 0)
    {
      strengthNumber = 0;
    }

    //array for strength wording
    var strength = {
      0: "Bad",
      1: "Weak",
      2: "Good",
      3: "Strong",
      4: "Super Strong"
    };

    //update the strength wording
    $('#password-strength-text').html("Strength: " + "<strong>" + strength[strengthNumber] + "</strong>"); 

    //update the password strength bar
    $('#password-strength-meter').val(strengthNumber);
  }

</script>
<script type="text/javascript">
  (function($){
    $(".subsettings").click(function()
    {
		$(this).parent().addClass("toggled");
        
	    url = 'my-dashboard.html?task=do_ajax&ajax_task=toggle_continue_education&format=ajax';
	    $.post(url,
				"sub_id=" + $(this).attr("sub_id"),
				function(data)
				{
					if(data.success)
					{
						animate($($(".toggled")[0]).children()[2]);
						$($(".toggled")[0]).removeClass("toggled");
		
					}
				},
				"json"
			);
    });

	function animate(message)
	{
		$(message).fadeIn(500,
			function()
			{
				$(this).fadeTo(1000, 1,
					function()
					{
						$(this).fadeOut(500);
					}
				);
			}
		);
	};
  })(jQuery); 
</script>