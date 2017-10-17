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
<style>
    .login-branding,.login-form,#nav,#backtoblog,.login-msg{
        width:400px;
        margin:auto;
    }
</style>
<div class="login-branding">
  <a href="#" class="login-logo">Expertonlinetraining.com</a>
  <p class="login-desc">
Lorem Ipsum  </p>
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
    'label_username' => __( 'Username or Email'),
        'label_password' => __( 'Password' ),
        'label_remember' => __( 'Remember Me' ),
        'label_log_in' => __( 'Log In' ),
        'remember' => true
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