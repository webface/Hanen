<?php
/**
 * Template for displaying pages
 * 
 * @package eot
 */
	
	get_header();

	$title = __("Subscribe to Expert Online Training", "EOT_LMS");

	if ( isset($_REQUEST['library_id']) && $_REQUEST['library_id'] > 0 )
    {
    	// get the library title
    	$library = getLibrary($_REQUEST['library_id']);
    	if ($library)
    	{
    		$title = __("Subscribe to " . $library->name, "EOT_LMS");
    	}
    }

?> 
<div id="main-content" class="s-c-x">
	<div id="colmask" class="ckl-color2">
		<div id="colmid" class="cdr-color1">
			<div id="colright" class="ctr-color1">
				<?php get_sidebar (); ?>
				<div id="col1wrap">
					<div id="col1pad">
						<div id="col1">
							<div class="component-pad">
								<h1 class="article_page_title"><?= $title ?></h1>
								<?php new_subscription (); ?>
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