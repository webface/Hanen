<?php
/**
 * Template Name: Custom Pages
 * 
 * @package eot
 */
global $post;
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
								<?php while (have_posts()) {
									the_post();
									get_template_part('content', $post->post_name);
								} ?> 
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