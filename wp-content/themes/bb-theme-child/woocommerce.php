<?php 
	get_header();
	echo do_shortcode( "[insert page='header' display='content']" );
?>
	<div class="fl-content-full container">
		<div class="row">
			<div class="fl-content col-md-12">
				<?= do_action('woo_custom_breadcrumb');  ?>
				<?php woocommerce_content(); ?>
			</div>
		</div>
	</div>
<?php 
	echo do_shortcode( "[insert page='footer' display='content']" );
	get_footer();
?>
