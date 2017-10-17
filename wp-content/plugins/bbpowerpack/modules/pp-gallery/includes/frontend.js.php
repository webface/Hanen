;(function($) {

	<?php if($settings->click_action == 'lightbox') : ?>

		<?php if( $settings->click_action == 'lightbox' && $settings->show_lightbox_thumb == 'no' ) {
			$selector = '.fancybox-button';
		} else if( $settings->show_lightbox_thumb == 'yes' ) {
			$selector = '.fancybox-thumb';
		} ?>

		$(".fl-node-<?php echo $id; ?> .fancybox-button").fancybox({
			closeBtn		: true,
			closeClick		: true,
			modal			: false,
			wrapCSS			: 'fancybox-<?php echo $id; ?>',
			helpers		: {
				title	: { type : 'inside' },
				<?php if( $settings->show_lightbox_thumb == 'yes' ) { ?>
					thumbs	: {
						width	: 50,
						height	: 50
					},
				<?php } ?>
			},
			afterLoad: function(current, previous) {
				$(".fancybox-<?php echo $id; ?>").parent().addClass('fancybox-<?php echo $id; ?>-overlay');
			}
		});

	<?php endif; ?>

	$(".fl-node-<?php echo $id; ?> .pp-photo-gallery-item, .fl-node-<?php echo $id; ?> .pp-gallery-masonry-item").find('.pp-photo-gallery-caption-below').parent().addClass('has-caption');

	new PPGallery({
		id: '<?php echo $id ?>',
		layout: '<?php echo $settings->gallery_layout; ?>',
	});

})(jQuery);
