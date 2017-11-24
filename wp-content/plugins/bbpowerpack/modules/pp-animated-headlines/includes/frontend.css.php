.fl-node-<?php echo $id; ?> .pp-headline {
	text-align: <?php echo $settings->alignment; ?>;
	<?php if ( $settings->font_family['family'] != 'Default' ) {
        FLBuilderFonts::font_css( $settings->font_family );
    } ?>
	<?php if ( $settings->font_size == 'custom' ) { ?>
		font-size: <?php echo $settings->font_size_custom; ?>px;
	<?php } ?>
	<?php if ( $settings->line_height == 'custom' ) { ?>
		line-height: <?php echo $settings->line_height_custom; ?>;
	<?php } ?>
	<?php if ( $settings->color != '' ) { ?>
		color: #<?php echo $settings->color; ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-headline-dynamic-wrapper {
	<?php if ( $settings->font_family['family'] != 'Default' ) {
        FLBuilderFonts::font_css( $settings->animated_font_family );
    } ?>
	<?php if ( $settings->animated_font_size == 'custom' ) { ?>
		font-size: <?php echo $settings->animated_font_size_custom; ?>px;
	<?php } ?>
	<?php if ( $settings->animated_line_height == 'custom' ) { ?>
		line-height: <?php echo $settings->animated_line_height_custom; ?>;
	<?php } ?>
	<?php if ( $settings->animated_color != '' ) { ?>
		color: #<?php echo $settings->animated_color; ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-headline-dynamic-wrapper path {
	<?php if ( $settings->shape_width != '' ) { ?>
		stroke-width: <?php echo $settings->shape_width; ?>px;
	<?php } ?>
	<?php if ( $settings->shape_color ) { ?>
		stroke: #<?php echo $settings->shape_color; ?>;
	<?php } ?>
}

@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-headline {
		<?php if ( $settings->font_size == 'custom' ) { ?>
			font-size: <?php echo $settings->font_size_custom_medium; ?>px;
		<?php } ?>
		<?php if ( $settings->line_height == 'custom' ) { ?>
			line-height: <?php echo $settings->line_height_custom_medium; ?>;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-headline-dynamic-wrapper {
		<?php if ( $settings->animated_font_size == 'custom' ) { ?>
			font-size: <?php echo $settings->animated_font_size_custom_medium; ?>px;
		<?php } ?>
		<?php if ( $settings->animated_line_height == 'custom' ) { ?>
			line-height: <?php echo $settings->animated_line_height_custom_medium; ?>;
		<?php } ?>
	}
}

@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-headline {
		<?php if ( $settings->font_size == 'custom' ) { ?>
			font-size: <?php echo $settings->font_size_custom_responsive; ?>px;
		<?php } ?>
		<?php if ( $settings->line_height == 'custom' ) { ?>
			line-height: <?php echo $settings->line_height_custom_responsive; ?>;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-headline-dynamic-wrapper {
		<?php if ( $settings->animated_font_size == 'custom' ) { ?>
			font-size: <?php echo $settings->animated_font_size_custom_responsive; ?>px;
		<?php } ?>
		<?php if ( $settings->animated_line_height == 'custom' ) { ?>
			line-height: <?php echo $settings->animated_line_height_custom_responsive; ?>;
		<?php } ?>
	}
}