<?php
$space_desktop = ( $settings->photo_grid_count['desktop'] - 1 ) * $settings->photo_spacing;
$photo_columns_desktop = ( 100 - $space_desktop ) / $settings->photo_grid_count['desktop'];

$space_tablet = ( $settings->photo_grid_count['tablet'] - 1 ) * $settings->photo_spacing;
$photo_columns_tablet = ( 100 - $space_tablet ) / $settings->photo_grid_count['tablet'];

$space_mobile = ( $settings->photo_grid_count['mobile'] - 1 ) * $settings->photo_spacing;
$photo_columns_mobile = ( 100 - $space_mobile ) / $settings->photo_grid_count['mobile'];
?>

<?php //echo $space_desktop; ?>
.fl-node-<?php echo $id; ?> .pp-photo-gallery,
.fl-node-<?php echo $id; ?> .pp-masonry-content {
	margin: -<?php echo $settings->photo_spacing / 2; ?>px;
}

<?php if($settings->gallery_layout == 'grid') { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item {
		width: <?php echo $photo_columns_desktop;?>%;
		margin-bottom: <?php echo $settings->photo_spacing; ?>%;
		<?php if ( $settings->photo_spacing == 0 ) { ?>
        margin-right: <?php echo $settings->photo_spacing - ( $settings->photo_border != 'none' ? $settings->photo_border_width : 0 ); ?>px;
        margin-bottom: <?php echo $settings->photo_spacing - ( $settings->photo_border != 'none' ? $settings->photo_border_width : 0 ); ?>px;
        <?php } else { ?>
        margin-bottom: <?php echo $settings->photo_spacing; ?>%;
        <?php } ?>
	}
	<?php if ( $settings->photo_grid_count['desktop'] > 1 ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['desktop']; ?>n+1){
		<!-- clear: left; -->
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['desktop']; ?>n+0){
		<!-- clear: right; -->
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['desktop']; ?>n){
		margin-right: 0;
	}
	<?php } ?>

