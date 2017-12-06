<?php
$photos = $module->get_photos();
?>
<div class="pp-image-carousel-wrapper">
	<div class="pp-image-carousel swiper-container<?php echo ( $settings->carousel_type == 'slideshow') ? ' pp-image-carousel-slideshow' : ''; ?><?php echo ($settings->pagination_position && $settings->carousel_type != 'slideshow') ? ' pp-carousel-navigation-' . $settings->pagination_position : ''; ?>">
		<div class="swiper-wrapper">
			<?php foreach($photos as $photo) : ?>
			<div class="pp-image-carousel-item<?php echo ( ( $settings->click_action != 'none' ) && !empty( $photo->link ) ) ? ' pp-image-carousel-link' : ''; ?> swiper-slide">
					<?php if( $settings->click_action != 'none' ) : ?>
							<?php $click_action_link = '#';
								$click_action_target = $settings->custom_link_target;

								if ( $settings->click_action == 'custom-link' ) {
									if ( ! empty( $photo->cta_link ) ) {
										$click_action_link = $photo->cta_link;
									}
								}

								if ( $settings->click_action == 'lightbox' ) {
									$click_action_link = $photo->link;
								}

							?>
					<a href="<?php echo $click_action_link; ?>" target="<?php echo $click_action_target; ?>">
					<?php endif; ?>

					<div class="pp-carousel-image-container" style="background-image:url(<?php echo $photo->src; ?>)">
					</div>

					<?php if( $settings->overlay != 'none' ) : ?>
						<!-- Overlay Wrapper -->
						<div class="pp-image-overlay <?php echo $settings->overlay_effects; ?>">

								<?php if( $settings->overlay == 'text' ) : ?>
									<div class="pp-caption">
										<?php echo $photo->caption; ?>
									</div>
								<?php endif; ?>

								<?php if( $settings->overlay == 'icon' ) : ?>
								<div class="pp-overlay-icon">
									<span class="<?php echo $settings->overlay_icon; ?>" ></span>
								</div>
								<?php endif; ?>

						</div> <!-- Overlay Wrapper Closed -->
					<?php endif; ?>

					<?php if( $settings->click_action != 'none' ) : ?>
					</a>
					<?php endif; ?>
				</div>
				<?php
			endforeach;
			?>
		</div>

		<?php if ( 1 < count( $photos ) ) : ?>
			<?php if( $settings->pagination_type ) { ?>
			<!-- pagination -->
			<div class="swiper-pagination"></div>
			<?php } ?>

			<?php if( $settings->slider_navigation == 'yes' ) { ?>
			<!-- navigation arrows -->
			<div class="pp-swiper-button pp-swiper-button-prev"><span class="fa fa-angle-left"></span></div>
			<div class="pp-swiper-button pp-swiper-button-next"><span class="fa fa-angle-right"></span></div>
			<?php } ?>
		<?php endif; ?>
			
	</div>

	<?php if ( $settings->carousel_type == 'slideshow' ) : ?>
	<div class="pp-thumbnails-swiper swiper-container pp-thumbs-ratio-<?php echo $settings->thumb_ratio; ?>">
		<div class="swiper-wrapper">
			<?php foreach($photos as $photo) : ?>
				<div class="swiper-slide">
					<div class="pp-image-carousel-thumb" style="background-image:url(<?php echo $photo->src; ?>)"></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>
</div>