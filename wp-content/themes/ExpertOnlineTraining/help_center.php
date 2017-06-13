<?php
	$admin_ajax_url = admin_url('admin-ajax.php');
	if( current_user_can( "is_director" ) )
	{
?>
		<!-- help button -->
		<div id="helpBtn" href="#helpbar">
			<a href=""><img src="<?= get_template_directory_uri() . '/images/help_btn_text.png'?>" alt="Help"></a>
		</div>

		<div id="helpbar">
            <iframe frameBorder="0" scrolling="yes" src="<?= $admin_ajax_url ?>?action=getDashboard&option=com_helpcenter&format=raw&sender=dashboard">
            </iframe>   
		</div>
	    <script src="<?php echo get_template_directory_uri() . '/js/jquery.pageslide.js' ?>"></script>
		<script>
	   /*  
		* This handles the facebox to open when the watch tutorial is clicked on the help center.
 		*/
		function open_facebox(help_function)
		{
			window.help_function = help_function;
			
			jQuery.facebox(function()
			{
				jQuery.ajax(
				{
					url: '<?= $admin_ajax_url ?>?action=displayHelp&help_name=' + window.help_function,
					success: function(data)
					{
						jQuery.facebox(data);
					}
				});
			});
		}
		function toggle_help_center()
		{
			jQuery("#helpBtn a").click();
		}
		jQuery(function($)
		{
			$(document).ready(function()
			{
				// This is the effect to make the help button bounce and attached page slider to the buttons.
				$("#helpBtn").effect( "bounce",  { times: 3, distance:50 }, "slow" );
				$("#helpBtn").pageslide({ href: '#helpbar' });
				// Handles the image to switch the arrow direction for the help button.
				$( "#helpBtn" ).click(function() {
				  if ($('#helpBtn img').attr("src") == "<?= get_template_directory_uri() . '/images/help_btn_text.png'?>") 
				  {
				  	jQuery("#helpBtn img").attr("src", "<?= get_template_directory_uri() . '/images/help_btn_text2.png'?>");
				  }
				  else
				  {
				  	jQuery("#helpBtn img").attr("src", "<?= get_template_directory_uri() . '/images/help_btn_text.png'?>");
				  }
				});
				// Makes the helpcenter menu triggered the help button.
				$( "#helpcenter-menu" ).click(function(event) 
				{
					 event.preventDefault();
					$("#helpBtn").click();
				});
			});
	  	});
		</script>
<?php
	}	


?>