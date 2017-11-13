<?php
	global $current_user;
	$user_id = $current_user->ID;
  	$first_name = get_user_meta($user_id, "first_name", true);	// First name
  	$last_name  = get_user_meta($user_id, "last_name", true);	// Last name
	$full_name = $first_name . " " . $last_name;	// Full name of user in wordpress db wp_users
	$email_address = $current_user->user_email;			// Email address in wordpress db wp_users
?>
<div id="col2" class="color2 sidebar">
	<div class="module">
		<div class="module-body">

			<?php if (is_user_logged_in ()) { ?>
				<div id="sidebar">
					<div id="accountArea">
						<div id="accountAreaTop">
							<div class="profile_pic">
								<a href="<?php echo bloginfo('url'); ?>/dashboard/?part=my_account#profile_picture">
									<?php echo get_avatar ($email_address, 200)?>
								</a>
							</div>
							<h1>
								<?php echo 'Hi ' . $full_name; ?>
							</h1>
						</div>
						<div class="clear"></div>
						<div id="accountAreaBottom">
							<ul>
								<li>
									<a href="<?php echo bloginfo('url'); ?>/dashboard/?part=my_account"><?= __("Account Settings", "EOT_LMS") ?>
									</a>
								</li>
<?php
								if(current_user_can ('is_student') || current_user_can ('is_director'))
								{
?>
<!-- HIDE CERTIFICATES UNTIL FIX IMAGEMAGICK
									<li><a href="<?php echo bloginfo('url'); ?>/dashboard/?part=mycertificates"><?= __("My Certificates", "EOT_LMS") ?></a></li>
-->
<?php
								}
?>

							<?php 
								if(current_user_can ('is_director'))
								{
							?>
									<li><a href="<?php echo bloginfo('url'); ?>/dashboard/?part=view_invoice"><?= __("View Invoice(s)", "EOT_LMS") ?></a></li>
									<!-- <li><a href="<?php echo bloginfo('url'); ?>/dashboard/?part=view_statistics">Statistics History</a></li>
									<li><a href="<?php echo bloginfo('url'); ?>/dashboard/?part=my_account#subscription_settings">Advance Training & Certification</a></li>
									-->
							<?php 
								}
							?>
						
							</ul>
						<?php 
							// Show only for camp directors
							if(current_user_can( "is_director" ))
							{
						?>
								<div id="helpcenter-menu" href="#helpbar">
									<a href="#">
										<span class="orange"><?= __("Help", "EOT_LMS") ?></span> <?= __("24/7", "EOT_LMS") ?>
									</a>
								</div>
						<?php
							}
						?>
						</div>
					<?php 
					    /*
						* The listOfCourses div is populdated in the part-default.php
						*/ 
					?>
						<div id="listOfCourses">
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div id="sidebar">
					<div id="accountArea">
						<h1 class="guest"><?= __("Main Menu", "EOT_LMS") ?></h1>
						<hr>
						<?php wp_nav_menu (array('theme_location' => 'sidebar', 'container' => '', 'menu_class' => 'guest'));?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<div class="sidebar_bg"></div>