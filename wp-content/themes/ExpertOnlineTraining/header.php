<?php
	global $eot_login_url, $eot_dashboard_url, $eot_logout_url, $current_user;
	wp_get_current_user();

	/* is trying to access register page, redirect to dashboard */
	if (is_page ('register') && is_user_logged_in ()) 
	{
		header ("Location: " . $eot_dashboard_url);
	}
	if (is_page ('dashboard') && !is_user_logged_in ()) 
	{
		header ("Location: " . $eot_login_url);
	}
	if (is_page ('dashboard') && is_user_logged_in())
	{
		//check if the current user is an author
		if (current_user_can ("is_author") || current_user_can ( "manage_options" )) 
		{ 
			wp_redirect(admin_url()); 
			exit; 
		}
		//subscribers are created when visiting the mastery page
		else if(current_user_can( "is_subscriber"))
		{
			wp_redirect("https://www.expertonlinetraining.com/xpress");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >
<?php 
	// ACF Form header will only load on manage_logo as the imported css/js files from this function conflicts with our javascript
	if(isset($_REQUEST['part']) &&  in_array($_REQUEST['part'], $GLOBALS['pages_with_acf_form']))
	{
		acf_form_head();
	} 
?>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta name="robots" content="index, follow" />
		<meta name="keywords" content="camp, staff, training, summer, online, courses, learning, lessons, videos, expert" />
		<meta name="description" content="Summer Camp Staff Training from Expert Online Training provides a summer camp staff training solution with online videos, auto-marked quizzes, activity tracking and expert content to prepare your staff to meet any challenges. These are great summer camp training ideas for counselors." />
		<meta name="generator" content="ExpertOnlineTraining" />
		<title><?php wp_title('|', true, 'right'); ?></title>
		<!--<base href="https://www.expertonlinetraining.com/" />-->
		<link href="/index.php?format=feed&amp;type=rss" rel="alternate" type="application/rss+xml" title="RSS 2.0" />
		<link href="/index.php?format=feed&amp;type=atom" rel="alternate" type="application/atom+xml" title="Atom 1.0" />
		<link rel="stylesheet" href="/wp-content/themes/ExpertOnlineTraining/header-css.css" />
		<link href="/wp-content/themes/ExpertOnlineTraining/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<?php wp_head(); ?>
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-6607087-5']);
			_gaq.push(['_trackPageview']);
			
			(function() {
			  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
			
		</script>

		<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '1636210973279611'); // Insert your pixel ID here.
		fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=1636210973279611&ev=PageView&noscript=1"
		/></noscript>
		<!-- DO NOT MODIFY -->
		<!-- End Facebook Pixel Code -->
		
<?php
/*
		<!-- Start of Woopra Code -->
		<script type="text/javascript">
			function woopraReady(tracker) {
			    tracker.setDomain('expertonlinetraining.com');
			    tracker.setIdleTimeout(1800000);
			    tracker.trackPageview({type:'pageview',url:window.location.pathname+window.location.search,title:document.title});
			    return false;
			}
			(function() {
			    var wsc = document.createElement('script');
			    wsc.src = document.location.protocol+'//static.woopra.com/js/woopra.js';
			    wsc.type = 'text/javascript';
			    wsc.async = true;
			    var ssc = document.getElementsByTagName('script')[0];
			    ssc.parentNode.insertBefore(wsc, ssc);
			})();
		</script>
		<!-- End of Woopra Code -->
*/
?>
	</head>
	<body <?php body_class(); ?>>
		<script type="text/javascript" src="<?php bloginfo ('stylesheet_directory'); ?>/js/tooltip.js"></script>
		<div id="main" class="fl-full-width">
			<div id="wrapper" class="foreground">
				<header class="fl-page-header fl-page-header-primary fl-page-nav-right fl-page-nav-toggle-icon" data-fl-distance=50 itemscope="itemscope" itemtype="http://schema.org/WPHeader">
					<div class="fl-page-header-wrap">
						<div class="fl-page-header-container container">
							<div class="fl-page-header-row row">
								<div class="col-md-4 col-sm-12 fl-page-header-logo-col">
									<div class="fl-page-header-logo" itemscope="itemscope" itemtype="http://schema.org/Organization">
										<a href="/" itemprop="url"><img class="fl-logo-img" itemscope itemtype="http://schema.org/ImageObject" src="/wp-content/uploads/2016/09/EOT-Clear.png" data-retina="" alt="EOT" /><meta itemprop="name" content="EOT" /></a>
									</div>
								</div>
								<div class="fl-page-nav-col col-md-8 col-sm-12">
									<div class="fl-page-nav-wrap">
										<nav class="fl-page-nav fl-nav navbar navbar-default" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">
											<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".fl-page-nav-collapse">
												<span><i class="fa fa-bars"></i></span>
											</button>
											<div class="fl-page-nav-collapse collapse navbar-collapse">
												<?php if (is_user_logged_in ()) { ?>
													<?php wp_nav_menu (array('theme_location' => 'primary_login', 'container' => '', 'menu_class' => 'nav navbar-nav navbar-right menu'));?>
													<?php } else { ?>
														<?php wp_nav_menu (array('theme_location' => 'primary', 'container' => '', 'menu_class' => 'nav navbar-nav navbar-right menu'));?>
														<?php } ?>
												<!-- <ul id="menu-main-menu-for-beaver-builder" class="nav navbar-nav navbar-right menu"><li id="menu-item-711" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-711"><a href="http://localhost/wordpress/features/">Features</a></li>
													<li id="menu-item-713" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-713"><a href="http://localhost/wordpress/pricing/">Pricing</a></li>
													<li id="menu-item-710" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-710"><a href="http://localhost/wordpress/experts/">Faculty</a></li>
													<li id="menu-item-708" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-708"><a href="http://localhost/wordpress/blog/">Blog</a></li>
													<li id="menu-item-709" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-709"><a href="http://localhost/wordpress/contact/">Contact</a></li>
													<li id="menu-item-712" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-712"><a href="http://localhost/wordpress/login/">Login</a></li>
												</ul>		 -->					</div>
											</nav>
										</div>
									</div>
								</div>
							</div>
						</div>
					</header>
<!-- 				<div id="header">
					<a href="#" class="menu">Menu</a>
					<a href="<?php bloginfo ('url'); ?>/" id="logo"></a> 
					<?php if (is_user_logged_in ()) { ?>	
						<a href="<?php echo $eot_logout_url; ?>" class="logout">Log Out</a>
					<?php } else { ?>
						<a href="<?php echo $eot_login_url; ?>" class="login">Log In</a>
					<?php } ?>
					<div id="header_icons">                   
						<a href="http://www.facebook.com/expertonlinetraining" id="facebook" target="_blank"></a>
						<a href="http://www.twitter.com/expontraining" id="twitter" target="_blank"></a>         
						<a href="http://www.youtube.com/user/ExpertOnlineTraining" id="youtube" target="_blank"></a>
						<a href="https://plus.google.com/105978285128980021284" id="google_plus" target="_blank"></a>
					</div>
				</div>
				<div id="mynav">
					<?php if (is_user_logged_in ()) { ?>
						<?php wp_nav_menu (array('theme_location' => 'primary_login', 'container' => '', 'menu_class' => 'main_menu'));?>
						<a href="<?php echo $eot_logout_url; ?>" id="logout-button" title="Log In">Log Out</a>
					<?php } else { ?>
						<?php wp_nav_menu (array('theme_location' => 'primary', 'container' => '', 'menu_class' => 'main_menu'));?>
						<a href="<?php echo $eot_login_url; ?>" id="login-button" title="Log In">Log In</a>
					<?php } ?>
					<div class="clear"></div>
				</div> -->
				<div id="message"></div>
				<script>
					$ = jQuery;
					$(document).ready(function()
					{
						//add appropriate classes to make the header look like the new theme
						$('body').addClass('fl-builder fl-preset-default fl-full-width fl-fixed-header fl-scroll-header');
						$('#wrapper header').addClass('fl-show');
						$('.fl-page-header').css('position', 'relative');
						$('.container').css('width', 'auto');
					});
				</script>
