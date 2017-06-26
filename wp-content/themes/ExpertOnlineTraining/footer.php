<?php
/**
 * The theme footer
 * 
 * @package bootstrap-basic
 */	
	global $eot_login_url, $eot_dashboard_url, $eot_logout_url, $current_user;
?>
<link rel="stylesheet" href="<?= get_template_directory_uri() . '/footer-css.css' ?>"/>
<div class="fl-row fl-row-full-width fl-row-bg-color fl-node-57a8e404b8e3e" data-node="57a8e404b8e3e">
	<div class="fl-row-content-wrap">
		<div class="fl-row-content fl-row-fixed-width fl-node-content">
			<div class="fl-col-group fl-node-57a8e3e10af22" data-node="57a8e3e10af22">
				<div class="fl-col fl-node-57a8e3e10af7d" data-node="57a8e3e10af7d" style="width: 100%;">
					<div class="fl-col-content fl-node-content">
						<div class="fl-module fl-module-menu fl-node-57a8e3e10afcd" data-node="57a8e3e10afcd" data-animation-delay="0.0">
							<div class="fl-module-content fl-node-content">
								<div class="fl-menu fl-menu-accordion-collapse">
									<div class="fl-clear">
									</div>
									<?php wp_nav_menu (array('theme_location' => 'footer', 'container' => '', 'menu_class' => 'menu fl-menu-horizontal fl-toggle-none'));?>
								</div>
							</div>
						</div>
						<div class="fl-module fl-module-separator fl-node-57a8fefe746bc" data-node="57a8fefe746bc" data-animation-delay="0.0">
							<div class="fl-module-content fl-node-content">
								<div class="fl-separator">
								</div>
							</div>
						</div>
						<div class="fl-module fl-module-rich-text fl-node-57a8e3e10b01d" data-node="57a8e3e10b01d" data-animation-delay="0.0">
							<div class="fl-module-content fl-node-content">
								<div class="fl-rich-text">
									<p style="text-align: center;"><span style="font-size: 14px;">© Copyright 2008 - <?= date("Y"); ?> <a href="http://www.campspirit.com/" target="_blank">CampSpirit, LLC</a> and <a href="http://www.targetdirectories.com/" target="_blank">Target Directories Corp</a>. All rights reserved.</span></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- 				<div id="footer">
					<div class="module">
						<div class="module-body">
							<?php wp_nav_menu (array('theme_location' => 'footer', 'container' => '', 'menu_class' => 'footer-menu'));?>
							<div class="clear"></div>
							&copy; Copyright 2008-<?php echo date('Y'); ?> <a href="http://www.campspirit.com" target="_blank">CampSpirit, LLC</a> and <a href="http://www.targetdirectories.com" target="_blank">Target Directories Corp</a>. All rights reserved.
						</div>
					</div>
				</div><!-- footer --> -->
				<?php wp_footer(); ?> 
			</div><!-- wrapper -->
		</div><!-- main -->
	  	<?php
	  			getLoadingDiv("load_video", "Loading Video", "Please wait while we load the contents of your video..."); // Loading message for load video
	  			getLoadingDiv("load_view_library", "Loading Library", "Please wait while we load your library..."); // Loading message for view library
	  			getLoadingDiv("load_loading", "Loading", " "); // Loading message for manage courses
	  			getLoadingDiv("load_manage_staff_accounts", "Loading your staff", "Please wait while we get your staff accounts..."); // Loading message for manage courses
	  			getLoadingDiv("load_courses", "Loading your courses", "Please wait while we process your courses..."); // Loading message for manage courses
	  			getLoadingDiv("load_statistics", "Loading the statistics page", "Please wait while we process your statistics..."); // Loading div for administration
	  			getLoadingDiv("load_administration", "Loading the administrator page", "Please wait while we load your administration page..."); // Loading div for administration
	  			getLoadingDiv("load_dashboard", "Loading your dashboard", "Please wait while we load your dashboard..."); // Loading div for administration
	  			getLoadingDiv("load_email", "Loading your recepients", "Please wait while we load your recepients..."); // Loading div for loading recepients
	  			getLoadingDiv("load_email_send", "Sending Message", "Please wait while we send your message to the selected staff members..."); // Loading div for sending e-mail message
	  			getLoadingDiv("load_staff_lounge", "Loading Staff Lounge", "Please wait while we load the staff lounge..."); // Loading div for sending e-mail message
	  	?>
	  	<script>
			$ = jQuery;
			// Process the loading screen when switching pages 
			function load(messageDisplay)
			{
				$.facebox($("#"+messageDisplay).html());
			};

			function unload(messageDisplay)
			{
				$("#"+messageDisplay).hide();
			}
		</script>
		<?php include_once ('help_center.php'); // The help center: help_center.php ?>
	</body>
</html>