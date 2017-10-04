<?php
$login  = (isset($_GET['login']) ) ? $_GET['login'] : 0;
if ( $login === "failed" ) {
  echo '<p class="login-msg"><strong>ERROR:</strong> Invalid username and/or password.</p>';
} elseif ( $login === "empty" ) {
  echo '<p class="login-msg"><strong>ERROR:</strong> Username and/or Password is empty.</p>';
} elseif ( $login === "false" ) {
  echo '<p class="login-msg"><strong>ERROR:</strong> You are logged out.</p>';
}
?>

<div class="login-branding">
  <a href="#" class="login-logo">Expertonlinetraining.com</a>
  <p class="login-desc">
    Donec sollicitudin molestie malesuada. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Cras ultricies ligula sed magna dictum porta. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Vivamus suscipit tortor eget felis porttitor volutpat. Pellentesque in ipsum id orci porta dapibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
  </p>
</div>
<div class="login-form">
<?php
    if(ICL_LANGUAGE_CODE=='fr'){
    $redirect  = home_url( '/fr/dashboard' );
    }
    else
    {
    $redirect  = home_url( '/dashboard' );
    }
$args = array(
    'redirect' => $redirect, 
    'id_username' => 'user',
    'id_password' => 'pass',
   ) 
;?>
<?php wp_login_form( $args ); ?>
</div>
<p id="nav">
<a href="http://www.eotv5.dev/register/"><?= __('Register','EOT_LMS')?></a> | 	<a href="http://www.eotv5.dev/wp-login.php?action=lostpassword"><?= __('Lost your password?','EOT_LMS')?></a>
</p>
<p id="backtoblog"><a href="http://www.eotv5.dev/">&larr; <?= __('Back to Expert Online Training','EOT_LMS')?></a></p>
	
	</div>

	
	
	<div class="clear"></div>