<?php
/**
 * Template for displaying pages
 * 
 * @package eot
 */

get_header();?> 
<div id="main-content" class="s-c-x">
	<div id="colmask" class="ckl-color2">
		<div id="colmid" class="cdr-color1">
			<div id="colright" class="ctr-color1">
				<?php get_sidebar (); ?>
				<div id="col1wrap">
					<div id="col1pad">
						<div id="col1">
							<div class="component-pad">
								<h1 class="article_page_title">Subscribe to Expert Online Training</h1>
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