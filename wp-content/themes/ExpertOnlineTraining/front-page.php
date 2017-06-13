<?php get_header (); ?>
<div id="showcase" class="dp100">
	<div class="background"></div>
	<div class="foreground">
		<div id="showcase_container">
			<div id="bannershowcase">
				<div class="featured_video">
					<div id='player123' style='width:400px;height:225px'>
					  	<video id="my-video" class="video-js vjs-default-skin" controls preload="auto" width="400" height="225" poster="<?php echo bloginfo('template_directory'); ?>/images/webpromo_2016.jpg" data-setup='{"controls": true}'>
							<source src="https://eot-output.s3.amazonaws.com/webpromo_2015.mp4" type='video/mp4'>4
							<p class="vjs-no-js">
							To view this video please enable JavaScript, and consider upgrading to a web browser that
							<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
							</p>
					  	</video>
				  	</div>
				</div>
				<div class="featured_text">
					<p>
						Because nothing is more important than the relationship between your staff and the children they serve, we offer the finest online content for youth development professionals.
					</p>
					<p>
						Our internationally renowned faculty has created nearly 100 innovative videos that will teach your staff how to supervise, lead and nurture healthy relationships.
					</p>
					<a href="#" id="watch-first" title="Watch Video" onclick="$f('player123').toggle();">Watch Video First</a>
					<a href="<?php bloginfo ('url'); ?>/sample-videos" title="Watch Sample Videos" class="sample-videos">Sample Videos</a>
				</div>
				<div class="clear"></div>
			</div>
			<div class="slider_wrapper">
				<p>
					Meet Our Faculty
				</p>
				<div class="staff_slider_container">
					<div class="faculty_reel">
						<?php 
						$presenters = get_posts (array ('post_type' => 'presenter', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1));
						foreach ($presenters as $presenter) { 
							$image = wp_get_attachment_image_src (get_post_thumbnail_id ($presenter->ID), 'single-post-thumbnail'); ?>
							<div>
								<img src="<?php echo $image[0]; ?>" alt="<?php echo ucwords ($presenter->post_title); ?>" />
								<a href="<?= get_home_url() ?>/presenter-bios/#<?php echo $presenter->post_name; ?>" class="caption"><?php echo ucwords ($presenter->post_title); ?></a>
							</div>
						<?php } ?>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery('.faculty_reel').slick({
								centerMode: true,
								centerPadding: '5px',
								slidesToShow: 9,
								focusOnSelect: true,
								prevArrow: '<a href="#" class="slick-prev">Previous"</a>',
								nextArrow: '<a href="#" class="slick-next">Next"</a>',
								responsive: [ 
									{
										breakpoint: 700,
										settings: {
											centerMode: true,
											centerPadding: '5px',
											slidesToShow: 7
										}
									},
									{
										breakpoint: 576,
											settings: {
											centerMode: true,
											centerPadding: '5px',
											slidesToShow: 5
										}
									},
									{
										breakpoint: 400,
											settings: {
											centerMode: true,
											centerPadding: '5px',
											slidesToShow: 3
										}
									}
								]
							});
						});
					</script>
				</div>
			</div>
		</div>
		<div id="home_content">
			<div id="feedjit-left-box">
				<!--<img id="feedjit-box-header" src="/images/live-traffic-feed.jpg"></img>-->
				<div id="feedjit-container">
					<div id="feedjit-box-top">Live Traffic Feed</div>
					<script type="text/javascript" src="http://feedjit.com/serve/?vv=955&amp;tft=3&amp;dd=bbfc2aee118ad63c11bf3b8d03bd72c1d684bb3d63297b0c8a5b84b57236f306&amp;wid=70da2910d3b74892&amp;pid=de1faf9727040923&amp;proid=3b1a5cce3de39566&amp;bc=E7E7E7&amp;tc=555555&amp;brd1=FFFFFF&amp;lnk=2B7FC9&amp;hc=FFFFFF&amp;hfc=1a81c6&amp;btn=1a81c6&amp;ww=200&amp;wne=5&amp;wh=fjnone&amp;hl=1&amp;hlnks=1&amp;hfce=1&amp;srefs=0&amp;hbars=1"></script>
					<noscript>
						<a href="http://feedjit.com/">Feedjit Live Blog Stats</a>
					</noscript>
				</div>
			</div>
			<div id="homecontent" class="plus-feedjit">
				<h1 class="article_page_title shrink-for-feedjit" id="homeh1">Online Summer Camp Staff Training</h1>
				<p>
					With Expert Online Training, staff training starts today! We are proud to offer three customizable libraries of training materials—videos, flash presentations, quizzes, and handouts—to jumpstart your training. 
					<br />
					<br />
					<b>Leadership Essentials</b>, features nearly 100 videos that teach critical leadership skills every staff member needs to effectively deal with intense social, behavioral, disciplinary, and supervisory challenges. 
					<br />
					<br />
					<b>Safety Essentials</b>, designed by child psychologist Dr. Christopher Thurber and attorney Jack Erler, is an extended course on child welfare and protection. Completing the course satisfies the requirement in many states and provinces.
					<br />
					<br />
					<b>Clinical Essentials</b>, designed by Dr. Chris Crean and Dr. Skip Walton, this advanced content library provides practical clinical guides and accessible reference material for nurses, physicians, EMT’s and PA’s who are treating youth in outdoor settings.
				</p>
				<center>
					<img src="<?php bloginfo ('stylesheet_directory'); ?>/images/associations.gif" alt="Camp Associations" style="margin-left: -110px;" />
				</center>
			</div>
			<script type="text/javascript">
				function swaptestimonial(id)
				{
					if (id == 1)
					{
						document.getElementById('testimonial1').style.display = 'block';
						document.getElementById('testimonial2').style.display = 'none';
						document.getElementById('testimonial3').style.display = 'none';
					}
					else if (id == 2)
					{
						document.getElementById('testimonial1').style.display = 'none';
						document.getElementById('testimonial2').style.display = 'block';
						document.getElementById('testimonial3').style.display = 'none';
					}
					else if (id == 3)
					{
						document.getElementById('testimonial1').style.display = 'none';
						document.getElementById('testimonial2').style.display = 'none';
						document.getElementById('testimonial3').style.display = 'block';
					}
				}
			</script>
			<div class="rightbox" id="testimonialbox">
				<div id="testimonial-top">Testimonials</div>
				<div class="rightbox-body">
					<div class="content">
						<div class="tm" id="testimonial1">
							"DECA and the American Diabetes Association (ADA) are collaborating on this very exciting project [to subscribe to Leadership Essentials] and I believe it will be such an asset to all participating diabetes camps.  Thanks for the amazing things you do for all camps and the kids we serve. I look forward to having our American Diabetes Assocation staff use your online counselor training program."							<br />
							<br />
							<table>
								<tr>
									<td><img src="<?php bloginfo ('stylesheet_directory'); ?>/images/testimonials/adacamp-small.jpg" alt="Shana Funk" /></td>
									<td class="tm_details">
										<b>Shana Funk</b>
										<br />
										Youth Initiatives / American Diabetes Assocation
										<br />
										<a href="http://www.diabetes.org/living-with-diabetes/parents-and-kids/ada-camps/">www.diabetes.org/living-with-diabetes/parents-and-kids/ada-camps/</a>
									</td>
								</tr>
							</table>
						</div>
						<div class="tm" id="testimonial2">
							"With more and more mandated staff training, and less and less time to accomplish it prior to camp, <strong>this series is a godsend</strong>!  The videos are well-produced, authentic, succinct, with relevant and appropriate content.  I love the <strong>manageability and online convenience</strong> -- for me and for my staff."							<br />
							<br />
							<table>
								<tr>
									<td><img src="<?php bloginfo ('stylesheet_directory'); ?>/images/testimonials/karen-rosenbaum-small.jpg" alt="Karen Rosenbaum" /></td>
									<td class="tm_details">
										<b>Karen Rosenbaum</b>
										<br />
										Owner/Director, TIC Summer Camp
										<br />
										<a href="http://www.ticcamp.com">www.ticcamp.com</a>
									</td>
								</tr>
							</table>
						</div>
						<div class="tm" id="testimonial3">
							"One of the strengths of Leadership Essentials is the ability to use the videos as part of staff training<strong> before our first day</strong> of staff training at camp.  All of the staff are required to watch specific videos and take the online quizzes to verify that they understand the principles being taught.  This gives us a <strong>head start</strong> before new hires arrive at camp for our pre-camp..." <a href="/testimonials/">Read entire testimonial</a>							<br />
							<br />
							<table>
								<tr>
									<td><img src="<?php bloginfo ('stylesheet_directory'); ?>/images/testimonials/george_paula_detellis-small.jpg" alt="George & Paula DeTellis" /></td>
									<td class="tm_details">
										<b>George & Paula DeTellis</b>
										<br />
										Directors, Camp Woodhaven
										<br />
										<a href="http://www.campwoodhaven.com">www.campwoodhaven.com</a>
									</td>
								</tr>
							</table>
						</div>
						<div class="swapper">
							<a href="#" onclick="swaptestimonial(1); return false;">
								<img class="rotatorbutton" src="<?php bloginfo ('stylesheet_directory'); ?>/images/testimonials/1.gif" alt="1" />
							</a>
							<a href="#" onclick="swaptestimonial(2); return false;">
								<img class="rotatorbutton" src="<?php bloginfo ('stylesheet_directory'); ?>/images/testimonials/2.gif" alt="2" />
							</a>
							<a href="#" onclick="swaptestimonial(3); return false;">
								<img class="rotatorbutton" src="<?php bloginfo ('stylesheet_directory'); ?>/images/testimonials/3.gif" alt="3" />
							</a>
							&nbsp;&nbsp;<a href="/testimonials/">All Testimonials (37)</a>&nbsp;&nbsp;&nbsp;
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer (); ?>