<?php

$desktop_col = ( $settings->photo_grid_count ) ? $settings->photo_grid_count : 4;
$medium_col = ( $settings->photo_grid_count_medium ) ? $settings->photo_grid_count_medium : 2;
$mobile_col = ( $settings->photo_grid_count_responsive ) ? $settings->photo_grid_count_responsive : 1;

$space_desktop = ( $desktop_col - 1 ) * $settings->photo_spacing;
$photo_columns_desktop = ( 100 - $space_desktop ) / $desktop_col;

$space_tablet = ( $medium_col - 1 ) * $settings->photo_spacing;
$photo_columns_tablet = ( 100 - $space_tablet ) / $medium_col;

$space_mobile = ( $mobile_col - 1 ) * $settings->photo_spacing;
$photo_columns_mobile = ( 100 - $space_mobile ) / $mobile_col;
?>

<?php if($settings->gallery_layout == 'grid') { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item {
		width: <?php echo $photo_columns_desktop;?>%;
		margin-right: <?php echo $settings->photo_spacing; ?>%;
		margin-bottom: <?php echo $settings->photo_spacing; ?>%;
		<?php if ( $settings->photo_spacing == 0 ) { ?>
        margin-right: <?php echo $settings->photo_spacing - ( $settings->photo_border != 'none' ? $settings->photo_border_width : 0 ); ?>px;
        margin-bottom: <?php echo $settings->photo_spacing - ( $settings->photo_border != 'none' ? $settings->photo_border_width : 0 ); ?>px;
        <?php } ?>
		<?php if( $settings->photo_padding ) { ?>
			padding: <?php echo $settings->photo_padding; ?>px;
		<?php } ?>
		border-style: <?php echo $settings->photo_border; ?>;
		<?php if( $settings->photo_border_width && $settings->photo_border != 'none' ) { ?>border-width: <?php echo $settings->photo_border_width; ?>px; <?php } ?>
		<?php if( $settings->photo_border_color ) { ?> border-color: #<?php echo $settings->photo_border_color; ?>; <?php } ?>
		<?php if( $settings->photo_border_radius >= 0 ) { ?> border-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
		<?php if ( 'yes' == $settings->show_image_shadow ) { ?>
	    -webkit-box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	    -moz-box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	    -o-box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	    box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	    <?php } ?>
		<?php if ( 'yes' == $settings->show_image_shadow_hover ) { ?>
		-webkit-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in;
		-moz-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		-ms-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		-o-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:hover {
		<?php if ( 'yes' == $settings->show_image_shadow_hover ) { ?>
	    -webkit-box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
	    -moz-box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
	    -o-box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
	    box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
		-webkit-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		-moz-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		-ms-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		-o-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
		<?php } ?>
	}
	<?php if ( $desktop_col > 1 ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $desktop_col; ?>n+1){
		clear: left;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $desktop_col; ?>n+0){
		clear: right;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $desktop_col; ?>n){
		margin-right: 0;
	}
	<?php } ?>

<?php } elseif ($settings->gallery_layout == 'masonry') { ?>

.fl-node-<?php echo $id; ?> .pp-grid-sizer {
	width: <?php echo $photo_columns_desktop;?>%;
}

.fl-node-<?php echo $id; ?> .pp-gallery-masonry-item {
	width: <?php echo $photo_columns_desktop;?>%;
	<?php if( $settings->photo_padding ) { ?>
		padding: <?php echo $settings->photo_padding; ?>px;
	<?php } ?>
	margin-bottom: <?php echo $settings->photo_spacing; ?>%;
	border-style: <?php echo $settings->photo_border; ?>;
	<?php if( $settings->photo_border_width && $settings->photo_border != 'none' ) { ?>border-width: <?php echo $settings->photo_border_width; ?>px; <?php } ?>
	<?php if( $settings->photo_border_color ) { ?> border-color: #<?php echo $settings->photo_border_color; ?>; <?php } ?>
	<?php if( $settings->photo_border_radius >= 0 ) { ?> border-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
	<?php if ( 'yes' == $settings->show_image_shadow ) { ?>
	-webkit-box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	-moz-box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	-o-box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	box-shadow: <?php echo $settings->image_shadow['horizontal']; ?>px <?php echo $settings->image_shadow['vertical']; ?>px <?php echo $settings->image_shadow['blur']; ?>px <?php echo $settings->image_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color, $settings->image_shadow_opacity / 100 ); ?>;
	<?php } ?>
	<?php if ( 'yes' == $settings->show_image_shadow_hover ) { ?>
	-webkit-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in;
	-moz-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	-ms-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	-o-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-gallery-masonry-item:hover {
	<?php if ( 'yes' == $settings->show_image_shadow_hover ) { ?>
	-webkit-box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
	-moz-box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
	-o-box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
	box-shadow: <?php echo $settings->image_shadow_hover['horizontal']; ?>px <?php echo $settings->image_shadow_hover['vertical']; ?>px <?php echo $settings->image_shadow_hover['blur']; ?>px <?php echo $settings->image_shadow_hover['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->image_shadow_color_hover, $settings->image_shadow_opacity_hover / 100 ); ?>;
	-webkit-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	-moz-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	-ms-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	-o-transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	transition: all <?php echo ($settings->image_shadow_hover_speed / 1000); ?>s ease-in; ease-in;
	<?php } ?>
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-photo-gallery-item img,
.fl-node-<?php echo $id; ?> .pp-gallery-masonry-item img,
.fl-node-<?php echo $id; ?> .pp-gallery-overlay,
.fl-node-<?php echo $id; ?> .pp-photo-gallery-content {
	<?php if( $settings->photo_border_radius >= 0 ) { ?> border-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
}

<?php if( $settings->show_captions == 'below' && $settings->caption_bg_color ) { ?>
.fl-node-<?php echo $id; ?> .pp-photo-gallery-item.has-caption img,
.fl-node-<?php echo $id; ?> .pp-gallery-masonry-item.has-caption img,
.fl-node-<?php echo $id; ?> .has-caption .pp-gallery-overlay,
.fl-node-<?php echo $id; ?> .has-caption .pp-photo-gallery-content {
	border-bottom-left-radius: 0;
	border-bottom-right-radius: 0;
}

.fl-node-<?php echo $id; ?> .pp-photo-gallery-item.has-caption .pp-photo-gallery-caption,
.fl-node-<?php echo $id; ?> .pp-gallery-masonry-item.has-caption .pp-photo-gallery-caption {
	<?php if( $settings->photo_border_radius >= 0 ) { ?> border-bottom-left-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
	<?php if( $settings->photo_border_radius >= 0 ) { ?> border-bottom-right-radius: <?php echo $settings->photo_border_radius; ?>px; <?php } ?>
}

<?php } ?>

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
	<?php if( $settings->overlay_type == 'solid' ) { ?>
		background: <?php echo ($settings->overlay_color != '' ) ? pp_hex2rgba('#'.$settings->overlay_color, ($settings->overlay_color_opacity/ 100)) : 'rgba(0,0,0,.5)'; ?>;
	<?php } ?>

	<?php if( $settings->overlay_type == 'gradient' ) : ?>
		background: -moz-linear-gradient(top,  <?php echo pp_hex2rgba('#'.$settings->overlay_primary_color, ($settings->overlay_color_opacity/ 100)); ?> 0%, <?php echo pp_hex2rgba('#'.$settings->overlay_secondary_color, ($settings->overlay_color_opacity/ 100)); ?> 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo pp_hex2rgba('#'.$settings->overlay_primary_color, ($settings->overlay_color_opacity/ 100)); ?>), color-stop(100%,<?php echo pp_hex2rgba('#'.$settings->overlay_secondary_color, ($settings->overlay_color_opacity/ 100)); ?>)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  <?php echo pp_hex2rgba('#'.$settings->overlay_primary_color, ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba('#'.$settings->overlay_secondary_color, ($settings->overlay_color_opacity/ 100)); ?> 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  <?php echo pp_hex2rgba('#'.$settings->overlay_primary_color, ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba('#'.$settings->overlay_secondary_color, ($settings->overlay_color_opacity/ 100)); ?> 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  <?php echo pp_hex2rgba('#'.$settings->overlay_primary_color, ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba('#'.$settings->overlay_secondary_color, ($settings->overlay_color_opacity/ 100)); ?> 100%); /* IE10+ */
		background: linear-gradient(to bottom,  <?php echo pp_hex2rgba('#'.$settings->overlay_primary_color, ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba('#'.$settings->overlay_secondary_color, ($settings->overlay_color_opacity/ 100)); ?> 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo pp_hex2rgba('#'.$settings->overlay_primary_color, ($settings->overlay_color_opacity/ 100)); ?>', endColorstr='<?php echo pp_hex2rgba('#'.$settings->overlay_secondary_color, ($settings->overlay_color_opacity/ 100)); ?>',GradientType=0 ); /* IE6-9 */
	<?php endif; ?>

	-webkit-transition: <?php echo ($settings->overlay_animation_speed/1000); ?>s ease;
	-moz-transition: <?php echo ($settings->overlay_animation_speed/1000); ?>s ease;
	-ms-transition: <?php echo ($settings->overlay_animation_speed/1000); ?>s ease;
	-o-transition: <?php echo ($settings->overlay_animation_speed/1000); ?>s ease;
	transition: <?php echo ($settings->overlay_animation_speed/1000); ?>s ease;
}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-icon span {
	width: auto;
	height: auto;
	color: #<?php echo $settings->overlay_icon_color; ?>;
	font-size: <?php echo $settings->overlay_icon_size; ?>px;
	background-color: #<?php echo $settings->overlay_icon_bg_color; ?>;
	<?php if( $settings->overlay_icon_radius ) { ?>border-radius: <?php echo $settings->overlay_icon_radius; ?>px;<?php } ?>
	<?php if( $settings->overlay_icon_vertical_padding ) { ?>padding-top: <?php echo $settings->overlay_icon_vertical_padding; ?>px;<?php } ?>
	<?php if( $settings->overlay_icon_vertical_padding ) { ?>padding-bottom: <?php echo $settings->overlay_icon_vertical_padding; ?>px;<?php } ?>
	<?php if( $settings->overlay_icon_horizotal_padding ) { ?>padding-left: <?php echo $settings->overlay_icon_horizotal_padding; ?>px;<?php } ?>
	<?php if( $settings->overlay_icon_horizotal_padding ) { ?>padding-right: <?php echo $settings->overlay_icon_horizotal_padding; ?>px;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-icon span:before {
	font-size: <?php echo $settings->overlay_icon_size; ?>px;
	width: auto;
	height: auto;
}

.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption,
.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-caption  {
	<?php if( $settings->caption_font['family'] != 'Default' ) { ?>
	   <?php FLBuilderFonts::font_css( $settings->caption_font ); ?>
   <?php } ?>

   <?php if( $settings->caption_font_size_toggle != 'default' && $settings->caption_custom_font_size ) { ?>
	  font-size: <?php echo $settings->caption_custom_font_size; ?>px;
   <?php } ?>
	<?php if( $settings->caption_color ) { ?>
	color: #<?php echo $settings->caption_color; ?>;
	<?php } ?>
}

<?php if( $settings->overlay_effects == 'none' && $settings->hover_effects == 'none' && $settings->show_captions == 'hover' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
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

<?php if( $settings->overlay_effects == 'framed' ) { ?>
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
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner:before,
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner:after {
		content: '';
	    display: block;
	    position: absolute;
	    top: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    left: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    bottom: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    right: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    -webkit-transition: -webkit-transform .35s ease 0s;
	    transition: transform .35s ease 0s;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner:before {
		border-style: solid;
		border-width: 0;
		border-color: <?php echo ( $settings->overlay_border_color ) ? '#' . $settings->overlay_border_color : '#ffffff'; ?>;
		border-top-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
		border-bottom-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
		-webkit-transform: scale(0,1);
		-ms-transform: scale(0,1);
		transform: scale(0,1);
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner:after {
		border-style: solid;
		border-width: 0;
		border-color: <?php echo ( $settings->overlay_border_color ) ? '#' . $settings->overlay_border_color : '#ffffff'; ?>;
		border-left-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
		border-right-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
	    -webkit-transform: scale(1,0);
	    -ms-transform: scale(1,0);
	    transform: scale(1,0);
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay .pp-overlay-inner:before,
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay .pp-overlay-inner:after {
		-webkit-transform: scale(1);
	    -ms-transform: scale(1);
	    transform: scale(1);
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		opacity: 1;
	}
<?php } ?>

<?php if( $settings->hover_effects == 'zoom-in' || $settings->hover_effects == 'zoom-out' || $settings->hover_effects == 'greyscale' || $settings->hover_effects == 'blur' || $settings->hover_effects == 'rotate' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		top: 0;
		opacity: 0;
		overflow: hidden;
		<?php if( $settings->overlay_effects == 'none') { ?>
			left: 0;
			width: 100%;
			height: 100%;
		<?php } ?>
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

<?php if( $settings->hover_effects != 'none' ) { ?>
.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content .pp-gallery-img,
.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content .pp-gallery-img {
	-webkit-transition: all <?php echo ($settings->image_animation_speed/1000); ?>s ease;
	-moz-transition: all <?php echo ($settings->image_animation_speed/1000); ?>s ease;
	-ms-transition: all <?php echo ($settings->image_animation_speed/1000); ?>s ease;
	-o-transition: all <?php echo ($settings->image_animation_speed/1000); ?>s ease;
	transition: all <?php echo ($settings->image_animation_speed/1000); ?>s ease;
}
<?php } ?>

<?php if( $settings->hover_effects == 'zoom-in' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content .pp-gallery-img {
		-webkit-transform: scale(1);
		-moz-transform: scale(1);
		-ms-transform: scale(1);
		-o-transform: scale(1);
		transform: scale(1);
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content:hover .pp-gallery-img {
		-webkit-transform: scale(1.3);
		-moz-transform: scale(1.3);
		-ms-transform: scale(1.3);
		-o-transform: scale(1.3);
		transform: scale(1.3);
	}
<?php } ?>

<?php if( $settings->hover_effects == 'zoom-out' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content .pp-gallery-img {
		-webkit-transform: scale(1.5);
		-moz-transform: scale(1.5);
		-ms-transform: scale(1.5);
		-o-transform: scale(1.5);
		transform: scale(1.5);
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content:hover .pp-gallery-img {
		-webkit-transform: scale(1);
		-moz-transform: scale(1);
		-ms-transform: scale(1);
		-o-transform: scale(1);
		transform: scale(1);
	}
<?php } ?>

<?php if( $settings->hover_effects == 'greyscale' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content:hover .pp-gallery-img {
		-webkit-filter: grayscale(100%);
    	-moz-filter: grayscale(100%);
    	-ms-filter: grayscale(100%);
    	filter: grayscale(100%);
	}
<?php } ?>

<?php if( $settings->hover_effects == 'blur' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content .pp-gallery-img {
		-webkit-filter: blur(0);
    	filter: blur(0);
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content:hover .pp-gallery-img {
		-webkit-filter: blur(3px);
    	filter: blur(3px);
	}
<?php } ?>

<?php if( $settings->hover_effects == 'rotate' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content .pp-gallery-img {
		-webkit-transform: rotate(0) scale(1);
    	transform: rotate(0) scale(1);
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img,
	.fl-node-<?php echo $id; ?> .pp-masonry-content .pp-photo-gallery-content:hover .pp-gallery-img {
		-webkit-transform: rotate(15deg) scale(1.6);
		transform: rotate(15deg) scale(1.6);
	}
<?php } ?>

.fancybox-<?php echo $id; ?>-overlay {
	background-image: none;
	<?php if( ! empty( $settings->lightbox_overlay_color ) ) : ?>
	background-color: <?php echo ( false === strpos( $settings->lightbox_overlay_color, 'rgb' ) ) ? '#' . $settings->lightbox_overlay_color : $settings->lightbox_overlay_color; ?>;
	<?php endif; ?>
}

.fancybox-<?php echo $id; ?> .fancybox-skin {
	<?php if( $settings->lightbox_border_width != '' ) { ?>
		padding: <?php echo $settings->lightbox_border_width; ?>px !important;
	<?php } ?>
	<?php if( $settings->lightbox_border_radius != '' ) { ?>
		border-radius: <?php echo $settings->lightbox_border_radius; ?>px;
	<?php } ?>
	background: <?php echo ( $settings->lightbox_border_color ) ? '#'. $settings->lightbox_border_color : 'transparent'; ?>;
}

@media only screen and ( max-width: <?php echo $global_settings->medium_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item,
	.fl-node-<?php echo $id; ?> .pp-gallery-masonry-item {
		width: <?php echo $photo_columns_tablet;?>%;
		<?php if( $settings->photo_padding_medium ) { ?>
			padding: <?php echo $settings->photo_padding_medium; ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $desktop_col; ?>n+1){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $desktop_col; ?>n+0){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $desktop_col; ?>n){
		margin-right: <?php echo $settings->photo_spacing; ?>%;
	}
	<?php if ( $medium_col > 1 ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $medium_col; ?>n+1){
		clear: left;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $medium_col; ?>n+0){
		clear: right;
	}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $medium_col; ?>n){
		margin-right: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption,
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-caption  {
		<?php if( $settings->caption_font_size_toggle != 'default' && $settings->caption_custom_font_size_medium ) { ?>
	 	  font-size: <?php echo $settings->caption_custom_font_size_medium; ?>px;
	    <?php } ?>
	}
}

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item,
	.fl-node-<?php echo $id; ?> .pp-gallery-masonry-item {
		width: <?php echo $photo_columns_mobile;?>%;
		<?php if( $settings->photo_padding_responsive ) { ?>
			padding: <?php echo $settings->photo_padding_responsive; ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $medium_col; ?>n+1){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $medium_col; ?>n+0){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $medium_col; ?>n){
		margin-right: <?php echo $settings->photo_spacing; ?>%;
	}
	<?php if ( $mobile_col > 1 ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $mobile_col; ?>n+1){
		clear: left;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $mobile_col; ?>n+0){
		clear: right;
	}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-item:nth-child(<?php echo $mobile_col; ?>n){
		margin-right: 0;
	}

	.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption,
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-caption  {
		<?php if( $settings->caption_font_size_toggle != 'default' && $settings->caption_custom_font_size_responsive ) { ?>
	 	  font-size: <?php echo $settings->caption_custom_font_size_responsive; ?>px;
	    <?php } ?>
	}
}
