<?php
	/**
	 * Template Name: Unsubsribe Page
	 *
	 * @package WordPress
	 */
	get_header();
	?> 
	<style>
	.btn {
	  background: #3498db;
	  background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
	  background-image: -moz-linear-gradient(top, #3498db, #2980b9);
	  background-image: -ms-linear-gradient(top, #3498db, #2980b9);
	  background-image: -o-linear-gradient(top, #3498db, #2980b9);
	  background-image: linear-gradient(to bottom, #3498db, #2980b9);
	  -webkit-border-radius: 28;
	  -moz-border-radius: 28;
	  border-radius: 28px;
	  font-family: Arial;
	  color: #ffffff;
	  font-size: 20px;
	  padding: 10px 20px 10px 20px;
	  text-decoration: none;
	}

	.btn:hover {
	  background: #3cb0fd;
	  background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
	  background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
	  background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
	  background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
	  background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
	  text-decoration: none;
	}
	</style>
	<div id="main-content" class="s-c-x">
		<div id="colmask" class="ckl-color2">
			<div id="colmid" class="cdr-color1">
				<div id="colright" class="ctr-color1">
					<?php get_sidebar (); ?>
					<div id="col1wrap">
						<div id="col1pad">
							<div id="col1">
								<div class="component-pad">
									<h1 class="article_page_title"><?= __("Unsubsribe", "EOT_LMS") ?></h1>
<?php
if( isset($_REQUEST['email']) && isset($_REQUEST['sec']) )
{
	$ip = do_shortcode( '[show_ip]' ); // User IP Address
	$user_email = filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL); // user email address
	$key = filter_var($_REQUEST['sec'], FILTER_SANITIZE_STRING); // hash email address and ID
	$unsubsribe = $wpdb->get_row ("SELECT * from " . TABLE_UNSUBSCRIBE . " where email = '$user_email'");
	if(!$unsubsribe)
	{
		$user = get_user_by( 'email', $user_email );
		if($user) // Check if the e-mail is a valid user.
		{
			if($key == wp_hash( $user->ID . $user_email )) // Check if the hash key is valid.
			{
				if( isset($_REQUEST['answer']) && $_REQUEST['answer'] == "unsubsribe") 
				{
					global $wpdb;
					$wpdb->insert( 
						TABLE_UNSUBSCRIBE, 
						array( 
							'email' => $user_email, 
							'ip' => $ip,
							'date' => current_time('mysql', 1)
						), 
						array( 
							'%s', 
							'%s',
							'%s'
						) 
					);
					echo __("You have been unsubscribed from our mailing list.", "EOT_LMS");
				}
				else
				{
?>
					<h2><u><?= __("We are sorry to see you go!", "EOT_LMS") ?></u></h2>
					<?= __("To unsubscribe from Expert Online Training helpful emails, simply click the button below. Please note that you can still receive email from your camp director.", "EOT_LMS") ?>
					<br><br>
					<a class="btn" href="?email=<?=$user_email?>&sec=<?=$key?>&answer=unsubsribe"><?= __("Unsubsribe", "EOT_LMS") ?></a>
<?php
				}
			} // Invalid Key.
			else
			{
				echo __("Invalid Request.", "EOT_LMS");
			}
		}
		else
		{
			echo __("User does not exist.", "EOT_LMS");
		}
	} // User has already been subscribed.
	else
	{
		echo __("You have already been unsubsribed. Please note that your camp director can still sends you an e-mail.", "EOT_LMS");
	}
} // Invalid parameters.
else
{
	echo __("Invalid Request.", "EOT_LMS");
}
?>

								</div>
							</div>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
	</div>
<?php get_footer(); ?> 
