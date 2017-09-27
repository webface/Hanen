<?php 
  global $current_user;

  /* 
  * User Info
  */
  $user_id    = $current_user->ID;                                // Wordpress account user id
  $email      = $current_user->user_email;                        // Wordpress account email address
  $first_name = get_user_meta($user_id, "first_name", true);      // First name
  $last_name  = get_user_meta($user_id, "last_name", true);       // Last name
?>
<h1 class="article_page_title">My Account</h1>
<?php
  // Process when user update the profile.
  if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submitted'] == true)
  {
    $email = $_POST['email'];
    $user_info =  array(
                    'ID' => $user_id,
                    'user_email' => $email,
                  );    
    $result = wp_update_user( $user_info );
    // Check for errors when updating the user in wordpress.
    if( is_wp_error( $result ) )
    {
      displayError ($result->get_error_message());
    }
    else
    {
      $first_name = filter_var($_REQUEST['first_name'],FILTER_SANITIZE_STRING);
      $last_name = filter_var($_REQUEST['last_name'],FILTER_SANITIZE_STRING);
      $password = $_POST['pw1'];

      // Change to new password.
      if( !empty($password) )
      {
        wp_set_password( $password, $user_id );
      }
      // Update user meta in wordpress.
      update_user_meta( $user_id, 'first_name', $first_name);
      update_user_meta( $user_id, 'last_name', $last_name);

      echo "Your account has been updated.<br><br>";
    }
  }

  // Function to display the error message on top of the form.
  function displayError( $message ) 
  {
?>
    <div class="errorboxcontainer_no_width">
      <div class="error-tl">
        <div class="error-tr">
          <div class="error-bl">
            <div class="error-br">
              <div class="errorbox">
                Error: <?= $message ?>
              </div>
            </div>
          </div>
        </div>
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

<!--
<h1 class="article_page_title" ><a name="subscription_settings">Subscription Settings</a></h1>
-->
<script>
  $ = jQuery;

  $(document).ready(function() {
      // On page load. This populates the fields for email, name and last name.
      $('#email').val("<?= $email; ?>");
      $('#first_name').val("<?= $first_name; ?>");
      $('#last_name').val("<?= $last_name; ?>");
      $('#accountAreaTop h1').html("Hi  <?= $first_name; ?> <?= $last_name; ?>");
     
  });

  /* Stops submitting the form if there's an error. */
  $('#updateProfile').submit(function() 
  {
    var password = $("#pw1").val();
    var confirmPassword = $("#pw2").val();
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
    if(strengthNumber < 2)                                             //3 means Good which is required for this field
    {
      alert("Password needs to be at least Good.");
      return false;                                                    //method reutrns true when password is Good
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