<?php
	/**
	 * Template Name: Unsubscribe Page
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
	global $wpdb;
	$ip = get_the_user_ip(); // User IP Address
	$user_email = filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL); // user email address
	$sec = filter_var($_REQUEST['sec'], FILTER_SANITIZE_STRING); // hash email address and ID
	$user = get_user_by( 'email', $user_email );
	if($user) // Check if the e-mail is a valid user.
	{
		if($sec == wp_hash( $user->ID . $user_email )) // Check if the hash key is valid.
		{
			$unsubsribe = getUnsubscribe( $user_email ); // check if the email is already in the unsubscribe table.
			if(!$unsubsribe)
			{
				if( isset($_REQUEST['answer']) && $_REQUEST['answer'] == "unsubsribe") 
				{
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
				else if( isset($_REQUEST['answer']) && $_REQUEST['answer'] == "subsribe") 
				{
					$wpdb->delete( TABLE_UNSUBSCRIBE, array( 'email' => $user_email ) );
					echo __("You have been subscribed to our notifications. Your camp director will now be able to send you notifications.", "EOT_LMS");
				}
				else
				{
?>
					<h2><u><?= __("We are sorry to see you go!", "EOT_LMS") ?></u></h2>
					<?= __("To unsubscribe from Expert Online Training notifications, simply click the button below. Please note that you <b>WILL NOT</b> receive emails from your camp director which may cause you to miss assigned courses.", "EOT_LMS") ?>
					<br><br>
					<a class="btn" href="?email=<?=$user_email?>&sec=<?=$sec?>&answer=unsubsribe"><?= __("Unsubsribe", "EOT_LMS") ?></a>
<?php
				}
			} 
			else 
			{
				// User has already been subscribed. Try to resubscribe them.
				echo __("You have already been unsubsribed. Please note that your camp director <b>WILL NOT</b> be able to send you emails through our system which may cause you to miss assigned courses. To re-subscribe to our notifications, click the link below:", "EOT_LMS");

				// add resubscribe link
				echo '<br><br><a class="btn" href="?email=<?=$user_email?>&sec=<?=$sec?>&answer=subsribe">' . __("Subsribe", "EOT_LMS") . '</a>';
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