<?php } elseif ($settings->gallery_layout == 'masonry') { ?>

.fl-node-<?php echo $id; ?> .pp-grid-sizer {
	width: <?php echo $photo_columns_desktop;?>%;
}

.fl-node-<?php echo $id; ?> .pp-masonry-item {
	width: <?php echo $photo_columns_desktop;?>%;
	padding-top: <?php echo $settings->photo_padding['top']; ?>px;
	padding-bottom: <?php echo $settings->photo_padding['bottom']; ?>px;
	padding-left: <?php echo $settings->photo_padding['left']; ?>px;
	padding-right: <?php echo $settings->photo_padding['right']; ?>px;
	margin-bottom: <?php echo $settings->photo_spacing; ?>%;
	<?php if ( $settings->photo_spacing == 0 ) { ?>
	margin-right: <?php echo $settings->photo_spacing - ( $settings->photo_border != 'none' ? $settings->photo_border_width : 0 ); ?>px;
	margin-bottom: <?php echo $settings->photo_spacing - ( $settings->photo_border != 'none' ? $settings->photo_border_width : 0 ); ?>px;
	<?php } else { ?>
	margin-bottom: <?php echo $settings->photo_spacing; ?>%;
	<?php } ?>
	border-style: <?php echo $settings->photo_border; ?>;
	<?php if( $settings->photo_border_width && $settings->photo_border != 'none' ) { ?>border-width: <?php echo $settings->photo_border_width; ?>px; <?php } ?>
	<?php if( $settings->photo_border_color ) { ?> border-color: #<?php echo $settings->photo_border_color; ?>; <?php } ?>
	<?php if( $settings->photo_border_radius >= 0 ) { ?> border-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-photo-gallery-item {
	padding-top: <?php echo $settings->photo_padding['top']; ?>px;
	padding-bottom: <?php echo $settings->photo_padding['bottom']; ?>px;
	padding-left: <?php echo $settings->photo_padding['left']; ?>px;
	padding-right: <?php echo $settings->photo_padding['right']; ?>px;
	border-style: <?php echo $settings->photo_border; ?>;
	<?php if( $settings->photo_border_width && $settings->photo_border != 'none' ) { ?>border-width: <?php echo $settings->photo_border_width; ?>px; <?php } ?>
	<?php if( $settings->photo_border_color ) { ?> border-color: #<?php echo $settings->photo_border_color; ?>; <?php } ?>
	<?php if( $settings->photo_border_radius >= 0 ) { ?> border-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-photo-gallery-item img,
.fl-node-<?php echo $id; ?> .pp-masonry-item img,
.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
	<?php if( $settings->photo_border_radius >= 0 ) { ?> border-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-photo-gallery-item .pp-photo-gallery-content > a {
	display: block;
	line-height: 0;
}

.fl-node-<?php echo $id; ?> .pp-photo-space {
	width: <?php echo $settings->photo_spacing; ?>%;
}


<?php if( $settings->show_captions == 'below' ) { ?>
.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption {
	<?php if( $settings->caption_bg_color ) { ?>
	background-color: #<?php echo $settings->caption_bg_color; ?>;
	<?php } ?>
	padding-top: <?php echo $settings->caption_padding['top']; ?>px;
	padding-bottom: <?php echo $settings->caption_padding['bottom']; ?>px;
	padding-left: <?php echo $settings->caption_padding['left']; ?>px;
	padding-right: <?php echo $settings->caption_padding['right']; ?>px;
	text-align: <?php echo $settings->caption_alignment; ?>;
}
<?php } ?>


<?php if($settings->click_action == 'lightbox' && !empty($settings->show_captions)) : ?>
.mfp-gallery img.mfp-img {
	padding: 0;
}

.mfp-counter {
	display: block !important;
}
<?php endif; ?>

<?php if( $settings->overlay_effects != 'none' ) : ?>
.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
	background: <?php echo ($settings->overlay_color != '' ) ? pp_hex2rgba('#'.$settings->overlay_color, ($settings->overlay_color_opacity/ 100)) : 'rgba(0,0,0,.5)'; ?>;
}
<?php endif; ?>
.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-icon span {
	color: #<?php echo $settings->overlay_icon_color; ?>;
	font-size: <?php echo $settings->overlay_icon_size; ?>px;
	background-color: #<?php echo $settings->overlay_icon_bg_color; ?>;
	<?php if( $settings->overlay_icon_radius ) { ?>border-radius: <?php echo $settings->overlay_icon_radius; ?>px;<?php } ?>
	<?php if( $settings->overlay_icon_padding ) { ?>padding: <?php echo $settings->overlay_icon_padding; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption,
.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-caption  {
	<?php if( $settings->caption_font['family'] != 'Default' ) { ?>
	   <?php FLBuilderFonts::font_css( $settings->caption_font ); ?>
   <?php } ?>
   <?php if( $settings->caption_font_size_toggle != 'default' && $settings->caption_custom_font_size['desktop'] ) { ?>
	  font-size: <?php echo $settings->caption_custom_font_size['desktop']; ?>px;
   <?php } ?>
	color: #<?php echo $settings->caption_color; ?>;
}

.fl-node-<?php echo $id; ?> .pp-gallery-filters {
	text-align: <?php echo $settings->filter_alignment; ?>;
	margin-bottom: <?php echo $settings->filter_margin_bottom; ?>px;
}

.fl-node-<?php echo $id; ?> .pp-gallery-filters li {
	background: <?php echo ($settings->filter_background['primary']) ? '#'.$settings->filter_background['primary'] : 'transparent'; ?>;
	border-color: <?php echo ( $settings->filter_border_color['primary'] ) ? '#' . $settings->filter_border_color['primary'] : 'transparent'; ?>;
	<?php if( $settings->filter_border_radius >= 0 ) { ?>border-radius: <?php echo $settings->filter_border_radius; ?>px;<?php } ?>
	<?php if( $settings->filter_border != 'none') { ?>
	border-top-width: <?php echo $settings->filter_border_width['top']; ?>px;
	border-bottom-width: <?php echo $settings->filter_border_width['bottom']; ?>px;
	border-left-width: <?php echo $settings->filter_border_width['left']; ?>px;
	border-right-width: <?php echo $settings->filter_border_width['right']; ?>px;
	<?php } ?>
	<?php if( $settings->filter_border ) { ?>border-style: <?php echo $settings->filter_border; ?>;<?php } ?>
	padding-top: <?php echo $settings->filter_padding['top']; ?>px;
	padding-right: <?php echo $settings->filter_padding['right']; ?>px;
	padding-bottom: <?php echo $settings->filter_padding['bottom']; ?>px;
	padding-left: <?php echo $settings->filter_padding['left']; ?>px;
	<?php if( $settings->filter_color['primary'] ) { ?>color: #<?php echo $settings->filter_color['primary']; ?>;<?php } ?>
	margin-right: <?php echo $settings->filter_margin; ?>px;
	margin-bottom: <?php echo ($settings->filter_margin / 2); ?>px;
	<?php if( $settings->filter_font['family'] != 'Default' ) { ?>
	   <?php FLBuilderFonts::font_css( $settings->filter_font ); ?>
   <?php } ?>
   font-size: <?php echo $settings->filter_font_size['desktop']; ?>px;
   <?php if( 'default' != $settings->filter_text_transform ) { ?>
   text-transform: <?php echo $settings->filter_text_transform; ?>
   <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-gallery-filters li:hover,
.fl-node-<?php echo $id; ?> .pp-gallery-filters li.pp-filter-active {
	background: <?php echo ($settings->filter_background['secondary']) ? '#'.$settings->filter_background['secondary'] : 'transparent'; ?>;
	<?php if( $settings->filter_color['secondary'] ) { ?>color: #<?php echo $settings->filter_color['secondary']; ?>;<?php } ?>
	border-color: <?php echo ( $settings->filter_border_color['secondary'] ) ? '#' . $settings->filter_border_color['secondary'] : 'transparent'; ?>;
}

<?php if( $settings->overlay_effects == 'fade' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		height: 100%;
		width: 100%;
		opacity: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		opacity: 1;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-left' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 0;
		left: 0;
		right: 0;
		width: 0;
		height: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		width: 100%;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-right' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 0;
		left: 100%;
		right: 0;
		width: 0;
		height: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		width: 100%;
		left: 0;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-top' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 100%;
		left: 0;
		right: 0;
		width: 100%;
		height: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		height: 100%;
		bottom: 0;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-bottom' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 0;
		left: 0;
		right: 0;
		width: 100%;
		height: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		height: 100%;
	}
<?php } ?>

<?php if( $settings->hover_effects == 'zoom-in' || $settings->hover_effects == 'zoom-out' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		opacity: 0;
		overflow: hidden;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-webkit-flex-direction: column;
		-ms-flex-direction: column;
		flex-direction: column;
		-webkit-box-pack: center;
		-webkit-justify-content: center;
		-ms-flex-pack: center;
		justify-content: center;
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		height: 100%;
		width: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		opacity: 1;
		-moz-transform: translate(0);
		-ms-transform: translate(0);
		-o-transform: translate(0);
		-webkit-transform: translate(0);
		transform: translate(0);
	}
<?php } ?>

<?php if( $settings->hover_effects == 'zoom-in' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content:hover .pp-gallery-img {
		-webkit-transform: scale(1.1);
		-moz-transform: scale(1.1);
		-ms-transform: scale(1.1);
		-o-transform: scale(1.1);
		transform: scale(1.1);
	}
<?php } ?>

<?php if( $settings->hover_effects == 'zoom-out' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content:hover .pp-gallery-img {
		-webkit-transform: scale(1,1);
		-moz-transform: scale(1,1);
		-ms-transform: scale(1,1);
		-o-transform: scale(1,1);
		transform: scale(1,1);
	}
<?php } ?>

@media only screen and ( max-width: 768px ) {
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item,
	.fl-node-<?php echo $id; ?> .pp-masonry-item {
		width: <?php echo $photo_columns_tablet;?>%;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['desktop']; ?>n+1){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['desktop']; ?>n+0){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['tablet']; ?>n){
		margin-right: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption,
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-caption  {
		<?php if( $settings->caption_font_size_toggle != 'default' && $settings->caption_custom_font_size['tablet'] ) { ?>
	 	  font-size: <?php echo $settings->caption_custom_font_size['tablet']; ?>px;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters-toggle {
		display: block;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters {
		display: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters li {
		display: block;
		float: none;
		margin: 0 !important;
		text-align: left;
	   	font-size: <?php echo $settings->filter_font_size['tablet']; ?>px;
	}
}

@media only screen and ( max-width: 480px ) {
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item,
	.fl-node-<?php echo $id; ?> .pp-masonry-item {
		width: <?php echo $photo_columns_mobile;?>%;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['tablet']; ?>n+1){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['tablet']; ?>n+0){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $settings->photo_grid_count['mobile']; ?>n){
		margin-right: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption,
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-caption  {
		<?php if( $settings->caption_font_size_toggle != 'default' && $settings->caption_custom_font_size['mobile'] ) { ?>
	 	  font-size: <?php echo $settings->caption_custom_font_size['mobile']; ?>px;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters li {
	   font-size: <?php echo $settings->filter_font_size['mobile']; ?>px;
	}
}
