(function($) {
	<?php if($settings->click_action == 'lightbox') : ?>

		var gallery_selector = $( '.fl-node-<?php echo $id; ?> .pp-image-carousel' );
		if( gallery_selector.length && typeof $.fn.magnificPopup !== 'undefined') {
			gallery_selector.magnificPopup({
				delegate: '.pp-image-carousel-item a',
				closeBtnInside: true,
				type: 'image',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
				},
				'image': {
					titleSrc: function(item) {
						return item.el.parent().find('.pp-caption').text();
					}
				}
			});
		}
	<?php endif; ?>

	var settings = {
		id: '<?php echo $id; ?>',
		type: '<?php echo $settings->carousel_type; ?>',
		initialSlide: 0,
		slidesPerView: {
			desktop: <?php echo absint($settings->columns); ?>,
			tablet: <?php echo absint($settings->columns_medium); ?>,
			mobile: <?php echo absint($settings->columns_responsive); ?>,
		},
		slideshow_slidesPerView: {
			desktop: <?php echo absint($settings->thumb_columns); ?>,
			tablet: <?php echo absint($settings->thumb_columns_medium); ?>,
			mobile: <?php echo absint($settings->thumb_columns_responsive); ?>,
		},
		spaceBetween: {
			desktop: '<?php echo $settings->spacing; ?>',
			tablet: '<?php echo $settings->spacing_medium; ?>',
			mobile: '<?php echo $settings->spacing_responsive; ?>',
		},
		isBuilderActive: <?php echo FLBuilderModel::is_builder_active() ? 'true' : 'false'; ?>,
		pagination: '<?php echo $settings->pagination_type; ?>',
		autoplay_speed: <?php echo $settings->autoplay == 'yes' ? $settings->autoplay_speed : 'false'; ?>,
		pause_on_interaction: <?php echo $settings->pause_on_interaction == 'yes' ? 'true' : 'false'; ?>,
		effect: '<?php echo $settings->effect; ?>',
		speed: <?php echo $settings->transition_speed; ?>,
		breakpoint: {
			medium: <?php echo $global_settings->medium_breakpoint; ?>,
			responsive: <?php echo $global_settings->responsive_breakpoint; ?>
		}
	};

	new PPImageCarousel(settings);
	
})(jQuery);