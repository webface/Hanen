<?php
	$admin_ajax_url = admin_url('admin-ajax.php');
	if( current_user_can( "is_director" ) )
	{
?>
		<!-- help button -->
		<div id="helpBtn" href="#helpbar">
			<a href=""><img src="<?= get_template_directory_uri() . '/images/help_btn_text.png'?>" alt="Help"></a>
		</div>

		<div id="helpbar"">
          <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
          
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
                parent.open_facebox($(this).attr('dest'));
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
                <span class="orange">
                  Help
                </span>
                Center
              </h1>
              <ul>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Watch Some Videos" class="topic">Watch Some Videos <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    This page allows you to view the content you have available for your staff members through your purchased subscription.
                    <br />
                    <br />
                    <span class="link" dest="some_videos" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Pick a Default Course" class="topic">Pick a Default Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to manage the default courses for your staff members.            
                    <br />
                    <br />
                    <span class="link" dest="default_course" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Modify a Default Course" class="topic">Modify a Default Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to modify the default courses for your staff members.
                    <br />
                    <br />
                    <span class="link" dest="modify_default" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Create a New Course" class="topic">Create a New Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to create you own custom course for your staff memebers.            
                    <br />
                    <br />
                    <span class="link" dest="create_course" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Publish a Course" class="topic">Publish a Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Before you can enroll your staff in a course, it must be published.        
                    <br />
                    <br />
                    <span class="link" dest="publish_course" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Modify a Published Course" class="topic">Modify a Published Course <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to modify a published course.
                    <br />
                    <br />
                    <span class="link" dest="modify_published" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Add Staff Information" class="topic">Add Staff Information <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" /></a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to add staff and enroll them into courses. 
                    <br />
                    <br />
                    <span class="link" dest="add_staff" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Change Course Enrollment" class="topic">Change Course Enrollment <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to modify user enrollments in specific courses.
                    <br />
                    <br />
                    <span class="link" dest="change_enroll" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Check Statistics" class="topic">Check Statistics <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to get statistics on how your staff is progressing through their assigned courses.
                    <br />
                    <br />
                    <span class="link" dest="check_stats" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Upload Custom Content" class="topic">Upload Custom Content <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to upload your own custom content so that your staff has access to it.
                    <br />
                    <br />
                    <span class="link" dest="upload_custom" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
                <div class="separator"></div>
                <li>
                  <a href="#" alt="Create a Custom Quiz" class="topic">Create a Custom Quiz <img src="<?= get_template_directory_uri() . "/images/down_arrow.png"?>" class="downArrow" />
                  </a>
                  <a href="#" alt="Summary" class="summary" style="display: none;">
                    Use this feature to create custom quizes for your staff.
                    <br />
                    <br />
                    <span class="link" dest="create_custom_quiz" rel="facebox">
                      Watch Tutorial
                    </span>
                  </a>
                </li>
              </ul>
            </div><!--end grayArea-->
          </div><!--end container--> 
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
				  	jQuery("#helpBtn").css('margin-left', "-8px");
				  	jQuery("#helpBtn img").attr("src", "<?= get_template_directory_uri() . '/images/help_btn_text2.png'?>");
				  }
				  else
				  {
				  	jQuery("#helpBtn").css('margin-left', "0px");
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