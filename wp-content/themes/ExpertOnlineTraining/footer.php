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
		getLoadingDiv("load_video", __("Loading Video", "EOT_LMS"), __("Please wait while we load the contents of your video...", "EOT_LMS") ); // Loading message for load video
		getLoadingDiv("load_view_library", __("Loading Library", "EOT_LMS"), __("Please wait while we load your library...", "EOT_LMS") ); // Loading message for view library
		getLoadingDiv("load_loading", __("Loading", "EOT_LMS"), __(" ", "EOT_LMS") ); // Loading message for manage courses
		getLoadingDiv("load_manage_staff_accounts", __("Loading your staff", "EOT_LMS"), __("Please wait while we get your staff accounts...", "EOT_LMS") ); // Loading message for manage courses
		getLoadingDiv("load_courses", __("Loading your courses", "EOT_LMS"), __("Please wait while we process your courses...", "EOT_LMS") ); // Loading message for manage courses
		getLoadingDiv("load_statistics", __("Loading the statistics page", "EOT_LMS"), __("Please wait while we process your statistics...", "EOT_LMS") ); // Loading div for administration
		getLoadingDiv("load_administration", __("Loading the administrator page", "EOT_LMS"), __("Please wait while we load your administration page...", "EOT_LMS") ); // Loading div for administration
		getLoadingDiv("load_dashboard", __("Loading your dashboard", "EOT_LMS"), __("Please wait while we load your dashboard...", "EOT_LMS") ); // Loading div for administration
		getLoadingDiv("load_email", __("Loading your recepients", "EOT_LMS"), __("Please wait while we load your recepients...", "EOT_LMS") ); // Loading div for loading recepients
		getLoadingDiv("load_email_send", __("Sending Message", "EOT_LMS"), __("Please wait while we send your message to the selected staff members...", "EOT_LMS") ); // Loading div for sending e-mail message
		getLoadingDiv("load_staff_lounge", __("Loading Staff Lounge", "EOT_LMS"), __("Please wait while we load the staff lounge...", "EOT_LMS") ); // Loading div for sending e-mail message
		getLoadingDiv("load_upload_file", __("Loading File Uploader", "EOT_LMS"), __("Please wait while we load the file uploader...", "EOT_LMS") );
		getLoadingDiv("load_quiz", __("Loading the quiz page", "EOT_LMS"), __("Please wait while we load the quizzes...", "EOT_LMS") ); // Loading div for sending e-mail message
		getLoadingDiv("load_edit_quiz", __("Loading the quiz page", "EOT_LMS"), __("Please wait while we load the edit page...", "EOT_LMS") ); // Loading div for sending e-mail message
		getLoadingDiv("load_edit_module", __("Loading the module page", "EOT_LMS"), __("Please wait while we load the edit page...", "EOT_LMS") ); // Loading div for sending e-mail message
		getLoadingDiv("load_processing", __("Processing your file", "EOT_LMS"), __("Please wait ...", "EOT_LMS") );
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