<?php
	$admin_ajax_url = admin_url('admin-ajax.php');
	// depending on which page we are on, assign $part that part from the URL
	if (isset($_REQUEST['part']))
	{
		$part = filter_var($_REQUEST['part'], FILTER_SANITIZE_STRING);
	}
	else
	{
		$part = 'dashboard';
	}

	if( current_user_can( "is_director" ) )
	{
		$helpVideos = getHelpForView($part, "manager"); // All views for the director in dashboard (director role is "manager").
	}
	else if ( current_user_can( "uber_manager" ) )
	{
		$helpVideos = getHelpForView($part, "uber_manager"); // All views for the uber_manager in dashboard
	}
	else if ( current_user_can( "umbrella_manager" ) )
	{
		$helpVideos = getHelpForView($part, "Umbrella_manager"); // All views for the umbrella_manager in dashboard 
	}
	else if ( current_user_can( "administrator" ) )
	{
		$helpVideos = getHelpForView($part, "administrator"); // All views for the administrator in dashboard 
	}
	else
	{
		$helpVideos = getHelpForView($part, "student"); // All views for the student.
	}
	// Do not display video button if there aren't any.
	if( isset($helpVideos) && count($helpVideos) > 0 )
	{
?>
		<!-- help button -->
		<div id="helpBtn" href="#helpbar">
			<a href=""><img src="<?= get_template_directory_uri() . '/images/help_btn_text.png'?>" alt="Help"></a>
		</div>

		<div id="helpbar">
			<link rel="stylesheet" href="<?= get_template_directory_uri() . "/css/helpcenter/reset.css" ?>" type="text/css" media="screen" />
			<link rel="stylesheet" href="<?= get_template_directory_uri() . "/css/helpcenter/style.css" ?>" type="text/css" media="screen" />
			<script type="text/javascript">
				window.anim_finished = true;
				window.anim_speed = 400;

				jQuery(function($)
				{   
				$(document).ready(function()
				{           
				  //$("#grayArea").height(Math.max($(window).height(), $(document).height(), document.documentElement.clientHeight));
				  
				  $("span[rel*=facebox]").click(function()
				  {
				    parent.toggle_help_center();
				    parent.open_facebox($(this).attr('video_id'));
				    return false;
				  });
				  
				  $("#grayArea li a.topic").each(function()
				  {
				    $(this).click(function()
				    {
				      if (window.anim_finished == false)
				        return false;
				      else
				        window.anim_finished = false;
				      
				      $("#grayArea li a.topic").each(function()
				      {
				        if ($(this).hasClass("inactive"))
				        {
				          $(this).slideDown(window.anim_speed, function()
				          {
				            $(this).removeClass("inactive");
				          }).next().slideUp(window.anim_speed);
				          return false;
				        }
				      });
				      
				      $(this).slideUp(window.anim_speed, function()
				      {
				        $(this).addClass("inactive");
				      }).next().slideDown(window.anim_speed);
				      setTimeout(function() { window.anim_finished = true; }, window.anim_speed + 50);
				      return false;
				    });
				  })
				});
				});
			</script>

          <div id="help_center">
            <div id="grayArea">
              <h1 id="helpTitle">
                <span class="orange"><?= __("Help", "EOT_LMS") ?></span> <?= __("24/7", "EOT_LMS") ?> 
              </h1>
              <ul>
                <div class="separator"></div>
<?php
				// Goes to each help video for director in the dashboard.
                foreach ($helpVideos as $video) 
                {
?>
					<li>
						<a href="#" alt="<?= $video['title'] ?>" class="topic"><?= $video['title'] ?> <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
						<a href="#" alt="Summary" class="summary" style="display: none;">
		                    <?= __($video['summary'], "EOT_LMS") ?>
		                    <br />
		                    <br />
		                    <span class="link" video_id="<?= $video['topic_id'] ?>" rel="facebox">
		                    	<?= __("Watch Tutorial", "EOT_LMS") ?>
		                    </span>
	                  	</a>
	                </li>
	                <div class="separator"></div>
<?php
                }
?>
              </ul>
            </div><!--end grayArea-->
          </div><!--end container--> 
		</div>
	    <script src="<?php echo get_template_directory_uri() . '/js/jquery.pageslide.js' ?>"></script>
		<script>
		   /*  
			* This handles the facebox to open when the watch tutorial is clicked on the help center.
	 		*/
			function open_facebox(video_id)
			{
				window.video_id = video_id;
				
				jQuery.facebox(function()
				{
					jQuery.ajax(
					{
						url: '<?= $admin_ajax_url ?>?action=displayHelp&video_id=' + window.video_id,
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

