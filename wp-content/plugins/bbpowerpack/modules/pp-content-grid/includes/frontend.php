<?php
FLBuilderModel::default_settings($settings, array(
	'post_grid_filters_display' => 'no',
	'post_type'	=> 'post',
	'post_grid_filters'	=> 'none'

));

$css_class = '';

if ( $settings->match_height == 'no' ) {
	$css_class .= ' pp-masonry-active';
} else {
	$css_class .= ' pp-equal-height';
}
if ( $settings->layout == 'grid' && $settings->post_grid_filters_display == 'yes' && ! empty( $settings->post_grid_filters ) ) {
	$css_class .= ' pp-filters-active';
}

// Get the query data.
$query = FLBuilderLoop::query( $settings );

?>
<div class="pp-posts-wrapper">
	<?php

	// Render the posts.
	if ( $query->have_posts() ) :

		do_action( 'pp_cg_before_posts', $settings, $query );

		$css_class .= ( FLBuilderLoop::get_paged() > 0 ) ? ' pp-paged-scroll-to' : '';

	// Post filters.
	if ( $settings->layout == 'grid' && $settings->post_grid_filters_display == 'yes' && 'none' != $settings->post_grid_filters ) {
		include $module->dir . 'includes/post-filters.php';
	}

	?>

	<div class="pp-content-post-<?php echo $settings->layout; ?><?php echo $css_class; ?> clearfix" itemscope="itemscope" itemtype="http://schema.org/Blog">
		<?php if( $settings->layout == 'carousel' ) { ?>
			<div class="pp-content-posts-inner owl-carousel">
		<?php } ?>

			<?php

			while( $query->have_posts() ) {

				$query->the_post();

				ob_start();

				$terms_list = wp_get_post_terms( get_the_id(), $settings->post_taxonomies );

				include apply_filters( 'pp_cg_module_layout_path', $module->dir . 'includes/post-' . $settings->layout . '.php', $settings->layout, $settings );

				// Do shortcodes here so they are parsed in context of the current post.
				echo do_shortcode( ob_get_clean() );
			}

			?>

			<?php if ( $settings->layout == 'grid' ) { ?>
			<div class="pp-grid-space"></div>
			<?php } ?>

		<?php if ( $settings->layout == 'carousel' ) { ?>
			</div>
		<?php } ?>
	</div>

	<div class="fl-clear"></div>

	<?php endif; ?>

	<?php

	do_action( 'pp_cg_after_posts', $settings, $query );

	// Render the pagination.
	if( $settings->layout != 'carousel' && $settings->pagination != 'none' && $query->have_posts() ) :

	?>

	<div class="pp-content-grid-pagination fl-builder-pagination"<?php if($settings->pagination == 'scroll') echo ' style="display:none;"'; ?>>
		<?php $module->pagination( $query, $settings ); ?>
	</div>

	<?php endif; ?>

	<?php

	do_action( 'pp_cg_after_pagination', $settings, $query );

	// Render the empty message.
	if( ! $query->have_posts() && ( defined('DOING_AJAX') || isset( $_REQUEST['fl_builder'] ) ) ) :

	?>
	<div class="pp-content-grid-empty"><?php esc_html_e('No post found.', 'bb-powerpack'); ?></div>

	<?php

	endif;

	wp_reset_postdata();

	?>
</div>
