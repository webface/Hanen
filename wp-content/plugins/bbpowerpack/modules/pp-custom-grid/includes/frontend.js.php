(function($) {

	$(function() {

		new PPCustomGrid({
			id: '<?php echo $id ?>',
			layout: '<?php echo $settings->match_height === '1' ? 'columns' : 'grid'; ?>',
			pagination: '<?php echo $settings->pagination; ?>',
			postSpacing: '<?php echo $settings->post_spacing; ?>',
			postWidth: '<?php echo $settings->post_width; ?>',
			matchHeight: <?php echo $settings->match_height; ?>
		});
	});

	<?php if($settings->match_height === '0') : ?>
	$(window).on('load', function() {
		$('.fl-node-<?php echo $id; ?> .pp-custom-grid-post').css('height', 'auto');
		$('.fl-node-<?php echo $id; ?> .pp-custom-grid').masonry('reloadItems');
	});
	<?php endif; ?>

})(jQuery);
