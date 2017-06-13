<?php
	/**
	 * Template Name: Presentor Page
	 *
	 * @package WordPress
	 */
	get_header();

	/* 
	 * Settings for the query
	 */
	$type = "presenter";
	$args=array(
	  'post_type' => $type,
	  'post_status' => 'publish',
	  'posts_per_page' => 100,
		);
	$my_query = new WP_Query($args);
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
									<h1 class="article_page_title"><?= get_the_title(); ?></h1>
								<?php
									// This will go to all presenters
									if( $my_query->have_posts() ) 
									{
										while ( $my_query->have_posts() ) 
										{
											$my_query->the_post();
											$id = $my_query->post->ID; // The presentor's Post ID
											$content = $my_query->post->post_content; // Presentor's content
											$name = $my_query->post->post_title; // Presentor's name
											$slug = $my_query->post->post_name; // Presentor's slug
											$image_id = get_field("presentor_image", $id); // Advance Custom Fields image ID
											$url_link = get_field("url", $id);
											$image = wp_get_attachment_image_src( $image_id, "presenter-headshot" );
								?>
											<p>
												<a name="<?= $slug ?>" id="<?= $slug ?>"></a>
												<img src="<?= $image[0] ?>" width="<?= $image[1] ?>" height="<?= $image[2] ?>" class="biopic"> 
											</p>
											<h2><?= $name ?></h2>
											<a href="http://<?= $url_link ?>" target="_blank"><?= $url_link ?></a>
											<br/>
											<?= $content ?>
											<p>
												&nbsp;
											</p>
											<p>
											</p>
								<?php
										}
									}
								?>
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
