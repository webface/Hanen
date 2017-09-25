<?php
wp_reset_query();

switch ( $settings->layout ) :

	case 1:
	case 5:
		$settings->posts_per_page = 4;
		break;

	case 2:
		$settings->posts_per_page = 5;
		break;

	case 3:
	case 4:
		$settings->posts_per_page = 3;
		break;

	default:
		$settings->posts_per_page = 4;
		break;

endswitch;

// Get the query data.
if ( is_front_page() ) {
	set_query_var('page', 1);
} else {
	set_query_var('paged', 1);
}
$query = FLBuilderLoop::query($settings);

// Render the posts.
if($query->have_posts()) :

?>
<div class="pp-post-tiles pp-tile-layout-<?php echo $settings->layout; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<?php

	$count = 1;

	while($query->have_posts()) :

		$query->the_post();

		if ( in_array( $settings->layout, array(1,2,3,4) ) ) :

			if ( $count == 1 ) {
				echo '<div class="pp-post-tile-left">';
			}
			if ( $count == 2 ) {
				echo '<div class="pp-post-tile-right">';
			}

			include $module->dir . 'includes/post-grid.php';

			if ( $count == 1 ) {
				echo '</div>';
			}
			if ( ($count == 4 && $settings->layout == 1) || ($count == 5 && $settings->layout == 2) ) {
				echo '</div>';
			}

		endif;

		$count++;

	endwhile;

	?>
</div>
<div class="fl-clear"></div>
<?php endif; ?>
<?php

// Render the empty message.
if(!$query->have_posts() && (defined('DOING_AJAX') || isset($_REQUEST['fl_builder']))) :

?>
<div class="fl-post-grid-empty">
	<?php
	if (isset($settings->no_results_message)) :
		echo $settings->no_results_message;
	else :
		_e( 'No posts found.', 'bb-powerpack' );
	endif;
	?>
</div>

<?php

endif;

wp_reset_postdata();

?>
