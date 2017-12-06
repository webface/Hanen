<?php
$item_class = 'pp-photo-gallery-item';

if ( $settings->gallery_layout == 'masonry' ) {
	$item_class .= ' pp-gallery-masonry-item';
}
if ( $settings->gallery_layout == 'justified' ) {
	$item_class .= ' pp-gallery-justified-item';
}
?>

<div class="pp-photo-gallery">
<?php foreach($module->get_photos() as $photo) : ?>
	<div class="<?php echo $item_class; ?>">
		<div class="pp-photo-gallery-content">

			<?php if( $settings->click_action != 'none' ) : ?>
					<?php $click_action_link = '#';
						if( $settings->click_action == 'custom-link' ) {
							$click_action_target = $settings->custom_link_target;
							if ( ! empty( $photo->cta_link ) ) {
								$click_action_link = $photo->cta_link;
							}
						}

						if ( $settings->click_action == 'lightbox' ) {
							$click_action_link = $photo->link;
						}

					?>
			<a href="<?php echo $click_action_link; ?>" <?php if( $settings->click_action == 'custom-link' ) { ?>target="<?php echo $click_action_target; ?>"<?php } ?> <?php if( $settings->click_action == 'lightbox' ) { ?>class="fancybox-button" rel="fancybox-button"<?php } ?>>
			<?php endif; ?>

			<img class="pp-gallery-img" src="<?php echo $photo->src; ?>" alt="<?php echo $photo->alt; ?>" />
				<!-- Overlay Wrapper -->
				<div class="pp-gallery-overlay">
					<div class="pp-overlay-inner">

						<?php if( $settings->show_captions == 'hover' ) : ?>
							<div class="pp-caption">
								<?php echo $photo->caption; ?>
							</div>
						<?php endif; ?>

						<?php if( $settings->icon == '1' && $settings->overlay_icon != '' ) : ?>
						<div class="pp-overlay-icon">
							<span class="<?php echo $settings->overlay_icon; ?>" ></span>
						</div>
						<?php endif; ?>

					</div>
				</div> <!-- Overlay Wrapper Closed -->

			<?php if( $settings->click_action != 'none' ) : ?>
			</a>
			<?php endif; ?>
		</div>
		<?php if($photo && !empty($photo->caption) && 'below' == $settings->show_captions) : ?>
		<div class="pp-photo-gallery-caption pp-photo-gallery-caption-below" itemprop="caption"><?php echo $photo->caption; ?></div>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>

	<?php if ( $settings->gallery_layout == 'masonry' ) { ?>
		<div class="pp-photo-space"></div>
	<?php } ?>
</div>